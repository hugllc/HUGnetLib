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
class ADuCInputTable
{
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
                0x04 => "ADuC RTD",
                0x41 => "ADuC Voltage",
            ),
            "desc" => "First Driver",
        ),
        "driver1"  => array(
            "value" => 0xFF,
            'mask'  => 0xFF,
            "valid" => array(
                0x04 => "ADuC RTD",
                0x41 => "ADuC Voltage",
                0xFF => "None",
            ),
            "desc" => "Second Driver",
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
                0x04 => "Calculate Power and Impedance",
            ),
            "desc"  => "Immediate Processing Routine",
        ),
        "ADC0EN"    => array(
            'value' => 1,
            'bit'   => 15,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "No", 1 => "Yes"),
            'desc'  => "ADC0 Enable",
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
            'register' => "ADC0CON",
        ),
        "HIGHEXTREF0" => array(
            'value' => 0,
            'bit'   => 12,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Ref", 1 => "Ref/2"),
            'desc'  => "ADC0 High Reference",
            'register' => "ADC0CON",
        ),
        "AMP_CM"    => array(
            'value' => 0,
            'bit'   => 11,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Common Mode Input", 1 => "AVDD/2"),
            'desc'  => "ADC0 PGA Common Mode Voltage",
            'register' => "ADC0CON",
        ),
        "ADC0CODE"  => array(
            'value' => 0,
            'bit'   => 10,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Two's Compliment", 1 => "Unipolar"),
            'desc'  => "ADC0 Output Coding",
            'register' => "ADC0CON",
        ),
        "ADC0CH"    => array(
            'value' => 0x3,
            'bit'   => 6,
            'mask'  => 0xF,
            'bits'  => 4,
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
            'desc'  => "ADC0 Channel",
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
            'register' => "ADC0CON",
        ),
        "ADC1EN"      => array(
            'value' => 1,
            'bit'   => 15,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "No", 1 => "Yes"),
            'desc'  => "ADC1 Enable",
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
            'register' => "ADC1CON",
        ),
        "HIGHEXTREF1" => array(
            'value' => 0,
            'bit'   => 12,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Ref", 1 => "Ref/2"),
            'desc'  => "ADC1 High Reference",
            'register' => "ADC1CON",
        ),
        "ADC1CODE"    => array(
            'value' => 0,
            'bit'   => 11,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Two's Compliment", 1 => "Unipolar"),
            'desc'  => "ADC1 Output Coding",
            'register' => "ADC1CON",
        ),
        "ADC1CH"      => array(
            'value' => 0xC,
            'bit'   => 7,
            'mask'  => 0xF,
            'bits'  => 4,
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
            'desc'  => "ADC1 Channel",
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
            'register' => "ADC1CON",
        ),
        "CHOPEN" => array(
            'value' => 1,
            'bit'   => 15,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "No", 1 => "Yes"),
            'desc'  => "Chop Enable",
            'register' => "ADCFLT",
        ),
        "RAVG2"  => array(
            'value' => 0,
            'bit'   => 14,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "No", 1 => "Yes"),
            'desc'  => "Running Average By 2",
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
            'register' => "ADCFLT",
        ),
        "NOTCH2" => array(
            'value' => 0,
            'bit'   => 7,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "No", 1 => "Yes"),
            'desc'  => "Sinc Notch Filter Enable",
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
            'register' => "ADCFLT",
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
    */
    private function _sinc3Validate($value)
    {
        $avg = $this->_params["AF"]["value"];
        if ($value < 32) {
            // Do nothing here
        } else if ($value < 64) {
            if  ($avg > 7) {
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
    * @param string $set The values to set the register to
    *
    * @return 16 bit integer that is the FLT setup
    */
    public function immediateProcessRoutine($set = null)
    {
        return sprintf("%02X", $this->_params("process", $set));
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
            $set = hexdec($set);
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
        $ret .= $this->immediateProcessRoutine();
        $ret .= $this->register("ADC0CON");
        $ret .= $this->register("ADC1CON");
        $ret .= $this->register("ADCFLT");
        $ret .= $this->driver0();
        $ret .= $this->driver1();
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
        if (strlen($string) === 20) {
            $this->priority(substr($string, 0, 2));
            $this->immediateProcessRoutine(substr($string, 2, 2));
            $this->register("ADC0CON", substr($string, 4, 4));
            $this->register("ADC1CON", substr($string, 8, 4));
            $this->register("ADCFLT", substr($string, 12, 4));
            $this->driver0(substr($string, 16, 2));
            $this->driver1(substr($string, 18, 2));
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
    */
    public function toArray($default = false)
    {
        $return = array();
        foreach ($this->_params as $field => $vals) {
            $return[$field] = $this->_params($field);
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
        foreach ($this->_params as $field => $vals) {
            if (isset($array[$field])) {
                $this->_params($field, $array[$field]);
            }
        }
    }

}


?>
