<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensorss
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class AVRDwyer616 extends \HUGnet\devices\inputTable\DriverAVR
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is the array of sensor information.
    */
    protected $params = array(
        "longName" => "Dwyer 616 Pressure Sensor",
        "shortName" => "Dwyer616",
        "unitType" => "Pressure",
        "storageUnit" => 'psi',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Min Current (mA)",
            "Max Current (mA)",
            "Read @ Min Current (psi)",
            "Read @ Max Current (psi)",
            "Sense Resistor (Ohms)",
            "Gain",
            "AtoD Ref Voltage (V)"
        ),
        "extraDesc" => array(
            "The minimum value the current from the pressure sensor",
            "The maximum value the current from the pressure sensor",
            "The pressure at the minimum current",
            "The pressure at the maximum current",
            "The current sense resistor value",
            "The gain on the current sense",
            "The reference voltage for the AtoD",
        ),
        "extraNames" => array(
            "minc"    => 0,
            "maxc"    => 1,
            "minread" => 2,
            "maxread" => 3,
            "r"       => 4,
            "gain"    => 5,
            "atodref" => 6,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, 5, 7, 7, 10, 5, 5),
        "extraDefault" => array(4, 20, -1.5, 1.5, 249, 1, 5.0),
        "maxDecimals" => 4,
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
        $Amin = $this->getExtra(0);
        $Amax = $this->getExtra(1);
        $Pmin = $this->getExtra(2);
        $Pmax = $this->getExtra(3);
        $R    = $this->getExtra(4);
        $Gain = $this->getExtra(5);
        $Vref = $this->getExtra(6);
        $Amps = $this->getCurrent($A, $R, $Gain, $Vref, $data["timeConstant"]);
        $mA = $Amps * 1000; // Convert to milliamps.
        $P = $this->linearBounded($mA, $Amin, $Amax, $Pmin, $Pmax);
        return round($P, 1);
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
        $Amin = $this->getExtra(0);
        $Amax = $this->getExtra(1);
        $Pmin = $this->getExtra(2);
        $Pmax = $this->getExtra(3);
        $R    = $this->getExtra(4);
        $Gain = $this->getExtra(5);
        $Vref = $this->getExtra(6);
        $mA = $this->linearBounded($value, $Pmin, $Pmax, $Amin, $Amax);
        $Amps = $mA / 1000;  // Convert to amps
        $A      = $this->revCurrent(
            $Amps, $R, $Gain, $Vref, $data["timeConstant"]
        );
        if (is_null($A) || is_null($value) || is_null($mA)) {
            return null;
        }
        return (int)round($A);
    }

}
?>
