<?php
/**
 * Sensor driver for capactive sensors
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
if (!class_exists('capacitiveSensor')) {


    /**
     * class for dealing with capacitive sensors.
     */
    class capacitiveSensor extends sensor_base
    {

        public $sensors = array(
            0x20 => array(
                'generic' => array(
                    "longName" => "Generic Capacitive Sensor",
                    "unitType" => "Capacitance",
                    "validUnits" => array('F', 'uF', 'nF'),
                    "storageUnit" =>  'uF',
                    "function" => "genericCap",
                    "unitModes" => array(
                        'F' => 'raw,diff',
                        'uF' => 'raw,diff',
                        'nF' => 'raw,diff',
                    ),

                ),
            ),
        );
    
        /**
         * This function takes in the AtoD value and returns the calculated
         * capacitance of the sensor.  It does this using a fairly complex
         * formula.  This formula and how it was derived is detailed at
         * {@link https://dev.hugllc.com/index.php/Project:HUGnet_Capacitive_Sensors Capacitive Sensors}
         * 
         * @param int $A The AtoD reading
         * @param int $T The time constant used to get the reading
         * @param float $R The bias resistance in kOhms
         * @return float The resistance corresponding to the values given
         * 
         */
        function getCapacitance($A, $T, $R, $t=1)
        {
            $Den1 = $this->Tf * $T * $this->s * $this->Am;
            if ($Den1 == 0) return(0);
            $insideLN = (1 - (($A * $this->D)/$Den1));
            if ($insideLN < 0) return(0);
            $Den2 = $R * log($insideLN);
            if ($Den2 == 0) return(0);
    
            $C = (-1.0 / $Den2) * $t;
            $C = round($C, 4);
            return($C);
        }    
        
        /**
          */
        function genericCap($val, $sensor, $TC, $extra=null) {
            return $val;
        }
    }
}

if (method_exists($this, "addGeneric")) {
    $this->addGeneric(array("Name" => "capacitiveSensor", "Type" => "sensor", "Class" => "capacitiveSensor"));
}

?>
