<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
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
            101,
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


}


?>
