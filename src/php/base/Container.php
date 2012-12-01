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
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
namespace HUGnet\base;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class Container
{
    /** @var int The configuration */
    protected $myConfig = null;
    /** @var array This is the default values for the data */
    protected $default = array();
    /** @var array This is where the data is stored */
    protected $data = array();
    /** @var array This is where the fixed data is stored (not writable outside) */
    protected $fixed = array();
    /** @var object The system object*/
    private $_system = null;
    /**
    * This is the constructor
    *
    * @param object &$system This is the system object
    * @param mixed  $data    This is an array or string to create the object from
    */
    protected function __construct(&$system, $data="")
    {
        \HUGnet\System::exception(
            get_class($this)." needs to be passed a system object",
            "InvalidArgument",
            !is_object($system)
        );
        $this->_system = $system;
        $this->clearData();
        $this->fromAny($data);
    }
    /**
    * Returns the system object
    *
    * @return object The system object
    */
    protected function &system()
    {
        return $this->_system;
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
            $fct = "set".ucfirst($name);
            if (method_exists($this, $fct)) {
                $this->$fct($value);
            } else {
                $this->data[$name] = $value;
            }
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
        } else if (array_key_exists($name, $this->fixed)) {
            return $this->fixed[$name];
        }
        return null;
    }

    /**
    * Converts the object to a string
    *
    * @return mixed The value of the attribute
    */
    public function __toString()
    {
        return $this->toString();
    }
    /**
    * Sets the extra attributes field
    *
    * @return mixed The value of the attribute
    */
    public function getProperties()
    {
        return array_keys((array)$this->default);
    }

    /**
    * Sets the extra attributes field
    *
    * @return mixed The value of the attribute
    */
    public function clearData()
    {
        foreach ($this->getProperties() as $name) {
            $this->setDefault($name);
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
        $this->set($name, $this->default[$name]);
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($array)
    {
        foreach ($this->getProperties() as $attrib) {
            if (isset($array[$attrib])) {
                $this->set($attrib, $array[$attrib]);
            }
        }
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
            if (($this->get($key) !== $this->default[$key]) || $default) {
                $value = $this->toArrayIterator(
                    $this->get($key),
                    $this->default[$key],
                    $default
                );
                if (($value !== $this->default[$key]) || $default) {
                    $data[$key] = $value;
                }
            }
        }
        return (array)$data;
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param mixed $array      The array to traverse
    * @param mixed $default    Pointer to the defaults for this item
    * @param bool  $retDefault Return items set to their default?
    *
    * @return null
    */
    protected function toArrayIterator($array, $default, $retDefault = true)
    {
        if (is_object($array) && method_exists($array, "toArray")) {
            return $array->toArray($retDefault);
        } else if (is_array($array)) {
            $default = (array)$default;
            $ret = array();
            foreach (array_keys($array) as $key) {
                if (($array[$key] !== $default[$key]) || $retDefault) {
                    $value = $this->toArrayIterator(
                        $array[$key],
                        $default[$key],
                        $retDefault
                    );
                    if (($value !== $default[$key]) || $retDefault) {
                        $ret[$key] = $value;
                    }
                }
            }
            return $ret;
        } else {
            return $array;
        }
    }
    /**
    * Creates the object from a string or array
    *
    * @param mixed $data This is whatever you want to give the class
    *
    * @return null
    */
    public function fromAny($data)
    {
        if (is_string($data)) {
            if ($this->fromString($data) === false) {
                @$this->fromZip($data);
            }
        } else if (is_array($data)) {
            $this->fromArray($data);
        }
    }
    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return boolean
    */
    public function fromString($string)
    {
        if (!empty($string)) {
            $stuff = self::fromStringDecode($string);
        }
        if ($stuff) {
            $this->fromArray($stuff);
        }
        return (bool)$stuff;
    }
    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return array
    */
    static protected function fromStringDecode($string)
    {
        return unserialize(base64_decode($string));
    }
    /**
    * Creates the object from a string
    *
    * @param array $array This is the array to encode
    *
    * @return string
    */
    static protected function toStringEncode($array)
    {
        return base64_encode(serialize($array));
    }
    /**
    * Returns the object as a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return string
    */
    public function toString($default = true)
    {
        return self::toStringEncode($this->toArray($default));
    }
    /**
    * Creates the object from a string
    *
    * @param string $zip This is the raw string for the device
    *
    * @return null
    */
    public function fromZip($zip)
    {
        if (!empty($zip)) {
            $stuff = unserialize(gzuncompress($zip));
        }
        if ($stuff) {
            $this->fromArray($stuff);
        }
        return (bool) $stuff;
    }
    /**
    * Returns the object as a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return string
    */
    public function toZip($default = true)
    {
        return gzcompress(serialize($this->toArray($default)));
    }
    /**
    * Returns the md5 hash of the object
    *
    * @return string
    */
    public function hash()
    {
        return md5((string)$this);
    }
    /**
    * returns true if the container is empty.  False otherwise
    *
    * @return bool Whether this container is empty or not
    */
    public function isEmpty()
    {
        $ret = true;
        foreach (array_keys((array)$this->default) as $key) {
            if ($this->default[$key] !== $this->data[$key]) {
                $ret = false;
                break;
            }
        }
        return (bool)($ret || empty($this->data));
    }

}
?>
