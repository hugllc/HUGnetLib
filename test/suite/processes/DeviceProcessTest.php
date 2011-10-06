<?php
/**
 * Tests the filter class
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
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteProcesses
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** This is a required class */
require_once CODE_BASE.'processes/DeviceProcess.php';
/** This is a required class */
require_once CODE_BASE.'plugins/devices/E00392606Device.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteProcesses
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceProcessTest extends PHPUnit_Framework_TestCase
{

    /**
    * Sets up the fixture, for example, open a network connection.
    * This method is called before a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function setUp()
    {
        $config = array(
            "servers" => array(
                array(
                    "driver" => "sqlite",
                    "file" => ":memory:",
                    "group" => "default",
                ),
                array(
                    "driver" => "sqlite",
                    "file" => ":memory:",
                    "group" => "volatile",
                ),
            ),
            "sockets" => array(
                array(
                    "dummy" => true,
                ),
            ),
            "script_gateway" => 1,
        );
        $data = array(
            "PluginDir" => realpath(
                TEST_CONFIG_BASE."files/plugins/"
            ),
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->config->sockets->forceDeviceID("000019");
        $this->socket = &$this->config->sockets->getSocket();
        $this->pdo = &$this->config->servers->getPDO();
        $this->device = array(
            "id"         => 0x000019,
            "DeviceID"   => "000019",
            "HWPartNum"  => "0039-26-06-P",
            "FWPartNum"  => "0039-26-06-P",
        );
        $this->o = new DeviceProcess($data, $this->device);
        $this->d = $this->readAttribute($this->o, "myDevice");
    }

    /**
    * Tears down the fixture, for example, close a network connection.
    * This method is called after a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function tearDown()
    {
        $this->o = null;
        $this->config = null;
        // Trap the exit signal and exit gracefully
        if (function_exists("pcntl_signal")) {
            pcntl_signal(SIGINT, SIG_DFL);
        }

    }
    /**
    * Tests for exceptions
    *
    * @expectedException Exception
    *
    * @return null
    */
    public function testConstructTableExec()
    {
        $config = array(

        );
        $this->config->forceConfig($config);
        $obj = new DeviceProcess(array(), $this->device);
    }
    /**
    * data provider for testConstructor
    *
    * @return array
    */
    public static function dataConstructor()
    {
        return array(
            array(
                array(),
                array(
                    "group" => "default",
                    "GatewayKey" => 1,
                    "PluginDir"       => "./plugins",
                    "PluginExtension" => "php",
                    "PluginType"      => "deviceProcess",
                ),
            ),
            array(
                array(
                    "group" => "test",
                    "GatewayKey" => 3,
                    "PluginDir"       => "here",
                    "PluginExtension" => "there",
                    "PluginType"      => "analyzeThis",
                ),
                array(
                    "group" => "test",
                    "GatewayKey" => 3,
                    "PluginDir"       => "here",
                    "PluginExtension" => "there",
                    "PluginType"      => "analyzeThis",
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The value to preload
    * @param array $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $expect)
    {
        $obj = new DeviceProcess($preload, $this->device);
        $ret = $this->readAttribute($obj, "data");
        $this->assertSame($expect, $ret);
        // Check the configuration is set correctly
        $config = $this->readAttribute($obj, "myConfig");
        $this->assertSame("ConfigContainer", get_class($config));
        // Check the configuration is set correctly
        $device = $this->readAttribute($obj, "device");
        $this->assertSame("DeviceContainer", get_class($device));
    }

    /**
    * data provider for testMain
    *
    * @return array
    */
    public static function dataMain()
    {
        return array(
            array( // #0
                array(
                    array(
                        "id" => hexdec("123456"),
                        "DeviceID" => "123456",
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                        "PollInterval" => 10,
                    ),
                    array(
                        "id" => hexdec("654321"),
                        "DeviceID" => "654321",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 2,
                        "PollInterval" => 10,
                    ),
                    array(
                        "id" => hexdec("234567"),
                        "DeviceID" => "234567",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                        "PollInterval" => 10,
                    ),
                ),
                array(
                    "PluginDir" => realpath(TEST_CONFIG_BASE."files/plugins"),
                    "PluginType" => "deviceProcess",
                ),
                array(
                ),
                "",
                array(
                    "id"         => 0x000019,
                    "DeviceID"   => "000019",
                    "HWPartNum"  => "0039-26-06-P",
                    "FWPartNum"  => "0039-26-06-P",
                    "ControllerKey" => 19,
                ),
                true,
                array(
                    hexdec("123456") => array(
                        "TestDeviceProcessPlugin" => 1,
                        "TestDeviceProcessPlugin2" => 1,
                    ),
                    hexdec("654321") => array(
                        "TestDeviceProcessPlugin" => null,
                        "TestDeviceProcessPlugin2" => null,
                    ),
                    hexdec("234567") => array(
                        "TestDeviceProcessPlugin" => 1,
                        "TestDeviceProcessPlugin2" => 1,
                    ),
                ),
                array("TestDeviceProcessPlugin", "TestDeviceProcessPlugin2"),
            ),
            // Checks to see what happens when main return false
            array( // #1
                array(
                    array(
                        "id" => hexdec("BADBAD"),
                        "DeviceID" => "BADBAD",
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                        "PollInterval" => 10,
                    ),
                    array(
                        "id" => hexdec("654321"),
                        "DeviceID" => "654321",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 2,
                        "PollInterval" => 10,
                    ),
                    array(
                        "id" => hexdec("234567"),
                        "DeviceID" => "234567",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                        "PollInterval" => 10,
                    ),
                ),
                array(
                    "PluginDir" => realpath(TEST_CONFIG_BASE."files/plugins"),
                    "PluginType" => "deviceProcess",
                ),
                array(
                ),
                "",
                array(
                    "id"         => 0x000019,
                    "DeviceID"   => "000019",
                    "HWPartNum"  => "0039-26-06-P",
                    "FWPartNum"  => "0039-26-06-P",
                    "ControllerKey" => 19,
                ),
                true,
                array(
                    hexdec("BADBAD") => array(
                        "TestDeviceProcessPlugin" => null,
                        "TestDeviceProcessPlugin2" => 1,
                    ),
                    hexdec("654321") => array(
                        "TestDeviceProcessPlugin" => null,
                        "TestDeviceProcessPlugin2" => null,
                    ),
                    hexdec("234567") => array(
                        "TestDeviceProcessPlugin" => 1,
                        "TestDeviceProcessPlugin2" => 1,
                    ),
                ),
                array("TestDeviceProcessPlugin", "TestDeviceProcessPlugin2"),
            ),
            // All of the devices locked
            array( // #2
                array(
                    array(
                        "id" => hexdec("123456"),
                        "DeviceID" => "123456",
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                        "PollInterval" => 10,
                    ),
                    array(
                        "id" => hexdec("654321"),
                        "DeviceID" => "654321",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 2,
                        "PollInterval" => 10,
                    ),
                    array(
                        "id" => hexdec("234567"),
                        "DeviceID" => "234567",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                        "PollInterval" => 10,
                    ),
                ),
                array(
                    "PluginDir" => realpath(TEST_CONFIG_BASE."files/plugins"),
                    "PluginType" => "deviceProcess",
                ),
                array(
                ),
                "",
                array(
                    "id"         => 0x000019,
                    "DeviceID"   => "000019",
                    "HWPartNum"  => "0039-26-06-P",
                    "FWPartNum"  => "0039-26-06-P",
                ),
                false,
                array(
                    hexdec("123456") => array(
                        "TestDeviceProcessPlugin" => null,
                        "TestDeviceProcessPlugin2" => null,
                    ),
                    hexdec("654321") => array(
                        "TestDeviceProcessPlugin" => null,
                        "TestDeviceProcessPlugin2" => null,
                    ),
                    hexdec("234567") => array(
                        "TestDeviceProcessPlugin" => null,
                        "TestDeviceProcessPlugin2" => null,
                    ),
                ),
                array("TestDeviceProcessPlugin", "TestDeviceProcessPlugin2"),
            ),
            // Already locked
            array( // #3
                array(
                    array(
                        "id" => hexdec("123456"),
                        "DeviceID" => "123456",
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                        "PollInterval" => 10,
                    ),
                    array(
                        "id" => hexdec("654321"),
                        "DeviceID" => "654321",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 2,
                        "PollInterval" => 10,
                    ),
                    array(
                        "id" => hexdec("234567"),
                        "DeviceID" => "234567",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                        "PollInterval" => 10,
                    ),
                ),
                array(
                    "PluginDir" => realpath(TEST_CONFIG_BASE."files/plugins"),
                    "PluginType" => "deviceProcess",
                ),
                array(
                    array(
                        "id" => 0xFEAAAA,
                        "type" => E00392606Device::LOCKTYPE,
                        "lockData" => "123456",
                        "expiration" => 1000000000000, // Way in the future
                    ),
                    array(
                        "id" => 0xFEAAAA,
                        "type" => E00392606Device::LOCKTYPE,
                        "lockData" => "234567",
                        "expiration" => 1000000000000, // Way in the future
                    ),
                ),
                "",
                array(
                    "id"         => 0x000019,
                    "DeviceID"   => "000019",
                    "HWPartNum"  => "0039-26-06-P",
                    "FWPartNum"  => "0039-26-06-P",
                    "params" => array(
                    ),
                ),
                true,
                array(
                    hexdec("123456") => array(
                        "TestDeviceProcessPlugin" => null,
                        "TestDeviceProcessPlugin2" => null,
                    ),
                    hexdec("654321") => array(
                        "TestDeviceProcessPlugin" => null,
                        "TestDeviceProcessPlugin2" => null,
                    ),
                    hexdec("234567") => array(
                        "TestDeviceProcessPlugin" => null,
                        "TestDeviceProcessPlugin2" => null,
                    ),
                ),
                array("TestDeviceProcessPlugin", "TestDeviceProcessPlugin2"),
            ),
            // Already locked Remote
            array( // #4
                array(
                    array(
                        "id" => hexdec("123456"),
                        "DeviceID" => "123456",
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                        "PollInterval" => 10,
                    ),
                    array(
                        "id" => hexdec("654321"),
                        "DeviceID" => "654321",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 2,
                        "PollInterval" => 10,
                    ),
                    array(
                        "id" => hexdec("234567"),
                        "DeviceID" => "234567",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                        "PollInterval" => 10,
                    ),
                    array(
                        "id" => 0xFEAAAA,
                        "DeviceID" => "FEAAAA",
                        "HWPartNum" => "0039-26-06-P",
                        "FWPartNum" => "0039-26-06-P",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                        "PollInterval" => 10,
                    ),
                ),
                array(
                    "PluginDir" => realpath(TEST_CONFIG_BASE."files/plugins"),
                    "PluginType" => "deviceProcess",
                ),
                array(
                ),
                (string)new PacketContainer(
                    array(
                        "To" => "000019",
                        "From" => "FEAAAA",
                        "Command" => "01",
                        "Data" => "FEAAAA0100",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "To" => "000019",
                        "From" => "FEAAAA",
                        "Command" => "01",
                        "Data" => "FEAAAA0100",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "To" => "000019",
                        "From" => "FEAAAA",
                        "Command" => "01",
                        "Data" => "FEAAAA0100",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "To" => "000019",
                        "From" => "FEAAAA",
                        "Command" => "01",
                        "Data" => "FEAAAA0100",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "To" => "000019",
                        "From" => "FEAAAA",
                        "Command" => "01",
                        "Data" => "FEAAAA0100",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "To" => "000019",
                        "From" => "FEAAAA",
                        "Command" => "01",
                        "Data" => "FEAAAA0100",
                    )
                ),
                array(
                    "id"         => 0x000019,
                    "DeviceID"   => "000019",
                    "HWPartNum"  => "0039-26-06-P",
                    "FWPartNum"  => "0039-26-06-P",
                    "params" => array(
                    ),
                ),
                true,
                array(
                    hexdec("123456") => array(
                        "TestDeviceProcessPlugin" => null,
                        "TestDeviceProcessPlugin2" => null,
                    ),
                    hexdec("654321") => array(
                        "TestDeviceProcessPlugin" => null,
                        "TestDeviceProcessPlugin2" => null,
                    ),
                    hexdec("234567") => array(
                        "TestDeviceProcessPlugin" => null,
                        "TestDeviceProcessPlugin2" => null,
                    ),
                ),
                array("TestDeviceProcessPlugin", "TestDeviceProcessPlugin2"),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $devs    The devices to load into the database
    * @param array  $preload The data to preload into the devices table
    * @param array  $locks   The locks that are present
    * @param string $read    The read stuff for the socke
    * @param array  $dev     Device to load into the class
    * @param bool   $loop    Whether to loop or not
    * @param string $expect  The expected return
    * @param array  $plugins The plugins to expect
    *
    * @return null
    *
    * @dataProvider dataMain
    */
    public function testMain(
        $devs, $preload, $locks, $read, $dev, $loop, $expect, $plugins
    ) {
        $obj = new DeviceProcess($preload, $dev);
        $obj->loop = $loop;
        $this->socket->readString = $read;
        $device = new DeviceContainer();
        foreach ((array)$devs as $load) {
            $device->clearData();
            $device->fromArray($load);
            $device->insertRow(true);
        }
        $lock = new LockTable();
        $lock->purgeAll();
        foreach ((array)$locks as $val) {
            $lock->clearData();
            $lock->fromAny($val);
            $lock->insertRow(true);
        }
        $obj->main();
        foreach ((array)$devs as $load) {
            $device->clearData();
            $device->getRow($load["id"]);
            $info = &$device->params->DriverInfo;
            foreach ($plugins as $p) {
                $this->assertSame(
                    $expect[$device->id][$p],
                    $info[$p],
                    "$p didn't run enough on ".$device->DeviceID
                );
            }
        }
        $plug = $this->readAttribute($obj, "active");
        foreach (array_keys((array)$plug) as $k) {
            // If the return type is int then array_search found the item
            $this->assertInternalType(
                "int",
                array_search(get_class($plug[$k]), $plugins),
                "Plugin class ".get_class($plug[$k])." not found"
            );
        }
    }

    /**
    * data provider for testMain
    *
    * @return array
    */
    public static function dataPacketConsumer()
    {
        return array(
            array(
                array(
                    "Date" => 1275683593,
                ),
                array(
                    "PluginDir" => realpath(TEST_CONFIG_BASE."files/plugins"),
                    "PluginType" => "deviceProcess",
                ),
                array(
                    "To" => "000000",
                    "From" => "000000",
                    "Date" => 1275683593,
                    "Command" => "00",
                    "Type" => "UNKNOWN",
                    "Length" => 0,
                    "Time" => 0.0,
                    "Data" => "",
                    "Reply" => null,
                    "Checksum" => "00",
                    "Timeout" => 5,
                    "Retries" => 3,
                    "GetReply" => true,
                    "group" => "TestDeviceProcessPlugin2",
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $pkt     The packet data to send
    * @param array  $preload The data to preload into the devices table
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataPacketConsumer
    */
    public function testPacketConsumer($pkt, $preload, $expect)
    {
        $packet = new PacketContainer($pkt);
        $obj = new DeviceProcess($preload, $this->device);
        $obj->packetConsumer($packet);
        //$this->assertAttributeSame($expect, "data", $p);
        foreach ($expect as $key => $value) {
            $this->assertSame($value, $packet->$key, "$key is wrong");
        }
    }
}

?>
