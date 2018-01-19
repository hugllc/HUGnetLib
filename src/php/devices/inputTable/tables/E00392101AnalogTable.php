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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
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
            'longDesc' => "The driver to use to sort out data for this ADC",
        ),
        "priority" => array(
            "value" => 0,
            'mask'  => 0xFF,
            "desc"  => "Priority",
            'longDesc' => "(0-255) The number of 1/128 s ticks to wait before 
                          running",
            'size'  => 4,
        ),
        "offset" => array(
            "value" => 0,
            'mask'  => 0xFFFF,
            "desc"  => "Offset",
            'longDesc' => "This is a calibration offset.  It is in the units of
                           the driver above.",
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
            'longDesc' => "The reference to use",
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
            'longDesc' => "How to align the result in the 16bit number returned",
            'register' => "ADMUX",
        ),
        "MUX" => array(
            'value' => 0,
            'bit'   => 0,
            'mask'  => 0x1F,
            'bits'  => 5,
            'valid' => array(
                0  => "HUGnet2 Current",
                1  => "HUGnet2 Temperature",
                2  => "HUGnet2 Voltage -",
                3  => "HUGnet2 Voltage +",
                4  => "HUGnet1 Voltage +",
                5  => "HUGnet1 Voltage -",
                6  => "HUGnet1 Temperature",
                7  => "HUGnet1 Current",
                8  => "HUGnet2 Current/HUGnet2 Current Diff 10x",
                9  => "HUGnet2 Temperature/HUGnet2 Current Diff 10x",
                10 => "HUGnet2 Current/HUGnet2 Current Diff 200x",
                11 => "HUGnet2 Temperature/HUGnet2 Current Diff 200x",
                12 => "HUGnet2 Voltage -/HUGnet2 Voltage - Diff 10x",
                13 => "HUGnet2 Voltage +/HUGnet2 Voltage - Diff 10x",
                14 => "HUGnet2 Voltage -/HUGnet2 Voltage - Diff 200x",
                15 => "HUGnet2 Voltage +/HUGnet2 Voltage - Diff 200x",
                16 => "HUGnet2 Current/HUGnet2 Temperature Diff 1x",
                17 => "HUGnet2 Temperature/HUGnet2 Temperature Diff 1x",
                18 => "HUGnet2 Voltage -/HUGnet2 Temperature Diff 1x",
                19 => "HUGnet2 Voltage +/HUGnet2 Temperature Diff 1x",
                20 => "HUGnet1 Voltage +/HUGnet2 Temperature Diff 1x",
                21 => "HUGnet1 Voltage -/HUGnet2 Temperature Diff 1x",
                22 => "HUGnet1 Temperature/HUGnet2 Temperature Diff 1x",
                23 => "HUGnet1 Current/HUGnet2 Temperature Diff 1x",
                24 => "HUGnet2 Current/HUGnet2 Voltage - Diff 1x",
                25 => "HUGnet2 Temperature/HUGnet2 Voltage - Diff 1x",
                26 => "HUGnet2 Voltage -/HUGnet2 Voltage - Diff 1x",
                27 => "HUGnet2 Voltage +/HUGnet2 Voltage - Diff 1x",
                28 => "HUGnet1 Voltage +/HUGnet2 Voltage - Diff 1x",
                29 => "HUGnet1 Voltage -/HUGnet2 Voltage - Diff 1x",
                30 => "1.22V Single Ended",
                31 => "0V Single Ended"
            ),
            'desc'  => "MUX Setting",
            'longDesc' => "Which port to use",
            'register' => "ADMUX",
        ),
    );
    /**
    * This is where we store our sensor object
    */
    protected $ports = array(
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
