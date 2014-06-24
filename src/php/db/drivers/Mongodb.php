<?php
/**
 * Sensor driver for light sensors.
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
 * @subpackage PluginsDatabase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
namespace HUGnet\db\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is for the base class */
require_once dirname(__FILE__)."/../Driver.php";
/** This is for the interface */
require_once dirname(__FILE__)."/../../interfaces/DBDriver.php";
/**
 * This class implements photo sensors.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsDatabase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Mongodb extends \HUGnet\db\Driver implements \HUGnet\interfaces\DBDriver
{
    /** @var object This is where we store our system object */
    protected $system = null;
    /** @var object This is where we store our connection object */
    protected $connect = null;
    
    /** @var object This is where we store our table object */
    protected $myTable = null;
    /** @var object This is where we store our DB object */
    protected $dbo = null;
    /** @var object This is where we store our collection */
    protected $collection = null;
    /** @var object This is where we store our cursor */
    protected $cursor = null;
    /** @var object This is our flag for autoincrementing */
    protected $autoIncrement = false;
    /**
    * Register this database object
    *
    * @param object &$system  The system object
    * @param object &$table   The table object
    * @param object &$connect The connection manager
    */
    protected function __construct(&$system, &$table, &$connect)
    {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a table object",
            !is_object($table)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a connection object",
            !is_object($connect)
        );
        $this->system     = &$system;
        $this->connect    = &$connect;
        $this->myTable    = &$table;
        $this->dbo        = $connect->getDBO($this->myTable->get("group"));
        $this->collection = $this->dbo->selectCollection($this->myTable->sqlTable);
        // This sets our autoincrement flag
        $this->autoIncrement();
    }
    /**
    * Register this database object
    */
    public function __destruct()
    {
        $this->reset();
        unset($this->collection);
        unset($this->dbo);
        unset($this->connect);
        unset($this->system);
        unset($this->cursor);
    }
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
            $connect = \HUGnet\db\Connection::factory($system);
        }
        $obj = new Mongodb($system, $table, $connect);
        return $obj;
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
        $ret = true;
        if (!empty($data) && is_array($data)) {
            $this->dataColumns($columns);
            if ($this->autoIncrement && is_null($data[$this->myTable->sqlId])) {
                $data[$this->myTable->sqlId] = $this->getNextID();
                $this->columns[$this->myTable->sqlId] = $this->myTable->sqlId;
            }
            $data = array_intersect_key($data, $this->columns);
            $options = array();
            if (!$replace) {
                try {
                    $insert = $this->collection->insert($data);
                } catch (\MongoCursorException $e) {
                    return false;
                }
            } else {
                $insert = $this->collection->update(
                    $this->idWhere($data),
                    $data, 
                    array(
                        "upsert"   => true,
                        "multiple" => false,
                        "w" => 1,
                    )
                );
            }
            if (is_array($insert)) {
                $ret = ($insert["ok"] == 1);
            }
        }
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
            $columns = array_keys((array)$this->myTable->sqlColumns);
        }
        $this->columns = array();
        foreach (array_keys((array)$this->myTable->sqlColumns) as $column) {
            if (!is_bool(array_search($column, (array)$columns))) {
                $this->columns[$column] = $column;
            }
        }
        if (empty($this->columns) && !empty($this->myTable->sqlColumns)) {
            $this->dataColumns();
        }
    }
    /**
    * This gets either a key or a unique index and returns it as a where.
    *
    * This sets $this->idWhere, which is used by prepareData() to add the stuff
    * from here into the data array.  That way multiple records can be updated
    * with the same query.
    *
    * @param array $data The data to use in the where
    *
    * @return array The where array to use
    */
    protected function idWhere($data = array())
    {
        $where = array();
        if (!empty($this->myTable->sqlId)) {
            // Use the table id field
            $this->idWhere[] = $this->myTable->sqlId;
            $where[$this->myTable->sqlId] = $data[$this->myTable->sqlId];
        } else {
            // Check for a unique index to use
            $indexes = &$this->myTable->sqlIndexes;
            foreach ((array)$indexes as $ind) {
                if (!$ind["Unique"]) {
                    continue;
                }
                $nWhere = array();
                foreach ($ind["Columns"] as $col) {
                    if (stripos($col, ",") === false) {
                        $nWhere[$col] = $data[$col];
                    }
                }
                if (count($nWhere) > 0) {
                    $where['$and'][] = $nWhere;
                }
            }
        }
        return $where;
    }
    /**
    * Returns the column list required to make autoincrement happen.
    *
    * @return array
    */
    public function autoIncrement()
    {
        $cols = array();
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
    public function insertOnce(
        $data = array(), $columns = array(), $replace = false
    ) {
        return $this->insert($data, $columns, $replace);
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
        return $this->insertOnce($data, $columns, true);
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
        $ret = true;
        if (!empty($data)) {
            $this->dataColumns($columns);
            $insert = $this->collection->update(
                $this->idWhere($data),
                $data, 
                array(
                    "upsert"   => false,
                    "multiple" => false,
                    "w" => 1,
                )
            );
        }
        if (is_array($insert)) {
            $ret = ($insert["ok"] == 1);
        }
        return $ret;
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
        return $this->update($data, $where, $whereData, $columns);
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
        $show = array();
        $show["_id"] = 0;
        foreach ((array)$columns as $col) {
            if (!empty($col)) {
                $show[$col] = 1;
            }
        }
        $where = (is_array($where)) ? $where : array();
        $this->cursor = $this->collection->find((array)$where, $show);
        $this->limit();
        $this->orderby();
        return $this->cursor->hasNext();
    }
    /**
    * Gets all rows from the database
    *
    * @return array
    */
    protected function orderby()
    {
        if (empty($this->myTable->sqlOrderBy) && is_object($this->cursor)) {
            return;
        }
        $sort = array();
        $orderby = explode(",", $this->myTable->sqlOrderBy);
        foreach ($orderby as $order) {
            $order = trim($order);
            $ord = explode(" ", $order);
            if (!empty($ord[0])) {
                $dir = substr(trim(strtolower($ord[1])), 0, 1);
                $sort[trim($ord[0])] = ($dir == "d") ? -1 : 1;
            }
        }
        $this->cursor->sort($sort);
    }

    /**
    * Return the ORDER BY clause
    * 
    * @param bool $start Not used here.
    *
    * @return string
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function limit($start = true)
    {
        if (empty($this->myTable->sqlLimit) && is_object($this->cursor)) {
            return;
        }
        if ((int)$this->myTable->sqlStart > 0) {
            $this->cursor->skip((int)$this->myTable->sqlStart);
        }
        $this->cursor->limit((int)$this->myTable->sqlLimit);
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
        if ($this->selectWhere($where, $whereData, $column)) {
            return $this->cursor->count();
        }
        return false;
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
    public function getNextID($where = array())
    {
        $rid = $this->myTable->sqlId;
        $where["_id"] = array('$ne' => $rid);
        $rec = $this->collection->find((array)$where)->sort(array($rid => -1));
        $rec = $rec->getNext();
        return $rec[$rid] + 1;
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
    public function getPrevID($where = array())
    {
        $rid = $this->myTable->sqlId;
        $where["_id"] = array('$ne' => $rid);
        $rec = $this->collection->find((array)$where)->sort(array($rid => 1));
        $rec = $rec->getNext();
        return $rec[$rid] - 1;
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
        if (empty($where)) {
            $ret = false;
        } else if ($where == 1) {
            $ret = $this->collection->remove();
        } else {
            $ret = $this->collection->remove($where);
        }
        if (is_array($ret)) {
            $ret = ($ret["ok"] == 1);
        }
        return $ret;
    }
    /**
    *  Adds a field to the devices table for cache information
    *
    * @param array $column @See columnDef for format
    *
    * @return null
    */
    public function addColumn($column)
    {
    }
    /**
    *  Adds a field to the devices table for cache information
    *
    * @param array $columns array of $column entries @See columnDef for
    *                       $column format
    *
    * @return null
    */
    public function createTable($columns = null)
    {
        if (!$this->tableExists()) {
            $this->dbo->createCollection($this->myTable->sqlTable);
            foreach ((array)$this->myTable->sqlColumns as $col) {
                if ($col["AutoIncrement"]) {
                    $this->autoIncrement = true;
                    $this->addIndex(
                        array(
                            "Unique" => true,
                            "Columns" => array($col["Name"])
                        )
                    );
                }
            }
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
        $keys    = array();
        $options = array();
        // Build the query
        if ($index["Unique"]) {
            $options["unique"] = true;
        }
        // SQLite requires the index name to be unique
        foreach ((array)$index["Columns"] as $col) {
            $c = explode(",", $col);
            $keys[$c[0]] = 1;
        }
        $this->collection->ensureIndex($keys, $options);
    }
    /**
    * Checks to see if a table exists
    *
    * @return null
    */
    public function tableExists()
    {
        $names = $this->dbo->getCollectionNames();
        return in_array($this->myTable->sqlTable, $names);
    }
    /**
    * Gets all rows from the database
    *
    * @param int $style The \PDO ftech style.  See
    *           {@link http://us.php.net/manual/en/pdostatement.fetch.php \PDO Fetch}
    *           for more information
    *
    * @return array of objects of the same class as myTable
    */
    public function &fetchAll($style = \PDO::FETCH_CLASS)
    {
        $ret = array();
        while (is_object($this->cursor) && $this->cursor->hasNext()) {
            $res = $this->cursor->getNext();
            if (is_array($res)) {
                if ($style == \PDO::FETCH_CLASS) {
                    $ret[] = $this->myTable->duplicate($res);
                } else {
                    $ret[] = $res;
                }
            }
        };
        return $ret;
    }
    /**
    * Gets one row from the database and puts it into $this->myTable
    *
    * @return true on success, false on failure
    */
    public function fetchInto()
    {
        if (!is_object($this->cursor) || !$this->cursor->hasNext()) {
            return false;
        }
        $res = $this->cursor->getNext();
        $ret = false;
        if (is_array($res)) {
            $this->myTable->fromArray($res);
            $ret = true;
        }
        return $ret;
    }
    /**
    * Resets all internal variables to be ready for the next query
    *
    * @return null
    */
    public function reset()
    {
        $this->cursor = null;
    }
    /**
     * Columns are irrelevant in Mongodb
     *
     * @return null
     */
    public function columns()
    {
        return array();
    }
    /**
     * Checks the database table, repairs and optimizes it
     *
     * @param bool $force Force the repair
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function check($force = false)
    {
        return true;
    }
    /**
     * Locks the table
     *
     * @return mixed
     */
    public function lock()
    {
        return true;
    }
    /**
     * Unlocks the table
     *
     * @return mixed
     */
    public function unlock()
    {
        return true;
    }
    /**
     * Get the names of all the tables in the current database
     *
     * @return array of table names
     */
    public function tables()
    {
        $list = $this->dbo->listCollections();
        $ret  = array();
        foreach ($list as $collection) {
            $name = $collection->getName();
            $ret[$name] = $name;
        }
        return $ret;
    }
    /**
    * Times out long running select queriess
    * 
    * @param int $timeout The timeout period to use
    *
    * @return int Count of the number of processes killed
    */
    public function selectTimeout($timeout = 120)
    {
        return 0;
    }
    
}

?>
