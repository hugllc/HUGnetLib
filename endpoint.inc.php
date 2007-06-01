<?php
/**
	$Id: endpoint.inc.php 53 2006-05-14 20:57:54Z prices $

	@file endpoint.inc.php
	@brief Class for talking with endpoints through hugnetd.
	@warning This interface is depreciated.
	@deprecated
	
	
	
*/

/** @brief The placeholder for the Acknoledge command */
define("PACKET_COMMAND_ACK", "01");
/** @brief The placeholder for the Echo Request command */
define("PACKET_COMMAND_ECHOREQUEST", "02");
/** @brief The placeholder for the Capabilities command */
define("PACKET_COMMAND_GETSETUP", "5C");
/** @brief The placeholder for the Capabilities command */
define("PACKET_COMMAND_GETDATA", "55");
/** @brief The placeholder for the Bad Command command */
define("PACKET_COMMAND_BADC", "FF");
/** @brief The placeholder for the Read E2 command */
define("PACKET_COMMAND_READE2", "0A");
/** @brief The placeholder for the Write E2 command */
define("PACKET_COMMAND_READRAM", "0B");

/* Memory Subcommands */

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



/** Error Message */
define("DRIVER_NOT_FOUND", 1);
/** Error Message */
define("DRIVER_NOT_COMPLETE", 2);


/**
	@brief Class for talking with HUGNet endpoints
	@deprecated
	@todo Fix the documentation in this file if we decide to use it.
*/

class endpointsocket {
	/** This contains all of the endpoints we know about */
	var $EndPoints = array();
	/** This is the packet buffer */
	var $Packets = array();
	/** How many times we retry the packet until we get a good one */
	var $Retries = 2;
	/** This is the socket descriptor */
	var $socket = 0;
	/** The error number.  0 if no error occurred */
	var $Errno = 0;
	/** The error string */
	var $Error = "";
	/** The timeout for waiting for a packet in seconds */
	var $PacketTimeout = 5;
	/** The timeout for waiting for a packet in seconds */
	var $AckTimeout = 2;
	/** The timeout for waiting for a packet in seconds */
	var $SockTimeout = 2;
	/** Infomation about various endpoints */
	var $Info = array();
	
	var $dev = array(); //!< Device info
	
