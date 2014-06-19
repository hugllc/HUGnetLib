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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.9
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class LinearTransformVirtual extends \HUGnet\devices\inputTable\DriverVirtual
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Linear Transform Virtual Sensor",
        "shortName" => "LinearTVirtual",
        "unitType" => "getExtra7",
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            array(), 15, 15, 15, 15,
            array("unbounded" => "Not Bound", "bounded" => "Bound"),
            10, 15,
            array(
                \HUGnet\devices\datachan\Driver::TYPE_RAW
                    => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                \HUGnet\devices\datachan\Driver::TYPE_DIFF
                    => \HUGnet\devices\datachan\Driver::TYPE_DIFF
            ),
            3,
        ),
        "extraText" => array(
            "Input",
            "Orig Value @ Point A",
            "Orig Value @ Point B",
            "New Value @ Point A",
            "New Value @ Point B",
            "Type",
            "Storage Unit",
            "Unit Type",
            "Data Type",
            "Max Decimals"
        ),
        "extraDesc" => array(
            "The data channel to use for the input",
            "The value that the input takes at an arbitrary point A",
            "The value that the input takes at an arbitrary point B",
            "The value that the output should be at the same arbitrary point A",
            "The value that the output should be at the same arbitrary point B",
            "Bounded means that the two points given, A & B are the boundries of the
             line.  Anything beyond these will be considered invalid.  Unbounded
             will go to infinity in either direction.",
            "The units that the output will be in",
            "The type that the units are in.  Valid values include Pressure,
             Temperature, Relative Humidity, Impedance, Power, Voltage, Current
             and others.",
            "The data type that the output will be in",
            "The maximum number of decimals that are valid for the output",
        ),
        "extraNames" => array(
            "datachan"    => 0,
            "origpta"     => 1,
            "origptb"     => 2,
            "newpta"      => 3,
            "newptb"      => 4,
            "type"        => 5,
            "storageunit" => 6,
            "unittype"    => 7,
            "datatype"    => 8,
            "maxdecimals" => 9,
        ),
        "extraDefault" => array(
            "", 0, 0, 0, 0, 'unbounded', "unknown", "Generic",
            \HUGnet\devices\datachan\Driver::TYPE_RAW, 4
        ),
        "storageType" => "getExtra8",
        "storageUnit" => "getExtra6",
        "maxDecimals" => "getExtra9",

        "virtual" => true,              // This says if we are a virtual sensor
        "dataTypes" => array(
            \HUGnet\devices\datachan\Driver::TYPE_IGNORE
                => \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
            \HUGnet\devices\datachan\Driver::TYPE_RAW
                => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        ),
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
        bcscale(10);
        $index = ((int)$this->getExtra(0));
        $In   = $data[$index]["value"];
        $Imin = $this->getExtra(1);
        $Imax = $this->getExtra(2);
        $Omin = $this->getExtra(3);
        $Omax = $this->getExtra(4);

        if ($this->getExtra(5) == "bounded") {
            $O = $this->linearBounded($In, $Imin, $Imax, $Omin, $Omax);
        } else {
            $O = $this->linearUnbounded($In, $Imin, $Imax, $Omin, $Omax);
        }
        if (is_null($O)) {
            return null;
        }
        return round((float)$O, $this->get("maxDecimals"));
    }

}


?>
