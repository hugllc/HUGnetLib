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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCPWM
{
    /**
    * This is where we store our sensor object
    */
    private $_sensor;
    /**
    * This is where we setup the sensor object
    */
    private $_params = array(
        "PWM0LEN" => array(
            "value" => 0xFFFF,
            'mask'  => 0xFFFF,
            "desc"  => "Freq Counter 0",
            'longDesc' => "This sets the maximum frequency for PWM1",
            'size'  => 6,
        ),
        "PWM1LEN" => array(
            "value" => 0xFFFF,
            'mask'  => 0xFFFF,
            "desc"  => "Freq Counter 1",
            'longDesc' => "This sets the maximum frequency for PWM3",
            'size'  => 6,
        ),
        "PWM2LEN" => array(
            "value" => 0xFFFF,
            'mask'  => 0xFFFF,
            "desc"  => "Freq Counter 2",
            'longDesc' => "This sets the maximum frequency for PWM5",
            'size'  => 6,
        ),
        "SYNC"    => array(
            'value' => 0,
            'bit'   => 14,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "No", 1 => "Yes"),
            'desc'  => "Enables PWM synchronization",
            'longDesc' => "This synchronizes the PWM to something.",
            'register' => "PWMCON",
        ),
        "PWM5INV"    => array(
            'value' => 0,
            'bit'   => 13,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Normal", 1 => "Invert"),
            'desc'  => "Invert PWM5",
            'longDesc' => "Inverts the output of PWM5",
            'register' => "PWMCON",
        ),
        "PWM3INV"    => array(
            'value' => 0,
            'bit'   => 12,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Normal", 1 => "Invert"),
            'desc'  => "Invert PWM3",
            'longDesc' => "Inverts the output of PWM3",
            'register' => "PWMCON",
        ),
        "PWM1INV"    => array(
            'value' => 0,
            'bit'   => 11,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Normal", 1 => "Invert"),
            'desc'  => "Invert PWM1",
            'longDesc' => "Inverts the output of PWM1",
            'register' => "PWMCON",
        ),
        "PWMTRIP"    => array(
            'value' => 0,
            'bit'   => 10,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Disable"),
            'desc'  => "PWM Interrupt",
            'register' => "PWMCON",
            'hidden' => true,
        ),
        "ENA"    => array(
            'value' => 0,
            'bit'   => 9,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Disable"),
            'desc'  => "PWM Enable (h-bridge)",
            'register' => "PWMCON",
            'hidden' => true,
        ),
        "PWMCP"    => array(
            'value' => 0,
            'bit'   => 6,
            'mask'  => 0x7,
            'bits'  => 3,
            'valid' => array(
                0 => "UCLK/2",
                1 => "UCLK/4",
                2 => "UCLK/8",
                3 => "UCLK/16",
                4 => "UCLK/32",
                5 => "UCLK/64",
                6 => "UCLK/128",
                7 => "UCLK/256",
            ),
            'desc'  => "Clock Prescaler",
            'longDesc' => "Sets the overall frequency that the PWM will run at",
            'register' => "PWMCON",
        ),
        "POINV"    => array(
            'value' => 0,
            'bit'   => 5,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Normal", 1 => "Invert"),
            'desc'  => "Invert All Channels",
            'longDesc' => "Inverts the output of all of the PWM channels",
            'register' => "PWMCON",
        ),
        "HOFF"    => array(
            'value' => 0,
            'bit'   => 4,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(
                0 => "Normal",
                1 => "PWM0&2 High/PWM1&3 Low"
            ),
            'desc'  => "High Side Off",
            'longDesc' => "",
            'register' => "PWMCON",
        ),
        "LCOMP"    => array(
            'value' => 0,
            'bit'   => 3,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Reserved"),
            'desc'  => "Reserved",
            'register' => "PWMCON",
            'hidden' => true,
        ),
        "DIR"    => array(
            'value' => 0,
            'bit'   => 2,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(
                0 => "PWM0:1 High/PWM2:3 Low",
                1 => "PWM2:3 High/PWM0:1 Low"
            ),
            'desc'  => "Direction Control",
            'longDesc' => "Controls the direction of the PWM",
            'register' => "PWMCON",
        ),
        "HMODE"    => array(
            'value' => 0,
            'bit'   => 1,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Disable"),
            'desc'  => "H-Bridge Mode",
            'register' => "PWMCON",
            'hidden' => true,
        ),
        "PWMEN"    => array(
            'value' => 1,
            'bit'   => 0,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(1 => "Enable"),
            'desc'  => "PWM Enable",
            'register' => "PWMCON",
            'hidden' => true,
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
        $object = new ADuCPWM($sensor, $config);
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
    * @param int    $count The number of the length register to set
    * @param string $set   The values to set the register to
    *
    * @return 16 bit integer that is the FLT setup
    */
    public function length($count, $set = null)
    {
        $count = (int)$count;
        return sprintf("%04X", $this->_params("PWM".$count."LEN", $set));
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
            $set = (int)hexdec($set);
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
        foreach (array("PWMCON") as $reg) {
            $value = $this->register($reg);
            $ret .= substr($value, 2, 2);
            $ret .= substr($value, 0, 2);
        }
        foreach (array(0, 1, 2) as $key) {
            $value = $this->length($key);
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
        if (strlen($string) >= 16) {
            $this->register("PWMCON", substr($string, 2, 2).substr($string, 0, 2));
            $this->length(0, substr($string, 6, 2).substr($string, 4, 2));
            $this->length(1, substr($string, 10, 2).substr($string, 8, 2));
            $this->length(2, substr($string, 14, 2).substr($string, 12, 2));
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

}


?>
