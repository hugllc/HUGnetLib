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
    /** This command runs the boot loader, crashing the running program */
    const COMMAND_RUNBOOTLOADER = "09";

    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00392100",
        "Type" => "device",
        "Class" => "E00392100Device",
        "Devices" => array(
            "0039-20-01-C" => array(
                "0039-21-01-A" => "DEFAULT",
            ),
            "0039-20-14-C" => array(
                "0039-21-02-A" => "DEFAULT",
            ),
        ),
    );
    /** @var This is what our targets are for the various hardware part numbers */
    protected $HWTargets = array(
        "0039-21-01-A" => "atmega16",
        "0039-21-02-A" => "atmega324p",
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
        $this->myDriver->DriverInfo["PacketTimeout"] = 2;
        $this->fromString($string);
        $this->_setFirmware();
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
            $this->_setFirmware();
            if ($this->myFirmware->compareVersion($this->myDriver->FWVersion) < 0) {
                // Crash the running program so the board can be reloaded
                $this->runBootloader();
                // This is because the program needs to be reloaded.  It can
                // only be reloaded if it is using the 00392101 driver.
                $ret = false;
            }
        }
        return $ret;
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    public function runBootloader()
    {
        $pkt = new PacketContainer(array(
            "To" => $this->myDriver->DeviceID,
            "Command" => self::COMMAND_RUNBOOTLOADER,
            "Timeout" => $this->myDriver->DriverInfo["PacketTimeout"],
            "GetReply" => false,
        ));
        $pkt->send();
        return false;
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
                "FWPartNum" => $this->myDriver->FWPartNum,
                "Version" => $this->myDriver->FWVersion,
                "Target" => $this->HWTargets[$this->myDriver->HWPartNum],
            )
        );
        $this->myFirmware->getLatest();
    }
}

?>
