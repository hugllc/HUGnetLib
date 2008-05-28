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
 * @category   UnixSocket
 * @package    HUGnetLib
 * @subpackage Socket
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the base socket code */
require_once HUGNET_INCLUDE_PATH."/base/SocketBase.php";


if (!class_exists("epsocket")) {
    /**
     * Class for talking with HUGNet endpoints through unix sockets
     * 
     * This class is meant to talk to something like {@link http://ser2net.sourceforge.net/ ser2net}
     * or any of the serial to ethernet servers from {@link http://www.bb-elec.com/ B&B Electronics}.
     * Other methods might work, but they are untested.  It basically expects a raw
     * serial port that is accessable through a unix socket.  
     *
     * @category   UnixSocket
     * @package    HUGnetLib
     * @subpackage Socket
     * @author     Scott Price <prices@hugllc.com>
     * @copyright  2007 Hunt Utilities Group, LLC
     * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
     * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
     */
    class EpSocket extends SocketBase
    {
        /** @var int How many times we retry the packet until we get a good one */
        var $Retries = 2;
        /** @var array Server information is stored here. */
        var $socket = false;
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
    
        protected $server = "127.0.0.1";
        protected $tcpport = "2000";
        /**
         * Write data out a socket
         *
         * @param string $data The data to send out the socket
         *
         * @return int The number of bytes written on success, false on failure
         */
        function write($packet) 
        {
            $pktData = self::packetBuild($packet["packet"]);
            $data = devInfo::deHexify($pktData);
            $this->Connect();
            usleep(mt_rand(500, 10000));
            $return = @fwrite($this->socket, $data);
            if ($this->verbose) print "Wrote: ".$return." chars (".$pktData.") on ".$this->socket."\r\n";
            return($return);
        }
    
    
        /**
         * Read data from the server
         *
         * @param int $timeout The amount of time to wait for the server to respond
         *
         * @return int Read bytes on success, false on failure
         */
        function readChar($timeout=-1) 
        {
            if ($timeout < 0) $timeout = $this->PacketTimeout;
    
            $read  = array($this->socket);
            $socks = @stream_select ($read, $write=null, $except=null, $timeout);
            $char  = false;
            if ($socks === false) {
                if ($this->verbose) print "Bad Connection\r\n";
            } else if (count($read) > 0) {
                foreach ($read as $tsock) {
                    $char = @fread($tsock, 1);
                    if (($char === false) || ($char === EOF)) return false;
                }
            }
            return ($char);
        }     
        
        /**
         * Closes the socket connection
         * 
         * @return null
         */
        function close() 
        {
            if ($this->socket === false) return;
            if ($this->verbose) print("Closing Connection\r\n");
            // Close the socket
            fclose($this->socket);
            $this->socket = false;
        }
    
        /**
         * Checks to make sure that all we are connected to the server
         * 
         * This routine only checks the connection.  It does nothing else.  If you want to
         * have the script automatically connect if it is not connected already then use
         * epsocket::Connect().
         *
         * @return bool true if the connection is good, false otherwise
         */
        function checkConnect() 
        {
            if ($this->socket == false) return false;
            if (feof($this->socket)) return false;            
            return true;
        }
        
        /**
         * Connects to the server
         * 
         * This function first checks the connection.  If you are planning on checking the
         * connection and want the server to automatically connect if not connectd, use this
         * routine.  If you just want to check the connection, use ep_socket::CheckConnect.
         *
         * @param array $config The configuration to use.
         *
         * @return bool true if the connection is good, false otherwise
         */
        function connect($config=array()) 
        {
            if (empty($config)) $config = $this->config;
            if ($this->CheckConnect()) return true;
    
            $this->Close();
            if (!empty($config["GatewayIP"])) $this->Server = $config["GatewayIP"];
            if (!empty($config["GatewayPort"])) $this->Port = $config["GatewayPort"];
    
            if (empty($this->Server) || empty($this->Port)) return false;
            
            return $this->_connectOpenSocket();
        }            
    
        /**
         * This actually opens the socket and sets blocking.
         *
         * @return bool
         */
        private function _connectOpenSocket() 
        {
            if ($this->verbose) print "Opening socket to ".$this->Server.":".$this->Port."\r\n";
            $this->socket = @fsockopen($this->Server, $this->Port, $this->Errno, $this->Error, $this->SockTimeout);
//            $dsn = "tcp://".$this->Server.":".$this->Port;
//            if ($this->verbose) print "Opening socket to ".$dsn."\r\n";
//            $this->socket = stream_socket_client($dsn, $this->Errno, $this->Error, $this->SockTimeout);
            if ($this->socket !== false) {
                stream_set_blocking($this->socket, false);
                if ($this->verbose) print("Opened the Socket ".$this->socket." to ".$this->Server.":".$this->Port."\n");
                return true;
            }
            if ($this->verbose) print("Connection to ".$this->Server." Failed. Error ".$this->Errno.": ".$this->Error."\n");
            return false;
        }
    
        /**
         * Constructor
         * 
         * @param array $config The configuration to use.
         *
         * @return null
         */
        function __construct($config=array()) 
        {
            parent::__construct($config);
            if ($this->verbose) print "Creating Class ".get_class($this)."\r\n";
            $this->Connect();
            if ($this->verbose) print "Done\r\n";
        }
        
    }
}
?>
