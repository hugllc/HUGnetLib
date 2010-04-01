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
/** The database went away */
define("HUGNETDB_META_ERROR_SERVER_GONE", 1);
/** The database went away */
define("HUGNETDB_META_ERROR_SERVER_GONE_MSG", "The server has gone away");
/** The database went away */
define("HUGNETDB_META_ERROR_DUPLICATE", 2);
/** The database went away */
define("HUGNETDB_META_ERROR_DUPLICATE_MSG", "Duplicate Entry");
/** Misc stuff */
require_once dirname(__FILE__)."/HUGnetClass.php";
require_once HUGNET_INCLUDE_PATH."/interfaces/HUGnetDB.php";
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
abstract class HUGnetDBDriver
{
    /**
    * Register this database object
    *
    * @param object $table The table to attach myself to
    *
    * @return object of type mysqlDriver
    */
    public function __construct(&$table)
    {
        $this->myTable = &$table;
        $this->getColumns();
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
        $query  = "ALTER TABLE `".$this->table."` ADD `$name` $type ";
        if (!$null) {
            $query .= "NOT NULL ";
        }
        if (!is_null($default)) {
            $query .= " DEFAULT '$default'";
        }
        if ($this->query($query, array(), false)) {
            $this->fields[$name] = $type;
            return true;
        } else {
            return false;
        }
    }

    /**
    * Creates the field array
    *
    * @return null
    */
    abstract protected function getColumns()

    /**
    * Creates the database table.
    *
    * Must be defined in child classes
    *
    * @return bool
    */
    abstract public function createTable();

    /**
    * Adds each element in the array as a row in the database
    *
    * @param array $infoArray An array of database rows to add
    * @param bool  $replace   If true it replaces the "INSERT"
    *                         keyword with "REPLACE".  Not all
    *                         databases support "REPLACE".
    *
    * @return int The number of successful inserts
    */
    public function addArray($infoArray, $replace = false)
    {
        if (!$this->checkDb()) {
            return 0;
        }
        if (!is_array($infoArray)) {
            return 0;
        }
        $query = $this->addQuery($infoArray[0], $keys, $replace);
        $ret   = $this->db->prepare($query);
        $count = 0;
        if (is_object($ret)) {
            foreach ($infoArray as $info) {
                $data = $this->prepareData($info, $keys);
                $val  = $this->queryExecute($query, $ret, $data, false);
                if ($val) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
    * Gets an attribute from the PDO object
    *
    * @param string $attrib The attribute to get.
    *
    * @return mixed
    */
    public function getAttribute($attrib)
    {
        if ($this->checkDb()) {
            return $this->db->getAttribute($attrib);
        }
        // @codeCoverageIgnoreStart
        // Can't get here.  So far every call is checked by checkDB before the call
        return null;
        // @codeCoverageIgnoreEnd
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
        $div    = "";
        $fields = "";
        $values = array();
        $v      = "";
        $keys   = array();
        $id = $this->myTable->id;
        if (empty($this->myTable->$id) && $this->autoIncrement) {
            $this->myTable->$id = $this->getNextID();
        }
        // Do we replace or insert?
        if ($replace) {
            $query = "REPLACE";
        } else {
            $query = "INSERT";
        }
        // Build the rest of the query.
        $query .= " INTO `".$this->table."` (".$fields.") VALUES (".$values.")";
        return $query;
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
    * This function MUST be overwritten by child classes
    *
    * @param array  $info  The row in array form.
    * @param string $where Where clause
    * @param array  $data  Data for query
    *
    * @return mixed
    */
    public function update()
    {
        $div    = "";
        $fields = "";
        $values = array();
        $v      = "";
        foreach ($this->fields as $key => $val) {
            if (!isset($info[$key]) || ($key == $this->id)) {
                continue;
            }
            $fields  .= $div.$key." = ? ";
            $values[] = $info[$key];
            $div      = ", ";
        }

        $values = array_merge($values, $data);
        $query  = " UPDATE `".$this->table."` SET ".$fields." WHERE ".$where;
        return $this->query($query, $values, false);
    }

    /**
    * Gets all rows from the database
    *
    * @param int    $limit   The maximum number of rows to return (0 to return all)
    * @param int    $start   The row offset to start returning records at
    * @param string $orderby The orderby Clause.  Must include "ORDER BY"
    *
    * @return array
    */
    public function getAll($limit = 0, $start = 0, $orderby="")
    {
        return $this->select("1", array(), $limit, $start, $orderby);
    }

    /**
    * Gets all rows from the database
    *
    * @param int $id The id of the row to get.
    *
    * @return array
    */
    public function get($id)
    {
        return $this->select($this->id."= ? ", array($id));
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
        $this->query = " SELECT * FROM ".$this->table()." "
                      .$this->where($where);
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
    * @return string
    */
    protected function limit()
    {
        if (empty($this->myTable->sqlLimit)) {
            return;
        }
        $this->query .= " LIMIT ";
        $this->query .= $this->myTable->sqlStart;
        $this->query .= ", ".(int)$this->myTable->sqlLimit;
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
                ." from `".$this->myTable->table."`";
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
                ." from `".$this->myTable->table."`";
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
        $id = $this->myTable->id;
        $this->query  = " DELETE FROM `".$this->myTable->table."`";
        $this->query .= $this->where($where);
        $data = array($this->myTable->$id);
        return $this->query($query, $data, false);
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
