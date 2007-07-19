<?php
/**
	$Id$

	@file packet.inc.php
	@brief Class for talking with endpoints

	
*/

/** @brief The placeholder for the Acknoledge command */
define("PACKET_COMMAND_ACK", "01");
/** @brief The placeholder for the Echo Request command */
define("PACKET_COMMAND_ECHOREQUEST", "02");
/** @brief The placeholder for the Capabilities command */
define("PACKET_COMMAND_GETCALIBRATION", "4C");
/** @brief The placeholder for the Capabilities command */
define("PACKET_COMMAND_GETCALIBRATION_NEXT", "4D");
/** @brief The placeholder for the Capabilities command */
define("PACKET_COMMAND_GETSETUP", "5C");
define("PACKET_COMMAND_GETSETUP_GROUP", "DC");
/** @brief The placeholder for the Capabilities command */
define("PACKET_COMMAND_GETDATA", "55");
/** @brief The placeholder for the Bad Command command */
define("PACKET_COMMAND_BADC", "FF");
/** @brief The placeholder for the Read E2 command */
define("PACKET_COMMAND_READE2", "0A");
/** @brief The placeholder for the Write E2 command */
define("PACKET_COMMAND_READRAM", "0B");
/** @brief The placeholder for the reply command */
define("PACKET_SETRTC_COMMAND", "50");
/** @brief The placeholder for the reply command */
define("PACKET_READRTC_COMMAND", "51");
/** @brief The placeholder for the Write E2 command */
define("PACKET_COMMAND_POWERUP", "5E");
/** @brief The placeholder for the Write E2 command */
define("PACKET_COMMAND_RECONFIG", "5D");
/** @brief The placeholder for the reply command */
define("PACKET_COMMAND_REPLY", PACKET_COMMAND_ACK);


/** @brief This is the smallest config data can be */
define("PACKET_CONFIG_MINSIZE", "36");



/** @brief Error number for not getting a packet back */
define("PACKET_ERROR_NOREPLY_NO", -1);
/** @brief Error message for not getting a packet back */
define("PACKET_ERROR_NOREPLY", "Board failed to respond");
/** @brief Error number for not getting a packet back */
define("PACKET_ERROR_BADC_NO", -2);
/** @brief Error message for not getting a packet back */
define("PACKET_ERROR_BADC", "Board responded: Bad Command");
/** @brief Error number for not getting a packet back */
define("PACKET_ERROR_TIMEOUT_NO", -3);
/** @brief Error message for not getting a packet back */
define("PACKET_ERROR_TIMEOUT", "Timeout waiting for reply");
/** @brief Error number for not getting a packet back */
define("PACKET_ERROR_BADC_NO", -4);
/** @brief Error message for not getting a packet back */
define("PACKET_ERROR_BADC", "Board responded: Bad Command");


/** Error Code */
define("DRIVER_NOT_FOUND", 1);
/** Error Code */
define("DRIVER_NOT_COMPLETE", 2);

if (!class_exists(ep_socket)) {
	require_once("socket.inc.php");
}


/**
	@brief Class for talking with HUGNet endpoints

	This class implements the packet structure for talking with endpoints.
*/
class EPacket {
	/** This contains all of the endpoints we know about */
	var $EndPoints = array();
	/** This is the packet buffer */
	var $Packets = array();
	/** How many times we retry the packet until we get a good one */
	var $Retries = 2;
	/** This is the socket descriptor */
	var $socket = array();
	/** The error number.  0 if no error occurred */
	var $Errno = 0;
	/** The error string */
	var $Error = "";
	/** The timeout for waiting for a packet in seconds */
	var $ReplyTimeout = 5;
	/** The timeout for waiting for a packet in seconds */
	var $IndirectReplyTimeout = 30;
	/** The timeout for waiting for a packet in seconds */
	var $AckTimeout = 2;
	/** The timeout for waiting for a packet in seconds */
	var $SockTimeout = 2;
	/** Infomation about various endpoints */
	var $verbose = FALSE;			//!< Whether to be verbose
	var $preambleByte = "5A";		//!< The preamble byte
	var $SN = "000020";				//!< The default serial number to use
	var $maxSN = 0x20;				//!< The default serial number to use

