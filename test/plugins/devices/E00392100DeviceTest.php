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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage ControllerBoard
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** Get our classes */
require_once dirname(__FILE__).'/../../../plugins/devices/E00392100Device.php';
require_once dirname(__FILE__).'/../../stubs/DummyDeviceContainer.php';
require_once dirname(__FILE__).'/DevicePluginTestBase.php';
require_once dirname(__FILE__).'/../../../containers/PacketContainer.php';

/**
 * Test class for the controller baord firmware
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage ControllerBoard
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
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
                    "NumSensors" => 6,
                ),
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
    * @dataProvider dataFromSetupString
    */
    public function testFromSetupString($preload, $expect)
    {
        $this->o->fromSetupString($preload);
        $this->assertSame($expect, $this->d->DriverInfo);
    }
    /**
    * data provider for testReadSetup, testReadConfig
    *
    * @return array
    */
    public static function dataReadSetup()
    {
        return array(
            array(
                array(
                    array(
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "Version" => "0.1.0",
                        "Target" => "atmega16",
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
                "000000002500392101410039200143000009FFFFFF50",
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000020",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000000002500392101410039200143000009FFFFFF50",
                )).
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000020",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000000002500392101410039200143000009FFFFFF50",
                )),
                (string)new PacketContainer(array(
                    "To" => "000025",
                    "From" => "000020",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                ))
                .(string)new PacketContainer(array(
                    "To" => "000025",
                    "From" => "000020",
                    "Command" => E00392100Device::COMMAND_RUNBOOTLOADER,
                    "Data" => "",
                )),
                false,
                array(),
            ),
            array(
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
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000020",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000000002500392101410039200143000009FFFFFF50",
                )).
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000020",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000082".str_repeat("000000", 29)
                        ."000073".str_repeat("000000", 29),
                )),
                (string)new PacketContainer(array(
                    "To" => "000025",
                    "From" => "000020",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                )).
                (string)new PacketContainer(array(
                    "To" => "000025",
                    "From" => "000020",
                    "Command" => E00392100Device::COMMAND_READDOWNSTREAM,
                    "Data" => "",
                )),
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
                            ."FFFFFF50",
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
                        "BoredomThreshold" => "80",
                        "LastConfig" => "0",
                        "LastPoll" => "0",
                        "LastHistory" => "0",
                        "LastAnalysis" => "0",
                        "MinAverage" => "15MIN",
                        "sensors" => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO"
                            ."3M6NzoiU2Vuc29ycyI7aTowO30=",
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
                            ."FFFFFF50",
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
                        "BoredomThreshold" => "80",
                        "LastConfig" => "0",
                        "LastPoll" => "0",
                        "LastHistory" => "0",
                        "LastAnalysis" => "0",
                        "MinAverage" => "15MIN",
                        "sensors" => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO"
                            ."3M6NzoiU2Vuc29ycyI7aTowO30=",
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
                            ."FFFFFF50",
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
                        "BoredomThreshold" => "80",
                        "LastConfig" => "0",
                        "LastPoll" => "0",
                        "LastHistory" => "0",
                        "LastAnalysis" => "0",
                        "MinAverage" => "15MIN",
                        "sensors" => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO"
                            ."3M6NzoiU2Vuc29ycyI7aTowO30=",
                        "params" => "YTowOnt9",
                    ),
                ),
            ),
            // Board rebooted
           array(
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
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000020",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000000002500392101410039200643000009FFFFFF50",
                )).
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000020",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000000002500392101410039200643000009FFFFFF50",
                )).
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000020",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000000002500392101410039200143000009FFFFFF50",
                )).
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000020",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000082".str_repeat("000000", 29)
                        ."000073".str_repeat("000000", 29),
                )),
                (string)new PacketContainer(array(
                    "To" => "000025",
                    "From" => "000020",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                )).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000025",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_RUNAPPLICATION,
                        "Data" => "",
                    )
                ).
                (string)new PacketContainer(array(
                    "To" => "000025",
                    "From" => "000020",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                )).
                (string)new PacketContainer(array(
                    "To" => "000025",
                    "From" => "000020",
                    "Command" => E00392100Device::COMMAND_READDOWNSTREAM,
                    "Data" => "",
                )),
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
                            ."FFFFFF50",
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
                        "BoredomThreshold" => "80",
                        "LastConfig" => "0",
                        "LastPoll" => "0",
                        "LastHistory" => "0",
                        "LastAnalysis" => "0",
                        "MinAverage" => "15MIN",
                        "sensors" => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO"
                            ."3M6NzoiU2Vuc29ycyI7aTowO30=",
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
                            ."FFFFFF50",
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
                        "BoredomThreshold" => "80",
                        "LastConfig" => "0",
                        "LastPoll" => "0",
                        "LastHistory" => "0",
                        "LastAnalysis" => "0",
                        "MinAverage" => "15MIN",
                        "sensors" => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO"
                            ."3M6NzoiU2Vuc29ycyI7aTowO30=",
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
                            ."FFFFFF50",
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
                        "BoredomThreshold" => "80",
                        "LastConfig" => "0",
                        "LastPoll" => "0",
                        "LastHistory" => "0",
                        "LastAnalysis" => "0",
                        "MinAverage" => "15MIN",
                        "sensors" => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIiO"
                            ."3M6NzoiU2Vuc29ycyI7aTowO30=",
                        "params" => "YTowOnt9",
                    ),
                ),
            ),
            array(
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
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000020",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000000002500392101410039200643000009FFFFFF50",
                )).
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000020",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000000002500392101410039200643000009FFFFFF50",
                )).
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000020",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000000002500392101410039200643000009FFFFFF50",
                )),
                (string)new PacketContainer(array(
                    "To" => "000025",
                    "From" => "000020",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                )).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000025",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_RUNAPPLICATION,
                        "Data" => "",
                    )
                ).
                (string)new PacketContainer(array(
                    "To" => "000025",
                    "From" => "000020",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                )),
                false,
                array(),
            ),
            array(
                array(),
                array(
                    "id"        => 0x25,
                    "DeviceID"  => "000025",
                ),
                array(
                ),
                "000000000000000000000000000000000000FFFFFF50",
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


}

?>
