<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\avr;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../DriverAVR.php";
/** This is the units class */
require_once dirname(__FILE__)."/../../../datachan/Driver.php";

/**
 * Driver for reading voltage based pressure sensors
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.9
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ControllerVoltage extends \HUGnet\devices\inputTable\DriverAVR
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Controller Board Voltage Sensor",
        "shortName" => "ContVoltage",
        "unitType" => "Voltage",
        "storageUnit" => 'V',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "R1 to Source (kOhms)",
            "R2 to Ground (kOhms)",
            "Vcc"
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, 5, 5),
        "extraDefault" => array(180, 27, 5.0),
        "inputSize" => 2,
        "maxDecimals" => 4,
        "dataTypes" => array(
            \HUGnet\devices\datachan\Driver::TYPE_IGNORE
                => \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
            \HUGnet\devices\datachan\Driver::TYPE_RAW
                => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        ),
    );
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
    protected function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        return null;
    }
    /**
    * This crunches the actual numbers for the sensor data
    *
    * Here is the actual sensor array (actual view):
    *    Input 0: HUGnet2 Current
    *    Input 1: HUGnet2 Temp
    *    Input 2: HUGnet2 Voltage Low    <---
    *    Input 3: HUGnet2 Voltage High   <---
    *    Input 4: HUGnet1 Voltage High   <---
    *    Input 5: HUGnet1 Voltage Low    <---
    *    Input 6: HUGnet1 Temp
    *    Input 7: HUGnet1 Current
    *
    * This is what we put forward to the world (world view):
    *    Output 0: HUGnet1 Voltage       <---
    *    Output 1: HUGnet1 Current
    *    Output 2: HUGnet1 Temp
    *    Output 3: HUGnet2 Voltage       <---
    *    Output 4: HUGnet2 Current
    *    Output 5: HUGnet2 Temp
    *
    * @param string &$string The data string
    * @param float  $deltaT  The time delta in seconds between this record
    * @param array  &$prev   The previous reading
    * @param array  &$data   The data from the other sensors that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function decodeData(
        &$string, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        if ($this->input()->id() == 0) {
            $size = $this->get("inputSize") * 2;
            /* Remove the current and temp */
            $pre = substr($string, 0, $size * 2);
            $string = substr($string, $size * 2);
            /* Get our values */
            $Al = $this->strToInt($string);
            $Ah = $this->strToInt($string);
            /* Put back the first part of the string */
            $string = $pre.$string;
        } else {
            $Ah = $this->strToInt($string);
            $Al = $this->strToInt($string);
        }
        $Vh    = $this->indirectVoltage($Ah, 1);
        $Vl    = $this->indirectVoltage($Al, 1);
        $ret = $this->channels();
        if (!is_null($Vh) && !is_null($Vl)) {
            $ret[0]["value"] = $Vh - $Vl;
        } else {
            $ret[0]["value"] = null;
        }
        return $ret;
    }

}


?>
