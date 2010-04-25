<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/**
 * Base class for all other classes
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.
 *
 * @category   Base
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class HUGnetClass
{
    /** These are error constants to be used with vprint */
    /** @var no messages */
    const VPRINT_NONE = 0;
    /** @var no messages */
    const VPRINT_NORMAL = 1;
    /** @var error messages */
    const VPRINT_ERROR = 2;
    /** @var verbose messages */
    const VPRINT_VERBOSE = 4;
    /** @var warning messages */
    const VPRINT_WARNING = 8;
    /** @var debug messages */
    const VPRINT_DEBUG = 16;

    /** @var int The verbosity level */
    public $verbose = 0;

    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param mixed $config The configuration array
    *
    * @return null
    */
    public function __construct($config = array())
    {
        if (is_array($config) && isset($config["verbose"])) {
            $this->verbose($config["verbose"]);
        }
    }

    /**
    * Sets the verbosity
    *
    * @param int $level The verbosity level
    *
    * @return null
    */
    public function verbose($level=0)
    {
        $this->verbose = (int)$level;
    }
    /**
    * Prints out a string
    *
    * @param string $str     The string to print out
    * @param int    $val     The minimum value to print this for
    * @param int    $verbose The verbosity level
    *                        (This is for if we are not an object)
    *
    * @return null
    */
    public function vprint(
        $str,
        $val = self::VPRINT_DEBUG,
        $verbose = self::VPRINT_NONE
    ) {
        if (is_object($this) && empty($verbose)) {
            $verbose = $this->verbose;
        }
        if (($verbose < $val) || empty($str)) {
            return;
        }
        if (is_object($this)) {
            $class  = get_class($this);
            print "(".$class.") ";
        }
        print $str."\n";
    }
    /**
    * Load a class file if possible
    *
    * This starts out at the base HUGnetLib directory.  $dir should be relative to
    * that.
    *
    * @param string $class The class or object to use
    * @param stirng $dir   The directory to search
    *
    * @return null
    */
    protected function findClass($class, $dir = "")
    {
        $file = realpath(dirname(__FILE__)."/../".$dir."/".$class.".php");
        // realpath retuns false if the file doesn't exist.
        if (!empty($file)) {
            include_once $file;
        }
        return class_exists($class);
    }
    /**
    * Throws an exception
    *
    * @param string $msg  The message
    * @param int    $code The error code
    *
    * @return null
    */
    protected function throwException($msg, $code)
    {
        if (is_object($this) && ($this->config["silent"])) {
            return;
        }

        throw new Exception($msg, $code);
    }

    /**
    * returns true if passed an object of the same type as me
    *
    * @param mixed  &$obj  The object to check
    * @param string $class The class to use.  Defaults to the current class
    *
    * @return bool Whether this container is empty or not
    */
    public function isMine(&$obj, $class = "")
    {
        if (empty($class)) {
            $class = get_class($this);
        }
        return (bool)(is_object($obj) && ($obj instanceof $class));
    }
    /**
    * Sets the string to a particular size. It modifies the $value
    * parameter.  It will shorten or lengthen the string as it needs to.
    *
    * - It will ALWAYS left pad the string if the string is too short.
    * - It will ALWAYS throw out the left end of the string if the string
    *  is too long
    *
    * @param string &$value The string to fix the size of
    * @param int    $size   The number of characters the string should be
    *                     fixed to
    * @param string $pad    The characters to pad to the LEFT end of the string
    *
    * @return string The modified string
    */
    public static function stringSize(&$value, $size, $pad="0")
    {
        $value = trim($value);
        $value = str_pad($value, $size, $pad, STR_PAD_LEFT);
        $value = substr($value, strlen($value)-$size);
        return strtoupper($value);
    }


}


?>
