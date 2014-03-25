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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\processes\replicate;
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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
abstract class Periodic
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
        "PullDevices", "PullHistory", "PullImages"
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
    * This function creates the system.
    *
    * @param object &$gui The user interface to use
    *
    * @return null
    */
    protected static function &intFactory(&$gui)
    {
        $class = get_called_class();
        $object = new $class($gui);
        return $object;
    }
    /**
    * This function creates the system.
    *
    * @return null
    */
    abstract public function &execute();
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
                "processes/replicate/periodic",
                true,
                "\\HUGnet\\processes\\replicate\\periodic"
            );
            if (class_exists($class)) {
                $plugins[$class] = $class::factory($gui);
            }

        }
        return (array)$plugins;
    }
    /**
    * This says if we are ready to run
    *
    * @return bool
    */
    protected function ready()
    {
        return ($this->period < (time() - $this->last)) && ($this->period != 0);
    }
    /**
    * This says if we are ready to run
    *
    * @return bool
    * @SuppressWarnings(PHPMD.ShortMethodName)
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
    public function &device()
    {
        return $this->system()->device(
            $this->system()->network()->device()->getID()
        );
    }
    /**
    * This says if we are ready to run
    *
    * @return bool
    */
    protected function success()
    {
        $this->last = $this->system()->now();
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
    /**
    * This says if we are ready to run
    *
    * @return bool
    */
    protected function hasMaster()
    {
        $master = $this->system()->get("master");
        return is_array($master) && (count($master) > 0);
    }
}


?>
