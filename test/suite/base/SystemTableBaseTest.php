<?php
/**
 * This runs all of the tests associated with HUGnetLib.
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
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/** This is a required class */
require_once CODE_BASE.'base/SystemTableBase.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once CODE_BASE.'system/Error.php';
/** This is a required class */
require_once CODE_BASE.'util/Util.php';
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class SystemTableBaseTest extends \PHPUnit_Framework_TestCase
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
    * This tests the exception when a system object is not passed
    *
    * @return null
    */
    public function testCreateThrowException()
    {
        $this->setExpectedException("InvalidArgumentException");
        // This throws an exception because $test is not a object
        SystemTableBaseTestStub::factory($test);
    }
    /**
    * This tests the exception when a system object is not passed
    *
    * @return null
    */
    public function testTableThrowException()
    {
        $this->setExpectedException("InvalidArgumentException");
        $system = new DummySystem();
        // This throws an exception because the table name is bad
        SystemTableBaseTestStub::factory($system, 2, "thisIsAbadClassName");
    }
    /**
    * Data provider for testFactory
    *
    * @return array
    */
    public static function dataFactory()
    {
        return array(
            array(array(), null, "DummyTable", array()),
            array(
                array(),
                array(
                    "id" => 5,
                    "name" => 3,
                    "value" => 1,
                ),
                "DummyTable",
                array(
                    "Table" => array(
                        "fromAny" => array(
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
            ),
            array(
                array(),
                2,
                new DummyTable("Table"),
                array(
                    "Table" => array(
                        "getRow" => array(
                            array(0 => 2),
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config      The configuration to use
    * @param mixed $gateway     The gateway to set
    * @param mixed $class       This is either the name of a class or an object
    * @param array $expectTable The table to expect
    *
    * @return null
    *
    * @dataProvider dataFactory
    */
    public function testFactory($config, $gateway, $class, $expectTable)
    {
        $sys = new DummySystem("System");
        $sys->resetMock($config);
        $obj = SystemTableBaseTestStub::factory($sys, $gateway, $class);
        // Make sure we have the right object
        $this->assertTrue(
            is_subclass_of($obj, "HUGnet\SystemTableBase"), "Class wrong"
        );
        $this->assertEquals($expectTable, $sys->retrieve(), "Data Wrong");
    }

    /**
    * Data provider for testLoad
    *
    * @return array
    */
    public static function dataLoad()
    {
        return array(
            array(
                array(),
                new DummyTable(),
                array(
                    "id" => 5,
                    "name" => 3,
                    "value" => 1,
                ),
                array(
                    "Table" => array(
                        "fromAny" => array(
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
                true,
            ),
            array(
                array(
                    "Table" => array(
                        array(
                            "getRow" => false,
                        )
                    ),
                ),
                new DummyTable("Table"),
                2,
                array(
                    "Table" => array(
                        "getRow" => array(
                            array(0 => 2),
                        ),
                    ),
                ),
                false,
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param object $config      The configuration to use
    * @param object $class       The table class to use
    * @param mixed  $gateway     The gateway data to set
    * @param array  $expectTable The table to expect
    * @param bool   $return      The expected return
    *
    * @return null
    *
    * @dataProvider dataLoad
    */
    public function testLoad($config, $class, $gateway, $expectTable, $return)
    {
        $sys = new DummySystem("System");
        $sys->resetMock($config);
        $obj = SystemTableBaseTestStub::factory($sys, null, $class);
        $ret = $obj->load($gateway);
        $this->assertSame($return, $ret, "Return Wrong");
        $this->assertEquals($expectTable, $class->retrieve(), "Data Wrong");
    }
    /**
    * Data provider for testStore
    *
    * @return array
    */
    public static function dataStore()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => 5,
                        "updateRow" => true,
                    ),
                ),
                new DummyTable("Table"),
                false,
                array(
                    "Table" => array(
                        "get" => array(array("id")),
                        "updateRow" => array(array()),
                    ),
                ),
                true,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => 0,
                        "insertRow" => false,
                    ),
                ),
                new DummyTable("Table"),
                false,
                array(
                    "Table" => array(
                        "get" => array(array("id")),
                        "insertRow" => array(array(false)),
                    ),
                ),
                false,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => null,
                        "insertRow" => true,
                    ),
                ),
                new DummyTable("Table"),
                true,
                array(
                    "Table" => array(
                        "get" => array(array("id")),
                        "insertRow" => array(array(true)),
                    ),
                ),
                true,
            ),
        );
    }
    /**
    * This tests storing to the database
    *
    * @param array  $config      The configuration to use
    * @param object $class       The table class to use
    * @param mixed  $replace     Whether to replace the current record or not
    * @param array  $expectTable The table to expect
    * @param bool   $return      The expected return
    *
    * @return null
    *
    * @dataProvider dataStore
    */
    public function testStore($config, $class, $replace, $expectTable, $return)
    {
        $sys = new DummySystem("System");
        $sys->resetMock($config);
        $obj = SystemTableBaseTestStub::factory($sys, null, $class);
        $ret = $obj->store($replace);
        $this->assertEquals($expectTable, $class->retrieve(), "Data Wrong");
        $this->assertSame($return, $ret, "Return Wrong");
    }

}
/**
 * Stub class for testing SystemTableBase
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class SystemTableBaseTestStub extends SystemTableBase
{
}
?>
