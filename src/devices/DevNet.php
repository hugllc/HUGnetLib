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
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * Networking for devices.
 *
 * This class will do all of the networking for devices.  It will poll, get configs,
 * update software, and anything else related to talking to devices.
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
class DevNet
{
    /**
    * This is the system object
    */
    private $_network = null;
    /**
    * This is the cache object
    */
    private $_table = null;
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$network The network application object
    * @param string &$table   The table object
    *
    * @return null
    */
    private function __construct(&$network, &$table)
    {
        \HUGnet\System::exception(
            get_class($this)." needs to be passed a network object",
            "InvalidArgument",
            !is_object($network)
        );
        \HUGnet\System::exception(
            get_class($this)." needs to be passed a table object",
            "InvalidArgument",
            !is_object($table)
        );
        $this->_network = &$network;
        $this->_table   = &$table;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
    }
    /**
    * This function creates the system.
    *
    * @param mixed  &$network (object)The system object to use
    * @param string &$table   (object)The table to use
    *
    * @return null
    */
    public static function &factory(&$network, &$table)
    {
        $object = new DevNet($network, $table);
        return $object;
    }
    /**
    * Polls the device in question
    *
    * @param string $callback The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config   The network config to use for the packet
    *
    * @return success or failure of the packet sending
    */
    public function poll($callback = null, $config = array())
    {
        if (!is_callable($callback)) {
            $config["block"] = true;
        }
        $ret = $this->_network->send(
            array(
                "To" => $this->_table->get("id"),
                "Command" => "55",
            ),
            $callback,
            $config
        );
        return $ret;
    }
    /**
    * Polls the device in question
    *
    * @param string $callback The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config   The network config to use for the packet
    *
    * @return success or failure of the packet sending
    */
    public function config($callback = null, $config = array())
    {
        if (!is_callable($callback)) {
            $config["block"] = true;
        }
        $ret = $this->_network->send(
            array(
                "To" => $this->_table->get("id"),
                "Command" => "5C",
            ),
            $callback,
            $config
        );
        return $ret;
    }

}


?>
