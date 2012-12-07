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
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
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
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Sensor extends \HUGnet\base\SystemTableBase
{
    /**
    * This is the cache for the drivers.
    */
    private $_driverCache = array();
    /**
    * This is the device we rode in on
    */
    private $_device;

    /**
    * This is the destructor
    */
    public function __destruct()
    {
        foreach (array_keys((array)$this->_driverCache) as $key) {
            unset($this->_driverCache[$key]);
        }
        unset($this->_device);
        parent::__destruct();
    }
    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $table   The table to use
    * @param object &$device The device object to use
    *
    * @return null
    */
    public static function &factory(
        &$system, $data=null, $table=null, &$device = null
    ) {
        System::exception(
            "\HUGnet\Sensor needs to be passed a device object",
            "InvalidArgument",
            !is_object($device)
        );
        if (empty($table)) {
            $table = "Sensors";
        }
        $object = parent::factory($system, $data, $table);
        $object->_device = &$device;
        return $object;
    }
    /**
    * Lists the ids of the table values
    *
    * @return The ID of this sensor
    *
    * @SuppressWarnings(PHPMD.ShortMethodName)
    */
    public function id()
    {
        return $this->table()->get("sensor");
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
        }
        return $ret;
    }
    /**
    * Returns the table as an array
    *
    * @param bool $default Whether to include the default params or not
    *
    * @return array
    */
    public function toArray($default = true)
    {
        $return = (array)$this->table()->toArray($default);
        if ($default) {
            $driver = $this->driver()->toArray();
            $return = array_merge($driver, $return);
        }
        $params = json_decode($return["params"], true);
        if (empty($return["type"])) {
            $return["type"] = implode(
                "", array_slice(explode('\\', get_class($this->driver())), -1)
            );
        }
        $return["params"] = (array)$params;
        if ($default) {
            $return["otherTypes"] = \HUGnet\devices\inputTable\Driver::getTypes($return["id"]);
            $return["validUnits"] = $this->units()->getValid();
            $return["validIds"] = $this->driver()->getDrivers();
        }
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
    * @return bool Whether we found this in the db or not.
    */
    public function load($data)
    {
        $ret = parent::load($data);
        if (!$ret) {
            $ret = $this->table()->insertRow();
        }
        return $ret;
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
        include_once dirname(__FILE__)."/../devices/inputTable/Driver.php";
        if (empty($driver)) {
            $driver = \HUGnet\devices\inputTable\Driver::getDriver(
                $this->table()->get("id"),
                $this->table()->get("type")
            );
            $this->table()->set("driver", $driver);
        }
        if (!is_object($this->_driverCache[$driver])) {
            $this->_driverCache[$driver] = \HUGnet\devices\inputTable\Driver::factory($driver, $this);
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
        include_once dirname(__FILE__)."/../devices/datachan/Driver.php";
        $units = \HUGnet\devices\datachan\Driver::factory(
            $this->get("unitType"),
            $this->get("storageUnit")
        );
        return $units;
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
    */
    public function decodeData(
        &$string, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        return $this->driver()->decodeData($string, $deltaT, $prev, $data);
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param array $data    The data to use
    * @param int   $channel The channel to get
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function encodeData($data, $channel = 0)
    {
        return $this->driver()->encodeData($data, $channel);
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
        if (!is_array($extra)) {
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
    /**
    * This takes the class and makes it into a setup string
    *
    * @return Reference to the network object
    */
    public function encode()
    {
        $string  = sprintf("%02X", ($this->get("id") & 0xFF));
        $string .= $this->driver()->encode($this);
        return $string;
    }
    /**
    * This builds the class from a setup string
    *
    * @param string $string The setup string to decode
    *
    * @return Reference to the network object
    */
    public function decode($string)
    {
        if (!is_string($string) || (strlen($string) < 2)) {
            return;
        }
        $this->set("id", hexdec(substr($string, 0, 2)));
        $extra = substr($string, 2);
        if (strlen($extra) > 1) {
            $this->driver()->decode($extra, $this);
        }
        $this->fixTable();
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channels()
    {
        $channels = (array)$this->driver()->channels();
        $sid = $this->id();
        foreach (array_keys($channels) as $key) {
            $channels[$key]['label'] = $this->get("location");
            $channels[$key]["sensor"] = $sid;
        }
        return $channels;
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channelStart()
    {
        $chan   = 0;
        $sensor = $this->id();
        for ($i = 0; $i < $sensor; $i++) {
            $chan += count($this->device()->input($i)->channels());
        }
        return $chan;
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function device()
    {
        return $this->_device;
    }
    /**
    * Gets the config and saves it
    *
    * @param string $url The url to post to
    *
    * @return string The left over string
    */
    public function post($url = null)
    {
        if (!is_string($url) || (strlen($url) == 0)) {
            $master = $this->system()->get("master");
            $url = $master["url"];
        }
        $sensor = $this->toArray(false);
        return \HUGnet\Util::postData(
            $url,
            array(
                "uuid"   => urlencode($this->system()->get("uuid")),
                "id"     => sprintf("%06X", $sensor["dev"]).".".$sensor["sensor"],
                "action" => "put",
                "task"   => "sensor",
                "data"   => $sensor,
            )
        );
    }
    /**
    * Stores data into the database
    *
    * @param bool $replace Replace any record that is in the way
    *
    * @return null
    */
    public function store($replace = true)
    {
        return parent::store($replace);
    }
}


?>
