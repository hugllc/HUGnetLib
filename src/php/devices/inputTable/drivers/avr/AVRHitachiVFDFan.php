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
class AVRHitachiVFDFan extends \HUGnet\devices\inputTable\DriverAVR
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is the array of sensor information.
    */
    protected $params = array(
        "longName" => "Hitachi VFD Fan Speed Sensor",
        "shortName" => "HitachiVFD",
        "unitType" => "Percent",
        "storageUnit" => 'decimal',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "R1 in kOhms",
            "R2 in kOhms",
            "Min Voltage (V)",
            "Max Voltage (V)",
            "Read @ Min Voltage (%)",
            "Read @ Max Voltage (%)",
            "AtoD Ref Voltage (V)"
        ),
        "extraDesc" => array(
            "The value of the resistor connecting the AtoD and the VFD",
            "The value of the resistor connecting the AtoD to ground",
            "The minimum value the voltage from the VFD will be",
            "The maximum value the voltage from the VFD will be",
            "The speed of the motors at the minimum voltage",
            "The speed of the motors at the maximum voltage",
            "The reference voltage for the AtoD",
        ),
        "extraNames" => array(
            "r1"      => 0,
            "r2"      => 1,
            "minv"    => 2,
            "maxv"    => 3,
            "minread" => 4,
            "maxread" => 5,
            "atodref" => 6,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, 5, 5, 5, 7, 7, 5),
        "extraDefault" => array(51, 33, 0, 10, 0, 100, 5),
        "maxDecimals" => 4,
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
        $R1   = $this->getExtra(0);
        $R2   = $this->getExtra(1);
        $Vmin = $this->getExtra(2);
        $Vmax = $this->getExtra(3);
        $Omin = $this->getExtra(4);
        $Omax = $this->getExtra(5);
        $Vref = $this->getExtra(6);
        $V = $this->getDividerVoltage($A, $R1, $R2, $Vref, $data["timeConstant"]);
        return round(
            $this->linearBounded($V, $Vmin, $Vmax, $Omin, $Omax) / 100,
            $this->get("maxDecimals")
        );
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
        $R1   = $this->getExtra(0);
        $R2   = $this->getExtra(1);
        $Vmin = $this->getExtra(2);
        $Vmax = $this->getExtra(3);
        $Omin = $this->getExtra(4);
        $Omax = $this->getExtra(5);
        $Vref = $this->getExtra(6);
        $V    = $this->linearBounded($value * 100, $Omin, $Omax, $Vmin, $Vmax);
        $A    = $this->revDividerVoltage(
            $V, $R1, $R2, $Vref, $data["timeConstant"]
        );
        if (($A < 0) || is_null($A) || is_null($value)) {
            return null;
        }
        return (int)round($A);
    }

}
?>
