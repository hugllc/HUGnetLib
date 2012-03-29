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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;

/**
 * This class controls all error messages and exceptions
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Util
{
    /** This is the polynomial for the CRC  */
    private static $_poly = 0xA6;
    /**
    * This function gives us access to the table class
    *
    * @param string $class The class to find
    * @param string $dir   The directory to look into
    * @param bool   $quiet If true no exception is thrown
    *
    * @return reference to the table class object
    */
    public static function findClass($class, $dir="tables", $quiet=false)
    {
        /** This is our table class */
        @include_once dirname(__FILE__)."/../".$dir."/".$class.".php";
        $baseclass = $class;
        if (!class_exists($class)) {
            $class = "\\".$class;
        }
        if (!class_exists($class)) {
            $class = "\\HUGnet".$class;
        }
        if (class_exists($class)) {
            return $class;
        }
        System::exception(
            "Class '".$baseclass."' doesn't exist",
            "InvalidArgument",
            !$quiet
        );
        return null;
    }
    /**
    * Returns the CRC8 of the packet
    *
    * @param string $string The string to get the CRC of.
    *
    * @return byte The total CRC
    */
    public static function crc8($string)
    {
        $pkt = str_split($string, 2);
        $crc = 0;
        foreach ($pkt as $value) {
            self::_crc8byte($crc, hexdec($value));
        }
        return $crc;
    }
    /**
    * Checks to see if this packet is valid
    *
    * @param int &$crc The total CRC so far.  SHould be set to 0 to start
    * @param int $byte The byte we are adding to the crc
    *
    * @return byte The total CRC
    */
    private static function _crc8byte(&$crc, $byte)
    {
        $crc = ((int)$crc ^ (int)$byte) & 0xFF;
        for ($bit = 8; $bit > 0; $bit--) {
            if (($crc & 0x80) == 0x80) {
                $crc = ($crc << 1) ^ self::$_poly;
            } else {
                $crc = $crc << 1;
            }
            $crc = $crc & 0xFF;
        }
    }

    /**
    * Turns a binary string into ascii hex
    *
    * @param string $string The binary string
    *
    * @return The ascii hex representation of string
    */
    public static function hexify($string)
    {
        $return = "";
        if (strlen($string) > 0) {
            foreach (str_split($string, 1) as $byte) {
                $return .= sprintf("%02X", ord($byte));
            }
        }
        return $return;
    }
    /**
    * Turns an ascii hex string into binary
    *
    * @param string $string The ascii hex string string
    *
    * @return The binary representation of the string
    */
    public static function binary($string)
    {
        $data = "";
        if (strlen($string) > 0) {
            foreach (str_split($string, 2) as $byte) {
                $data .= chr(hexdec($byte));
            }
        }
        return $data;
    }
    /**
    * Changes an n-bit twos compliment number into a signed number PHP can use
    *
    * @param int   $A    The incoming number
    * @param float $bits The number of bits the incoming number is
    *
    * @return int A signed integer for PHP to use
    */
    public static function getTwosCompliment($A, $bits = 24)
    {
        /* Clear off any excess */
        $A = (int)($A & (pow(2, $bits) - 1));
        /* Calculate the top bit */
        $topBit = pow(2, ($bits - 1));
        /* Check to see if the top bit is set */
        if (($A & $topBit) == $topBit) {
            /* This is a negative number */
            $A = -(pow(2, $bits) - $A);
        }
        return $A;
    }
    /**
    * Compensates for an input and bias resistance.
    *
    * The bias and input resistance values can be in Ohms, kOhms or even MOhms.  It
    * doesn't matter as long as they are both the same units.
    *
    * @param float $A     The incoming number
    * @param float $Rin   The input resistor.
    * @param float $Rbias The bias resistor.
    *
    * @return float The compensated value
    */
    public static function inputBiasCompensation($A, $Rin, $Rbias)
    {
        if ($Rbias == 0) {
            return null;
        }
        return (float)(($A * ($Rin + $Rbias)) / $Rbias);
    }
    /**
    * This makes a line of two ordered pairs, then puts $A on that line
    *
    * @param float $A    The incoming value
    * @param float $Imin The input minimum
    * @param float $Imax The input maximum
    * @param float $Omin The output minimum
    * @param float $Omax The output maximum
    *
    * @return output rounded to 4 places
    */
    public static function linearBounded($A, $Imin, $Imax, $Omin, $Omax)
    {
        if (is_null($A)) {
            return null;
        }
        if ($Imax == $Imin) {
            return null;
        }
        if ($A > $Imax) {
            return null;
        }
        if ($A < $Imin) {
            return null;
        }
        $m = ($Omax - $Omin) / ($Imax - $Imin);
        $b = $Omax - ($m * $Imax);
        $O = ($m * $A) + $b;
        return $O;
    }
}


?>
