<?php
/**
 * This file howses the socket class
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2011 Hunt Utilities Group, LLC
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
 * @subpackage CLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\cli;
/** This is our system class */
require_once dirname(__FILE__)."/../system/System.php";

/**
 * This code routes packets to their correct destinations.
 *
 * This is the router class, essentially.  It will take packets and figure out
 * which network interface to send them out.  This implements the Network layer
 * of the OSI model.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage CLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class CLI
{
    /** This says if we should loop or not */
    private $_loop = true;
    /** The config we are using */
    private $_config = array();
    /**
    * Sets our configuration
    *
    * @param mixed &$config The configuration to use
    */
    protected function __construct(&$config)
    {
        $this->setConfig($config);
        pcntl_signal(SIGINT, array($this, "quit"));
    }

    /**
    * Creates the object
    *
    * @param array &$config The configuration to use
    *
    * @return null
    */
    public function &factory(&$config = array())
    {
        $class = get_called_class();
        return new $class($config);
    }

    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function __destruct()
    {
        // Shut down the network
        unset($this->_system);
    }
    /**
    * Disconnects from the database
    *
    * @param mixed &$config Object or array configuration
    *
    * @return null
    */
    protected function setConfig(&$config)
    {
        if (is_object($config)) {
            if (is_a($config, "HUGnet\cli\Args")) {
                $this->_config = $config->config();
            }
        }
    }
    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function system()
    {
        if (!is_object($this->_system)) {
            $this->_system = \HUGnet\System::factory($this->_config);
        }
        return $this->_system;
    }
    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function main()
    {
        pcntl_signal_dispatch();
        //$this->system()->main();
    }
    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function quit()
    {
        $this->_loop = false;
    }
    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function loop()
    {
        return $this->_loop;
    }


}
?>
