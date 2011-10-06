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
 * @package    HUGnetLib
 * @subpackage Interfaces
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Interfaces
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
interface HUGnetExtensibleContainerInterface
{
    /**
    * Registers extra vars
    *
    * @param mixed  &$obj The class or object to use
    * @param string $var  The variable to register the object on
    *
    * @return null
    */
    public function register(&$obj, $var);
    /**
    * Registers extra vars
    *
    * @param object &$obj    The class or object to use
    * @param bool   $recurse Whether to modify this new object
    *
    * @return null
    */
    public function registerNext(&$obj, $recurse = true);
    /**
    * Registers extra vars
    *
    * @param object &$obj    The class or object to use
    * @param bool   $recurse Whether to modify this new object
    *
    * @return null
    */
    public function registerPrev(&$obj, $recurse = true);
    /**
    * Registers extra vars
    *
    * @param string $var     The variable to register the object on
    * @param bool   $recurse Whether to modify the old object
    *
    * @return null
    */
    public function unregister($var, $recurse = true);
    /**
    * Registers extra vars
    *
    * @param bool $recurse Whether to modify this new object
    *
    * @return null
    */
    public function unregisterNext($recurse = true);
    /**
    * Registers extra vars
    *
    * @param bool $recurse Whether to modify this new object
    *
    * @return null
    */
    public function unregisterPrev($recurse = true);
    /**
    * Sets the extra attributes field
    *
    * @param string $var The variable to check
    *
    * @return mixed The value of the attribute
    */
    public function getProperties($var = null);
    /**
    * Sets the extra attributes field
    *
    * @param string $var The variable to check
    *
    * @return mixed The value of the attribute
    */
    public function getMethods($var = null);
    /**
    * resets a value to its default
    *
    * @param string $name This is the attribute to get
    *
    * @return mixed The value of the attribute
    */
    public function setDefault($name);
    /**
    * resets a value to its default
    *
    * @param string $name Array of names to lock
    * @param string $var  The name of the variable to traverse
    *                     *** For internal use only ***
    *
    * @return mixed The value of the attribute
    */
    public function locked($name = null, $var = null);
}
?>
