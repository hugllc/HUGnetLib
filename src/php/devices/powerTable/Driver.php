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
 * @subpackage PowerPorts
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2015 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\powerTable;
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
 * @subpackage PowerPorts
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2015 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.14.5
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
    protected $tableLoc = "powerTable";
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $tableEntry = array();
    /**
    * This is where all of the defaults are stored.
    */
    protected $default = array(
        "longName" => "Unknown Power",
        "shortName" => "Unknown",
        "extraText" => array(
            0 => "Type",
            1 => "Priority",
        ),
        "extraDesc" => array(
            0 => "The type of this power port",
            1 => "The priority of this power port"
        ),
        "extraDefault" => array(
            0 => 0,
            1 => 0,
        ),
        "extraNames" => array(
            "type" => 0,
            "priority" => 1,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            array(0 => "None"), array(0 => "Highest")
        ),
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
        "A0:DEFAULT"                 => "Load",
        "E0:DEFAULT"                 => "PowerSupply",
        "E1:DEFAULT"                 => "SolarPanel",
        "FE:DEFAULT"                 => "PowerConverter",
        "FF:DEFAULT"                 => "EmptyPower",
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
        ),
        "0039-21-01" => array(
        ),
        "0039-21-01" => array(
        ),
        "0039-28" => array(
        ),
        "0039-37" => array(
        ),
        "1046-02" => array(
            0xA0 => "Load",
            0xE0 => "Power Supply",
            0xE1 => "Solar Panel",
        ),
        "1046-03" => array(
            0xA0 => "Load",
            0xE0 => "Power Supply",
        ),
        "Linux" => array(
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
    protected function __construct(&$sensor)
    {
        parent::__construct($sensor);
    }
    /**
    * This is the destructor
    *
    * @return object
    */
    public function power()
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
            $extra = $this->power()->table()->get("extra");
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
    * @param object &$power The power object
    * @param array  $table   The table to use.  This forces the table, instead of
    *                        using the database to find it
    *
    * @return null
    */
    public static function &factory($driver, &$power, $table = null)
    {
        $class = '\\HUGnet\\devices\\powerTable\\drivers\\'.$driver;
        $file = dirname(__FILE__)."/drivers/".$driver.".php";
        if (file_exists($file)) {
            include_once $file;
        }
        $interface = "\\HUGnet\\devices\\powerTable\\DriverInterface";
        if (is_subclass_of($class, $interface)) {
            return new $class($power, $table);
        }
        include_once dirname(__FILE__)."/drivers/EmptyPower.php";
        return new \HUGnet\devices\powerTable\drivers\EmptyPower($power);
    }
    /**
    * Returns the driver that should be used for a particular device
    *
    * @param mixed  $sid  The ID of the power
    * @param string $type The type of the power
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
        return "EmptyPower";
    }
    /**
    * Returns an array of types that this power could be
    *
    * @param int $sid The ID to check
    *
    * @return The extra value (or default if empty)
    */
    public static function getTypes($sid)
    {
        $array = array();
        $power = sprintf("%02X", (int)$sid);
        foreach ((array)self::$_drivers as $key => $driver) {
            $k = explode(":", $key);
            if (trim(strtoupper($k[0])) == $power) {
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
        $driver = '\\HUGnet\\devices\\powerTable\\drivers\\'.$class;
        if (class_exists($driver) && !isset(self::$_drivers[$key])) {
            self::$_drivers[$key] = $class;
        }
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
        $extra = $this->power()->get("extra");
        $extra[0] = hexdec(substr($string, 0, 2));
        $extra[1] = hexdec(substr($string, 2, 2));
        $loc = stristr((string)pack("H*", substr($string, 4)), "\0", true); // end at \0
        $loc = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $loc);  // Remove non printing chars
        $this->power()->set("location", (string)$loc);
        $this->power()->set("extra", $extra);
    }

    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function encode()
    {
        // Type
        $string  = sprintf("%02X", ($this->getExtra(0) & 0xFF));
        // Priority
        $string .= sprintf("%02X", ($this->getExtra(1) & 0xFF));
        // Name
        $loc = $this->power()->get("location");
        if (strlen($loc) > 0) {
            $string .= strtoupper((string)array_shift(unpack('H*', substr($loc, 0, 10))));
        }
        $string .= "00";
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
                "label" => (string)$this->power()->get("location"),
                "index" => 0,
                "port" => $this->port(),
            ),
        );
    }

}


?>