	/**
		@private
		@brief Writes a packet out to the socket
		@param $Packet This is an array with packet commands in it.
		
		This function actually writes the packet out to the socket.  It takes in
		an array  of the form:
		@code
		array["Command"] = 02;
		array["Data"][0] = 00;
		array["Data"][1] = 01;
		...
		@endcode 
	*/
	function socketwrite($Packet) {
		if (!is_array($Packet)) $Packet = array();
		$string = "Begin\r\n";
		foreach($Packet as $key => $val) {
			if ((trim(strtolower($key)) == "data") && is_array($val)) {
				$string .= $key.":";
				foreach($val as $stuff) {
					if (is_string($stuff)) {
						$string .= " ".trim($stuff);	
					} else {
						$string .= " ".str_pad(dechex($stuff), 2, "0", STR_PAD_LEFT);
					}
				}
				$string .= "\r\n";
			} else {
				if (trim($val) != "") {
					$string .= $key.": ".$val."\r\n";
				} else {
					$string .= $key."\r\n";
				}
			}
		}
		$string .= "End\r\n";
		add_debug_output(nl2br($string));
		if ($this->printdebug === TRUE) $this->debugtext .= "<b>Sending:</b><br>\n".nl2br($string);
		@fwrite($this->socket, $string);		
		$return = $this->GetAck();

		// If we don't get an ack then try reopening the connection and trying again.		
		if ($return === FALSE) {
			if ($this->printdebug === TRUE) $this->debugtext .= "Ack Failed<br>\n";
			$this->connect($this->server, $this->port);
			add_debug_output("Trying Again<BR>\n");
			if ($this->printdebug === TRUE) $this->debugtext .= "Trying Again<br>\n";
			$return = @fwrite($this->socket, $string);	
			return($this->GetAck());				
		}
		return($return);
	}
	/**
		@public
		@brief Sends out a packet
		@param $Info The array with the device information in it
		@param $Packet Array with packet information in it.
		@param $Wait The timeout to use. Default is used if Wait == 0
		@param $Retry Boolean Whether to retry the connection or not
		@param $level asdf
	*/
	function SendPacket($Info, $Packet, $Wait = 0, $Retry=FALSE, $level = 0) {
		if ($Retry === FALSE) $Retry = $this->Retries;
		$Retry++;
		$this->checkConnect($Info["GatewayIP"], $Info["GatewayPort"]);
		add_debug_output("Sending a packet<BR>\n");
		if ($this->printdebug === TRUE) $this->debugtext .= "Sending a packet<br>\n";
		add_debug_output(get_stuff($Packet, "Packet"));
		if ($this->printdebug === TRUE) $this->debugtext .= get_stuff($Packet, "Packet");
		if ((isset($Packet["Command"]) && isset($Packet["To"])) || isset($Packet["PacketStat"]) || isset($Packet["unsolicited"]) || isset($Packet["SN"]) || isset($Packet["clientstat"]) || isset($Packet["pingstat"])) {
			$this->Packets = array();
			$gotreply = FALSE;
			$count = 0;
			if ($this->socket != 0) {
				while (($count < $Retry) && ($gotreply == FALSE)) {
					$retval = $this->socketwrite($Packet);
//					$retval = $this->socketwrite("Begin\r\nTo:".$to."\r\nCommand:".$command."\r\nData:".$data."\r\nEnd\r\n");
		
					if ($retval === FALSE) {
						if ($this->socket != 0) {
							@$this->Errno = socket_last_error($this->socket);
							@$this->Error = socket_strerror($this->Errno);
							add_debug_output("Error ".$this->Errno." ".$this->Error."<BR>\n");
							if ($this->printdebug === TRUE) $this->debugtext .= "Error ".$this->Errno." ".$this->Error."<br>\n";

						} else {
							$this->Errno = "-1";
							$this->Error = "Socket not open";
						}
					} else {
						$gotreply = $this->RecvPacket($Packet, $Wait);
					}
					$count++;
				}
	
			} else {
				if ($level < $Retry) { 
					$this->connect($this->server, $this->port);
					if ($this->Errno == 0) {
						$this->SendPacket($Info, $Packet, $Wait, $Retry, $level + 1);
					} else {
						$this->Errno = "-2";
						$this->Error = "Bad Socket not open";
						add_debug_output("Bad Socket (".$this->socket.")<br>\n");
						if ($this->printdebug === TRUE) $this->debugtext .= "Bad Socket (".$this->socket.")<br>\n";
					}
				} else {
					add_debug_output("Too much recursion\n");
					if ($this->printdebug === TRUE) $this->debugtext .= "Too much recursion\n";
				}
			}
		}
		if (isset($this->Packets[0])) {
			return($this->Packets[0]);
		} else {
			return(FALSE);
		}
	}

