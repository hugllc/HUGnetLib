<?php
/**
 * This is the driver code for the HUGnet data collector (0039-26).
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsDevices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is a required class */
require_once dirname(__FILE__).'/../../base/DeviceDriverBase.php';
/** This is a required class */
require_once dirname(__FILE__).'/../../interfaces/DeviceDriverInterface.php';
/** This is a required class */
require_once dirname(__FILE__).'/../../interfaces/PacketConsumerInterface.php';

/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Libraries
* @package    HUGnetLib
* @subpackage PluginsDevices
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2011 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class EVIRTUALDevice extends DeviceDriverBase
    implements DeviceDriverInterface
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "eVIRTUAL",
        "Type" => "device",
        "Class" => "EVIRTUALDevice",
        "Flags" => array(
            "DEFAULT:VIRTUAL:DEFAULT",
            "DEFAULT:0039-24-02-P:DEFAULT",
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
        $obj->DriverInfo["PhysicalSensors"] = 0;
        $obj->DriverInfo["VirtualSensors"] = 20;
        if (empty($obj->FWPartNum)) {
            $obj->FWPartNum = "0039-24-02-P";
        }
        $obj->FWVersion = HUGNET_LIB_VERSION;
        $obj->PollInterval = 0;
        parent::__construct($obj, $string);
    }
    /**
    * This always forces the sensors to the same thing.  All of the sensors
    * here are virtual sensors.  The call to fromTypeArray sets that up.
    *
    * @param string $string This is totally ignored.
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function fromSetupString($string)
    {
        if (is_object($this->myDriver->sensors)) {
            $this->myDriver->sensors->Sensors = 20;
            $this->myDriver->sensors->PhysicalSensors = 0;
            $this->myDriver->sensors->VirtualSensors = 20;
            $this->myDriver->sensors->fromTypeArray(array());
        }
    }
}

?>
