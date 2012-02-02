<?php
/**
 * This file howses the socket class
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\network\physical;
/**
 * This class implements a sever for sockets.
 *
 * This class was written based on the tutorial at:
 *
 * http://devzone.zend.com/article/1086
 * by Ori Staub posted on August 27, 2003
 *
 * The configuration options for this are as follows:
 * "type"     - Required - Should be AF_UNIX or AF_INET (constants not strings)
 * "location" - Required - IP address for AF_INET, file for AF_UNIX
 * "port"     - Required - Only for AF_INET - TCP Port to use
 * "bus"      - Optional - If true clients see eachothers writes
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
final class SocketServer
{
    /**
    * This our configuration resides here
    */
    private $_config = array();
    /**
    * This our socket
    */
    private $_socket;
    /**
    * This maximum read length
    */
    private $_maxClients = 10;
    /**
    * These are our clients
    */
    private $_clients = array();
    /**
    * This our configuration resides here
    */
    private $_defaultConfig = array(
        "type"     => AF_UNIX,
        "bus"      => true,
        "location" => null,
        "port"     => null,
        "quiet"    => false,
    );
    /**
    * Sets our configuration
    *
    * @param array $config The configuration to use
    */
    private function __construct($config)
    {
        $this->_config = array_merge($this->_defaultConfig, $config);
        if (is_string($this->_config["type"]) && defined($this->_config["type"])) {
            $this->_config["type"] = constant($this->_config["type"]);
        }
        if ($this->_config["force"] && ($this->_config["type"] == AF_UNIX)) {
            // Remove the old one...
            unlink($this->_config["location"]);
        }
        $this->_setup();
        \HUGnet\System::exception(
            "Failed to create socket with\n ".print_r($config, true),
            "Runtime",
            !is_resource($this->_socket)
        );
    }
    /**
    * Creates the object
    *
    * @param array $config The configuration to use
    *
    * @return null
    */
    public function &factory($config = array())
    {
        $obj = new SocketServer((array)$config);
        return $obj;
    }

    /**
    * Disconnects from the database
    *
    */
    public function __destruct()
    {
        foreach (array_keys($this->_clients) as $key) {
            $this->_disconnect($key);
        }
        \HUGnet\VPrint::out("Closing socket", 3);
        if (is_resource($this->_socket)) {
            socket_close($this->_socket);
        }
        if (($this->_config["type"] == AF_UNIX)
            && file_exists($this->_config["location"])
        ) {
            \HUGnet\VPrint::out("Removing file ".$this->_config["location"], 3);
            unlink($this->_config["location"]);
        }
    }
    /**
    * Sets up the class
    *
    * @param int $key The key of the client to disconnect
    *
    * @return null
    */
    private function _disconnect($key)
    {
        if (is_resource($this->_clients[$key]['socket'])) {
            socket_close($this->_clients[$key]['socket']);
            unset($this->_clients[$key]);
        }
    }
    /**
    * Sets up the class
    *
    * @return null
    */
    private function _setup()
    {
        \HUGnet\VPrint::out(
            $this->_config["name"].": Setting up at "
            .$this->_config["location"],
            6
        );
        $this->_socket = @socket_create($this->_config["type"], SOCK_STREAM, 0);
        $bound = @socket_bind(
            $this->_socket, $this->_config["location"], $this->_config["port"]
        );
        \HUGnet\System::exception(
            "Failed to bind to socket ".print_r($this->_config, true),
            "Runtime",
            !$bound
        );
        @socket_listen($this->_socket);
        @socket_set_nonblock($this->_socket);

    }
    /**
    * Reads from all ready to read from and sends the data back out to everyone
    *
    * @param array &$ready The array of ready things
    *
    * @return string The string that was read
    */
    private function _read(&$ready)
    {
        $return = "";
        for ($key = 0; $key < 10; $key++) {
            if (in_array($this->_clients[$key]['socket'], $ready)) {
                $input = \HUGnet\Util::hexify(
                    @socket_read($this->_clients[$key]['socket'], 1024)
                );
                if (strlen($input) === 0) {
                    $this->_disconnect($key);
                } else if ($input) {
                    \HUGnet\VPrint::out(
                        $this->_config["name"]."(".$this->_config["driver"]."):"
                        .$key." -> ".$input,
                        6
                    );
                    if ($this->_config["bus"]) {
                        $this->_write($key, (string)$input);
                    }
                    $return .= (string)$input;
                }
            }
        }
        return $return;
    }
    /**
    * Sets up the class
    *
    * @param int    $skip   The client doing the writing (It won't get the message)
    * @param string $string The string to write
    *
    * @return null
    */
    private function _write($skip, $string)
    {
        // Don't write anything if there is no string
        if (!is_string($string) || (strlen($string) < 1)) {
            return;
        }
        // Write to everybody who still has a connection
        foreach (array_keys($this->_clients) as $key) {
            $client = &$this->_clients[$key];
            if (!is_null($client['socket']) && ($skip !== $key)) {
                \HUGnet\VPrint::out(
                    $this->_config["name"]."(".$this->_config["driver"]."):".$key
                    ." <- ".$string, 6
                );
                @socket_write($client['socket'], \HUGnet\Util::binary($string));
            }
        }
    }

    /**
    * Sets up the class
    *
    * @param array &$read This will be the array of ready items for reading
    *
    * @return null
    */
    private function _ready(&$read)
    {
        $read  = array(0 => $this->_socket);
        $write = array();
        $exe = array();
        foreach (array_keys($this->_clients) as $key) {
            if (!is_null($this->_clients[$key]['socket'])) {
                $read[$key + 1] = $this->_clients[$key]['socket'];
            }
        }
        $ready = @socket_select(
            $read, $write, $exe, 0, 10000
        );
        if (in_array($this->_socket, $read) && ($ready > 0)) {
            $this->_connect();
            $ready--;
        }
        return $ready;
    }
    /**
    * Connects a client
    *
    * @return null
    */
    private function _connect()
    {
        for ($key = 0; $key < $this->_maxClients; $key++) {
            if (is_null($this->_clients[$key]['socket'])) {
                $this->_clients[$key]['socket'] = @socket_accept($this->_socket);
                break;
            }
        }
    }
    /**
    * Checks to see if this socket is available
    *
    * @return Socket object
    */
    public function available()
    {
        return is_resource($this->_socket);
    }
    /**
    * Writes to the socket
    *
    * @param string $string The string to write
    *
    * @return int|bool # of bytes on success, False on failure
    */
    public function write($string)
    {
        $this->_write(-99, (string)$string);
        return strlen((string)$string);
    }
    /**
    * Reads from the socket
    *
    * @return string on success, False on failure
    */
    public function read()
    {
        $read = array();
        if ($this->_ready($read) > 0) {
            return $this->_read($read);
        }
        return "";
    }

}
?>
