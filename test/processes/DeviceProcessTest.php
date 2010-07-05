<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../processes/DeviceProcess.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Processes
 * @package    HUGnetLibTest
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
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
            "sockets" => array(
                array(
                    "dummy" => true,
                ),
            ),
            "script_gateway" => 1,
        );
        $data = array(
            "PluginDir" => realpath(
                dirname(__FILE__)."/../files/plugins/"
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
            "HWPartNum"  => "0039-26-02-P",
            "FWPartNum"  => "0039-26-02-P",
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
        $o = new DeviceProcess(array(), $this->device);
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
        $o = new DeviceProcess($preload, $this->device);
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
    * data provider for testMain
    *
    * @return array
    */
    public static function dataMain()
    {
        return array(
            array(
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
                    "PluginDir" => realpath(dirname(__FILE__)."/../files/plugins"),
                    "PluginType" => "deviceProcess",
                ),
                true,
                array(
                    hexdec("123456") => 1,
                    hexdec("654321") => null,
                    hexdec("234567") => 1
                ),
                array("TestDeviceProcessPlugin", "TestDeviceProcessPlugin2"),
            ),
            array(
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
                    "PluginDir" => realpath(dirname(__FILE__)."/../files/plugins"),
                    "PluginType" => "deviceProcess",
                ),
                false,
                array(
                    hexdec("123456") => null,
                    hexdec("654321") => null,
                    hexdec("234567") => null
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
    * @param bool   $loop    Whether to loop or not
    * @param string $expect  The expected return
    * @param array  $plugins The plugins to expect
    *
    * @return null
    *
    * @dataProvider dataMain
    */
    public function testMain($devs, $preload, $loop, $expect, $plugins)
    {
        $d = new DeviceContainer();
        foreach ((array)$devs as $load) {
            $d->clearData();
            $d->fromArray($load);
            $d->insertRow(true);
        }
        $o = new DeviceProcess($preload, $this->device);
        $o->loop = $loop;
        $o->main();
        foreach ((array)$devs as $load) {
            $d->clearData();
            $d->getRow($load["id"]);
            $info = &$d->params->DriverInfo;
            foreach ($plugins as $p) {
                $this->assertSame(
                    $expect[$d->id], $info[$p], "$p didn't run enough"
                );
            }
        }
        $plug = $this->readAttribute($o, "active");
        foreach (array_keys((array)$plug) as $k) {
            // If the return type is int then array_search found the item
            $this->assertType(
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
                    "PluginDir" => realpath(dirname(__FILE__)."/../files/plugins"),
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
        $p = new PacketContainer($pkt);
        $o = new DeviceProcess($preload, $this->device);
        $o->packetConsumer($p);
        $this->assertAttributeSame($expect, "data", $p);
    }
}

?>
