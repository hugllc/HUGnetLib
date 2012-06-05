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
class Updater extends \HUGnet\ui\Daemon
{
    /** This is the amount of time we wait */
    const WAIT_TIME = 30;

    /** This is the start time of the current run */
    private $_mainStart;
    /** How long we should wait */
    private $_wait;
    /** This is my ID */
    private $_myID;
    /** This is the start time of the current run */
    private $_device;
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
        $this->_device->load($this->_myID);
        if ($this->_device->get("Active") != 0) {
            $plugins = &\HUGnet\updater\Periodic::plugins($this);
            foreach ($plugins as $key => $obj) {
                $obj->execute();
            }
        } else {
            $this->_wait = 600;
            $this->out("Updater script ".$this->_myID." is disabled.");
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
        $this->_wait = self::WAIT_TIME - (time() - $this->_mainStart);
        if (($this->_wait > 0) && $this->loop()) {
            $this->out("Waiting ".$this->_wait." seconds at ".date("Y-m-d H:i:s"));
            for (; ($this->_wait > 0) && $this->loop(); $this->_wait--) {
                parent::main();
                sleep(1);
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
        $this->system()->network()->unsolicited(
            array($this, "packet"),
            $this->_myID
        );
        return $ret;
    }
}
?>
