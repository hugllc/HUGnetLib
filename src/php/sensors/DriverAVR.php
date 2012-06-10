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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\sensors;
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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
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
    protected function indirect($val, $Tc)
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
    protected function direct($A, $Tc)
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
}


?>
