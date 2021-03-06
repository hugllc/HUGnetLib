<?php
/**
 * This file howses the socket class
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
 * @package    HUGnetLib
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\network\physical;
/** This is our interface */
require_once dirname(__FILE__)."/PhysicalInterface.php";
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
 * "quiet"    - Optional - If true no exceptions are thrown
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
final class Socket implements PhysicalInterface
{
    /**
    * This is the maximum number of bytes that we read
    */
    const MAX_BYTES = 1024;
    /**
    * This is the maximum number of bytes that we read
    */
    const WAIT_SEC = 0;
    /**
    * This is the maximum number of bytes that we read
    */
    const WAIT_USEC = 10000;
    /**
    * This is the maximum number of bytes that we read
    */
    const WAIT_CONN = 5;
    /**
    * This our configuration resides here
    */
    private $_config = array();
    /**
    * This is the system object to use
    */
    private $_system = "";
    /**
    * This our socket
    */
    private $_socket;
    /**
    * The last time we failed to connect
    */
    private $_lastConnectFail = 0;
    /**
    * This our configuration resides here
    */
    private $_defaultConfig = array(
        "quiet"    => true,
        "type"     => AF_INET,
        "port"     => null,
        "location" => "",
    );
    /**
    * Sets our configuration
    *
    * @param object &$system The system object to use
    * @param array  $config  The configuration to use
    */
    private function __construct(&$system, $config)
    {
        $this->_system = &$system;
        $this->_config = array_merge($this->_defaultConfig, $config);
        if (is_string($this->_config["type"]) && defined($this->_config["type"])) {
            $this->_config["type"] = constant($this->_config["type"]);
        }
        // Connect immediately
        $this->_connect();
    }
    /**
    * Creates the object
    *
    * @param object &$system The system object to use
    * @param array  $config  The configuration to use
    *
    * @return null
    */
    static public function &factory(&$system, $config = array())
    {
        $obj = new Socket($system, (array)$config);
        return $obj;
    }

    /**
    * Disconnects from the database
    *
    */
    public function __destruct()
    {
        $this->_disconnect();
    }

    /**
    * Disconnects from the database
    *
    * @return null
    */
    private function _disconnect()
    {
        if (is_resource($this->_socket)) {
            socket_close($this->_socket);
        }
    }
    /**
    * Sets up the connection to the socket
    *
    * @return null
    */
    private function _connect()
    {
        if (is_resource($this->_socket)) {
            return true;
        }
        if ($this->_lastConnectFail > ($this->_system->now() - self::WAIT_CONN)) {
            return false;
        }
        $this->_lastConnectFail = 0;

        $this->_system->out(
            $this->_config["name"]."(".$this->_config["driver"].") Opening "
            ."connection to ".$this->_config["location"],
            6
        );
        $this->_socket = @socket_create($this->_config["type"], SOCK_STREAM, 0);
        $bound = @socket_connect(
            $this->_socket, $this->_config["location"], $this->_config["port"]
        );
        $this->_system->fatalError(
            "Failed to connect to socket ".print_r($this->_config, true),
            !$bound && !$this->_config["quiet"]
        );
        if ((socket_last_error() > 0) && $this->_config["quiet"]) {
            socket_clear_error();
            $this->_system->out(
                "Failed to connect to socket.  Waiting 5 seconds",
                3
            );
            $this->_lastConnectFail = $this->_system->now();
            return false;
        }
        socket_set_nonblock($this->_socket);
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
        if (is_resource($this->_socket) && in_array($this->_socket, $ready)) {
            $return = \HUGnet\Util::hexify(
                @socket_read($this->_socket, self::MAX_BYTES)
            );
            if (socket_last_error($this->_socket) > 0) {
                socket_clear_error($this->_socket);
                $this->_disconnect();
                $this->_connect();
            }
            $this->_system->out(
                $this->_config["name"]."(".$this->_config["driver"].") -> ".$return,
                6
            );
        }
        return $return;
    }
    /**
    * Sets up the class
    *
    * @param string $string The string to write
    *
    * @return null
    */
    private function _write($string)
    {
        if (!is_resource($this->_socket)) {
            return 0;
        }
        $this->_system->out(
            $this->_config["name"]."(".$this->_config["driver"].") <- ".$string,
            6
        );
        return @socket_write($this->_socket, \HUGnet\Util::binary($string));
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
        $read  = array($this->_socket);
        $write = array();
        $exe = array();
        $ready = @socket_select(
            $read, $write, $exe, self::WAIT_SEC, self::WAIT_USEC
        );
        return $ready;
    }
    /**
    * Checks to see if this socket is available
    *
    * @return Socket object
    */
    public function available()
    {
        $this->_connect();
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
        $this->_connect();
        return (int)$this->_write((string)$string);
    }
    /**
    * Reads from the socket
    *
    * @return string on success, False on failure
    */
    public function read()
    {
        $this->_connect();
        $read = array();
        $this->_ready($read);
        return $this->_read($read);
    }

}
?>
