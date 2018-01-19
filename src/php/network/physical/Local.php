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
 * This class hands out references to the sockets that are available.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
final class Local implements PhysicalInterface
{
    /**
     * This is the name of our socket
     */
    private $_name = "";
    /**
     * This is the system object to use
     */
    private $_system = "";
    /**
     * This our configuration resides here
     */
    private $_config = "";
    /**
    * Sets our configuration
    *
    * @param object &$system The system object to use
    * @param array  $config  The configuration to use
    */
    private function __construct(&$system, $config)
    {
        $this->_system = &$system;
        $this->_config = $config;
        $this->_name = $this->_config["name"];
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
        return new Local($system, (array)$config);
    }

    /**
    * Disconnects from the database
    *
    */
    public function __destruct()
    {
    }

    /**
    * Checks to see if this socket is available
    *
    * @return Socket object
    */
    public function available()
    {
        return true;
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
        $this->_buffer .= (string)$string;
        return strlen($string) / 2;
    }
    /**
    * Reads from the socket
    *
    * @return int|bool # of bytes on success, False on failure
    */
    public function read()
    {
        $ret = $this->_buffer;
        $this->_buffer = "";
        return $ret;
    }

}
?>
