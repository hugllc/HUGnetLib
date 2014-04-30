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
namespace HUGnet\devices\inputTable\tables;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCInputTable
{
    /** This is our power calculator. */
    const IPR_POWER = 0x04;
    /** This is our power calculator. */
    const IPR_ACRES = 0x05;
    /**
    * This is where we store our sensor object
    */
    private $_sensor;
    /**
    * This is where we setup the sensor object
    */
    private $_params = array(
        "driver0"  => array(
            "value" => 0xFF,
            'mask'  => 0xFF,
            "valid" => array(
                0x04 => "ADuC Vishay RTD",
                0x41 => "ADuC Voltage",
                0x42 => "ADuC Thermocouple",
                0x43 => "ADuC Scaled Temperature",
                0x44 => "ADuC Pressure",
                0x45 => "ADuC Generic Linear",
                0x46 => "MKS 901P Pressure Sensor",
                0x47 => "MF51E Series Thermistor",
                0x48 => "ADuC Resistance",
                0x49 => "Omega Alpha pH",
                0x4A => "ADuC AC Resistance",
                0x4B => "ADuC US Sensor RTD",
                0x11 => "ADuC DC Power",
            ),
            "desc"     => "First Driver",
            "longDesc" => "The driver for the first ENABLED ADC.",
        ),
        "driver1"  => array(
            "value" => 0xFF,
            'mask'  => 0xFF,
            "valid" => array(
                0x04 => "ADuC Vishay RTD",
                0x41 => "ADuC Voltage",
                0x42 => "ADuC Thermocouple",
                0x43 => "ADuC Scaled Temperature",
                0x44 => "ADuC Pressure",
                0x45 => "ADuC Generic Linear",
                0x46 => "MKS 901P Pressure Sensor",
                0x47 => "MF51E Series Thermistor",
                0x48 => "ADuC Resistance",
                0x49 => "Omega Alpha pH",
                0x4B => "ADuC US Sensor RTD",
                0xFF => "None",
            ),
            "desc"     => "Second Driver",
            "longDesc" => "The driver for the second ENABLED ADC.",
        ),
        "priority" => array(
            "value" => 0xFF,
            'mask'  => 0xFF,
            "desc"  => "Priority",
            'size'  => 3,
        ),
        "process"  => array(
            "value" => 0,
            'mask'  => 0xFF,
            "valid" => array(
                0x00 => "None",
                0x01 => "Multiply by 128",
                0x02 => "Divide by 128",
                0x03 => "Square Data",
                self::IPR_POWER => "Calculate Power and Impedance", // 4
                self::IPR_ACRES => "AC Resistance", // 5
            ),
            "desc"     => "Immediate Processing 0",
            "longDesc" => "The immediate processing routine to use for ADC0 or
                           both of the ADCs in some cases.",
        ),
        "process1"  => array(
            "value" => 0,
            'mask'  => 0xFF,
            "valid" => array(
                0x00 => "None",
                0x01 => "Multiply by 128",
                0x02 => "Divide by 128",
                0x03 => "Square Data",
            ),
            "desc"     => "Immediate Processing 1",
            "longDesc" => "The immediate processing routine to use for ADC1"
        ),
        "ADC0EN"    => array(
            'value' => 1,
            'bit'   => 15,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "No", 1 => "Yes"),
            'desc'  => "ADC0 Enable",
            'longDesc' => "Enable ADC0",
            'register' => "ADC0CON",
        ),
        "ADC0DIAG"  => array(
            'value' => 0x0,
            'bit'   => 13,
            'mask'  => 0x3,
            'bits'  => 2,
            'valid' => array(
                0 => "Off",
                1 => "50mA on Positive Input",
                2 => "50mA on Negative Input",
                3 => "50mA on Both",
            ),
            'desc'  => "ADC0 Diagnostic Current",
            'longDesc' => "Set diagnostic current to test ADC0",
            'register' => "ADC0CON",
        ),
        "HIGHEXTREF0" => array(
            'value' => 0,
            'bit'   => 12,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Ref", 1 => "Ref/2"),
            'desc'  => "ADC0 High Reference",
            'longDesc' => "",
            'register' => "ADC0CON",
        ),
        "AMP_CM"    => array(
            'value' => 0,
            'bit'   => 11,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Common Mode Input", 1 => "AVDD/2"),
            'desc'  => "ADC0 PGA Common Mode Voltage",
            'longDesc' => "",
            'register' => "ADC0CON",
        ),
        "ADC0CODE"  => array(
            'value' => 0,
            'bit'   => 10,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Two's Compliment", 1 => "Unipolar"),
            'desc'  => "ADC0 Output Coding",
            'longDesc' => "Signed or unsigned, that is the question",
            'register' => "ADC0CON",
        ),
        "ADC0CH"    => array(
            'value' => 0x3,
            'bit'   => 6,
            'mask'  => 0xF,
            'bits'  => 4,
            'valid' => array(
                0 => "Input 4/3 Differential",
                1 => "Input 4 Single Ended",
                2 => "Input 3 Single Ended",
                3 => "VREF+/VREF-",
                5 => "Input 2/1 Differential",
                6 => "Input 2 Single Ended",
                7 => "Input 1 Single Ended",
                8 => "Internal Short to ADC1",
                9 => "Internal Short to ADC1",
            ),
            /*
            'valid' => array(
                0 => "ADC0/ADC1 Differential",
                1 => "ADC0/ADC5 Single Ended",
                2 => "ADC1/ADC5 Single Ended",
                3 => "VREF+/VREF-",
                5 => "ADC2/ADC3 Differential",
                6 => "ADC2/ADC5 Single Ended",
                7 => "ADC3/ADC5 Single Ended",
                8 => "Internal Short to ADC1",
                9 => "Internal Short to ADC1",
            ),
            */
            'desc'  => "ADC0 Channel",
            'longDesc' => "Which input to take the signal off of",
            'register' => "ADC0CON",
        ),
        "ADC0REF"   => array(
            'value' => 0x0,
            'bit'   => 4,
            'mask'  => 0x3,
            'bits'  => 2,
            'valid' => array(
                0 => "Internal",
                1 => "External VREF+/VREF-",
                2 => "External AUX",
                3 => "AVDD/AGND (div/2 selected)",
            ),
            'desc'  => "ADC0 Reference",
            'longDesc' => "The reference to use",
            'register' => "ADC0CON",
        ),
        "ADC0PGA"   => array(
            'value' => 0x0,
            'bit'   => 0,
            'mask'  => 0xF,
            'bits'  => 4,
            'valid' => array(
                0 => "1",
                1 => "2",
                2 => "4",
                3 => "8",
                4 => "16",
                5 => "32",
                6 => "64",
                7 => "128",
                8 => "256",
                9 => "512",
            ),
            'desc'  => "ADC0 Gain",
            'longDesc' => "The gain on ADC0",
            'register' => "ADC0CON",
        ),
        "ADC1EN"      => array(
            'value' => 1,
            'bit'   => 15,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "No", 1 => "Yes"),
            'desc'  => "ADC1 Enable",
            'longDesc' => "Enable ADC1",
            'register' => "ADC1CON",
        ),
        "ADC1DIAG"    => array(
            'value' => 0x0,
            'bit'   => 13,
            'mask'  => 0x2,
            'bits'  => 2,
            'valid' => array(
                0 => "Off",
                1 => "50mA on Positive Input",
                2 => "50mA on Negative Input",
                3 => "50mA on Both",
            ),
            'desc'  => "ADC1 Diagnostic Current",
            'longDesc' => "Diagnostic current for testing purposes",
            'register' => "ADC1CON",
        ),
        "HIGHEXTREF1" => array(
            'value' => 0,
            'bit'   => 12,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Ref", 1 => "Ref/2"),
            'desc'  => "ADC1 High Reference",
            'longDesc' => "",
            'register' => "ADC1CON",
        ),
        "ADC1CODE"    => array(
            'value' => 0,
            'bit'   => 11,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Two's Compliment", 1 => "Unipolar"),
            'desc'  => "ADC1 Output Coding",
            'longDesc' => "Signed or unsigned, that is the question",
            'register' => "ADC1CON",
        ),
        "ADC1CH"      => array(
            'value' => 0xC,
            'bit'   => 7,
            'mask'  => 0xF,
            'bits'  => 4,
            'valid' => array(
                0  => "Input 2/1 Differential",
                1  => "ADC4/ADC5 Differential",
                2  => "Input 5/6 Differential",
                3  => "Input 7/8 Differential",
                4  => "Input 2 Single Ended",
                5  => "Input 1 Single Ended",
                6  => "ADC4/ADC5 Single Ended (RTD)",
                7  => "Input 5 Single Ended",
                8  => "Input 6 Single Ended",
                9  => "Input 7 Single Ended",
                10 => "Input 8 Single Ended",
                11 => "Internal Temp",
                12 => "VREF+/VREF-",
                13 => "DAC_OUT/AGND",
                15 => "Internal Short to ADC3",
            ),
            /*
            'valid' => array(
                0  => "ADC2/ADC3 Differential",
                1  => "ADC4/ADC5 Differential",
                2  => "ADC6/ADC7 Differential",
                3  => "ADC8/ADC9 Differential",
                4  => "ADC2/ADC5 Single Ended",
                5  => "ADC3/ADC5 Single Ended",
                6  => "ADC4/ADC5 Single Ended",
                7  => "ADC6/ADC5 Single Ended",
                8  => "ADC7/ADC5 Single Ended",
                9  => "ADC8/ADC5 Single Ended",
                10 => "ADC9/ADC5 Single Ended",
                11 => "Internal Temp",
                12 => "VREF+/VREF-",
                13 => "DAC_OUT/AGND",
                15 => "Internal Short to ADC3",
            ),
            */
            'desc'  => "ADC1 Channel",
            'longDesc' => "The channel for ADC1",
            'register' => "ADC1CON",
        ),
        "ADC1REF"     => array(
            'value' => 0x0,
            'bit'   => 4,
            'mask'  => 0x7,
            'bits'  => 3,
            'valid' => array(
                0 => "Internal",
                1 => "External VREF+/VREF-",
                2 => "External AUX",
                3 => "AVDD/AGND (div/2 selected)",
                4 => "AVCC/ADC3",
            ),
            'desc'  => "ADC1 Reference",
            'longDesc' => "Which reference would you choose?",
            'register' => "ADC1CON",
        ),
        "BUF_BYPASS"  => array(
            'value' => 0x0,
            'bit'   => 2,
            'mask'  => 0x3,
            'bits'  => 2,
            'valid' => array(
                0 => "Full buffer on",
                1 => "Negative buffer bypass",
                2 => "Positive buffer bypass",
                3 => "Full buffer bypass",
            ),
            'desc'  => "ADC1 Buffer Bypass",
            'longDesc' => "Buffer the incoming signal",
            'register' => "ADC1CON",
        ),
        "ADC1PGA"     => array(
            'value' => 0x0,
            'bit'   => 0,
            'mask'  => 0x3,
            'bits'  => 2,
            'valid' => array(
                0 => "1",
                1 => "2",
                2 => "4",
                3 => "8",
            ),
            'desc'  => "ADC1 Gain",
            'longDesc' => "The gain on ADC1",
            'register' => "ADC1CON",
        ),
        "CHOPEN" => array(
            'value' => 1,
            'bit'   => 15,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "No", 1 => "Yes"),
            'desc'  => "Chop Enable",
            'longDesc' => "Enable chop.  This cuts the frequency in half.",
            'register' => "ADCFLT",
        ),
        "RAVG2"  => array(
            'value' => 0,
            'bit'   => 14,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "No", 1 => "Yes"),
            'desc'  => "Running Average By 2",
            'longDesc' => "This enables a running average and cuts the freq in half",
            'register' => "ADCFLT",
        ),
        "AF"     => array(
            'value' => 0x0,
            'bit'   => 8,
            'mask'  => 0x3F,
            'bits'  => 6,
            'valid' => "_avgFilterValidate",
            'size'  => 3,
            'desc'  => "Averaging Filter Value",
            'longDesc' => "Value to use for the averaging filter.  Some limitations
                            apply",
            'register' => "ADCFLT",
        ),
        "NOTCH2" => array(
            'value' => 0,
            'bit'   => 7,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "No", 1 => "Yes"),
            'desc'  => "Sinc Notch Filter Enable",
            'longDesc' => "Enable the notch filter",
            'register' => "ADCFLT",
        ),
        "SF"     => array(
            'value' => 0x9,
            'bit'   => 0,
            'mask'  => 0x7F,
            'bits'  => 7,
            'valid' => "_sinc3Validate",
            'size'  => 3,
            'desc'  => "Sinc3 Filter Value",
            'longDesc' => "Value to use for the sinc3 filter.  Some limitations
                          apply",
            'register' => "ADCFLT",
        ),
    );
    /**
    * This is where we store our sensor object
    */
    protected $ports = array(
        0 => array(
            0 => "Port4 -,Port3 +",
            1 => "Port4",
            2 => "Port3",
            3 => "VREF+,VREF-",
            5 => "Port2 -,Port1 +",
            6 => "Port2",
            7 => "Port1",
        ),
        1 => array(
            0  => "Port2 -,Port1 +",
            1  => "ADC4,ADC5",
            2  => "Port5 -,Port6 +",
            3  => "Port7 -,Port8 +",
            4  => "Port2",
            5  => "Port1",
            6  => "ADC4",
            7  => "Port5",
            8  => "Port6",
            9  => "Port7",
            10 => "Port8",
        ),
    );
    /**
    * This is the constructor
    *
    * @param object $sensor The sensor object we are working with
    * @param mixed  $config This could be a string or array or null
    */
    private function __construct($sensor, $config = null)
    {
        $this->_sensor = &$sensor;
        if (is_string($config)) {
            $this->decode($config);
        } else if (is_array($config)) {
            $this->fromArray($config);
        }
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_sensor);
    }
    /**
    * This is the constructor
    *
    * @param object $sensor The sensor object we are working with
    * @param mixed  $config This could be a string or array or null
    *
    * @return object The new object
    */
    public static function &factory($sensor, $config = null)
    {
        $object = new ADuCInputTable($sensor, $config);
        return $object;
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param string $register The register to get
    * @param string $set      The values to set the register to
    *
    * @return 16 bit integer that is the FLT setup
    */
    public function register($register, $set = null)
    {
        if (is_string($set) || is_int($set)) {
            if (is_string($set)) {
                $set = hexdec($set);
            }
            foreach ($this->_params as $field => $vals) {
                if ($vals["register"] === $register) {
                    $mask = $vals["mask"] << $vals["bit"];
                    $val = ($set & $mask) >> $vals["bit"];
                    $this->_params[$field]["value"] = $val;
                }
            }
        }
        $ret  = 0;
        $bits = 0;
        foreach ($this->_params as $field => $vals) {
            if ($vals["register"] === $register) {
                $val = $vals["value"] & $vals["mask"];
                $val <<= $vals["bit"];
                $ret |= $val;
                $bits += $vals["bits"];
            }
        }
        return sprintf("%0".round($bits / 4)."X", $ret);
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param mixed $value The value to check
    *
    * @return 16 bit integer in a hex string
    *
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _avgFilterValidate($value)
    {
        $sinc = $this->_params["SF"]["value"];
        if ($value === 0) {
            // Do nothing here
        } else if ($value < 8) {
            if ($sinc > 63) {
                $this->_params["SF"]["value"] = 63;
            }
        } else if ($value < 64) {
            if ($sinc > 31) {
                $this->_params["SF"]["value"] = 31;
            }
        }
        return true;
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param mixed $value The value to check
    *
    * @return 16 bit integer in a hex string
    *
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _sinc3Validate($value)
    {
        $avg = $this->_params["AF"]["value"];
        if ($value < 32) {
            // Do nothing here
        } else if ($value < 64) {
            if ($avg > 7) {
                $this->_params["AF"]["value"] = 7;
            }
        } else if ($value < 128) {
            if ($avg !== 0) {
                $this->_params["AF"]["value"] = 0;
            }
        }
        return true;
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param string $set The values to set the register to
    *
    * @return 16 bit integer that is the FLT setup
    */
    public function driver0($set = null)
    {
        if (is_string($set)) {
            $set = hexdec($set);
        }
        return sprintf("%02X", $this->_params("driver0", $set));
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param string $set The values to set the register to
    *
    * @return 16 bit integer that is the FLT setup
    */
    public function driver1($set = null)
    {
        if (is_string($set)) {
            $set = hexdec($set);
        }
        return sprintf("%02X", $this->_params("driver1", $set));
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param string $set The values to set the register to
    *
    * @return 16 bit integer that is the FLT setup
    */
    public function priority($set = null)
    {
        return sprintf("%02X", $this->_params("priority", $set));
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param int    $channel The channel to use
    * @param string $set     The values to set the register to
    *
    * @return 16 bit integer that is the FLT setup
    */
    public function immediateProcessRoutine($channel = 0, $set = null)
    {
        if ($channel == 0) {
            $process = "process";
        } else {
            $process = "process1";
        }
        return sprintf("%02X", $this->_params($process, $set));
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param int $channel the channel to use
    *
    * @return True if the channel is enabled, false otherwise
    */
    public function enabled($channel)
    {
        if ($channel == 0) {
            return (bool)$this->_params("ADC0EN");
        }
        return (bool)$this->_params("ADC1EN");
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param int $channel the channel to use
    *
    * @return True if two's compliment is enabled, false otherwise
    */
    public function twosComplimentEnabled($channel)
    {
        if ($channel == 0) {
            return !(bool)$this->_params("ADC0CODE");
        }
        return !(bool)$this->_params("ADC1CODE");
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param string $param The parameter to set
    * @param string $set   The values to set the register to
    *
    * @return 16 bit integer in a hex string
    */
    private function _params($param, $set = null)
    {
        if (is_string($set)) {
            $set = (int)$set;
        }
        if (is_int($set)) {
            $par = &$this->_params[$param];
            $set &= $par["mask"];
            if (is_string($par["valid"]) && method_exists($this, $par["valid"])) {
                $fct = $par["valid"];
                $check = $this->$fct($set);
            } else if (is_array($par["valid"]) && !isset($par["valid"][$set])) {
                $check = false;
            } else {
                $check = true;
            }
            if ($check) {
                $par["value"] = $set;
            }
        }
        return $this->_params[$param]["value"];
    }
    /**
    * This takes the class and makes it into a setup string
    *
    * @return string The encoded string
    */
    public function freq()
    {
        $ret = 0;
        $sinc = $this->_params("SF");
        $flt  = $this->_params("AF");
        if ($this->_params("CHOPEN")) {
            $ret = 512000 / ((($sinc +1) * 64 * (3 + $flt)) + 3);
        } else if ($flt > 0) {
            $ret = 512000 / (($sinc +1) * 64 * (3 + $flt));
        } else if ($flt === 0) {
            $ret = 512000 / (($sinc + 1) * 64);
        }
        return round($ret, 4);
    }
    /**
    * This takes the class and makes it into a setup string
    *
    * @return string The encoded string
    */
    public function encode()
    {
        $ret  = "";
        $ret .= $this->priority();
        $ret .= $this->immediateProcessRoutine(0);
        /* This is because encoding is little endian */
        foreach (array("ADC0CON", "ADC1CON", "ADCFLT") as $reg) {
            $value = $this->register($reg);
            $ret .= substr($value, 2, 2);
            $ret .= substr($value, 0, 2);
        }
        $ret .= $this->driver0();
        $ret .= $this->driver1();
        $ret .= $this->immediateProcessRoutine(1);
        return $ret;
    }
    /**
    * This builds the class from a setup string
    *
    * @param string $string The setup string to decode
    *
    * @return bool True on success, false on failure
    */
    public function decode($string)
    {
        if (strlen($string) >= 20) {
            $this->priority(substr($string, 0, 2));
            $this->immediateProcessRoutine(0, substr($string, 2, 2));
            $this->register("ADC0CON", substr($string, 6, 2).substr($string, 4, 2));
            $this->register("ADC1CON", substr($string, 10, 2).substr($string, 8, 2));
            $this->register("ADCFLT", substr($string, 14, 2).substr($string, 12, 2));
            $this->driver0(substr($string, 16, 2));
            $this->driver1(substr($string, 18, 2));
            $this->immediateProcessRoutine(1, substr($string, 20, 2));
            return true;
        }
        return false;
    }
    /**
    * Returns the table as an array
    *
    * @param bool $default Whether or not to include the default values
    *
    * @return array
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function toArray($default = false)
    {
        $return = array();
        foreach (array_keys($this->_params) as $field) {
            $return[$field] = $this->_params($field);
        }
        return $return;
    }
    /**
    * Returns the table as an array
    *
    * @param bool $default Whether or not to include the default values
    *
    * @return array
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function fullArray($default = false)
    {
        $return = array();
        foreach ($this->_params as $field => $vals) {
            $return[$field] = $vals;
            $return[$field]["value"] = $this->_params($field);
        }
        return $return;
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($array)
    {
        foreach (array_keys($this->_params) as $field) {
            if (isset($array[$field])) {
                $this->_params($field, $array[$field]);
            }
        }
    }
    /**
    * Gets the total gain.
    *
    * @param int $channel The channel to get the gain for
    *
    * @return null
    */
    public function gain($channel = 0)
    {
        $gain = 1;
        if ($channel == 0) {
            $process = (int)$this->_params("process");
        } else {
            $process = (int)$this->_params("process1");
        }
        switch($process) {
        case 1:
            $gain *= 128;
            break;
        case 2:
            $gain /= 128;
            break;
        default:
            /* Do nothing */
        }
        if ($channel == 1) {
            $gain *= pow(2, $this->_params("ADC1PGA"));
        } else {
            $gain *= pow(2, $this->_params("ADC0PGA"));
        }
        return $gain;
    }
    /**
    * Gets the total gain.
    *
    * @param int $channel The channel to get the port for
    
    * @return null
    */
    public function port($channel = 0)
    {
        if (!$this->enabled($channel)) {
            // If we are given channel 0, but channel 0 isn't enabled, check
            // channel 1.
            if (($channel == 0) && $this->enabled(1)){
                $mux = $this->_params("ADC1CH");
                $channel = 1;
            } else {
                return null;
            }
        } else if ($channel == 1) {
            $mux = $this->_params("ADC1CH");
        } else {
            $mux     = $this->_params("ADC0CH");
            $channel = 0;
        }
        return $this->ports[$channel][$mux];
    }

}


?>
