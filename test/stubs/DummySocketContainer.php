<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once CODE_BASE."base/HUGnetClass.php";
/** This is our test configuration */
require_once CODE_BASE."base/HUGnetContainer.php";
/** This is our test configuration */
require_once CODE_BASE."interfaces/HUGnetSocketInterface.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DummySocketContainer implements HUGnetSocketInterface
{
    /** @var bool This says if we are connected or not */
    protected $connected = false;
    /** @var bool The string to read from the socket */
    public $readString = "";
    /** @var bool The string written to the socket */
    public $writeString = "";
    /** @var bool The buffer for incoming stuff */
    public $buffer = "";
    /** @var string The group we are in */
    protected $data = array(
        "group" => "default",
        "DeviceID" => "000020",
    );


    /**
    * Creates a database object
    *
    * @param array $config The configuration array to use.
    *
    * @return bool true on success, false on failure
    */
    public function __construct($config = array())
    {
        $this->group =& $this->data["group"];
        $this->DeviceID =& $this->data["DeviceID"];
        if (!empty($config["group"])) {
            $this->data["group"] = $config["group"];
        }
        if (!empty($config["readString"])) {
            $this->readString = $config["readString"];
        }
        if (!empty($config["writeString"])) {
            $this->writeString = $config["writeString"];
        }
    }

    /**
    * Creates a database object
    *
    * @return bool true on success, false on failure
    */
    public function connected()
    {
        return $this->connected;
    }

    /**
    * Creates a socket connection to the gateway
    *
    * @return bool true on success, false on failure
    */
    public function connect()
    {
        $this->connected = true;
        return $this->connected;
    }

    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function disconnect()
    {
        $this->connected = false;
    }
    /**
    * Write data out a socket
    *
    * @param string $string The hexified string to send out
    *
    * @return int The number of bytes written on success, false on failure
    */
    public function write($string)
    {
        $this->writeString .= (string)$string;
        return strlen($string)/2;
    }

    /**
    * Read data from the server
    *
    * @param int $maxChars The number of characters to read
    *
    * @return string Of hexified characters
    */
    public function read($maxChars = 50)
    {
        $string = substr($this->readString, 0, ($maxChars*2));
        $this->readString = substr($this->readString, ($maxChars*2));
        return $string;
    }
    /**
    * Sends out a packet
    *
    * @param PacketContainer &$pkt The packet to send out
    *
    * @return bool true on success, false on failure
    */
    public function sendPkt(PacketContainer &$pkt)
    {
        return (bool)$this->write((string)$pkt);
    }
    /**
    * Waits for a reply packet for the packet given
    *
    * @param PacketContainer &$pkt The packet to send out
    *
    * @return bool true on success, false on failure
    */
    public function recvPkt(PacketContainer &$pkt)
    {
        $timeout = time() + $pkt->Timeout;
        do {
            $this->buffer .= $this->read();
            $ret = $pkt->recv($this->buffer);
        } while (($ret === false) && ($timeout > time()));
        return $ret;
    }
        /**
    * Sets all of the endpoint attributes from an array
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toArray($default = false)
    {
        return array(
            "readString" => $this->readString,
            "writeString" => $this->writeString,
            "group" => $this->data["group"],
        );
    }

}
?>