	var $_getAll = FALSE;			//!< Whether or not to return ALL packets received.
	var $_getUnsolicited = FALSE; //!< Whether or not to return unsolicited packets.
	var $unsolicitedID = '000000'; //!< The ID that unsolicited packets will be sent to.

    var $callBackObject = NULL;
    var $callBackFunction = NULL;

    var $_direct = TRUE;        //!< This is for if we should be going directly to the endpoint or through the database.

    var $_SNCheck = TRUE;

	/**
		@private
		@brief Builds a packet
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
	function PacketBuild($Packet) {
		if (!is_array($Packet)) $Packet = array();
		$string = "";
		$Packet = array_change_key_case($Packet, CASE_LOWER);
		$return = FALSE;
		$Packet["to"] = trim($Packet["to"]);
		$Packet["to"] = substr($Packet["to"], 0, 6);
		if (strlen($Packet["to"]) > 0) {
			$Packet["to"] = str_pad($Packet["to"], 6, "0", STR_PAD_LEFT);
			$string = $Packet["command"];
			$string .= $Packet["to"];
			$string .= substr(trim($this->SN), 0, 6);
			$string .= sprintf("%02X", (strlen($Packet["data"])/2));
			$string .= $Packet["data"];
			$return = $this->preambleByte.$this->preambleByte.$this->preambleByte.$string.$this->PacketGetChecksum($string);
		}
		return($return);
	}
	
	function deHexify($string) {
	    $string = trim($string);
	    $bin = "";
        for($i = 0; $i < strlen($string); $i+=2) {
            $bin .= chr(hexdec(substr($string, $i, 2)));
        }
        return $bin;
	}
	/**
		@private
		@brief Computes the checksum of a packet
		@param PktStr String The raw packet string
		@return Hex String The checksum
	*/
	function PacketGetChecksum($PktStr) {
		$chksum = 0;
		for($i = 0; $i < strlen($PktStr); $i+=2) {
			$val = hexdec(substr($PktStr, $i, 2));
			$chksum ^= $val;
		}
		$return = sprintf("%02X", $chksum);
		return($return);
	}
	
    function buildPacket($to, $command, $data="") {
        $pkt = array(
            'To' => $to,
            'Command' => $command,
            'Data' => $data,
        );
        return $pkt;
    }	

    function packetSetCallBack($function, &$object = NULL) {
        $this->callBackFunction = $function;
        $this->callBackObject = &$object;
    }

    function packetCallBack($pkt) {
        if ($this->verbose) print "Checking for Callback Function...  ";
        $function = $this->callBackFunction;
        if ($this->verbose) print " ".$function." ";
        if (is_object($this->callBackObject)) {
            if ($this->verbose) print " ".get_class($this->callBackObject)." ";
            if (method_exists($this->callBackObject, $function)) {
                if ($this->verbose) print " Calling ".get_class($this->callBackObject)."->".$function;
                $this->callBackObject->$function($pkt);
            }
        } else {
            if (function_exists($function)) {
                if ($this->verbose) print "Calling ".$function;
                $function($pkt);
            }
        }
        if ($this->verbose) print " Done\r\n";
    }
	
