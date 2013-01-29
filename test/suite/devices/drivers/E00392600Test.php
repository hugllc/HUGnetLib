<?php
/**
 * This runs all of the tests associated with HUGnetLib.
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
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\drivers;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBase.php";
/** This is a required class */
require_once CODE_BASE.'devices/drivers/E00392600.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class E00392600Test extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "E00392600";
    /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    *
    * @access protected
    *
    * @return null
    */
    protected function setUp()
    {
        parent::setUp();
        $device  = new \HUGnet\DummyTable("Device");
        $device->resetMock(array());
        $this->o = \HUGnet\devices\Driver::factory("E00392600", $device);;
    }

    /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    *
    * @access protected
    *
    * @return null
    */
    protected function tearDown()
    {
        parent::tearDown();
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecode()
    {
        return array(
            array( // #0 Devices
                array(
                    "Device" => array(
                        "get" => array(
                            "HWPartNum" => "0039-26-06-P",
                        ),
                    ),
                ),
                "9DDC21A799814E81A02BF1361D496AB1C0A824980001",
                array(
                    "Device" => array(
                        "set" => array(
                            array(
                                "DeviceName",
                                "9ddc21a7-9981-4e81-a02b-f1361d496ab1"
                            ),
                            array("DeviceLocation", "192.168.36.152"),
                            array("GatewayKey", 1),
                            array("DeviceJob", "Gatherer"),
                        ),
                        "get" => array(array("HWPartNum")),
                        "setParam" => array(
                            array("Enable", 0),
                        ),
                    ),
                ),
            ),
            array( // #1 Router
                array(
                    "Device" => array(
                        "get" => array(
                            "HWPartNum" => "0039-26-04-P",
                        ),
                    ),
                ),
                "9DDC21A799814E81A02BF1361D496AB1C0A824980002",
                array(
                    "Device" => array(
                        "set" => array(
                            array(
                                "DeviceName",
                                "9ddc21a7-9981-4e81-a02b-f1361d496ab1"
                            ),
                            array("DeviceLocation", "192.168.36.152"),
                            array("GatewayKey", 2),
                            array("DeviceJob", "Router"),
                        ),
                        "get" => array(array("HWPartNum")),
                        "setParam" => array(
                            array("Enable", 0),
                        ),
                    ),
                ),
            ),
            array( // #2 Unknown
                array(
                    "Device" => array(
                        "get" => array(
                            "HWPartNum" => "0039-26-00-P",
                        ),
                    ),
                ),
                "9DDC21A799814E81A02BF1361D496AB1C0A82498FFFF02",
                array(
                    "Device" => array(
                        "set" => array(
                            array(
                                "DeviceName",
                                "9ddc21a7-9981-4e81-a02b-f1361d496ab1"
                            ),
                            array("DeviceLocation", "192.168.36.152"),
                            array("GatewayKey", 0xFFFF),
                            array("DeviceJob", "Unknown"),
                        ),
                        "get" => array(array("HWPartNum")),
                        "setParam" => array(
                            array("Enable", 2),
                        ),
                    ),
                ),
            ),
            array( // #3 Updater
                array(
                    "Device" => array(
                        "get" => array(
                            "HWPartNum" => "0039-26-02-P",
                        ),
                    ),
                ),
                "9DDC21A799814E81A02BF1361D496AB1C0A82498123401",
                array(
                    "Device" => array(
                        "set" => array(
                            array(
                                "DeviceName",
                                "9ddc21a7-9981-4e81-a02b-f1361d496ab1"
                            ),
                            array("DeviceLocation", "192.168.36.152"),
                            array("GatewayKey", 0x1234),
                            array("DeviceJob", "Updater"),
                        ),
                        "get" => array(array("HWPartNum")),
                        "setParam" => array(
                            array("Enable", 1),
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $string The setup string to test
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDecode
    */
    public function testDecode($mocks, $string, $expect)
    {
        $device  = new \HUGnet\DummyTable("Device");
        $device->resetMock($mocks);
        $this->o->decode($string, $device);
        $ret = $device->retrieve();
        $this->assertEquals($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataEncode()
    {
        return array(
            array( // #0
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "get" => array(
                            "DeviceLocation" => "192.168.36.152",
                            "GatewayKey" => 0x25,
                        ),
                        "getParam" => array(
                            "Enable" => 1,
                        ),
                    ),
                    "System" => array(
                        "get" => array(
                            "uuid"   => "9ddc21a7-9981-4e81-a02b-f1361d496ab1",
                        ),
                    ),
                ),
                true,
                "9DDC21A799814E81A02BF1361D496AB1C0A82498002501",
            ),
            array( // #1
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "get" => array(
                            "DeviceLocation" => "",
                            "GatewayKey" => 0,
                        ),
                        "getParam" => array(
                            "Enable" => 1,
                        ),
                    ),
                    "System" => array(
                        "get" => array(
                            "uuid"   => "",
                        ),
                    ),
                ),
                true,
                "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF00000000000001",
            ),
            array( //#2
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "get" => array(
                            "DeviceLocation" => "192.168.36.152",
                            "GatewayKey" => 0x1002
                        ),
                        "getParam" => array(
                            "Enable" => 1,
                        ),
                    ),
                    "System" => array(
                        "get" => array(
                            "uuid"   => "9ddc21a7-9981-4e81-a02b-f1361d496ab1",
                        ),
                    ),
                ),
                false,
                "9DDC21A799814E81A02BF1361D496AB1C0A82498100201",
            ),
            array( // #3
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "get" => array(
                            "DeviceLocation" => "5192.5168.336.6152",
                            "GatewayKey" => 0x1234,
                        ),
                        "getParam" => array(
                            "Enable" => 5,
                        ),
                    ),
                    "System" => array(
                        "get" => array(
                            "uuid"   => "9ddc21a7-9981-4e81-a02b-f1361d496ab1",
                        ),
                    ),
                ),
                true,
                "9DDC21A799814E81A02BF1361D496AB148305008123405",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $mocks     The value to preload into the mocks
    * @param bool  $showFixed Show the fixed portion of the data
    * @param array $expect    The expected return
    *
    * @return null
    *
    * @dataProvider dataEncode
    */
    public function testEncode($mocks, $showFixed, $expect)
    {
        $device  = new \HUGnet\DummyTable("Device");
        $device->resetMock($mocks);
        $ret = $this->o->encode($device, $showFixed);
        $this->assertSame($expect, $ret);
    }
}
?>
