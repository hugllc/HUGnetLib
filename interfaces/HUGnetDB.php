<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @subpackage Interfaces
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/**
 * This is the interface for the HUGnet database files
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Interfaces
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
interface HUGnetDBInterface
{
    /**
    * Creates a database object
    *
    * @param mixed $config The configuration to use
    *
    * @return object PDO object
    */
    static public function &createPDO($config = array());
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
    public function addField($name, $type="TEXT", $default=null, $null=false);
    /**
    * Constructor
    *
    * @param string $file The database file to use (SQLite)
    *
    * @return bool
    */
    public function createCache($file = null);
    /**
    * Creates the database table.
    *
    * Must be defined in child classes
    *
    * @return bool
    */
    public function createTable();
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
    public function addArray($infoArray, $replace = false);
    /**
    * Adds an row to the database
    *
    * @param array $info    The row in array form
    * @param bool  $replace If true it replaces the "INSERT"
    *                       keyword with "REPLACE".  Not all
    *                       databases support "REPLACE".
    *
    * @return bool
    */
    public function add($info, $replace = false);
    /**
    * Adds an row to the database
    *
    * @param array $info The row in array form
    *
    * @return bool Always False
    */
    public function replace($info);
    /**
    * Updates a row in the database.
    *
    * This function MUST be overwritten by child classes
    *
    * @param array $info The row in array form.
    *
    * @return mixed
    */
    public function update($info);
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
    public function updateWhere($info, $where, $data = array());
    /**
    * Gets all rows from the database
    *
    * @param int    $limit   The maximum number of rows to return (0 to return all)
    * @param int    $start   The row offset to start returning records at
    * @param string $orderby The orderby Clause.  Must include "ORDER BY"
    *
    * @return array
    */
    public function getAll($limit = 0, $start = 0, $orderby="");
    /**
    * Gets all rows from the database
    *
    * @param int $sqlId The id of the row to get.
    *
    * @return array
    */
    public function get($sqlId);
    /**
    * Gets all rows from the database
    *
    * @param string $where   Where clause
    * @param array  $data    Data for query
    * @param int    $limit   The maximum number of rows to return (0 to return all)
    * @param int    $start   The row offset to start returning records at
    * @param string $orderby The orderby Clause.  Must include "ORDER BY"
    *
    * @return array
    */
    public function getWhere(
        $where,
        $data = array(),
        $limit = 0,
        $start = 0,
        $orderby = ""
    );
    /**
    * Returns the first row the where statement finds
    *
    * @param string $where a valid SQL where statement
    * @param array  $data  Data for query
    *
    * @return mixed
    */
    function getOneWhere($where, $data = array());
    /**
    * Returns the first row in the database
    *
    * @return mixed
    */
    function getOne();
    /**
    * Gets the next ID to use from the table.
    *
    * This only works with integer ID columns!
    *
    * @param int $sqlId The cureent ID to use
    *
    * @return int
    */
    function getNextID($sqlId = null);
    /**
    * Gets one less that the smallest ID to use from the table.
    *
    * This only works with integer ID columns!
    *
    * @param int $sqlId The cureent ID to use
    *
    * @return int
    */
    function getPrevID($sqlId = null);
    /**
    * Fixes variable so they are the correct type
    *
    * @param mixed  $val  The value to fix
    * @param string $type The type to use.  This is the SQL column type
    *
    * @return mixed
    */
    public function fixType($val, $type);
    /**
    * Print Errors
    *
    * @return null
    */
    public function printError();
    /**
    * Queries the database
    *
    * @param string $query  SQL query to send to the database
    * @param array  $data   The data to use to replace '?' in the query
    * @param bool   $getRet Whether to expect data back from the query
    *
    * @return mixed
    */
    public function query($query, $data=array(), $getRet=true);
    /**
    * Removes a row from the database.
    *
    * @param mixed $sqlId The id value of the row to delete
    *
    * @return mixed
    */
    public function remove($sqlId);
    /**
    * Removes a row from the database.
    *
    * @param string $where Where clause
    * @param array  $data  Data for query
    *
    * @return mixed
    */
    public function removeWhere($where, $data=array());
    /**
    * Sets the verbosity
    *
    * @param int $level The verbosity level
    *
    * @return null
    */
    public function verbose($level=0);
    /**
    * Tells us if the database is still connected
    *
    * @return bool
    */
    public function isConnected();
    /**
    * Takes a date and turns it into a SQL date
    *
    * @param mixed $date The date in just about any format
    *
    * @return string SQL Date
    */
    public function sqlDate($date);
    /**
    * Gets an instance of the HUGnet Driver
    *
    * @param string $class  The class to create
    * @param array  $config The configuration to use
    *
    * @return object A reference to a driver object
    */
    function &getInstance($class = "HUGnetDB", $config = null);
    /**
    * Gets and sets the configuration
    *
    * @return array The configuration
    */
    public function getConfig();
    /**
    * Gets and sets the configuration
    *
    * @param array $config    The configuration to set.  If left out the
    *                         configuration is retrieved.
    * @param bool  $overwrite Overwrite a config if there is one already
    *
    * @return array The configuration
    */
    public function setConfig($config=array(), $overwrite = true);
}


?>
