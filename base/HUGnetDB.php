<?php
/**
 * Classes for dealing with devices
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
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** The database went away */
define("HUGNETDB_META_ERROR_SERVER_GONE", 1);
/** The database went away */
define("HUGNETDB_META_ERROR_SERVER_GONE_MSG", "The server has gone away");
/** The database went away */
define("HUGNETDB_META_ERROR_DUPLICATE", 2);
/** The database went away */
define("HUGNETDB_META_ERROR_DUPLICATE_MSG", "Duplicate Entry");
/** Misc stuff */
require_once dirname(__FILE__)."/HUGnetClass.php";
require_once HUGNET_INCLUDE_PATH."/interfaces/HUGnetDB.php";
/**
 * Base class for all database work
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.
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
class HUGnetDB extends HUGnetClass implements HUGnetDBInterface
{
    /** @var string The database table to use */
    protected $table = "none";
    /** @var string This is the Field name for the key of the record */
    protected $id = "id";
    /** SQL date format */
    protected $dateFormat = "Y-m-d H:i:s";
    /** The default file */

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
    protected $db = null;

    /** @var object The database object for the cache */
    protected $cacheDb = null;

    /** @var object The cache */
    protected $cache = null;

    /** @var object The cache */
    protected $doCache = false;

    /** Whether to fake autoincrmement */
    protected $autoIncrement = false;

    /** @var string The dsn we are connected to */
    private $_dsn = "";

    /** @var int The timeout for connecting to the database */
    protected $dbTimeout = 5;
    /** @var string The state of the error */
    public $errorState = "";
    /** @var int The error number */
    public $error = 0;
    /** @var string The error message. */
    public $errorMsg = "";
    /** @var int The database agnostic error number */
    public $metaError = 0;
    /** @var string The database agnostic error message */
    public $metaErrorMsg = "";

    /** @var int The verbosity level */
    public $verbose = 0;

    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param mixed $config The configuration array
    *
    * @return null
    */
    public function __construct($config = array())
    {
        // If this is not already defined by here, we need to define it.
        // I put it here so that there can be ample opportunity to define it
        // other places.
        // @codeCoverageIgnoreStart
        // This is run from an ignored file first, so It never gets counted, even
        // though it is run.
        if (!defined("HUGNET_LOCAL_DATABASE")) {
            define("HUGNET_LOCAL_DATABASE", sys_get_temp_dir()."/HUGnet.sq3");
        }
        // @codeCoverageIgnoreEnd

        $this->config = $config;
        parent::__construct($config);

        $this->file = $this->setFile($config);

        if (is_string($config["table"])) {
            $this->table = $config["table"];
        }
        if (is_string($config["id"])) {
            $this->id = $config["id"];
        }
        if (is_int($config["dbTimeout"])) {
            $this->dbTimeout = $config["dbTimeout"];
        }
        $this->verbose($config["verbose"]);
        unset($config["table"]);
        unset($config["id"]);

        $this->db = &HUGnetDB::createPDO($config);
        // @codeCoverageIgnoreStart
        // This is impossible to test because it would cause the test to abort.
        if (!$this->checkDB()) {
            $except = "No Database Connection in class ".get_class($this);
            self::throwException($except, -2);
            die("No database server available");
        }
        // @codeCoverageIgnoreEnd
        $this->driver = $this->_getAttribute(PDO::ATTR_DRIVER_NAME);
        $this->db->setAttribute(PDO::ATTR_TIMEOUT, $this->dbTimeout);

        $this->getColumns();

    }


    /**
    * Creates a database object
    *
    * @param mixed $config The configuration to use
    *
    * @return object PDO object
    */
    static public function &createPDO($config = array())
    {
        $verbose = (int)$config['verbose'];
        $file = self::setFile($config);
        if (is_string($config['db_name'])) {
            $db_name = $config['db_name'];
        } else {
            $db_name = HUGNET_DATABASE;
        }
        if (is_array($config['servers'])) {
            $servers = $config['servers'];
        } else {
            $servers = array();
        }
        if (is_string($config["servers"][0]['user'])) {
            $servers[0]['user'] = $config["servers"][0]['user'];
        } else {
            $servers[0]['user'] = null;
        }
        if (is_string($config["servers"][0]['password'])) {
            $servers[0]['password'] = $config["servers"][0]['password'];
        } else {
            $servers[0]['password'] = null;
        }

        // Okday, now try to connect
        foreach ($servers as $serv) {
            $useDriver = is_string($serv['driver']) ? $serv['driver'] : "sqlite";
            $dsn       = self::createDSN(
                $useDriver,
                $db_name,
                $file,
                $serv["host"]
            );
            $pdo = self::_createPDO(
                $dsn,
                $serv["user"],
                $serv["password"],
                $serv["options"],
                $verbose
            );
            if (is_object($pdo)) {
                return $pdo;
            }
        }
        return null;
    }

    /**
    * Creates a database object
    *
    * @param string $dsn     The DSN to use to create the PDO object
    * @param string $user    The username
    * @param string $pass    The password
    * @param array  $options Options to pass to the PDO constructor
    * @param int    $verbose The verbosity number
    *
    * @return object PDO object
    */
    static private function &_createPDO(
        $dsn,
        $user = null,
        $pass = null,
        $options = array(),
        $verbose = 0
    ) {
        static $pdo;
        if (!is_array($options)) {
            $options = array();
        }
        $key = md5($dsn.$user.serialize($options));
        if (!is_object($pdo[$key]) || (get_class($pdo[$key]) != "PDO")) {
            try {
                $pdo[$key] = new PDO($dsn, $user, $pass);
            } catch (PDOException $e) {
                self::vprint(
                    "Error (".$e->getCode()."): ".$e->getMessage()."\n",
                    1,
                    $verbose
                );
                return null;
            }
        }
        return $pdo[$key];
    }

    /**
    * Checks to see if the database object is valid
    *
    * If the database object given to it is null it will check $this.
    *
    * $db can not be a 'reference'.
    *
    * @param object $db The database object to check.
    *
    * @return bool
    */
    final protected function checkDb($db=null)
    {
        if (is_null($db)) {
            $db = &$this->db;
        }
        if (!is_object($db)) {
            return false;
        }
        $class = get_class($db);
        if (trim(strtoupper($class)) !== "PDO") {
            return false;
        }
        return true;
    }
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
    public function addField($name, $type="TEXT", $default=null, $null=false)
    {
        if (isset($this->fields[$name])) {
            return true;
        }
        $query  = "ALTER TABLE `".$this->table."` ADD `$name` $type ";
        if (!$null) {
            $query .= "NOT NULL ";
        }
        if (!is_null($default)) {
            $query .= " DEFAULT '$default'";
        }
        if ($this->query($query, array(), false)) {
            $this->fields[$name] = $type;
            return true;
        } else {
            return false;
        }
    }
    /**
    * Throws an exception
    *
    * @param string $msg  The message
    * @param int    $code The error code
    *
    * @return null
    */
    protected function throwException($msg, $code)
    {
        // @codeCoverageIgnoreStart
        // No way to test this as it will kill the test. ;)
        if (is_object($this) && ($this->config["silent"])) {
            return;
        }

        throw new Exception($msg, $code);
        // @codeCoverageIgnoreEnd
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
        $class = get_class($this);
        $lowClass = strtolower($class);
        $this->cacheConfig[$lowClass."file"] = $this->config[$lowClass."file"];
        $this->cacheConfig["file"] = $this->file;
        // Can't cache a sqlite database server
        if ($this->driver == "sqlite") {
            $file = substr(trim(strtolower($this->file)), 0, 7);
            if ($file == ":memory") {
                return false;
            }
            $this->cacheConfig["file"] = ":memory:";
        } else {
            $this->setFile($this->cacheConfig, $file);
        }
        self::vprint("Creating a cache at ".$this->cacheConfig["file"], 1);
        $this->cache =& self::getInstance($class, $this->cacheConfig);
        $this->cache->createTable($this->table);
        $this->doCache = true;
        $this->cache->createTable();
        return true;
    }

    /**
    * Figures out what file to use and returns it.
    *
    * @param array  &$config The configuration array
    * @param string $file    The file to set it to
    *
    * @return null
    */
    protected function setFile(&$config, $file=null)
    {
        // This enables us to put this class other places
        $myclass = strtolower(get_class($this));
        if (!empty($myclass) && isset($config[$myclass."file"])) {
            $config["file"] = $config[$myclass."file"];
            self::vprint("Using a custom file: ".$config["file"], 1);
            self::vprint(" for ".$myclass."\n", 1);
        } else if (is_string($file) && !empty($file)) {
            $config["file"] = $file;
        } else if (!is_string($config["file"]) || empty($config["file"])) {
            $config["file"] = constant("HUGNET_LOCAL_DATABASE");
        }
        return $config["file"];
    }


    /**
    * Creates the field array
    *
    * @return null
    */
    protected function getColumns()
    {
        $this->fields = array();
        $this->getColumnsSQLite();
        $this->getColumnsMySQL();
        $this->getColumnsOther();
    }
    /**
    * Gets columns from a SQLite server
    *
    * @return null
    */
    protected function getColumnsSQLite()
    {
        if ($this->driver != "sqlite") {
            return;
        }
        $columns = $this->query("PRAGMA table_info(".$this->table.")");
        foreach ($columns as $col) {
            $this->fields[$col['name']] = $col['type'];
        }
    }

    /**
    * Gets columns from a mysql server
    *
    * @return null
    */
    protected function getColumnsMySQL()
    {
        if ($this->driver != "mysql") {
            return;
        }
        $columns = $this->query("SHOW COLUMNS FROM ".$this->table);
        foreach ($columns as $col) {
            // @codeCoverageIgnoreStart
            // This is impossible to test without a mysql server
            $this->fields[$col['Field']] = $col['Type'];
            // @codeCoverageIgnoreEnd
        }

    }

    /**
    * Gets columns from a mysql server
    *
    * @return null
    */
    protected function getColumnsOther()
    {
        if ($this->driver == "mysql") {
            return;
        }
        if ($this->driver == "sqlite") {
            return;
        }
        // @codeCoverageIgnoreStart
        // There is no way to test this function.  The exception will cause the test
        // to fail.
        self::throwException("Driver ".$this->driver." is not implemented.", -1);
    }
    // @codeCoverageIgnoreEnd

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
        if (!$this->checkDb()) {
            return 0;
        }
        if (!is_array($infoArray)) {
            return 0;
        }
        $query = $this->addQuery($infoArray[0], $keys, $replace);
        $ret   = $this->db->prepare($query);
        $count = 0;
        if (is_object($ret)) {
            foreach ($infoArray as $info) {
                $data = $this->prepareData($info, $keys);
                $val  = $this->queryExecute($query, $ret, $data, false);
                if ($val) {
                    $count++;
                }
            }
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
    private function _getAttribute($attrib)
    {
        if ($this->checkDb()) {
            return $this->db->getAttribute($attrib);
        }
        // @codeCoverageIgnoreStart
        // Can't get here.  So far every call is checked by checkDB before the call
        return null;
        // @codeCoverageIgnoreEnd
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
        if (!is_array($keys)) {
            return array();
        }
        $ret = array();
        if (!isset($data[$this->id]) && $this->autoIncrement) {
            $data[$this->id] = $this->getNextID();
        }

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
        $query  = $this->addQuery($info, $keys, $replace);
        $values = $this->prepareData($info, $keys);
        return $this->query($query, $values, false);
    }

    /**
    * Cleans the sql for the various drivers
    *
    * @param string $sql The sql string to clean
    *
    * @return string
    */
    protected function cleanSql($sql)
    {
        $sql = $this->cleanSqlSqlite($sql);
        $sql = $this->cleanSqlMySql($sql);
        return $sql;
    }
    /**
    * Cleans the sql for the various drivers
    *
    * @param string $sql The sql string to clean
    *
    * @return string
    */
    protected function cleanSqlSqlite($sql)
    {
        if ($this->driver != "sqlite") {
            return $sql;
        }
        if (strpos($sql, "auto_increment") !== false) {
            $this->autoIncrement = true;
        }
        $sql = str_ireplace("auto_increment", "", $sql);
        return $sql;
    }
    /**
    * Cleans the sql for the various drivers
    *
    * @param string $sql The sql string to clean
    *
    * @return string
    */
    protected function cleanSqlMySql($sql)
    {
        if ($this->driver != "mysql") {
            return $sql;
        }
        return $sql;
    }

    /**
    * Creates an add query
    *
    * @param array &$info   The row in array form
    * @param array &$keys   The column names to use
    * @param bool  $replace If true it replaces the "INSERT"
    *                       keyword with "REPLACE".  Not all
    *                       databases support "REPLACE".
    *
    * @return string
    */
    protected function addQuery(&$info, &$keys, $replace = false)
    {
        $div    = "";
        $fields = "";
        $values = array();
        $v      = "";
        $keys   = array();
        if (!isset($info[$this->id]) && $this->autoIncrement) {
            $info[$this->id] = $this->getNextID();
        }
        foreach ($this->fields as $key => $val) {
            if (!isset($info[$key])) {
                continue;
            }
            $fields .= $div.$key;
            $v      .= $div." ? ";
            $keys[]  = $key;
            $div     = ", ";
        }
        // Don't do the query if there is nothing to query.
        if (empty($fields)) {
            return false;
        }

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
        if (!is_array($info)) {
            return false;
        }
        if (!isset($info[$this->id])) {
            return false;
        }

        $values[] = $info[$this->id];
        return $this->updateWhere($info, $this->id."= ? ", array($info[$this->id]));
    }


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
    public function updateWhere($info, $where, $data = array())
    {
        if (!is_array($info)) {
            return false;
        }

        $div    = "";
        $fields = "";
        $values = array();
        $v      = "";
        foreach ($this->fields as $key => $val) {
            if (!isset($info[$key]) || ($key == $this->id)) {
                continue;
            }
            $fields  .= $div.$key." = ? ";
            $values[] = $info[$key];
            $div      = ", ";
        }

        $values = array_merge($values, $data);
        $query  = " UPDATE `".$this->table."` SET ".$fields." WHERE ".$where;
        return $this->query($query, $values, false);
    }

    /**
    * Gets all rows from the database
    *
    * @param int    $limit   The maximum number of rows to return (0 to return all)
    * @param int    $start   The row offset to start returning records at
    * @param string $orderby The orderby Clause.  Must include "ORDER BY"
    *
    * @return array
    */
    public function getAll($limit = 0, $start = 0, $orderby="")
    {
        return $this->getWhere("1", array(), $limit, $start, $orderby);
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
    ) {
        $query = " SELECT * FROM `".$this->table."` WHERE ".$where." ".$orderby;
        $limit = (int) $limit;
        $start = (int) $start;
        if (!empty($limit)) {
            $query .= " LIMIT $start, $limit";
        }
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
    * @param int $id The cureent ID to use
    *
    * @return int
    */
    function getNextID($id = null)
    {
        if (empty($id)) {
            $id = $this->id;
        }
        $query = "SELECT MAX(".$id.") as id from `".$this->table."`";
        $ret   = $this->query($query);
        $newID = (isset($ret[0]['id'])) ? (int) $ret[0]['id'] : 0 ;
        return $newID + 1;
    }

    /**
    * Gets one less that the smallest ID to use from the table.
    *
    * This only works with integer ID columns!
    *
    * @param int $id The cureent ID to use
    *
    * @return int
    */
    function getPrevID($id = null)
    {
        if (empty($id)) {
            $id = $this->id;
        }
        $query = "SELECT MIN(".$id.") as id from `".$this->table."`";
        $ret   = $this->query($query);
        $newID = ($ret[0]['id'] < 0) ? (int) $ret[0]['id'] : 0 ;
        return $newID - 1;
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
        if (is_null($val)) {
            return null;
        }
        $type = trim(strtolower($type));
        if (substr($type, 0, 4) == "char") {
            return (string) $val;
        }
        if (substr($type, 0, 4) == "text") {
            return (string) $val;
        }
        if (substr($type, 0, 7) == "varchar") {
            return (string) $val;
        }
        if (substr($type, 0, 3) == "int") {
            return (int) $val;
        }
        if (substr($type, 0, 5) == "float") {
            return (float) $val;
        }
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
    * @return null
    */
    protected function errorInfo($clear = false, $obj = null)
    {
        if (!$this->checkDB()) {
            $this->errorState   = "NODBE";
            $this->error        = -1;
            $this->errorMsg     = "Database Not Connected";
            $this->metaError    = HUGNETDB_META_ERROR_SERVER_GONE;
            $this->metaErrorMsg = HUGNETDB_META_ERROR_SERVER_GONE_MSG;
            $this->printError();
            return;
        }
        if (!$clear) {
            if (method_exists($obj, "errorInfo")) {
                $err = $obj->errorInfo();
            } else {
                $err = $this->db->errorInfo();
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
    * @return null
    */
    protected function metaErrorInfo($err)
    {
        if (empty($err)) {
            $this->metaError    = null;
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
    * @return null
    */
    protected function mysqlMetaErrorInfo($err)
    {
        if ($this->driver != "mysql") {
            return;
        }
        if ($err[1] == 2006) {
            $this->metaError    = HUGNETDB_META_ERROR_SERVER_GONE;
            $this->metaErrorMsg = HUGNETDB_META_ERROR_SERVER_GONE_MSG;
            return;
        } else if ($err[1] == 1062) {
            $this->metaError    = HUGNETDB_META_ERROR_DUPLICATE;
            $this->metaErrorMsg = HUGNETDB_META_ERROR_DUPLICATE_MSG;
            return;
        }
    }
    /**
    * This turns the errors into meta errors
    *
    * @param array $err The error array
    *
    * @return null
    */
    protected function sqliteMetaErrorInfo($err)
    {
        if ($this->driver != "sqlite") {
            return;
        }
    }
    /**
    * Print Errors
    *
    * @return null
    */
    public function printError()
    {
        if ($this->verbose) {
            if (!empty($this->errorState) && ($this->errorState != "00000")) {
                self::vprint("Error State: ".$this->errorState);
            }
            if (!empty($this->error)) {
                self::vprint("Error: ".$this->error);
            }
            if (!empty($this->errorMsg)) {
                self::vprint("Error Message: ".$this->errorMsg);
            }
            if (!empty($this->metaError)) {
                self::vprint("Meta Error: ".$this->metaError);
            }
            if (!empty($this->metaErrorMsg)) {
                self::vprint("Meta Error Message: ".$this->metaErrorMsg);
            }
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

        if ($getRet) {
            $badRet = array();
        } else {
            $badRet = false;
        }
        if (!$this->checkDb()) {
            return $badRet;
        }

        if (!is_array($data)) {
            $data = array();
        }

        $this->cacheQuery($query, $data, $getRet);
        self::vprint("Preparing query: ".$query."\n");
        $ret = $this->db->prepare($query);
        if (is_object($ret)) {
            return $this->queryExecute($query, $ret, $data, $getRet);
        }

        $this->errorInfo();

        return $badRet;
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
    *
    * @todo Think about removing the check on the object
    */
    protected function queryExecute($query, &$ret, &$data, $getRes = false)
    {
        self::vprint("Executing using data: \n".print_r($data, true));
        $res = $ret->execute($data);
        if ($getRes) {
            $res = $ret->fetchAll(PDO::FETCH_ASSOC);
            if (empty($res)) {
                $res = $this->cacheQuery($query, $data, $getRes);
            }
            self::vprint("Query Returned: ".count($res)." rows");
            $this->cacheResult($res);
            $this->errorInfo(false, $ret);
            return $res;
        } else {
            self::vprint("Query Returned: ".print_r($res, true));
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
        if ($getRet) {
            $badRet = array();
        }
        if (!$this->doCache || !is_array($data) || !is_object($this->cache)) {
            return $badRet;
        }
        $ret = $this->cache->query($query, $data, $getRet);
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
        if (!$this->doCache || !is_array($res) || !is_object($this->cache)) {
            return false;
        }
        $tries = count($res);
        $count = $this->cache->addArray($res, true);
        self::vprint("Cache entry: $count/$tries");

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
        $query = " DELETE FROM `".$this->table."` WHERE ".$this->id."= ? ;";
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
        $query = " DELETE FROM `".$this->table."` WHERE ".$where;
        return $this->query($query, $data, false);
    }

    /**
    * Sets the verbosity
    *
    * @param int $level The verbosity level
    *
    * @return null
    */
    public function verbose($level=0)
    {
        $level = (int) $level;
        $this->verbose = $level;
    }

    /**
    * Tells us if the database is still connected
    *
    * @return bool
    */
    public function isConnected()
    {
        if (!$this->checkDb()) {
            return false;
        }
        if ($this->metaError == HUGNETDB_META_ERROR_SERVER_GONE) {
            return false;
        }
        if ($this->driver == "sqlite") {
            return true;
        }
        // @codeCoverageIgnoreStart
        // This is impossible to test without a MySQL server.
        return $this->_getAttribute(PDO::ATTR_CONNECTION_STATUS);
        // @codeCoverageIgnoreEnd
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
        if (trim(strtoupper((string)$date)) == "NOW") {
            return date($this->dateFormat);
        }
        if (is_numeric($date)) {
            return date($this->dateFormat, $date);
        }
        if (is_string($date)) {
            return date($this->dateFormat, strtotime($date));
        }
        return $date;
    }
    /**
    * Gets an instance of the HUGnet Driver
    *
    * @param string $class  The class to create
    * @param array  $config The configuration to use
    *
    * @return object A reference to a driver object
    */
    function &getInstance($class = "HUGnetDB", $config = null)
    {
        static $instances;
        if (file_exists(HUGNET_INCLUDE_PATH."/database/".$class.".php")) {
            include_once HUGNET_INCLUDE_PATH."/database/".$class.".php";
        }
        if (!class_exists($class)) {
            return false;
        }
        if (!is_subclass_of($class, "HUGnetDB") && ($class != "HUGnetDB")) {
            return false;
        }
        if (is_null($config)) {
            $config = self::getConfig();
        }
        $key = serialize($config);
        if (empty($instances[$class][$key])) {
            self::setConfig($config, false);

            $instances[$class][$key] = new $class($config);
        }
        return $instances[$class][$key];

    }

    /**
    * creates a dsn for the PDO stuff.  The DSNs apper in the $servers array
    *
    * @param string $driver The PDO driver to use
    * @param string $db     The database to use
    * @param string $file   The file to use if the driver is 'sqlite'
    * @param array  $host   The host to use.
    *
    * @return null
    */
    function createDSN($driver, $db, $file, $host)
    {
        $driver = strtolower($driver);

        if ($driver == "mysql") {
            if (empty($host)) {
                $host = "localhost";
            }
            $dsn = "mysql:host=".$host.";dbname=".$db;
        } else {
            if (empty($file)) {
                $file = ":memory:";
            }
            $dsn = "sqlite:".$file;
        }
        return $dsn;
    }

    /**
    * Gets and sets the configuration
    *
    * @return array The configuration
    */
    function getConfig()
    {
        return self::_config();
    }
    /**
    * Gets and sets the configuration
    *
    * @param array $config    The configuration to set.  If left out the
    *                         configuration is retrieved.
    * @param bool  $overwrite Overwrite a config if there is one already
    *
    * @return array The configuration
    */
    function setConfig($config=array(), $overwrite = true)
    {
        return self::_config($config, $overwrite);
    }

    /**
    * Gets and sets the configuration
    *
    * @param array $config    The configuration to set.  If left out the
    *                         configuration is retrieved.
    * @param bool  $overwrite Overwrite a config if there is one already
    *
    * @return array The configuration
    */
    private function _config($config = null, $overwrite = true)
    {
        static $saveConfig;

        if (is_array($config) && (empty($saveConfig) || $overwrite)) {
            $saveConfig = $config;
        }

        return $saveConfig;
    }

    /**
    * This converts a CSV string to a database array
    *
    * @param string $CSV      The CSV string to use
    * @param string $fieldSep The separator to use.  "," is the default
    * @param string $rowSep   The separator for rows.  "\n" is the default
    *
    * @return array The database array
    */
    function fromCSV($CSV, $fieldSep = ",", $rowSep = "\n")
    {
        if (!is_array($CSV)) {
            $CSV = explode($rowSep, $CSV);
        }

        $ret = array();
        $count = 0;
        foreach ($CSV as $row) {
            if (empty($row)) {
                continue;
            }
            $r = explode($fieldSep, $row);
            $index = 0;
            foreach (array_keys($this->fields) as $field) {
                $ret[$count][$field] = str_replace('"', "", $r[$index]);
                $index++;
            }
            $count++;
        }
        return $ret;
    }
}


?>
