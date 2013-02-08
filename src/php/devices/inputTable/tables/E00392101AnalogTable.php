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
class E00392101AnalogTable
{
    /** This is our power calculator. */
    const IPR_POWER = 4;
    /**
    * This is where we store our sensor object
    */
    private $_sensor;
    /**
    * This is where we store our sensor object
    */
    private $_subdriver = array(
        0x02 => array(
            "ControllerTemp" => 0,
        ),
        0x40 => array(
            "ControllerVoltage" => 0,
        ),
        0x50 => array(
            "ControllerVoltage" => 0,
        ),
    );
    /**
    * This is where we setup the sensor object
    */
    private $_params = array(
        "driver"  => array(
            "value" => "02:ControllerTemp",
            "valid" => array(
                "02:ControllerTemp"    => "Temperature Sensor",
                "40:ControllerVoltage" => "Voltage Sensor",
                "50:ControllerCurrent" => "Current Sensor",
            ),
            "desc" => "Driver",
        ),
        "priority" => array(
            "value" => 0,
            'mask'  => 0xFF,
            "desc"  => "Priority",
            'size'  => 4,
        ),
        "offset" => array(
            "value" => 0,
            'mask'  => 0xFFFF,
            "desc"  => "Offset",
            'size'  => 7,
        ),
        "REFS"    => array(
            'value' => 1,
            'bit'   => 6,
            'mask'  => 0x3,
            'bits'  => 2,
            'valid' => array(
                0 => "AREF",
                1 => "AVCC",
                3 => "Internal 2.56V",
            ),
            'desc'  => "Reference",
            'register' => "ADMUX",
        ),
        "ADLAR"  => array(
            'value' => 0x1,
            'bit'   => 5,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(
                0 => "Lower 10 bits",
                1 => "Upper 10 bits",
            ),
            'desc'  => "Result Location",
            'register' => "ADMUX",
        ),
        "MUX" => array(
            'value' => 0,
            'bit'   => 0,
            'mask'  => 0x1F,
            'bits'  => 5,
            'valid' => array(
                0  => "ADC0 Single Ended",
                1  => "ADC1 Single Ended",
                2  => "ADC2 Single Ended",
                3  => "ADC3 Single Ended",
                4  => "ADC4 Single Ended",
                5  => "ADC5 Single Ended",
                6  => "ADC6 Single Ended",
                7  => "ADC7 Single Ended",
                8  => "ADC0/ADC0 Differential 10x",
                9  => "ADC1/ADC0 Differential 10x",
                10 => "ADC0/ADC0 Differential 200x",
                11 => "ADC1/ADC0 Differential 200x",
                12 => "ADC2/ADC2 Differential 10x",
                13 => "ADC3/ADC2 Differential 10x",
                14 => "ADC2/ADC2 Differential 200x",
                15 => "ADC3/ADC2 Differential 200x",
                16 => "ADC0/ADC1 Differential 1x",
                17 => "ADC1/ADC1 Differential 1x",
                18 => "ADC2/ADC1 Differential 1x",
                19 => "ADC3/ADC1 Differential 1x",
                20 => "ADC4/ADC1 Differential 1x",
                21 => "ADC5/ADC1 Differential 1x",
                22 => "ADC6/ADC1 Differential 1x",
                23 => "ADC7/ADC1 Differential 1x",
                24 => "ADC0/ADC2 Differential 1x",
                25 => "ADC1/ADC2 Differential 1x",
                26 => "ADC2/ADC2 Differential 1x",
                27 => "ADC3/ADC2 Differential 1x",
                28 => "ADC4/ADC2 Differential 1x",
                29 => "ADC5/ADC2 Differential 1x",
                30 => "1.22V Single Ended",
                31 => "0V Single Ended"
            ),
            'desc'  => "MUX Setting",
            'register' => "ADMUX",
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
        $object = new E00392101AnalogTable($sensor, $config);
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
    * @param string $set The values to set the register to
    *
    * @return 16 bit integer that is the FLT setup
    */
    public function driver($set = null)
    {
        if (is_string($set)) {
            if (strpos($set, ":") === false) {
                $driver = hexdec(substr($set, 0, 2));
                $subdriver = hexdec(substr($set, 2, 2));
                $array = array_flip((array)$this->_subdriver[$driver]);
                $set = sprintf("%02x:%s", $driver, $array[$subdriver]);
            }
        }
        $driver = $this->_params("driver", $set);
        $drivers = explode(":", $driver);
        $driver = hexdec($drivers[0]);
        return sprintf(
            "%02X%02X",
            $driver,
            $this->_subdriver[$driver][$drivers[1]]
        );
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
        if (is_int($set) || is_string($set)) {
            $par = &$this->_params[$param];
            if (is_int($set)) {
                $set &= $par["mask"];
            }
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
        $ret .= $this->driver();
        $ret .= sprintf("%02X", $this->_params("priority"));
        $ret .= $this->register("ADMUX");
        $offset = $this->_params("offset");
        $ret .= sprintf("%02X%02X", ($offset & 0xFF), (($offset>>8) & 0xFF));
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
        if (strlen($string) >= 12) {
            $this->driver(substr($string, 0, 4));
            $this->_params("priority", hexdec(substr($string, 4, 2)));
            $this->register("ADMUX", hexdec(substr($string, 6, 2)));
            $this->_params(
                "offset",
                hexdec(substr($string, 10, 2).substr($string, 8, 2))
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
    * @return null
    */
    public function gain()
    {
        $mux  = (int)$this->_params("MUX");
        $gain = 1;
        switch($mux) {
        case 8:
        case 9:
        case 12:
        case 13:
            $gain = 10;
            break;
        case 10:
        case 11:
        case 14:
        case 15:
            $gain = 200;
            break;
        default:
            /* Do nothing */
        }
        return $gain;
    }

}


?>
