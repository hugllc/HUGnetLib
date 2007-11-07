<?php
/**
 *   Tests the filter class
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Test
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$
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
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    }

    /**
     *
     */
    public function testRegisterFilter() {
        $o = new filter();
        $class = "testFilter";
        $o->registerFilter($class);
        $this->assertTrue(is_object($o->filters[$class]), "Driver object is missing");
        $this->assertEquals(get_class($o->filters[$class]), $class, "The wrong class got registered");    
        foreach($o->filters[$class]->filters as $type => $sInfo) {
            foreach($sInfo as $filter => $val) {
                $this->assertEquals($o->dev[$type][$filter], $class, "'$type->$sensor': Not found");
            }
        }
    }
    /**
     * @todo Implement testGetReading().
     */
    public function testFilterdataCall() {
        $o = new filter();
        $cName = "testFilter";
        $o->registerFilter($this->getMock($cName), $cName);
        $o->filters[$cName]->expects($this->once())
                           ->method('Test1')
                           ->with($this->equalTo(1), $this->arrayHasKey("longName"), $this->equalTo(10), $this->equalTo("extra"));
        $val = 1;
        $ret = $o->Filterdata($val, "testType", "testSensor1", 10, "extra");
    }

    /**
     * @todo Implement testFilterdata().
     */
    public function testFilterdata() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testRunFunction().
     */
    public function testRunFunctionCall() {
        $o = new filter();
        $cName = "testFilter";
        $o->registerFilter($this->getMock($cName), $cName);
        $o->filters[$cName]->expects($this->once())
                           ->method('Test1')
                           ->with($this->equalTo(1), $this->equalTo(2), $this->equalTo(3), $this->equalTo(4));
        $args = array(1,2,3,4);
        $ret = $o->runFunction($o->filters[$cName], 'Test1', $args, "2");
    }
    /**
     * @todo Implement testRunFunction().
     */
    public function testRunFunctionGood() {
        $o = new filter();
        $cName = "testFilter";
        $o->registerFilter($cName);
        $args = array(1,2,3,4);
        $ret = $o->runFunction($o->filters[$cName], 'Test2', $args, 5);
        $this->assertEquals($ret, 1);
    }
    /**
     * @todo Implement testRunFunction().
     */
    public function testRunFunctionBadFct() {
        $o = new filter();
        $cName = "testFilter";
        $o->registerFilter($cName);
        $args = array(1,2,3,4);
        $ret = $o->runFunction($o->filters[$cName], 'badFunction', $args, 5);
        $this->assertEquals($ret, 5);
    }
    /**
     * @todo Implement testRunFunction().
     */
    public function testRunFunctionBadClass() {
        $o = new filter();
        $cName = "testFilter";
        $o->registerFilter($cName);
        $args = array(1,2,3,4);
        $class = "badClass";
        $ret = $o->runFunction($class, 'Test1', $args, 5);
        $this->assertEquals($ret, 5);
    }


    /**
     * @todo Implement testGetClass().
     */
    public function testGetClassSetFilter() {
        $o = new filter();
        $cName = "testFilter";
        $o->registerFilter($cName);
        $type = "testType";
        $filter = "";
        $class = $o->getClass($type, $filter);
        $this->assertTrue(is_object($class), "Object not returned");
        $this->assertEquals(get_class($class), $cName, "Object not returned");
        $this->assertEquals($type, "testType", "Type changed incorrectly");
        $this->assertEquals($filter, "testFilter1", "Sensor changed incorrectly");
    }
    /**
     * @todo Implement testGetClass().
     */
    public function testGetClassGood() {
        $o = new filter();
        $cName = "testFilter";
        $o->registerFilter($cName);
        $type = "testType";
        $filter = "testFilter2";
        $class = $o->getClass($type, $filter);
        $this->assertTrue(is_object($class), "Object not returned");
        $this->assertEquals(get_class($class), $cName, "Object not returned");
        $this->assertEquals($type, "testType", "Type changed incorrectly");
        $this->assertEquals($filter, "testFilter2", "Sensor changed incorrectly");
    }
    /**
     * @todo Implement testGetClass().
     */
    public function testGetClassBad() {
        $o = new filter();
        $cName = "testFilter";
        $o->registerFilter($cName);
        $type = 0x400;
        $sensor = "testFilter2";
        $class = $o->getClass($type, $sensor);
        $this->assertEquals($class, NULL);
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
                "unitType" => "TestType",
                "validUnits" => array('A', 'B', 'C'),
                "storageUnit" =>  'B',
                "function" => "Test1",
                "extra" => "extraTest",
                "extraDefault" => "extraDefaultTest",
                "unitModes" => array(
                    'A' => 'raw,diff',
                    'B' => 'diff',
                    'C' => 'raw',
                ),
            ),
            "testFilter2" => array(
                "longName" => "Generic Test Sensor 2",
                "unitType" => "TestType",
                "validUnits" => array('D', 'E', 'F'),
                "storageUnit" =>  'E',
                "function" => "Test2",
                "unitModes" => array(
                    'E' => 'raw,diff',
                    'D' => 'diff',
                    'F' => 'raw',
                ),
            ),
        ),
    );

    /**
     * 
     */    
    public function Test1($val, $sensor, $TC, $extra) {
        // This must stay the same. 
        return $val*10;
    }
    public function Test2($val, $sensor, $TC, $extra) {
        return $val;
    }
}
?>
