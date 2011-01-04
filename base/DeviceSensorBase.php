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
require_once dirname(__FILE__)."/HUGnetContainer.php";
require_once dirname(__FILE__)."/UnitsBase.php";
require_once dirname(__FILE__)."/../interfaces/DeviceSensorInterface.php";

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
abstract class DeviceSensorBase extends HUGnetContainer
    implements DeviceSensorInterface
{
    /** This is a raw record */
    const TYPE_RAW = UnitsBase::TYPE_RAW;
    /** This is a differential record */
    const TYPE_DIFF = UnitsBase::TYPE_DIFF;
    /** This is a raw record */
    const TYPE_IGNORE = UnitsBase::TYPE_IGNORE;
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "id" => null,                    // The id of the sensor.  This is the value
                                         // Stored in the device  It will be an int
        "type" => "",                    // The type of the sensors
        "location" => "",                // The location of the sensors
        "dataType" => UnitsBase::TYPE_RAW,      // The datatype of each sensor
        "extra" => array(),              // Extra input for crunching numbers
        "rawCalibration" => "",          // The raw calibration string
        "units" => "",                   // The units to put the data into by default
        "decimals" => 2,                 // The number of decimal places to use
    );
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Unknown Sensor",
        "unitType" => "Generic",
        "storageUnit" => 'unknown',     // This is how the data is stored
        "extraText" => array(),
        "extraDefault" => array(),
        "maxDecimals" => 2,
    );

    /** @var object This is where our unit conversion is stored */
    protected $unitConvert = null;

    /** @var object This is the device I am attached to */
    protected $myDevice = null;
    /** @var object This is where we store our configuration */
    protected $myConfig = null;
    /** @var object These are the valid values for dataType */
    protected $dataTypeValues = array(
        UnitsBase::TYPE_RAW, UnitsBase::TYPE_DIFF, UnitsBase::TYPE_IGNORE
    );
    /** @var object These are the valid values for unitType */
    protected $unitTypeValues = array();
    /** @var object These are the valid values for units */
    protected $unitsValues = array();

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
        // Set up the default units
        $this->setupUnits();
        if (empty($this->default["units"])) {
            if (strtolower($this->unitConvert->to) == "unknown") {
                $this->default["units"] = $this->storageUnit;
            } else {
                $this->default["units"] = $this->unitConvert->to;
            }
        }
        if (!$this->unitConvert->valid($data["units"])) {
            unset($data["units"]);
        }
        if (is_null($this->default["decimals"])
            || ($this->default["decimals"] > $this->maxDecimals)) {
            $this->default["decimals"] = (int)$this->maxDecimals;
        }
        if ($data["decimals"] > $this->maxDecimals) {
            unset($data["decimals"]);
        }
        // Set up the class
        parent::__construct($data);
    }
    /**
    * Gets the extra values
    *
    * @param array $index The extra index to use
    *
    * @return The extra value (or default if empty)
    */
    protected function getExtra($index)
    {
        if (isset($this->extra[$index])) {
            return $this->extra[$index];
        }
        return $this->extraDefault[$index];
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toArray($default = true)
    {
        // Always return the type and id
        return array_merge(
            array(
                "id"   => $this->id,
                "type" => $this->type,
            ),
            parent::toArray($default)
        );
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array $prev   The previous reading
    *
    * @return float The direction in degrees
    */
    public function getUnits($A, $deltaT = 0, $prev = null)
    {
        $val = $this->getReading($A, $deltaT);
        $ret = array(
            "value" => $val,
            "units" => $this->storageUnit,
            "unitType" => $this->unitType,
            "dataType" => $this->dataType,
        );
        if ($this->dataType == UnitsBase::TYPE_DIFF) {
            $ret["raw"] = $val;
            $ret["value"] = ($val - $prev);
        }
        return $ret;
    }

    /**
    * Sets up the unit conversion
    *
    * @return true on success, false on failure
    */
    protected function setupUnits()
    {
        if (empty($this->unitConvert)) {
            $driver = $this->myConfig->plugins->getPlugin(
                "Units", $this->unitType
            );
            $class = $driver["Class"];
            $d = array(
                "to" => $this->units,
                "from" => $this->storageUnit,
                "type" => $this->dataType,
            );
            $this->throwException(
                "No default unit class found",
                -5,
                !class_exists($class)
            );
            $this->unitConvert = new $class($d);
        }

    }
    /**
    * Converts data between units
    *
    * @param mixed  &$data The data to convert
    *
    * @return true on success, false on failure
    */
    public function convertUnits(&$data)
    {
        $this->setupUnits();
        if (!is_null($data)) {
            $ret = $this->unitConvert->convert($data, $this->units, $this->storageUnit);
            if ($ret === false) {
                $this->units = $this->storageUnit;
            }
            if (is_numeric($data)) {
                $data = round($data, (int)$this->decimals);
            }
        } else {
            $ret = true;
        }
        return $ret;
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/
    /**
    * function to set a limited set of values
    *
    * @param string $key   The key to use
    * @param mixed  $value The value to set
    *
    * @return null
    */
    protected function limitedValues($key, $value)
    {
        $values = $key."Values";
        if (in_array($value, (array)$this->$values)) {
            return $value;
        }
        return $this->default[$key];
    }
    /**
    * function to set DataType
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setDataType($value)
    {
        $this->data["dataType"] = $this->limitedValues(
            "dataType", strtolower($value)
        );
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
        // This should not be set externally
    }
    /**
    * function to set id
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setId($value)
    {
        // This should not be set externally
        $this->data["id"] = $this->limitedValues("id", (int)$value);;
        $this->data["type"] = $this->default["type"];
    }


}
?>
