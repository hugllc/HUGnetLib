<?php
/**
 *   Sensor driver for current sensors
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *   
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *   
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *   
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Sensors
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$    
 *
 */
if (!class_exists('currentSensor')) {
    $this->add_generic(array("Name" => "currentSensor", "Type" => "sensor", "Class" => "currentSensor"));

    /**
     * Class for dealing with current sensors.
    */
    class currentSensor extends sensor_base
    {
    
        var $sensors = array(
            0x50 => array(
                "FETBoard" => array(
                    "longName" => "FET Board Current Sensor",
                    "unitType" => "Current",
                    "validUnits" => array('mA', 'A'),
                    "defaultUnits" =>  'mA',
                    "function" => "FETBoard",
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
                    "function" => "FETBoard",
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
         * https://dev.hugllc.com/index.php/Project:HUGnet_Current_Sensors Current Sensors }
         *
         * @param int $A The raw AtoD reading
         * @param float $R The resistance of the current sensing resistor
         * @param float $G The gain of the circuit
         * @param int $T The time constant 
         * @return float The current sensed
         */
        function getCurrent($A, $R, $G, $T) 
        {
            $denom = $this->s * $T * $this->Tf * $this->Am * $G * $R;
            if ($denom == 0) return 0;
            $numer = $A * $this->D * $this->Vcc;
    
            $Read = $numer/$denom;
            return $Read ;
        }
    
        /**
         *  This is specifically for the current sensor in the FET board.
         *
         * @param float $val The incoming value
         * @param array $sensor The sensor setup array
         * @param int $TC The time constant
         * @param mixed $extra Extra parameters for the sensor
         * @return float Current rounded to 1 place
         */
        function FETBoard($val, $sensor, $TC, $extra=NULL) {
            $R = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $G = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $A = $this->getCurrent($val, $R, $G, $TC);
            return round($A * 1000, 1);
        }    
    
    }
}



?>
