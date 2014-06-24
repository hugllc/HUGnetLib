<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * Networking for devices.
 *
 * This class will do all of the networking for devices.  It will poll, get configs,
 * update software, and anything else related to talking to devices.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Action
{
    /**
    * This is the system object
    */
    protected $system = null;
    /**
    * This is the driver object
    */
    protected $driver = null;
    /**
    * This is the table object
    */
    protected $device = null;
    /**
    * This is the table object
    */
    protected $average = null;
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$system The network application object
    * @param object &$device The device device object
    * @param object &$driver The device driver object
    *
    * @return null
    */
    protected function __construct(&$system, &$device, &$driver)
    {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a driver object",
            !is_object($driver)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a device object",
            !is_object($device)
        );
        $this->system = &$system;
        $this->driver  = &$driver;
        $this->device  = &$device;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->system);
        unset($this->driver);
        unset($this->device);
    }
    /**
    * This function creates the system.
    *
    * @param mixed  &$network (object)The system object to use
    * @param string &$device  (object)The device to use
    * @param object &$driver  The device driver object
    *
    * @return null
    */
    public static function &factory(&$network, &$device, &$driver)
    {
        $object = new Action($network, $device, $driver);
        return $object;
    }
    /**
    * Sends a packet
    *
    * @param mixed  $command  The command to send the packet with
    * @param string $callback The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config   The network config to use for the packet
    * @param mixed  $data     Array|String of data to send out
    *
    * @return success or failure of the packet sending
    */
    public function send(
        $command, $callback = null, $config = array(), $data = null
    ) {
        if (is_array($command)) {
            $command = array_change_key_case($command);
            if (isset($command['command'])) {
                $command = array($command);
            }
        }
        $ret = $this->device->network()->send($command, $callback, $config, $data);
        return $ret;
    }
    /**
    * Pings the device and sets the LastContact if it is successful
    *
    * @param bool $find Whether or not to use a find ping
    *
    * @return string The left over string
    */
    public function ping($find = false)
    {
        $pkt = $this->device->network()->ping(
            $find, null, null, array("tries" => 1, "find" => false)
        );
        $this->device->load($this->device->id());
        if (is_object($pkt) && is_string($pkt->reply())) {
            $this->device->setParam("LastContact", time());
            $this->device->setParam("ContactFail", 0);
            $ret = true;
        } else {
            $fail = $this->device->getParam("ContactFail");
            $this->device->setParam("ContactFail", $fail+1);
            $ret = false;
        }
        $this->device->store();
        return $ret;
    }
    /**
    * Gets the config and saves it
    *
    * @return string The left over string
    */
    public function config()
    {
        if ($this->programLock()) {
            return false;
        }
        $pkt = $this->device->network()->config();
        $this->device->load($this->device->id());
        if ($this->storeConfig($pkt->reply())) {
            $this->configStuff();
            return true;
        }
        $fail = $this->device->getParam("ConfigFail");
        $this->device->setParam("ConfigFail", $fail+1);
        $this->device->store();
        return false;
    }
    /**
    * Gets the config and saves it
    *
    * @return string The left over string
    */
    protected function configStuff()
    {
        $arch = $this->device->get("arch");
        if ($arch === "old") {
            /* This device doesn't have loadable sensors */
            return true;
        }
        $input = (int)$this->device->get("InputTables");
        for ($i = 0; $i < $input; $i++) {
            $this->system->out("InputTables $i", 2);
            $ret = $this->send(
                "READINPUTTABLE", null, array("find" => false), sprintf("%02X", $i)
            );
            if (!$this->storeIOP($i, $ret->reply(), "input")) {
                // Failure.  Stop trying
                return;
            }
        }
        $output = (int)$this->device->get("OutputTables");
        for ($i = 0; $i < $output; $i++) {
            $this->system->out("OutputTables $i", 2);
            $ret = $this->send(
                "READOUTPUTTABLE", null, array("find" => false), sprintf("%02X", $i)
            );
            if (!$this->storeIOP($i, $ret->reply(), "output")) {
                // Failure.  Stop trying
                return;
            }
        }
        $process = (int)$this->device->get("ProcessTables");
        for ($i = 0; $i < $process; $i++) {
            $this->system->out("ProcessTables $i", 2);
            $ret = $this->send(
                "READPROCESSTABLE", null, array("find" => false), sprintf("%02X", $i)
            );
            if (!$this->storeIOP($i, $ret->reply(), "process")) {
                // Failure.  Stop trying
                return;
            }
        }
    }
    /**
    * Gets the config and saves it
    *
    * @param string $string The string to decode
    *
    * @return bool True on success, false on failure
    */
    public function storeConfig($string)
    {
        if (is_string($string) && strlen($string)) {
            if ($this->device->decode($string)) {
                $this->device->set(
                    "GatewayKey", (int)$this->system->get("GatewayKey")
                );
                $this->device->setParam("LastContact", time());
                $this->device->setParam("LastConfig", time());
                $this->device->setParam("ConfigFail", 0);
                $this->device->setParam("ContactFail", 0);
                $this->device->store();
                return true;
            }
        }
        return false;
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $num    The number of the input to store
    * @param string $string The string to decode
    * @param string $type   The type to use
    *
    * @return bool True on success, false on failure
    */
    public function storeIOP($num, $string, $type)
    {

        if (!is_string($string) || !is_int($num) || !is_string($type)) {
            return false;
        }
        $type = trim(strtolower($type));
        if ($type == "input") {
            $iop = $this->device->input($num);
        } else if ($type == "output") {
            $iop = $this->device->output($num);
        } else if ($type == "process") {
            $iop = $this->device->process($num);
        }
        if (is_object($iop)) {
            $oldID = $iop->get("id");
            $iop->decode($string);
            if (($iop->get("id") == $oldID) || ($oldID == 0xFF)) {
                $iop->store();
            }
            return true;
        }
        return false;
    }
    /**
    * Gets the config and saves it
    *
    * @param mixed $set - null = read lock stat, true = set, false = remove
    *
    * @return int if lock is set,false if lock doesn't exist, true if it does
    */
    protected function programLock($set = null)
    {
        $now = $this->system->now();
        $locked = $this->device->getParam("ProgramLock");
        if (($now <= $locked) && !($set === false)) {
            $this->system->out(
                "Device locked until ".date("Y-m-d H:i:s", $locked), 1
            );
            return true;
        }
        if (is_bool($set)) {
            if ($set) {
                $lock = $now + 60;
                $this->system->out(
                    "Setting device lock to expire ".date("Y-m-d H:i:s", $lock), 1
                );
            } else {
                $lock = 0;
                $this->system->out(
                    "Device unlocked", 1
                );
            }
            $this->device->load($this->device->id());
            $this->device->setParam("ProgramLock", $lock);
            $this->device->store();
            return $lock;
        }
        return false;

    }
    /**
    * Polls the device and saves the poll
    *
    * @param int $TestID The test ID of this poll
    * @param int $time   The time to use for the poll
    *
    * @return false on failure, the history object on success
    */
    public function poll($TestID = null, $time = null)
    {
        if ($this->programLock()) {
            return false;
        }
        $HWPart = $this->device->get("HWPartNum");
        if (empty($HWPart)) {
            return false;
        }
        $pkt = $this->device->network()->poll();
        return $this->storePoll($pkt, $time, $TestID);
    }
    /**
    * This deals with a poll
    *
    * @param object &$pkt   The packet from the poll
    * @param int    $time   The time to use for the poll
    * @param int    $TestID The test ID of this poll
    *
    * @return false on failure, the history object on success
    */
    public function storePoll(&$pkt, $time = null, $TestID = null)
    {
        if (empty($time)) {
            $time = time();
        }
        if (strlen($pkt->reply()) > 0) {
            $prev   = (array)$this->device->getParam("LastPollData");
            $deltaT = $time - $prev["Date"];
            $data   = $this->device->decodeData(
                $pkt->Reply(),
                $pkt->Command(),
                $deltaT,
                $prev
            );
            // If the data index is 0 this could be a bad packet.
            if ($data["DataIndex"] != 0) {
                $raw = $this->system->table(
                    "RawHistory",
                    array(
                        "id" => $this->device->id(),
                        "Date" => $time,
                        "packet" => array(
                            "Command" => $pkt->command(),
                            "Data"    => (string)$pkt->data(),
                            "Reply"   => (string)$pkt->reply(),
                            "To"      => $pkt->to(),
                        ),
                        "dataIndex" => $data["DataIndex"],
                        "command"   => $pkt->command(),
                    )
                );
                $raw->insertRow();
                $data["id"]     = $this->device->get("id");
                $data["Date"]   = $time;
                $data["TestID"] = $TestID;
                $data["deltaT"] = $deltaT;
                $this->device->load($this->device->id());
                $this->device->setParam("LastPollData", $data);
                $hist = $this->device->historyFactory($data);
                if ($hist->insertRow()) {
                    $this->device->setParam("LastHistory", $time);
                    $this->device->setLocalParam("LastHistory", $time);
                }
                $this->device->setParam("LastPoll", $time);
                $this->device->setParam("LastContact", $time);
                $this->device->setParam("PollFail", 0);
                $this->device->setParam("ContactFail", 0);
                $this->device->store();
                return $hist;
            }
        } else {
            $this->device->load($this->device->id());
            $fail = $this->device->getParam("PollFail");
            $this->device->setParam("PollFail", $fail+1);
            $this->device->store();
        }
        return false;
    }
    /**
    * Writes the firmware into this device
    *
    * If it is not given a firmware, it finds one to load
    *
    * @param object $firmware The data to write
    * @param bool   $loadData Load the data or not
    *
    * @return success or failure of the packet sending
    */
    public function loadFirmware($firmware = null, $loadData = true)
    {
        $ret = true;
        if (!is_object($firmware)) {
            $firmware = $this->device->system()->table("Firmware");
            if (!$this->device->get("bootloader")) {
                $firmware->set("FWPartNum", $this->device->get("FWPartNum"));
            } else {
                $firmware->set("FWPartNum", "0039-38-01-C");
            }
            $firmware->set("HWPartNum", $this->device->get("HWPartNum"));
            $firmware->set("RelStatus", \HUGnet\db\tables\Firmware::DEV);
            $ret = $firmware->getLatest();
        }
        if ($ret && is_int($this->programLock(true))) {
            if ($this->device->network()->loadFirmware($firmware, $loadData)) {
                $ret = true;
            }
        }
        $this->programLock(false);
        return $ret;
    }
    /**
    * Uploads config to the device
    *
    * @return string The left over string
    */
    public function loadConfig()
    {
        if (is_bool($this->programLock(true))) {
            return false;
        }
        $ret = $this->device->network()->loadConfig();
        $this->programLock(false);
        return $ret;
    }
    /**
    * Checks the record to see if something needs to be done about it.
    *
    * @return null
    */
    public function checkRecord()
    {
        $this->driver->checkRecord();
    }
    /**
    * Gets the config and saves it
    *
    * @param string $url The url to post to
    *
    * @return string The left over string
    */
    public function post($url = null)
    {
        if (!is_string($url) || (strlen($url) == 0)) {
            $master = $this->device->system()->get("master");
            $url = $master["url"];
        }
        $device  = $this->device->fixture()->export(true);
        if (is_string($device)) {
            $device = json_decode($device, true);
        }
        return \HUGnet\Util::postData(
            $url,
            array(
                "uuid"    => urlencode($this->device->system()->get("uuid")),
                "id"      => sprintf("%06X", $this->device->get("id")),
                "action"  => "import",
                "task"    => "device",
                "data"    => $device,
            )
        );
    }
    /**
    * Gets the config and saves it
    *
    * @param string $url The url to post to
    *
    * @return string The left over string
    */
    public function sync($url = null)
    {
        if (!is_string($url) || (strlen($url) == 0)) {
            $partner = $this->device->system()->get("partner");
            $url = $partner["url"];
        }
        $device  = $this->device->fixture()->export(true);
        if (is_string($device)) {
            $device = json_decode($device, true);
        }
        return \HUGnet\Util::postData(
            $url,
            array(
                "uuid"    => urlencode($this->device->system()->get("uuid")),
                "id"      => sprintf("%06X", $this->device->get("id")),
                "action"  => "sync",
                "task"    => "device",
                "data"    => $device,
            )
        );
    }
    /**
    * Pulls the record from the given URL
    *
    * @param string $url The url to post to
    *
    * @return string The left over string
    */
    public function pull($url)
    {
        if (!is_string($url) || (strlen($url) == 0)) {
            return false;
        }
        $ret = \HUGnet\Util::postData(
            $url,
            array(
                "uuid"    => urlencode($this->device->system()->get("uuid")),
                "id"      => sprintf("%06X", $this->device->get("id")),
                "action"  => "export",
                "task"    => "device",
                "format"  => "inline",
            )
        );
        $fixture = $this->device->fixture();
        $fixture->import($ret);
        $fixture->mergeDevice();
        return $this->device->load($this->device->id());
        
    }
    /**
    * This calculates the averages
    *
    * It will return once for each average that it calculates.  The average will be
    * stored in the instance this is called from.  If this is fed history table
    * then it will calculate 15 minute averages.
    *
    * @param object &$data   This is the data to use to calculate the average
    *                        This is not used here, but it is required to
    *                        match the main implementation.
    * @param string $avgType The type of average to do
    *
    * @return null
    */
    public function &calcAverage(&$data, $avgType)
    {
        if (!is_object($this->average)) {
            include_once dirname(__FILE__)."/Average.php";
            $this->average = Average::factory($this->system, $this->device);
        }
        return $this->average->get($data, $avgType);
    }

}


?>
