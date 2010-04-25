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
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";

/**
 * This class keeps track of hooks that can be defined and used other places in the
 * code to cause custom functions to happen.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HooksContainer extends HUGnetContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "hooks" => array(),               // The array of server information
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($array)
    {
        foreach ((array)$array as $key => $hook) {
            $class = &$hook["class"];
            if (class_exists($class)) {
                $this->data["hooks"][$key] = array(
                    "obj" => new $class($hook["obj"]),
                    "class" => $class,
                );
            }
        }
    }
    /**
    * Registers a hook, along with the object to use
    *
    * @param string $group   The group to which this hook belongs
    * @param object &$object The object to use for the hook
    *
    * @return object PDO object, null on failure
    */
    public function registerHook($group, &$object)
    {
        if (is_object($object)) {
            $this->data["hooks"][$group] = array(
                "obj" => &$object,
                "class" => get_class($object),
            );
            return true;
        } else {
            return false;
        }
    }
    /**
    * returns an object to call stuff from
    *
    * @param string $name      The hook to use
    * @param string $interface The interface or class to look for.  Blank for any
    *
    * @return bool True on success, false on failure
    */
    public function &hook($name, $interface = "")
    {
        if (!is_object($this->data["hooks"][$name]["obj"])) {
            return $this;
        }
        if (!empty($interface)
            && !($this->data["hooks"][$name]["obj"] instanceof $interface)
        ) {
            return $this;
        }
        return $this->data["hooks"][$name]["obj"];
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
        self::vprint("No hook defined", HUGnetClass::VPRINT_VERBOSE);
    }

}
?>
