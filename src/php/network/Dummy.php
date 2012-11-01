<?php
/**
 * This file howses the socket class
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
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\network;
/**
 * This code routes packets to their correct destinations.
 *
 * This is the router class, essentially.  It will take packets and figure out
 * which network interface to send them out.  This implements the Network layer
 * of the OSI model.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Dummy
{
    /** This is our system */
    private $_system;

    /**
    * Sets our configuration
    *
    * @param object &$system The system object to use
    */
    private function __construct(&$system)
    {
        $this->_system = &$system;
        include_once dirname(__FILE__)."/packets/Packet.php";
    }
    /**
    * Creates the object
    *
    * @param object &$system The system object to use
    *
    * @return null
    */
    static public function &factory(&$system)
    {
        $obj = new Dummy($system);
        return $obj;
    }

    /**
    * Creates the object
    *
    * @param array $config The configuration to use
    *
    * @return null
    */
    public function &device($config = array())
    {
        return $this;
    }

    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function __destruct()
    {
        unset($this->_device);
    }
    /**
    * Sets callbacks for incoming packets
    *
    * The function should take 1 argument.  That will be the packet.
    *
    * @param mixed  $callback the callback function.
    * @param string $DeviceID The device ID to take packets from
    *
    * @return bool true on success, fales of failure
    */
    public function unsolicited($callback, $DeviceID = 0)
    {
        return false;
    }
    /**
    * Sets callbacks for incoming packets
    *
    * The function should take 2 arguments.  The reply and the packet.
    *
    * @param mixed $callback the callback function.
    *
    * @return bool true on success, fales of failure
    */
    public function match($callback)
    {
        return false;
    }
    /**
    * Sets callbacks for incoming packets
    *
    * The function should take 1 argument.  That will be the packet.
    *
    * @param mixed $packet   The packet to send out
    * @param mixed $callback The callback function.
    * @param array $config   The configuration to use with the packet
    *
    * @return bool true on success, false of failure
    */
    public function send($packet, $callback = null, $config = array())
    {
        if (is_null($callback) || $config["block"]) {
            return packets\Packet::factory($packet);
        }
        return false;
    }
    /**
    * Sets callbacks for incoming packets
    *
    * The function should take 1 argument.  That will be the packet.
    *
    * @param mixed $callback the callback function.
    *
    * @return bool true on success, fales of failure
    */
    public function monitor($callback)
    {
        return false;
    }

    /**
    * The main routine should be called periodically (once per loop at least)
    *
    * @return null
    */
    public function main()
    {
        /* Do nothing */
    }
    /**
    * Finds a good ID to use
    *
    * This function is from device.  We have it here so that we can return $this
    * for device.  That simplifies this dummy networking quite a lot.
    *
    * @return null
    */
    public function getID()
    {
        return 0;
    }
}
?>
