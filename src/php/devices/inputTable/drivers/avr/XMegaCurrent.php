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
class XMegaCurrent extends \HUGnet\devices\inputTable\DriverAVR
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /** This is a constant */
    const AM = 2048;
    /** This is a constant */
    const S = 1;
    /** This is a constant */
    const TF = 1;
    /** This is a constant */
    const D = 1;
    /**
    * This is the array of sensor information.
    */
    protected $params = array(
        "longName" => "Xmega Current Sensor",
        "shortName" => "XMegaCurrent",
        "unitType" => "Current",
        "storageUnit" => 'mA',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Sense Resistor (Ohms)",
            "Gain",
            "AtoD Ref Voltage (V)"
        ),
        "extraDesc" => array(
            "The current sense resistor",
            "Any gain between the resistor and the AtoD.",
            "The power supply voltage for the board.  Normally 5",
        ),
        "extraNames" => array(
            "r"       => 0,
            "gain"    => 1,
            "atodref" => 2,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(7, 7, 5),
        "extraDefault" => array(0.001, 64.0, 1.0),
        "maxDecimals" => 4,
        "requires" => array("AI", "ATODREF"),
        "provides" => array("DC"),
        "inputSize" => 2,
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
        $R    = $this->getExtra(0);
        $Gain = $this->getExtra(1);
        $Vref = $this->getExtra(2);
        $A    = $this->twosCompliment($A, $this->get("inputSize") * 8);

        $Amps = $this->getCurrent($A, $R, $Gain, $Vref, $data["timeConstant"]);
        return round($Amps * 1000, 1);  // Return mA
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
        $R    = $this->getExtra(0);
        $Gain = $this->getExtra(1);
        $Vref = $this->getExtra(2);
        $A      = $this->revCurrent(
            $value / 1000, $R, $Gain, $Vref, $data["timeConstant"]
        );
        if (is_null($A) ||is_null($value)) {
            return null;
        }
        return (int)round($A);
    }

}
?>
