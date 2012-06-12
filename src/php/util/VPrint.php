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
 * @subpackage Util
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
 * @subpackage Util
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class VPrint
{
    /** This is the current verbosity level */
    private static $_verbose = 0;
    /** This is the flag for HTML mode  */
    private static $_html = false;
    /** This is the flag for debug mode  */
    private static $_debug = false;
    /** This is the string to output for debug mode  */
    private static $_debugOut = "";
    /** This is the string to output for debug mode  */
    private static $_sess = null;
    /**
    * This function gives us access to the table class
    *
    * @param array $config The confituration to use
    *
    * @return reference to the table class object
    */
    public static function config($config)
    {
        self::$_verbose = $config["verbose"];
        self::$_debug = $config["debug"];
        self::$_html = isset($config["html"]) ? $config["html"] : PHP_SAPI != "cli";
        self::$_sess = $config["session"];
    }
    /**
    * This function prints out string if level >= verbosity
    *
    * @param string $string The string to print out
    * @param int    $level  The verbosity level to print it at
    *
    * @return none
    */
    public static function out($string, $level=0)
    {
        if ($level <= self::$_verbose) {
            if (is_string(self::$_sess)) {
                $_SESSION[self::$_sess] .= $string.PHP_EOL;
            } else if (is_resource(self::$_sess)) {
                fwrite(self::$_sess, $string.PHP_EOL);
            } else if (self::$_html && self::$_debug) {
                // Save everything for later
                self::$_debugOut .= (string)$string.self::_eol();
            } else if (!is_string(self::$_sess)) {
                print (string)$string.self::_eol();
            }
        }
    }
    /**
    * This function figures out what EOL character to use
    *
    * @return EOL character
    */
    private static function _eol()
    {
        $return = "";
        if (self::$_html) {
            $return .= "<br />";
        }
        return $return.PHP_EOL;
    }
    /**
    * This outputs all of our print outs all at once
    *
    * @return none
    */
    public static function debug()
    {
        return self::$_debugOut;
    }

}


?>