	/**
		@private
		@brief Waits for an "ack" to get returned from the daemon
		@param $timeout timeout for waiting.  Default is used if timeout == 0	
	*/
	function GetAck($timeout=0) {
		add_debug_output("Waiting for an Ack<BR>\n");
		if ($this->printdebug === TRUE) $this->debugtext .= "Waiting for an Ack<BR>\n";
		if ($timeout == 0) $timeout = $this->AckTimeout;
		$Start = time();

		$string = "";
		while ((time() - $Start) < $timeout) {
		
			$char = @fread($this->socket, 1);
			if ($char !== FALSE) {
				$string .= $char;
//print $string."\n";
			}
			if (stristr($string, "ack")) {
//				print " Got Ack ";
				add_debug_output("Got Ack<BR>\n");
				if ($this->printdebug === TRUE) $this->debugtext .= "<p>Got Ack<BR>\n";

				return(TRUE);
				break;
			}
			usleep(10000);
		}
//		print " No Ack ";
		add_debug_output("No Ack<BR>\n");
		if ($this->printdebug === TRUE) $this->debugtext .= "No Ack<BR>\n";
		return(FALSE);
	}
	/**
		@private
		@brief Waits for an packet to get returned from the daemon
		@param $Packet The data packet from the endpoint
		@param $timeout timeout for waiting.  Default is used if timeout == 0	
	*/
	function RecvPacket($Packet, $timeout=0) {
		add_debug_output("Waiting for a reply<BR>\n");
		if ($this->printdebug === TRUE) $this->debugtext .= "<p>Waiting for a reply<BR>\n";

		if ($timeout == 0) $timeout = $this->PacketTimeout;
		$Start = time();

		$string = "";
		while ((time() - $Start) < $timeout) {
			$char = @fread($this->socket, 1);
			if ($char !== FALSE) {
				$string .= $char;
			}
			if (stristr($string, "End")) {
				add_debug_output("Got a reply:<BR>\n");
				if ($this->printdebug === TRUE) $this->debugtext .= "<b>Got a reply:</b><BR>\n";
				$string = str_replace("\r", "", $string);
				add_debug_output(nl2br($string));
				if ($this->printdebug === TRUE) $this->debugtext .= nl2br(trim($string))."<BR>\n";
				$return = $this->InterpReturn($string, $Packet);
				return($return);
				break;
			} else if (stristr($string, "Exit")) {
				return(TRUE);
				break;
			}
			usleep(10000);
		}

//		$string = explode("\n", $string);
		add_debug_output("Failed to get a reply:<BR>\n");
		return(FALSE);
	}

	/** 
		@private
		@brief This function puts return packets into the $this->Packet array
		@param $data The raw packet data
		@param $Packet The data packet from the endpoint
	*/
	function InterpReturn($data, $Packet) {
		$Index = 0;
		$this->Packets = array();
		$return = FALSE;
		if (trim($data) == "") {
			$this->Errno = PACKET_ERROR_NOREPLY_NO;
			$this->Error = PACKET_ERROR_NOREPLY;
		} else {
			$EP = explode("\n", $data);
			$Index = 0;
			foreach($EP as $value) {
				if ($value != "") {
					if (trim(strtolower($value)) == "end") {
						if (isset($Packet["To"])) {
							if (strtoupper(trim($this->Packets[$Index]["from"])) == strtoupper(trim($Packet["To"]))) {
								$Index++;
							} else {
								unset($this->Packets[$Index]);
							}
						} else {
							$Index++;
						}
					} else if (trim(strtolower($value)) == "begin") {
						// Do nothing here.
					} else if (trim(strtolower($value)) == "bad") {
						$this->Packets["errno"] = -1;
						$this->Packets["error"] = "Bad Gateway Command";
					} else {
						$return = TRUE;
						$pair = explode(":", $value);
						if (trim(strtolower($pair[0])) != "data") {
							$this->Packets[$Index][strtolower($pair[0])] = trim($pair[1]);
						} else {
							$this->Packets[$Index]["rawdata"] = $pair[1];
						}
					}
				}
			}
		
			if ($this->Packets[0]["command"] == "FF") {
				$this->Errno = PACKET_ERROR_BADC_NO;
				$this->Error = PACKET_ERROR_BADC;
			} else {
				if (count($this->Packets) > 0) {
					add_debug_output("Interpreted as:<BR>\n");
					add_debug_output(get_stuff($this->Packets));
					
					if ($this->printdebug === TRUE) $this->debugtext .= "<b>Interpreted as:</b><BR>\n";
					if ($this->printdebug === TRUE) $this->debugtext .= get_stuff($this->Packets)."\n";

//print get_stuff($this->Packets);				
					$this->Errno = 0;
					$this->Error = "";
				} else {
					$this->Errno = PACKET_ERROR_NOREPLY_NO;
					$this->Error = PACKET_ERROR_NOREPLY;
					$return = FALSE;
				}
			}
		}
		return($return);
	}
	
