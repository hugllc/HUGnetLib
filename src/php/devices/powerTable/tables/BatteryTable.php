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
namespace HUGnet\devices\powerTable\tables;
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
class BatteryTable
{
    /**
    * This is where we store our power object
    */
    private $_power;
    /**
    * This is where we store our power object
    */
    protected $subdriver = array(
    );
    /**
    * This is where we setup the power object
    */
    protected $params = array(
        "AbsorbDwellTime" => array(
            "value" => 0,
            'signed' => false,
            "desc"  => "Offset",
            'longDesc' => "Time to absorb (s)",
            'size'  => 7,
            'min' => 0,
            'max' => 65535,
        ),
        "AbsorbCoefficient" => array(
            "value" => 0,
            'signed' => true,
            "desc"  => "Offset",
            'longDesc' => "Temperature coefficient for Absorbtion (mV/C)",
            'size'  => 5,
            'min' => -10000,
            'max' => 10000,
        ),
        "FloatCoefficient" => array(
            "value" => 0,
            'signed' => true,
            "desc"  => "Offset",
            'longDesc' => "Temperature coefficient for Float (mV/C)",
            'size'  => 5,
            'min' => -10000,
            'max' => 10000,
        ),
        "AbsorbVoltage" => array(
            "value" => 0,
            'signed' => true,
            "desc"  => "Offset",
            'longDesc' => "The voltage to absorb at (mV)",
            'size'  => 6,
            'min' => 0,
            'max' => 18000,
        ),
        "FloatVoltage" => array(
            "value" => 0,
            'signed' => false,
            "desc"  => "Offset",
            'longDesc' => "Voltage to float at (mV)",
            'size'  => 6,
            'min' => 0,
            'max' => 18000,
        ),
        "BulkChargeTriggerVoltage" => array(
            "value" => 0,
            'signed' => false,
            "desc"  => "Offset",
            'longDesc' => "Charge if below this voltage (mV)",
            'size'  => 6,
            'min' => 0,
            'max' => 18000,
        ),
        "ResumeVoltage" => array(
            "value" => 0,
            'signed' => false,
            "desc"  => "Offset",
            'longDesc' => "Resume discharge at this voltage (mV)",
            'size'  => 6,
            'min' => 0,
            'max' => 18000,
        ),
        "CutoffVoltage" => array(
            "value" => 0,
            'signed' => false,
            "desc"  => "Offset",
            'longDesc' => "No discharge below this (mV)",
            'size'  => 6,
            'min' => 0,
            'max' => 18000,
        ),
        "MinimumVoltage" => array(
            "value" => 0,
            'signed' => false,
            "desc"  => "Offset",
            'longDesc' => "No battery below this voltage (mV)",
            'size'  => 6,
            'min' => 0,
            'max' => 18000,
        ),
    );
    /**
    * This is the constructor
    *
    * @param object $power The power object we are working with
    * @param mixed  $config This could be a string or array or null
    */
    protected function __construct($power, $config = null)
    {
        $this->_power = &$power;
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
        unset($this->_power);
    }
    /**
    * This is the constructor
    *
    * @param object $power The power object we are working with
    * @param mixed  $config This could be a string or array or null
    *
    * @return object The new object
    */
    public static function &factory($power, $config = null)
    {
        $object = new BatteryTable($power, $config);
        return $object;
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param string $param The parameter to set
    * @param string $set   The values to set the register to
    *
    * @return 16 bit integer in a hex string
    */
    protected function params($param, $set = null)
    {
        if (is_int($set) || is_string($set)) {
            $par = &$this->params[$param];
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
        return $this->params[$param]["value"];
    }
    /**
    * This gets a parameter
    *
    * @param string $param The parameter to set
    *
    * @return 16 bit integer in a hex string
    */
    public function get($param)
    {
        return $this->params($param);
    }
    /**
    * This takes the class and makes it into a setup string
    *
    * @return string The encoded string
    */
    public function encode()
    {
        $ret  = "";
        /*
        $ret .= $this->driver();
        $ret .= sprintf("%02X", $this->params("priority"));
        $ret .= $this->register("ADMUX");
        $offset = $this->params("offset");
        $ret .= sprintf("%02X%02X", ($offset & 0xFF), (($offset>>8) & 0xFF));
        */
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
    /*
        if (strlen($string) >= 12) {
            $this->driver(substr($string, 0, 4));
            $this->params("priority", hexdec(substr($string, 4, 2)));
            $this->register("ADMUX", hexdec(substr($string, 6, 2)));
            $this->params(
                "offset",
                hexdec(substr($string, 10, 2).substr($string, 8, 2))
            );
            return true;
        }
        */
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
        foreach (array_keys($this->params) as $field) {
            $return[$field] = $this->params($field);
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
        foreach ($this->params as $field => $vals) {
            if ($vals["hidden"] !== true) {
                $return[$field] = $vals;
                $return[$field]["value"] = $this->params($field);
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
        foreach (array_keys($this->params) as $field) {
            if (isset($array[$field])) {
                $this->params($field, $array[$field]);
            }
        }
    }

}


?>
