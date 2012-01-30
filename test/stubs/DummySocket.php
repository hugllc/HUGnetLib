<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\network\physical;
/** This is our base class */
require_once dirname(__FILE__)."/DummyBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DummySocket extends \HUGnet\DummyBase
{
    /** @var This is our returns */
    protected $class = "DummySocket";
    /**
    * Creates the object
    *
    * @param array $config The configuration to use
    *
    * @return null
    */
    public function &factory($config = array())
    {
        return new DummySocket($config["name"]);
    }
    /**
    * Reads from the socket
    *
    * @return string on success, False on failure
    */
    public function read()
    {
        $ret = parent::__call("read", func_get_args());
        if (is_string(self::$ret[$this->class]["read"])) {
            self::$ret[$this->class]["read"] = "";
        } else if (is_array(self::$ret[$this->class]["read"])) {
            return array_shift(self::$ret[$this->class]["read"]);
        }
        return (string)$ret;
    }

    /**
    * Reads from the socket
    *
    * @param string $string The string to write
    *
    * @return string on success, False on failure
    */
    public function write($string)
    {
        $ret = parent::__call("write", func_get_args());
        if (is_string(self::$ret[$this->class]["write"])) {
            self::$ret[$this->class]["write"] = "";
        } else if (is_array(self::$ret[$this->class]["write"])) {
            return array_shift(self::$ret[$this->class]["write"]);
        }
        if (!is_null($ret)) {
            return (string)$ret;
        }
        return strlen($string)/2;
    }


}
?>
