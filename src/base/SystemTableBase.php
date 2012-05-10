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
 * @subpackage Base
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
/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
abstract class SystemTableBase
{
    /** @var int The database table to use */
    private $_table = null;
    /** @var int The config to use */
    private $_system = null;
    /** @var int The database table class to use */
    protected $tableClass = null;

    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$system The configuration array
    * @param string $table   The table class to use
    *
    * @return null
    */
    private function __construct(&$system, $table)
    {
        System::exception(
            get_class($this)." needs to be passed a system object",
            "InvalidArgument",
            !is_object($system)
        );
        $this->_system = &$system;
        $this->_setTable($table);
    }
    /**
    * Sets the database table to use. It expects either an object or a string
    *
    * @param mixed &$table The table class to use
    *
    * @return null
    */
    private function _setTable(&$table)
    {
        if (is_string($table)) {
            $this->tableClass = $table;
        } else if (is_object($table)) {
            $this->tableClass = get_class($table);
            $this->_table = $table;
        }
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        if (is_object($this->_table)) {
            // This calls the destructor on the table object
            unset($this->_table);
        }
    }
    /**
    * This function gives us access to the table class
    *
    * @return reference to the table class object
    */
    protected function &table()
    {
        if (!is_object($this->_table)) {
            $class = Util::findClass($this->tableClass, "tables");
            $system = &$this->system();
            $this->_table = new $class($system);
        }
        return $this->_table;
    }
    /**
    * This function gives us access to the table class
    *
    * @return reference to the system object
    */
    public function &system()
    {
        return $this->_system;
    }
    /**
    * This function creates the system.
    *
    * @param object &$system The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $table   The table to use
    *
    * @return null
    */
    public static function &factory(
        &$system, $data=null, $table="GenericTable"
    ) {
        $class = get_called_class();
        $object = new $class($system, $table);
        $object->load($data);
        return $object;
    }
    /**
    * Deletes this record
    *
    * @return null
    */
    public function delete()
    {
        return $this->table()->deleteRow();
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
        if (is_int($data)) {
            $this->table()->set($this->table()->sqlId, $data);
            $ret = $this->table()->getRow($data);
        } else if (is_array($data)) {
            $where = "";
            $whereData = array();
            $sep = "";
            foreach ($data as $key => $value) {
                $where .= "$sep`$key` = ?";
                $sep = " AND ";
                $whereData[] = $value;
            }
            $ret = $this->table()->selectOneInto($where, $whereData);
        }
        if (!$ret && (is_array($data) || is_string($data))) {
            $this->table()->fromAny($data);
            $this->fixTable();
            $ret = true;
        }
        return (bool)$ret;
    }
    /**
    * Changes data that is in the table and saves it
    *
    * @param mixed $data (int)The id of the record,
    *                    (array) or (string) data info array
    *
    * @return null
    */
    public function change($data)
    {
        $ret = false;
        if (is_array($data) || is_string($data)) {
            $this->table()->fromAny($data);
            $this->fixTable();
            $this->table()->updateRow();
            $ret = true;
        }
        return (bool)$ret;
    }
    /**
    * Returns the table as a json string
    *
    * @return json string
    */
    public function json()
    {
        return json_encode($this->table()->toArray(true));
    }
    /**
    * Stores data into the database
    *
    * @param bool $replace Replace any record that is in the way
    *
    * @return null
    */
    public function store($replace = false)
    {
        $sid = $this->table()->get($this->table()->sqlId);
        $this->fixTable();
        if (!empty($sid)) {
            $ret = $this->table()->updateRow();
        } else {
            $ret = $this->table()->insertRow($replace);
        }
        return (bool)$ret;
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
        return $this->table()->get($field);
    }
    /**
    * Sets a value
    *
    * @param string $field the field to set
    * @param mixed  $value the value to set
    *
    * @return null
    */
    public function set($field, $value)
    {
        return $this->table()->set($field, $value);
    }
    /**
    * Lists the ids of the table values
    *
    * @param array $data The data to use with the where clause
    *
    * @return null
    */
    public function ids($data = array())
    {
        $where = "1";
        $whereData = array();
        foreach ((array)$data as $key => $value) {
            $where .= " AND `$key` = ?";
            $whereData[] = $value;
        }
        return $this->table()->selectIDs($where, $whereData);
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
    * This function creates the action item.  The default is just to return this.
    *
    * @return Reference to the action object
    */
    public function &action()
    {
        return $this;
    }
}


?>
