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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is our namespace */
namespace HUGnet;

/** This define allows everything else to be included */
if (!defined("_HUGNET")) {
    define("_HUGNET", true);
}
/** This is the system error class.  Everybody needs it */
require_once dirname(__FILE__).'/Error.php';
/** This is the system utility class.  Everybody needs it also */
require_once dirname(__FILE__).'/../util/Util.php';
/** This is the system utility class.  Everybody needs it also */
require_once dirname(__FILE__).'/../util/VPrint.php';


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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Channels
{
    /** @var array The configuration that we are going to use */
    private $_channels = array();
    /** @var array The configuration that we are going to use */
    private $_system = null;

    /**
    * This sets up the basic parts of the object for us when we create it
    *
    * @param object &$system The system oject
    * @param object &$device The device object
    *
    * @return null
    */
    private function __construct(&$system, &$device)
    {
        System::exception(
            get_class($this)." needs to be passed a system object",
            "InvalidArgument",
            !is_object($system)
        );
        System::exception(
            get_class($this)." needs to be passed a device object",
            "InvalidArgument",
            !is_object($device)
        );
        $this->_system = &$system;
        $this->_device = &$device;
        $sensors = $this->_device->get("totalSensors");
        for ($i = 0; $i < $sensors; $i++) {
            $this->_channels = array_merge(
                $this->_channels, $this->_device->sensor($i)->channels()
            );
        }
        $channels = json_decode($this->_device->get("channels"), true);
        foreach (array_keys($this->_channels) as $chan) {
            if (is_array($channels[$chan])) {
                $this->_channels[$chan] = array_merge(
                    $this->_channels[$chan],
                    $channels[$chan]
                );
            }
        }
    }
    /**
    * This function creates the system.
    *
    * @param object &$system The system oject
    * @param object &$device The device object
    *
    * @return null
    */
    public static function &factory(&$system, &$device)
    {
        $obj = new Channels($system, $device);
        return $obj;
    }
    /**
    * Throws an exception
    *
    * @param array $record The record to convert
    *
    * @return null
    */
    public function convert($record)
    {
        return $record;
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

}


?>