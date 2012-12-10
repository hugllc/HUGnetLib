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
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\base;
/** This is a required class */
require_once CODE_BASE.'base/SystemTableAction.php';
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
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyNetwork.php';
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class SystemTableActionTest extends \PHPUnit_Framework_TestCase
{
    /** This is our system object */
    protected $system;
    /** This is our system table */
    protected $table;
    /** This is our test object */
    protected $o;

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
        $this->system = new \HUGnet\DummySystem("System");
        $this->table = new \HUGnet\DummyTable("Table");
        $this->o = SystemTableActionTestStub::factory(
            $this->system, null, $this->table
        );
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
        unset($this->table);
        unset($this->system);
        parent::tearDown();
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataCall()
    {
        return array(
            array(
                array(
                ),
                array(),
                array(),
                "asdf",
                "asdf",
                2,
                "RuntimeException",
            ),
            array(
                array(
                    "Silly" => array(
                        "sillyFct" => array(
                            "hello" => 25,
                            "there" => 50,
                       ),
                    ),
                ),
                array(
                    "sillyFct" => "silly",
                ),
                array(
                ),
                "sillyFct",
                "hello",
                25,
                null,
            ),
            array(
                array(
                    "SillySystem" => array(
                        "sillyFct" => array(
                            "hello" => 25,
                            "there" => 50,
                       ),
                    ),
                ),
                array(
                    "sillyFct" => "NonFunction",
                ),
                array(
                    "NonFunction" => "\\HUGnet\\network\\DummyNetwork",
                ),
                "sillyFct",
                "hello",
                25,
                null,
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $config    The configuration to use
    * @param array  $functions The functions array to use
    * @param array  $classes   The classes array to use
    * @param string $function  This is the function to call
    * @param mixed  $arg       This is the argument to the function
    * @param mixed  $expect    The value we expect back
    * @param string $exception If this is a string, it is the excpected exception
    *
    * @return null
    *
    * @dataProvider dataCall
    */
    public function testCall(
        $config, $functions, $classes, $function, $arg, $expect, $exception
    ) {
        $this->system->resetMock($config);
        if (is_string($exception)) {
            $this->setExpectedException($exception);
        }
        $this->o->setFunctions($functions);
        $this->o->setClasses($classes);
        $this->assertSame($expect, $this->o->$function($arg));
    }

}
/**
 * Stub class for testing SystemTableBase
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class SystemTableActionTestStub extends SystemTableAction
{
    /**
    * This sets the functions
    *
    * @param array $functions The functions to set
    *
    * @return null
    */
    public function setFunctions($functions)
    {
        $this->functions = $functions;
    }
    /**
    * This sets the classes
    *
    * @param array $classes The classes to set
    *
    * @return null
    */
    public function setClasses($classes)
    {
        $this->classes = $classes;
    }
    /**
    * This sets the classes
    *
    * @return null
    */
    public function silly()
    {
        return new \HUGnet\DummyBase("Silly");
    }
    /**
    * This sets the classes
    *
    * @return null
    */
    public function &system()
    {
        return "SillySystem";
    }
}
?>