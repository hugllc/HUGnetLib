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

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PacketRouter extends HUGnetContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "groups"     => array(),  // The groups to route between
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** @var object This is our config */
    protected $myConfig = null;
    /** @var array We store our routes here */
    protected $Routes = array();

    /**
    * Builds the class
    *
    * @param array $data The data to build the class with
    *
    * @return null
    */
    public function __construct($data = array())
    {
        // Clear the data
        $this->clearData();
        // This is our config
        $this->myConfig = &ConfigContainer::singleton();
        // Run the parent stuff
        parent::__construct($data);
        // Set the verbosity
        $this->verbose($this->myConfig->verbose);
        // This defaults us to all groups present in the config
        if (empty($this->groups)) {
            $this->groups = $this->myConfig->sockets->groups();
        }
        if (count($this->groups) < 2) {
            $this->throwException(
                "There must be at least 2 interface groups defined to use the"
                ." router functionality.  Please define 2 or more interface"
                ." groups.", -3
            );
            // @codeCoverageIgnoreStart
            // It thinks this line won't run.  The above function never returns.
        }
        // @codeCoverageIgnoreEnd
    }

    /**
    * This function routes a packet
    *
    * @param PacketContainer &$pkt   The packet to route
    * @param array           $groups The groups to send it out to
    *
    * @return null
    */
    public function send(PacketContainer &$pkt, $groups = array())
    {
        if (empty($groups)) {
            // This takes care to not send back to the original interface
            $groups = $this->_getGroups($pkt);
        }
        // Remove the original group if it is there.
        unset($groups[$pkt->group]);
        // Print out this packet
        self::vprint(
            $pkt->group." -> (".implode((array)$groups, ", ")
            .") ".$this->_output($pkt),
            HUGnetClass::VPRINT_NORMAL
        );
        // Loop through the groups
        foreach ((array)$groups as $group) {
            // Set this so we don't get a find-ping ever
            $pkt->Retries = 3;
            // Sets the group to send the packet out on
            $pkt->group = $group;
            // We don't want to wait for a reply
            $pkt->GetReply = false;
            // Send the packet
            $ret = $pkt->send();
        }
    }
    /**
    * This function checks all of the interfaces for packets
    *
    * This function should be called periodically as often as possible.  It will
    * only return the first packet it finds on each interface.
    *
    * @param DeviceContainer &$device This is the class to send packets to me to.
    *
    * @return int The number of packets routed
    */
    public function route(DeviceContainer &$device)
    {
        $packets = 0;
        foreach ($this->groups as $group) {
            self::vprint("Reading $group", HUGnetClass::VPRINT_VERBOSE);

            // Make sure of our timeout is low.  We set it to 0.5
            $data = array(
                "Timeout" => 0.5,
                "group" => $group,
                "verbose" => $this->verbose,
            );
            // Check for packets coming in.
            $pkt = &PacketContainer::monitor($data);
            if (is_object($pkt)) {
                if ($pkt->toMe()) {
                    // Print out this packet
                    self::vprint(
                        $pkt->group." -> Me ".$this->_output($pkt),
                        HUGnetClass::VPRINT_NORMAL
                    );
                    $device->packetConsumer($pkt);
                } else {
                    $this->_setRoute($pkt);
                    $this->send($pkt);
                    $packets++;
                }
            }
        }
        return $packets;
    }
    /**
    * This function sets the groups correctly to route the packets.  It doesn't
    * route findping packets.  It broadcasts them.
    *
    * @param PacketContainer &$pkt The packet to route
    *
    * @return null
    */
    private function _getGroups(PacketContainer &$pkt)
    {
        // Don't route if it is a find ping
        if ($pkt->Command == PacketContainer::COMMAND_FINDECHOREQUEST) {
            $groups = $this->groups;
        } else if (isset($this->Routes[$pkt->To])) {
            // If we know where to put this, set the groups to where it needs to go.
            $groups = array($this->Routes[$pkt->To] => $this->Routes[$pkt->To]);
        } else {
            $groups = $this->groups;
        }
        return (array)$groups;
    }
    /**
    * This function checks all of the interfaces for packets
    *
    * @param mixed &$pkt The packet to check if it is a reply
    *
    * @return null
    */
    private function _output(PacketContainer &$pkt)
    {
        $output .= "From: ".$pkt->From." -> To: ".$pkt->To;
        $output .= "  Command: ".$pkt->Command."  Type: ".$pkt->Type;
        if (!empty($pkt->Data)) {
            $output .= "\r\nData: ".$pkt->Data;
        }
        return $output;
    }
    /**
    * This function checks all of the interfaces for packets
    *
    * @param mixed &$pkt The packet to check if it is a reply
    *
    * @return null
    */
    private function _setRoute(PacketContainer &$pkt)
    {
        if (!$pkt->unsolicited()) {
            $this->Routes[$pkt->From] = $pkt->group;
        }
    }
    /**
    * This function routes a packet
    *
    * @return null
    */
    public function powerup()
    {
        foreach ((array)$this->groups as $group) {
            PacketContainer::powerup("", $group);
        }
    }

}
?>
