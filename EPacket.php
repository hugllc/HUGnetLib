<?php
/**
 *   HUGnet packet code
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
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage PacketStructure
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$    
 *
 */

/** The placeholder for the Acknoledge command */
define("PACKET_COMMAND_ACK", "01");
/** The placeholder for the Echo Request command */
define("PACKET_COMMAND_ECHOREQUEST", "02");
/** The placeholder for the Capabilities command */
define("PACKET_COMMAND_GETCALIBRATION", "4C");
/** The placeholder for the Capabilities command */
define("PACKET_COMMAND_GETCALIBRATION_NEXT", "4D");
/** The placeholder for the Capabilities command */
define("PACKET_COMMAND_GETSETUP", "5C");
define("PACKET_COMMAND_GETSETUP_GROUP", "DC");
/** The placeholder for the Capabilities command */
define("PACKET_COMMAND_GETDATA", "55");
/** The placeholder for the Bad Command command */
define("PACKET_COMMAND_BADC", "FF");
/** The placeholder for the Read E2 command */
define("PACKET_COMMAND_READE2", "0A");
/** The placeholder for the Write E2 command */
define("PACKET_COMMAND_READRAM", "0B");
/** The placeholder for the reply command */
define("PACKET_SETRTC_COMMAND", "50");
/** The placeholder for the reply command */
define("PACKET_READRTC_COMMAND", "51");
/** The placeholder for the Write E2 command */
define("PACKET_COMMAND_POWERUP", "5E");
/** The placeholder for the Write E2 command */
define("PACKET_COMMAND_RECONFIG", "5D");
/** The placeholder for the reply command */
define("PACKET_COMMAND_REPLY", PACKET_COMMAND_ACK);


/** This is the smallest config data can be */
define("PACKET_CONFIG_MINSIZE", "36");



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


/** Error Code */
define("DRIVER_NOT_FOUND", 1);
/** Error Code */
define("DRIVER_NOT_COMPLETE", 2);

if (!class_exists(epsocket)) {
    /** Make sure we have the socket interface */
    require_once("epsocket.php");
}


