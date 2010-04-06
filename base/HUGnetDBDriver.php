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
//require_once dirname(__FILE__)."/../interfaces/HUGnetDBDriver.php";
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";
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
abstract class HUGnetDBDriver extends HUGnetClass
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
    /** @var string The name of this driver */
    protected $driver = "";
    /** @var bool Does this driver support auto_increment? */
    protected $autoIncrement = true;

    /**
    * Register this database object
    */
    public function __construct()
    {
        $this->myConfig = &ConfigContainer::singleton();
        $this->columns();
    }
    /**
    * Register this database object
    */
    public function __destruct()
    {
        $this->reset();
    }

    /**
    * Gets the instance of the class and
    *
    * @param object $table The table to attach myself to
    * @param object $pdo   The database object
    *
    * @return null
    */
    abstract static public function &singleton(&$table, PDO &$pdo);

    /**
    *  Adds a field to the devices table for cache information
    *
    * @param array $column @See columnDef for format
    *
    * @return null
    */
    public function addColumn($column)
    {
        $this->query  = "ALTER TABLE ".$this->table()." ADD ";
        $this->columnDef($column);
        $this->prepare();
        $this->execute();
        $this->reset();
    }
    /**
    *  Adds a field to the devices table for cache information
    *
    * @param array $columns array of $column entries @See columnDef for
    *                       $column format
    *
    * @return null
    */
    public function createTable($columns)
    {
        $this->query  = "CREATE TABLE IF NOT EXISTS ".$this->table();
        $this->query .= " (\n";
        $sep = "";
        foreach ((array)$columns as $column) {
            $this->query .= $sep."     ";
            $this->columnDef($column);
            $sep = ",\n";
        }
        $this->query .= "\n)";
        $this->prepare();
        $this->execute();
        $this->reset();
    }
    /**
    *  Adds a field to the devices table for cache information
    *
    *  The column parameter is defined as follows:
    *  $column["Name"] => string The name of the column
    *  $column["Type"] => string The type of the column
    *  $column["Default"] => mixed The default value for the column
    *  $column["Null"] => bool true if null is allowed, false otherwise
    *
    * @param string $name    The name of the field
    * @param string $type    The type of field to add
    * @param mixed  $default The default value for the field
    * @param bool   $null    Whether null is a valid value for the field
    *
    * @return null
    */
    protected function columnDef($column)
    {
        $this->query .= "`".$column["Name"]."` ".$column["Type"]." ";
        if ($column["Null"] == true) {
            $this->query .= " NULL ";
        } else {
            $this->query .= " NOT NULL ";
        }
        if (!is_null($column["Default"])) {
            $this->query .= " DEFAULT '".$column["Default"]."'";
        }
    }
    /**
    *  Adds a field to the devices table for cache information
    *
    *  The $index parameter should be defined as follows:
    *  $index["Name"] => string The name of the index
    *  $index["Type"] => string The type of the field
    *  $index["Columns"] => array Array of column names
    *
    * @param array $index Index array defined above.
    *
    * @return null
    */
    public function addIndex($index)
    {
        // Build the query
        $this->query  = "CREATE  ".strtoupper($index["Type"]);
        $this->query .= " INDEX `".$index["Name"]."` ON ";
        $this->query .= $this->table();
        $this->query .= " (`".implode((array)$index["Columns"], "`, `")."`)";
        $this->query();
    }

    /**
    * Creates the field array
    *
    * @return null
    */
    abstract protected function columns();

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
    * Sets the columns to use
    *
    * @param $columns array An array of columns to use
    *
    * @return null
    */
    protected function dataColumns($columns = array())
    {
        if (empty($columns)) {
            $this->columns = array_keys((array)$this->myTable->columns);
        } else {
            foreach (array_keys((array)$this->myTable->columns) as $column) {
                if (!is_bool(array_search($column, (array)$columns))) {
                    $this->columns[$column] = $column;
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
    public function selectWhere($where) {
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
        return "`".$this->myTable->sqlTable."`";
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
        $this->execute($data);
    }
    /**
    * Prepares a query to be put into the database
    *
    * @return mixed
    */
    public function prepare()
    {
        if (!is_object($this->pdo) || empty($this->query)) {
            return false;
        }
        if (($this->pdoStatement = $this->pdo->prepare($this->query)) === false) {
            return false;
        }
        return true;
    }
    /**
    * Removes a row from the database.
    *
    * @param array $data Data to use for the query.  Associate Array
    *
    * @return mixed
    */
    public function execute($data = array())
    {
        // Make sure everything is set up for us
        if (!is_object($this->pdoStatement)) {
            if (!$this->prepare()) {
                return false;
            }
        }
        return $this->pdoStatement->execute($data);
    }
    /**
    * Removes a row from the database.
    *
    * @param array $data Data to use for the query.  Associate Array
    *
    * @return mixed
    */
    public function reset()
    {
        if (is_object($this->pdoStatement)) {
            // close the cursor
            $this->pdoStatement->closeCursor();
            // Remove the statuemt
            $this->pdoStatement = null;
        }
    }
    /**
    * Removes a row from the database.
    *
    * @param array $query The query string
    * @param array $data  Data to use for the query
    *
    * @return mixed
    */
    protected function query($query = "", $data = array())
    {
        if (!is_object($this->pdo) || empty($query)) {
            return false;
        }
        $pdo = $this->pdo->prepare($query);
        $pdo->execute($data);
        $res = $pdo->fetchAll(PDO::FETCH_ASSOC);
        $pdo->closeCursor();
        return $res;
    }
}


?>