	/**
		@brief Sends out a packet
		@param $Info The array with the device information in it
		@param $Packet Array with packet information in it.
		@param $GetReply Whether or not to wait for a reply.
		@return FALSE on failure, TRUE on success
	*/
	function SendPacket($Info, $PacketList, $GetReply=TRUE, $pktTimeout = NULL) {
        if ($pktTimeout === NULL) {
            if (!is_null($Info['PacketTimeout'])) {
                $pktTimeout = $Info['PacketTimeout'];
            } else {
                $pktTimeout = $this->ReplyTimeout;
            }
        }
        if (!is_array($PacketList)) return FALSE;

        if (isset($Info['GatewayKey'])) {
    		$socket = $Info['GatewayKey'];
        } else {
            // If one is not given, use the first one.
            reset($this->socket);
            list($socket, $tmp) = each($this->socket);
        }
        if ($this->_direct) {
    		$useSocket = &$this->socket[$socket];
        } else {
            $useSocket = &$this->_db;
        }
		if ($this->verbose) print("Sending a packet on ".$Info["GatewayIP"].":".$Info["GatewayPort"]."\n");
		$gotack = FALSE;
		$this->Packets[$socket] = array();
		$PacketList = array_change_key_case($PacketList, CASE_LOWER);
		if (isset($PacketList['to'])) {
			$PacketList = array($PacketList);
		} else {
			foreach($PacketList as $key => $p) {
				if (is_array($p)) {
					$PacketList[$key] = array_change_key_case($p, CASE_LOWER);
				} else {
					unset($PacketList[$key]);
				}
			}
		}
		// Make sure we are connected.
		$this->Connect($Info);

		$return = FALSE;
		foreach($PacketList as $Packet) {
			$group = (bool)(hexdec($Packet["command"]) & 0x80);
			$index = $this->_index++;

            if (empty($Packet['data'])) $Packet['data'] = "";
			$this->Packets[$index] = array(
                "pktTimeout" => $pktTimeout,
			    "GetReply" => $GetReply, 
				"SentTime" => $this->PacketTime(),
				"SentFrom" => trim(strtoupper($this->SN)),
				"SentTo" => trim(strtoupper($Packet["to"])),
				"sendCommand" => trim(strtoupper($Packet['command'])),
				'group' => $group,
				'packet' => $Packet,
                "PacketTo" => trim(strtoupper($Packet["to"])),
                "Date" => date("Y-m-d H:i:s"),
                "GatewayKey" => $Info['GatewayKey'],
                "DeviceKey" => $Info['DeviceKey'],
                "Type" => "OUTGOING",
                "RawData" => trim(strtoupper($Packet['data'])),
                "sentRawData" => trim(strtoupper($Packet['data'])),
                "Parts" => (empty($Packet['Parts']) ? 1 : $Packet['Parts']),
			);
			$count = 0;
			$GotReply = FALSE;
			$sPktStr = $this->PacketBuild($Packet);
			$PktStr = $this->deHexify($sPktStr);
			do {
				if (is_object($useSocket)) {
				    if ($this->verbose) print "Sending: ".$sPktStr."\n";
                    if ($this->_direct) {
    					$retval = $useSocket->Write($PktStr);
    			    } else if (is_object($this->_db)) {
                        $this->Packets[$index]["id"] = rand(-24777216, 24777216);  // Big random number for the id

                        $retval = $this->_db->AutoExecute("PacketSend", $this->Packets[$index], 'INSERT');
                    } else {
                        if ($this->verbose) print "Sending Failed.\n";
                    }
				}
				if ($retval === FALSE) {
					$this->Connect($Info);
				}
	
				if ($retval) {
				    if (!$GetReply) {
				        $GotReply = TRUE;
				        $return = TRUE;
				    }
					while (!$GotReply){
						$gotack = $this->RecvPacket($socket, $pktTimeout);
						if ($gotack === FALSE) break;
						if (is_array($gotack)) {
							$return[] = $gotack;
							if ($gotack['Reply'] === TRUE) $GotReply = TRUE;
						}
					};
				}
				
				$count++;
			} while (($count < $this->Retries) && !$GotReply);
			unset($this->Packets[$index]);
			
		}
		return($return);

	}
    /**
        @brief Sends a reply packet
    */
    function sendReply($Info, $to, $data=NULL) {
        if (!is_string($to)) $to = $this->hexify($to, 6);
        
        if (is_array($data)) $data = $this->arrayToData($data);        
        
        $pkt = array(
            'To' => $to,
            'Data' => $data,
            'Command' => PACKET_COMMAND_REPLY,
        );
        return $this->sendPacket($Info, array($pkt), FALSE);
    }

    function arrayToData($array) {
        if (is_array($array)) {
            $ret = '';
            foreach($array as $d) {
                $ret .= $this->hexify($d, 2);
            }
        } else if (is_string($array)) {
            return $array;
        } else {
            return '';
        }
    }
    
