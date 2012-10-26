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
class BCTherm2322640DeviceSensor extends ResistiveDeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "BC Components Thermistor #2322640",
        "Type" => "sensor",
        "Class" => "BCTherm2322640DeviceSensor",
        "Flags" => array("00", "02", "02:BCTherm2322640"),
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(0, 2);
    /** @var object These are the valid values for type */
    protected $typeValues = array("BCTherm2322640");

    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "BC Components Thermistor #2322640",
        "unitType" => "Temperature",
        "storageUnit" => '&#176;C',
        "storageType" => UnitsBase::TYPE_RAW,  // This is the dataType as stored
        "extraText" => array(
            "Bias Resistor (kOhms)",
            "Value @25&#176;C (kOhms)"
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, 5),
        "extraDefault" => array(10, 10),
        "maxDecimals" => 2,
    );
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 2;
        $this->default["type"] = "BCTherm2322640";
        parent::__construct($data, $device);
        // This takes care of The older sensors with the 100k bias resistor
        if ($this->id == 0x00) {
            $this->fixed["extraDefault"] = array(100, 10);
        }
    }

    /**
    * Converts resistance to temperature for BC Components #2322 640 66103
    * 10K thermistor.
    *
    * <b>BC Components #2322 640 series</b>
    *
    * This function implements the formula in $this->BCThermInterpolate
    * for a is from BCcomponents PDF file for thermistor
    * #2322 640 series datasheet on page 6.
    *
    * <b>Thermistors available:</b>
    *
    * -# 10K Ohm BC Components #2322 640 66103. This is defined as thermistor
    * 0 in the type code.
    *     - R0 10
    *     - A 3.354016e-3
    *     - B 2.569355e-4
    *     - C 2.626311e-6
    *     - D 0.675278e-7
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
        $Bias      = $this->getExtra(0);
        $baseTherm = $this->getExtra(1);
        $ohms      = $this->getResistance($A, $Bias);
        $T         = $this->_BcTherm2322640Interpolate(
            $ohms,
            $baseTherm,
            3.354016e-3,
            2.569355e-4,
            2.626311e-6,
            0.675278e-7
        );

        if (is_null($T)) {
            return null;
        }
        if ($T > 150) {
            return null;
        }
        if ($T < -40) {
            return null;
        }
        $T = round($T, 4);
        return $T;
    }

    /**
    * This formula is from BCcomponents PDF file for the
    * # 2322 640 thermistor series on page 6.  See the data sheet for
    * more information.
    *
    * This function should be called with the values set for the specific
    * thermistor that is used.  See eDEFAULT::Therm0Interpolate as an example.
    *
    * @param float $R  The current resistance of the thermistor in kOhms
    * @param float $R0 The resistance of the thermistor at 25C in kOhms
    * @param float $A  Thermistor Constant A (From datasheet)
    * @param float $B  Thermistor Constant B (From datasheet)
    * @param float $C  Thermistor Constant C (From datasheet)
    * @param float $D  Thermistor Constant D (From datasheet)
    *
    * @return float The Temperature in degrees C
    */
    private function _bcTherm2322640Interpolate($R, $R0, $A, $B, $C, $D)
    {
        // This gets out bad values
        if ($R <= 0) {
            return null;
        }
        if ($R0 == 0) {
            return null;
        }
        $T  = $A;
        $T += $B * log($R/$R0);
        $T += $C * pow(log($R/$R0), 2);
        $T += $D * pow(log($R/$R0), 3);
        $T  = pow($T, -1);

        $T -= 273.15;
        return($T);
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>
