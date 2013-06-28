<?php
/**
 * Abstract class for building SQL queries
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
namespace HUGnet\interfaces;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/**
 * Feature added DB class
 *
 * This class adds many features to the base database class.  Different methods
 * of input and output.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.11.o
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.11.0
 */
interface DBTable
{
    /**
    * This function gets a record with the given key
    *
    * @return bool True on success, False on failure
    */
    public function refresh();
    /**
    * Creates the object from a string or array
    *
    * @param mixed $data This is whatever you want to give the class
    *
    * @return null
    */
    public function fromArray($data);
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param string $string The CSV string to import
    *
    * @return null
    */
    public function fromCSV($string);
    /**
    * This function gets a record with the given key
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return bool True on success, False on failure
    */
    public function selectOneInto($where, $data = array());
    /**
    * This remove everything from the array but keys that are columns
    *
    * @param array $array The array to sanitize
    *
    * @return array
    */
    public function sanitizeWhere($array);
    /**
    * This function creates other tables that are identical to this one, except
    * for the data given.
    *
    * @param mixed $data This is an array or string to create the object from
    *
    * @return object A reference to a table object
    */
    public function &duplicate($data);
    /**
    * Clears out the data
    *
    * @return null
    */
    public function clearData();
    /**
    * Returns an array with only the values the database cares about
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toDB($default = true);

    /**
    * This function gets a record with the given key
    *
    * @param mixed $key This is either an array or a straight value
    *
    * @return bool True on success, False on failure
    */
    public function getRow($key);
    /**
    * This function updates the record currently in this table
    *
    * @param array $columns The columns to update, defaults to all
    *
    * @return bool True on success, False on failure
    */
    public function updateRow($columns = array());
    /**
    * This function updates the record currently in this table
    *
    * @param bool $replace Replace any records found that collide with this one.
    *
    * @return bool True on success, False on failure
    */
    public function insert($replace = false);
    /**
    * This function updates the record currently in this table
    *
    * @return bool True on success, False on failure
    */
    public function insertEnd();
    /**
    * This function updates the record currently in this table
    *
    * @param bool $replace Replace any records found that collide with this one.
    *
    * @return bool True on success, False on failure
    */
    public function insertRow($replace = false);

    /**
    * This function updates the record currently in this table
    *
    * @return bool True on success, False on failure
    */
    public function deleteRow();

    /**
    * This function updates the record currently in this table
    *
    * @return bool True on success, False on failure
    */
    public function create();
    /**
    * This function gets a record with the given key
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return array Array of objects
    */
    public function &select($where, $data = array(), $style = \PDO::FETCH_CLASS);
    /**
    * Returns the number of records this query would return
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return false on failure, int on success
    */
    public function count($where, $data = array());
    /**
    * This function gets a record with the given key
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return array Array of objects
    */
    public function selectIDs($where, $data = array());
    /**
    * This function gets a record with the given key
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return bool True on success, False on failure
    */
    public function selectInto($where, $data = array());
    /**
    * This puts the next result into the object
    *
    * @return bool True on success, False on failure
    */
    public function nextInto();
}


?>
