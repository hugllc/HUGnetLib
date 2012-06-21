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
/** This is required for polling */
require_once dirname(__FILE__)."/../tables/RawHistoryTable.php";

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
    protected $system = null;
    /**
    * This is the driver object
    */
    protected $driver = null;
    /**
    * This is the table object
    */
    protected $device = null;
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
        $this->system = &$system;
        $this->driver  = &$driver;
        $this->device  = &$device;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->system);
        unset($this->driver);
        unset($this->device);
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
        $pkt = $this->device->network()->ping(
            $find, null, null, array("tries" => 1, "find" => false)
        );
        if (is_string($pkt->reply())) {
            $this->device->setParam("LastContact", time());
            $this->device->setParam("ContactFail", 0);
            return true;
        }
        $fail = $this->device->getParam("ContactFail");
        $this->device->setParam("ContactFail", $fail+1);
        return false;
    }
    /**
    * Gets the config and saves it
    *
    * @return string The left over string
    */
    public function config()
    {
        $pkt = $this->device->network()->config();
        if (strlen($pkt->reply())) {
            if ($this->device->decode($pkt->reply())) {
                $this->device->setParam("LastContact", time());
                $this->device->setParam("LastConfig", time());
                $this->device->setParam("ConfigFail", 0);
                $this->device->setParam("ContactFail", 0);
                return true;
            }
        }
        $fail = $this->device->getParam("ConfigFail");
        $this->device->setParam("ConfigFail", $fail+1);
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
        $HWPart = $this->device->get("HWPartNum");
        if (empty($HWPart)) {
            return false;
        }
        if (empty($time)) {
            $time = time();
        }
        $pkt = $this->device->network()->poll();
        if (strlen($pkt->reply()) > 0) {
            $prev = (array)$this->device->getParam("LastPollData");
            $data = $this->device->decodeData(
                $pkt->Reply(),
                $pkt->Command(),
                0,
                $prev
            );
            \RawHistoryTable::insertRecord(
                array(
                    "id" => $this->device->id(),
                    "Date" => $time,
                    "packet" => array(
                        "Command" => $pkt->command(),
                        "Data"    => (string)$pkt->data(),
                        "Reply"   => (string)$pkt->reply(),
                        "To"      => $pkt->to(),
                    ),
                    "dataIndex" => $data["DataIndex"],
                    "command"   => $pkt->command(),
                )
            );
            $data["id"]     = $this->device->get("id");
            $data["Date"]   = $time;
            $data["TestID"] = $TestID;
            $data["deltaT"] = $time - $prev["Date"];
            $this->device->setParam("LastPollData", $data);
            $this->device->setUnits($data);
            $d = $this->device->historyFactory($data);
            $d->insertRow();
            $this->device->setParam("LastPoll", $time);
            $this->device->setParam("LastContact", $time);
            $this->device->setParam("PollFail", 0);
            $this->device->setParam("ContactFail", 0);
            return $d;
        }
        $fail = $this->device->getParam("PollFail");
        $this->device->setParam("PollFail", $fail+1);
        return false;
    }
    /**
    * Checks the record to see if something needs to be done about it.
    *
    * @return null
    */
    public function checkRecord()
    {
        $this->driver->checkRecord();
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
            $master = $this->device->system()->get("master");
            $url = $master["url"];
        }
        $device  = $this->device->toArray(true);
        return \HUGnet\Util::postData(
            $url,
            array(
                "uuid"    => urlencode($this->device->system()->get("uuid")),
                "id"      => $device["id"],
                "action"  => "post",
                "task"    => "device",
                "device"  => $device,
            )
        );
    }


}


?>
