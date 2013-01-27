<?php
/**
 * This file howses the socket class
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
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
    /** Read buffer */
    private $_local = "lo";
    /** Route Buffer */
    private $_routes = array();
    /** Route Buffer */
    private $_system;
    /** This is where we store our config */
    private $_config = array();
    /** This is where we store our config */
    private $_defaultConfig = array(
        "forward" => false,
    );

    /**
    * Sets our configuration
    *
    * @param object &$system The system object to use
    * @param array  $config  The configuration to use
    */
    private function __construct(&$system, $config)
    {
        $this->_system = &$system;
        $this->_config = array_merge($this->_defaultConfig, $config);
        include_once dirname(__FILE__)."/packets/Packet.php";
        foreach (array_keys($this->_ifaces()) as $key) {
            $this->_read[$key] = "";
            $this->_write[$key] = "";
        }
    }
    /**
    * Creates the object
    *
    * @param object &$system The system object to use
    * @param array  $config  The configuration to use
    *
    * @return null
    */
    static public function &factory(&$system, $config = array())
    {
        $obj = new Network($system, (array)$config);
        return $obj;
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
            "Runtime",
            !isset($this->_sockets[$socket]) || !is_object($this->_sockets[$socket])
        );
        return $this->_sockets[$socket];
    }
    /**
    * Sends out a packet
    *
    * @param object $pkt The packet to send out
    *
    * @return bool true on success, false on failure
    */
    public function send($pkt)
    {
        $this->_send($pkt, $this->_getRoute($pkt));
        $this->_write();
    }
    /**
    * Sends out a packet
    *
    * @param object $pkt    The packet to send out
    * @param string $routes Routes to send the packet out
    *
    * @return bool true on success, false on failure
    */
    private function _send($pkt, $routes)
    {
        foreach ((array)$routes as $key) {
            $this->_write[$key] .= (string)$pkt;
        }

    }
    /**
    * Sends out a packet
    *
    * @param object $pkt The packet to send out
    *
    * @return null
    */
    private function _forward($pkt)
    {
        $ifaces = $this->_ifaces();
        if ((count($ifaces) > 1) && $this->_config["forward"]) {
            $fto = array_diff($ifaces, array($pkt->iface(), $this->_local));
            $this->_system->out(
                "Forwarding from ".$pkt->iface()." to ".implode(", ", $fto)
                ." of (".implode(", ", $ifaces).")",
                3
            );
            $this->_send($pkt, $fto);
        }

    }
    /**
    * Sends out a packet
    *
    * @param object &$pkt The packet to send out
    *
    * @return bool true on success, false on failure
    */
    private function _setRoute(&$pkt)
    {
        if (is_object($pkt)) {
            $this->_routes[$pkt->from()]["socket"] = $pkt->iface();
            $this->_routes[$pkt->from()]["ttl"] = self::ROUTE_TIME_TO_LIVE;
        }
    }
    /**
    * Sends out a packet
    *
    * @param object &$pkt The packet to send out
    *
    * @return array of interfaces
    */
    private function _getRoute(&$pkt)
    {
        if (is_object($pkt) && ($pkt->type() != "FINDPING")
            && isset($this->_routes[$pkt->to()])
        ) {
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
    * @return array of interfaces
    */
    private function _ifaces()
    {
        if (!isset($this->_config["ifaces"])
            || !is_array($this->_config["ifaces"])
        ) {
            $this->_config["ifaces"] = array();
            if ($this->_config["noLocal"] != true) {
                $this->_config[$this->_local] = array(
                    "driver" => "Local",
                    "name" => $this->_local,
                );
            }
            foreach (array_keys($this->_config) as $key) {
                if (is_object($this->_config[$key])
                    && (strtolower($key) !== "device")
                    ||  (is_array($this->_config[$key])
                    && isset($this->_config[$key]["driver"]))
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
            \HUGnet\System::loopcheck();
            if (isset($this->_write[$key]) && (strlen($this->_write[$key]) > 0)) {
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
            \HUGnet\System::loopcheck();
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
            \HUGnet\System::loopcheck();
            if (isset($this->_read[$key]) && (strlen($this->_read[$key]) > 0)) {
                $pkt = packets\Packet::factory($this->_read[$key]);
                if ($pkt->isValid() === true) {
                    // Set the interface
                    $pkt->iface($key);
                    // This sets the buffer to the left over characters
                    $this->_read[$key] = $pkt->extra();
                    $this->_setRoute($pkt);
                    $this->_forward($pkt);
                    return $pkt;
                } else if ($pkt->isValid() === false) {
                    // Bad packet, remove it from the buffer
                    $this->_read[$key] = $pkt->extra();
                }
            }
        }
        $ret = null;
        return $ret;
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
        if (isset($this->_sockets[$socket])
            && is_object($this->_sockets[$socket])
        ) {
            return;
        }
        if (isset($this->_config[$socket])
            && is_object($this->_config[$socket])
        ) {
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
        if (isset($this->_config[$socket])
            && isset($this->_config[$socket]["driver"])
        ) {

            $this->_system->out("Opening socket ".$socket, 6);
            $class = $this->_config[$socket]["driver"];
            @include_once dirname(__FILE__)."/physical/".$class.".php";
            $class = __NAMESPACE__."\\physical\\".$class;

            if (class_exists($class)) {
                $this->_system->out("Using class ".$class, 6);
                $this->_sockets[$socket] = $class::factory(
                    $this->_system, $this->_config[$socket]
                );
                return;
            }
            // Last resort include SocketNull
            include_once dirname(__FILE__)."/physical/SocketNull.php";
            $this->_sockets[$socket] = physical\SocketNull::factory(
                $this->_system, $this->_config[$socket]
            );
        }
    }

}
?>
