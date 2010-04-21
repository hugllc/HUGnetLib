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
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
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
        "MaxPackets" => 5,        // The maximum number of packets/interface
        "groups"     => array(),  // The groups to route between
        "Timeout"    => 5,        // The default packet timeout in seconds
        "Retries"    => 3,        // The number of times to retry the packet
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** @var array This is Packet Setup */
    protected $myPackets = null;
    /** @var object This is our config */
    protected $myConfig = null;
    /** @var array This is our packet buffer */
    protected $PacketBuffer = array();
    /** @var array We queue up packets here before sending them out */
    protected $PacketQueue = array();
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
    public function send(PacketContainer &$pkt, $groups)
    {
        // save the group to restore later
        $oldgroup = $pkt->group;
        // Save the retries, so we can make sure it only decrements once
        $retries  = $pkt->Retries;
        $this->_checkGroups($pkt, $groups);
        foreach ($groups as $group) {
            // Don't send back the way we came
            if ($group !== $oldgroup) {
                // This makes sure the retries are decremented the correct number
                // of times.
                $pkt->Retries = $retries;
                // Sets the group to send the packet out on
                $pkt->group = $group;
                // We don't want to wait for a reply
                $pkt->GetReply = false;
                // Send the packet
                $pkt->send();
            }
        }
        // Set a new timeout
        $pkt->TimeoutAt = $this->Timeout;
        // Return the old group
        $pkt->group   = $oldgroup;
    }
    /**
    * This function checks all of the interfaces for packets
    *
    * This function should be called periodically as often as possible.  It will
    * only return the first packet it finds on each interface.
    *
    * @param mixed &$pkt If it is a PacketContainer we queue it.  Otherwise ignore
    *
    * @return null
    */
    public function queue(&$pkt)
    {
        if ($this->isMine($pkt, "PacketContainer")) {
            if ($this->_check($pkt)) {
                $output  = $pkt->group.": ";
                $output .= "From: ".$pkt->From." -> To: ".$pkt->To;
                $output .= "  Command: ".$pkt->Command."  Type: ".$pkt->Type;
                if (!empty($pkt->Data)) {
                    $output .= "\r\nData: ".$pkt->Data;
                }
                $this->Routes[$pkt->From] = $pkt->group;
                $pkt->Timeout = $this->Timeout;
                $this->PacketQueue[] = &$pkt;
                self::vprint($output, HUGnetClass::VPRINT_NORMAL);
            }
        }
    }
    /**
    * This function checks all of the interfaces for packets
    *
    * This function should be called periodically as often as possible.  It will
    * only return the first packet it finds on each interface.
    *
    * @return null
    */
    public function read()
    {
        foreach ($this->groups as $group) {
            // Make sure of our timeout
            $data = array(
                "Timeout" => 1,
                "group" => $group,
            );
            $pkt = &PacketContainer::monitor($data);

            $this->queue($pkt);
        }
    }
    /**
    * This function checks the queue and sends out packets from it, putting them
    * into the packet buffer.
    *
    * This function should be called periodically as often as possible.
    *
    * @return null
    */
    public function route()
    {
        foreach (array_keys((array)$this->PacketQueue) as $key) {
            if (count($this->PacketBuffer) >= $this->MaxPackets) {
                break;
            }
            $this->send($this->PacketQueue[$key], $this->groups);
            $this->PacketBuffer[] = &$this->PacketQueue[$key];
            unset($this->PacketQueue[$key]);
        }
    }
    /**
    * This function checks the packets in the queue and resends the one that
    * need it.
    *
    * This function should be called periodically as often as possible.  It will
    * only return the first packet it finds on each interface.
    *
    * @return null
    */
    public function gc()
    {
        // Go though the packet buffer and check all the packets
        foreach (array_keys((array)$this->PacketBuffer) as $key) {
            // Check the timeout
            if ($this->PacketBuffer[$key]->Timeout()) {
                if ($this->PacketBuffer[$key]->Retries > 0) {
                    // This needs to be sent again.
                    $this->send($this->PacketBuffer[$key], $this->groups);
                } else {
                    // This packet is done.  Remove it.
                    unset($this->PacketBuffer[$key]);
                }
            }
        }
    }
    /**
    * This function checks all of the interfaces for packets
    *
    * This function should be called periodically as often as possible.  It will
    * only return the first packet it finds on each interface.
    *
    * @param mixed &$pkt The packet to check if it is a reply
    *
    * @return null
    */
    private function _check(&$pkt)
    {
        $return = true;
        foreach (array_keys((array)$this->PacketBuffer) as $key) {
            // Check if this is one we sent out echo'd back
            if ($this->PacketBuffer[$key]->same($pkt)) {
                $return = false;
            }
            // Check if this is a reply to one we have.
            if ($this->PacketBuffer[$key]->myReply($pkt)) {
                $this->send($pkt, array($this->PacketBuffer[$key]->group));
                unset($this->PacketBuffer[$key]);
                $return = false;
            }
        }
        return $return;
    }
    /**
    * This function sets the groups correctly to route the packets.  It doesn't
    * route findping packets.  It broadcasts them.
    *
    * @param PacketContainer &$pkt    The packet to route
    * @param array           &$groups The groups to send it out to
    *
    * @return null
    */
    private function _checkGroups(&$pkt, &$groups)
    {
        // Don't route if it is a find ping
        if ($pkt->Command == PacketContainer::COMMAND_FINDECHOREQUEST) {
            return;
        }
        // If we know where to put this, set the groups to where it needs to go.
        if (isset($this->Routes[$pkt->To])) {
            $groups = array($this->Routes[$pkt->To]);
        }
    }
}
?>
