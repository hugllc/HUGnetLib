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
 * @subpackage Util
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;

/**
 * This class controls all error messages and exceptions
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Util
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Util
{
    /** This is the polynomial for the CRC-8-CCITT  */
    private static $_poly = 0x07;
    /**
    * This function gives us access to the table class
    *
    * @param string $class     The class to find
    * @param string $dir       The directory to look into
    * @param bool   $quiet     If true no exception is thrown
    * @param string $namespace The namespace to try
    *
    * @return reference to the table class object
    */
    public static function findClass(
        $class, $dir="tables", $quiet=false, $namespace = "\HUGnet"
    ) {
        /** This is our table class */
        if (file_exists(dirname(__FILE__)."/../".$dir."/".$class.".php")) {
            include_once dirname(__FILE__)."/../".$dir."/".$class.".php";
        }
        $baseclass = $class;
        if (!class_exists($class)) {
            $class = "\\".$class;
        }
        if (!class_exists($class)) {
            $class = $namespace.$class;
        }
        if (class_exists($class)) {
            return $class;
        }
        System::systemMissing(
            "Class '".$baseclass."' doesn't exist",
            !$quiet
        );
        return null;
    }
    /**
    * Posts data to the URL given
    *
    * This will automatically json_decode the result if it is in JSON format.  It
    * will then return an associative array.
    *
    * I am not sure yet how to test this function.
    *
    * @param string $url      The URL to post the data to
    * @param array  $postdata The data to post to the URL
    * @param int    $timeout  The timeout in seconds
    *
    * @return mixed
    */
    public static function postData($url, $postdata, $timeout=60)
    {
        global $ctx;
        $params = array(
            'http' => array(
                'method' => 'POST',
                'content' => http_build_query($postdata)."\n",
                'timeout' => $timeout,
            )
        );
        $ctx = stream_context_create($params);
        $response = @file_get_contents($url, false, $ctx);
        $return = json_decode($response, true);
        if (is_null($return) && ($response != "null")) {
            $return = $response;
        }
        unset($response);
        unset($params);
        unset($ctx);
        return $return;
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
            self::crc8_update($crc, hexdec($value));
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
    public static function crc8_update(&$crc, $byte)
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
}


?>
