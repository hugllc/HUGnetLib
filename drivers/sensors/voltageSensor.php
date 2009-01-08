<?php
/**
 * Sensor driver for voltage sensors.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2009 Hunt Utilities Group, LLC
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
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
if (!class_exists('voltageSensor')) {

    /**
    * class for dealing with resistive sensors.
    *
    * @category   Drivers
    * @package    HUGnetLib
    * @subpackage Sensors
    * @author     Scott Price <prices@hugllc.com>
    * @copyright  2007-2009 Hunt Utilities Group, LLC
    * @copyright  2009 Scott Price
    * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
    * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
    */
    class VoltageSensor extends sensor_base
    {
        /**
        *    This defines all of the sensors that this driver deals with...
        */
        public $sensors = array(
            0x10 => array(
                'chsMss' => array(
                    "longName" => "TDK CHS-MSS ",
                    "unitType" => "Humidity",
                    "validUnits" => array('%'),
                    "defaultUnits" =>  '%',
                    "function" => "chsMss",
                    "storageUnit" => '%',
                    "extraText" => "AtoD Ref Voltage",
                    "extraDefault" => 1.1,
                    "unitModes" => array(
                        '%' => 'raw,diff',
                   ),
               ),
           ),
            0x40 => array(
                "Indirect" => array(
                    "longName" => "Resistor Divider Voltage Sensor",
                    "unitType" => "Voltage",
                    "validUnits" => array('V', 'mV'),
                    "defaultUnits" =>  'V',
                    "function" => "indirect",
                    "storageUnit" => 'V',
                    "unitModes" => array(
                        'mV' => 'raw,diff',
                        'V' => 'raw,diff',
                   ),
                    "extraText" => array(
                        "R1 in kOhms",
                        "R2 in kOhms",
                        "AtoD Ref Voltage"
                    ),
                    "extraDefault" => array(150, 10, 5),
               ),
                "Direct" => array(
                    "longName" => "Direct Voltage Sensor",
                    "unitType" => "Voltage",
                    "validUnits" => array('V', 'mV'),
                    "defaultUnits" =>  'V',
                    "function" => "direct",
                    "storageUnit" => 'V',
                    "unitModes" => array(
                        'mV' => 'raw,diff',
                        'V' => 'raw,diff',
                   ),
                    "extraText" => array("AtoD Ref Voltage"),
                    "extraDefault" => array(5),
               ),
                "fetBoard" => array(
                    "longName" => "FET Board Voltage Sensor",
                    "unitType" => "Voltage",
                    "validUnits" => array('V', 'mV'),
                    "defaultUnits" =>  'V',
                    "function" => "fetBoard",
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
                    "function" => "fetBoard",
                    "storageUnit" => 'V',
                    "unitModes" => array(
                        'mV' => 'raw,diff',
                        'V' => 'raw,diff',
                   ),
                    "extraText" => array("R1 in kOhms", "R2 in kOhms"),
                    "extraDefault" => array(180, 27),
               ),
                "BAROA4" => array(
                    "longName" => "All Sensors BARO-A-4V Barometric Pressure Sensor",
                    "unitType" => "Pressure",
                    "validUnits" => array('mBar', 'in Hg', 'hPa'),
                    "defaultUnits" =>  'mBar',
                    "function" => "linearBounded",
                    "storageUnit" => 'mBar',
                    "unitModes" => array(
                        'mBar' => 'raw,diff',
                        'in Hg' => 'raw,diff',
                        'hPa' => 'raw,diff',
                   ),
                    "extraText" => array(
                        "Min Voltage (V)",
                        "Max Voltage (V)",
                        "Pressure at Min Voltage (mBar)",
                        "Pressure at Max Voltage (mBar)",
                        "AtoD Reference Voltage (V)"
                    ),
                    "extraDefault" => array(.25, 4.25, 600, 1100, 5),
               ),
                "GA100" => array(
                    "longName" => "All Sensors GA100 Differential Pressure Sensor",
                    "unitType" => "Pressure",
                    "validUnits" => array('mBar', 'in Hg', 'hPa', 'psi'),
                    "defaultUnits" =>  'psi',
                    "function" => "linearBounded",
                    "storageUnit" => 'psi',
                    "unitModes" => array(
                        'mBar' => 'raw,diff',
                        'in Hg' => 'raw,diff',
                        'hPa' => 'raw,diff',
                        'psi' => 'raw,diff',
                   ),
                    "extraText" => array(
                        "Min Voltage (V)",
                        "Max Voltage (V)",
                        "Pressure at Min Voltage (mBar)",
                        "Pressure at Max Voltage (mBar)",
                        "AtoD Reference Voltage (V)"
                    ),
                    "extraDefault" => array(.5, 4.5, 0, 1, 5),
                ),
                "HitachiVFDFan" => array(
                    "longName" => "Hitachi VFD Fan Speed",
                    "unitType" => "Pulses",
                    "validUnits" => array('RPM'),
                    "defaultUnits" =>  'RPM',
                    "function" => "linearBoundedIndirect",
                    "storageUnit" => 'RPM',
                    "unitModes" => array(
                        'RPM' => 'diff',
                    ),
                    "extraText" => array(
                        "R1 in kOhms",
                        "R2 in kOhms",
                        "Min Voltage (V)",
                        "Max Voltage (V)",
                        "Pressure at Min Voltage (mBar)",
                        "Pressure at Max Voltage (mBar)",
                        "AtoD Reference Voltage (V)"
                    ),
                    "extraDefault" => array(51, 33, 0, 10, 0, 1040, 5),
               ),

           ),
        );

        /**
        * This sensor returns us 10mV / % humidity
        *
        * @param int   $A    The incoming value
        * @param float $R1   The resistor to the voltage
        * @param float $R2   The resistor to ground
        * @param int   $T    The time constant
        * @param float $Vref The voltage reveference
        *
        * @return float Voltage rounded to 4 places
        */
        function getDividerVoltage($A, $R1, $R2, $T, $Vref = null)
        {
            if (empty($Vref)) {
                $Vref = $this->Vcc;
            }
            $denom = $this->s * $T * $this->Tf * $this->Am * $R2;
            if ($denom == 0) {
                return 0.0;
            }
            $numer = $A * $this->D * $Vref * ($R1 + $R2);

            $Read = $numer/$denom;
            return round($Read, 4);
        }

        /**
        * Volgate for the FET board voltage dividers
        *
        * @param float $val    The incoming value
        * @param array $sensor The sensor setup array
        * @param int   $TC     The time constant
        * @param mixed $extra  Extra parameters for the sensor
        *
        * @return float Voltage rounded to 4 places
        */
        function fetBoard($val, $sensor, $TC, $extra=null)
        {
            $R1 = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $R2 = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $V  = $this->getDividerVoltage($val, $R1, $R2, $TC);
            if ($V < 0) {
                $V = null;
            }
            $V = round($V, 4);
            return $V;
        }
        /**
        * Volgate for the FET board voltage dividers
        *
        * @param float $val    The incoming value
        * @param array $sensor The sensor setup array
        * @param int   $TC     The time constant
        * @param mixed $extra  Extra parameters for the sensor
        *
        * @return float Voltage rounded to 4 places
        */
        function indirect($val, $sensor, $TC, $extra=null)
        {
            $R1   = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $R2   = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $Vref = (float)(empty($extra[2])) ? $sensor['extraDefault'][2]:$extra[2];
            $V    = $this->getDividerVoltage($val, $R1, $R2, $TC, $Vref);
            if ($V < 0) {
                $V = null;
            }
            $V = round($V, 4);
            return $V;
        }

        /**
        * This sensor returns us 10mV / % humidity
        *
        * @param float $A      The incoming value
        * @param array $sensor The sensor setup array
        * @param int   $T      The time constant
        * @param mixed $extra  Extra parameters for the sensor
        *
        * @return float Relative Humidity rounded to 4 places
        */
        function direct($A, $sensor, $T, $extra)
        {
            if (is_null($A)) {
                return null;
            }
            $Vref = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $V    = $this->getVoltage($A, $T, (float) $Vref);
            if ($V < 0) {
                return null;
            }
            if ($V > $Vref) {
                return null;
            }
            $V = round($V, 4);
            return $V;
        }

        /**
        * Gets the units for a sensor
        *
        * @param int   $A    The AtoD reading
        * @param int   $T    The time constant
        * @param float $Vref The voltage reference
        *
        * @return The units for a particular sensor type
        */
        function getVoltage($A, $T, $Vref)
        {
            if (is_null($A)) {
                return null;
            }
            if (is_null($Vref)) {
                return null;
            }
            $denom = $T * $this->Tf * $this->Am * $this->s;
            if ($denom == 0) {
                return 0.0;
            }
            $num = $A * $this->D * $Vref;

            $volts = $num / $denom;
            return round($volts, 4);
        }


        /**
        * This sensor returns us 10mV / % humidity
        *
        * @param float $A      The incoming value
        * @param array $sensor The sensor setup array
        * @param int   $T      The time constant
        * @param mixed $extra  Extra parameters for the sensor
        *
        * @return float Relative Humidity rounded to 4 places
        */
        function chsMss($A, $sensor, $T, $extra)
        {
            if (is_null($A)) {
                return null;
            }
            $Vref     = (empty($extra)) ? $sensor['extraDefault'] : $extra;
            $volts    = $this->getVoltage($A, $T, (float) $Vref);
            $humidity = $volts * 100;
            if ($humidity < 0) {
                return null;
            }
            $humidity = round($humidity, 4);
            return $humidity;
        }

        /**
        * This will work with sensors that are linear and bounded
        *
        * Basically if we have a sensor that is linear and the ends
        * of the line are specified (max1,max2) and (min1,min2) then this
        * is the routine for you.
        *
        * Takd the case of a pressure sensor.  We are give that at Vmax the
        * pressure is Pmax and at Vmin the pressure is Vmin.  That gives us
        * the boundries of the line.  The pressure has to be between Pmax and Pmin
        * and the voltage has to be between Vmax and Vmin.  If it is not null
        * is returned.
        *
        * Given the formula I am using, P MUST be in bounds.
        *
        * @param float $A      The incoming value
        * @param array $sensor The sensor setup array
        * @param int   $T      The time constant
        * @param mixed $extra  Extra parameters for the sensor
        *
        * @return float Relative Humidity rounded to 4 places
        */
        function linearBounded($A, $sensor, $T, $extra)
        {
            if (is_null($A)) {
                return null;
            }
            $Vmin = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $Vmax = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $Pmin = (empty($extra[2])) ? $sensor['extraDefault'][2] : $extra[2];
            $Pmax = (empty($extra[3])) ? $sensor['extraDefault'][3] : $extra[3];
            $Vref = (empty($extra[4])) ? $sensor['extraDefault'][4] : $extra[4];
            $V    = $this->getVoltage($A, $T, (float) $Vref);
            if ($V > $Vmax) {
                return null;
            }
            if ($V < $Vmin) {
                return null;
            }
            if ($Vmax == $Vmin) {
                return null;
            }
            $m = ($Pmax - $Pmin) / ($Vmax - $Vmin);
            $b = $Pmax - ($m * $Vmax);
            $P = ($m * $V) + $b;
            $P = round($P, 4);
            return $P;
        }

        /**
        * This will work with sensors that are linear and bounded
        *
        * Basically if we have a sensor that is linear and the ends
        * of the line are specified (max1,max2) and (min1,min2) then this
        * is the routine for you.
        *
        * Takd the case of a pressure sensor.  We are give that at Vmax the
        * pressure is Pmax and at Vmin the pressure is Vmin.  That gives us
        * the boundries of the line.  The pressure has to be between Pmax and Pmin
        * and the voltage has to be between Vmax and Vmin.  If it is not null
        * is returned.
        *
        * Given the formula I am using, P MUST be in bounds.
        *
        * @param float $A      The incoming value
        * @param array $sensor The sensor setup array
        * @param int   $T      The time constant
        * @param mixed $extra  Extra parameters for the sensor
        *
        * @return float Relative Humidity rounded to 4 places
        */
        function linearBoundedIndirect($A, $sensor, $T, $extra)
        {
            if (is_null($A)) {
                return null;
            }
            $R1   = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $R2   = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $Vmin = (empty($extra[2])) ? $sensor['extraDefault'][2] : $extra[2];
            $Vmax = (empty($extra[3])) ? $sensor['extraDefault'][3] : $extra[3];
            $Pmin = (empty($extra[4])) ? $sensor['extraDefault'][4] : $extra[4];
            $Pmax = (empty($extra[5])) ? $sensor['extraDefault'][5] : $extra[5];
            $Vref = (empty($extra[6])) ? $sensor['extraDefault'][6] : $extra[6];
            $V    = $this->getDividerVoltage($A, $R1, $R2, $T);
            if ($V > $Vmax) {
                return null;
            }
            if ($V < $Vmin) {
                return null;
            }
            if ($Vmax == $Vmin) {
                return null;
            }
            $m = ($Pmax - $Pmin) / ($Vmax - $Vmin);
            $b = $Pmax - ($m * $Vmax);
            $P = ($m * $V) + $b;
            $P = round($P, 4);
            return $P;
        }

    }
}

if (method_exists($this, "addGeneric")) {
    $this->addGeneric(array("Name" => "voltageSensor",
                            "Type" => "sensor",
                            "Class" => "voltageSensor"));
}


?>
