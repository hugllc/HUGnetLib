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
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCPower extends \HUGnet\sensors\Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected static $params = array(
        "longName" => "ADuC Power Meter",
        "shortName" => "ADuCPower",
        "unitType" => array(
            0 => "Unknown",
            1 => "Current",
            2 => "Voltage",
            3 => "Current",
            4 => "Voltage",
            5 => "Power",
            6 => "Impedance",
            7 => "Power",
            8 => "Impedance",
        ),
        "storageUnit" => array(
            0 => 'Unknown',
            1 => 'A',
            2 => 'V',
            3 => 'A',
            4 => 'V',
            5 => 'W',
            6 => 'Ohms',
            7 => 'W',
            8 => 'Ohms',
        ),
        "storageType" => \HUGnet\units\Driver::TYPE_RAW, // Storage dataType
        "extraText" => array(
            "Voltage Ref (V)",
            "Shunt Resistor (Ohms)",
            "Voltage Input R (kOhms)",
            "Voltage Bias R (kOhms)",
            "Current Input R (kOhms)",
            "Current Bias R (kOhms)",
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(10, 10, 10, 10, 10, 10),
        "extraDefault" => array(1.2, 0.5, 100, 1, 1, 10),
        "maxDecimals" => 6,
        "inputSize" => 4,
    );
    /**
    * This function creates the system.
    *
    * @return null
    */
    public static function &factory()
    {
        return parent::intFactory();
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A       Output of the A to D converter
    * @param array &$sensor The sensor information
    * @param float $deltaT  The time delta in seconds between this record
    * @param array &$data   The data from the other sensors that were crunched
    * @param mixed $prev    The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getReading(
        $A, &$sensor, $deltaT = 0, &$data = array(), $prev = null
    ) {
        bcscale(10);
        $Am   = pow(2, 23);
        $A = \HUGnet\Util::getTwosCompliment($A, 32);
        $sid = $sensor->get("sensor");
        $maxDecimals = $this->get("maxDecimals", $sid);
        if (($sid == 2) || ($sid == 4)) {
            /* Voltage */
            $Vref  = $this->getExtra(0, $sensor, $sid - 1);
            $Rin   = $this->getExtra(2, $sensor, $sid - 1);
            $Rbias = $this->getExtra(3, $sensor, $sid - 1);
            $A = \HUGnet\Util::inputBiasCompensation($A, $Rin, $Rbias);
            $Va = ($A / $Am) * $Vref;
            return round($Va, $maxDecimals);
        } else if (($sid == 1) || ($sid == 3)) {
            /* Current */
            $Vref  = $this->getExtra(0, $sensor, $sid);
            $R     = $this->getExtra(1, $sensor, $sid);
            $Rin   = $this->getExtra(4, $sensor, $sid);
            $Rbias = $this->getExtra(5, $sensor, $sid);
            if ($R == 0) {
                return null;
            }
            $A = \HUGnet\Util::inputBiasCompensation($A, $Rin, $Rbias);
            $Va = ($A / $Am) * $Vref;
            $I = $Va / $R;
            return round($I, $maxDecimals);
        } else if (($sid == 5) || ($sid == 7)) {
            /* Power */
            $I = $data[$sid - 4]["value"];
            $V = $data[$sid - 3]["value"];
            $P = $I * $V;
            return round($P, $maxDecimals);
        } else if (($sid == 6) || ($sid == 8)) {
            /* Impedance */
            $I = $data[$sid - 5]["value"];
            $V = $data[$sid - 4]["value"];
            if ($I == 0) {
                return null;
            }
            $R = $V / $I;
            return round($R, $maxDecimals);
        }
        return null;
    }
    /**
    * Returns all of the parameters and defaults in an array
    *
    * @param int $sensor The sensor number
    *
    * @return array of data from the sensor
    */
    public function toArray($sensor)
    {
        $sensor = (int)$sensor;
        $array = parent::toArray($sensor);
        $array["unitType"] = $array["unitType"][$sensor];
        $array["storageUnit"] = $array["storageUnit"][$sensor];
        if (($sensor != 1) && ($sensor != 3)) {
            $array["extraDefault"] = array();
            $array["extraValues"] = array();
            $array["extraText"] = array();
        }
        return $array;
    }
    /**
    * Returns the driver that should be used for a particular device
    *
    * @param string $name   The name of the property to check
    * @param int    $sensor The sensor number
    *
    * @return string The driver to use
    */
    public static function getParam($name, $sensor)
    {
        $sensor = (int)$sensor;
        $param = parent::getParam($name, $sensor);
        if (($name == "unitType") || ($name == "storageUnit")) {
            $param = $param[$sensor];
        } else if (($sensor != 1) && ($sensor != 3)) {
            if (($name == "extraDefault")
                || ($name == "extraText")
                || ($name == "extraValues")
            ) {
                $param = array();
            }
        }
        return $param;
    }

}


?>
