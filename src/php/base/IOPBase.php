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
    * @param array  $table   The table to use
    *
    * @return null
    */
    public static function &factory(
        &$system, $data=null, $dbtable=null, &$device = null, $table = null
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
        $object->_driverTable = $table;
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
                $ent = $entry->toArray();
            } else {
                $ent = array();
            }
            if ($default === "entryonly") {
                return $ent;
            } else {
                $driver = $this->driver()->toArray();
                $return = array_merge($driver, $return);
            }
        }
        $params = json_decode($return["params"], true);
        if (empty($return["type"])) {
            $return["type"] = implode(
                "", array_slice(explode('\\', get_class($this->driver())), -1)
            );
        }
        $return["params"] = (array)$params;
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
                $driver, $this, $this->_driverTable
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
}


?>
