<?php
/**
 * Abstract class for building SQL queries
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
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
namespace HUGnet\db;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** require our base class */
require_once dirname(__FILE__)."/connections/PDO.php";
/** require our base class */
require_once dirname(__FILE__)."/DriverBase.php";
/**
 * Base class for all database work
 *
 * This class uses the {@link http://www.php.net/pdo \PDO} extension to php.  This
 * is a query building class.  That is just about all that it does.  It is abstract
 * because a class should be built for each pdo driver.  These are generally very
 * small.  This class will be used by the table classes to query the database.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class Driver extends DriverBase
{
    /** These are the MongoDB conditionals we support */
    protected $conditionals = array(
        '$gt' => ">",
        '$gte' => ">=",
        '$lt' => "<",
        '$lte' => "<=",
        '$ne' => "<>",
    );
    /** These are the MongoDB logic gates we support */
    protected $gates = array(
        '$or' => " OR ",
        '$and' => " AND ",
        '$not' => " NOT ",
    );
    
    /**
    * Create the object
    *
    * @param object &$system  The system object
    * @param object &$table   The table object
    * @param object &$connect The connection manager
    * @param string $driver   The driver to use.  The right one is found if this
    *                         is left null.
    *
    * @return object The driver object
    */
    static public function &factory(
        &$system, &$table, &$connect = null, $driver=null
    ) {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a table object",
            !is_object($table)
        );
        if (!is_a($connect, "ConnectionManager")) {
            $connect = Connection::factory($system);
        }
        $group = $table->get("group");
        $connect->connect();
        if (!is_string($driver)) {
            $driver = $connect->driver($group);
        }
        $driver = ucfirst($driver);
        $class  = "\\HUGnet\\db\\drivers\\$driver";
        @include_once dirname(__FILE__)."/drivers/".$driver.".php";
        if (!class_exists($class)) {
            @include_once dirname(__FILE__)."/drivers/Sqlite.php";
            $class = "\\HUGnet\\db\\drivers\\Sqlite";
        }
        $obj = new $class($system, $table, $connect);
        return $obj;
    }
    /**
    * Register this database object
    */
    public function __destruct()
    {
        $this->reset();
    }

    /**
    * This can be called over and over with new data to insert many rows.
    *
    * reset() must be called before this is called.  Otherwise it could try to
    * use the wrong insert query.  @see insertOne if you just want to insert
    * one record.
    *
    * This is designed to be called in a loop.  $columns and $replace should
    * not change between records.  It should be called like:
    *
    * <pre>
    * $obj->reset();
    * loop {
    *    $obj->insert(stuff, here, asdf);
    * }
    * $obj->reset();
    * </pre>
    *
    * @param array $data    The data to use.  It just sets up the query if this is
    *                       empty.
    * @param array $columns The columns to insert.  Uses all of this is blank.
    * @param bool  $replace If true it replaces the "INSERT"
    *                       keyword with "REPLACE".  Not all
    *                       databases support "REPLACE".
    *
    * @return bool True on success, False on failure
    */
    public function insert($data = array(), $columns = array(), $replace = false)
    {
        // If there already is a query don't build another one.
        if (empty($this->query)) {
            $this->reset();
            // Do we replace or insert?
            if ($replace) {
                $this->query = "REPLACE";
            } else {
                $this->query = "INSERT";
            }
            $this->dataColumns($columns);
            // get the stuff to put in the query
            $fields = "`".implode("`, `", $this->columns)."`";
            $values = implode(", ", array_fill(0, count($this->columns), "?"));
            // Build the rest of the query.
            $this->query .= " INTO ".$this->table()." (".$fields.")";
            $this->query .= " VALUES (".$values.")";
        }
        if (!empty($data)) {
            // Insert it
            return $this->executeData($data);
        }
        return true;
    }

    /**
    * Returns the column list required to make autoincrement happen.
    *
    * @return array
    */
    public function autoIncrement()
    {
        foreach ((array)$this->myTable->sqlColumns as $col) {
            if (!$col["AutoIncrement"]) {
                $cols[$col["Name"]] = $col["Name"];
            }
        }
        return (array)$cols;
    }
    /**
    * Inserts a record into the database.  This one cleans up after itsef and
    * doesn't need any other things called around it.
    *
    * @param array $data    The data to use.  It just sets up the query if this is
    *                       empty.
    * @param array $columns The columns to insert.  Uses all of this is blank.
    * @param bool  $replace If true it replaces the "INSERT"
    *                       keyword with "REPLACE".  Not all
    *                       databases support "REPLACE".
    *
    * @return bool True on success, False on failure
    */
    public function insertOnce($data = array(), $columns = array(), $replace = false)
    {
        $this->reset();
        $ret = $this->insert($data, $columns, $replace);
        $this->reset();
        return $ret;
    }

    /**
    * This is an alias for insert
    *
    * This must be called in a certain way.  @see insert for information on this
    * one.  It has the same caveats
    *
    * @param array $data    The data to use.  It just sets up the query if this is
    *                       empty.
    * @param array $columns The columns to insert.  Uses all of this is blank.
    *
    * @return null
    */
    public function replace($data, $columns)
    {
        return $this->insert($data, $columns, true);
    }
    /**
    * Inserts a record into the database.  This one cleans up after itsef and
    * doesn't need any other things called around it.
    *
    * @param array $data    The data to use.  It just sets up the query if this is
    *                       empty.
    * @param array $columns The columns to insert.  Uses all of this is blank.
    *
    * @return string
    */
    public function replaceOnce($data, $columns)
    {
        $this->reset();
        $ret = $this->insert($data, $columns, true);
        $this->reset();
        return $ret;
    }
    /**
    * This gets either a key or a unique index and returns it as a where.
    *
    * This sets $this->idWhere, which is used by prepareData() to add the stuff
    * from here into the data array.  That way multiple records can be updated
    * with the same query.
    *
    * @return null
    */
    protected function idWhere()
    {
        $where = "";
        if (!empty($this->myTable->sqlId)) {
            // Use the table id field
            $this->idWhere[] = $this->myTable->sqlId;
            $where .= "`".$this->myTable->sqlId."` = ?";
        } else {
            // Check for a unique index to use
            $indexes = &$this->myTable->sqlIndexes;
            foreach ((array)$indexes as $ind) {
                if (!$ind["Unique"]) {
                    continue;
                }
                $nWhere = "";
                $sep = "";
                foreach ($ind["Columns"] as $col) {
                    if (stripos($col, ",") === false) {
                        $nWhere .= $sep."`".$col."` = ?";
                        $sep = " AND ";
                        $this->idWhere[] = $col;
                    }
                }
                if (strlen($nWhere) > 0) {
                    if (!empty($where)) {
                        $where .= " AND ";
                    }
                    $where .= "(".$nWhere.")";
                }
            }
        }
        if (empty($where)) {
            $where = "(0)";
        }
        $this->where($where);
    }
    /**
    * This converts a MongoDB style array where into an SQL where statement
    * 
    * @param array $array The array to convert
    *
    * @return null
    */
    protected function arrayWhere($array)
    {
        if (!is_array($array)) {
            return;
        }
        $string = "";
        $data = array();
        foreach ((array)$array as $name => $value) {
            $string .= $sep.$this->arrayWhereDoStuff($name, $value, $data);
            $sep = $this->gates['$and'];
        }
        $this->where("(".$string.")", $data);
    }
    /**
     * This converts a single set of Comparison operators
     * 
     * @param string $name  The name of the column
     * @param array  $value The array to convert
     * @param array  &$data The data array to use
     *
     * @return null
     */
    protected function arrayWhereDoStuff($name, $value, &$data)
    {
        $string = "";
        if (isset($this->gates[$name])) {
            $string .= $this->arrayWhereLogic($name, $value, $data);
        } else if (isset($this->conditionals[$name])) {
            $string .= $this->arrayWhereComparison($name, $value, $data);
        } else if (isset($this->myTable->sqlColumns[$name])) {
            $string .= "`".$name."` = ? ";
            $data[] = $value;
        } else {
            $string = "0";
        }
        return $string;
    }
    /**
     * This converts a single set of Comparison operators
     * 
     * @param string $name  The name of the column
     * @param array  $array The array to convert
     * @param array  &$data The data array to use
     *
     * @return null
     */
    protected function arrayWhereComparison($name, $array, &$data)
    {
        $string = "";
        $sep = "";
        if (is_array($array)) {
            foreach ($this->conditionals as $cond => $op) {
                if (isset($array[$cond])) {
                    $string .= $sep."`".$name."` $op ? ";
                    $data[] = $array[$cond]; 
                    $sep = " AND ";
                }
            }
        } else {
            $string = "`".$name."` = ? ";
            $data[] = $array;
        }
        return "(".$string.")";
    }
    /**
     * This converts a single set of Comparison operators
     * 
     * @param string $gate  The logic gate to use
     * @param array  $array The array to convert
     * @param array  &$data The data array to use
     *
     * @return null
     */
    protected function arrayWhereLogic($gate, $array, &$data)
    {
        $string = "";
        $sep = "";
        foreach ((array)$array as $bit) {
            foreach ((array)$bit as $name => $value) {
                $string .= $sep.$this->arrayWhereDoStuff($name, $value, $data);
                $sep = $this->gates[$gate];
            }
        }
        return "(".$string.")";
    }
    /**
    * Updates a row in the database.
    *
    * @param array  $data      The data to update
    * @param string $where     Where clause
    * @param array  $whereData Data for query
    * @param array  $columns   The columns to select
    *
    * @return mixed
    */
    public function update(
        $data,
        $where = "",
        $whereData = array(),
        $columns = array()
    ) {
        // If there already is a query don't build another one.
        if (empty($this->query)) {
            $this->reset();
            $this->dataColumns($columns);
            $values = "`".implode("` = ?, `", $this->columns)."` = ?";
            $this->query  = " UPDATE ".$this->table()." SET ".$values;
            if (!empty($where)) {
                $this->where($where, $whereData);
            } else if (is_array($where)) {
                $this->arrayWhere($where);
            } else {
                $this->idWhere();
            }
        }
        if (!empty($data)) {
            // Insert it
            return $this->executeData($data);
        }
        return true;
    }
    /**
    * Updates a row in the database.
    *
    * @param array  $data      The data to update
    * @param string $where     Where clause
    * @param array  $whereData Data for query
    * @param array  $columns   The columns to select
    *
    * @return mixed
    */
    public function updateOnce(
        $data,
        $where = "",
        $whereData = array(),
        $columns = array()
    ) {
        $this->reset();
        $ret = $this->update($data, $where, $whereData, $columns);
        $this->reset();
        return $ret;

    }
    /**
    * Gets all rows from the database
    *
    * @param mixed $where     Where clause or array if using ID where
    * @param array $whereData Data for query
    * @param array $columns   The columns to select
    *
    * @return null
    */
    public function selectWhere(
        $where,
        $whereData = array(),
        $columns = array()
    ) {
        $this->reset();
        $this->dataColumns($columns);
        $this->query  = "SELECT";
        $this->query .= "`".implode("`, `", $this->columns)."`";
        $this->query .= " FROM ".$this->table();
        if (is_array($where)) {
            $this->arrayWhere($where);
            $this->orderby();
            $this->limit();
            $ret = $this->executeData();
        } else {
            $this->where($where, $whereData);
            $this->orderby();
            $this->limit();
            $ret = $this->executeData();
        }
        return $ret;
    }

    /**
    * Gets all rows from the database
    *
    * @param mixed $where     Where clause or array if using ID where
    * @param array $whereData Data for query
    * @param array $column    The column to count
    *
    * @return integer
    */
    public function countWhere(
        $where,
        $whereData = array(),
        $column = ""
    ) {
        $this->reset();
        if (empty($column)) {
            $column = $this->myTable->sqlId;
        }
        $this->query  = "SELECT COUNT($column) AS count";
        $this->query .= " FROM ".$this->table();
        if (is_array($where)) {
            // This selects by id.  If we are here, we are only getting one record
            // as it searches for unique values.  That is why we are not adding
            // orderby or limit.  Both are meaningless when getting only one value.
            $this->idWhere($where);
            $ret = $this->execute($this->prepareIdData($where));
        } else {
            $this->where($where, $whereData);
            $ret = $this->executeData();
        }
        if ($ret) {
            $res = $this->fetchAll(\PDO::FETCH_ASSOC);
            return (int)$res[0]["count"];
        }
        return false;
    }



    /**
    * Gets all rows from the database
    *
    * @param string $where     The where string to use
    * @param array  $whereData The data to use for the string
    *
    * @return array
    */
    protected function where($where, $whereData=array())
    {
        if (empty($where)) {
            return;
        }
        $this->query .= " WHERE ".$where;
        $this->whereData = $whereData;
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
    * @param string $where The where data to use
    *
    * This only works with integer ID columns!
    *
    * @return int
    */
    public function getNextID($where = "")
    {
        $query = "SELECT MAX(".$this->myTable->sqlId.") as id "
                ." from ".$this->table();
        if (is_string($where) && (strlen($where) > 0)) {
            $query .= " WHERE ".$where;
        }
        $ret   = $this->query($query);
        $newID = (isset($ret[0]['id'])) ? (int) $ret[0]['id'] : 0 ;
        return $newID + 1;
    }

    /**
    * Gets one less that the smallest ID to use from the table.
    *
    * @param string $where The where data to use
    *
    * This only works with integer ID columns!
    *
    * @return int
    */
    public function getPrevID($where = "")
    {
        $query = "SELECT MIN(".$this->myTable->sqlId.") as id "
                ." from ".$this->table();
        if (is_string($where) && (strlen($where) > 0)) {
            $query .= " WHERE ".$where;
        }
        $ret   = $this->query($query);
        $newID = ($ret[0]['id'] < 0) ? (int) $ret[0]['id'] : 0 ;
        return $newID - 1;
    }
    /**
    * Removes rows from the database
    *
    * This routine won't remove everything with an empty where clause.  You must
    * feed it a "1" in the where clause to delete everything.
    *
    * @param mixed $where     (string) Where clause
    *                         (array)  An associative indexed array with key or
    *                                  index values in it.
    * @param array $whereData Data for query
    *
    * @return bool True on success, False on failure
    */
    public function deleteWhere($where, $whereData = array())
    {
        if ($where == "") {
            return false;
        }
        $this->reset();
        // This clears out the columns, as we don't want them to add to our
        // data array
        $this->columns = array();
        // Build the query
        $this->query  = "DELETE FROM ".$this->table();
        if (is_array($where)) {
            // This selects by id.  If we are here, we are only getting one record
            // as it searches for unique values.  That is why we are not adding
            // orderby or limit.  Both are meaningless when getting only one value.
            $this->idWhere($where);
            $ret = $this->execute($this->prepareIdData($where));
        } else {
            $this->where($where, $whereData);
            $ret = $this->executeData();
        }
        $this->reset();
        return $ret;
    }
}


?>
