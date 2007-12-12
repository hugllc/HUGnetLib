<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @version SVN: $Id$
 *
 */

// Call filterTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "filterTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../filter.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 */
class filterTest extends PHPUnit_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
      */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("filterTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
      */
    protected function setUp() {
        $this->o = new filter();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
      */
    protected function tearDown() {
        unset($this->o);
    }

    /**
     * dataProvider for testRegisterFilter
      */
    public static function dataRegisterFilter() {
        return array(
            array("testFilter", true),
            array("testFilterBad", false),
            array("testFilterNoFilters", false),
        );
    }
    /**
     * @dataProvider dataRegisterFilter
      */
    public function testRegisterFilter($class, $expect) {
        $ret = $this->o->registerFilter($class);
        $this->assertSame($expect, $ret);
        if ($expect) {
            $this->assertThat($this->o->filters[$class], $this->isInstanceOf($class));
            foreach ($this->o->filters[$class]->filters as $type => $sInfo) {
                foreach ($sInfo as $filter => $val) {
                    $this->assertSame($this->o->dev[$type][$filter], $class, "'$type->$filter': Not found");
                }
            }
        }
    }
    public static function dataFilter() {
        return array(
        );
    }

    /**
     * @dataProvider dataFilter
      */
    public function testFilter($data, $type, $filter, $expect) {
        $this->o->registerFilter("testFilter");
        $ret = $this->o->filter($data, $type, $filter);
        $this->assertSame($expect, $ret);
    }

    /**
     * @todo Implement testRunFunction().
      */
    public function testRunFunctionCall() {
        $cName = "testFilter";
        $this->o->registerFilter($this->getMock($cName), $cName);
        $this->o->filters[$cName]->expects($this->once())
                           ->method('Test1')
                           ->with($this->equalTo(1), $this->equalTo(2), $this->equalTo(3), $this->equalTo(4));
        $args = array(1,2,3,4);
        $ret = $this->o->runFunction($this->o->filters[$cName], 'Test1', $args, "2");
    }

    /**
     *
      */
    public static function dataRunFunction() {
        return array(
            array("testFilter", "Test1", array(array(2,1,0),2,3,4,), array(0,1,2)),
            array("testFilter", "badFunction", array(array(1,2,3),2,3,4), array(1,2,3)),
            array("badClass", "Test1", array(array(1),2,3,4), array(1)),
        );
    }
    /**
     * @dataProvider dataRunFunction
      */
    public function testRunFunction($class, $function, $args, $expect) {
        $this->o->registerFilter($class);
        $this->o->runFunction($this->o->filters[$class], $function, $args);
        // The history is modified in $args[0];
        $this->assertSame($expect, $args[0]);
    }

    /**
     *
      */
    public static function dataGetClass() {
        return array(
            array("testType", "", "sameClass", "testType", "testFilter1"),
            array("testType", "testFilter2", "sameClass", "testType", "testFilter2"),
            array("badType", "testFilter2", null, "badType", "testFilter2"),
        );
    }
    /**
     * @dataProvider dataGetClass().
      */
    public function testGetClass($type, $sensor, $expect, $typeExpect, $sensorExpect) {
        $cName = "testFilter";
        $this->o->registerFilter($cName);
        $class = $this->o->getClass($type, $sensor);
        if ($expect === "sameClass") {
            $expect = $this->o->filters[$cName];
        }
        $this->assertSame($class, $expect, "Wrong object returned");
        $this->assertEquals($type, $typeExpect, "Type changed incorrectly");
        $this->assertEquals($sensor, $sensorExpect, "Sensor changed incorrectly");
    }

}

// Call filterTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "filterTest::main") {
    filterTest::main();
}

/**
 *  This is a test sensor.  It is not used for anything else.
 */
class testFilter extends filter_base {
    var $filters = array(
        "testType" => array(
            "testFilter1" => array(
                "longName" => "Generic Test Sensor 1",
                "function" => "Test1",
                "extraText" => "extraTest",
                "extraDefault" => "extraDefaultTest",
            ),
            "testFilter2" => array(
                "longName" => "Generic Test Sensor 2",
                "function" => "Test2",
            ),
        ),
    );

    /**
     * 
     */    
    public function Test1(&$history, $index, $filter, $extra, $deltaT = null)
    {
        // This must stay the same. 
        return array_reverse($history);
    }
    public function Test2(&$history, $index, $filter, $extra, $deltaT = null) 
    {
    }
}
/**
 * This class is to test how things handle not having a sensors variable;
 */
class testFilterNoFilters extends filter_base {
    function __construct() {
        // Make absolutely sure that there are no sensors
        unset($this->filters);
    }
}

?>
