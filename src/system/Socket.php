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
 * This class hands out references to the sockets that are available.
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
final class Socket
{
    /** This is where we store our sockets */
    private $_sockets = array();
    /** This is where we store our config */
    private $_config = array();

    /**
    * Sets our configuration
    *
    * @param object &$system The system object to use
    * @param array  $config  The configuration to use
    */
    private function __construct(&$system, $config)
    {
        $this->_system =& $system;
        $this->_config = $config;
    }
    /**
    * Creates the object
    *
    * @param object &$system The system object to use
    * @param array  $config  The configuration to use
    *
    * @return null
    */
    public function &factory(&$system, $config = array())
    {
        return new Socket($system, (array)$config);
    }

    /**
    * Disconnects from the database
    *
    */
    public function __destruct()
    {
        foreach (array_keys($this->_sockets) as $key) {
            unset($this->_sockets[$key]);
        }
    }

    /**
    * Checks to see if we are connected to a database
    *
    * @param string $socket The group to check
    *
    * @return Socket object
    */
    public function available($socket = "default")
    {
        $this->_connect($socket);
        return is_object($this->_sockets[$socket]);
    }
    /**
    * Checks to see if we are connected to a database
    *
    * @param string $socket The group to check
    *
    * @return Socket object
    */
    public function &socket($socket = "default")
    {
        $this->_connect($socket);
        System::exception(
            "No connection available on socket ".$socket,
            101,
            !is_object($this->_sockets[$socket])
        );
        return $this->_sockets[$socket];
    }

    /**
    * Connects to a database group
    *
    * @param string $socket The socket to connect to
    *
    * @return bool True on success, false on failure
    */
    private function _connect($socket)
    {
        if (is_object($this->_sockets[$socket])) {
            return;
        }
        if (is_object($this->_config[$socket])) {
            $this->_sockets[$socket] = &$this->_config[$socket];
            return;
        }
        $this->_findDriver($socket);
    }
    /**
    * Connects to a database group
    *
    * @param string $socket The socket to use
    *
    * @return null
    */
    private function _findDriver($socket)
    {
        $class = $this->_config[$socket]["driver"];
        if (!class_exists($class)) {
            $class = "\\".$class;
        }
        if (!class_exists($class)) {
            $class = "\\HUGnet".$class;
        }
        if (class_exists($class)) {
            $this->_sockets[$socket] = $class::factory(
                $this->_config[$socket]
            );
            return;
        }
        // Last resort include NullSocket
        include_once dirname(__FILE__)."/../sockets/NullSocket.php";
        $this->_sockets[$socket] = NullSocket::factory(
            $socket, $this->_config[$socket]
        );
    }

}
?>
