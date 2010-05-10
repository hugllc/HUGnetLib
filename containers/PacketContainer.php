<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetClass.php";
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";
require_once dirname(__FILE__)."/../interfaces/HUGnetPacketInterface.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PacketContainer extends HUGnetContainer implements HUGnetPacketInterface
{
    /** The placeholder for the Acknoledge command */
    const COMMAND_ACK = "01";
    /** The placeholder for the Echo Request command */
    const COMMAND_ECHOREQUEST = "02";
    /** The placeholder for the Echo Request command */
    const COMMAND_FINDECHOREQUEST = "03";
    /** The placeholder for the Capabilities command */
    const COMMAND_GETCALIBRATION = "4C";
    /** The placeholder for the Capabilities command */
    const COMMAND_GETCALIBRATION_NEXT = "4D";
    /** The placeholder for the Capabilities command */
    const COMMAND_SETCONFIG = "5B";
    /** The placeholder for the Capabilities command */
    const COMMAND_GETSETUP = "5C";
    const COMMAND_GETSETUP_GROUP = "DC";
    /** The placeholder for the Capabilities command */
    const COMMAND_GETDATA = "55";
    /** The placeholder for the Bad Command command */
    const COMMAND_BADC = "FF";
    /** The placeholder for the Read E2 command */
    const COMMAND_READE2 = "0A";
    /** The placeholder for the Write E2 command */
    const COMMAND_READRAM = "0B";
    /** The placeholder for the reply command */
    const SETRTC_COMMAND = "50";
    /** The placeholder for the reply command */
    const READRTC_COMMAND = "51";
    /** The placeholder for the Write E2 command */
    const COMMAND_POWERUP = "5E";
    /** The placeholder for the Write E2 command */
    const COMMAND_RECONFIG = "5D";
    /** The placeholder for the reply command */
    const COMMAND_REPLY = self::COMMAND_ACK;

    /** This is a preamble byte */
    const PREAMBLE = "5A";
    /** This is a preamble byte */
    const FULL_PREAMBLE = "5A5A5A";
    /** This is the 'to' field for an unsolicited packet */
    const UNSOLICITED_TO = "000000";
    /** This is the maximum our SN can be */
    const MAX_SN = 0x1F;

    static private $_Types = array(
        self::COMMAND_ACK                 => "REPLY",
        self::COMMAND_ECHOREQUEST         => "PING",
        self::COMMAND_FINDECHOREQUEST     => "FINDPING",
        self::COMMAND_GETCALIBRATION      => "CALIBRATION",
        self::COMMAND_GETCALIBRATION_NEXT => "CAL_NEXT",
        self::COMMAND_SETCONFIG           => "SETCONFIG",
        self::COMMAND_GETSETUP            => "CONFIG",
        self::COMMAND_GETDATA             => "SENSORREAD",
        self::COMMAND_BADC                => "BAD COMMAND",
        self::COMMAND_READE2              => "READ_E2",
        self::COMMAND_READRAM             => "READ_RAM",
        self::SETRTC_COMMAND              => "SET_RTC",
        self::READRTC_COMMAND             => "READ_RTC",
        self::COMMAND_POWERUP             => "POWERUP",
        self::COMMAND_RECONFIG            => "RECONFIG",
        self::COMMAND_REPLY               => "REPLY",
    );
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "To"       => "000000",   // Who the packet is to
        "From"     => "000000",   // Who the packet is from
        "Date"     => "",         // The date we created this object
        "Command"  => "00",       // The command we used
        "Length"   => 0,          // The length of the data section
        "Time"     => 0.0,        // The time we sent the packet out
        "Data"     => "",         // The data to send out
        "Type"     => "UNKNOWN",  // The type of packet
        "Reply"    => null,       // Reference to the reply packet
        "Checksum" => "00",       // The checksum we received
        "Timeout"  => 5,          // Timeout for the packet in seconds
        "Retries"  => 3,          // Number of times to retry the packet
        "GetReply" => true,       // Should we wait for a reply
        "group"    => "default",  // This is the socket group we belong to
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** @var object This is our socket */
    protected $mySocket = null;
    /** @var object This is our config */
    protected $myConfig = null;

    /**
    * Builds the class
    *
    * @param array $data The data to build the class with
    *
    * @return null
    */
    public function __construct($data = array())
    {
        $this->clearData();
        $this->myConfig = &ConfigContainer::singleton();
        parent::__construct($data);
        // This sets up the socket for us
        $this->group = $this->data["group"];
        if (empty($this->Date)) {
            $this->Date = date("Y-m-d H:i:s");
        }
        $this->verbose($this->myConfig->verbose);
    }

    /**
    * Creates the object from a string
    *
    * @param object &$pkt PacketSocketTable object
    *
    * @return null
    */
    public function fromPacketSocket(PacketSocketTable &$pkt)
    {
        $this->Command  = $pkt->Command;
        $this->To       = $pkt->PacketTo;
        $this->From     = $pkt->PacketFrom;
        $this->Length   = strlen($pkt->RawData)/2;
        $this->Data     = $pkt->RawData;
        $this->Checksum = $this->checksum();
        $this->setType("UNKNOWN");
    }
    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function fromString($string)
    {
        if (!($string = self::_checkStr($string))) {
            return;
        }
        $string             = strtoupper($string);
        $this->Command      = substr($string, 0, 2);
        $this->To           = substr($string, 2, 6);
        $this->From         = substr($string, 8, 6);
        $this->Length       = (int) hexdec(substr($string, 14, 2));
        $this->Data         = substr($string, 16, ($this->Length*2));
        $this->Checksum     = substr($string, (16 + ($this->Length*2)), 2);
        $pktdata            = substr($string, 0, strlen($data)-2);
        $this->setType("UNKNOWN");
    }
    /**
    * Returns the object as a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return string
    */
    public function toString($default = true)
    {
        // Return an empty string if the packet is empty
        if ($this->isEmpty()) {
            return "";
        }
        // Command (2 chars)
        $string  = self::stringSize($this->Command, 2);
        // To (6 chars)
        $string .= self::stringSize($this->To, 6);
        // From (6 chars)
        $string .= self::stringSize($this->From, 6);
        // Length (2 chars)
        $string .= sprintf("%02X", (strlen($this->Data)/2));
        // Data ('Length' chars)
        $string .= $this->Data;
        $this->Checksum = self::_checksum($string);
        // Add this and the checksum (2 chars) to the return
        return self::FULL_PREAMBLE.$string.$this->Checksum;
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($array)
    {
        parent::fromArray($array);
        $this->Length = strlen($this->Data)/2;
        if ((int)$this->Checksum == 0) {
            $this->Checksum = $this->checksum();
        }
    }

    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toArray($default = true)
    {
        if (is_object($this->data["Reply"])) {
            $Reply = $this->data["Reply"]->toArray();
        }
        for ($i = 0; $i < (strlen($this->Data)/2); $i++) {
            $Data[] = hexdec(substr($this->Data, ($i*2), 2));
        }

        return array(
            "To"           => self::stringSize($this->To, 6),
            "From"         => self::stringSize($this->From, 6),
            "Date"         => $this->Date,
            "Command"      => self::stringSize($this->Command, 2),
            "Length"       => (int) $this->Length,
            "Time"         => (float) $this->Time,
            "Data"         => (array)$Data,
            "RawData"      => $this->Data,
            "Type"         => $this->Type,
            "Reply"        => $Reply,
            "Checksum"     => $this->Checksum,
            "CalcChecksum" => $this->checksum(),
        );
    }
    /**
    * Creates the object from a string or array
    *
    * @param mixed &$data This is whatever you want to give the class
    *
    * @return null
    */
    public function fromAny(&$data)
    {
        if ($this->isMine($data, "PacketSocketTable")) {
            $this->fromPacketSocket($data);
        } else {
            parent::fromAny($data);
        }
    }

    /**
    * Looks for a packet in a string.
    *
    * This is meant to be call with every byte received.  The incoming byte should
    * be appended onto the string.  This routine will take care of removing
    * the portion of string that it turns into packets.
    *
    * @param mixed &$packet The raw packet string to check.  Could be a string, or
    *                       it could be a PacketContainer object
    *
    * @return bool true on success, false on failure
    */
    public function &recv(&$packet)
    {
        // Check the string.  If it doesn't look like a packet return.
        $pktStr = self::_checkStr($packet);
        if (!is_string($pktStr)) {
            return false;
        }
        // We got something that looks like a packet, so remove the buffer
        $packet = "";
        // Create a new packet object
        // checkStr strips the preamble but this expects it so we re-add it.
        if ($this->GetReply) {
            $pkt = self::_new(self::FULL_PREAMBLE.$pktStr);
        } else {
            $pkt = &$this;
            $this->fromString(self::FULL_PREAMBLE.$pktStr);
        }
        // Check the checksum  If it is bad return a false
        if ($pkt->Checksum !== $pkt->checksum()) {
            self::vprint(
                "Bad Checksum ".$pkt->Checksum." != ".$pkt->checksum(),
                1
            );
            return false;
        }
        self::vprint("Packet From ".$pkt->From, HUGnetClass::VPRINT_VERBOSE);
        // Set the time on the packet
        $pkt->_packetTime();
        // Set the socket on the packet
        $pkt->group = $this->group;
        // Check the packet to see what we got
        if ($this->GetReply) {
            if (self::myReply($pkt)) {
                // This is our reply.  Set it and return
                $this->data["Reply"] =& $pkt;
                return true;
            } else if (self::_unsolicited($pkt)) {
                // This is an unsolicited packet
                $this->myConfig->hooks->hook(
                    "UnsolicitedPacket", "PacketConsumerInterface"
                )->packetConsumer($pkt);
            } else if (self::_toMe($pkt)) {
                // This is different packet to me, not a reply thi this pkt
                $this->myConfig->hooks->hook(
                    "myPacket", "PacketConsumerInterface"
                )->packetConsumer($pkt);
            }
        } else {
            return true;
        }
        return false;
    }
    /**
    * Sends a packet out
    *
    * This function will wait for a reply if "GetReply" is true.  It will also
    * try to send the packet out the number of times in "Retries" in the case
    * of failure.
    *
    * @return PacketContainer object on success, null
    */
    public function send()
    {
        // Send the packet out
        if (is_object($this->mySocket)) {
            if ($this->From == "000000") {
                // Get our device ID.
                $this->From = $this->mySocket->DeviceID;
            }
            // Set the time on the packet
            $this->_packetTime();
            do {
                // Decrement the retries left
                $this->Retries--;
                // Send the packet
                $ret = $this->mySocket->sendPkt($this);
                // Get a reply if we want one
                if ($ret && $this->GetReply) {
                    $ret = $this->mySocket->recvPkt($this);
                }
                // This sends out a single 'findping' if we have tried and failed
                // twice to get a reply to this packet.  It can't run if we are
                // trying to send out a findping, otherwise it is a infinite loop.
                if (($this->Command !== self::COMMAND_FINDECHOREQUEST)
                    && (($this->Retries == 1))
                ) {
                    // Most of the stuff stays the same, so we are just cloning this
                    $ping = clone $this;
                    $ping->ping(array("Retries" => 1), true);
                }
                // Loop while:
                // * We still have retries  ($this->Retries > 0)
                // * We haven't gotten a positive return  (!$ret)
                // If we don't want a return packet we will retry if the send fails
            } while (($this->Retries > 0) && ($ret !== true));

            return $ret;
        }
        return false;
    }
    /**
    * Looks for any packet and returns it
    *
    * @param array $data The data to build the class with if called statically
    *                    This is ignored if not called statically.
    *
    * @return mixed PacketContainer on success, false on failure
    */
    public function &monitor($data = array())
    {
        // This is overridden to make sure we are in monitor mode
        if (empty($data["GetReply"])) {
            $data["GetReply"] = false;
        }
        // Make a new packet
        $pkt = self::_new($data);
        // If we get a packet return it
        if ($pkt->mySocket->recvPkt($pkt)) {
            self::vprint(
                "Monitor: Packet from ".$pkt->From,
                HUGnetClass::VPRINT_VERBOSE
            );
            return $pkt;
        }
        // If we don't get a packet return false
        return false;
    }
    /**
    * returns the calculated checksum of this packet
    *
    * @return string the calculated checksum of this packet
    */
    public function checksum()
    {
        return str_pad(substr((string)$this, -2), 2, "0", STR_PAD_LEFT);
    }
    /**
    * returns the calculated checksum of this packet
    *
    * @return string the calculated checksum of this packet
    */
    public function replyTime()
    {
        if (!is_object($this->Reply)) {
            return 0.0;
        }
        return (float)($this->Reply->Time - $this->Time);
    }
    /**
    * Sends a reply to this packet.
    *
    * @param array $data This is ONLY the data field.
    *
    * @return bool true on success, false on failure
    */
    public function &reply($data = "")
    {
        $pktArray = array(
            "To" => $this->From,
            "Data" => $data,
            "Command" => self::COMMAND_REPLY,
            "GetReply" => false,
            "group" => $this->group,
        );
        $this->Reply = self::_new($pktArray);
        return (bool)$this->Reply->send();
    }
    /**
    * Sends a packet out
    *
    * @param array  $data        This is ONLY the data field.
    * @param string $socketGroup The socket group to use
    *
    * @return bool true on success, false on failure
    */
    public function powerup($data = "", $socketGroup = "")
    {
        $pktArray = array(
            "Data" => $data,
            "Command" => self::COMMAND_POWERUP,
            "GetReply" => false,
            "group" => $socketGroup,
        );
        $pkt = self::_new($pktArray);
        return (bool)$pkt->send();
    }
    /**
    * Sends a ping packet out and waits for the reply
    *
    * @param array $data This is ONLY the data field.
    * @param bool  $find If true a findping is used
    *
    * @return bool true on success, false on failure
    */
    public function ping($data = "", $find = false)
    {
        // Get any new stuff from the command
        $this->fromAny($data);
        // Set our command
        if ($find) {
            $this->Command = self::COMMAND_FINDECHOREQUEST;
        } else {
            $this->Command = self::COMMAND_ECHOREQUEST;
        }
        // Send the packet
        return (bool)$this->send();
    }
    /**
    * Checks to see if the contained packet is an unsolicited
    *
    * @return bool true if it is unsolicited, false otherwise
    */
    public function unsolicited()
    {
        return $this->_unsolicited($this);
    }
    /**
    * Checks to see if the contained packet is an to me
    *
    * @return bool true if it is to me, false otherwise
    */
    public function toMe()
    {
        return $this->_toMe($this);
    }
    /**
    * returns true if the container is empty.  False otherwise
    *
    * @return bool Whether this container is empty or not
    */
    public function isEmpty()
    {
        return (bool)(
            (($this->default["Command"] === $this->data["Command"])
            && ($this->default["To"] === $this->data["To"])
            && ($this->default["From"] === $this->data["From"]))
            || empty($this->data));
    }
    /**
    * returns true if this packet has timed out
    *
    * @param int $timeout The timeout to use.  Default to $this->Timeout
    *
    * @return bool If this packet has timed out or not
    */
    public function timeout($timeout=0)
    {
        $timeout = empty($timeout) ? $this->Timeout : $timeout;
        return (bool)((strtotime($this->Date) + $timeout) < time());
    }
    /**
    * returns true if this packet is the same as the given one.
    *
    * @param object &$pkt The packet to check
    *
    * @return bool If this packet has timed out or not
    */
    public function same(PacketContainer &$pkt)
    {
        return (bool) (($pkt->Command === $this->Command) && ($pkt->To === $this->To)
            && ($pkt->From === $this->From) && ($pkt->Data === $this->Data)
            && ($pkt->Date === $this->Date));
    }
    /**
    * Checks to see if the given packet is a reply to this packet
    *
    * @param PacketContainer &$pkt The packet to check
    *
    * @return bool true if it is a reply, false otherwise
    */
    public function myReply(PacketContainer &$pkt)
    {
        if ($this->_toMe($pkt)
            && ($pkt->Command == self::COMMAND_REPLY)
            && ($this->To == $pkt->From)
            && (strtotime($this->Date) <= strtotime($pkt->Date))
        ) {
            return true;
        }
        return false;
    }
    /**
    * Computes the checksum of a packet
    *
    * @param string $string The raw packet string
    *
    * @return string The checksum
    */
    private function _checksum($string)
    {
        $chksum = 0;
        for ($i = 0; $i < strlen($string); $i+=2) {
            $val     = hexdec(substr($string, $i, 2));
            $chksum ^= $val;
        }
        return sprintf("%02X", $chksum);
    }
    /**
    * Looks for a packet in a string.
    *
    * @param string $string The raw packet string to check
    *
    * @return string On success, false on failure
    */
    private function _checkStr($string)
    {
        // This strips everything before the preamble (extra stuff)
        if (($pkt = stristr($string, self::PREAMBLE.self::PREAMBLE)) === false) {
            return false;
        }
        // This removes the preamble
        $this->_removePreamble($pkt);
        // Don't even try until we get to the length (chars 14 & 15)
        if (strlen($pkt) < 15) {
            return false;
        }
        // Get the length and calculate the size of the packet
        $len = hexdec(substr($pkt, 14, 2));
        // Get the size the packet needs to be
        $size = (9 + $len)*2;
        // If the packet is the right size return it.
        if (strlen($pkt) >= $size) {
            return substr($pkt, 0, $size);
        }
        // If we got here we didn't see a valid packet.
        return false;
    }
    /**
    * Checks to see if the given packet is a reply to this packet
    *
    * @param PacketContainer &$pkt The packet to check
    *
    * @return bool true if it is a reply, false otherwise
    */
    private function _unsolicited(PacketContainer &$pkt)
    {
        if (($pkt->To == self::UNSOLICITED_TO)
            && ($pkt->Command !== self::COMMAND_REPLY)
            && ($pkt->Command !== "00")
        ) {
            return true;
        }
        return false;
    }
    /**
    * Checks to see if the given packet is a reply to this packet
    *
    * @param PacketContainer &$pkt The packet to check
    *
    * @return bool true if it is a reply, false otherwise
    */
    private function _toMe(PacketContainer &$pkt)
    {
        if ((($pkt->To == $this->From)&&($this->From != "000000")&&($this != $pkt))
            || ($pkt->To == $this->mySocket->DeviceID)
        ) {
            return true;
        }
        return false;
    }
    /**
    * Builds the class
    *
    * @param array $data The data to build the class with
    *
    * @return object of type PacketContainer
    */
    private function &_new($data)
    {
        $class = __CLASS__;
        return new $class($data);
    }
    /**
    * Removes the preamble from a packet string
    *
    * @param string &$data The preamble will be removed from this packet string
    *
    * @return null
    */
    private function _removePreamble(&$data)
    {
        while (strtoupper(substr($data, 0, 2)) == self::PREAMBLE) {
            $data = substr($data, 2);
        }
    }
    /**
    * Gets the current time
    *
    * @return float The current time in seconds
    */
    private function _packetTime()
    {
        list($usec, $sec) = explode(" ", microtime());
        $this->Time = ((float)$usec + (float)$sec);
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

    /**
    * function to set To
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setTo($value)
    {
        if (is_int($value)) {
            $value = dechex($value);
        }
        $this->data["To"] = self::stringSize($value, 6);
    }
    /**
    * function to set From
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setFrom($value)
    {
        if (is_int($value)) {
            $value = dechex($value);
        }
         $this->data["From"] = self::stringSize($value, 6);
    }
    /**
    * function to set the Command
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setCommand($value)
    {
        $this->data["Command"] = self::stringSize($value, 2);
        $this->setType($value);
    }
    /**
    * function to set the Command
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setData($value)
    {
        if (is_array($value)) {
            $data = "";
            foreach ($value as $d) {
                $data .= sprintf("%02X", $d);
            }
            $this->data["Data"] = $data;
        } else if (is_string($value)) {
            $this->data["Data"] = $value;
        }
    }
    /**
    * function to check sentTo
    *
    * @param string $value This is not used here
    *
    * @return null
    */
    protected function setType($value = null)
    {
        if (is_string(self::$_Types[$this->Command])) {
            $this->data["Type"] = self::$_Types[$this->Command];
        } else {
            $this->data["Type"] = "UNKNOWN";
        }
    }
    /**
    * function to check sentTo
    *
    * @param string $value This is not used here
    *
    * @return null
    */
    protected function setGroup($value = null)
    {
        if (empty($value)) {
            if (!empty($this->myConfig->useSocket)) {
                $this->data["group"] = $this->myConfig->useSocket;
            } else {
                $this->data["group"] = "default";
            }
        } else {
            $this->data["group"] = $value;
        }
        $this->myConfigSetup();
        $this->mySocket = &$this->myConfig->sockets->getSocket($this->data["group"]);
    }
    /**
    * function to set Timeout
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setTimeout($value)
    {
        $value = (int) $value;
        // If this is empty we wait forever, or not at all.  Neither is good.
        if (!empty($value)) {
            $this->data["Timeout"] = $value;
        }
    }
}
?>
