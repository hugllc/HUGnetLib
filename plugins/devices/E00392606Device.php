<?php
/**
 * This is the driver code for the HUGnet data collector (0039-26).
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

// This is our base class
require_once dirname(__FILE__).'/E00392600Device.php';

/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Endpoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2011 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class E00392606Device extends E00392600Device
    implements DeviceDriverInterface
{
    /** The placeholder for the reading the downstream units from a controller */
    const COMMAND_READDOWNSTREAM = "56";
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00392606",
        "Type" => "device",
        "Class" => "E00392606Device",
        "Flags" => array(
            "DEFAULT:0039-26-06-P:DEFAULT",
        ),
    );
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
        $this->pktSetupEcho($pkt);
        $this->pktDownstreamDevices($pkt);
    }
    /**
    * This deals with Packets to me
    *
    * @param PacketContainer &$pkt The packet that is to us
    *
    * @return string
    */
    protected function pktDownstreamDevices(PacketContainer &$pkt)
    {
        if ($pkt->toMe() && ($pkt->Command == self::COMMAND_READDOWNSTREAM)) {
            $devs = $this->myDriver->selectIDs(
                "GatewayKey = ? AND Active = ?",
                array($this->myDriver->GatewayKey, 1)
            );
            $data = "";
            foreach ($devs as $d) {
                $data .= $this->stringSize(dechex($d), 6);
            }
            $pkt->reply($data);
        }

    }
    /**
    * Reads the setup out of the device.
    *
    * If the device is using outdated firmware we have to
    *
    * @return bool True on success, False on failure
    */
    public function readSetup()
    {
        $ret = $this->readConfig();
        if ($ret) {
            // This doesn't count towards whether the config passes or fails because
            // the packet is currently too big to go through the new controller
            // board.  If it works it works.  If it doesn't it doesn't.
            $this->readDownstreamDevices();
        }
        return $this->setLastConfig($ret);
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    protected function readDownstreamDevices()
    {
        // Send the packet out
        $ret = $this->sendPkt(self::COMMAND_READDOWNSTREAM);
        if (is_string($ret) && !empty($ret)) {
            $devs = str_split($ret, 6);
            foreach ($devs as $d) {
                // If we have not seen this before try to put it in the database
                DevicesTable::insertDeviceID(
                    array(
                        "DeviceID" => $d,
                        "GatewayKey" => $this->myDriver->GatewayKey,
                    )
                );

            }
            $ret = true;;
        }
        return (bool) $ret;
    }

}

?>
