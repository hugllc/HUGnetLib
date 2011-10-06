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
require_once dirname(__FILE__)."/../../base/sensors/PulseDeviceSensorBase.php";
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
class MaximumAnemometerDeviceSensor extends PulseDeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Maximum Inc Hall Effect Anemometer",
        "Type" => "sensor",
        "Class" => "MaximumAnemometerDeviceSensor",
        "Flags" => array("70:maximumAnemometer"),
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(0x70);
    /** @var object These are the valid values for type */
    protected $typeValues = array("maximumAnemometer");
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Maximum Inc Hall Effect Anemometer",
        "unitType" => "Speed",
        "storageUnit" => "MPH",
        "storageType" => UnitsBase::TYPE_DIFF,  // This is the dataType as stored
        "extraText" => array(),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(),
        "extraDefault" => array(),
        "maxDecimals" => 2,
    );
    /** @var object These are the valid values for dataType */
    protected $dataTypeValues = array(
        UnitsBase::TYPE_DIFF, UnitsBase::TYPE_IGNORE
    );
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 0x70;
        $this->default["type"] = "maximumAnemometer";
        $this->default["dataType"] = UnitsBase::TYPE_DIFF;
        parent::__construct($data, $device);
    }

    /**
    * This function returns the output in RPM
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
        if (empty($deltaT)) {
            return null;
        }
        if ($A <= 0) {
            return 0.0;
        }
        $speed = (($A / $deltaT) * 1.6965) - 0.1;
        if ($speed < 0) {
            $speed = 0.0;
        }
        return round($speed, 4);
    }

}

?>
