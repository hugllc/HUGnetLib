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
namespace HUGnet\processes\analysis;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
require_once dirname(__FILE__)."../../../db/Average.php";

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
abstract class Device
{
    /**
    * the period in seconds
    */
    protected $period = 0;
    /**
    * the last time we ran
    */
    protected $last = 0;
    /**
    * the last time we ran
    */
    protected $enable = true;
    /**
    * the system object
    */
    private $_ui = null;
    /**
    * This is where all of the driver information is stored.
    *
    * Drivers must be registered here, otherwise they will never get loaded.  The
    * index in the root array is the driver name.  It should be exactly the same
    * as the driver class name.
    */
    private static $_drivers = array(
        "Average"
        //, "AverageHourly", "AverageDaily", "AverageWeekly",
        //"AverageMonthly", "AverageYearly",
    );
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$gui The user interface to use
    *
    * @return null
    */
    protected function __construct(&$gui)
    {
        $this->_ui = &$gui;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
    }
    /**
    /**
    * This function creates the system.
    *
    * @param object &$device The device to use
    *
    * @return null
    */
    abstract public function &execute(&$device);
    /**
    * This function creates the system.
    *
    * @param object &$gui the user interface object
    *
    * @return null
    */
    public static function &plugins(&$gui)
    {
        $plugins = array();
        foreach (self::$_drivers as $driver) {
            $class = \HUGnet\Util::findClass(
                $driver,
                "processes/analysis/device",
                true,
                "\\HUGnet\\processes\\analysis\\device"
            );
            if (class_exists($class)) {
                $plugins[$class] = new $class($gui);
            }
        }
        return (array)$plugins;
    }
    /**
    * This function does the stuff in the class.
    *
    * @param object &$device The device to check
    *
    * @return bool True if ready to return, false otherwise
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function ready(&$device)
    {
        return true;
    }
    /**
    * This says if we are ready to run
    *
    * @return bool
    */
    protected function &ui()
    {
        return $this->_ui;
    }
    /**
    * This says if we are ready to run
    *
    * @return bool
    */
    public function &system()
    {
        return $this->_ui->system();
    }
    /**
    * This says if we are ready to run
    *
    * @return bool
    */
    protected function success()
    {
        $this->last = time();
        $this->system()->out(
            "Success.  Next run ".date("Y-m-d H:i:s", $this->last + $this->period)
        );
    }
    /**
    * This says if we are ready to run
    *
    * @return bool
    */
    protected function failure()
    {
        $this->last = (time() - $this->period + 60);
        $this->system()->out(
            "Failure. Will try again in 1 minute"
        );
    }
}


?>
