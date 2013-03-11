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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Gather extends \HUGnet\ui\Daemon
{
    /** This is where the active bit is */
    const ACTIVEMASK = 0x01;
    /** This is where the poll bit is */
    const POLLMASK = 0x02;
    /** This is where the config bit is */
    const CONFIGMASK = 0x04;
    /** This is the time we lose contact for before we start pinging */
    const PING_TIME = 1200;
    /** This is the time we lose contact for before we start pinging */
    const FAIL_THRESHOLD = 20;
    /** This is the amount of time we wait */
    const WAIT_TIME = 60;
    /** This is how long we wait before getting the config of a device */
    const CONFIG_WAIT = 60;

    /** This is the start time of the current run */
    private $_mainStart;
    /** How long we should wait */
    private $_wait;
    /** The ids we are going through */
    private $_ids;
    /** This is my ID */
    private $_myID;
    /** This is the start time of the current run */
    private $_unsolicited;
    /** This is the start time of the current run */
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
        /* Set up the unsolicited packets */
        $this->system()->network()->unsolicited(array($this, "unsolicited"));
        /* Get our Device */
        $this->_device = $this->system()->device();
        $this->_unsolicited = $this->system()->device();
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
        $obj = new Gather($config);
        return $obj;
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
        $this->_runtime();
        if ($this->_runtime["gather"] !== false) {
            $this->_ids = $this->_device->ids(array("Active" => 1));
            foreach (array_keys((array)$this->_ids) as $key) {
                if (!$this->loop()) {
                    break;
                }
                if ($key == 0) {
                    /* I don't need info an empty device */
                    continue;
                }
                $this->_device->load($key);
                if ($this->_doPing()) {
                    $this->_ping();
                } else if ($this->_doConfig()) {
                    $this->_config();
                } else if ($this->_doPoll()) {
                    $this->_poll();
                }
            }
        } else {
            $this->_wait = 30;
            $this->out(
                "Gather script ".sprintf("%06X", $this->_myID)." is disabled."
            );
        }
        parent::main();
        $this->_wait();
    }
    /**
    * If true, we should ping this device
    *
    * This routine pings continuosly until it reaches self::FAIL_THRESHOLD, at
    * which poing it only pings once every self::PING_TIME seconds.  This is so
    * that pinging devices doesn't swamp the bus.
    *
    * @return bool
    */
    private function _doPing()
    {
        if ($this->_device->getParam("ContactFail") > self::FAIL_THRESHOLD) {
            $lastContact = time() - $this->_device->getParam("LastContactTry");
        } else {
            $lastContact = time() - $this->_device->getParam("LastContact");
        }
        $ret = $lastContact > self::PING_TIME;
        if ($ret) {
            $this->_device->load($this->_device->id());
            $this->_device->setParam("LastContactTry", time());
            $this->_device->store();
        }
        return $ret;
    }
    /**
    * If true, we should poll this device
    *
    * @return bool
    */
    private function _doPoll()
    {
        if (($this->_runtime["gatherpoll"] === false) || !$this->_doContact()) {
            return false;
        }
        /* PollInterval is in minutes, we need it in seconds */
        $PollInterval = (int)$this->_device->get("PollInterval");
        if ($PollInterval === 0) {
            return false;
        }
        if ($PollInterval < ($this->_wait * 2)) {
            $this->_wait = $PollInterval / 2;
        }
        /* Don't run if the poll interval is 0 */
        if ($PollInterval <= 0) {
            return false;
        }
        $lastPoll = (time() - $this->_device->getParam("LastPoll"));
        $slip = $PollInterval * 0.1;
        if ($slip > (self::WAIT_TIME * 2)) {
            $slip = self::WAIT_TIME * 2;
        }
        return $lastPoll > ($PollInterval - $slip);
    }
    /**
    * If true, we should get the config of this device
    *
    * @return bool
    */
    private function _doConfig()
    {
        if (($this->_runtime["gatherconfig"] === false) || !$this->_doContact()) {
            return false;
        }
        $lastConfig = time() - $this->_device->getParam("LastConfig");
        $ConfigInterval = (int)$this->_device->get("ConfigInterval");
        if ($ConfigInterval === 0) {
            return false;
        }
        if ($ConfigInterval < $this->_wait) {
            $this->_wait = $ConfigInterval;
        }
        return $lastConfig > $ConfigInterval;
    }
    /**
    * If true, we should try to contact this device
    *
    * @return bool
    */
    private function _doContact()
    {
        $lastContact = time() - $this->_device->getParam("LastContact");
        return $lastContact < self::PING_TIME;
    }
    /**
    * This is our wait
    *
    * @return null
    */
    private function _wait()
    {
        parent::main(); // This should be called at least once per iteration
        $this->_wait -= (time() - $this->_mainStart);
        if (($this->_wait > 0) && $this->loop()) {
            if ($this->_wait > 10) {
                $this->out(
                    "Waiting ".$this->_wait." seconds at ".date("Y-m-d H:i:s")
                );
            }
            for (; ($this->_wait > 0) && $this->loop(); $this->_wait--) {
                parent::main();
                sleep(1);
            }
        }
        $this->_wait = self::WAIT_TIME;
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
        $LastConfig = $this->_unsolicited->getParam("LastConfig");
        $now = $this->system()->now();
        $ConfigInt = $this->_unsolicited->get("ConfigInterval");
        if ($ConfigInt < self::CONFIG_WAIT) {
            $ConfigInt = self::CONFIG_WAIT * 2;
        }
        $LastConfig = ($now - $ConfigInt) + self::CONFIG_WAIT;
        $this->_unsolicited->setParam("LastConfig", $LastConfig);
        $this->out(
            "Setting next config of ".$this->_unsolicited->get("DeviceID")." to "
            .date("Y-m-d H:i:s", $LastConfig + $ConfigInt)
        );
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
                "---> Failed.  Failure #".$fails
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
                    (($this->_device->getParam("LastConfig") - $lastConfig)), 2
                )."/".($this->_device->get("ConfigInterval"))
            );
        } else {
            $this->out(
                "---> Failed.  Failure #".$this->_device->getParam("ConfigFail")
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
        $time = microtime(true);
        $ret = $this->_device->action()->poll();
        $time = microtime(true) - $time;
        $time = round($time, 2);
        if ($ret) {
            $this->out(
                "Success in $time s.  LastPoll set to: "
                .date("Y-m-d H:i:s", $this->_device->getParam("LastPoll"))
                ." Interval: "
                .round(
                    (($this->_device->getParam("LastPoll") - $lastPoll)), 2
                )."/".$this->_device->get("PollInterval")
            );
        } else {
            $this->out(
                "---> Failed.  Failure #".$this->_device->getParam("PollFail")
            );
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
        if ($pkt->type() === "SETCONFIG") {
            $this->out("Being reconfigured by ".$pkt->from());
            $dev = $this->system()->device($this->_myID);
            $this->_wait = 0;
            $data = $pkt->data();
            $index = hexdec(substr($data, 0, 2));
            for ($i = 2; $i < strlen($data); $i+=2) {
                switch ($index) {
                case 0:
                    $value = hexdec(substr($data, $i, 2));
                    $this->out(
                        "Setting Enable to ".(($value & self::ACTIVEMASK) ? 1 : 0)
                    );
                    $this->out(
                        "Setting Polling to ".(($value & self::POLLMASK) ? 1 : 0)
                    );
                    $this->out(
                        "Setting Config to ".(($value & self::CONFIGMASK) ? 1 : 0)
                    );
                    $dev->setParam("Enable", $value);
                    break;
                }
                $index++;
            }
            $dev->store();
            $out = sprintf("%02X", (int)$dev->store());
            $this->_reply($pkt, $out);
        }
    }
    /**
    * Replies to a packet
    *
    * @param object $pkt  The packet to send out
    * @param string $data The data to reply with
    *
    * @return null
    */
    private function _reply($pkt, $data)
    {
        $newPacket = array(
            "To"      => $pkt->from(),
            "Command" => "REPLY",
            "Data"    => $data,
        );
        $this->system()->network()->send(
            $newPacket, null, array("tries" => 1, "find" => false)
        );
    }

    /**
    * Sets the runtime config
    *
    * @return null
    */
    private function _runtime()
    {
        $this->_runtime = $this->system()->runtime();
        if (!is_bool($this->_runtime["gather"])) {
            $this->_runtime["gather"] = true;
            $this->_runtime["gatherpoll"] = true;
            $this->_runtime["gatherconfig"] = true;
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
        $ret = parent::device($config);
        $this->_myID = $this->system()->network()->device()->getID();
        $this->system()->device($this->_myID);
        $this->system()->network()->unsolicited(
            array($this, "packet"),
            $this->_myID
        );
        return $ret;
    }
}
?>
