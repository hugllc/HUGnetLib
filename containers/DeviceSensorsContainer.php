<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceSensorsContainer extends HUGnetContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "RawCalibration" => "",             // The raw calibration string
        "Sensors" => 0,                     // The number of sensors
    );

    /** @var object This is the device I am attached to */
    protected $myDevice = null;
    /** @var object This is where we store our configuration */
    protected $myConfig = null;
    /** @var object This is where we store our sensors */
    protected $sensor = array();
    /** @var object This is where we store our sensor driver listing */
    protected $drivers = array();

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
        if (empty($this->Sensors)) {
            $this->Sensors = (int)$this->myDevice->DriverInfo["NumSensors"];
        }
        // Now setup our sensors
        for ($i = 0; $i < $this->Sensors; $i++) {
            $this->upgradeArray($array[$i], $i);
            $this->_setSensor($array[$i], $i, true);
            //$this->sensor[$i] = &$this->sensorFactory($array[$i]);
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
        if (empty($array) || !is_array($array)) {
            return;
        }
        for ($key = 0; $key < $this->Sensors; $key++) {
            $this->_setSensor($array[$key], $key);
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
    public function _setSensor($data, &$key, $force=false)
    {
        if (is_object($this->sensor[$key])) {
            $vals = $this->sensor($key)->toArray();
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
        $good = $this->checkSensor($vals["id"], $vals["type"], $this->sensor[$key]);
        if ($good && !$force) {
            $this->sensor($key)->fromArray($vals);
        } else {
            $this->sensor[$key] = &$this->sensorFactory($vals);
        }
        $this->_setSensorInputSize($key);
    }

    /**
    * Creates a sensor object, or updates the one in place
    *
    * @param int   &$key  The key to use for the sensor array
    *
    * @return null
    */
    public function _setSensorInputSize(&$key)
    {
        $inputSize = $this->sensor($key)->inputSize;
        for ($j = 1; $j < $inputSize; $j++) {
            $this->sensor[$key+$j] = &$this->sensorFactory(
                array(
                    "id" => 0xFF, "type" => "null", "dataType" => "ignore",
                )
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
        for($i = 0; $i < $this->Sensors; $i++) {
            $ret[$i] = $this->sensor($i)->getUnits(
                $data[$i], $data["deltaT"], $prev[$i]
            );
        }
        return $ret;
    }

}
?>
