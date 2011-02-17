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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../../plugins/devices/E00392606Device.php';
require_once dirname(__FILE__).'/../../stubs/DummyDeviceContainer.php';
require_once dirname(__FILE__).'/DevicePluginTestBase.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class E00392606DeviceTest extends DevicePluginTestBase
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
        $this->socket = &$this->config->sockets->getSocket("default");
        $this->pdo = &$this->config->servers->getPDO();
        $this->d = new DummyDeviceContainer();
        $this->d->DeviceID = "000019";
        $this->o = new E00392606Device($this->d);
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
        unset($this->o);
    }
    /**
    * test the loadable routine.
    *
    * @return null
    */
    public function testGateway()
    {
        $this->assertTrue($this->o->gateway());
    }

    /**
    * Data provider for testRegisterPlugin
    *
    * @return array
    */
    public static function dataRegisterPlugin()
    {
        return array(
            array("E00392606Device"),
        );
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
                ),
                array(
                    "id" => 25,
                    "DeviceID" => "000019",
                    "DeviceName" => "",
                    "HWPartNum" => "0039-26-06-P",
                    "FWPartNum" => "0039-26-06-P",
                    "FWVersion" => "0.7.0",
                    "DriverInfo" => array(
                        "Job" => 6,
                    ),
                    "RawSetup" => "",
                    "Active" => "1",
                    "GatewayKey" => "1",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "e00392606",
                    "PollInterval" => "0",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "FFFFFF",
                    "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO3M6"
                        ."NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMiO2k6MDtz"
                        ."OjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJWaXJ0dWFsU2Vu"
                        ."c29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7YjowO30=",
                    "params" => "YTowOnt9",
                ),
                array(
                    "To" => "000000",
                    "From" => "123456",
                    "Command" => "5C",
                    "group" => "default",
                ),
                array(
                    array(
                        "id" => "25",
                        "DeviceID" => "000019",
                        "DeviceName" => "Config Process",
                        "HWPartNum" => "0039-26-06-P",
                        "FWPartNum" => "0039-26-06-P",
                        "FWVersion" => "0.7.0",
                        "RawSetup" => "000000001900392606500039260650000700FFFFFF00"
                            ."0600010000000000000000000000000000000000000000000000"
                            ."000000000000000000000000",
                        "Active" => "1",
                        "GatewayKey" => "1",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "0.0.0.0",
                        "DeviceJob" => "Config",
                        "Driver" => "e00392606",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO3"
                        ."M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMiO2k6MDtz"
                        ."OjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJWaXJ0dWFsU2Vu"
                        ."c29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                ),
                "",
            ),
            array(
                array(
                ),
                array(
                    "id" => 25,
                    "DeviceID" => "000019",
                    "DeviceName" => "",
                    "HWPartNum" => "0039-26-06-P",
                    "FWPartNum" => "0039-26-06-P",
                    "FWVersion" => "0.7.0",
                    "DriverInfo" => array(
                        "Job" => 6,
                    ),
                    "RawSetup" => "",
                    "Active" => "1",
                    "GatewayKey" => "1",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "e00392606",
                    "PollInterval" => "0",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "FFFFFF",
                    "sensors" => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiI"
                        ."iO3M6NzoiU2Vuc29ycyI7aTowO30=",
                    "params" => "YTowOnt9",
                ),
                array(
                    "To" => "000019",
                    "From" => "123456",
                    "Command" => "5C",
                    "group" => "default",
                ),
                array(
                    array(
                        "id" => "25",
                        "DeviceID" => "000019",
                        "DeviceName" => "Config Process",
                        "HWPartNum" => "0039-26-06-P",
                        "FWPartNum" => "0039-26-06-P",
                        "FWVersion" => "0.7.0",
                        "RawSetup" => "000000001900392606500039260650000700FFFFFF00"
                            ."0600010000000000000000000000000000000000000000000000"
                            ."000000000000000000000000",
                        "Active" => "1",
                        "GatewayKey" => "1",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "0.0.0.0",
                        "DeviceJob" => "Config",
                        "Driver" => "e00392606",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO3"
                        ."M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMiO2k6MDtz"
                        ."OjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJWaXJ0dWFsU2Vu"
                        ."c29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                ),
                "5A5A5A011234560000193C000000001900392606500039260650000700FFFFFF00"
                ."06000100000000000000000000000000000000000000000000000000000000000"
                ."00000000000B2",
            ),
            array(
                array(
                ),
                array(
                    "id" => 25,
                    "DeviceID" => "000019",
                    "DeviceName" => "",
                    "HWPartNum" => "0039-26-06-P",
                    "FWPartNum" => "0039-26-06-P",
                    "FWVersion" => "0.7.0",
                    "DriverInfo" => array(
                        "Job" => 6,
                    ),
                    "RawSetup" => "",
                    "Active" => "1",
                    "GatewayKey" => "1",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "e00392606",
                    "PollInterval" => "0",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "FFFFFF",
                    "sensors" => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiI"
                        ."iO3M6NzoiU2Vuc29ycyI7aTowO30=",
                    "params" => "YTowOnt9",
                ),
                array(
                    "To" => "000019",
                    "From" => "123456",
                    "Command" => "03",
                    "Data" => "01020304",
                    "group" => "default",
                ),
                array(
                    array(
                        "id" => "25",
                        "DeviceID" => "000019",
                        "DeviceName" => "Config Process",
                        "HWPartNum" => "0039-26-06-P",
                        "FWPartNum" => "0039-26-06-P",
                        "FWVersion" => "0.7.0",
                        "RawSetup" => "000000001900392606500039260650000700FFFFFF00"
                            ."0600010000000000000000000000000000000000000000000000"
                            ."000000000000000000000000",
                        "Active" => "1",
                        "GatewayKey" => "1",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "0.0.0.0",
                        "DeviceJob" => "Config",
                        "Driver" => "e00392606",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO3"
                        ."M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMiO2k6MDtz"
                        ."OjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJWaXJ0dWFsU2Vu"
                        ."c29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                ),
                "5A5A5A01123456000019040102030468",
            ),
            array(
                array(
                ),
                array(
                    "id" => 25,
                    "DeviceID" => "000019",
                    "DeviceName" => "",
                    "HWPartNum" => "0039-26-06-P",
                    "FWPartNum" => "0039-26-06-P",
                    "FWVersion" => "0.7.0",
                    "DriverInfo" => array(
                        "Job" => 6,
                    ),
                    "RawSetup" => "",
                    "Active" => "1",
                    "GatewayKey" => "1",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "e00392606",
                    "PollInterval" => "0",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "FFFFFF",
                    "sensors" => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiI"
                        ."iO3M6NzoiU2Vuc29ycyI7aTowO30=",
                    "params" => "YTowOnt9",
                ),
                array(
                    "To" => "000019",
                    "From" => "123456",
                    "Command" => "02",
                    "Data" => "01020304",
                    "group" => "default",
                ),
                array(
                    array(
                        "id" => "25",
                        "DeviceID" => "000019",
                        "DeviceName" => "Config Process",
                        "HWPartNum" => "0039-26-06-P",
                        "FWPartNum" => "0039-26-06-P",
                        "FWVersion" => "0.7.0",
                        "RawSetup" => "000000001900392606500039260650000700FFFFFF00"
                            ."0600010000000000000000000000000000000000000000000000"
                            ."000000000000000000000000",
                        "Active" => "1",
                        "GatewayKey" => "1",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "0.0.0.0",
                        "DeviceJob" => "Config",
                        "Driver" => "e00392606",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO3"
                        ."M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMiO2k6MDtz"
                        ."OjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJWaXJ0dWFsU2Vu"
                        ."c29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                ),
                "5A5A5A01123456000019040102030468",
            ),
            array(
                array(
                    array(
                        "id" => "26",
                        "DeviceID" => "00001A",
                        "GatewayKey" => "1",
                    ),
                    array(
                        "id" => "27",
                        "DeviceID" => "00001B",
                        "GatewayKey" => "1",
                    ),
                ),
                array(
                    "id" => 25,
                    "DeviceID" => "000019",
                    "DeviceName" => "",
                    "HWPartNum" => "0039-26-06-P",
                    "FWPartNum" => "0039-26-06-P",
                    "FWVersion" => "0.7.0",
                    "DriverInfo" => array(
                        "Job" => 6,
                    ),
                    "RawSetup" => "",
                    "Active" => "1",
                    "GatewayKey" => "1",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "e00392606",
                    "PollInterval" => "0",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "FFFFFF",
                    "sensors" => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiI"
                        ."iO3M6NzoiU2Vuc29ycyI7aTowO30=",
                    "params" => "YTowOnt9",
                ),
                array(
                    "To" => "000019",
                    "From" => "123456",
                    "Command" => E00392606Device::COMMAND_READDOWNSTREAM,
                    "Data" => "01020304",
                    "group" => "default",
                ),
                array(
                    array(
                        "id" => "25",
                        "DeviceID" => "000019",
                        "DeviceName" => "Config Process",
                        "HWPartNum" => "0039-26-06-P",
                        "FWPartNum" => "0039-26-06-P",
                        "FWVersion" => "0.7.0",
                        "RawSetup" => "000000001900392606500039260650000700FFFFFF00"
                            ."0600010000000000000000000000000000000000000000000000"
                            ."000000000000000000000000",
                        "Active" => "1",
                        "GatewayKey" => "1",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "0.0.0.0",
                        "DeviceJob" => "Config",
                        "Driver" => "e00392606",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO3"
                        ."M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMiO2k6MDtz"
                        ."OjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJWaXJ0dWFsU2Vu"
                        ."c29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                    array(
                        "id" => "26",
                        "DeviceID" => "00001A",
                        "DeviceName" => "",
                        "HWPartNum" => "",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup" => "000000000000000000000000000000000000FFFFFF00",
                        "Active" => "1",
                        "GatewayKey" => "1",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO3"
                        ."M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMiO2k6MDtz"
                        ."OjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJWaXJ0dWFsU2Vu"
                        ."c29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                    array(
                        "id" => "27",
                        "DeviceID" => "00001B",
                        "DeviceName" => "",
                        "HWPartNum" => "",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup" => "000000000000000000000000000000000000FFFFFF00",
                        "Active" => "1",
                        "GatewayKey" => "1",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO3"
                        ."M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMiO2k6MDtz"
                        ."OjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJWaXJ0dWFsU2Vu"
                        ."c29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                ),
                "5A5A5A011234560000190900001900001A00001B79",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The data to preload into the devices table
    * @param array  $dev     The device array to use
    * @param string $pkt     The packet string to use
    * @param string $expect  The expected return
    * @param string $write   The packet string expected to be written
    *
    * @return null
    *
    * @dataProvider dataPacketConsumer
    */
    public function testPacketConsumer($preload, $dev, $pkt, $expect, $write)
    {
        $d = new DeviceContainer();
        foreach ((array)$preload as $load) {
            $d->fromArray($load);
            $d->insertRow(true);
        }
        $d->fromAny($dev);
        $d->insertRow(true);
        $p = new PacketContainer($pkt);
        $o = new E00392606Device($d);

        $o->packetConsumer($p);
        $stmt = $this->pdo->query("SELECT * FROM `devices`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows, "Devices Wrong");
        $this->assertSame($write, $this->socket->writeString);

    }
    /**
    * data provider for testReadSetup, testReadConfig
    *
    * @return array
    */
    public static function dataReadSetup()
    {
        return array(
            array(  // #0
                array(
                    "id"        => 0x25,
                    "DeviceID"  => "000025",
                    "HWPartNum" => "0039-26-06-P",
                    "FWPartNum" => "0039-26-06-P",
                    "FWVersion" => "0.7.0",
                    "Driver" => "e00392606",
                ),
                array(
                    array(
                        "id" => (string)0x71,
                        "DeviceID" => "000071",
                        "DeviceName" => "Hello",
                        "HWPartNum" => "0039-23-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "FWVersion" => "0.1.2",
                        "RawSetup"  => "000000007100392101410039200143000102"
                            ."FFFFFF00",
                        "Active" => "1",
                        "GatewayKey" => "0",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "",
                        "params" => "",
                    ),
                ),
                "000000002500392606500039260650000009FFFFFF50",
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000019",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000000002500392606500039260650000009FFFFFF50",
                )).
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000019",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000082000073000071"
                )),
                (string)new PacketContainer(array(
                    "To" => "000025",
                    "From" => "000019",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                )).
                (string)new PacketContainer(array(
                    "To" => "000025",
                    "From" => "000019",
                    "Command" => E00392606Device::COMMAND_READDOWNSTREAM,
                    "Data" => "",
                )),
                true,
                array(
                    array(
                        "id" => (string)0x71,
                        "DeviceID" => "000071",
                        "DeviceName" => "Hello",
                        "HWPartNum" => "0039-23-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "FWVersion" => "0.1.2",
                        "RawSetup"  => "000000007100392101410039200143000102"
                            ."FFFFFF00",
                        "Active" => "1",
                        "GatewayKey" => "0",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO3"
                        ."M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMiO2k6MDtz"
                        ."OjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJWaXJ0dWFsU2Vu"
                        ."c29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                    array(
                        "id" => (string)0x73,
                        "DeviceID" => "000073",
                        "DeviceName" => "",
                        "HWPartNum" => "",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup"  => "",
                        "Active" => "1",
                        "GatewayKey" => "0",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "",
                        "params" => "",
                    ),
                    array(
                        "id" => (string)0x82,
                        "DeviceID" => "000082",
                        "DeviceName" => "",
                        "HWPartNum" => "",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup"  => "",
                        "Active" => "1",
                        "GatewayKey" => "0",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "",
                        "params" => "",
                    ),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $device    Parameters to load into the device
    * @param string $devs      The Devices to load into the database
    * @param string $string    The string for the dummy device to return
    * @param string $read      The read string to put in
    * @param string $write     The write string expected
    * @param string $expect    The expected return
    * @param array  $expectDev The expected devices table
    *
    * @return null
    *
    * @dataProvider dataReadSetup
    */
    public function testReadSetup(
        $device, $devs, $string, $read, $write, $expect, $expectDev
    ) {
        $dev = new DeviceContainer();
        foreach ((array)$devs as $key => $val) {
            $dev->clearData();
            $dev->fromAny($val);
            $dev->insertRow();
        }
        $dev->clearData();
        $dev->fromAny($device);
        $this->socket->readString = $read;
        $o = new E00392606Device($dev);
        $ret = $o->readSetup();
        $this->assertSame($write, $this->socket->writeString, "Wrong writeString");
        $this->assertSame($string, $dev->RawSetup, "Wrong Setup String");
        $this->assertSame($expect, $ret, "Wrong return value");
        $stmt = $this->pdo->query("SELECT * FROM `devices`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame($expectDev, $rows);

    }

}

?>
