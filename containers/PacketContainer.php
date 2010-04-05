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
require_once dirname(__FILE__)."/../devInfo.php";

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
        "CalcChecksum"  => "00",  // The checksum we calculated
        "Timeout"  => 5,          // Timeout for the packet in seconds
        "Retries"  => 3,          // Number of times to retry the packet
        "GetReply" => true,       // Should we wait for a reply
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** @var object This is our socket */
    protected $mySocket = null;
    /** @var object This is our config */
    protected $myConfig = null;
    /** @var string This is the socket group we are using */
    private $_group = "default";
    /**
    * Builds the class
    *
    * @param array  $data        The data to build the class with
    * @param string $socketGroup The socket group to use
    *
    * @return null
    */
    public function __construct($data = array(), $socketGroup="")
    {
        $this->myConfig = &ConfigContainer::singleton();
        $this->socket($socketGroup);
        parent::__construct($data);
        $this->Date = date("Y-m-d H:i:s");
    }

    /**
    * Sets the socket to use
    *
    * @param string $group The socket group to use
    *
    * @return null
    */
    public function socket($group="")
    {
        if (!empty($group)) {
            $this->_group = $group;
        } else if (!empty($this->myConfig->useSocket)) {
            $this->_group = $this->myConfig->useSocket;
        }
        $this->mySocket = &$this->myConfig->sockets->getSocket($this->_group);
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
        $this->CalcChecksum = $this->_checksum($pktdata);
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
        // Command (2 chars)
        $string  = devInfo::setStringSize($this->Command, 2);
        // To (6 chars)
        $string .= devInfo::setStringSize($this->To, 6);
        // From (6 chars)
        $string .= devInfo::setStringSize($this->From, 6);
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
            "To"           => devInfo::setStringSize($this->To, 6),
            "From"         => devInfo::setStringSize($this->From, 6),
            "Date"         => $this->Date,
            "Command"      => devInfo::setStringSize($this->Command, 2),
            "Length"       => (int) $this->Length,
            "Time"         => (float) $this->Time,
            "Data"         => (array)$Data,
            "RawData"      => $this->Data,
            "Type"         => $this->Type,
            "Reply"        => $Reply,
            "Checksum"     => $this->Checksum,
            "CalcChecksum" => $this->CalcChecksum,
        );
    }
    /**
    * Looks for a packet in a string.
    *
    * This is meant to be call with every byte received.  The incoming byte should
    * be appended onto the string.  This routine will take care of removing
    * the portion of string that it turns into packets.
    *
    * @param string &$string The raw packet string to check
    *
    * @return bool true on success, false on failure
    */
    public function recv(&$string)
    {
        // Check the string.  If it doesn't look like a packet return.
        $pktStr = self::_checkStr($string);
        if (!is_string($pktStr)) {
            return false;
        }
        // We got something that looks like a packet, so remove the buffer
        $string = "";
        // Create a new packet object
        // checkStr strips the preamble but this expects it so we re-add it.
        $pkt = self::_new(self::FULL_PREAMBLE.$pktStr);
        // Set the time on the packet
        $pkt->_packetTime();
        // Check the packet to see what we got
        if (self::_reply($pkt)) {
            // This is our reply.  Set it and return
            $this->data["Reply"] =& $pkt;
            return true;
        } else if (self::_unsolicited($pkt)) {
            // This is an unsolicited packet
            return false;
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
    * @param array $data The data to build the class with if called statically
    *
    * @return PacketContainer object on success, null
    */
    public function send($data = array())
    {
        // This deals with us being called statically
        if (!self::_me()) {
            $pkt = self::_new($data);
            $pkt->send();
            return $pkt;
        }
        // Send the packet out
        if (is_object($this->mySocket)) {
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
                // Loop while:
                // * We still have retries  ($this->Retries > 0)
                // * We haven't gotten a positive return  (!$ret)
                // If we don't want a return packet we will retry if the send fails
            } while (($this->Retries > 0) && !$ret);

            return $ret;
        }
        return false;
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
        $return = sprintf("%02X", $chksum);
        return $return;
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
    private function _reply(PacketContainer &$pkt)
    {
        if (($pkt->To == $this->From)
            && ($pkt->From == $this->To)
            && ($pkt->Command == self::COMMAND_REPLY)
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
        return new $class($data, $this->_group);
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
    /**
    * Looks for a packet in a string.
    *
    * @return PacketContainer object on success, null
    */
    private function _me()
    {
        $class = __CLASS__;
        return (get_class($this) === $class);
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
        $this->data["To"] = devInfo::setStringSize($value, 6);
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
        $this->data["From"] = devInfo::setStringSize($value, 6);
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
        $this->data["Command"] = devInfo::setStringSize($value, 2);
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

}
?>
