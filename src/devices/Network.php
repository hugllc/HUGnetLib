<?php
/**
 * Classes for dealing with devices
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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Network
{
    /**
    * This is the system object
    */
    private $_network = null;
    /**
    * This is the driver object
    */
    private $_driver = null;
    /**
    * This is the table object
    */
    private $_table = null;
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$network The network application object
    * @param object &$table   The device table object
    * @param object &$driver  The device driver object
    *
    * @return null
    */
    private function __construct(&$network, &$table, &$driver)
    {
        \HUGnet\System::exception(
            get_class($this)." needs to be passed a network object",
            "InvalidArgument",
            !is_object($network)
        );
        \HUGnet\System::exception(
            get_class($this)." needs to be passed a driver object",
            "InvalidArgument",
            !is_object($driver)
        );
        \HUGnet\System::exception(
            get_class($this)." needs to be passed a driver object",
            "InvalidArgument",
            !is_object($table)
        );
        $this->_network = &$network;
        $this->_driver  = &$driver;
        $this->_table   = &$table;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_driver);
        unset($this->_table);
    }
    /**
    * This function creates the system.
    *
    * @param mixed  &$network (object)The system object to use
    * @param string &$table   (object)The table to use
    * @param object &$driver  The device driver object
    *
    * @return null
    */
    public static function &factory(&$network, &$table, &$driver)
    {
        $object = new Network($network, $table, $driver);
        return $object;
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
            $data, "WRITE_FLASH", $chunkSize, "flash", $address, $empty
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
            $data, "WRITE_E2", $chunkSize, "EEPROM", $address, $empty
        );
    }
    /**
    * Writes a data buffer to the Flash
    *
    * @param \FirmwareTable &$firmware The data to write
    *
    * @return success or failure of the packet sending
    */
    public function loadFirmware(\FirmwareTable &$firmware)
    {

        \HUGnet\VPrint::out("Running the bootloader...  ", 1);
        if (!$this->runBootloader()) {
            return false;
        }
        \HUGnet\VPrint::out("Getting the bootloader configuration...  ", 1);
        $bootConfig = $this->config();
        if (!is_object($bootConfig) || is_null($bootConfig->Reply())) {
            return false;
        }
        \HUGnet\VPrint::out("Writing the code...\n", 1);
        $code = $this->writeFlashBuffer(
            $firmware->getCode()
        );
        if (!$code) {
            return false;
        }
        /* Data is not required */
        if (strlen($firmware->getData()) > 0) {
            \HUGnet\VPrint::out("Writing the data...\n", 1);
            $data = $this->writeE2Buffer(
                $firmware->getData(), 0
            );
            if (!$data) {
                return false;
            }
        }
        \HUGnet\VPrint::out("Setting the CRC...  ", 1);
        $crc = $this->setCRC();
        if ($crc === false) {
            return false;
        }

        \HUGnet\VPrint::out("Running the application...  ", 1);
        if (!$this->runApplication()) {
            return false;
        }
        return true;
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
                "To" => $this->_table->get("id"),
                "Command" => $pkt,
            );
            if (!is_null($data) && (is_array($data) || is_string($data))) {
                $pkt["Data"] = $data;
            }
        } else {
            foreach (array_keys($pkt) as $key) {
                $pkt[$key]["To"] = $this->_table->get("id");
            }
        }
        $ret = $this->_network->send($pkt, $callback, (array)$config);
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
            $devID = $this->_table->get("id");
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
                    \HUGnet\VPrint::out(
                        "Writing ".$memName." Page ".($page + 1)." of $pages in "
                        ."device ".sprintf("%06X", $devID)." Failed",
                        1
                    );
                    return false;
                }
                \HUGnet\VPrint::out(
                    "Writing ".$memName." Page ".($page + 1)." of $pages in "
                    ."device ".sprintf("%06X", $devID)." Succeeded",
                    1
                );
            }
        }
        return true;
    }
}


?>
