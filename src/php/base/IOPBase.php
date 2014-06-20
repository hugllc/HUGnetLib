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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
abstract class IOPBase extends SystemTableBase
    implements \HUGnet\interfaces\WebAPI
{
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
    * Returns the driver object
    *
    * @return object The driver requested
    */
    private function _getTableEntries()
    {
        $entry = $this->system()->table(ucfirst($this->driverLoc));
        $return = array();
        $arch = $this->device()->get("arch");
        $values = $entry->select(
            array("arch" => $arch)
        );
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
        if (!$ret) {
            $this->_new = true;
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
    * Sets the table entry, based on the given ID
    *
    * @param int $id The id of the entry to set this input to
    *
    * @return boolean True on success, false on failure
    */
    public function setEntry($id)
    {
        $entry = $this->system()->table(ucfirst($this->driverLoc));
        $arch = $this->device()->get("arch");
        $ret = $entry->selectOneInto(
            array("arch" => $arch, "id" => (int)$id)
        );
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
        if (is_object($entry)) {
            $data["tableEntry"] = $entry->toArray();
        } else {
            $data["tableEntry"] = array();
        }
        $ret = $this->change($data);
        if ($ret) {
            $this->device()->setParam("LastModified", $this->system()->now());
            $this->device()->store();
            return "regen";
        }
        return -1;
    }
}


?>
