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
/** This is our base class */
require_once dirname(__FILE__)."/Action.php";
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
 * @since      0.9.8
 */
class ActionVirtual extends Action
{
    /** This is a cache of history records */
    private $_histCache = array();
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
        $object = new ActionVirtual($network, $device, $driver);
        return $object;
    }
    /**
    * Gets all of the device ids that are needed
    *
    * @return array of device keys
    */
    private function _getIDs()
    {
        $sensors = $this->device->get("totalSensors");
        for ($i = 0; $i < $sensors; $i++) {
            $sen = $this->device->sensor($i);
            if ($sen->get("driver") === "CloneVirtual") {
                $extra = $sen->get("extra");
                $dev = hexdec($extra[0]);
                if (!empty($dev)) {
                    $ret[$dev] = sprintf("%06X", $dev);
                }
            }
        }
        return $ret;
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
        return false;
    }
    /**
    * Gets the config and saves it
    *
    * @return string The left over string
    */
    public function config()
    {
        $this->checkRecord();
        return true;
    }
    /**
    * Polls the device and saves the poll
    *
    * @param object &$sensor The sensor to use
    * @param int    $time    The time to use
    *
    * @return false on failure, the history object on success
    */
    private function _getPoint(&$sensor, $time)
    {
        $extra = $sensor->get("extra");
        $dev = hexdec($extra[0]);
        $sen = $extra[1];
        if (empty($dev)) {
            return null;
        }
        if (!is_object($this->_histCache[$dev])) {
            $device = &$this->system->device($dev);
            $this->_histCache[$dev] = &$device->action()->poll(
                $this->device->get('id'), $time
            );
            $device->store();
        }
        if (is_object($this->_histCache[$dev])) {
            return $this->_histCache[$dev]->get("Data".$sen);
        }
        return null;
    }
    /**
    * Polls the device and saves the poll
    *
    * @return false on failure, the history object on success
    */
    private function _clearHistCache()
    {
        $this->_histCache = array();
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
        $this->_clearHistCache();
        if (is_null($time)) {
            $time = time();
        }
        $id = $this->device->get("id");
        $prev = (array)$this->device->getParam("LastPollData");
        $hist = array(
            "id" => $id,
            "Date" => $time,
            "TestID" => $TestID,
            "deltaT" => $time - $prev["Date"],
        );
        $sensors = $this->device->get("totalSensors");
        for ($i = 0; $i < $sensors; $i++) {
            $sen = $this->device->sensor($i);
            if ($sen->get("driver") === "CloneVirtual") {
                $hist[$i] = array(
                    "value" => $this->_getPoint($sen, $time),
                    "units" => $sen->get("storageUnit"),
                    "unitType" => $sen->get("unitType"),
                    "dataType" => $sen->get("storageType"),
                );
            } else {
                $string = "";
                $hist[$i] = $sen->decodeData(
                    $string,
                    $hist["deltaT"],
                    $prev[$i],
                    $hist
                );
            }
        }
        $this->device->setParam("LastPollData", $hist);
        $this->device->setParam("LastPoll", $time);
        $this->device->setParam("LastContact", $time);
        $this->device->setParam("PollFail", 0);
        $this->device->setParam("ContactFail", 0);
        $history = &$this->device->historyFactory($hist);
        $history->insertRow();
        return $history;
    }
}


?>
