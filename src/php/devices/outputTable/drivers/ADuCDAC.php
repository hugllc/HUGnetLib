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
namespace HUGnet\devices\outputTable\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../Driver.php";

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
class ADuCDAC extends \HUGnet\devices\outputTable\Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Digital to Analog Converter",
        "shortName" => "DAC",
        "extraText" => array(
            0 => "Low Power Mode",
            1 => "Op Amp Mode",
            2 => "Output Buffer",
            3 => "Mode",
            4 => "Interpolation Clock",
            5 => "Range"
        ),
        "extraDefault" => array(
            0, 0, 0, 0, 0, 3
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            array(
                0 => "Disable",
                1 => "Enable"
            ),
            array(
                0 => "Disable",
                1 => "Enable"
            ),
            array(
                0 => "Enable",
                1 => "Disable"
            ),
            array(
                0 => "12 Bit",
                1 => "16 Bit Interpolation"
            ),
            array(
                0 => "UCLK/32",
                1 => "UCLK/16"
            ),
            array(
                3 => "0 V to AVDD",
                2 => "REF2IN− to REF2IN+",
                1 => "VREF− to VREF+",
                0 => "0 V to VREF (1.2 V)",
            ),
        ),
    );
    /** This is the base for our setup byte */
    protected $regBase = 0x0010;

    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string The string to decode
    *
    * @return array
    */
    public function decode($string)
    {
        $extra = (array)$this->output()->get("extra");
        $DAC0Con  = hexdec(substr($string, 0, 2));
        $DAC0Con += hexdec(substr($string, 2, 2))<<8;
        $extra[0] = ($DAC0Con>>8) & 0x01;  // Bit 8
        $extra[1] = ($DAC0Con>>7) & 0x01;  // Bit 7
        $extra[2] = ($DAC0Con>>6) & 0x01;  // Bit 6
        $extra[3] = ($DAC0Con>>3) & 0x01;  // Bit 3
        $extra[4] = ($DAC0Con>>2) & 0x01;  // Bit 2
        $extra[5] = $DAC0Con & 3;  // Bits 0 & 1

        $this->output()->set("extra", $extra);
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function encode()
    {
        $DAC0Con = $this->regBase;
        $DAC0Con |= ((int)$this->getExtra(0))<<8;
        $DAC0Con |= ((int)$this->getExtra(1))<<7;
        $DAC0Con |= ((int)$this->getExtra(2))<<6;
        $DAC0Con |= ((int)$this->getExtra(3))<<3;
        $DAC0Con |= ((int)$this->getExtra(4))<<2;
        $DAC0Con |= ((int)$this->getExtra(5));
        $string = sprintf(
            "%02X%02X",
            $DAC0Con & 0xFF,
            ($DAC0Con >> 8) & 0xFF
        );
        return $string;
    }

}


?>
