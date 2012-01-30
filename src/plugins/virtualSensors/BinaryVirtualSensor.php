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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsVirtualSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/VirtualSensorBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsVirtualSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class BinaryVirtualSensor extends VirtualSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Binary Virtual Sensor",
        "Type" => "sensor",
        "Class" => "BinaryVirtualSensor",
        "Flags" => array("FE:binary"),
    );
    /** @var object These are the valid values for type */
    protected $typeValues = array("binary");
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Binary Virtual Sensor",
        "unitType" => "Percent",
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            5, 10, 10, 10, 15, 15, 5
        ),
        "extraText" => array(
            "input", "High Threshold", "Low Threshold", "Multiplier", "Storage Unit",
            "Unit Type", "Max Decimals"
        ),
        "extraDefault" => array(
            1, 0, 1, 1, "decimal", "Percent", 4
        ),
        "storageType" => UnitsBase::TYPE_RAW,  // This is the dataType as stored
        "storageUnit" => "decimal",
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
        $this->default["type"] = "binary";
        if (isset($data["extra"][4])) {
            $this->fixed["storageUnit"] = (string)$data["extra"][4];
        }
        if (isset($data["extra"][5])) {
            $this->fixed["unitType"] = (string)$data["extra"][5];
        }
        if (isset($data["extra"][6])) {
            $this->fixed["maxDecimals"] = (int)$data["extra"][6];
        }
        parent::__construct($data, $device);
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
        $index = ((int)$this->getExtra(0)) - 1;
        $mult  = (float)$this->getExtra(3);
        if (($index < 0) || empty($mult)) {
            return null;
        }
        $val = $data[$index]["value"];
        if (is_null($val)) {
            return $prev;
        }
        $out = (int)($prev / $mult);
        $high = (float)$this->getExtra(1);
        if ($val >= $high) {
            $out = 1;
        }
        $low  = (float)$this->getExtra(2);
        if ($val <= $low) {
            $out = 0;
        }
        return round($out * $mult, $this->maxDecimals);
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>
