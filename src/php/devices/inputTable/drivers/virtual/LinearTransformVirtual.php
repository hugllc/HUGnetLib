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
namespace HUGnet\devices\inputTable\drivers\virtual;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../DriverVirtual.php";

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
class LinearTransformVirtual extends \HUGnet\devices\inputTable\DriverVirtual
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Linear Transform Virtual Sensor",
        "shortName" => "LinearTVirtual",
        "unitType" => "getExtra6",
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            5, 10, 10, 15, 15, 10, 15,
            array(
                \HUGnet\devices\datachan\Driver::TYPE_RAW
                    => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                \HUGnet\devices\datachan\Driver::TYPE_DIFF
                    => \HUGnet\devices\datachan\Driver::TYPE_DIFF
            ),
            3,
        ),
        "extraText" => array(
            "Input", "Slope", "Y Intercept", "Y Min", "Y Max", "Storage Unit",
            "Unit Type", "Data Type", "Max Decimals"
        ),
        "extraDefault" => array(
            "", 0, 0, "none", "none", "unknown", "Generic",
            \HUGnet\devices\datachan\Driver::TYPE_RAW, 4
        ),
        "storageType" => "getExtra7",
        "storageUnit" => "getExtra5",
        "maxDecimals" => "getExtra8",

        "virtual" => true,              // This says if we are a virtual sensor
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
        $index = ((int)$this->getExtra(0));
        $y = $data[$index]["value"];
        if (is_null($y)) {
            return null;
        }
        $m = $this->getExtra(1);
        $b = $this->getExtra(2);
        $x = ($m * $y) + $b;
        $min = $this->getExtra(3);
        $max = $this->getExtra(4);
        if (is_numeric($min) && ($x < $min)) {
            $x = $min;
        } else if (is_numeric($max) && ($x > $max)) {
            $x = $max;
        }
        $x = round($x, $this->get("maxDecimals"));
        return $x;
    }

}


?>
