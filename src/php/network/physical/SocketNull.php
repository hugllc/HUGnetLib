<?php
/**
 * This file howses the socket class
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\network\physical;
/**
 * This class hands out references to the sockets that are available.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
final class SocketNull
{
    /**
     * This is the name of our socket
     */
    private $_name = "";
    /**
     * This our configuration resides here
     */
    private $_config = "";
    /**
    * This is the system object to use
    */
    private $_system = "";
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
        return new SocketNull($system, (array)$config);
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
        return false;
    }
    /**
    * Writes to the socket
    *
    * @return int|bool # of bytes on success, False on failure
    */
    public function write()
    {
        \HUGnet\System::exception(
            "No connection available on ".$this->_name,
            "Runtime"
        );
        // @codeCoverageIgnoreStart
        // Due to the exception here the function never ends
    }
    // @codeCoverageIgnoreEnd
    /**
    * Reads from the socket
    *
    * @return int|bool # of bytes on success, False on failure
    */
    public function read()
    {
        \HUGnet\System::exception(
            "No connection available on ".$this->_name,
            "Runtime"
        );
        // @codeCoverageIgnoreStart
        // Due to the exception here the function never ends
    }
    // @codeCoverageIgnoreEnd

}
?>
