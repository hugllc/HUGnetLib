<?php
/**
 * Abstract class for building SQL queries
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
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
namespace HUGnet\db;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** require our base class */
require_once dirname(__FILE__)."/Driver.php";
/** require our base class */
require_once dirname(__FILE__)."/../base/Container.php";
/**
 * Base class for all database work
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.  This
 * is a query building class.  That is just about all that it does.  It is abstract
 * because a class should be built for each pdo driver.  These are generally very
 * small.  This class will be used by the table classes to query the database.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class TableBase extends \HUGnet\base\Container
{
    /** @var int This is where we store the limit */
    public $sqlLimit = 0;
    /** @var int This is where we store the start */
    public $sqlStart = 0;
    /** @var string The orderby clause for this table */
    public $sqlOrderBy = "";

    /** @var string This is the table we should use */
    protected $sqlTable = "";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    protected $sqlId = "";
    /**
    * @var array This is the definition of the columns
    *
    * This should consist of the following structure:
    * array(
    *   "name" => array(
    *       "Name"          => string The name of the column
    *       "Type"          => string The type of the column
    *       "Default"       => mixed  The default value for the column
    *       "Null"          => bool   true if null is allowed, false otherwise
    *       "AutoIncrement" => bool   true if the column is auto_increment
    *       "CharSet"       => string the character set if the column is text or char
    *       "Collate"       => string colation if the table is text or char
    *       "Primary"       => bool   If we are a primary Key.
    *       "Unique"        => bool   If we are a unique column.
    *   ),
    *   "name2" => array(
    *   .
    *   .
    *   .
    * );
    *
    * Not all fields have to be filled in.  Name and Type are the only required
    * fields.  The index of the base array should be the same as the "Name" field.
    */
    protected $sqlColumns = array();
    /**
    * @var array This is the definition of the indexes
    *
    *   array(
    *       "Name" => array (
    *           "Name"    => string The name of the index
    *           "Unique"  => bool   Create a Unique index
    *           "Columns" => array  Array of column names
    *       ),
    *       "name2" => array(
    *       .
    *       .
    *   ),
    *
    *  To add a length to the column, simply add a comma and then the length to
    *  the column name in the "Columns" array.  For a column named "colName" with
    *  a index length of 15 would be:
    *
    *  "colName,15"
    */
    protected $sqlIndexes = array();

    /** @var object This is where we store our sqlDriver */
    private $_driver = null;
    /** @var object This is where we store our connection object */
    private $_connect = null;
    /** @var object This is where we store our configuration object */
    protected $myConfig = null;
    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
    );
    /** @var object This is where we store our connection object */
    private $_readonly = false;

    /**
    * This is the constructor
    *
    * @param object &$system  The system object to use
    * @param mixed  $data     This is an array or string to create the object from
    * @param object &$connect The connection manager
    */
    protected function __construct(&$system, $data="", &$connect = null)
    {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $this->setupColsDefault();
        parent::__construct($system, $data);
        $this->_connect = $connect;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_driver);
    }
    /**
    * This helps put us to sleep
    *
    * @return array The elements to save
    */
    public function __sleep()
    {
        return array_keys(get_object_vars($this));
    }
    /**
    * This helps wake us up.
    *
    * @return null
    */
    public function __wakeup()
    {
    }
    /**
    * This function returns a reference to the database driver.
    *
    * @return Database Driver object reference
    */
    protected function &dbDriver()
    {
        $group = $this->get("group");
        if (!is_object($this->_driver) || ($this->default["group"] != $group)) {
            $this->_driver = Driver::factory(
                $this->system(), $this, $this->_connect
            );
            $this->default["group"] = $group;
        }
        return $this->_driver;
    }
    /**
    * This function creates other tables that are identical to this one, except
    * for the data given.
    *
    * @param mixed $data This is an array or string to create the object from
    *
    * @return object A reference to a table object
    */
    public function &duplicate($data)
    {
        $obj = clone $this;
        $obj->clearData();
        $obj->fromAny($data);
        return $obj;
    }
    /**
    * This function creates other tables that are identical to this one, except
    * for the data given.
    *
    * @param mixed $flag Set the flag if it is bool.  Otherwise just return the flag
    *
    * @return bool The read only flag
    */
    public function readonly($flag = null)
    {
        if (is_bool($flag) || is_numeric($flag)) {
            $this->_readonly = (bool)$flag;
        }
        return $this->_readonly;
    }
    /**
    * Clears out the data
    *
    * @return null
    */
    public function clearData()
    {
        $this->data = $this->default;
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @return null
    */
    protected function setupColsDefault()
    {
        // This loads any columns that are not already in $this->default into
        // that array so that they will be picked up by the database.  This must
        // happen before any calls to the parent constructor or to from***().
        foreach ((array)$this->sqlColumns as $col) {
            if (!isset($this->default[$col["Name"]])) {
                $this->default[$col["Name"]] = $col["Default"];
            }
        }
        $this->clearData();
    }
    /**
    * Returns an array with only the values the database cares about
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function toDB($default = true)
    {
        foreach ((array)$this->sqlColumns as $col) {
            $key = $col["Name"];
            $array[$key] = $this->get($key);
        }
        return (array)$array;
    }

    /**
    * This function gets a record with the given key
    *
    * @param mixed $key This is either an array or a straight value
    *
    * @return bool True on success, False on failure
    */
    public function getRow($key)
    {
        if (!is_array($key)) {
            $key = array($this->sqlId => $key);
        }
        $ret = $this->dbDriver()->selectWhere($key);
        $this->dbDriver()->fetchInto();
        $this->dbDriver()->reset();
        return $ret;
    }
    /**
    * This function updates the record currently in this table
    *
    * @param array $columns The columns to update, defaults to all
    *
    * @return bool True on success, False on failure
    */
    public function updateRow($columns = array())
    {
        if ($this->isEmpty() || $this->readonly()) {
            return false;
        }
        $ret = $this->dbDriver()->updateOnce($this->toDB(), "", array(), $columns);
        $this->dbDriver()->reset();
        return $ret;
    }
    /**
    * This function updates the record currently in this table
    *
    * @param bool $replace Replace any records found that collide with this one.
    *
    * @return bool True on success, False on failure
    */
    public function insert($replace = false)
    {
        if ($this->isEmpty() || $this->readonly()) {
            return false;
        }
        $sqlId = $this->sqlId;
        if ($this->default[$this->sqlId] === $this->get($sqlId)) {
            $cols = $this->dbDriver()->autoIncrement();
        }
        $ret = $this->dbDriver()->insert($this->toDB(), (array)$cols, $replace);
        return $ret;
    }
    /**
    * This function updates the record currently in this table
    *
    * @return bool True on success, False on failure
    */
    public function insertEnd()
    {
        $this->dbDriver()->reset();
    }
    /**
    * This function updates the record currently in this table
    *
    * @param bool $replace Replace any records found that collide with this one.
    *
    * @return bool True on success, False on failure
    */
    public function insertRow($replace = false)
    {
        $this->dbDriver()->reset();
        $ret = $this->insert($replace);
        $this->insertEnd();
        return $ret;
    }

    /**
    * This function updates the record currently in this table
    *
    * @return bool True on success, False on failure
    */
    public function deleteRow()
    {
        return $this->delete($this->toDB());
    }
    /**
    * This function gets a record with the given key
    *
    * @param mixed $where The where clause
    *
    * @return array Array of objects
    */
    public function &delete($where)
    {
        if ($this->readonly()) {
            return false;
        }
        $ret = $this->dbDriver()->deleteWhere($where);
        $this->dbDriver()->reset();
        return $ret;
    }

    /**
    * This function updates the record currently in this table
    *
    * @return bool True on success, False on failure
    */
    public function create()
    {
        $ret = $this->dbDriver()->createTable();
        if ($ret) {
            foreach ((array)$this->sqlIndexes as $index) {
                $this->dbDriver()->addIndex($index);
            }
        }
        $this->dbDriver()->reset();
        return $ret;
    }
    /**
    * This function gets a record with the given key
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    * @param int    $style The style of the return
    *
    * @return array Array of objects
    */
    public function &select($where, $data = array(), $style = \PDO::FETCH_CLASS)
    {
        $this->dbDriver()->selectWhere($where, $data);
        $ret = $this->dbDriver()->fetchAll($style);
        $this->dbDriver()->reset();
        return $ret;
    }
    /**
    * Returns the number of records this query would return
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return false on failure, int on success
    */
    public function count($where, $data = array())
    {
        return $this->dbDriver()->countWhere($where, $data);
    }
    /**
    * This function gets a record with the given key
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return array Array of objects
    */
    public function selectIDs($where, $data = array())
    {
        $this->dbDriver()->selectWhere(
            $where,
            $data,
            array($this->sqlId => $this->sqlId)
        );
        $res = $this->dbDriver()->fetchAll(\PDO::FETCH_ASSOC);
        $this->dbDriver()->reset();
        $ret = array();
        foreach ((array)$res as $r) {
            $ret[$r[$this->sqlId]] = $r[$this->sqlId];
        }
        return $ret;
    }
    /**
    * This function gets a record with the given key
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return bool True on success, False on failure
    */
    public function selectInto($where, $data = array())
    {
        $this->dbDriver()->selectWhere($where, $data);
        return $this->nextInto();
    }
    /**
    * This puts the next result into the object
    *
    * @return bool True on success, False on failure
    */
    public function nextInto()
    {
        $this->clearData();
        $ret = $this->dbDriver()->fetchInto();
        if ($ret === false) {
            $this->dbDriver()->reset();
        }
        return $ret;
    }
    /**
    * function to set Group
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setGroup($value)
    {
        if (is_string($value) && ($value !== $this->data["group"])) {
            $this->data["group"] = $value;
            $this->system()->out("Setting group to $value", 7);
            $this->create();
        }
    }

}


?>
