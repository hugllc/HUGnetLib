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
class WebAPI extends HTML
{
    /** The config we are using */
    private $_config = array();
    /** The arguments we got */
    private $_args = array();
    /** The system object */
    private $_system = null;
    /** The tells if we are read only or not */
    private $_ro = true;
    /** The tells what objects we accept */
    private $_targets = array(
        "device" => array(
            "methods" => "GET,POST,PUT,PATCH,DELETE",
            "input" => "GET,POST,PUT,PATCH,DELETE",
            "output" => "GET,POST,PUT,PATCH,DELETE",
            "process" => "GET,POST,PUT,PATCH,DELETE",
            "annotation" => "GET,POST,PUT,PATCH,DELETE",
            "power" => "GET,POST,PUT,PATCH,DELETE",
            "history" => "GET,POST",
            "rawhistory" => "GET,POST",
            "history" => "GET,POST",
        ),
        "datacollector" => "GET,POST,PUT,PATCH,DELETE",
        "errorlog" => "GET,POST,PUT,PATCH,DELETE",
    );
    /** This is the id we were given */
    private $_id = null;
    /** This is the sid we were given */
    private $_sid = null;
    /** This is the object we were given */
    private $_object = null;
    /** This is the subobject we were given */
    private $_subobject = null;
    /** This is the method used */
    private $_method = "GET";

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
        $obj = new WebAPI($config, $system);
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
            include_once "WebAPIOld.php";
            $api = WebAPIOld::factory($this->args(), $this->system(), $this->_ro);
            $api->execute($extra);
        } else {
            $object = strtolower($this->args()->get("object"));
            $id     = $this->args()->get("id");
            if (isset($this->_targets[$object])) {
                $this->_object = $object;
                $this->_id     = $id;
                $this->_method = $this->args()->get("method");
                $obj = $this->system()->$object($id);
                $sid     = $this->args()->get("sid");
                $subobject = strtolower($this->args()->get("subobject"));
                $methods = "GET";
                if (is_array($this->_targets[$object]) && isset($this->_targets[$object][$subobject])) {
                    $this->_sid = $sid;
                    $this->_subobject = $subobject;
                    $obj = $obj->$subobject($sid);
                    if (is_string($this->_targets[$object][$subobject])) {
                        $methods = $this->_targets[$object][$subobject];
                    }
                } else if (is_string($this->_targets[$object])) {
                    $methods = $this->_targets[$object];
                } else if (is_string($this->_targets[$object]["methods"])) {
                    $methods = $this->_targets[$object]["methods"];
                }
            }
            if (stripos($methods, $this->_method) !== false) {
                $fct = "_execute".ucfirst($object);
                if (method_exists($this, $fct)) {
                    $ret = $this->$fct($obj, $methods);
                } else {
                    $ret = $this->_executeSystem($obj, $methods);
                }
            }
            $this->_body($ret);
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
        if ($write && $this->_ro) {
            return false;
        }
        $uuid = strtolower($this->args()->get("uuid"));

        return true;
    }
    /**
    * This function executes the api call.
    *
    * @param mixed  $ident The ID to use
    * @param object $obj   The object to work on
    * @param array  $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeSystem($obj, $methods)
    {
        $ret = null;
        if (($this->_method === "GET") && $this->_auth(false)) {
            if (is_null($this->_id) || (is_null($this->_sid) && !empty($this->_subobject))) {
                $ret = $obj->getList($data, true);
            } else {
                $ret = $obj->toArray(true);
            }
        } else if (($action === "POST") && $this->_auth(true)) {
            $data = (array)$this->args()->get("data");
            $obj->create($data);
        } else if (($action === "PUT") && $this->_auth(true)) {
            $data = (array)$this->args()->get("data");
            $obj->change($data);
        } else if (($action === "PATCH") && $this->_auth(true)) {
            $data = (array)$this->args()->get("data");
            $obj->change($data);
        }
        if (is_null($ret)) {
            $obj->load($obj->id());
            $ret = $obj->toArray(true);
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
    * @param mixed  $ident The ID to use
    * @param object &$obj  The object to work on
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    private function _executeTableList($ident, &$obj)
    {
        $data = (array)$this->args()->get("data");
        $where = $obj->sanitizeWhere($data);
        if (!is_array($where) || empty($where)) {
            $where = array();
        }
        $ret = array();
        foreach ((array)$obj->select($where) as $row) {
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
        $did  = hexdec($this->args()->get("id"));
        $data = (array)$this->args()->get("data");
        $type = trim(strtoupper($data["type"]));
        if (!isset($data["convert"])) {
            $convert = 1;
        } else {
            $convert = (int)$data["convert"];
        }
        $dev  = $this->system()->device($did);
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
        $action = strtolower(trim($this->args()->get("action")));
        if ($action === "get") {
            $ret = $this->_executeHistoryGet($did, $hist, $convert);
        } else if (($action === "put") && $this->_auth(true)) {
            $ret = array();
            $last = 0;
            foreach ($data as $key => $row) {
                if (is_array($row) && ($row["id"] == $did)
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
            $where = array("id" => $did);
            if (!is_null($hist->get("Type"))) {
                $where["Type"] = $type;
            }
            $hist->selectOneInto($where);
            $ret = array();
            if (!$hist->isEmpty()) {
                $channels = $this->system()->device($did)->dataChannels();
                $stuff = $hist->toArray(true);
                if ($convert) {
                    $channels->convert($stuff);
                }
                $ret[] = $stuff;
                $format = trim(strtoupper((string)$this->args()->get("format")));
                if ($format == "HTML") {
                    $ret = $this->_historyHTML($did, $ret);
                } else if ($format == "CSV") {
                    $ret = $this->_historyCSV($did, $ret);
                }
            }
        }
        return $ret;
    }
    /**
    * This function executes the api call.
    *
    * @param int    $did     The deviceID to use
    * @param object &$hist   The history to use
    * @param bool   $convert Whether to convert the records or not
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeHistoryGet($did, &$hist, $convert)
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
        $type = trim(strtoupper($data["type"]));
        $extraData = array();
        $res = $hist->getPeriod(
            (int)$data["since"],
            (int)$data["until"],
            $did,
            (empty($type)) ? "history": $type,
            $extraWhere,
            $extraData
        );
        $ret = array();
        $channels = $this->system()->device($did)->dataChannels();
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
            $ret = $this->_historyHTML($did, $ret);
        } else if ($format == "CSV") {
            $ret = $this->_historyCSV($did, $ret);
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
    * @param int   $did     The deviceID to use
    * @param array $records The history to use
    *
    * @return null
    */
    private function _historyCSV($did, $records)
    {

        $channels = $this->system()->device($did)->dataChannels();
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
    * @param int   $did     The deviceID to use
    * @param array $records The history to use
    *
    * @return null
    */
    private function _historyHTML($did, $records)
    {

        $channels = $this->system()->device($did)->dataChannels();
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
        $format = trim($this->args()->get("format"));
        if (strtoupper($format) === "HTML") {
            $this->_headerNoCache();
            $this->_headerHTML();
            if (is_array($data)) {
                $data = $this->_arrayHTML($data);
            }
            print $data;
        } else if (strtoupper($format) === "CSV") {
            $this->_headerNoCache();
            $this->_headerCSV();
            print $data;
        } else if (strtoupper($format) === "DEBUG") {
            if (!headers_sent()) {
                // @codeCoverageIgnoreStart
                header('Content-type: text/plain; charset=UTF-8');
            }
            // @codeCoverageIgnoreEnd
            $this->_headerNoCache();
            var_dump($data);
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
