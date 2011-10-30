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
class NullSocket
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
    * Sets our configuration
    *
    * @param string $name   The name of this socket
    * @param array  $config The configuration to use
    */
    private function __construct($name, $config)
    {
        $this->_name   = $name;
        $this->_config = $config;
    }
    /**
    * Creates the object
    *
    * @param string $name   The name of this socket
    * @param array  $config The configuration to use
    *
    * @return null
    */
    public function &factory($name, $config = array())
    {
        return new NullSocket($name, (array)$config);
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
        System::exception(
            "No connection available on ".$this->_name,
            101
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
        System::exception(
            "No connection available on ".$this->_name,
            101
        );
        // @codeCoverageIgnoreStart
        // Due to the exception here the function never ends
    }
    // @codeCoverageIgnoreEnd

}
?>
