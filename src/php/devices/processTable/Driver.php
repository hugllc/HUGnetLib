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
namespace HUGnet\devices\processTable;
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
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
    * This is where all of the defaults are stored.
    */
    protected $default = array(
        "longName" => "Unknown Output",
        "shortName" => "Unknown",
        "extraText" => array(),
        "extraDefault" => array(),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(),
    );
    /**
    * This is where all of the driver information is stored.
    *
    * Drivers must be registered here, otherwise they will never get loaded.  The
    * index in the root array is the driver name.  It should be exactly the same
    * as the driver class name.
    */
    private static $_drivers = array(
        "01:DEFAULT"                 => "LevelHolderProcess",
        "02:DEFAULT"                 => "PIDProcess",
        "FF:DEFAULT"                 => "EmptyProcess",
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
            0x01 => "LevelHolderProcess",
            0x02 => "PIDProcess",
        ),
        "Linux" => array(
            0x01 => "LevelHolderProcess",
            0x02 => "PIDProcess",
        ),
        "all" => array(
            0xFF => "Empty Slot"
        ),
    );
    /**
    * This is the destructor
    *
    * @return object
    */
    public function process()
    {
        return parent::iopobject();
    }
    /**
    * This function creates the system.
    *
    * @param string $driver   The driver to load
    * @param object &$process The process object
    * @param array  $table    The table to use.  This forces the table, instead of
    *                         using the database to find it
    *
    * @return null
    */
    public static function &factory($driver, &$process, $table = null)
    {
        $class = '\\HUGnet\\devices\\processTable\\drivers\\'.$driver;
        $file = dirname(__FILE__)."/drivers/".$driver.".php";
        if (file_exists($file)) {
            include_once $file;
        }
        $interface = "\\HUGnet\\devices\\processTable\\DriverInterface";
        if (is_subclass_of($class, $interface)) {
            return new $class($process, $table);
        }
        include_once dirname(__FILE__)."/drivers/EmptyProcess.php";
        return new \HUGnet\devices\processTable\drivers\EmptyProcess($process);
    }
    /**
    * Returns the driver that should be used for a particular device
    *
    * @param mixed  $sid  The ID of the process
    * @param string $type The type of the process
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
        return "EmptyProcess";
    }
    /**
    * Returns an array of types that this process could be
    *
    * @param int $sid The ID to check
    *
    * @return The extra value (or default if empty)
    */
    public static function getTypes($sid)
    {
        $array = array();
        $process = sprintf("%02X", (int)$sid);
        foreach ((array)self::$_drivers as $key => $driver) {
            $k = explode(":", $key);
            if (trim(strtoupper($k[0])) == $process) {
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
        $driver = '\\HUGnet\\devices\\processTable\\drivers\\'.$class;
        if (class_exists($driver) && !isset(self::$_drivers[$key])) {
            self::$_drivers[$key] = $class;
        }
    }

}


?>
