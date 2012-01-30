<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** This is the required files */
require_once dirname(__FILE__).'/../base/HUGnetClass.php';
require_once dirname(__FILE__).'/../containers/DeviceContainer.php';
require_once dirname(__FILE__).'/../interfaces/DeviceDriverInterface.php';
require_once dirname(__FILE__).'/../interfaces/OutputInterface.php';
require_once dirname(__FILE__).'/../containers/PacketContainer.php';
require_once dirname(__FILE__).'/../tables/RawHistoryTable.php';

/**
 * Base class for all device drivers
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class DeviceDriverBase extends HUGnetClass
    implements DeviceDriverInterface, OutputInterface
{
    /** @var This is to register the class */
    public static $registerPlugin = array();
    /** @var This is to register the class */
    protected $myDriver = null;
    /** @var This is to register the class */
    protected $outputLabels = array(
        "PhysicalSensors" => "Physical Sensors",
        "VirtualSensors" => "Virtual Sensors",
    );
    /**
    * Builds the class
    *
    * @param object &$obj   The object that is registering us
    * @param mixed  $string The string we will use to build the object
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function __construct(&$obj, $string = "")
    {
        $this->myDriver = &$obj;
        $this->data = &$this->myDriver->params->DriverInfo;
        $this->verbose($this->myDriver->verbose);
    }
    /**
    * Says whether this device has loadable firmware or not
    *
    * This default always returns false.  It should be overwritten in classes
    * that use loadable firmware.
    *
    * @return bool False
    */
    public function loadable()
    {
        return false;
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
        return false;
    }
    /**
    * Says whether this device is a controller board or not
    *
    * This default always returns false.  It should be overwritten in classes
    * that are for controller boards.
    *
    * @return bool False
    */
    public function controller()
    {
        return false;
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    public function readSetup()
    {
        $ret = $this->readConfig();
        return $this->setLastConfig($ret);
    }
    /**
    * Checks the interval to see if it is ready to config.
    *
    * I want:
    *    If the config is not $interval old: return false
    *    else: if we have tried in the last 5 minutes, return false
    *    else: return true
    *
    * @param int $interval The interval to check, in hours
    *
    * @return bool True on success, False on failure
    */
    public function readSetupTime($interval = 12)
    {
        $this->data = &$this->myDriver->params->DriverInfo;
        // This is what would normally be our time.  Every 12 hours.
        if ($this->data["LastConfig"] > time()) {
            // If our time is in the future we have a clock problem.  Go now
            return true;
        } else if (($this->data["LastConfig"] + ($interval * 3600)) > time()) {
            return false;
        } else if (($this->data["LastConfigTry"] + 300) > time()) {
            return false;
        }
        return true;
    }
    /**
    * Resets all of the timers associated with reading and writing.
    *
    * @return bool True on success, False on failure
    */
    public function readSetupTimeReset()
    {
        $this->data = &$this->myDriver->params->DriverInfo;
        // Config
        $this->data["LastConfig"] = 0;
        $this->data["ConfigFail"] = 0;
        $this->data["LastConfigTry"] = 0;
    }
    /**
    * Resets all of the timers associated with reading and writing.
    *
    * @return bool True on success, False on failure
    */
    public function readDataTimeReset()
    {
        $this->data = &$this->myDriver->params->DriverInfo;
        // Poll
        $this->data["LastPoll"] = 0;
        $this->data["PollFail"] = 0;
        $this->data["LastPollTry"] = 0;
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    public function readConfig()
    {
        $this->data = &$this->myDriver->params->DriverInfo;
        // Save the time.
        $this->data["LastConfigTry"] = time();
        // Send the packet out
        $ret = $this->sendPkt(PacketContainer::COMMAND_GETSETUP);
        if (is_string($ret)) {
            $this->myDriver->fromSetupString($ret);
            return true;
        }
        return false;
    }
    /**
    * Reads the setup out of the device
    *
    * @param bool $pass Whether to set a pass or fail
    *
    * @return bool True on success, False on failure
    */
    protected function setLastConfig($pass=true)
    {
        $this->data = &$this->myDriver->params->DriverInfo;
        if ($pass === true) {
            $this->data["LastConfig"] = time();
            $this->data["ConfigFail"] = 0;
            $this->data["TotalConfigSuccess"]++;
            $this->myDriver->params->LastContact = time();
            $this->myDriver->Active = 1;
            return true;
        } else if (is_null($pass)) {
            $this->data["TotalConfigNull"]++;
            return null;
        }
        // We failed.  State that.
        $this->data["ConfigFail"]++;
        $this->data["TotalConfigFail"]++;
        return false;
    }
    /**
    * Reads the setup out of the device
    *
    * @param bool $pass Whether to set a pass or fail
    *
    * @return bool True on success, False on failure
    */
    protected function setLastPoll($pass=true)
    {
        $this->data = &$this->myDriver->params->DriverInfo;
        if ($pass) {
            $now = time();
            if (!empty($this->data["LastPoll"])) {
                $this->data["PollInt"] += ($now - $this->data["LastPoll"]);
            }
            $this->data["LastPoll"] = $now;
            $this->data["PollFail"] = 0;
            $this->data["TotalPollSuccess"]++;
            $this->myDriver->params->LastContact = time();
            return true;
        }
        // We failed.  State that.
        $this->data["PollFail"]++;
        $this->data["TotalPollFail"]++;
        return $pass;
    }
    /**
    * Reads the calibration out of the device
    *
    * @return bool True on success, False on failure
    */
    protected function readCalibration()
    {
        $ret = $this->sendPkt(PacketContainer::COMMAND_GETCALIBRATION);
        if (is_string($ret)) {
            $this->myDriver->params->Raw["Calibration"] = $ret;
            $this->myDriver->sensors->fromCalString($ret);
            return true;
        }
        return false;
    }
        /**
    * Checks the interval to see if it is ready to config.
    *
    * I want:
    *    If the config is not $interval old: return false
    *    else: return based on number of failures.  Pause longer for more failures
    *
    *    It waits an extra 6 seconds for each failed poll
    *
    * @return bool True on success, False on failure
    */
    public function readDataTime()
    {
        $this->data = &$this->myDriver->params->DriverInfo;
        // This is what would normally be our time.  Every 12 hours.
        $interval = $this->myDriver->PollInterval;
        if (empty($interval)) {
            // No polling if the interval is set to 0
            return false;
        }
        if ($this->data["LastPoll"] > time()) {
            // If our time is in the future we have a clock problem.  Go now
            return true;
        } else if (($this->data["LastPoll"] + ($interval * 60) - 30) > time()) {
            // The '-  30' is so that it can poll slightly before the interval ends
            return false;
        } else if (($this->data["LastPollTry"] + 60) > time()) {
            return false;
        }
        return true;
    }
    /**
    * Reads the data out of the device
    *
    * @return bool True on success, False on failure
    */
    public function readData()
    {
        $ret = $this->readSensors();
        return $this->setLastPoll($ret);
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    protected function readSensors()
    {
        $this->data = &$this->myDriver->params->DriverInfo;
        // Save the time.
        $this->data["LastPollTry"] = time();
        // Send the packet out
        $pkt = new PacketContainer(
            array(
                "To"       => (int)$this->myDriver->id,
                "Command"  => PacketContainer::COMMAND_GETDATA,
                "Timeout"  => $this->myDriver->DriverInfo["PacketTimeout"],
            )
        );
        $pkt->send();
        if (is_object($pkt->Reply)) {
            if ($this->data["DataIndex"] === $this->dataIndex($pkt->Reply->Data)) {
                return false;
            }
            $this->data["ReplyPkts"]++;
            $this->data["ReplyTotal"] += $pkt->replyTime();
            $hist = new RawHistoryTable(
                array(
                    "id" => (int)$this->myDriver->id,
                    "Date" => $pkt->Date,
                    "packet" => $pkt,
                    "device" => $this->myDriver->toString(),
                    "command" => $pkt->Command,
                    "dataIndex" => $this->dataIndex($pkt->Reply->Data),
                )
            );
            $this->data["DataIndex"] = $this->dataIndex($pkt->Reply->Data);
            return $hist->insertRow();
        }
        $this->data["NoReplyPkts"]++;
        return false;
    }
    /**
    * Deals with memory.  This will read and write to any type of memory
    *
    * @param int    $addr    The start address of this block
    * @param string $data    The data to program into E2 as a hex string
    * @param string $command The command to use
    *
    * @return true on success, false on failure
    */
    protected function memPage($addr, $data, $command)
    {
        return $this->sendPkt(
            $command,
            $this->stringSize(dechex(($addr>>8) & 0xFF), 2)
            .$this->stringSize(dechex($addr & 0xFF), 2)
            .$data
        );
    }
    /**
    * Deals with memory.  This will read and write to any type of memory
    *
    * @param string $command The command to use
    * @param string $data    The data to use
    * @param bool   $reply   Wait for a reply
    *
    * @return true on success, false on failure
    */
    protected function sendPkt($command, $data = "", $reply = true)
    {
        // If the driver has gone away exit.
        if (empty($this->myDriver->id)) {
            return false;
        }
        $pkt = new PacketContainer(
            array(
                "To"       => (int) $this->myDriver->id,
                "Command"  => $command,
                "Data"     => $data,
                "GetReply" => $reply,
                "Timeout"  => $this->myDriver->DriverInfo["PacketTimeout"],
            )
        );
        $ret = $pkt->send();
        if ($reply === false) {
            $this->data["NoReplyPkts"]++;
            return $ret;
        } else if (is_object($pkt->Reply)) {
            $this->data["ReplyPkts"]++;
            $this->data["ReplyTotal"] += $pkt->replyTime();
            return $pkt->Reply->Data;
        }
        return false;
    }
    /**
    * reads a block of flash
    *
    * @param int $addr   The start address of this block
    * @param int $length The length to read.  0-255
    *
    * @return true on success, false on failure
    */
    protected function readSRAM($addr, $length)
    {
        return $this->memPage(
            $addr,
            $this->stringSize(dechex($length), 2),
            PacketContainer::COMMAND_READSRAM
        );
    }
    /**
    * reads a block of flash
    *
    * @param int $addr   The start address of this block
    * @param int $length The length to read.  0-255
    *
    * @return true on success, false on failure
    */
    protected function readFlash($addr, $length)
    {
        return $this->memPage(
            $addr,
            $this->stringSize(dechex($length), 2),
            PacketContainer::COMMAND_READFLASH
        );
    }

    /**
    * Reads a block of E2
    *
    * @param int $addr   The start address of this block
    * @param int $length The length to read.  0-255
    *
    * @return true on success, false on failure
    */
    protected function readE2($addr, $length)
    {

        return $this->memPage(
            $addr,
            $this->stringSize(dechex($length), 2),
            PacketContainer::COMMAND_READE2
        );
    }

    /**
    * Creates the object from a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function toSetupString($default = true)
    {
        return "";
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
        $this->myDriver->DriverInfo["TimeConstant"] = hexdec(substr($string, 0, 2));
        if (is_object($this->myDriver->sensors)) {
            $this->myDriver->sensors->fromTypeString(substr($string, 2));
        }
    }
    /**
    * Takes in a raw string from a sensor gets the dataIndex out of it
    *
    * @param string $string The string to convert
    *
    * @return int
    */
    public function dataIndex($string)
    {
        return hexdec(substr($string, 0, 2));
    }
    /**
    * Takes in a raw string from a sensor gets the timeConstant
    *
    * @param string $string The string to convert
    *
    * @return int
    */
    public function timeConstant($string)
    {
        $timeconstant = hexdec(substr($string, 4, 2));
        if (empty($timeconstant)) {
            // This is the old location for the time constant
            $timeconstant = hexdec(substr($string, 2, 2));
        }
        return $timeconstant;
    }
    /**
    * Takes in a raw string from a sensor gets the sensor data
    *
    * @param string $string The string to convert
    *
    * @return array
    */
    public function sensorData($string)
    {
        // Get the raw sensor string
        $raw = str_split(substr($string, 6), 6);
        return $this->sensorStringArrayToInts($raw);
    }
    /**
    * Takes in a raw string from a sensor and makes an int out it
    *
    * The sensor data is stored little-endian, so it just takes that and adds
    * the bytes together.
    *
    * @param string $string The string to convert
    *
    * @return int
    */
    protected function sensorStringToInt($string)
    {
        $bytes = str_split($string, 2);
        $shift = 0;
        $return = 0;
        foreach ($bytes as $b) {
            $return += hexdec($b) << $shift;
            $shift += 8;
        }
        return $return;
    }

    /**
    * Takes in an array of raw strings and returns an array of integers
    *
    * @param array $array The array of sensor strings to convert.
    *
    * @return array of ints
    */
    protected function sensorStringArrayToInts($array)
    {
        $return = array();
        foreach ((array)$array as $key => $string) {
            $return[$key] = $this->sensorStringToInt($string);
        }
        return $return;
    }
    /**
    * This returns true if the device is out of contact
    *
    * @return bool True if ready to return, false otherwise
    */
    public function lostContact()
    {
        return $this->myDriver->params->LastContact < (time() - 3600);
    }
    /**
    * Decodes the sensor data
    *
    * @param string $string  The string of sensor data
    * @param string $command The command that was used to get the data
    * @param float  $deltaT  The time difference between this packet and the next
    * @param float  $prev    The previous record
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function decodeData($string, $command="", $deltaT = 0, $prev = null)
    {
        $data = $this->decodeSensorString($string);
        $data["deltaT"] = $deltaT;
        $ret = $this->myDriver->sensors->decodeSensorData($data, $prev);
        $ret["DataIndex"] = $data["DataIndex"];
        $ret["timeConstant"] = $data["timeConstant"];
        $ret["deltaT"] = $data["deltaT"];
        return $ret;
    }

    /**
    * Decodes the sensor string
    *
    * @param string $string The string of sensor data
    *
    * @return null
    */
    protected function decodeSensorString($string)
    {
        $ret = $this->sensorStringArrayToInts(str_split(substr($string, 6), 6));
        $ret["DataIndex"] = hexdec(substr($string, 0, 2));
        $ret["timeConstant"] = hexdec(substr($string, 4, 2));
        return $ret;
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function toOutput($cols = null)
    {
        $ret = array_merge(
            (array)$this->myDriver->params->DriverInfo,
            (array)$this->myDriver->DriverInfo
        );
        return $ret;
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    */
    public function toOutputHeader($cols = null)
    {
        if (empty($cols)) {
            return $this->outputLabels;
        } else {
            return (array)$cols;
        }
    }
    /**
    * There should only be a single instance of this class
    *
    * @param string $type The output plugin type
    * @param array  $cols The columns to get
    *
    * @return array
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function outputParams($type, $cols = null)
    {
        return array();
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function outputFilters($cols = null)
    {
        return array();
    }
    /**
    * Registers extra vars
    *
    * @param mixed $data The data to import into the class
    *
    * @return null
    */
    public function &outputFactory($data = array())
    {
        include_once dirname(__FILE__)."/../containers/OutputContainer.php";
        return new OutputContainer($data, $this);
    }
}