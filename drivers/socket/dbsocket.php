<?php
/**
 * Communicates with the tcp to serial driver
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Socket
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** The base for all database classes */
require_once HUGNET_INCLUDE_PATH."/base/SocketBase.php";

if (!class_exists("dbsocket")) {
    /**
     * Class for talking with HUGNet endpoints through unix sockets
     * 
     * This class is meant to talk to something like {@link http://ser2net.sourceforge.net/ ser2net}
     * or any of the serial to ethernet servers from {@link http://www.bb-elec.com/ B&B Electronics}.
     * Other methods might work, but they are untested.  It basically expects a raw
     * serial port that is accessable through a unix socket.  
     *
     * @category   Database
     * @package    HUGnetLib
     * @subpackage Socket
     * @author     Scott Price <prices@hugllc.com>
     * @copyright  2007 Hunt Utilities Group, LLC
     * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
     * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
     */
    class DbSocket extends SocketBase
    {
        /** @var string The database table to use */
        protected $table = "PacketSend";
        /** @var string The database table to use */
        protected $id = "id";
        /** @var int How many times we retry the packet until we get a good one */
        var $Retries = 2;
        /** @var array Server information is stored here. */
        var $socket = null;
        /** @var int The error number.  0 if no error occurred */
        var $Errno = 0;
        /** @var string The error string */
        var $Error = "";
        /** @var int The port number.   This is the default */
        var $Port = 2000;
        /** @var string The server string */
        var $Server = "";
    
    
        /** @var int The timeout for waiting for a packet in seconds */
        var $PacketTimeout = 5;
        /** @var int The timeout for waiting for a packet in seconds */
        var $AckTimeout = 2;
        /** @var int The timeout for waiting for a packet in seconds */
        var $SockTimeout = 2;
        /** @var bool Whether we should print out a lot output */
        var $verbose = false;        
        /** @var int The default period for checking in with the servers */
        var $CheckPeriod = 60;        
        /** @var array Array of strings that we are reading */
        var $readstr = array();        
    
        /** @var array The packet we are currently sending out */
        var $packet = array();
    
        /** @var int The index of the reply array */
        private $replyIndex = 0;
        
        private $index = 0;
        /**
         * Write data out a socket.  
         *
         * Returns the random id on success, false on failure.
         *
         * @param string $data The data to send out the socket in string form
         * @param array  $pkt  The data to send out the socket in array form
         *
         * @return mixed
         */
        public function write($pkt) 
        {
            $id                      = rand(1, 24777216);  // Big random number for the id
            $this->packet[$id]       = $pkt;
            $this->packet[$id]["id"] = $id;
            $this->sent              = date("Y-m-d H:i:s");
            $this->replyId           = $id;

            $ret = $this->socket->add($this->packet[$id]);

            if ($ret === false) {
                return false;
            } else {
                return $id;
            }
        }
        
        /**
         *  Gets the first of the packets that is destined for us.
         *
         * @return bool
         */
        private function _getPacket($reply=false) 
        {
            if ($reply) return $this->_getPacketReply();
            static $lastCheck;
            $now = date("Y-m-d H:i:s");
            
            if (empty($lastCheck)) $lastCheck = "0000-00-00 00:00:00";
            $query = " `Date` > ?";
            $data[] = $lastCheck;
            $res = $this->socket->getWhere($query, $data);
            $ret = false;
            if (is_array($res)) {
                foreach ($res as $pkt) {
                    $ret[] = $this->unbuildPacket($pkt);
                }
            }
            $lastCheck = $now;
            return $ret;
        }
        /**
         *  Gets the first of the packets that is destined for us.
         *
         * @return bool
         */
        private function _getPacketReply() 
        {
            $query = " Type = 'REPLY'";
            if (!empty($this->sent)) {
                $query .= " AND `Date` > ?";
                $data[] = $this->sent;
            }
            if ($this->replyId) {
                $query .= " AND id = ?";
                $data[] = $this->replyId;
            }
            $res = $this->socket->getWhere($query, $data);
            if (is_array($res)) {
                foreach ($res as $pkt) {
                    if (is_array($this->packet[$pkt["id"]])) {
                        $pkt["PacketTo"] = $this->packet[$pkt["id"]]["SentFrom"];
                        $this->_deletePacket($pkt["id"]);
                        $this->replyId = 0;
                        return $this->unbuildPacket($pkt);
                    }
                }
            }
            return false;
        }
    
        /**
         *  removes the packet for the id given
         *
         * @param int $id The random id of the packet to delete.
         *
         * @return null
         */
        private function _deletePacket($id) 
        {
            unset($this->packet[$id]);
            $this->socket->remove($id);
    
        }
        /**
         * Receives a packet from the socket interface
         *
         * @param int $timeout Timeout for waiting.  Default is used if timeout == 0    
         *
         * @return bool false on failure, the Packet array on success
         */
        function RecvPacket($timeout=0, $reply = true) 
        {
            $timeout  = $this->getReplyTimeout($timeout);
            $Start    = time();
            $GotReply = false;
    
            do {
                $GotReply = $this->_getPacket($reply);
                if (is_array($GotReply)) break;
            } while ((time() - $Start) < $timeout);
            $this->replyId = 0;
            return $GotReply;
    
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
            $pkt = array();
            $pkt["Command"] = (empty($data["Command"])) ? $data["sendCommand"] : $data["Command"];
            $pkt["To"]      = (empty($data["PacketTo"])) ? $data["To"] : $data["PacketTo"]; 
            devInfo::setStringSize($pkt["To"], 6);
            $pkt["From"]    = (empty($data["PacketFrom"])) ? $data["From"] : $data["PacketFrom"];
            devInfo::setStringSize($pkt["From"], 6);
    
            $pkt["Length"]       = strlen($data["RawData"] / 2);
            $pkt["RawData"]      = $data["RawData"];
            $pkt["Data"]         = self::splitDataString($pkt["RawData"]);
            $pkt["Checksum"]     = self::PacketGetChecksum($pkt["RawData"]);
            $pkt["CalcChecksum"]     = self::PacketGetChecksum($pkt["RawData"]);
            return $pkt;
        }
        
        /**
         * Closes the socket connection
         * 
         * @return null
         */
        public function close() 
        {
            $this->socket = false;
        }
    
        /**
         * Checks to make sure that all we are connected to the server
         * 
         * This routine only checks the connection.  It does nothing else.  If you want to
         * have the script automatically connect if it is not connected already then use
         * dbsocket::Connect().
         *
         * @return bool true if the connection is good, false otherwise
         *
         * @todo How do I make this check the connection when the connection might be
         *      
         */
        public function checkConnect() 
        {
            return is_object($this->socket) && (get_class($this->socket) == "HUGnetDB");
        }
        
        /**
         * Connects to the server
         * 
         * This function first checks the connection.  If you are planning on checking the
         * connection and want the server to automatically connect if not connectd, use this
         * routine.  If you just want to check the connection, use ep_socket::CheckConnect.
         *
         *
         * @return bool true if the connection is good, false otherwise
         */
        public function connect($config=array()) 
        {
            if (empty($config)) $config = $this->config;
            if ($this->CheckConnect()) return true;
            $this->close();
            $this->socket = new HUGnetDB($config);
            return $this->CheckConnect();
        }            
    
    
        /**
         * Constructor
         * 
         * @param array $config The configuration to use.
         *
         * @return null
         */
        public function __construct($config = array()) 
        {
            $config["table"] = empty($config["socketTable"]) ? "PacketLog" : $config["socketTable"];
            parent::__construct($config);
            $this->connect();
        }
        
    }
}
?>
