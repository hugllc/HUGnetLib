<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\aduc;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../DriverADuC.php";

/**
 * Driver for reading voltage based pressure sensors
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCPressure extends \HUGnet\devices\inputTable\DriverADuC
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "ADuC Pressure Sensor",
        "shortName" => "ADuCPressure",
        "unitType" => "Pressure",
        "storageUnit" => 'psi',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Voltage @ point A (V)",
            "Voltage @ point B (V)",
            "Read @ A Voltage (psi)",
            "Read @ B Voltage (psi)",
            "Voltage Ref (V)",
            "R input (kOhms)",
            "R to ground (kOhms)"
        ),
        "extraDesc" => array(
            "The input voltage at an arbitrary point on the transform line",
            "The input voltage at a second arbitrary point on the transform line",
            "The pressure reading at the first arbitrary voltage",
            "The pressure reading at the second arbitrary voltage",
            "The AtoD reference voltage",
            "The input resistance to the AtoD",
            "The resistor connecting the AtoD to ground",
        ),
        "extraNames" => array(
            "volta"   => 0,
            "voltb"   => 1,
            "reada"   => 2,
            "readb"   => 3,
            "atodref" => 4,
            "rin"     => 5,
            "r"       => 6,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(20, 20, 20, 20, 20, 20, 20),
        "extraDefault" => array(0, 5, 0, 100, 1.2, 100, 1),
        "maxDecimals" => 4,
        "inputSize" => 4,
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
    protected function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        bcscale(20);
        $Am   = pow(2, 23);
        $Vmin  = $this->getExtra(0);
        $Vmax  = $this->getExtra(1);
        $Pmin  = $this->getExtra(2);
        $Pmax  = $this->getExtra(3);
        $Vref  = $this->getExtra(4);
        $Rin   = $this->getExtra(5);
        $Rbias = $this->getExtra(6);

        $A = $this->inputBiasCompensation($A, $Rin, $Rbias);

        $Va = ($A / $Am) * $Vref;
        $P = $this->linearUnbounded($Va, $Vmin, $Vmax, $Pmin, $Pmax);

        return round($P, $this->get("maxDecimals", 1));

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
        bcscale(20);
        $Am   = pow(2, 23);
        $Vmin  = $this->getExtra(0);
        $Vmax  = $this->getExtra(1);
        $Pmin  = $this->getExtra(2);
        $Pmax  = $this->getExtra(3);
        $Vref  = $this->getExtra(4);
        $Rin   = $this->getExtra(5);
        $Rbias = $this->getExtra(6);

        if ($Vref == 0) {
            return null;
        }

        $Va = $this->linearUnbounded($value, $Pmin, $Pmax, $Vmin, $Vmax);
        $A = (int)round(($Va / $Vref) * $Am);
        $Amod = $this->inputBiasCompensation($A, $Rin, $Rbias);
        if ($Amod != 0) {
            $A = $A * ($A / $Amod);
        }
        return (int)round($A);
    }
}


?>
