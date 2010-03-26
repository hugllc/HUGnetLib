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
/** This is for the base class */
require_once dirname(__FILE__)."/HUGnetClass.php";

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
abstract class HUGnetContainer extends HUGnetClass
{
    /** @var object The extra stuff class */
    private $_extra = null;
    /** @var object The locked values */
    private $_lock = array();

    /**
    * This is the constructor
    *
    * @param mixed  $data  This is an array or string to create the object from
    * @param string $extra This should be an extension of the devInfo object
    */
    function __construct($data="", $extra=null)
    {
        if (class_exists($extra)) {
            $this->_extra = new $extra($mixed, null);
        }
        $this->clearData();
        if (is_string($data)) {
            $this->fromString($data);
        } else if (is_array($data)) {
            $this->fromArray($data);
        }
    }
    /**
    * Overload the set attribute
    *
    * @param string $name  This is the attribute to set
    * @param mixed  $value The value to set it to
    *
    * @return mixed The value of the attribute
    */
    public function __set($name, $value)
    {
        if ($this->locked($name)) {
            self::vprint(
                "Error: Trying to access a locked property\n",
                1
            );
        } else if (array_key_exists($name, $this->default)
            && !$this->locked($name)
        ) {
            $this->data[$name] = $value;
            if (method_exists($this, $name)) {
                $this->$name();
            }
        } else if (is_object($this->_extra)) {
            $this->_extra->$name = $value;
        }
    }
    /**
    * Overload the get attribute
    *
    * @param string $name This is the attribute to get
    *
    * @return mixed The value of the attribute
    */
    private function __get($name)
    {
        if (array_key_exists($name, $this->default)) {
            return $this->data[$name];
        } else if (is_object($this->_extra)) {
            return $this->_extra->$name;
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
    private function __unset($name)
    {
        if ($this->locked($name)) {
            self::vprint(
                "Error: Trying to access a locked property\n",
                1
            );
        } else if (array_key_exists($name, $this->default)) {
            unset($this->data[$name]);
        } else if (is_object($this->_extra)) {
            unset($this->_extra->$name);
        }
    }
    /**
    * Check if something is set
    *
    * @param string $name This is the attribute to get
    *
    * @return mixed The value of the attribute
    */
    private function __isset($name)
    {
        if (array_key_exists($name, $this->default)) {
            return (bool)isset($this->data[$name]);
        } else if (is_object($this->_extra)) {
            return (bool)isset($this->_extra->$name);
        }
        return false;
    }

    /**
    * Converts the object to a string
    *
    * @return mixed The value of the attribute
    */
    private function __toString()
    {
        return $this->toString();
    }

    /**
    * Sets the extra attributes field
    *
    * @return mixed The value of the attribute
    */
    public function getAttributes()
    {
        if (is_object($this->_extra)) {
            $extra = $this->_extra->getAttributes();
        }
        return array_merge(array_keys((array)$this->default), (array)$extra);
    }

    /**
    * Sets the extra attributes field
    *
    * @return mixed The value of the attribute
    */
    public function clearData()
    {
        foreach ($this->default as $name => $value) {
            if (!$this->locked($name)) {
                $this->data[$name] = $this->default[$name];
            }
        }
        if (is_object($this->_extra)) {
            $this->_extra->clearData();
        }
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
        if ($this->locked($name)) {
            self::vprint(
                "Error: Trying to access a locked property\n",
                1
            );
        } else if (array_key_exists($name, $this->default)) {
            $this->data[$name] = $this->default[$name];
        } else if (is_object($this->_extra)) {
            $this->_extra->setDefault($name);
        }
    }
    /**
    * resets a value to its default
    *
    * @param mixed $names Array of names to lock
    *
    * @return mixed The value of the attribute
    */
    public function lock($names = array())
    {
        if (is_array($names)) {
            foreach ($this->default as $name => $value) {
                if (!is_bool(array_search($name, (array)$names))) {
                    $this->_lock[$name] = $name;
                }
            }
        } else if (is_string($names)) {
            if (array_key_exists($names, $this->default)) {
                $this->_lock[$names] = $names;
            }
        }
        if (is_object($this->_extra)) {
            $this->_extra->lock($names);
        }
    }


    /**
    * resets a value to its default
    *
    * @param mixed $names Array of names to lock
    *
    * @return mixed The value of the attribute
    */
    public function unlock($names = array())
    {
        if (is_array($names)) {
            foreach ($this->default as $name => $value) {
                if (!is_bool(array_search($name, (array)$names))) {
                    unset($this->_lock[$name]);
                }
            }
        } else if (is_string($names)) {
            if (array_key_exists($names, $this->default)) {
                unset($this->_lock[$names]);
            }
        }
        if (is_object($this->_extra)) {
            $this->_extra->unlock($names);
        }
    }

    /**
    * resets a value to its default
    *
    * @param string $names Array of names to lock
    *
    * @return mixed The value of the attribute
    */
    public function locked($name = null)
    {
        if (array_key_exists($name, $this->default)) {
            return isset($this->_lock[$name]);
        } else if (is_null($name)) {
            if (is_object($this->_extra)) {
                $extra = $this->_extra->locked();
            }
            return array_merge(array_keys($this->_lock), (array) $extra);
        } else if (is_object($this->_extra)) {
            return $this->_extra->locked($name);
        }
        return false;
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $devInfo This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($devInfo)
    {
        foreach ($this->getAttributes() as $attrib) {
            if (isset($devInfo[$attrib])) {
                $this->$attrib = $devInfo[$attrib];
            }
        }
        if (is_object($this->_extra)) {
            $this->_extra->fromArray($devInfo);
        }
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @return null
    */
    public function toArray()
    {

        if (is_object($this->_extra)) {
            $extra = $this->_extra->toArray();
        }
        foreach (array_keys((array)$this->data) as $key) {
            if (is_object($this->data[$key])
                && method_exists($this->data[$key], "toArray")
            ) {
                $data[$key] = $this->data[$key]->toArray();
            } else {
                $data[$key] = $this->data[$key];
            }
        }
        return array_merge($data, (array)$extra);
    }

    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function fromString($string)
    {
        if (is_object($this->_extra)) {
            $this->_extra->fromString($string);
        }
    }
    /**
    * Returns the object as a string
    *
    * @return string
    */
    public function toString()
    {
        if (is_object($this->_extra)) {
            $extra = $this->_extra->toString();
        }
        return $string.$extra;
    }

}
?>
