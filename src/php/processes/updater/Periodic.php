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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\processes\updater;
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
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
    * the count of failures
    */
    private $_failcnt = 0;
    /**
    * This is where all of the driver information is stored.
    *
    * Drivers must be registered here, otherwise they will never get loaded.  The
    * index in the root array is the driver name.  It should be exactly the same
    * as the driver class name.
    */
    private static $_drivers = array(
        "Checkin", "PushDevices", "GetFirmware", "SyncTables", "SyncDevices",
        "PushAnnotations"
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
                "processes/updater/periodic",
                true,
                "\\HUGnet\\processes\\updater\\periodic"
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
    * @param string $msg The message to use
    * 
    * @return bool
    */
    protected function success($msg = null)
    {
        $this->last = $this->system()->now();
        if (is_null($msg)) {
            $msg = "  Next run ".date("Y-m-d H:i:s", $this->last + $this->period);
        }
        $this->system()->out(
            "Success.".$msg
        );
        // Reset the failcnt
        if ($this->_failcnt != 0) {
            $this->_failcnt = 0;
            $this->system()->out("Failure count reset.");
        }
    }
    /**
    * This says if we are ready to run
    *
    * @param string $msg      The message to use
    * @param int    $timeout  The timeout
    * @param int    $maxcount The max count
    *
    * @return bool
    */
    protected function failure(
        $msg = "  Will try again in 1 minute", 
        $timeout = 60,
        $maxcount = 20
    ) {
        $this->last = (time() - $this->period + $timeout);
        $this->_failcnt++;
        $this->system()->out("Failure Cnt: ".$this->_failcnt, 2);
        if ($this->_failcnt > $maxcount) {
            $this->system()->out(
                "Too many failures.  Exiting"
            );
            $this->system()->quit(true);
        } else {
            $this->system()->out(
                "Failure.".$msg
            );
        }
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
    /**
    * This says if we are ready to run
    *
    * @return bool
    */
    protected function hasPartner()
    {
        $master = $this->system()->get("partner");
        return is_array($master) && (count($master) > 0);
    }
}


?>
