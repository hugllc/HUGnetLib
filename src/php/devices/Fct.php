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
namespace HUGnet\devices;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/Container.php";
/** This is our base class */
require_once dirname(__FILE__)."/../interfaces/WebAPI.php";

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
 * @since      0.14.3
 */
class Fct extends \HUGnet\base\Container
    implements \HUGnet\interfaces\WebAPI
{
    /** @var array This is the default values for the data */
    protected $default = array(
        "id" => null,
        "driver" => "",
        "params" => "",
        "tableEntry" => "",
    );
    /**
    * This is the cache for the drivers.
    */
    private $_driverCache = array();
    /**
    * This is the device we rode in on
    */
    private $_device;
    /**
    * This is the device we rode in on
    */
    private $_driverTable = null;
    /**
    * This is the device we rode in on
    */
    private $_new = false;

    /**
    * This is the destructor
    */
    public function __destruct()
    {
        foreach (array_keys((array)$this->_driverCache) as $key) {
            unset($this->_driverCache[$key]);
        }
        unset($this->_device);
        parent::__destruct();
    }
    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param object &$device The device object to use
    *
    * @return null
    */
    public static function &factory(
        &$system, $data=null, &$device = null
    ) {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a device object",
            !is_object($device)
        );
        $object = new Fct($system, $data);
        $object->_device = &$device;
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
        return $this->_get($field, $this->driver());
    }
    /**
    * Gets a value
    *
    * @param string $field  the field to get
    * @param object $driver The driver to use
    *
    * @return null
    */
    private function _get($field, $driver)
    {
        $ret = $driver->get($field);
        if (is_null($ret)) {
            $ret = parent::get($field);
        }
        return $ret;
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
        $return = (array)parent::toArray($default);
        if ($default) {
            $driver = $this->driver()->toArray();
            $return = array_merge($driver, $return);
        }
        $params = json_decode($return["params"], true);
        if (empty($return["driver"])) {
            $return["driver"] = implode(
                "", array_slice(explode('\\', get_class($this->driver())), -1)
            );
        }
        $return["params"] = (array)$params;
        if (!is_array($return["tableEntry"])) {
            $return["tableEntry"] = (array)json_decode($return["tableEntry"], true);
        }
        return (array)$return;
    }
    /**
    * This creates the driver
    *
    * @param string $driver The driver to use.  Leave blank for automatic.
    *
    * @return object
    */
    protected function &driver($driver = null)
    {
        $driver = (string)$driver;
        $file = dirname(__FILE__)."/../devices/functions/Driver.php";
        if (file_exists($file)) {
            include_once $file;
        }
        $class = "\\HUGnet\\devices\\functions\\Driver";
        if (empty($driver)) {
            $driver = (string)$this->data["driver"];
        }
        if (!is_object($this->_driverCache[$driver])) {
            $this->_driverCache[$driver] = $class::factory(
                $driver, $this
            );
        }
        return $this->_driverCache[$driver];
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function device()
    {
        return $this->_device;
    }
    /**
    * returns a history object for this device
    *
    * @param object $args  The argument object
    * @param array  $extra Extra data from the
    *
    * @return string
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function webAPI($args, $extra)
    {
        $action = trim(strtolower($args->get("action")));
        $ret = null;
        if ($action === "put") {
            $ret = $this->_put($args);
        } else if ($action === "settable") {
            $ret = $this->_settable($args);
        }
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _settable($args)
    {
        $data = (array)$args->get("data");
        $ret = $this->setEntry((int)$data["id"]);
        if ($ret) {
            $this->store();
            return "regen";
        }
        return -1;
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _put($args)
    {
        $data = (array)$args->get("data");
        $entry = $this->driver()->entry($data["tableEntry"]);
        $data["tableEntry"] = is_object($entry) ? $entry->toArray() : array();
        $ret = $this->change($data);
        if ($ret) {
            $this->device()->setParam("LastModified", $this->system()->now());
            $this->device()->store();
            return "regen";
        }
        return -1;
    }
}


?>
