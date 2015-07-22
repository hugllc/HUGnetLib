<?php
/**
 * Classes for dealing with devices
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
/** This is the HUGnet namespace */
namespace HUGnet\base;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is the base of our table class */
require_once dirname(__FILE__)."/../db/Table.php";
/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
abstract class SystemTableBase
{
    /** @var int The database table to use */
    private $_table = null;
    /** @var int The config to use */
    private $_system = null;
    /** @var int The database table class to use */
    protected $tableClass = null;
    /** @var int The database table class to use */
    private $_new = false;
    /** This is our connection object */
    private $_connect = null;
    /** These are our keys to search for.  Null means search everything given */
    protected $keys = null;
    /** This is our url */
    protected $url = "";
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$system The configuration array
    * @param string $table   The table class to use
    * @param array  $data    The initial data for this object
    *
    * @return null
    */
    protected function __construct(&$system, $table, $data = array())
    {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $this->_system = &$system;
        $this->_setTable($table);
        // Create the table with initial data in it.
        // The main reason this is here is to set the group immediately.
        $this->_table($data);
    }
    /**
    * Sets the database table to use. It expects either an object or a string
    *
    * @param mixed &$table The table class to use
    *
    * @return null
    */
    private function _setTable(&$table)
    {
        if (is_string($table)) {
            $this->tableClass = $table;
        } else if (is_object($table)) {
            $this->tableClass = get_class($table);
            $this->_table = $table;
        }
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        if (is_object($this->_table)) {
            // This calls the destructor on the table object
            unset($this->_table);
        }
        unset($this->_connect);
    }
    /**
    * This returns the URL, including the id, if it exists
    *
    * @param string $url This is the base URL
    *
    * @return reference to the table class object
    */
    public function url($url = "")
    {
        $url = (string)$url.$this->url;
        $id = $this->id();
        if (!is_null($id)) {
            $url .= "/$id";
        }
        return $url;
    }
    /**
    * This function gives us access to the table class
    *
    * @return reference to the table class object
    */
    public function &table()
    {
        return $this->_table();
    }
    /**
    * This function gives us access to the table class
    *
    * @param array $data This is the inital data for the table.
    *
    * @return reference to the table class object
    */
    private function &_table($data = array())
    {
        if (!is_object($this->_table)) {
            $this->_table = $this->system()->table(
                $this->tableClass, $data
            );
        }
        return $this->_table;
    }
    /**
    * This function gives us access to the table class
    *
    * @return reference to the system object
    */
    public function &system()
    {
        return $this->_system;
    }
    /**
    * This function creates the system.
    *
    * @param object &$system The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $table   The table to use
    *
    * @return null
    */
    public static function &factory(
        &$system, $data=null, $table="GenericTable"
    ) {
        $class = get_called_class();
        $object = new $class($system, $table, $data);
        if (!is_null($data) && ($data !== array())) {
            $object->load($data);
        }
        return $object;
    }
    /**
    * Deletes this record
    *
    * @return null
    */
    public function delete()
    {
        return $this->table()->deleteRow();
    }
    /**
    * Loads the data into the table class
    *
    * @param mixed $data (int)The id of the record,
    *                    (array) or (string) data info array
    *
    * @return bool Whether we found this in the db or not.
    */
    public function load($data)
    {
        $ret = false;
        $this->table()->clearData();
        if (is_array($data) && isset($data["group"])) {
            // This needs to get set before we search the DB
            $this->table()->set("group", $data["group"]);
        }
        if (is_int($data) || is_string($data)) {
            $this->table()->getRow($data);
            if ($this->table()->isEmpty()) {
                $ret = false;
                $this->table()->set($this->table()->sqlId, $data);
            } else {
                $ret = true;
            }
        } else if (is_array($data) && !empty($data)) {
            $ret = $this->_find($data, $this->keys);
        }
        if (is_array($data) || is_object($data)) {
            $this->table()->fromAny($data);
            $this->fixTable();
        }
        if (!$ret) {
            $this->_new = true;
        }
        return (bool)$ret;
    }
    /**
    * Loads the data into the table class
    *
    * @param array $data The data to create the table with
    *
    * @return bool Whether we found this in the db or not.
    */
    public function create($data)
    {
        if (!is_array($data)) {
            return false;
        }
        $this->table()->clearData();
        $this->table()->fromArray($data);
        $this->fixTable();
        $this->_new = true;
        if ($this->store()) {
            if (!empty($this->table()->sqlId)) {
                $this->table()->sqlOrderBy = $this->table()->sqlId." desc";
            }
            return $this->_find($this->table()->toDB());
        }
        return false;
    }
    /**
    * Loads the data into the table class
    *
    * @param array $data The data to create the table with
    * @param array $keys The keys to find stuff with
    *
    * @return bool Whether we found this in the db or not.
    */
    private function _find($data, $keys = null)
    {
        if (isset($data["group"])) {
            $this->table()->set("group", $data["group"]);
        }
        $wdata = (array)$this->table()->sanitizeWhere($data);
        $ret = $this->table()->selectOneInto($wdata);
        return $ret;
    }
    /**
    * Changes data that is in the table and saves it
    *
    * @param mixed $data (int)The id of the record,
    *                    (array) or (string) data info array
    *
    * @return null
    */
    public function change($data)
    {
        $ret = false;
        if (is_array($data) || is_string($data)) {
            $this->table()->fromAny($data);
            $this->fixTable();
            $this->table()->updateRow();
            $ret = true;
        }
        return (bool)$ret;
    }
    /**
    * Changes data that is in the table and saves it
    *
    * @param array $where   The things the list should filter for
    * @param bool  $default Whether to add the default stuff on or not.
    *
    * @return null
    */
    public function getList($where = null, $default = false)
    {
        $this->table()->clearData();
        if (!empty($this->table()->sqlId)) {
            $this->table()->sqlOrderBy = $this->table()->sqlId." asc";
        }
        $where = (array)$this->table()->sanitizeWhere($where);
        $ret = $this->table()->selectInto($where);
        $return = array();
        while ($ret) {
            $return[] = $this->toArray($default);
            $ret = $this->table()->nextInto();
        }
        return $return;
    }
    /**
    * Returns the table as a json string
    *
    * @return json string
    */
    public function json()
    {
        return json_encode($this->toArray(true));
    }
    /**
    * Returns the table as an array
    *
    * @param bool $default Whether or not to include the default values
    *
    * @return array
    */
    public function toArray($default = false)
    {
        return $this->table()->toArray($default);
    }
    /**
    * Gets columns from a mysql server
    *
    * @return null
    */
    public function lastError()
    {
        return $this->table()->lastError();
    }
    /**
    * Stores data into the database
    *
    * @param bool $replace Replace any record that is in the way
    *
    * @return null
    */
    public function store($replace = false)
    {
        $sid = $this->table()->get($this->table()->sqlId);
        $this->fixTable();
        if (empty($sid) || $replace || $this->isNew()) {
            $ret = $this->table()->insertRow($replace);
            $this->_new = false;
        } else {
            $ret = $this->table()->updateRow();
        }
        return (bool)$ret;
    }
    /**
    * Gets a value
    *
    * @param string $field the field to get
    *
    * @return null
    */
    public function get($field)
    {
        return $this->table()->get($field);
    }
    /**
    * Sets a value
    *
    * @param string $field the field to set
    * @param mixed  $value the value to set
    *
    * @return null
    */
    public function set($field, $value)
    {
        return $this->table()->set($field, $value);
    }
    /**
    * Lists the ids of the table values
    *
    * @param array $data The data to use with the where clause
    *
    * @return null
    */
    public function ids($data = array())
    {
        $where = (array)$this->table()->sanitizeWhere((array)$data);
        return $this->table()->selectIDs($where);
    }
    /**
    * Lists the ids of the table values
    *
    * @return int The ID of this device
    *
    * @SuppressWarnings(PHPMD.ShortMethodName)
    */
    public function id()
    {
        return $this->table()->get($this->table()->sqlId);
    }
    /**
    * This function should be overloaded to make changes to the table based on
    * changes to incoming data.
    *
    * This is a way to make sure that the data is consistant before it gets stored
    * in the database
    *
    * @return null
    */
    protected function fixTable()
    {
    }
    /**
    * This function creates the action item.  The default is just to return this.
    *
    * @return Reference to the action object
    */
    public function &action()
    {
        return $this;
    }
    /**
    * This function creates the action item.  The default is just to return this.
    *
    * @return Reference to the action object
    */
    public function isNew()
    {
        return $this->_new;
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    public function retrieve($url = null, $timeout=60)
    {
        if (empty($url)) {
            $master = $this->system()->get("master");
            $url = $master["url"];
        }
        $url .= $this->url();
        $return = $this->httpMethod(
            "GET", null, $url, $timeout
        );
        return $return;

    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    public function post($url = null, $timeout=60)
    {
        $return = false;
        if (empty($url)) {
            $master = $this->system()->get("master");
            $url = $master["url"];
        }
        $url .= $this->url();
        if ($this->isNew()) {
            $return = $this->httpMethod(
                "POST", http_build_query($this->toArray(false)), $url, $timeout
            );
        } else {
            $return = $this->httpMethod(
                "PUT", json_encode($this->toArray(false)), $url, $timeout
            );
        }
        return $return;
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function httpMethod($method, $data, $url = null, $timeout=60)
    {
        $params = array(
            'http' => array(
                'method' => $method,
                'content' => $data,
                'timeout' => $timeout,
                'header'=>"Connection: close\r\nAccept: application/json\r\n",
            )
        );
        $this->_postError = array();
        $ctx = stream_context_create($params);
        try {
            $code = 400;
            $response = @file_get_contents($url, false, $ctx);
            foreach ((array)$http_response_header as $head) {
                if (substr($head, 0, 4) == "HTTP") {
                    $c = explode(" ", $head);
                    $code = intval($c[1]);
                }
            }
            if (($code >= 200) && ($code < 300)) {
                $return = json_decode($response, true);
                if (is_null($return) && ($response != "null")) {
                    $return = $response;
                }
            } else {
                $this->_postError = json_decode($response, true);
                $return = null;
            }

        } catch (Exception $e) {
            var_dump($e);
            //handle error here
        }
        unset($response);
        unset($params);
        unset($ctx);
        return $return;
    }

}


?>
