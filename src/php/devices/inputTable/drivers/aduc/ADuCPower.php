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
namespace HUGnet\devices\inputTable\drivers\aduc;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../DriverADuC.php";


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
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCPower extends \HUGnet\devices\inputTable\DriverADuC
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "ADuC Power Meter",
        "shortName" => "ADuCPower",
        "unitType" => "Varies",
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
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
        $A = $A / $this->gain();
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
    * @param float $Va The value to reverse
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getRawVoltage($Va)
    {
        if (is_null($Va)) {
            return null;
        }
        $Am   = pow(2, 23);
        $Vref  = $this->getExtra(0);
        $Rin   = $this->getExtra(2);
        $Rbias = $this->getExtra(3);

        $A = ($Va / $Vref) * $Am;
        $Amod = $this->inputBiasCompensation($A, $Rin, $Rbias);
        if ($Amod != 0) {
            $A = $A * ($A / $Amod);
        }
        $A = $A * $this->gain();
        return round($A);
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
        $A = $A / $this->gain();
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
    * @param float $I The value to reverse
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getRawCurrent($I)
    {
        if (is_null($I)) {
            return null;
        }
        $Am   = pow(2, 23);
        $Vref  = $this->getExtra(0);
        $R     = $this->getExtra(1);
        $Rin   = $this->getExtra(4);
        $Rbias = $this->getExtra(5);
        if ($R == 0) {
            return null;
        }
        $Va = $I * $R;
        $A = ($Va / $Vref) * $Am;
        $Amod = $this->inputBiasCompensation($A, $Rin, $Rbias);
        if ($Amod != 0) {
            $A = $A * ($A / $Amod);
        }
        $A = $A * $this->gain();
        return round($A);
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
    * @param float $P The value to reverse
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getRawPower($P)
    {
        return null;
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
    * Changes a raw reading into a output value
    *
    * @param float $R The value to reverse
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getRawImpedance($R)
    {
        return null;
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
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param array $data    The data to use
    * @param int   $channel The channel to get
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function encodeData($data, $channel = 0)
    {
        $return = 0;
        if ($channel == 0) {
            $return = $this->getRawCurrent($data);
        } else if ($channel == 1) {
            $return = $this->getRawVoltage($data);
        } else if ($channel == 2) {
            $return = $this->getRawPower($data);
        } else if ($channel == 3) {
            $return = $this->getRawImpedance($data);
        }
        return $this->intToStr($return);
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channels()
    {
        return array(
            array(
                "decimals" => 6,
                "units" => "A",
                "maxDecimals" => 6,
                "storageUnit" => "A",
                "unitType" => "Current",
                "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                "index" => 0,
            ),
            array(
                "decimals" => 6,
                "units" => "V",
                "maxDecimals" => 6,
                "storageUnit" => "V",
                "unitType" => "Voltage",
                "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                "index" => 1,
            ),
            array(
                "decimals" => 6,
                "units" => "W",
                "maxDecimals" => 6,
                "storageUnit" => "W",
                "unitType" => "Power",
                "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                "index" => 2,
            ),
            array(
                "decimals" => 6,
                "units" => "Ohms",
                "maxDecimals" => 6,
                "storageUnit" => "Ohms",
                "unitType" => "Impedance",
                "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                "index" => 3,
            )
        );
    }

}


?>