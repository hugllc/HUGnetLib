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
namespace HUGnet\devices\inputTable;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../../base/LoadableDriver.php";
/** This is our interface */
require_once dirname(__FILE__)."/DriverInterface.php";
/** This is our units class */
require_once dirname(__FILE__)."/../datachan/Driver.php";

/**
 * Base driver class for devices.
 *
 * This class deals with loading the drivers and figuring out what driver needs
 * to be loaded.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class Driver extends \HUGnet\base\LoadableDriver
{
    /**
    * The location of our tables.
    */
    protected $tableLoc = "inputTable";
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
    );
    /**
    * This is where all of the defaults are stored.
    */
    protected $default = array(
        "longName" => "",
        "shortName" => "",
        "unitType" => "",
        "bound" => false,                // This says if this sensor is changeable
        "virtual" => false,              // This says if we are a virtual sensor
        "total"   => false,              // Whether to total instead of average
        "extraText" => array(),
        "extraDesc" => array(),
        "extraDefault" => array(),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(),
        "extraNames" => array(),
        "storageUnit" => "unknown",
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "maxDecimals" => 2,
        "dataTypes" => array(
            \HUGnet\devices\datachan\Driver::TYPE_RAW
                => \HUGnet\devices\datachan\Driver::TYPE_RAW,
            \HUGnet\devices\datachan\Driver::TYPE_DIFF
                => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
            \HUGnet\devices\datachan\Driver::TYPE_IGNORE
                => \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
        ),
        "inputSize" => 3,
        "requires" => array(),
        "provides" => array(),
    );
    /**
    * This is where all of the driver information is stored.
    *
    * Drivers must be registered here, otherwise they will never get loaded.  The
    * index in the root array is the driver name.  It should be exactly the same
    * as the driver class name.
    */
    private static $_drivers = array(
        "60:DEFAULT"                 => "ControlInput",
        "62:DEFAULT"                 => "DifferenceInput",
        "64:DEFAULT"                 => "NullInput",
        "6F:DEFAULT"                 => "MaximumWindDirection",
        "70:DEFAULT"                 => "GenericPulse",
        "70:Bravo3Motion"            => "Bravo3Motion",
        "70:GenericRevolving"        => "GenericRevolving",
        "70:LiquidFlow"              => "LiquidFlow",
        "70:LiquidVolume"            => "LiquidVolume",
        "70:MaximumAnemometer"       => "MaximumAnemometer",
        "70:MaximumRain"             => "MaximumRain",
        "70:WattNode"                => "WattNode",
        "7F:DEFAULT"                 => "GenericPulse",
        "7F:GenericRevolving"        => "GenericRevolving",
        "7F:LiquidFlow"              => "LiquidFlow",
        "FA:DEFAULT"                 => "SDEFAULT",
        "FF:DEFAULT"                 => "EmptySensor",
        // ADuC
        "04:DEFAULT"                 => "ADuCVishayRTD",
        "11:DEFAULT"                 => "ADuCPower",
        "41:DEFAULT"                 => "ADuCVoltage",
        "41:ADuCPressure"            => "ADuCPressure",
        "42:DEFAULT"                 => "ADuCThermocouple",
        "43:DEFAULT"                 => "ADuCScaledTemp",
        "44:DEFAULT"                 => "ADuCPressure",
        "45:DEFAULT"                 => "ADuCGenericLinear",
        "46:DEFAULT"                 => "MKS901PPressure",
        "47:DEFAULT"                 => "ADuCMF51E",
        "48:DEFAULT"                 => "ADuCResistance",
        "49:DEFAULT"                 => "OmegaAlphaPH",
        "4A:DEFAULT"                 => "ADuCACResistance",
        "4B:DEFAULT"                 => "ADuCUSSensorRTD",
        "F9:DEFAULT"                 => "ADuCInputTable",
        // AVR
        "00:DEFAULT"                 => "AVRBC2322640_0",
        "02:DEFAULT"                 => "AVRBC2322640",
        "02:AVRBC2322640"            => "AVRBC2322640",
        "02:AVRB57560G0103F000"      => "AVRB57560G0103F000",
        "02:ControllerTemp"          => "ControllerTemp",
        "02:imcSolar"                => "AVRIMCSolar",
        "02:XMegaTemp"               => "XMegaTemp",
        "02:potDirection"            => "AVRPotDirection",
        "10:DEFAULT"                 => "AVRChsMss",
        "10:chsMss"                  => "AVRChsMss",
        "30:DEFAULT"                 => "AVROSRAMLight",
        "30:OSRAM BPW-34"            => "AVROSRAMLight",
        "40:DEFAULT"                 => "AVRVoltage",
        "40:XMegaVCC"                => "XMegaVCC",
        "40:XMegaVoltage"            => "XMegaVoltage",
        "40:ControllerVoltage"       => "ControllerVoltage",
        "40:BARO4"                   => "AVRBAROA4V",
        "40:fetBoard"                => "FETBoardVoltage",
        "40:GA100"                   => "AVRGA100",
        "40:HitachiVFDFan"           => "AVRHitachiVFDFan",
        "50:DEFAULT"                 => "AVRCurrent",
        "50:XMegaCurrent"            => "XMegaCurrent",
        "50:ControllerCurrent"       => "ControllerCurrent",
        "50:dwyer616"                => "AVRDwyer616",
        "50:DwyerAirVel"             => "AVRDwyerAirVel",
        "50:fetBoard"                => "FETBoardCurrent",
        "7E:DEFAULT"                 => "AVROnTimePulse",
        "F8:DEFAULT"                 => "AVRAnalogTable",
        // Linux
        "61:DEFAULT"                 => "ControlSumInput",
        "63:DEFAULT"                 => "NoisyInput",
        "65:DEFAULT"                 => "MemInput",
        "66:DEFAULT"                 => "FileInput",
        // Virtual
        "FE:DEFAULT"                 => "EmptyVirtual",
        "FE:AlarmVirtual"            => "AlarmVirtual",
        "FE:BinaryVirtual"           => "BinaryVirtual",
        "FE:CalorimeterPowerVirtual" => "CalorimeterPowerVirtual",
        "FE:CelaniPowerCalVirtual"   => "CelaniPowerCalVirtual",
        "FE:CloneVirtual"            => "CloneVirtual",
        "FE:ComputationVirtual"      => "ComputationVirtual",
        "FE:DewPointVirtual"         => "DewPointVirtual",
        "FE:LinearTransformVirtual"  => "LinearTransformVirtual",
        "FE:WindChillVirtual"        => "WindChillVirtual",

    );
    /**
    * This is where the correlation between the drivers and the arch is stored.
    *
    * If a driver is not registered here, it will not be in the list of drivers
    * that can be chosen.
    *
    */
    protected $arch = array(
        "0039-12" => array(
            0x70 => "Pulse Counter",
            0xF8 => "Analog Input Table",
        ),
        "0039-21-01" => array(
            0xF8 => "Analog Input Table",
        ),
        "0039-21-02" => array(
            0xF8 => "Analog Input Table",
        ),
        "0039-28" => array(
            0x6F => "Maximum Inc. Wind Direction Sensor",
            0x70 => "Pulse Counter",
            0xF8 => "Analog Input Table",
        ),
        "0039-37" => array(
            0x60 => "Control Value Input",
            0x70 => "Pulse Counter",
            0xF9 => "Input Table Entry",
        ),
        "Linux" => array(
            0x60 => "Control Value Input",
            0x61 => "Control Sum Input",
            0x62 => "Difference Input",
            0x63 => "Noisy Input",
            0x64 => "Null Input",
            0x65 => "Memory Input",
            0x66 => "File Input",
        ),
        "all" => array(
            0xFE => "Virtual",
            0xFF => "Empty Slot",
        ),
    );
    /**
    * This is the destructor
    *
    * @return object
    */
    public function input()
    {
        return parent::iopobject();
    }
    /**
    * This function creates the system.
    *
    * @param string $driver  The driver to load
    * @param object &$sensor The sensor object
    * @param int    $offset  The extra offset to use
    *
    * @return null
    */
    public static function &factory($driver, &$sensor, $offset = 0)
    {
        $filebase = dirname(__FILE__)."/drivers/";
        if (file_exists($filebase."aduc/".$driver.".php")) {
            $class = '\\HUGnet\\devices\\inputTable\\drivers\\aduc\\'.$driver;
            include_once $filebase."aduc/".$driver.".php";
        } else if (file_exists($filebase."avr/".$driver.".php")) {
            $class = '\\HUGnet\\devices\\inputTable\\drivers\\avr\\'.$driver;
            include_once $filebase."avr/".$driver.".php";
        } else if (file_exists($filebase."linux/".$driver.".php")) {
            $class = '\\HUGnet\\devices\\inputTable\\drivers\\linux\\'.$driver;
            include_once $filebase."linux/".$driver.".php";
        } else if (file_exists($filebase."virtual/".$driver.".php")) {
            $class = '\\HUGnet\\devices\\inputTable\\drivers\\virtual\\'.$driver;
            include_once $filebase."virtual/".$driver.".php";
        } else {
            if (file_exists($filebase.$driver.".php")) {
                include_once $filebase.$driver.".php";
            }
            $class = '\\HUGnet\\devices\\inputTable\\drivers\\'.$driver;
        }
        $interface = "\\HUGnet\\devices\\inputTable\\DriverInterface";
        if (is_subclass_of($class, $interface)) {
            return new $class($sensor, $offset);
        }
        include_once dirname(__FILE__)."/drivers/SDEFAULT.php";
        return new \HUGnet\devices\inputTable\drivers\SDEFAULT($sensor);
    }
    /**
    * Returns the driver that should be used for a particular device
    *
    * @param mixed  $sid  The ID of the sensor
    * @param string $type The type of the sensor
    *
    * @return string The driver to use
    */
    public static function getDriver($sid, $type = "DEFAULT")
    {
        $try = array(
            sprintf("%02X", (int)$sid).":".$type,
            sprintf("%02X", (int)$sid),
            sprintf("%02X", (int)$sid).":DEFAULT",
        );
        foreach ($try as $mask) {
            if (isset(self::$_drivers[$mask])) {
                $driver = self::$_drivers[$mask];
                break;
            }
        }
        if (is_null($driver)) {
            $driver = "SDEFAULT";
        }
        return $driver;
    }
    /**
    * Returns id:type for the class given.  It will only return the first one it
    * encounters
    *
    * @param string $class The class the driver belongs to
    *
    * @return string The driver to use
    */
    public static function getDriverID($class)
    {
        return array_search($class, self::$_drivers);
    }
    /**
    * Returns the driver that should be used for a particular device
    *
    * @param bool $all Wether to return all of the driver, or only mine.
    * 
    * @return array The array of drivers that will work
    */
    public function getDrivers($all = false)
    {
        if (!$all) {
            $ret = parent::getDrivers();
        } else {
            $ret = self::$_drivers;
            ksort($ret);
        }
        return $ret;
    }
    /**
    * Returns an array of types that this sensor could be
    *
    * @param int $sid The ID to check
    *
    * @return The extra value (or default if empty)
    */
    public static function getTypes($sid)
    {
        $array = array();
        $sensor = sprintf("%02X", (int)$sid);
        foreach ((array)self::$_drivers as $key => $driver) {
            $k = explode(":", $key);
            if (trim(strtoupper($k[0])) == $sensor) {
                $array[$k[1]] = $driver;
            }
        }
        return (array)$array;
    }
    /**
    * Registers an extra driver to be used by the class
    *
    * The new class will only be registered if it doesn't already exist
    *
    * @param string $key   The key to use for the class
    * @param string $class The class to use for the key
    *
    * @return null
    */
    public static function register($key, $class)
    {
        $driver = '\\HUGnet\\devices\\inputTable\\drivers\\'.$class;
        if (class_exists($driver) && !isset(self::$_drivers[$key])) {
            self::$_drivers[$key] = $class;
        }
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
    protected function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        return $A;
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
    protected function getRaw(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        return $value;
    }
    /**
    * Takes in a raw string from a sensor and makes an int out it
    *
    * The sensor data is stored little-endian, so it just takes that and adds
    * the bytes together.
    *
    * @param string &$string The string to convert
    *
    * @return int
    */
    protected function strToInt(&$string)
    {
        $size = $this->get("inputSize");
        if ($size > strlen($string)) {
            return null;
        }
        $work = substr($string, 0, ($size * 2));
        $string = (string)substr($string, ($size * 2));
        return $this->decodeInt($work, $size);
    }
    /**
    * Takes in a raw string from a sensor and makes an int out it
    *
    * The sensor data is stored little-endian, so it just takes that and adds
    * the bytes together.
    *
    * @param int $value the value to convert to a string
    *
    * @return int
    */
    protected function intToStr($value)
    {
        if (is_null($value)) {
            return "";
        }
        $size = $this->get("inputSize");

        return $this->encodeInt($value, $size);
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
        &$string, $deltaT = 0, &$prev = array(), &$data = array()
    ) {
        $A    = $this->getRawData($string);
        $ret  = $this->channels();
        $type = $this->get("storageType");
        $chan = (int)$this->input()->channelStart();
        $prev = is_array($prev) ? $prev : array();
        $ret[0]["value"] = $this->decodeDataPoint(
            $A, 0, $deltaT, $prev[$chan], $data
        );
        $ret[0]["raw"] = $A;
        return $ret;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param int    $channel The channel to decode
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
        $A = null;
        if (!is_null($string)) {
            $A = $this->getRawData($string, $channel);
        }
        $type = $this->get("storageType");
        if ($type == \HUGnet\devices\datachan\Driver::TYPE_DIFF) {
            $val = $this->calcDiff($A, $prev);
            $ret = $this->getReading(
                $val, $deltaT, $data, $prev
            );
        } else {
            $ret = $this->getReading(
                $A, $deltaT, $data, $prev
            );
        }
        return $ret;
    }
    /**
    * Calculates the difference between this value and the previous one.
    *
    * @param int   &$A    The data value given
    * @param array &$prev The previous reading
    *
    * @return int The difference value
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    protected function calcDiff(&$A, &$prev = null) 
    {
        if (is_null($prev["raw"])) {
            $val = null;
        } else {
            $val = $A - $prev["raw"];
        }
        return $val;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param int    $channel The channel to decode
    *
    * @return float The raw value
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getRawData(&$string, $channel = 0)
    {
        return (is_string($string)) ? $this->strToInt($string) : (int)$string;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
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
    public function encodeDataPoint(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $val = $this->getRaw(
            $value, $channel, $deltaT, $prev, $data
        );
        if (!is_null($val)) {
            return $this->intToStr((int)$val);
        }
        return "";
    }
    /**
    * This makes a line of two ordered pairs, then puts $A on that line
    *
    * @param float $value The incoming value
    * @param float $Imin  The input minimum
    * @param float $Imax  The input maximum
    * @param float $Omin  The output minimum
    * @param float $Omax  The output maximum
    *
    * @return output rounded to 4 places
    */
    protected function linearBounded($value, $Imin, $Imax, $Omin, $Omax)
    {
        if ($value > $Imax) {
            return null;
        }
        if ($value < $Imin) {
            return null;
        }
        return $this->linearUnbounded($value, $Imin, $Imax, $Omin, $Omax);
    }
    /**
    * This makes a line of two ordered pairs, then puts $A on that line
    *
    * @param float $value The incoming value
    * @param float $Imin  The input minimum
    * @param float $Imax  The input maximum
    * @param float $Omin  The output minimum
    * @param float $Omax  The output maximum
    *
    * @return output rounded to 4 places
    */
    protected function linearUnbounded($value, $Imin, $Imax, $Omin, $Omax)
    {
        if (is_null($value)) {
            return null;
        }
        if (($Imax == $Imin) || ($Omax == $Omin)) {
            return null;
        }
        $mult = bcdiv(bcsub($Omax, $Omin), bcsub($Imax, $Imin));
        $Yint = bcsub($Omax, bcmul($mult, $Imax));
        $Out = bcadd(bcmul($mult, $value), $Yint);
        return $Out;
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
                "decimals" => $this->get("maxDecimals"),
                "units" => $this->get("storageUnit"),
                "maxDecimals" => $this->get("maxDecimals"),
                "storageUnit" => $this->get("storageUnit"),
                "unitType" => $this->get("unitType"),
                "dataType" => $this->get("storageType"),
                "label" => (string)$this->input()->get("location"),
                "index" => 0,
                "epChannel" => true,
                "port" => $this->port(),
            ),
        );
    }
    /**
    * Returns the port this data channel is attached to
    *
    * @return array
    */
    protected function port()
    {
        return $this->get("port");
    }
    /**
    * This is for a generic pulse counter
    *
    * @param int   $val    Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    *                      and the last one
    *
    * @return float
    */
    protected function getPPM($val, $deltaT)
    {
        if ($deltaT <= 0) {
            return null;
        }
        $ppm = ($val / $deltaT) * 60;
        if ($ppm < 0) {
            return null;
        }
        return round($ppm, 4);
    }
    /**
    * This is for a generic pulse counter
    *
    * @param int   $ppm    Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    *                      and the last one
    *
    * @return float
    */
    protected function revPPM($ppm, $deltaT)
    {
        if (($deltaT <= 0) || ($ppm < 0)) {
            return null;
        }
        $val = ($ppm / 60) * $deltaT;
        return (int)$val;
    }
    /**
    * This function should be called with the values set for the specific
    * thermistor that is used.
    *
    * @param float $R      The current resistance of the thermistor in ohms
    * @param array &$table The table to use
    *
    * @return float The Temperature in degrees C
    */
    protected function tableInterpolate($R, &$table)
    {
        $max = max(array_keys($table));
        $min = min(array_keys($table));

        if (($R < $min) || ($R > $max)) {
            return null;
        }
        foreach (array_keys($table) as $ohm) {
            $last = $ohm;

            if ((float)$ohm <= (float)$R) {
                break;
            }
            $next = $ohm;
        }
        $T = $table[$last];
        if ((($last - $next) == 0) || ((float)$ohm == (float)$R)) {
            return $T;
        }
        $fract = ($R - $last) / ($last - $next);
        $diff = $fract * ($table[$last] - $table[$next]);
        return (float)($T + $diff);
    }
}


?>
