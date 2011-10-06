<?php
/**
 * Sensor driver for current sensors
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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/DeviceSensorBase.php";
/**
 * Class for dealing with current sensors.
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
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class CurrentDeviceSensorBase extends DeviceSensorBase
{
    /** @var array This is the default values for the data */
    protected $default = array(
        "location" => "",                // The location of the sensors
        "id" => null,                    // The id of the sensor.  This is the value
                                         // Stored in the device  It will be an int
        "type" => "",                    // The type of the sensors
        "dataType" => UnitsBase::TYPE_RAW,       // The datatype of each sensor
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
    * This takes in a raw AtoD reading and returns the current.
    *
    * This is further documented at: {@link
    * https://dev.hugllc.com/index.php/Project:HUGnet_Current_Sensors Current
    * Sensors }
    *
    * @param int   $A The raw AtoD reading
    * @param float $R The resistance of the current sensing resistor
    * @param float $G The gain of the circuit
    *
    * @return float The current sensed
    */
    protected function getCurrent($A, $R, $G)
    {
        $denom = $this->s * $this->timeConstant * $this->Tf * $this->Am * $G * $R;
        if ($denom == 0) {
            return 0.0;
        }
        $numer = $A * $this->D * $this->Vcc;

        $Read = $numer/$denom;
        return round($Read, 4);
    }

    /**
    *  This is specifically for the current sensor in the FET board.
    *
    * @param float $val The incoming value
    *
    * @return float Current in amps rounded to 1 place
    */
    protected function direct($val)
    {
        if (is_null($val)) {
            return null;
        }
        $R = $this->getExtra(0);
        $G = $this->getExtra(1);
        $A = $this->getCurrent($val, $R, $G);
        return round($A * 1000, 1);
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
        $Imin = $this->getExtra(0);
        $Imax = $this->getExtra(1);
        $Pmin = $this->getExtra(2);
        $Pmax = $this->getExtra(3);
        $Rsense = $this->getExtra(4);
        $Gain = $this->getExtra(5);
        if ($Imax == $Imin) {
            return null;
        }
        $I = ($this->getCurrent($A, $Rsense, $Gain) * 1000);
        if ($I > $Imax) {
            return null;
        }
        if ($I < $Imin) {
            return null;
        }
        $m = ($Pmax - $Pmin) / ($Imax - $Imin);
        $b = $Pmax - ($m * $Imax);
        $P = ($m * $I) + $b;
        $P = round($P, 4);
        return $P;
    }

}


?>
