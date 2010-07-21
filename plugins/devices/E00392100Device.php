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
class E00392100Device extends DeviceDriverLoadableBase
    implements DeviceDriverInterface
{
    /** The placeholder for the reading the downstream units from a controller */
    const COMMAND_READDOWNSTREAM = "56";

    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00392100",
        "Type" => "device",
        "Class" => "E00392100Device",
        "Flags" => array(
            "0039-20-01-C:0039-21-01-A:DEFAULT",
            "0039-20-14-C:0039-21-02-A:DEFAULT",
        ),
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
        $this->myDriver->DriverInfo["NumSensors"] = 6;
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
    * If the device is using outdated firmware we have to
    *
    * @return bool True on success, False on failure
    */
    public function readSetup()
    {
        $ret = $this->readConfig();
        if ($ret) {
            if ($this->myDriver->Driver !== self::$registerPlugin["Name"]) {
                // Reset config time so this device is checked again.
                //$this->readSetupTimeReset();
                // Try to just run the application first
                $this->runApplication();
                // Wrong Driver  We should exit with a failure unless the setup
                // returns us with the right one
                $ret = $this->readConfig();
                if ($this->myDriver->Driver !== self::$registerPlugin["Name"]) {
                    $this->vprint(
                        "Running the Application:  Failed",
                        HUGnetClass::VPRINT_NORMAL
                    );
                    $ret = null;
                } else {
                    $this->vprint(
                        "Running the Application:  Succeeded",
                        HUGnetClass::VPRINT_NORMAL
                    );
                }
            }
        }
        if ($ret) {
            $this->_setFirmware();
            $ver = $this->myFirmware->compareVersion($this->myDriver->FWVersion);
            if ($ver < 0) {
                $this->vprint(
                    "Found new firmware ".$this->myFirmware->FWPartNum
                    ." v".$this->myFirmware->Version
                    ." > v".$this->myDriver->FWVersion,
                    HUGnetClass::VPRINT_NORMAL
                );

                // Crash the running program so the board can be reloaded
                $this->runBootloader();
                // This forces us to not just run the application again
                $this->readConfig();
                // This is because the program needs to be reloaded.  It can
                // only be reloaded if it is using the 00392101 driver.
                $ret = null;
            }
        }
        if ($ret) {
            // This doesn't count towards whether the config passes or fails because
            // the packet is currently too big to go through the new controller
            // board.  If it works it works.  If it doesn't it doesn't.
            $this->readDownstreamDevices();
        }
        return $this->setLastConfig($ret);
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    protected function readDownstreamDevices()
    {
        for ($key = 0; $key < 2; $key++) {
            // Send the packet out
            $ret = $this->sendPkt(
                self::COMMAND_READDOWNSTREAM,
                $this->stringSize($key, 2)
            );
            if (is_string($ret) && !empty($ret)) {
                $dev = new DeviceContainer();
                $devs = str_split($ret, 6);
                foreach ($devs as $d) {
                    $dev->clearData();
                    $id = hexdec($d);
                    if (!empty($id)) {
                        $dev->getRow($id);
                        $dev->ControllerKey = $this->myDriver->id;
                        $dev->ControllerIndex = $key;
                        $dev->updateRow(array("ControllerKey", "ControllerIndex"));
                    }
                }
                $ret = true;;
            }
        }
        return (bool) $ret;
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    private function _setFirmware()
    {
        $this->myFirmware->clearData();
        $this->myFirmware->fromArray(
            array(
                "HWPartNum" => $this->myDriver->HWPartNum,
                "FWPartNum" => $this->myDriver->FWPartNum,
            )
        );
        $this->myFirmware->getLatest();
    }
}

?>
