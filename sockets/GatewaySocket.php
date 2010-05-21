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
require_once dirname(__FILE__)."/../interfaces/HUGnetSocketInterface.php";
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
class GatewaySocket extends HUGnetContainer implements HUGnetSocketInterface
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "GatewayKey" => 0,                  // Database key for gateways
        "GatewayIP" => "127.0.0.1",         // IP address for the gateway
        "GatewayPort" => "2000",            // TCP Port for the gateway
        "GatewayName" => "Localhost",       // The name of the gateway
        "GatewayLocation" => "",            // The location of the gateway
        "database" => "",                   // Different database to use
        "FirmwareStatus" => "RELEASE",      // The type of firmware to use with this
        "isVisible" => 0,                   // Whether this gateway is visible
        "Timeout" => 2,                     // Timeout when talking on this gateway
        "group" => "default",               // The group this gateway is in
        "DeviceID" => "FD0020",    // This is our device ID
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** @var Unix Socket This is the socket for our connection */
    protected $socket = null;
    /** @var int The error number.  0 if no error occurred */
    protected $Errno = 0;
    /** @var string The error string */
    protected $Error = "";
    /** @var string The character buffer */
    protected $buffer = "";

    /**
    * Builds the class
    *
    * @param array $data The data to build the class with
    *
    * @return null
    */
    public function __construct($data)
    {
        // This forces it to always be present, even if the data gets cleared
        $this->default["DeviceID"] = PacketContainer::tempDeviceID();
        $this->data = $this->default;
        $this->fromArray($data);
    }

    /**
    * Disconnects from the gateway
    *
    * @return null
    */
    public function __destruct()
    {
        $this->disconnect();
    }
    /**
    * Creates a database object
    *
    * @return bool true on success, false on failure
    */
    public function connected()
    {
        if (!is_resource($this->socket) || feof($this->socket)) {
            return false;
        }
        return true;
    }

    /**
    * Creates a socket connection to the gateway
    *
    * @return bool true on success, false on failure
    */
    public function connect()
    {
        if ($this->connected()) {
            return true;
        }
        $this->vprint(
            "Opening socket to ".$this->GatewayIP.":".$this->Port,
            HUGnetClass::VPRINT_VERBOSE
        );
        $this->socket = @fsockopen(
            $this->data["GatewayIP"],
            $this->data["GatewayPort"],
            $this->Errno,
            $this->Error,
            $this->data["Timeout"]
        );
        if (is_resource($this->socket)) {
            stream_set_blocking($this->socket, false);
            $this->vprint(
                "Opened the Socket ".$this->socket." to "
                .$this->GatewayIP.":".$this->Port,
                HUGnetClass::VPRINT_VERBOSE
            );
            return true;
        }
        $this->socket = null;
        $this->vprint(
            "Connection to ".$this->GatewayIP." Failed."
            ." Error ".$this->Errno.": ".$this->Error,
            HUGnetClass::VPRINT_ERROR
        );
        return false;
    }

    /**
    * Disconnects from the database
    *
    * @return object PDO object, null on failure
    */
    public function disconnect()
    {
        if (!$this->connected()) {
            return;
        }
        $this->vprint(
            "Closing Connection ".$this->socket,
            HUGnetClass::VPRINT_VERBOSE
        );
        // Close the socket
        fclose($this->socket);
        $this->socket = null;
    }
    /**
     * This makes multiple gateways able to register on the same SQL row
     *
     * @param string $IP The string to decode
     *
     * @return mixed false on failure, Array of gateway information on success
     */
    function decodeIP($IP)
    {
        $ret = array();
        if (is_string($IP)) {
            // This gives us the old way
            if (stristr($IP, ":") === false) {
                return $IP;
            }
            $ip = explode("\n", $IP);
            foreach ($ip as $line) {
                if (empty($line)) {
                    continue;
                }
                $l = explode(":", $line);
                $ret[$l[0]] = $l[1];
            }
        }
        return $ret;
    }

    /**
     * This makes multiple gateways able to register on the same SQL row
     *
     * @param string $array The array to encode
     *
     * @return mixed false on failure, Array of gateway information on success
     */
    function encodeIP($array)
    {
        $ret = "";
        if (is_array($array)) {
            foreach ($array as $key => $val) {
                $ret .= $key.":".$val."\n";
            }
        } if (is_string($array)) {
            return $array;
        }
        return $ret;
    }
    /**
    * Write data out a socket
    *
    * @param string $string The string to send out
    *
    * @return int The number of bytes written on success, false on failure
    */
    function write($string)
    {
        if (!$this->connect()) {
            return false;
        }
        usleep(mt_rand(500, 10000));
        $string = devInfo::dehexify($string);
        $return = @fwrite($this->socket, $string);
        $this->vprint("Wrote: $return chars ($string) on ".$this->socket, 5);
        return($return);
    }

    /**
    * Read data from the server
    *
    * @param int $maxChars The number of characters to read
    *
    * @return int Read bytes on success, false on failure
    */
    function read($maxChars = 50)
    {
        if (!$this->connect()) {
            return false;
        }
        $read = array($this->socket);

        $socks = @stream_select($read, $write, $except, $this->data["Timeout"]);

        $string = "";
        if ($socks === false) {
            // This bit of code I am not sure how to get to.  It is unlikely it will
            // ever get hit, but it will stop a fatal error if it is hit.
            // @codeCoverageIgnoreStart
            $this->vprint("Bad Connection", HUGnetClass::VPRINT_VERBOSE);
            return false;
            // @codeCoverageIgnoreEnd
        } else if (count($read) > 0) {
            foreach ($read as $tsock) {
                $string .= @fread($tsock, $maxChars);
            }
        }
        $this->vprint(
            "read: ".strlen($string)." chars on ".$this->socket
            ." (".devInfo::hexifyStr($string).")",
            HUGnetClass::VPRINT_DEBUG
        );
        return devInfo::hexifyStr($string);
    }

    /**
    * Sends out a packet
    *
    * @param PacketContainer &$pkt The packet to send out
    *
    * @return bool true on success, false on failure
    */
    function sendPkt(PacketContainer &$pkt)
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
            $this->buffer .= $this->read(1);
            $ret = $pkt->recv($this->buffer);
        } while (($ret === false) && ($timeout > time()));
        return $ret;
    }
    /**
    * function to set DeviceID
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setDeviceID($value)
    {
        if (is_int($value)) {
            $value = dechex($value);
        }
        $this->data["DeviceID"] = self::stringSize($value, 6);
    }

}
?>
