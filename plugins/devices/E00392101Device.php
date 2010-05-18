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
        "Devices" => array(
            "0039-20-06-C" => array(
                "0039-21-01-A" => "DEFAULT"
            ),
            "0039-20-15-C" => array(
                "0039-21-02-A" => "DEFAULT",
            ),
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
                $ret = false;
            } else {
                $ret = $this->writeProgram();
            }
        }
        return $this->setLastConfig($ret);
    }
    /**
    * Writes the program into the device
    *
    * @return bool True on success, False on failure
    */
    public function writeProgram()
    {
        $this->_setFirmware();
        $ret = $this->writeCode();
        if ($ret) {
            $ret = $this->writeData();
        }
        if ($ret) {
            $ret = $this->runApplication();
        }
        if ($ret) {
            $this->logError(
                "LAODPROG",
                "Device ".$this->myDriver->DeviceID." has been loaded with "
                .$this->myFirmware->FWPartNum." v".$this->myFirmware->Version.".",
                ErrorTable::SEVERITY_NOTICE,
                __METHOD__
            );
        }
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
                return false;
            }
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
                return false;
            }
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
    * @return bool True on success, False on failure
    */
    private function _setFirmware()
    {
        $this->myFirmware->fromArray(
            array(
                "HWPartNum" => $this->myDriver->HWPartNum,
                "FWPartNum" => $this->FWPartNum[$this->myDriver->HWPartNum],
            )
        );
        $this->myFirmware->getLatest();
    }
}

?>
