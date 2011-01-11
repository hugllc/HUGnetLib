<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/**
 * Interface for device drivers
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
interface DeviceDriverInterface
{
    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function fromSetupString($string);
    /**
    * Creates the object from a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toSetupString($default = true);
    /**
    * This decodes the data returned from the endpoint
    *
    * @param string $string  The string of sensor data
    * @param string $command The command that was used to get the data
    * @param float  $deltaT  The time difference between this packet and the next
    *
    * @return null
    */
    public function decodeData($string, $command="", $deltaT = 0);
    /**
    * Says whether this device has loadable firmware or not
    *
    * This default always returns false.  It should be overwritten in classes
    * that use loadable firmware.
    *
    * @return bool False
    */
    public function loadable();
    /**
    * Says whether this device is a gateway process or not
    *
    * This default always returns false.  It should be overwritten in classes
    * that are for gateway processes.
    *
    * @return bool False
    */
    public function gateway();
    /**
    * Says whether this device is a controller board or not
    *
    * This default always returns false.   This is a controller baord, so we
    * return true
    *
    * @return bool False
    */
    public function controller();
    /**
    * Reads the setup out of the device.
    *
    * If the device is using outdated firmware we have to
    *
    * @return bool True on success, False on failure
    */
    public function readSetup();
    /**
    * Checks the interval to see if it is ready to config.
    *
    * I want:
    *    If the config is not $interval old: return false
    *    else: if we have tried in the last 5 minutes, return false
    *    else: return true
    *
    * @param int $interval The interval to check, in hours
    *
    * @return bool True on success, False on failure
    */
    public function readSetupTime($interval);
    /**
    * Resets all of the timers associated with reading and writing.
    *
    * @return bool True on success, False on failure
    */
    public function readSetupTimeReset();
    /**
    * Resets all of the timers associated with reading and writing.
    *
    * @return bool True on success, False on failure
    */
    public function readDataTimeReset();
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    public function readConfig();
    /**
    * Checks the interval to see if it is ready to config.
    *
    * I want:
    *    If the config is not $interval old: return false
    *    else: return based on number of failures.  Pause longer for more failures
    *
    *    It waits an extra 6 seconds for each failed poll
    *
    * @return bool True on success, False on failure
    */
    public function readDataTime();
    /**
    * Reads the data out of the device
    *
    * @return bool True on success, False on failure
    */
    public function readData();

}