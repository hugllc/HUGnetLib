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
final class Transport
{
    /** This is the max number of unsolicited packets that can queue up */
    const MAX_UNSOL = 100;
    /** This is our network */
    private $_network = array();
    /** These are the packets we are sending */
    private $_packets = array();
    /** These are the packets we get we weren't expecting */
    private $_unsolicited = array();
    /** This is our configuration */
    private $_config = array();
    /** These are the packets we are sending */
    private $_defaultConfig = array(
        "channels" => 2,
        "quiet"    => false,
    );

    /**
    * Sets our configuration
    *
    * @param object &$network The network object to use
    * @param array  $config   The configuration to use
    */
    private function __construct(&$network, $config)
    {
        $this->_config  = array_merge($this->_defaultConfig, $config);
        $this->_network = &$network;
        // This is our packet container
        include_once dirname(__FILE__)."/TransportPacket.php";
    }
    /**
    * Creates the object
    *
    * @param object &$network The network object to use
    * @param array  $config   The configuration to use
    *
    * @return Transport Object
    */
    static public function &factory(&$network, $config = array())
    {
        $obj = new Transport($network, (array)$config);
        return $obj;
    }

    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function __destruct()
    {
        // Shut down the network
        unset($this->_network);
        // Get rid of any packets
        foreach (array_keys($this->_packets) as $key) {
            unset($this->_packets[$key]);
        }
    }

    /**
    * Sets the packet to be sent
    *
    * @param object &$pkt   The packet to send out
    * @param array  $config The configuration to use for this packet
    *
    * @return False if no channels are available, int token otherwise
    */
    public function send(&$pkt, $config=array())
    {
        $return = false;
        if (is_object($pkt)) {
            if (count($this->_packets) < $this->_config["channels"]) {
                // Generate a unique token
                $token = uniqid();
                // Add in the packets special configuration
                $config = $pkt->config($config);
                $this->_packets[$token] =& TransportPacket::factory(
                    array_merge($this->_config, (array)$config),
                    $pkt
                );
                $return = $token;
            }
        }
        $this->_receive();
        $this->_send();
        return $return;
    }
    /**
    * Sets the packet to be sent
    *
    * @param int $token The token to check for the receive
    *
    * @return Packet object, false or null.
    */
    public function &receive($token)
    {
        // Might as well do these even if it is a bad call  ;)
        $this->_receive();
        $this->_send();
        // Check to see if the token is valid
        if (!is_object($this->_packets[$token])) {
            return false;
        }
        $reply = &$this->_packets[$token]->reply();
        if (is_object($reply) || ($reply === false)) {
            unset($this->_packets[$token]);
        }
        return $reply;
    }
    /**
    * Sets the packet to be sent
    *
    * @return Packet Object or null
    */
    public function &unsolicited()
    {
        $this->_receive();
        $this->_send();
        $ret = array_shift($this->_unsolicited);
        return $ret;
    }
    /**
    * Checks for incoming packets and deals with them
    *
    * @return null
    */
    private function _send()
    {
        foreach (array_keys($this->_packets) as $key) {
            $pkt =& $this->_packets[$key]->send();
            if (is_string($pkt) || is_object($pkt)) {
                $this->_network->send($pkt);
            }
        }
    }
    /**
    * Checks for incoming packets and deals with them
    *
    * @return null
    */
    private function _receive()
    {
        $pkt =& $this->_network->receive();
        // If we don't get a packet back exit
        if (is_object($pkt)) {
            // Check every packet until one claims it or we get to the end
            foreach (array_keys($this->_packets) as $key) {
                $reply = &$this->_packets[$key]->reply($pkt);
                if ($reply) {
                    break;
                }
            }
            // Save this packet if no one claimed it.
            if (!isset($reply) || is_null($reply)) {
                if (count($this->_unsolicited) > self::MAX_UNSOL) {
                    array_shift($this->_unsolicited);
                }
                $this->_unsolicited[] = &$pkt;
            }
        }
    }

}
?>
