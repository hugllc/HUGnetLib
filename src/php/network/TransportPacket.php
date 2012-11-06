<?php
/**
 * Classes for dealing with packets
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\network;
/**
 * This is the packet transport class.
 *
 * Config (default):
 *    "find"    bool   Optional Whether to send out find packets default (true)
 *    "ident"   string Optional 6 char hex identity to send in find packet (random)
 *    "tries"   int    Optional Number of times to try sending out the packet (3)
 *    "quiet"   bool   Optional If true it won't throw exceptions (false)
 *    "timeout" float  Optional Time in seconds to wait for a reply (5)
 *
 * This handles retries and checking for a reply.  It will also provide reply
 * times.  It will try the number of times given, plus will try a 'findping' before
 * the last packet.  If they findping doesn't return it doesn't send the last
 * packet.
 *
 * This class is very closely coupled to \HUGnet\network\Packet because it is
 * sending those out.  They are the only thing this will ever deal with.
 *
 * Requires: BCMath extension
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
final class TransportPacket
{
    /** This is the packet we are sending */
    private $_packet;
    /** This is the reply we have */
    private $_reply;
    /** This is the reply we have */
    private $_find = true;
    /** This is the first time (in seconds) that the packet was sent */
    private $_sent = null;
    /** This is the time (in seconds) that the reply was received */
    private $_return = null;
    /** This is the last time (in seconds) that the packet was sent */
    private $_time = 0;
    /** This is the current count down for retries */
    private $_retries = null;
    /** This is the ident that was sent out */
    private $_ident = null;
    /** This the default configuration */
    private $_defaultConfig = array(
        "find"    => true,
        "ident"   => "",
        "tries"   => 3,
        "timeout" => 5.0,
    );
    /** This the live configuration */
    private $_config = array();

    /**
    * This builds and populates the packet
    *
    * @param array $config The configuration array
    * @param mixed &$pkt   Array, string or packet object to send out
    */
    private function __construct($config, &$pkt)
    {
        $this->_config = array_merge($this->_defaultConfig, (array)$config);
        if ($this->_config["tries"] > 0) {
            $this->_retries = $this->_config["tries"];
        }
        if (empty($this->_config["ident"])) {
            $this->_config["ident"] = sprintf("%06X", mt_rand(0, 0xFFFFFF));
        }
        $this->_packet = &$this->_fix($pkt);
        $this->_find = (bool)$this->_config["find"]
            && ($this->_packet->type() !== "FINDPING");
    }
    /**
    * Creates the object
    *
    * @param array $config The configuration array
    * @param mixed &$pkt   Array, string or packet object to send out
    *
    * @return PacketTransport object
    */
    static public function &factory($config, &$pkt)
    {
        $obj = new TransportPacket($config, $pkt);
        return $obj;
    }
    /**
    * Returns a link to the original packet
    *
    * @return Packet object
    */
    public function &packet()
    {
        return $this->_packet;
    }
    /**
    * Creates the object
    *
    * @return PacketTransport object
    */
    public function &send()
    {
        // Set the time if this is the first one
        if (is_null($this->_sent)) {
            $this->_sent = $this->_time();
        }
        if ($this->_timeout()) {
            return $this->_find();
        }
        $ret = "";
        return $ret;
    }
    /**
    * Creates the object
    *
    * @param mixed &$pkt Array, string or packet object to send out
    *
    * @return PacketTransport object
    */
    public function &reply(&$pkt = null)
    {
        $return = null;
        if (!is_null($pkt) && !is_object($this->_reply)) {
            $reply = &$this->_fix($pkt);
            if ($this->_isReply($reply)) {
                if ($reply->data() === $this->_ident) {
                    // Ident packet.  Return true and reset _ident
                    $this->_ident = null;
                    $return = true;
                } else {
                    // This is our actual reply
                    $this->_return = $this->_time();
                    $this->_reply = &$reply;
                }
            }
        }
        if (is_object($this->_reply)) {
            return $this->_reply;
        }
        if (($this->_retries < 1) && $this->_timeout()) {
            $return = false;
            return $return;
        }
        return $return;
    }
    /**
    * Returns the time it took to get a reply to this packet
    *
    * @return False if no reply yet, float otherwise
    */
    public function time()
    {
        if (empty($this->_return)) {
            return false;
        }
        return bcsub($this->_return, $this->_sent, 4);
    }
    /**
    * Decides if this is a reply to our packet
    *
    * @param object &$pkt Packet object to check
    *
    * @return PacketTransport object
    */
    private function _isReply(&$pkt)
    {
        return $pkt->isValid()
            && ($pkt->type() === "REPLY")
            && ($pkt->To() === $this->_packet->From())
            && ($pkt->From() === $this->_packet->To());
    }
    /**
    * Gets the current time
    *
    * @return float The current time in seconds
    */
    private function &_find()
    {
        if (($this->_find) && ($this->_retries === 1)) {
            // Only find once
            $this->_find  = false;
            $this->_ident = $this->_config["ident"];
            $this->_time = $this->_time();
            return packets\Packet::factory(
                array(
                    "To"      => $this->_packet->to(),
                    "From"    => $this->_packet->from(),
                    "Command" => "FINDPING",
                    "Data"    => $this->_ident,
                )
            );
        } else if (($this->_retries > 0) && is_null($this->_ident)) {
            $this->_retries--;
            $this->_time = $this->_time();
            return $this->_packet;
        } else if (!is_null($this->_ident)) {
            // No reply to the findping packet, so we are ending with this packet
            $this->_retries = 0;
        }
        $ret = false;
        return $ret;
    }
    /**
    * Gets the current time
    *
    * @return float The current time in seconds
    */
    private function _time()
    {
        list($usec, $sec) = explode(" ", microtime());
        return bcadd($usec, $sec, 8);
    }
    /**
    * Creates the object
    *
    * @param mixed &$pkt Array, string or packet object to send out
    *
    * @return Packet object
    */
    private function &_fix(&$pkt)
    {
        if (is_object($pkt) && is_a($pkt, "HUGnet\\network\\Packet")) {
            return $pkt;
        } else {
            return packets\Packet::factory($pkt);
        }
    }
    /**
    * Clears the channel
    *
    * @return bool True if time has expired, false otherwise
    */
    private function _timeout()
    {
        return bcsub($this->_time(), $this->_time, 3) > $this->_config["timeout"];
    }
}


?>
