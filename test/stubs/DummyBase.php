<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
namespace HUGnet;

/**
 * This function is a stub for just about anything.  It should be extended as
 * needed.
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
 */
class DummyBase
{
    /** @var This is our set values */
    protected static $set = array(
    );
    /** @var This is our returns */
    protected static $ret = array(
    );
    /** @var This is our returns */
    protected $class = "DummyBase";
    /**
    * Tries to run a function defined by what is called..
    *
    * @param array $ret The return array to set
    *
    * @return mixed
    */
    public function resetMock($ret = null)
    {
        self::$ret = (array)$ret;
        self::$set = array();
    }
    /**
    * Tries to run a function defined by what is called..
    *
    * @param string $function The name of the function to call
    *
    * @return mixed
    */
    public function retrieve($function = null)
    {
        if (is_null($function)) {
                return (array)self::$set;
        }
        return (array)self::$set[$function];
    }

    /**
    * Tries to run a function defined by what is called..
    *
    * @param array $class The class to emulate
    *
    * @return mixed
    */
    public function __construct($class = null)
    {
        if (is_string($class)) {
            $this->class = $class;
        }
    }
    /**
    * Tries to run a function defined by what is called..
    *
    * @param string $name The name of the function to call
    * @param array  $args The array of arguments
    *
    * @return mixed
    */
    public static function __callStatic($name, $args)
    {
        $class = get_called_class();
        self::$set[$class][$name][] = $args;
        if (is_array(self::$ret[$class][$name])
            && isset(self::$ret[$class][$name][$args[0]])
        ) {
            return self::$ret[$class][$name][$args[0]];
        }
        return self::$ret[$class][$name];
    }

    /**
    * Tries to run a function defined by what is called..
    *
    * @param string $name The name of the function to call
    * @param array  $args The array of arguments
    *
    * @return mixed
    */
    public function __call($name, $args)
    {
        $class = $this->class;
        self::$set[$class][$name][] = $args;
        if (is_array(self::$ret[$class][$name])
            && (is_string($args[0]) || is_numeric($args[0]))
            && isset(self::$ret[$class][$name][$args[0]])
        ) {
            return self::$ret[$class][$name][$args[0]];
        }
        return self::$ret[$class][$name];
    }

}
?>
