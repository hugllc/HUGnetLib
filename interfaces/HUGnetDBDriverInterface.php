<?php
/**
 * Classes for dealing with devices
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
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
require_once dirname(__FILE__)."/HUGnetClass.php";
require_once dirname(__FILE__)."/../interfaces/HUGnetDBDriverInterface.php";
/**
 * Base class for all database work
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.
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
abstract class HUGnetDBDriver extends HUGnetClass implements HUGnetDBDriverInterface
{
    /** @var int This is where we store the limit */
    public $limit = 0;
    /** @var int This is where we store the start */
    public $start = 0;
    /** @var array This is where we store the fields */
    protected $fields = array();
    /** @var array This is where we store the fields in the query */
    protected $dataFields = array();

    /** @var string This is where we store the query */
    protected $query = "";

    /**
    * Register this database object
    *
    * @param object $table The table to attach myself to
    *
    * @return object of type mysqlDriver
    */
    public function __construct(&$table)
    {
        $this->myConfig = &ConfigContainer::singleton();
        $this->pdo = &$this->myConfig->dbServers()->getPDO();
        $this->myTable = &$table;
        $this->columns();
    }

    /**
    *  Adds a field to the devices table for cache information
    *
    * @param string $name    The name of the field
    * @param string $type    The type of field to add
    * @param mixed  $default The default value for the field
    * @param bool   $null    Whether null is a valid value for the field
    *
    * @return null
    */
    public function addField($name, $type="TEXT", $default=null, $null=false)
    {
        if (isset($this->fields[$name])) {
            return true;
        }
        $this->query  = "ALTER TABLE `".$this->table."` ADD `$name` $type ";
        if (!$null) {
            $this->query .= "NOT NULL ";
        }
        if (!is_null($default)) {
            $this->query .= " DEFAULT '$default'";
        }
    }

    /**
    * Creates the field array
    *
    * @return null
    */
    abstract protected function columns();
    /**
    * Creates the database table.
    *
    * Must be defined in child classes
    *
    * @return bool
    */
    abstract public function createTable();

    /**
    * Gets an attribute from the PDO object
    *
    * @param string $attrib The attribute to get.
    *
    * @return mixed
    */
    public function getAttribute($attrib)
    {
        return $this->myTable->getAttribute($attrib);
    }

    /**
    * Returns an array made for the execute query
    *
    * @param array $data The data to prepare
    * @param array $keys The keys to use
    *
    * @return array
    */
    protected function prepareData($data, $keys)
    {
        if (!is_array($keys)) {
            return array();
        }
        $ret = array();
        if (!isset($data[$this->id]) && $this->autoIncrement) {
            $data[$this->id] = $this->getNextID();
        }

        foreach ($keys as $k) {
            $ret[] = $data[$k];
        }
        return $ret;
    }

    /**
    * Creates an add query
    *
    * @param bool  $replace If true it replaces the "INSERT"
    *                       keyword with "REPLACE".  Not all
    *                       databases support "REPLACE".
    *
    * @return string
    */
    protected function insert($replace = false)
    {
        // Do we replace or insert?
        if ($replace) {
            $query = "REPLACE";
        } else {
            $query = "INSERT";
        }
        //
        $fields = implode(", ", $this->fields);
        $values = implode(", ", "?");
        // Build the rest of the query.
        $query .= " INTO ".$this->table()." (".$fields.") VALUES (".$values.")";
        return $query;
    }

    /**
    * Adds an row to the database
    *
    * @return bool Always False
    */
    public function dataFields($fields = array())
    {
        if (empty($fields)) {
            $this->dataFields = $this->fields;
        } else {
            foreach (array_keys((array)$this->fields) as $field) {
                if (!is_bool(array_search($field, (array)$fields))) {
                    $this->dataFields[$field] = $field;
                }
            }
        }
    }
    /**
    * Adds an row to the database
    *
    * @param array $info The row in array form
    *
    * @return bool Always False
    */
    public function replace($info)
    {
        return $this->insert($info, true);
    }

    /**
    * Updates a row in the database.
    *
    * @return mixed
    */
    public function update()
    {
        $values = implode(" = ?, ", $this->fields)." = ?";
        $this->query  = " UPDATE ".$this->table()." SET ".$fields;
    }

    /**
    * Gets all rows from the database
    *
    * @param string $where   Where clause
    * @param array  $data    Data for query
    *
    * @return null
    */
    public function selectWhere($where, $data) {
        $this->query = " SELECT * FROM ".$this->table();
        $this->where($where);
        $this->orderby();
        $this->limit();
    }

    /**
    * Gets all rows from the database
    *
    * @return array
    */
    protected function where($where)
    {
        if (empty($where)) {
            return;
        }
        $this->query .= " WHERE ".$where;
    }

    /**
    * Gets all rows from the database
    *
    * @return array
    */
    protected function table()
    {
        $this->query .= "`".$this->myTable->sqlTable."`";
    }

    /**
    * Gets all rows from the database
    *
    * @return array
    */
    protected function orderby()
    {
        if (empty($this->myTable->sqlOrderBy)) {
            return;
        }
        $this->query .= " ORDER BY ".$this->myTable->sqlOrderBy;
    }

    /**
    * Return the ORDER BY clause
    *
    * @param bool $start Whether to include the 'start' portion
    *
    * @return string
    */
    protected function limit($start = true)
    {
        if (empty($this->myTable->sqlLimit)) {
            return;
        }
        $this->query .= " LIMIT ";
        if ($start) {
            $this->query .= (int)$this->myTable->sqlStart.", ";
        }
        $this->query .= (int)$this->myTable->sqlLimit;
    }

    /**
    * Gets the next ID to use from the table.
    *
    * This only works with integer ID columns!
    *
    * @return int
    */
    function getNextID()
    {
        $query = "SELECT MAX(".$this->myTable->id.") as id "
                ." from ".$this->table();
        $ret   = $this->query($query);
        $newID = (isset($ret[0]['id'])) ? (int) $ret[0]['id'] : 0 ;
        return $newID + 1;
    }

    /**
    * Gets one less that the smallest ID to use from the table.
    *
    * This only works with integer ID columns!
    *
    * @return int
    */
    function getPrevID()
    {
        $query = "SELECT MIN(".$this->myTable->id.") as id "
                ." from ".$this->table();
        $ret   = $this->query($query);
        $newID = ($ret[0]['id'] < 0) ? (int) $ret[0]['id'] : 0 ;
        return $newID - 1;
    }
    /**
    * Removes a row from the database.
    *
    * @param string $where Where clause
    * @param array  $data  Data for query
    *
    * @return mixed
    */
    public function deleteWhere($where, $data)
    {
        $this->query  = " DELETE FROM ".$this->table();
        $this->where($where);
    }
    /**
    * Removes a row from the database.
    *
    * @param string $where Where clause
    * @param array  $data  Data for query
    *
    * @return mixed
    */
    public function delete()
    {
        $id = $this->myTable->id;
        $this->deleteWhere($id." = ? ");
        $data = array($this->myTable->$id);
        return $this->query($query, $data, false);
    }

}


?>
