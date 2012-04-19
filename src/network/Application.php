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
 */
final class Application
{
    /** This is our network */
    private $_transport;
    /** This is our system */
    private $_system;
    /** This is our device */
    private $_device;
    /** These are the monitor callbacks */
    private $_monitor = array();
    /** These are the unsolicited callbacks */
    private $_unsolicited = array();
    /** These are the packets going out */
    private $_queue = array();
    /** This is our configuration */
    private $_config = array();
    /** This is our default configuration */
    private $_defaultConfig = array(
        "quiet" => false,
        "block" => false,
        "from"  => null,
    );

    /**
    * Sets our configuration
    *
    * @param object &$transport The network transport to use
    * @param object &$system    The system object to use
    * @param array  $config     The configuration to use
    */
    private function __construct(&$transport, &$system, $config)
    {
        $this->_config = array_merge($this->_defaultConfig, $config);
        $this->_system = &$system;
        $this->_transport =& $transport;
        if (empty($this->_config["from"])) {
            $this->_config["from"] = sprintf("%06X", mt_rand(0xFD0000, 0xFDFFFF));
        }

    }
    /**
    * Creates the object
    *
    * @param object &$transport The network transport to use
    * @param object &$system    The system object to use
    * @param array  $config     The configuration to use
    *
    * @return null
    */
    public function &factory(&$transport, &$system, $config = array())
    {
        $obj = new Application($transport, $system, (array)$config);
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
        if (!is_object($this->_device)) {
            include_once dirname(__FILE__)."/Device.php";
            $this->_device = &Device::factory($this, $this->_system, (array)$config);
        }
        return $this->_device;
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
        unset($this->_system);
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
    public function unsolicited($callback, $DeviceID = '000000')
    {
        $return = is_callable($callback);
        if ($return) {
            $this->_unsolicited[$DeviceID][] = $callback;
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
            include_once dirname(__FILE__)."/TransportPacket.php";
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
        if (!is_null($packet) && is_object($packet)) {
            /* Monitor is first because unsolicited might print a reply packet */
            /* If this happens they come out backwards if unsolicited is first */
            $this->_monitor($packet);
            foreach ((array)$this->_unsolicited[$packet->To()] as $callback) {
                if (is_callable($callback)) {
                    call_user_func($callback, $packet);
                }
            }
        }
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
        $qid = uniqid();
        $this->_queue[$qid]["callback"] = $callback;
        $this->_queue[$qid]["config"] = (array)$config;
        if (!is_callable($callback)) {
            $this->_queue[$qid]["config"]["block"] = true;
        }
        if (is_array($packet)) {
            $packet = array_change_key_case($packet);
            if (!isset($packet["command"])) {
                $this->_queue[$qid]["queue"] = $packet;
            } else {
                $this->_queue[$qid]["queue"] = array($packet);
            }
        } else {
            $this->_queue[$qid]["queue"] = array($packet);
        }
        return $this->_send($qid);
    }
    /**
    * Sets callbacks for incoming packets
    *
    * @param mixed $qid The id of the system to send
    *
    * @return bool true on success, false of failure
    */
    private function _send($qid)
    {
        $return = false;
        $packet = array_shift($this->_queue[$qid]["queue"]);
        if (empty($packet)) {
            // No packets left.
            unset($this->_queue[$qid]);
            return false;
        }
        $packet = Packet::factory($packet);
        if ($packet->type() !== "POWERUP") {
            $packet->from($this->_from());
        }
        $token = $this->_transport->send(
            $packet,
            $this->_queue[$qid]["config"]
        );
        if (!is_bool($token) && (is_string($token) || is_numeric($token))) {
            $this->_monitor($packet);
            if ($this->_config["block"] || $this->_queue[$qid]["config"]["block"]) {
                $return = &$packet;
                $reply = $this->_wait($token);
                if (is_object($reply)) {
                    $return->Reply($reply->Data());
                }
            } else {
                $this->_queue[$qid]["token"] = $token;
                $this->_queue[$qid]["packet"] = &$packet;
                $return = true;
            }
        }
        return $return;
    }
    /**
    * Calls the callbacks with this packet
    *
    * @param string $qid     The token attached to that packet
    * @param mixed  &$packet The packet to send out
    *
    * @return null
    */
    private function _receive($qid, &$packet)
    {
        $callback = $this->_queue[$qid]["callback"];
        if (is_callable($callback)) {
            if (is_object($packet)) {
                $this->_queue[$qid]["packet"]->reply($packet->data());
            }
            if (call_user_func($callback, $this->_queue[$qid]["packet"])) {
                // Send the next one out
                $this->_send($qid);
            } else {
                unset($this->_queue[$qid]);
            }
        }
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
        if (is_object($packet)) {
            foreach ($this->_monitor as $callback) {
                if (is_callable($callback)) {
                    call_user_func($callback, $packet);
                }
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
        $this->_main();
        foreach ($this->_queue as $qid => $pkt) {
            \HUGnet\System::loopcheck();
            $return = $this->_transport->receive($pkt["token"]);
            if (!is_null($return)) {
                $this->_receive($qid, $return);
            }
        }
    }
    /**
    * Waits for a certian packet to come in.
    *
    * @param string $token The token to wait for
    *
    * @return null
    */
    private function _wait($token)
    {
        while (is_null($ret = $this->_transport->receive($token))) {
            $this->_main();
        }
        return $ret;
    }
    /**
    * This is the stuff that must get done no matter how we are looping
    *
    * @return null
    */
    private function _main()
    {
        \HUGnet\System::loopcheck();
        // Continue to do the unsolicited stuff
        $this->_unsolicited($this->_transport->unsolicited());
    }
    /**
    * This is the stuff that must get done no matter how we are looping
    *
    * @return null
    */
    private function _from()
    {
        if (is_object($this->_device)) {
            return $this->_device->getID();
        }
        return $this->_config["from"];
    }
}
?>
