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
class PacketLogContainer extends HUGnetContainer
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
    /** This is the 'to' field for an unsolicited packet */
    const UNSOLICITED_TO = "000000";

    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        // Packet Log definition
        "DeviceKey"     => 0,               // Database key
        "GatewayKey"    => 0,               // The gateway for this
        "Date"          => "2000-01-01 00:00:00",  // The date we got the packet
        "Command"       => "00",            // The command we got in the packet
        "sendCommand"   => "00",            // The command we sent out
        "PacketFrom"    => "000000",        // Who the packet was from
        "RawData"       => "",              // The raw data from the packet
        "sentRawData"   => "",              // The raw data we sent
        "Type"          => "UNSOLICITED",   // The type of packet
        "ReplyTime"     => 0,               // The reply time
        // Other stuff we need
        "PacketTo"      => "000020",        // Who the packet is to
        "From"          => "000020",        // Who we should send from
        "Checksum"      => 0,               // The checksum we received
        "CalcChecksum"  => 0,               // The checksum we received
        // The following are dummies just for ease of use
        "To"            => "",              // Goes into sentTo

    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** The database table to use */
    protected $table = "PacketLog";
    /** This is the Field name for the key of the record */
    protected $id = null;

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
        $this->clearData();
        $this->fromArray($data);
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

        $string = strtoupper($string);
        $pkt = array();
        $this->Command  = substr($data, 0, 2);
        devInfo::setStringSize($pkt["To"], 6);
        $pkt["From"] = substr($data, 8, 6);
        devInfo::setStringSize($pkt["From"], 6);

        $length              = hexdec(substr($data, 14, 2));
        $pkt["Length"]       = (int)$length;
        $pkt["RawData"]      = substr($data, 16, ($length*2));
        $pkt["Data"]         = self::splitDataString($pkt["RawData"]);
        $pkt["Checksum"]     = substr($data, (16 + ($length*2)), 2);
        $pktdata             = substr($data, 0, strlen($data)-2);
        $pkt["CalcChecksum"] = self::PacketGetChecksum($pktdata);
        $pkt['RawPacket']    = $data;
        return $pkt;

    }
    /**
    * Gets the 'To' field of a packet string
    *
    * @param string $string The raw packet string to use
    *
    * @return string
    */
    private function _getTo($string)
    {
        return substr($string, 2, 6);
    }
    /**
    * Gets the 'To' field of a packet string
    *
    * @param string $string The raw packet string to use
    *
    * @return string
    */
    private function _getFrom($string)
    {
        return substr($string, 8, 6);
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
        $string  = $this->Command;
        // To (2 chars)
        $string .= $this->PacketTo;
        // From (2 chars)
        $string .= $this->From;
        // Length (2 chars)
        $string .= sprintf("%02X", (strlen($this->sentRawData)/2));
        // Data ('Length' chars)
        $string .= $this->sentRawData;
        // Add the preamble to the return
        $return  = self::PREAMBLE.self::PREAMBLE.self::PREAMBLE;
        // Add this and the checksum to the return
        $return .= $string.self::checksum($string);
        return $return;

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
        self::removePreamble($pkt);
        $len = hexdec(substr($pkt, 14, 2));
        if (strlen($pkt) >= ((9 + $len)*2)) {
            $ret = substr($pkt, 0, (9+$len)*2);
            return $ret;
        } else {
            return false;
        }
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

    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

    /**
    * function to check sentTo
    *
    * @param string $value This is just for convience.
    *
    * @return null
    */
    protected function setTo($value)
    {
        $this->data["PacketTo"] = devInfo::setStringSize($value, 6);
    }
    /**
    * function to check sentTo
    *
    * @param string $value This is just for convience.
    *
    * @return null
    */
    protected function setSentTo($value)
    {
        $this->data["sentTo"] = devInfo::setStringSize($value, 6);
    }
    /**
    * function to check sentTo
    *
    * @param string $value This is just for convience.
    *
    * @return null
    */
    protected function setPacketTo($value)
    {
        $this->data["PacketTo"] = devInfo::setStringSize($value, 6);
    }

    /**
    * function to check sentTo
    *
    * @param string $value This is just for convience.
    *
    * @return null
    */
    protected function setPacketFrom($value)
    {
        $this->data["PacketFrom"] = devInfo::setStringSize($value, 6);
    }
    /**
    * function to check sentTo
    *
    * @param string $value This is just for convience.
    *
    * @return null
    */
    protected function setCommand($value)
    {
        $this->data["Command"] = devInfo::setStringSize($value, 2);
    }
    /**
    * function to check sentTo
    *
    * @param string $value This is just for convience.
    *
    * @return null
    */
    protected function setSendCommand($value)
    {
        $this->data["sendCommand"] = devInfo::setStringSize($value, 2);
    }

    /**
    * function to check sentTo
    *
    * @param string $value This is just for convience.
    *
    * @return null
    */
    protected function setSentRawData($value)
    {
        if (!is_string($value)) {
            $value = "";
        }
        $this->data["sentRawData"] = $value;
    }

}
?>
