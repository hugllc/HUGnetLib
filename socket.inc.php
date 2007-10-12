<?php
/*
HUGnetLib is a library of HUGnet code
Copyright (C) 2007 Hunt Utilities Group, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 3
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
?>
<?php
/**
	$Id$

	@file socket.inc.php
	@brief Class for talking with seriald

	
	
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
	@brief Class for talking with HUGNet endpoints
	
	
*/

class ep_socket {
	/**
		@privatesection
		@brief How many times we retry the packet until we get a good one 
	*/
	var $Retries = 2;
	/**
		@private
		Server information is stored here.
	
		@par What is 'Sock' anyway?
		A significant number of the methods in this class have the parameter
		$sock.  This is the same everywhere.  It is an index to this array
		that stores socket information.  It is just a key to identify the
		particalur server connection that is stored here.  It can actually
		be anything.
	*/
	var $socket = NULL;
	/** The error number.  0 if no error occurred */
	var $Errno = 0;
	/** The error string */
	var $Error = "";
	/** The port number.   This is the default */
	var $Port = 2000;
	/** The server string */
	var $Server = "";


	/** The timeout for waiting for a packet in seconds */
	var $PacketTimeout = 5;
	/** The timeout for waiting for a packet in seconds */
	var $AckTimeout = 2;
	/** The timeout for waiting for a packet in seconds */
	var $SockTimeout = 2;
	var $verbose = FALSE;		//!< Whether we should print out a lot output
	var $CheckPeriod = 60;		//!< The default period for checking in with the servers
	var $readstr = array();		//!< Array of strings that we are reading
	/**
		@publicsection
	*/
	/**
		@private
		@brief Write data out a socket
		@param $line String the data to send out the socket
		@param $sock Mixed The socket to use.  See ep_socket::socket for more information.
		@return FALSE on failure, the number of bytes written on success
	*/
	function Write($data) {
		if ($this->CheckConnect()) $this->Connect("", 0);
        usleep(mt_rand(500, 10000));
		$return = @fwrite($this->socket, $data);
//		if ($this->verbose) print "Writing: '".$line."' on ".$sock."\r\n";
		return($return);
	}


	/**
		@brief Read data from the server
		@param $sock Mixed The socket to use.  See ep_socket::socket for more information.
		@param $timeout Integer the amount of time to wait for the server to respond
		@return Read bytes on success, FALSE on failure
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
//			    $char = ord($char);
			}
		}
        return ($char);
    }     
    
    function hexify(&$string, $width=2) {
        $string = dechex($string);
        $string = str_pad($string, $width, "0", STR_PAD_LEFT);
        $string = substr($string, 0, $width);
        $string = strtoupper($string);
    }   


	/**
		@brief Closes the socket connection
		@param $sock Mixed The socket to use.  See ep_socket::socket for more information.

		This function sends "exit\r\n" to the server to announce its intention
		to break the connection before it actually does.  Use this function unless
		you absolutely need ep_socket::ForceClose.
	*/
	function Close() {
		if ($this->socket != 0) {
			if ($this->verbose) print("Closing Connection\r\n");
			fclose($this->socket);
			$this->socket = 0;
		}
	}

	/**
		@brief Checks to make sure that all we are connected to the server
		@param $server String Name or IP address of the server to connect to
		@param $port Integer The TCP port on the server to connect to
		@param $sock Mixed The socket to use.  See ep_socket::socket for more information.
		@param $timeout Integer The time to wait before giving up on a bad connection
		@return TRUE if the connection is good, FALSE otherwise
		
		This routine only checks the connection.  It does nothing else.  If you want to
		have the script automatically connect if it is not connected already then use
		ep_socket::Connect.
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
		@brief Connects to the server
		@param $server String Name or IP address of the server to connect to
		@param $port Integer The TCP port on the server to connect to
		@param $sock Mixed The socket to use.  See ep_socket::socket for more information.
		@param $timeout Integer The time to wait before giving up on a bad connection
		@return TRUE if the connection is good, FALSE otherwise
		
		This function first checks the connection.  If you are planning on checking the
		connection and want the server to automatically connect if not connectd, use this
		routine.  If you just want to check the connection, use ep_socket::CheckConnect.
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
		@brief Constructor	
		@param $server String The name or IP of the server to connect to
		@param $tcpport Integer the TCP port to connect to on the server.
		@param $sock Mixed The socket to use.  See ep_socket::socket for more information.
		@param $verbose Boolean Make the class put out a lot of output
	*/
	function ep_socket($server, $tcpport = SOCKET_DEFAULT_PORT, $sock=0, $verbose=FALSE) {
		if (empty($tcpport)) $tcpport = SOCKET_DEFAULT_PORT;
		$this->verbose = $verbose;
		if ($this->verbose) print "Creating Class ".get_class($this)."\r\n";
		if (trim($server) != "") {
			$this->Connect($server, $tcpport, $sock);
		}
		if ($this->verbose) print "Done\r\n";
	}
	
}
?>
