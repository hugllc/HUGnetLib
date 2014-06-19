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
 * Driver for reading voltage based pressure sensors
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
class ADuCPower extends \HUGnet\devices\inputTable\DriverADuC
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /** This is our offset for impedance */
    const IMPEDANCE_OFFSET = 0x100000000;
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "ADuC Power Meter",
        "shortName" => "ADuCPower",
        "unitType" => "Varies",
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Voltage Ref (V)",
            "Shunt Resistor (Ohms)",
            "Voltage Input R (kOhms)",
            "Voltage Bias R (kOhms)",
            "Current Input R (kOhms)",
            "Current Bias R (kOhms)",
        ),
        "extraDesc" => array(
            "The AtoD reference voltage",
            "The current sense resistor value",
            "The input resistance to the AtoD reading the voltage",
            "The resistor connecting the AtoD reading the voltage to ground",
            "The input resistance to the AtoD reading the current",
            "The resistor connecting the AtoD reading the current to ground",
        ),
        "extraNames" => array(
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(10, 10, 10, 10, 10, 10),
        "extraDefault" => array(1.2, 0.05, 100, 1, 1, 10),
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
    protected function getVoltage(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        if (is_null($A)) {
            return null;
        }
        bcscale(20);
        $Am   = pow(2, 23);
        $A = bcdiv($A, $this->gain(1));
        $Vref  = $this->getExtra(0);
        $Rin   = $this->getExtra(2);
        $Rbias = $this->getExtra(3);
        $A = $this->inputBiasCompensation($A, $Rin, $Rbias);
        $Va = bcmul(bcdiv($A, $Am), $Vref);
        return round($Va, $this->get("maxDecimals"));
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param float $Va The value to reverse
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getRawVoltage($Va)
    {
        if (is_null($Va)) {
            return null;
        }
        bcscale(20);
        $Am   = pow(2, 23);
        $Vref  = $this->getExtra(0);
        $Rin   = $this->getExtra(2);
        $Rbias = $this->getExtra(3);
        if ($Vref == 0) {
            return null;
        }
        $A = bcmul(bcdiv($Va, $Vref), $Am);
        $Amod = $this->inputBiasCompensation($A, $Rin, $Rbias);
        if ($Amod != 0) {
            $A = bcmul($A, bcdiv($A, $Amod));
        }
        $A = bcmul($A, $this->gain(1));
        return round($A);
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
    protected function getCurrent(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        if (is_null($A)) {
            return null;
        }
        bcscale(20);
        $Am    = pow(2, 23);
        $A     = bcdiv($A, $this->gain());
        $Vref  = $this->getExtra(0);
        $R     = $this->getExtra(1);
        $Rin   = $this->getExtra(4);
        $Rbias = $this->getExtra(5);
        if ($R == 0) {
            return null;
        }
        $A = $this->inputBiasCompensation($A, $Rin, $Rbias);
        $Va = bcmul(bcdiv($A, $Am), $Vref);
        $I = bcdiv($Va, $R);
        return round($I, $this->get("maxDecimals"));
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param float $I The value to reverse
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getRawCurrent($I)
    {
        if (is_null($I)) {
            return null;
        }
        bcscale(20);
        $Am   = pow(2, 23);
        $Vref  = $this->getExtra(0);
        $R     = $this->getExtra(1);
        $Rin   = $this->getExtra(4);
        $Rbias = $this->getExtra(5);
        if ($Vref == 0) {
            return null;
        }
        $Va = bcmul($I, $R);
        $A = bcmul(bcdiv($Va, $Vref), $Am);
        $Amod = $this->inputBiasCompensation($A, $Rin, $Rbias);
        if ($Amod != 0) {
            $A = bcmul($A, bcdiv($A, $Amod));
        }
        $A = bcmul($A, $this->gain(0));
        return round($A);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getCalcPower(
        $deltaT = 0, &$data = array(), $prev = null
    ) {
        $I = $data[0]["value"];
        $V = $data[1]["value"];
        if (is_null($I) || is_null($V)) {
            return null;
        }
        $P = bcmul($I, $V);
        return round($P, $this->get("maxDecimals"));
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
    protected function getPower(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        if (is_null($A)) {
            return null;
        }
        bcscale(20);
        // This calculates what 1W would be
        $scale = bcmul($this->getRawVoltage(1), $this->getRawCurrent(1));
        if ($scale == 0) {
            return null;
        }
        // We then scale what we got against that.
        $P = bcdiv($A, $scale);
        return round($P, $this->get("maxDecimals"));
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param float $P The value to reverse
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getRawPower($P)
    {
        if (is_null($P)) {
            return null;
        }
        bcscale(20);
        // This calculates what 1W would be
        $scale = bcmul($this->getRawVoltage(1), $this->getRawCurrent(1));
        // We then scale what we got against that.
        $A = bcmul($P, $scale);
        return round($A);
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
    protected function getImpedance(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        if (is_null($A)) {
            return null;
        }
        bcscale(20);
        $I = $this->getRawCurrent(1);
        if ($I == 0) {
            return null;
        }
        // This removes the offset
        $A = bcdiv($A, self::IMPEDANCE_OFFSET);
        // This calculates what 1 Ohm would be
        $scale = bcdiv($this->getRawVoltage(1), $I);
        // We then scale what we got against that.
        $Z = bcdiv($A, $scale);
        return round($Z, $this->get("maxDecimals"));
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getCalcImpedance(
        $deltaT = 0, &$data = array(), $prev = null
    ) {
        /* Impedance */
        $I = $data[0]["value"];
        $V = $data[1]["value"];
        if (($I == 0) || is_null($V)) {
            return null;
        }
        $R = abs($V / $I);
        return round($R, $this->get("maxDecimals"));
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param float $Z The value to reverse
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getRawImpedance($Z)
    {
        if (is_null($Z)) {
            return null;
        }
        $I = $this->getRawCurrent(1);
        if ($I == 0) {
            return null;
        }
        // This calculates what 1 Ohm would be
        $scale = bcdiv($this->getRawVoltage(1), $I);
        // We then scale what we got against that.
        $A = bcmul($Z, $scale);
        $A = bcmul($A, self::IMPEDANCE_OFFSET);
        return round($A);
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param float  $deltaT  The time delta in seconds between this record
    * @param array  &$prev   The previous reading
    * @param array  &$data   The data from the other sensors that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function decodeData(
        &$string, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $ret = $this->channels();
        foreach (array_keys($ret) as $key) {
            $A = $this->getRawData($string, $key);
            $ret[$key]["value"] = $this->decodeDataPoint(
                $A, $key, $deltaT, $prev, $ret
            );
            $ret[$key]["raw"] = $A;
        }
        return $ret;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
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
    public function encodeDataPoint(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $val = $this->getRaw(
            $value, $channel, $deltaT, $prev, $data
        );
        if (!is_null($val)) {
            if ($channel < 2) {
                return $this->encodeInt($val, 4);
            } else {
                return $this->encodeFloat($val, 4);
            }
        }
        return "";
    }

    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param int    $channel The channel to use
    * @param float  $deltaT  The time delta in seconds between this record
    * @param array  &$prev   The previous reading
    * @param array  &$data   The data from the other sensors that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function decodeDataPoint(
        &$string, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $return = null;
        $Enable = $this->_hardwareEnable();
        $A = $this->getRawData($string, $channel);
        if ($channel == 0) {
            $return = $this->getCurrent($A, $deltaT, $data, $prev);
        } else if ($channel == 1) {
            $return = $this->getVoltage($A, $deltaT, $data, $prev);
        } else if ($channel == 2) {
            if ($Enable) {
                $return = $this->getPower($A, $deltaT, $data, $prev);
            } else {
                $return = $this->getCalcPower($deltaT, $data, $prev);
            }
        } else if ($channel == 3) {
            if ($Enable) {
                $return = $this->getImpedance($A, $deltaT, $data, $prev);
            } else {
                $return = $this->getCalcImpedance($deltaT, $data, $prev);
            }
        }
        return $return;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param int    $channel The channel to decode
    *
    * @return float The raw value
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function getRawData(&$string, $channel = 0)
    {
        if (is_null($string)) {
            return null;
        }
        if (!is_string($string)) {
            return (float)$string;
        }
        $return = null;
        $Enable = $this->_hardwareEnable();
        if (($channel < 2) || ($Enable && ($channel < 4))) {
            $size = $this->get("inputSize");
            if ($size > strlen($string)) {
                return null;
            }
            $work = substr($string, 0, ($size * 2));
            $str2 = $string;
            $string = (string)substr($string, ($size * 2));
            if ($channel < 2) {
                $A = $this->decodeInt($work, $size);
                $return = $this->getTwosCompliment($A, $size * 8);
            } else {
                $return = $this->decodeFloat($work, $size);
            }
            //print "$channel => $size => ".str_pad($return, 15).": $str2\n";
        }
        return $return;
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
    public function getRaw(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $return = 0;
        if ($channel == 0) {
            $return = $this->getRawCurrent($value);
        } else if ($channel == 1) {
            $return = $this->getRawVoltage($value);
        } else if ($channel == 2) {
            $return = $this->getRawPower($value);
        } else if ($channel == 3) {
            $return = $this->getRawImpedance($value);
        }
        return $return;
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    private function _hardwareEnable()
    {
        $ipr = $this->ipRoutine(0);
        return $ipr == \HUGnet\devices\inputTable\tables\ADuCInputTable::IPR_POWER;
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channels()
    {
        $Enable = $this->_hardwareEnable();
        $decimals = $this->get("maxDecimals");
        return array(
            array(
                "decimals" => $decimals,
                "units" => "A",
                "maxDecimals" => $decimals,
                "storageUnit" => "A",
                "unitType" => "Current",
                "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                "label" => $this->input()->get("location")." 0",
                "index" => 0,
                "epChannel" => true,
                "port" => $this->entry()->port(0),
            ),
            array(
                "decimals" => $decimals,
                "units" => "V",
                "maxDecimals" => $decimals,
                "storageUnit" => "V",
                "unitType" => "Voltage",
                "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                "label" => $this->input()->get("location")." 1",
                "index" => 1,
                "epChannel" => true,
                "port" => $this->entry()->port(1),
            ),
            array(
                "decimals" => $decimals,
                "units" => "W",
                "maxDecimals" => $decimals,
                "storageUnit" => "W",
                "unitType" => "Power",
                "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                "label" => $this->input()->get("location")." 2",
                "index" => 2,
                "epChannel" => $Enable,
                "port" => null,
            ),
            array(
                "decimals" => $decimals,
                "units" => "Ohms",
                "maxDecimals" => $decimals,
                "storageUnit" => "Ohms",
                "unitType" => "Impedance",
                "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                "label" => $this->input()->get("location")." 3",
                "index" => 3,
                "epChannel" => $Enable,
                "port" => null,
            )
        );
    }

}


?>
