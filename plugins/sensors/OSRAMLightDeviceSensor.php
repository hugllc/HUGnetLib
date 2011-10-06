<?php
/**
 * Sensor driver for wind direction sensors
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/DeviceSensorBase.php";
/**
 * This class deals with wind direction sensors.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class OSRAMLightDeviceSensor extends DeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "OSRAM Photodiode",
        "Type" => "sensor",
        "Class" => "OSRAMLightDeviceSensor",
        "Flags" => array("30", "30:OSRAM BPW-34"),
    );
    /** @var array This is the default values for the data */
    protected $default = array(
        "location" => "",                // The location of the sensors
        "id" => null,                    // The id of the sensor.  This is the value
                                         // Stored in the device  It will be an int
        "type" => "",                    // The type of the sensors
        "dataType" => UnitsBase::TYPE_RAW,       // The datatype of each sensor
        "extra" => array(),              // Extra input for crunching numbers
        "units" => "",                   // The units to put the data into by default
        "bound" => false,                // This says if this sensor is changeable
        "rawCalibration" => "",          // The raw calibration string
        "timeConstant" => 1,             // The time constant
        "Am" => 1023,                    // The maximum value for the AtoD convertor
        "Tf" => 65536,                   // The Tf value
        "D" => 65536,                    // The D value
        "s" => 64,                       // The s value
        "Vcc" => 5,                      // The Vcc value
        "filter" => array(),             // Information on the output filter
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(0x30);
    /** @var object These are the valid values for type */
    protected $typeValues = array("OSRAM BPW-34");
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "OSRAM BPW-34 Photodiode",
        "unitType" => "Heat/Unit Area",
        "storageUnit" => 'W/m^2',
        "storageType" => UnitsBase::TYPE_RAW,  // This is the dataType as stored
        "extraText" => array(),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(),
        "extraDefault" => array(),
        "maxDecimals" => 2,
    );
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 0x30;
        $this->default["type"] = "OSRAM BPW-34";
        parent::__construct($data, $device);
    }

    /**
    * This function returns the light in W/m^2
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
        $den = $this->Am*$this->s*$this->D;
        if ($den == 0) {
            return(1500.0);
        }
        $L  = (-1500.0)*$A * $this->Tf * $this->timeConstant;
        $L  = $L / $den;
        $L += 1500.0;

        return round($L, 4);

    }

}

?>
