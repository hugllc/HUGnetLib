<?php
/**
 * Classes for dealing with devices
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
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
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
 * Sensor driver for direct voltage reading on the ADuC706x
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCVishayRTD extends \HUGnet\devices\inputTable\DriverADuC
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Vishay Platinum Temperature Sensor RTD",
        "shortName" => "ADUCVishayRTD",
        "unitType" => "Temperature",
        "storageUnit" => '&#176;C',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array("Bias Resistor (Ohms)"),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5),
        "extraDefault" => array(2210),
        "maxDecimals" => 4,
        "inputSize" => 4,
    );
    /** @var array The lookup table */
    private $_valueTable = array(
        "78.32" => -55, "80.31" => -50, "82.29" => -45,
        "84.27" => -40, "86.25" => -35, "88.22" => -30,
        "90.19" => -25, "92.16" => -20, "94.12" => -15,
        "96.09" => -10, "98.04" => -5, "100.00" => 0,
        "101.95" => 5, "103.90" => 10, "105.85" => 15,
        "107.79" => 20, "109.73" => 25, "111.67" => 30,
        "113.61" => 35, "115.54" => 40, "117.47" => 45,
        "119.40" => 50, "121.32" => 55, "123.24" => 60,
        "125.16" => 65, "127.08" => 70, "128.99" => 75,
        "130.90" => 80, "132.80" => 85, "134.71" => 90,
        "136.61" => 95, "138.51" => 100, "140.40" => 105,
        "142.29" => 110, "144.18" => 115, "146.07" => 120,
        "147.95" => 125, "149.83" => 130, "151.71" => 135,
        "153.58" => 140, "155.46" => 145, "157.33" => 150,
        "159.19" => 155,
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
        $Am    = pow(2, 23);
        $Rbias = $this->getExtra(0);

        $A = abs($A);
        if ($A == $Am) {
            return null;
        }
        $R = (float)(($A * $Rbias) / ($Am - $A));
        $T = $this->_tableInterpolate($R, $this->_valueTable);
        return round($T, $this->get('maxDecimals', 1));
    }
    /**
    * This function should be called with the values set for the specific
    * thermistor that is used.
    *
    * @param float $input  The input value
    * @param array &$table The table to look through.
    *
    * @return float The Temperature in degrees C
    */
    private function _tableInterpolate($input, &$table)
    {
        $max = max(array_keys($table));
        $min = min(array_keys($table));
        if (($input < $min) || ($input > $max)) {
            return null;
        }
        foreach (array_keys($table) as $key) {
            $last = $key;
            if ((float)$key > $input) {
                break;
            }
            $prev = $key;
        }
        $out   = $table[$prev];
        $fract = ($prev - $input) / ($prev - $last);
        $diff  = $fract * ($table[$last] - $table[$prev]);
        return (float)($out + $diff);
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
        $Am    = pow(2, 23);
        $Rbias = $this->getExtra(0);

        if (is_null($value)) {
            return null;
        }
        $table = array_flip($this->_valueTable);
        $R = $this->_tableInterpolate($value, $table);
        $A = ($R * $Am) / ($Rbias + $R);
        return (int)round(($A * -1));

    }
}


?>
