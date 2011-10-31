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
    /** This is refresh count for the routes */
    const ROUTE_TIME_TO_LIVE = 2;
    /** This is where we store our sockets */
    private $_sockets = array();
    /** Write buffer */
    private $_write = array();
    /** Read buffer */
    private $_read = array();
    /** This is where we store our config */
    private $_config = array();

    /**
    * Sets our configuration
    *
    * @param array $config The configuration to use
    */
    private function __construct($config)
    {
        $this->_config = $config;
        include_once dirname(__FILE__)."/Packet.php";
    }
    /**
    * Creates the object
    *
    * @param array $config The configuration to use
    *
    * @return null
    */
    public function &factory($config = array())
    {
        return new Network((array)$config);
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
    private function &_socket($socket = "default")
    {
        $this->_connect($socket);
        \HUGnet\System::exception(
            "No connection available on socket ".$socket,
            101,
            !is_object($this->_sockets[$socket])
        );
        return $this->_sockets[$socket];
    }
    /**
    * Sends out a packet
    *
    * @param object &$pkt The packet to send out
    *
    * @return bool true on success, false on failure
    */
    public function send(&$pkt)
    {
        foreach ($this->_getRoute($pkt) as $key) {
            $this->_write[$key] .= (string)$pkt;
        }
        $this->_write();
    }
    /**
    * Sends out a packet
    *
    * @param object &$pkt  The packet to send out
    * @param string $iface The interface to set the route to
    *
    * @return bool true on success, false on failure
    */
    private function _setRoute(&$pkt, $iface)
    {
        if (is_object($pkt)) {
            $this->_routes[$pkt->from()]["socket"] = $iface;
            $this->_routes[$pkt->from()]["ttl"] = self::ROUTE_TIME_TO_LIVE;
        }
    }
    /**
    * Sends out a packet
    *
    * @param object &$pkt The packet to send out
    *
    * @return bool true on success, false on failure
    */
    private function _getRoute(&$pkt)
    {
        if (is_object($pkt) && ($pkt->type() != "FINDPING")) {
            if ($this->_routes[$pkt->to()]["ttl"]-- > 0) {
                // This route still has time to live.  Return it.
                return array($this->_routes[$pkt->to()]["socket"]);
            } else {
                // Remove the route, it is old
                unset($this->_routes[$pkt->to()]);
            }
        }
        // No routes or bad route found.  Return all routes.
        return $this->_ifaces();
    }
    /**
    * Returns the interfaces that we are waiting for
    *
    * @return bool true on success, false on failure
    */
    private function _ifaces()
    {
        if (!is_array($this->_config["ifaces"])) {
            $this->_config["ifaces"] = array();
            foreach (array_keys($this->_config) as $key) {
                if (is_object($this->_config[$key])
                    ||  isset($this->_config[$key]["driver"])
                ) {
                    $this->_config["ifaces"][$key] = $key;
                }
            }
            if (empty($this->_config["ifaces"])) {
                $this->_config["ifaces"]["default"] = "default";
            }
        }
        return $this->_config["ifaces"];
    }
    /**
    * Sends out buffers
    *
    * The write routine doesn't always write all of the characters given to it.  It
    * might encounter a full buffer or other things like that.  That is why it only
    * remove the number of characters that it was told were actually written.
    *
    * @return null
    */
    private function _write()
    {
        foreach ($this->_ifaces() as $key) {
            if (strlen($this->_write[$key]) > 0) {
                $this->_connect($key);
                $chars = $this->_socket($key)->write($this->_write[$key]);
                $this->_write[$key] = (string)substr(
                    $this->_write[$key], ($chars*2)
                );
            }
        }
    }
    /**
    * reads in buffers
    *
    * @return null
    */
    private function _read()
    {
        // Check to see if there is anything to receive
        foreach ($this->_ifaces() as $key) {
            $this->_read[$key] .= $this->_socket($key)->read();
        }
    }
    /**
    * Waits for a reply packet for the packet given
    *
    * @return null on failure, Packet container on success
    */
    public function &receive()
    {
        $this->_write();
        $this->_read();
        // Check for packets
        foreach ($this->_ifaces() as $key) {
            $pkt = Packet::factory($this->_read[$key]);
            if ($pkt->isValid()) {
                // This sets the buffer to the left over characters
                $this->_read[$key] = $pkt->extra();
                $this->_setRoute($pkt, $key);
                return $pkt;
            }
        }
        return null;
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
        $this->_config[$socket]["name"] = $socket;
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
        $class = $this->_config[$socket]["driver"];
        @include_once dirname(__FILE__)."/".$class.".php";
        $class = __NAMESPACE__."\\physical\\".$class;
        if (class_exists($class)) {
            $this->_sockets[$socket] = $class::factory(
                $this->_config[$socket]
            );
            return;
        }
        // Last resort include SocketNull
        include_once dirname(__FILE__)."/SocketNull.php";
        $this->_sockets[$socket] = physical\SocketNull::factory(
            $socket, $this->_config[$socket]
        );
    }

}
?>
