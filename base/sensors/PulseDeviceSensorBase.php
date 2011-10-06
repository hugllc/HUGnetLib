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
 * @subpackage SensorBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/DeviceSensorBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage SensorBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class PulseDeviceSensorBase extends DeviceSensorBase
{
    /** These are the endpoint information bits */
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
        "filter" => array(),             // Information on the output filter
    );
    /** @var object These are the valid values for unitType */
    protected $unitTypeValues = array();
    /** @var object These are the valid values for units */
    protected $unitsValues = array();
    /** @var object These are the valid values for type */
    protected $typeValues = array();

    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        parent::__construct($data, $device);
    }

    /**
    * Returns whether the reading is valid
    *
    * @param int $value The current sensor value
    *
    * @return bool
    */
    function pulseCheck($value)
    {
        if ($value < 0) {
            return false;
        }
        return true;
    }

    /**
    * Crunches the numbers for the Liquid Flow Meter
    *
    * @param int   $val    Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    *                      and the last one
    *
    * @return float
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    function liquidFlowMeter($val, $deltaT=null)
    {
        $extra = $this->getExtra(0);
        if (empty($extra)) {
            $extra = 1;
        }
        $G = $val / $extra;
        if ($G < 0) {
            return null;
        }
        return round((float)$G, 4);
    }

    /**
    * This is for a generic pulse counter
    *
    * @param int   $val    Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    *                      and the last one
    *
    * @return float
    */
    function getPPM($val, $deltaT)
    {
        if ($deltaT <= 0) {
            return null;
        }
        $ppm = ($val / $deltaT) * 60;
        if ($ppm < 0) {
            return null;
        }
        return round($ppm, 4);
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
        $ret = parent::fromArray($array);
        if ($this->id == 0x7F) {
            $this->fixed["longName"] = "High Speed ".$this->fixed["longName"];
        }
        return $ret;
    }

}
?>
