<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\sensors\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../DriverVirtual.php";

/**
 * Driver for reading voltage based pressure sensors
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.9
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ComputationVirtual extends \HUGnet\sensors\DriverVirtual
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Computation Virtual Sensor",
        "unitType" => "getExtra2",
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            20, 10, 15,
            array(
                \HUGnet\units\Driver::TYPE_RAW => \HUGnet\units\Driver::TYPE_RAW,
                \HUGnet\units\Driver::TYPE_DIFF => \HUGnet\units\Driver::TYPE_DIFF
            ),
            3,
            array(0 => "No", 1 => "Yes"),
        ),
        "extraText" => array(
            "Math", "Storage Unit", "Unit Type", "Data Type", "Max Decimals",
            "Treat Null as Zero"
        ),
        "extraDefault" => array(
            "", "unknown", "Generic", \HUGnet\units\Driver::TYPE_RAW, 4, 0
        ),
        "storageType" => "getExtra3",
        "storageUnit" => "getExtra1",
        "maxDecimals" => "getExtra4",

        "virtual" => true,              // This says if we are a virtual sensor
        "dataTypes" => array(
            \HUGnet\units\Driver::TYPE_IGNORE => \HUGnet\units\Driver::TYPE_IGNORE,
            \HUGnet\units\Driver::TYPE_RAW => \HUGnet\units\Driver::TYPE_RAW,
        ),
    );
    /**
    * This function creates the system.
    *
    * @param object &$sensor The sensor object
    *
    * @return null
    */
    public static function &factory(&$sensor)
    {
        return parent::intFactory($sensor);
    }
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
    public function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        $fct = $this->createFunction($this->getExtra(0), $data);
        $ret = (is_callable($fct)) ? @call_user_func($fct) : false;
        if (!is_bool($ret)) {
            $ret = round($ret, $this->get("maxDecimals"));
        } else {
            $ret = null;
        }
        return $ret;

    }
    /**
     * Creates a function to crunch numbers
     *
     * @param string $math  The math to use
     * @param array  &$data The data from the other sensors that were crunched
     *
     * @return string
     */
    protected function createFunction($math, &$data)
    {
        $zero = (bool)$this->getExtra(5);
        $mathCode = $math;
        for ($i = 1; $i < 20; $i++) {
            $index = $i - 1;
            $mathCode = str_ireplace(
                '{'.$i.'}', (float)$data[$index]["value"], $mathCode, $count
            );
            if (is_null($data[$index]["value"]) && ($count > 0) && !$zero) {
                $mathCode = false;
                break;
            }
        }
        if (is_string($mathCode)) {
            $text = "return ".$this->sanatize($mathCode).";";
        } else {
            $text = "return false;";
        }
        return @create_function("", $text);
    }
    /**
     * Creates a function to crunch numbers
     *
     * @param string $string The string to sanatize
     *
     * @return bool|string The name of the function created.
     */
    protected function sanatize($string)
    {
        $string = preg_replace(
            '/[^0-9\-\/\+\*\(\)\.]+/',
            "",
            $string
        );
        return $string;
    }

}


?>