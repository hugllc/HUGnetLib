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
 * @subpackage Network
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
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
final class Application
{
    /** This is our network */
    private $_transport;
    /** These are the monitor callbacks */
    private $_monitor = array();
    /** These are the unsolicited callbacks */
    private $_unsolicited = array();
    /** These are the callbacks for the outgoing packets */
    private $_receive = array();
    /** These are the packets going out */
    private $_packet = array();
    /** This is our configuration */
    private $_config = array();
    /** This is our default configuration */
    private $_defaultConfig = array(
        "quiet" => false,
    );

    /**
    * Sets our configuration
    *
    * @param object &$transport The network transport to use
    * @param array  $config     The configuration to use
    */
    private function __construct(&$transport, $config)
    {
        $this->_config = array_merge($this->_defaultConfig, $config);
        $this->_transport =& $transport;
    }
    /**
    * Creates the object
    *
    * @param object &$transport The network transport to use
    * @param array  $config     The configuration to use
    *
    * @return null
    */
    public function &factory(&$transport, $config = array())
    {
        return new Application($transport, (array)$config);
    }

    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function __destruct()
    {
        // Shut down the network
        unset($this->_transport);
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
    public function unsolicited($callback)
    {
        $return = is_callable($callback);
        if ($return) {
            $this->_unsolicited[] = $callback;
        }
        return (bool)$return;
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
        $return = is_callable($callback);
        if ($return) {
            include_once dirname(__FILE__)."/Matcher.php";
            $this->_matcher = Matcher::factory($this->_config, $callback);
            $this->unsolicited(array($this->_matcher, "match"));
        }
        return (bool)$return;
    }
    /**
    * Calls the callbacks with this packet
    *
    * @param mixed &$packet The packet to send out
    *
    * @return null
    */
    private function _unsolicited(&$packet)
    {
        if (!is_null($packet)) {
            foreach ($this->_unsolicited as $callback) {
                if (is_callable($callback)) {
                    call_user_func($callback, $packet);
                }
            }
            $this->_monitor($packet);
        }
    }
    /**
    * Sets callbacks for incoming packets
    *
    * The function should take 1 argument.  That will be the packet.
    *
    * @param mixed &$packet  The packet to send out
    * @param mixed $callback the callback function.
    *
    * @return bool true on success, fales of failure
    */
    public function send(&$packet, $callback = null)
    {
        $return = false;
        $config = array();
        if (!is_callable($callback)) {
            // We we don't get a callback, they don't expect a reply
            $config = array("find" => false, "retries" => 1);
        }
        $token = $this->_transport->send($packet, $config);
        if (!is_bool($token) && (is_string($token) || is_numeric($token))) {
            $this->_receive[$token] = $callback;
            $this->_packet[$token] = &$packet;
            $this->_monitor($packet);
            $return = true;
        }
        return (bool)$return;
    }
    /**
    * Calls the callbacks with this packet
    *
    * @param string $token   The token attached to that packet
    * @param mixed  &$packet The packet to send out
    *
    * @return null
    */
    private function _receive($token, &$packet)
    {
        $callback = $this->_receive[$token];
        if (is_callable($callback)) {
            $this->_packet[$token]->reply($packet->data());
            call_user_func($callback, $this->_packet[$token]);
        }
        unset($this->_receive[$token]);
        unset($this->_packet[$token]);
        $this->_monitor($packet);
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
        $return = is_callable($callback);
        if ($return) {
            $this->_monitor[] = $callback;
        }
        return (bool)$return;
    }
    /**
    * Calls the callbacks with this packet
    *
    * @param mixed $packet The packet to send out
    *
    * @return null
    */
    private function _monitor($packet)
    {
        foreach ($this->_monitor as $callback) {
            if (is_callable($callback)) {
                call_user_func($callback, $packet);
            }
        }
    }

    /**
    * The main routine should be called periodically (once per loop at least)
    *
    * @return null
    */
    public function main()
    {
        pcntl_signal_dispatch();
        foreach (array_keys($this->_receive) as $token) {
            $return = $this->_transport->receive($token);
            if (!is_null($return)) {
                $this->_receive($token, $return);
            }
        }
        $this->_unsolicited($this->_transport->unsolicited());
    }


}
?>
