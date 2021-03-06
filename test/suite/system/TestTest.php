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
namespace HUGnet;
/** This is a required class */
require_once CODE_BASE.'system/Test.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class TestTest extends \PHPUnit_Framework_TestCase
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
        parent::setUp();
        $this->system = new DummySystem();
        // This just resets the mock
        $this->system->resetMock($mocks);
        $this->o = Test::factory($this->system, array());
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
        unset($this->o);
        parent::tearDown();
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataCreate()
    {
        return array(
            array(
                new DummySystem(),
                array(),
                null,
                null,
                array(
                ),
            ),
            array(
                new DummySystem(),
                array(
                    "Tests" => array(
                        "sanitizeWhere" => array(
                            "id" => 5,
                            "name" => 3,
                            "value" => 1,
                        ),
                    ),
                ),
                array(
                    "id" => 5,
                    "name" => 3,
                    "value" => 1,
                ),
                null,
                array(
                    "fromAny" => array(
                        array(
                            array(
                                "id" => 5,
                                "name" => 3,
                                "value" => 1,
                            ),
                        ),
                    ),
                    "clearData" => array(array()),
                    "selectOneInto" => array(
                        array(
                            array(
                                "id" => 5,
                                "name" => 3,
                                "value" => 1,
                            ),
                        ),
                    ),
                    "sanitizeWhere" => array(
                        array(
                            array(
                                "id" => 5,
                                "name" => 3,
                                "value" => 1,
                            ),
                        ),
                    ),
                ),
            ),
            array(
                new DummySystem(),
                array(),
                2,
                new DummyTable("Tests"),
                array(
                    "getRow" => array(
                        array(0 => 2),
                    ),
                    "isEmpty" => array(
                        array(),
                    ),
                    "clearData" => array(array()),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config      The configuration to use
    * @param array $mocks       The mocks to use
    * @param mixed $gateway     The gateway to set
    * @param mixed $class       This is either the name of a class or an object
    * @param array $expectTable The table to expect
    *
    * @return null
    *
    * @dataProvider dataCreate
    */
    public function testCreate($config, $mocks, $gateway, $class, $expectTable)
    {
        $table = new DummyTable();
        // This just resets the mock
        $table->resetMock($mocks);
        $obj = Test::factory($config, $gateway, $class);
        // Make sure we have the right object
        $this->assertTrue((get_class($obj) === "HUGnet\Test"), "Class wrong");
        if (is_object($table)) {
            $this->assertEquals(
                $expectTable, $table->retrieve("Tests"), "Data Wrong"
            );
        }
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataDevice()
    {
        return array(
            array(
                array(
                    "System" => array(
                        "device" => "asdf",
                    ),
                    "Test" => array(
                        "id" => 5,
                    ),
                ),
                "asdf",
                array(
                    "Tests" => array(
                        "get" => array(
                            array("device"),
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $mocks  The mocks to use
    * @param array $expect The table to expect
    * @param array $calls  The expected calls
    *
    * @return null
    *
    * @dataProvider dataDevice
    */
    public function testDevice($mocks, $expect, $calls)
    {
        // This just resets the mock
        $this->system->resetMock($mocks);
        $this->assertEquals(
            $expect, $this->o->device()
        );
        foreach ((array)$calls as $fct => $call) {
            $this->assertEquals(
                $call, $this->system->retrieve($fct), "$fct Calls Wrong"
            );
        }
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataHistory()
    {
        return array(
            array(
                array(
                    "System" => array(
                        "device" => new \HUGnet\DummyTable("Device"),
                    ),
                    "Device" => array(
                        "historyFactory" => new \HUGnet\DummyTable("History"),
                        "id" => 12,
                    ),
                    "Tests" => array(
                        "get" => array(
                            "startdate" => 1,
                            "enddate" => 2,
                        ),
                    ),
                    "History" => array(
                        "getPeriod" => "asdf",
                    ),
                ),
                "asdf",
                array(
                    "History" => array(
                        "getPeriod" => array(
                            array(1, 2, 12),
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $mocks  The mocks to use
    * @param array $expect The table to expect
    * @param array $calls  The expected calls
    *
    * @return null
    *
    * @dataProvider dataHistory
    */
    public function testHistory($mocks, $expect, $calls)
    {
        // This just resets the mock
        $this->system->resetMock($mocks);
        $this->assertEquals(
            $expect, $this->o->history(), "Return Wrong"
        );
        foreach ((array)$calls as $fct => $call) {
            $this->assertEquals(
                $call, $this->system->retrieve($fct), "$fct Calls Wrong"
            );
        }
    }
}
?>
