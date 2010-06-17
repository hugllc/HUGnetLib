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
require_once dirname(__FILE__)."/DataPointBase.php";
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
    const TYPE_RAW = DataPointBase::TYPE_RAW;
    /** This is a differential record */
    const TYPE_DIFF = DataPointBase::TYPE_DIFF;
    /** This is a raw record */
    const TYPE_IGNORE = DataPointBase::TYPE_IGNORE;
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "id" => null,                    // The id of the sensor.  This is the value
                                         // Stored in the device  It will be an int
        "type" => "",                    // The type of the sensors
        "location" => "",                // The location of the sensors
        "dataType" => "raw",             // The datatype of each sensor
        "extra" => array(),              // Extra input for crunching numbers
        "rawCalibration" => "",          // The raw calibration string
    );
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Unknown Sensor",
        "unitType" => "Generic",
        "units" => 'unknown',
        "extraText" => array(),
        "extraDefault" => array(),
    );

    /** @var object This is the device I am attached to */
    protected $myDevice = null;
    /** @var object This is where we store our configuration */
    protected $myConfig = null;
    /** @var object These are the valid values for dataType */
    protected $dataTypeValues = array("raw", "diff", "ignore");
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
        // Set up the class
        parent::__construct($data);
    }
    /**
    * Gets the extra values
    *
    * @param array $index The extra index to use
    *
    * @return
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
