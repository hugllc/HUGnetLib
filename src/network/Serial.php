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
 * "quiet"    - Optional - If true no exceptions are thrown
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
final class Serial
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
    * This our configuration resides here
    */
    private $_config = array();
    /**
    * This our socket
    */
    private $_port;
    /**
    * This our configuration resides here
    */
    private $_defaultConfig = array(
        "quiet" => false,
        "baud" => 115200,
        "rtscts" => true,
    );
    /**
    * This our configuration resides here
    */
    private $_validBaud = array(115200, 38400);
    /**
    * Sets our configuration
    *
    * @param array $config The configuration to use
    */
    private function __construct($config)
    {
        $this->_config = array_merge($this->_defaultConfig, $config);
        // Connect immediately
        $this->_connect();
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
        return new Serial((array)$config);
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
        if (is_resource($this->_port)) {
            @fclose($this->_port);
        }
    }
    /**
    * Sets up the connection to the socket
    *
    * @return null
    */
    private function _connect()
    {
        if (is_resource($this->_port)) {
            return true;
        }
        \HUGnet\System::exception(
            "Serial port doesn't exist:  ".$this->_config["location"],
            "Runtime",
            !file_exists($this->_config["location"]) && !$this->_config["quiet"]
        );
        $this->_setupPort();
        $this->_port = @fopen($this->_config["location"], "r+b");
        \HUGnet\System::exception(
            "Failed to open port:  ".$this->_config["location"],
            "Runtime",
            !is_resource($this->_port) && !$this->_config["quiet"]
        );

        @stream_set_blocking($this->_port, 0);
    }
    /**
    * Sets up the connection to the socket
    *
    * @return null
    */
    private function _setupPort()
    {
        $command  = "stty -F ".$this->_config["location"];
        $command .= " ".(int)$this->_config["baud"];
        $command .= ($this->_config["rtscts"]) ? " crtscts" : " -crtscts" ;
        // 1 Stop bit, 8 databits, no parity
        @exec(
            $command." -cstopb cs8 -parenb clocal -ixon -ixoff 2>&1",
            $out, $return
        );
        \HUGnet\System::exception(
            "stty failed on ".$this->_config["name"]." (".$return."):  "
            .implode($out, "\n"),
            "Runtime",
            ($return  != 0) && !$this->_config["quiet"]
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
        if (in_array($this->_port, $ready)) {
            $return = \HUGnet\Util::hexify(
                @fread($this->_port, self::MAX_BYTES)
            );
            \HUGnet\VPrint::out(
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
        \HUGnet\VPrint::out(
            $this->_config["name"]."(".$this->_config["driver"].") <- ".$string,
            6
        );
        return @fwrite($this->_port, \HUGnet\Util::binary($string));
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
        $read  = array($this->_port);
        $write = array();
        $exe = array();
        $ready = @stream_select(
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
        if (file_exists($this->_config["location"])) {
            $this->_connect();
            return is_resource($this->_port);
        }
        return false;
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
