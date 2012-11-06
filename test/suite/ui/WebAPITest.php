<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @subpackage SuiteCLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\ui;
/** This is a required class */
require_once CODE_BASE.'ui/WebAPI.php';
/** This is a required class */
require_once CODE_BASE.'ui/WebAPIArgs.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteCLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class WebAPITest extends \PHPUnit_Framework_TestCase
{
    /** Files that have been created */
    private $_files = array();
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
        foreach ($this->_files as $file) {
            unlink($file);
        }
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataSystem()
    {
        $data = array();
        $htmlargs = \HUGnet\ui\HTMLArgs::factory($argv, $argc, $data);
        return array(
            array(
                new \HUGnet\DummySystem("System"),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 5,
                            "other" => "stuff",
                        ),
                    ),
                ),
                array(
                    "verbose" => 5,
                    "other" => "stuff",
                    "html" => true,
                ),
            ),
            array(
                array(
                    "verbose" => 5,
                ),
                null,
                array(
                    "verbose" => 5,
                    "html" => true,
                )
            ),
            array(
                $htmlargs,
                null,
                array(
                    "verbose" => 0,
                    "html" => true,
                    "quiet" => false,
                    "debug" => false,
                    "test" => false,
                )
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param mixed $config The config to use
    * @param array $mock   The mocks to use
    * @param mixed $expect The system object we are expecting
    *
    * @return null
    *
    * @dataProvider dataSystem()
    */
    public function testSystem($config, $mock, $expect)
    {
        if (is_array($mock)) {
            $config->resetMock($mock);
        }
        $obj = WebAPI::factory($config);
        $conf = $obj->system()->config();
        unset($conf["IPAddr"]);
        $this->assertEquals($expect, $conf);
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataExecute()
    {
        $config1 = \HUGnet\ui\WebAPIArgs::factory(
            array(
                "task" => "dataCollector",
                "action" => "someFunction",
                "id" => "5",
            ),
            3
        );
        $config2 = \HUGnet\ui\WebAPIArgs::factory(
            array(
                "task" => "inputtable",
                "action" => "someFunction",
                "id" => "5",
            ),
            3
        );

        return array(
            array(  // #0
                array(
                    "task" => "device",
                    "action" => "get",
                    "id" => "5",
                ),
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
                    ),
                ),
                array(),
                json_encode(array("Real" => "array")),
                array(
                    "Device" => array(
                        "toArray" => array(
                            array(true),
                        ),
                        "load" => array(
                            array(5),
                        ),
                    ),
                ),
            ),
            array(  // #1
                array(
                    "task" => "device",
                    "action" => "get",
                    "id" => 5,
                    "format" => "CSV",
                ),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "load" => true,
                        "toArray" => "Test",
                    ),
                ),
                array(),
                "Test",
                array(
                    "Device" => array(
                        "toArray" => array(
                            array(true),
                        ),
                        "load" => array(
                            array(5),
                        ),
                    ),
                ),
            ),
            array(  // #2
                array(
                    "task" => "device",
                    "action" => "put",
                    "id" => "10",
                    "data" => array(
                        "a" => "b",
                        "c" => "d",
                    ),
                ),
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
                    ),
                ),
                array(),
                json_encode(array("Real" => "array")),
                array(
                    "Device" => array(
                        "toArray" => array(
                            array(true),
                        ),
                        "change" => array(
                            array(
                                array(
                                    "a" => "b",
                                    "c" => "d",
                                ),
                            ),
                        ),
                        "load" => array(
                            array(16),
                            array(16),
                        ),
                    ),
                ),
            ),
            array(  // #3
                array(
                    "task" => "dataCollector",
                    "action" => "get",
                    "id" => "e035bd03-c52b-4061-89ab-cf5b6ab8243f",
                ),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "datacollector" => new \HUGnet\DummyBase("Datacollector"),
                    ),
                    "Datacollector" => array(
                        "load" => true,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                    ),
                ),
                array(),
                json_encode(array("Real" => "array")),
                array(
                    "Datacollector" => array(
                        "toArray" => array(
                            array(true),
                        ),
                        "load" => array(
                            array(
                                "e035bd03-c52b-4061-89ab-cf5b6ab8243f",
                            ),
                        ),
                    ),
                ),
            ),
            array(  // #4
                $config1,
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "datacollector" => new \HUGnet\DummyBase("Datacollector"),
                    ),
                    "Datacollector" => array(
                        "webAPI" => array(
                            "Real" => "array",
                        ),
                    ),
                ),
                array(),
                json_encode(array("Real" => "array")),
                array(
                    "Datacollector" => array(
                        "webAPI" => array(
                            array(
                                $config1,
                                array(),
                            ),
                        ),
                    ),
                ),
            ),
            array(  // #5
                array(
                    "task" => "sensor",
                    "action" => "get",
                    "id" => "10.5",
                    "data" => array(
                        "a" => "b",
                        "c" => "d",
                    ),
                ),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Sensor"),
                    ),
                    "Sensor" => array(
                        "load" => true,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                    ),
                ),
                array(),
                json_encode(array("Real" => "array")),
                array(
                    "Sensor" => array(
                        "toArray" => array(
                            array(true),
                        ),
                        "load" => array(
                            array(array("dev" => 16, "sensor" => 5)),
                        ),
                    ),
                ),
            ),
            array(  // #6
                array(
                    "task" => "sensor",
                    "action" => "list",
                    "id" => "10",
                    "data" => array(
                        "a" => "b",
                        "c" => "d",
                    ),
                ),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Sensor"),
                    ),
                    "Sensor" => array(
                        "load" => true,
                        "getList" => array(
                            "Real" => "array",
                        ),
                    ),
                ),
                array(),
                json_encode(array("Real" => "array")),
                array(
                    "Sensor" => array(
                        "getList" => array(
                            array(array("dev" => 16), true),
                        ),
                    ),
                ),
            ),
            array(  // #7
                array(
                    "task" => "inputtable",
                    "action" => "get",
                    "id" => "10",
                    "data" => array(
                        "a" => "b",
                        "c" => "d",
                    ),
                ),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "table" => new \HUGnet\DummyTable("Table"),
                    ),
                    "Table" => array(
                        "isEmpty" => false,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                    ),
                ),
                array(),
                json_encode(array("Real" => "array")),
                array(
                    "Table" => array(
                        "getRow" => array(
                            array(10),
                        ),
                        "isEmpty" => array(array()),
                        "toArray" => array(array(true)),
                    ),
                ),
            ),
            array(  // #8
                array(
                    "task" => "inputtable",
                    "action" => "put",
                    "id" => "10",
                    "data" => array(
                        "a" => "b",
                        "c" => "d",
                    ),
                ),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "table" => new \HUGnet\DummyTable("Table"),
                    ),
                    "Table" => array(
                        "isEmpty" => false,
                        "getRow" => true,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                    ),
                ),
                array(),
                json_encode(array("Real" => "array")),
                array(
                    "Table" => array(
                        "getRow" => array(
                            array(10),
                            array(10),
                        ),
                        "fromAny" => array(array(array("a" => "b", "c" => "d"))),
                        "toArray" => array(array(true)),
                        "updateRow" => array(array()),
                    ),
                ),
            ),
            array(  // #9
                $config2,
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "table" => new \HUGnet\DummyTable("InputTable"),
                    ),
                    "InputTable" => array(
                        "webAPI" => array(
                            "Real" => "array",
                        ),
                    ),
                ),
                array(),
                json_encode(array("Real" => "array")),
                array(
                    "InputTable" => array(
                        "webAPI" => array(
                            array(
                                $config2,
                                array(),
                            ),
                        ),
                    ),
                ),
            ),
            array(  // #10
                array(
                    "task" => "inputtable",
                    "action" => "list",
                    "data" => array(
                        "a" => "b",
                        "c" => "d",
                    ),
                ),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "table" => new \HUGnet\DummyTable("Table"),
                    ),
                    "Table" => array(
                        "isEmpty" => false,
                        "getRow" => true,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                        "sanitizeWhere" => array(
                            "a" => "b",
                        ),
                        "select" => array(
                            new \HUGnet\DummyTable("Table1"),
                            new \HUGnet\DummyTable("Table2")
                        ),
                    ),
                    "Table1" => array(
                        "toArray" => array("ab" => "cd", "ef" => "gh"),
                    ),
                    "Table2" => array(
                        "toArray" => array("ij" => "kl", "mn" => "op"),
                    ),
                ),
                array(),
                json_encode(
                    array(
                        array("ab" => "cd", "ef" => "gh"),
                        array("ij" => "kl", "mn" => "op"),
                    )
                ),
                array(
                    "Table" => array(
                        "sanitizeWhere" => array(
                            array(array('a' => 'b', 'c' => 'd'))
                        ),
                        "select" => array(
                            array('`a` = ?', array('b')),
                        ),
                    ),
                ),
            ),
            array(  // #11
                array(
                    "task" => "inputtable",
                    "action" => "list",
                    "data" => array(
                        "a" => "b",
                        "c" => "d",
                    ),
                ),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "table" => new \HUGnet\DummyTable("Table"),
                    ),
                    "Table" => array(
                        "isEmpty" => false,
                        "getRow" => true,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                        "sanitizeWhere" => array(
                        ),
                        "select" => array(
                            1 => array(
                                new \HUGnet\DummyTable("Table1"),
                                new \HUGnet\DummyTable("Table2")
                            )
                        ),
                    ),
                    "Table1" => array(
                        "toArray" => array("ab" => "cd", "ef" => "gh"),
                    ),
                    "Table2" => array(
                        "toArray" => array("ij" => "kl", "mn" => "op"),
                    ),
                ),
                array(),
                json_encode(
                    array(
                        array("ab" => "cd", "ef" => "gh"),
                        array("ij" => "kl", "mn" => "op"),
                    )
                ),
                array(
                    "Table" => array(
                        "sanitizeWhere" => array(
                            array(array('a' => 'b', 'c' => 'd'))
                        ),
                        "select" => array(
                            array('1', array()),
                        ),
                    ),
                ),
            ),
            array(  // #12
                array(
                    "task" => "history",
                    "action" => "last",
                    "id" => "10",
                ),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "device" => new \HUGnet\DummyTable("Device"),
                    ),
                    "Device" => array(
                        "historyFactory" => new \HUGnet\DummyTable("History"),
                    ),
                    "History" => array(
                        "isEmpty" => false,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                    ),
                ),
                array(),
                json_encode(
                    array(
                        "Real" => "array",
                    )
                ),
                array(
                    "History" => array(
                        "isEmpty" => array(
                            array()
                        ),
                        "selectOneInto" => array(
                            array('`id` = ?', array(16)),
                        ),
                        "toArray" => array(
                            array(true)
                        ),
                    ),
                ),
            ),
            array(  // #13
                array(
                    "task" => "history",
                    "action" => "put",
                    "id" => "10",
                    "data" => array(
                        "type" => "history",
                        array("id" => 42, "Date" => 3214),
                        array("id" => 16, "Date" => 1234),
                    ),
                ),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "device" => new \HUGnet\DummyTable("Device"),
                    ),
                    "Device" => array(
                        "historyFactory" => new \HUGnet\DummyTable("History"),
                    ),
                    "History" => array(
                        "isEmpty" => false,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                        "insertRow" => true,
                    ),
                ),
                array(),
                json_encode(
                    array(
                        1 => 1,
                    )
                ),
                array(
                    "History" => array(
                        "insertRow" => array(
                            array(true)
                        ),
                        "fromArray" => array(
                            array(array("id" => 16, "Date" => 1234)),
                        ),
                        "clearData" => array(
                            array()
                        ),
                    ),
                ),
            ),
            array(  // #14
                array(
                    "task" => "history",
                    "action" => "get",
                    "id" => "10",
                    "data" => array(
                        "since" => 1234,
                        "until" => 4321,
                        "limit" => 1,
                        "start" => 0,
                        "order" => "desc",
                    ),
                ),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 0,
                        ),
                        "device" => new \HUGnet\DummyTable("Device"),
                    ),
                    "Device" => array(
                        "historyFactory" => new \HUGnet\DummyTable("History"),
                    ),
                    "History" => array(
                        "isEmpty" => false,
                        "toArray" => array(
                            "Real" => "array",
                        ),
                        "insertRow" => true,
                        "getPeriod" => true,
                        "nextInto" => false,
                    ),
                ),
                array(),
                json_encode(
                    array(
                        array("Real" => "array"),
                    )
                ),
                array(
                    "History" => array(
                        "getPeriod" => array(
                            array(1234, 4321, 16, 'history', '', array())
                        ),
                        "toArray" => array(
                            array(true),
                        ),
                        "nextInto" => array(
                            array(null)
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param mixed $config The config to use
    * @param array $mock   The mocks to use
    * @param array $extra  The extra data to send to execute
    * @param mixed $expect The system object we are expecting
    * @param array $calls  The calls to expect
    *
    * @return null
    *
    * @dataProvider dataExecute()
    */
    public function testExecute($config, $mock, $extra, $expect, $calls)
    {
        $system = new \HUGnet\DummySystem("System");
        $system->resetMock($mock);
        if (!is_object($config)) {
            $args = \HUGnet\ui\WebAPIArgs::factory(
                $config,
                count($config)
            );
        } else {
            $args = $config;
        }
        $obj = WebAPI::factory($args, $system);
        ob_start();
        $obj->execute($extra);
        $ret = ob_get_contents();
        ob_end_clean();
        $this->assertEquals($expect, $ret, "Output wrong");
        foreach ((array)$calls as $obj => $call) {
            $this->assertEquals(
                $call, $system->retrieve($obj), "$obj Calls Wrong"
            );
        }
    }

}
?>
