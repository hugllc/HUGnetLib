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
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/HUGnetClass.php";
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
/** This is for the base class */
require_once dirname(__FILE__)
    ."/../interfaces/HUGnetExtensibleContainerInterface.php";

/**
 * This is a generic, extensible container class
 *
 * Classes can be added in so that their methods and properties can be used
 * by this class and the reverse of that.  There can be a whole linked list
 * of containers that extend eachother.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
abstract class HUGnetExtensibleContainer extends HUGnetContainer
    implements HUGnetExtensibleContainerInterface
{
    /** @var object The extra stuff class */
    private $_extra = null;
    /** @var object The extra stuff class */
    private $_extraPrev = null;
    /** @var object The extra stuff class */
    private $_extraNext = null;
    /** @var object The locked values */
    private $_lock = array();

    /**
    * This is the constructor
    *
    * @param mixed  $data  This is an array or string to create the object from
    * @param object &$next The next object in the list
    * @param object &$prev The previous object in the list
    */
    function __construct($data="", &$next=null, &$prev=null)
    {
        $this->registerPrev($prev);
        $this->registerNext($next);
        $this->clearData();
        if (is_string($data)) {
            $this->fromString($data);
        } else if (is_array($data)) {
            $this->fromArray($data);
        }
    }
    /**
    * Registers extra vars
    *
    * @param mixed  &$obj    The class or object to use
    * @param string $var     The variable to register the object on
    * @param bool   $recurse Whether to modify this new object
    *
    * @return null
    */
    final public function register(&$obj, $var, $recurse = true)
    {
        if (strtolower($var) == "next") {
            $var   = "_extraNext";
            $name  = "next";
            $other = "prev";
        } else if (strtolower($var) == "prev") {
            $var   = "_extraPrev";
            $name  = "prev";
            $other = "next";
        } else {
            $name  = null;
            // This sets up the other object
            $other = "prev";
        }
        if (!is_object($obj) || is_object($this->$var) || empty($var)) {
            return false;
        }
        // Set up the object
        $this->$var =& $obj;
        if (!is_null($name)) {
            // Get the properties
            $this->_extra["Properties"][$name] = $this->$var->getProperties($var);
            // Get the methods
            $this->_extra["Methods"][$name] = $this->$var->getMethods($var);
            // Set as registered
            $this->_extra["Registered"][$var] = $var;
        }
        // Register the class
        $this->_extra["Classes"][$var] = get_class($obj);
        // Set up the other object.
        if ($recurse) {
            $this->$var->register($this, $other, false);
        }
        return true;
    }
    /**
    * Registers extra vars
    *
    * @param object &$obj    The class or object to use
    * @param bool   $recurse Whether to modify this new object
    *
    * @return null
    */
    final public function registerNext(&$obj, $recurse = true)
    {
        return $this->register($obj, "next", $recurse);
    }
    /**
    * Registers extra vars
    *
    * @param object &$obj    The class or object to use
    * @param bool   $recurse Whether to modify this new object
    *
    * @return null
    */
    final public function registerPrev(&$obj, $recurse = true)
    {
        return $this->register($obj, "prev", $recurse);
    }
    /**
    * Registers extra vars
    *
    * @param string $var     The variable to register the object on
    * @param bool   $recurse Whether to modify the old object
    *
    * @return null
    */
    final public function unregister($var, $recurse = true)
    {
        if (strtolower($var) == "next") {
            $var   = "_extraNext";
            $name  = "next";
            $other = "prev";
        } else if (strtolower($var) == "prev") {
            $var   = "_extraPrev";
            $name  = "prev";
            $other = "next";
        } else {
            $name  = null;
            // This sets up the other object
            $other = "prev";
        }
        if (empty($var)) {
            return false;
        }
        if (!is_object($this->$var)) {
            return true;
        }
        // Set up the other object.
        if ($recurse) {
            $this->$var->unregister($other, false);
        }
        if (!is_null($name)) {
            // Get the properties
            unset($this->_extra["Properties"][$name]);
            // Get the methods
            unset($this->_extra["Methods"][$name]);
            // Set as registered
            unset($this->_extra["Registered"][$var]);
        }
        // Register the class
        unset($this->_extra["Classes"][$var]);
        // obliterate the object
        $obj = null;
        $this->$var =& $obj;
        return true;
    }
    /**
    * Registers extra vars
    *
    * @param bool $recurse Whether to modify this new object
    *
    * @return null
    */
    final public function unregisterNext($recurse = true)
    {
        return $this->unregister("next", $recurse);
    }
    /**
    * Registers extra vars
    *
    * @param bool $recurse Whether to modify this new object
    *
    * @return null
    */
    final public function unregisterPrev($recurse = true)
    {
        return $this->unregister("prev", $recurse);
    }

    /**
    * Overload the set attribute
    *
    * @param string $name  This is the attribute to set
    * @param mixed  $value The value to set it to
    *
    * @return mixed The value of the attribute
    */
    public function set($name, $value)
    {
        if (array_key_exists($name, $this->default)) {
            parent::set($name, $value);
        } else if ($var = $this->_findExtra($name)) {
            $this->$var->$name = $value;
        }
    }
    /**
    * Overload the get attribute
    *
    * @param string $name This is the attribute to get
    *
    * @return mixed The value of the attribute
    */
    public function get($name)
    {
        if (array_key_exists($name, $this->default)) {
            return $this->data[$name];
        } else if ($var = $this->_findExtra($name)) {
            return $this->$var->$name;
        }
        return null;
    }
    /**
    * unset an attribute
    *
    * @param string $name This is the attribute to get
    *
    * @return mixed The value of the attribute
    */
    public function __unset($name)
    {
        if (array_key_exists($name, $this->default)) {
            parent::__unset($name);
        } else if ($var = $this->_findExtra($name)) {
            unset($this->$var->$name);
        }
    }
    /**
    * Check if something is set
    *
    * @param string $name This is the attribute to get
    *
    * @return mixed The value of the attribute
    */
    public function __isset($name)
    {
        if (array_key_exists($name, $this->default)) {
            return (bool)isset($this->data[$name]);
        } else if ($var = $this->_findExtra($name)) {
            return (bool)isset($this->$var->$name);
        }
        return false;
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
        if ($var = $this->_findExtra($name, false)) {
            return call_user_func_array(array($this->$var, $name), $args);
        }
        return null;
    }

    /**
    * Finds the variable to use to find a property or method
    *
    * @param string $find     The name of the thing to find
    * @param bool   $property Find a property if true
    *
    * @return string the variable to reference
    */
    private function _findExtra($find, $property = true)
    {
        $index = ($property) ? "Properties" : "Methods";
        $haystack = &$this->_extra[$index];
        if (!is_bool(array_search($find, (array)$haystack["next"]))) {
            return "_extraNext";
        }
        if (!is_bool(array_search($find, (array)$haystack["prev"]))) {
            return "_extraPrev";
        }
        return false;
    }
    /**
    * Sets the extra attributes field
    *
    * @param string $var The variable to check
    *
    * @return mixed The value of the attribute
    */
    public function getProperties($var = null)
    {
        $extra = array();
        if (is_object($this->$var)) {
            $extra = $this->$var->getProperties($var);
        } else if (is_null($var)) {
            foreach ((array)$this->_extra["Properties"] as $vars) {
                $extra = array_merge((array)$extra, $vars);
            }
        }
        return array_merge(array_keys((array)$this->default), (array)$extra);
    }

    /**
    * Sets the extra attributes field
    *
    * @param string $var The variable to check
    *
    * @return mixed The value of the attribute
    */
    public function getMethods($var = null)
    {
        if (is_object($this->$var)) {
            $extra = $this->$var->getMethods($var);
        } else if (is_null($var)) {
            foreach ((array)$this->_extra["Methods"] as $vars) {
                $extra = array_merge((array)$extra, $vars);
            }
        }
        $myMethods = get_class_methods(__CLASS__);
        $methods = array_diff(get_class_methods(get_class($this)), $myMethods);
        $methods = array_merge($methods, (array)$extra);
        return $methods;
    }

    /**
    * resets a value to its default
    *
    * @param string $name This is the attribute to get
    *
    * @return mixed The value of the attribute
    */
    public function setDefault($name)
    {
        if (array_key_exists($name, (array)$this->default)) {
            parent::setDefault($name);
        } else if ($var = $this->_findExtra($name)) {
            $this->$var->setDefault($name);
        }
    }
    /**
    * resets a value to its default
    *
    * @param mixed $names Array of names to lock
    *
    * @return mixed The value of the attribute
    */
    public function lock($names)
    {
        $this->_lock($names, "lock");
    }


    /**
    * resets a value to its default
    *
    * @param mixed $names Array of names to lock
    *
    * @return mixed The value of the attribute
    */
    public function unlock($names)
    {
        $this->_lock($names, "unlock");
    }

    /**
    * resets a value to its default
    *
    * @param mixed  $names  Array of names to lock
    * @param string $method 'lock' or 'unlock'
    *
    * @return mixed The value of the attribute
    */
    private function _lock($names, $method = "lock")
    {
        if (is_string($names)) {
            $names = array($names);
        }
        foreach ((array)$names as $name) {
            if (!is_string($name)) {
                continue;
            }
            if (array_key_exists((string)$name, $this->default)) {
                if (strtolower($method) == "lock") {
                    $this->_lock[$name] = $name;
                } else {
                    unset($this->_lock[$name]);
                }
            } else if ($var = $this->_findExtra($name)) {
                $this->$var->$method($name);
            }
        }
    }

    /**
    * resets a value to its default
    *
    * @param string $name Array of names to lock
    * @param string $var  The name of the variable to traverse
    *                     *** For internal use only ***
    *
    * @return mixed The value of the attribute
    */
    public function locked($name = null, $var = null)
    {
        if (array_key_exists($name, $this->default)) {
            return isset($this->_lock[$name]);
        } else if ($useVar = $this->_findExtra($name)) {
            return $this->$useVar->locked($name);
        } else if (is_null($name)) {
            if (is_object($this->$var)) {
                $extra = $this->$var->locked(null, $var);
            } else if (empty($var)) {
                foreach ((array)$this->_extra["Registered"] as $var) {
                    $extra = array_merge(
                        (array)$extra,
                        $this->$var->locked(null, $var)
                    );
                }
            }
            return array_merge(array_keys($this->_lock), (array) $extra);
        }
        return false;
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toArray($default = true)
    {
        foreach ($this->getProperties() as $key) {
            if (($this->$key != $this->default[$key]) || $default) {
                $value = $this->toArrayIterator(
                    $this->$key,
                    $this->default[$key],
                    $default
                );
                if (($value != $default[$key]) || $default) {
                    $data[$key] = $value;
                }
            }
        }
        return (array)$data;
    }
}
?>
