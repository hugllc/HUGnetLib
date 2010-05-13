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
/**
* Class for dealing with current sensors.
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Sensors
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2010 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
* @deprecated since version 0.9.0
*/
class CurrentSensor extends SensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "currentSensor",
        "Type" => "sensor",
    );
    /**
    * Sensor information array
    */
    public $sensors = array(
        0x50 => array(
            "fetBoard" => array(
                "longName" => "FET Board Current Sensor",
                "unitType" => "Current",
                "validUnits" => array('mA', 'A'),
                "defaultUnits" =>  'mA',
                "function" => "fetBoard",
                "storageUnit" => 'mA',
                "unitModes" => array(
                    'mA' => 'raw,diff',
                    'A' => 'raw,diff',
                ),
                "extraText" => array("R in Ohms", "Gain"),
                "extraDefault" => array(0.5, 1),
            ),
            "Controller" => array(
                "longName" => "Controller Board Current Sensor",
                "unitType" => "Current",
                "validUnits" => array('mA', 'A'),
                "defaultUnits" =>  'mA',
                "function" => "fetBoard",
                "storageUnit" => 'mA',
                "unitModes" => array(
                    'mA' => 'raw,diff',
                    'A' => 'raw,diff',
                ),
                "extraText" => array("R in Ohms", "Gain"),
                "extraDefault" => array(0.5, 7),
            ),
        ),
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
    * @param int   $T The time constant
    *
    * @return float The current sensed
    */
    function getCurrent($A, $R, $G, $T)
    {
        $denom = $this->s * $T * $this->Tf * $this->Am * $G * $R;
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
    * @param float $val    The incoming value
    * @param array $sensor The sensor setup array
    * @param int   $TC     The time constant
    * @param mixed $extra  Extra parameters for the sensor
    *
    * @return float Current rounded to 1 place
    */
    function fetBoard($val, $sensor, $TC, $extra=null)
    {
        $R = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
        $G = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
        $A = $this->getCurrent($val, $R, $G, $TC);
        return round($A * 1000, 1);
    }

}


?>
