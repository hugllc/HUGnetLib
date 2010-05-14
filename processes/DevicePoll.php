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
require_once dirname(__FILE__)."/../base/ProcessBase.php";
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
class DevicePoll extends ProcessBase
{

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
        parent::__construct($data, $device);
        $this->registerHooks();
        $this->requireGateway();
    }
    /**
    * This function gets setup information from all of the devices
    *
    * This function should be called periodically as often as possible.  It will
    * go through the whole list of devices before returning.
    *
    * @param bool $loadable Only do devices that have loadable firmware
    *
    * @return null
    */
    public function poll($loadable = false)
    {
        // Get the devices
        $devs = $this->device->select(1);
        // Go through the devices
        foreach (array_keys((array)$devs) as $key) {
            if (!$this->loop()) {
                return;
            }
            $device = &$devs[$key];
            if (!$loadable || $device->loadable() && $device->Active) {
                // We don't want to get our own config
                if ($device->DeviceID !== $this->myDevice->DeviceID) {
                    // We should only poll stuff for our gateway
                    if ($this->GatewayKey == $device->GatewayKey) {
                        if ($device->readDataTime()) {
                            $this->_check($device);
                        }
                    }
                }
            }
        }
    }
    /**
    * This function should be used to wait between config attempts
    *
    * @param DeviceContainer &$dev The device to check
    *
    * @return int The number of packets routed
    */
    private function _check(DeviceContainer &$dev)
    {
        // Be verbose ;)
        self::vprint(
            "Polling ".$dev->DeviceID." LastPoll: ".
            $dev->LastPoll,
            HUGnetClass::VPRINT_NORMAL
        );
        // Read the setup
        if (!$dev->readData()) {
            $this->_checkFail($dev);
        }
        // Update the row.  It changes the row even if it fails
        $dev->updateRow();
    }
    /**
    * This function should be used to wait between config attempts
    *
    * @param DeviceContainer &$dev The device to check
    *
    * @return int The number of packets routed
    */
    private function _checkFail(DeviceContainer &$dev)
    {
        // Print out the failure if verbose
        self::vprint(
            "Failed. Failures: ".$dev->params->DriverInfo["PollFail"]
            ." LastPoll try: "
            .date("Y-m-d H:i:s", $dev->params->DriverInfo["LastPoll"]),
            HUGnetClass::VPRINT_NORMAL
        );
        // Log an error for every 10 failures
        if ((($dev->params->DriverInfo["PollFail"] % 10) == 0)
            && ($dev->params->DriverInfo["PollFail"] > 0)
        ) {
            $this->logError(
                "NOPOLL",
                "Device ".$dev->DeviceID." is has failed "
                .$dev->params->DriverInfo["PollFail"]." polls",
                ErrorTable::SEVERITY_WARNING,
                "DeviceConfig::config"
            );
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
            $this->unsolicited->readDataTimeReset();
            // Set our gateway key
            $this->unsolicited->GatewayKey = $this->GatewayKey;
            // Set the device active
            $this->unsolicited->Active = 1;
            // Update the row
            $this->unsolicited->updateRow();
        }
    }
}
?>
