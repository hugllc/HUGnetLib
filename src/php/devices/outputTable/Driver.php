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
namespace HUGnet\devices\outputTable;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../../base/LoadableDriver.php";

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
 * @since      0.10.0
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class Driver extends \HUGnet\base\LoadableDriver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
    );
     /**
    * The location of our tables.
    */
    protected $tableLoc = "outputTable";
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $tableEntry = array();
    /**
    * This is where all of the defaults are stored.
    */
    protected $default = array(
        "longName" => "Unknown Output",
        "shortName" => "Unknown",
        "extraText" => array(),
        "extraDesc" => array(),
        "extraDefault" => array(),
        "extraNames" => array(),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(),
        "min" => 0,
        "max" => 0,
        "zero" => 0,
        "requires" => array(),
        "provides" => array(),
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
        "02:DEFAULT"                 => "ADuCPWM",
        "03:DEFAULT"                 => "ADuCGPIO",
        "20:DEFAULT"                 => "MagDir",
        "30:DEFAULT"                 => "HUGnetPower",
        "31:DEFAULT"                 => "FET003912",
        "32:DEFAULT"                 => "GPIO003928",
        "40:DEFAULT"                 => "FETCtrl104603",
        "60:DEFAULT"                 => "InputSumOutput",
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
    protected $arch = array(
        "0039-12" => array(
            0x31 => "FET",
        ),
        "0039-21-01" => array(
        ),
        "0039-21-01" => array(
        ),
        "0039-28" => array(
            0x32 => "GPIO",
        ),
        "0039-37" => array(
            0x01 => "ADuC DAC",
            0x02 => "ADuC PWM",
            0x03 => "ADuC GPIO",
            0x20 => "Magnitude/Direction",
        ),
        "Linux" => array(
            0x20 => "Magnitude/Direction",
            0x60 => "Input Sum Control",
        ),
        "all" => array(
            0xFE => "Null Output",
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
    protected function __construct(&$sensor)
    {
        parent::__construct($sensor);
    }
    /**
    * This is the destructor
    *
    * @return object
    */
    public function output()
    {
        return parent::iopobject();
    }
    /**
    * Returns the converted table entry
    *
    * @return bool The table to use
    */
    protected function convertOldEntry()
    {
        $table = array();
        if (is_array($this->entryMap)) {
            // Get the really old system
            $extra = $this->output()->table()->get("extra");
            foreach ($this->entryMap as $key => $field) {
                if (!is_null($extra[$key])) {
                    $table[$field] = $extra[$key];
                }
            }
        }
        return $table;
    }
    /**
    * This function creates the system.
    *
    * @param string $driver  The driver to load
    * @param object &$output The output object
    * @param array  $table   The table to use.  This forces the table, instead of
    *                        using the database to find it
    *
    * @return null
    */
    public static function &factory($driver, &$output, $table = null)
    {
        $class = '\\HUGnet\\devices\\outputTable\\drivers\\'.$driver;
        $file = dirname(__FILE__)."/drivers/".$driver.".php";
        if (file_exists($file)) {
            include_once $file;
        }
        $interface = "\\HUGnet\\devices\\outputTable\\DriverInterface";
        if (is_subclass_of($class, $interface)) {
            return new $class($output, $table);
        }
        include_once dirname(__FILE__)."/drivers/EmptyOutput.php";
        return new \HUGnet\devices\outputTable\drivers\EmptyOutput($output);
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
    * Returns the port this data channel is attached to
    *
    * @return array
    */
    protected function port()
    {
        return $this->get("port");
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
                "label" => (string)$this->output()->get("location"),
                "index" => 0,
                "port" => $this->port(),
            ),
        );
    }

}


?>
