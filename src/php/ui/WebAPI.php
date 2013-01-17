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
    /** The tells if we are read only or not */
    private $_ro = true;

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
        $task = (string)$this->args()->get("task");
        $method = "_execute".ucfirst(strtolower($task));
        if (method_exists($this, $method)) {
            $ret = $this->{$method}($extra);
        }
        $this->_body($ret);
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
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeDevice($extra = array())
    {
        $did = hexdec($this->args()->get("id"));
        $dev = $this->system()->device();
        $action = strtolower(trim($this->args()->get("action")));
        if (($action === "list") || ($action == "get")) {
            return $this->_executeSystem($did, $dev, $extra);
        } else if ($this->_auth(true)) {
            $dev->load($did);
            return $dev->webAPI($this->args(), $extra);
        }
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeInputtable($extra = array())
    {
        $iid = $this->args()->get("id");
        if (!is_null($iid)) {
            $iid = (int)$iid;
        }
        $table = $this->system()->table("InputTable");
        $ret = $this->_executeTable($iid, $table, $extra);
        $action = strtolower(trim($this->args()->get("action")));
        $fle = dirname(__FILE__)."/../devices/inputTable/tables/ADuCInputTable.php";
        include_once $fle;
        $table = \HUGnet\devices\inputTable\tables\ADuCInputTable::factory(array());
        if ($action == "list") {
            foreach (array_keys($ret) as $key) {
                $table->fromArray($ret[$key]);
                $ret[$key]["params"] = $table->fullArray();
            }
        } else {
            $table->fromArray($ret);
            $ret["params"] = $table->fullArray();
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
    */
    private function _executeOutputtable($extra = array())
    {
        $iid = $this->args()->get("id");
        if (!is_null($iid)) {
            $iid = (int)$iid;
        }
        $table = $this->system()->table("OutputTable");
        return $this->_executeTable($iid, $table, $extra);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeProcesstable($extra = array())
    {
        $iid = $this->args()->get("id");
        if (!is_null($iid)) {
            $iid = (int)$iid;
        }
        $table = $this->system()->table("ProcessTable");
        return $this->_executeTable($iid, $table, $extra);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeAnnotation($extra = array())
    {
        $aid = (int)$this->args()->get("id");
        $table = $this->system()->table("Annotations");
        return $this->_executeTable($aid, $table, $extra);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeTests($extra = array())
    {
        $tid = (int)$this->args()->get("id");
        $test = $this->system()->test();
        return $this->_executeSystem($tid, $test, $extra);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeDeviceInput($extra = array())
    {
        $ids = explode(".", $this->args()->get("id"));
        $did = hexdec($ids[0]);
        $sid = (int)$ids[1];
        $ident = array("dev" => $did, "input" => $sid);
        $action = strtolower(trim($this->args()->get("action")));
        if ($action === "list") {
            $this->args()->set("data", array("dev" => $did));
        }
        $sen = $this->system()->device($did)->input($sid);
        return $this->_executeSystem($ident, $sen, $extra);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeDeviceOutput($extra = array())
    {
        $ids = explode(".", $this->args()->get("id"));
        $did = hexdec($ids[0]);
        $sid = (int)$ids[1];
        $ident = array("dev" => $did, "output" => $sid);
        $action = strtolower(trim($this->args()->get("action")));
        if ($action === "list") {
            $this->args()->set("data", array("dev" => $did));
        }
        $sen = $this->system()->device($did)->output($sid);
        return $this->_executeSystem($ident, $sen, $extra);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeDeviceProcess($extra = array())
    {
        $ids = explode(".", $this->args()->get("id"));
        $did = hexdec($ids[0]);
        $sid = (int)$ids[1];
        $ident = array("dev" => $did, "process" => $sid);
        $action = strtolower(trim($this->args()->get("action")));
        if ($action === "list") {
            $this->args()->set("data", array("dev" => $did));
        }
        $sen = $this->system()->device($did)->process($sid);
        return $this->_executeSystem($ident, $sen, $extra);
    }
    /**
    * This function executes the api call.
    *
    * @param array $extra Extra data that should be added to the HTMLArgs data
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeDatacollector($extra = array())
    {
        $uuid = strtolower($this->args()->get("id"));
        $datacol = $this->system()->datacollector();
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
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    */
    private function _executeSystem($ident, $obj, $extra = array())
    {
        $ret = null;
        $action = strtolower(trim($this->args()->get("action")));
        if ($action === "get") {
            if ($obj->load($ident)) {
                $ret = $obj->toArray(true);
            }
        } else if (($action === "put") && $this->_auth(true)) {
            $data = (array)$this->args()->get("data");
            if ($obj->load($ident)) {
                $obj->change($data);
                // Reload it, so that we get what is in the database
            } else {
                $obj->load($data);
                $obj->store();
            }
            $obj->load($ident);
            $ret = $obj->toArray(true);
        } else if ($action === "list") {
            $data = $this->args()->get("data");
            $ret = $obj->getList($data, true);
        } else if ($this->_auth(true)) {
            if (is_callable(array($obj, "webAPI"))) {
                $obj->load($ident);
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
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
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
        } else if (($action === "put") && $this->_auth(true)) {
            $data = (array)$this->args()->get("data");
            if (is_null($ident) && isset($data["name"])) {
                $obj->clearData();
                $obj->set("id", null);
                $obj->set("name", $data["name"]);
                if ($obj->insertRow()) {
                    $obj->sqlOrderBy = "id desc";
                    $obj->selectOneInto("name = ?", $data["name"]);
                    $ret = $obj->toArray();
                }
            } else if ($obj->getRow($ident)) {
                $obj->fromAny($data);
                $obj->updateRow();
                // Reload it, so that we get what is in the database
                $obj->getRow($ident);
                $ret = $obj->toArray(true);
            }
        } else if ($action === "list") {
            $ret = $this->_executeTableList($ident, $obj);
        } else if ($this->_auth(true)) {
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
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    private function _executeHistory($extra = array())
    {
        $did     = hexdec($this->args()->get("id"));
        $data    = (array)$this->args()->get("data");
        $type    = trim(strtoupper($data["type"]));
        switch ($type) {
        case "RAW":
            $hist = $this->system()->table("RawHistory");
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
            $hist = $this->system()->device($did)->historyFactory(array(), false);
            break;
        default:
            $hist = $this->system()->device($did)->historyFactory(array(), true);
            break;
        }
        $ret = null;
        $action = strtolower(trim($this->args()->get("action")));
        if ($action === "get") {
            $ret = $this->_executeHistoryGet($did, $hist);
        } else if (($action === "put") && $this->_auth(true)) {
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
            if (!is_null($hist->get("Type"))) {
                $whereText .= " AND Type = ?";
                $whereData[] = $type;
            }
            $hist->selectOneInto($whereText, $whereData);
            $ret = array();
            if (!$hist->isEmpty()) {
                $channels = $this->system()->device($did)->dataChannels();
                $stuff = $hist->toArray(true);
                if (trim(strtolower($data["type"])) != "raw") {
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
    * @param int    $did   The deviceID to use
    * @param object &$hist The history to use
    *
    * @return null
    * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
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
            if (trim(strtolower($data["type"])) != "raw") {
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
            $out .= date("Y-m-d H:i:s", $hist["Date"]);
            for ($i = 0; $i < count($chan); $i++) {
                if ($chan[$i]["dataType"] !== 'ignore') {
                    $data = $hist["Data".$i];
                    $out .= $sep.$data;
                    $sep = ",";
                }
            }
            $out .= "\r\n";
        }
        return $out;
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
            $out .= "<td>".date("Y-m-d H:i:s", $hist["Date"])."</td>";
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
