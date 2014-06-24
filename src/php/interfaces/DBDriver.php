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
namespace HUGnet\interfaces;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
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
interface DBDriver
{

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
    public function insert($data = array(), $columns = array(), $replace = false);

    /**
    * Returns the column list required to make autoincrement happen.
    *
    * @return array
    */
    public function autoIncrement();
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
    );

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
    public function replace($data, $columns);
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
    public function replaceOnce($data, $columns);

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
    );
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
    );
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
    );

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
    );

    /**
    * Gets the next ID to use from the table.
    *
    * @param string $where The where data to use
    *
    * This only works with integer ID columns!
    *
    * @return int
    */
    public function getNextID($where = "");

    /**
    * Gets one less that the smallest ID to use from the table.
    *
    * @param string $where The where data to use
    *
    * This only works with integer ID columns!
    *
    * @return int
    */
    public function getPrevID($where = "");
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
    public function deleteWhere($where, $whereData = array());
    /**
    *  Adds a field to the devices table for cache information
    *
    * @param array $column @See columnDef for format
    *
    * @return null
    */
    public function addColumn($column);
    /**
    *  Adds a field to the devices table for cache information
    *
    * @param array $columns array of $column entries @See columnDef for
    *                       $column format
    *
    * @return null
    */
    public function createTable($columns = null);
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
    public function addIndex($index);
    /**
    * Checks to see if a table exists
    *
    * @return null
    */
    public function tableExists();
    /**
    * Gets all rows from the database
    *
    * @param int $style The \PDO ftech style.  See
    *           {@link http://us.php.net/manual/en/pdostatement.fetch.php \PDO Fetch}
    *           for more information
    *
    * @return array of objects of the same class as myTable
    */
    public function &fetchAll($style = \PDO::FETCH_CLASS);
    /**
    * Gets one row from the database and puts it into $this->myTable
    *
    * @return true on success, false on failure
    */
    public function fetchInto();
    /**
    * Resets all internal variables to be ready for the next query
    *
    * @return null
    */
    public function reset();
    /**
    * Times out long running select queriess
    *
    * @param int $timeout The timeout period to use
    *
    * @return int Count of the number of processes killed
    */
    public function selectTimeout($timeout);
    
}


?>
