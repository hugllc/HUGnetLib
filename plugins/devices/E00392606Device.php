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
require_once dirname(__FILE__).'/../../tables/LockTable.php';

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
    const ERROR_CLOCK_SKEW = -2321;
    /** The placeholder for the reading the downstream units from a controller */
    const COMMAND_READDOWNSTREAM = "56";
    /** The verbose setting for output */
    const VERBOSITY = 2;
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
    * Builds the class
    *
    * @param object &$obj   The object that is registering us
    * @param mixed  $string The string we will use to build the object
    *
    * @return null
    */
    public function __construct(&$obj, $string = "")
    {
        parent::__construct($obj, $string);
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
        $this->pktSetupEcho($pkt);
        $this->pktDownstreamDevices($pkt);
        $this->pktGetDevLock($pkt);
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
            $ret = $this->readDownstreamDevices();
            $rtc = $this->readRTC();
            if (is_int($rtc)) {
                if ((($rtc + 60) < $this->now())
                    || (($rtc - 60) > $this->now())
                ) {
                    $this->logError(
                        self::ERROR_CLOCK_SKEW,
                        "Clock skew between data collectors found"
                        ."(".($rtc-$this->now())." s)",
                        ErrorTable::SEVERITY_CRITICAL,
                        "readSetup"
                    );
                    // Try to set the clock
                    @system("ntpdate -s ntp.ubuntu.com");
                }
            }
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
    /**
    * This deals with Packets to me
    *
    * @param PacketContainer &$pkt The packet that is to us
    *
    * @return string
    */
    protected function pktGetDevLock(PacketContainer &$pkt)
    {
        if ($pkt->toMe() && ($pkt->Command == self::COMMAND_GETDEVLOCK)) {
            $DeviceID = substr($pkt->Data, 0, 6);
            self::vprint(
                "Got a lock request for ".$DeviceID." from ".$pkt->From,
                self::VERBOSITY
            );
            $data = $this->checkLocalDevLock($DeviceID, true);
            $locker = substr($data, 0, 6);
            $time = " for ".hexdec(substr($data, 6, 4))." s";
            if (empty($data)) {
                $dev = new DeviceContainer();
                $dev->getRow(hexdec($DeviceID));
                $this->setDevLock($dev, hexdec($pkt->From), null, true);
                $locker = "no one";
                $time = "";
            }
            self::vprint(
                "Replying that $locker has a lock $time",
                self::VERBOSITY
            );
            $pkt->reply($data);
        }
    }
    /**
    * Reads the setup out of the device
    *
    * @param string          $locker The deviceID of the locking device
    * @param DeviceContainer &$dev   The device to get a lock on
    *
    * @return bool True on success, False on failure
    */
    protected function readDevLock($locker, &$dev)
    {
        self::vprint(
            "Sending a lock request for ".$dev->DeviceID." to ".$locker,
            self::VERBOSITY
        );
        $pkt = new PacketContainer(
            array(
                "To"      => $locker,
                "From"    => (int) $this->myDriver->id,
                "Command" => self::COMMAND_GETDEVLOCK,
                "Data"    => $dev->DeviceID,
                "Timeout" => 3,
            )
        );
        $ret = $pkt->send();
        if (is_object($pkt->Reply) && !empty($pkt->Reply->Data)) {
            $DeviceID = substr($pkt->Reply->Data, 0, 6);
            $time     = hexdec(substr($pkt->Reply->Data, 6, 4));
            self::vprint(
                "$locker Replied: ".$dev->DeviceID." locked by $DeviceID"
                ." for $time s",
                self::VERBOSITY
            );
            $ret = $this->setDevLock($dev, hexdec($DeviceID), $time, true);
        }
        if ($ret) {
            return $DeviceID;
        }
        return "";
    }
    /**
    * Reads the setup out of the device.
    *
    * @param DeviceContainer &$dev The device to get a lock on
    *
    * @return bool True on success, False on failure
    */
    public function getDevLock(DeviceContainer &$dev)
    {
        $ret = $this->checkDevLock($dev);
        if (empty($ret) || (hexdec($ret) === $this->myDriver->ControllerKey)) {
            $ret = $this->setDevLock(
                $dev, $this->myDriver->ControllerKey, null, true
            );
        } else {
            $ret = false;
        }
        return $ret;
    }
    /**
    * Reads the setup out of the device.
    *
    * @param DeviceContainer &$dev The device to get a lock on
    *
    * @return bool True on success, False on failure
    */
    protected function checkDevLock(&$dev)
    {
        $local = $this->checkLocalDevLock($dev->DeviceID, false);
        $remote = $this->checkRemoteDevLock($dev);
        if (!empty($local) && !empty($remote) && ($local !== $remote)) {
            $this->logError(
                $errorInfo[0],
                $dev->DeviceID." is locked by both $local and $remote",
                ErrorTable::SEVERITY_ERROR,
                "checkDevLock"
            );
        }
        if (!empty($remote)) {
            return $remote;
        }
        return $local;
    }
    /**
    * Reads the setup out of the device.
    *
    * @param DeviceContainer &$dev The device to get a lock on
    *
    * @return bool True on success, False on failure
    */
    protected function checkRemoteDevLock(&$dev)
    {
        $devs = $this->myDriver->selectIDs(
            "GatewayKey = ? AND Driver = ? AND id <> ?",
            array(
                $this->myDriver->GatewayKey,
                static::$registerPlugin["Name"],
                $this->myDriver->id
            )
        );
        $ret = false;
        foreach ($devs as $d) {
            $ret = $this->readDevLock(dechex($d), $dev);
            if (!empty($ret)) {
                break;
            }
        }
        return $ret;
    }
    /**
    * Reads the setup out of the device.
    *
    * @param DeviceContainer &$dev   The device to lock
    * @param string          $locker The deviceID of the locking device
    * @param int             $time   The time to lock for
    * @param bool            $force  Whether to force the writing or not
    *
    * @return bool True on success, False on failure
    */
    protected function setDevLock(&$dev, $locker, $time = null, $force=false)
    {
        if ($dev->isEmpty()) {
            return false;
        }
        $timeout = $this->getDevLockTime($dev, $time);
        if (is_int($timeout) && !empty($timeout)) {
            self::vprint(
                "Setting expiration of lock on ".$dev->DeviceID." by "
                .self::stringSize(dechex($locker), 6)." for "
                .date("Y-m-d H:i:s", $timeout + $this->now()),
                self::VERBOSITY
            );
            return $this->devLocks->place(
                $locker,
                self::LOCKTYPE,
                $dev->DeviceID,
                $timeout,
                $force
            );
        }
        return false;
    }
    /**
    * Reads the setup out of the device.
    *
    * @param DeviceContainer &$dev The device to lock
    * @param int             $time The time to lock for
    *
    * @return bool True on success, False on failure
    */
    protected function getDevLockTime(&$dev, $time)
    {
        $interval = (empty($dev->PollInterval)) ? 10 : $dev->PollInterval;
        $timeout = $interval * 60 * 1.5;
        while ($timeout < 600) {
            // Keep adding poll intervals until we are > 10 minutes
            // This way the timeout is never an even multiple of the poll interval
            $timeout += $interval * 60;
        }
        if ($time > $timeout) {
            return false;
        }
        // Only allow 1800 seconds at most for the lock
        if (empty($time) || ($time > 1800)) {
            return (int)$timeout;
        }
        return (int)$time;
    }


}

?>
