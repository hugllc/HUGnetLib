<?php
/**
 * Sensor driver for wind direction sensors
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
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/sensors/PulseDeviceSensorBase.php";
/**
* This class deals with wind direction sensors.
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Sensors
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2010 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class WattNodeDeviceSensor extends PulseDeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "WattNodeDeviceSensor",
        "Type" => "sensor",
        "Class" => "WattNodeDeviceSensor",
        "Flags" => array("70:wattnode"),
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(0x70);
    /** @var object These are the valid values for type */
    protected $typeValues = array("wattnode");
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "CCS WattNode Pulse Output Power Meter",
        "unitType" => "Power",
        "storageUnit" => 'kWh',
        "extraText" => array(
            "Watt Hours / Pulse",
        ),
        "extraDefault" => array(5),
    );
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 0x70;
        $this->default["type"] = "wattnode";
        parent::__construct($data, $device);
    }

    /**
    * This function returns the output in RPM
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    *
    * @return float Revolutions per minute
    */
    function getReading($A, $deltaT = 0)
    {
        $extra = $this->getExtra(0);
        $Wh = $A * $extra;
        if ($Wh < 0) {
            return null;
        }
        return round($Wh / 1000, 4);
    }

}

?>
