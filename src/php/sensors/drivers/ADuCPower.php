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
require_once dirname(__FILE__)."/../DriverADuC.php";


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
class ADuCPower extends \HUGnet\sensors\DriverADuC
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
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
    public function getVoltage(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        if (is_null($A)) {
            return null;
        }
        $Am   = pow(2, 23);
        $A = $this->getTwosCompliment($A, 32);
        $Vref  = $this->getExtra(0);
        $Rin   = $this->getExtra(2);
        $Rbias = $this->getExtra(3);
        $A = $this->inputBiasCompensation($A, $Rin, $Rbias);
        $Va = ($A / $Am) * $Vref;
        return round($Va, $this->get("maxDecimals"));
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
    public function getCurrent(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        if (is_null($A)) {
            return null;
        }
        $Am   = pow(2, 23);
        $A = $this->getTwosCompliment($A, 32);
        $Vref  = $this->getExtra(0);
        $R     = $this->getExtra(1);
        $Rin   = $this->getExtra(4);
        $Rbias = $this->getExtra(5);
        if ($R == 0) {
            return null;
        }
        $A = $this->inputBiasCompensation($A, $Rin, $Rbias);
        $Va = ($A / $Am) * $Vref;
        $I = $Va / $R;
        return round($I, $this->get("maxDecimals"));
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getPower(
        $deltaT = 0, &$data = array(), $prev = null
    ) {
        $I = $data[0]["value"];
        $V = $data[1]["value"];
        if (is_null($I) || is_null($V)) {
            return null;
        }
        $P = $I * $V;
        return round($P, $this->get("maxDecimals"));
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getImpedance(
        $deltaT = 0, &$data = array(), $prev = null
    ) {
        /* Impedance */
        $I = $data[0]["value"];
        $V = $data[1]["value"];
        if (($I == 0) || is_null($V)) {
            return null;
        }
        $R = $V / $I;
        return round($R, $this->get("maxDecimals"));
    }
    /**
    * Returns all of the parameters and defaults in an array
    *
    * @return array of data from the sensor
    */
    public function toArray()
    {
        $sensor = (int)$this->sensor()->id();
        $array = parent::toArray();
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
    * Gets an item
    *
    * @param string $name The name of the property to get
    * @param int    $sid  The sensor ID to use
    *
    * @return null
    */
    public function get($name, $sid = null)
    {
        if (!is_int($sid)) {
            $sid = $this->sensor()->id();
        }
        $sid = (int)$sid;
        $param = parent::get($name);
        if (($name == "unitType") || ($name == "storageUnit")) {
            $param = $param[$sid];
        } else if (($sid != 1) && ($sid != 3)) {
            if (($name == "extraDefault")
                || ($name == "extraText")
                || ($name == "extraValues")
            ) {
                $param = array();
            }
        }
        return $param;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param float  $deltaT  The time delta in seconds between this record
    * @param array  &$prev   The previous reading
    * @param array  &$data   The data from the other sensors that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function decodeData(
        &$string, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $sid = (int)$this->sensor()->id();
        if (($sid !== 1) && ($sid !== 3)) {
            return array();
        }
        $ret = $this->channels();
        $A = $this->strToInt($string);
        $ret[0]["value"] = $this->getCurrent(
            $A, $deltaT, $ret, $prev
        );
        $A = $this->strToInt($string);
        $ret[1]["value"] = $this->getVoltage(
            $A, $deltaT, $ret, $prev
        );
        $ret[2]["value"] = $this->getPower(
            $deltaT, $ret, $prev
        );
        $ret[3]["value"] = $this->getImpedance(
            $deltaT, $ret, $prev
        );
        return $ret;
    }
    /**
    * Gets the extra values
    *
    * @param int $index The extra index to use
    * @param int $sid   Alternative sensor ID to use
    *
    * @return The extra value (or default if empty)
    */
    public function getExtra($index, $sid = null)
    {
        if (!is_int($sid)) {
            $sid = (int)$this->sensor()->id();
        }
        $extra = (array)$this->sensor()->get("extra");
        if (!isset($extra[$index])) {
            $extra = $this->get("extraDefault", $sid);
        }
        return $extra[$index];
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channels()
    {
        $ret = array();
        foreach (array(1,2,5,6) as $i) {
            $ret[] = array(
                "decimals" => $this->get("maxDecimals", $i),
                "units" => $this->get("storageUnit", $i),
                "unitType" => $this->get("unitType", $i),
                "dataType" => $this->get("storageType", $i),
            );
        }
        return $ret;
    }

}


?>
