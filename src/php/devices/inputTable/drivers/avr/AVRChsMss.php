<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @subpackage PluginsSensorss
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\avr;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../DriverAVR.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class AVRChsMss extends \HUGnet\devices\inputTable\DriverAVR
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is the array of sensor information.
    */
    protected $params = array(
        "longName" => "TDK CHS-MSS",
        "shortName" => "ChsMss",
        "unitType" => "RelativeHumidity",
        "storageUnit" => '%',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Min Voltage (V)",
            "Max Voltage (V)",
            "Read @ Min Voltage (%)",
            "Read @ Max Voltage (%)",
            "AtoD Ref Voltage (V)"
        ),
        "extraDesc" => array(
            "The minimum value the voltage from the relative humidity sensor",
            "The maximum value the voltage from the relative humidity sensor",
            "The relative humidity at the minimum voltage",
            "The relative humidity at the maximum voltage",
            "The reference voltage for the AtoD",
        ),
        "extraNames" => array(
            "minv"    => 0,
            "maxv"    => 1,
            "minread" => 2,
            "maxread" => 3,
            "atodref" => 4,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, 5, 4, 4, 5),
        "extraDefault" => array(0, 1, 0, 100, 1.1),
        "maxDecimals" => 2,
        "requires" => array("AI", "ATODREF"),
        "provides" => array("DC"),
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
    protected function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {

        bcscale(6);
        $Vmin = $this->getExtra(0);
        $Vmax = $this->getExtra(1);
        $Hmin = $this->getExtra(2);
        $Hmax = $this->getExtra(3);
        $Vref = $this->getExtra(4);
        $V = $this->getVoltage($A, $Vref, $data["timeConstant"]);
        $H = $this->linearBounded($V, $Vmin, $Vmax, $Hmin, $Hmax);
        return round($H, 2);
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
        bcscale(6);
        $Vmin = $this->getExtra(0);
        $Vmax = $this->getExtra(1);
        $Hmin = $this->getExtra(2);
        $Hmax = $this->getExtra(3);
        $Vref = $this->getExtra(4);
        $V      = $this->linearUnbounded($value, $Hmin, $Hmax, $Vmin, $Vmax);
        $A      = $this->revVoltage($V, $Vref, $data["timeConstant"]);
        if (is_null($V) || is_null($A)) {
            return null;
        }
        return (int)round($A);
    }

}
?>
