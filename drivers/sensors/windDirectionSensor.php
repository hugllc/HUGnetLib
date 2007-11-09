<?php
/**
 *   Sensor driver for wind direction sensors
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
/**
	@brief class for dealing with resistive sensors.
*/
if (!class_exists('windDirectionSensor')) {

    class windDirectionSensor extends sensor_base
    {
    
        /**
            This defines all of the sensors that this driver deals with...
        */
        var $sensors = array(
            0x6F => array(
                'maximum-inc' => array(
                    "longName" => "Maximum Inc wind direction sensor",
                    "unitType" => "Direction",
                    "validUnits" => array('&#176;', 'Direction'),
                    "function" => "maximumIncSensor",
                    "storageUnit" => '&#176;',
                    "unitModes" => array(
                        '&#176;' => 'raw',
                        'Direction' => 'raw'
                    ),
                    "inputSize" => 5,
                ),
            ),
        );
        var $defaultSensor = 'maximum-inc';    
    
        function maximumIncSensor($ndir, $sensor, $TC) {
            $dir = NULL;        
    
            if ($ndir & (1<<0)) $dir1 = 0;   //N
            if ($ndir & (1<<2)) $dir1 = 90;  //E
            if ($ndir & (1<<4)) $dir1 = 180; //S
            if ($ndir & (1<<6)) $dir1 = 270; //W
    
            if ($ndir & (1<<1)) $dir2 = 45;    //NE
            if ($ndir & (1<<3)) $dir2 = 135;    //SE
            if ($ndir & (1<<5)) $dir2 = 225;    //SW
            if ($ndir & (1<<7)) $dir2 = 315;    //NW
    
            // This code encompances all posible good values.  It is safe
            // in that respect.  It could probably be speeded up, though.  ;)
            if (!is_null($dir1) && is_null($dir2)) 
            {
                $dir = $dir1;
            } else if (is_null($dir1) && !is_null($dir2)) 
            {
                $dir = $dir2;
            } else 
            {
                if ($dir1 == 0) {
                    if ($dir2 == 45) {
                        $dir = 22.5;
                    } else if ($dir2 == 315) {
                        $dir = 337.5;
                    }
                } else if ($dir1 == 90) {
                    if ($dir2 == 45) {
                        $dir = 67.5;
                    } else if ($dir2 == 135) {
                        $dir = 112.5;
                    }
                } else if ($dir1 == 180) {
                    if ($dir2 == 135) {
                        $dir = 157.5;
                    } else if ($dir2 == 225){
                        $dir = 202.5;
                    }
                } else if ($dir1 == 270) {
                    if ($dir2 == 225) {
                        $dir = 247.5;
                    } else if ($dir2 == 315) {
                        $dir = 292.5;
                    }
                }
            }
            
            return $dir;
        }
    }
}

if (method_exists($this, "add_generic")) {
    $this->add_generic(array("Name" => "windDirectionSensor", "Type" => "sensor", "Class" => "windDirectionSensor"));
}

?>
