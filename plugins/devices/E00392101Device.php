<?php
/**
 * This is the driver code for the HUGnet data collector (0039-26).
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
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

// This is our base class
require_once dirname(__FILE__).'/../../base/DeviceDriverLoadableBase.php';
// This is the interface we are implementing
require_once dirname(__FILE__).'/../../interfaces/DeviceDriverInterface.php';
require_once dirname(__FILE__).'/../../interfaces/PacketConsumerInterface.php';

/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Endpoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2010 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class E00392101Device extends DeviceDriverLoadableBase
    implements DeviceDriverInterface
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00392101",
        "Type" => "device",
        "Class" => "E00392101Device",
        "Flags" => array(
            "0039-20-06-C:0039-21-01-A:DEFAULT",
            "0039-20-15-C:0039-21-02-A:DEFAULT",
            "0039-20-16-C:0039-21-02-A:DEFAULT",
        ),
    );
    /** @var This is what our targets are for the various hardware part numbers */
    protected $FWPartNum = array(
        "0039-21-01-A" => "0039-20-01-C",
        "0039-21-02-A" => "0039-20-14-C",
    );
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
        $this->myDriver->DriverInfo["NumSensors"] = 0;
        $this->fromSetupString($string);
    }
    /**
    * Says whether this device is a controller board or not
    *
    * This default always returns false.   This is a controller baord, so we
    * return true
    *
    * @return bool False
    */
    public function controller()
    {
        return true;
    }
    /**
    * Reads the setup out of the device.
    *
    * After we get the setup we try to load the program and try to run it.
    *
    * @return bool True on success, False on failure
    */
    public function readSetup()
    {
        $ret = $this->readConfig();
        if ($ret) {
            if ($this->myDriver->Driver !== self::$registerPlugin["Name"]) {
                // Reset config time so this device is checked again.
                $this->readSetupTimeReset();
                // Wrong Driver  We should exit with a failure
                $ret = null;
            } else {
                // If that fails,
                $ret = $this->writeProgram();
                if ($ret) {
                    $this->myDriver->params->DriverInfo["loadProgSuccess"]++;
                    $ret = $this->readConfig();
                } else {
                    $this->myDriver->params->DriverInfo["loadProgFail"]++;
                }
            }
        }
        return $this->setLastConfig($ret);
    }
    /**
    * Writes the program into the device
    *
    * The steps are as follows:
    * 1. Get the firmware
    * 2. Write the Code (Flash)
    * 3. Write the Data (E2)
    * 4. Write the CRC (This allows the program to boot)
    * 5. Run the program
    *
    * The program will not boot unless the CRC is written.  The device calculates
    * it and sets it, we just have to tell the device to do it.  On boot the
    * device checks the saved CRC against the calculated one.  If they match it
    * tries to run the loaded application.
    *
    * @param string $version Selects a certain version of firmware.  If left empty
    *                        it gets the latest.
    *
    * @todo This routine could use some cleanup
    * @return bool True on success, False on failure
    */
    public function writeProgram($version = "")
    {
        // We need to set the packet timeout to 10 seconds,because of the long
        // packets.
        $OldTimeout = $this->myDriver->DriverInfo["PacketTimeout"];
        $this->myDriver->DriverInfo["PacketTimeout"] = 10;
        // Sets up the firmware
        $this->_setFirmware($version);
        if (($this->myFirmware->RelStatus == FirmwareTable::BAD)
            || $this->myFirmware->isEmpty()
        ) {
            $this->logError(
                "NOFIRMWARE",
                "There is no firmware avaiable for HW#".$this->myDriver->HWPartNum,
                ErrorTable::SEVERITY_CRITICAL,
                __METHOD__
            );
            return false;
        }
        // Print out some stuff
        self::vprint(
            "Found firmware ".$this->myFirmware->FWPartNum
            ." v".$this->myFirmware->Version,
            HUGnetClass::VPRINT_NORMAL
        );
        $ret = $this->writeCode();
        if ($ret) {
            $ret = $this->writeData();
        }
        if ($ret) {
            $crc = $this->writeCRC();
            self::vprint(
                "Setting the application CRC to $crc",
                HUGnetClass::VPRINT_NORMAL
            );
            $ret = $this->runApplication();
            if ($ret) {
                $msg = "Running the program "
                    ."in device ".$this->myDriver->DeviceID." Succeeded";
            } else {
                $msg = "Running the program "
                    ."in device ".$this->myDriver->DeviceID." Failed";
            }
            self::vprint(
                $msg,
                HUGnetClass::VPRINT_NORMAL
            );
        }
        if ($ret) {
            $msg = "Device ".$this->myDriver->DeviceID." has been loaded with "
                .$this->myFirmware->FWPartNum." v".$this->myFirmware->Version.".";
            self::vprint(
                $msg,
                HUGnetClass::VPRINT_NORMAL
            );
            $this->logError(
                "LOADPROG",
                $msg,
                ErrorTable::SEVERITY_NOTICE,
                __METHOD__
            );
        }
        $this->myDriver->DriverInfo["PacketTimeout"] = $OldTimeout;
        return $ret;
    }
    /**
    * Writes the code into the device
    *
    * @return bool True on success, False on failure
    */
    protected function writeCode()
    {
        $code = $this->myFirmware->getCode("FF");
        $size = strlen($code)/2;
        $pageSize = $this->myDriver->DriverInfo["FLASHPAGE"];
        $code = str_split($code, $pageSize*2);
        foreach ($code as $page => $data) {
            $data = str_pad($data, $pageSize*2, "FF");
            $addr = $page * $pageSize;
            $ret = $this->writeFlash($addr, $data);
            if ($ret === false) {
                self::vprint(
                    "Writing Code Page ".$page." "
                    ."in device ".$this->myDriver->DeviceID." Failed",
                    HUGnetClass::VPRINT_NORMAL
                );
                return false;
            }
            self::vprint(
                "Writing Code Page ".$page." "
                ."in device ".$this->myDriver->DeviceID." Succeeded",
                HUGnetClass::VPRINT_NORMAL
            );
        }
        return true;
    }
    /**
    * Writes the data into the device
    *
    * @return bool True on success, False on failure
    */
    protected function writeData()
    {
        $code = $this->myFirmware->getData("FF");
        $size = strlen($code)/2;
        $pageSize = $this->myDriver->DriverInfo["FLASHPAGE"];
        $code = str_split($code, $pageSize*2);
        foreach ($code as $page => $data) {
            $data = str_pad($data, $pageSize*2, "FF");
            $addr = $page * $pageSize;
            $ret = $this->writeE2($addr, $data);
            if ($ret === false) {
                self::vprint(
                    "Writing Data Page ".$page." "
                    ."in device ".$this->myDriver->DeviceID." Failed",
                    HUGnetClass::VPRINT_NORMAL
                );
                return false;
            }
            self::vprint(
                "Writing Data Page ".$page." "
                ."in device ".$this->myDriver->DeviceID." Succeeded",
                HUGnetClass::VPRINT_NORMAL
            );
        }
        return true;
    }
    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function fromSetupString($string)
    {
        if (empty($string)) {
            return;
        }
        $this->Info = &$this->myDriver->DriverInfo;
        $this->Info["SRAM"]     = hexdec(substr($string, 0, 4));
        $this->Info["E2"]       = hexdec(substr($string, 4, 4));
        $this->Info["FLASH"]    = hexdec(substr($string, 8, 6));
        $this->Info["FLASHPAGE"]= hexdec(substr($string, 14, 4));
        if ($this->Info["FLASHPAGE"] == 0) {
            $this->Info["FLASHPAGE"] = 128;
        }
        $this->Info["PAGES"] = $this->Info["FLASH"]/$this->Info["FLASHPAGE"];
        $this->Info["CRC"] = strtoupper(substr($string, 18, 4));
    }
    /**
    * Reads the setup out of the device
    *
    * @param string $version Selects a certain version of firmware.  If left empty
    *                        it gets the latest.
    *
    * @return bool True on success, False on failure
    */
    private function _setFirmware($version = "")
    {
        $this->myFirmware->fromArray(
            array(
                "HWPartNum" => $this->myDriver->HWPartNum,
                "FWPartNum" => $this->FWPartNum[$this->myDriver->HWPartNum],
                "Version"   => $version,
            )
        );
        $this->myFirmware->getLatest();
    }
}

?>
