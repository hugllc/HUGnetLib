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
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** Get our classes */
require_once CODE_BASE.'plugins/devices/E00392100Device.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyDeviceContainer.php';
/** This is a required class */
require_once TEST_BASE.'plugins/devices/DevicePluginTestBase.php';
/** This is a required class */
require_once CODE_BASE.'containers/PacketContainer.php';

/**
 * Test class for the controller baord firmware
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class E00392100DeviceTest extends DevicePluginTestBase
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
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->pdo = &$this->config->servers->getPDO();
        $this->socket = &$this->config->sockets->getSocket("default");
        $this->d = new DeviceContainer();
        $this->o = new E00392100Device($this->d);
        $this->firmware = new FirmwareTable();
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
    * Data provider for testRegisterPlugin
    *
    * @return array
    */
    public static function dataRegisterPlugin()
    {
        return array(
            array("E00392100Device"),
        );
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataToSetupString()
    {
        return array(
            array(
                array(
                ),
                "",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload This is the attribute to set
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataToSetupString
    */
    public function testToSetupString($preload, $expect)
    {
        $this->d->DriverInfo = $preload;
        $this->d->GatewayKey = (int)$preload["GatewayKey"];
        $ret = $this->o->toSetupString();
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataFromSetupString()
    {
        return array(
            array(
                "0102020202020202027070707070707070",
                array(
                    "PacketTimeout" => 0,
                    "TimeConstant" => 1,
                    "PhysicalSensors" => 6,
                    "VirtualSensors" => 4,
                ),
                array(
                    'Sensors' => 10,
                    'ActiveSensors' => 6,
                    'PhysicalSensors' => 6,
                    'VirtualSensors' => 4,
                    0 => array(
                        'id' => 64,
                        'type' => 'Controller',
                        'location' => 'HUGnet 1 Voltage',
                        'extra' =>
                        array (
                        0 => 180,
                        1 => 27,
                        ),
                        'bound' => true,
                    ),
                    1 => array(
                        'id' => 80,
                        'type' => 'Controller',
                        'location' => 'HUGnet 1 Current',
                        'extra' =>
                        array (
                        0 => 0.5,
                        1 => 7,
                        ),
                        'bound' => true,
                    ),
                    2 => array(
                        'id' => 2,
                        'type' => 'BCTherm2322640',
                        'location' => 'HUGnet 1 FET Temperature',
                        'extra' =>
                        array (
                        0 => 100,
                        1 => 10,
                        ),
                        'bound' => true,
                    ),
                    3 => array(
                        'id' => 64,
                        'type' => 'Controller',
                        'location' => 'HUGnet 2 Voltage',
                        'extra' =>
                        array (
                        0 => 180,
                        1 => 27,
                        ),
                        'bound' => true,
                    ),
                    4 => array(
                        'id' => 80,
                        'type' => 'Controller',
                        'location' => 'HUGnet 2 Current',
                        'extra' =>
                        array (
                        0 => 0.5,
                        1 => 7,
                        ),
                        'bound' => true,
                    ),
                    5 => array(
                        'id' => 2,
                        'type' => 'BCTherm2322640',
                        'location' => 'HUGnet 2 FET Temperature',
                        'extra' =>
                        array (
                        0 => 100,
                        1 => 10,
                        ),
                        'bound' => true,
                    ),
                    6 => array(
                        'id' => 254,
                        'type' => 'Placeholder',
                        'location' => null,
                    ),
                    7 => array(
                        'id' => 254,
                        'type' => 'Placeholder',
                        'location' => null,
                    ),
                    8 => array(
                        'id' => 254,
                        'type' => 'Placeholder',
                        'location' => null,
                    ),
                    9 => array(
                        'id' => 254,
                        'type' => 'Placeholder',
                        'location' => null,
                    ),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload This is the attribute to set
    * @param string $expect  The expected return
    * @param array  $sensors The expected sensor array
    *
    * @return null
    *
    * @dataProvider dataFromSetupString
    */
    public function testFromSetupString($preload, $expect, $sensors)
    {
        $this->o->fromSetupString($preload);
        $this->assertSame($expect, $this->d->DriverInfo, "DriverInfo Wrong");
        $this->assertSame($sensors, $this->d->sensors->toArray(), "Sensors Wrong");
    }
    /**
    * test the loadable routine.
    *
    * @return null
    */
    public function testController()
    {
        $this->assertTrue($this->o->controller());
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
                    array(
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "Version" => "0.1.0",
                        "Target" => "atmega16",
                        "RelStatus" => FirmwareTable::RELEASE,
                    ),
                ),
                array(
                    "id"        => 0x25,
                    "DeviceID"  => "000025",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "FWVersion" => "0.0.9",
                    "Driver" => "e00392100",
                ),
                array(
                ),
                "000000002500392101410039200643000002FFFFFF50",
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200143000009FFFFFF50",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200143000009FFFFFF50",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200643000002FFFFFF50",
                    )
                ),
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                )
                .(string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => E00392100Device::COMMAND_RUNBOOTLOADER,
                        "Data" => "",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                ),
                null,
                array(),
            ),
            array(  // #1
                array(
                    array(
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "Version" => "0.0.8",
                        "Target" => "atmega16",
                        "RelStatus" => FirmwareTable::RELEASE,
                    ),
                ),
                array(
                    "id"        => 0x25,
                    "DeviceID"  => "000025",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "FWVersion" => "0.0.9",
                    "Driver" => "e00392100",
                ),
                array(
                    array(
                        "id"        => 0x82,
                        "DeviceID"  => "000082",
                    ),
                    array(
                        "id"        => 0x73,
                        "DeviceID"  => "000073",
                    ),
                    array(
                        "id"        => 0x71,
                        "DeviceID"  => "000071",
                    ),
                ),
                "000000002500392101410039200143000009FFFFFF50",
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200143000009FFFFFF50",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000082".str_repeat("000000", 29)
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000073".str_repeat("000000", 29),
                    )
                ),
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => E00392100Device::COMMAND_READDOWNSTREAM,
                        "Data" => "00",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => E00392100Device::COMMAND_READDOWNSTREAM,
                        "Data" => "01",
                    )
                ),
                true,
                array(
                    array(
                        "id" => (string)0x71,
                        "DeviceID" => "000071",
                        "DeviceName" => "",
                        "HWPartNum" => "",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup"  => "000000000000000000000000000000000000"
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
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIi"
                            ."O3M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMi"
                            ."O2k6MDtzOjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJW"
                            ."aXJ0dWFsU2Vuc29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7"
                            ."YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                    array(
                        "id" => (string)0x73,
                        "DeviceID" => "000073",
                        "DeviceName" => "",
                        "HWPartNum" => "",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup"  => "000000000000000000000000000000000000"
                            ."FFFFFF00",
                        "Active" => "1",
                        "GatewayKey" => "0",
                        "ControllerKey" => "37",
                        "ControllerIndex" => "1",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIi"
                            ."O3M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMi"
                            ."O2k6MDtzOjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJW"
                            ."aXJ0dWFsU2Vuc29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7"
                            ."YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                    array(
                        "id" => (string)0x82,
                        "DeviceID" => "000082",
                        "DeviceName" => "",
                        "HWPartNum" => "",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup"  => "000000000000000000000000000000000000"
                            ."FFFFFF00",
                        "Active" => "1",
                        "GatewayKey" => "0",
                        "ControllerKey" => "37",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIi"
                            ."O3M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMi"
                            ."O2k6MDtzOjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJW"
                            ."aXJ0dWFsU2Vuc29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7"
                            ."YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                ),
            ),
            // Board rebooted
           array(  // #2
                array(
                    array(
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "Version" => "0.0.8",
                        "Target" => "atmega16",
                        "RelStatus" => FirmwareTable::RELEASE,
                    ),
                ),
                array(
                    "id"        => 0x25,
                    "DeviceID"  => "000025",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "FWVersion" => "0.0.9",
                    "Driver" => "e00392100",
                ),
                array(
                    array(
                        "id"        => 0x82,
                        "DeviceID"  => "000082",
                    ),
                    array(
                        "id"        => 0x73,
                        "DeviceID"  => "000073",
                    ),
                    array(
                        "id"        => 0x71,
                        "DeviceID"  => "000071",
                    ),
                ),
                "000000002500392101410039200143000009FFFFFF50",
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200643000009FFFFFF50",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200643000009FFFFFF50",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200143000009FFFFFF50",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000082".str_repeat("000000", 29)
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000073".str_repeat("000000", 29),
                    )
                ),
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000025",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_RUNAPPLICATION,
                        "Data" => "",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => E00392100Device::COMMAND_READDOWNSTREAM,
                        "Data" => "00",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => E00392100Device::COMMAND_READDOWNSTREAM,
                        "Data" => "01",
                    )
                ),
                true,
                array(
                    array(
                        "id" => (string)0x71,
                        "DeviceID" => "000071",
                        "DeviceName" => "",
                        "HWPartNum" => "",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup"  => "000000000000000000000000000000000000"
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
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIi"
                            ."O3M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMi"
                            ."O2k6MDtzOjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJW"
                            ."aXJ0dWFsU2Vuc29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7"
                            ."YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                    array(
                        "id" => (string)0x73,
                        "DeviceID" => "000073",
                        "DeviceName" => "",
                        "HWPartNum" => "",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup"  => "000000000000000000000000000000000000"
                            ."FFFFFF00",
                        "Active" => "1",
                        "GatewayKey" => "0",
                        "ControllerKey" => "37",
                        "ControllerIndex" => "1",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIi"
                            ."O3M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMi"
                            ."O2k6MDtzOjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJW"
                            ."aXJ0dWFsU2Vuc29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7"
                            ."YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                    array(
                        "id" => (string)0x82,
                        "DeviceID" => "000082",
                        "DeviceName" => "",
                        "HWPartNum" => "",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup"  => "000000000000000000000000000000000000"
                            ."FFFFFF00",
                        "Active" => "1",
                        "GatewayKey" => "0",
                        "ControllerKey" => "37",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "YTo2OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIi"
                            ."O3M6NzoiU2Vuc29ycyI7aTowO3M6MTM6IkFjdGl2ZVNlbnNvcnMi"
                            ."O2k6MDtzOjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6MDtzOjE0OiJW"
                            ."aXJ0dWFsU2Vuc29ycyI7aTowO3M6MTI6ImZvcmNlU2Vuc29ycyI7"
                            ."YjowO30=",
                        "params" => "YTowOnt9",
                    ),
                ),
            ),
            array( // #3
                array(
                    array(
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "Version" => "0.0.8",
                        "Target" => "atmega16",
                    ),
                ),
                array(
                    "id"        => 0x25,
                    "DeviceID"  => "000025",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "FWVersion" => "0.0.9",
                    "Driver" => "e00392101",
                ),
                array(
                ),
                "000000002500392101410039200643000009FFFFFF50",
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200643000009FFFFFF50",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200643000009FFFFFF50",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200643000009FFFFFF50",
                    )
                ),
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000025",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_RUNAPPLICATION,
                        "Data" => "",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                ),
                null,
                array(),
            ),
            array( // #4
                array(),
                array(
                    "id"        => 0x25,
                    "DeviceID"  => "000025",
                ),
                array(
                ),
                "000000000000000000000000000000000000FFFFFF00",
                "",
                "5A5A5A5C00002500002000595A5A5A5C0000250000200059"
                    ."5A5A5A0300002500002000065A5A5A5C0000250000200059",
                false,
                array(),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $firmware  Firmware to load into the database
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
        $firmware, $device, $devs, $string, $read, $write, $expect, $expectDev
    ) {
        foreach ((array)$firmware as $firm) {
            $this->firmware->fromAny($firm);
            $this->firmware->insertRow();
        }
        foreach ((array)$device as $key => $val) {
            $this->d->$key = $val;
        }
        $dev = new DeviceContainer();
        foreach ((array)$devs as $key => $val) {
            $dev->fromAny($val);
            $dev->insertRow();
        }
        $this->socket->readString = $read;
        $ret = $this->o->readSetup();
        $this->assertSame($write, $this->socket->writeString, "Wrong writeString");
        $this->assertSame($string, $this->d->RawSetup, "Wrong Setup String");
        $this->assertSame($expect, $ret, "Wrong return value");
        $stmt = $this->pdo->query("SELECT * FROM `devices`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame($expectDev, $rows);

    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecodeData()
    {
        return array(
            array(
                "7FFF00F00FFF3FFF7EFF7EFF3FF00FFF00",
                PacketContainer::COMMAND_GETDATA,
                0,
                array(
                    "DataIndex" => 127,
                    "timeConstant" => 1,
                    "deltaT" => 0,
                    0 => array(
                        "value" => 9.4428,
                        "units" => "V",
                        "unitType" => "Voltage",
                        "dataType" => "raw",
                    ),
                    1 => array(
                        "value" => 5.6,
                        "units" => "mA",
                        "unitType" => "Current",
                        "dataType" => "raw",
                    ),
                    2 => array(
                        "value" => 34.5929,
                        "units" => "&#176;C",
                        "unitType" => "Temperature",
                        "dataType" => "raw",
                    ),
                    3 => array(
                        "value" => 9.4428,
                        "units" => "V",
                        "unitType" => "Voltage",
                        "dataType" => "raw",
                    ),
                    4 => array(
                        "value" => 5.6,
                        "units" => "mA",
                        "unitType" => "Current",
                        "dataType" => "raw",
                    ),
                    5 => array(
                        "value" => 34.5929,
                        "units" => "&#176;C",
                        "unitType" => "Temperature",
                        "dataType" => "raw",
                    ),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $data    The raw data
    * @param string $command The command that was used to get the data
    * @param float  $deltaT  The time difference between this packet and the next
    * @param array  $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataDecodeData
    */
    public function testDecodeData($data, $command, $deltaT, $expect)
    {
        $this->readAttribute($this->o, "myDriver");
        $ret = $this->o->decodeData($data, $command, $deltaT);
        $this->assertEquals($expect, $ret, "Arrays are not the same", 0.1);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataToOutput()
    {
        return array(
            array(
                array(
                ),
                "",
                null,
                array(
                    'PacketTimeout' => 0,
                    'TimeConstant' => 1,
                    'PhysicalSensors' => 6,
                    'VirtualSensors' => 4,
                    'CPU' => 'Atmel Mega16',
                    'SensorConfig' => 'Fixed',
                    'bootloader' => "No",
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $preload The data to preload into the class
    * @param string $setup   The setup string to use
    * @param array  $cols    The columns to use
    * @param int    $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataToOutput
    */
    public function testToOutput($preload, $setup, $cols, $expect)
    {
        $this->d->DriverInfo = array_merge(
            (array)$this->d->DriverInfo, (array)$preload
        );
        $this->o->fromSetupString($setup);
        $ret = $this->o->toOutput($cols);
        $this->assertSame(
            $expect,
            $ret
        );
    }

}

?>
