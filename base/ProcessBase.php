<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Processes
 * @package    HUGnetLib
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetClass.php";
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";
require_once dirname(__FILE__)."/../containers/PacketContainer.php";
require_once dirname(__FILE__)."/../interfaces/PacketConsumerInterface.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Processes
 * @package    HUGnetLib
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class ProcessBase extends HUGnetContainer implements PacketConsumerInterface
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "group"      => "default",  // The groups to route between
        "GatewayKey" => 0,          // The gateway key we are using
    );

    /** @var object This is our config */
    public $myConfig = null;
    /** @var object This is our device configuration */
    public $myDevice = null;
    /** @var object This is the device to use for whatever */
    protected $device = null;
    /** @var object This is the device we use for unsolicited packets */
    protected $unsolicited = null;
    /** @var object This is tells stuff to keep looping */
    public $loop = true;

    /**
    * Builds the class
    *
    * @param array $data   The data to build the class with
    * @param array $device This is the setup for my device class
    *
    * @return null
    */
    public function __construct($data, $device)
    {
        // Clear the data
        $this->clearData();
        // This is our config
        $this->myConfig = &ConfigContainer::singleton();
        // Run the parent stuff
        parent::__construct($data);
        // Set the verbosity
        $this->verbose($this->myConfig->verbose);
        // This is our device container
        $this->device = new DeviceContainer();
        // This is our device container
        $this->unsolicited = new DeviceContainer();
        // Set the gatewaykey if it hasn't been set
        if (empty($this->GatewayKey)) {
            $this->GatewayKey = $this->myConfig->script_gateway;
        }
        $this->setupMyDevice($device);
        // Trap the exit signal and exit gracefully
        if (function_exists("pcntl_signal")) {
            pcntl_signal(SIGINT, array($this, "loopEnd"));
        }
    }
    /**
    * Registers the packet hooks
    *
    * @param array $device This is the setup for my device class
    *
    * @return null
    */
    protected function setupMyDevice($device)
    {
        // This sets us up as a device
        self::vprint("Setting up my device...", HUGnetClass::VPRINT_NORMAL);
        $this->myDevice = new DeviceContainer($device);
        $this->myDevice->GatewayKey = $this->GatewayKey;
        $this->myDevice->DeviceJob = posix_getpid();
        $this->myDevice->DeviceLocation = ProcessBase::getIP();
        // Get the deviceID
        if (empty($device["id"])) {
            $id = $this->getMyDeviceID();
            // Setting up the id
            $this->myDevice->id = hexdec($id);
        }
        $this->myDevice->DeviceID = $this->myDevice->id;
        // This is the device container with our setup information in it.
        $this->myDevice->LastConfig = time();
        $this->myDevice->insertRow(true);
    }
    /**
    * Registers the packet hooks
    *
    * @return null
    */
    protected function getMyDeviceID()
    {
        self::vprint("Finding my DeviceID...", HUGnetClass::VPRINT_NORMAL);
        // If this is a restart, pull all the old device info.
        $ret = $this->myDevice->selectOneInto(
            "HWPartNum = ? AND DeviceLocation = ? AND GatewayKey = ?",
            array(
                $this->myDevice->HWPartNum,
                $this->myDevice->DeviceLocation,
                $this->myDevice->GatewayKey,
            )
        );
        if ($ret) {
            $DeviceID = $this->myDevice->DeviceID;
            $this->myConfig->sockets->forceDeviceID($DeviceID);
        } else {
            $DeviceID = $this->myConfig->sockets->deviceID(array());
        }
        return $DeviceID;
    }
    /**
    * Registers the packet hooks
    *
    * @return null
    */
    protected function registerHooks()
    {
        // Set up our hooks
        $this->myConfig->hooks->registerHook("UnsolicitedPacket", $this);
        $this->myConfig->hooks->registerHook("myPacket", $this->myDevice);
    }
    /**
    * Requires a gatewaykey to continue
    *
    * @return null
    */
    protected function requireGateway()
    {
        // We need a GatewayKey
        if ($this->GatewayKey <= 0) {
            $this->throwException(
                "A GatewayKey must be specified.", -3
            );
            // @codeCoverageIgnoreStart
            // It thinks this line won't run.  The above function never returns.
        }
        // @codeCoverageIgnoreEnd
    }
    /**
    * This function should be used to wait between config attempts
    *
    * It waits up to the Timeout.  The timeout is calculated from the last time
    * that it ran, so it will only wait the full timeout if there is nothing
    * for the process to do.  If the process takes more than the timeout period
    * to finish then it will not wait at all.
    *
    * @param int $Timeout The timeout period to wait
    *
    * @return int The number of packets routed
    */
    public function wait($Timeout = 60)
    {
        static $end;
        // Be verbose ;)
        self::vprint(
            "Pausing $Timeout s  Using ID: ".$this->myDevice->DeviceID
            ." ".date("Y-m-d H:i:s"),
            HUGnetClass::VPRINT_NORMAL
        );
        // Update our device
        $this->updateMyDevice();
        // Monitor for packets.  The GetReply => true allows the hooks to
        // take care of any packets.
        while ((time() < $end) && $this->loop()) {
            PacketContainer::monitor(array("GetReply" => true, "Timeout" => 5));
        }
        // Set our end time
        $end = time() + $Timeout;
    }
    /**
    * This function sends a powerup packet
    *
    * @return null
    */
    public function powerup()
    {
        $this->vprint(
            "Starting... (".$this->myDevice->DeviceID.")",
            HUGnetClass::VPRINT_NORMAL
        );
        // Send a powerup packet
        PacketContainer::powerup("", $this->group);
        $cmd = PacketContainer::COMMAND_POWERUP;
        $this->myDevice->params->ProcessInfo["unsolicited"][$cmd]++;
    }
    /**
    * Consumes packets and returns some stuff.
    *
    * This function deals with setup and ping requests
    *
    * @param PacketContainer &$pkt The packet that is to us
    *
    * @return string
    */
    public function packetConsumer(PacketContainer &$pkt)
    {
    }
    /**
    * Handles signals
    *
    * @param int $signo The signal number
    *
    * @return none
    */
    public function loopEnd($signo)
    {
        // Be verbose
        self::vprint(
            "Got exit signal",
            HUGnetClass::VPRINT_NORMAL
        );
        $this->loop = false;
    }

    /**
    * Handles signals
    *
    * @return none
    */
    protected function loop()
    {
        // @codeCoverageIgnoreStart
        // This doesn't exist in some versions of php that we are testing.
        if (function_exists("pcntl_signal_dispatch")) {
            pcntl_signal_dispatch();
        }
        // @codeCoverageIgnoreEnd
        return $this->loop;
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
    static public function getIP()
    {
        $line = trim(`/sbin/ifconfig`);
        preg_match(
            "/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/",
            $line,
            $match
        );
        return trim((string)$match[0]);
    }
    /**
    * This updates my device record
    *
    * @return string
    */
    protected function updateMyDevice()
    {
        static $last;
        // Do this only once per minute max
        if ($last != date("i")) {
            $this->myDevice->params->DriverInfo["LastConfig"] = time();
            $this->myDevice->params->LastContact = time();
            $this->myDevice->Active = 1;
            $this->myDevice->updateRow();
            $last = date("i");
        }
    }

}
?>
