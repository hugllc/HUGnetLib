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
 * @version    0.9.7
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
class E00392800Device extends DeviceDriverBase
    implements DeviceDriverInterface
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00392800",
        "Type" => "device",
        "Class" => "E00392800Device",
        "Flags" => array(
            "0039-20-12-C:0039-28-01-A:DEFAULT",
            "0039-20-12-C:0039-28-01-B:DEFAULT",
            "0039-20-12-C:0039-28-01-C:DEFAULT",
            "0039-20-13-C:0039-28-01-A:DEFAULT",
            "0039-20-13-C:0039-28-01-B:DEFAULT",
            "0039-20-13-C:0039-28-01-C:DEFAULT",
            "DEFAULT:0039-28-01-A:DEFAULT",
            "DEFAULT:0039-28-01-B:DEFAULT",
            "DEFAULT:0039-28-01-C:DEFAULT",
        ),
    );
    /** @var This is to register the class */
    protected $outputLabels = array(
        "PhysicalSensors" => "Physical Sensors",
        "VirtualSensors" => "Virtual Sensors",
        "CPU" => "CPU",
        "SensorConfig" => "Sensor Configuration",
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
        $this->myDriver->DriverInfo["PhysicalSensors"] = 16;
        $this->myDriver->DriverInfo["VirtualSensors"] = 4;
        $this->fromSetupString($string);
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    public function readSetup()
    {
        $ret = $this->readConfig();
        if ($ret) {
            $ret = $this->readCalibration();
        }
        return $this->setLastConfig($ret);
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    */
    public function toOutput($cols = null)
    {
        $ret = parent::toOutput($cols);
        $ret["CPU"] = "Atmel Mega168";
        $ret["SensorConfig"] = "1-8 analog or digital, 9-16 digital only";
        return $ret;
    }

}

?>
