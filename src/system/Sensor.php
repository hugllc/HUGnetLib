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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/SystemTableBase.php";
require_once dirname(__FILE__)."/../units/Driver.php";

/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Sensor extends SystemTableBase
{
    /**
    * This is the cache for the drivers.
    */
    private $_driverCache = array();
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        foreach (array_keys((array)$this->_driverCache) as $key) {
            unset($this->_driverCache[$key]);
        }
        parent::__destruct();
    }
    /**
    * This function creates the system.
    *
    * @param mixed  $system (object)The system object to use
    * @param mixed  $data   (int)The id of the item, (array) data info array
    * @param string $table  The table to use
    *
    * @return null
    */
    public static function &factory($system, $data=null, $table="SensorsTable")
    {
        $object = &parent::factory($system, $data, $table);
        return $object;
    }
    /**
    * Gets a value
    *
    * @param string $field the field to get
    *
    * @return null
    */
    public function get($field)
    {
        return $this->_get($field, $this->driver());
    }
    /**
    * Gets a value
    *
    * @param string $field  the field to get
    * @param object $driver The driver to use
    *
    * @return null
    */
    private function _get($field, $driver)
    {
        $ret = $driver->get($field);
        if (is_null($ret)) {
            $ret = parent::get($field);
        } else if (is_string($ret)) {
            $this->_getExtra($ret);
        }
        return $ret;
    }
    /**
    * Sets the value of a getExtra parameter if it finds one.
    *
    * @param string $value Set the value to check
    *
    * @return null
    */
    private function _getExtra(&$value)
    {
        if (is_string($value) && (strtolower(substr($value, 0, 8)) === "getextra")) {
            $value = $this->driver()->getExtra(
                (int)substr($value, 8),
                $this->table()->get("extra")
            );
        }
    }
    /**
    * Returns the table as an array
    *
    * @return array
    */
    public function toArray()
    {
        $driver = $this->driver()->toArray();
        foreach (array_keys($driver) as $key) {
            $this->_getExtra($driver[$key]);
        }
        $return = array_merge(
            $driver,
            $this->table()->toArray(true)
        );
        $params = json_decode($return["params"], true);
        if (empty($return["type"])) {
            $return["type"] = implode(
                "", array_slice(explode('\\', get_class($this->driver())), -1)
            );
        }
        $return["params"] = (array)$params;
        $return["otherTypes"] = \HUGnet\sensors\Driver::getTypes($return["id"]);
        $return["validUnits"] = $this->units()->getValid();
        return (array)$return;
    }
    /**
    * Returns the table as a json string
    *
    * @return json string
    */
    public function json()
    {
        return json_encode($this->toArray(true));
    }
    /**
    * Loads the data into the table class
    *
    * @param mixed $data (int)The id of the record,
    *                    (array) or (string) data info array
    *
    * @return null
    */
    public function load($data)
    {
        $ret = false;
        $this->table()->clearData();
        if (is_array($data) && (count($data) == 2)
            && isset($data["dev"]) && isset($data["sensor"])
        ) {
            $ret = $this->table()->selectOneInto(
                "dev = ? AND sensor = ?",
                array($data["dev"], $data["sensor"])
            );
        } else if (is_array($data) || is_string($data)) {
            $this->table()->fromAny($data);
            $this->fixTable();
            $ret = true;
        }
        return (bool)$ret;
    }
    /**
    * This creates the driver
    *
    * @param string $driver The driver to use.  Leave blank for automatic.
    *
    * @return object
    */
    protected function &driver($driver = null)
    {
        include_once dirname(__FILE__)."/../sensors/Driver.php";
        if (empty($driver)) {
            $driver = sensors\Driver::getDriver(
                $this->table()->get("id"),
                $this->table()->get("type")
            );
        }
        if (!is_object($this->_driverCache[$driver])) {
            $this->_driverCache[$driver] = &sensors\Driver::factory($driver);
        }
        return $this->_driverCache[$driver];
    }
    /**
    * This creates the units driver
    *
    * @return object
    */
    protected function &units()
    {
        include_once dirname(__FILE__)."/../units/Driver.php";
        $units = \HUGnet\units\Driver::factory(
            $this->get("unitType"),
            $this->get("storageUnit")
        );
        return $units;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$prev  The previous reading
    * @param array &$data  The data from the other sensors that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function decodeData($A, $deltaT = 0, &$prev = null, &$data = array())
    {
        $ret = array();
        if ($this->get("storageType") == \HUGnet\units\Driver::TYPE_DIFF) {
            $ret["value"] = $this->driver()->getReading(
                ($A - $prev), $deltaT, $data, $prev, $this->table()->toArray()
            );
            $ret["raw"] = $A;
        } else {
            $ret["value"] = $this->driver()->getReading(
                $A, $deltaT, $data, $prev, $this->table()->toArray()
            );
        }
        $ret["units"] = $this->get("storageUnit");
        $ret["unitType"] = $this->get("unitType");
        $ret["dataType"] = $this->get("storageType");
        return $ret;
    }
    /**
    * This function should be overloaded to make changes to the table based on
    * changes to incoming data.
    *
    * This is a way to make sure that the data is consistant before it gets stored
    * in the database
    *
    * @return null
    */
    protected function fixTable()
    {
        $table =& $this->table();
        $driver =& $this->driver();
        if (!$this->units()->valid($table->get("units"))) {
            $table->set("units", $this->_get("storageUnit", $driver));
        }
        $extra = (array)$table->get("extra");
        $extraDefault = (array)$this->_get("extraDefault", $driver);
        if (count($extra) != count($extraDefault)) {
            $table->set("extra", array());
        }
        $min = $table->get("min");
        $max = $table->get("max");
        if (!is_numeric($min) || ($min == $max)) {
            $table->set("min", $this->_get("defMin", $driver));
        }
        if (!is_numeric($max) || ($min == $max)) {
            $table->set("max", $this->_get("defMax", $driver));
        }
    }
    /**
    * Converts data between units
    *
    * @param mixed  &$data The data to convert
    * @param string $units The units to convert to
    *
    * @return true on success, false on failure
    */
    public function convertUnits(&$data, $units = null)
    {
        if (is_array($data) && !is_null($data["value"])) {
            if (is_null($units)) {
                $units = $this->table()->get("units");
            }
            $ret = $this->units()->convert(
                $data["value"],
                $units,
                $data["units"],
                $data["unitType"]
            );
            if ($ret === true) {
                $data["units"] = $units;
            }
            if (is_numeric($data["value"])) {
                $data["value"] = round($data["value"], (int)$this->get("decimals"));
            }
        } else {
            $ret = true;
        }
        return $ret;
    }

}


?>
