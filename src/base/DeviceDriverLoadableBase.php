<?php
/**
 * Tests the filter class
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
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** This is the stuff we need to include */
require_once dirname(__FILE__).'/DeviceDriverBase.php';
require_once dirname(__FILE__).'/../tables/FirmwareTable.php';

/**
 * Base for loadable device drivers
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class DeviceDriverLoadableBase extends DeviceDriverBase
    implements DeviceDriverInterface
{
    /** The placeholder for the Read CRC command */
    const COMMAND_READCRC = "06";
    /** The placeholder for the Read CRC command */
    const COMMAND_WRITECRC = "07";
    /** This command runs the application */
    const COMMAND_RUNAPPLICATION = "08";
    /** This command runs the boot loader, crashing the running program */
    const COMMAND_RUNBOOTLOADER = "09";

    /** @var This is our firmware interface */
    protected $myFirmware = null;
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
        parent::__construct($obj, $string);
        $this->myFirmware = new FirmwareTable();
    }
    /**
    * Devices that inherit this class have loadable firmware, so this should return
    * true.
    *
    * @return bool True
    */
    public function loadable()
    {
        return true;
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
    public function readSetupTime($interval = 10)
    {
        $this->data = &$this->myDriver->params->DriverInfo;
        // This is what would normally be our time.  Every 12 hours.
        if ($this->data["LastConfig"] > time()) {
            // If our time is in the future we have a clock problem.  Go now
            return true;
        } else if (($this->data["LastConfig"] + ($interval * 60)) > time()) {
            return false;
        } else if (($this->data["LastConfigTry"] + 60) > time()) {
            return false;
        }
        return true;
    }
    /**
    * Programs a page of flash
    *
    * Due to the nature of flash, $Val must contain the data for
    * a whole page of flash.
    *
    * @param int    $addr The start address of this block
    * @param string $data The data to program into E2 as a hex string
    *
    * @return true on success, false on failure
    */
    protected function writeFlash($addr, $data)
    {
        $ret = $this->memPage($addr, $data, PacketContainer::COMMAND_WRITEFLASH);
        return ($ret === $data);
    }

    /**
    * Programs a block of E2
    *
    * This function won't let locations 0-9 be written.  They are reserved for the
    * serial number and shouldn't be overwritten
    *
    * @param int    $addr The start address of this block
    * @param string $data The data to program into E2 as a hex string
    *
    * @return true on success, false on failure
    */
    protected function writeE2($addr, $data)
    {

        // Protect the first 10 bytes of E2
        if ($addr < 10) {
            $data = substr($data, (20 - (2*$addr)));
            $addr = 10;
        }
        $ret = $this->memPage($addr, $data, PacketContainer::COMMAND_WRITEE2);
        return ($ret === $data);
    }
    /**
    * Gets the CRC of the data
    *
    * @return The CRC on success, false on failure
    */
    protected function readCRC()
    {
        return $this->sendPkt(self::COMMAND_READCRC);
    }

    /**
    * Gets the CRC of the data
    *
    * @return The CRC on success, false on failure
    */
    protected function writeCRC()
    {
        return $this->sendPkt(self::COMMAND_WRITECRC);
    }
    /**
    * Runs the application
    *
    * @return bool true on success, false on failure
    */
    function runApplication()
    {
        $ret = $this->sendPkt(self::COMMAND_RUNAPPLICATION, "");
        return is_string($ret);
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    public function runBootloader()
    {
        $ret = $this->sendPkt(self::COMMAND_RUNBOOTLOADER, "");
        return is_string($ret);
    }
}