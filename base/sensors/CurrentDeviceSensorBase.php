<?php
/**
 * Sensor driver for current sensors
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
require_once dirname(__FILE__)."/../../base/DeviceSensorBase.php";
/**
* Class for dealing with current sensors.
*
* @category   Plugins
* @package    HUGnetLib
* @subpackage Sensors
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2010 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
abstract class CurrentDeviceSensorBase extends DeviceSensorBase
{
    /** @var array This is the default values for the data */
    protected $default = array(
        "location" => "",                // The location of the sensors
        "id" => null,                    // The id of the sensor.  This is the value
                                         // Stored in the device  It will be an int
        "type" => "",                    // The type of the sensors
        "dataType" => "raw",             // The datatype of each sensor
        "extra" => array(),              // Extra input for crunching numbers
        "rawCalibration" => "",          // The raw calibration string
        "timeConstant" => 1,             // The time constant
        "Am" => 1023,                    // The maximum value for the AtoD convertor
        "Tf" => 65536,                   // The Tf value
        "D" => 65536,                    // The D value
        "s" => 64,                       // The s value
        "Vcc" => 5,                      // The Vcc value
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

}


?>
