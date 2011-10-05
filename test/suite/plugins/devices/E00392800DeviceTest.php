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
 * @subpackage Endpoint
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** Get our classes */
require_once CODE_BASE.'plugins/devices/E00392800Device.php';
require_once TEST_CONFIG_BASE.'stubs/DummyDeviceContainer.php';
require_once TEST_BASE.'plugins/devices/DevicePluginTestBase.php';
require_once CODE_BASE.'containers/PacketContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Endpoint
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class E00392800DeviceTest extends DevicePluginTestBase
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
        $this->o = new E00392800Device($this->d);
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
            array("E00392800Device"),
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
                    "PhysicalSensors" => 16,
                    "VirtualSensors" => 4,
                    "TimeConstant" => 1,
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
                "0102020202020202027070707070707070",
                null,
                array(
                    'PhysicalSensors' => 16,
                    'VirtualSensors' => 4,
                    'TimeConstant' => 1,
                    'CPU' => 'Atmel Mega168',
                    'SensorConfig' => '1-8 analog or digital, 9-16 digital only',
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
