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
namespace HUGnet\devices\functions;
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
 * @since      0.14.3
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class Driver extends \HUGnet\base\LoadableDriver
{
    /**
    * This is where we store the process.
    */
    private $_fctobject = null;
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
    );
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $tableEntry = array();
    /**
    * This is where all of the defaults are stored.
    */
    protected $default = array(
        'longName' => 'Unknown Function',
        'shortName' => 'Unknown',
        "extraText" => array(
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
        ),
        "extraDefault" => array(
        ),
        "extraDesc" => array(
        ),
        "extraNames" => array(
        ),
    );
    /**
    * This is where all of the driver information is stored.
    *
    * Drivers must be registered here, otherwise they will never get loaded.  The
    * index in the root array is the driver name.  It should be exactly the same
    * as the driver class name.
    */
    private static $_drivers = array(
    );
    /**
    * This is the destructor
    *
    * @return object
    */
    public function fct()
    {
        return parent::iopobject();
    }
    /**
    * This function creates the system.
    *
    * @param string $driver    The driver to load
    * @param object &$function The function object
    * @param array  $table     The table to use.  This forces the table, instead of
    *                          using the database to find it
    *
    * @return null
    */
    public static function &factory($driver, &$function, $table = null)
    {
        $class = '\\HUGnet\\devices\\functions\\drivers\\'.$driver;
        $file = dirname(__FILE__)."/drivers/".$driver.".php";
        if (file_exists($file)) {
            include_once $file;
        }
        $interface = "\\HUGnet\\devices\\functions\\DriverInterface";
        if (is_subclass_of($class, $interface)) {
            return new $class($function, $table);
        }
        include_once dirname(__FILE__)."/drivers/InputFunction.php";
        return new \HUGnet\devices\functions\drivers\InputFunction($function);
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
        $driver = '\\HUGnet\\devices\\functionTable\\drivers\\'.$class;
        if (class_exists($driver) && !isset(self::$_drivers[$key])) {
            self::$_drivers[$key] = $class;
        }
    }


}


?>
