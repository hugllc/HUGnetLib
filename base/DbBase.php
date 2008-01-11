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
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** The database went away */
define("DBBASE_META_ERROR_SERVER_GONE", 1);
/** The database went away */
define("DBBASE_META_ERROR_SERVER_GONE_MSG", "The server has gone away");
/** The database went away */
define("DBBASE_META_ERROR_DUPLICATE", 2);
/** The database went away */
define("DBBASE_META_ERROR_DUPLICATE_MSG", "Duplicate Entry");
/**
 * Base class for all database work
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.
 *
 * @category   Base
 * @package    HUGnetLib
 * @subpackage Database
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
    /** SQL date format */
    protected $dateFormat = "Y-m-d H:i:s";

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

    /** 
     * This is the file used for the cache
     *
     * @var string
     */
    protected $cacheFile = "";
    
    /** @var string This is the name of the current driver */
    public $driver = "";

    /** @var object The database object */
    protected $_db = null;

    /** @var object The database object for the cache */
    protected $_cacheDb = null;

    /** @var object The cache */
    protected $_cache = null;

    /** @var object The cache */
    protected $_doCache = false;

    /** @var string The dsn we are connected to */
    private $_dsn = "";
    
    /**
     * This function sets up the driver object, and the database object.  The
     * database object is taken from the driver object.
     *
     * @param mixed  &$db     This should be an object of class driver
     * @param string $table   The database table to use
     * @param string $id      The 'id' column to use
     * @param bool   $verbose Whether to be verbose or not
     *
     * @return void
     */
    public function __construct(&$db = null, $table = false, $id = false, $verbose = false) 
    {

        $this->verbose($verbose);
        if (is_string($db) && !empty($db)) {
            if (trim(strtolower($db)) == ":memory:") $db = trim(strtolower($db));
            $this->file = $db;
        } else {
            $this->file = HUGNET_LOCAL_DATABASE;
        }
        if ($this->checkDb($db)) {
            $this->_db = &$db;
        } else {
            $this->_db = self::_createPDO("sqlite:".$this->file);
        }
        if (is_string($table)) $this->table = $table;
        if (is_string($id)) $this->id = $id;

        $this->driver = $this->getAttribute(PDO::ATTR_DRIVER_NAME);

        $this->getColumns();
    }


    /**
     * Creates a database object
     *
     * @param string $dsn  The DSN to use to create the PDO object
     * @param string $user The username
     * @param string $pass THe password
     *
     * @return object PDO object
     */
    static public function &createPDO(&$dsn, $user = null, $pass = null) 
    {
        // The order of these is important!
        if (is_string($dsn)) $dsn = array("dsn" => $dsn, "user" => $user, "password" => $pass);
        if (is_object($dsn) && (get_class($dsn) == "PDO")) return $dsn;
        if (is_object($dsn)) return false;
        if (!is_array($dsn)) return false;
        if (isset($dsn["dsn"])) $dsn = array($dsn);

        // Okday, now try to connect
        foreach ($dsn as $serv) {
            $db = self::_createPDO($serv["dsn"], $serv["user"], $serv["password"]);
            if (is_object($dsn) && (get_class($dsn) == "PDO")) break;
        }
        return $db;
    }
    
    /**
     * Creates a database object
     *
     * @param string $dsn  The DSN to use to create the PDO object
     * @param string $user The username
     * @param string $pass THe password
     *
     * @return object PDO object
     */
    static private function &_createPDO($dsn, $user = null, $pass = null) 
    {
        try {
            $db = new PDO($dsn, $user, $pass);
        } catch (PDOException $e) {
            return false;
        }
        return $db;
    }
    /**
     * Checks to see if the database object is valid
     *
     * If the database object given to it is null it will check $this->_db.
     *
     * $db can not be a 'reference'.
     *
     * @param object $db The database object to check.
     *
     * @return bool
     */
    final protected function checkDb($db=null) 
    {
        if (is_null($db)) $db = &$this->_db; 
        if (!is_object($db)) return false;
        $class = get_class($db);
        if (trim(strtoupper($class)) !== "PDO") return false;
        return true;
    }     


    /**
     * Constructor
     *
     * @param string $file The database file to use (SQLite)
     *
     * @return bool
     */
    public function createCache($file = null) 
    {
        // Can't cache a sqlite database server
        if ($this->driver == "sqlite") {
            $file = substr(trim(strtolower($this->file)), 0, 7);
            if ($file == ":memory") return false;
            $this->cacheFile = ":memory:";
        } else {
            if (is_string($file) && !empty($file)) $this->cacheFile = $file;
        }
        if (!is_string($this->cacheFile) || empty($this->cacheFile)) $this->cacheFile = ":memory:";
        if (empty($this->cacheFile)) $this->cacheFile = ":memory:";

        $this->vprint("Creating a cache at ".$this->cacheFile);
        $class = get_class($this);
        $this->_cache = new $class($this->cacheFile, $this->table, $this->id, $this->verbose);
        $this->_cache->createTable($this->table);
        $this->_doCache = true;
        $this->_cache->createTable();
        return true;
    }



    /**
     * Creates the field array
     *
     * @return void
     */
    protected function getColumns()
    {
        $this->getColumnsSQLite();
        $this->getColumnsMySQL();
        $this->getColumnsOther();
    }
    /**
     * Gets columns from a SQLite server
     *
     * @return void
     */
    protected function getColumnsSQLite()
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
     * @return void
     */
    protected function getColumnsMySQL()
    {
        if ($this->driver != "mysql") return;
        $columns = $this->query("SHOW COLUMNS FROM ".$this->table);
        if (!is_array($columns)) return;
        foreach ($columns as $col) {
            $this->fields[$col['Field']] = $col['Type'];
        }
        
    }

    /**
     * Gets columns from a mysql server
     *
     * @return void
     */
    protected function getColumnsOther()
    {
        if ($this->driver == "mysql") return;        
        if ($this->driver == "sqlite") return;
        throw new Exception("Driver ".$this->driver." is not implemented.", -1);
    }
    
    /**
     * Creates the database table.
     *
     * Must be defined in child classes
     *
     * @return bool
     */
    public function createTable() 
    {
        return false;
    }

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
    public function addArray($infoArray, $replace = false) 
    {
        if (!$this->checkDb()) return 0;
        if (!is_array($infoArray)) return 0;
        $query = $this->addQuery($infoArray[0], $keys, $replace);
        $ret = $this->_db->prepare($query);
        $count = 0;
        foreach ($infoArray as $info) {
            $data = $this->prepareData($info, $keys);
            $val = $this->queryExecute($query, $ret, $data, false);
            if ($val) $count++;
        }
        return $count;
    }
    
    /**
     * Gets an attribute from the PDO object
     *
     * @param string $attrib The attribute to get.
     *
     * @return mixed
     */     
    private function getAttribute($attrib) 
    {
        if (!$this->checkDb()) return null; 
        return $this->_db->getAttribute($attrib);
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
        if (!is_array($keys)) return array();
        $ret = array();
        foreach ($keys as $k) {
            $ret[] = $data[$k];
        }
        return $ret;
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
    public function add($info, $replace = false)
    {
        $query = $this->addQuery($info, $keys, $replace);
        $values = $this->prepareData($info, $keys);
        return $this->query($query, $values, false);    
    }

    /**
     * Creates an add query
     *
     * @param array $info    The row in array form
     * @param array &$keys   The column names to use
     * @param bool  $replace If true it replaces the "INSERT" 
     *                       keyword with "REPLACE".  Not all
     *                       databases support "REPLACE".
     *
     * @return string
     */
    protected function addQuery($info, &$keys, $replace = false) 
    {    
        $div    = "";
        $fields = "";
        $values = array();
        $v      = "";
        $keys   = array();
        foreach ($this->fields as $key => $val) {
            if (!isset($info[$key])) continue;
            $fields .= $div.$key;
            $v.= $div." ? ";
            $keys[] = $key;
            $div = ", ";
        }
        // Don't do the query if there is nothing to query.
        if (empty($fields)) return false;

        // Do we replace or insert?
        if ($replace) {
            $query = "REPLACE";
        } else {
            $query = "INSERT";
        }
        // Build the rest of the query.
        $query .= " INTO `".$this->table."` (".$fields.") VALUES (".$v.")";
        
        return $query;
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
    public function update($info) 
    {   
        if (!is_array($info)) return false;
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
        $query    = " UPDATE `".$this->table."` SET ".$fields." WHERE ".$this->id."= ? ";
        return $this->query($query, $values, false);
    }


    /**
     * Gets all rows from the database
     *
     * @param int $limit The maximum number of rows to return (0 to return all)
     * @param int $start The row offset to start returning records at
     *
     * @return array
     */
    public function getAll($limit = 0, $start = 0) 
    {
        return $this->getWhere("1", array(), $limit, $start);
    }

    /**
     * Gets all rows from the database
     *
     * @param int $id The id of the row to get.
     *
     * @return array
     */
    public function get($id) 
    {
        return $this->getWhere($this->id."= ? ", array($id));
    }

    /**
     * Gets all rows from the database
     *
     * @param string $where Where clause
     * @param array  $data  Data for query
     * @param int    $limit The maximum number of rows to return (0 to return all)
     * @param int    $start The row offset to start returning records at
     *
     * @return array
     */
    public function getWhere($where, $data = array(), $limit = 0, $start = 0) 
    {
        $query = " SELECT * FROM `".$this->table."` WHERE ".$where;
        $limit = (int) $limit;
        $start = (int) $start;
        if (!empty($limit)) $query .= " LIMIT $start, $limit";
        return $this->query($query, $data, true);
    }



    /**
     * Returns the first row the where statement finds
     *
     * @param string $where a valid SQL where statement
     * @param array  $data  Data for query
     *
     * @return mixed
      */
    function getOneWhere($where, $data = array()) 
    {
        $res = $this->getWhere($where, $data, 0, 1);
        return $res[0];
    }

    /**
     * Returns the first row in the database
     *
     * @return mixed
     */
    function getOne() 
    {
        return $this->getOneWhere("1");
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
        $query = "SELECT MAX(".$this->id.") as id from `".$this->table."`";    
        $ret   = $this->query($query);
        $newID = (isset($ret[0]['id'])) ? (int) $ret[0]['id'] : 0 ;
        return $newID + 1;
    }

    /**
     * Fixes variable so they are the correct type
     *
     * @param mixed  $val  The value to fix
     * @param string $type The type to use.  This is the SQL column type
     *
     * @return mixed
     */
    public function fixType($val, $type)
    {
        $type = trim(strtolower($type));
        if (substr($type, 0, 4) == "char") return (string) $val;
        if (substr($type, 0, 4) == "text") return (string) $val;
        if (substr($type, 0, 7) == "varchar") return (string) $val;
        if (substr($type, 0, 3) == "int") return (int) $val;
        if (substr($type, 0, 5) == "float") return (float) $val;
        return $val;
    }

    /**
     * Sets the error code for the last query
     *
     * @param bool   $clear Clears the error
     * @param object $obj   The object to use to get the error code.
     *                      This must be either a PDO or PDOStatement
     *                      object.
     *
     * @return void
     */
    protected function errorInfo($clear=false, $obj = null)
    {
        if (!$this->checkDB()) {
            $this->errorState = "NODBE";
            $this->error      = -1;
            $this->errorMsg   = "Database Not Connected";
            $this->metaError = DBBASE_META_ERROR_SERVER_GONE;
            $this->metaErrorMsg = DBBASE_META_ERROR_SERVER_GONE_MSG;
            $this->printError();
            return;
        }
        if (!$clear) {
            if (method_exists($obj, "errorInfo")) {
                $err = $obj->errorInfo();
            } else {
                $err = $this->_db->errorInfo();
            }
        }
        $this->errorState = $err[0];
        $this->error      = $err[1];
        $this->errorMsg   = $err[2];
        $this->metaErrorInfo($err);
        $this->printError();
    }
    
    /**
     * This turns the errors into meta errors
     *
     * @param array $err The error array 
     *
     * @return void
     */
    protected function metaErrorInfo($err)
    {
        if (empty($err)) {
            $this->metaError = null;
            $this->metaErrorMsg = null;
            return;
        }
        $this->mysqlMetaErrorInfo($err);
        $this->sqliteMetaErrorInfo($err);
    }
    /**
     * This turns the errors into meta errors
     *
     * @param array $err The error array 
     *
     * @return void
     */
    protected function mysqlMetaErrorInfo($err)
    {
        if ($this->driver != "mysql") return;     
        if ($err[1] == 2006) {
            $this->metaError = DBBASE_META_ERROR_SERVER_GONE;
            $this->metaErrorMsg = DBBASE_META_ERROR_SERVER_GONE_MSG;
            return;
        } else if ($err[1] == 1062) {
            $this->metaError = DBBASE_META_ERROR_DUPLICATE;
            $this->metaErrorMsg = DBBASE_META_ERROR_DUPLICATE_MSG;
            return;           
        }
    }
    /**
     * This turns the errors into meta errors
     *
     * @param array $err The error array 
     *
     * @return void
     */
    protected function sqliteMetaErrorInfo($err)
    {
        if ($this->driver != "sqlite") return;
    }
    /**
     * Print Errors
     *
     * @return void
     */
    public function printError() 
    {
        if ($this->verbose) {
            if (!empty($this->errorState) && ($this->errorState != "00000"))
                $this->vprint("Error State: ".$this->errorState);
            if (!empty($this->error))
                $this->vprint("Error: ".$this->error);
            if (!empty($this->errorMsg))
                $this->vprint("Error Message: ".$this->errorMsg);
            if (!empty($this->metaError))
                $this->vprint("Meta Error: ".$this->metaError);
            if (!empty($this->metaErrorMsg))
                $this->vprint("Meta Error Message: ".$this->metaErrorMsg);
        }        
    }
    /**
     * Queries the database
     *
     * @param string $query  SQL query to send to the database
     * @param array  $data   The data to use to replace '?' in the query
     * @param bool   $getRet Whether to expect data back from the query
     *
     * @return mixed
     */
    public function query($query, $data=array(), $getRet=true) 
    {
        // Clear out the error
        $this->errorInfo(true); 

        $badRet = false;
        if ($getRet) $badRet = array();
        if (!$this->checkDb()) return $badRet;

        if (!is_array($data)) $data = array();

        $this->cacheQuery($query, $data, $getRet);
        $this->vprint("Preparing query: ".$query."\n");
        $ret = $this->_db->prepare($query);
        if (is_object($ret)) {
            return $this->queryExecute($query, $ret, $data, $getRet);
        } else {
            $this->errorInfo();        
        }
        
        if ($getRet) return array();
        return false;
    }

    /**
     * This function actually executes a query.
     *
     * @param string $query  The query we sent
     * @param object &$ret   The database query object
     * @param array  &$data  The data array to insert
     * @param bool   $getRes Whether to expect a result back.
     *
     * @return mixed
     */
    protected function queryExecute($query, &$ret, &$data, $getRes = false)
    {
        if (!is_object($ret)) return false;
        $this->vprint("Executing using data: \n".print_r($data, true));
        $res = $ret->execute($data);
        if ($getRes) {
            $res = $ret->fetchAll(PDO::FETCH_ASSOC);
            if (empty($res)) $res = $this->cacheQuery($query, $data, $getRes);
            $this->vprint("Query Returned: ".count($res)." rows");
            $this->cacheResult($res);
            $this->errorInfo(false, $ret);
            return $res;
        } else {
            $this->vprint("Query Returned: ".print_r($res, true));
        }
        $this->errorInfo(false, $ret);
        return $res;
    }
    /**
     * Queries the database
     *
     * @param string $query  SQL query to send to the database
     * @param array  $data   The data to use to replace '?' in the query
     * @param bool   $getRet Whether to expect data back from the query
     *
     * @return mixed
     */
    protected function cacheQuery($query, $data=array(), $getRet=true) 
    {
        $badRet = false;
        if ($getRet) $badRet = array();
        if (!is_array($data)) return $badRet;
        if (!is_object($this->_cache)) return $badRet;
        if (!$this->_doCache) return $badRet;
        $ret = $this->_cache->query($query, $data, $getRet);
        return $ret;
    }

    /**
     * Queries the database
     *
     * @param array $res The result array from a query 
     *
     * @return mixed
     */
    protected function cacheResult($res) 
    {
        if (!is_array($res)) return false;
        if (!is_object($this->_cache)) return false;
        if (!$this->_doCache) return false;
        $tries = count($res); 
        $count = $this->_cache->addArray($res, true);
        $this->vprint("Cache entry: $count/$tries");        
        
    }


    /**
     * Removes a row from the database.
     *
     * @param mixed $id The id value of the row to delete
     *
     * @return mixed 
     */
    public function remove($id) 
    {
        $query = " DELETE FROM '".$this->table."' WHERE ".$this->id."= ? ;";
        return $this->query($query, array($id), false);
    }

    /**
     * Removes a row from the database.
     *
     * @param string $where Where clause
     * @param array  $data  Data for query
     *
     * @return mixed 
     */
    public function removeWhere($where, $data=array())
    {
        $query = " DELETE FROM '".$this->table."' WHERE ".$where;
        return $this->query($query, $data, false);
    }
    
    /**
     * Sets the verbosity
     *
     * @param int $level The verbosity level
     *
     * @return void
     */    
    public function verbose($level=0)
    {
        $this->verbose = (int) $level;
    }

    /**
     * Prints out a string
     *
     * @param string $str The string to print out
     *
     * @return void
     */
    protected function vprint($str) 
    {
        if (!$this->verbose) return;
        if (empty($str)) return;
        $class = get_class($this);
        $driver = $this->driver;
        if ($driver == "sqlite") $file = $this->file;
        print "(".$class." - ".$driver." ".$file.") ".$str."\n";
    }
 
    /**
     * Tells us if the database is still connected
     *
     * @return bool
     */
    public function isConnected() 
    {
        if (!$this->checkDb()) return false;
        if ($this->metaError == DBBASE_META_ERROR_SERVER_GONE) return false;
        if ($this->driver == "sqlite") return true;
        return $this->_db->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    }

    /**
     * Takes a date and turns it into a SQL date
     * 
     * @param mixed $date The date in just about any format
     *
     * @return string SQL Date
     */
    function sqlDate($date) 
    {
        if (trim(strtoupper($date)) == "NOW") return date($this->dateFormat);
        if (is_numeric($date)) return date($this->dateFormat, $date);
        if (is_string($date)) return date($this->dateFormat, strtotime($date));
        return $date;
    }
    
}


?>
