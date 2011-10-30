<?php
/**
 * This file howses the socket class
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/**
 * This code routes packets to their correct destinations.
 *
 * This is the router class, essentially.  It will take packets and figure out
 * which network interface to send them out.  This implements the Network layer
 * of the OSI model.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
final class Network
{
    /** This is where we store our sockets */
    private $_sockets = array();
    /** This is where we store our config */
    private $_config = array();

    /**
    * Sets our configuration
    *
    * @param object &$system The system object to use
    * @param array  $config  The configuration to use
    */
    private function __construct(&$system, $config)
    {
        $this->_system =& $system;
        $this->_config = $config;
        include_once dirname(__FILE__)."/../system/Packet.php";
    }
    /**
    * Creates the object
    *
    * @param object &$system The system object to use
    * @param array  $config  The configuration to use
    *
    * @return null
    */
    public function &factory(&$system, $config = array())
    {
        return new Network($system, (array)$config);
    }

    /**
    * Disconnects from the database
    *
    */
    public function __destruct()
    {
        foreach (array_keys($this->_sockets) as $key) {
            unset($this->_sockets[$key]);
        }
    }

    /**
    * Checks to see if we are connected to a database
    *
    * @param string $socket The group to check
    *
    * @return Network object
    */
    public function &socket($socket = "default")
    {
        $this->_connect($socket);
        System::exception(
            "No connection available on socket ".$socket,
            101,
            !is_object($this->_sockets[$socket])
        );
        return $this->_sockets[$socket];
    }
    /**
    * Sends out a packet
    *
    * @param object &$pkt   The packet to send out
    * @param string $socket The socket to send it out
    *
    * @return bool true on success, false on failure
    */
    public function send(&$pkt)
    {
        // Send out what we have
        $return = (bool)$this->socket($socket)->write((string)$pkt);
        // Check to see if there is anything to receive
        $this->buffer .= $this->socket($socket)->read();
        // Return
        return $return;
    }
    /**
    * Waits for a reply packet for the packet given
    *
    * @param PacketContainer &$pkt The packet to send out
    *
    * @return bool true on success, false on failure
    */
    public function &receive()
    {
        // Check to see if there is anything to receive
        $this->buffer .= $this->socket($socket)->read();
        $pkt = Packet::factory($this->buffer);
        if ($ret->isValid()) {
            $this->buffer = $ret->extra();
            return $pkt;
        }
        return null;
    }
    /**
    * function to set DeviceID
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setDeviceID($value)
    {
        if (is_int($value)) {
            $value = dechex($value);
        }
        $this->data["DeviceID"] = self::stringSize($value, 6);
    }

    /**
    * Connects to a database group
    *
    * @param string $socket The socket to connect to
    *
    * @return bool True on success, false on failure
    */
    private function _connect($socket)
    {
        if (is_object($this->_sockets[$socket])) {
            return;
        }
        if (is_object($this->_config[$socket])) {
            $this->_sockets[$socket] = &$this->_config[$socket];
            return;
        }
        $this->_findDriver($socket);
    }
    /**
    * Connects to a database group
    *
    * @param string $socket The socket to use
    *
    * @return null
    */
    private function _findDriver($socket)
    {
        $class = Util::findClass(
            $this->_config[$socket]["driver"],
            "network", true
        );
        if (class_exists($class)) {
            $this->_sockets[$socket] = $class::factory(
                $this->_config[$socket]
            );
            return;
        }
        // Last resort include NullSocket
        include_once dirname(__FILE__)."/NullSocket.php";
        $this->_sockets[$socket] = NullSocket::factory(
            $socket, $this->_config[$socket]
        );
    }

}
?>
