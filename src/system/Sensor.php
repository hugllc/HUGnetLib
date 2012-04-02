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
        $ret = $this->driver()->get($field);
        if (is_null($ret)) {
            $ret = parent::get($field);
        }
        return $ret;
    }
    /**
    * Returns the table as a json string
    *
    * @return json string
    */
    public function json()
    {
        $return = array_merge(
            $this->driver()->toArray(),
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
        return json_encode($return);
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
            $units = $this->table()->get("units");
            if (empty($units)) {
                $this->table()->set("units", $this->driver()->get("storageUnit"));
            }
            $ret = true;
        }
        return (bool)$ret;
    }
    /**
    * This creates the driver
    *
    * @param string $driver The driver to use.  Leave blank for automatic.
    *
    * @return null
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
        if ($this->storageType == \UnitsBase::TYPE_DIFF) {
            $ret["value"] = $this->driver()->getReading(
                ($A - $prev), $deltaT, $data, $prev
            );
            $ret["raw"] = $A;
        } else {
            $ret["value"] = $this->driver()->getReading($A, $deltaT, $data, $prev);
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
        $this->table()->set("units", $this->driver()->get("storageUnit"));
        $extra = (array)$this->table()->get("extra");
        $extraDefault = (array)$this->driver()->get("extraDefault");
        if (count($extra) != count($extraDefault)) {
            $this->table()->set("extra", array());
        }
    }

}


?>
