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

    /** @var object This is where we store our PDO */
    protected $pdo = null;
    /** @var object This is where we store our table object */
    protected $myTable = null;
    /** @var string This is where we store the query */
    protected $query = "";
    /** @var string The name of this driver */
    protected $driver = "";
    /** @var bool Does this driver support auto_increment? */
    protected $whereData = array();
    /** @var bool This is where we store the ID columns we are currently using in
                   our where cause */
    protected $idWhere = array();

    /**
    * Register this database object
    *
    * @param object &$table The table object
    * @param PDO    &$pdo   The PDO object
    */
    public function __construct(&$table, PDO &$pdo)
    {
        if (!is_object($table)) {
            $this->throwException("No table given", -2);
            // @codeCoverageIgnoreStart
            // It thinks this line won't run.  The above function never returns.
        }
        // @codeCoverageIgnoreEnd

        $this->myTable = &$table;
        $this->pdo     = &$pdo;
        $this->myConfig = &ConfigContainer::singleton();
        $this->dataColumns();
        if ($this->myConfig->verbose > 5) {
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } else if ($this->myConfig->verbose > 0) {
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        } else {
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        }
        $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
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
    * @param object &$table The table to attach myself to
    * @param PDO    &$pdo   The database object
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
        $this->reset();
        $this->query  = "ALTER TABLE ".$this->table()." ADD ";
        $this->columnDef($column);
        $this->prepare();
        $this->executeData();
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
        $this->reset();
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
        $this->executeData();
    }
    /**
    *  Adds a field to the devices table for cache information
    *
    *  The column parameter is defined as follows:
    *  $column["Name"] => string The name of the column
    *  $column["Type"] => string The type of the column
    *  $column["Default"] => mixed The default value for the column
    *  $column["Null"] => bool true if null is allowed, false otherwise
    *  $column["AutoIncrement"] => bool true if the column is auto_increment
    *  $column["Collate"] => string colation if the table is text or char
    *  $column["Primary"] => bool If we are a primary Key.
    *  $column["Unique"] => boll If we are a unique column.
    *
    * @param array $column The array of column information
    *
    * @return null
    */
    protected function columnDef($column)
    {
        $this->query .= "`".$column["Name"]."` ".strtoupper($column["Type"]);
        if ($column["AutoIncrement"]) {
            $this->query .= " PRIMARY KEY AUTOINCREMENT";
        } else if ($column["Primary"]) {
            $this->query .= " PRIMARY KEY";
        } else if ($column["Unique"]) {
            $this->query .= " UNIQUE";
        }
        if (!empty($column["Collate"])) {
            $this->query .= " COLLATE ".strtoupper($column["Collate"]);
        }
        if ($column["Null"] == true) {
            $this->query .= " NULL";
        } else {
            $this->query .= " NOT NULL";
        }
        if (!is_null($column["Default"])) {
            $this->query .= " DEFAULT ".$this->pdo->quote($column["Default"]);
        }
    }
    /**
    *  Adds a field to the devices table for cache information
    *
    *  The $index parameter should be defined as follows:
    *  $index["Name"] => string The name of the index
    *  $index["Unique"] => bool Create a Unique index
    *  $index["Columns"] => array Array of column names
    *
    * @param array $index Index array defined above.
    *
    * @return null
    */
    public function addIndex($index)
    {
        $this->reset();
        // Build the query
        $this->query  = "CREATE";
        if ($index["Unique"]) {
            $this->query .= " UNIQUE";
        }
        $this->query .= " INDEX IF NOT EXISTS `".$index["Name"]."` ON ";
        $this->query .= $this->table();
        $this->query .= " (`".implode((array)$index["Columns"], "`, `")."`)";
        $this->prepare();
        $this->executeData();
    }

    /**
    * Creates the field array
    *
    * @return null
    */
    abstract public function columns();

    /**
    * Gets an attribute from the PDO object
    *
    * @param string $attrib The attribute to get.
    *
    * @return mixed
    */
    public function getAttribute($attrib)
    {
        if (is_object($this->pdo)) {
            $ret = $this->pdo->getAttribute($attrib);
        }
        return $ret;
    }

    /**
    * Returns an array made for the execute query
    *
    * @param array $data The data to prepare
    *
    * @return array
    */
    public function prepareData($data)
    {
        $ret = array();
        if (!empty($data)) {
            foreach ($this->columns as $k) {
                $ret[] = $data[$k];
            }
        }
        if (!empty($this->idWhere)) {
            $this->whereData = array();
            foreach ($this->idWhere as $col) {
                $this->whereData[] = $data[$col];
            }
        }
        $ret = array_merge($ret, (array)$this->whereData);
        return $ret;
    }
    /**
    * Sets the columns to use
    *
    * @param array $columns An array of columns to use
    *
    * @return null
    */
    protected function dataColumns($columns = array())
    {
        if (empty($columns)) {
            $this->columns = array_keys((array)$this->myTable->sqlColumns);
        } else {
            $this->columns = array();
            foreach (array_keys((array)$this->myTable->sqlColumns) as $column) {
                if (!is_bool(array_search($column, (array)$columns))) {
                    $this->columns[$column] = $column;
                }
            }
            if (empty($this->columns)) {
                $this->dataColumns();
            }
        }
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
                $where .= " (`".implode("` = ? AND `", $ind["Columns"])."` = ?)";
                $this->idWhere = $ind["Columns"];
            }
        }
        $this->where($where);
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
    * @param string $where     Where clause
    * @param array  $whereData Data for query
    * @param array  $columns   The columns to select
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
        $this->where($where, $whereData);
        $this->orderby();
        $this->limit();
        $ret = $this->executeData();
        return $ret;
    }

    /**
    * Gets all rows from the database
    *
    * @param int $style The PDO ftech style.  See
    *           {@link http://us.php.net/manual/en/pdostatement.fetch.php PDO Fetch}
    *           for more information
    *
    * @return null
    */
    public function fetchAll($style = PDO::FETCH_CLASS)
    {
        if (!is_object($this->pdoStatement)) {
            return array();
        }
        if ($style == PDO::FETCH_CLASS) {
            $this->pdoStatement->setFetchMode(
                PDO::FETCH_CLASS,
                get_class($this->myTable)
            );
        } else {
            $this->pdoStatement->setFetchMode($style);
        }
        $ret = $this->pdoStatement->fetchAll();
        $this->reset();
        return $ret;
    }

    /**
    * Gets one row from the database and puts it into $this->myTable
    *
    * @return null
    */
    public function fetchInto()
    {
        $this->pdoStatement->setFetchMode(
            PDO::FETCH_INTO,
            $this->myTable
        );
        $ret = $this->pdoStatement->fetch();
        $this->reset();
        return $ret;
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
        $query = "SELECT MAX(".$this->myTable->sqlId.") as id "
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
        $query = "SELECT MIN(".$this->myTable->sqlId.") as id "
                ." from ".$this->table();
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
    * @param string $where     Where clause
    * @param array  $whereData Data for query
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
        $this->where($where, $whereData);
        $ret = $this->executeData();
        $this->reset();
        return $ret;
    }
    /**
    * Prepares a query to be put into the database
    *
    * @param string $query The query to use.  If empty $this->query is used
    *
    * @return mixed
    */
    public function prepare($query = null)
    {
        if (empty($this->query)) {
            $this->query = $query;
        }
        if (empty($this->query)) {
            return false;
        }
        $this->pdoStatement = $this->pdo->prepare($this->query);
        return (bool) $this->pdoStatement;
    }
    /**
    * Executes a query.
    *
    * @param array $data Data to use for the query.
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
        $ret = $this->pdoStatement->execute($data);
        //$this->pdoStatement->debugDumpParams();
        return $ret;
    }
    /**
    * This function prepares the data before calling execute.
    *
    * It is for internal purposes only.  Call 'execute' instead
    *
    * @param array $data Data to use for the query.  Associate Array
    *
    * @return mixed
    */
    protected function executeData($data = array())
    {
        $data = $this->prepareData($data);
        return $this->execute($data);
    }
    /**
    * Resets all internal variables to be ready for the next query
    *
    * @return null
    */
    public function reset()
    {
        if (is_object($this->pdoStatement)) {
            // close the cursor
            $this->pdoStatement->closeCursor();
            // Remove the statuemt
            $this->pdoStatement = null;
        }
        $this->query = "";
        $this->whereData = array();
        $this->idWhere = array();
    }
    /**
    * .Queries the database
    *
    * This function is meant for very small sql statements, like those from
    * nextID and prevID.  It is also meant to be used where it needs to not mess
    * up a query in progress.
    *
    * @param array $query The query string
    * @param array $data  Data to use for the query
    *
    * @return array
    */
    public function query($query = "", $data = array())
    {
        if (!is_object($this->pdo)) {
            return false;
        }
        $pdo = $this->pdo->prepare($query);
        if (is_object($pdo)) {
            $pdo->execute($data);
            $res = $pdo->fetchAll(PDO::FETCH_ASSOC);
            $pdo->closeCursor();
        }
        return $res;
    }
}


?>
