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
namespace HUGnet\devices\outputTable\tables;
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCDAC
{
    /**
    * This is where we store our sensor object
    */
    private $_sensor;
    /**
    * This is where we setup the sensor object
    */
    private $_params = array(
        "reserved"    => array(
            'value' => 0,
            'bit'   => 10,
            'mask'  => 0x3F,
            'bits'  => 6,
            'valid' => array(0 => "Reserved"),
            'desc'  => "Reserved Space",
            'register' => "DAC0CON",
            'hidden' => true,
        ),
        "DACPD"    => array(
            'value' => 0,
            'bit'   => 9,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "DAC On"),
            'desc'  => "DAC Power",
            'register' => "DAC0CON",
            'hidden' => true,
        ),
        "DACBUFLP"    => array(
            'value' => 0,
            'bit'   => 8,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Normal", 1 => "Low Power"),
            'desc'  => "Power Mode",
            'longDesc' => "Normal or low power mode",
            'register' => "DAC0CON",
        ),
        "OPAMP"    => array(
            'value' => 0,
            'bit'   => 7,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Normal", 1 => "Op Amp Mode"),
            'desc'  => "Buffer Mode",
            'longDesc' => "Mode to set the buffer in",
            'register' => "DAC0CON",
        ),
        "DACBUFBYPASS"    => array(
            'value' => 0,
            'bit'   => 6,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Normal", 1 => "Bypass Buffer"),
            'desc'  => "Buffer Bypass",
            'longDesc' => "Don't bypass the buffer unless you have a true, high
                           impedance input.  It will not work.",
            'register' => "DAC0CON",
        ),
        "DACCLK"    => array(
            'value' => 0,
            'bit'   => 5,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "HCLK", 1 => "Timer1"),
            'desc'  => "Update Timer",
            'longDesc' => "How often to update the output",
            'register' => "DAC0CON",
        ),
        "DACCLR"    => array(
            'value' => 1,
            'bit'   => 4,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(1 => "Normal"),
            'desc'  => "DAC Clear",
            'register' => "DAC0CON",
            'hidden' => true,
        ),
        "DACMODE"    => array(
            'value' => 1,
            'bit'   => 3,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "12-bit", 1 => "16-bit interpolation"),
            'desc'  => "Interpolation Mode",
            'longDesc' => "Should the DAC interpolate to get a little more 
                           resolution?  An RC filter will be needed on the output
                           if this is enabled.",
            'register' => "DAC0CON",
        ),
        "Rate"    => array(
            'value' => 0,
            'bit'   => 2,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "UCLK/16", 1 => "UCLK/12"),
            'desc'  => "Interpolation Rate",
            'longDesc' => "The rate at which the interpolation should be done.",
            'register' => "DAC0CON",
        ),
        "Range"    => array(
            'value' => 3,
            'bit'   => 0,
            'mask'  => 0x3,
            'bits'  => 2,
            'valid' => array(
                0 => "0V to 1.2V",
                1 => "VREF- to VREF+",
                2 => "EXT_REF2IN- to EXTREF2IN+",
                3 => "0V tto AVDD",
            ),
            'desc'  => "Output Range",
            'longDesc' => "How wide should the output be?",
            'register' => "DAC0CON",
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
        $object = new ADuCDAC($sensor, $config);
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
                $set = (int)$set;
            }
            foreach ($this->_params as $field => $vals) {
                if (($vals["register"] === $register) && !isset($vals["hidden"])) {
                    $mask = $vals["mask"] << $vals["bit"];
                    $val = ($set & $mask) >> $vals["bit"];
                    $this->_params($field, $val);
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
            if (is_array($par["valid"]) && !isset($par["valid"][$set])) {
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
    public function encode()
    {
        $ret  = "";
        /* This is because encoding is little endian */
        foreach (array("DAC0CON") as $reg) {
            $value = $this->register($reg);
            $ret .= substr($value, 2, 2);
            $ret .= substr($value, 0, 2);
        }
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
        if (strlen($string) >= 4) {
            $this->register(
                "DAC0CON", 
                hexdec(substr($string, 2, 2).substr($string, 0, 2))
            );
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
        foreach ($this->_params as $field => $vals) {
            if ($vals["hidden"] !== true) {
                $return[$field] = $this->_params($field);
            }
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
            if ($vals["hidden"] !== true) {
                $return[$field] = $vals;
                $return[$field]["value"] = $this->_params($field);
            }
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
            if (isset($array[$field]) && !isset($vals["hidden"])) {
                $this->_params($field, $array[$field]);
            }
        }
    }
    /**
    * Returns an array of the pins and stuff this one uses
    *
    * @return null
    */
    public function uses()
    {
        return array("DAC");
    }

}


?>
