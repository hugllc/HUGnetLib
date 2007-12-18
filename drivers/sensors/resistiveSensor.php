<?php
/**
 * Sensor driver for resistive sensors
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
if (!class_exists('resistiveSensor')) {

    /**
     * class for dealing with resistive sensors.
     *
     *  This class deals with all resistive sensors.  This includes thermistors,
     *  resistive door sensors, and other resistors.
     *
      */
    class resistiveSensor extends sensor_base
    {
        /** @var float Moisture red zone % */
        private $Mr = 18;
        /** @var float Moisture yellow zone % */
        private $My = 12;
        
        /**
         * This is the array of sensor information.  
         *
         * The BC Components thermistor
         * is in here twice for historical compatability.  There are some endpoints
         * that still have sensor type 0 with 100k bias resistors.  This first entry
         * is to take care of those, even though this new system is flexible enough
         * to deal with the change in bias resistors on the same sensor type.  The
         * two entries should be kept identical except for the first extraDefault, which
         * should be 100 under the 0x00 type and 10 under the 0x02 type.
         */
        public $sensors = array(
            0x00 => array(
                'BCTherm2322640' => array(
                    "longName" => "BC Components Thermistor #2322640 ",
                    "unitType" => "Temperature",
                    "validUnits" => array('&#176;F', '&#176;C'),
                    "function" => "BCTherm2381_640_66103",
                    "storageUnit" => '&#176;C',
                    "unitModes" => array(
                        '&#176;C' => 'raw,diff',                        
                        '&#176;F' => 'raw,diff',
                    ),
                    "extraText" => array("Bias Resistor in k Ohms", "Thermistor Value @25C"),
                    "extraDefault" => array(100, 10),
                ),
            ),
            0x01 => array(
                'BaleMoistureV1' => array(
                    "longName" => "Bale Moisture V1",
                    "unitType" => "Bale Moisture",
                    "validUnits" => array('%'),
                    "function" => "getMoistureV1",
                    "storageUnit" => '%',
                    "unitModes" => array(
                        '%' => 'raw,diff',                        
                    ),
                    "extraText" => array("Bias Resistor in k Ohms", "Red Zone resistance in Ohms", "Yellow Zone resistance in Ohms"),
                    "extraDefault" => array(100, 10000, 100000),
                ),
            ),
            0x02 => array(
                'BCTherm2322640' => array(
                    "longName" => "BC Components Thermistor #2322640 ",
                    "unitType" => "Temperature",
                    "validUnits" => array('&#176;F', '&#176;C'),
                    "function" => "BCTherm2381_640_66103",
                    "storageUnit" => '&#176;C',
                    "unitModes" => array(
                        '&#176;C' => 'raw,diff',                        
                        '&#176;F' => 'raw,diff',
                    ),
                    "extraText" => array("Bias Resistor in k Ohms", "Thermistor Value @25C"),
                    "extraDefault" => array(10, 10),
                ),
                'resisDoor' => array(
                    "longName" => "Resistive Door Sensor",
                    "unitType" => "Door",
                    "validUnits" => array('%'),
                    "function" => "resisDoor",
                    "storageUnit" => '%',
                    "unitModes" => array(
                        '%' => 'raw',                        
                    ),
                    "extraText" => array("Bias Resistor in kOhms", "Fixed Resistor in kOhms", "Switched Resistor in kOhms"),
                    "extraDefault" => array(10,10,10),
                ),
            ),
            0x03 => array(
                'BaleMoistureV2' => array(
                    "longName" => "Bale Moisture V2",
                    "unitType" => "Bale Moisture",
                    "validUnits" => array('%'),
                    "function" => "getMoistureV2",
                    "storageUnit" => '%',
                    "unitModes" => array(
                        '%' => 'raw,diff',                        
                    ),
                    "extraText" => array("Bias Resistor in k Ohms", "Red Zone resistance in k Ohms", "Yellow Zone resistance in k Ohms"),
                    "extraDefault" => array(1000, 10, 1000),
                ),
            ),
        );
    
        /**
         * Converts a raw AtoD reading into resistance
         *
         * This function takes in the AtoD value and returns the calculated
         * resistance of the sensor.  It does this using a fairly complex
         * formula.  This formula and how it was derived is detailed in 
         *
         * @param int   $A    Integer The AtoD reading
         * @param int   $TC   Integer The time constant used to get the reading
         * @param float $Bias Float The bias resistance in kOhms
         * @param int   $Tf   See {@link sensor_base::$Tf}
         * @param int   $D    See {@link sensor_base::$D}
         * @param int   $s    See {@link sensor_base::$s}
         * @param int   $Am   See {@link sensor_base::$Am}
         *
         * @return The resistance corresponding to the values given
         */
        function getResistance($A, $TC, $Bias, $Tf = null, $D = null, $s = null, $Am=null)
        {
            if (is_null($Tf)) $Tf = $this->Tf;    
            if (is_null($D)) $D = $this->D;    
            if (is_null($s)) $s = $this->s;    
            if (is_null($Am)) $Am = $this->Am;    
        
            if ($D == 0) return 0.0;
            $Den = ((($Am*$s*$TC*$Tf)/$D) - $A);
            if (($Den == 0) || !is_numeric($Den)) $Den = 1.0;
            $R = (float)($A*$Bias)/$Den;
            return round($R, 4);
        }
        
        /**
         * Converts resistance to temperature for BC Components #2322 640 66103 10K thermistor.
         *
         * <b>BC Components #2322 640 series</b>
         *
         * This function implements the formula in $this->BCThermInterpolate
         * for a is from BCcomponents PDF file for thermistor
         * #2322 640 series datasheet on page 6.  
         *
         * <b>Thermistors available:</b>
         * 
         * -# 10K Ohm BC Components #2322 640 66103. This is defined as thermistor 0 in the type code.                
         *     - R0 10
         *     - A 3.354016e-3
         *     - B 2.569355e-4
         *     - C 2.626311e-6
         *     - D 0.675278e-7
         *
         * @param int   $A      Output of the A to D converter
         * @param array $sensor The sensor information array
         * @param int   $TC     The time constant
         * @param mixed $extra  Extra sensor information
         * @param float $deltaT The time delta in seconds between this record
         *                      and the last one
         *
         * @return float The temperature in degrees C.    
         */
        function BCTherm2381_640_66103($A, $sensor, $TC, $extra, $deltaT=null) 
        {
            if (!is_array($extra)) $extra = array();
            $Bias      = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $baseTherm = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $ohms      = $this->getResistance($A, $TC, $Bias);
            $T         = $this->_BCTherm2322640Interpolate($ohms, $baseTherm, 3.354016e-3, 2.569355e-4, 2.626311e-6, 0.675278e-7);

            if (is_null($T)) return null;
            if ($T > 150) return null;
            if ($T < -40) return null;
            $T = round ($T, 4);
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
         * @param float $R  The current resistance of the thermistor.
         * @param float $R0 The resistance of the thermistor at 25C
         * @param float $A  Thermistor Constant A (From datasheet)
         * @param float $B  Thermistor Constant B (From datasheet)
         * @param float $C  Thermistor Constant C (From datasheet)
         * @param float $D  Thermistor Constant D (From datasheet)
         *
         * @return float The Temperature in degrees C
         */
        private function _BCTherm2322640Interpolate($R, $R0, $A, $B, $C, $D)
        {
            // This gets out bad values
            if ($R <= 0) return null;
            if ($R0 == 0) return null;
            $T  = $A;
            $T += $B * log($R/$R0);
            $T += $C * pow(log($R/$R0),2);
            $T += $D * pow(log($R/$R0), 3);
            $T  = pow($T, -1);
    
            $T -= 273.15;
            return($T);
        }
    
        /**
         * This function calculates the open percentage based on the resistance seen.
         *
         * This sensor expects the following extras:
         *  0. The bias resistor
         *  1. The fixed resistor
         *  2. The switched resistor
         *
         * @param int   $A      The incoming value
         * @param array $sensor The sensor setup array
         * @param int   $TC     The time constant
         * @param mixed $extra  Extra parameters for the sensor
         *
         * @return float The percentage of time the door is open
         */ 
        function resisDoor($A, $sensor, $TC, $extra) 
        {
            $Bias  = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $Fixed = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            if ($Fixed <= 0) return null;        
            $Switched = (empty($extra[2])) ? $sensor['extraDefault'][2] : $extra[2];
            if ($Switched <= 0) return null;        
            $R  = $this->getResistance($A, $TC, $Bias);
            $R -= $Fixed;
            // Got something wrong here.  We shouldn't have a negative resistance.
            if ($R < 0) return null;
            $perc = ($R / $Switched) * 100;
            // We need to limit this to between 0 and  100.
            // It can't be open more than all the time.
            // It can't be open less than none of the time.
            if (($perc < 0) || ($perc > 100)) return null;
            return round($perc, 2);
        }

        /**
         * This function calculates the open percentage based on the resistance seen.
         *
         * This sensor expects the following extras:
         *  0. The bias resistor
         *  1. The red zone resistance
         *  2. The yellow zone resistance
         *
         * @param float $A      The incoming value
         * @param array $sensor The sensor setup array
         * @param int   $TC     The time constant
         * @param mixed $extra  Extra parameters for the sensor
         *
         * @return float The percentage of time the door is open
         */ 
        function getMoistureV2($A, $sensor, $TC, $extra) 
        {
            $Bias = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $Rr   = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $Ry   = (empty($extra[2])) ? $sensor['extraDefault'][2] : $extra[2];
            if ($Ry <= $Rr) return null;
            $R = $this->getResistance($A, $TC, $Bias, 1, 1, 64);            
            $M = $R;
            return $M;
        }

        /**
         * This function calculates the open percentage based on the resistance seen.
         *
         * This is for V1 of the moisture sensor.  No more of these will be made.
         *
         * This sensor expects the following extras:
         *  0. The bias resistor
         *  1. The red zone resistance
         *  2. The yellow zone resistance
         *
         * It is not well documented.  It seems to contain the formula:
         *  - B = ( My - Mr ) / ( log( Ry ) - log( Rr ) )
         *  - A = Mr - ( B * log( Rr ) )
         *  - M = A + (B * log( R ) );
         * where:
         * - M = Moisture (%)
         * - Mr = Minimum % for red zone (bad)
         * - My = Minimum % for yellow zone (marginal)
         * - Rr = Maximum Ohms for red zone (bad)
         * - Ry = Maximum Ohms for yellow zone (marginal)
         * - A = ???
         * - B = ???
         *
         * I think this formula is based on logrythmic curves with the points
         * (Ry, My) and (Rr, Mr).  Resistance and Moiture have an inverse
         * relationship.
         *
         * @param float $A      The incoming value
         * @param array $sensor The sensor setup array
         * @param int   $TC     The time constant
         * @param mixed $extra  Extra parameters for the sensor
         *
         * @return float The percentage of time the door is open
         */ 
        function getMoistureV1($A, $sensor, $TC, $extra) 
        {
            $Bias = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $Rr   = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $Ry   = (empty($extra[2])) ? $sensor['extraDefault'][2] : $extra[2];
            if ($Ry <= $Rr) return null;
            $R = $this->getResistance($A, 1, $Bias);

            if ($R == 0) return(35.0);
            //$R is coming in k Ohms.  We need Ohms.
            $R   = $R * 1000;
            $num = $this->My - $this->Mr;
            $den = log($Ry) - log($Rr);
            if ($den == 0) return(35.0);
            $B = $num / $den;
            $A = $this->Mr - ($B * log($Rr));
            $M = $A + ($B * log($R));
            
            if ($M > 35) return null;
            if ($M < 0) return null;
            return round($M, 2);
        }
    
    }
}

if (method_exists($this, "addGeneric")) {
    $this->addGeneric(array("Name" => "resistiveSensor", "Type" => "sensor", "Class" => "resistiveSensor"));
}

?>
