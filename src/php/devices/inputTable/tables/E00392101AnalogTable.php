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
namespace HUGnet\devices\inputTable\tables;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/AVRAnalogTable.php";
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class E00392101AnalogTable extends AVRAnalogTable
{
    /**
    * This is where we store our sensor object
    */
    protected $subdriver = array(
        0x02 => array(
            "AVRBC2322640" => 0,
        ),
        0x40 => array(
            "DEFAULT" => 0,
        ),
        0x50 => array(
            "DEFAULT" => 0,
        ),
    );
    /**
    * This is where we setup the sensor object
    */
    protected $params = array(
        "driver"  => array(
            "value" => "02:ControllerTemp",
            "valid" => array(
                "02:AVRBC2322640"    => "Temperature Sensor",
                "40:DEFAULT" => "Voltage Sensor",
                "50:DEFAULT" => "Current Sensor",
            ),
            "desc" => "Driver",
        ),
        "priority" => array(
            "value" => 0,
            'mask'  => 0xFF,
            "desc"  => "Priority",
            'size'  => 4,
        ),
        "offset" => array(
            "value" => 0,
            'mask'  => 0xFFFF,
            "desc"  => "Offset",
            'size'  => 7,
        ),
        "REFS"    => array(
            'value' => 1,
            'bit'   => 6,
            'mask'  => 0x3,
            'bits'  => 2,
            'valid' => array(
                0 => "AREF",
                1 => "AVCC",
                3 => "Internal 2.56V",
            ),
            'desc'  => "Reference",
            'register' => "ADMUX",
        ),
        "ADLAR"  => array(
            'value' => 0x1,
            'bit'   => 5,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(
                0 => "Lower 10 bits",
                1 => "Upper 10 bits",
            ),
            'desc'  => "Result Location",
            'register' => "ADMUX",
        ),
        "MUX" => array(
            'value' => 0,
            'bit'   => 0,
            'mask'  => 0x1F,
            'bits'  => 5,
            'valid' => array(
                0  => "ADC0 Single Ended",
                1  => "ADC1 Single Ended",
                2  => "ADC2 Single Ended",
                3  => "ADC3 Single Ended",
                4  => "ADC4 Single Ended",
                5  => "ADC5 Single Ended",
                6  => "ADC6 Single Ended",
                7  => "ADC7 Single Ended",
                8  => "ADC0/ADC0 Differential 10x",
                9  => "ADC1/ADC0 Differential 10x",
                10 => "ADC0/ADC0 Differential 200x",
                11 => "ADC1/ADC0 Differential 200x",
                12 => "ADC2/ADC2 Differential 10x",
                13 => "ADC3/ADC2 Differential 10x",
                14 => "ADC2/ADC2 Differential 200x",
                15 => "ADC3/ADC2 Differential 200x",
                16 => "ADC0/ADC1 Differential 1x",
                17 => "ADC1/ADC1 Differential 1x",
                18 => "ADC2/ADC1 Differential 1x",
                19 => "ADC3/ADC1 Differential 1x",
                20 => "ADC4/ADC1 Differential 1x",
                21 => "ADC5/ADC1 Differential 1x",
                22 => "ADC6/ADC1 Differential 1x",
                23 => "ADC7/ADC1 Differential 1x",
                24 => "ADC0/ADC2 Differential 1x",
                25 => "ADC1/ADC2 Differential 1x",
                26 => "ADC2/ADC2 Differential 1x",
                27 => "ADC3/ADC2 Differential 1x",
                28 => "ADC4/ADC2 Differential 1x",
                29 => "ADC5/ADC2 Differential 1x",
                30 => "1.22V Single Ended",
                31 => "0V Single Ended"
            ),
            'desc'  => "MUX Setting",
            'register' => "ADMUX",
        ),
    );
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
        $object = new E00392101AnalogTable($sensor, $config);
        return $object;
    }
    /**
    * Gets the total gain.
    *
    * @return null
    */
    public function gain()
    {
        $mux  = (int)$this->_params("MUX");
        $gain = 1;
        switch($mux) {
        case 8:
        case 9:
        case 12:
        case 13:
            $gain = 10;
            break;
        case 10:
        case 11:
        case 14:
        case 15:
            $gain = 200;
            break;
        default:
            /* Do nothing */
        }
        return $gain;
    }

}


?>
