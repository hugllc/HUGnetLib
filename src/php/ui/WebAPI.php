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
 * @version    Release: 0.14.8
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
    private function _executeFirmware($extra = array())
    {
        $ret = array();
        $firmware = $this->system()->table("firmware");
        $action = strtolower(trim($this->args()->get("action")));
        if ($action == "list") {
            $ret = $this->_executeTableList($ident, $firmware);
            foreach ($ret as &$value) {
                unset($value["Code"]);
                unset($value["Data"]);
                unset($value["CodeHash"]);
                unset($value["DataHash"]);
            }
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
    private function _executeDevice($extra = array())
    {
        $did = hexdec($this->args()->get("id"));
        $dev = $this->system()->device($did);
        $action = strtolower(trim($this->args()->get("action")));
        if ($action == "get") {
            $ret = $this->_executeSystem($did, $dev, $extra);
        } else if ($this->_auth(true) || ($action == "list")) {
            //$dev->load($did);
            if ($action == "import") {
                if (file_exists($_FILES["import"]["tmp_name"])) {
                    $data = json_decode(
                        file_get_contents($_FILES["import"]["tmp_name"]),
                        true
                    );
                    $this->args()->set("data", $data);
                }
            }
            $ret = $dev->webAPI($this->args(), $extra);
        }
        if ($ret === "regen") {
            return $this->system()->device($did)->toArray(true);
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
    private function _executeInputtable($extra = array())
    {
        $iid = $this->args()->get("id");
        $inputTable = $this->system()->inputTable($iid);
        $ret = $this->_executeSystem($iid, $inputTable, $extra);
        if ($ret === "regen") {
            return $this->system()->inputTable($iid)->toArray(true);
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
        $outputTable = $this->system()->outputTable($iid);
        $ret = $this->_executeSystem($iid, $outputTable, $extra);
        if ($ret === "regen") {
            return $this->system()->outputTable($iid)->toArray(true);
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
    private function _executeProcesstable($extra = array())
    {
        $iid = $this->args()->get("id");
        $processTable = $this->system()->processTable($iid);
        $ret = $this->_executeSystem($iid, $processTable, $extra);
        if ($ret === "regen") {
            return $this->system()->processTable($iid)->toArray(true);
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
    private function _executeAnnotation($extra = array())
    {
        $aid = (int)$this->args()->get("id");
        if ($aid == 0) {
            $aid = null;
        }
        $table = $this->system()->annotation();
        $action = strtolower(trim($this->args()->get("action")));
        if ($action == "list") {
            $ret = $table->webAPI($this->args(), $extra);
        } else {
            $data = (array)$this->args()->get("data");
            $data["date"] = time();
            unset($data["id"]);
            $this->args()->set("data", $data);
            $ret = $this->_executeSystem($aid, $table, $extra);
        }
        if (($aid == 0) && ($action == "put")) {
            $data = (array)$this->args()->get("data");
            return $this->system()->annotation($data)->toArray(true);
        } else if ($ret === "regen") {
            return $this->system()->annotation($aid)->toArray(true);
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
    private function _executeTests($extra = array())
    {
        $tid = (int)$this->args()->get("id");
        $test = $this->system()->test($tid);
        $ret = $this->_executeSystem($tid, $test, $extra);
        if ($ret === "regen") {
            return $this->system()->test($tid)->toArray(true);
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
    private function _executeDeviceInput($extra = array())
    {
        $ids = explode(".", $this->args()->get("id"));
        $did = hexdec($ids[0]);
        $sid = $this->args()->get("sid");
        if (empty($sid)) {
            $sid = (int)$ids[1];
        }
        $ident = array("dev" => $did, "input" => $sid);
        $action = strtolower(trim($this->args()->get("action")));
        $sen = $this->system()->device($did)->input($sid);
        if ($action === "list") {
            $this->args()->set("data", array("dev" => $did));
        } else if ($action == "put") {
            $ret = $sen->webAPI($this->args(), $extra);
        }
        $ret = $this->_executeSystem($ident, $sen, $extra);
        if ($ret === "regen") {
            return $this->system()->device($did)->input($sid)->toArray(true);
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
    private function _executeDeviceControlChan($extra = array())
    {
        $ids = explode(".", $this->args()->get("id"));
        $did = hexdec($ids[0]);
        $chan = $this->args()->get("sid");
        if (empty($sid)) {
            $chan = (int)$ids[1];
        }
        $action = strtolower(trim($this->args()->get("action")));
        if ($action == "get") {
            $pkt = $this->system()->device($did)->action()->send(
                array(
                    "Command" => '0x65',
                    "Data" => sprintf("%02X", $chan),
                )
            );
            if (is_object($pkt)) {
                $reply = $pkt->reply();
                $data = 0;
                for ($i = 0; $i < 4; $i++) {
                    $data += hexdec(substr($reply, ($i * 2), 2))<<($i * 8);
                }
                return $data;
            }
        } else if ($action == "set") {
            $data = (int)$this->args()->get("data");
            $datastr = sprintf("%08X", $data);
            for ($i = 6; $i >= 0; $i-=2) {
                $value .= substr($datastr, $i, 2);
            }
            $pkt = $this->system()->device($did)->action()->send(
                array(
                    "Command" => '0x64',
                    "Data" => sprintf("%02X", $chan).$value,
                )
            );
            if ($pkt->reply() == $value) {
                return $data;
            }
        }
        return null;
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
        $sid = $this->args()->get("sid");
        if (empty($sid)) {
            $sid = (int)$ids[1];
        }
        $ident = array("dev" => $did, "output" => $sid);
        $action = strtolower(trim($this->args()->get("action")));
        $sen = $this->system()->device($did)->output($sid);
        if ($action === "list") {
            $this->args()->set("data", array("dev" => $did));
        } else if ($action == "put") {
            $ret = $sen->webAPI($this->args(), $extra);
        }
        $ret = $this->_executeSystem($ident, $sen, $extra);
        if ($ret === "regen") {
            return $this->system()->device($did)->output($sid)->toArray(true);
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
    private function _executeDeviceProcess($extra = array())
    {
        $ids = explode(".", $this->args()->get("id"));
        $did = hexdec($ids[0]);
        $sid = $this->args()->get("sid");
        if (empty($sid)) {
            $sid = (int)$ids[1];
        }
        $ident = array("dev" => $did, "process" => $sid);
        $action = strtolower(trim($this->args()->get("action")));
        $sen = $this->system()->device($did)->process($sid);
        if ($action === "list") {
            $this->args()->set("data", array("dev" => $did));
        } else if ($action == "put") {
            $ret = $sen->webAPI($this->args(), $extra);
        }
        $ret = $this->_executeSystem($ident, $sen, $extra);
        if ($ret === "regen") {
            return $this->system()->device($did)->process($sid)->toArray(true);
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
    private function _executeDevicePower($extra = array())
    {
        $ids = explode(".", $this->args()->get("id"));
        $did = hexdec($ids[0]);
        $sid = $this->args()->get("sid");
        if (empty($sid)) {
            $sid = (int)$ids[1];
        }
        $ident = array("dev" => $did, "power" => $sid);
        $action = strtolower(trim($this->args()->get("action")));
        $sen = $this->system()->device($did)->power($sid);
        if ($action === "list") {
            $this->args()->set("data", array("dev" => $did));
        } else if ($action == "put") {
            $ret = $sen->webAPI($this->args(), $extra);
        }
        $ret = $this->_executeSystem($ident, $sen, $extra);
        if ($ret === "regen") {
            return $this->system()->device($did)->power($sid)->toArray(true);
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
    private function _executeDatacollector($extra = array())
    {
        $uuid = strtolower($this->args()->get("id"));
        $datacol = $this->system()->datacollector($uuid);
        $ret = $this->_executeSystem($uuid, $datacol, $extra);
        if ($ret === "regen") {
            return $this->system()->datacollector($uuid)->toArray(true);
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
    private function _executeGateway($extra = array())
    {
        $gid     = strtolower($this->args()->get("id"));
        $gateway = $this->system()->gateway($gid);
        $ret     = $this->_executeSystem($gid, $gateway, $extra);
        if ($ret === "regen") {
            return $this->system()->gateway($gid)->toArray(true);
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
    private function _executeImage($extra = array())
    {
        $iid    = $this->args()->get("id");
        $image  = $this->system()->image($iid);
        $action = strtolower(trim($this->args()->get("action")));
        if ($this->_auth(true) 
            || ($action == "list") 
            || ($action == "get") 
            || ($action == "getreading")
        ) {
            $ret = $image->webAPI($this->args(), $extra);
        }
        if ($ret === "regen") {
            return $this->system()->image($iid)->toArray(true);
        }
        return $ret;
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
            $ret = $obj->toArray(true);
        } else if (($action === "put") && $this->_auth(true)) {
            $data = (array)$this->args()->get("data");
            $ret = "regen";
            if ($obj->load($ident)) {
                $obj->change($data);
            } else if (is_null($ident) && isset($data["name"])) {
                $obj->create($data);
                // We don't want to regen this one.  $ident is wrong.
                $ret = $obj->toArray(true);
            } else {
                $obj->load($data);
                $obj->store(true);
            }
        } else if ($action === "list") {
            $data = $this->args()->get("data");
            $ret = $obj->getList($data, true);
        } else if ($this->_auth(true)) {
            $interface = "\\HUGnet\\interfaces\\WebAPI";
            if (is_subclass_of($obj, $interface)) {
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
