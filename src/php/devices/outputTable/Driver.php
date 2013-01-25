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
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\outputTable;
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
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class Driver
{
    /**
    * This is where we store the output.
    */
    private $_output = null;
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
        "extraText" => array(),
        "extraDefault" => array(),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(),
        "min" => 0,
        "max" => 0,
        "zero" => 0,
    );
    /**
    * This is where all of the driver information is stored.
    *
    * Drivers must be registered here, otherwise they will never get loaded.  The
    * index in the root array is the driver name.  It should be exactly the same
    * as the driver class name.
    */
    private static $_drivers = array(
        "01:DEFAULT"                 => "ADuCDAC",
        "FE:DEFAULT"                 => "NullOutput",
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
            0x01 => "ADuC DAC",
        ),
        "all" => array(
            0xFE => "Null Output",
            0xFF => "Empty Slot"
        ),
    );
    /**
    * This is where we store our entry in the input table
    */
    private $_entry;
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$output The output in question
    *
    * @return null
    */
    protected function __construct(&$output)
    {
        $this->_output = &$output;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_output);
    }
    /**
    * This is the destructor
    *
    * @return object
    */
    public function output()
    {
        return $this->_output;
    }
    /**
    * Returns the driver object
    *
    * @return object The driver requested
    */
    protected function &entry()
    {
        $file = dirname(__FILE__)."/tables/".$this->entryClass.".php";
        if (!is_object($this->_entry) && file_exists($file)) {
            include_once $file;
            $class = "\\HUGnet\\devices\\outputTable\\tables\\".$this->entryClass;
            $entry = $class::factory(
                $this
            );
            $this->_entry = &$entry;
        }
        return $this->_entry;
    }
    /**
    * This function creates the system.
    *
    * @param string $driver  The driver to load
    * @param object &$output The output object
    *
    * @return null
    */
    public static function &factory($driver, &$output)
    {
        $class = '\\HUGnet\\devices\\outputTable\\drivers\\'.$driver;
        $file = dirname(__FILE__)."/drivers/".$driver.".php";
        if (file_exists($file)) {
            include_once $file;
        }
        if (class_exists($class)) {
            return new $class($output);
        }
        include_once dirname(__FILE__)."/drivers/EmptyOutput.php";
        return new \HUGnet\devices\outputTable\drivers\EmptyOutput($output);
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
        return !is_null($this->get($name, $this->output()));
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
    * @return array of data from the output
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
    * @param mixed  $sid  The ID of the output
    * @param string $type The type of the output
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
    * Returns an array of types that this output could be
    *
    * @param int $sid The ID to check
    *
    * @return The extra value (or default if empty)
    */
    public static function getTypes($sid)
    {
        $array = array();
        $output = sprintf("%02X", (int)$sid);
        foreach ((array)self::$_drivers as $key => $driver) {
            $k = explode(":", $key);
            if (trim(strtoupper($k[0])) == $output) {
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
        $driver = '\\HUGnet\\devices\\outputTable\\drivers\\'.$class;
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
        $extra = (array)$this->output()->get("extra");
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
    */
    public function encode()
    {
        $string  = $this->output()->get("RawSetup");
        if (!is_string($string)) {
            $string = "";
        }
        return $string;
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
                "min" => $this->get("min"),
                "max" => $this->get("max"),
                "label" => $this->get("shortName"),
                "index" => 0,
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
        return (array)$this->_arch[$this->output()->device()->get("arch")]
            + (array)$this->_arch["all"];
    }
}


?>