	/**
		@public
		@brief Sends a ping out to the desired device
		@param $Info Device information array
		@return Returs TRUE if it got a reply, FALSE otherwise
	*/
	function ping($Info) {
		add_debug_output("Pinging ".$Info["DeviceID"]."<BR>\n");
		if ($this->printdebug === TRUE) $this->debugtext .= "Pinging ".$Info["DeviceID"]."<BR>\n";

		$pkt["Command"] = "02";
		$pkt["To"] = $Info["DeviceID"];
		$this->SendPacket($Info, $pkt);
		if (strtoupper($this->Packets[0]["from"]) == strtoupper($pkt["To"])) {
			return(TRUE);
		} else {
			return(FALSE);
		}	
	}
		
	/**
		@public
		@brief Gets the packetstats from a gateway
		@param $Info Device information array
		@param $iface the interface to use
		@return Returs the data array if it got a reply, FALSE otherwise
	*/
	function GetPacketStats($Info, $iface = 0) {
		$this->SendPacket($Info, array("PacketStat"=>$iface));
//		$this->close();
		if (isset($this->Packets[0])) {
			return ($this->Packets[0]);
		} else {
			return(FALSE);
		}
	}


	/**
		@public
		@brief Gets the packetstats from a gateway
		@param $Info Device information array
		@return Returs the data array if it got a reply, FALSE otherwise
	*/
	function GetGatewaySN($Info) {
		$this->SendPacket($Info, array("SN"=>""), 2, 0);
//		$this->close();
		if (isset($this->Packets[0])) {
			return ($this->Packets[0]);
		} else {
			return(FALSE);
		}
	}

	/**
		@public
		@brief Gets the packetstats from a gateway
		@param $Info Device information array
		@param $Ident  Identity string
		@param $PingKey 
		@return Returs the data array if it got a reply, FALSE otherwise
	*/
	function GatewayPingStat($Info, $Ident, $PingKey) {
		$this->SendPacket($Info, array("pingstat"=>$this->hexify($PingKey), "ident" => $Ident));
//		$this->close();
		if (isset($this->Packets[0])) {
			return ($this->Packets[0]);
		} else {
			return(FALSE);
		}
	}
	/**
		@public
		@brief Gets the packetstats from a gateway
		@param $Info Device information array
		@return Returs the data array if it got a reply, FALSE otherwise
	*/
	function GatewayClientStat($Info) {
		$this->SendPacket($Info, array("clientstat"=>""));
//		$this->close();
		if (isset($this->Packets[0])) {
			return ($this->Packets[0]);
		} else {
			return(FALSE);
		}
	}
			
	/**
		@public
		@brief Gets the packetstats from a gateway
		@param $Info Device information array
		@return Returs the data array if it got a reply, FALSE otherwise
	*/
	function GetUnsolicited($Info) {
		$this->SendPacket($Info, array("unsolicited"=>""));
		if (isset($this->Packets[0])) {
			return ($this->Packets[0]);
		} else {
			return(FALSE);
		}
	}
		
