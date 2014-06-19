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
    /** This is the number of decimal places we use for our *MATH*, not the output */
    const DECIMAL_PLACES = 20;
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "ADuC MF51E Series Thermistor",
        "shortName" => "ADuCMF51E",
        "unitType" => "Temperature",
        "storageUnit" => '&#176;C',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Bias Resistor (kOhms)",
            "Thermistor"
        ),
        "extraDesc" => array(
            "The other resistor in the resistor divider with the thermistor",
            "The thermistor used",
        ),
        "extraNames" => array(
            "r"     => 0,
            "therm" => 1,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            10, 
            array(
                "10-3950" => "10k @ 25C  B=3950",
            ),
        ),
        "extraDefault" => array(10, "10-3950"),
        "maxDecimals" => 8,
        "inputSize" => 4,
    );
    /** These are the coeffients of the thermocouple equasion */
    private $_coef = array(
        "10-3950" => array(
            "A" => 1.224922E-3,
            "B" => 2.3591320E-4,
            "C" => 7.4995733E-8
        ),
    );
    /** @var array The lookup table */
    private $_valueTable = array(
        "10-3950" => array(
            "181.7"  => -30,
            "133.5"  => -25,
            "98.99"  => -20,
            "74.06"  => -15,
            "56.06"  => -10,
            "42.81"  => -5,
            "32.96"  =>  0,
            "25.57"  =>  5,
            "20"     =>  10,
            "15.76"  =>  15,
            "12.51"  =>  20,
            "10"     =>  25,
            "8.048"  =>  30,
            "6.517"  =>  35,
            "5.321"  =>  40,
            "4.356"  =>  45,
            "3.588"  =>  50,
            "2.972"  =>  55,
            "2.467"  =>  60,
            "2.073"  =>  65,
            "1.734"  =>  70,
            "1.473"  =>  75,
            "1.25"   =>  80,
            "1.065"  =>  85,
            "0.911"  =>  90,
            "0.7824" =>  95,
            "0.6744" =>  100,
            "0.5834" =>  105,
            "0.5066" =>  110,
        ),
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
        bcscale(self::DECIMAL_PLACES);
        $Am    = pow(2, 23);
        $Rbias = $this->getExtra(0);
        $P     = $this->getExtra(1);

        $A = abs($A);
        if ($A == $Am) {
            return null;
        }
        $R = (float)(($A * $Rbias) / ($Am - $A));
        $T = $this->tableInterpolate($R, $this->_valueTable[$P]);
        if (is_null($T)) {
            return null;
        }
        /*
        $T = $this->getTemp($R, $P);
        */
        return round($T, $this->get('maxDecimals', 1));
    }
    /**
    * Implements the formula:
    * 
    * T = 1 / (A + B(ln(R)) + C(ln(R)^3))
    *
    * @param float $R The resistance 
    * @param float $P The thermistor used
    *
    * @return float The resistance in Ohms
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    protected function getTemp($R, $P) 
    {
        if ($R == 0) {
            return null;
        }
        $A     = number_format($this->_coef[$P]["A"], self::DECIMAL_PLACES, '.', '');
        $B     = number_format($this->_coef[$P]["B"], self::DECIMAL_PLACES, '.', '');
        $C     = number_format($this->_coef[$P]["C"], self::DECIMAL_PLACES, '.', '');
        $log   = log($R);
        $denom = bcadd($A, bcadd(bcmul($B, $log), bcmul($C, pow($log, 3))));
        if ($denom == 0) {
            return null;
        }
        $T = bcdiv(1, $denom);
        
        return (float)bcsub($T, 273.15);
    }
    /**
    * Returns the reversed reading
    *
    * @param float $T       The data to use
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
        $T, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        bcscale(self::DECIMAL_PLACES);
        $Am    = pow(2, 23);
        $Rbias = $this->getExtra(0);
        $P     = $this->getExtra(1);

        if (is_null($T)) {
            return null;
        }
        $table = array_reverse(array_flip($this->_valueTable[$P]), true);
        $R = $this->tableInterpolate($T, $table);
        //$R = $this->getRes($T, $P) / 1000;
        $A = ($R * $Am) / ($Rbias + $R);
        return (int)round($A * -1);
    }
    /**
    * Implements the inverse Steinhart-Hart formula:
    * 
    * R = exp((x-y)^1/3 - (x+y)^1/3)
    * 
    * y = (A - 1/T) / 2C
    * 
    * x = ((B/3C)^3 + y^2)^1/2
    *
    * @param float $T The temperature 
    * @param float $P The thermistor used
    *
    * @return float The resistance in Ohms
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    protected function getRes($T, $P) 
    {
        $T    = 273.15 + $T;  // Put the temperature in K
        $A    = number_format($this->_coef[$P]["A"], self::DECIMAL_PLACES, '.', '');
        $B    = number_format($this->_coef[$P]["B"], self::DECIMAL_PLACES, '.', '');
        $C    = number_format($this->_coef[$P]["C"], self::DECIMAL_PLACES, '.', '');
        if (($C == 0) || ($T == 0)) {
            return null;
        }
        $cube = bcdiv(1, 3);
        $y = bcdiv(bcsub($A, bcdiv(1, $T)), bcmul(2, $C));
        $x = pow(bcadd(pow(bcdiv($B, bcmul(3, $C)), 3), pow($y, 2)), 0.5);
        $e = bcsub(pow(bcsub($x, $y), $cube), pow(bcadd($x, $y), $cube));
        $R = pow(M_E, $e);
        return (float)$R;
    }
}


?>
