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
 * @category   Processes
 * @package    HUGnetLibTest
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../processes/DeviceConfig.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Processes
 * @package    HUGnetLibTest
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceConfigTest extends PHPUnit_Framework_TestCase
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
            "sockets" => array(
                array(
                    "dummy" => true,
                ),
            ),
            "script_gateway" => 1,
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
        $this->o = new DeviceConfig(array(), $this->device);
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
        $o = new DeviceConfig(array(), $this->device);
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
                ),
            ),
           array(
                array(
                    "group" => "test",
                    "GatewayKey" => 3,
                ),
                array(
                    "group" => "test",
                    "GatewayKey" => 3,
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
        $o = new DeviceConfig($preload, $this->device);
        $ret = $this->readAttribute($o, "data");
        $this->assertSame($expect, $ret);
        // Check the configuration is set correctly
        $config = $this->readAttribute($o, "myConfig");
        $this->assertSame("ConfigContainer", get_class($config));
        // Check the configuration is set correctly
        $device = $this->readAttribute($o, "device");
        $this->assertSame("DeviceContainer", get_class($device));
    }

    /**
    * data provider for testConfig
    *
    * @return array
    */
    public static function dataConfig()
    {
        return array(
            // #0
            array(
                array(
                    array(
                        "id" => 0x123456,
                        "DeviceID" => "123456",
                        "GatewayKey" => 1,
                    ),
                    array(
                        "id" => 0x654321,
                        "DeviceID" => "654321",
                        "GatewayKey" => 2,
                    ),
                    array(
                        "id" => 0x000019,
                        "DeviceID" => "000019",
                        "GatewayKey" => 1,
                    ),
                ),
                true,
                false,
                (string)new PacketContainer(array(
                    "From" => "123456",
                    "To" => "000019",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000012345600391101410039201343000009FFFFFF50",
                )),
                (string)new PacketContainer(array(
                    "To" => "123456",
                    "From" => "000019",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                )),
            ),
            // #1
            array(
                array(
                ),
                true,
                false,
                "",
                "",
            ),
            // #2
            array(
                array(
                    array(
                        "id" => 0x123456,
                        "DeviceID" => "123456",
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                    ),
                    array(
                        "id" => 0x654321,
                        "DeviceID" => "654321",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 2,
                    ),
                    array(
                        "id" => 0x234567,
                        "DeviceID" => "234567",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                    ),
                ),
                true,
                true,
                (string)new PacketContainer(array(
                    "From" => "123456",
                    "To" => "000019",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000012345600392101410039200143000009FFFFFF50",
                )).
                (string)new PacketContainer(array(
                    "From" => "123456",
                    "To" => "000019",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => str_repeat("000000", 30),
                )).
                (string)new PacketContainer(array(
                    "From" => "123456",
                    "To" => "000019",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => str_repeat("000000", 30),
                )),
                (string)new PacketContainer(array(
                    "To" => "123456",
                    "From" => "000019",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                )).
                (string)new PacketContainer(array(
                    "To" => "123456",
                    "From" => "000019",
                    "Command" => E00392100Device::COMMAND_READDOWNSTREAM,
                    "Data" => "00",
                )).
                (string)new PacketContainer(array(
                    "To" => "123456",
                    "From" => "000019",
                    "Command" => E00392100Device::COMMAND_READDOWNSTREAM,
                    "Data" => "01",
                )),
            ),
            // #3
            array(
                array(
                    array(
                        "id" => 0x123456,
                        "DeviceID" => "123456",
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                        "params" => array(
                            "DriverInfo" => array(
                                "ConfigFail" => 109,
                            ),
                        ),
                    ),
                    array(
                        "id" => 0x654321,
                        "DeviceID" => "654321",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 2,
                        "params" => array(
                            "DriverInfo" => array(
                                "ConfigFail" => 109,
                            ),
                        ),
                    ),
                    array(
                        "id" => 0x234567,
                        "DeviceID" => "234567",
                        "HWPartNum" => "0039-28-01-A",
                        "FWPartNum" => "0039-20-13-C",
                        "FWVersion" => "1.2.3",
                        "GatewayKey" => 1,
                        "params" => array(
                            "DriverInfo" => array(
                                "ConfigFail" => 109,
                            ),
                        ),
                    ),
                ),
                true,
                true,
                "",
                (string)new PacketContainer(array(
                    "To" => "123456",
                    "From" => "000019",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                )).
                (string)new PacketContainer(array(
                    "To" => "123456",
                    "From" => "000019",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                )).
                (string)new PacketContainer(array(
                    "To" => "123456",
                    "From" => "000019",
                    "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                    "Data" => "",
                )).
                (string)new PacketContainer(array(
                    "To" => "123456",
                    "From" => "000019",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                )),
            ),
            // #4
            // Should exit before it does anything
            array(
                array(
                    array(
                        "id" => 0x123456,
                        "DeviceID" => "123456",
                        "GatewayKey" => 1,
                    ),
                    array(
                        "id" => 0x654321,
                        "DeviceID" => "654321",
                        "GatewayKey" => 2,
                    ),
                    array(
                        "id" => hexdec("00019"),
                        "DeviceID" => "000019",
                        "GatewayKey" => 1,
                    ),
                ),
                false,
                false,
                "",
                "",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload  The data to preload into the devices table
    * @param bool   $loop     What to set the loop variable to
    * @param bool   $loadable Do only devices with loadable firmware
    * @param string $read     The read string for the socket
    * @param string $expect   The expected return
    *
    * @return null
    *
    * @dataProvider dataConfig
    */
    public function testConfig($preload, $loop, $loadable, $read, $expect)
    {
        $d = new DeviceContainer();
        foreach ((array)$preload as $load) {
            $d->clearData();
            $d->fromArray($load);
            $d->insertRow(true);
        }
        $this->socket->readString = $read;
        $this->o->loop = $loop;
        $this->o->config($loadable);
        $this->assertSame($expect, $this->socket->writeString);
    }
        /**
    * data provider for testPacketConsumer
    *
    * @return array
    */
    public static function dataPacketConsumer()
    {
        return array(
            array(
                array(
                    array(
                        "DeviceName" => "Hello",
                        "id" => hexdec("123456"),
                        "DeviceID" => "123456",
                        "GatewayKey" => 3,
                        "HWPartNum" => "0039-21-20-C",
                        "FWPartNum" => "0039-08-20-C",
                        "FWVersion" => "1.2.3",
                    ),
                ),
                array(
                    "To" => "000000",
                    "From" => "123456",
                    "Command" => "5C",
                    "group" => "default",
                ),
                array(
                    array(
                        "id"                => (string)hexdec("123456"),
                        "DeviceID"          => "123456",
                        "DeviceName"        => "Hello",
                        "HWPartNum" => "0039-21-20-C",
                        "FWPartNum" => "0039-08-20-C",
                        "FWVersion" => "1.2.3",
                        "RawSetup"=> "000012345600392120430039082043010203FFFFFF00",
                        "Active"            => "1",
                        "GatewayKey"        => "1",
                        "ControllerKey"     => "0",
                        "ControllerIndex"   => "0",
                        "DeviceLocation"    => "",
                        "DeviceJob"         => "",
                        "Driver"            => "eDEFAULT",
                        "PollInterval"      => "0",
                        "ActiveSensors"     => "0",
                        "DeviceGroup"       => "FFFFFF",
                        "sensors"           => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvb"
                            ."iI7czowOiIiO3M6NzoiU2Vuc29ycyI7aTowO30=",
                        "params"            => "YToyOntzOjEwOiJEcml2ZXJJbmZvIjthOj"
                            ."M6e3M6MTA6Ikxhc3RDb25maWciO2k6MDtzOjEwOiJDb25maWdGYW"
                            ."lsIjtpOjA7czoxMzoiTGFzdENvbmZpZ1RyeSI7aTowO31zOjExOi"
                            ."JQcm9jZXNzSW5mbyI7YToxOntzOjExOiJ1bnNvbGljaXRlZCI7YT"
                            ."oxOntzOjI6IjVDIjtpOjE7fX19",
                    ),
                ),
                "",
            ),
            array(
                array(
                ),
                array(
                    "To" => "000000",
                    "From" => "123456",
                    "Command" => "5C",
                    "group" => "default",
                ),
                array(
                    array(
                        "id"                => "1193046",
                        "DeviceID"          => "123456",
                        "DeviceName"        => "",
                        "HWPartNum"         => "",
                        "FWPartNum"         => "",
                        "FWVersion"         => "",
                        "RawSetup"          => "",
                        "Active"            => "1",
                        "GatewayKey"        => "1",
                        "ControllerKey"     => "0",
                        "ControllerIndex"   => "0",
                        "DeviceLocation"    => "",
                        "DeviceJob"         => "",
                        "Driver"            => "eDEFAULT",
                        "PollInterval"      => "0",
                        "ActiveSensors"     => "0",
                        "DeviceGroup"       => "FFFFFF",
                        "sensors"           => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvb"
                            ."iI7czowOiIiO3M6NzoiU2Vuc29ycyI7aTowO30=",
                        "params"            => "YTowOnt9",
                    ),
                ),
                "",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The data to preload into the devices table
    * @param string $pkt     The packet string to use
    * @param string $expect  The expected return
    * @param string $write   The packet string expected to be written
    *
    * @return null
    *
    * @dataProvider dataPacketConsumer
    */
    public function testPacketConsumer($preload, $pkt, $expect, $write)
    {
        $d = new DeviceContainer();
        foreach ((array)$preload as $load) {
            $d->clearData();
            $d->fromArray($load);
            $d->insertRow(true);
        }
        $p = new PacketContainer($pkt);
        $this->o->packetConsumer($p);
        $stmt = $this->pdo->query(
            "SELECT * FROM `devices` where id <> ".$this->d->id
        );
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows);
        $this->assertSame($write, $this->socket->writeString);

    }

}


?>
