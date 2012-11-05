<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensorss
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\sensors\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../DriverAVR.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class AVRDwyer616 extends \HUGnet\sensors\DriverAVR
{
    /**
    * This is the array of sensor information.
    */
    protected $params = array(
        "longName" => "Dwyer 616 Pressure Sensor",
        "unitType" => "Pressure",
        "storageUnit" => 'psi',
        "storageType" => \HUGnet\channels\Driver::TYPE_RAW,
        "extraText" => array(
            "Min Current (mA)",
            "Max Current (mA)",
            "Read @ Min Current (psi)",
            "Read @ Max Current (psi)",
            "Sense Resistor (Ohms)",
            "Gain",
            "AtoD Ref Voltage (V)"
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
    public function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
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
        return $this->linearBounded($mA, $Amin, $Amax, $Pmin, $Pmax);
    }

}
?>