    function ChangeSN($Info = NULL) {
        if (!is_array($Info)) $Info = array();
        $getAll = $this->_getAll;
        $this->_getAll = FALSE;
        do {
            do {
                $SN = $this->hexify(mt_rand(1,$this->maxSN), 6);
            } while ($SN == $this->SN);
            $Info = array("DeviceID" => $SN);
            if ($this->verbose) print "Checking Serial Number ".$SN."\r\n";
            $ret = $this->ping($Info);
        } while (is_array($ret));
        $this->SN = $SN;
        if ($this->verbose) print "Using Serial Number ".$this->SN."\r\n";
        $this->_getAll = $getAll;
    }    
    
	/**
		@private
		@brief Gets the current time
		@return Float The current time in seconds	
	*/
	function PacketTime() {
	   list($usec, $sec) = explode(" ", microtime());
	   return ((float)$usec + (float)$sec);
	} 


	/**
		@private
		@brief Gets the current time
		@return Float The current time in seconds	
	*/

    function RecvPacket($socket, $timeout=0) {
        if ($this->_direct) {
            $return = $this->_RecvPacket_Direct($socket, $timeout);
        } else {
            $return = $this->_RecvPacket_Indirect($socket, $timeout);        
        }
        return $return;
    }


	function _RecvPacket_Indirect($socket, $timeout=0) {

		if (empty($timeout)) $timeout = $this->IndirectReplyTimeout;
		if (!is_array($this->buffers)) $this->buffers = array();
		$Start = time();
		$return = FALSE;
        if (!is_object($this->_db)) return FALSE;
        do {
            $query = "SELECT * FROM PacketSend WHERE Type = 'REPLY' ";
            $res = $this->_db->getArray($query);
            if (is_array($res)) {
                foreach($res as $key => $record) {
                    foreach($this->Packets as $index => $pkt) {
                        if ($record['id'] == $pkt['id']) {
                            $return = array_merge($pkt, $record);
     						$return["ReplyTime"] = $return["Time"] - $return["SentTime"]; 
    						$return["Socket"] = $socket;
    						$return["sendCommand"] = $ThePkt['sendCommand'];
    						$return["Reply"] = TRUE;
    	        			$return["toMe"] = TRUE;
    						$return["isGateway"] = $this->isGateway($return["From"]);                              
                        }
                    }
                }
            }
   			$time = (time() - $Start);
   			if ($return === FALSE) sleep(2);

		} while (($time < $timeout) && ($return === FALSE)) ;
		if ($return === FALSE) {
			if ($this->verbose) print "Packet Timeout\r\n";
		} else {
            $query = "DELETE FROM PacketSend WHERE id = '".$return['id']."' ";
            $this->_db->execute($query);
			if ($this->verbose) {
				print "Got Reply on Socket ".$socket." in ".$GotReply["ReplyTime"]."s \r\n";
			}
		}
		return($return);

    }


	/**
		@private
		@brief Waits for an packet to get returned from the daemon
		@param $socket Integer The socket to send it out of.  0 is the default.
		@param $timeout Integer timeout for waiting.  Default is used if timeout == 0	
		@return FALSE on failure, the Packet array on success
	*/
	function _RecvPacket_Direct($socket, $timeout=0) {
		if (empty($timeout)) $timeout = $this->ReplyTimeout;
		if (!is_array($this->buffers)) $this->buffers = array();
		$Start = time();
		$GotReply = FALSE;
		$return = FALSE;
		$mySocket = &$this->socket[$socket];
        if (!is_object($mySocket)) return FALSE;

		do {	
			$char = $mySocket->ReadChar($timeout);
			if ($char !== FALSE) {
			    $char = ord($char);
			    $char = $this->hexify($char);
    			$mySocket->buffer .= strtoupper(trim($char));
    			$pkt = stristr($mySocket->buffer, $this->preambleByte.$this->preambleByte);
    			if (strlen($pkt) > 11) {
    				while (substr($pkt, 0, 2) == $this->preambleByte) $pkt = substr($pkt, 2);
    				// Location 14 is the length of the data section.
    				$len = hexdec(substr($pkt, 14, 2));
    				if (strlen($pkt) >= ((9 + $len)*2)) {
    					$pkt = substr($pkt, 0, (9+$len)*2);
    					$GotReply = $this->InterpPacket($pkt, $socket);
    					if ($this->verbose) print "Got Pkt: ".$mySocket->buffer." on ".$socket."\r\n";
    					$mySocket->buffer = "";
    				}
    			}
    	    }
   			$time = (time() - $Start);
		} while (($time < $timeout) && ($GotReply === FALSE)) ;

		if ($GotReply === FALSE) {
			if (strlen($mySocket->buffer) > 0) {
				if ($this->verbose) print "Got Other Stuff: ".$mySocket->buffer." on ".$socket."\r\n";
                // This line clips off anything before the start of a packet.
                $mySocket->buffer = stristr($mySocket->buffer, "5A");
                $mySocket->buffer = "";
			}
			if ($this->verbose) print "Packet Timeout\r\n";
		} else {
			if ($this->verbose) {
				if ($GotReply['Unsolicited']) {
					print "Got Unsolicited Packet on Socket ".$socket.".\r\n";
				} else {
					print "Got Reply on Socket ".$socket." in ".$GotReply["ReplyTime"]."s \r\n";
				}
			}
			$return = $GotReply;
		}
		return($return);

	}

