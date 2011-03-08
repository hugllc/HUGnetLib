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
require_once dirname(__FILE__)."/../../base/VirtualSensorBase.php";

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
class PhysicalPointVirtualSensor extends VirtualSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Physical Point Virtual Sensor",
        "Type" => "sensor",
        "Class" => "PhysicalPointVirtualSensor",
        "Flags" => array("FE:physicalpoint"),
    );
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "id" => 0xFE,                    // The id of the sensor.  This is the value
                                         // Stored in the device  It will be an int
        "type" => "physicalpoint",                    // The type of the sensors
        "location" => "",                // The location of the sensors
        "dataType" => UnitsBase::TYPE_RAW,      // The datatype of each sensor
        "extra" => array(),              // Extra input for crunching numbers
        "units" => "",                   // The units to put the data into by default
        "bound" => false,                // This says if this sensor is changeable
        "rawCalibration" => "",          // The raw calibration string
        "decimals" => null,
    );
    /** @var object These are the valid values for type */
    protected $typeValues = array("physicalpoint");
    /** @var object These are the valid values for type */
    protected $dev = null;
    /** @var object These are the valid values for type */
    protected $DeviceID = null;
    /** @var object These are the valid values for type */
    protected $avg = null;
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "PhysicalPoint Virtual Sensor",
        "unitType" => "Generic",
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            8, 4
        ),
        "extraText" => array(
            "Device ID", "Sensor"
        ),
        "extraDefault" => array(
            "", 1
        ),
        "storageType" => UnitsBase::TYPE_RAW,  // This is the dataType as stored
        "storageUnit" => "unknown",
        "maxDecimals" => 4,
    );

    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $dev = &$this->getDevice($data["extra"][0]);
        $this->cloneSensor($data);
    
        if (empty($data["location"])) {
            unset($data["location"]);
        }
        parent::__construct($data, $device);
    }

    /**
    * Clones a sensor
    *
    * @param array $data The servers to use
    * 
    * @return null
    */
    protected function cloneSensor($data)
    {
        $dev = &$this->getDevice();
        $sensor = &$dev->sensors->sensor($data["extra"][1] - 1);
        $fixed = array("unitType", "storageUnit", "maxDecimals", "storageType");
        foreach ($fixed as $f) {
            $this->fixed[$f] = $sensor->$f;
        }
        $default = array("dataType", "decimals", "units", "location");
        foreach ($default as $d) {
            $this->default[$d] = $sensor->$d;
        }
    }
    /**
    * Disconnects from the database
    *
    */
    public function __destruct()
    {
        unset($this->dev);
        unset($this->avg);
    }
    /**
    * Changes a raw reading into a output value
    * 
    * @param string $DeviceID The Device ID of the device to get
    *
    * @return mixed The value in whatever the units are in the sensor
    */
    public function &getDevice($DeviceID = null)
    {
        if (!empty($DeviceID)) {
            $this->DeviceID = hexdec($DeviceID);
        }
        if (!is_a($this->dev, "DeviceContainer")) {
            $this->dev = new DeviceContainer(
                array("group" => $this->myDevice->group)
            );
            $this->dev->getRow($this->DeviceID);
        }
        return $this->dev;
    }
    /**
    * Changes a raw reading into a output value
    *
    * @return mixed The value in whatever the units are in the sensor
    */
    protected function &getAvg()
    {
        if (!is_a($this->avg, "AverageTableBase")) {
            $this->avg = &$this->getDevice()->historyFactory(array(), false);
        }
        return $this->avg;
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    *
    * @return mixed The value in whatever the units are in the sensor
    */
    public function getReading($A, $deltaT = 0, &$data = array())
    {
        return null;
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param array $i         The point we are working on
    * @param array &$data     The data from the other sensors that were crunched
    * @param array &$avgTable The average table to get the readings from
    *
    * @return mixed The value in whatever the units are in the sensor
    */
    public function get15MINAverage($i, &$data, &$avgTable)
    {
        if ($data["Date"] > $this->dev->params->DriverInfo["LastAverage15MIN"]) {
            return false;
        }
        $avg = &$avgTable[$this->DeviceID];
        if (!is_a($avg, "AverageTableBase")) {
            $avg = $this->getAvg();
            $avg->sqlLimit = 1;
            $avg->selectInto(
                "`id` = ? and `Type`=? and `Date` = ?",
                array(hexdec($this->getExtra(0)), "15MIN", $data["Date"])
            );
        }
        $col = "Data".($this->getExtra(1) - 1);
        $data[$i] = array(
            "value" => $avg->$col,
            "units" => $this->storageUnit,
            "unitType" => $this->unitType,
            "dataType" => $this->storageType,
        );
        return true;
    }
    /**
    * This returns the first average from this device
    *
    * @return null
    */
    public function getFirstAverage15Min()
    {
        $avg = &$this->getAvg();
        $avg->sqlLimit = 1;
        $avg->sqlOrderBy = "Date ASC";
        $avg->selectInto(
            "id = ? AND Type = ?",
            array($avg->device->id, AverageTableBase::AVERAGE_15MIN)
        );
        return $avg->Date;
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/
}
?>
