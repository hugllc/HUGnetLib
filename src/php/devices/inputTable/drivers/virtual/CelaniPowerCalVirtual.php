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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.9
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class CelaniPowerCalVirtual extends \HUGnet\devices\inputTable\DriverVirtual
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Celani Power Calibration Virtual Sensor",
        "shortName" => "CelaniPowerCal",
        "unitType" => "Power",
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            5, 20, 20, 20, 20, 20
        ),
        "extraText" => array(
            "Temperature Input",
            "Constant A",
            "Constant B",
            "Constant C",
            "Constant D",
            "Constant E"
        ),
        "extraDefault" => array(
            0,
            -3.3215281155898113E-02,
            2.0603322646346728E-01,
            8.4476368057181570E-04,
            -1.2619599884486933E+01,
            4.2109260827983724E+02
        ),
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "storageUnit" => "W",
        "maxDecimals" => 6,

        "virtual" => true,              // This says if we are a virtual sensor
        "dataTypes" => array(
            \HUGnet\devices\datachan\Driver::TYPE_IGNORE
                => \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
            \HUGnet\devices\datachan\Driver::TYPE_RAW
                => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        ),
    );
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
    public function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        bcscale(16);
        $temp = $this->getExtra(0);
        $a    = $this->getExtra(1);
        $b    = $this->getExtra(2);
        $c    = $this->getExtra(3);
        $d    = $this->getExtra(4);
        $e    = $this->getExtra(5);

        $x_in = $data[$temp]["value"];

        if (is_null($x_in)) {
            return null;
        }

        $Bint = bcmul((string)$b, (string)$x_in);
        $Cint = bcmul((string)$c, bcpow((string)$x_in, 2));
        $Dint = bcmul((string)$d, bcpow((string)$x_in, 2));
        $Eint = bcsub(bcpow((string)$x_in, 2), bcpow((string)$e, 2));
        if ($Eint == 0) {
            return null;
        }
        $Dint2 = bcdiv($Dint, $Eint);

        $P = bcadd(bcadd((string)$a, $Bint), bcadd($Cint, $Dint2));
        return round($P, $this->get("maxDecimals"));

    }

}


?>
