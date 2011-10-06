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
 * @version    Release: 0.9.7
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
class E00391200Device extends DeviceDriverBase
    implements DeviceDriverInterface
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00391200",
        "Type" => "device",
        "Class" => "E00391200Device",
        "Flags" => array(
            "0039-11-02-B:0039-12-00-A:DEFAULT",
            "0039-11-02-B:0039-12-01-A:DEFAULT",
            "0039-11-02-B:0039-12-02-A:DEFAULT",
            "0039-11-02-B:0039-12-01-B:DEFAULT",
            "0039-11-02-B:0039-12-02-B:DEFAULT",
            "0039-11-03-B:0039-12-00-A:DEFAULT",
            "0039-11-03-B:0039-12-01-A:DEFAULT",
            "0039-11-03-B:0039-12-02-A:DEFAULT",
            "0039-11-03-B:0039-12-01-B:DEFAULT",
            "0039-11-03-B:0039-12-02-B:DEFAULT",
            "0039-20-02-C:0039-12-02-A:DEFAULT",
            "0039-20-02-C:0039-12-02-B:DEFAULT",
            "0039-20-03-C:0039-12-02-A:DEFAULT",
            "0039-20-03-C:0039-12-02-B:DEFAULT",
            "0039-20-07-C:0039-12-02-A:DEFAULT",
            "0039-20-07-C:0039-12-02-B:DEFAULT",
            "0039-20-17-C:0039-12-02-C:DEFAULT",
            "DEFAULT:0039-12-00-A:DEFAULT",
            "DEFAULT:0039-12-01-A:DEFAULT",
            "DEFAULT:0039-12-02-A:DEFAULT",
            "DEFAULT:0039-12-01-B:DEFAULT",
            "DEFAULT:0039-12-02-B:DEFAULT",
            "DEFAULT:0039-12-02-C:DEFAULT",
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
        $this->myDriver->DriverInfo["PhysicalSensors"] = 9;
        $this->myDriver->DriverInfo["VirtualSensors"] = 4;
        $this->fromSetupString($string);
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
        $ret["CPU"] = "Atmel Tiny26";
        $ret["SensorConfig"] = "1-9 Analog or Digital";

        return $ret;
    }

}

?>
