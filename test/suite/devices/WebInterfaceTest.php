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
namespace HUGnet\devices;
/** This is a required class */
require_once CODE_BASE.'devices/WebInterface.php';
/** This is a required class */
require_once CODE_BASE.'network/packets/Packet.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once CODE_BASE.'db/Table.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyDeviceDriver.php';
/** This is a required class */
require_once CODE_BASE.'db/tables/Firmware.php';



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
class WebInterfaceTest extends \PHPUnit_Framework_TestCase
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
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataWebInterface()
    {

        return array(
            array(  // #0
                new \HUGnet\DummyBase("Args"),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                        "now" => 1234,
                    ),
                    "Device" => array(
                        "load" => true,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                        "network" => new \HUGnet\DummyBase("Network"),
                    ),
                    "Args" => array(
                        "get" => array(
                            "action" => "put",
                            "data" => array(
                                "a" => "B",
                                "c" => "D",
                                "params" => array(
                                    "e" => "F",
                                ),
                            ),
                        ),
                    ),
                ),
                false,
                array(),
                array("Real" => "array"),
                array(
                    "Device" => array(
                        "toArray" => array(
                            array(true),
                        ),
                        "set" => array(
                            array("a", "B"),
                            array("c", "D"),
                        ),
                        "setParam" => array(
                            array("e", "F"),
                            array("LastModified", 1234),
                        ),
                        "store" => array(
                            array(),
                        ),
                    ),
                ),
            ),
            array(  // #1
                new \HUGnet\DummyBase("Args"),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "load" => true,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                        "network" => new \HUGnet\DummyBase("Network"),
                        "action" => new \HUGnet\DummyBase("Action"),
                    ),
                    "Args" => array(
                        "get" => array(
                            "action" => "config",
                        ),
                    ),
                ),
                false,
                array(),
                -1,
                array(
                ),
            ),
            array(  // #2
                new \HUGnet\DummyBase("Args"),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "load" => true,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                        "network" => new \HUGnet\DummyBase("Network"),
                        "action" => new \HUGnet\DummyBase("Action"),
                    ),
                    "Args" => array(
                        "get" => array(
                            "action" => "config",
                        ),
                    ),
                    "Action" => array(
                        "config" => true,
                    ),

                ),
                false,
                array(),
                array("Real" => "array"),
                array(
                ),
            ),
            array(  // #3
                new \HUGnet\DummyBase("Args"),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "load" => true,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                        "network" => new \HUGnet\DummyBase("Network"),
                        "action" => new \HUGnet\DummyBase("Action"),
                    ),
                    "Args" => array(
                        "get" => array(
                            "action" => "loadconfig",
                        ),
                    ),
                ),
                false,
                array(),
                -1,
                array(
                ),
            ),
            array(  // #4
                new \HUGnet\DummyBase("Args"),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "load" => true,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                        "network" => new \HUGnet\DummyBase("Network"),
                        "action" => new \HUGnet\DummyBase("Action"),
                    ),
                    "Args" => array(
                        "get" => array(
                            "action" => "loadconfig",
                        ),
                    ),
                    "Network" => array(
                        "loadConfig" => true,
                    ),

                ),
                false,
                array(),
                array("Real" => "array"),
                array(
                ),
            ),
            array(  // #5
                new \HUGnet\DummyBase("Args"),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                        "now" => 5,
                    ),
                    "Device" => array(
                        "load" => true,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                        "network" => new \HUGnet\DummyBase("Network"),
                        "action" => new \HUGnet\DummyBase("Action"),
                        "insertVirtual" => true,
                    ),
                    "Args" => array(
                        "get" => array(
                            "action" => "new",
                        ),
                    ),
                    "Network" => array(
                        "loadConfig" => true,
                    ),

                ),
                false,
                array(),
                array("Real" => "array"),
                array(
                    "Device" => array(
                        "insertVirtual" => array(
                            array(
                                array(),
                            ),
                        ),
                        "setParam" => array(array("Created", 5)),
                        "store" => array(array()),
                        "toArray" => array(array(true)),
                    ),
                ),
            ),
            array(  // #6
                new \HUGnet\DummyBase("Args"),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "load" => true,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                        "network" => new \HUGnet\DummyBase("Network"),
                        "action" => new \HUGnet\DummyBase("Action"),
                        "insertVirtual" => false,
                    ),
                    "Args" => array(
                        "get" => array(
                            "action" => "new",
                            "data" => array("type" => "test"),
                        ),
                    ),
                    "Network" => array(
                        "loadConfig" => true,
                    ),

                ),
                false,
                array(),
                array(),
                array(
                    "Device" => array(
                        "insertVirtual" => array(
                            array(
                                array('HWPartNum' => '0039-24-03-P'),
                            ),
                        ),
                    ),
                ),
            ),

        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param mixed $config   The config to use
    * @param array $mock     The mocks to use
    * @param bool  $readonly Whether the system is read only or not
    * @param array $extra    The extra data to send to execute
    * @param mixed $expect   The system object we are expecting
    * @param array $calls    The calls to expect
    *
    * @return null
    *
    * @dataProvider dataWebInterface()
    */
    public function testWebInterface(
        $config, $mock, $readonly, $extra, $expect, $calls
    ) {
        $system = new \HUGnet\DummySystem("System");
        $system->resetMock($mock);
        $device = new \HUGnet\DummyBase("Device");
        $driver = new \HUGnet\DummyBase("Driver");
        $obj = WebInterface::factory($system, $device, $driver);
        $ret = $obj->WebAPI($config, $extra);
        $this->assertEquals($expect, $ret, "Output wrong");
        foreach ((array)$calls as $obj => $call) {
            $this->assertEquals(
                $call, $system->retrieve($obj), "$obj Calls Wrong"
            );
        }
    }

}
?>
