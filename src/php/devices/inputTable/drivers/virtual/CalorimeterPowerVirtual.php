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
class CalorimeterPowerVirtual extends \HUGnet\devices\inputTable\DriverVirtual
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Calorimeter Power Virtual Sensor",
        "shortName" => "CalorimeterPow",
        "unitType" => "Power",
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            array(), array(), 20, 20, 20
        ),
        "extraText" => array(
            "Inner Tube Voltage",
            "Outer Tube Voltage",
            "X^2 Coefficient",
            "X^1 Coefficient",
            "Constant",
        ),
        "extraDefault" => array(
            0,
            1,
            12450,
            -1657.7,
            3.3529,
        ),
        "extraDesc" => array(
            "The data channel where the inner tube voltage is found",
            "The data channel where the outer tube voltage is found",
            "X^2 Coefficient of the curve fit polynomial to use",
            "X^1 Coefficient of the curve fit polynomial to use",
            "Constant of the curve fit polynomial to use",
        ),
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "storageUnit" => "W",
        "maxDecimals" => 8,

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
            $ret[1] = $ret[0];
        }
        return $ret;
    }
    /**
    * Changes a raw reading into a output value
    *
    * This is implementing the formula:
    * P = A + B*x_in + C*x_in*x_in + (D*x_in*x_in/((x_in*x_in)-(E*E)))
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
        $inner = $this->getExtra(0);
        $outer = $this->getExtra(1);
        $X2    = $this->getExtra(2);
        $X1    = $this->getExtra(3);
        $X0    = $this->getExtra(4);

        $x_in  = $data[$inner]["value"];
        $x_out = $data[$outer]["value"];

        if (is_null($x_in) || is_null($x_out)) {
            return null;
        }
        $x  = bcsub($x_in, $x_out);
        $y  = bcmul(bcmul($x, $x), $X2);
        $y += bcmul($x, $X1);
        $y += $X0;
        return round($y, $this->get("maxDecimals"));

    }

}


?>
