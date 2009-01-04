<?php
/**
 * Main sensor driver.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2009 Hunt Utilities Group, LLC
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
 * @category   Units
 * @package    HUGnetLib
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** Error number for not getting a packet back */
define("PACKET_ERROR_NOREPLY_NO", -1);
/** Error message for not getting a packet back */
define("PACKET_ERROR_NOREPLY", "Board failed to respond");
/** Error number for not getting a packet back */
define("PACKET_ERROR_BADC_NO", -2);
/** Error message for not getting a packet back */
define("PACKET_ERROR_BADC", "Board responded: Bad Command");
/** Error number for not getting a packet back */
define("PACKET_ERROR_TIMEOUT_NO", -3);
/** Error message for not getting a packet back */
define("PACKET_ERROR_TIMEOUT", "Timeout waiting for reply");
/** Error number for not getting a packet back */
define("PACKET_ERROR_BADC_NO", -4);
/** Error message for not getting a packet back */
define("PACKET_ERROR_BADC", "Board responded: Bad Command");

/** Used for manipulating devInfo arrays */
require_once HUGNET_INCLUDE_PATH."/devInfo.php";


/**
 * Base class for sensors.
 *
 * @category   Sensors
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class SocketBase
{    


    /** @var int How many times we retry the packet until we get a good one */
    var $Retries = 2;
    /** @var array Server information is stored here. */
    var $socket = null;
    /** @var int The error number.  0 if no error occurred */
    var $Errno = 0;
    /** @var string The error string */
    var $Error = "";
    /** @var string The server string */
    var $Server = "";
    /** @var bool Whether this socket supports multiple packets */
    public $multiPacket = false;
    /** The timeout for waiting for a packet in seconds */
    var $ReplyTimeout = 5;

    /** @var bool Whether we should print out a lot output */
    var $verbose = false;        

    /** The preamble byte */
    static $preambleByte = "5A";

    /** Return all packets coming in */    
    protected $getAll = false;    
    /**
     * Sets the flag to get and return all packets.
     * 
     * @param bool $val The value to set getAll to.
     *
     * @return bool true if the DeviceID belongs to a gateway, false otherwise.
     */
    function getAll($val = true) 
    {
        $this->getAll = (bool) $val;
    }

    /**
     * Constructor.
     *
     * @param array $config The configuration array
     *
     * This just sets up the variables if they are passed to it.
     *
     */
    function __construct($config=array())
    {
        $this->verbose = $config["verbose"];
        $this->config = $config;
    }

    /**
     * Turns a packet string into an array of values.
     *
     * @param string $data The raw packet to parse
     *
     * @return array The packet array created from the string
     */
    function unbuildPacket($data) 
    {
        // Strip off any preamble bytes.
        $data = strtoupper($data);
        self::removePreamble($data);
        $pkt = array();
        $pkt["Command"] = substr($data, 0, 2);
        $pkt["To"]      = substr($data, 2, 6); 
        devInfo::setStringSize($pkt["To"], 6);
        $pkt["From"] = substr($data, 8, 6);
        devInfo::setStringSize($pkt["From"], 6);

        $length              = hexdec(substr($data, 14, 2));
        $pkt["Length"]       = (int)$length;
        $pkt["RawData"]      = substr($data, 16, ($length*2));
        $pkt["Data"]         = self::splitDataString($pkt["RawData"]);
        $pkt["Checksum"]     = substr($data, (16 + ($length*2)), 2);
        $pktdata             = substr($data, 0, strlen($data)-2);
        $pkt["CalcChecksum"] = self::PacketGetChecksum($pktdata);
        $pkt['RawPacket']    = $data;
        return $pkt;
    }
    /**
     * Computes the checksum of a packet
     *
     * @param string $PktStr The raw packet string
     *
     * @return string The checksum
     */
    public function packetGetChecksum($PktStr) 
    {
        $chksum = 0;
        for ($i = 0; $i < strlen($PktStr); $i+=2) {
            $val     = hexdec(substr($PktStr, $i, 2));
            $chksum ^= $val;
        }
        $return = sprintf("%02X", $chksum);
        return $return;
    }

    /**
     * Builds a packet
     * 
     * This function actually builds the packet to write to the socket.  It takes in
     * an array of the form:
     * <code>
     * array["To"] = "0000A5";
     * array["Command"] = 02;
     * array["Data"][0] = 00;
     * array["Data"][1] = 01;
     * ...
     * </code> 
     *
     * @param array  $Packet This is an array with packet commands in it.
     * @param string $from   If the packet is not from me this is the from address to use.
     *
     * @return string The packet in string form.
     */
    function packetBuild($Packet, $from=null) 
    {
        if (!is_array($Packet)) $Packet = array();
        $string       = "";
        $Packet       = array_change_key_case($Packet, CASE_LOWER);
        if (empty($from)) $from = $Packet["from"];
        $return       = false;
        $Packet["to"] = trim($Packet["to"]);
        $Packet["to"] = substr($Packet["to"], 0, 6);
        if (strlen($Packet["to"]) > 0) {
            $Packet["to"] = str_pad($Packet["to"], 6, "0", STR_PAD_LEFT);
            $string       = $Packet["command"];
            $string      .= $Packet["to"];
            $string      .= substr(trim($from), 0, 6);
            $string      .= sprintf("%02X", (strlen($Packet["data"])/2));
            $string      .= $Packet["data"];
            $return       = self::$preambleByte.self::$preambleByte.self::$preambleByte;
            $return      .= $string.self::PacketGetChecksum($string);
        }
        return($return);
    }
    /**
     * Removes the preamble from a packet string
     *
     * @param string &$data The preamble will be removed from this packet string
     *
     * @return null
     */ 
    protected function removePreamble(&$data) 
    {
        while (substr($data, 0, 2) == SocketBase::$preambleByte) $data = substr($data, 2);    
    }
    /**
     * Splits the data string into an array
     *
     * @param string $data The preamble will be removed from this packet string
     *
     * @return array
     */ 
    protected function splitDataString($data) 
    {
        for ($i = 0; $i < (strlen($data)/2); $i++) {
            $ret[] = hexdec(substr($data, ($i*2), 2));
        }
        return $ret;
    }
    /**
     *  Gets a character
     *
     * Returns the packet array on success, and false on failure
     *
     * @param int $socket  The socket we are using
     * @param int $timeout The timeout to use.
     *
     * @return bool
     */
    private function _recvPacketGetChar($timeout) 
    {
        $char = $this->ReadChar($timeout);
        if ($char !== false) {
            $char = devInfo::hexify(ord($char));
            
            $this->buffer .= $char;
            
            return true;
        } else {
            return false;
        }    
    }
    /**
     * Checks to see if what is in the buffer is a packet
     *
     * Returns the packet array on success, and false on failure
     *
     * @param int $socket The socket we are using
     *
     * @return mixed
     */
    private function _recvPacketCheckPkt() 
    {
        $pkt = $this->_recvPacketGetPacket();
        if (is_string($pkt)) {
            return self::unbuildPacket($pkt);
        } else {
            return false;
        }
    }

    
    /**
     * Finds a potential packet in a string
     *
     * Returns the packet array on success, and false on failure
     *
     * @return mixed
     */
    private function _recvPacketGetPacket() 
    {
        $pkt = stristr($this->buffer, SocketBase::$preambleByte.SocketBase::$preambleByte);
        self::removePreamble($pkt);
        $len = hexdec(substr($pkt, 14, 2));
        if (strlen($pkt) >= ((9 + $len)*2)) {
            $ret = substr($pkt, 0, (9+$len)*2);
            // Erase the buffer right away
            $this->buffer = ""; //= substr($pkt, (9+$len)*2);
            return $ret;
        } else {
            return false;
        }
    
    }
    /**
     * Receives a packet from the socket interface
     *
     * @param int $timeout Timeout for waiting.  Default is used if timeout == 0    
     *
     * @return bool false on failure, the Packet array on success
     */
    function RecvPacket($timeout=0) 
    {
        $timeout  = $this->getReplyTimeout($timeout);
        $Start    = time();
        $GotReply = false;
        while ((time() - $Start) < $timeout) {
            if (!$this->_recvPacketGetChar($timeout)) continue;
            $GotReply = $this->_recvPacketCheckPkt();
            if ($GotReply !== false) break;
        }
        return $GotReply;

    }
    /**
     * Figures out if we should use the timeout given or the default one
     *
     * @param int $timeout The timeout to use, if it is set
     *
     * @return int
     */
    protected function getReplyTimeout($timeout) 
    {
        if (!is_numeric($timeout) || ($timeout <= 0)) {
            $timeout = $this->ReplyTimeout;
        }
        return $timeout;        
    }
    /**
     * Sends out a packet
     *
     * @param array $packet   the packet to send out
     * @param bool  $GetReply Whether to expect a reply or not
     *
     * @return bool false on failure, true on success
     */
    function sendPacket($packet, $GetReply=true) 
    {
        if ($this->verbose) print "Sending Pkt: T:".$packet['PacketTo']." C:".$packet['sendCommand']."\n";
        $ret = $this->Write($packet, $GetReply);
        if (empty($ret)) return false;
        return $ret;

    }
    
}

?>
