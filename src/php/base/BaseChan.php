<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\base;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.13.0
 */
abstract class BaseChan
{
    /**
    * This is the device we rode in on
    */
    private $_device;
    /** @var array The configuration that we are going to use */
    protected $setable = array();
    /**
    * This is the device we rode in on
    */
    private $_data = array();

    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object $device The device object to use
    * @param array  $driver The driver information
    *
    * @return null
    */
    protected function __construct($device, $driver)
    {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a device object",
            !is_object($device)
        );
        $this->_device = &$device;
        $this->_data = (array)$driver;
    }

    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_device);
    }
    /**
    * This function creates the system.
    *
    * @param object $device The device object to use
    * @param array  $driver The driver information
    * @param mixed  $data   (array) data info array
    *
    * @return null
    */
    public static function &factory($device, $driver, $data)
    {
        $class = get_called_class();
        $object = new $class($device, $driver);
        $object->fromArray($data);
        return $object;
    }
    /**
    * Gets a value
    *
    * @param string $field the field to get
    *
    * @return null
    */
    public function get($field)
    {
        return $this->_data[$field];
    }
    /**
    * Gets a value
    *
    * @param string $field the field to get
    * @param mixed  $value The value to set it to
    *
    * @return null
    */
    protected function set($field, $value)
    {
        if (in_array($field, $this->setable)) {
            $this->_data[$field] = $value;
        }
        return $this->get($field);
    }
    /**
    * Returns the table as an array
    *
    * @param bool $default Whether to include the default params or not
    *
    * @return array
    */
    public function toArray($default = true)
    {
        $data = (array)$this->_data;
        if (!$default) {
            $data = array_intersect_key($data, array_flip($this->setable));
        }
        return $data;
    }
    /**
    * Returns the table as an array
    *
    * @param array $array The array to use
    *
    * @return array
    */
    public function fromArray($array)
    {
        foreach ((array)$array as $field => $value) {
            $this->set($field, $value);
        }
        $this->check();
    }
    /**
    * Returns the table as an array
    *
    * @return array
    */
    public function device()
    {
        return $this->_device;
    }
    /**
    * Checks for consistancy
    *
    * @return object
    */
    protected function check()
    {
    }
    
}


?>
