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
namespace HUGnet\processes;
/** This is our base class */
require_once dirname(__FILE__)."/../ui/Daemon.php";
/** This is our base class */
require_once dirname(__FILE__)."/../updater/Periodic.php";

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
class Route extends \HUGnet\ui\Daemon
{
    /** This is the amount of time we wait */
    const WAIT_TIME = 120;

    /** This is the start time of the current run */
    private $_mainStart;
    /** How long we should wait */
    private $_wait = 60;
    /** This is my ID */
    private $_myID;
    /** This is the device we are using */
    private $_device;
    /** This is the runtime configuration */
    private $_runtime;
    /**
    * Sets our configuration
    *
    * @param mixed &$config The configuration to use
    */
    protected function __construct(&$config)
    {
        parent::__construct($config);
        /* Get our Device */
        $this->_device = $this->system()->device();
    }
    /**
    * This is our main routine
    *
    * @return null
    */
    public function main()
    {
        $this->_mainStart = time();
        $this->_runtime();
        if ($this->_runtime["route"] !== false) {
            parent::main();
            $this->_wait = self::WAIT_TIME;
        } else {
            $this->_wait = 600;
            $this->out(
                "Route script ".sprintf("%06X", $this->_myID)." is disabled."
            );
        }
        $this->_wait();
    }
    /**
    * This is our wait
    *
    * @return null
    */
    private function _wait()
    {
        parent::main(); // This should be called at least once per iteration
        if (($this->_wait > 0) && $this->loop()) {
            for (; ($this->_wait > 0) && $this->loop(); $this->_wait--) {
                parent::main();
            }
        }
    }
    /**
    * Deals with incoming packets
    *
    * @param object $pkt The packet to send out
    *
    * @return null
    */
    public function packet($pkt)
    {
        if ($pkt->type() === "RECONFIG") {
        }
    }
    /**
    * Deals with incoming packets
    *
    * @param object $pkt The packet to send out
    *
    * @return null
    */
    public function monitor($pkt)
    {
        if (is_object($pkt)) {
            print date("Y-m-d H:i:s");
            print " From: ".$pkt->From();
            print " -> To: ".$pkt->To();
            print "  Command: ".$pkt->Command();
            print "  Type: ".$pkt->Type();
            print "\r\n";
            $data = $pkt->Data();
            if (!empty($data)) {
                print "Data: ".$data."\r\n";
            }
        }
    }
    /**
    * Sets the runtime config
    *
    * @return null
    */
    private function _runtime()
    {
        $this->_runtime = $this->system()->runtime();
        if (!is_bool($this->_runtime["route"])) {
            $this->_runtime["route"] = true;
            $this->system()->runtime($this->_runtime);
        }
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
        $ret = &parent::device($config);
        $this->_myID = $this->system()->network()->device()->getID();
        /* Print packets out on the screen */
        $this->system()->network()->monitor(array($this, "monitor"));
        $this->system()->network()->unsolicited(
            array($this, "packet"),
            $this->_myID
        );
        return $ret;
    }
}
?>
