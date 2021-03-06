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
        "Temperature" => "Temperature Sensor",
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
        "0039-21-02" => array(
        ),
        "0039-28" => array(
        ),
        "0039-37" => array(
            "Temperature" => "Temperature Sensor",
        ),
        "Linux" => array(
        ),
        "all" => array(
            "NoOp" => "Do Nothing",
        ),
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
        include_once dirname(__FILE__)."/drivers/NoOp.php";
        return new \HUGnet\devices\functions\drivers\NoOp($function);
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
    /**
    * Applies this function
    *
    * @return null
    */
    public function execute()
    {
        return false;
    }
    /**
    * Gets one port for each element of the array $specs
    * 
    * @param array $specs The specifications of the required ports
    *
    * @return null
    */
    protected function getPorts($specs)
    {
        $ports      = array();
        $used       = (array)$this->fct()->device()->uses();
        $properties = &$this->fct()->device()->properties();
        $has        = (array)$properties->getPinList();
        $unused     = array_diff($has, $used);
        foreach ((array)$specs as $key => $spec) {
            $count   = null;
            $useport = null;
            // This picks the pin that has the fewest properties.
            foreach ($unused as $k => $port) {
                $cnt = $this->portCheck($port, $spec);
                if (($cnt >= 0) && (is_null($useport) || ($cnt < $count))) {
                    $count   = $cnt;
                    $useport = $port;
                    $index   = $k;
                }
            }
            if (!is_null($useport)) {
                $ports[$key] = $useport;
                unset($unused[$index]);
            }
        }
        return $ports;
    }
    /**
    * Checks to see if port meets specs
    * 
    * @param string $port The port to check
    * @param array  $spec The specifications of the required port
    *
    * @return null
    */
    protected function portCheck($port, $spec)
    {
        $prop = $this->fct()->device()->properties()->getPinProperties($port);
        if (is_array($prop) && is_string($prop["properties"])) {
            $props = explode(",", $prop["properties"]);
            $diff = array_diff((array)$spec, (array)$props);
            if (count($diff) > 0) {
                $ret = -1 * count($diff);
            } else {
                $ret = count($props) - count($spec);
            }
        } else {
            $ret = -1 * count($spec);
        }
        return $ret;
    }
    /**
    * Checks to see if port is in use
    * 
    * @param string $port The port to check
    *
    * @return null
    */
    protected function portAvailable($port)
    {
        $has = (array)$this->fct()->device()->properties()->getPinList();
        if (!in_array($port, $has)) {
            return false;
        }
        return !in_array($port, (array)$this->fct()->device()->uses());
    }
}


?>
