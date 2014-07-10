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
require_once dirname(__FILE__)."/DriverQuery.php";
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class DriverBase extends DriverQuery
{
    /** @var int This is where we store the limit */
    public $limit = 0;
    /** @var int This is where we store the start */
    public $start = 0;
    /** @var array This is where we store the fields */
    protected $fields = array();
    /** @var array This is where we store the fields in the query */
    protected $dataFields = array();

    /** @var object This is where we store our system object */
    protected $system = null;
    /** @var object This is where we store our connection object */
    protected $connect = null;

    /** @var object This is where we store our \PDO */
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
    * @param object &$system  The system object
    * @param object &$table   The table object
    * @param object &$connect The connection manager
    */
    protected function __construct(&$system, &$table, &$connect)
    {
        parent::__construct($system, $table, $connect);
        $this->dataColumns();
    }
    /**
    * Register this database object
    */
    public function __destruct()
    {
        $this->reset();
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
    * @param array $column @See columnDef for format
    *
    * @return null
    */
    public function modifyColumn($column)
    {
        $this->reset();
        $this->query  = "ALTER TABLE ".$this->table()." MODIFY COLUMN ";
        $this->columnDef($column);
        $this->prepare();
        $this->executeData();
    }
    /**
    *  Removes the column given
    *
    * @param string $column The column to drop.
    *
    * @return null
    */
    public function removeColumn($column)
    {
        $this->reset();
        $this->query  = "ALTER TABLE ".$this->table()." DROP COLUMN `$column`";
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
    public function createTable($columns = null)
    {
        if ((empty($columns) && empty($this->myTable->sqlColumns)
            || ($this->table() == "table"))
            || $this->tableExists()
        ) {
            return false;
        }
        if (empty($columns)) {
            $columns = $this->myTable->sqlColumns;
        }
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
        return $this->executeData();
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
            $this->query .= " DEFAULT ".$this->pdo()->quote($column["Default"]);
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
        // SQLite requires the index name to be unique
        $name = $index["Name"]."_".$this->myTable->sqlTable;
        $this->query .= " INDEX `".$name."` ON ";
        $this->query .= $this->table();
        $this->query .= " (";
        $sep = "";
        foreach ((array)$index["Columns"] as $col) {
            $c = explode(",", $col);
            $this->query .= $sep."`".$c[0]."`";
            /*
            if (!empty($c[1])) {
                $this->query .= " (".$c[1].")";
            }*/
            $sep = ", ";
        }
        $this->query .= ")";
        $this->prepare();
        $this->executeData();
    }
    /**
    *  Removes the named index.
    *
    * @param string $name The name of the index to remove
    *
    * @return null
    */
    public function removeIndex($name)
    {
        $this->reset();
        // Build the query
        $this->query  = "DROP INDEX `".$name."` ON ";
        $this->query .= $this->table();
        $this->prepare();
        $this->executeData();
    }
    /**
    * Checks to see if a table exists
    *
    * @return null
    */
    public function tableExists()
    {
        return (count(@$this->columns()) > 0);
    }
    /**
    * Checks the table in the database against the definition, and returns
    * the differences.
    *
    * @return null
    */
    public function tableDiff()
    {
        return array(
            "column" => $this->_columnDiff(),
            "index"  => $this->_indexDiff(),
        );
    }
    /**
    * Checks the table in the database against the definition, and returns
    * the differences.
    *
    * @return null
    */
    private function _columnDiff()
    {
        $table = $this->columns();
        $ret   = array();
        foreach ((array)$this->myTable->sqlColumns as $name => $col) {
            if (is_array($table[$name])) {
                $diff = array_diff_assoc($col, (array)$table[$name]);
                if (!empty($diff)) {
                    $ret[$name] = array(
                        "type" => "update",
                        "diff" => $diff,
                    );
                }
                unset($table[$name]);
            } else {
                $ret[$name] = array(
                    "type" => "add",
                    "diff" => $col,
                );
            }
        }
        foreach ($table as $col) {
            $ret[$col["Name"]] = array(
                "type" => "remove",
                "diff" => $col,
            );
        }
        return $ret;
    }
    /**
    * Checks the table in the database against the definition, and returns
    * the differences.
    *
    * @return null
    */
    private function _indexDiff()
    {
        $table = $this->indexes();
        $ret   = array();
        foreach ((array)$this->myTable->sqlIndexes as $name => $ind) {
            $found = false;
            foreach ($table as $tindex) {
                if ($this->_indexSame($ind, (array)$table[$name])) {
                    unset($table[$name]);
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $ret[$name] = array(
                    "type" => "add",
                    "diff" => $ind,
                );
            }
        }
        foreach ($table as $ind) {
            $ret[$ind["Name"]] = array(
                "type" => "remove",
                "diff" => $ind,
            );
        }
        return $ret;
    }
    /**
    * Checks the table in the database against the definition, and returns
    * the differences.
    *
    * @return null
    */
    private function _indexSame($index1, $index2)
    {
        if ($index1["Unique"] != $index2["Unique"]) {
            return false;
        }
        $coldiff = array_diff_assoc(
            (array)$index1["Columns"], (array)$index2["Columns"]
        );
        if ($coldiff !== array()) {
            return false;
        }
        return true;
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
        $this->pdoStatement = $this->pdo()->prepare($this->query);
        if ($this->pdoStatement === false) {
            $this->errorHandler(
                $this->pdo()->errorInfo(),
                __METHOD__,
                1 //\ErrorTable::SEVERITY_WARNING
            );
        }
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
        if (!is_object($this->pdoStatement) && !$this->prepare()) {
            return false;
        }
        $this->system->out(
            "Executing (group: ".$this->myTable->get("group")."): "
            .print_r($this->query, true),
            7
        );
        $this->system->out(
            "With Data: ".print_r($data, true),
            7
        );
        $ret = $this->pdoStatement->execute($data);

        $this->system->out(
            "With Result: ".print_r($ret, true)
            . "(".$this->pdoStatement->rowCount()." rows)",
            7
        );
        if (!$ret) {
            $this->errorHandler(
                $this->pdoStatement->errorInfo(),
                __METHOD__,
                1 //\ErrorTable::SEVERITY_WARNING
            );
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
        $this->prepareIdData($data);
        $ret = array_merge($ret, (array)$this->whereData);
        return $ret;
    }
    /**
    * Returns an array made for the execute query
    *
    * @param array $data The data to prepare
    *
    * @return array
    */
    protected function prepareIdData($data)
    {
        if (!empty($this->idWhere)) {
            $this->whereData = array();
            foreach ($this->idWhere as $col) {
                $this->whereData[] = $data[$col];
            }
        }
        return (array)$this->whereData;
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
        if (!is_object($this->pdoStatement)) {
            return array();
        }
        $ret = array();
        if ($style == \PDO::FETCH_CLASS) {
            do {
                $res = $this->pdoStatement->fetch(\PDO::FETCH_ASSOC);
                if (is_array($res)) {
                    $ret[] = $this->myTable->duplicate($res);
                }
            } while ($res !== false);
        } else {
            $this->pdoStatement->setFetchMode($style);
            $ret = $this->pdoStatement->fetchAll();
        }
        $this->reset();
        return $ret;
    }

    /**
    * Gets one row from the database and puts it into $this->myTable
    *
    * @return true on success, false on failure
    */
    public function fetchInto()
    {

        if (!is_object($this->pdoStatement)) {
            return false;
        }
        $res = $this->pdoStatement->fetch(\PDO::FETCH_ASSOC);
        $ret = false;
        if (is_array($res)) {
            $this->myTable->fromArray($res);
            $ret = true;
        }
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
}


?>
