<?php
/**
 * HUGnet packet code
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
 * @category   PacketStructure
 * @package    HUGnetLib
 * @subpackage PacketStructure
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the base socket code */
require_once HUGNET_INCLUDE_PATH."/base/SocketBase.php";

/** The placeholder for the Acknoledge command */
define("PACKET_COMMAND_ACK", "01");
/** The placeholder for the Echo Request command */
define("PACKET_COMMAND_ECHOREQUEST", "02");
/** The placeholder for the Echo Request command */
define("PACKET_COMMAND_FINDECHOREQUEST", "03");
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

/** Make sure we have the socket interface */
require_once HUGNET_INCLUDE_PATH."/drivers/socket/epsocket.php";
require_once HUGNET_INCLUDE_PATH."/drivers/socket/dbsocket.php";
require_once HUGNET_INCLUDE_PATH."/devInfo.php";


/**
 * Class for talking with HUGNet endpoints
 *
 * This class implements the packet structure for talking with endpoints.
 * It can use a number of different methods to talk to an endpoint:
 * 1. <b>Indirect</b> This involves using the database.  The updatedb.php script
 *     reads the packet out of the database and feeds it to poll.php which sends it
 *     out.  The opposite happens for the return packet if there is one.  This is
 *     implemented because there can be only one connection to any particular serial
 *     port.  That means that the polling script is the only thing that can connect.
 *     therefore the polling script must send the packet out.
 * 2. <b>Socket</b> This method sends the data out a unix socket.  It requires a
 *     serial to ethernet adapter of some kind.  This can be either software like
 *     ser2net or hardware like any number of serial ethernet servers from places
 *     like {@link http://bb-elec.com/ B&B Electronics}.
 *
 * @todo Add DirectIO support when it gets stabilized in PHP.
 *
 * @category   PacketStructure
 * @package    HUGnetLib
 * @subpackage PacketStructure
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class EPacket
{
    /** This contains all of the endpoints we know about */
    var $EndPoints = array();
    /** This is the packet buffer */
    private $Packets = array();
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
    var $IndirectReplyTimeout = 20;
    /** The timeout for waiting for a packet in seconds */
    var $AckTimeout = 2;
    /** The timeout for waiting for a packet in seconds */
    var $SockTimeout = 2;

    /** Whether to be verbose */
    var $verbose = false;

    /** The default serial number to use */
    public $SN = "000020";
    /** The default serial number to use */
    private $maxSN = 0x20;

    /** Whether or not to return ALL packets received. */
    protected $getAll = false;
    /** Whether or not to return unsolicited packets.*/
    private $_getUnsolicited = false;
    /** The ID that unsolicited packets will be sent to. */
    public $unsolicitedID = '000000'; 

    /** Object for the incoming packet callback */
    private $callBackObject = null;
    /** Method to use on above object (if it is an object) */
    private $callBackFunction = null;

    /** Check to see if we are a unique serial number on this net */
    protected $_DeviceIDCheck = true;

    /** @var bool Tells us whether to use direct access to the endpoints */
    private $_direct = true;

    
    /**
     * Creates a packet array from the parameters given
     *
     * @param string $to      The hexified DeviceID to send the packet to
     * @param string $command The hexified packet command
     * @param string $data    The hexified data string
     *
     * @return array The packet array
     */
    function buildPacket($to, $command, $data="") 
    {
        $pkt = array(
            'To'      => $to,
            'From'    => $this->SN,
            'Command' => $command,
            'Data'    => $data,
        );
        return $pkt;
    }    

    /**
     * Sets the callback function for when a packet comes in
     * that we don't have a destination for.
     *
     * @param string $function The name of the function or method to call
     * @param object &$object  The object to use if $function is a method.  If
     *      this is null or unset the function will be called as a standard
     *      function.
     *
     * @return null
     */
    function packetSetCallBack($function, &$object = null) 
    {
        $this->callBackFunction = $function;
        $this->callBackObject   = &$object;
    }

    /**
     * Actually calls the callback function when given a packet.  The
     * decoded packet should be the only argument for the callback function.
     * If an object has been provided the function is called as a method of that
     * object.
     *
     * @param array $pkt The decoded packet we got
     *
     * @return null
     */
    function packetCallBack($pkt) 
    {
        if ($this->verbose > 1) print "Checking for Callback Function...  ";
        $function = $this->callBackFunction;
        if ($this->verbose > 1) print " ".$function." ";
        if (is_object($this->callBackObject)) {
            if ($this->verbose > 1) print " ".get_class($this->callBackObject)." ";
            if (method_exists($this->callBackObject, $function)) {
                if ($this->verbose > 1) print " Calling ".get_class($this->callBackObject)."->".$function;
                $this->callBackObject->$function($pkt);
            }
        } else {
            if (function_exists($function)) {
                if ($this->verbose > 1) print "Calling ".$function;
                $function($pkt);
            }
        }
        if ($this->verbose > 1) print " Done\r\n";
    }
    
    /**
     * Figures out if we should use the timeout given or the default one
     *
     * @param int $timeout The timeout to use, if it is set
     *
     * @return int
     */
    private function _getReplyTimeout($timeout) 
    {
        if (!is_numeric($timeout)) {
            $timeout = $this->ReplyTimeout;
        }
        return $timeout;        
    }

    /**
     *  Setup an array to send out a packet
     *
     * @param array &$Info    The array with the device information in it
     * @param array &$Packet  Array with packet information in it.
     * @param int   $timeout  The timeout value to use
     * @param bool  $getReply Whether or not to wait for a reply.
     *
     * @return int The index of this packet
     */
    private function _setupsendPacket(&$Info, &$Packet, $timeout, $getReply) 
    {
        if ($this->verbose) print "Setting up the packet send\n";
        $Packet = array_change_key_case($Packet, CASE_LOWER);
        if (empty($Packet['data'])) $Packet['data'] = "";
        if (empty($Packet['from'])) $Packet['from'] = $this->SN;
        devInfo::setStringSize($Packet["to"], 6);
        devInfo::setStringSize($Packet["from"], 6);
        devInfo::setStringSize($Packet["command"], 2);
        $this->Packets[$this->_index] = array(
            "pktTimeout" => $timeout,
            "GetReply" => $getReply, 
            "SentTime" => EPacket::packetTime(),
            "SentFrom" => trim(strtoupper($this->SN)),
            "SentTo" => $Packet["to"],
            "sendCommand" => $Packet['command'],
            'group' => (bool)(hexdec($Packet["command"]) & 0x80),
            'packet' => $Packet,
            "PacketTo" => $Packet["to"],
            "PacketFrom" => $Packet["from"],
            "Date" => date("Y-m-d H:i:s"),
            "GatewayKey" => $Info['GatewayKey'],
            "DeviceKey" => $Info['DeviceKey'],
            "Type" => "OUTGOING",
            "RawData" => trim(strtoupper($Packet['data'])),
            "sentRawData" => trim(strtoupper($Packet['data'])),
            "Parts" => (empty($Packet['Parts']) ? 1 : $Packet['Parts']),
        );
        return $this->_index++;
    }
    /**
     * Sends out a packet
     *
     * @param array &$Info      The array with the device information in it
     * @param array $PacketList Array with packet information in it.
     * @param bool  $GetReply   Whether or not to wait for a reply.
     * @param int   $pktTimeout The timeout value to use
     *
     * @return bool false on failure, true on success
     */
    function sendPacket(&$Info, $PacketList, $GetReply=true, $pktTimeout = null) 
    {
        $pktTimeout = $this->_getReplyTimeout($pktTimeout);
        // Setup the packet array
        if (!is_array($PacketList)) return false;
        $PacketList = array_change_key_case($PacketList, CASE_LOWER);
        if (isset($PacketList['to'])) {
            $PacketList = array($PacketList);
        }

        // Setup the socket we are working on
        $socket = $this->_packetSendSetSocket($Info);
        if ($this->verbose) print("Sending a packet on ".$this->Info[$socket]["GatewayIP"].":".$this->Info[$socket]["GatewayPort"]."\n");

        // Make sure we are connected.
        $this->connect($Info);

        $ret = array();
        foreach ($PacketList as $Packet) {
            if (!is_array($Packet)) continue;
            if (!is_object($this->socket[$socket])) $this->connect($this->config);
            $index = $this->_setupsendPacket($Info, $Packet, $pktTimeout, $GetReply);
//            $Packet["PktStr"] = devInfo::deHexify(SocketBase::packetBuild($Packet));
            if ($retval = $this->socket[$socket]->SendPacket($this->Packets[$index])) {
                $retval = $this->_sendPacketGetReply($socket, $index);
            }
            if (is_array($retval)) $ret = array_merge($ret, $retval);
            unset($this->Packets[$index]);
        }

        if (($retval === true) && empty($ret)) return true;
        if (empty($ret)) return false;
        return $ret;

    }



    /**
     *  Gets a reply from the endpoint.
     *
     *  Returns an array of packet arrays on success, false on failure
     *
     * @param int $socket The socket to use
     * @param int $index  The packet index to use
     *
     * @return mixed
     */
    private function _sendPacketGetReply($socket, $index) 
    {
        $ret = array();
        if ($this->Packets[$index]["GetReply"] === false) return true;
        do {
            $pktRet = $this->RecvPacket($socket, $this->Packets[$index]["pktTimeout"]);
            if (is_array($pktRet)) {
                $ret[] = $pktRet;
                if ($pktRet["Reply"] === true) break;
            }
        } while ($pktRet !== false);
        return $ret;
    }

    /**
     * Sets up the sockets for sending a packet
     *
     * @param array $Info The array with the device information in it
     *
     * @return int
     */
    private function _packetSendSetSocket($Info) 
    {

        if (isset($Info['GatewayKey'])) {
            $socket = $Info['GatewayKey'];
        } else {
            // If one is not given, use the first one.
            reset($this->socket);
            list($socket, $tmp) = each($this->socket);
        }
        return $socket;
    }
    /**
     * Sends a reply packet
     *
     * @param array  $Info The device Info array
     * @param string $to   The deviceID to send the packet to
     * @param string $data The data to send with the packet.
     *
     * @return bool false on failure, true on success
     */
    function sendReply($Info, $to, $data=null) 
    {
        if (!is_string($to)) $to = devInfo::hexify($to, 6);
        
        if (is_array($data)) $data = $this->arrayToData($data);        
        
        $pkt = array(
            'To' => $to,
            'Data' => $data,
            'Command' => PACKET_COMMAND_REPLY,
        );
        return $this->sendPacket($Info, array($pkt), false);
    }

    /**
     * Takes in an array and creates a data string out of it.
     *
     * @param array $array The data to convert.
     *
     * @return string The data string.
     */
    function arrayToData($array) 
    {
        if (is_array($array)) {
            $ret = '';
            foreach ($array as $d) {
                $ret .= devInfo::hexify($d, 2);
            }
            return $ret;
        } else if (is_string($array)) {
            return $array;
        } else {
            return '';
        }
    }

    /**
     *  Creates an array of possible serial numbers
     *
     * @return null
     */
    private function _createSNArray() 
    {
        for ($i = 0; $i < $this->maxSN; $i++) {
            $SN = devInfo::hexify(mt_rand(1,$this->maxSN), 6);
            $this->SNArray[] = $SN;
        }
    }

    /**
     * Checks a given serial number to see if it is in use
     *
     * @param array $Info The device Info array
     * @param int   $key  The key where to find the serial number to check
     *
     * @return bool
     */
    private function checkSN($Info, $key) 
    {
        $Info = array("DeviceID" => $this->SNArray[$key]);
        if ($this->verbose) print "Checking Serial Number ".$this->SNArray[$key]."\r\n";
        $ret = $this->ping($Info, true);
        if (is_array($ret)) {
            unset($this->SNArray[$key]);
            return false;
        } else {
            $this->SN = $this->SNArray[$key];
            return true;
        }
    }
    /**
     * Creates a random serial number then checks to see if
     * that serial number is unique on the bus.
     *
     * @param array $Info The device information array
     *
     * @return null
     */
    function changeSN($Info = null) 
    {
        if (!is_array($Info)) $Info = array();
        $getAll = $this->getAll;
        $this->getAll(false);
        $count = count($this->SNArray);
        for ($i = 0; $i < $count; $i++) {
            $key = array_rand($this->SNArray, 1);
            if ($this->checkSN($Info, $key)) break;
        }        
        if ($this->verbose) print "Using Serial Number ".$this->SN."\r\n";
        $this->getAll($getAll);
    }    
    
    /**
     * Gets the current time
     *
     * @return float The current time in seconds    
     */
    function packetTime() 
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    } 



    /**
     * Receives a packet from the socket interface
     *
     * @param int $socket  The socket to send it out of.  0 is the default.
     * @param int $timeout Timeout for waiting.  Default is used if timeout == 0    
     *
     * @return bool false on failure, the Packet array on success
     */
    function RecvPacket($socket, $timeout=0, $reply=true) 
    {
        if (!is_object($this->socket[$socket])) return false;
        $Start    = time();
        $GotReply = false;

        do {
            $pkt = $this->socket[$socket]->RecvPacket($timeout, $reply);
            if ($pkt !== false) $GotReply = $this->interpPacket($pkt, $socket);
        } while (($GotReply == false) && ((time() - $Start) < $timeout));
        return $GotReply;
    }

    /**
     * Checks to see if a particular DeviceID is a gateway
     * 
     * @param string $DeviceID The DeviceID to check
     *
     * @return bool true if the DeviceID belongs to a gateway, false otherwise.
     */
    function isGateway($DeviceID) 
    {
        if (is_string($DeviceID)) {
            $DeviceID = hexdec($DeviceID);
        }
        return (($DeviceID < $this->maxSN) && ($DeviceID > 0));
    }

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
        foreach (array_keys($this->socket) as $k) {
            $this->socket[$k]->getAll((bool)$val);
        }
    }

    /**
     * Finds the array that the given packet is a reply to
     *
     * @param array $pkt   The packet information sent out
     * @param array $reply The packet returned
     *
     * @return bool
     */
    private function _isReply($pkt, $reply) 
    {
        return (($pkt["SentTo"] == $reply["From"]) || $pkt['group']) && ($pkt["SentFrom"] == $reply["To"]);
    }

    /**
     * Finds the array that the given packet is a reply to
     *
     * @param array $reply The packet returned
     *
     * @return mixed
     */
    private function _findReply($reply) 
    {
        if (!is_array($this->Packets)) return false;
        foreach ($this->Packets as $key => $pkt) {
            if ($this->_isReply($pkt, $reply)) return $key;
        }
        return false;
    }
    /**
     * Checks to see if $reply is a reply to the packet $pkt 
     * that was sent out.  If it is, it sets all sorts of good
     * parameters.  If it is not it returns false.
     *
     * @param array &$reply The packet returned
     * @param int   $socket The socket used
     *
     * @return mixed
     */
    private function _checkPacketReply(&$reply, $socket) 
    {
        if ($reply['Command'] != PACKET_COMMAND_REPLY) return false;
        $key = $this->_findReply($reply);
        if ($key === false) return false;

        $reply                = array_merge($this->Packets[$key], $reply);
        $reply["ReplyTime"]   = $reply["Time"] - $reply["SentTime"]; 
        $reply["Socket"]      = $socket;
        $reply["GatewayKey"]  = $socket;
        $reply["sendCommand"] = $this->Packets[$key]['sendCommand'];
        $reply["Reply"]       = true;
        $reply["toMe"]        = true;
        $reply["isGateway"]   = $this->isGateway($reply["From"]);
    
        unset($this->Packets[$key]);
        return $reply;
    }     
    /**
     * Checks the status of a packet.  If it is a reply it ignores it.
     * If it is not a reply it deals with it.
     *
     * @param array &$pkt   The packet to check
     * @param int   $socket The socket used
     *
     * @return mixed
     */
    private function _checkPacketOther(&$pkt, $socket) 
    {
        if ($pkt["Reply"] !== true) {
            $pkt["Unsolicited"] = (trim(strtoupper($pkt["To"])) == $this->unsolicitedID);
            $pkt["Socket"]      = $socket;
            $pkt["GatewayKey"]  = $socket;
            $pkt["Reply"]       = false;
            $pkt["toMe"]        = (trim(strtoupper($pkt["To"])) == $this->SN);
            $pkt["isGateway"]   = $this->isGateway($pkt["From"]);

            $this->packetCallBack($pkt);
                
            if (!($this->getAll ||  ($pkt["Unsolicited"] && $this->_getUnsolicited))) {
                $pkt = false;
            }
        }
    }     

    /**
     * Resets packets if they have timed out.
     *
     * @return null
     */
    private function _checkPacketTimeout() 
    {
        $timeout = $this->_getReplyTimeout($this->Packets[$key]['pktTimeout']);
        foreach ($this->Packets as $key => $pkt) { 
            if (($this->packetTime() - $pkt["SentTime"]) > $timeout) {
                unset($this->Packets[$key]);
            }
        }
    }
    /** 
     * This function puts return packets into the $this->Packet array
     *
     * @param string $data The raw packet data
     * @param int    $sock The socket we got the data on
     *
     * @return mixed false on failure, The interpreted packet on success.
     */
    function interpPacket($pkt, $sock=0) 
    {
        $pkt["Time"]         = EPacket::packetTime();

        if ($pkt["Checksum"] != $pkt["CalcChecksum"]) {
            if ($this->verbose) print "Bad Packet Received: ".$pkt["RawData"]."\r\n";
            return false;
        }
        $this->_checkPacketReply($pkt, $sock);
        $this->_checkPacketTimeout();
        $this->_checkPacketOther($pkt, $sock);
        return $pkt;
    }

    
    
    
    /**
     * Waits for unsolicited packets.
     *
     * @param array $Info    Gateway info array
     * @param int   $timeout The packet timeout.  Set to 0 for the default timeout.
     *
     * @return string returns the packet it got.
     */
    function monitor($config=array(), $timeout = 0) 
    {
        if (empty($config)) $config = $this->config;
        $this->connect($config);
        $this->getAll(true);
        if ($this->verbose) print("Waiting on Packets from ".$config["GatewayName"].":".$config["GatewayPort"]."\n");
        $return = $this->RecvPacket($config["GatewayKey"], $timeout, false);
        $this->getAll(false);
        if (!is_array($return)) return array();
        return $return;
    }
    
    
    /**
     * Sends a ping out to the desired device
     * 
     * @param array $Info Device information array
     * @param bool  $find If true this causes it to have the packet sent out by ALL
     *      controller boards connected.  This is to find a device that may not be
     *      reporting because the controller board it is connected to doesn't know it
     *      exists.
     *
     * @return bool Returns true if it got a reply, false otherwise
     */
    function ping($Info, $find=false) 
    {
        if ($this->verbose) print("Pinging ".$Info["DeviceID"]."\n");

        if ($find) {
            $pkt["Command"] = "03";
        } else {
            $pkt["Command"] = "02";
        }
        $pkt["To"] = $Info["DeviceID"];

        $return = $this->sendPacket($Info, $pkt);        
        
        return $return;
    }
        

    /**
     * Check if we are connected.  If not it connects to the gateway specified in $config.
     *
     * @param array $config Infomation about the device to use            
     *
     * @return bool false on failure, true on success
     */
    function connect($config) 
    {
        if (empty($config)) $config = $this->config;
        if ($this->checkConnect($config["GatewayKey"])) return true;
        $ret = $this->_connectOpenSocket($config);
        if ($ret) $ret = $this->socket[$config["GatewayKey"]]->connect();
        if ($ret) $this->_connectSetSN($config);
        return true;
    }
    
    /**
     * Check to see if we are connected
     *
     * @param int $socket The socket to check
     *
     * @return bool
     */
    function checkConnect($socket) 
    {
        if (!is_object($this->socket[$socket])) return false;
//        return $this->socket[$socket]->checkConnect();
        return true;
    }
    /**
     * Opens a socket depending on the value of $Info["socketType"]
     *
     * @param array $Info Array Infomation about the device to use            
     *
     * @return bool false on failure, true on success
     */
    private function _connectOpenSocket($config=array()) 
    {
        if (empty($config)) $config = $this->config;
        if ($config['socketType'] == "db") {
            $class = "dbsocket";
            $this->ReplyTimeout = $this->IndirectReplyTimeout;
        } else if ($config['socketType'] == "test") {
            $class = "epsocketMock";
        } else {
            $class = "epsocket";
        }
        if (!class_exists($class)) return false;
        $this->socket[$config['GatewayKey']] = new $class($config);
        $this->Info[$config['GatewayKey']] = $config;
        return is_object($this->socket[$config['GatewayKey']]);
    }

    /**
     * Figures out if we need to check the serial number.
     *
     * @param array &$config Array Infomation about the device to use            
     *
     * @return bool false on failure, true on success
     */
    private function _connectSetSN(&$config) 
    {
        if (!$this->checkConnect($config["GatewayKey"])) return;
        if ($this->_DeviceIDCheck) {
            if (($this->SN == false) || ((hexdec($this->SN) >= $this->maxSN) || (hexdec($this->SN) < 1))) {
                $this->changeSN($config);
                if ($this->verbose) print "Using Serial Number ".$this->SN."\r\n";
                // Put something here
            }
        }    
    }

    /**
     * Closes the connection to the specified gateway.    
     *
     * @param array $config Infomation about the device to use            
     *
     * @return null
     */
    function close($config)
    {
        if (is_object($this->socket[$config['GatewayKey']])) {
            $this->socket[$config['GatewayKey']]->close();
        }
    }

    /**
     * If $config is given it will try to connect to the server that is specified in it.
     *
     * @param array $config  Infomation about the connection
     * @param bool  $snCheck Should we check the serial number
     *
     * @return null
     */
    function __construct($config=array(), $snCheck=true) 
    {
        if (!isset($config["packetSNCheck"])) $config["packetSNCheck"] = $snCheck;
        $this->snCheck($config["packetSNCheck"]);
        $this->_createSNArray();
        $this->verbose = $config["verbose"];
        if (!empty($config)) {
            $this->connect($config);
        }
        if (empty($config["GatewayKey"])) $config["GatewayKey"] = 1;
        $this->config = $config;
    }
    
    /**
     * Set the flag as to whether to check the DeviceID.  If the DeviceID
     * is not checked then it will be set to the default and stay that way.
     *
     * @param bool $val true checks the serial number false does not.
     *
     * @return null
     */
    function snCheck($val=true) 
    {
        $this->_DeviceIDCheck = (bool) $val;
    }
}

?>
