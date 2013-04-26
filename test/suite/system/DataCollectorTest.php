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
namespace HUGnet;
/** This is a required class */
require_once CODE_BASE.'system/DataCollector.php';
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DataCollectorTest extends \PHPUnit_Framework_TestCase
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
                "DummyTable",
                array(
                ),
            ),
            array(
                new DummySystem(),
                array(
                    "Datacollectors" => array(
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
                "DummyTable",
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
                            "`id` = ? AND `name` = ? AND `value` = ?",
                            array(5, 3, 1),
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
                new DummyTable(),
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
        $obj = DataCollector::factory($config, $gateway);
        // Make sure we have the right object
        $this->assertTrue(
            (get_class($obj) === "HUGnet\DataCollector"), "Class wrong"
        );
        if (is_object($table)) {
            $this->assertEquals(
                $expectTable, $table->retrieve("Datacollectors"), "Data Wrong"
            );
        }
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataToArray()
    {
        return array(
            array(
                new DummySystem(),
                array(
                    "System" => array(
                        "now" => 3700,
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "to" => "array",
                            "this" => "here",
                            "LastContact" => 50,
                        ),
                    ),
                ),
                true,
                array(
                    "to" => "array",
                    "this" => "here",
                    "LastContact" => 50,
                    "LateCheckin" => true,
                ),
            ),
            array(
                new DummySystem(),
                array(
                    "System" => array(
                        "now" => 3700,
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "to" => "array",
                            "this" => "here",
                            "LastContact" => 150,
                        ),
                    ),
                ),
                true,
                array(
                    "to" => "array",
                    "this" => "here",
                    "LastContact" => 150,
                    "LateCheckin" => false,
                ),
            ),
            array(
                new DummySystem(),
                array(
                    "Table" => array(
                        "toArray" => array(
                            "to" => "array",
                            "this" => "here",
                        ),
                    ),
                ),
                false,
                array(
                    "to" => "array",
                    "this" => "here",
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config  The configuration to use
    * @param array $mocks   The mocks to use
    * @param bool  $default Whether or not to use the default values
    * @param mixed $expect  The return to expect
    *
    * @return null
    *
    * @dataProvider dataToArray
    */
    public function testToArray($config, $mocks, $default, $expect)
    {
        $table = new DummyTable("Table");
        // This just resets the mock
        $table->resetMock($mocks);
        $obj = DataCollector::factory($config, null, $table);
        $this->assertEquals(
            $expect, $obj->toArray($default)
        );
    }
}
?>
