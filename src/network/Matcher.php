<?php
/**
 * This file howses the socket class
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\network;
/**
 * This code routes packets to their correct destinations.
 *
 * Config (default):
 *    "quiet"    bool Optional If true it won't throw exceptions (false)
 *    "channels" int  The number of channels to keep open (5)
 *
 * This class keeps track of packets and waits for replies.  It will use the
 * TransportPacket class to keep track of each packet and where it is in its
 * timeouts and retries.  It will keep a number of channels open and should NOT
 * block.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
final class Matcher
{
    /** These are the packets we are sending */
    private $_packets = array();
    /** This is our configuration */
    private $_config = array();
    /** This is our configuration */
    private $_callback = array();
    /** These are the packets we are sending */
    private $_defaultConfig = array(
        "channels" => 10,
        "quiet"    => false,
        "timeout"  => 5,
    );

    /**
    * Sets our configuration
    *
    * @param array $config   The configuration to use
    * @param mixed $callback The callback function when we found a packet
    */
    private function __construct($config, $callback)
    {
        $this->_config  = array_merge($this->_defaultConfig, $config);
        $this->_callback = $callback;
    }
    /**
    * Creates the object
    *
    * @param array $config   The configuration to use
    * @param mixed $callback The callback function when we found a packet
    *
    * @return Matcher Object
    */
    public function &factory($config, $callback)
    {
        return new Matcher((array)$config, $callback);
    }

    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function __destruct()
    {
        // Get rid of any packets
        foreach (array_keys($this->_packets) as $key) {
            unset($this->_packets[$key]);
        }
    }

    /**
    * Deals with incoming packets
    *
    * @param object $pkt The packet to send out
    *
    * @return null
    */
    public function match($pkt)
    {
        if ($pkt->type() == "REPLY") {
            $this->_matchReply($pkt);
        } else {
            $this->_matchOther($pkt);
        }
    }
    /**
    * Deals with incoming packets
    *
    * @return null
    */
    public function main()
    {
        foreach (array_keys($this->_packets) as $index) {
            // Pretend to send the packet
            $send = $this->_packets[$index]->send();
            if ($send === false) {
                // Timeout!
                unset($this->_packets[$index]);
            }
        }
    }
    /**
    * Deals with incoming packets
    *
    * @param object &$pkt The packet to send out
    *
    * @return null
    */
    private function _matchReply(&$pkt)
    {
        $this->main();
        foreach (array_keys($this->_packets) as $index) {
            $reply = &$this->_packets[$index]->reply($pkt);
            if (is_object($reply)) {
                $this->_callback($index);
                unset($this->_packets[$index]);
            }
        }
    }
    /**
    * Deals with incoming packets
    *
    * @param object &$pkt The packet to send out
    *
    * @return null
    */
    private function _matchOther(&$pkt)
    {
        $pid = uniqid();
        $this->_packets[$pid] = TransportPacket::factory(
            array(
                "tries" => 1,
                "find" => false,
                "timeout" => $this->_config["timeout"]
            ),
            $pkt
        );
        $this->_packets[$pid]->send();
    }

    /**
    * Deals with incoming packets
    *
    * @param int $index The packet index to get
    *
    * @return null
    */
    private function _callback($index)
    {
        if (is_callable($this->_callback)) {
            $this->_packets[$index]->packet()->reply(
                $this->_packets[$index]->reply()->data()
            );
            call_user_func(
                $this->_callback,
                $this->_packets[$index]->packet()
            );
        }
        unset($this->_packets[$index]);
    }
}
?>
