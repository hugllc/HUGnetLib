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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\cli;
/**
 * This code interprets command line arguments
 *
 * The configuration array should consist of an array with each key being a single
 * letter that is the command line argument.  The value attached to each key
 * should be an array with the following:
 *    "name" - optional - string The name in the config array output
 *    "type" - optional - The return type for the argument
 *    "args" - optional - Is there extra stuff that this argument is requiring?
 *
 * The arguments can be gotten with $obj->x where 'x' is the command line argument.
 * So say your argument was -v you would get it by $obj->v.  For maximum accuracy
 * the arguments should be set up in the config array.  If any of your arguments
 * take arguments then they MUST be defined in the config array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage CLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
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
    private $_config = array();
    /** These are pretty standard config changes */
    private $_defaultConfig = array(
        "q" => array("name" => "quiet", "type" => "bool", "default" => false),
        "v" => array("name" => "verbose", "type" => "int", "default" => 0),
        "d" => array("name" => "debug", "type" => "bool", "default" => false),
        "t" => array("name" => "test", "type" => "bool", "default" => false),
        "f" => array("name" => "file", "type" => "string", "args" => true),
        "n" => array("type" => "bool"),
    );
    /** These are the locations we are going to try to find a config, in order */
    private $_configLocations = array(
        "./config.ini", "/etc/hugnet/config.ini"
    );

    /**
    * Sets our configuration
    *
    * @param array $args   The argument array
    * @param int   $count  The argument count
    * @param array $config The configuration of command line args
    */
    private function __construct($args, $count, $config)
    {
        $this->_argv = $args;
        $this->_argc = $count;
        $this->_config = array_merge($this->_defaultConfig, $config);
        $this->_defaults();
        $this->_interpret();
    }
    /**
    * Creates the object
    *
    * @param array $args   The argument array
    * @param int   $count  The argument count
    * @param array $config The configuration of command line args
    *
    * @return Args object
    */
    public function &factory($args, $count, $config = array())
    {
        return new Args((array)$args, (int)$count, (array)$config);
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
        return $this->_value($name);
    }
    /**
    * Creates the object
    *
    * @param string $name The name of the argument to retrieve
    *
    * @return mixed
    */
    private function _value($name)
    {
        switch ($this->_config[$name]["type"]) {
        case "int":
            $return = (int)$this->_arguments[$name];
            break;
        case "string":
            $return = (string)$this->_arguments[$name];
            break;
        case "bool":
            $return = (bool)$this->_arguments[$name];
            break;
        default:
            $return = $this->_arguments[$name];
            break;
        }
        return $return;
    }
    /**
    * Creates the config to go with the command line
    *
    * @return Configuration array
    */
    public function config()
    {
        $return = array();
        if (file_exists($this->f)) {
            \HUGnet\VPrint::out("Using config at ".$this->f);
            $return = parse_ini_file($this->f, true);
        } else if (!$this->n) {
            $return = $this->_findConfig();
        }
        foreach ($this->_config as $key => $conf) {
            if (isset($conf["name"])) {
                $return[$conf["name"]] = $this->_value($key);
            }
        }
        return $return;
    }
    /**
    * Creates the config to go with the command line
    *
    * @return Configuration array
    */
    private function _findConfig()
    {
        foreach ($this->_configLocations as $file) {
            if (file_exists($file)) {
                \HUGnet\VPrint::out("Found config at $file");
                $this->_arguments["f"] = $file;
                return parse_ini_file($file, true);
            }
        }
        return array();
    }
    /**
    * Sets the defaults
    *
    * @return null
    */
    private function _defaults()
    {
        foreach ($this->_config as $key => $conf) {
            if (isset($conf["default"])) {
                $val = $conf["default"];
                if (is_bool($val)) {
                    $val = (int)$val;
                }
                $this->_arguments[$key] = $val;
            }
        }

    }
    /**
    * Pulls the arguments apart and stores them
    *
    * @return null
    */
    private function _interpret()
    {
        $this->_name = trim($this->_argv[0]);
        for ($i = 1; $i < $this->_argc; $i++) {
            $arg = $this->_fixArg($this->_argv[$i]);
            if ($this->_config[$arg]["args"]
                && (substr($this->_argv[$i+1], 0, 1) != "-")
                && (strlen($this->_argv[$i+1]) > 0)
            ) {
                $this->_arguments[$arg] = $this->_argv[$i+1];
                $i++;
            } else if (strlen($arg) > 0) {
                $this->_arguments[$arg]++;
            }
        }
    }

    /**
    * Pulls the arguments apart and stores them
    *
    * @param int $arg the argument to tear apart
    *
    * @return null
    */
    private function _fixArg($arg)
    {
        if (substr($arg, 0, 1) == "-") {
            $args = str_split(substr($arg, 1), 1);
            for ($i = 0; $i < (count($args) - 1); $i++) {
                $this->_arguments[$args[$i]]++;
            }
            $ret = $args[$i];
        } else {
            $this->_arguments["loose"][] = $arg;
        }
        return $ret;
    }
}
?>
