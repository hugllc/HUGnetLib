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
class E003928AnalogTable extends AVRAnalogTable
{
    /**
    * This is where we store our sensor object
    */
    protected $subdriver = array(
        0x00 => array(
            "DEFAULT"            => 0,
        ),
        0x02 => array(
            "DEFAULT"            => 0,
            "AVRB57560G0103F000" => 1,
            "imcSolar"           => 2,
            "potDirection"       => 3,
        ),
        0x10 => array(
            "DEFAULT"            => 0,
            "chsMss"             => 0,
        ),
        0x30 => array(
            "DEFAULT"            => 0,
            "OSRAM BPW-34"       => 0,
        ),
        0x40 => array(
            "DEFAULT"            => 0,
            "BARO4"              => 1,
            "GA100"              => 2,
            "HitachiVFDFan"      => 3,
            "fetBoard"           => 4,
        ),
        0x50 => array(
            "DEFAULT"            => 0,
            "dwyer616"           => 1,
            "fetBoard"           => 2,
        ),
    );
    /**
    * This is where we setup the sensor object
    */
    protected $params = array(
        "driver"  => array(
            "value" => "02:DEFAULT",
            "valid" => array(
                "00:DEFAULT"                 => "BC2322 Thermistor (Old)",
                "02:DEFAULT"                 => "BC2322 Thermistor",
                "02:AVRB57560G0103F000"      => "B5756 Thermistor",
                "02:imcSolar"                => "IMC Solar Temperature",
                "02:potDirection"            => "POT Direction",
                "10:chsMss"                  => "CHS MSS Humidity Sensor",
                "30:OSRAM BPW-34"            => "Diode Light Sensor",
                "40:DEFAULT"                 => "Generic Voltage",
                "40:BARO4"                   => "Barometric Pressure Sensor",
                "40:GA100"                   => "GA100 Pressure Sensor",
                "40:HitachiVFDFan"           => "VFD Fan Speed",
                "40:fetBoard"                => "FET Board Voltage",
                "50:DEFAULT"                 => "Generic Current",
                "50:dwyer616"                => "Dwyer 616 Pressure Sensor",
                "50:fetBoard"                => "FET Board Current",
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
            'size'  => 7,
            'longDesc' => "This is a calibration offset.  It is in the units of
                           the driver above.",
        ),
        "REFS"    => array(
            'value' => 1,
            'bit'   => 6,
            'mask'  => 0x3,
            'bits'  => 2,
            'valid' => array(
                0 => "AREF",
                1 => "AVCC",
                3 => "Internal 1.1V",
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
        "ADMUXreserved"    => array(
            'value' => 0,
            'bit'   => 4,
            'mask'  => 0x1,
            'bits'  => 1,
            'valid' => array(0 => "Reserved"),
            'desc'  => "Reserved Space",
            'register' => "ADMUX",
            'hidden' => true,
        ),
        "MUX" => array(
            'value' => 0,
            'bit'   => 0,
            'mask'  => 0xF,
            'bits'  => 4,
            'valid' => array(
                0  => "Port 1 Single Ended",
                1  => "Port 2 Single Ended",
                2  => "Port 3 Single Ended",
                3  => "Port 4 Single Ended",
                4  => "Port 5 Single Ended",
                5  => "Port 6 Single Ended",
                6  => "Port 7 Single Ended",
                7  => "Port 8 Single Ended",
                14 => "1.1V Single Ended",
                15 => "0V Single Ended"
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
        0 => "Port1",
        1 => "Port2",
        2 => "Port3",
        3 => "Port4",
        4 => "Port5",
        5 => "Port6",
        6 => "Port7",
        7 => "Port8",
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
        $object = new E003928AnalogTable($sensor, $config);
        return $object;
    }
}


?>
