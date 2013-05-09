<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\aduc;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../DriverADuC.php";

/**
 * Sensor driver for direct voltage reading on the ADuC706x
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.12.0
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.12.0
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCMF51E extends \HUGnet\devices\inputTable\DriverADuC
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "ADuC Scaled Temperature Input",
        "shortName" => "ADuCScaledTemp",
        "unitType" => "Temperature",
        "storageUnit" => '&#176;C',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "R1 to Source (kOhms)",
            "R2 to Ground (kOhms)",
            "AtoD Ref Voltage",
            "Scale (&#176;C/mV)",
            "Offset (mV)",
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, 5, 5, 10, 10),
        "extraDefault" => array(100, 1, 1.2, 0.25641026, 0),
        "maxDecimals" => 8,
        "inputSize" => 4,
    );
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        bcscale(20);
        $Am     = pow(2, 23);
        $Rin    = $this->getExtra(0);
        $Rbias  = $this->getExtra(1);
        $Vref   = $this->getExtra(2);
        $scale  = $this->getExtra(3);
        $offset = $this->getExtra(4);

        $A = $this->inputBiasCompensation($A, $Rin, $Rbias);
        $Va = ($A / $Am) * $Vref;
        //$T = (($Va * 1000) / $scale) + $offset;
        $T = (($Va * 1000) + $offset) * $scale;
        return round($T, $this->get('maxDecimals', 1));
    }
    /**
    * Implements the formula:
    * 
    * T = B / ((B / T1) - ln(Rt1/Rt2))
    *
    * @param float $RT2 The resistance measured for the thermistor
    * @param float $RT1 The base resistance of the thermistor
    * @param int   $B   The B value for the thermistor
    *
    * @return float The temperature in C
    */
    protected function getTemp($RT2, $RT1, $B) 
    {
        if ($R == 0) {
            return null;
        }
        $T1    = 298.15;
        $res   = log(bcdiv($RT1,$RT2));
        $denom = bcsub(bcdiv($B, $T1), $res);
        if ($denom == 0) {
            return null;
        }
        $T = bcdiv($B, $denom);
        return bcsub($T, 273.15);
    }
    /**
    * Returns the reversed reading
    *
    * @param array $value   The data to use
    * @param int   $channel The channel to get
    * @param float $deltaT  The time delta in seconds between this record
    * @param array &$prev   The previous reading
    * @param array &$data   The data from the other sensors that were crunched
    *
    * @return string The reading as it would have come out of the endpoint
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getRaw(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        if (is_null($value)) {
            return null;
        }
        bcscale(20);
        $Am   = pow(2, 23);
        $Rin    = $this->getExtra(0);
        $Rbias  = $this->getExtra(1);
        $Vref   = $this->getExtra(2);
        $scale  = $this->getExtra(3);
        $offset = $this->getExtra(4);

        //$Va = (($value - $offset) * $scale) / 1000;
        $Va = (($value / $scale) - $offset) / 1000;
        $A = ($Va / $Vref) * $Am;
        $Amod = $this->inputBiasCompensation($A, $Rin, $Rbias);
        if ($Amod != 0) {
            $A = $A * ($A / $Amod);
        }
        //$A = $A * $this->gain(1);
        return (int)round($A);
    }
    /**
    * Implements the formula:
    * 
    * R = Rt2 / e ^ ((T1/T2) / B (T2 - T1))
    *
    * @param float $T   The temperature 
    * @param float $RT1 The base resistance of the thermistor
    * @param int   $B   The B value for the thermistor
    *
    * @return float The resistance in Ohms
    */
    protected function getRes($T2, $RT1, $B) 
    {
        if ($B == 0) {
            return null;
        }
        $T2    = 273.15 + $T2;  // Put the temperature in K
        $T1    = 298.15;
        if ($T2 == $T1) {
            return $RT1;
        }
        $exp = bcdiv(bcmul($T1, $T2), bcmul($B, bcsub($T2 - $T1)));
        $pow = pow(M_E, $exp);
        $RT2 = bcdiv($RT1 / $pow);
        return $RT2;
    }

}


?>
