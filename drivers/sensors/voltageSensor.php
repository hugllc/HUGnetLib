<?php
/**
 * Sensor driver for voltage sensors.
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
if (!class_exists('voltageSensor')) {

    /**
     * class for dealing with resistive sensors.
     */
    class voltageSensor extends sensor_base
    {
        /**
            This defines all of the sensors that this driver deals with...
         */
        public $sensors = array(
            0x10 => array(
                'CHSMSS' => array(
                    "longName" => "TDK CHS-MSS ",
                    "unitType" => "Humidity",
                    "validUnits" => array('%'),
                    "defaultUnits" =>  '%',
                    "function" => "CHSMSS",
                    "storageUnit" => '%',
                    "extraText" => "AtoD Ref Voltage",
                    "extraDefault" => 1.1,
                    "unitModes" => array(
                        '%' => 'raw,diff',
                    ),
                ),
            ),
            0x40 => array(
                "FETBoard" => array(
                    "longName" => "FET Board Voltage Sensor",
                    "unitType" => "Voltage",
                    "validUnits" => array('V', 'mV'),
                    "defaultUnits" =>  'V',
                    "function" => "FETBoard",
                    "storageUnit" => 'V',
                    "unitModes" => array(
                        'mV' => 'raw,diff',
                        'V' => 'raw,diff',
                    ),
                    "extraText" => array("R1 in kOhms", "R2 in kOhms"),
                    "extraDefault" => array(150, 10),
                ),
                "Controller" => array(
                    "longName" => "Controller Board Voltage Sensor",
                    "unitType" => "Voltage",
                    "validUnits" => array('V', 'mV'),
                    "defaultUnits" =>  'V',
                    "function" => "FETBoard",
                    "storageUnit" => 'V',
                    "unitModes" => array(
                        'mV' => 'raw,diff',
                        'V' => 'raw,diff',
                    ),
                    "extraText" => array("R1 in kOhms", "R2 in kOhms"),
                    "extraDefault" => array(180, 27),
                ),
            ),
        );
    
        function getDividerVoltage($A, $R1, $R2, $T)
        {
                $denom = $this->s * $T * $this->Tf * $this->Am * $R2;
                if ($denom == 0) return 0.0;
                $numer = $A * $this->D * $this->Vcc * ($R1 + $R2);

                $Read = $numer/$denom;
                return round($Read, 4);
        }

        function FETBoard($val, $sensor, $TC, $extra=null) {
            $R1 = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $R2 = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $V = $this->getDividerVoltage($val, $R1, $R2, $TC);
            if ($V < 0) $V = null;
            $V = round($V, 4);
            return $V;
        }    
    
        /**
         * @public
         * Gets the units for a sensor
         * @param $type Int The type of sensor.
         * @return The units for a particular sensor type
        
         * @par Introduction
         */
        function getVoltage($A, $T, $Vref) 
        {
            if (is_null($A)) return null;
            if (is_null($Vref)) return null;
            $denom = $T * $this->Tf * $this->Am * $this->s;
            if ($denom == 0) return 0.0;
            $num = $A * $this->D * $Vref;
            
            $volts = $num / $denom;
            return round($volts, 4);
        }
    
    
        /**
            This sensor returns us 10mV / % humidity
         */
        function CHSMSS($A, $sensor, $T, $extra) {
            if (is_null($A)) return null;
            $Vref = (empty($extra)) ? $sensor['extraDefault'] : $extra;            
            $volts = $this->getVoltage($A, $T, (float) $Vref);
            $humidity = $volts * 100;
            if ($humidity < 0) return null;
            $humidity = round($humidity, 4);
            return $humidity;
        }
        
    }
}

if (method_exists($this, "add_generic")) {
    $this->add_generic(array("Name" => "voltageSensor", "Type" => "sensor", "Class" => "voltageSensor"));
}


?>
