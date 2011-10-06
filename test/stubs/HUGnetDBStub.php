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
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    git: $Id$
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
require_once CODE_BASE."base/HUGnetClass.php";
/** This is our test configuration */
require_once CODE_BASE."/interfaces/HUGnetDB.php";
/**
 * Base class for all database work
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class HUGnetDBStub extends HUGnetClass implements HUGnetDBInterface
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

    /** @var array Where we store the data */
    private $_data = array();
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
        parent::__construct($config);
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
        if (!is_array($infoArray)) {
            return false;
        }
        foreach ($infoArray as $row) {
            $this->add($row, $replace);
        }
        return true;
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
        if ($replace || !isset($this->_data[$info[$this->id]])) {
            $this->_data[$info[$this->id]] = $info;
            return true;
        }
        return false;
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
        if (isset($this->_data[$info[$this->id]])) {
            $this->_data[$info[$this->id]] = $info;
            return true;
        }
        return false;

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
        return $this->_data;
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
        return $this->_data[$id];
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
    }

    /**
    * Returns the first row in the database
    *
    * @return mixed
    */
    function getOne()
    {
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
        if (count($this->_data) > 0) {
            return max(array_keys($this->_data))+1;
        }
        return 1;
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
        if (count($this->_data) > 0) {
            return min(array_keys($this->_data))-1;
        }
        return -1;
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
    * Print Errors
    *
    * @return null
    */
    public function printError()
    {
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
        unset($this->_data[$id]);
        return true;
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

    }

    /**
    * Tells us if the database is still connected
    *
    * @return bool
    */
    public function isConnected()
    {
        return true;
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
        if (trim(strtoupper($date)) == "NOW") {
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
        if (file_exists(CODE_BASE."/database/".$class.".php")) {
            include_once CODE_BASE."/database/".$class.".php";
        }
        if (!class_exists($class)) {
            return false;
        }
        if (!is_subclass_of($class, "HUGnetDBStub") && ($class != "HUGnetDB")) {
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
    * Gets and sets the configuration
    *
    * @return array The configuration
    */
    function getConfig()
    {
        return $this->_config;
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
        $this->_config = $config;
        return $this->_config;
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
    public function fromCSV($CSV, $fieldSep = ",", $rowSep = "\n")
    {
    }
}


?>
