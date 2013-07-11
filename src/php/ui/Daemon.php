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
/** This is our base class */
require_once dirname(__FILE__)."/CLI.php";

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
class Daemon extends CLI
{
    /** This says if we should loop or not */
    private $_loop = true;
    /** This says the last time we checked for memory */
    private $_memCheck = 0;
    /** This is the max memory we can use in megabytes */
    protected $maxMemory = 50;
    /** This is our program config */
    protected $progConfig = array();
    /** This is our PID file */
    protected $pidfile = null;
    
    /**
    * Sets our configuration
    *
    * @param mixed &$config The configuration to use
    */
    protected function __construct(&$config)
    {
        parent::__construct($config);
        $this->register();
        $program = str_replace("hugnet_", "", $this->system()->get("program"));
        $this->progConfig = array_merge(
            $this->progConfig, (array)$this->system()->get($program)
        );
        if (function_exists("pcntl_signal")) {
            pcntl_signal(SIGINT, array($this, "quit"));
        }
    }
    /**
    * Sets our configuration
    *
    * @param mixed &$config The configuration to use
    */
    public function __destruct()
    {
        if (is_string($this->pidfile)) {
            @unlink($this->pidfile);
            $this->pidfile = null;
        }
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
        $obj = new Daemon($config);
        return $obj;
    }
    /**
    * Gets the configuration for this process
    *
    * @param string $field The field to get.  NULL gets everything
    *
    * @return null
    */
    protected function config($field = null)
    {
        $program = $this->system()->get("program");
        $config = $this->system()->get($program);
        if (is_null($field)) {
            return (array)$config;
        } else if (isset($config[$field])) {
            return $config[$field];
        }
        return null;
    }
    /**
    * Gets config entries
    *
    * @param string $field The field to get
    *
    * @return mixed The value of the given field
    */
    public function get($field)
    {
        if (isset($this->progConfig[$field])) {
            return $this->progConfig[$field];
        }
        return null;
    }
    /**
    * Checks for a valid UUID
    *
    * @param string $msg The message to print out if there is no valid UUID
    *
    * @return null
    */
    public function requireUUID($msg = "")
    {
        $uuid = $this->system()->get("uuid");
        preg_match(
            '/([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/',
            $uuid,
            $match
        );
        $uuid2 = $match[0];
        if (empty($uuid2) || (strlen($uuid) != 36)) {
            if (empty($msg)) {
                $msg  = "A valid UUID must be supplied in the INI file.\n";
                $msg .= "'$uuid' is not valid.";
            }
            $this->help();
            $this->out();
            $this->out((string)$msg);
            exit(1);
        }
    }
    /**
    * Checks for a valid INI file
    *
    * @param string $msg The message to print out if there is no valid ini file
    *
    * @return null
    */
    public function requireINI($msg = "")
    {
        if (strlen($this->system()->get("file")) == 0) {
            if (empty($msg)) {
                $msg  = "No valid INI file found";
            }
            $this->help();
            $this->out();
            $this->out((string)$msg);
            exit(1);
        }
    }
    /**
    * Checks for a valid UUID
    *
    * @return null
    */
    private function _checkEnabled()
    {
        $program = $this->system()->get("program");
        $config = $this->system()->get($program);
        if (is_array($config)) {
            if (isset($config["enable"]) && ($config["enable"] == false)) {
                $this->out("$program is disabled in ".$this->system()->get("file"));
                if (isset($config["wait"])) {
                    sleep($config["wait"]);
                }
                exit(1);
            }
        }
    }
    /**
    * Checks for a valid UUID
    *
    * @return null
    */
    private function _checkMemory()
    {
        if (time() > $this->_memCheck) {
            $mem = round((memory_get_usage()) / 1024.0 / 1024.0, 3);
            $this->out("Memory: ".$mem." M");
            if ($mem > $this->maxMemory) {
                $this->out("Too much memory being used, exiting");
                $this->quit();
            }
            $this->_memCheck = time() + 30;
        }
    }
    /**
    * Runs periodically
    *
    * @return null
    */
    public function main()
    {
        $this->_checkMemory();
        parent::main();
    }
    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function quit()
    {
        if (!$this->system()->quit()) {
            $this->system()->quit(true);
            $this->out("Got exit signal");
            $this->out("Closing things out.  Please be patient.");
        }
    }
    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function loop()
    {
        return !$this->system()->quit();
    }
    /**
    * Creates the object
    *
    * @param array $config The configuration to use
    *
    * @return null
    */
    public function &device($config = array())
    {
        if (!is_array($config)) {
            $config = array();
        }
        $active = $this->config("enable");
        if (!is_null($active) && ((int)$active === 0)) {
            $config["Active"] = 0;
        } else if (!is_null($active)) {
            $config["Active"] = 1;
        }
        $ret = $this->system()->network()->device($config);
        $this->out(
            "Using ".sprintf("%06X", $this->system()->network()->device()->getID())
        );
        return $ret;
    }
    /**
    * This sets us up on the OS
    *
    * @return null
    */
    protected function register()
    {
        $dir = $this->_setupVarRun();
        if (is_string($dir) && is_writable($dir)) {
            $program = (string)$this->system()->get("program");
            if (function_exists("posix_getpid") && (strlen($program) > 0)) {
                $fname = $dir.$program.".pid";
                $file  = fopen($fname, "w");
                if ($file) {
                    $this->pidfile = $fname;
                    fwrite($file, (string)posix_getpid());
                    fclose($file);
                }
            }
        }
    }
    /**
    * This sets up /var/run/hugnet
    *
    * @return true if successful, false otherwise
    */
    private function _setupVarRun()
    {
        $basedir = "/var/run";
        if (is_dir($basedir)) {
            $dir = $basedir."/hugnet/";
        } else {
            return false;
        }
        if (!is_dir($dir)) {
            $ret = is_writable($basedir);
            if ($ret) {
                $ret = @mkdir($dir);
            }
            if (!$ret) {
                return false;
            }
        }
        return $dir;
    }
}
?>