    function isGateway($sn) {
        if (is_string($sn)) {
            $sn = hexdec($sn);
        }
        return (($sn < $this->maxSN) && ($sn > 0));
    }

    function getAll($val = TRUE) {
        $this->_getAll = $val;
    }

	/** 
		@private
		@brief This function puts return packets into the $this->Packet array
		@param $data String The raw packet data
		@param $sock Integer The socket we got the data on
		@return FALSE on failure, The interpreted packet on success.
	*/
	function InterpPacket($data, $sock=0) {
		$return = FALSE;
/** @brief The placeholder for the reply command */
//define("PACKET_COMMAND_REPLY", PACKET_COMMAND_ACK);

		$pkt = $this->UnbuildPacket($data);
		if ($pkt["Checksum"] == $pkt["CalcChecksum"]) {
            if ($pkt['Command'] == PACKET_COMMAND_REPLY) {
    			if (is_array($this->Packets)) {
    				foreach($this->Packets as $key => $ThePkt) {
    					$toMe = (bool)(($ThePkt["SentTo"] == $pkt["From"]) || $ThePkt['group']);
    					$fromMe = (bool)($ThePkt["SentFrom"] == $pkt["To"]);
    					// Make sure that it is really a reply to me.
    					if ($toMe && $fromMe ) {
    						$return = array_merge($this->Packets[$key], $pkt);
    						$return["ReplyTime"] = $return["Time"] - $return["SentTime"]; 
    						$return["Socket"] = $sock;
    						$return["GatewayKey"] = $sock;
    						$return["sendCommand"] = $ThePkt['sendCommand'];
    						$return["Reply"] = TRUE;
    	        			$return["toMe"] = TRUE;
    						$return["isGateway"] = $this->isGateway($return["From"]);
    						for ($i = 0; $i < (strlen($return["RawData"])/2); $i++) {
    							$return["Data"][] = hexdec(substr($return["RawData"], ($i*2), 2));
    						}
    
    						unset($this->Packets[$key]);
    						break;
    					} else {
    					    $timeout = (isset($ThePkt['pktTimeout'])) ? $ThePkt['pktTimeout'] : $this->ReplyTimeout;
    					    if (($this->PacketTime() - $ThePkt["SentTime"]) > $timeout) {
    //						if ($this->verbose) print "Not for us: ".$this->Packets[$key]."\r\n";
        						unset($this->Packets[$key]);
        					}
    					}
    				}
                }
			}

            if ($return["Reply"] !== TRUE) {
    			$pkt["Socket"] = $sock;
    			$pkt["GatewayKey"] = $sock;
    			$pkt["Reply"] = FALSE;
    			$pkt["Unsolicited"] = (trim(strtoupper($pkt["To"])) == $this->unsolicitedID);
    			$pkt["toMe"] = (trim(strtoupper($pkt["To"])) == $this->SN);
	    		$pkt["isGateway"] = $this->isGateway($pkt["From"]);

                $this->packetCallBack($pkt);
		    		
    			if ($this->_getAll ||  ((trim(strtoupper($pkt["To"])) == $this->unsolicitedID) && $this->_getUnsolicited)) 
	    		{
    			    $return = $pkt;
    			}
    		}
		} else {
			if ($this->verbose) print "Bad Packet Received: ".$data."\r\n";
		}

		if ($return !== FALSE) $return['RawPacket'] = $data;
		return($return);
	}
	
