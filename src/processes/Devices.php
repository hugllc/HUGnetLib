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
class Devices extends \HUGnet\ui\Daemon
{
    /** This is the time we lose contact for before we start pinging */
    const PING_TIME = 1200;
    /** This is the amount of time we wait */
    const WAIT_TIME = 30;

    /** This is the start time of the current run */
    private $_mainStart;
    /** How long we should wait */
    private $_wait;
    /** The ids we are going through */
    private $_ids;
    /** This is the start time of the current run */
    private $_unsolicited;
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
        /* Set up the unsolicited packets */
        $this->system()->network()->unsolicited(array($this, "unsolicited"));
        /* Get our Device */
        $this->_device = $this->system()->device();
        $this->_unsolicited = $this->system()->device();
    }
    /**
    * This is our main routine
    *
    * @return null
    */
    public function main()
    {
        $this->_mainStart = time();
        $this->_ids = $this->_device->ids(array("Active" => 1));
        foreach ((array)$this->_ids as $key => $devID) {
            parent::main();
            if (!$this->loop()) {
                break;
            }
            if ($key == $this->system()->network()->device()->getID()) {
                /* I don't need info on me */
                continue;
            }
            $this->_device->load($key);
            $lastContact = time() - $this->_device->getParam("LastContact");
            $lastConfig = time() - $this->_device->getParam("LastConfig");
            $lastPoll = (time() - $this->_device->getParam("LastPoll"));
            /* This gives us some leeway so we are closer to the actual poll time */
            $PollInterval = $this->_device->get("PollInterval");
            $PollInterval = ($PollInterval * 60) - self::WAIT_TIME;
            $action = false;
            if ($lastContact > self::PING_TIME) {
                $action = true;
                $this->_ping();
            } else if ($lastConfig > $this->_device->get("ConfigInterval")) {
                $action = true;
                $this->_config();
            } else if (($PollInterval > 0) && ($lastPoll > $PollInterval)) {
                $action = true;
                $this->_poll();
            }
            $did = $this->_device->get("id");
            if ($action && !empty($did)) {
                if (!$this->_device->store()) {
                    $this->out("Ouch!  Save failed!");
                }
            }
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
        } else {
            $this->out("Too busy to wait at ".date("Y-m-d H:i:s"));
        }
    }

    /**
    * Deals with incoming packets
    *
    * @param object $pkt The packet to send out
    *
    * @return null
    */
    public function unsolicited($pkt)
    {
        $this->out("Got unsolicited packet from ".$pkt->from());
        $this->_unsolicited->load(array("DeviceID" => $pkt->from()));
        $this->_unsolicited->set("GatewayKey", $this->system()->get("GatewayKey"));
        $this->_unsolicited->setParam("LastConfig", 0);
        $this->_unsolicited->setParam("LastContact", time());
        $this->_unsolicited->store();
        $this->_wait = 0;
    }
    /**
    * Deals with incoming packets
    *
    * @return null
    */
    private function _ping()
    {
        $lastContact = $this->_device->getParam("LastContact");
        $this->out(
            "Trying to find ".$this->_device->get("DeviceID")." Last Contact: ".
            date("Y-m-d H:i:s", $lastContact)
        );
        $ret = $this->_device->action()->ping(true);
        if ($ret) {
            $this->out(
                "Success.  LastContact set to: "
                .date("Y-m-d H:i:s", $this->_device->getParam("LastContact"))
            );
        } else {
            $fails = $this->_device->getParam("ContactFail");
            $this->out(
                "Failed.  Failure #".$fails
            );
            $this->_device->action()->checkRecord();
        }
    }
    /**
    * Deals with incoming packets
    *
    * @return null
    */
    private function _config()
    {
        $lastConfig = $this->_device->getParam("LastConfig");
        $this->out(
            "Checking ".$this->_device->get("DeviceID")." Last Config: ".
            date("Y-m-d H:i:s", $lastConfig)
        );
        $ret = $this->_device->action()->config();
        if ($ret) {
            $this->out(
                "Success.  LastConfig set to: "
                .date("Y-m-d H:i:s", $this->_device->getParam("LastConfig"))
                ." Interval: "
                .round(
                    (($this->_device->getParam("LastConfig") - $lastConfig)/60), 2
                )
            );
        } else {
            $this->out(
                "Failed.  Failure #".$this->_device->getParam("ConfigFail")
            );
        }
    }
    /**
    * Deals with incoming packets
    *
    * @return null
    */
    private function _poll()
    {
        $lastPoll = $this->_device->getParam("LastPoll");
        $this->out(
            "Polling ".$this->_device->get("DeviceID")." Last Poll: ".
            date("Y-m-d H:i:s", $lastPoll)
        );
        $ret = $this->_device->action()->poll();
        if ($ret) {
            $this->out(
                "Success.  LastPoll set to: "
                .date("Y-m-d H:i:s", $this->_device->getParam("LastPoll"))
                ." Interval: "
                .round(
                    (($this->_device->getParam("LastPoll") - $lastPoll)/60), 2
                )."/".$this->_device->get("PollInterval")
            );
        } else {
            $this->out(
                "Failed.  Failure #".$this->_device->getParam("PollFail")
            );
        }
    }
}
?>
