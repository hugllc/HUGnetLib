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
/** This is our device based plugins */
require_once dirname(__FILE__)."/analysis/Device.php";
/** This is our periodic plugins */
require_once dirname(__FILE__)."/analysis/Periodic.php";

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
class Analysis extends \HUGnet\ui\Daemon
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
    const WAIT_TIME = 20;

    /** This is the start time of the current run */
    private $_mainStart;
    /** How long we should wait */
    private $_wait;
    /** The ids we are going through */
    private $_ids;
    /** This is my ID */
    private $_myID;
    /** This is the start time of the current run */
    private $_plugins;
    /** This is the start time of the current run */
    private $_device;
    /** This is the start time of the current run */
    private $_dev;
    /** This is the runtime configuration */
    private $_runtime;
    /** This is the max memory we can use in megabytes */
    protected $maxMemory = 100;
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
        $this->_dev = $this->system()->device();
        $this->_plugins = array(
            "periodic" => \HUGnet\processes\analysis\Periodic::plugins($this),
            "device"   => \HUGnet\processes\analysis\Device::plugins($this)
        );

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
        $obj = new Analysis($config);
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
        if ($this->_runtime["analysis"] !== false) {
            foreach ($this->_plugins['periodic'] as $obj) {
                $obj->execute();
            }
            $this->_ids = $this->_device->ids(array("Active" => 1));
            foreach (array_keys((array)$this->_ids) as $key) {
                if (!$this->loop()) {
                    break;
                }
                if (($key == 0) || ($key > 0xFD0000)) {
                    /* I don't need info an empty device, or script */
                    continue;
                }
                $this->_dev->load($key);
                $this->out($this->_dev->get("DeviceID"));
                foreach ($this->_plugins['device'] as $key => $obj) {
                    $obj->execute($this->_dev);
                }
                $this->_dev->store();
            }
        } else {
            $this->_wait = 30;
            $this->out(
                "Analysis script ".sprintf("%06X", $this->_myID)." is disabled."
            );
        }
        parent::main();
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
    * Sets the runtime config
    *
    * @return null
    */
    private function _runtime()
    {
        $this->_runtime = $this->system()->runtime();
        if (!is_bool($this->_runtime["analysis"])) {
            $this->_runtime["analysis"] = true;
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
