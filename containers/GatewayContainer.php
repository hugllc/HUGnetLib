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
class GatewayContainer extends HUGnetContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "GatewayKey" => 0,
        "GatewayIP" => "127.0.0.1",
        "GatewayPort" => "2000",
        "GatewayName" => "Localhost",
        "GatewayLocation" => "",
        "database" => "",
        "FirmwareStatus" => "RELEASE",
        "isVisible" => 0,
        "Timeout" => 2,
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** @var Unix Socket This is the socket for our connection */
    protected $socket = null;

    /**
    * Disconnects from the database
    *
    * @return object PDO object, null on failure
    */
    public function __destruct()
    {
        $this->disconnect();
    }
    /**
    * Creates a database object
    *
    * @param int    $verbose The verbosity number
    *
    * @return object PDO object, null on failure
    */
    public function connected()
    {
        if (is_null($this->socket)) {
            return false;
        }
        if (feof($this->socket)) {
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
        $this->vprint("Opening socket to ".$this->GatewayIP.":".$this->Port, 1);
        $this->socket = @fsockopen(
            $this->GatewayIP,
            $this->Port,
            $this->Errno,
            $this->Error,
            $this->Timeout
        );
        if (!is_null($this->socket)) {
            stream_set_blocking($this->socket, false);
            $this->vprint(
                "Opened the Socket ".$this->socket." to "
                .$this->GatewayIP.":".$this->Port,
                2
            );
            return true;
        }
        $this->vprint(
            "Connection to ".$this->GatewayIP." Failed."
            ." Error ".$this->Errno.": ".$this->Error,
            2
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
        $this->vprint("Closing Connection", 1);
        // Close the socket
        fclose($this->socket);
        $this->socket = null;
    }
    /**
     * Try to automatically find out which gateway to use
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
     * Try to automatically find out which gateway to use
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
        if (!$this->connected()) {
            return false;
        }
        usleep(mt_rand(500, 10000));
        $return = @fwrite($this->socket, $string);
        $this->vprint("Wrote: ".$return." chars  on ".$this->socket, 5);
        return($return);
    }

    /**
    * Read data from the server
    *
    * @param int $chars The number of characters to read
    *
    * @return int Read bytes on success, false on failure
    */
    function read($maxChars = 50)
    {
        if (!$this->connected()) {
            return false;
        }
        $read = array($this->socket);

        $socks = @stream_select(
            $read,
            $write = null,
            $except = null,
            $this->timeout
        );

        $string = "";
        if ($socks === false) {
            $this->vprint("Bad Connection", 1);
            return false;
        } else if (count($read) > 0) {
            foreach ($read as $tsock) {
                $string .= @fread($tsock, $maxChars);
            }
        }
        $this->vprint("read: ".strlen($string)." chars on ".$this->socket, 5);
        return $string;
    }
}
?>
