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
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\network;
/**
 * This code routes packets to their correct destinations.
 *
 * Config (default):
 *    "quiet"    bool Optional If true it won't throw exceptions (false)
 *    "channels" int  The number of channels to keep open (5)
 *
 * This class keeps track of packets and waits for replies.  It will use the
 * TransportPacket class to keep track of each packet and where it is in its
 * timeouts and retries.  It will keep a number of channels open and should NOT
 * block.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
final class Device
{
    /** This is our configuration */
    private $_network = null;
    /** This is our configuration */
    private $_config = array();
    /** This is our configuration */
    private $_device = null;
    /** This is our configuration */
    private $_system = null;
    /** This is our configuration */
    private $_lastContact = null;
    /** These are the packets we are sending */
    private $_defaultConfig = array(
        "HWPartNum" => "0039-26-00-P",
    );

    /**
    * Sets our configuration
    *
    * @param object &$application The application network layer to use
    * @param object &$system      The system object to use
    * @param array  $config       The configuration to use
    */
    private function __construct(&$application, &$system, $config)
    {
        $this->_lastContact = time();
        $this->_config  = array_merge($this->_defaultConfig, $config);
        $this->_network = &$application;
        $this->_system  = &$system;
        $this->_config["FWPartNum"] = "0039-26-00-P";
        $this->_config["FWVersion"] = @file_get_contents(
            dirname(__FILE__).'/../VERSION.TXT'
        );
        $this->getID();
        $this->_network->unsolicited(
            array($this, "packet"), $this->_config["DeviceID"]
        );
        // Send the powerup packet
        \HUGnet\VPrint::out("Using ID: ".$this->_config["DeviceID"], 1);
        $this->_powerup();
    }
    /**
    * Creates the object
    *
    * @param object &$application The application network layer to use
    * @param object &$system      The system object to use
    * @param array  $config       The configuration to use
    *
    * @return Matcher Object
    */
    public function &factory(&$application, &$system, $config)
    {
        return new Device($application, $system, (array)$config);
    }

    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function __destruct()
    {
        // Remove our network
        unset($this->_network);
        unset($this->_system);
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
        $this->_lastContact = time();
        if (($pkt->type() === "PING") || ($pkt->type() === "FINDPING")) {
            $this->_reply($pkt, $pkt->data());
        } else if (($pkt->type() === "CONFIG")) {
            $this->_reply($pkt, $this->_device()->encode());
        }
    }
    /**
    * Gets the device associated with this
    *
    * @return null
    */
    private function _device()
    {
        if (!is_object($this->_device)) {
            include_once dirname(__FILE__)."/../system/Device.php";
            $this->_device = \HUGnet\Device::factory($this->_system, $this->_config);
            $this->_device->set("DeviceName", $this->_system->get("uuid"));
            $this->_device->set("DeviceLocation", $this->_system->get("IPAddr"));
            $this->_device->store();
        }
        return $this->_device;
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
        $newPacket = &Packet::factory(
            array(
                "To"      => $pkt->from(),
                "Command" => "REPLY",
                "Data"    => $data,
            )
        );
        $this->_network->send(
            $newPacket, null, array("tries" => 1, "find" => false)
        );
    }
    /**
    * Replies to a packet
    *
    * @return null
    */
    private function _powerup()
    {
        $data = substr($this->_device()->encode(), 0, 20);

        $newPacket = &Packet::factory(
            array(
                "To"      => "000000",
                "From"    => $this->_config["id"],
                "Command" => "POWERUP",
                "Data"    => $data,
            )
        );
        $this->_network->send(
            $newPacket, null, array("tries" => 1, "find" => false)
        );
    }
    /**
    * Finds a good ID to use
    *
    * @return null
    */
    public function getID()
    {
        if (empty($this->_config["id"])) {
            do {
                $did = sprintf("%06X", mt_rand(0xFE0000, 0xFEFFFF));
                $ret = $this->_network->send(
                    array(
                        "To" => $did,
                        "Command" => "FINDPING",
                        "Data" => $did,
                    ),
                    null,
                    array("block" => 1)
                );
                $reply = $ret->reply();
            } while (!empty($reply));
            $this->_config["id"] = $did;
        }
        if (!is_int($this->_config["id"])) {
            $this->_config["id"] = hexdec($this->_config["id"]);
        }
        $this->_config["DeviceID"] = sprintf("%06X", $this->_config["id"]);
        return $this->_config["id"];
    }
    /**
    * The main routine should be called periodically (once per loop at least)
    *
    * @return null
    */
    public function main()
    {
        static $lastBoredom;
        static $lastID;

        $last = time() - $this->_lastContact;
        if (($last > 3600) && ((time() - $lastBoredom) > 3600)) {
            $newPacket = &Packet::factory(
                array(
                    "To"      => "000000",
                    "From"    => $this->_config["id"],
                    "Command" => "BOREDOM",
                    "Data"    => sprintf("%08X", $last),
                )
            );
            $this->_network->send(
                $newPacket, null, array("tries" => 1, "find" => false)
            );
            $lastBoredom = time();
        } else if (($last > 600) && ((time() - $lastID) > 3600)) {
            \HUGnet\VPrint::out("Using ID: ".$this->_config["DeviceID"], 1);
            $lastID = time();
        }
    }
}
?>
