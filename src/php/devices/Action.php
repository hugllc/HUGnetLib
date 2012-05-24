<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * Networking for devices.
 *
 * This class will do all of the networking for devices.  It will poll, get configs,
 * update software, and anything else related to talking to devices.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Action
{
    /**
    * This is the system object
    */
    private $_system = null;
    /**
    * This is the driver object
    */
    private $_driver = null;
    /**
    * This is the table object
    */
    private $_device = null;
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$system The network application object
    * @param object &$device The device device object
    * @param object &$driver The device driver object
    *
    * @return null
    */
    protected function __construct(&$system, &$device, &$driver)
    {
        \HUGnet\System::exception(
            get_class($this)." needs to be passed a system object",
            "InvalidArgument",
            !is_object($system)
        );
        \HUGnet\System::exception(
            get_class($this)." needs to be passed a driver object",
            "InvalidArgument",
            !is_object($driver)
        );
        \HUGnet\System::exception(
            get_class($this)." needs to be passed a device object",
            "InvalidArgument",
            !is_object($device)
        );
        $this->_system = &$system;
        $this->_driver  = &$driver;
        $this->_device  = &$device;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_system);
        unset($this->_driver);
        unset($this->_device);
    }
    /**
    * This function creates the system.
    *
    * @param mixed  &$network (object)The system object to use
    * @param string &$device  (object)The device to use
    * @param object &$driver  The device driver object
    *
    * @return null
    */
    public static function &factory(&$network, &$device, &$driver)
    {
        $object = new Action($network, $device, $driver);
        return $object;
    }
    /**
    * Pings the device and sets the LastContact if it is successful
    *
    * @param bool $find Whether or not to use a find ping
    *
    * @return string The left over string
    */
    public function ping($find = false)
    {
        $pkt = $this->_device->network()->ping(
            $find, null, null, array("tries" => 1, "find" => false)
        );
        if (is_string($pkt->reply())) {
            $this->_device->setParam("LastContact", time());
            $this->_device->setParam("ContactFail", 0);
            return true;
        }
        $fail = $this->_device->getParam("ContactFail");
        $this->_device->setParam("ContactFail", $fail+1);
        return false;
    }
    /**
    * Gets the config and saves it
    *
    * @return string The left over string
    */
    public function config()
    {
        $pkt = $this->_device->network()->config();
        if (strlen($pkt->reply())) {
            if ($this->_device->decode($pkt->reply())) {
                $this->_device->setParam("LastContact", time());
                $this->_device->setParam("LastConfig", time());
                $this->_device->setParam("ConfigFail", 0);
                $this->_device->setParam("ContactFail", 0);
                return true;
            }
        }
        $fail = $this->_device->getParam("ConfigFail");
        $this->_device->setParam("ConfigFail", $fail+1);
        return false;
    }
    /**
    * Polls the device and saves the poll
    *
    * @param int $TestID The test ID of this poll
    * @param int $time   The time to use for the poll
    *
    * @return false on failure, the history object on success
    */
    public function poll($TestID = null, $time = null)
    {
        if (empty($time)) {
            $time = time();
        }
        $pkt = $this->_device->network()->poll();
        if (strlen($pkt->reply()) > 0) {
            $data = $this->_device->decodeData(
                $pkt->Reply(),
                $pkt->Command(),
                0,
                (array)$this->_device->getParam("LastPollData")
            );
            $data["id"]     = $this->_device->get("id");
            $data["Date"]   = $time;
            $data["TestID"] = $TestID;
            $this->_device->setParam("LastPollData", $data);
            $this->_device->setUnits($data);
            $d = $this->_device->historyFactory($data);
            $d->insertRow();
            $this->_device->setParam("LastPoll", $time);
            $this->_device->setParam("LastContact", $time);
            $this->_device->setParam("PollFail", 0);
            $this->_device->setParam("ContactFail", 0);
            return $d;
        }
        $fail = $this->_device->getParam("PollFail");
        $this->_device->setParam("PollFail", $fail+1);
        return false;
    }
    /**
    * Checks the record to see if something needs to be done about it.
    *
    * @return null
    */
    public function checkRecord()
    {
        $this->_driver->checkRecord($this->_device);
    }
    /**
    * Gets the config and saves it
    *
    * @param string $url The url to post to
    *
    * @return string The left over string
    */
    public function post($url = null)
    {
        if (!is_string($url) || (strlen($url) == 0)) {
            $master = $this->_device->system()->get("master");
            $url = $master["url"];
        }
        $device  = $this->_device->toArray(true);
        $sens = $this->_device->get("totalSensors");
        $sensors = array();
        for ($i = 0; $i < $sens; $i++) {
            $sensors[$i] = $this->_device->sensor($i)->toArray(false);
        }
        return \HUGnet\Util::postData(
            $url,
            array(
                "uuid"    => urlencode($this->_device->system()->get("uuid")),
                "id"      => $device["id"],
                "action"  => "post",
                "task"    => "device",
                "device"  => $device,
                "sensors" => $sensors,
            )
        );
    }


}


?>