	/**
		@private
		@brief Turns a packet string into an array of values.
		@param $data String to parse
		@return The packet array created from the string
	*/
	function UnbuildPacket($data) {
		// Strip off any preamble bytes.
		while (substr($data, 0, 2) == $this->preambleByte) $data = substr($data, 2);
		$pkt = array();
		$pkt["Command"] = substr($data, 0, 2);
		$pkt["To"] = trim(strtoupper(substr($data, 2, 6))); 
		$pkt["From"] = trim(strtoupper(substr($data, 8, 6)));
		$length = hexdec(substr($data, 14, 2));
		$pkt["Length"] = $length;
		$pkt["RawData"] = substr($data, 16, ($length*2));
		$pkt["Checksum"] = trim(strtoupper(substr($data, (16 + ($length*2)), 2)));
		$pktdata = substr($data, 0, strlen($data)-2);
		$pkt["CalcChecksum"] = trim(strtoupper($this->PacketGetChecksum($pktdata)));
		$pkt["Time"] = $this->PacketTime();
		return($pkt);
	}
	
	
	
	/**
		@brief Waits for unsolicited packets.
		@param $InfoArray Array of gateway info arrays
		@param $from String the packet from address to look for
		@return returns the packet it got.
	*/
	function GetUnsolicited($InfoArray, $from="000000") {
		$this->_getUnsolicited = TRUE;
		$this->unsolicitedID = $from;
		if ($this->verbose) print("Waiting on Packets from ".$Info["GatewayKey"].":".$Info["GatewayPort"]."\n");
		foreach($InfoArray as $Info) {
			$return = $this->Connect($Info);
			if ($return) {
				$this->socket[$socket]->SetTimeout(120, $Info["GatewayKey"]);
			}
		}
		$return = $this->RecvPacket(-1);
		if ($return !== FALSE) $return = array($return);

		return($return);
	}

	/**
		@brief Waits for unsolicited packets.
		@param $InfoArray Array of gateway info arrays
		@param $from String the packet from address to look for
		@return returns the packet it got.
	*/
	function monitor($Info, $timeout = 0) {
	    $this->_getAll = TRUE;
		if ($this->verbose) print("Waiting on Packets from ".$Info["GatewayName"].":".$Info["GatewayPort"]."\n");
		$return = $this->Connect($Info);
		$return = $this->RecvPacket($Info['GatewayKey'], $timeout);
	    $this->_getAll = FALSE;
		return($return);
	}
	
	
	/**
		@brief Sends a ping out to the desired device
		@param $Info Device information array
		@return Returns TRUE if it got a reply, FALSE otherwise
	*/
	function ping($Info, $find=FALSE) {
		if ($this->verbose) print("Pinging ".$Info["DeviceID"]."\n");

        if ($find) {
    		$pkt["Command"] = "03";
        } else {
    		$pkt["Command"] = "02";
    	}
		$pkt["To"] = $Info["DeviceID"];
		$return = $this->SendPacket($Info, $pkt);		
		
		return($return);
	}
		

	/**
		@brief Finds a device among  the gateways.
		@param $Info Device information array
		@param $gateways The array of gateway information so we know where to look
		@return Returns the gateway information array if it got a reply, FALSE otherwise
	*/
	function FindDevice($Info, $gateways) {
		if ($this->verbose) print("Finding Device ".$Info["DeviceID"]."\n");

		if (isset($Info["GatewayIP"]) && isset($Info["GatewayPort"])) {
			if ($this->verbose) print("Trying Gateway ".$Info["GatewayName"]."\n");

			$this->Connect($Info);	
			if ($this->ping($Info)) {
				return($Info);
			}
		}
		foreach ($gateways as $gw) {
			if ($this->verbose) print("Trying Gateway ".$gw["GatewayName"]."		\n");
			if ($gw["GatewayKey"] != $oldgw["GatewayKey"]) {
				$this->Connect($gw);
				$gw["DeviceID"] = $Info["DeviceID"];
				if ($this->ping($gw)) {
					return(array_merge($Info, $gw));
				}
			}
		}
		return(FALSE);	
	}
	
	


