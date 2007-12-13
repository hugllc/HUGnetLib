<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: device.php 532 2007-12-11 02:31:41Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/**
 * Base class for all database work
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.
 *
 * @category   DatabaseCache
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DbBase
{
    /** @var string The database table to use */
    protected $table = "none";
    /** @var string This is the Field name for the key of the record */
    protected $id = "id";

    /**
     *  These are the database fields
     *
     *  This MUST be overwritten by child classes. 
     *
     * @var array
     */
    protected $fields = array();

    /** 
     * This is used only with SQLite and other databases that create local files
     *
     * @var string
     */
    protected $file = "";
    
    /** @var string This is the name of the current driver */
    protected $driver = "";

    /** @var object The database object */
    protected $_db = null;

    /** @var object The database object for the cache */
    protected $_cacheDb = null;

    /** @var object The cache */
    protected $_cache = null;

    /** @var object The cache */
    protected $_doCache = false;

    /**
     * This function sets up the driver object, and the database object.  The
     * database object is taken from the driver object.
     *
     * @param object &$driver This should be an object of class driver
     */
    function __construct(&$db = null, $table = false, $id = false) 
    {
        // Set it here since it needs a call to sys_get_temp_dir
        if (is_string($db)) {
            $this->file = $db;
        } else {
            $this->file = sys_get_temp_dir()."/HUGnetLocal.db";
        }
        
        if (get_class($db) == "PDO") {
            $this->_db = &$db;
        } else {
            // We got the wrong database.  Punt.  ;)
            $this->_db = new PDO("sqlite:".$this->file);    
        }

        if (is_string($table)) $this->table = $table;
        if (is_string($id)) $this->id = $id;

        $this->driver = $this->_db->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        $this->_getColumns();
    }

    /**
     * Constructor
     *
     * @param string $file The database file to use (SQLite)
      */
    function createCache($file = null) 
    {
        // Can't cache a sqlite database server
        if ($this->driver == "sqlite") return;

        if (is_string($file)) $this->file = $file;
        if (!is_string($this->file)) $this->file = ":memory";
        $this->_cacheDb = new PDO("sqlite:".$this->file);
        $class = get_class($this);
        $this->_cache = new $class($this->_cacheDb);
        $this->_doCache = true;
    }



    /**
     * Creates the field array
     *
     * @return none
     */
    private function _getColumns() {
        $this->_getColumnsSQLite();
        $this->_getColumnsMySQL();
    }
    /**
     * Gets columns from a SQLite server
     *
     * @return none
     */
    private function _getColumnsSQLite()
    {
        if ($this->driver != "sqlite") return;
        $columns = $this->query("PRAGMA table_info(".$this->table.")");
        if (!is_array($columns)) return;
        foreach ($columns as $col) {
            $this->fields[$col['name']] = $col['type'];
        }
    }

    /**
     * Gets columns from a mysql server
     *
     * @return none
     */
    private function _getColumnsMySQL() {
        if ($this->driver != "mysql") return;
        $columns = $this->query("SHOW COLUMNS FROM ".$this->table);
        if (!is_array($columns)) return;
        foreach ($columns as $col) {
            $this->fields[$col['Field']] = $col['Type'];
        }
        
    }
    
    /**
     * Creates the database table.
     *
     * @return bool
     */
    public function createTable() 
    {
        $query = "CREATE TABLE `".$this->table."` (
              `id` int(11) NOT null,
              `name` varchar(16) NOT null default '',
              `value` text NOT null,
              PRIMARY KEY  (`id`)
            );";

        $ret = $this->query($query);
        $this->_getColumns();
        return $ret;
    }

    /**
     * Adds each element in the array as a row in the database
     *
     * @param array $InfoArray An array of database rows to add
     *
     * @return none
      */
    function addArray($InfoArray) 
    {
        if (!is_array($InfoArray)) return;

        foreach ($InfoArray as $info) {
            $this->add($info);
        }
    }
    
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
    function add($info, $replace = FALSE) 
    {    
        $div    = "";
        $fields = "";
        $values = array();
        $v      = "";
        foreach ($this->fields as $key => $val) {
            if (!isset($info[$key])) continue;
            $fields .= $div.$key;
            $v.= $div." ? ";
            $values[] = $info[$key];
            $div = ", ";
        }

        if ($replace) {
            $query = "REPLACE";
        } else {
            $query = "INSERT";
        }
        $query .= " INTO '".$this->table."' (".$fields.") VALUES (".$v.")";
        return $this->query($query, $values);
    }

    /**
     * Adds an row to the database
     *
     * @param array $info The row in array form
     *
     * @return bool Always False 
      */
    function replace($info) 
    {    
        return $this->add($info, true);
    }

    /**
     * Updates a row in the database.
     *
     * This function MUST be overwritten by child classes
     *
     * @param array $info The row in array form.
     *
     * @return mixed 
      */
    function update($info) 
    {    
        if (!isset($info[$this->id])) return false;
        
        $div    = "";
        $fields = "";
        $values = array();
        $v      = "";
        foreach ($this->fields as $key => $val) {
            if (!isset($info[$key])) continue;
            if ($key == $this->id) continue;
            $fields  .= $div.$key." = ? ";
            $values[] = $info[$key];
            $div      = ", ";
        }

        $values[] = $info[$this->id];
        $query    = " UPDATE ".$this->table." SET ".$fields." WHERE ".$this->id."= ? ";
        return $this->query($query, $values);
    }


    /**
     * Gets all rows from the database
     *
     * @return array
      */
    function getAll() 
    {
        return $this->getWhere("1");
    }

    /**
     * Gets all rows from the database
     *
     * @return array
      */
    function get($id) 
    {
        $query = " SELECT * FROM '".$this->table."' WHERE ".$this->id."= ? ;";
        return $this->query($query, array($id));
    }

    /**
     * Gets all rows from the database
     *
     * @return array
      */
    function getWhere($where, $data = array()) 
    {
        $query = " SELECT * FROM '".$this->table."' WHERE ".$where;
        return $this->query($query, $data);
    }


    /**
     * Sets the error code for the last query
     *
     * @param bool $clear Clears the error
     */
    protected function _errorInfo($clear=false)
    {
        if (!$clear) $err = $this->_db->errorInfo();
        $this->errorState = $err[0];
        $this->error      = $err[1];
        $this->errorMsg   = $err[2];
        
    }
    /**
     * Queries the database
     *
     * @param string $query SQL query to send to the database
     *
     * @return mixed
     */
    function query($query, $data=array()) 
    {

        if (!is_array($data)) return false;
        if (!is_object($this->_db)) return false;
        // Clear out the error
        $this->_errorInfo(true); 
               
        $ret = $this->_db->prepare($query);
        if (is_object($ret)) {
            if ($ret->execute($data)) return $ret->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Set the error
        $this->_errorInfo();
        
        return array();
    }

    /**
     * Removes a row from the database.
     *
     * This function MUST be overwritten by child classes
     *
     * @param array $info The row in array form.
     *
     * @return mixed 
      */
    function remove($id) 
    {
        $query = " DELETE FROM '".$this->table."' WHERE ".$this->id."= ? ;";
        return $this->query($query, array($id));
    }
    
    /**
     * Sets the verbosity
     *
     * @param int $level The verbosity level
     */    
    public function verbose($level=0)
    {
        $this->verbose = (int) $level;
    }
    
}


?>
