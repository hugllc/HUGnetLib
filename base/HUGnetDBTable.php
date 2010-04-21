<?php
/**
 * Abstract class for building SQL queries
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Base
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** require our base class */
require_once dirname(__FILE__)."/HUGnetClass.php";
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";
require_once dirname(__FILE__)."/HUGnetContainer.php";
/**
 * Base class for all database work
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.  This
 * is a query building class.  That is just about all that it does.  It is abstract
 * because a class should be built for each pdo driver.  These are generally very
 * small.  This class will be used by the table classes to query the database.
 *
 * @category   Base
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class HUGnetDBTable extends HUGnetContainer
{
    /** @var int This is where we store the limit */
    public $sqlLimit = 0;
    /** @var int This is where we store the start */
    public $sqlStart = 0;
    /** @var string The orderby clause for this table */
    public $sqlOrderBy = "";

    /** @var string This is the table we should use */
    public $sqlTable = "";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = "";
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
    public $sqlColumns = array();
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
    */
    public $sqlIndexes = array();

    /** @var object This is where we store our sqlDriver */
    protected $myDriver = null;
    /** @var object This is where we store our configuration object */
    protected $myConfig = null;
    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /**
    * This is the constructor
    *
    * @param mixed $data This is an array or string to create the object from
    */
    function __construct($data="")
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
        parent::__construct($data);
        $this->myConfig = &ConfigContainer::singleton();
        if (is_object($this->myConfig->servers)) {
            $this->myDriver = &$this->myConfig->servers->getDriver(
                $this,
                $this->group
            );
        }
        if (!is_object($this->myDriver)) {
            $this->throwException(
                "No available database connection available in group '".$this->group
                ."'.  Check your database configuration.  Available php drivers: "
                .implode(", ", PDO::getAvailableDrivers()), -2
            );
            // @codeCoverageIgnoreStart
            // It thinks this line won't run.  The above function never returns.
        }
        // @codeCoverageIgnoreEnd
        $this->verbose($this->myConfig->verbose);
    }

    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toDB($default = true)
    {
        foreach ((array)$this->sqlColumns as $col) {
            $array[$col["Name"]] = $this->data[$col["Name"]];
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
        $ret = $this->myDriver->selectWhere($key);
        $this->myDriver->fetchInto();
        return $ret;
    }
    /**
    * This function updates the record currently in this table
    *
    * @return bool True on success, False on failure
    */
    public function updateRow()
    {
        if ($this->default == $this->data) {
            return false;
        }
        return $this->myDriver->updateOnce($this->toDB());
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
        if ($this->isEmpty()) {
            return false;
        }
        if ($this->default[$this->sqlId] === $this->data[$this->sqlId]) {
            $cols = $this->myDriver->autoIncrement();
        }
        return $this->myDriver->insertOnce($this->toDB(), (array)$cols, $replace);
    }

    /**
    * This function updates the record currently in this table
    *
    * @return bool True on success, False on failure
    */
    public function deleteRow()
    {
        if ($this->default == $this->data) {
            return false;
        }
        return $this->myDriver->deleteWhere($this->toDB());
    }

    /**
    * This function updates the record currently in this table
    *
    * @return bool True on success, False on failure
    */
    public function create()
    {
        return $this->myDriver->createTable();
    }
    /**
    * This function gets a record with the given key
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return array Array of objects
    */
    public function select($where, $data = array())
    {
        $this->myDriver->selectWhere($where, $data);
        return $this->myDriver->fetchAll();
    }
    /**
    * This function creates other tables that are identical to this one, except
    * for the data given.
    *
    * @param array &$data The data to populate in the new class.
    *
    * @return object A reference to a table object
    */
    public function &factory(&$data = array())
    {
        $class = get_class($this);
        $ret = new $class($this->toArray());
        $ret->fromAny($data);
        return $ret;
    }
    /**
    * This function creates other tables that are identical to this one, except
    * for the data given.
    *
    * @param int $verbose The verbose number to use
    *
    * @return object A reference to a table object
    */
    public function verbose($verbose)
    {
        parent::verbose($verbose);
        if (is_object($this->myDriver)) {
            $this->myDriver->verbose($verbose);
        }
    }
}


?>
