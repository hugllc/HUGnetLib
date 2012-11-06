<?php
/**
 * This file howses the socket class
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\ui;
/** This is our system class */
require_once dirname(__FILE__)."/HTML.php";

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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class WebAPI extends HTML
{
    /** The config we are using */
    private $_config = array();
    /** The arguments we got */
    private $_args = array();
    /** The system object */
    private $_system = null;

    /**
    * Creates the object
    *
    * @param array &$config The configuration to use
    * @param mixed &$system The system object to use
    *
    * @return null
    */
    static public function &factory(&$config = array(), &$system = null)
    {
        $obj = new WebAPI($config, $system);
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
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    */
    public function execute($extra = array())
    {
        $task = (string)$this->args()->get("task");
        $method = "_execute".ucfirst(strtolower($task));
        if (method_exists($this, $method)) {
            $ret = $this->{$method}($extra);
        }
        $this->_body($ret);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    */
    private function _executeDevice($extra = array())
    {
        $did = hexdec($this->args()->get("id"));
        $dev = &$this->system()->device();
        return $this->_executeSystem($did, $dev, $extra);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    */
    private function _executeInputtable($extra = array())
    {
        $iid = (int)$this->args()->get("id");
        $table = &$this->system()->table("InputTable");
        return $this->_executeTable($iid, $table, $extra);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    */
    private function _executeSensor($extra = array())
    {
        $ids = explode(".", $this->args()->get("id"));
        $did = hexdec($ids[0]);
        $sid = (int)$ids[1];
        $ident = array("dev" => $did, "sensor" => $sid);
        $action = strtolower(trim($this->args()->get("action")));
        if ($action === "list") {
            $this->args()->set("data", array("dev" => $did));
        }
        $sen = &$this->system()->device($did)->sensor($sid);
        return $this->_executeSystem($ident, $sen, $extra);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    */
    private function _executeDatacollector($extra = array())
    {
        $uuid = strtolower($this->args()->get("id"));
        $datacol = &$this->system()->datacollector();
        return $this->_executeSystem($uuid, $datacol, $extra);
    }
    /**
    * This function executes the api call.
    *
    * @param mixed  $ident The ID to use
    * @param object $obj   The object to work on
    * @param array  $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    */
    private function _executeSystem($ident, $obj, $extra = array())
    {
        $ret = null;
        $action = strtolower(trim($this->args()->get("action")));
        if ($action === "get") {
            if ($obj->load($ident)) {
                $ret = $obj->toArray(true);
            }
        } else if ($action === "put") {
            $data = (array)$this->args()->get("data");
            if ($obj->load($ident)) {
                $obj->change($data);
                // Reload it, so that we get what is in the database
            } else {
                $obj->load($data);
                $obj->store($data);
            }
            $obj->load($ident);
            $ret = $obj->toArray(true);
        } else if ($action === "list") {
            $data = $this->args()->get("data");
            $ret = $obj->getList($data, true);
        } else {
            if (is_callable(array($obj, "webAPI"))) {
                $ret = $obj->webAPI($this->args(), $extra);
            }
        }
        return $ret;
    }
    /**
    * This function executes the api call.
    *
    * @param mixed  $ident The ID to use
    * @param object &$obj  The object to work on
    * @param array  $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    */
    private function _executeTable($ident, &$obj, $extra = array())
    {
        $ret = null;
        $action = strtolower(trim($this->args()->get("action")));
        if ($action === "get") {
            $obj->getRow($ident);
            if (!$obj->isEmpty()) {
                $ret = $obj->toArray(true);
            }
        } else if ($action === "put") {
            $data = (array)$this->args()->get("data");
            if ($obj->getRow($ident)) {
                $obj->fromAny($data);
                $obj->updateRow();
                // Reload it, so that we get what is in the database
                $obj->getRow($ident);
                $ret = $obj->toArray(true);
            }
        } else if ($action === "list") {
            $ret = $this->_executeTableList($ident, $obj);
        } else {
            if (is_callable(array($obj, "webAPI"))) {
                $ret = $obj->webAPI($this->args(), $extra);
            }
        }
        return $ret;
    }
    /**
    * This function executes the api call.
    *
    * @param mixed  $ident The ID to use
    * @param object &$obj  The object to work on
    *
    * @return null
    */
    private function _executeTableList($ident, &$obj)
    {
        $data = (array)$this->args()->get("data");
        $where = $obj->sanitizeWhere($data);
        $whereText = "";
        $whereData = array();
        if (is_array($where) && !empty($where)) {
            $sep       = "";
            foreach ($where as $key => $value) {
                $whereText .= $sep."`$key` = ?";
                $sep = " AND ";
                $whereData[] = $value;
            }
        } else {
            $whereText = "1";
        }
        $ret = array();
        foreach ((array)$obj->select($whereText, $whereData) as $row) {
            $ret[] = $row->toArray(true);
        }
        return $ret;
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    */
    private function _executeHistory($extra = array())
    {
        $did = hexdec($this->args()->get("id"));
        $hist = &$this->system()->device($did)->historyFactory(array(), true);
        $ret = null;
        $action = strtolower(trim($this->args()->get("action")));
        if ($action === "get") {
            $ret = $this->_executeHistoryGet($did, $hist);
        } else if ($action === "put") {
            $data = (array)$this->args()->get("data");
            $ret = array();
            foreach ($data as $key => $row) {
                if (is_array($row) && ($row["id"] == $did)
                    && isset($row["Date"])
                ) {
                    $hist->clearData();
                    $hist->fromArray($row);
                    $ret[$key] = (int)$hist->insertRow(true);
                }
            }
        } else if ($action === "last") {
            $hist->sqlLimit = 1;
            $hist->sqlStart = 0;
            $hist->sqlOrderBy = "Date desc";
            $whereText = "`id` = ?";
            $whereData = array($did);
            $hist->selectOneInto($whereText, $whereData);
            if (!$hist->isEmpty()) {
                $ret = $hist->toArray(true);
            }
        }
        return $ret;
    }
    /**
    * This function executes the api call.
    *
    * @param int    $did   The deviceID to use
    * @param object &$hist The history to use
    *
    * @return null
    */
    private function _executeHistoryGet($did, &$hist)
    {
        $data = (array)$this->args()->get("data");
        $extraWhere = "";
        if (isset($data["limit"]) && is_numeric($data["limit"])) {
            $hist->sqlLimit = (int)$data["limit"];
        }
        if (isset($data["start"]) && is_numeric($data["start"])) {
            $hist->sqlStart = (int)$data["start"];
        }
        if (isset($data["order"])) {
            $order = trim(strtolower($data["order"]));
            if (($order === "asc") || ($order === "desc")) {
                $hist->sqlOrderBy = "Date ".$order;
            }
        }
        $extraData = array();
        $res = $hist->getPeriod(
            (int)$data["since"],
            (int)$data["until"],
            $did,
            "history",
            $extraWhere,
            $extraData
        );
        $ret = array();
        while ($res) {
            $ret[] = $hist->toArray(true);
            $res = $hist->nextInto();
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
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 4 Apr 1998 20:00:00 GMT');
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
            header('Content-type: text/csv');
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
    private function _headerJSON()
    {
        if (!headers_sent()) {
            // @codeCoverageIgnoreStart
            header('Content-type: application/json');
        }
        // @codeCoverageIgnoreEnd
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
        $format = trim($this->args()->get("format"));
        if (strtoupper($format) === "CSV") {
            $this->_headerNoCache();
            $this->_headerCSV();
            print $data;
        } else {
            $this->_headerNoCache();
            $this->_headerJSON();
            if (!is_null($data)) {
                print json_encode($data);
            }
        }
        if ($this->system()->get("verbose") > 0) {
            print PHP_EOL.implode(PHP_EOL, $this->debug());
        }
    }
}
?>
