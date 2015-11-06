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
class LeadAcidBatteryTable
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
     * This is the order we save the values in when encoding/decoding
     */
    protected $paramOrder = array(
        "BulkChargeDwellTime",
        "BulkChargeCoeff",
        "BulkChargeVoltage",
        "BulkChargeTriggerVoltage",
        "BulkChargeKickoutCurrent",
        "FloatCoeff",
        "FloatVoltage",
        "ResumeVoltage",
        "CutoffVoltage",
        "MinimumVoltage"
    );
    /**
    * This is where we setup the power object
    */
    protected $params = array(
        "BulkChargeDwellTime" => array(
            "value" => 3600,
            'signed' => false,
            "desc"  => "Bulk Charge Dwell Time (s)",
            'longDesc' => "This is the total time to stay in bulk charge mode (s)",
            'size'  => 7,
            'bytes' => 2,
            'min' => 0,
            'max' => 65535,
        ),
        "BulkChargeCoeff" => array(
            "value" => 1,
            'signed' => true,
            "desc"  => "Bulk Charge Temp Coeff (mV/&deg;C)",
            'longDesc' => "Temperature coefficient for the Bulk Charge voltage (mV/&deg;C)",
            'size'  => 5,
            'bytes' => 2,
            'min' => -10000,
            'max' => 10000,
        ),
        "BulkChargeVoltage" => array(
            "value" => 14000,
            'signed' => false,
            "desc"  => "Bulk Charge Voltage (mV)",
            'longDesc' => "The voltage to hold the battery at when Bulk Charging (mV)",
            'size'  => 6,
            'bytes' => 4,
            'min' => 0,
            'max' => 18000,
        ),
        "BulkChargeTriggerVoltage" => array(
            "value" => 3000,
            'signed' => false,
            "desc"  => "Bulk Charge Trigger Voltage (mV)",
            'longDesc' => "Bulk charge if below this voltage (mV)",
            'size'  => 6,
            'bytes' => 4,
            'min' => 0,
            'max' => 31000,
        ),
        "BulkChargeKickoutCurrent" => array(
            "value" => 12000,
            'signed' => false,
            "desc"  => "Bulk Charge Kickout Current (mA)",
            'longDesc' => "Exit bulk charge if the current is below this value (mA)",
            'size'  => 6,
            'bytes' => 4,
            'min' => 0,
            'max' => 18000,
        ),
        "FloatCoeff" => array(
            "value" => 1,
            'signed' => true,
            "desc"  => "Float Temp Coeff (mV/&deg;C)",
            'longDesc' => "Temperature coefficient for Float Voltage (mV/C)",
            'size'  => 5,
            'bytes' => 2,
            'min' => -10000,
            'max' => 10000,
        ),
        "FloatVoltage" => array(
            "value" => 13500,
            'signed' => false,
            "desc"  => "Float Voltage (mV)",
            'longDesc' => "Voltage to float at when the battery is fully charged (mV)",
            'size'  => 6,
            'bytes' => 4,
            'min' => 0,
            'max' => 18000,
        ),
        "ResumeVoltage" => array(
            "value" => 11000,
            'signed' => false,
            "desc"  => "Resume Discharge Voltage (mV)",
            'longDesc' => "Resume discharge if battery is above this voltage (mV)",
            'size'  => 6,
            'bytes' => 4,
            'min' => 0,
            'max' => 18000,
        ),
        "CutoffVoltage" => array(
            "value" => 10500,
            'signed' => false,
            "desc"  => "Cutoff Voltage (mV)",
            'longDesc' => "No discharge below this voltage (mV)",
            'size'  => 6,
            'bytes' => 4,
            'min' => 0,
            'max' => 18000,
        ),
        "MinimumVoltage" => array(
            "value" => 1000,
            'signed' => false,
            "desc"  => "Minimum Voltage (mV)",
            'longDesc' => "If the port voltage is below this, there is no battery present (mV)",
            'size'  => 6,
            'bytes' => 4,
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
        $object = new LeadAcidBatteryTable($power, $config);
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
                if ($set < $par["min"]) {
                    $set = $par["min"];
                } else if ($set > $par["max"]) {
                    $set = $par["max"];
                }
            }
            $par["value"] = $set;
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
    * This builds the string for the levelholder.
    *
    * @param int $val   The value to use
    * @param int $bytes The number of bytes to set
    *
    * @return string The string
    */
    protected function encodeInt($val, $bytes = 4)
    {
        $val = (int)$val;
        for ($i = 0; $i < $bytes; $i++) {
            $str .= sprintf(
                "%02X",
                ($val >> ($i * 8)) & 0xFF
            );
        }
        return $str;

    }
    /**
    * This builds the string for the levelholder.
    *
    * @param string $val    The value to use
    * @param int    $bytes  The number of bytes to set
    * @param bool   $signed If the number is signed or not
    *
    * @return string The string
    */
    protected function decodeInt($val, $bytes = 4, $signed = false)
    {
        $int = 0;
        for ($i = 0; $i < $bytes; $i++) {
            $int += hexdec(substr($val, ($i * 2), 2))<<($i * 8);
        }
        $bits = $bytes * 8;
        $int = (int)($int & (pow(2, $bits) - 1));
        if ($signed) {
            $int = $this->signedInt($int, $bytes);
        }
        return $int;

    }
    /**
    * This builds the string for the levelholder.
    *
    * @param int $val   The value to use
    * @param int $bytes The number of bytes to set
    *
    * @return string The string
    */
    protected function signedInt($val, $bytes = 4)
    {
        $bits = $bytes * 8;
        /* Calculate the top bit */
        $topBit = pow(2, ($bits - 1));
        /* Check to see if the top bit is set */
        if (($val & $topBit) == $topBit) {
            /* This is a negative number */
            $val = -(pow(2, $bits) - $val);
        }
        return $val;

    }
    /**
    * This takes the class and makes it into a setup string
    *
    * @return string The encoded string
    */
    public function encode()
    {
        $ret  = "";
        foreach ($this->paramOrder as $key) {
            $info = &$this->params[$key];
            $ret .= $this->encodeInt($this->params($key), $info["bytes"]);
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
        $ptr = 0;
        $ret = true;
        foreach ($this->paramOrder as $key) {
            $info = &$this->params[$key];
            $value = $this->decodeInt(
                substr($string, $ptr, ($info["bytes"] * 2)),
                $info["bytes"],
                $info["signed"]
            );
            $this->params($key, $value);
            $ptr += $info["bytes"] * 2;
            if ($ptr > strlen($string)) {
                $ret = false;
                break;
            }
        }
        return $ret;
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
    /**
    * Returns an array of the pins and stuff this one uses
    *
    * @return null
    */
    public function uses()
    {
        return array();
    }

}


?>
