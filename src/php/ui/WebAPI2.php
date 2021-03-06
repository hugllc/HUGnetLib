<?php
/**
 * This file howses the socket class
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage UI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\ui;
/** This is our system class */
require_once dirname(__FILE__)."/HTML.php";
/** This is our base class */
require_once dirname(__FILE__)."/../interfaces/WebAPI.php";
/** This is our base class */
require_once dirname(__FILE__)."/../interfaces/WebAPI2.php";

/**
 * This code routes packets to their correct destinations.
 *
 * This is the router class, essentially.  It will take packets and figure out
 * which network interface to send them out.  This implements the Network layer
 * of the OSI model.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage UI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class WebAPI2 extends HTML
{
    const API_VERSION = 1.0;
    const NOT_IMPLEMENTED = 1;
    const NOT_FOUND = 2;
    const SAVE_FAILED = 3;
    const DELETE_FAILED = 4;
    const BAD_REQUEST = 5;
    /** The config we are using */
    private $_config = array();
    /** The arguments we got */
    private $_args = array();
    /** The system object */
    private $_system = null;
    /** The tells if we are read only or not */
    private $_ro = true;
    /** This is our response code */
    private $_response = 200;
    /** The tells what objects we accept */
    private $_targets = array(
        "device" => array(
            "methods" => "GET,DELETE",
            "action" => false,
            "subobjects" => array(
                "input" => array(
                    "methods" => "DELETE",
                    "action" => false,
                    "subobjects" => array(
                        "settable" => array(
                            "methods" => "PUT",
                            "action" => true,
                        ),
                    ),
                ),
                "output" => array(
                    "methods" => "DELETE",
                    "action" => false,
                    "subobjects" => array(
                        "settable" => array(
                            "methods" => "PUT",
                            "action" => true,
                        ),
                    ),
                ),
                "process" => array(
                    "methods" => "DELETE",
                    "action" => false,
                    "subobjects" => array(
                        "settable" => array(
                            "methods" => "PUT",
                            "action" => true,
                        ),
                    ),
                ),
                "power" => array(
                    "methods" => "DELETE",
                    "action" => false,
                    "subobjects" => array(
                        "settable" => array(
                            "methods" => "PUT",
                            "action" => true,
                        ),
                        "data" => array(
                            "methods" => "GET",
                            "action" => true,
                        ),
                    ),
                ),
                "annotation" => array(
                    "methods" => "GET,POST,PUT,DELETE",
                    "action" => false,
                ),
                "history" => array(
                    "methods" => "GET,POST",
                    "action" => false,
                ),
                "rawhistory" => array(
                    "methods" => "GET,POST",
                    "action" => false,
                ),
                "history" => array(
                    "methods" => "GET,POST",
                    "action" => false,
                ),
                "error" => array(
                    "methods" => "GET,POST,PUT,DELETE",
                    "action" => false,
                ),
                "config" => array(
                    "methods" => "external",
                    "action" => true,  // This is not an object, but an action
                ),
                "export" => array(
                    "methods" => "external",
                    "action" => true,  // This is not an object, but an action
                ),
                "import" => array(
                    "methods" => "external",
                    "action" => true,  // This is not an object, but an action
                ),
                "fcts" => array(
                    "methods" => "external",
                    "action" => true,  // This is not an object, but an action
                ),
                "fctsetup" => array(
                    "methods" => "external",
                    "action" => true,  // This is not an object, but an action
                ),
                "fctapply" => array(
                    "methods" => "external",
                    "action" => true,  // This is not an object, but an action
                ),
                "controlchan" => array(
                    "methods" => "external",
                    "action" => true,  // This is not an object, but an action
                ),
                "firmware" => array(
                    "methods" => "GET",
                    "action" => false,
                ),
            ),
        ),
        "datacollector" => array(
            "methods" => "GET,POST,PUT,DELETE",
            "action" => false,
            "subobjects" => array(
                "run" => array(
                    "methods" => "external",
                    "action" => true,  // This is not an object, but an action
                ),
                "checkin" => array(
                    "methods" => "external",
                    "action" => true,  // This is not an object, but an action
                ),
            ),
        ),
        "gateway" => array(
            "methods" => "GET,POST,PUT,DELETE",
            "action" => false,
        ),
        "image" => array(
            "methods" => "GET,POST,PUT,DELETE",
            "subobjects" => array(
                "reading" => array(
                    "methods" => "external",
                    "action" => true,  // This is not an object, but an action
                ),
                "insert" => array(
                    "methods" => "external",
                    "action" => true,  // This is not an object, but an action
                ),
                "image" => array(
                    "methods" => "external",
                    "action" => true,  // This is not an object, but an action
                ),
            ),
        ),
        "inputtable" => array(
            "methods" => "GET,POST,PUT,DELETE",
            "action" => false,
        ),
        "outputtable" => array(
            "methods" => "GET,POST,PUT,DELETE",
            "action" => false,
        ),
        "processtable" => array(
            "methods" => "GET,POST,PUT,DELETE",
            "action" => false,
        ),
        "powertable" => array(
            "methods" => "GET,POST,PUT,DELETE",
            "action" => false,
        ),
        "error" => array(
            "methods" => "GET,POST,PUT,DELETE",
            "action" => false,
        ),
        "time" => array(
            "methods" => "GET,PUT",
            "action" => false,
        ),
        "version" => array(
            "methods" => "GET",
            "action" => false,
        ),
        "status" => array(
            "methods" => "GET",
            "action" => false,
        ),
    );
    /** This is the id we were given */
    private $_id = null;
    /** This is the sid we were given */
    private $_sid = null;
    /** This is the object we were given */
    private $_object = null;
    /** This is the final object we are going to use */
    private $_obj = null;
    /** This is the info on the final object we are going to use */
    private $_info = null;
    /** This is the subobject we were given */
    private $_subobject = null;
    /** This is the method used */
    private $_method = "GET";
    /** This is the available methods */
    private $_methods = "GET";
    /** These are the HTTP response codes that we support */
    private $_codes = array(
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        400 => "Bad Request",
        401 => "Unauthorized",
        403 => "Forbidden",
        404 => "Not Found",
        501 => "Not Implemented",
    );
    /** These are our error messages */
    private $_errorMsg = array(
        self::BAD_REQUEST     => "This is a bad request",
        self::NOT_IMPLEMENTED => "This is not implemented at the present time",
        self::NOT_FOUND       => "The object you requested does not exist",
        self::SAVE_FAILED     => "Could not save this to the database",
        self::DELETE_FAILED   => "Could not delete this from the database",
    );

    /**
    * Creates the object
    *
    * @param array $config   The configuration to use
    * @param mixed $system   The system object to use
    * @param bool  $readonly If set to true, all write operations silently fail
    *
    * @return null
    */
    static public function &factory(
        $config = array(), $system = null, $readonly = true
    ) {
        $obj = new WebAPI2($config, $system);
        $obj->_ro = (bool)$readonly;
        return $obj;
    }

    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function __destruct()
    {
        $this->out(get_class($this)." destroying system", 3);
        // Shut down the network
        unset($this->_system);
    }
    /**
    * Set the response code for the HTTP server
    *
    * This does not support all codes.  See this->_codes for which ones are
    * supported.
    *
    * @param int $code The code to set it to.
    *
    * @return null
    */
    public function response($code = null)
    {
        if (is_int($code) && isset($this->_codes[$code])) {
            $this->_response = $code;
        }
        return $this->_response;
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    */
    public function execute($extra = array())
    {
        $ret = "";
        if (!is_null($this->args()->get("task"))) {
            include_once "WebAPI.php";
            $api = WebAPI::factory($this->args(), $this->system(), $this->_ro);
            $api->execute($extra);
        } else {
            $this->_setObject();
            $this->_setId();
            $this->_setSubobject();
            $this->_setSid();
            $this->_setMethod();
            $this->_createObj();
            $fct = "_execute".ucfirst($this->_object).ucfirst((string)$this->_subobject);
            if (method_exists($this, $fct)) {
                $ret = $this->$fct();
            } else {
                if (($this->_info["action"] || !$this->_checkMethod()) && $this->_auth(true)) {
                    $interface = "\\HUGnet\\interfaces\\WebAPI2";
                    if (is_subclass_of((object)$this->_obj, $interface)) {
                        $ret = $this->_obj->webAPI2($this, $extra);
                    } else {
                        $this->response(501);
                        $ret = $this->error(self::NOT_IMPLEMENTED);
                    }
                } else if (!$this->_checkMethod()) {
                    $this->response(501);
                    $ret = $this->error(self::NOT_IMPLEMENTED, "This method is not implemented, or does not work with this object");
                } else if ($this->_object === "version") {
                    $ret = $this->_executeVersion();
                } else if ($this->_object === "time") {
                    $ret = $this->_executeTime();
                } else if ($this->_object === "status") {
                    $ret = $this->_executeStatus();
                } else if (is_a((object)$this->_obj, '\HUGnet\base\SystemTableBase')) {
                    $ret = $this->_executeSystem();
                } else if (is_a((object)$this->_obj, '\HUGnet\db\Table')) {
                    $ret = $this->_executeTable();
                } else {
                    $ret = $this->_executeUnknown();
                }
            }
            $this->_body($ret);
        }
    }
    /**
    * Returns the id.
    *
    * @return The ID
    */
    public function id()
    {
        return $this->_id;
    }
    /**
    * Returns the id.
    *
    * @return The ID
    */
    public function sid()
    {
        return $this->_sid;
    }
    /**
    * This returns a json encoded error object
    *
    * @param mixed  $error    The errorInfo array returned from the PDO object
    * @param int    $code     The error code
    * @param string $message  The message to send with it
    * @param string $moreInfo Where to get more information
    *
    * @return bool
    */
    public function pdoerror($error, $code, $message = null, $moreInfo = "")
    {
        if (is_array($error)) {
            $this->error($error[0], $error[2], $moreInfo);
        } else {
            $this->error($code, $message, $moreInfo);
        }
    }
    /**
    * This returns a json encoded error object
    *
    * @param int    $code     The error code
    * @param string $message  The message to send with it
    * @param string $moreInfo Where to get more information
    *
    * @return bool
    */
    public function error($code, $message = null, $moreInfo = "")
    {
        if (empty($message) && isset($this->_errorMsg[$code])) {
            $message = $this->_errorMsg[$code];
        }
        $path = "/".$this->_object;
        if (!is_null($this->_id)) {
            $path .= "/".$this->_id;
        }
        if (!is_null($this->_subobject)) {
            $path .= "/".$this->_subobject;
        }
        if (!is_null($this->_sid)) {
            $path .= "/".$this->_sid;
        }
        
        error_log("WebAPI2: $path ($code) $message"); 
        if (is_object($this->_obj)) {
            $class = get_class($this->_obj);
        } else {
            $class = "None";
        }
        $data = array(
            "code" => $code,
            "message" => $message,
            "moreInfo" => "($path) ".(string)$moreInfo,
            "stack" => array(
                "method" => $this->_method,
                "object" => $this->_object,
                "id" => $this->_id,
                "subobject" => $this->_subobject,
                "sid" => $this->_sid,
                "extra" => $this->args()->get("restextra"),
                "objectClass" => $class,
                "data" => $this->args()->get("data"),
            ),
        );
        $this->_headerJSON();
        print json_encode($data, JSON_PRETTY_PRINT);
        if ($this->system()->get("verbose") > 0) {
            print PHP_EOL.implode(PHP_EOL, $this->debug());
        }
    }
    /**
    * This gets the ID of the object
    *
    * @param mixed $object The object to get the ID for
    *
    * @return bool
    */
    private function _checkMethod()
    {
        return (stripos($this->_info["methods"], $this->_method) !== false);
    }
    /**
    * This gets the ID of the object
    *
    * @param mixed $object The object to get the ID for
    *
    * @return bool
    */
    private function _setId()
    {
        if (isset($this->_targets[$this->_object])) {
            $this->_id = $this->args()->get("id");
            if (!is_null($this->_id)) {
                $this->_id = trim(strtolower($this->_id));
            }
        }
    }
    /**
    * This gets the ID of the object
    *
    * @param mixed $object The object to get the ID for
    *
    * @return bool
    */
    private function _setObject()
    {
        $object = trim(strtolower($this->args()->get("object")));
        if (isset($this->_targets[$object])) {
            $this->_object = $object;
        }
    }
    /**
    * This gets the ID of the object
    *
    * @param mixed $object The object to get the ID for
    *
    * @return bool
    */
    private function _setSid()
    {
        if (isset($this->_targets[$this->_object]) && isset($this->_targets[$this->_object]['subobjects'][$this->_subobject])) {
            $this->_sid = $this->args()->get("sid");
            if (!is_null($this->_sid)) {
                $this->_sid = trim(strtolower($this->_sid));
                if (isset($this->_targets[$this->_object]['subobjects'][$this->_subobject]["idformat"]) && ($this->_targets[$this->_object][$this->_subobject]["idformat"] == 'hex')) {
                    $this->_sid = hexdec($this->_sid);
                }
            }
        }
    }
    /**
    * This gets the ID of the object
    *
    * @param mixed $object The object to get the ID for
    *
    * @return bool
    */
    private function _setSubobject()
    {
        if (!is_null($this->_id)) {
            $object = trim(strtolower($this->args()->get("subobject")));
            if (isset($this->_targets[$this->_object]) && isset($this->_targets[$this->_object]['subobjects'][$object])) {
                $this->_subobject = $object;
            }
        }
    }
    /**
    * This gets the ID of the object
    *
    * @param mixed $object The object to get the ID for
    *
    * @return bool
    */
    private function _setMethod()
    {
        $this->_method = (string)$this->args()->get("method");
    }
    /**
    * This gets the ID of the object
    *
    * @param mixed $object The object to get the ID for
    *
    * @return bool
    */
    private function &_createObj()
    {
        if (isset($this->_targets[$this->_object]) && isset($this->_targets[$this->_object]["subobjects"][$this->_subobject])) {
            $this->_info = &$this->_targets[$this->_object]["subobjects"][$this->_subobject];
        } else if (isset($this->_targets[$this->_object])) {
            $this->_info = &$this->_targets[$this->_object];
        } else {
            $this->_info = null;
        }
        if (is_array($this->_info)) {
            if (is_string($this->_object) && is_callable(array($this->system(), $this->_object))) {
                $this->_obj = $this->system()->{$this->_object}($this->_id);
                if (is_object($this->_obj) && is_string($this->_subobject) && method_exists($this->_obj, (string)$this->_subobject) && !$this->_info["action"]) {
                    $this->_obj = $this->_obj->{$this->_subobject}($this->_sid);
                }
            }
        }
    }
    /**
    * This authenticates a user and returns true if the user has access
    *
    * @param bool $write Asking for write access
    *
    * @return bool
    */
    private function _auth($write = true)
    {
        if (!is_array($this->_info)) {
            return false;
        }
        if ($write && $this->_ro) {
            return false;
        }
        $uuid = strtolower($this->args()->get("uuid"));

        return true;
    }
    /**
    * This function executes the api call.
    *
    * @param object $obj The object to work on
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeVersion()
    {
        $ret = "";
        if ($this->_auth(false)) {
            $ret = (float)self::API_VERSION;
        }
        return $ret;
    }
    /**
    * This function executes the api call.
    *
    * @param object $obj The object to work on
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeTime()
    {
        $ret = "";
        if ($this->_auth(false)) {
            if ($this->_method == "GET") {
                $ret = $this->system()->now();
            } else {
                $this->response(501);
            }
        }
        return $ret;
    }
    /**
    * This function executes the api call.
    *
    * @param object $obj The object to work on
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeStatus()
    {
        $ret = array();
        if ($this->_auth(false)) {
            if ($this->_method == "GET") {
                $dev = $this->system()->device();
                $ret["devicesTotal"] = $dev->table()->count(
                    array(
                        "id" => array('$lt' => 0xFD0000)
                    )
                );
                $ret["devicesActive"] = $dev->table()->count(
                    array(
                        "Active" => 1,
                        "id" => array('$lt' => 0xFD0000)
                    )
                );
                $ret["systemType"] = "HUGNET";
            } else {
                $this->response(501);
            }
        }
        return $ret;
    }
    /**
    * This function executes the api call.
    *
    * @param object $obj The object to work on
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeUnknown()
    {
        $ret = "";
        // GET is the only method for unknown
        if (($this->_method === "GET") && $this->_auth(false)) {
            if ((is_null($this->_id) || (is_null($this->_sid) && !empty($this->_subobject))) && is_callable(array($this->_obj, "getList"))) {
                $ret = $this->_obj->getList($data, true);
            } else if (is_callable(array($this->_obj, "toArray"))) {
                $ret = $this->_obj->toArray(true);
            }
        }
        return $ret;
    }

    /**
    * This function executes the api call.
    *
    * @param object $obj The object to work on
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeSystem()
    {
        $ret = null;
        if ($this->_obj->isNew() && ($this->_method !== "POST")) {
            $this->response(404);
            $ret = $this->error(self::NOT_FOUND);
        } else if (($this->_method === "GET") && $this->_auth(false)) {
            if (is_null($this->_id) || (is_null($this->_sid) && !empty($this->_subobject))) {
                $where = array();
                $field = $this->_obj->table()->dateField();
                $limit = $this->args()->get("limit");
                if (!is_null($limit) && is_numeric($limit)) {
                    $$this->_obj->table()->sqlLimit = (int)$limit;
                }
                $start = $this->args()->get("start");
                if (!is_null($start) && is_numeric($start)) {
                    $this->_obj->table()->sqlStart = (int)$start;
                }
                $order = $this->args()->get("order");
                if (!is_null($order) && !empty($field)) {
                    $order = trim(strtolower($order));
                    if (($order === "asc") || ($order === "desc")) {
                        $this->_obj->table()->sqlOrderBy = $field." ".$order;
                    }
                }
                $date = array();
                $since = $this->args()->get("since");
                if (!is_null($since)) {
                    $date['$gte'] = (int)$since;
                }
                $until = $this->args()->get("until");
                if (!is_null($until)) {
                    $date['$lte'] = (int)$until;
                }
                if (!empty($date) && !empty($field)) {
                    $where[$field] = $date;
                }
                $ret = $this->_obj->getList($where, true);
            } else {
                if ($this->_obj->isNew()) {
                    $ret = new \StdClass();
                } else {
                    $ret = $this->_obj->toArray(true);
                }
            }
        } else if (($this->_method === "POST") && $this->_auth(true)) {
            $data = (array)$this->args()->get("data");
            $ret = $this->_obj->create($data);
            if ($ret) {
                $this->response(202);
                $ret = $this->_obj->toArray(true);
            } else {
                $this->response(400);
                $this->pdoerror($this->_obj->lastError(), self::SAVE_FAILED);
            }
            
        } else if (($this->_method === "PUT") && $this->_auth(true)) {
            $data = (array)$this->args()->get("data");
            $ret = (int)$this->_obj->change($data);
            if ($ret) {
                $this->response(202);
                $ret = $this->_obj->toArray(true);
            } else {
                $this->response(400);
                $this->pdoerror($this->_obj->lastError(), self::SAVE_FAILED);
            }
        } else if (($this->_method === "PATCH") && $this->_auth(true)) {
            $this->response(501);
            $this->error(self::NOT_IMPLEMENTED);
        } else if (($this->_method === "DELETE") && $this->_auth(true)) {
            $ret = (int)$this->_obj->delete();
            if ($ret) {
                $this->response(202);
            } else {
                $this->response(400);
                $this->pdoerror($this->_obj->lastError(), self::DELETE_FAILED);
            }
        }
        if (is_null($ret)) {
            $this->_obj->load($this->_obj->id());
            $ret = $this->_obj->toArray(true);
        }
        /*
        } else if ($this->_auth(true)) {
            $interface = "\\HUGnet\\interfaces\\WebAPI";
            if (is_subclass_of($obj, $interface)) {
                $obj->load($ident);
                $ret = $obj->webAPI($this->args(), $extra);
            }
        }
        */
        return $ret;
    }
    /**
    * This function executes the api call.
    *
    * @param object &$obj The object to work on
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    private function _executeTable()
    {
        return $ret;
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    private function _executeDeviceHistory($extra = array())
    {
        return $this->_executeHistory($extra);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    private function _executeHistory($extra = array())
    {
        $data = (array)$this->args()->get("data");
        if (!isset($data["convert"])) {
            $convert = 1;
        } else {
            $convert = (int)$data["convert"];
        }
        $dev  = $this->system()->device($this->_id);
        $type = strtoupper($this->_sid);
        switch ($type) {
        case "RAW":
            $hist = $this->system()->table("RawHistory");
            $convert = 0;
            break;
        case "30SEC":
        case "1MIN":
        case "5MIN":
        case "15MIN":
        case "HOURLY":
        case "DAILY":
        case "WEEKLY":
        case "MONTHLY":
        case "YEARLY":
            $hist = $dev->historyFactory(array(), false);
            break;
        default:
            $hist = $dev->historyFactory(array(), true);
            break;
        }
        $ret = null;
        if ($this->_method === "GET") {
            $ret = $this->_executeHistoryGet($hist, $convert);
        } else if (($this->_method === "POST") && $this->_auth(true)) {
            $ret = array();
            $last = 0;
            foreach ($data as $key => $row) {
                if (is_array($row) && ($row["id"] == $this->_id)
                    && isset($row["Date"])
                ) {
                    $hist->clearData();
                    $hist->fromArray($row);
                    $interval = $dev->get("PollInterval");
                    if ($interval < 60) {
                        $interval = 0;
                    }
                    // Only insert a row that doesn't exist
                    if (!$hist->exists($interval)) {
                        $ret[$key] = (int)$hist->insertRow(true);
                        if ($ret[$key]) {
                            $date = $hist->get("Date");
                            if ($date > $last) {
                                $last = $date;
                            }
                        }
                    } else {
                        $ret[$key] = 2;
                    }
                }
            }
            if ($last > 0) {
                $dev->setLocalParam("LastHistory", $last);
                $dev->store();
            }
        } else if ($action === "last") {
            $hist->sqlLimit = 1;
            $hist->sqlStart = 0;
            $hist->sqlOrderBy = "Date desc";
            $where = array("id" => $this->_id);
            if (!is_null($hist->get("Type"))) {
                $where["Type"] = $type;
            }
            $hist->selectOneInto($where);
            $ret = array();
            if (!$hist->isEmpty()) {
                $channels = $this->system()->device($this->_id)->dataChannels();
                $stuff = $hist->toArray(true);
                if ($convert) {
                    $channels->convert($stuff);
                }
                $ret[] = $stuff;
                $format = trim(strtoupper((string)$this->args()->get("format")));
                if ($format == "HTML") {
                    $ret = $this->_historyHTML($this->_id, $ret);
                } else if ($format == "CSV") {
                    $ret = $this->_historyCSV($this->_id, $ret);
                }
            }
        }
        return $ret;
    }
    /**
    * This function executes the api call.
    *
    * @param object &$hist   The history to use
    * @param bool   $convert Whether to convert the records or not
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeHistoryGet(&$hist, $convert)
    {
        $data = (array)$this->args()->get("data");
        $type = strtoupper($this->_sid);
        $extraWhere = "";
        $limit = $this->args()->get("limit");
        if (!is_null($limit) && is_numeric($limit)) {
            $hist->sqlLimit = (int)$limit;
        }
        $start = $this->args()->get("start");
        if (!is_null($start) && is_numeric($start)) {
            $hist->sqlStart = (int)$start;
        }
        $order = $this->args()->get("order");
        if (!is_null($order)) {
            $order = trim(strtolower($order));
            if (($order === "asc") || ($order === "desc")) {
                $hist->sqlOrderBy = "Date ".$order;
            }
        }
        $since = $this->args()->get("since");
        $until = $this->args()->get("until");
        if (empty($until)) {
            $until = time();
        }
        if (is_null($since)) {
            $since = $until - 1800;
        }
        $extraData = array();
        $res = $hist->getPeriod(
            (int)$since,
            (int)$until,
            $this->_id,
            (empty($type)) ? "HISTORY": $type,
            $extraWhere,
            $extraData
        );
        $ret = array();
        $channels = $this->system()->device($this->_id)->dataChannels();
        while ($res) {
            $stuff = $hist->toArray(true);
            if ($convert) {
                $channels->convert($stuff);
            }
            $ret[] = $stuff;
            $res = $hist->nextInto();
        }
        $format = trim(strtoupper((string)$this->args()->get("format")));
        if ($format == "HTML") {
            $ret = $this->_historyHTML($ret);
        } else if ($format == "CSV") {
            $ret = $this->_historyCSV($ret);
        }
        return $ret;
    }
    /**
    * Sends the headers out
    *
    * This function is not testable.  Headers can't be sent in the tests.
    *
    * @return null
    */
    private function _headerNoCache()
    {
        if (!headers_sent()) {
            // @codeCoverageIgnoreStart
            header('Cache-Control: no-cache, must-revalidate, max-age=0');
            header('Expires: Sat, 4 Apr 1998 20:00:00 GMT');
            header('Pragma: no-cache');
        }
        // @codeCoverageIgnoreEnd
    }
    /**
    * Sends the headers out
    *
    * This function is not testable.  Headers can't be sent in the tests.
    *
    * @return null
    */
    private function _headerCSV()
    {
        if (!headers_sent()) {
            // @codeCoverageIgnoreStart
            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');
            header(
                'Content-disposition: attachment;'
                .'filename=HUGnet.'.$this->args()->get("id").'.csv'
            );
        }
        // @codeCoverageIgnoreEnd
    }
    /**
    * Sends the headers out
    *
    * This function is not testable.  Headers can't be sent in the tests.
    *
    * @return null
    */
    private function _headerHTML()
    {
        if (!headers_sent()) {
            // @codeCoverageIgnoreStart
            header('Content-type: text/html');
        }
        // @codeCoverageIgnoreEnd
    }
    /**
    * Sends the headers out
    *
    * This function is not testable.  Headers can't be sent in the tests.
    *
    * @return null
    */
    private function _headerJSON()
    {
        if (!headers_sent()) {
            // @codeCoverageIgnoreStart
            header('Content-type: application/json');
        }
        // @codeCoverageIgnoreEnd
    }
    /**
    * Sends the headers out
    *
    * This function is not testable.  Headers can't be sent in the tests.
    *
    * @return null
    */
    private function _headerResponse()
    {
        if (!headers_sent()) {
            // @codeCoverageIgnoreStart
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            $header = $protocol.' '.$this->_response.' '.$this->_codes[$this->_response];
            header($header);
        }
        // @codeCoverageIgnoreEnd
    }
    /**
    * Sends the headers out
    *
    * @param array $records The history to use
    *
    * @return null
    */
    private function _historyCSV($records)
    {

        $channels = $this->system()->device($this->_id)->dataChannels();
        $chan = $channels->toArray();
        $out = "";
        $sep = ",";
        $out .= "Date";
        for ($i = 0; $i < count($chan); $i++) {
            if ($chan[$i]["dataType"] !== 'ignore') {
                $out .= $sep.$chan[$i]['label'];
                $out .= " (".html_entity_decode($chan[$i]['units']).")";
                $sep = ",";
            }
        }
        $out .= "\r\n";
        $sep  = ",";
        foreach ($records as $hist) {
            $out .= gmdate("Y-m-d H:i:s", $hist["Date"]);
            for ($i = 0; $i < count($chan); $i++) {
                if ($chan[$i]["dataType"] !== 'ignore') {
                    $data = $hist["Data".$i];
                    $out .= $sep.$data;
                    $sep = ",";
                }
            }
            $out .= "\r\n";
        }
        return utf8_encode($out);
    }
    /**
    * Sends the headers out
    *
    * @param array $array The array to make into an HTML table
    *
    * @return null
    */
    private function _arrayHTML($array)
    {
        $out = "<!DOCTYPE html>\r\n<html>\r\n<body><table>\r\n";
        if (!is_array($array[0])) {
            $out .= $this->_arrayHTMLHeader($array);
            $out .= $this->_arrayHTMLRow($array);
        } else {
            $out .= $this->_arrayHTMLHeader($array[0]);
            foreach ($array as $row) {
                $out .= $this->_arrayHTMLRow($row);
            }
        }
        $out .= "</table></body>\r\n</html>";
        return $out;
    }
    /**
    * Sends the headers out
    *
    * @param array $array The array to make into an HTML table
    *
    * @return null
    */
    private function _arrayHTMLRow($array)
    {
        $out = "<tr>";
        foreach ((array)$array as $data) {
            $out .= "<td>".$data."</td>";
        }
        $out .= "</tr>\r\n";
        return $out;
    }
    /**
    * Sends the headers out
    *
    * @param array $array The array to make into an HTML table
    *
    * @return null
    */
    private function _arrayHTMLHeader($array)
    {
        $out = "<tr>";
        foreach (array_keys((array)$array) as $data) {
            $out .= "<th>".$data."</th>";
        }
        $out .= "</tr>\r\n";
        return $out;
    }
    /**
    * Sends the headers out
    *
    * @param array $records The history to use
    *
    * @return null
    */
    private function _historyHTML($records)
    {

        $channels = $this->system()->device($this->_id)->dataChannels();
        $chan = $channels->toArray();
        $out   = "<!DOCTYPE html>\r\n<html>\r\n<body><table>\r\n";
        $out  .= "<tr>";
        $out  .= "<th>Date</th>";
        $type = false;
        if (isset($records[0]["Type"])) {
            $type = true;
            $out .= "<th>Type</th>";
        }
        for ($i = 0; $i < count($chan); $i++) {
            if ($chan[$i]["dataType"] !== 'ignore') {
                $out .= "<th>";
                $out .= $chan[$i]['label'];
                $out .= " (".$chan[$i]['units'].")";
                $out .= "</th>";
            }
        }
        $out .= "</tr>\r\n";
        foreach ($records as $hist) {
            $out .= "<tr>";
            $out .= "<td>".gmdate("Y-m-d H:i:s", $hist["Date"])."</td>";
            if ($type) {
                $out .= "<td>".$hist["Type"]."</td>";
            }
            for ($i = 0; $i < count($chan); $i++) {
                if ($chan[$i]["dataType"] !== 'ignore') {
                    $data = $hist["Data".$i];
                    $out .= "<td>".$data."</td>";
                }
            }
            $out .= "</tr>\r\n";
        }
        $out .= "</table></body>\r\n</html>";
        return $out;
    }
    /**
    * Disconnects from the database
    *
    * @param string $data The data to print out.
    *
    * @return null
    */
    private function _body($data)
    {
        $this->_headerResponse();
        if ($this->_response < 400) {
            $format = trim($this->args()->get("format"));
            if ($format === "text/html") {
                $this->_headerNoCache();
                $this->_headerHTML();
                if (is_array($data)) {
                    $data = $this->_arrayHTML($data);
                }
                print $data;
            } else if ($format === "text/csv") {
                $this->_headerNoCache();
                $this->_headerCSV();
                print $data;
            } else {
                $this->_headerNoCache();
                $this->_headerJSON();
                if (!is_null($data)) {
                    print json_encode($data, JSON_PRETTY_PRINT);
                }
            }
            if ($this->system()->get("verbose") > 0) {
                print PHP_EOL.implode(PHP_EOL, $this->debug());
            }
        }
    }
}
?>
