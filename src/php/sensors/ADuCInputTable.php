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
namespace HUGnet\sensors;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCInputTable
{
    /**
    * This is where we store our sensor object
    */
    private $_sensor;
    /**
    * This is where we store our sensor object
    */
    private $_registers = array(
        "ADC0CON" => array(
            "ADC0EN"    => array(
                'value' => 1,
                'bit'   => 15,
                'mask'  => 0x1,
                'bits'  => 1,
            ),
            "ADC0DIAG"  => array(
                'value' => 0x0,
                'bit'   => 13,
                'mask'  => 0x3,
                'bits'  => 2,
            ),
            "HIGHEXTREF0" => array(
                'value' => 0,
                'bit'   => 12,
                'mask'  => 0x1,
                'bits'  => 1,
            ),
            "AMP_CM"    => array(
                'value' => 0,
                'bit'   => 11,
                'mask'  => 0x1,
                'bits'  => 1,
            ),
            "ADC0CODE"  => array(
                'value' => 0,
                'bit'   => 10,
                'mask'  => 0x1,
                'bits'  => 1,
            ),
            "ADC0CH"    => array(
                'value' => 0x3,
                'bit'   => 6,
                'mask'  => 0xF,
                'bits'  => 4,
            ),
            "ADC0REF"   => array(
                'value' => 0x0,
                'bit'   => 4,
                'mask'  => 0x3,
                'bits'  => 2,
            ),
            "ADC0PGA"   => array(
                'value' => 0x0,
                'bit'   => 0,
                'mask'  => 0xF,
                'bits'  => 4,
            ),
        ),
        "ADC1CON" => array(
            "ADC1EN"      => array(
                'value' => 1,
                'bit'   => 15,
                'mask'  => 0x1,
                'bits'  => 1,
            ),
            "ADC1DIAG"    => array(
                'value' => 0x0,
                'bit'   => 13,
                'mask'  => 0x2,
                'bits'  => 2,
            ),
            "HIGHEXTREF1" => array(
                'value' => 0,
                'bit'   => 12,
                'mask'  => 0x1,
                'bits'  => 1,
            ),
            "ADC1CODE"    => array(
                'value' => 0,
                'bit'   => 11,
                'mask'  => 0x1,
                'bits'  => 1,
            ),
            "ADC1CH"      => array(
                'value' => 0xC,
                'bit'   => 7,
                'mask'  => 0xF,
                'bits'  => 4,
            ),
            "ADC1REF"     => array(
                'value' => 0x0,
                'bit'   => 4,
                'mask'  => 0x7,
                'bits'  => 3,
            ),
            "BUF_BYPASS"  => array(
                'value' => 0x0,
                'bit'   => 2,
                'mask'  => 0x3,
                'bits'  => 2,
            ),
            "ADC1PGA"     => array(
                'value' => 0x0,
                'bit'   => 0,
                'mask'  => 0x3,
                'bits'  => 2,
            ),
        ),
        "ADCFLT" => array(
            "CHOPEN" => array(
                'value' => 1,
                'bit'   => 15,
                'mask'  => 0x1,
                'bits'  => 1,
            ),
            "RAVG2"  => array(
                'value' => 0,
                'bit'   => 14,
                'mask'  => 0x1,
                'bits'  => 1,
            ),
            "AF"     => array(
                'value' => 0x0,
                'bit'   => 8,
                'mask'  => 0x3F,
                'bits'  => 6,
            ),
            "NOTCH2" => array(
                'value' => 0,
                'bit'   => 7,
                'mask'  => 0x1,
                'bits'  => 1,
            ),
            "SF"     => array(
                'value' => 0x9,
                'bit'   => 0,
                'mask'  => 0x7F,
                'bits'  => 7,
            ),

        ),
    );
    /**
    * This is where we setup the sensor object
    */
    private $_params = array(
        "driver0"  => array(
            "value" => 0xFF,
        ),
        "driver1"  => array(
            "value" => 0xFF,
        ),
        "priority" => array(
            "value" => 0xFF,
        ),
        "process"  => array(
            "value" => 0,
        ),
    );
    /**
    * This is the constructor
    *
    * @param object $sensor The sensor object we are working with
    * @param mixed  $config This could be a string or array or null
    */
    private function __construct($sensor, $config = null)
    {
        $this->_sensor = &$sensor;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_sensor);
    }
    /**
    * This is the constructor
    *
    * @param object $sensor The sensor object we are working with
    * @param mixed  $config This could be a string or array or null
    *
    * @return object The new object
    */
    public static function &factory($sensor, $config = null)
    {
        $object = new ADuCInputTable($sensor, $config);
        return $object;
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param string $register The register to get
    * @param string $set      The values to set the register to
    *
    * @return 16 bit integer that is the FLT setup
    */
    public function register($register, $set = null)
    {
        $reg = &$this->_registers[$register];
        if (is_string($set) || is_int($set)) {
            if (is_string($set)) {
                $set = hexdec($set);
            }
            foreach ($reg as $field => $vals) {
                $mask = $vals["mask"] << $vals["bit"];
                $val = ($set & $mask) >> $vals["bit"];
                $reg[$field]["value"] = $val;
            }
        }
        $ret  = 0;
        $bits = 0;
        foreach ($reg as $field => $vals) {
            $val = $vals["value"] & $vals["mask"];
            $val <<= $vals["bit"];
            $ret |= $val;
            $bits += $vals["bits"];
        }
        return sprintf("%0".round($bits / 4)."X", $ret);
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param string $set The values to set the register to
    *
    * @return 16 bit integer that is the FLT setup
    */
    public function driver0($set = null)
    {
        return $this->_params("driver0", $set);
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param string $set The values to set the register to
    *
    * @return 16 bit integer that is the FLT setup
    */
    public function driver1($set = null)
    {
        return $this->_params("driver1", $set);
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param string $set The values to set the register to
    *
    * @return 16 bit integer that is the FLT setup
    */
    public function priority($set = null)
    {
        return $this->_params("priority", $set);
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param string $set The values to set the register to
    *
    * @return 16 bit integer that is the FLT setup
    */
    public function immediateProcessRoutine($set = null)
    {
        return $this->_params("process", $set);
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param string $param The parameter to set
    * @param string $set   The values to set the register to
    *
    * @return 16 bit integer in a hex string
    */
    private function _params($param, $set = null)
    {
        if (isset($this->_params[$param])) {
            if (is_string($set)) {
                $set = hexdec($set);
            }
            if (is_int($set)) {
                $this->_params[$param]["value"] = $set;
            }
            return sprintf("%02X", $this->_params[$param]["value"]);
        }
        return "";
    }
    /**
    * This takes the class and makes it into a setup string
    *
    * @return string The encoded string
    */
    public function freq()
    {
        $ret = 0;
        $sinc = $this->_registers["ADCFLT"]["SF"]["value"];
        $Filt = $this->_registers["ADCFLT"]["AF"]["Value"];
        if ($this->_registers["ADCFLT"]["CHOPEN"]) {
            $ret = 512000 / ((($sinc +1) * 64 * (3 + $flt)) + 3);
        } else if ($flt > 0) {
            $ret = 512000 / (($sinc +1) * 64 * (3 + $flt));
        } else if ($flt === 0) {
            $ret = 512000 / (($sinc + 1) * 64);
        }
        return round($ret, 4);
    }
    /**
    * This takes the class and makes it into a setup string
    *
    * @return string The encoded string
    */
    public function encode()
    {
        return "";
    }
    /**
    * This builds the class from a setup string
    *
    * @param string $string The setup string to decode
    *
    * @return bool True on success, false on failure
    */
    public function decode($string)
    {
        return false;
    }

}


?>
