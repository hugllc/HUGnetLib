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
class E003912AnalogTable extends AVRAnalogTable
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
                2 => "Internal 1.1V",
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
                8  => "ADC8 Single Ended",
                9  => "ADC9 Single Ended",
                10 => "ADC10 Single Ended",
                11 => "ADC0/ADC1 Differential 20x",
                12 => "ADC0/ADC1 Differential 1x",
                13 => "ADC1/ADC1 Differential 20x",
                14 => "ADC2/ADC1 Differential 20x",
                15 => "ADC2/ADC1 Differential 1x",
                16 => "ADC2/ADC3 Differential 1x",
                17 => "ADC3/ADC3 Differential 20x",
                18 => "ADC4/ADC3 Differential 20x",
                19 => "ADC4/ADC3 Differential 1x",
                20 => "ADC4/ADC5 Differential 20x",
                21 => "ADC4/ADC5 Differential 1x",
                22 => "ADC5/ADC5 Differential 20x",
                23 => "ADC6/ADC5 Differential 20x",
                24 => "ADC6/ADC5 Differential 1x",
                25 => "ADC8/ADC9 Differential 20x",
                26 => "ADC8/ADC9 Differential 1x",
                27 => "ADC9/ADC9 Differential 20x",
                28 => "ADC10/ADC9 Differential 20x",
                29 => "ADC10/ADC9 Differential 1x",
                30 => "1.1V Single Ended",
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
        $object = new E003912AnalogTable($sensor, $config);
        return $object;
    }
    /**
    * Gets the total gain.
    *
    * @return null
    */
    public function gain()
    {
        $mux  = (int)$this->params("MUX");
        switch($mux) {
        case 11:
        case 13:
        case 14:
        case 17:
        case 18:
        case 22:
        case 23:
        case 25:
        case 27:
        case 28:
            $gain = 20;
            break;
        default:
            $gain = 1;
            break;
        }
        return $gain;
    }

}


?>
