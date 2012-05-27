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
class ADuCInputHelper
{
    /**
    * This is where we setup the sensor object
    */
    private $_pos = array(
        "ADC0CON" => array(
            "ADC0EN"      => 15,
            "ADC0DIAG"    => 13,
            "HIGHEXTREF0" => 12,
            "AMP_CM"      => 11,
            "ADC0CODE"    => 10,
            "ADC0CH"      => 6,
            "ADC0REF"     => 4,
            "ADC0PGA"     => 0,
        ),
        "ADC1CON" => array(
            "ADC1EN"      => 15,
            "ADC1DIAG"    => 13,
            "HIGHEXTREF1" => 12,
            "ADC1CODE"    => 11,
            "ADC1CH"      => 7,
            "ADC1REF"     => 4,
            "BUF_BYPASS"  => 2,
            "ADC1PGA"     => 0,
        ),
        "ADCFLT" => array(
            "CHOPEN" => 15,
            "RAVG2"  => 14,
            "AF"     => 8,
            "NOTCH2" => 7,
            "SF"     => 0,
        ),
    );

    /**
    * This is where we store our sensor object
    */
    private $_sensor;
    /**
    * This is where we store our sensor object
    */
    private $_registers = array(
        "ADC0CON" => array(
            "ADC0EN"      => 1,
            "ADC0DIAG"    => 0x0,
            "HIGHEXTREF0" => 0,
            "AMP_CM"      => 0,
            "ADC0CODE"    => 0,
            "ADC0CH"      => 0x3,
            "ADC0REF"     => 0x0,
            "ADC0PGA"     => 0x0,
        ),
        "ADC1CON" => array(
            "ADC1EN"      => 1,
            "ADC1DIAG"    => 0x0,
            "HIGHEXTREF1" => 0,
            "ADC1CODE"    => 0,
            "ADC1CH"      => 0xC,
            "ADC1REF"     => 0x0,
            "BUF_BYPASS"  => 0x0,
            "ADC1PGA"     => 0x0,
        ),
        "ADCFLT" => array(
            "CHOPEN" => 1,
            "RAVG2"  => 0,
            "SF"     => 0x9,
            "NOTCH2" => 0,
            "AF"     => 0x0,

        ),
    );
    /**
    * This is where we setup the sensor object
    */
    private $_mask = array(
        "ADC0CON" => array(
            "ADC0EN"      => 0x1,
            "ADC0DIAG"    => 0x3,
            "HIGHEXTREF0" => 0x1,
            "AMP_CM"      => 0x1,
            "ADC0CODE"    => 0x1,
            "ADC0CH"      => 0xF,
            "ADC0REF"     => 0x3,
            "ADC0PGA"     => 0xF,
        ),
        "ADC1CON" => array(
            "ADC1EN"      => 0x1,
            "ADC1DIAG"    => 0x3,
            "HIGHEXTREF1" => 0x1,
            "ADC1CODE"    => 0x1,
            "ADC1CH"      => 0xF,
            "ADC1REF"     => 0x7,
            "BUF_BYPASS"  => 0x3,
            "ADC1PGA"     => 0x3,
        ),
        "ADCFLT" => array(
            "CHOPEN" => 0x1,
            "RAVG2"  => 0x1,
            "SF"     => 0x7F,
            "NOTCH2" => 0x1,
            "AF"     => 0x3F,
        ),
    );
    /**
    * This is where we setup the sensor object
    */
    private $_print = array(
        "ADC0CON" => "%04X",
        "ADC1CON" => "%04X",
        "ADCFLT"  => "%04X",
    );
    /**
    * This is the constructor
    *
    * @param object $sensor The sensor object we are working with
    * @param mixed  $config This could be a string or array or null
    *
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
    */
    public static function &factory($sensor, $config = null)
    {
        $object = new ADuCInputHelper($sensor, $config);
        return $object;
    }
    /**
    * This builds teh ADCFLT Register
    *
    * @param string $reg The register to get
    *
    * @return 16 bit integer that is the FLT setup
    */
    private function _build($reg)
    {
        $ret = 0;
        foreach ($this->_registers[$reg] as $field => $value) {
            $val = $value & $this->_mask[$reg][$field];
            $val <<= $this->_pos[$reg][$field];
            $ret |= $val;
        }
        return $ret;
    }
    /**
    * This takes the class and makes it into a setup string
    *
    * @return string The encoded string
    */
    public function encode()
    {
        return sprintf("%04X%04X%04X",
            $this->_build("ADCFLT"),
            $this->_build("ADC0CON"),
            $this->_build("ADC1CON")
        );
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
