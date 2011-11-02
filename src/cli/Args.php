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
 * @subpackage CLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\cli;
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Args
{
    /** This is our network */
    private $_arguments = array();
    /** This is the argument array */
    private $_argv = array();
    /** This the number of arguments */
    private $_argc = 0;
    /** This is our name */
    private $_name = "";
    /** These are pretty standard config changes */
    private $_config = array(
        "q" => "quiet",
        "v" => "verbose",
    );

    /**
    * Sets our configuration
    *
    * @param array $argv The argument array
    * @param int   $argc The argument count
    */
    private function __construct($argv, $argc)
    {
        $this->_argv = $argv;
        $this->_argc = $argc;
        $this->_interpret();
    }
    /**
    * Creates the object
    *
    * @param array $argv The argument array
    * @param int   $argc The argument count
    *
    * @return Args object
    */
    public function &factory($argv, $argc)
    {
        return new Args((array)$argv, (int)$argc);
    }

    /**
    * Disconnects from the database
    *
    */
    public function __destruct()
    {
    }
    /**
    * Creates the object
    *
    * @param string $name The name of the argument to retrieve
    *
    * @return Args object
    */
    public function __get($name)
    {
        return $this->_arguments[$name];
    }
    /**
    * Creates the object
    *
    * @return Configuration array
    */
    public function config()
    {
        $return = array();
        foreach ($this->_config as $key => $name) {
            if (isset($this->_arguments[$key])) {
                $return[$name] = $this->_arguments[$key];
            }
        }
        return $return;
    }

    /**
    * Pulls the arguments apart and stores them
    *
    * @return null
    */
    private function _interpret()
    {
        $this->_name = trim($this->_argv[0]);
        for ($i = 1; $i < count($this->_argv); $i++) {
            if (substr($this->_argv[$i], 0, 1) == "-") {
                $arg = substr($this->_argv[$i], 1);
                if ((substr($this->_argv[$i+1], 0, 1) != "-")
                    && (strlen($this->_argv[$i+1]) > 0)
                ) {
                    $this->_arguments[$arg] = $this->_argv[$i+1];
                    $i++;
                } else {
                    $this->_arguments[$arg]++;
                }
            } else {
                $this->_arguments["loose"][] = $this->_argv[$i];
            }
        }
    }


}
?>
