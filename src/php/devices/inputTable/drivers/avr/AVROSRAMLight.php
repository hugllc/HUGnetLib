<?php
/**
 * Sensor driver for wind direction sensors
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @subpackage PluginsSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
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
/**
 * This class deals with wind direction sensors.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class AVROSRAMLight extends \HUGnet\devices\inputTable\DriverAVR
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is the array of sensor information.
    */
    protected $params = array(
        "longName" => "OSRAM BPW-34 Photodiode",
        "shortName" => "OSRAMLight",
        "unitType" => "HeatPerUnitArea",
        "storageUnit" => 'W/m^2',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(),
        "extraDesc" => array(),
        "extraNames" => array(
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(),
        "extraDefault" => array(),
        "maxDecimals" => 2,
        "requires" => array("AI"),
        "provides" => array("DC"),
    );

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
    protected function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        $L  = (-1500.0)*$A * self::TF * $data["timeConstant"];
        $L  = $L / (self::AM * self::S * self::D);
        $L += 1500.0;

        return round($L, 4);

    }
    /**
    * Returns the reversed reading
    *
    * @param array $value   The data to use
    * @param int   $channel The channel to get
    * @param float $deltaT  The time delta in seconds between this record
    * @param array &$prev   The previous reading
    * @param array &$data   The data from the other sensors that were crunched
    *
    * @return string The reading as it would have come out of the endpoint
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getRaw(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $value -= 1500;
        $denom = (-1500.0) * self::TF * $data["timeConstant"];
        if ($denom == 0) {
            return null;
        }
        $A = $value * (self::AM * self::S * self::D) / $denom;
        return (int)round($A);
    }
}

?>
