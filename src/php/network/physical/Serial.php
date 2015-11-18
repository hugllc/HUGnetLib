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
final class Serial implements PhysicalInterface
{
    /**
    * This is the maximum number of bytes that we read
    */
    const MAX_BYTES = 1024;
    /**
    * This is the maximum number of bytes that we read
    */
    const RETRY_TIMEOUT = 10;
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
    * This is the system object to use
    */
    private $_system = "";
    /**
    * This our socket
    */
    private $_port;
    /**
    * This our socket
    */
    private $_wasConnected;
    /**
    * This is the time of the last byte we got
    */
    private $_lastReceived = 0;
    /**
    * The last time we failed to connect
    */
    private $_lastConnectFail = 0;
    /**
    * This our configuration resides here
    */
    private $_defaultConfig = array(
        "quiet" => true,
        "baud" => 115200,
        "rtscts" => false,
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
        $obj = new Serial($system, (array)$config);
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
        if (is_resource($this->_port)) {
            fclose($this->_port);
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
        $port = $this->_config["location"];
        $this->_checkPort($port);
        $time = ($this->_system->now() - self::RETRY_TIMEOUT);
        if ($this->_lastConnectFail < $time) {
            $this->_setupPort($port);
            $this->_port = @fopen($port, "rn+b");
            $this->_system->fatalError(
                "Failed to open port:  ".$this->_config["location"],
                !is_resource($this->_port) && !$this->_config["quiet"]
            );
            if (is_resource($this->_port)) {
                $this->_system->out("Using port $port", 1);
                $this->_wasConnected = true;
                $this->_lastConnectFail = 0;
            } else {
                $this->_lastConnectFail = $this->_system->now();
            }
            @stream_set_blocking($this->_port, 0);
        }
    }
    /**
    * Sets up the connection to the socket
    *
    * This is not really testable
    *
    * @param string &$port The port to use
    *
    * @return null
    * @codeCoverageIgnore
    */
    private function _checkPort(&$port)
    {
        for ($j = 0; $j < 20; $j++) {
            for ($i = 0; $i < 20; $i++) {
                if (file_exists($port.$i)) {
                    $port = $port.$i;
                    break;
                }
            }
            if (!$this->_config["quiet"]) {
                break;
            }
        }
        $this->_system->fatalError(
            "Serial port doesn't exist:  ".$this->_config["location"],
            !file_exists($port) && !$this->_config["quiet"]
        );
        if ($this->_wasConnected) {
            $this->_system->out(
                "Serial port disappeared.  "
                ."Trying again in ".self::RETRY_TIMEOUT." seconds.",
                1
            );
            $this->_wasConnected = false;
        }
    }
    /**
    * Sets up the connection to the socket
    *
    * This is not really testable
    *
    * @param string $port The port to use
    *
    * @return null
    * @codeCoverageIgnore
    */
    private function _setupPort($port)
    {
        if (stristr($this->_config["location"], "com") !== false) {
            $this->_setupPortWindows($port);
        } else {
            $this->_setupPortLinux($port);
        }
    }
    /**
    * Sets up the connection to the socket
    *
    * This is not really testable
    *
    * @param string $port The port to use
    *
    * @return null
    * @codeCoverageIgnore
    */
    private function _setupPortLinux($port)
    {
        $command  = "stty -F ".$port;
        $command .= " ".(int)$this->_config["baud"];
        $flags  = " -parenb -parodd cs8 hupcl -cstopb cread clocal -ignbrk -brkint";
        $flags .= " -ignpar -parmrk -inpck -istrip -inlcr -igncr -icrnl -ixon";
        $flags .= " -ixoff -iuclc -ixany -imaxbel -iutf8 -opost -olcuc -ocrnl";
        $flags .= " -onlcr -onocr -onlret -ofill -ofdel nl0 cr0 tab0 bs0 vt0 ";
        $flags .= " ff0 -isig -icanon -iexten -echo -echoe -echok -echonl";
        $flags .= " -noflsh -xcase -tostop -echoprt -echoctl -echoke";
        $flags .= ($this->_config["rtscts"]) ? " crtscts" : " -crtscts" ;
        // 1 Stop bit, 8 databits, no parity
        exec(
            "$command $flags 2>&1",
            $out, $return
        );
        $this->_system->fatalError(
            "stty failed on ".$this->_config["name"]." (".$return."):  "
            .implode($out, "\n"),
            ($return  != 0) && !$this->_config["quiet"]
        );
    }
    /**
    * Sets up the connection to the socket
    *
    * See http://www.microsoft.com/resources/documentation/windows/xp/all/proddocs/
    * en-us/mode.mspx?mfr=true
    * for more information.
    *
    * Also see
    * http://stackoverflow.com/questions/627965/serial-comm-with-php-on-windows
    *
    * This is not really testable
    *
    * @param string $port The port to use
    *
    * @return null
    * @codeCoverageIgnore
    */
    private function _setupPortWindows($port)
    {
        $command  = "mode ".$port;
        $command .= " BAUD=".(int)$this->_config["baud"];
        if ($this->_config["rtscts"]) {
            $command = " RTS=hs OCTS=on";
        } else {
            $command = " RTS=on OCTS=off";
        }
        // 1 Stop bit, 8 databits, no parity
        @exec(
            $command." DATA=8 STOP=1 PARITY=n TO=on DTR=off XON=off",
            $out, $return
        );
        $this->_system->fatalError(
            "mode failed on ".$this->_config["name"]." (".$return."):  "
            .implode($out, "\n"),
            ($return  != 0) && !$this->_config["quiet"]
        );
    }
    /* @codeCoverageIgnoreEnd */
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
            $this->_system->out(
                $this->_config["name"]."(".$this->_config["driver"].") -> ".$return,
                6
            );
            if (!is_resource($this->_port) || feof($this->_port)) {
                $this->_disconnect();
                $this->_connect();
            }
            if (!empty($return)) {
                $this->_lastReceived = microtime(true);
            }
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
        if ($this->_lastReceived < (microtime(true) - 0.015)) { 
            $this->_system->out(
                $this->_config["name"]."(".$this->_config["driver"].") <- ".$string,
                6
            );
            return @fwrite($this->_port, \HUGnet\Util::binary($string));
        } else {
            return 0;
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
