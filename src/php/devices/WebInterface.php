<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class WebInterface
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
        $object = new WebInterface($network, $device, $driver);
        return $object;
    }
    /**
    * returns a history object for this device
    *
    * @param object $args  The argument object
    * @param array  $extra Extra data from the
    *
    * @return string
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function webAPI($args, $extra)
    {
        $action = trim(strtolower($args->get("action")));
        $ret = null;
        if ($action === "put") {
            $ret = $this->_put($args);
        } else if ($action === "list") {
            $ret = $this->_list($args);
        } else if ($action === "new") {
            $ret = $this->_new($args);
        } else if ($action === "config") {
            $ret = $this->_config($args);
        } else if ($action === "loadfirmware") {
            $ret = $this->_loadfirmware($args);
        } else if ($action === "loadconfig") {
            $ret = $this->_loadconfig($args);
        } else if ($action === "getraw") {
            $ret = $this->_getRaw($args);
        } else if ($action === "export") {
            $ret = $this->_export($args);
        } else if ($action === "import") {
            $ret = $this->_import($args);
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
        if (isset($data["limit"]) && is_numeric($data["limit"])) {
            $this->_device->table()->sqlLimit = (int)$data["limit"];
            unset($data["limit"]);
        }
        if (isset($data["start"]) && is_numeric($data["start"])) {
            $this->_device->table()->sqlStart = (int)$data["start"];
            unset($data["start"]);
        }
        $ret = $this->_device->getList($data, false);
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _new($args)
    {
        $data = (array)$args->get("data");
        $dev  = array();
        if (trim(strtolower($data["type"])) == "test") {
            $dev["HWPartNum"] = "0039-24-03-P";
        } else if (trim(strtolower($data["type"])) == "fastaverage") {
            $dev["HWPartNum"] = "0039-24-04-P";
        } else if (trim(strtolower($data["type"])) == "slowaverage") {
            $dev["HWPartNum"] = "0039-24-02-P";
        }
        if ($this->_device->insertVirtual($dev)) {
            $this->_device->setParam("Created", $this->_system->now());
            $this->_device->store();
            return $this->_device->toArray(true);
        }
        return array();
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _put($args)
    {
        $data = (array)$args->get("data");
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
            return $this->_device->toArray(true);
        }
        return -1;
    }
    /**
    * returns a history object for this device
    *
    * @return string
    */
    private function _config()
    {
        if ($this->_device->action()->config()) {
            $this->_device->setParam("LastModified", $this->_system->now());
            $this->_device->store();
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
    private function _loadFirmware()
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
    private function _loadConfig()
    {
        if ($this->_device->action()->loadConfig()) {
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
    * @param object $args The argument object
    *
    * @return string
    */
    private function _export($args)
    {
        $format = $args->get("format");
        if (!headers_sent() && ($format != "inline")) {
error_log("here");
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
    * @param object $args The argument object
    *
    * @return string
    */
    private function _import($args)
    {
        header('Content-type: text/plain; charset=UTF-8');
        $data = $args->get("data");
        if (is_string($data)) {
            $data = stripcslashes($data);
            $data = json_decode($data, true);
        }
        if (is_array($data)) {
            $fixture = $this->_system->device()->fixture();
            $fixture->import($data);
            $dev = $fixture->mergeDevice(false);
            print json_encode($dev->get("DeviceID"));
        } else {
            print json_encode("0");
        }
        return null;
    }
}


?>