/**
 *   Class for talking with HUGNet endpoints
 *
 *   This class implements the packet structure for talking with endpoints.
 *   It can use a number of different methods to talk to an endpoint:
 *   1. <b>Indirect</b> This involves using the database.  The updatedb.php script
 *       reads the packet out of the database and feeds it to poll.php which sends it
 *       out.  The opposite happens for the return packet if there is one.  This is
 *       implemented because there can be only one connection to any particular serial
 *       port.  That means that the polling script is the only thing that can connect.
 *       therefore the polling script must send the packet out.
 *   2. <b>Socket</b> This method sends the data out a unix socket.  It requires a
 *       serial to ethernet adapter of some kind.  This can be either software like
 *       ser2net or hardware like any number of serial ethernet servers from places
 *       like {@link http://bb-elec.com/ B&B Electronics}.
 *
 *  @todo Add DirectIO support when it gets stabilized in PHP.
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

    /** Whether to be verbose */
    var $verbose = FALSE;

    /** The preamble byte */
    var $preambleByte = "5A";
    /** The default serial number to use */
    var $SN = "000020";
    /** The default serial number to use */
    var $maxSN = 0x20;

    /** Whether or not to return ALL packets received. */
    var $_getAll = FALSE;
    /** Whether or not to return unsolicited packets.*/
    var $_getUnsolicited = FALSE;
    /** The ID that unsolicited packets will be sent to. */
    var $unsolicitedID = '000000'; 

    /** Object for the incoming packet callback */
    var $callBackObject = NULL;
    /** Method to use on above object (if it is an object) */
    var $callBackFunction = NULL;

    /** This is for if we should be going directly to the endpoint or through the database.
    var $_direct = TRUE;

    /** Check to see if we are a unique serial number on this net */
    var $_DeviceIDCheck = TRUE;

    /** @var bool Tells us whether to use direct access to the endpoints */
    var $_direct = TRUE;

    /**
     *   @private
     *   Builds a packet
     *   @param array $Packet This is an array with packet commands in it.
     *   @return string The packet in string form.
     *   
     *   This function actually builds the packet to write to the socket.  It takes in
     *   an array of the form:
     *   <code>
     *   array["To"] = "0000A5";
     *   array["Command"] = 02;
     *   array["Data"][0] = 00;
     *   array["Data"][1] = 01;
     *   ...
     *   </code> 
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
    /**
     *   Changed a hex string into a binary string.
     *
     *   @param string $string The hex packet string
     *   @return string The binary string.
     */
    
    function deHexify($string) {
        $string = trim($string);
        $bin = "";
        for($i = 0; $i < strlen($string); $i+=2) {
            $bin .= chr(hexdec(substr($string, $i, 2)));
        }
        return $bin;
    }
    /**
     *   Computes the checksum of a packet
     *
     *   @param string PktStr The raw packet string
     *   @return string The checksum
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
    
    /**
     *   Creates a packet array from the parameters given
     *
     *   @param string $to The hexified DeviceID to send the packet to
     *   @param string $command The hexified packet command
     *   @param string $data The hexified data string
     *   @return array The packet array
     */
    function buildPacket($to, $command, $data="") {
        $pkt = array(
            'To' => $to,
            'Command' => $command,
            'Data' => $data,
        );
        return $pkt;
    }    

    /**
     *   Sets the callback function for when a packet comes in
     *   that we don't have a destination for.
     *
     *   @param string $function The name of the function or method to call
     *   @param object $object The object to use if $function is a method.  If
     *        this is NULL or unset the function will be called as a standard
     *        function.
     */
    function packetSetCallBack($function, &$object = NULL) {
        $this->callBackFunction = $function;
        $this->callBackObject = &$object;
    }

    /**
     *   Actually calls the callback function when given a packet.  The
     *   decoded packet should be the only argument for the callback function.
     *   If an object has been provided the function is called as a method of that
     *   object.
     *
     *   @param array $pkt The decoded packet we got
     */
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
     *   Sends out a packet
     *
     *   @param array $Info The array with the device information in it
     *   @param array $Packet Array with packet information in it.
     *   @param bool $GetReply Whether or not to wait for a reply.
     *   @return bool FALSE on failure, TRUE on success
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
                "SentTo" => str_pad(trim(strtoupper($Packet["to"])), 6, "0", STR_PAD_LEFT),
                "sendCommand" => trim(strtoupper($Packet['command'])),
                'group' => $group,
                'packet' => $Packet,
                "PacketTo" => str_pad(trim(strtoupper($Packet["to"])), 6, "0", STR_PAD_LEFT),
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
     *   Sends a reply packet
     *
     *   @param array $Info The device Info array
     *   @param string|int $to The deviceID to send the packet to
     *   @param array|string $data The data to send with the packet.
     *   @return bool FALSE on failure, TRUE on success
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

    /**
     *  Takes in an array and creates a data string out of it.
     *
     *   @param array $array The data to convert.
     *   @return string The data string.
     */
    function arrayToData($array) {
        if (is_array($array)) {
            $ret = '';
            foreach($array as $d) {
                $ret .= EPacket::hexify($d, 2);
            }
            return $ret;
        } else if (is_string($array)) {
            return $array;
        } else {
            return '';
        }
    }
    
    /**
     *  Creates a random serial number then checks to see if
     *  that serial number is unique on the bus.
     *
     *   @param array $Info The device information array
     */
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
            $ret = $this->ping($Info, TRUE);
        } while (is_array($ret));
        $this->SN = $SN;
        if ($this->verbose) print "Using Serial Number ".$this->SN."\r\n";
        $this->_getAll = $getAll;
    }    
    
    /**
     *   Gets the current time
     *
     *   @return Float The current time in seconds    
    */
    function PacketTime() {
       list($usec, $sec) = explode(" ", microtime());
       return ((float)$usec + (float)$sec);
    } 


    /**
     *   Gets the current time
     *
     *   @return Float The current time in seconds    
     */

    function RecvPacket($socket, $timeout=0) {
        if ($this->_direct) {
            $return = $this->_RecvPacket_Socket($socket, $timeout);
        } else {
            $return = $this->_RecvPacket_Indirect($socket, $timeout);        
        }
        return $return;
    }


    /**
     *   Receives a packet from the database interface (indirect)
     *
     *   @param resource $socket The socket to send the information on
     *   @param int $timeout The timeout to use.  0 means use the default
     *   @return bool TRUE on success, FALSE on failure
     */
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
     *   Receives a packet from the socket interface
     *
     *   @param int $socket The socket to send it out of.  0 is the default.
     *   @param int $timeout Timeout for waiting.  Default is used if timeout == 0    
     *   @return bool FALSE on failure, the Packet array on success
     */
    function _RecvPacket_Socket($socket, $timeout=0) {
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

    /**
     *  Checks to see if a particular DeviceID is a gateway
     * 
     *  @param int|string $DeviceID The DeviceID to check
     *  @return bool TRUE if the DeviceID belongs to a gateway, FALSE otherwise.
     */
    function isGateway($DeviceID) {
        if (is_string($DeviceID)) {
            $DeviceID = hexdec($DeviceID);
        }
        return (($DeviceID < $this->maxSN) && ($DeviceID > 0));
    }

    /**
     *  Sets the flag to get and return all packets.
     * 
     *  @param bool $DeviceID The DeviceID to check
     *  @return bool TRUE if the DeviceID belongs to a gateway, FALSE otherwise.
     */
    function getAll($val = TRUE) {
        $this->_getAll = $val;
    }

    /** 
     *   This function puts return packets into the $this->Packet array
     *
     *   @param string $data The raw packet data
     *   @param int $sock The socket we got the data on
     *   @return bool|array FALSE on failure, The interpreted packet on success.
     */
    function InterpPacket($data, $sock=0) {
        $return = FALSE;
        
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
    //                        if ($this->verbose) print "Not for us: ".$this->Packets[$key]."\r\n";
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
     *   Turns a packet string into an array of values.
     *
     *   @param string $data The raw packet to parse
     *   @return array The packet array created from the string
     */
    function UnbuildPacket($data) {
        // Strip off any preamble bytes.
        while (substr($data, 0, 2) == $this->preambleByte) $data = substr($data, 2);
        $pkt = array();
        $pkt["Command"] = substr($data, 0, 2);
        $pkt["To"] = trim(strtoupper(substr($data, 2, 6))); 
        $pkt["To"] = str_pad($pkt["To"], 6, "0", STR_PAD_LEFT);
        $pkt["From"] = trim(strtoupper(substr($data, 8, 6)));
        $pkt["From"] = str_pad($pkt["From"], 6, "0", STR_PAD_LEFT);

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
     *   Waits for unsolicited packets.
     *   
     *   @param array $InfoArray Array of gateway info arrays
     *   @param string $from The packet from address to look for
     *   @return string returns the packet it got.
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
     *   Waits for unsolicited packets.
     *   @param array $Info Gateway info array
     *   @param int $timeout The packet timeout.  Set to 0 for the default timeout.
     *   @return string returns the packet it got.
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
     *   Sends a ping out to the desired device
     * 
     *   @param array $Info Device information array
     *   @param bool $find If TRUE this causes it to have the packet sent out by ALL
     *        controller boards connected.  This is to find a device that may not be
     *        reporting because the controller board it is connected to doesn't know it
     *        exists.
     *   @return bool Returns TRUE if it got a reply, FALSE otherwise
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
     *   Finds a device among  the gateways.
     *
     *   @param array $Info Device information array
     *   @param array $gateways The array of gateway information so we know where to look
     *   @return array|bool Returns the gateway information array if it got a reply, FALSE otherwise
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
            if ($this->verbose) print("Trying Gateway ".$gw["GatewayName"]."        \n");
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
     *   Check if we are connected.  If not it connects to the gateway specified in $Info.
     *
     *   @param array $Info Array Infomation about the device to use            
     *   @return bool FALSE on failure, TRUE on success
     */
    function Connect($Info) {
        if ($this->_direct) {
            $sock = $Info['GatewayKey'];
            if (is_object($this->socket[$sock])) {
                if ($this->socket[$sock]->CheckConnect()) {
                    return TRUE;
                }
            }
            if (isset($Info["GatewayIP"]) && isset($Info["GatewayKey"]) && isset($Info["GatewayPort"])) {
                if (!is_object($this->socket[$sock])) {
                    $this->socket[$sock] = new epsocket("", 0, $verbose);
                    $this->socket[$sock]->verbose = $this->verbose;
                }
                $return = $this->socket[$sock]->Connect($Info["GatewayIP"], $Info["GatewayPort"]);
                if ($this->_DeviceIDCheck) {
                    if (($this->SN == FALSE) || ((hexdec($this->SN) >= $this->maxSN) || (hexdec($this->SN) < 1))) {
                        $this->ChangeSN($Info);
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
     *   Closes the connection to the specified gateway.    
     *
     *   @param array $Info Infomation about the device to use            
     */
    function Close($Info) {
        if (is_object($this->socket[$Info['GatewayKey']])) {
            $this->socket[$Info['GatewayKey']]->Close();
        }
    }

    /**
     *   If $Info is given it will try to connect to the server that is specified in it.
     *
     *   @param array $Info Infomation about the device to use
     *   @param bool $verbose If TRUE a lot of stuff is printed out
     *   @param object $db adodb database object
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
     *   Turns a number into a text hexidecimal string
     *   
     *   If the number comes out smaller than $width the string is padded 
     *   on the left side with zeros.
     *
     *   Duplicate: {@link epsocket::hexify()}
     *
     *   @param int $value The number to turn into a hex string
     *   @param int $width The width of the final string
     *   @return string The hex string created.
    */
    function hexify($value, $width=2) {
        $value = dechex($value);
        $value = str_pad($value, $width, "0", STR_PAD_LEFT);
        $value = substr($value, strlen($value)-$width);
        $value = strtoupper($value);

        return($value);
    }

    /**
     *   Turns a binary string into a text hexidecimal string
     *   
     *   If the number comes out smaller than $width the string is padded 
     *   on the left side with zeros.
     *
     *   If $width is not set then the string is kept the same lenght as
     *   the incoming string.
     *
     *   @param string $str The binary string to convert to hex
     *   @param int $width The width of the final string
     *   @return string The hex string created.
    */
    function hexifyStr($str, $width=NULL) {
        $value = "";
        $length = strlen($str);
        if (is_null($width)) $width = $length;
        for($i = 0; ($i < $length) && ($i < $width); $i++) {
            $char = substr($str, $i, 1);
            $char = ord($char);
            $value .= EPacket::hexify($char, 2);
        }
        $value = str_pad($value, $width, "0", STR_PAD_RIGHT);
        
        return($value);
    }

    /**
     *  Set the flag as to whether to check the DeviceID.  If the DeviceID
     *  is not checked then it will be set to the default and stay that way.
     *
     *  @param bool $val TRUE checks the serial number FALSE does not.
     */
    function SNCheck($val=TRUE) {
        $this->_DeviceIDCheck = (bool) $val;
    }

}

?>
