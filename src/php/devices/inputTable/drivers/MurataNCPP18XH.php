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
namespace HUGnet\devices\inputTable\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../Driver.php";

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
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class MurataNCPP18XH extends \HUGnet\devices\inputTable\Driver
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is the array of sensor information.
    */
    protected $params = array(
        "longName" => "Murata NCPP18XH Thermistor",
        "shortName" => "MurataNCPP18XH",
        "unitType" => "Temperature",
        "storageUnit" => '&#176;C',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Bias Resistor (kOhms)",
            "ADC Max Read",
        ),
        "extraDesc" => array(
            "The resistance connected between the thermistor and the reference
             voltage",
            "The max reading of the ADC.  Usually a power of 2",
        ),
        "extraNames" => array(
            "r" => 0,
            "Am" => 1,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, 10),
        "extraDefault" => array(160, 4096),
        "maxDecimals" => 2,
        "requires" => array("AI"),
        "provides" => array("DC"),
        "inputSize" => 2,
    );
    /** @var array The table for IMC Sensors */
    protected $valueTable = array(
        "195.65" => "-40.00",
        "148.17" => "-35.00",
        "113.35" => "-30.00",
        "87.56" => "-25.00",
        "68.24" => "-20.00",
        "53.65" => "-15.00",
        "42.51" => "-10.00",
        "33.89" => "-5.00",
        "27.22" => "0.00",
        "22.02" => "5.00",
        "17.93" => "10.00",
        "14.67" => "15.00",
        "12.08" => "20.00",
        "10.00" => "25.00",
        "8.32" => "30.00",
        "6.95" => "35.00",
        "5.83" => "40.00",
        "4.92" => "45.00",
        "4.16" => "50.00",
        "3.54" => "55.00",
        "3.01" => "60.00",
        "2.59" => "65.00",
        "2.23" => "70.00",
        "1.93" => "75.00",
        "1.67" => "80.00",
        "1.45" => "85.00",
        "1.27" => "90.00",
        "1.11" => "95.00",
        "0.97" => "100.00",
        "0.86" => "105.00",
        "0.76" => "110.00",
        "0.67" => "115.00",
        "0.60" => "120.00",
        "0.53" => "125.00",
    );
    /**
    * Converts resistance to temperature for IMCSolar thermistor
    * 10K thermistor.
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
        $Bias = $this->getExtra(0);
        $Am   = $this->getExtra(1);
        if ($A == $Am) {
            return null;
        }
        // self::AM is only half of the voltage the two resistors are connected
        // between.  Therefore, I need to multiply it by two to get the correct
        // reading.
        $kohms = (($A * $Bias) / ($Am - $A));
        $T     = $this->tableInterpolate($kohms, $this->valueTable);
        if (is_null($T)) {
            return null;
        }
        // tableInterpolate forces the result to be in range, or returns null
        $T = round($T, 4);
        return $T;
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
        $Bias  = $this->getExtra(0);
        $table = array_reverse(array_flip($this->valueTable), true);
        $Kohms = $this->tableInterpolate($value, $table) / 1000;
        $A     = $this->revResistance($Kohms, $Bias, $data["timeConstant"]);
        if (is_null($A)) {
            return null;
        }
        return (int)round($A);
    }

}
?>
