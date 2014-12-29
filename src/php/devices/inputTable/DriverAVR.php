<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
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
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $offset = 0;
    /**
    * This is where our table entry is stored
    */
    protected $entry = null;
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
    * This function creates the system.
    *
    * @param string $driver  The driver to load
    * @param object &$sensor The sensor object
    * @param int    $offset  The offset to use
    * @param object $entry   The table entry
    * @param int    $channel The channel in that entry
    *
    * @return null
    */
    public static function &factory(
        $driver, &$sensor, $offset = 0, $entry = null, $channel = 0
    ) {
        $obj = parent::factory($driver, $sensor, $offset);
        $obj->entry = $entry;
        return $obj;
    }
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
        // This avoids a divide by zero error.
        if ($R2 == 0) {
            return 0.0;
        }
        $V = $this->getVoltage($A, $Vref, $Tc);
        if (is_null($A)) {
            return null;
        }
        $Read = $V * ($R1 + $R2) / $R2;
        return (float)$Read;
    }
    /**
    * This returns the voltage on the upper side of a voltage divider if the
    * AtoD input is in the middle of the divider
    *
    * @param int   $V    The incoming value
    * @param float $R1   The resistor to the voltage
    * @param float $R2   The resistor to ground
    * @param float $Vref The voltage reveference
    * @param int   $Tc   The time constant
    *
    * @return float Voltage rounded to 4 places
    */
    protected function revDividerVoltage($V, $R1, $R2, $Vref, $Tc)
    {
        // This avoids a divide by zero error.
        if (($R2 <= 0) || ($R1 <= 0)) {
            return null;
        }
        $value = $V * $R2 / ($R1 + $R2);
        $Read = $this->revVoltage($value, $Vref, $Tc);
        if (is_null($Read)) {
            return null;
        }
        return (int)round($Read);
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
        $Am = static::AM;
        $s = static::S;
        $Tf = static::TF;
        $D = static::D;
        $denom = $Tc * $Tf * $Am * $s;
        if ($denom == 0) {
            return 0.0;
        }
        $num = $A * $D * $Vref;

        $volts = $num / $denom;
        return (float)$volts;
    }
    /**
    * This returns the voltage that the port is seeing
    *
    * @param int   $V    The Voltage
    * @param float $Vref The voltage reference
    * @param int   $Tc   The time constant
    *
    * @return The units for a particular sensor type
    */
    protected function revVoltage($V, $Vref, $Tc)
    {
        if (is_null($V)) {
            return null;
        }
        $Am = static::AM;
        $s = static::S;
        $Tf = static::TF;
        $D = static::D;
        $denom = $D * $Vref;
        if ($denom == 0) {
            return null;
        }
        $num = $V * $Tc * $Tf * $Am * $s;

        $A = $num / $denom;
        return (int)round($A);
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
        $Am = static::AM;
        $s = static::S;
        $Tf = static::TF;
        $D = static::D;
        $denom = $s * $Tc * $Tf * $Am * $G * $R;
        if ($denom == 0) {
            return 0.0;
        }
        $numer = $A * $D * $Vref;

        $Read = $numer/$denom;
        return (float)$Read;
    }
    /**
    * This takes in a raw AtoD reading and returns the current.
    *
    * This is further documented at: {@link
    * https://dev.hugllc.com/index.php/Project:HUGnet_Current_Sensors Current
    * Sensors }
    *
    * @param int   $I    The raw AtoD reading
    * @param float $R    The resistance of the current sensing resistor
    * @param float $G    The gain of the circuit
    * @param float $Vref The voltage reference
    * @param int   $Tc   The time constant
    *
    * @return float The current sensed
    */
    protected function revCurrent($I, $R, $G, $Vref, $Tc)
    {
        $Am = static::AM;
        $s = static::S;
        $Tf = static::TF;
        $D = static::D;
        $denom = $D * $Vref;
        if (is_null($I) || ($denom == 0)) {
            return null;
        }
        $numer = $I * $s * $Tc * $Tf * $Am * $G * $R;

        $A = $numer/$denom;
        return (int)round($A);
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
        return (float)($A * 1000);
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
        $Am = static::AM;
        $s = static::S;
        $Tf = static::TF;
        $D = static::D;
        $Den = ((($Am*$s*$Tc*$Tf)/$D) - $A);
        if (($Den == 0) || !is_numeric($Den)) {
            $Den = 1.0;
        }
        $R = (float)($A*$Bias)/$Den;
        return (float)$R;
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
        $Am = static::AM;
        $s = static::S;
        $Tf = static::TF;
        $D = static::D;
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
        $Am = static::AM;
        $s = static::S;
        $Tf = static::TF;
        $D = static::D;
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
        return (float)$Rs;
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
        $Am = static::AM;
        $s = static::S;
        $Tf = static::TF;
        $D = static::D;
        $Den = (($Am*$s*$Tc*$Tf)/$D);
        if ($R == 0) {
            return null;
        }
        $A = ($Rs * $Den) / $R;
        return (int)round($A);
    }

}


?>
