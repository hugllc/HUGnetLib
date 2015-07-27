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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\base;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/SystemTableBase.php";
/** This is our base class */
require_once dirname(__FILE__)."/../interfaces/WebAPI.php";

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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
abstract class IOPBase extends SystemTableBase
    implements \HUGnet\interfaces\WebAPI, \HUGnet\interfaces\WebAPI2
{
    /** These are our keys to search for.  Null means search everything given */
    protected $keys = array("dev");
    /** This is the type of IOP this is */
    protected $type = "replaceme";
    /**
    * This is the cache for the drivers.
    */
    private $_driverCache = array();
    /**
    * This is the cache for the drivers.
    */
    protected $driverLoc = "replaceme";
    /**
    * This is the device we rode in on
    */
    private $_device;
    /**
    * This is the device we rode in on
    */
    private $_driverTable = null;
    /**
    * This is the device we rode in on
    */
    private $_new = false;

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
    * @param string $dbtable The table to use
    * @param object &$device The device object to use
    *
    * @return null
    */
    public static function &factory(
        &$system, $data=null, $dbtable=null, &$device = null
    ) {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a device object",
            !is_object($device)
        );
        $class = get_called_class();
        $object = new $class($system, $dbtable);
        $object->_device = &$device;
        if (!is_null($data)) {
            $object->load($data);
        }
        return $object;
    }
    /**
    * This returns the number of tables on this device
    *
    * @return The number of tables on the is device
    */
    public function count()
    {
        return (int)$this->_device->get(ucfirst($this->type)."Tables");
    }
    /**
    * This returns the URL, including the id, if it exists
    *
    * @param string $url This is the base URL
    *
    * @return reference to the table class object
    */
    public function url($url = "")
    {
        $url = (string)$url.$this->_device->url().$this->url;
        $id = $this->id();
        if (!is_null($id)) {
            $url .= "/$id";
        }
        return $url;
    }
    /**
    * Sets a value
    *
    * @param string $field the field to set
    * @param mixed  $value the value to set
    *
    * @return null
    */
    public function mix($field, $value)
    {
        if (is_array($value)) {
            $old = (array)parent::get($field);
            foreach ($value as $k => $v) {
                $old[$k] = $v;
            }
            return parent::set($field, $old);
        }
        $old = parent::get($field);
        if (empty($old)) {
            return parent::set($field, $value);
        }
        return parent::get($field);
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
    * Returns the arch to use for the table
    *
    * @return string The arch
    */
    protected function getTableArch()
    {
        return $this->device()->get("arch");
    }
    /**
    * Returns the driver object
    *
    * @return object The driver requested
    */
    private function _getTableEntries()
    {
        $entry = $this->system()->table(ucfirst($this->driverLoc));
        $return = array();
        $arch = $this->getTableArch();
        $where = array();
        if (!empty($arch)) {
            $where["arch"] = $arch;
        }
        $values = $entry->select($where);
        foreach ((array)$values as $val) {
            $return[$val->get("id")] = $val->get("name");
        }
        return $return;
    }
    /**
    * Gets the extra values
    *
    * @param mixed $index The extra index to use
    *
    * @return The extra value (or default if empty)
    */
    public function getExtra($index)
    {
        return $this->driver()->getExtra($index);
    }
    /**
    * Sets the extra values
    *
    * @param mixed $index The extra index to use
    * @param mixed $value The value to set it to
    *
    * @return The extra value (or default if empty)
    */
    public function setExtra($index, $value)
    {
        return $this->driver()->setExtra($index, $value);
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
            $entry = $this->driver()->entry();
            if (is_object($entry)) {
                $return["fullEntry"] = $entry->fullArray();
            } else {
                unset($return["fullEntry"]);
            }
            $driver = $this->driver()->toArray();
            $return = array_merge($driver, $return);
            $return["otherTables"] = $this->_getTableEntries();
        }
        $params = json_decode($return["params"], true);
        if (empty($return["type"])) {
            $return["type"] = implode(
                "", array_slice(explode('\\', get_class($this->driver())), -1)
            );
        }
        $return["params"] = (array)$params;
        if (!is_array($return["tableEntry"])) {
            $return["tableEntry"] = (array)json_decode($return["tableEntry"], true);
        }
        return (array)$return;
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
        $file = dirname(__FILE__)."/../devices/".$this->driverLoc."/Driver.php";
        if (file_exists($file)) {
            include_once $file;
        }
        $class = "\\HUGnet\\devices\\".$this->driverLoc."\\Driver";
        if (empty($driver)) {
            $driver = $class::getDriver(
                $this->table()->get("id"),
                $this->table()->get("type")
            );
            $this->table()->set("driver", $driver);
        }
        $this->_driverName = $driver;
        if (!is_object($this->_driverCache[$driver])) {
            $this->_driverCache[$driver] = $class::factory(
                $driver, $this
            );
        }
        return $this->_driverCache[$driver];
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
        if ($this->id() >= $this->count()) {
            $this->_new = true;
        } else if (!$ret) {
            $ret = $this->table()->insertRow();
        }
        return $ret;
    }
    /**
    * Loads the data into the table class
    *
    * @return bool true if this is a new iop.  False otherwise
    */
    public function isNew()
    {
        return $this->_new;
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
        // This sets the driver
        $this->driver();

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
        $this->set("RawSetup", substr($string, 2));
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
    public function device()
    {
        return $this->_device;
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
     /**
    * Returns the driver that should be used for a particular device
    *
    * @return array The array of drivers that will work
    */
    public function getDrivers()
    {
        return $this->driver()->getDrivers();
    }
    /**
    * Clears out the data, while preserving the dev and index
    *
    * @return null
    */
    public function clear()
    {
        $data = array_intersect_key(
            $this->table()->toArray(true),
            array_flip($this->keys)
        );
        $this->table()->clearData();
        $this->table()->fromArray($data);
    }
    /**
    * Sets the table entry, based on the given ID
    *
    * @param int $id The id of the entry to set this input to
    *
    * @return boolean True on success, false on failure
    */
    public function setEntry($id)
    {
        $entry = $this->system()->table(ucfirst($this->driverLoc));
        $arch = $this->getTableArch();
        $where = array("id" => (int)$id);
        if (!empty($arch)) {
            $where["arch"] = $arch;
        }
        if ($ret) {
            $this->set("tableEntry", $entry->get("params"));
            $this->set(
                "lastTable", 
                $entry->get("id").": ".$entry->get("name")
            );
        }
        return (bool)$ret;
    }
    /**
    * returns a history object for this device
    *
    * @param object $args  The argument object
    * @param array  $extra Extra data from the
    *
    * @return string
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function webAPI($args, $extra)
    {
        $action = trim(strtolower($args->get("action")));
        $ret = null;
        if ($action === "put") {
            $ret = $this->_put($args);
        } else if ($action === "settable") {
            $ret = $this->_settable($args);
        }
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @param object $api   The API object
    * @param array  $extra Extra data from the
    *
    * @return string
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function webAPI2($api, $extra)
    {
        $method = trim(strtoupper($api->args()->get("method")));
        $extra  = $api->args()->get("restextra");
        $ret = null;
        if ($this->isNew() && ($method === "PUT")) {
            $api->response(404);
        } else if ($method === "PUT") {
            if (trim(strtolower($extra[0])) == "settable") {
                $ret = $this->setEntry((int)$api->args()->get("data"));
                if ($ret) {
                    $this->store();
                    $ret = "regen";
                }
            } else {
                $ret = $this->_put($api->args());
            }
            if ($ret == "regen") {
                $api->response(202);
                $ret = $this->toArray(true);
            } else {
                $api->response(500);
                $api->pdoerror($this->lastError(), \HUGnet\ui\WebAPI2::SAVE_FAILED);
            }
        } else {
            $api->response(501);
            $api->error(\HUGnet\ui\WebAPI2::NOT_IMPLEMENTED);
        }
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _settable($args)
    {
        $data = (array)$args->get("data");
        $ret = $this->setEntry((int)$data["id"]);
        if ($ret) {
            $this->store();
            return "regen";
        }
        return -1;
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _put($args)
    {
        $data = (array)$args->get("data");
        $entry = $this->driver()->entry($data["tableEntry"]);
        $data["tableEntry"] = is_object($entry) ? $entry->toArray() : array();
        $ret = $this->change($data);
        if ($ret) {
            $this->device()->setParam("LastModified", $this->system()->now());
            $this->device()->store();
            return "regen";
        }
        return -1;
    }
    /**
    * Returns an array of the pins and stuff this one uses
    *
    * @return null
    */
    public function uses()
    {
        return $this->driver()->uses();
    }
    /**
    * Returns an array of the pins and stuff this one uses
    *
    * @return null
    */
    public function ports()
    {
        if (method_exists($this->driver(), "ports")) {
            return $this->driver()->ports();
        }
        return array();
    }
    /**
    * Returns true if the object is empty, false otherwise
    *
    * @return bool
    */
    public function isEmpty()
    {
        // If the driver is empty, then the whole IOP object is empty
        return ($this->table()->get("id") == 0xFF);
    }
    /**
    * Changes data that is in the table and saves it
    *
    * @param array $where   The things the list should filter for
    * @param bool  $default Whether to add the default stuff on or not.
    *
    * @return null
    */
    public function getList($where = null, $default = false)
    {
        $return = array();
        $fct = $this->type;
        if (is_callable(array($this->_device, $fct))) {
            for ($i = 0; $i < $this->count(); $i++) {
                $return[$i] = $this->_device->$fct($i)->toArray($default);
            }
        }
        return $return;
    }

}


?>
