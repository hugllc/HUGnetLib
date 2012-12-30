<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is our namespace */
namespace HUGnet\devices;

/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/ControlChan.php";


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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class ControlChannels
{
    /** @var Channels objects are stored here */
    private $_channels = array();
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
        \HUGnet\System::exception(
            get_class($this)." needs to be passed a system object",
            "InvalidArgument",
            !is_object($system)
        );
        \HUGnet\System::exception(
            get_class($this)." needs to be passed a device object",
            "InvalidArgument",
            !is_object($device)
        );
        $this->_system = &$system;
        $this->_device = &$device;
        $outputs = (int)$this->_device->get("OutputTables");
        $chans = array();
        for ($i = 0; $i < $outputs; $i++) {
            $chans = array_merge(
                $chans, $this->_device->output($i)->channels()
            );
        }
        if (!is_string($channels) && !is_array($channels)) {
            $channels = $this->_device->get("controlChannels");
        }
        if (is_string($channels)) {
            $channels = json_decode($channels, true);
        }
        foreach (array_keys($chans) as $chan) {
            $chans[$chan]["label"] = $chans[$chan]["label"]." $chan";
            $this->_channels[$chan] = \HUGnet\devices\ControlChan::factory(
                $this->_device,
                $chans[$chan],
                $channels[$chan]
            );
        }
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
    public static function &factory(&$system, &$device, $channels = null)
    {
        $obj = new ControlChannels($system, $device, $channels);
        return $obj;
    }
    /**
    * Throws an exception
    *
    * @param int $chan The data channel to get
    *
    * @return null
    */
    public function controlChannel($chan)
    {
        $chan = (int)$chan;
        if (is_object($this->_channels[$chan])) {
            return $this->_channels[$chan];
        }
        return \HUGnet\devices\ControlChan::factory(
            $this->_device,
            array(),
            array()
        );
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
        foreach (array_keys($this->_channels) as $key) {
            $ret[$key] = $this->controlChannel($key)->toArray($default);
            if ($default) {
                $ret[$key]["channel"] = $key;
            }
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
        return $this->_device->set("controlChannels", json_encode($ret));

    }
    /**
    * Returns an array to select the data channel
    *
    * @param array $ret The base array to start with
    *
    * @return array of id -> name pairs
    */
    public function select($ret = array())
    {
        $ret = (array)$ret;
        foreach (array_keys($this->_channels) as $chan) {
            $ret[$chan] = $this->_channels[$chan]->get("label");
        }
        return $ret;
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
