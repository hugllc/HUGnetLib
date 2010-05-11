<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../containers/DeviceContainer.php';
require_once dirname(__FILE__).'/../interfaces/DeviceDriverInterface.php';
require_once dirname(__FILE__).'/../containers/DeviceContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class DeviceDriverBase implements DeviceDriverInterface
{
    /** @var This is to register the class */
    public static $registerPlugin = array();
    /** @var This is to register the class */
    protected $myDriver = null;
    /**
    * Builds the class
    *
    * @param object &$obj   The object that is registering us
    * @param mixed  $string The string we will use to build the object
    *
    * @return null
    */
    public function __construct(&$obj, $string = "")
    {
        $this->myDriver = &$obj;
        $this->myDriver->DriverInfo = array();
        $this->info = &$this->myDriver->DriverInfo;
        $this->data = &$this->myDriver->params->DriverInfo;
    }
    /**
    * Says whether this device has loadable firmware or not
    *
    * This default always returns false.  It should be overwritten in classes
    * that use loadable firmware.
    *
    * @return bool False
    */
    public function loadable()
    {
        return false;
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    public function readSetup()
    {
        return $this->readConfig();
    }
    /**
    * Checks the interval to see if it is ready to config.
    *
    * I want:
    *    If the config is not $interval old: return false
    *    else: return based on number of failures.  Pause longer for more failures
    *
    *    It waits an extra minute for each failure
    *
    * @param int $interval The interval to check, in hours
    *
    * @return bool True on success, False on failure
    */
    public function readSetupTime($interval = 12)
    {
        // This is what would normally be our time.  Every 12 hours.
        $base = strtotime($this->myDriver->LastConfig) < (time() - $interval*3600);
        if ($base === false) {
            return $base;
        }
        // Accounts for failures
        return $this->data["LastConfig"] < (time() - $this->data["ConfigFail"]*60);
    }
    /**
    * Resets all of the timers associated with reading and writing.
    *
    * @return bool True on success, False on failure
    */
    public function readTimeReset()
    {
        // Config
        $this->myDriver->setDefault("LastConfig");
        $this->data["ConfigFail"] = 0;
        $this->data["LastConfig"] = 0;
        // Poll
        $this->myDriver->setDefault("LastPoll");
        $this->data["PollFail"] = 0;
        $this->data["LastPoll"] = 0;
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    protected function readconfig()
    {
        // Save the time.
        $this->data["LastConfig"] = time();
        // Build the packet
        $pkt = new PacketContainer(array(
            "To" => $this->myDriver->DeviceID,
            "Command" => PacketContainer::COMMAND_GETSETUP,
            "Timeout" => $this->myDriver->DriverInfo["PacketTimeout"],
        ));
        // send the packet
        $pkt->send();
        if (is_object($pkt->Reply)) {
            $this->myDriver->fromString($pkt->Reply->Data);
            $this->myDriver->LastConfig = $pkt->Reply->Date;
            $this->data["ConfigFail"] = 0;
            return true;
        }
        // We failed.  State that.
        $this->data["ConfigFail"]++;
        return false;
    }
    /**
    * Reads the calibration out of the device
    *
    * @return bool True on success, False on failure
    */
    protected function readCalibration()
    {
        $pkt = new PacketContainer(array(
            "To" => $this->myDriver->DeviceID,
            "Command" => PacketContainer::COMMAND_GETCALIBRATION,
            "Timeout" => $this->myDriver->DriverInfo["PacketTimeout"],
        ));
        $pkt->send();
        if (is_object($pkt->Reply)) {
            $this->myDriver->params->Raw["Calibration"] = $pkt->Reply->Data;
            $this->myDriver->sensors->fromCalString($pkt->Reply->Data);
            return true;
        }
        return false;
    }

    /**
    * Creates the object from a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toString($default = true)
    {
        return "";

    }
    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function fromString($string)
    {
        $this->myDriver->DriverInfo["TimeConstant"] = hexdec(substr($string, 0, 2));
        if (is_object($this->myDriver->sensors)) {
            $this->myDriver->sensors->fromTypeString(substr($string, 2));
        }
    }

}