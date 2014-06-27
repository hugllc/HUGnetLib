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
/** This is our namespace */
namespace HUGnet\devices;

/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/Fct.php";


/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be included
 * to get HUGnetLib functionality.  This class will load everything else it needs,
 * so the user doesn't have to worry about it.
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
class Fcts
{
    /** @var Channels objects are stored here */
    private $_channels = array();
    /** @var Channels objects are stored here */
    private $_objCache = array();
    /** @var System is stored here */
    private $_system = null;
    /** @var Device is stored here */
    private $_device = null;

    /**
    * This sets up the basic parts of the object for us when we create it
    *
    * @param object &$system  The system oject
    * @param object &$device  The device object
    * @param mixed  $channels The channels.  If not provided retrieved from device
    *
    * @return null
    */
    private function __construct(&$system, &$device, $channels)
    {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a device object",
            !is_object($device)
        );
        $this->_system   = &$system;
        $this->_device   = &$device;
        $this->_channels = (array)$channels;
    }
    /**
    * This function creates the system.
    *
    * @param object &$system  The system oject
    * @param object &$device  The device object
    * @param mixed  $channels The channels.  If not provided retrieved from device
    *
    * @return null
    */
    public static function &factory(&$system, &$device, $channels)
    {
        $obj = new Fcts($system, $device, $channels);
        return $obj;
    }
    /**
    * Throws an exception
    *
    * @param int $chan The data channel to get
    *
    * @return null
    */
    public function fct($chan)
    {
        $chan = (int)$chan;
        if (!is_object($this->_objCache[$chan])) {
            $this->_channels[$chan]["id"] = $chan;
        
            $this->_objCache[$chan] = \HUGnet\devices\Fct::factory(
                $this->_system,
                $this->_channels[$chan],
                $this->_device
            );
        }
        return $this->_objCache[$chan];
    }
    /**
    * This function gives us access to the table class
    *
    * @return reference to the system object
    */
    public function &system()
    {
        return $this->_system;
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
        $ret = array();
        foreach (array_keys((array)$this->_channels) as $key) {
            $ret[$key] = $this->fct($key)->toArray($default);
        }
        return $ret;

    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @return null
    */
    public function store()
    {
        $ret = $this->toArray(false);
        return $this->_device->setParam("fcts", json_encode($ret));

    }

    /**
    * Returns the number of channels
    *
    * @return null
    */
    public function count()
    {
        return count($this->_channels);
    }
}


?>
