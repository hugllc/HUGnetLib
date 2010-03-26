<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
interface HUGnetContainerInterface
{
    /**
    * Registers extra vars
    *
    * @param mixed  &$obj The class or object to use
    * @param string $var  The variable to register the object on
    *
    * @return null
    */
    function register(&$obj, $var);
    /**
    * Sets the extra attributes field
    *
    * @return mixed The value of the attribute
    */
    public function clearData();
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
    * @param mixed $names Array of names to lock
    *
    * @return mixed The value of the attribute
    */
    public function lock($names);
    /**
    * resets a value to its default
    *
    * @param mixed $names Array of names to lock
    *
    * @return mixed The value of the attribute
    */
    public function unlock($names);
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
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($array);
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @return null
    */
    public function toArray();
    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function fromString($string);
    /**
    * Returns the object as a string
    *
    * @return string
    */
    public function toString();

}
?>
