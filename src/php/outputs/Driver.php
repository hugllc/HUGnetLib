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
namespace HUGnet\outputs;
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
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class Driver
{
    /**
    * This is where we store the sensor.
    */
    private $_sensor = null;
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
    );
    /**
    * This is where all of the defaults are stored.
    */
    private $_default = array(
        "longName" => "Unknown Output",
        "shortName" => "Unknown",
    );
    /**
    * This is where all of the driver information is stored.
    *
    * Drivers must be registered here, otherwise they will never get loaded.  The
    * index in the root array is the driver name.  It should be exactly the same
    * as the driver class name.
    */
    private static $_drivers = array(
        "FF:DEFAULT"                 => "EmptyOutput",
    );
    /**
    * This is where the correlation between the drivers and the arch is stored.
    *
    * If a driver is not registered here, it will not be in the list of drivers
    * that can be chosen.
    *
    */
    private $_arch = array(
        "AVR" => array(
        ),
        "ADuC" => array(
        ),
        "all" => array(
            0xFF => "Empty Slot"
        ),
    );
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$sensor The sensor in question
    *
    * @return null
    */
    private function __construct(&$sensor)
    {
        $this->_sensor = &$sensor;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_sensor);
    }
    /**
    * This is the destructor
    *
    * @return object
    */
    public function sensor()
    {
        return $this->_sensor;
    }
    /**
    * This function creates the system.
    *
    * @param object &$sensor The sensor object
    *
    * @return null
    */
    protected static function &intFactory(&$sensor)
    {
        $class = get_called_class();
        $object = new $class($sensor);
        return $object;
    }
    /**
    * This function creates the system.
    *
    * @param string $driver  The driver to load
    * @param object &$sensor The sensor object
    *
    * @return null
    */
    public static function &factory($driver, &$sensor)
    {
        $class = '\\HUGnet\\outputs\\drivers\\'.$driver;
        $file = dirname(__FILE__)."/drivers/".$driver.".php";
        if (file_exists($file)) {
            include_once $file;
        }
        if (class_exists($class)) {
            return $class::factory($sensor);
        }
        include_once dirname(__FILE__)."/drivers/EmptyOutput.php";
        return \HUGnet\outputs\drivers\EmptyOutput::factory($sensor);
    }
    /**
    * Checks to see if a piece of data exists
    *
    * @param string $name The name of the property to check
    *
    * @return true if the property exists, false otherwise
    */
    public function present($name)
    {
        return !is_null($this->get($name, $this->sensor()));
    }
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    public function get($name)
    {
        $ret = null;
        if (isset($this->params[$name])) {
            $ret = $this->params[$name];
        } else if (isset($this->_default[$name])) {
            $ret = $this->_default[$name];
        }
        if (is_string($ret) && (strtolower(substr($ret, 0, 8)) === "getextra")) {
            $key = (int)substr($ret, 8);
            $ret = $this->getExtra($key);
        }
        return $ret;
    }
    /**
    * Returns all of the parameters and defaults in an array
    *
    * @return array of data from the sensor
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function toArray()
    {
        $return = array();
        $keys = array_merge(array_keys($this->_default), array_keys($this->params));
        foreach ($keys as $key) {
            $return[$key] = $this->get($key);
        }
        return $return;
    }
    /**
    * Returns the driver that should be used for a particular device
    *
    * @param mixed  $sid  The ID of the sensor
    * @param string $type The type of the sensor
    *
    * @return string The driver to use
    */
    public static function getDriver($sid, $type = "DEFAULT")
    {
        $try = array(
            sprintf("%02X", (int)$sid).":".$type,
            sprintf("%02X", (int)$sid),
            sprintf("%02X", (int)$sid).":DEFAULT",
        );
        foreach ($try as $mask) {
            if (isset(self::$_drivers[$mask])) {
                return self::$_drivers[$mask];
            }
        }
        return "EmptyOutput";
    }
    /**
    * Returns an array of types that this sensor could be
    *
    * @param int $sid The ID to check
    *
    * @return The extra value (or default if empty)
    */
    public static function getTypes($sid)
    {
        $array = array();
        $sensor = sprintf("%02X", (int)$sid);
        foreach ((array)self::$_drivers as $key => $driver) {
            $k = explode(":", $key);
            if (trim(strtoupper($k[0])) == $sensor) {
                $array[$k[1]] = $driver;
            }
        }
        return (array)$array;
    }
    /**
    * Registers an extra driver to be used by the class
    *
    * The new class will only be registered if it doesn't already exist
    *
    * @param string $key   The key to use for the class
    * @param string $class The class to use for the key
    *
    * @return null
    */
    public static function register($key, $class)
    {
        $driver = '\\HUGnet\\outputs\\drivers\\'.$class;
        if (class_exists($driver) && !isset(self::$_drivers[$key])) {
            self::$_drivers[$key] = $class;
        }
    }
    /**
    * Gets the extra values
    *
    * @param int $index The extra index to use
    *
    * @return The extra value (or default if empty)
    */
    public function getExtra($index)
    {
        $extra = (array)$this->sensor()->get("extra");
        if (!isset($extra[$index])) {
            $extra = $this->get("extraDefault");
        }
        return $extra[$index];
    }

    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string The string to decode
    *
    * @return array
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function decode($string)
    {
        /* Do nothing by default */
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function encode()
    {
        $string  = "";
        return $string;
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
    public function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        return null;
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
        $A = $this->strToInt($string);
        $ret = $this->channels();
        if ($this->get("storageType") == \HUGnet\units\Driver::TYPE_DIFF) {
            $ret[0]["value"] = $this->getReading(
                ($A - $prev["raw"]), $deltaT, $data, $prev
            );
            $ret[0]["raw"] = $A;
        } else {
            $ret[0]["value"] = $this->getReading(
                $A, $deltaT, $data, $prev
            );
        }
        return $ret;
    }
    /**
    * This makes a line of two ordered pairs, then puts $A on that line
    *
    * @param float $value The incoming value
    * @param float $Imin  The input minimum
    * @param float $Imax  The input maximum
    * @param float $Omin  The output minimum
    * @param float $Omax  The output maximum
    *
    * @return output rounded to 4 places
    */
    protected function linearBounded($value, $Imin, $Imax, $Omin, $Omax)
    {
        if (is_null($value)) {
            return null;
        }
        if ($Imax == $Imin) {
            return null;
        }
        if ($value > $Imax) {
            return null;
        }
        if ($value < $Imin) {
            return null;
        }
        $mult = bcdiv(bcsub($Omax, $Omin), bcsub($Imax, $Imin));
        $Yint = bcsub($Omax, bcmul($mult, $Imax));
        $Out = bcadd(bcmul($mult, $value), $Yint);
        return $Out;
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channels()
    {
        return array(
            array(
                "decimals" => $this->get("maxDecimals"),
                "units" => $this->get("storageUnit"),
                "maxDecimals" => $this->get("maxDecimals"),
                "storageUnit" => $this->get("storageUnit"),
                "unitType" => $this->get("unitType"),
                "dataType" => $this->get("storageType"),
            ),
        );
    }
    /**
    * Returns the driver that should be used for a particular device
    *
    * @return array The array of drivers that will work
    */
    public function getDrivers()
    {
        return (array)$this->_arch[$this->sensor()->device()->get("arch")]
            + (array)$this->_arch["all"];
    }
}


?>