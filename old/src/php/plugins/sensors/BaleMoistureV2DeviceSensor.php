<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensorss
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/sensors/ResistiveDeviceSensorBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class BaleMoistureV2DeviceSensor extends ResistiveDeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Bale Moisture Sensor Version 2",
        "Type" => "sensor",
        "Class" => "BaleMoistureV2DeviceSensor",
        "Flags" => array("03", "03:BaleMoistureV2"),
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(3);
    /** @var object These are the valid values for type */
    protected $typeValues = array("BaleMoistureV2");

    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Bale Moisture Sensor Version 2",
        "unitType" => "Resistance",
        "storageUnit" => 'kOhms',
        "storageType" => UnitsBase::TYPE_RAW,  // This is the dataType as stored
        "extraText" => array(
            "Bias Resistor (kOhms)",
            "Red Zone (kOhms)",
            "Yellow Zone (kOhms)",
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(10, 10, 10),
        "extraDefault" => array(1000, 10, 1000),
        "maxDecimals" => 3,
    );
    /** @var float Moisture red zone % */
    protected $Mr = 18;
    /** @var float Moisture yellow zone % */
    protected $My = 12;
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 0x03;
        $this->default["type"] = "BaleMoistureV2";
        parent::__construct($data, $device);
    }

    /**
    * This function calculates the open percentage based on the resistance seen.
    *
    * This is for V2 of the moisture sensor.
    *
    * This is incomplete.  It reads out in resistance.
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        $Bias = $this->getExtra(0);
        $Rr   = $this->getExtra(1);
        $Ry   = $this->getExtra(2);
        if ($Ry <= $Rr) {
            return null;
        }
        $R = $this->getResistance($A, $Bias);

        return round($R, $this->decimals);
    }

    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>