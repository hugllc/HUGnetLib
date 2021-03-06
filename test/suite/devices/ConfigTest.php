<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This is a required class */
require_once CODE_BASE.'devices/Config.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
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
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecode()
    {
        return array(
            array( // #0
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 0xE8,
                        ),
                    ),
                ),
                "00000000E80039CF01410039246743000302FFFFFF50",
                array(
                    array("id", 0xE8),
                    array("DeviceID", 0xE8),
                    array("HWPartNum", "0039CF0141"),
                    array("FWPartNum", "0039246743"),
                    array("FWVersion", "000302"),
                    array("DeviceGroup", "FFFFFF"),
                    array(
                        "RawSetup",
                        "00000000E80039CF01410039246743000302FFFFFF50"
                    ),
                    array("Driver", "EDEFAULT"),
                ),
                "",
            ),
            array( // #1
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 0xE8,
                        ),
                    ),
                ),
                "00000000E80039CF01410039246743000102FFFFFF50",
                array(
                    array("id", 0xE8),
                    array("DeviceID", 0xE8),
                    array("HWPartNum", "0039CF0141"),
                    array("FWPartNum", "0039246743"),
                    array("FWVersion", "000102"),
                    array("DeviceGroup", "FFFFFF"),
                    array(
                        "RawSetup",
                        "00000000E80039CF01410039246743000102FFFFFF50"
                    ),
                    array("Driver", "EDEFAULT"),
                ),
                "",
            ),
            array( // #2
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 0xE8,
                        ),
                    ),
                ),
                "00000000E80039CE01410039246743000005FFFFFF1E",
                array(
                    array("id", 0xE8),
                    array("DeviceID", 0xE8),
                    array("HWPartNum", "0039CE0141"),
                    array("FWPartNum", "0039246743"),
                    array("FWVersion", "000005"),
                    array("DeviceGroup", "FFFFFF"),
                    array(
                        "RawSetup",
                        "00000000E80039CE01410039246743000005FFFFFF1E"
                    ),
                    array("Driver", "EDEFAULT"),
                ),
                "",
            ),
            array( // #3
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 0xE8,
                        ),
                    ),
                ),
                "00000000E80039CD01410039456743000005FFFFFF530123456789",
                array(
                    array("id", 0xE8),
                    array("DeviceID", 0xE8),
                    array("HWPartNum", "0039CD0141"),
                    array("FWPartNum", "0039456743"),
                    array("FWVersion", "000005"),
                    array("DeviceGroup", "FFFFFF"),
                    array(
                        "RawSetup",
                        "00000000E80039CD01410039456743000005FFFFFF"
                        ."530123456789"
                    ),
                    array("Driver", "EDEFAULT"),
                ),
                "0123456789",
            ),
            array(  // #4
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 0xE8,
                        ),
                    ),
                ),
                "00000000E80039CC01410039256743000005FFFFFF2101",
                array(
                    array("id", 0xE8),
                    array("DeviceID", 0xE8),
                    array("HWPartNum", "0039CC0141"),
                    array("FWPartNum", "0039256743"),
                    array("FWVersion", "000005"),
                    array("DeviceGroup", "FFFFFF"),
                    array(
                        "RawSetup",
                        "00000000E80039CC01410039256743000005FFFFFF2101"
                    ),
                    array("Driver", "EDEFAULT"),
                ),
                "01",
            ),
            array(  // #5 Bad HWPartNum
                array(
                ),
                "00000000E80038CC01410039256743000005FFFFFF2101",
                array(
                ),
                false,
            ),
            array(  // #6 Bad FWPartNum
                array(
                ),
                "00000000E80039CC01410038256743000005FFFFFF2101",
                array(
                ),
                false,
            ),
            array(  // #7 Specific example
                array(
                ),
                "00000000016E00392801410039201843000002FFFFFF50010202020202020202"
                    ."7046707070707008200820082008204F034F034F034F03",
                array(
                ),
                false,
            ),
            array(  // #8 0 for id
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 0,
                        ),
                    ),
                ),
                "00000000E80039CC01410039256743000005FFFFFF2101",
                array(
                    array("id", 0xE8),
                    array("DeviceID", 0xE8),
                    array("HWPartNum", "0039CC0141"),
                    array("FWPartNum", "0039256743"),
                    array("FWVersion", "000005"),
                    array("DeviceGroup", "FFFFFF"),
                    array(
                        "RawSetup",
                        "00000000E80039CC01410039256743000005FFFFFF2101"
                    ),
                    array("Driver", "EDEFAULT"),
                ),
                "01",
            ),
            array(  // #9 Wrong ID
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 0xE9,
                        ),
                    ),
                ),
                "00000000E80039CC01410039256743000005FFFFFF2101",
                array(
                ),
                false,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks       The value to preload into the mocks
    * @param string $string      The setup string to test
    * @param array  $expect      The expected return
    * @param string $expectExtra The extra stuff returned
    *
    * @return null
    *
    * @dataProvider dataDecode
    */
    public function testDecode($mocks, $string, $expect, $expectExtra)
    {
        $driver = new \HUGnet\DummyBase("Device");
        $driver->resetMock($mocks);
        $extra = Config::decode($string, $driver);
        $this->assertEquals($expectExtra, $extra, "Return Wrong");
        $ret = $driver->retrieve();
        $this->assertEquals($expect, (array)$ret["Device"]["set"], "Calls Wrong");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataEncode()
    {
        return array(
            array(
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 0xE8,
                            "DeviceID" => "0000E8",
                            "HWPartNum" => "0039-CF-01-A",
                            "FWPartNum" => "0039-24-67-C",
                            "FWVersion" => "0.3.2",
                            "DeviceGroup" => "FFFFFF",
                        ),
                    ),
                ),
                true,
                "00000000E80039CF01410039246743000302FFFFFFFF",
            ),
            array(
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 0xE8,
                            "DeviceID" => "0000E8",
                            "HWPartNum" => "0039-CF-01-A",
                            "FWPartNum" => "0039-24-67-C",
                            "FWVersion" => "12.34.56",
                            "DeviceGroup" => "FFFFFF",
                        ),
                    ),
                ),
                true,
                "00000000E80039CF01410039246743123456FFFFFFFF",
            ),
            array(
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 0xE8,
                            "DeviceID" => "0000E8",
                            "HWPartNum" => "0039-CE-01-A",
                            "FWPartNum" => "0039-24-67-C",
                            "FWVersion" => "0.0.5",
                            "DeviceGroup" => "FFFFFF",
                        ),
                    ),
                ),
                true,
                "00000000E80039CE01410039246743000005FFFFFFFF",
            ),
            array(
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 0xE8,
                            "DeviceID" => "0000E8",
                            "HWPartNum" => "0039-CD-01-A",
                            "FWPartNum" => "0123-45-67-C",
                            "FWVersion" => "0.0.5",
                            "DeviceGroup" => "FFFFFF",
                        ),
                    ),
                ),
                true,
                "00000000E80039CD01410123456743000005FFFFFFFF",
            ),
            array(
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 0xE8,
                            "DeviceID" => "0000E8",
                            "HWPartNum" => "0039-CC-01-A",
                            "FWPartNum" => "0039-25-67-C",
                            "FWVersion" => "0.0.5",
                            "DeviceGroup" => "FFFFFF",
                        ),
                    ),
                ),
                true,
                "00000000E80039CC01410039256743000005FFFFFFFF",
            ),
            array(
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 0xE8,
                            "DeviceID" => "0000E8",
                            "HWPartNum" => "0039-CC-01-A",
                            "FWPartNum" => "0039-25-67-C",
                            "FWVersion" => "0.0.5",
                            "DeviceGroup" => "FFFFFF",
                        ),
                    ),
                ),
                false,
                "FFFFFFFF",
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
        $driver = new \HUGnet\DummyBase("Device");
        $driver->resetMock($mocks);
        $ret = Config::encode($driver, $showFixed);
        $this->assertEquals($expect, $ret);
    }
}
?>
