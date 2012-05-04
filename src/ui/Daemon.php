<?php
/**
 * This file howses the socket class
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Daemon extends CLI
{
    /** This says if we should loop or not */
    private $_loop = true;

    /**
    * Sets our configuration
    *
    * @param mixed &$config The configuration to use
    */
    protected function __construct(&$config)
    {
        parent::__construct($config);
        $this->_checkEnabled();
        \HUGnet\System::loopcheck();
        if (function_exists("pcntl_signal")) {
            pcntl_signal(SIGINT, array($this, "quit"));
        }
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
            $m
        );
        $uuid2 = $m[0];
        if (empty($uuid2) || (strlen($uuid) != 36)) {
            if (empty($msg)) {
                $msg  = "A valid UUID must be supplied in the ";
                $msg .= $this->system()->get("file")." file.\n";
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
                exit(1);
            }
        }
    }
    /**
    * Runs periodically
    *
    * @return null
    */
    public function main()
    {
        parent::main();
    }
    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function quit()
    {
        if ($this->_loop) {
            $this->out("Got exit signal");
            $this->out("Closing things out.  Please be patient.");
            $this->_loop = false;
        }
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
