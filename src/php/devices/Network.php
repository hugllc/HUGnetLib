<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Network
{
    /**
    * This is the system object
    */
    private $_system = null;
    /**
    * This is the driver object
    */
    private $_driver = null;
    /**
    * This is the table object
    */
    private $_device = null;
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
    private function __construct(&$system, &$device, &$driver)
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
        $this->_system = &$system;
        $this->_driver  = &$driver;
        $this->_device  = &$device;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_system);
        unset($this->_driver);
        unset($this->_device);
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
        $object = new Network($network, $device, $driver);
        return $object;
    }
    /**
    * Pings the device in question
    *
    * @param bool   $find     Whether to use FindPing or not
    * @param string $data     The data to use in the ping
    * @param string $callback The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config   The network config to use for the packet
    *
    * @return success or failure of the packet sending
    */
    public function ping(
        $find = false, $data = null, $callback = null, $config = array()
    ) {
        $command = "PING";
        if ($find) {
            $command = "FINDPING";
        }
        if (empty($data)) {
            $data = sprintf("%06X", mt_rand(0, 0xFFFFFF));
        }
        return $this->_sendPkt($command, $callback, $config, $data);
    }
    /**
    * Polls the device in question
    *
    * @param mixed  $pkt      The command to send the packet with
    * @param string $callback The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config   The network config to use for the packet
    * @param mixed  $data     Array|String of data to send out
    *
    * @return success or failure of the packet sending
    */
    public function send(
        $pkt, $callback = null, $config = array(), $data = null
    ) {
        return $this->_sendPkt($pkt, $callback, $config, $data);
    }
    /**
    * Polls the device in question
    *
    * @param string $callback The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config   The network config to use for the packet
    *
    * @return success or failure of the packet sending
    */
    public function poll($callback = null, $config = array())
    {
        $command = "SENSORREAD";
        if (method_exists($this->_driver, "poll")) {
            $command = $this->_driver->poll();
        }
        return $this->_sendPkt($command, $callback, $config);
    }
    /**
    * Gets the configuration for the device in question
    *
    * @param string $callback The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config   The network config to use for the packet
    *
    * @return success or failure of the packet sending
    */
    public function config($callback = null, $config = array())
    {
        $command = "CONFIG";
        if (method_exists($this->_driver, "config")) {
            $command = $this->_driver->config();
        }
        return $this->_sendPkt($command, $callback, $config);
    }
    /**
    * Gets the configuration for the device in question
    *
    * @param int    $sensor   The sensor to read info on
    * @param string $callback The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config   The network config to use for the packet
    *
    * @return success or failure of the packet sending
    */
    public function sensorConfig($sensor, $callback = null, $config = array())
    {
        $command = array(
            array(
                "Command" => "SENSORCONFIG",
                "Data" => sprintf("%02X", ($sensor & 0xFF)),
            ),
        );
        if (method_exists($this->_driver, "sensorConfig")) {
            $command = $this->_driver->sensorConfig($sensor);
        }
        return $this->_sendPkt($command, $callback, $config);
    }
    /**
    * Gets the configuration for the device in question
    *
    * @param int    $sensor       The sensor to read info on
    * @param string $sensorConfig The string to set the sensor config to
    * @param string $callback     The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config       The network config to use for the packet
    *
    * @return success or failure of the packet sending
    */
    public function setInputTable(
        $sensor, $sensorConfig, $callback = null, $config = array()
    ) {
        if (!is_string($sensorConfig) || (strlen($sensorConfig) == 0)) {
            return false;
        }
        $command = array(
            array(
                "Command" => "SETINPUTTABLE",
                "Data" => sprintf("%02X", ($sensor & 0xFF)).$sensorConfig,
            ),
        );
        $reply = $this->_sendPkt($command, $callback, $config);
        $data = substr($command[0]["Data"], 2);
        if (is_object($reply) && is_string($reply->Reply())) {
            if (strtoupper($reply->reply()) === strtoupper($data)) {
                return true;
            }
        }
        return false;
    }
    /**
    * Gets the configuration for the device in question
    *
    * @param int    $sensor       The sensor to read info on
    * @param string $sensorConfig The string to set the sensor config to
    * @param string $callback     The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config       The network config to use for the packet
    *
    * @return success or failure of the packet sending
    */
    public function setOutputTable(
        $sensor, $sensorConfig, $callback = null, $config = array()
    ) {
        if (!is_string($sensorConfig) || (strlen($sensorConfig) == 0)) {
            return false;
        }
        $command = array(
            array(
                "Command" => "SETOUTPUTTABLE",
                "Data" => sprintf("%02X", ($sensor & 0xFF)).$sensorConfig,
            ),
        );
        $reply = $this->_sendPkt($command, $callback, $config);
        $data = substr($command[0]["Data"], 2);
        if (is_object($reply) && is_string($reply->Reply())) {
            if (strtoupper($reply->reply()) === strtoupper($data)) {
                return true;
            }
        }
        return false;
    }
    /**
    * Gets the configuration for the device in question
    *
    * @param int    $sensor       The sensor to read info on
    * @param string $sensorConfig The string to set the sensor config to
    * @param string $callback     The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config       The network config to use for the packet
    *
    * @return success or failure of the packet sending
    */
    public function setProcessTable(
        $sensor, $sensorConfig, $callback = null, $config = array()
    ) {
        if (!is_string($sensorConfig) || (strlen($sensorConfig) == 0)) {
            return false;
        }
        $command = array(
            array(
                "Command" => "SETPROCESSTABLE",
                "Data" => sprintf("%02X", ($sensor & 0xFF)).$sensorConfig,
            ),
        );
        $reply = $this->_sendPkt($command, $callback, $config);
        $data = substr($command[0]["Data"], 2);
        if (is_object($reply) && is_string($reply->Reply())) {
            if (strtoupper($reply->reply()) === strtoupper($data)) {
                return true;
            }
        }
        return false;
    }
    /**
    * Gets the application CRC for the device in question.
    *
    * This only works on devices that have loadable firmware, and only when they are
    * running the bootloader.
    *
    * @param array $config The network config to use for the packet
    *
    * @return The CRC as a string, or false on failure
    */
    public function getCRC($config = array())
    {
        $reply = $this->_sendPkt("GETCRC", null, (array)$config);
        if (is_object($reply) && is_string($reply->Reply())) {
            return $reply->Reply();
        }
        return false;
    }
    /**
    * Sets the application CRC for the device in question.
    *
    * This only works on devices that have loadable firmware, and only when they are
    * running the bootloader.
    *
    * @param array $config The network config to use for the packet
    *
    * @return The CRC as a string, or false on failure
    */
    public function setCRC($config = array())
    {
        $reply = $this->_sendPkt("SETCRC", null, (array)$config);
        if (is_object($reply) && is_string($reply->Reply())) {
            return $reply->Reply();
        }
        return false;
    }
    /**
    * Sets the application CRC for the device in question.
    *
    * This only works on devices that have loadable firmware, and only when they are
    * running the bootloader.
    *
    * @param array $config The network config to use for the packet
    *
    * @return success or failure of the packet sending
    */
    public function runBootloader($config = array())
    {
        $reply = $this->_sendPkt("BOOTLOADER", null, $config);
        if (is_object($reply)) {
            if (is_string($reply->Reply())) {
                return true;
            }
        }
        return false;
    }
    /**
    * Sets the application CRC for the device in question.
    *
    * This only works on devices that have loadable firmware, and only when they are
    * running the bootloader.
    *
    * @param array $config The network config to use for the packet
    *
    * @return success or failure of the packet sending
    */
    public function runApplication($config = array())
    {
        $reply = $this->_sendPkt("BOOT", null, $config);
        if (is_object($reply)) {
            if (is_string($reply->Reply())) {
                return true;
            }
        }
        return false;
    }
    /**
    * Writes data to the flash
    *
    * @param int    $address  The address to write to
    * @param string $data     The data to write
    * @param string $callback The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config   The network config to use for the packet
    *
    * @return success or failure of the write
    */
    public function writeFlash($address, $data, $callback = null, $config = array())
    {
        return $this->_writeMem($address, $data, "WRITE_FLASH", $callback, $config);
    }
    /**
    * Writes data to the E2
    *
    * This function does nothing if there is no e2 to write.
    *
    * @param int    $address  The address to write to
    * @param string $data     The data to write
    * @param string $callback The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config   The network config to use for the packet
    *
    * @return success or failure of the packet sending
    */
    public function writeE2($address, $data, $callback = null, $config = array())
    {
        return $this->_writeMem($address, $data, "WRITE_E2", $callback, $config);
    }
    /**
    * Writes a data buffer to the Flash
    *
    * @param string $data      The data to write
    * @param int    $address   The address to start the write at
    * @param string $chunkSize The size of the chunks to send.  MUST BE LESS THAN 255
    * @param array  $empty     The 'empty' value of the memory
    *
    * @return success or failure of the packet sending
    */
    public function writeFlashBuffer(
        $data, $address = 0, $chunkSize = 128, $empty = "FF"
    ) {
        return $this->_writeMemBuffer(
            $data, "WRITE_FLASH", $chunkSize, "program", $address, $empty
        );
    }
    /**
    * Writes a data buffer to the Flash
    *
    * @param string $data      The data to write
    * @param int    $address   The address to start the write at
    * @param string $chunkSize The size of the chunks to send.  MUST BE LESS THAN 255
    * @param array  $empty     The 'empty' value of the memory
    *
    * @return success or failure of the packet sending
    */
    public function writeE2Buffer(
        $data, $address = 0, $chunkSize = 128, $empty = "FF"
    ) {
        return $this->_writeMemBuffer(
            $data, "WRITE_E2", $chunkSize, "config", $address, $empty
        );
    }
    /**
    * Writes a data buffer to the Flash
    *
    * @param string $callback The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config   The network config to use for the packet
    *
    * @return success or failure of the packet sending
    */
    public function loadConfig($callback = null, $config = array())
    {
        if (!$this->_device->get("setConfig")) {
            return true;
        }
        $part = $this->_device->get("FWPartNum");
        if (substr($part, 0, 7) === "0039-20") {
            /* This device doesn't have loadable sensors */
            return true;
        }
        $ret = $this->writeE2(0, $this->_device->encode(false), $callback, $config);
        if (!$ret) {
            $this->_system->out("config fail", 1);
            return false;
        }
        $this->_system->out("config success", 1);
        $input = (int)$this->_device->get("InputTables");
        for ($i = 0; $i < $input; $i++) {
            $ret = $this->setInputTable(
                $i,
                $this->_device->input($i)->encode(),
                $callback,
                $config
            );
            if (!$ret) {
                $this->_system->out("inputTable $i fail", 1);
                return false;
            }
            $this->_system->out("inputTable $i success", 1);
        }
        $output = (int)$this->_device->get("OutputTables");
        for ($i = 0; $i < $output; $i++) {
            $ret = $this->setOutputTable(
                $i,
                $this->_device->output($i)->encode(),
                $callback,
                $config
            );
            if (!$ret) {
                $this->_system->out("outputTable $i fail", 1);
                return false;
            }
            $this->_system->out("outputTable $i success", 1);
        }
        $process = (int)$this->_device->get("ProcessTables");
        for ($i = 0; $i < $process; $i++) {
            $ret = $this->setProcessTable(
                $i,
                $this->_device->process($i)->encode(),
                $callback,
                $config
            );
            if (!$ret) {
                $this->_system->out("processTable $i fail", 1);
                return false;
            }
            $this->_system->out("processTable $i success", 1);
        }
        /* This reboots the board */
        $this->_system->out("rebooting", 1);
        $this->reboot();
        return true;

    }
    /**
    * Causes the endpoint to reboot
    *
    * @return null
    */
    public function reboot()
    {
        $this->_sendPkt(
            "SETCONFIG", null, array('tries' => 1, "block" => false, "find" => false)
        );
    }
    /**
    * Writes a data buffer to the Flash
    *
    * @param object &$firmware The data to write
    * @param bool   $loadData  Load the data or not
    *
    * @return success or failure of the packet sending
    */
    public function loadFirmware(&$firmware, $loadData = true)
    {
        $part = $firmware->get("HWPartNum");
        if (empty($part)) {
            $this->_system->out("check 1/2 fail", 1);
            return false;
        }
        $this->_system->out("check 1/2 success", 1);
        /* This verifies that we are in the right place */
        if (substr($this->_device->get("HWPartNum"), 0, strlen($part)) !== $part) {
            $this->_system->out("check 2/2 fail", 1);
            return false;
        }
        $this->_system->out("check 2/2 success", 1);
        if (!$this->runBootloader()) {
            $this->_system->out("bootloader 1/1 fail", 1);
            return false;
        }
        $this->_system->out("bootloader 1/1 success", 1);
        $pkt = $this->config();
        if (is_object($pkt)
            && strlen($pkt->reply())
            && ($this->_device->decode($pkt->reply()))
        ) {
            $this->_system->out("config 1/1 success", 1);
        } else {
            $this->_system->out("config 1/1 fail", 1);
            return false;
        }
        $code = $this->writeFlashBuffer(
            $firmware->getCode()
        );
        if (!$code) {
            return false;
        }
        /* Data is not required */
        if ((strlen($firmware->getData()) > 0) && $loadData) {
            $part = $firmware->get("FWPartNum");
            $data = $firmware->getData();
            $start = 0;
            if (substr($part, 0, 7) === "0039-20") {
                /* This is a band-aid for the older firmware */
                $data = substr($data, 20);
                $start = 10;
            }
            $data = $this->writeE2Buffer($data, $start);
            if (!$data) {
                return false;
            }
        }
        $crc = $this->setCRC();
        if ($crc === false) {
            $this->_system->out("crc 1/1 fail", 1);
            return false;
        }
        $this->_system->out("crc 1/1 success", 1);

        if (!$this->runApplication()) {
            $this->_system->out("run 1/1 fail", 1);
            return false;
        }
        $this->_system->out("run 1/1 success", 1);

        $pkt = $this->config();
        if (is_object($pkt)
            && strlen($pkt->reply())
            && ($this->_device->decode($pkt->reply()))
        ) {
            $this->_system->out("config 1/1 success", 1);
        } else {
            $this->_system->out("config 1/1 fail", 1);
            return false;
        }
        return $this->loadConfig();
    }
    /**
    * Polls the device in question
    *
    * @param mixed  $pkt      The command to send the packet with
    * @param string $callback The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config   The network config to use for the packet
    * @param mixed  $data     Array|String of data to send out
    *
    * @return success or failure of the packet sending
    */
    private function _sendPkt(
        $pkt, $callback = null, $config = array(), $data = null
    ) {
        if (!is_array($pkt)) {
            $pkt = array(
                "To" => $this->_device->get("id"),
                "Command" => $pkt,
            );
            if (!is_null($data) && (is_array($data) || is_string($data))) {
                $pkt["Data"] = $data;
            }
        } else {
            foreach (array_keys($pkt) as $key) {
                $pkt[$key]["To"] = $this->_device->get("id");
            }
        }
        if (!is_array($config)) {
            $config = array();
        }
        if (!is_int($config["timeout"])) {
            $config["timeout"] = $this->_device->get("packetTimeout");
        }
        $ret = $this->_system->network()->send($pkt, $callback, (array)$config);
        return $ret;
    }
    /**
    * Writes data to the memory
    *
    * @param int    $address  The address to write to
    * @param string $data     The data to write
    * @param mixed  $command  The command to send out
    * @param string $callback The name of the function to call when the packet
    *                   arrives.  If this is not callable, it will block until the
    *                   packet arrives.
    * @param array  $config   The network config to use for the packet
    *
    * @return success or failure of the write
    */
    private function _writeMem(
        $address, $data, $command, $callback = null, $config = array()
    ) {
        if (is_string($data)) {
            $write = sprintf("%04X", $address);
            $write .= $data;
            $reply = $this->_sendPkt($command, $callback, $config, $write);
            if (is_object($reply)) {
                if (strtoupper($reply->Reply()) === strtoupper($data)) {
                    return true;
                }
            }
        }
        return false;
    }
    /**
    * Writes the Memory into the device specified
    *
    * @param string $buffer    The data to write
    * @param mixed  $command   The command to send out
    * @param int    $chunkSize The size of the chunks to send.
    * @param string $memName   The name of the memory (for printing purposes)
    * @param int    $start     The location to start writing
    * @param string $empty     The value of an 'empty' location
    *
    * @return bool True on success, False on failure
    */
    private function _writeMemBuffer(
        $buffer,
        $command,
        $chunkSize = 128,
        $memName = "memory",
        $start = 0,
        $empty = "FF"
    ) {
        if (!is_string($buffer) || ($chunkSize <= 0)) {
            return false;
        }
        if (strlen($buffer) > 0) {
            $buffer = str_split($buffer, $chunkSize*2);
            $pages = count($buffer);
            foreach ($buffer as $page => $data) {
                $data = str_pad($data, $chunkSize*2, $empty);
                $addr = $start + ($page * $chunkSize);
                $ret = $this->_writeMem(
                    $addr,
                    $data,
                    $command,
                    null,
                    array("find" => false)
                );
                if ($ret === false) {
                    $this->_system->out(
                        "$memName ".($page + 1)."/$pages failed",
                        1
                    );
                    return false;
                }
                $this->_system->out(
                    "$memName ".($page + 1)."/$pages success",
                    1
                );
            }
        }
        return true;
    }
}


?>
