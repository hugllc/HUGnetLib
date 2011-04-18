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
require_once dirname(__FILE__).'/../../base/DeviceDriverBase.php';
// This is the interface we are implementing
require_once dirname(__FILE__).'/../../interfaces/DeviceDriverInterface.php';
require_once dirname(__FILE__).'/../../interfaces/PacketConsumerInterface.php';

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
class E00392600Device extends DeviceDriverBase
    implements DeviceDriverInterface
{
    /** The placeholder for the reading the downstream units from a controller */
    const COMMAND_READDOWNSTREAM = "56";
    /** The placeholder for locking a device */
    const COMMAND_GETDEVLOCK = "57";
    /** The type of locks we are doing */
    const LOCKTYPE = "device";
    /** The verbose setting for output */
    const VERBOSITY = 2;
    /** @var int The job number for unused1 */
    const JOB_UNUSED1  = 1;
    /** @var int The job number for periodic function */
    const JOB_PERIODIC = 2;
    /** @var int The job number for analysis */
    const JOB_ANALYSIS = 3;
    /** @var int The job number for endpoint */
    const JOB_ENDPOINT = 4;
    /** @var int The job number for unused2 */
    const JOB_UNUSED2  = 5;
    /** @var int The job number for device polling and stuff */
    const JOB_DEVICE   = 6;
    /** @var int The job number for unused3 */
    const JOB_UNUSED3  = 7;
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00392600",
        "Type" => "device",
        "Class" => "E00392600Device",
        "Flags" => array(
            "DEFAULT:0039-26-00-P:DEFAULT",
            "DEFAULT:0039-26-01-P:DEFAULT",
            "DEFAULT:0039-26-02-P:DEFAULT",
            "DEFAULT:0039-26-03-P:DEFAULT",
            "DEFAULT:0039-26-04-P:DEFAULT",
            "DEFAULT:0039-26-05-P:DEFAULT",
            "DEFAULT:0039-26-07-P:DEFAULT",
        ),
    );
    /** @var array These define what jobs this driver might see */
    protected $jobs = array(
        self::JOB_PERIODIC => "Periodic",
        self::JOB_ANALYSIS => "Analysis",
        self::JOB_ENDPOINT => "Endpoint",
        self::JOB_DEVICE   => "Device",
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
        $this->myDriver->DriverInfo["PhysicalSensors"] = 0;
        $this->myDriver->DriverInfo["VirtualSensors"] = 0;
        $this->myDriver->DriverInfo["PacketTimeout"] = 10;
        $this->fromSetupString($string);
        if (empty($this->myDriver->ControllerKey)) {
            $this->myDriver->ControllerKey = $this->myDriver->id;
        }
        $this->devLocks = new LockTable();
    }
    /**
    * Says whether this device is a gateway process or not
    *
    * This default always returns false.  It should be overwritten in classes
    * that are for gateway processes.
    *
    * @return bool False
    */
    public function gateway()
    {
        return true;
    }
    /**
    * Creates the object from a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toSetupString($default = true)
    {
        $this->Info = &$this->myDriver->DriverInfo;
        $string  = $this->myDriver->hexify($this->Info["Job"], 2);
        $string .= $this->myDriver->hexify($this->myDriver->GatewayKey, 4);
        $string .= $this->myDriver->hexifyStr(
            $this->myDriver->DriverInfo["Name"], 60
        );

        $myIP = explode(".", $this->Info["IP"]);

        for ($i = 0; $i < 4; $i++) {
            $string .= $this->myDriver->hexify($myIP[$i], 2);
        }

        $string .= $this->myDriver->hexify($this->Info["Priority"], 2);
        return $string;

    }

    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function fromSetupString($string)
    {
        if (empty($string)) {
            return;
        }
        $this->Info = &$this->myDriver->DriverInfo;
        $index = 0;
        // This byte is currently not used
        $this->Info["Job"] = hexdec(substr($string, $index, 2));
        $this->Info["Function"] = $this->_getFunction($this->Info["Job"]);
        $this->myDriver->DeviceName = $this->Info["Function"]." Process";
        $this->myDriver->DeviceJob = $this->Info["Function"];

        $index += 2;
        $this->Info["CurrentGatewayKey"] = hexdec(substr($string, $index, 4));

        $index += 4;
        $this->Info["Name"] = $this->myDriver->deHexify(
            trim(strtoupper(substr($string, $index, 60)))
        );
        $this->Info["Name"] = trim($this->Info["Name"]);

        $index += 60;
        $IP     = str_split(substr($string, $index, 8), 2);
        $index += 8;

        foreach ($IP as $k => $v) {
            $IP[$k] = hexdec($v);
        }
        $this->Info["IP"] = implode(".", $IP);
        $this->myDriver->DeviceLocation = $this->Info["IP"];
        $this->Info["Priority"] = hexdec(substr($string, $index, 2));
    }
    /**
    * This takes the numeric job and replaces it with a name
    *
    * @param int $job The job
    *
    * @return string
    */
    private function _getFunction($job)
    {
        if (!empty($this->jobs[$job])) {
            return $this->jobs[$job];
        }
        return "Unknown";
    }
    /**
    * Checks the interval to see if it is ready to config.
    *
    * I want:
    *    If the config is not $interval old: return false
    *    else: return based on number of failures.  Pause longer for more failures
    *
    *    It waits an extra minute for each failure
    *
    * @param int $interval The interval to check, in hours
    *
    * @return bool True on success, False on failure
    */
    public function readSetupTime($interval = 10)
    {
        if ($this->data["LastConfig"] > time()) {
            // If our time is in the future we have a clock problem.  Go now
            return true;
        }
        // This is what would normally be our time.  Every 10 minutes
        $base = $this->data["LastConfig"] < (time() - $interval*60);
        if ($base === false) {
            return $base;
        }
        // Accounts for failures
        return $this->data["LastConfigTry"]<(time() - $this->data["ConfigFail"]*60);
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    protected function readRTC()
    {
        $ret = $this->sendPkt(PacketContainer::READRTC_COMMAND);
        if (is_string($ret) && !empty($ret)) {
            $ret = hexdec($ret);
            return $ret;
        }
        return false;
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
        //$this->pktGetDevLock($pkt);
        $this->pktReadRTC($pkt);
    }
    /**
    * This deals with Packets to me
    *
    * @param PacketContainer &$pkt The packet that is to us
    *
    * @return string
    */
    protected function pktSetupEcho(PacketContainer &$pkt)
    {
        if ($pkt->toMe()) {
            if ($pkt->Command == PacketContainer::COMMAND_GETSETUP) {
                $pkt->reply((string)$this->myDriver);
                // Try to insert anything getting my configuration
                DevicesTable::insertDeviceID(
                    array(
                        "DeviceID" => $pkt->From,
                        "GatewayKey" => $this->myDriver->GatewayKey,
                    )
                );

            } else if (($pkt->Command == PacketContainer::COMMAND_ECHOREQUEST)
                || ($pkt->Command == PacketContainer::COMMAND_FINDECHOREQUEST)
            ) {
                $pkt->reply($pkt->Data);
            }
        }
    }
    /**
    * This deals with Packets to me
    *
    * @param PacketContainer &$pkt The packet that is to us
    *
    * @return string
    */
    protected function pktReadRTC(PacketContainer &$pkt)
    {
        if ($pkt->toMe() && ($pkt->Command == PacketContainer::READRTC_COMMAND)) {
            $data = self::stringSize(dechex($this->now()), 16);
            $pkt->reply($data);
        }
    }
    /**
    * Reads the setup out of the device.
    *
    * @param DeviceContainer &$dev The device to get a lock on
    *
    * @return bool True on success, False on failure
    */
    public function getMyDevLock(DeviceContainer &$dev)
    {
        $ret = $this->checkLocalDevLock($dev->DeviceID);
        if (hexdec($ret) === $this->myDriver->ControllerKey) {
            $ret = true;
        } else {
            $ret = false;
        }
        return $ret;
    }
    /**
    * Reads the setup out of the device.
    *
    * @param string $DeviceID The deviceID to check
    * @param bool   $time     The time left on the lock
    *
    * @return bool True on success, False on failure
    */
    protected function checkLocalDevLock($DeviceID, $time = false)
    {
        $data = "";
        $this->devLocks->check(
            $this->myDriver->ControllerKey, self::LOCKTYPE, $DeviceID
        );
        if (!$this->devLocks->isEmpty()) {
            $data = self::stringSize(dechex($this->devLocks->id), 6);
            self::vprint(
                "$data has lock on ".$DeviceID." until "
                .date("Y-m-d H:i:s", $this->devLocks->expiration),
                self::VERBOSITY
            );
            if ($time) {
                $data .= self::stringSize(
                    dechex($this->devLocks->expiration - $this->now()), 4
                );
            }
        }
        return $data;
    }

}

?>
