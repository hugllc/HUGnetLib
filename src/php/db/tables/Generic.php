<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
namespace HUGnet\db\tables;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our system interface */
require_once dirname(__FILE__)."/../../interfaces/DBTable.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Generic extends \HUGnet\db\Table
    implements \HUGnet\interfaces\DBTable
{
    /** @var string This is the table we should use */
    public $sqlTable = "table";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = null;
    /**
    * @var array This is the definition of the columns
    *
    * This should consist of the following structure:
    * array(
    *   "name" => array(
    *       "Name"          => string The name of the column
    *       "Type"          => string The type of the column
    *       "Default"       => mixed  The default value for the column
    *       "Null"          => bool   true if null is allowed, false otherwise
    *       "AutoIncrement" => bool   true if the column is auto_increment
    *       "CharSet"       => string the character set if the column is text or char
    *       "Collate"       => string colation if the table is text or char
    *       "Primary"       => bool   If we are a primary Key.
    *       "Unique"        => bool   If we are a unique column.
    *   ),
    *   "name2" => array(
    *   .
    *   .
    *   .
    * );
    *
    * Not all fields have to be filled in.  Name and Type are the only required
    * fields.  The index of the base array should be the same as the "Name" field.
    */
    public $sqlColumns = array(
        "id" => array(
            "Name" => "id",
            "Type" => "int",
        ),
    );
    /**
    * @var array This is the definition of the indexes
    *
    *   array(
    *       "Name" => array (
    *           "Name"    => string The name of the index
    *           "Unique"  => bool   Create a Unique index
    *           "Columns" => array  Array of column names
    *       ),
    *       "name2" => array(
    *       .
    *       .
    *   ),
    */
    public $sqlIndexes = array(
        "IDIndex" => array(
            "Name" => "IDIndex",
            "Unique" => true,
            "Columns" => array("id"),
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
    );
    /** @var This is the packet */
    public $packet = null;
    /** @var This is the device */
    public $device = null;
    /**
    * This is the constructor
    *
    * @param object &$system  The system object to use
    * @param mixed  $data     This is an array or string to create the object from
    * @param object &$connect The connection manager
    * @param string $table    The table to use
    */
    protected function __construct(
        &$system, $data="", &$connect = null, $table = "table"
    ) {
        parent::__construct($system, $data, $connect);
        $this->forceTable($table);
        $this->fromAny($data);
    }


    /**
    * Sets all of the necessary stuff for the table
    *
    * @param string $table The table to use
    *
    * @return null
    */
    public function forceTable($table)
    {
        if (empty($table)) {
            return;
        }
        $this->sqlTable = $table;
        $this->sqlColumns = $this->dbDriver()->columns();
        $this->setupColsDefault();
    }
    /**
    * Check all database tables
    *
    * @param array $tables The tables to check
    *
    * @return null
    */
    public function checkTables($tables = array())
    {
        if (empty($tables)) {
            $tables = $this->dbDriver()->tables();
        }
        $oldTable = $this->sqlTable;
        foreach ($tables as $table) {
            $ret = $this->checkTable($table);
        }
        $this->forceTable($oldTable);
    }
    /**
    * Check all database tables
    *
    * @param array $table The table to check
    *
    * @return null
    */
    public function checkTable($table)
    {
        $this->forceTable($table);
        $ret = $this->dbDriver()->check();
        return $ret;
    }
    /**
    * Check all database tables
    *
    * @return null
    */
    public function getTables()
    {
        return $this->dbDriver()->tables();
    }
    /**
    * Times out long running select queriess
    *
    * @return null
    */
    public function selectTimeout()
    {
        return $this->dbDriver()->selectTimeout();
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
        return $this->dbDriver()->updateOnce($data, $where, $whereData, $columns);
    }
}
?>
