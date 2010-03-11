<?php
/**
 * Sensor driver for light sensors.
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
if (!class_exists('lightSensor')) {
    /**
    * This class implements photo sensors.
    *
    * @category   Drivers
    * @package    HUGnetLib
    * @subpackage Sensors
    * @author     Scott Price <prices@hugllc.com>
    * @copyright  2007-2010 Hunt Utilities Group, LLC
    * @copyright  2009 Scott Price
    * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
    * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
    */
    class LightSensor extends SensorBase
    {
        /** @var array Sensor information array */
        public $sensors = array(
            0x30 => array(
                'OSRAM BPW-34' => array(
                    "longName" =>  "OSRAM BPW-34 Photodiode",
                    "unitType" => "Light",
                    "validUnits" => array("W/m^2"),
                    "storageUnit" =>  "W/m^2",
                    "function" => "osramBPW34",
                    "unitModes" => array(
                        'W/m^2' => 'raw,diff',
                   ),

               ),
           ),
        );

        /**
        * This takes the raw AtoD reading and converts it into light.
        *
        * @param int $A  The raw AtoD reading
        * @param int $TC The time constant used to get the reading
        *
        * @return float The light seen in W/m^2
        */
        function getLight($A, $TC)
        {
            /*
            $den = $this->Am * $this->s * $this->*this->Tf * $T;
            if ($den == 0) return(1500);
            $L = (-1500)*$this->D*$A;
            $L = $L / $den;
            $L += 1500;
            */
            /*
            $den = $this->Am * $this->s * $this->Tf * $TC;
            if ($den == 0) return(1500);
            $L = (-1500)*$this->s*$this->D*$A;
            $L = $L / $den;
            $L += 1500;
            */
            // I am not sure why this is here.  It seems to be the
            // wrong formula according to the docs  2006-12-15 SLP
            // This formula is correct.
            $den = $this->Am*$this->s*$this->D;
            if ($den == 0) {
                return(1500.0);
            }
            $L  = (-1500.0)*$A * $this->Tf * $TC;
            $L  = $L / $den;
            $L += 1500.0;

            return round($L, 4);
        }

        /**
        *  This is specifically for the OSRAM BPW34 photodiode
        *
        * This routine doesn't use the extra field.
        *
        * @param float $A      The incoming value
        * @param array $sensor The sensor setup array
        * @param int   $TC     The time constant
        * @param mixed $extra  Extra parameters for the sensor
        *
        * @return float Current rounded to 1 place
        */
        function osramBPW34($A, $sensor, $TC, $extra=null)
        {
/*
            $den = $this->Am*$this->s*$this->D;
            if ($den == 0) {
                return(1500.0);
            }
            $L  = (-1500.0)*$A * $this->Tf * $TC;
            $L  = $L / $den;
            $L += 1500.0;

            return round($L, 4);
*/
            return $this->getLight($A, $TC);
        }
    }
}

if (method_exists($this, "addGeneric")) {
    $this->addGeneric(array("Name" => "lightSensor",
                            "Type" => "sensor",
                            "Class" => "lightSensor"));
}



?>
