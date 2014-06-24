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
 * @since      0.11.0
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class MKS901PPressure extends \HUGnet\devices\inputTable\DriverADuC
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "MKS 901P Pressure Sensor",
        "shortName" => "MKS901PPressure",
        "unitType" => "Pressure",
        "storageUnit" => 'Torr',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Voltage Ref (V)",
            "R input (kOhms)",
            "R to ground (kOhms)"
        ),
        "extraDesc" => array(
            "The reference voltage for the AtoD",
            "The input resistance to the AtoD",
            "The resistor connecting the pressure sensor to ground",
        ),
        "extraNames" => array(
            "atodref" => 0,
            "rin"     => 1,
            "r"       => 2,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(20, 20, 20),
        "extraDefault" => array(1.2, 100, 1),
        "maxDecimals" => 6,
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
        $Vref  = $this->getExtra(0);
        $Rin   = $this->getExtra(1);
        $Rbias = $this->getExtra(2);

        $A = $this->inputBiasCompensation($A, $Rin, $Rbias);

        $Va = bcmul(bcdiv($A, $Am), $Vref);
        $P  = pow(10, $Va - 6);

        return round($P, $this->get("maxDecimals"));
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
        $Vref  = $this->getExtra(0);
        $Rin   = $this->getExtra(1);
        $Rbias = $this->getExtra(2);

        if ($Vref == 0) {
            return null;
        }

        $Va = log($value, 10) + 6;
        $A = (int)round(bcmul(bcdiv($Va, $Vref), $Am));
        $Amod = $this->inputBiasCompensation($A, $Rin, $Rbias);
        if ($Amod != 0) {
            $A = $A * ($A / $Amod);
        }
        return (int)round($A);
    }
}


?>
