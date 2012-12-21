<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our units class */
require_once dirname(__FILE__)."/Driver.php";
/**
 * Base driver class for devices.
 *
 * This class deals with loading the drivers and figuring out what driver needs
 * to be loaded.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class DriverAVR extends Driver
{
    /** This is a constant */
    const AM = 1023;
    /** This is a constant */
    const S = 64;
    /** This is a constant */
    const TF = 65535;
    /** This is a constant */
    const D = 65535;
    /**
    * This is where all of the driver information is stored.
    *
    * Drivers must be registered here, otherwise they will never get loaded.  The
    * index in the root array is the driver name.  It should be exactly the same
    * as the driver class name.
    */
    protected static $drivers = array(
        "00:DEFAULT"                 => "AVRBC2322640_0",
        "02:DEFAULT"                 => "AVRBC2322640",
        "02:AVRB57560G0103F000"      => "AVRB57560G0103F000",
        "02:ControllerTemp"          => "ControllerTemp",
        "02:imcSolar"                => "AVRIMCSolar",
        "02:potDirection"            => "AVRPotDirection",
        "10:DEFAULT"                 => "AVRChsMss",
        "10:chsMss"                  => "AVRChsMss",
        "30:DEFAULT"                 => "AVROSRAMLight",
        "30:OSRAM BPW-34"            => "AVROSRAMLight",
        "40:ControllerVoltage"       => "ControllerVoltage",
        "40:BARO4"                   => "AVRBAROA4V",
        "40:fetBoard"                => "FETBoardVoltage",
        "40:GA100"                   => "AVRGA100",
        "40:HitachiVFDFan"           => "AVRHitachiVFDFan",
        "50:ControllerCurrent"       => "ControllerCurrent",
        "50:dwyer616"                => "AVRDwyer616",
        "50:fetBoard"                => "FETBoardCurrent",
        "7E:DEFAULT"                 => "AVROnTimePulse",
    );
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "Am" => 1023,
        "s"  => 64,
        "Tf" => 65535,
        "D"  => 65535,
    );
    /**
    * This returns the voltage on the upper side of a voltage divider if the
    * AtoD input is in the middle of the divider
    *
    * @param int   $A    The incoming value
    * @param float $R1   The resistor to the voltage
    * @param float $R2   The resistor to ground
    * @param float $Vref The voltage reveference
    * @param int   $Tc   The time constant
    *
    * @return float Voltage rounded to 4 places
    */
    protected function getDividerVoltage($A, $R1, $R2, $Vref, $Tc)
    {
        // If we get null we should return it.
        if (is_null($A)) {
            return null;
        }
        $Am = self::AM;
        $s = self::S;
        $Tf = self::TF;
        $D = self::D;
        $denom = $s * $Tc * $Tf * $Am * $R2;
        if ($denom == 0) {
            return 0.0;
        }
        $numer = $A * $D * $Vref * ($R1 + $R2);
        $Read = $numer/$denom;
        return round($Read, 4);
    }
    /**
    * This function creates an object if it finds the right class
    *
    * @param object &$obj    The object container to put an object in.
    * @param string $driver  The driver to load
    * @param object &$sensor The sensor object
    *
    * @return null
    */
    protected static function driverFactory(&$obj, $driver, &$sensor)
    {
        if (is_object($obj)) {
            return false;
        }
        $class = '\\HUGnet\\devices\\inputTable\\drivers\\avr\\'.$driver;
        $file = dirname(__FILE__)."/drivers/avr/".$driver.".php";
        if (file_exists($file)) {
            include_once $file;
        }
        if (class_exists($class)) {
            $obj = new $class($sensor);
            return true;
        }
        return false;
    }
    /**
    * This returns the voltage that the port is seeing
    *
    * @param int   $A    The AtoD reading
    * @param float $Vref The voltage reference
    * @param int   $Tc   The time constant
    *
    * @return The units for a particular sensor type
    */
    protected function getVoltage($A, $Vref, $Tc)
    {
        if (is_null($A)) {
            return null;
        }
        $Am = self::AM;
        $s = self::S;
        $Tf = self::TF;
        $D = self::D;
        $denom = $Tc * $Tf * $Am * $s;
        if ($denom == 0) {
            return 0.0;
        }
        $num = $A * $D * $Vref;

        $volts = $num / $denom;
        return round($volts, 4);
    }


    /**
    * Volgate for the FET board voltage dividers
    *
    * @param float $val The incoming value
    * @param int   $Tc  The time constant
    *
    * @return float Voltage rounded to 4 places
    */
    protected function indirectVoltage($val, $Tc)
    {
        $R1   = $this->getExtra(0);
        $R2   = $this->getExtra(1);
        $Vref = $this->getExtra(2);
        $V    = $this->getDividerVoltage($val, $R1, $R2, $Vref, $Tc);
        if ($V < 0) {
            return null;
        }
        return $V;
    }

    /**
    * This sensor returns us 10mV / % humidity
    *
    * @param float $A  The incoming value
    * @param int   $Tc The time constant
    *
    * @return float Relative Humidity rounded to 4 places
    */
    protected function directVoltage($A, $Tc)
    {
        $Vref = $this->getExtra(0);
        $V    = $this->getVoltage($A, $Vref, $Tc);
        if ($V < 0) {
            return null;
        }
        if ($V > $Vref) {
            return null;
        }
        return $V;
    }
    /**
    * This takes in a raw AtoD reading and returns the current.
    *
    * This is further documented at: {@link
    * https://dev.hugllc.com/index.php/Project:HUGnet_Current_Sensors Current
    * Sensors }
    *
    * @param int   $A    The raw AtoD reading
    * @param float $R    The resistance of the current sensing resistor
    * @param float $G    The gain of the circuit
    * @param float $Vref The voltage reference
    * @param int   $Tc   The time constant
    *
    * @return float The current sensed
    */
    protected function getCurrent($A, $R, $G, $Vref, $Tc)
    {
        $Am = self::AM;
        $s = self::S;
        $Tf = self::TF;
        $D = self::D;
        $denom = $s * $Tc * $Tf * $Am * $G * $R;
        if ($denom == 0) {
            return 0.0;
        }
        $numer = $A * $D * $Vref;

        $Read = $numer/$denom;
        return round($Read, 4);
    }

    /**
    *  This is specifically for the current sensor in the FET board.
    *
    * @param float $val The incoming value
    * @param int   $Tc  The time constant
    *
    * @return float Current in amps rounded to 1 place
    */
    protected function directCurrent($val, $Tc)
    {
        if (is_null($val)) {
            return null;
        }
        $R = $this->getExtra(0);
        $G = $this->getExtra(1);
        $Vref = $this->getExtra(2);
        $A = $this->getCurrent($val, $R, $G, $Vref, $Tc);
        return round($A * 1000, 1);
    }
    /**
    * Converts a raw AtoD reading into resistance
    *
    * This function takes in the AtoD value and returns the calculated
    * resistance of the sensor.  It does this using a fairly complex
    * formula.  This formula and how it was derived is detailed in
    *
    * @param int   $A    Integer The AtoD reading
    * @param float $Bias Float The bias resistance in kOhms
    * @param int   $Tc   The time constant
    *
    * @return The resistance corresponding to the values given in k Ohms
    */
    protected function getResistance($A, $Bias, $Tc)
    {
        $Am = self::AM;
        $s = self::S;
        $Tf = self::TF;
        $D = self::D;
        $Den = ((($Am*$s*$Tc*$Tf)/$D) - $A);
        if (($Den == 0) || !is_numeric($Den)) {
            $Den = 1.0;
        }
        $R = (float)($A*$Bias)/$Den;
        return round($R, 4);
    }
    /**
    * Converts a raw AtoD reading into resistance
    *
    * This function takes in the AtoD value and returns the calculated
    * resistance of the sensor.  It does this using a fairly complex
    * formula.  This formula and how it was derived is detailed in
    *
    * @param int   $R    Integer The AtoD reading
    * @param float $Bias Float The bias resistance in kOhms
    * @param int   $Tc   The time constant
    *
    * @return The value corresponding the the resistance given
    */
    protected function revResistance($R, $Bias, $Tc)
    {
        $Am = self::AM;
        $s = self::S;
        $Tf = self::TF;
        $D = self::D;
        if (($R < 0) || ($Bias <= 0)) {
            return null;
        }
        $A = ((($Am*$s*$Tc*$Tf)/$D)*$R)/($R + $Bias);
        return (int)round($A);
    }
    /**
    * Converts a raw AtoD reading into resistance
    *
    * If you connect the two ends of a pot up to Vcc and ground, and connect the
    * sweep terminal to the AtoD converter, this function returns the
    * resistance between ground and the sweep terminal.
    *
    * This function takes in the AtoD value and returns the calculated
    * resistance that the sweep is at.  It does this using a fairly complex
    * formula.  This formula and how it was derived is detailed in
    *
    * @param int   $A  Integer The AtoD reading
    * @param float $R  Float The overall resistance in kOhms
    * @param int   $Tc The time constant
    *
    * @return The resistance corresponding to the values given in k Ohms
    */
    protected function getSweep($A, $R, $Tc)
    {
        $Am = self::AM;
        $s = self::S;
        $Tf = self::TF;
        $D = self::D;
        $Den = (($Am*$s*$Tc*$Tf)/$D);
        if (($Den == 0) || !is_numeric($Den)) {
            $Den = 1.0;
        }
        $Rs = (float)(($A*$R)/$Den);
        if ($Rs > $R) {
            return round($R, 4);
        }
        if ($Rs < 0) {
            return 0.0;
        }
        return round($Rs, 4);
    }
    /**
    * Converts a raw AtoD reading into resistance
    *
    * If you connect the two ends of a pot up to Vcc and ground, and connect the
    * sweep terminal to the AtoD converter, this function returns the
    * resistance between ground and the sweep terminal.
    *
    * This function takes in the AtoD value and returns the calculated
    * resistance that the sweep is at.  It does this using a fairly complex
    * formula.  This formula and how it was derived is detailed in
    *
    * @param int   $Rs Integer The AtoD reading
    * @param float $R  Float The overall resistance in kOhms
    * @param int   $Tc The time constant
    *
    * @return The resistance corresponding to the values given in k Ohms
    */
    protected function revSweep($Rs, $R, $Tc)
    {
        $Am = self::AM;
        $s = self::S;
        $Tf = self::TF;
        $D = self::D;
        $Den = (($Am*$s*$Tc*$Tf)/$D);
        if ($R == 0) {
            return null;
        }
        $A = ($Rs * $Den) / $R;
        return (int)round($A);
    }


    /**
    * This function should be called with the values set for the specific
    * thermistor that is used.
    *
    * @param float $R      The current resistance of the thermistor in ohms
    * @param array &$table The table to use
    *
    * @return float The Temperature in degrees C
    */
    protected function tableInterpolate($R, &$table)
    {
        $max = max(array_keys($table));
        $min = min(array_keys($table));

        if (($R < $min) || ($R > $max)) {
            return null;
        }
        foreach (array_keys($table) as $ohm) {
            $last = $ohm;

            if ((float)$ohm <= (float)$R) {
                break;
            }
            $next = $ohm;
        }
        $T = $table[$last];
        if ((($last - $next) == 0) || ((float)$ohm == (float)$R)) {
            return $T;
        }
        $fract = ($R - $last) / ($last - $next);
        $diff = $fract * ($table[$last] - $table[$next]);
        return (float)($T + $diff);
    }
}


?>
