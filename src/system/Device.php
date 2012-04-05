<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/SystemTableBase.php";
/** This the driver class we use */
require_once dirname(__FILE__)."/../devices/Driver.php";

/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Device extends SystemTableBase
{
    /**
    * This is the cache for the drivers.
    */
    private $_driverCache = array();
    /**
    * This is the cache for the drivers.
    */
    private $_network = null;
    /**
    * This is the firmware table
    */
    private $_firmware = null;
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        foreach (array_keys($this->_driverCache) as $key) {
            unset($this->_driverCache[$key]);
        }
        unset($this->_network);
        parent::__destruct();
    }
    /**
    * This function creates the system.
    *
    * @param mixed  $system (object)The system object to use
    * @param mixed  $data   (int)The id of the item, (array) data info array
    * @param string $table  The table to use
    *
    * @return null
    */
    public static function &factory($system, $data=null, $table="DevicesTable")
    {
        $object = &parent::factory($system, $data, $table);
        return $object;
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
        $ret = $this->driver()->get($field);
        if (is_null($ret)) {
            $ret = $this->table()->get($field);
        }
        return $ret;
    }
    /**
    * Returns the table as a json string
    *
    * @return json string
    */
    public function json()
    {
        $return = array_merge(
            $this->driver()->toArray(),
            $this->table()->toArray(true)
        );
        $return["sensors"] = array();
        for ($i = 0; $i < $return["totalSensors"]; $i++) {
            $return["sensors"][$i] = json_decode($this->sensor($i)->json(), true);
        }
        $params = json_decode($return["params"], true);
        if (!is_array($params)) {
            $params = unserialize(base64_decode($return["params"]));
            $params = $params["DriverInfo"];
        }
        $return["params"] = $params;
        if ($return["loadable"]) {
            $this->firmware()->set("HWPartNum", $return["HWPartNum"]);
            $this->firmware()->set("FWPartNum", $return["FWPartNum"]);
            $this->firmware()->set("RelStatus", \FirmwareTable::DEV);
            $this->firmware()->getLatest();
            $new = $this->firmware()->compareVersion(
                $return["FWVersion"], $this->firmware()->Version
            );
            if ($new < 0) {
                $return["update"] = $this->firmware()->Version;
            }
        }
        return json_encode($return);
    }

    /**
    * This function creates the system.
    *
    * @return Reference to the network object
    */
    public function &network()
    {
        if (!is_object($this->_network)) {
            include_once dirname(__FILE__)."/../devices/Network.php";
            $this->_network = \HUGnet\devices\Network::factory(
                $this->system()->network(),
                $this
            );
        }
        return $this->_network;
    }
    /**
    * This function creates the system.
    *
    * @return Reference to the network object
    */
    public function &config()
    {
        if (!is_object($this->_config)) {
            include_once dirname(__FILE__)."/../devices/Config.php";
            $this->_config = \HUGnet\devices\Config::factory(
                $this->table()
            );
        }
        return $this->_config;
    }
    /**
    * This creates the driver
    *
    * It doesn't worry too much about a valid driver.  If the driver is not valid
    * then devices\Driver::factory returns an EDEFAULT object.
    *
    * @param string $driver The driver to use.  Leave blank for automatic.
    *
    * @return null
    */
    protected function &driver($driver = null)
    {
        if (empty($driver)) {
            $driver = strtoupper((string)$this->table()->get("Driver"));
        }
        if (!is_object($this->_driverCache[$driver])) {
            include_once dirname(__FILE__)."/../devices/Driver.php";
            $this->_driverCache[$driver] = &devices\Driver::factory($driver);
        }
        return $this->_driverCache[$driver];
    }
    /**
    * This creates the sensor drivers
    *
    * @param int $sid The sensor id to get.  They are labaled 0 to sensors
    *
    * @return null
    */
    public function &sensor($sid)
    {
        include_once dirname(__FILE__)."/Sensor.php";
        $data = array(
            "sensor" => $sid,
            "dev" => $this->table()->get("id"),
        );
        $obj = Sensor::factory($this->system(), $data);
        if (is_null($obj->get("id"))) {
            $tSensors = $this->table()->get("sensors");
            if (is_string($tSensors) && !empty($tSensors)) {
                $tSensors = unserialize(base64_decode($tSensors));
                $obj->load((array)$tSensors[$sid]);
            } else if (is_array($tSensors)) {
                $obj->load((array)$tSensors[$sid]);
            } else {
                if ($sid < $this->driver()->get("physicalSensors")) {
                    $data["id"] = devices\Driver::getSensorID(
                        $sid, $this->table()->get("RawSetup")
                    );
                } else {
                    $data["id"] = 0xFE; // Virtual Sensor
                }
                $obj->load($data);
            }
            $obj->store();
        }
        return $obj;
    }
    /**
    * Gets one of the parameters
    *
    * @param string $field The field to get
    *
    * @return The value of the field
    */
    public function &getParam($field)
    {
        $params = $this->table()->get("params");
        $array = json_decode($params, true);
        if (!is_array($array)) {
            /* This converts the old system */
            $array = unserialize(base64_decode($params));
            /* Most of the old stuff is stored in "DriverInfo" */
            if (is_array($array["DriverInfo"])) {
                $array = $array["DriverInfo"];
            }
            /* Now re encode it properly, or return null if it is empty */
            if (is_array($array)) {
                $this->table()->set("params", json_encode($array));
            } else {
                $array = array();
            }
        }
        return $array[$field];
    }
    /**
    * Sets one of the parameters
    *
    * @param string $field The field to set
    * @param mixed  $value The value to set the field to
    *
    * @return null
    */
    public function &setParam($field, $value)
    {
        /* This makes sure the field is always in json format */
        $this->getParam($field);
        /* get the fields */
        $params = $this->table()->get("params");
        $params = json_decode($params, true);
        $params[$field] = $value;
        return $this->table()->set("params", json_encode($params));
    }
    /**
    * This function gives us access to the table class
    *
    * @return reference to the table class object
    */
    public function &firmware()
    {
        if (!is_object($this->_firmware)) {
            $class = Util::findClass("FirmwareTable", "tables");
            $system = &$this->system();
            $this->_firmware = new $class($system);
        }
        return $this->_firmware;
    }

    /**
    * Loads the data into the table class
    *
    * @param mixed $data (int)The id of the record,
    *                    (array) or (string) data info array
    *
    * @return null
    */
    public function load($data)
    {
        $ret = parent::load($data);
        if ($ret) {
            $this->table()->set(
                "Driver",
                \HUGnet\devices\Driver::getDriver(
                    $this->table()->get("HWPartNum"),
                    $this->table()->get("FWPartNum"),
                    $this->table()->get("FWVersion")
                )
            );
        }
        return $ret;
    }
    /**
    * Changes the units on the data
    *
    * The data coming in to this function should be the same as that produced
    * by decodeData.
    *
    * @param array &$data The data to convert.
    *
    * @return null
    */
    public function setUnits(&$data)
    {
        $sensors = $this->get("totalSensors");
        for ($i = 0; $i < $sensors; $i++) {
            $this->sensor($i)->convertUnits($data[$i]);
        }
    }
    /**
    * Decodes the sensor data
    *
    * @param string $string  The string of sensor data
    * @param string $command The command that was used to get the data
    * @param float  $deltaT  The time difference between this packet and the next
    * @param float  $prev    The previous record
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function decodeData($string, $command="", $deltaT = 0, $prev = null)
    {
        $data = $this->driver()->decodeSensorString((string)$string);
        $data["deltaT"] = $deltaT;
        $ret = array();
        $sensors = $this->get("totalSensors");
        for ($i = 0; $i < $sensors; $i++) {
            $ret[$i] = $this->sensor($i)->decodeData(
                $data[$i], $deltaT, $prev[$i], $ret
            );
        }
        $ret["deltaT"] = $deltaT;
        $ret["DataIndex"] = $data["DataIndex"];
        $ret["timeConstant"] = $data["timeConstant"];
        $ret["deltaT"] = $data["deltaT"];
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @param array $data    The data to build the history record with.
    * @param bool  $history History if true, average if false
    *
    * @return string
    */
    public function &historyFactory($data, $history = true)
    {
        $class = $this->driver()->historyTable($history);
        $obj = new $class($data);
        //$obj->labels($this->historyHeader());
        $obj->device = &$this;
        return $obj;
    }
}


?>
