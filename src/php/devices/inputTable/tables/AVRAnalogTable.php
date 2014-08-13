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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class AVRAnalogTable
{
    /**
    * This is where we store our sensor object
    */
    private $_sensor;
    /**
    * This is where we store our sensor object
    */
    protected $subdriver = array(
        0x00 => array(
            "DEFAULT"            => 0,
        ),
        0x02 => array(
            "DEFAULT"            => 0,
            "AVRB57560G0103F000" => 1,
            "imcSolar"           => 2,
            "potDirection"       => 3,
        ),
        0x10 => array(
            "DEFAULT"            => 0,
            "chsMss"             => 0,
        ),
        0x30 => array(
            "DEFAULT"            => 0,
            "OSRAM BPW-34"       => 0,
        ),
        0x40 => array(
            "DEFAULT"            => 0,
            "BARO4"              => 1,
            "GA100"              => 2,
            "HitachiVFDFan"      => 3,
            "fetBoard"           => 4,
        ),
        0x50 => array(
            "DEFAULT"            => 0,
            "dwyer616"           => 1,
            "fetBoard"           => 2,
        ),
    );
    /**
    * This is where we setup the sensor object
    */
    protected $params = array(
        "driver"  => array(
            "value" => "02:DEFAULT",
            "valid" => array(
                "00:DEFAULT"                 => "BC2322 Thermistor (Old)",
                "02:DEFAULT"                 => "BC2322 Thermistor",
                "02:AVRB57560G0103F000"      => "B5756 Thermistor",
                "02:imcSolar"                => "IMC Solar Temperature",
                "02:potDirection"            => "POT Direction",
                "10:chsMss"                  => "CHS MSS Humidity Sensor",
                "30:OSRAM BPW-34"            => "Diode Light Sensor",
                "40:DEFAULT"                 => "Generic Voltage",
                "40:BARO4"                   => "Barometric Pressure Sensor",
                "40:GA100"                   => "GA100 Pressure Sensor",
                "40:HitachiVFDFan"           => "VFD Fan Speed",
                "40:fetBoard"                => "FET Board Voltage",
                "50:DEFAULT"                 => "Generic Current",
                "50:dwyer616"                => "Dwyer 616 Pressure Sensor",
                "50:fetBoard"                => "FET Board Current",
            ),
            "desc" => "Driver",
            'longDesc' => "The driver to use to sort out data for this ADC",
        ),
        "priority" => array(
            "value" => 0,
            'mask'  => 0xFF,
            "desc"  => "Priority",
            'size'  => 4,
            'longDesc' => "(0-255) The number of 1/128 s ticks to wait before 
                          running",
        ),
        "offset" => array(
            "value" => 0,
            'mask'  => 0xFFFF,
            "desc"  => "Offset",
            'longDesc' => "This is a calibration offset.  It is in the units of
                           the driver above.",
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
            ),
            'desc'  => "Reference",
            'longDesc' => "The reference to use",
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
            'longDesc' => "How to align the result in the 16bit number returned",
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
            ),
            'desc'  => "MUX Setting",
            'longDesc' => "Which port to use",
            'register' => "ADMUX",
        ),
    );
    /**
    * This is where we store our sensor object
    */
    protected $ports = array(
        0 => "ADC0",
        1 => "ADC1",
        2 => "ADC2",
        3 => "ADC3",
        4 => "ADC4",
        5 => "ADC5",
        6 => "ADC6",
        7 => "ADC7",
    );
    /**
    * This is the constructor
    *
    * @param object $sensor The sensor object we are working with
    * @param mixed  $config This could be a string or array or null
    */
    protected function __construct($sensor, $config = null)
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
        $object = new AVRAnalogTable($sensor, $config);
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
            foreach ($this->params as $field => $vals) {
                if (($vals["register"] === $register) && !isset($vals["hidden"])) {
                    $mask = $vals["mask"] << $vals["bit"];
                    $val = ($set & $mask) >> $vals["bit"];
                    $this->params($field, $val);
                }
            }
        }
        $ret  = 0;
        $bits = 0;
        foreach ($this->params as $field => $vals) {
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
                $array = array_flip((array)$this->subdriver[$driver]);
                $set = sprintf("%02x:%s", $driver, $array[$subdriver]);
            }
        }
        $driver = $this->params("driver", $set);
        $drivers = explode(":", $driver);
        $driver = hexdec($drivers[0]);
        return sprintf(
            "%02X%02X",
            $driver,
            $this->subdriver[$driver][$drivers[1]]
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
        $ret .= $this->driver();
        $ret .= sprintf("%02X", $this->params("priority"));
        $ret .= $this->register("ADMUX");
        $offset = $this->params("offset");
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
            $this->params("priority", hexdec(substr($string, 4, 2)));
            $this->register("ADMUX", hexdec(substr($string, 6, 2)));
            $this->params(
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
    * Gets the total gain.
    *
    * @return null
    */
    public function gain()
    {
        return 1;
    }
    /**
    * Gets port this is using
    *
    * @return null
    */
    public function port()
    {
        $mux = $this->params("MUX");
        return $this->ports[$mux];
    }
    /**
    * Gets the ports this can possibly use.
    *
    * @return null
    */
    public function ports()
    {
        return $this->ports;
    }
    /**
    * Returns an array of the pins and stuff this one uses
    *
    * @return null
    */
    public function uses()
    {
        $uses = array();
        $ports = $this->port();
        $ports = str_replace("+", "", $ports);
        $ports = str_replace("-", "", $ports);
        $ports = str_replace(" ", "", $ports);
        foreach (explode(",", $ports) as $port) {
            $uses[] = trim($port);
        }
        return $uses;
    }

}


?>
