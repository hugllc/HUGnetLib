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
class PacketContainer extends HUGnetContainer
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

    /** This is the smallest config data can be */
    const CONFIG_MINSIZE = "36";

    /** Error number for not getting a packet back */
    const ERROR_NOREPLY_NO = -1;
    /** Error message for not getting a packet back */
    const ERROR_NOREPLY = "Board failed to respond";
    /** Error number for not getting a packet back */
    const ERROR_BADC_NO = -2;
    /** Error message for not getting a packet back */
    const ERROR_BADC = "Board responded: Bad Command";
    /** Error number for not getting a packet back */
    const ERROR_TIMEOUT_NO = -3;
    /** Error message for not getting a packet back */
    const ERROR_TIMEOUT = "Timeout waiting for reply";


    /** Error Code */
    const DRIVER_NOT_FOUND = 1;
    /** Error Code */
    const DRIVER_NOT_COMPLETE = 2;

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
        $this->myConfig = &ConfigContainer::singleton();
        $this->mySocket = &$this->myConfig->socket;
        parent::__construct($data);
        $this->Date = date("Y-m-d H:i:s");
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
        if (!($string = $this->check($string))) {
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
        $this->CalcChecksum = $this->checksum($pktdata);
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
        $this->Checksum = self::checksum($string);
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
        if (!is_array($array)) {
            return;
        }
        parent::fromArray($array);
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
    * Computes the checksum of a packet
    *
    * @param string $string The raw packet string
    *
    * @return string The checksum
    */
    protected function checksum($string)
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
    public function check($string)
    {
        if (($pkt = stristr($string, self::PREAMBLE.self::PREAMBLE)) === false) {
            return false;
        }
        $this->removePreamble($pkt);
        $len = hexdec(substr($pkt, 14, 2));
        if (strlen($string) >= ((9 + $len)*2)) {
            return substr($pkt, 0, (9+$len)*2);
        }
        return false;
    }
    /**
    * Looks for a packet in a string.
    *
    * @param string $string The raw packet string to check
    *
    * @return PacketContainer object on success, null
    */
    public function &recv($string)
    {
        if ((($pkt = self::check($string)) === false)
            || (self::_unsolicited($string))
        ) {
            return null;
        }
        if (is_object($this)) {
            if ($this->_getTo($pkt) == $this->PacketFrom) {
                // Not for us
                return null;
            }
            $this->fromString($pkt);
            return $this;
        }
        $class = __CLASS__;
        $ret = new $class();
        $ret->fromString($pkt);
        return $ret;
    }

    /**
    * Looks for a packet in a string.
    *
    * @param string $string The raw packet string to check
    *
    * @return PacketContainer object on success, null
    */
    private function _unsolicited($string)
    {
        if (self::_getTo($pkt) != self::UNSOLICITED_TO) {
            return false;
        }
        // Do something here!
        return true;
    }

    /**
    * Removes the preamble from a packet string
    *
    * @param string &$data The preamble will be removed from this packet string
    *
    * @return null
    */
    protected function removePreamble(&$data)
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
