<?php
/**
 *   Communicates with the tcp to serial driver
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *   
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *   
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *   
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package HUGnetLib
 * @subpackage Socket
 * @copyright 2007 Hunt Utilities Group, LLC
 * @author Scott Price <prices@hugllc.com>
 * @version $Id$    
 *
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



/**
 *   Class for talking with HUGNet endpoints through unix sockets
 *   
 * This class is meant to talk to something like {@link http://ser2net.sourceforge.net/ ser2net}
 * or any of the serial to ethernet servers from {@link http://www.bb-elec.com/ B&B Electronics}.
 * Other methods might work, but they are untested.  It basically expects a raw
 * serial port that is accessable through a unix socket.  
 */
class epsocket {
    /** @var int How many times we retry the packet until we get a good one */
    var $Retries = 2;
    /** @var array Server information is stored here. */
    var $socket = NULL;
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
    var $verbose = FALSE;        
    /** @var int The default period for checking in with the servers */
    var $CheckPeriod = 60;        
    /** @var array Array of strings that we are reading */
    var $readstr = array();        

    /**
     *   Write data out a socket
     *
     * @param string $data The data to send out the socket
     * @return int The number of bytes written on success, FALSE on failure
     */
    function Write($data) {
        if ($this->CheckConnect()) $this->Connect("", 0);
        usleep(mt_rand(500, 10000));
        $return = @fwrite($this->socket, $data);
//        if ($this->verbose) print "Writing: '".$line."' on ".$sock."\r\n";
        return($return);
    }


    /**
     *   Read data from the server
     *
     * @param int $timeout The amount of time to wait for the server to respond
     * @return int Read bytes on success, FALSE on failure
     */
    function readChar($timeout=-1) {
        if ($timeout < 0) $timeout = $this->PacketTimeout;

        $read = array($this->socket);
        $socks = @stream_select ($read, $write=NULL, $except=NULL, $timeout);
        $char = FALSE;
        if ($socks === FALSE) {
            if ($this->verbose) print "Bad Connection\r\n";
        } else if (count($read) > 0) {
            foreach($read as $tsock) {
                $char = @fread($tsock, 1);
                if (($char === FALSE) || ($char === EOF)) return FALSE;
//                $char = ord($char);
            }
        }
        return ($char);
    }     
    
    /**
     *   Turns a number into a text hexidecimal string
     *   
     *   If the number comes out smaller than $width the string is padded 
     *   on the left side with zeros.
     *
     *   Duplicate: {@link EPacket::hexify()}
     *
     * @param int $value The number to turn into a hex string
     * @param int $width The width of the final string
     * @return string The hex string created.
    */    
    function hexify(&$string, $width=2) {
        $string = dechex($string);
        $string = str_pad($string, $width, "0", STR_PAD_LEFT);
        $string = substr($string, 0, $width);
        $string = strtoupper($string);
    }   


    /**
     *   Closes the socket connection
     *   
     */
    function Close() {
        if ($this->socket != 0) {
            if ($this->verbose) print("Closing Connection\r\n");
            fclose($this->socket);
            $this->socket = 0;
        }
    }

    /**
     *   Checks to make sure that all we are connected to the server
     *   
     *   This routine only checks the connection.  It does nothing else.  If you want to
     *   have the script automatically connect if it is not connected already then use
     *   ep_socket::Connect().
     *
     * @uses ep_socket::Connect()
     * @return bool TRUE if the connection is good, FALSE otherwise
    */
    function CheckConnect() {

        if ($this->socket != 0) {
            if (feof($this->socket)) {
                $return = FALSE;
            } else {
                $return = TRUE;
            }            
        } else {
            $return = FALSE;
        }
        return($return);
    }
    
    /**
     *   Connects to the server
     *   
     *   This function first checks the connection.  If you are planning on checking the
     *   connection and want the server to automatically connect if not connectd, use this
     *   routine.  If you just want to check the connection, use ep_socket::CheckConnect.
     *
     * @param string $server Name or IP address of the server to connect to
     * @param int $port The TCP port on the server to connect to
     * @param int $timeout The time to wait before giving up on a bad connection
     * @return bool TRUE if the connection is good, FALSE otherwise
     */
    function Connect($server = "", $port = 0, $timeout=0) {

        $return = FALSE;
        if ($this->CheckConnect()) {
            $return = TRUE;
        } else {
            $this->Close();
        }
        if ($return === FALSE) {
            if (!empty($server)) $this->Server = $server;
            if (!empty($port)) $this->Port = $port;

            if (!empty($this->Server) && !empty($this->Port)) {
    
                if ($this->verbose) print "Connecting to ".$this->Server.":".$this->Port."\r\n";
                $this->socket = @fsockopen($this->Server, $this->Port, $this->Errno, $this->Error, $this->SockTimeout);
                if (($this->Errno == 0) && ($this->socket != 0)) {
                    stream_set_blocking($this->socket, FALSE);
                    if ($this->verbose) print("Opened the Socket ".$this->socket." to ".$this->Server.":".$port."\n");
                    $return = TRUE;
                } else {
                    if ($this->verbose) print("Connection to ".$server." Failed. Error ".$this->Errno.": ".$this->Error."\n");
                    $this->socket = 0;
                }
            } else {
                $this->Errno = -1;
                $this->Error = "No server specified";
                $return = FALSE;
            }
        }        
        return($return);
    }            


    /**
     *   Constructor
     *   
     * @param string $server The name or IP of the server to connect to
     * @param int $tcpport The TCP port to connect to on the server. Set to 0 for
     *       the default port.
     * @param bool $verbose Make the class put out a lot of output
     */
    function ep_socket($server, $tcpport = SOCKET_DEFAULT_PORT, $verbose=FALSE) {
        if (empty($tcpport)) $tcpport = SOCKET_DEFAULT_PORT;
        $this->verbose = $verbose;
        if ($this->verbose) print "Creating Class ".get_class($this)."\r\n";
        if (trim($server) != "") {
            $this->Connect($server, $tcpport);
        }
        if ($this->verbose) print "Done\r\n";
    }
    
}
?>