	/**
		@public
		@brief Finds a device among  the gateways.
		@param $Info Device information array
		@param $gateways The array of gateway information so we know where to look
		@return Returs the gateway information array if it got a reply, FALSE otherwise
	*/
	function FindDevice($Info, $gateways) {
		add_debug_output("Finding Device ".$Info["DeviceID"]."<BR>\n");
		if ($this->printdebug === TRUE) $this->debugtext .= "Finding Device ".$Info["DeviceID"]."<BR>\n";

		if (isset($Info["GatewayIP"]) && isset($Info["GatewayPort"])) {
			add_debug_output("Trying Gateway ".$Info["GatewayName"]."<BR>\n");
			if ($this->printdebug === TRUE) $this->debugtext .= "Trying Gateway ".$Info["GatewayName"]."<BR>\n";

			$this->connect($Info["GatewayIP"], $Info["GatewayPort"]);	
			if ($this->ping($Info)) {
				return($Info);
			}
		}
		foreach ($gateways as $gw) {
			add_debug_output("Trying Gateway ".$gw["GatewayName"]."<BR>\n");
			if ($this->printdebug === TRUE) $this->debugtext .= "Trying Gateway ".$gw["GatewayName"]."<BR>\n";
			if ($gw["GatewayKey"] != $oldgw["GatewayKey"]) {
				$this->connect($gw["GatewayIP"], $gw["GatewayPort"]);
				$gw["DeviceID"] = $Info["DeviceID"];
				if ($this->ping($gw)) {
					return($gw);
				}
			}
		}
		return(FALSE);	
	}

	/**
		@brief Closes the socket connection
	*/
	function forceclose() {
		if ($this->socket != 0) {
//			$retval = fwrite($this->socket, "Exit\r\n");
			add_debug_output("Closing Connection<BR>\n");
			if ($this->printdebug === TRUE) $this->debugtext .= "Closing Connection<BR>\n";

			fclose($this->socket);
			$this->socket = 0;
		}
	}

	/**
		@brief Closes the socket connection
	*/
	function close() {
		if ($this->socket != 0) {
//			$retval = fwrite($this->socket, "Exit\r\n");
			@fwrite($this->socket, "exit\r\n");
			add_debug_output("Exiting<BR>\n");
			if ($this->printdebug === TRUE) $this->debugtext .= "Exiting<BR>\n";

			$this->forceclose();
		}
	}

	/**
		@brief Checks to make sure that we are connected to the correct server.
	*/

	function checkConnect($server = "", $port = 0) {
		add_debug_output("Checking ".$server.":".$port." against '".$this->server."':".$this->port."<br>\n");	
		if ($this->printdebug === TRUE) $this->debugtext .= "Checking ".$server.":".$port." against '".$this->server."':".$this->port."<br>\n";

		if (($this->server != $server) || ($this->port != $port)) {
			$this->connect($server, $port);
		}
		
	}
	
	/**
		@brief Connects to the server
	*/
	function connect($server = "", $port = 0) {
		$this->Packets = array();
		if ($this->socket != 0) $this->forceclose();
		if (!empty($server)) $this->server = $server;
		if (!empty($port)) $this->port = $port;

		if (!empty($this->server) && !empty($this->port)) {

			$this->socket = @fsockopen($this->server, $this->port, $this->Errno, $this->Error, $this->SockTimeout);
			if (($this->Errno == 0) && ($this->socket != 0)) {
				stream_set_blocking($this->socket, FALSE);
				add_debug_output("Opened the Socket ".$this->socket." to ".$this->server."<BR>\n");
				if ($this->printdebug === TRUE) $this->debugtext .= "Opened the Socket ".$this->socket." to ".$this->server."<BR>\n";

			} else {
				add_debug_output("<B>Connection to ".$server." Failed. Error ".$this->Errno.":</B> ".$this->Error."<BR>\n");
				if ($this->printdebug === TRUE) $this->debugtext .= "<B>Connection to ".$server." Failed. Error ".$this->Errno.":</B> ".$this->Error."<BR>\n";
				$this->socket = 0;
			}
		} else {
			$this->Errno = -1;
			$this->Error = "No server specified";
		}
	}			


	/**
		@brief Constructor	
		@param $Info
	*/
	function endpoint($Info="") {

	}
	
	/**
		@brief  Turn a decimal number into a hex string
		@param $value
		@param $width
	*/
	function hexify($value, $width=2) {
		return(str_pad(hexdec($value), $width, "0", STR_PAD_LEFT));
	}
}





?>
