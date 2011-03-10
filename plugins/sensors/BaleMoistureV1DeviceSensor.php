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
require_once dirname(__FILE__)."/../../base/sensors/ResistiveDeviceSensorBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Sensors
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class BaleMoistureV1DeviceSensor extends ResistiveDeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Bale Moisture Sensor Version 1",
        "Type" => "sensor",
        "Class" => "BaleMoistureV1DeviceSensor",
        "Flags" => array("01", "01:BaleMoistureV1"),
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(1);
    /** @var object These are the valid values for type */
    protected $typeValues = array("BaleMoistureV1");

    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Bale Moisture Sensor Version 1",
        "unitType" => "Percent",
        "storageUnit" => 'decimal',
        "storageType" => UnitsBase::TYPE_RAW,  // This is the dataType as stored
        "extraText" => array(
            "Bias Resistor (kOhms)",
            "Red Zone (kOhms)",
            "Yellow Zone (kOhms)",
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(10, 10, 10),
        "extraDefault" => array(100, 10000, 100000),
        "maxDecimals" => 3,
    );
    /** @var float Moisture red zone % */
    protected $Mr = 18;
    /** @var float Moisture yellow zone % */
    protected $My = 12;
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 0x01;
        $this->default["type"] = "BaleMoistureV1";
        parent::__construct($data, $device);
    }

    /**
    * This function calculates the open percentage based on the resistance seen.
    *
    * This is for V1 of the moisture sensor.  No more of these will be made.
    *
    * This sensor expects the following extras:
    *  0. The bias resistor
    *  1. The red zone resistance
    *  2. The yellow zone resistance
    *
    * It is not well documented.  It seems to contain the formula:
    *  - B = (My - Mr) / (log(Ry) - log(Rr))
    *  - A = Mr - (B * log(Rr))
    *  - M = A + (B * log(R));
    * where:
    * - M = Moisture (%)
    * - Mr = Minimum % for red zone (bad)
    * - My = Minimum % for yellow zone (marginal)
    * - Rr = Maximum Ohms for red zone (bad)
    * - Ry = Maximum Ohms for yellow zone (marginal)
    * - A = ???
    * - B = ???
    *
    * I think this formula is based on logrythmic curves with the points
    * (Ry, My) and (Rr, Mr).  Resistance and Moiture have an inverse
    * relationship.
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    */
    public function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        $Bias = $this->getExtra(0);
        $Rr   = $this->getExtra(1);
        $Ry   = $this->getExtra(2);
        if ($Ry <= $Rr) {
            return null;
        }
        $R = $this->getResistance($A, $Bias);

        if ($R == 0) {
            return(35.0);
        }
        //$R is coming in k Ohms.  We need Ohms.
        $R = $R * 1000;
        //$num = $this->My - $this->Mr;
        //$den = log($Ry) - log($Rr);
        // The denominator can't be zero because Ry !== Rr
        $B = ($this->My - $this->Mr) / (log($Ry) - log($Rr)); //$num / $den;
        $A = $this->Mr - ($B * log($Rr));
        $M = $A + ($B * log($R));

        if (($M > 35) || ($M < 0)) {
            return null;
        }
        return round($M, $this->decimals);
    }

    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>
