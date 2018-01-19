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
namespace HUGnet\devices\inputTable\drivers\virtual;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../DriverVirtual.php";
/** This is my base class */
require_once dirname(__FILE__)."/../../../../contrib/evalmath.class.php";

/**
 * Driver for reading voltage based pressure sensors
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.9
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ComputationVirtual extends \HUGnet\devices\inputTable\DriverVirtual
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Computation Virtual Sensor",
        "shortName" => "Computation",
        "unitType" => "getExtra2",
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            200, 10, 15,
            array(
                \HUGnet\devices\datachan\Driver::TYPE_RAW
                    => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                \HUGnet\devices\datachan\Driver::TYPE_DIFF
                    => \HUGnet\devices\datachan\Driver::TYPE_DIFF
            ),
            3,
            array(0 => "No", 1 => "Yes"),
        ),
        "extraText" => array(
            "Math", "Storage Unit", "Unit Type", "Data Type", "Max Decimals",
            "Treat Null as Zero"
        ),
        "extraDefault" => array(
            "", "unknown", "Generic", \HUGnet\devices\datachan\Driver::TYPE_RAW, 4, 0
        ),
        "extraDesc" => array(
            "The math routine.  Most standard PHP math functions are accepted, plus
             operators.  To put in data channels add {x} into the math, where x
             is the number of the data channel (zero based).",
            "The unit that the output will be in",
            "The type that the units are in.  Valid values include Pressure,
             Temperature, Relative Humidity, Impedance, Power, Voltage, Current
             and others.",
            "The data type the output are in",
            "The maximum number of valid decimal places",
            "If Yes, then invalid values from the data channels (null) will be 
             treated as zero in the math.  If No, then if any of the data channels
             referenced have invalid values this will output a null."
        ),
        "extraNames" => array(
            "math"        => 0, 
            "storageunit" => 1, 
            "unittype"    => 2,
            "datatype"    => 3,
            "maxdecimals" => 4,
            "zeronull"    => 5,
        ),
        "storageType" => "getExtra3",
        "storageUnit" => "getExtra1",
        "maxDecimals" => "getExtra4",

        "virtual" => true,              // This says if we are a virtual sensor
        "dataTypes" => array(
            \HUGnet\devices\datachan\Driver::TYPE_IGNORE
                => \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
            \HUGnet\devices\datachan\Driver::TYPE_RAW
                => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        ),
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
        $fct = $this->createFunction($this->getExtra(0), $data);
        //$ret = (is_callable($fct)) ? @call_user_func($fct) : false;
        $math = new \EvalMath;
        $math->suppress_errors = true;
        $ret = $math->e($fct);
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
        for ($i = 0; $i < 40; $i++) {
            $mathCode = str_ireplace(
                '{'.$i.'}',
                sprintf("%f", (float)$data[$i]["value"]),
                $mathCode,
                $count
            );
            if (is_null($data[$i]["value"]) && ($count > 0) && !$zero) {
                $mathCode = false;
                break;
            }
        }
        return $mathCode;
    }

}


?>
