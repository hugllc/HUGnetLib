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
namespace HUGnet\devices;
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
    );
    /**
    * This is where all of the defaults are stored.
    */
    private $_default = array(
        "packetTimeout" => 5,
        "totalSensors" => 13,
        "physicalSensors" => 9,
        "virtualSensors" => 4,
        "historyTable" => "EDEFAULTHistoryTable",
        "averageTable" => "EDEFAULTAverageTable",
        "loadable" => false,
        "bootloader" => false,
        "ConfigInterval" => 43200,
        "type" => "unknown",
        "actionClass" => "Action",
    );
    /**
    * This is where all of the driver information is stored.
    *
    * Drivers must be registered here, otherwise they will never get loaded.  The
    * index in the root array is the driver name.  It should be exactly the same
    * as the driver class name.
    */
    private static $_drivers = array(
        "0039-20-06-C:0039-21-01-A:DEFAULT" => "E00393802",
        "0039-20-15-C:0039-21-02-A:DEFAULT" => "E00393802",
        "0039-20-16-C:0039-21-02-A:DEFAULT" => "E00393802",
        "0039-38-02-C:DEFAULT:DEFAULT"      => "E00393802",
        "DEFAULT:0039-12-00-A:DEFAULT"      => "E00391200",
        "DEFAULT:0039-12-01-A:DEFAULT"      => "E00391200",
        "DEFAULT:0039-12-02-A:DEFAULT"      => "E00391200",
        "DEFAULT:0039-12-01-B:DEFAULT"      => "E00391200",
        "DEFAULT:0039-12-02-B:DEFAULT"      => "E00391200",
        "DEFAULT:0039-12-02-C:DEFAULT"      => "E00391200",
        "0039-11-06-A:0039-12-01-B:DEFAULT" => "E00391201",
        "0039-11-06-A:0039-12-02-B:DEFAULT" => "E00391201",
        "0039-11-07-A:0039-12-01-B:DEFAULT" => "E00391201",
        "0039-11-07-A:0039-12-02-B:DEFAULT" => "E00391201",
        "0039-11-08-A:0039-12-01-B:DEFAULT" => "E00391201",
        "0039-11-08-A:0039-12-02-B:DEFAULT" => "E00391201",
        "0039-20-04-C:0039-12-02-B:DEFAULT" => "E00391201",
        "0039-20-05-C:0039-12-02-B:DEFAULT" => "E00391201",
        "0039-20-01-C:DEFAULT:DEFAULT"      => "E00392100",
        "0039-20-14-C:DEFAULT:DEFAULT"      => "E00392100",
        "0039-38-01-C:0039-21-01-A:DEFAULT" => "E00392100",
        "0039-38-01-C:0039-21-02-A:DEFAULT" => "E00392100",
        "DEFAULT:0039-26-00-P:DEFAULT"      => "E00392600",
        "DEFAULT:0039-26-01-P:DEFAULT"      => "E00392600",
        "DEFAULT:0039-26-02-P:DEFAULT"      => "E00392600",
        "DEFAULT:0039-26-03-P:DEFAULT"      => "E00392600",
        "DEFAULT:0039-26-04-P:DEFAULT"      => "E00392600",
        "DEFAULT:0039-26-05-P:DEFAULT"      => "E00392600",
        "DEFAULT:0039-26-07-P:DEFAULT"      => "E00392600",
        "DEFAULT:0039-26-06-P:DEFAULT"      => "E00392606",
        "DEFAULT:0039-28-01-A:DEFAULT"      => "E00392800",
        "DEFAULT:0039-28-01-B:DEFAULT"      => "E00392800",
        "DEFAULT:0039-28-01-C:DEFAULT"      => "E00392800",
        "0039-20-18-C:0039-28-01-A:DEFAULT" => "E00392801",
        "0039-20-18-C:DEFAULT:DEFAULT"      => "E00392801",
        "0039-38-01-C:0039-37-01-A:DEFAULT" => "E00393700",
        "DEFAULT:0039-37-01-A:DEFAULT"      => "E00393700",
        "DEFAULT:VIRTUAL:DEFAULT"           => "EVIRTUAL",
        "DEFAULT:0039-24-02-P:DEFAULT"      => "EVIRTUAL",
    );
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @return null
    */
    private function __construct()
    {
        /* This class shouldn't be instanciated. */
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
        $class = \HUGnet\Util::findClass(
            $driver, "devices/drivers", true, "\\HUGnet\\devices\\drivers"
        );
        if (class_exists($class)) {
            return $class::factory();
        }
        include_once dirname(__FILE__)."/drivers/EDEFAULT.php";
        return \HUGnet\devices\drivers\EDEFAULT::factory();
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
    * Gets the ID of the sensor from the raw setup string
    *
    * @param int    $sensor   The sensor number
    * @param string $RawSetup The raw setup string
    *
    * @return int The sensor id
    */
    public static function getSensorID($sensor, $RawSetup)
    {
        $sid = substr($RawSetup, 46 + ($sensor * 2), 2);
        return hexdec($sid);
    }
    /**
    * Returns the driver that should be used for a particular device
    *
    * @param string $HWPartNum The hardware part number
    * @param string $FWPartNum The firmware part number
    * @param string $FWVersion The firmware version
    *
    * @return string The driver to use
    */
    public static function getDriver($HWPartNum, $FWPartNum, $FWVersion)
    {
        $try = array(
            $FWPartNum.":".$HWPartNum.":".$FWVersion,
            $FWPartNum.":".$HWPartNum.":DEFAULT",
            $FWPartNum.":DEFAULT:DEFAULT",
            "DEFAULT:".$HWPartNum.":DEFAULT",
            $FWPartNum.":DEFAULT:".$FWVersion,
        );
        foreach ($try as $key) {
            if (isset(self::$_drivers[$key])) {
                return self::$_drivers[$key];
            }
        }
        return "EDEFAULT";
    }
    /**
    * Decodes the sensor string
    *
    * @param string $string The string of sensor data
    *
    * @return null
    */
    public function decodeSensorString($string)
    {
        $ret = array(
            "DataIndex" => hexdec(substr($string, 0, 2)),
            "timeConstant" => hexdec(substr($string, 4, 2)),
            "String" => substr($string, 6),
        );
        return $ret;
    }
    /**
    * Returns the name of the history table
    *
    * @param bool $history History if true, average if false
    *
    * @return string
    */
    public function historyTable($history = true)
    {
        if ($history) {
            $class = \HUGnet\Util::findClass(
                $this->get("historyTable"), "plugins/historyTable", true
            );
            if (is_null($class)) {
                $class = \HUGnet\Util::findClass(
                    "EDEFAULTHistoryTable", "plugins/historyTable", true
                );
            }
        } else {
            $class = \HUGnet\Util::findClass(
                $this->get("averageTable"), "plugins/averageTable", true
            );
            if (is_null($class)) {
                $class = \HUGnet\Util::findClass(
                    "EDEFAULTAverageTable", "plugins/averageTable", true
                );
            }
        }
        return $class;
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string  The string to decode
    * @param object &$device The device object
    *
    * @return array
    */
    public function decode($string, &$device)
    {
        $device->set("TimeConstant", hexdec(substr($string, 0, 2)));
        $sensors = $this->get("physicalSensors");
        for ($i = 0; $i < $sensors; $i++) {
            $sid = substr($string, 2 + (2 * $i), 2);
            // Only do this if we have enough string
            if (strlen($sid) === 2) {
                $device->sensor($i)->change(
                    array(
                        "id" => hexdec($sid),
                    )
                );
            }
        }
    }
    /**
    * Encodes this driver as a setup string
    *
    * @param object &$device   The device object
    * @param bool   $showFixed Show the fixed portion of the data
    *
    * @return array
    */
    public function encode(&$device, $showFixed = true)
    {
        $string  = "";
        $string .= sprintf("%02X", ($device->get("TimeConstant") & 0xFF));
        if ($showFixed) {
            $sensors = $this->get("physicalSensors");
            for ($i = 0; $i < $sensors; $i++) {
                $string .= sprintf(
                    "%02X", ($device->sensor($i)->get("id") & 0xFF)
                );
            }
        }
        return $string;
    }
    /**
    * Checks a record to see if it needs fixing
    *
    * @param object &$device The device object
    *
    * @return array
    */
    public function checkRecord(&$device)
    {
        /* By default do nothing */
    }
}


?>
