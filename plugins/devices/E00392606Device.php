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
    /** Error for the clock skew */
    const MAX_LOCK_TIME = 1800;
    /** Error for the clock skew */
    const ERROR_CLOCK_SKEW = -2321;
    /** The placeholder for the reading the downstream units from a controller */
    const COMMAND_READDOWNSTREAM = "56";
    /** The placeholder for locking a device */
    const COMMAND_GETDEVLOCK = "57";
    /** The placeholder for locking a device */
    const COMMAND_SETDEVLOCK = "58";
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
        $this->pktSetDevLock($pkt);
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
                "Got a lock request for ".$DeviceID." from "
                .self::stringSize($pkt->From, 6),
                self::VERBOSITY
            );
            $lock = $this->checkLocalDevLock($DeviceID);
            $locker = self::stringSize(dechex($lock->id), 6);
            $this->checkLockerID(hexdec($pkt->From), true);
            $time = "until ".date("Y-m-d H:i:s", $lock->expiration);
            if (($lock->isEmpty()) || empty($locker)) {
                $dev = new DeviceContainer();
                $dev->getRow(hexdec($DeviceID));
                $this->setLocalDevLock($dev, hexdec($pkt->From), 10, true);
                $locker = "no one";
                $time = "";
            } else {
                $data  = self::stringSize(dechex($lock->id), 6);
                $data .= self::stringSize(
                    dechex($lock->expiration - $this->now()), 4
                );
            }
            self::vprint(
                "Replying that $locker has a lock $time ($data)",
                self::VERBOSITY
            );
            $pkt->reply($data);
        }
    }
    /**
    * This deals with Packets to me
    *
    * @param PacketContainer &$pkt The packet that is to us
    *
    * @return string
    */
    protected function pktSetDevLock(PacketContainer &$pkt)
    {
        if ($pkt->toMe() && ($pkt->Command == self::COMMAND_SETDEVLOCK)) {
            $DeviceID = substr($pkt->Data, 0, 6);
            $this->checkLockerID(hexdec($pkt->From), true);
            $timeout = hexdec(substr($pkt->Data, 6, 4));
            self::vprint(
                "Got a set lock request for ".$DeviceID." from ".$pkt->From,
                self::VERBOSITY
            );
            $lock = $this->checkLocalDevLock($DeviceID);
            if ($lock->isEmpty() || ($lock->id === hexdec($pkt->From))) {
                $dev = new DeviceContainer();
                $dev->getRow(hexdec($DeviceID));
                $lock->id = $pkt->From;
                $lock->type = static::LOCKTYPE;
                $lock->lockData = $DeviceID;
                $lock->expiration = $this->now() + $timeout;
                $ret = $this->setLocalDevLock(
                    $dev, hexdec($pkt->From), $timeout, true
                );
                $data  = $DeviceID;
                $data .= self::stringSize(dechex($timeout), 4);
                self::vprint(
                    "Replying that $locker has a lock "
                    ." until ".date("Y-m-d H:i:s", $lock->expiration),
                    self::VERBOSITY
                );
            } else {
                $data = "";
            }
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
    protected function &readDevLock($locker, &$dev)
    {
        $class = get_class($this->devLocks);
        $lock = new $class();
        if ($dev->isEmpty()) {
            return $lock;
        }
        self::vprint(
            "Sending a lock request for ".$dev->DeviceID." to "
            .self::stringSize($locker, 6),
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
            $lock->lockData   = $dev->DeviceID;
            $lock->id         = hexdec(substr($pkt->Reply->Data, 0, 6));
            $timeout          = hexdec(substr($pkt->Reply->Data, 6, 4));
            if ($this->checkLockerID($lock->id)) {
                if ($timeout > self::MAX_LOCK_TIME) {
                    $lock->clearData();
                } else {
                    self::vprint(
                        strtoupper($locker)." Replied: ".$dev->DeviceID." locked by "
                        .self::stringSize(dechex($lock->id), 6)
                        ." until ".date("Y-m-d H:i:s", $lock->expiration)." s",
                        self::VERBOSITY
                    );
                    $lock->expiration = $this->now() + $timeout;
                    $lock->type = static::LOCKTYPE;
                }
            } else {
                // Bad locker ID
                $lock->clearData();
            }
        }
        return $lock;
    }
    /**
    * Checks to see if a device is a valid locker
    *
    * @param int  $id     The id to check
    * @param bool $update Whether to update the record or not
    *
    * @return bool True on success, False on failure
    */
    protected function checkLockerID($id, $update = false)
    {
        if ($id < 0xFD0000) {
            return false;
        }
        $dev = new DevicesTable();
        $dev->getRow($id);
        if ($dev->isEmpty()) {
            DevicesTable::insertDeviceID(
                array(
                    "id" => $id, "DeviceID" => $id,
                    "Active" => 1, "GatewayKey" => $this->myDriver->GatewayKey,
                    "Driver" => $this->myDriver->Driver,
                    "HWPartNum" => $this->myDriver->HWPartNum,
                    "FWPartNum" => $this->myDriver->FWPartNum,
                    "FWVersion" => $this->myDriver->FWVersion,
                )
            );
        } else {
            if ($dev->HWPartNum !== $this->myDriver->HWPartNum) {
                return false;
            }
            if (($dev->Active != 1) && !$update) {
                return false;
            } else {
                $dev->Active = 1;
            }
        }
        if ($update) {
            $dev->updateRow(array("Active" => "Active"));
        }
        return true;
    }
    /**
    * Reads the setup out of the device.
    *
    * @param DeviceContainer &$dev The device to get a lock on
    *
    * @return bool True on success, False on failure
    */
    public function &getDevLock(DeviceContainer &$dev)
    {
        $remote = null;
        $time   = null;
        $locks    = &$this->checkRemoteDevLock($dev);
        foreach (array_keys($locks) as $key) {
            if (is_object($locks[$key]) && !$locks[$key]->isEmpty()) {
                $remote = $locks[$key]->id;
                $time   = ($locks[$key]->expiration - $this->now());
                break;
            }
        }
        $locks[1] = &$this->checkLocalDevLock($dev->DeviceID, false);
        // This resets the local lock if it is different from the remote ones.
        if (($locks[1]->id !== $remote) && !is_null($remote)) {
            $this->setLocalDevLock(&$dev, $remote, $time, true);
            $locks[1] = &$this->checkLocalDevLock($dev->DeviceID, false);
        }
        return $locks;
    }
    /**
    * Reads the setup out of the device.
    *
    * @param DeviceContainer &$dev The device to get a lock on
    *
    * @return bool True on success, False on failure
    */
    protected function &checkRemoteDevLock(&$dev)
    {
        $devs = $this->myDriver->selectIDs(
            "GatewayKey = ? AND Driver = ? AND id <> ? AND Active = ?",
            array(
                $this->myDriver->GatewayKey,
                static::$registerPlugin["Name"],
                $this->myDriver->id,
                1
            )
        );
        $ret = array();
        foreach ($devs as $d) {
            $ret[$d] = $this->readDevLock(dechex($d), $dev);
            if (!empty($ret)) {
                break;
            }
        }
        return $ret;
    }
    /**
    * Reads the setup out of the device
    *
    * @param DeviceContainer &$dev   The device to lock
    * @param int             $locker The deviceID of the locking device
    * @param int             $time   The time to lock for
    * @param bool            $force  Whether to force the writing or not
    *
    * @return bool True on success, False on failure
    */
    public function setRemoteDevLock(&$dev, $locker, $time=null, $force=false)
    {
        if ($dev->isEmpty()) {
            return false;
        }
        self::vprint(
            "Sending a save lock request for ".$dev->DeviceID." to "
            .self::stringSize(dechex($locker), 6),
            self::VERBOSITY
        );
        $pkt = new PacketContainer(
            array(
                "To"      => dechex($locker),
                "From"    => (int) $this->myDriver->id,
                "Command" => self::COMMAND_SETDEVLOCK,
                "Data"    => $dev->DeviceID.self::stringSize(
                    dechex($time), 4
                ),
                "Timeout" => 3,
            )
        );
        $ret = $pkt->send();
        if ($ret && is_object($pkt->Reply) && !empty($pkt->Reply->Data)) {
            return $pkt->Reply->Data === $pkt->Data;
        }
        return false;
    }
    /**
    * Reads the setup out of the device.
    *
    * @param DeviceContainer &$dev   The device to lock
    * @param int             $locker The deviceID of the locking device
    * @param int             $time   The time to lock for
    * @param bool            $force  Whether to force the writing or not
    *
    * @return bool True on success, False on failure
    */
    public function setLocalDevLock(&$dev, $locker, $time = null, $force=false)
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
            if ($locker === $this->myDriver->id) {
                $locker = 1;
            }
            return $this->devLocks->place(
                $locker,
                static::LOCKTYPE,
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
    * @param DeviceContainer &$dev  The device to get a lock on
    * @param int             $time  The time to lock for
    * @param bool            $force Whether to force the writing or not
    *
    * @return bool True on success, False on failure
    */
    public function &setDevLock(
        DeviceContainer &$dev, $time = null, $force=false
    ) {
        $locks[1] = &$this->setLocalDevLock(
            $dev, $this->myDriver->id, $time, $force
        );
        $devs = $this->myDriver->selectIDs(
            "GatewayKey = ? AND Driver = ? AND id <> ? AND Active = ?",
            array(
                $this->myDriver->GatewayKey,
                static::$registerPlugin["Name"],
                $this->myDriver->id,
                1
            )
        );
        $ret = array();
        foreach ($devs as $d) {
            $locks[$d] = &$this->setRemoteDevLock(
                $dev, $d, $time, $force
            );
        }
        return $locks;
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
        if (empty($time) || ($time > self::MAX_LOCK_TIME)) {
            return (int)$timeout;
        }
        return (int)$time;
    }

}

?>
