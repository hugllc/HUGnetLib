<?php
/**
 *   Sensor driver for voltage sensors.
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
if (!class_exists('voltageSensor')) {

    /**
    	@brief class for dealing with resistive sensors.
    */
    class voltageSensor extends sensor_base
    {
        /**
            This defines all of the sensors that this driver deals with...
        */
        var $sensors = array(
            0x10 => array(
                'CHSMSS' => array(
                    "longName" => "TDK CHS-MSS ",
                    "unitType" => "Humidity",
                    "validUnits" => array('%'),
                    "defaultUnits" =>  '%',
                    "function" => "CHSMSS",
                    "storageUnit" => '%',
                    "checkFunction" => "CHSMSS_check",
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
                if ($denom == 0) return 0;
                $numer = $A * $this->D * $this->Vcc * ($R1 + $R2);

                $Read = $numer/$denom;
                return($Read);
        }

        function FETBoard($val, $sensor, $TC, $extra=NULL) {
            $R1 = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $R2 = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $V = $this->getDividerVoltage($val, $R1, $R2, $TC);
            if ($V < 0) $V = NULL;
    	    $V = round($V, 4);
            return $V;
        }    
    
    	/**
    		@public
    		@brief Gets the units for a sensor
    		@param $type Int The type of sensor.
    		@return The units for a particular sensor type
    	
    		@par Introduction
    	*/
    	function getVoltage($A, $T, $Vref) 
    	{
    	    if (is_null($A)) return NULL;
    	    if (is_null($Vref)) return NULL;
    		$denom = $T * $this->Tf * $this->Am * $this->s;
    		if ($denom == 0) return 0;
    		$num = $A * $this->D * $Vref;
    	    
    	    $volts = $num / $denom;
    		return $volts;
    	}
    
    
        /**
            This sensor returns us 10mV / % humidity
        */
        function CHSMSS($A, $sensor, $T, $Vref=1.1) {
            if (is_null($A)) return NULL;
            if (empty($Vref)) $Vref = 1.1;
            $volts = $this->getVoltage($A, $T, (float) $Vref);
            $humidity = $volts * 100;
            if (($humidity > 100) && ($humidity < 105)) return 100;
            if ($humidity > 100) return NULL;
            if ($humidity < 0) return NULL;
            return $humidity;
        }
        
        function CHSMSS_check($humidity, $units) {
            return TRUE;
        }

    }
}

if (method_exists($this, "add_generic")) {
    $this->add_generic(array("Name" => "voltageSensor", "Type" => "sensor", "Class" => "voltageSensor"));
}


?>
