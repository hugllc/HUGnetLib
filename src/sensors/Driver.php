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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\sensors;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our units class */
require_once dirname(__FILE__)."/../base/UnitsBase.php";
/**
 * Base driver class for devices.
 *
 * This class deals with loading the drivers and figuring out what driver needs
 * to be loaded.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
abstract class Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "unitType" => "asdf", /* This is for test value only */
        "testParam" => "12345", /* This is for test value only */
        "extraDefault" => array(2,3,5,7,11),
    );
    /**
    * This is where all of the defaults are stored.
    */
    private $_default = array(
        "longName" => "Unknown Sensor",
        "shortName" => "Unknown",
        "unitType" => "unknown",
        "bound" => false,                // This says if this sensor is changeable
        "virtual" => false,              // This says if we are a virtual sensor
        "extraText" => array(),
        "extraDefault" => array(),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(),
        "storageUnit" => "unknown",
        "storageType" => \UnitsBase::TYPE_RAW,  // This is the dataType as stored
        "maxDecimals" => 2,
    );
    /**
    * This is where all of the driver information is stored.
    *
    * Drivers must be registered here, otherwise they will never get loaded.  The
    * index in the root array is the driver name.  It should be exactly the same
    * as the driver class name.
    */
    private static $_drivers = array(
        "02:DEFAULT"     => "SDEFAULT",
        "41"             => "ADuCVoltage",
        "41:ADuCVoltage" => "ADuCVoltage",
        "43"             => "ADuCVoltage",
        "43:ADuCVoltage" => "ADuCVoltage"
    );
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$sensor The sensor object
    *
    * @return null
    */
    private function __construct()
    {
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
    }
    /**
    * This function creates the system.
    *
    * @return null
    */
    protected static function &intFactory()
    {
        $class = get_called_class();
        $object = new $class();
        return $object;
    }
    /**
    * This function creates the system.
    *
    * @param string $driver The driver to load
    *
    * @return null
    */
    public static function &factory($driver)
    {
        $class = '\\HUGnet\\sensors\\drivers\\'.$driver;
        $file = dirname(__FILE__)."/drivers/".$driver.".php";
        if (file_exists($file) || class_exists($class)) {
            include_once $file;
            if (class_exists($class)) {
                return $class::factory();
            }
        }
        include_once dirname(__FILE__)."/drivers/SDEFAULT.php";
        return \HUGnet\sensors\drivers\SDEFAULT::factory();
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
        if (isset($this->params[$name])) {
            return true;
        } else if (isset($this->_default[$name])) {
            return true;
        }
        return false;
    }
    /**
    * Creates the object from a string
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    public function get($name)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        } else if (isset($this->_default[$name])) {
            return $this->_default[$name];
        }
        return null;
    }
    /**
    * Returns all of the parameters and defaults in an array
    *
    * @return null
    */
    public function toArray()
    {
        return array_merge($this->_default, (array)$this->params);
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
            $driver = array_search($mask, (array)self::$_drivers);
            if ($driver !== false) {
                return $driver;
            }
        }
        return "SDEFAULT";
    }
    /**
    * Gets the extra values
    *
    * @param int   $index The extra index to use
    * @param array $extra The extra array
    *
    * @return The extra value (or default if empty)
    */
    protected function getExtra($index, $extra)
    {
        if (!(is_array($extra) && isset($extra[$index]))) {
            $extra = $this->get("extraDefault");
        }
        return $extra[$index];
    }


    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    * @param array $extra  The extra setup that is needed.
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    abstract public function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null, $extra = array()
    );

}


?>
