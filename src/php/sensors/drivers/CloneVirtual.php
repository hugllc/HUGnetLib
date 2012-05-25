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
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\sensors\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../DriverVirtual.php";

/**
 * Driver for reading voltage based pressure sensors
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class CloneVirtual extends \HUGnet\sensors\DriverVirtual
{
    /**
     * This is the sensor we are cloning
     */
    private $_clone = null;
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Clone Virtual Sensor",
        "shortName" => "CloneVirtual",
        "virtual" => true,              // This says if we are a virtual sensor
        "extraText" => array(
            "Device ID", "Sensor"
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(8, 3),
        "extraDefault" => array("", ""),
    );
    /**
    * This function creates the system.
    *
    * @param object &$sensor The sensor object
    *
    * @return null
    */
    public static function &factory(&$sensor)
    {
        return parent::intFactory($sensor);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        return $this->_clone()->getReading($A, $deltaT, $data, $prev);

    }
    /**
     * This is the routine that gets the sensor that we are cloning
     *
     * @return object The object for the sensor we are cloning
     */
    private function _clone()
    {
        if (!is_object($this->_clone)) {
            $did = hexdec($this->getExtra(0));
            $sen = $this->getExtra(1);
            if ($did == 0) {
                $this->_clone = &parent::factory("SDEFAULT", $this->sensor());
            } else {
                $sensor = &$this->sensor()->system()->device($did)->sensor($sen);
                $this->_clone = &parent::factory(
                    $sensor->get("driver"),
                    $sensor
                );
            }
        }
        return $this->_clone;
    }
    /**
    * Returns all of the parameters and defaults in an array
    *
    * @return array of data from the sensor
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function toArray()
    {
        return array_merge($this->_clone()->toArray(), $this->params);
    }
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    public function get($name)
    {
        $value = parent::get($name);
        if (isset($this->params[$name]) || is_null($value)) {
            return $value;
        } else {
            return $this->_clone()->get($name);
        }
    }

}


?>