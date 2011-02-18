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
class ComputationVirtualSensor extends VirtualSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Computation Virtual Sensor",
        "Type" => "sensor",
        "Class" => "ComputationVirtualSensor",
        "Flags" => array("FE:computation"),
    );
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "id" => 0xFE,                    // The id of the sensor.  This is the value
                                         // Stored in the device  It will be an int
        "type" => "computation",                    // The type of the sensors
        "location" => "",                // The location of the sensors
        "dataType" => UnitsBase::TYPE_RAW,      // The datatype of each sensor
        "extra" => array(),              // Extra input for crunching numbers
        "units" => "",                   // The units to put the data into by default
        "rawCalibration" => "",          // The raw calibration string
        "decimals" => null,
    );
    /** @var object These are the valid values for type */
    protected $typeValues = array("computation");
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Computation Virtual Sensor",
        "unitType" => "Generic",
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            20, 10, 15,
            array(
                UnitsBase::TYPE_RAW => UnitsBase::TYPE_RAW,
                UnitsBase::TYPE_DIFF => UnitsBase::TYPE_DIFF
            ),
            3,
        ),
        "extraText" => array(
            "Math", "Storage Unit", "Unit Type", "Data Type", "Max Decimals"
        ),
        "extraDefault" => array(
            "", "unknown", "Generic", UnitsBase::TYPE_RAW, 4
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
        $this->default["type"] = "computation";
        if (isset($data["extra"][1])) {
            $this->fixed["storageUnit"] = $data["extra"][1];
        }
        if (isset($data["extra"][2])) {
            $this->fixed["unitType"] = $data["extra"][2];
        }
        if (isset($data["extra"][3])) {
            $this->fixed["storageType"] = $data["extra"][3];
        }
        if (isset($data["extra"][4])) {
            $this->fixed["maxDecimals"] = $data["extra"][4];
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
        $fct = $this->createFunction($this->getExtra(0), $data);
        if ($ret = @eval( "return $fct;")) {
            $ret = round($ret, $this->decimals);
        } else {
            $ret = null;
        }
        return $ret;
    }

    /**
     * Creates a function to crunch numbers
     *
     * @param string $math The math to use
     * @param array  $data The data from the other sensors that were crunched
     *
     * @return bool|string The name of the function created.
     */
    protected function createFunction($math, $data)
    {
        $mathCode = $math;
        for ($i = 1; $i < 20; $i++) {
            $index = $i - 1;
            $mathCode = str_ireplace(
                '{'.$i.'}', $data[$index]["value"], $mathCode
            );
        }
        return $this->sanatize($mathCode);
    }
    /**
     * Creates a function to crunch numbers
     *
     * @param string $string The string to sanatize
     *
     * @return bool|string The name of the function created.
     */
    protected function sanatize($string)
    {
        $pattern = preg_quote('+*)(');
        $string = preg_replace(
            '/[^0-9\-\/\+\*\(\)\^\.]+/',
            "",
            $string
        );
        return $string;
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
