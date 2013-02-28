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
namespace HUGnet\devices\inputTable;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our units class */
require_once dirname(__FILE__)."/Driver.php";
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
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class DriverVirtual extends Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
    );
    /**
    * This is where all of the driver information is stored.
    *
    * Drivers must be registered here, otherwise they will never get loaded.  The
    * index in the root array is the driver name.  It should be exactly the same
    * as the driver class name.
    */
    protected static $drivers = array(
        "FE:DEFAULT"                 => "EmptyVirtual",
        "FE:AlarmVirtual"            => "AlarmVirtual",
        "FE:BinaryVirtual"           => "BinaryVirtual",
        "FE:CalorimeterPowerVirtual" => "CalorimeterPowerVirtual",
        "FE:CelaniPowerCalVirtual"   => "CelaniPowerCalVirtual",
        "FE:CloneVirtual"            => "CloneVirtual",
        "FE:ComputationVirtual"      => "ComputationVirtual",
        "FE:DewPointVirtual"         => "DewPointVirtual",
        "FE:LinearTransformVirtual"  => "LinearTransformVirtual",
        "FE:WindChillVirtual"        => "WindChillVirtual",
    );
    /**
    * This function creates an object if it finds the right class
    *
    * @param object &$obj    The object container to put an object in.
    * @param string $driver  The driver to load
    * @param object &$sensor The sensor object
    * @param array  $table   The table to use.  This forces the table, instead of
    *                        using the database to find it
    *
    * @return null
    */
    protected static function driverFactory(&$obj, $driver, &$sensor, $table = null)
    {
        if (is_object($obj)) {
            return false;
        }
        $class = '\\HUGnet\\devices\\inputTable\\drivers\\virtual\\'.$driver;
        $file = dirname(__FILE__)."/drivers/virtual/".$driver.".php";
        if (file_exists($file)) {
            include_once $file;
        }
        $interface = "\\HUGnet\\devices\\inputTable\\DriverInterface";
        if (is_subclass_of($class, $interface)) {
            $obj = new $class($sensor, $table);
            return true;
        }
        return false;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param float  $deltaT  The time delta in seconds between this record
    * @param array  &$prev   The previous reading
    * @param array  &$data   The data from the other sensors that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function decodeData(
        &$string, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $ret = $this->channels();
        $A = null;
        $ret[0]["value"] = $this->decodeDataPoint($A, 0, $deltaT, $prev, $data);
        return $ret;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param array $value   The data to use
    * @param int   $channel The channel to get
    * @param float $deltaT  The time delta in seconds between this record
    * @param array &$prev   The previous reading
    * @param array &$data   The data from the other sensors that were crunched
    *
    * @return string The reading as it would have come out of the endpoint
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function encodeDataPoint(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        return "";
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channels()
    {
        $ret = parent::channels();
        foreach (array_keys((array)$ret) as $key) {
            $ret[$key]["epChannel"] = false;
        }
        return $ret;
    }

}


?>
