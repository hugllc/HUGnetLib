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
 * @version    SVN: $Id: driver.php 545 2007-12-11 21:50:55Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** The default port for connection to seriald */
define("SOCKET_DEFAULT_PORT", 1200);
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

require_once HUGNET_INCLUDE_PATH."/devInfo.php";
require_once HUGNET_INCLUDE_PATH."/base/DbBase.php";

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
    class DbSocket extends DbBase
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
        public function Write($data, $pkt) 
        {
            $id                      = rand(1, 24777216);  // Big random number for the id
            $this->packet[$id]       = $pkt;
            $this->packet[$id]["id"] = $id;
    
            $ret = $this->_insertPacket($this->packet[$id]);
    
            if ($ret === false) {
                return false;
            } else {
                return $id;
            }
        }
    
        /**
         * Write data out a socket
         *
         * @param array $pkt The data to send out the socket in array form
         *
         * @return bool
         */
        private function _insertPacket($pkt) 
        {
            return $this->add($pkt);
        }
        /**
         * Turns an array into a packet.
         *
         * @param array  $pkt  The data to send out the socket in array form
         *
         * @return string
         */
        private function _packetify(&$pkt) 
        {
            $pkt["To"]   = $pkt["PacketTo"];
            $pkt["Data"] = $pkt["RawData"];
            return EPacket::PacketBuild($pkt, $pkt["PacketFrom"]);
        }
    
        /**
         *  Gets the first of the packets that is destined for us.
         *
         * @return bool
         */
        private function _getPacket() 
        {
            if (!is_string($this->replyPacket)) $this->replyPacket = "";
            if (!empty($this->replyPacket)) return true;
            $res = $this->getWhere(" Type = 'REPLY'");
            if (is_array($res)) {
                foreach ($res as $pkt) {
                    if (is_array($this->packet[$pkt["id"]])) {
                        $this->replyPacket = $this->_packetify($pkt);
                        $this->reply       = $pkt["id"];
                        $this->index       = 0;
                        return true;
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
         * @return none
         */
        private function _deletePacket($id) {
            unset($this->packet[$id]);
            if ($this->reply == $id) {
                unset($this->reply);
                $this->index = 0;
            }
            $this->remove($id);
    
        }
        /**
         * Read data from the server
         *
         * @param int $timeout The amount of time to wait for the server to respond
         *
         * @return mixed Read bytes on success, false on failure
         */
        public function readChar($timeout=-1) 
        {
            if ($timeout < 0) $timeout = $this->PacketTimeout;
            $char = false;
            if ($this->_getPacket()) {
                if ($this->index < strlen($this->replyPacket)) {
                    $char = hexdec(substr($this->replyPacket, $this->index, 2));
                    
                    $this->index += 2;
                    if ($this->index >= strlen($this->replyPacket)) {
                        $this->_deletePacket($this->reply);
                        $this->index       = 0;
                        $this->replyPacket = "";
                    }
                    $char = chr($char);
                }
            }
            return $char;
        }     
        
        /**
         * Closes the socket connection
         * 
         * @return none
         */
        public function Close() 
        {
            $this->packet = array();
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
        public function CheckConnect() 
        {
            if ($this->driver == "sqlite") return true;
            return $this->_db->getAttribute(PDO::ATTR_CONNECTION_STATUS);
        }
        
        /**
         * Connects to the server
         * 
         * This function first checks the connection.  If you are planning on checking the
         * connection and want the server to automatically connect if not connectd, use this
         * routine.  If you just want to check the connection, use ep_socket::CheckConnect.
         *
         * @param string $server  Name or IP address of the server to connect to
         * @param int    $port    The TCP port on the server to connect to
         * @param int    $timeout The time to wait before giving up on a bad connection
         *
         * @return bool true if the connection is good, false otherwise
         */
        public function Connect($server = "", $port = 0, $timeout=0) 
        {
    
            if ($this->CheckConnect()) return true;
            $this->Close();
            return false;
        }            
    
    
        /**
         * Constructor
         * 
         * @param object $db      Adodb connect object
         * @param bool   $verbose Whether to give a lot of output.
         *
         * @return none
         */
        public function __construct(&$db, $verbose=false) 
        {
            $this->verbose($verbose);
            parent::__construct($db);
        }
        
    }
}
?>