	/**
		@brief Connects to the gateway specified in $Info
		@param $Info Array Infomation about the device to use			
		@return Boolean FALSE on failure, TRUE on success

		Check if it is connected.  If not it connects to the gateway specified in $Info.
	*/
	function Connect($Info) {
	    if ($this->_direct) {
    		if (isset($Info["GatewayIP"]) && isset($Info["GatewayKey"]) && isset($Info["GatewayPort"])) {
                $sock = $Info['GatewayKey'];
            	if (!is_object($this->socket[$sock])) {
            	    $this->socket[$sock] = new ep_socket("", 0, 0, $verbose);
            	    $this->socket[$sock]->verbose = $this->verbose;
                }
    			$return = $this->socket[$sock]->Connect($Info["GatewayIP"], $Info["GatewayPort"]);
                if ($this->_SNCheck) {
        			if (($this->SN == FALSE) || ((hexdec($this->SN) >= $this->maxSN) || (hexdec($this->SN) < 1))) {
        				if ($return === FALSE) {
                            $this->ChangeSN($Info);
        //                    $this->SN = $this->hexify(mt_rand(1,$this->maxSN), 6);
        //					$this->SN = strtoupper(str_pad(dechex(mt_rand(1,32)), 6, "0", STR_PAD_LEFT));
        				} else {
                            $this->ChangeSN($Info);
        //                    $this->SN = $this->hexify(mt_rand(1,$this->maxSN), 6);
        //					$this->SN = strtoupper(str_pad(dechex(mt_rand(1,32)), 6, "0", STR_PAD_LEFT));
        				}
        				if ($this->verbose) print "Using Serial Number ".$this->SN."\r\n";
        				// Put something here
        			}
                }
    		} else {
    			if ($this->verbose) print "GatewayIP, GatewayKey, and GatewayPort must be defined.\r\n";
    			$return = FALSE;
    		}
        } else {
            // Nothing needed here.  The software just has to think it is connected
           $query = "DELETE FROM PacketSend WHERE Date < '".date("Y-m-d H:i:s", (time() - 300))."' ";
           $this->_db->execute($query);
           $return = TRUE;
        }
		return($return);
	}

	/**
		@brief Closes the connection to the specified gateway.	
		@param $Info Array Infomation about the device to use			
	*/
	function Close($Info) {
	    if (is_object($this->socket[$Info['GatewayKey']])) {
    		$this->socket[$Info['GatewayKey']]->close();
        }
	}

	/**
		@brief Constructor	
		@param $Info Array Infomation about the device to use
		@param $verbose Boolean If TRUE a lot of stuff is printed out

		If $Info is given it will try to connect to the server that is specified in it.
		
	*/
	function EPacket($Info=FALSE, $verbose=FALSE, &$db = NULL) {
        if (is_object($db)) {
            $this->_direct = FALSE;
            $this->_db = &$db;
        }

		$this->verbose = $verbose;
		if (is_array($Info)) {
			$this->Connect($Info);
		}
	}
	
	/**
		@brief Turns a number into a text hexidecimal string
		@param $value Integer The number to turn into a hex string
		@param $width Integer The width of the final string
		@return String The hex string created.
		
		If the number comes out smaller than $width the string is padded 
		on the left side with zeros.
	*/
	function hexify($value, $width=2) {
        $value = dechex($value);
        $value = str_pad($value, $width, "0", STR_PAD_LEFT);
        $value = substr($value, 0, $width);
        $value = strtoupper($value);

		return($value);
	}

	function hexifyStr($str, $width=NULL) {
        $value = "";
        $length = strlen($str);
	    if (is_null($width)) $width = $length;
	    for($i = 0; ($i < $length) && ($i < $width); $i++) {
	        $char = substr($str, $i, 1);
            $char = ord($char);
	        $value .= $this->hexify($char, 2);
	    }
        $value = str_pad($value, $width, "0", STR_PAD_RIGHT);
        
		return($value);
	}


    function SNCheck($val=TRUE) {
        $this->_SNCheck = (bool) $val;
    }

}

?>
