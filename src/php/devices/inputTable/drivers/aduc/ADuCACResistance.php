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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.14.0
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCACResistance extends \HUGnet\devices\inputTable\DriverADuC
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "ADuC AC Resistance Sensor",
        "shortName" => "ADuCACRes",
        "unitType" => "varies",
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "R to Source (Ohms)",
            "R to Ground (Ohms)",
            "Fixed Resistance (Ohms)",
            "AtoD Ref Voltage",
            "Digital Control Port"
        ),
        "extraDesc" => array(
            "The input resistance to the AtoD",
            "The resistor connecting the AtoD to ground",
            "The fixed resistor in the resistor divider",
            "The voltage used for the AtoD reference.",
            "The port to use for the digital reference.  Only the setting from the
            first AC Resistance input is used.  The rest are ignored." 
        ),
        "extraNames" => array(
            "rsrc"        => 0,
            "rgnd"        => 1,
            "fixedr"      => 2,
            "atodref"     => 3,
            "controlchan" => 4,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(20, 20, 20, 10, array()),
        "extraDefault" => array(0, "Infinite", 1.0, 1.2, 0),
        "maxDecimals" => 8,
        "inputSize" => 4,
        "requires" => array("AI", "DI", "ATODREF"),
        "provides" => array("DC", "DC"),
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
        $param = parent::get($name);
        switch ($name) {
        case "extraValues":
            $param = (array)$param;
            $param[4] = $this->input()->device()->get(
                "DigitalInputs"
            );
        }
        return $param;
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
    protected function getVoltage(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        bcscale(20);
        $Am    = pow(2, 23);
        $Rin   = $this->getExtra(0);
        $Rbias = $this->getExtra(1);
        $Vref  = $this->getExtra(3);
        // The divide by two is because of the nature of this driver
        $A = bcdiv($A, 2);
        $A = $this->inputBiasCompensation($A, $Rin, $Rbias);
        
        $Va = bcmul(bcdiv($A, $Am), $Vref);
        return round($Va, $this->get('maxDecimals'));
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
    protected function getRawVoltage(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        if (is_null($value)) {
            return null;
        }
        bcscale(20);
        $Am   = pow(2, 23);
        $Rin   = $this->getExtra(0);
        $Rbias = $this->getExtra(1);
        $Vref  = $this->getExtra(3);
        $A = ($value / $Vref) * $Am;
        $Amod = $this->inputBiasCompensation($A, $Rin, $Rbias);
        if ($Amod != 0) {
            $A = $A * ($A / $Amod);
        }
        //$A = $A * $this->gain(1);
        return (int)round($A*2);
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
    protected function getResistance(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        bcscale(20);
        $Am    = pow(2, 23);
        $Rbias = $this->getExtra(2);

        $A = abs($A);
        // The divide by two is because of the nature of this sensor
        $A = bcdiv($A, 2);
        if ($A >= $Am) {
            return null;
        }
        $R = (float)(($A * $Rbias) / ($Am - $A));
        return round($R, $this->get('maxDecimals', 1));
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
    protected function getRawResistance(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        if (is_null($value)) {
            return null;
        }
        bcscale(20);
        $Am    = pow(2, 23);
        $Rbias = $this->getExtra(2);

        if (is_null($R)) {
            return null;
        }
        $A = ($R * $Am) / ($Rbias + $R);
        return (int)round(($A * -1 * 2));
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param int    $channel The channel to use
    * @param float  $deltaT  The time delta in seconds between this record
    * @param array  &$prev   The previous reading
    * @param array  &$data   The data from the other sensors that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function decodeDataPoint(
        &$string, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $return = null;
        $A      = null;
        if (!is_null($string)) {
            $A = $this->getRawData($string, $channel);
        }
        if ($channel == 0) {
            $return = $this->getVoltage($A, $deltaT, $data, $prev);
        } else if ($channel == 1) {
            $return = $this->getResistance($A, $deltaT, $data, $prev);
        }
        return $return;
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
    public function getRaw(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $return = 0;
        if ($channel == 0) {
            $return = $this->getRawVoltage($value);
        } else if ($channel == 2) {
            $return = $this->getRawResistance($value);
        }
        return $return;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param int    $chan    The channel this input starts at
    * @param float  $deltaT  The time delta in seconds between this record
    * @param array  &$prev   The previous reading
    * @param array  &$data   The data from the other sensors that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function decodeData(
        &$string, $chan, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $ret = $this->channels();
        foreach (array_keys($ret) as $key) {
            if (!is_null($string) && (strlen($string) > 0)) {
                $A = $this->getRawData($string, $channel);
            } else {
                $A = null;
            }
            $ret[$key]["value"] = $this->decodeDataPoint(
                $A, $key, $deltaT, $prev, $ret
            );
            $ret[$key]["raw"] = $A;
        }
        return $ret;
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channels()
    {
        $decimals = $this->get("maxDecimals");
        return array(
            array(
                "decimals" => $decimals,
                "units" => "V",
                "maxDecimals" => $decimals,
                "storageUnit" => "V",
                "unitType" => "Voltage",
                "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                "label" => $this->input()->get("location")." 0",
                "index" => 0,
                "epChannel" => true,
                "port" => $this->entry()->port(0),
            ),
            array(
                "decimals" => $decimals,
                "units" => "Ohms",
                "maxDecimals" => $decimals,
                "storageUnit" => "Ohms",
                "unitType" => "Impedance",
                "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                "label" => $this->input()->get("location")." 1",
                "index" => 1,
                "epChannel" => true,
                "port" => $this->entry()->port(1),
            ),
        );
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return string
    */
    public function encode()
    {
        $val  = (int)$this->getExtra(4);
        $string .= $this->encodeInt($val, 1);
        return $string;
    }

    
}


?>
