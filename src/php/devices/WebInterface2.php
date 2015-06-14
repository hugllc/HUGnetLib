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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * Networking for devices.
 *
 * This class will do all of the networking for devices.  It will poll, get configs,
 * update software, and anything else related to talking to devices.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.5
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.14.5
 */
class WebInterface2
{
    /**
    * This is the system object
    */
    private $_system = null;
    /**
    * This is the driver object
    */
    private $_driver = null;
    /**
    * This is the table object
    */
    private $_device = null;
    /**
     * These are params we don't want to push
     */
    private $_nopush = array(
        "LastAverage15MIN", "LastAverageHOURLY", "LastAverageDAILY", 
        "LastAverageWEEKLY", "LastAverageMONTHLY", "LastAverageYEARLY", 
        "LastAverage30SEC", "LastAverage1MIN", "LastAverage5MIN", 
        "LastAverage15MINTry", "LastAverageHOURLYTry", "LastAverageDAILYTry", 
        "LastAverageWEEKLYTry", "LastAverageMONTHLYTry", "LastAverageYEARLYTry", 
        "LastAverage30SECTry", "LastAverage1MINTry", "LastAverage5MINTry", 
        "LastHistoryPush", "LastMasterRawHistoryPush",
        "LastMasterHistoryPush"
    );
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object $system The network application object
    * @param object $device The device device object
    * @param object $driver The device driver object
    *
    * @return null
    */
    private function __construct($system, $device, $driver)
    {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a driver object",
            !is_object($driver)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a device object",
            !is_object($device)
        );
        $this->_system = &$system;
        $this->_driver  = &$driver;
        $this->_device  = &$device;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_system);
        unset($this->_driver);
        unset($this->_device);
    }
    /**
    * This function creates the system.
    *
    * @param mixed  $network (object)The system object to use
    * @param string $device  (object)The device to use
    * @param object $driver  The device driver object
    *
    * @return null
    */
    public static function factory($network, $device, $driver)
    {
        $object = new WebInterface2($network, $device, $driver);
        return $object;
    }
    /**
    * This does the extra API stuff for this object
    *
    * @param object $api   The api object
    * @param array  $extra Extra data from the
    *
    * @return string
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function WebAPI2($api, $extra)
    {
        $args = $api->args();
        $subobject = trim(strtolower($args->get("subobject")));
        $method = trim(strtoupper($args->get("method")));
        $ret = null;
        if ($subobject === "config") {
            $ret = $this->_config($api);
        } else if ($this->_device->isNew()) {
            $api->response(404);
        } else if (($method === "PUT") && empty($subobject)) {
            $ret = $this->_put($api);
        } else if (($method === "POST") && empty($subobject)) {
            $ret = $this->_new($api);
        } else if ($subobject === "firmware") {
            $ret = $this->_firmware();
        } else if ($subobject === "controlchan") {
            $ret = $this->_controlchan($api);
        } else if ($subobject === "export") {
            $ret = $this->_export($api);
        } else if ($subobject === "import") {
            $ret = $this->_import($api, false);
        } else if (($subobject === "fctsetup") && ($method == "GET")) {
            $ret = $this->_fctsetup($api);
        } else if (($subobject === "fctapply") && ($method == "PUT")) {
            $ret = $this->_fctapply($api);
        } else if ($subobject === "fcts") {
            if ($method == "GET") {
                $ret = $this->_getfcts();
            } else if ($method === "PUT") {
                $ret = $this->_putfcts($args);
            }
        /*
        } else if ($subobject === "getraw") {
            $ret = $this->_getRaw($args);
        } else if ($subobject === "export") {
            $ret = $this->_export($args);
        } else if ($subobject === "import") {
            $ret = $this->_import($args, false);
        } else if ($subobject === "sync") {
            $ret = $this->_import($args, true);
        } else if ($subobject === "lastdata") {
            $ret = $this->_lastdata($args);
        } else if ($subobject === "getfcts") {
            $ret = $this->_getfcts();
        } else if ($subobject === "putfcts") {
            $ret = $this->_putfcts($args);
        } else if ($subobject === "fctsetup") {
            $ret = $this->_fctsetup($args);
        } else if ($subobject === "fctapply") {
            $ret = $this->_fctapply($args);
        */
        }
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _list($args)
    {
        $data = $args->get("data");
        if (isset($data["limit"]) && is_numeric(trim($data["limit"]))) {
            $this->_device->table()->sqlLimit = (int)trim($data["limit"]);
            unset($data["limit"]);
        }
        if (isset($data["start"]) && is_numeric(trim($data["start"]))) {
            $this->_device->table()->sqlStart = (int)trim($data["start"]);
            unset($data["start"]);
        }
        $ret = $this->_device->getList($data, false);
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @param object $api The API object
    *
    * @return string
    */
    private function _controlchan($api)
    {
        $method = trim(strtoupper($api->args()->get("method")));
        $data = null;
        $sid = $api->args()->get("sid");
        if ($method == "GET") {
            $pkt = $this->_device->action()->send(
                array(
                    "Command" => '0x65',
                    "Data" => sprintf("%02X", $sid),
                )
            );
            if (is_object($pkt)) {
                $reply = $pkt->reply();
                $data = 0;
                for ($i = 0; $i < 4; $i++) {
                    $data += hexdec(substr($reply, ($i * 2), 2))<<($i * 8);
                }
                return $data;
            } else {
                $api->response(401);
                $c = get_class($api);
                $api->error($c::NO_RESPONSE, "No response getting control channel $sid on board ".sprintf("%06X", $this->device->id()));
            }
        } else if (($method == "PUT") || ($method == "POST")) {
            $data = $api->args()->get("data");
            $datastr = sprintf("%08X", $data);
            for ($i = 6; $i >= 0; $i-=2) {
                $value .= substr($datastr, $i, 2);
            }
            $pkt = $this->_device->action()->send(
                array(
                    "Command" => '0x64',
                    "Data" => sprintf("%02X", $sid).$value,
                )
            );
            if (is_object($pkt) && ($pkt->reply() == $value)) {
                $api->response(202);
                return (int)$data;
            } else {
                $api->response(401);
                $c = get_class($api);
                $api->error($c::NO_RESPONSE, "No response setting control channel $sid on board ".sprintf("%06X", $this->device->id()));
            }
        }

    }
    /**
    * returns a history object for this device
    *
    * @param object $api The API object
    *
    * @return string
    */
    private function _new($api)
    {
        $data = (array)$api->args()->get("data");
        $dev  = array();
        if (isset($data["type"])) {
            $type = trim(strtolower($data["type"]));
        } else {
            $type = null;
        }
        if ($type == "test") {
            $dev["HWPartNum"] = "0039-24-03-P";
        } else if ($type == "fastaverage") {
            $dev["HWPartNum"] = "0039-24-04-P";
        } else if ($type == "slowaverage") {
            $dev["HWPartNum"] = "0039-24-02-P";
        }
        if (!is_null($type) && isset($dev["HWPartNum"])) {
            if ($this->_device->insertVirtual($dev)) {
                $this->_device->setParam("Created", $this->_system->now());
                if ($this->_device->store()) {
                    return $this->_device->toArray(true);
                }
            }
        }
        $api->response(401);
        $c = get_class($api);
        $api->pdoerror($this->_device->lastError(), $c::SAVE_FAILED);
        return array();
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _put($api)
    {

        $data = (array)$api->args()->get("data");
        $params = (array)$data["params"];
        unset($data["params"]);
        unset($data["localParams"]);
        foreach ($data as $key => $value) {
            $this->_device->set($key, $value);
        }
        // This can't be pushed.  It can only be set locally.
        $params = array_diff_key($params, array_fill_keys($this->_nopush, 0));
        foreach ($params as $key => $value) {
            $this->_device->setParam($key, $value);
        }
        $this->_device->setParam("LastModified", $this->_system->now());
        if ($this->_device->store(true)) {
            $api->response(202);
        } else {
            $api->response(400);
            $c = get_class($api);
            $api->pdoerror($this->_device->lastError(), $c::SAVE_FAILED);
        }
        return "";
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _getfcts($args = null)
    {
        return $this->_device->fcts()->toArray(true);
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _fctsetup()
    {
        return $this->_device->fcts()->apply(true);
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _fctapply()
    {
        $this->_device->fcts()->apply(false);
        return $this->_device->toArray(true);
    }
    /**
    * returns a history object for this device
    *
    * @param object $api The API object
    *
    * @return string
    */
    private function _putfcts($api)
    {
        $data = (array)$api->args()->get("data");
        $ret = $this->_device->fcts($data, true);
        $this->_device->store();
        return $ret->toArray(true);
    }
    /**
    * returns a history object for this device
    *
    * @return string
    */
    private function _config($api)
    {
        $ret = "";
        $method = trim(strtoupper($api->args()->get("method")));
        if (($method == "PUT") || ($method == "POST")) {
            if ($this->_device->isNew()) {
                $api->response(404);
            } else if ($this->_device->action()->loadConfig()) {
                $ret = $this->_device->toArray(true);
            } else {
                $api->response(400);
            }
        } else {
            if ($this->_device->action()->config()) {
                $this->_device->setParam("LastModified", $this->_system->now());
                $this->_device->store();
                $ret = $this->_device->toArray(true);
            } else {
                $api->response(400);
            }
        }
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @return string
    */
    private function _firmware()
    {
        if ($this->_device->action()->loadFirmware()) {
            $ret = $this->_device->toArray(true);
        } else {
            $ret = -1;
        }
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @return string
    */
    private function _getRaw()
    {
        $ret = array();
        $pkt = $this->_device->action()->send(
            "54"
        );
        if (is_object($pkt)) {
            $string = $pkt->Reply();
            $ret = $this->_device->dataChannels()->decodeRaw($string);
        }
        return $ret;
    }
    /**
    * Export the device
    *
    * @param object $api The API object
    *
    * @return string
    */
    private function _export($api)
    {
        $format = $api->args()->get("format");
        if (!headers_sent() && ($format != "inline")) {
            // @codeCoverageIgnoreStart
            header('Content-type: application/json');
            header(
                'Content-disposition: attachment;'
                .'filename='.$this->_device->get("DeviceID").'.json'
            );
        }
        // @codeCoverageIgnoreEnd
        $fixture = $this->_device->fixture();
        $data = json_decode($fixture->export());
        $options = 0;
        if (defined("JSON_PRETTY_PRINT")) {
            $options = JSON_PRETTY_PRINT;
        }
        print json_encode($data, $options);
        return null;

    }
    /**
    * Import the device
    *
    * @param object $api       The API object
    * @param bool   $checkdate Whether to check the date
    *
    * @return string
    */
    private function _import($args, $checkdate = false)
    {
        header('Content-type: text/plain; charset=UTF-8');
        $data = $api->args()->get("data");
        if (is_string($data)) {
            $data = stripcslashes($data);
            $data = json_decode($data, true);
        }
        if (is_array($data)) {
            $fixture = $this->_system->device()->fixture();
            $fixture->import($data);
            $new = (int)$fixture->getParam("LastModified");
            $me  = (int)$this->_device->getParam("LastModified");
            if (($checkdate == false) || ($new > $me)) {
                $dev = $fixture->mergeDevice(false);
            }
            print json_encode($dev->get("DeviceID"));
        } else {
            print json_encode("0");
        }
        return null;
    }
    /**
    * Export the device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _lastdata($args)
    {
        $poll = $this->_device->getParam("LastPollData");
        $data = $args->get("data");
        if (isset($data["channel"]) && isset($poll[$data["channel"]])) {
            $chan = $this->_device->dataChannels()->dataChannel($data["channel"]);
            $chan->convert(
                $poll[$data["channel"]]['value'] 
            );
            print $poll[$data["channel"]]['value'];
            print " ".html_entity_decode($chan->get("units"));
            print "\n";
        }
        return null;

    }
}


?>
