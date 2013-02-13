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
require_once CODE_BASE.'devices/drivers/E00392101.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';

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
class E00392101Test extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "E00392101";
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
        $this->o = \HUGnet\devices\Driver::factory("E00392101", $device);
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
    public static function dataGet()
    {
        return array(
            array(
                "ThisIsABadName",
                null,
            ),
            array(
                "packetTimeout",
                2,
            ),
            array(
                "virtualSensors",
                4,
            ),
            array(
                "totalSensors",
                10,
            ),
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecodeSensorString()
    {
        return array(
            array(
                array(),
                "ThisIsString",
                array(
                    "DataIndex" => 0,
                    "timeConstant" => 1,
                    "String" => "isIsString",
                ),
            ),
            array(
                array(),
                "400B033F1300004A48994800007F134403",
                array(
                    "DataIndex" => 64,
                    "timeConstant" => 1,
                    "String" => "0B033F1300004A48994800007F134403"
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $string The string to decode
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDecodeSensorString
    */
    public function testDecodeSensorString($mocks, $string, $expect)
    {
        $device  = new \HUGnet\DummyTable("Device");
        $device->resetMock($mocks);
        $this->assertEquals($expect, $this->o->decodeSensorString($string));
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataInput()
    {
        return array(
            array(
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "id" => 5,
                        "get" => array(
                            "id" => 5,
                        ),
                    ),
                ),
                0,
                array(
                    "id" => 0xF8,
                    "input" => 0,
                    "dev" => 5,
                    "type" => "DEFAULT",
                ),
            ),
            array(
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "id" => 5,
                        "get" => array(
                            "id" => 5,
                        ),
                    ),
                ),
                1,
                array(
                    "id" => 0xF8,
                    "input" => 1,
                    "dev" => 5,
                    "type" => "DEFAULT",
                ),
            ),
            array(
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "id" => 5,
                        "get" => array(
                            "id" => 5,
                        ),
                    ),
                ),
                2,
                array(
                    "id" => 0xF8,
                    "input" => 2,
                    "dev" => 5,
                    "type" => "DEFAULT",
                ),
            ),
            array(
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "id" => 5,
                        "get" => array(
                            "id" => 5,
                        ),

                    ),
                ),
                3,
                array(
                    "id" => 0xF8,
                    "input" => 3,
                    "dev" => 5,
                    "type" => "DEFAULT",
                ),
            ),
            array(
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "id" => 5,
                        "get" => array(
                            "id" => 5,
                        ),
                    ),
                ),
                4,
                array(
                    "id" => 0xF8,
                    "input" => 4,
                    "dev" => 5,
                    "type" => "DEFAULT",
                ),
            ),
            array(
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "id" => 5,
                        "get" => array(
                            "id" => 5,
                        ),
                    ),
                ),
                5,
                array(
                    "id" => 0xF8,
                    "input" => 5,
                    "dev" => 5,
                    "type" => "DEFAULT",
                ),
            ),
            array(
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "id" => 5,
                        "get" => array(
                            "id" => 5,
                        ),
                    ),
                ),
                6,
                array(
                    "id" => 0xFE,
                    "input" => 6,
                    "dev" => 5,
                ),
            ),
            array(
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "id" => 5,
                        "get" => array(
                            "id" => 5,
                        ),
                    ),
                ),
                7,
                array(
                    "id" => 0xFE,
                    "input" => 7,
                    "dev" => 5,
                ),
            ),
            array(
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "id" => 5,
                        "get" => array(
                            "id" => 5,
                        ),
                    ),
                ),
                8,
                array(
                    "id" => 0xFE,
                    "input" => 8,
                    "dev" => 5,
                ),
            ),
            array(
                array(
                    "Device" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "id" => 5,
                        "get" => array(
                            "id" => 5,
                        ),
                    ),
                ),
                9,
                array(
                    "id" => 0xFE,
                    "input" => 9,
                    "dev" => 5,
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param int    $sid    The input id to get
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataInput
    */
    public function testInput($mocks, $sid, $expect)
    {
        $device  = new \HUGnet\DummyTable("Device");
        $device->resetMock($mocks);
        $input = $this->o->input($sid);
        $ret = $device->retrieve();
        $this->assertEquals(
            $expect, $ret["DeviceInputs"]["fromAny"][1][0], "Setup is wrong"
        );

        /*
        foreach ((array)$expect as $class => $calls) {
            foreach ($calls as $function => $args) {
                $this->assertEquals(
                    $args, $ret[$class][$function], "$key is wrong"
                );
            }
        }
        */
    }
}
?>
