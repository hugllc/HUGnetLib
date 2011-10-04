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
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class PlaceholderVirtualSensor extends VirtualSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Empty Virtual Sensor",
        "Type" => "sensor",
        "Class" => "PlaceholderVirtualSensor",
        "Flags" => array("FE", "FE:Placeholder"),
    );
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "location" => "",                // The location of the sensors
    );
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "id" => 0xFE,                    // The id of the sensor.  This is the value
                                         // Stored in the device  It will be an int
        "type" => "Placeholder",                    // The type of the sensors
        "dataType" => UnitsBase::TYPE_IGNORE,      // The datatype of each sensor
        "extra" => array(),              // Extra input for crunching numbers
        "units" => "-",                  // The units to put the data into by default
        "rawCalibration" => "",          // The raw calibration string
        "longName" => "Empty Virtual Sensor",
        "units" => '-',
        "bound" => false,                // This says if this sensor is changeable
        "extraText" => array(),
        "extraDefault" => array(),
        "extraValues" => array(),
        "storageUnit" => "-",
        "storageType" => UnitsBase::TYPE_IGNORE,  // This is the dataType as stored
        "decimals" => 0,
        "maxDecimals" => 0,
        "unitType" => "-",
        "filter" => array(),             // Information on the output filter
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(0xFE);
    /** @var object These are the valid values for type */
    protected $typeValues = array("Placeholder");

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
        if (isset($data["location"])) {
            $this->location = $data["location"];
        }
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        return null;
    }
}
?>
