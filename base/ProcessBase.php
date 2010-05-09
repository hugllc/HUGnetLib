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
    protected $myConfig = null;
    /** @var object This is our device configuration */
    protected $myDevice = null;
    /** @var object This is the device to use for whatever */
    protected $device = null;
    /** @var object This is the device we use for unsolicited packets */
    protected $unsolicited = null;
    /** @var object This is tells stuff to keep looping */
    public $loop = true;

    /**
    * Builds the class
    *
    * @param array           $data    The data to build the class with
    * @param DeviceContainer &$device This is the class to send packets to me to.
    *
    * @return null
    */
    public function __construct($data, DeviceContainer &$device)
    {
        // Clear the data
        $this->clearData();
        // This is our config
        $this->myConfig = &ConfigContainer::singleton();
        // Run the parent stuff
        parent::__construct($data);
        // Set the gatewaykey if it hasn't been set
        if (empty($this->GatewayKey)) {
            $this->GatewayKey = $this->myConfig->script_gateway;
        }
        // Set the verbosity
        $this->verbose($this->myConfig->verbose);
        // This is our device container
        $this->device = new DeviceContainer();
        // This is our device container
        $this->unsolicited = new DeviceContainer();
        // This is the device container with our setup information in it.
        $this->myDevice = &$device;
        // Trap the exit signal and exit gracefully
        if (function_exists("pcntl_signal")) {
            pcntl_signal(SIGINT, array($this, "loopEnd"));
        }
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
        $this->myConfig->hooks->registerHook("myPacket", $this);
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
    * @param int $Timeout The timeout period to wait
    *
    * @return int The number of packets routed
    */
    public function wait($Timeout = 60)
    {
        // Be verbose ;)
        self::vprint(
            "Pausing $Timeout s  Using ID: ".$this->myDevice->DeviceID,
            HUGnetClass::VPRINT_NORMAL
        );
        // Set our end time
        $end = time() + $Timeout;
        // Monitor for packets.  The GetReply => true allows the hooks to
        // take care of any packets.
        while ((time() < $end) && $this->loop()) {
            PacketContainer::monitor(array("GetReply" => true, "Timeout" => 1));
        }
    }
    /**
    * This function sends a powerup packet
    *
    * @return null
    */
    public function powerup()
    {
        // Send a powerup packet
        PacketContainer::powerup("", $this->group);
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
        if ($pkt->toMe()) {
            $this->toMe($pkt);
        } else if ($pkt->unsolicited()) {
            $this->unsolicited($pkt);
        }
    }
    /**
    * This deals with Packets to me
    *
    * @param PacketContainer &$pkt The packet that is to us
    *
    * @return string
    */
    protected function toMe(PacketContainer &$pkt)
    {
        if ($pkt->Command == PacketContainer::COMMAND_GETSETUP) {
            $pkt->reply((string)$this->myDevice);
        } else if (($pkt->Command == PacketContainer::COMMAND_ECHOREQUEST)
            || ($pkt->Command == PacketContainer::COMMAND_FINDECHOREQUEST)
        ) {
            $pkt->reply($pkt->Data);
        }
    }
    /**
    * This deals with Unsolicited Packets
    *
    * @param PacketContainer &$pkt The packet that is to us
    *
    * @return string
    */
    protected function unsolicited(PacketContainer &$pkt)
    {
        // Be verbose
        self::vprint(
            "Got Unsolicited Packet from: ".$pkt->From." Type: ".$pkt->Type,
            HUGnetClass::VPRINT_NORMAL
        );
        // Set up our DeviceContainer
        $this->unsolicited->clearData();
        // Find the device if it is there
        $this->unsolicited->selectInto("DeviceID = ?", array($pkt->From));

        if (!$this->unsolicited->isEmpty()) {
            // If it is not empty, reset the LastConfig.  This causes it to actually
            // try to get the config.
            $this->unsolicited->setDefault("LastConfig");
            // Set our gateway key
            $this->unsolicited->GatewayKey = $this->GatewayKey;
            // Update the row
            $this->unsolicited->updateRow();
        } else {
            // This is a brand new device.  Set the DeviceID
            $this->unsolicited->DeviceID = $pkt->From;
            // Set our gateway key
            $this->unsolicited->GatewayKey = $this->GatewayKey;
            $this->unsolicited->insertRow();
        }
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
    public function getIP()
    {
        $line = trim(`/sbin/ifconfig`);
        preg_match(
            "/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/",
            $line,
            $match
        );
        return trim((string)$match[0]);
    }

}
?>
