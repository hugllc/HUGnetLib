<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, 
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: eDEFAULT.php 445 2007-11-13 16:53:06Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

if (!class_exists("devInfo")) {
    /**
     * This class has functions that relate to the manipulation of elements
     * of the devInfo array.
     *
     * @category   Misc
     * @package    HUGnetLib
     * @subpackage Endpoints
     * @author     Scott Price <prices@hugllc.com>
     * @copyright  2007 Hunt Utilities Group, LLC
     * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
     * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
     */
    class DevInfo 
    {
        /**
         * Sets the DeviceID if it is not set.  Valid places to set 
         * the DeviceID from are:
         *  - PacketFrom
         *  - From
         *
         * @param array &$Info devInfo array
         *
         * @return string The DeviceID
         */
        public static function deviceID(&$Info) 
        {
            if (empty($Info['DeviceID'])) {
                if (isset($Info['PacketFrom'])) {
                    $Info['DeviceID'] = $Info['PacketFrom'];
                } else if (isset($Info['From'])) {
                    $Info['DeviceID'] = $Info['From'];
                }    
            }
            if (!empty($Info['DeviceID'])) {
                devInfo::setStringSize($Info['DeviceID'], 6);
            }
            return $Info['DeviceID'];
        }
    
        /**
         * Sets the RawData if it is not set.  Valid places to 
         * set the RawData from are:
         * - Data
         * - rawdata
         * - RawSetup
         *
         * @param array &$Info devInfo array
         *
         * @return string The RawData
         */
        public static function rawData(&$Info) 
        {
            if (empty($Info["RawData"])) {
                if (!empty($Info["Data"])) {
                    $Info["RawData"] = $Info["Data"];
                } else if (!empty($Info["rawdata"])) {
                    $Info["RawData"] = $Info["rawdata"];
                } else if (!empty($Info["RawSetup"])) {
                    $Info['RawData'] = $Info['RawSetup'];
                }
            }
            return $Info['RawData'];
        }
    
        /**
         *  Sets the Date if it is not set.  Valid places to set the Date from are:
         *  - Date
         *
         * @param array  &$Info devInfo array
         * @param string $Field The field in the $Info array to set the date in.
         *
         * @return string The RawData
         */
        public static function setDate(&$Info, $Field) 
        {
            if (!empty($Info["Date"])) {
                $Info[$Field] = $Info["Date"];
            } else {
                $Info[$Field] = date("Y-m-d H:i:s");
            }
            return $Info[$Field];
        }
    
        
        /**
         * Sets the string to a particular size. It modifies the $value 
         * parameter.  It will shorten or lengthen the string as it needs to.
         *  
         * - It will ALWAYS left pad the string if the string is too short.
         * - It will ALWAYS throw out the left end of the string if the string 
         *    is too long
         *
         * @param string &$value The string to fix the size of
         * @param int    $size   The number of characters the string should be 
         *                       fixed to
         * @param string $pad    The characters to pad to the LEFT end of the string
         *
         * @return string The modified string
         */
        public static function setStringSize(&$value, $size, $pad="0") 
        {
            $value = trim($value);
            $value = str_pad($value, $size, $pad, STR_PAD_LEFT);
            $value = substr($value, strlen($value)-$size);
            $value = strtoupper($value);
            return $value;
        }   
        
        /**
         * Hexifies a version in x.y.z form.
         *
         * @param string $version The version is x.y.z form
         *
         * @return string Hexified version (asciihex)
         */
        public static function hexifyVersion($version) 
        {
            $ver = explode(".", $version);
            $str = "";
            for($i = 0; $i < 3; $i++) $str .= self::setStringSize($ver[$i], 2);
            return $str;
        }
    
        /**
         * Hexifies a version in x.y.z form.
         *
         * @param string $PartNum The part number in XXXX-XX-XX-A form
         *
         * @return string Hexified version (asciihex)
         */
        public static function hexifyPartNum($PartNum) 
        {
            $part = explode("-", $PartNum);
            $str  = self::setStringSize($part[0], 4);
            $str .= self::setStringSize($part[1], 2);
            $str .= self::setStringSize($part[2], 2);
            if (!empty($part[3])) {
                $chr  = ord($part[3]);
                $str .= self::hexify($chr, 2);
            }
            self::setStringSize($str, 10); 
            return $str;
        }
    
        /**
         * Hexifies a version in x.y.z form.
         *
         * @param string $version The version is x.y.z form
         *
         * @return string Hexified version (asciihex)
         */
        public static function dehexifyVersion($version) 
        {
            $version = strtoupper($version);
            $str     = array();
            for($i = 0; $i < 3; $i++) $str[] .= (int)substr($version, ($i*2), 2);
            return implode(".", $str);
        }
    
        /**
         * Hexifies a version in x.y.z form.
         *
         * @param string $PartNum The part number in XXXXXXXXX form.
         *
         * @return string Hexified version (asciihex)
         */
        public static function dehexifyPartNum($PartNum) 
        {
            $PartNum = strtoupper($PartNum);
            $str     = array();
            $str[]   = substr($PartNum, 0, 4);
            $str[]   = substr($PartNum, 4, 2);
            $str[]   = substr($PartNum, 6, 2);
            $str[]   = self::dehexify(substr($PartNum, 8, 2));
            return implode("-", $str);
        }
        /**
         * Turns a number into a text hexidecimal string
         *   
         * If the number comes out smaller than $width the string is padded 
         * on the left side with zeros.
         *
         * Duplicate: {@link epsocket::hexify()}
         *
         * @param int $value The number to turn into a hex string
         * @param int $width The width of the final string
         *
         * @return string The hex string created.
        */
        function hexify($value, $width=2) 
        {
            $value = dechex($value);
            $value = str_pad($value, $width, "0", STR_PAD_LEFT);
            $value = substr($value, strlen($value)-$width);
            $value = strtoupper(trim($value));
    
            return($value);
        }
    
    
        /**
         * Turns a binary string into a text hexidecimal string
         *   
         * If the number comes out smaller than $width the string is padded 
         * on the left side with zeros.
         *
         * If $width is not set then the string is kept the same lenght as
         * the incoming string.
         *
         * @param string $str   The binary string to convert to hex
         * @param int    $width The width of the final string
         *
         * @return string The hex string created.
        */
        function hexifyStr($str, $width=null) 
        {
            $value = "";
            $length = strlen($str);
            if (is_null($width)) $width = $length;
            for ($i = 0; ($i < $length) && ($i < $width); $i++) {
                $char   = substr($str, $i, 1);
                $char   = ord($char);
                $value .= self::hexify($char, 2);
            }
            $value = str_pad($value, $width, "0", STR_PAD_RIGHT);
            
            return($value);
        }
    
        /**
         * Changed a hex string into a binary string.
         *
         * @param string $string The hex packet string
         *
         * @return string The binary string.
         */
        
        function deHexify($string) 
        {
            $string = trim($string);
            $bin    = "";
            for ($i = 0; $i < strlen($string); $i+=2) {
                $bin .= chr(hexdec(substr($string, $i, 2)));
            }
            return $bin;
        }
    
    
    }
}
?>