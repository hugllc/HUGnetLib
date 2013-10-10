<?php
/**
 * This file howses the socket class
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @subpackage UI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\ui;
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
 * @subpackage UI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ExitExpression)
 */
class CLI
{
    /** The config we are using */
    private $_config = array();
    /** The config we are using */
    private $_system = null;
    /** This is the help text to sling out */
    protected $help = "";
    /**
    * Sets our configuration
    *
    * @param mixed &$config The configuration to use
    */
    protected function __construct(&$config)
    {
        $this->setConfig($config);
    }

    /**
    * Creates the object
    *
    * @param array &$config The configuration to use
    *
    * @return null
    */
    static public function &factory(&$config = array())
    {
        $obj = new CLI($config);
        return $obj;
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
            if (is_a($config, "HUGnet\ui\Args")) {
                $this->_config = $config->config();
            } else {
                // Whatever it is, use it as our system object
                $this->_system = &$config;
                $this->_config = $this->_system->config();
            }
        } else if (is_array($config)) {
            $this->_config = $config;
        }
        if (strlen($this->_config["file"])) {
            $this->out("Using config at ".$this->_config["file"]);
        } else {
            $this->out("No config file found.  I hope that is right...", 1);
        }
        // Set up our IP address
        $this->_config["IPAddr"] = $this->_getIP();
        // Ratchet up the verbosity one level so more stuff prints
        $this->_config["verbose"]++;
        $this->_verbose = $this->_config["verbose"];
        $this->_config["html"] = false;
        return $this->_config;
    }
    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function system()
    {
        if (!is_object($this->_system)) {
            $this->out(get_class($this)." building sytem", 3);
            $this->_system = \HUGnet\System::factory($this->_config, $this);
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
        $this->system()->main();
    }

    /**
    * This function prints out string
    *
    * @param string $string The string to print out
    * @param int    $level  The verbosity level to print it at
    *
    * @return none
    */
    public function out($string = "", $level=0)
    {
        if ($this->_verbose >= $level) {
            print $string.PHP_EOL;
        }
    }
    /**
    * Sets or prints out the help text
    *
    * If no string is given it prints out the help text
    *
    * @param string $help  The help text to use
    * @param bool   $force Force the printout and exit
    *
    * @return null
    */
    public function help($help = null, $force = false)
    {
        if (is_string($help)) {
            $this->help = $help;
        }
        if (!is_string($help) || $force) {
            $this->out($this->help);
            if ($force) {
                $this->out();
                exit(0);
            }
        }
    }
    /**
    * Gets the ip address
    *
    * This gets the IP address.  Right now it only works for IPV4 addresses.
    *
    * This will only work in posix environments where ifconfig exists
    *
    * @return string IP address
    */
    private function _getIP()
    {
        $line = trim(`/sbin/ifconfig`);
        preg_match_all(
            "/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/",
            $line,
            $match
        );
        $cnt = 0;
        // This removes localhost, netmask and broadcast addresses
        do {
            $ret = trim((string)$match[0][$cnt++]);
            $local = preg_match("/127\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/", $ret);
            $netmask = preg_match("/255/", $ret);
        } while (($local + $netmask) > 0);
        return $ret;
    }
    /**
    * Set the verbosity level
    *
    * @param mixed $set (null) set the default value, otherwise set the value given
    *
    * @return null
    */
    public function verbose($set = null)
    {
        if (is_null($set)) {
            $this->_verbose = $this->_config["verbose"];
        } else if (is_int($set)) {
            $this->_verbose = $set;
        }
        return $this->_verbose;
    }
}
?>
