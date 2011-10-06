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
require_once CODE_BASE.'plugins/devices/E00392801Device.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyDeviceContainer.php';
/** This is a required class */
require_once TEST_BASE.'plugins/devices/DevicePluginTestBase.php';
/** This is a required class */
require_once CODE_BASE.'containers/PacketContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
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
class E00392801DeviceTest extends DevicePluginTestBase
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
        $this->socket = &$this->config->sockets->getSocket("default");
        $this->d = new DummyDeviceContainer();
        $this->o = new E00392801Device($this->d);
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
            array("E00392801Device"),
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
                "010202020202020202707070707070707001020304050607084F034F034F034F03",
                array(
                    "PhysicalSensors" => 16,
                    "VirtualSensors" => 4,
                    "TimeConstant" => 1,
                    "c0-0" => 1,
                    "c1-0" => 2,
                    "c0-1" => 3,
                    "c1-1" => 4,
                    "c0-2" => 5,
                    "c1-2" => 6,
                    "c0-3" => 7,
                    "c1-3" => 8,
                    "alarm0" => "148.8 &#176;F",
                    "alarm1" => "148.8 &#176;F",
                    "alarm2" => "148.8 &#176;F",
                    "alarm3" => "148.8 &#176;F",
                ),
                array(
                    'Sensors' => 20,
                    'ActiveSensors' => 16,
                    'PhysicalSensors' => 16,
                    'VirtualSensors' => 4,
                    0 => array('id' => 0x02, 'type' => 'BCTherm2322640',),
                    1 => array('id' => 0x02, 'type' => 'BCTherm2322640',),
                    2 => array('id' => 0x02, 'type' => 'BCTherm2322640',),
                    3 => array('id' => 0x02, 'type' => 'BCTherm2322640',),
                    4 => array('id' => 0x02, 'type' => 'BCTherm2322640',),
                    5 => array('id' => 0x02, 'type' => 'BCTherm2322640',),
                    6 => array('id' => 0x02, 'type' => 'BCTherm2322640',),
                    7 => array('id' => 0x02, 'type' => 'BCTherm2322640',),
                    8 => array(
                        'id' => 0x70, 'type' => 'generic', 'location' => "Output 1",
                    ),
                    9 => array(
                        'id' => 0x70, 'type' => 'generic', 'location' => "Output 2",
                    ),
                    10 => array(
                        'id' => 0x70, 'type' => 'generic', 'location' => "Output 3",
                    ),
                    11 => array(
                        'id' => 0x70, 'type' => 'generic', 'location' => "Output 4",
                    ),
                    12 => array(
                        'id' => 0x70, 'type' => 'generic', 'location' => "Alarm 1",
                    ),
                    13 => array(
                        'id' => 0x70, 'type' => 'generic', 'location' => "Alarm 2",
                    ),
                    14 => array(
                        'id' => 0x70, 'type' => 'generic', 'location' => "Alarm 3",
                    ),
                    15 => array(
                        'id' => 0x70, 'type' => 'generic', 'location' => "Alarm 4",
                    ),
                    16 => array(
                        'id' => 0xFE, 'type' => 'Placeholder', "location" => null
                    ),
                    17 => array(
                        'id' => 0xFE, 'type' => 'Placeholder', "location" => null
                    ),
                    18 => array(
                        'id' => 0xFE, 'type' => 'Placeholder', "location" => null
                    ),
                    19 => array(
                        'id' => 0xFE, 'type' => 'Placeholder', "location" => null
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
    * data provider for testReadSetup, testReadConfig
    *
    * @return array
    */
    public static function dataReadSetup()
    {
        return array(
            array(
                "000025",
                5,
                "000000002500391101410039201343000009FFFFFF50",
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500391101410039201343000009FFFFFF50",
                    )
                )
                .(string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "01020304",
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
                        "Command" => PacketContainer::COMMAND_GETCALIBRATION,
                        "Data" => "",
                    )
                ),
                true,
            ),
            array(
                "000025",
                2,
                "000000002500391101410039201343000009FFFFFF50",
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500391101410039201343000009FFFFFF50",
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
                        "Command" => PacketContainer::COMMAND_GETCALIBRATION,
                        "Data" => "",
                    )
                )
                .(string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETCALIBRATION,
                        "Data" => "",
                    )
                )
                .(string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                        "Data" => "",
                    )
                )
                .(string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETCALIBRATION,
                        "Data" => "",
                    )
                ),
                false,
            ),
            array(
                "000025",
                2,
                "000000000100392601500039260150010203FFFFFF10",
                "",
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
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                )
                .(string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                        "Data" => "",
                    )
                )
                .(string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                ),
                false,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $devId   The Device ID to pretend to be
    * @param int    $timeout The packet timeout to use
    * @param string $string  The string for the dummy device to return
    * @param string $read    The read string to put in
    * @param string $write   The write string expected
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataReadSetup
    */
    public function testReadSetup($devId, $timeout, $string, $read, $write, $expect)
    {
        $this->d->id = hexdec($devId);
        $this->d->DeviceID = $devId;
        $this->d->DriverInfo["PacketTimeout"] = $timeout;
        $this->socket->readString = $read;
        $ret = $this->o->readSetup();
        $this->assertSame($write, $this->socket->writeString, "Wrong writeString");
        $this->assertSame($string, $this->d->string, "Wrong Setup String");
        $this->assertSame($expect, $ret, "Wrong return value");
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
                "010202020202020202707070707070707008070605040302014F034F034F034F03",
                null,
                array(
                    'PhysicalSensors' => 16,
                    'VirtualSensors' => 4,
                    'TimeConstant' => 1,
                    "c0-0" => 8,
                    "c1-0" => 7,
                    "c0-1" => 6,
                    "c1-1" => 5,
                    "c0-2" => 4,
                    "c1-2" => 3,
                    "c0-3" => 2,
                    "c1-3" => 1,
                    "alarm0" => "148.8 &#176;F",
                    "alarm1" => "148.8 &#176;F",
                    "alarm2" => "148.8 &#176;F",
                    "alarm3" => "148.8 &#176;F",
                    'CPU' => 'Atmel Mega168',
                    'SensorConfig' => '1-8 analog, 9-16 digital',
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
