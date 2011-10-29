<?php
/**
 * This file howses the socket class
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet;
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
 * "quiet"    - Optional - If true no exceptions are used
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Socket
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
    private $_maxRead = 1024;
    /**
    * This our configuration resides here
    */
    private $_defaultConfig = array(
        "quiet" => false,
    );
    /**
    * Sets our configuration
    *
    * @param array $config The configuration to use
    */
    private function __construct($config)
    {
        $this->_config = array_merge($this->_defaultConfig, $config);
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
        return new Socket((array)$config);
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
    */
    public function _disconnect()
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
        $this->_socket = @socket_create($this->_config["type"], SOCK_STREAM, 0);
        $bound = @socket_connect(
            $this->_socket, $this->_config["location"], $this->_config["port"]
        );
        System::exception(
            "Failed to connect to socket ".print_r($this->_config, true),
            102,
            !$bound && !$this->_config["quiet"]
        );
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
        if (in_array($this->_socket, $ready)) {
            $return = @socket_read($this->_socket, 1024);
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
        return @socket_write($this->_socket, $string);
    }

    /**
    * Sets up the class
    *
    * @param array &$read  This will be the array of ready items for reading
    *
    * @return null
    */
    private function _ready(&$read)
    {
        $read  = array($this->_socket);
        $write = array();
        $exe = array();
        $ready = @socket_select($read, $write, $exe, 0, 10000);
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
