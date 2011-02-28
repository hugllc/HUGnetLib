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
class LinearTransformVirtualSensor extends VirtualSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Linear Transform Virtual Sensor",
        "Type" => "sensor",
        "Class" => "LinearTransformVirtualSensor",
        "Flags" => array("FE:lineartransform"),
    );
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "id" => 0xFE,                    // The id of the sensor.  This is the value
                                         // Stored in the device  It will be an int
        "type" => "lineartransform",                    // The type of the sensors
        "location" => "",                // The location of the sensors
        "dataType" => UnitsBase::TYPE_RAW,      // The datatype of each sensor
        "extra" => array(),              // Extra input for crunching numbers
        "units" => "",                   // The units to put the data into by default
        "bound" => false,                // This says if this sensor is changeable
        "rawCalibration" => "",          // The raw calibration string
        "decimals" => null,
    );
    /** @var object These are the valid values for type */
    protected $typeValues = array("lineartransform");
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Linear Transform Virtual Sensor",
        "unitType" => "Generic",
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            5, 10, 10, 10, 15,
            array(
                UnitsBase::TYPE_RAW => UnitsBase::TYPE_RAW,
                UnitsBase::TYPE_DIFF => UnitsBase::TYPE_DIFF
            ),
            3,
        ),
        "extraText" => array(
            "Input", "Slope", "Y Intercept", "Storage Unit",
            "Unit Type", "Data Type", "Max Decimals"
        ),
        "extraDefault" => array(
            "", 0, 0, "unknown", "Generic", UnitsBase::TYPE_RAW, 4
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
        $this->default["id"] = 0xFE;
        $this->default["type"] = "lineartransform";
        if (isset($data["extra"][3])) {
            $this->fixed["storageUnit"] = $data["extra"][3];
        }
        if (isset($data["extra"][4])) {
            $this->fixed["unitType"] = $data["extra"][4];
        }
        if (isset($data["extra"][5])) {
            $this->fixed["storageType"] = $data["extra"][5];
            $this->default["dataType"] = $data["extra"][5];
        }
        if (isset($data["extra"][6])) {
            $this->fixed["maxDecimals"] = $data["extra"][6];
        }
        parent::__construct($data, $device);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param array $data The data from the other sensors that were crunched
    *
    * @return mixed The value in whatever the units are in the sensor
    */
    function getVirtualReading($data)
    {
        $index = ((int)$this->getExtra(0)) - 1;
        $y = $data[$index]["value"];
        if (is_null($y)) {
            return null;
        }
        $m = $this->getExtra(1);
        $b = $this->getExtra(2);
        $x = ($m * $y) + $b;
        $x = round($x, $this->maxDecimals);
        return $x;
    }

    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/
    /**
    * function to set units
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setUnits($value)
    {
        $this->data["units"] = (string)$value;
    }
    /**
    * function to set type
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setType($value)
    {
        $this->data["type"] = (string)$value;
    }
    /**
    * function to set type
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setId($value)
    {
        $this->data["id"] = (int)$value;
    }

}
?>
