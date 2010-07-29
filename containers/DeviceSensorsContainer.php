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
    /** @var array This is where the data is stored */
    protected $data = array();

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
        $this->Sensors = 0;
        // Now setup our sensors
        for ($i = 0; $i < (int)$this->myDevice->DriverInfo["NumSensors"]; $i++) {
            $this->upgradeArray($array[$i], $i);
            $this->sensor[$i] = &$this->sensorFactory($array[$i]);
            $this->Sensors++;
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
        $sensors = str_split($string, 2);
        foreach ($sensors as $key => $value) {
            if ($key >= $this->Sensors) {
                break;
            }
            $id = hexdec($value);
            $good = $this->checkSensor(
                $id,
                $this->sensor($key)->type,
                $this->sensor($key)
            );
            if (!$good) {
                $data = $this->sensor($key)->toArray();
                $data["id"] = $id;
                $this->sensor[$key] = $this->sensorFactory($data);
            }
            $this->sensor($key)->id = $id;
        }
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
            $old = $this->sensor($key)->toArray();
            $vals = array_merge($old, $array[$key]);
            $good = $this->checkSensor(
                $vals["id"],
                $vals["type"],
                $this->sensor($key)
            );
            if ($good) {
                $this->sensor($key)->fromArray($array[$key]);
            } else {
                $this->sensor[$key] = $this->sensorFactory($vals);
            }
        }
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
        return is_a($sensor, $this->sensorClass($id, $type));
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
        return $this->sensorFactory($array);
    }

}
?>
