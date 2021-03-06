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
        $this->_system = &$system;
        $this->_device = &$device;
        $this->_setChannels($channels);
    }
    /**
    * This removes all of the object cache
    *
    * @return null
    */
    public function __destruct()
    {
        $this->_clearCache();
        unset($this->_system);
        unset($this->_device);
    }
    /**
    * This removes all of the object cache
    *
    * @return null
    */
    private function _clearCache()
    {
        foreach (array_keys((array)$this->_objCache) as $key) {
            unset($this->_objCache[$key]);
        }
    }
    /**
    * This function normalizes the channels, so that they are always zero based,
    * and never skip any indexes.
    *
    * @param mixed $channels The channels
    *
    * @return null
    */
    private function _setChannels($channels)
    {
        $chans = array();
        $index = 0;
        foreach ((array)$channels as $chan) {
            if (is_array($chan)) {
                $chans[$index]       = $chan;
                $chans[$index]["id"] = $index;
                $index++;
            }
        }
        $this->_channels = (array)$chans;
    }
    /**
    * This function creates the system.
    *
    * @param object &$system  The system oject
    * @param object &$device  The device object
    * @param mixed  $channels The channels
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
        if (!is_array($this->_channels[$chan])) {
            return \HUGnet\devices\Fct::factory(
                $this->_system,
                array("id" => $chan),
                $this->_device,
                $this
            );
        }
        if (!is_object($this->_objCache[$chan])) {
            $this->_channels[$chan]["id"] = $chan;
        
            $this->_objCache[$chan] = \HUGnet\devices\Fct::factory(
                $this->_system,
                $this->_channels[$chan],
                $this->_device,
                $this
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
        $this->_device->fcts($ret, true);
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param bool $pretend Whether to pretend to actually do this, to see what
    *                      it would look like.
    *                      
    * @return null
    */
    public function apply($pretend = true)
    {
        $this->_system->out(
            "Applying functions for Device "
            .$this->_device->get("DeviceID"), 
            7
        );
        if ($pretend) {
            $this->_system->out(
                "************************** Just Pretending ***********************",
                7
            );
            $data = $this->_device->toArray(false);
            $data["group"] = "tmp";
            $dev = $this->_system->device($data);
            $ret = $dev->fcts($this->toArray(false), true)->execute();
            $dev->delete();
            return $ret;
        } else {
            return $this->execute();
        }
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @return null
    */
    protected function execute()
    {
        // This deletes all of the IOP
        $this->_device->deleteIOP();
        // The raw setup will pollute the inputs.  It needs to be cleared.
        $this->_device->set("RawSetup", "");
        // Put the device back in the database.
        $this->_device->store();
        // Execute all of the functions
        foreach (array_keys($this->_channels) as $key) {
            $this->_system->out("Executing function $key", 7);
            $this->fct($key)->execute();
        }

        $this->_device->setParam("fctsApplied", $this->toArray(false));
        $this->_device->setParam("fctsAppliedDate", $this->_system->now());
        $this->_device->dataChannels()->store();
        
        $this->_device->store();
        $return = $this->_device->fixture()->toArray(true);
        $return["dataChannels"] = $this->_device->dataChannels()->toArray(true);
        $return["controlChannels"] = $this->_device->controlChannels()->toArray(
            true
        );
        return $return;
        
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
