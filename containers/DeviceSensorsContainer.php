<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
require_once dirname(__FILE__)."/../interfaces/OutputInterface.php";
require_once dirname(__FILE__)."/../interfaces/IteratorInterface.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceSensorsContainer extends HUGnetContainer
    implements OutputInterface, IteratorInterface
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "RawCalibration" => "",             // The raw calibration string
        "Sensors" => 0,                     // The number of sensors
        "ActiveSensors" => 0,               // The number of active physical sensors
        "PhysicalSensors" => 0,             // The number of actual sensors
        "VirtualSensors" => 0,              // The number of virtual sensors
        "forceSensors" => false,            // Force the Physical and Virtual
                                            // sensor numbers to be different than
                                            // the driver gives us.
    );

    /** @var object This is the device I am attached to */
    protected $myDevice = null;
    /** @var object This is where we store our configuration */
    protected $myConfig = null;
    /** @var object This is where we store our sensors */
    protected $sensor = array();
    /** @var object This is where we store our sensor driver listing */
    protected $drivers = array();
    /** @var int The index of the sensor we are currently looking at */
    private $_sensorIndex = 0;
    /** @var array The labels for the column headers */
    protected $labels = array(
        "num" => "#",
        "location" => "Location",
        "type" => "Type",
        "dataType" => "Data Type",
        "units" => "Units",
        "decimals" => "Decimal Places",
    );
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        // Set up my device
        $this->myDevice = &$device;
        // Setup our configuration
        $this->myConfig = &ConfigContainer::singleton();
        parent::__construct($data);
    }
    /**
    * Builds the class
    *
    * @return null
    */
    public function __destruct()
    {
        for ($i = 0; $i < $this->Sensors; $i++) {
            unset($this->sensor[$i]);
        }
        unset($this->sensor);
        unset($this->myConfig);
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($array)
    {
        // Set up everything else
        parent::fromArray($array);
        // Clear the number of sensors
        $this->setupSensorNumbers();
        // Now setup our sensors
        for ($i = 0; $i < $this->Sensors; $i++) {
            $this->upgradeArray($array[$i], $i);
            $this->updateSensor($array[$i], $i, true);
        }
    }

    /**
    * Sets the number of sensors
    *
    * @return null
    */
    protected function setupSensorNumbers()
    {
        $driverInfo =& $this->myDevice->DriverInfo;
        $this->ActiveSensors = (int)$this->myDevice->ActiveSensors;
        if (!$this->forceSensors) {
            $this->PhysicalSensors = (int)$driverInfo["PhysicalSensors"];
            $this->VirtualSensors = (int)$driverInfo["VirtualSensors"];
        }
        if (empty($this->ActiveSensors)) {
            $this->ActiveSensors = $this->PhysicalSensors;
        }
        $sensors = $this->PhysicalSensors + $this->VirtualSensors;
        if (empty($this->Sensors) || ($this->Sensors < $sensors)) {
            $this->Sensors = $sensors;
        }
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toArray($default = false)
    {
        $data = parent::toArray($default);
        foreach (array_keys($this->sensor) as $key) {
            $data[$key] = $this->sensor[$key]->toArray($default);
        }
        return (array)$data;
    }
    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function fromTypeString($string)
    {
        if (empty($string) || !is_string($string)) {
            return;
        }
        $this->fromTypeArray(str_split($string, 2));
    }
    /**
    * Creates the object from a string
    *
    * @param array $array the array to use
    *
    * @return null
    */
    public function fromTypeArray($array)
    {
        if (!is_array($array)) {
            return;
        }
        // Clear the number of sensors
        $this->setupSensorNumbers();
        for ($key = 0; $key < $this->Sensors; $key++) {
            $this->updateSensor($array[$key], $key);
        }
    }

    /**
    * Creates a sensor object, or updates the one in place
    *
    * @param array $data  The data to use for the sensor
    * @param int   &$key  The key to use for the sensor array
    * @param bool  $force Force the creation of a new object
    *
    * @return null
    */
    public function updateSensor($data, &$key, $force=false)
    {
        if (is_object($this->sensor[$key])) {
            $vals = $this->sensor($key)->toArray();
            unset($vals["decimals"]);
        } else {
            $vals = array();
        }
        if (is_array($data)) {
            $vals = array_merge($vals, $data);
        } else if (is_string($data)) {
            $vals["id"] = hexdec($data);
        } else {
            $vals["id"] = (int)$data;
        } 
        if ($key >= $this->PhysicalSensors) {
            // This is forced to be a virtual sensor.
            $vals["id"] = 0xFE;
        } else if ($key >= $this->ActiveSensors) {
            // This is to help in the transition away from "ActiveSensors"
            $vals["dataType"] = UnitsBase::TYPE_IGNORE;
        }
        $good = $this->checkSensor($vals["id"], $vals["type"], $this->sensor[$key]);
        if ($good && !$force) {
            $this->sensor($key)->fromArray($vals);
        } else {
            $this->sensor[$key] = &$this->sensorFactory($vals);
        }
        $this->_updateSensorInputSize($key);
    }

    /**
    * Creates a sensor object, or updates the one in place
    *
    * @param int &$key The key to use for the sensor array
    *
    * @return null
    */
    private function _updateSensorInputSize(&$key)
    {
        $inputSize = $this->sensor($key)->inputSize;
        for ($j = 1; $j < $inputSize; $j++) {
            $this->sensor[$key+$j] = &$this->sensorFactory(
                array("id" => 0xFF, "location" => $this->sensor($key)->location)
            );
        }
        $key += $j - 1;
    }
    /**
    * Creates the sensors from the old method of storing them.
    *
    * @param DeviceParamsContainer &$array the array to upgrade to use
    * @param int                   $i      the index to get data from
    *
    * @return null
    */
    protected function upgradeArray(&$array, $i)
    {
        $stuff = array(
            "type"     => &$this->myDevice->params->sensorType,
            "location" => &$this->myDevice->params->Loc,
            "dataType" => &$this->myDevice->params->dType,
            "extra"    => &$this->myDevice->params->Extra,
        );
        $sync = array("location", "type", "dataType", "extra");
        // Now setup our sensors
        foreach ($sync as $key) {
            if (empty($array[$key])
                && !empty($stuff[$key][$i])
            ) {
                $array[$key] = $stuff[$key][(int)$i];
            }
        }
    }
    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function fromCalString($string)
    {
    }
    /**
    * Creates a sensor object
    *
    * @param array $array The setup array to use for the sensor class
    *
    * @return null
    */
    protected function &sensorFactory($array)
    {
        $class = $this->sensorClass($array["id"], $array["type"]);
        return new $class($array, $this->myDevice);
    }
    /**
    * Creates a sensor object
    *
    * @param int    $id   The ID for the sensor to use
    * @param string $type The type to check
    *
    * @return string The class for this sensor
    */
    protected function sensorClass($id, $type)
    {
        $sid = $this->stringSize(dechex($id), 2);
        $driver = $this->myConfig->plugins->getPlugin(
            "sensor", $sid.":".$type
        );
        return $driver["Class"];
    }
    /**
    * Creates a sensor object
    *
    * @param int    $id      The ID for the sensor to use
    * @param string $type    The type to check
    * @param object &$sensor The sensor to check
    *
    * @return bool True if the sensor is correct, false otherwise
    */
    protected function checkSensor($id, $type, &$sensor)
    {
        return is_object($sensor) && is_a($sensor, $this->sensorClass($id, $type));
    }

    /**
    * Creates a sensor object
    *
    * @param int $key The array key for the sensor object
    *
    * @return Returns a reference to the sensor object
    */
    public function &sensor($key)
    {
        if (isset($this->sensor[$key])) {
            return $this->sensor[$key];
        }
        return $this->sensorFactory(array());
    }

    /**
    * Creates a sensor object
    *
    * @param array $data The data to decode
    * @param array $prev The previous raw reading
    *
    * @return Returns an array of the data decoded
    */
    public function decodeSensorData($data, $prev = null)
    {
        $ret = array(
            "deltaT" => $data["deltaT"],
        );
        // This is for the physical sensors
        for ($i = 0; $i < $this->Sensors; $i++) {
            $ret[$i] = $this->sensor($i)->getUnits(
                $data[$i], $data["deltaT"], $prev[$i], $ret
            );
        }
        return $ret;
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    */
    public function toOutput($cols = null)
    {
        $ret = $this->sensor($this->_sensorIndex)->toArray(true, true);
        $ret["num"] = $this->_sensorIndex;
        $ret["bound"] = $this->sensor($this->_sensorIndex)->bound;
        return $ret;
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    */
    public function toOutputHeader($cols = null)
    {
        if (!is_array($cols) || empty($cols)) {
            return $this->labels;
        }
        $ret = array();
        foreach ($cols as $col) {
            if (isset($this->labels[$col])) {
                $ret[$col] = $this->labels[$col];
            } else {
                $ret[$col] = $col;
            }
        }
        return $ret;
    }
    /**
    * There should only be a single instance of this class
    *
    * @param string $type The output plugin type
    * @param array  $cols The columns to get
    *
    * @return array
    */
    public function outputParams($type, $cols = null)
    {
        return array();
    }
    /**
    * This puts the next result into the object
    *
    * @return bool True on success, False on failure
    */
    public function nextInto()
    {
        $this->_sensorIndex++;
        if ($this->_sensorIndex < $this->Sensors) {
            return true;
        }
        return false;
    }
}
?>
