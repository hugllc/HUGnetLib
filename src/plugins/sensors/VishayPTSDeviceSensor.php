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
class VishayPTSDeviceSensor extends ResistiveDeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Vishay Platinum Temperature Sensor RTD",
        "Type" => "sensor",
        "Class" => "VishayPTSDeviceSensor",
        "Flags" => array("04"),
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(4);
    /** @var object These are the valid values for type */
    protected $typeValues = array("VishayPTS");

    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Vishay Platinum Temperature Sensor RTD",
        "unitType" => "Temperature",
        "storageUnit" => '&#176;C',
        "storageType" => UnitsBase::TYPE_RAW,  // This is the dataType as stored
        "extraText" => array("Bias Resistor (kOhms)"),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5),
        "extraDefault" => array(2.21),
        "maxDecimals" => 8,
    );
    /** @var array The table for IMC Sensors */
    protected $valueTable = array(
        "78.32" => -55, "80.31" => -50, "82.29" => -45,
        "84.27" => -40, "86.25" => -35, "88.22" => -30,
        "90.19" => -25, "92.16" => -20, "94.12" => -15,
        "96.09" => -10, "98.04" => -5, "100.00" => 0,
        "101.95" => 5, "103.90" => 10, "105.85" => 15,
        "107.79" => 20, "109.73" => 25, "111.67" => 30,
        "113.61" => 35, "115.54" => 40, "117.47" => 45,
        "119.40" => 50, "121.32" => 55, "123.24" => 60,
        "125.16" => 65, "127.08" => 70, "128.99" => 75,
        "130.90" => 80, "132.80" => 85, "134.71" => 90,
        "136.61" => 95, "138.51" => 100, "140.40" => 105,
        "142.29" => 110, "144.18" => 115, "146.07" => 120,
        "147.95" => 125, "149.83" => 130, "151.71" => 135,
        "153.58" => 140, "155.46" => 145, "157.33" => 150,
        "159.19" => 155,
    );
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 4;
        $this->default["type"] = "VishayPTS";
        parent::__construct($data, $device);
        // This takes care of The older sensors with the 100k bias resistor
    }
    /**
    * Changes a raw reading into a output value
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
        $A = (int)$A;
        if (($A & 0x800000) == 0x800000) {
            /* This is a negative number */
            $A = -(pow(2, 24) - $A);
        }
        $A = abs($A);
        $ohms = $this->getResistanceRTD($A, $Bias);
        return $ohms;
    }
    /**
    * Converts a raw AtoD reading into resistance
    *
    * This function takes in the AtoD value and returns the calculated
    * resistance of the sensor.  It does this using a fairly complex
    * formula.
    *
    * @param int   $A    Integer The AtoD reading
    * @param float $Bias Float The bias resistance in kOhms
    *
    * @return The resistance corresponding to the values given in Ohms
    */
    protected function getResistanceRTD($A, $Bias)
    {
        $Am = pow(2, 23);
        if ($A == $Am) {
            return null;
        }
        $R = (float)(($A * $Bias * 1000) / ($Am - $A));
        return round($R, $this->maxDecimals);
    }
    /**
    * This function should be called with the values set for the specific
    * thermistor that is used.
    *
    * @param float $R The current resistance of the thermistor in k ohms
    *
    * @return float The Temperature in degrees C
    */
    public function tableInterpolate($R)
    {
        $max = max(array_keys($this->valueTable));
        $min = min(array_keys($this->valueTable));
        if (($R < $min) || ($R > $max)) {
            return null;
        }
        $table = &$this->valueTable;
        foreach (array_keys($table) as $ohm) {
            $last = $ohm;
            if ((float)$ohm > $R) {
                break;
            }
            $prev = $ohm;
        }
        $T     = $table[$prev];
        $fract = ($prev - $R) / ($prev - $last);
        $diff  = $fract * ($table[$last] - $table[$prev]);
        return (float)($T + $diff);
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>
