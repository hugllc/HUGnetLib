<?php
/**
 * Sensor driver for pulse counters
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
/**
    @brief class for dealing with resistive sensors.
*/
if (!class_exists('pulseSensor')) {
    
    class pulseSensor extends sensor_base
    {
    
        /**
            This defines all of the sensors that this driver deals with...
         */
        // PPM = Pulses per minute
        public $sensors = array(
            0x70 => array(
                'generic' => array(
                    "longName" => "Generic Pulse Counter",
                    "unitType" => "Pulses",
                    "validUnits" => array('PPM', 'counts'),
                    "storageUnit" =>  'PPM',
                    "function" => "getPPM",
                    "unitModes" => array(
                        'PPM' => 'diff',
                        'counts' => 'raw,diff',
                    ),
                    "checkFunction" => "pulseCheck",

                ),
                'genericRevolver' => array(
                    "longName" => "Generic Revolving Thingy",
                    "unitType" => "Pulses",
                    "validUnits" => array('PPM', 'counts', 'RPM'),
                    "storageUnit" =>  'PPM',
                    "function" => "getPPM",
                    "unitModes" => array(
                        'PPM' => 'diff',
                        'RPM' => 'diff',
                        'counts' => 'raw,diff',
                    ),
                    "extraText" => "Counts per Revolution",
                    "extraDefault" => 1,
                ),
                'maximumAnemometer' => array(
                    "longName" => "Maximum Inc type Hall Effect Anemometer",
                    "unitType" => "Speed",
                    "validUnits" => array('MPH'),
                    "storageUnit" =>  'MPH',
                    "unitModes" => array(
                        'MPH' => 'diff',
                    ),
                    "function" => "maximumAnemometer",
//                    "checkFunction" => "pulseCheck",
                ),
                'maximumRainGauge' => array(
                    "longName" => "Maximum Inc rain gauge",
                    "unitType" => "Rain",
                    "validUnits" => array('&#34;'),
                    "storageUnit" =>  '&#34;',
                    "unitModes" => array(
                        '&#34;' => 'diff',
                    ),
                    "mult" => 0.01,
//                    "checkFunction" => "pulseCheck",
                    "doTotal" => true,
                ),
                'bravo3motion' => array(
                    "longName" => "DSC Bravo 3 Motion Sensor",
                    "unitType" => "Pulses",
                    "validUnits" => array('counts', 'PPM'),
                    "storageUnit" =>  'counts',
                    "unitModes" => array(
                        'counts' => 'diff,raw',
                        'PPM' => 'diff',
                    ),
                    "checkFunction" => "pulseCheck",
                    "doTotal" => true,
                ),
                'wattnode' => array(
                    "longName" => "CCS WattNode Pulse Output Power Meter",
                    "unitType" => "Power",
                    "validUnits" => array('kWh', 'Wh', 'kW', 'W'),
                    "storageUnit" =>  'kWh',
                    "function" => "WattNode",
                    "unitModes" => array(
                        'kWh' => 'raw,diff',
                        'Wh' => 'raw,diff',
                        'kW' => 'diff',
                        'W' => 'diff',
                    ),
                    "extraText" => "Watt Hours / Pulse",
                    "extraDefault" => 5,
                    "doTotal" => true,
//                     "checkFunction" => "pulseCheck",
                ),
            ),
        );
    

        /**
            This implements the function:
                Freq = (Speed + 0.1)/1.6965
            or:
                Speed = (Freq * 1.6965) - 0.1
            
            Freq = Pulses/Time
         */
        function maximumAnemometer($val, $sensor, $TC, $extra, $deltaT=null) {
            if (empty($deltaT)) return null;
            if ($val <= 0) return 0;
            $speed = (($val / $deltaT) * 1.6965) - 0.1;
            if ($speed < 0) $speed = 0;
            return $speed;
        }
        
        function pulseCheck($value, $sensor, $units, $dType) {
            if ($value < 0) return false;
            return true;
        }

        function WattNode($val, $sensor, $TC, $extra, $deltaT=null) {
            $Wh = $val * $extra;
            if ($Wh < 0) return null;
            return $Wh / 1000;
        }

        function getPPM($val, $sensor, $TC, $extra, $deltaT) {
            if ($deltaT <= 0) return null;
            $ppm = ($val / $deltaT) * 60;
            if ($ppm < 0) return null;
            return $ppm;
        }

    }
    
}
if (method_exists($this, "addGeneric")) {
    $this->addGeneric(array("Name" => "pulseSensor", "Type" => "sensor", "Class" => "pulseSensor"));
}    


?>
