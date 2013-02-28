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
 * Sensor driver for a thermocouple on the ADuC706x
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCThermocouple extends \HUGnet\devices\inputTable\DriverADuC
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /** This is the number of decimal places we use for our *MATH*, not the output */
    const DECIMAL_PLACES = 20;
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "ADuC Thermocouple",
        "shortName" => "ADuCThermo",
        "unitType" => "Temperature",
        "storageUnit" => '&#176;C',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "R1 to Source (kOhms)",
            "R2 to Ground (kOhms)",
            "AtoD Ref Voltage (mV)",
            "Thermocouple Type",
            "Junction Temp Channel",
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, 5, 5, array("k" => "k"), array()),
        "extraDefault" => array(1, 10, 1200, "k", 0),
        "maxDecimals" => 4,
        "inputSize" => 4,
    );
    /** These are the coeffients of the thermocouple equasion */
    private $_coeffients = array(
        "k" => array(
            "-5.891" => null, // For below -200(-5.891 mV) deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "0" => array(// For below 0(0 mV) degC
                0, 2.5173462E1, -1.1662878, -1.0833638, -8.9773540E-1,
                -3.7342377E-1, -8.6632643E-2, -1.0450598E-2, -5.1920577E-4
            ),
            "20.644" => array(// For between 0(0 mV) and 500(20.644 mV) deg C
                0, 2.508355E1, 7.860106E-2, -2.503131E-1, 8.315270E-2,
                -1.228034E-2, 9.804036E-4, -4.413030E-5, 1.057734E-6,
                -1.052755E-8
            ),
            "54.886" => array(// For between 500(20.644 mV) and 1370(54.886 mV) deg C
                -1.318058E2, 4.830222E1, -1.646031, 5.464731E-2, -9.650715E-4,
                8.802193E-6, -3.110810E-8
            ),
        ),
    );
    /** These are the coeffients of the thermocouple equasion */
    private $_revCoeffients = array(
        "k" => array(
            "-270" => null, // For below -270 deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "0" => array(// For below 0(0 mV) degC
                "c" => array(
                    0, 0.394501280250E-1, 0.236223735980E-4, -0.328589067840E-6,
                    -0.499048287770E-8, -0.675090591730E-10, -0.574103274280E-12,
                    -0.310888728940E-14, -0.104516093650E-16, -0.198892668780E-19,
                    -0.163226974860E-22
                ),
                "a" => array(0, 0, 0),
            ),
            "1372" => array(// For between 0(0 mV) and 1372 deg C
                "c" => array(
                    -0.176004136860E-1, 0.389212049750E-1, 0.185587700320E-4,
                    -0.994575928740E-7, 0.318409457190E-9, -0.560728448890E-12,
                    0.560750590590E-15, -0.320207200030E-18, 0.971511471520E-22,
                    -0.121047212750E-25
                ),
                "a" => array(0.1185976, -0.1183432E-3, 0.1269686E3),
            ),
        ),
    );
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    public function get($name)
    {
        $ret = parent::get($name);
        if ($name == "extraValues") {
            $ret[4] = $this->input()->device()->dataChannels()->select();
        }
        return $ret;
    }
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
        $Rin   = $this->getExtra(0);
        $Rbias = $this->getExtra(1);
        $Vref  = $this->getExtra(2);
        $type  = $this->getExtra(3);
        $ref   = $this->getExtra(4);

        $A = $this->inputBiasCompensation($A, $Rin, $Rbias);
        $Va = ($A / $Am) * $Vref;
        $T = $this->_getThermocouple($Va, $data[$ref]["value"], $type);
        if (is_null($T)) {
            return null;
        }
        return round($T, $this->get('maxDecimals'));
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param float $V     Voltage output of thermocouple in milliVolts
    * @param float $TCold Cold junction temperature in degrees C
    * @param float $type  Thermocouple type
    *
    * @return mixed The temperature
    */
    private function _getThermocouple($V, $TCold, $type = "k")
    {
        foreach ((array)array_keys($this->_coeffients[$type]) as $k) {
            if ($V < (float)$k) {
                if (empty($this->_coeffients[$type][$k])) {
                    break;
                }
                $T = 0;
                $c = &$this->_coeffients[$type][$k];
                for ($i = 0; isset($c[$i]); $i++) {
                    /* This is required so when php converts the float to a string
                     * it is *NOT* in scientific notation.  bc functions don't seem
                     * to like scientific notation, and they require string inputs */
                    $coef = number_format($c[$i], self::DECIMAL_PLACES, '.', '');
                    $T = bcadd($T, bcmul($coef, bcpow($V, $i)));
                }
                $T += $TCold;
                break;
            }
        }
        return $T;
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
        bcscale(self::DECIMAL_PLACES);
        $Am    = pow(2, 23);
        $Rin   = $this->getExtra(0);
        $Rbias = $this->getExtra(1);
        $Vref  = $this->getExtra(2);
        $type  = $this->getExtra(3);
        $ref   = $this->getExtra(4);

        if ($Vref == 0) {
            return null;
        }
        $Va = $this->_revThermocouple($value, $data[$ref]["value"], $type);
        if (is_null($Va)) {
            return null;
        }
        $A = ($Va / $Vref) * $Am;
        $Amod = $this->inputBiasCompensation($A, $Rin, $Rbias);
        if ($Amod != 0) {
            $A = $A * ($A / $Amod);
        }
        return (int)round($A);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param float $T     Voltage output of thermocouple in milliVolts
    * @param float $TCold Cold junction temperature in degrees C
    * @param float $type  Thermocouple type
    *
    * @return mixed The temperature
    */
    private function _revThermocouple($T, $TCold, $type = "k")
    {
        $V = null;
        if (!is_array($this->_revCoeffients[$type])) {
            return null;
        }
        foreach ((array)array_keys($this->_revCoeffients[$type]) as $k) {
            if ($T < (float)$k) {
                if (empty($this->_revCoeffients[$type][$k])) {
                    break;
                }
                $V = 0;
                $T -= $TCold;
                $c = &$this->_revCoeffients[$type][$k]["c"];
                for ($i = 0; isset($c[$i]); $i++) {
                    /* This is required so when php converts the float to a string
                     * it is *NOT* in scientific notation.  bc functions don't seem
                     * to like scientific notation, and they require string inputs */
                    $coef = number_format($c[$i], self::DECIMAL_PLACES, '.', '');
                    $V = bcadd($V, bcmul($coef, bcpow($T, $i)));
                }
                $a = &$this->_revCoeffients[$type][$k]["a"];
                $a0 = number_format($a[0], self::DECIMAL_PLACES, '.', '');
                $a1 = number_format($a[1], self::DECIMAL_PLACES, '.', '');
                $a2 = number_format($a[2], self::DECIMAL_PLACES, '.', '');
                $exp = (float)bcmul($a1, bcpow(bcsub($T, $a2), 2));
                $adder = bcmul($a0, exp($exp));
                $V = (float)bcadd($V, $adder);
                break;
            }
        }
        return $V;
    }
}


?>
