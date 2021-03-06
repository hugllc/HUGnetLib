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
 * @since      0.9.9
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class BinaryVirtual extends \HUGnet\devices\inputTable\DriverVirtual
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Binary Virtual Sensor",
        "shortName" => "BinaryVirtual",
        "unitType" => "Percent",
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            5, 10, 10, 10, 15, 15, 5
        ),
        "extraText" => array(
            "input", "High Threshold", "Low Threshold", "Multiplier", "Storage Unit",
            "Unit Type", "Max Decimals"
        ),
        "extraDefault" => array(
            array(), 0, 1, 1, "decimal", "Percent", 4
        ),
        "extraDesc" => array(
            "The data channel to use for the input",
            "The threshold above which we can count the value as high",
            "The threshold below which we can count the calue as low",
            "The output scale to use.  The output will be either 0, or this number",
            "The units that the return is in",
            "The type that the units are in.  Valid values include Pressure,
             Temperature, Relative Humidity, Impedance, Power, Voltage, Current
             and others.",
            "The maximum number of valid decimal places",
        ),
        "extraNames" => array(
            "datachan0"   => 0,
            "highthresh"  => 1,
            "lowthresh"   => 2,
            "multiplier"  => 3,
            "storageunit" => 4,
            "unittype"    => 5,
            "maxdecimals" => 6,
        ),
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "storageUnit" => "decimal",
        "maxDecimals" => 4,

        "virtual" => true,              // This says if we are a virtual sensor
        "dataTypes" => array(
            \HUGnet\devices\datachan\Driver::TYPE_IGNORE
                => \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
            \HUGnet\devices\datachan\Driver::TYPE_RAW
                => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        ),
        "requires" => array("DC"),
        "provides" => array("DC"),
    );
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    public function get($name)
    {
        $ret = parent::get($name);
        if ($name == "extraValues") {
            $ret[0] = $this->input()->device()->dataChannels()->select();
        }
        return $ret;
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
    protected function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        $index = ((int)$this->getExtra(0));
        $mult  = (float)$this->getExtra(3);
        if (($index < 0) || empty($mult)) {
            return null;
        }
        $val = $data[$index]["value"];
        if (is_null($val)) {
            return $prev;
        }
        $out = (int)($prev / $mult);
        $high = (float)$this->getExtra(1);
        if ($val >= $high) {
            $out = 1;
        }
        $low  = (float)$this->getExtra(2);
        if ($val <= $low) {
            $out = 0;
        }
        return round($out * $mult, $this->maxDecimals);

    }

}


?>
