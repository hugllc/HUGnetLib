<?php
/**
 * Sensor driver for voltage sensors.
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
 * @subpackage SensorBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/DeviceSensorBase.php";

/**
* class for dealing with resistive sensors.
*
* @category   Libraries
* @package    HUGnetLib
* @subpackage SensorBase
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2011 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*
* @SuppressWarnings(PHPMD.ShortVariable)
*/
abstract class VoltageDeviceSensorBase extends DeviceSensorBase
{
    /** @var array This is the default values for the data */
    protected $default = array(
        "location" => "",                // The location of the sensors
        "id" => null,                    // The id of the sensor.  This is the value
                                         // Stored in the device  It will be an int
        "type" => "",                    // The type of the sensors
        "dataType" => UnitsBase::TYPE_RAW,     // The datatype of each sensor
        "extra" => array(),              // Extra input for crunching numbers
        "units" => "",                   // The units to put the data into by default
        "bound" => false,                // This says if this sensor is changeable
        "rawCalibration" => "",          // The raw calibration string
        "timeConstant" => 1,             // The time constant
        "Am" => 1023,                    // The maximum value for the AtoD convertor
        "Tf" => 65536,                   // The Tf value
        "D" => 65536,                    // The D value
        "s" => 64,                       // The s value
        "Vcc" => 5,                      // The Vcc value
        "filter" => array(),             // Information on the output filter
    );

    /**
    * This returns the voltage on the upper side of a voltage divider if the
    * AtoD input is in the middle of the divider
    *
    * @param int   $A    The incoming value
    * @param float $R1   The resistor to the voltage
    * @param float $R2   The resistor to ground
    * @param float $Vref The voltage reveference
    *
    * @return float Voltage rounded to 4 places
    */
    protected function getDividerVoltage($A, $R1, $R2, $Vref = null)
    {
        // If we get null we should return it.
        if (is_null($A)) {
            return null;
        }
        // Set the Vref it is not set
        if (empty($Vref)) {
            $Vref = $this->Vcc;
        }
        $denom = $this->s * $this->timeConstant * $this->Tf * $this->Am * $R2;
        if ($denom == 0) {
            return 0.0;
        }
        $numer = $A * $this->D * $Vref * ($R1 + $R2);
        $Read = $numer/$denom;
        return round($Read, 4);
    }
    /**
    * This returns the voltage that the port is seeing
    *
    * @param int   $A    The AtoD reading
    * @param float $Vref The voltage reference
    *
    * @return The units for a particular sensor type
    */
    protected function getVoltage($A, $Vref)
    {
        if (is_null($A)) {
            return null;
        }
        if (is_null($Vref)) {
            $Vref = $this->Vcc;
        }
        $denom = $this->timeConstant * $this->Tf * $this->Am * $this->s;
        if ($denom == 0) {
            return 0.0;
        }
        $num = $A * $this->D * $Vref;

        $volts = $num / $denom;
        return round($volts, 4);
    }


    /**
    * Volgate for the FET board voltage dividers
    *
    * @param float $val The incoming value
    *
    * @return float Voltage rounded to 4 places
    */
    protected function indirect($val)
    {
        $R1   = $this->getExtra(0);
        $R2   = $this->getExtra(1);
        $Vref = $this->getExtra(2);
        $V    = $this->getDividerVoltage($val, $R1, $R2, $Vref);
        if ($V < 0) {
            return null;
        }
        return $V;
    }

    /**
    * This sensor returns us 10mV / % humidity
    *
    * @param float $A The incoming value
    *
    * @return float Relative Humidity rounded to 4 places
    */
    protected function direct($A)
    {
        $Vref = $this->getExtra(0);
        $V    = $this->getVoltage($A, $Vref);
        if ($V < 0) {
            return null;
        }
        if ($V > $Vref) {
            return null;
        }
        return $V;
    }

    /**
    * This will work with sensors that are linear and bounded
    *
    * Basically if we have a sensor that is linear and the ends
    * of the line are specified (max1,max2) and (min1,min2) then this
    * is the routine for you.
    *
    * Take the case of a pressure sensor.  We are give that at Vmax the
    * pressure is Pmax and at Vmin the pressure is Vmin.  That gives us
    * the boundries of the line.  The pressure has to be between Pmax and Pmin
    * and the voltage has to be between Vmax and Vmin.  If it is not null
    * is returned.
    *
    * Given the formula I am using, P MUST be in bounds.
    *
    * @param float $A The incoming value
    *
    * @return output rounded to 4 places
    */
    protected function linearBounded($A)
    {
        if (is_null($A)) {
            return null;
        }
        $Vmin = $this->getExtra(0);
        $Vmax = $this->getExtra(1);
        $Pmin = $this->getExtra(2);
        $Pmax = $this->getExtra(3);
        $Vref = $this->getExtra(4);
        if ($Vmax == $Vmin) {
            return null;
        }
        $V    = $this->getVoltage($A, $Vref);
        if ($V > $Vmax) {
            return null;
        }
        if ($V < $Vmin) {
            return null;
        }
        $m = ($Pmax - $Pmin) / ($Vmax - $Vmin);
        $b = $Pmax - ($m * $Vmax);
        $P = ($m * $V) + $b;
        $P = round($P, 4);
        return $P;
    }

    /**
    * This will work with sensors that are linear and bounded
    *
    * Basically if we have a sensor that is linear and the ends
    * of the line are specified (max1,max2) and (min1,min2) then this
    * is the routine for you.
    *
    * Take the case of a pressure sensor.  We are give that at Vmax the
    * pressure is Pmax and at Vmin the pressure is Vmin.  That gives us
    * the boundries of the line.  The pressure has to be between Pmax and Pmin
    * and the voltage has to be between Vmax and Vmin.  If it is not null
    * is returned.
    *
    * Given the formula I am using, P MUST be in bounds.
    *
    * @param float $A The incoming value
    *
    * @return output to 4 places
    */
    protected function linearBoundedIndirect($A)
    {
        if (is_null($A)) {
            return null;
        }
        $R1   = $this->getExtra(0);
        $R2   = $this->getExtra(1);
        $Vmin = $this->getExtra(2);
        $Vmax = $this->getExtra(3);
        $Pmin = $this->getExtra(4);
        $Pmax = $this->getExtra(5);
        $Vref = $this->getExtra(6);
        if ($Vmax == $Vmin) {
            return null;
        }
        $V    = $this->getDividerVoltage($A, $R1, $R2, $Vref);
        if ($V > $Vmax) {
            return null;
        }
        if ($V < $Vmin) {
            return null;
        }
        $m = ($Pmax - $Pmin) / ($Vmax - $Vmin);
        $b = $Pmax - ($m * $Vmax);
        $P = ($m * $V) + $b;
        $P = round($P, 4);
        return $P;
    }

}

?>
