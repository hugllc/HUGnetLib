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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\sensors\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../DriverVirtual.php";

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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.9
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class AlarmVirtual extends \HUGnet\sensors\DriverVirtual
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Alarm Virtual Sensor",
        "unitType" => "Percent",
        "shortName" => "AlarmVirtual",
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            5, 10, 10
        ),
        "extraText" => array(
            "input", "Alarm Threshold", "Reset Threshold"
        ),
        "extraDefault" => array(
            1, 1, 0
        ),
        "storageType" => \HUGnet\channels\Driver::TYPE_RAW,
        "storageUnit" => "decimal",
        "maxDecimals" => 4,
        "virtual" => true,              // This says if we are a virtual sensor
        "dataTypes" => array(
            \HUGnet\channels\Driver::TYPE_IGNORE
                => \HUGnet\channels\Driver::TYPE_IGNORE,
            \HUGnet\channels\Driver::TYPE_RAW => \HUGnet\channels\Driver::TYPE_RAW,
        ),
    );
    /**
    * This function creates the system.
    *
    * @param object &$sensor The sensor object
    *
    * @return null
    */
    public static function &factory(&$sensor)
    {
        return parent::intFactory($sensor);
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
    public function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        $index = ((int)$this->getExtra(0)) - 1;
        if (($index < 0)) {
            return null;
        }
        $alarm = (float)$this->getExtra(1);
        $reset = (float)$this->getExtra(2);
        $val = $data[$index]["value"];
        $out = (int)($prev != 0);
        if (is_null($val)) {
            if (is_null($prev)) {
                return $prev;
            } else {
                return $out;
            }
        }
        if ($alarm > $reset) {
            // If the alarm value is greater than the reset value we need the
            // following:
            if ($val <= $reset) {
                $out = 0;
            }
            if ($val >= $alarm) {
                $out = 1;
            }
        } else {
            // If the alarm value is less than the reset value we need the
            // following:
            if ($val >= $reset) {
                $out = 0;
            }
            if ($val <= $alarm) {
                $out = 1;
            }
        }
        return $out;

    }

}


?>
