<?php
/**
 * Tests the sensor class
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

// Call sensorTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "sensorTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../sensor.php';

/**
 * Test class for sensor.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:46:15.
 */
class sensorTest extends PHPUnit_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
      */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("sensorTest");
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
     * @todo Implement testGetReading().
      */
    public function testSensorVar() {
    }
    /**
     * dataProvider for testRegisterSensor
      */
    public static function dataRegisterSensor() {
        return array(
            array("testSensor", true),
            array("testSensorBad", false),
            array("testSensorNoSensors", false),
        );
    }
    /**
     * @dataProvider dataRegisterSensor
      */
    public function testRegisterSensor($class, $expect) {
        $o = new sensor();
        $ret = $o->registerSensor($class);
        $this->assertSame($expect, $ret);
        if ($expect) {
            $this->assertThat($o->sensors[$class], $this->isInstanceOf($class));
            foreach ($o->sensors[$class]->sensors as $type => $sInfo) {
                foreach ($sInfo as $sensor => $val) {
                    $this->assertEquals($o->dev[$type][$sensor], $class, "'$type->$sensor': Not found");
                }
            }
        }
    }

    /**
     * @todo Implement testGetReading().
      */
    public function testGetReadingCall() {
        $o = new sensor();
        $cName = "testSensor";
        $o->registerSensor($this->getMock($cName), $cName);
        $o->sensors[$cName]->expects($this->once())
                           ->method('Test1')
                           ->with($this->equalTo(1), $this->arrayHasKey("longName"), $this->equalTo(10), $this->equalTo("extra"));
        $sensor = "testSensor1";
        $ret = $o->getReading(1, 0x100, $sensor, 10, "extra");
    }
    /**
     * data Provider for testGetReading
      */
    public static function dataGetReading() {
        return array(
            array(1, 0x400, "testSensor1", 10, 1, 1, "testSensor1"),
            array(5, 0x100, "testSensor1", 6, 10, 50, "testSensor1"),
        );
    }
    /**
     * @dataProvider dataGetReading
      */
    public function testGetReadingBadType($val, $type, $sensor, $TC, $extra, $expect, $sensorExpect) {
        $o = new sensor();
        $o->registerSensor("testSensor");
        $ret = $o->getReading($val, $type, $sensor, $TC, $extra);
        $this->assertSame($expect, $ret);
        // Sensor is passed by reference so it can change.
        $this->assertSame($sensorExpect, $sensor, "Sensor did not match");
    }

    /**
     * @todo Implement testRunFunction().
      */
    public function testRunFunctionCall() {
        $o = new sensor();
        $cName = "testSensor";
        $o->registerSensor($this->getMock($cName), $cName);
        $o->sensors[$cName]->expects($this->once())
                           ->method('Test1')
                           ->with($this->equalTo(1), $this->equalTo(2), $this->equalTo(3), $this->equalTo(4));
        $args = array(1,2,3,4);
        $ret = $o->runFunction($o->sensors[$cName], 'Test1', $args, "2");
    }
    /**
     *
      */
    public static function dataRunFunction() {
        return array(
            array("testSensor", "Test2", array(1,2,3,4), 24, 1),
            array("testSensor", "badFunction", array(1,2,3,4), 5, 5),
            array("badClass", "Test1", array(1,2,3,4), 7, 7),
        );
    }
    /**
     * @dataProvider dataRunFunction
      */
    public function testRunFunction($class, $function, $args, $default, $expect) {
        $o = new sensor();
        $o->registerSensor($class);
        $ret = $o->runFunction($o->sensors[$class], $function, $args, $default);
        $this->assertSame($expect, $ret);
    }
    /**
     *
      */
    public static function dataGetClass() {
        return array(
            array(0x100, "", "sameClass", 0x100, "testSensor1"),
            array(0x100, "testSensor2", "sameClass", 0x100, "testSensor2"),
            array(0x400, "testSensor2", null, 0x400, "testSensor2"),
        );
    }
    /**
     * @dataProvider dataGetClass().
      */
    public function testGetClass($type, $sensor, $expect, $typeExpect, $sensorExpect) {
        $o = new sensor();
        $cName = "testSensor";
        $o->registerSensor($cName);
        $class = $o->getClass($type, $sensor);
        if ($expect === "sameClass") {
            $expect = $o->sensors[$cName];
        }
        $this->assertSame($class, $expect, "Wrong object returned");
        $this->assertEquals($type, $typeExpect, "Type changed incorrectly");
        $this->assertEquals($sensor, $sensorExpect, "Sensor changed incorrectly");
    }

    /**
     * Data provider for testGetUnits
      */
    public static function dataGetUnits() {
        return array(
            array(0x100, "testSensor1", "Q", "B", "testSensor1"),
            array(0x100, "testSensor1", "A", "A", "testSensor1"),
        );
    }
    /**
     * @dataProvider dataGetUnits().
      */
    public function testGetUnits($type, $sensor, $unit, $expect, $sensorExpect) {
        $o = new sensor();
        $o->registerSensor("testSensor");
        $ret = $o->getUnits($type, $sensor, $unit);
        $this->assertSame($expect, $ret);
        // Sensor is passed by reference so it can change.
        $this->assertSame($sensorExpect, $sensor, "Sensor did not match");
    }

    /**
     * Data provider for testGetUnitType
      */
    public static function dataGetExtra() {
        $expect[0] = array(
            array(
                "text" => "extraTest1",
                "default" => "extraDefaultTest1",
            ),
            array(
                "text" => "extraTest2",
                "default" => "extraDefaultTest2",
            ),
        );
        $expect[1] = array(
            array(
                "text" => "extraTest",
                "default" => "extraDefaultTest",
            ),
        );

        return array(
            array(0x100, "testSensor3", $expect[0], "testSensor3"),
            array(0x100, "testSensor1", $expect[1], "testSensor1"),
            array(0x100, "testSensor2", array(), "testSensor2"),
            array(0x400, "testSensor3", array(), "testSensor3"),
        );
    }
    /**
     * @dataProvider dataGetExtra
      */
    public function testGetExtra($type, $sensor, $expect, $sensorExpect) {
        $o = new sensor();
        $o->registerSensor("testSensor");
        $ret = $o->getExtra($type, $sensor);
        $this->assertSame($expect, $ret);
        // Sensor is passed by reference so it can change.
        $this->assertSame($sensorExpect, $sensor, "Sensor did not match");
    }
    /**
     * Data provider for testGetUnitType
      */
    public static function dataGetUnitType() {
        return array(
            array(0x100, "testSensor3", "Test", "testSensor3"),
            array(0x400, "testSensor3", null, "testSensor3"),
        );
    }
    /**
     * @dataProvider dataGetUnitType
      */
    public function testGetUnitType($type, $sensor, $expect, $sensorExpect) {
        $o = new sensor();
        $o->registerSensor("testSensor");
        $ret = $o->getUnitType($type, $sensor);
        $this->assertSame($expect, $ret);
        // Sensor is passed by reference so it can change.
        $this->assertSame($sensorExpect, $sensor, "Sensor did not match");
    }
    /**
     * Data provider for testGetSize
      */
    public static function dataGetSize() {
        return array(
            array(0x100, "testSensor3", 3),
            array(0x100, "testSensor1", 1),
            array(0x400, "testSensor3", 1),
        );
    }
    /**
     * @dataProvider dataGetSize
      */
    public function testGetSize($type, $sensor, $expect) {
        $o = new sensor();
        $o->registerSensor("testSensor");
        $ret = $o->getSize($type, $sensor);
        $this->assertSame($expect, $ret);
    }

    /**
     *  Data provider for testDoTotal
      */
    public static function dataDoTotal() {
        return array(
            array(0x100, "testSensor3", true),
            array(0x100, "testSensor1", false),
            array(0x400, "testSensor3", false),
        );
    }
    /**
     * @dataProvider dataDoTotal
      */
    public function testDoTotal($type, $sensor, $expect) {
        $o = new sensor();
        $o->registerSensor("testSensor");
        $ret = $o->doTotal($type, $sensor);
        $this->assertSame($expect, $ret);
    }
    /**
     * Data provider for testGetUnitMode
      */
    public static function dataGetUnitMode() {
        return array(
            array("A", "raw", "ignore", "testSensor2", 0x100, "testSensor2"), // Wrong unit for this sensor
            array("A", "raw", "raw", "testSensor1", 0x100, "testSensor1"),
            array("B", "raw", "diff", "testSensor1", 0x100, "testSensor1"),
            array("B", "diff", "diff", "testSensor1", 0x100, "testSensor1"),
            array(null, false, array("A"=>array("raw", "diff")), "testSensor3", 0x100, "testSensor3"),
            array("A", false, array("raw", "diff"), "testSensor3", 0x100, "testSensor3"),
            array("A", "ignore", "ignore", "testSensor3", 0x100, "testSensor3"),
        );
    }
    /**
     * @dataProvider dataGetUnitMode
      */
    public function testGetUnitMode($unit, $mode, $expect, $sensor, $type, $sensorExpect) {
        $o = new sensor();
        $o->registerSensor("testSensor");
        $ret = $o->getUnitMode($type, $sensor, $unit, $mode);
        $this->assertSame($expect, $ret);
        // Sensor is passed by reference so it can change.
        $this->assertSame($sensorExpect, $sensor, "Sensor did not match");

    }

    /**
     * Data provider for testGetUnitDefMode
      */
    public static function dataGetUnitDefMode() {
        return array(
            array(0x100, "testSensor1", "A", "raw", "testSensor1"),
            array(0x100, "testSensor1", "B", "diff", "testSensor1"),
            array(0x100, "testSensor1", null, null, "testSensor1"),
            array(0x100, "testSensorBad", null, null, "testSensor1"),
        );
    }
    /**
     * @dataProvider dataGetUnitDefMode
      */
    public function testGetUnitDefMode($type, $sensor, $unit, $expect, $sensorExpect) {
        $o = new sensor();
        $o->registerSensor("testSensor");
        $ret = $o->getUnitDefMode($type, $sensor, $unit);
        $this->assertSame($expect, $ret);
        // Sensor is passed by reference so it can change.
        $this->assertSame($sensorExpect, $sensor, "Sensor did not match");

    }

    /**
     * Data provider for testGetAllUnits
      */
    public static function dataGetAllUnits() {
        return array(
            array(0x100, "testSensor1", array("A","B","C"), "testSensor1"),
            array(0x100, "testSensor2", array("D","E","F"), "testSensor2"),
            array(0x100, "testSensorBad", array("A","B","C"), "testSensor1"),
            array(0x400, "testSensorBad", array(), "testSensorBad"),
        );
    }
    /**
     * @dataProvider dataGetAllUnits
      */
    public function testGetAllUnits($type, $sensor, $expect, $sensorExpect) {
        $o = new sensor();
        $o->registerSensor("testSensor");
        $ret = $o->getAllUnits($type, $sensor);
        $this->assertSame($expect, $ret);
        // Sensor is passed by reference so it can change.
        $this->assertSame($sensorExpect, $sensor, "Sensor did not match");

    }

    /**
     * Data provider for testGetAllSensors
      */
    public static function dataGetAllSensors() {
        $expect[0] = array(
            "test1" => array(
                "longName" => "Invalid1",
            ),
        );
        $expect[1] = array(
            "test2" => array(
                "longName" => "Invalid2",
            ),
            "test4" => array(
                "longName" => "Invalid4",
            ),
        );

        return array(
            array(0x101, $expect[0]),
            array(0x102, $expect[1]),
            array(0x400, array()),
        );
    }
    /**
     * @dataProvider dataGetAllSensors
      */
    public function testGetAllSensors($type, $expect) {
        $o = new sensor();
        $o->registerSensor("testSensor");
        $o->registerSensor("testSensorExtraSensors");
        $ret = $o->getAllSensors($type);
        $this->assertSame($expect, $ret);

    }

    /**
     * Data provider for testCheckUnits
      */
    public static function dataCheckUnits() {
        return array(
            array(0x100, "testSensor1", "A", "diff", "testSensor1", "A", "diff"),
            array(array(0x100, 0x100), "testSensor1", "A", "diff", array("testSensor1", "testSensor1"), array("B", "B"), array("diff", "diff")),
            array(array(0x100, 0x100), array("testSensor1", "testSensor2"), array("B", "A"), array("raw", "diff"), array("testSensor1", "testSensor2"), array("B", "E"), array("diff", "diff")),
        );
    }
    /**
     * @dataProvider dataCheckUnits
      */
    public function testCheckUnits($type, $sensor, $units, $mode, $expectSensor, $expectUnits, $expectMode) {
        $o = new sensor();
        $o->registerSensor("testSensor");
        $expectType = $type;
        $o->checkUnits($type, $sensor, $units, $mode);
        $this->assertSame($expectType, $type, "Type is not the same");
        $this->assertSame($expectSensor, $sensor, "Sensor is not the same");
        $this->assertSame($expectUnits, $units, "Units is not the same");
        $this->assertSame($expectMode, $mode, "Mode is not the same");
    }

    /**
     * Data provider for testDecodeData
      */
    public static function dataDecodeData() {
        // Run is not used.  It is there so that the particular run is printed.
        // Otherwise we don't know which run failed.  It should be incremented
        // for each test case.
        return array(
            // Test case 1
            // This test case has only one device in it
            array(
                "info" => array(
                    1 => array(
                        "params" => array(
                            "sensorType" => array("testSensor1", "testSensor1", "testSensor2", "testSensor3", "testSensor2"),
                            "Extra" => array(5,4,3,2,1),
                        ),
                    ),
                ),
                "data" => array(
                    array(
                        "Date" => "2007-01-02 03:00:00",
                        "DeviceKey" => 1,
                        "ActiveSensors" => 5,
                        "raw" => array(1,2,3,4,5),
                        "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                        "Units" => array("A", "B", null, "B", "B"),
                        "dType" => array("raw", "diff", "diff", "ignore", "raw"),
                    ),
                    array(
                        "Date" => "2007-01-02 03:10:00",
                        "DeviceKey" => 1,
                        "ActiveSensors" => 5,
                        "raw" => array(2,3,4,5,6),
                        "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                        "Units" => array("A", "B", null, "B", "B"),
                        "dType" => array("raw", "diff", "diff", "ignore", "raw"),
                    ),
                    array(
                        "Date" => "2007-01-02 03:20:00",
                        "DeviceKey" => 1,
                        "ActiveSensors" => 5,
                        "raw" => array(3,5,5,6,7),
                        "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                        "Units" => array("A", "B", null, "B", "B"),
                        "dType" => array("raw", "diff", "diff", "ignore", "raw"),
                    ),
                ),
                "expectInfo" => array(
                    1 => array(
                        "params" => array(
                            "sensorType" => array("testSensor1", "testSensor1", "testSensor2", "testSensor3", "testSensor2"),
                            "Extra" => array(5,4,3,2,1),
                        ),
                    ),
                ),
                "expectData" => array(
                    1 => array(
                        array(
                            "Date" => "2007-01-02 03:00:00",
                            "DeviceKey" => 1,
                            "ActiveSensors" => 5,
                            "raw" => array(1,2,3,4,5),
                            "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                            "Units" => array("A", "B", "E", "B", "B"),
                            "dType" => array("raw", "diff", "raw", "ignore", "raw"),
                            "unitType" => array("Test", "Test", "Test2", "Test"),
                            "Data0" => 5,
                            "data" => array(5, null, 6, 8),
                            "Data2" => 6,
                            "Data3" => 8,
                            "deltaT" => 0,
                            "Status" => "GOOD",
                        ),
                        array(
                            "Date" => "2007-01-02 03:10:00",
                            "DeviceKey" => 1,
                            "ActiveSensors" => 5,
                            "raw" => array(2,3,4,5,6),
                            "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                            "Units" => array("A", "B", "E", "B", "B"),
                            "dType" => array("raw", "diff", "raw", "ignore", "raw"),
                            "unitType" => array("Test", "Test", "Test2", "Test"),
                            "Data0" => 10,
                            "data" => array(10, 4, 8, 10),
                            "deltaT" => 600,
                            "Data1" => 4,
                            "Data2" => 8,
                            "Data3" => 10,
                            "Status" => "GOOD",
                        ),
                        array(
                            "Date" => "2007-01-02 03:20:00",
                            "DeviceKey" => 1,
                            "ActiveSensors" => 5,
                            "raw" => array(3,5,5,6,7),
                            "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                            "Units" => array("A", "B", "E", "B", "B"),
                            "dType" => array("raw", "diff", "raw", "ignore", "raw"),
                            "unitType" => array("Test", "Test", "Test2", "Test"),
                            "Data0" => 15,
                            "data" => array(15, 8, 10, 12),
                            "deltaT" => 600,
                            "Data1" => 8,
                            "Data2" => 10,
                            "Data3" => 12,
                            "Status" => "GOOD",
                        ),
                    ),
                ),
                "run" => 1,
            ),

            // Test case 2
            // This test case has 2 alternating devices in it
            array(
                "info" => array(
                    1 => array(
                        "params" => array(
                            "sensorType" => array("testSensor1", "testSensor1", "testSensor2", "testSensor3", "testSensor2"),
                            "Extra" => array(5,4,3,2,1),
                        ),
                    ),
                    2 => array(
                        "params" => array(
                            "sensorType" => array("testSensor2", "testSensor2", "testSensor1", "testSensor1", "testSensor1"),
                            "Extra" => array(11,12,2,3,4),
                        ),
                    ),
                ),
                "data" => array(
                    array(
                        "Date" => "2007-01-02 03:00:00",
                        "DeviceKey" => 1,
                        "ActiveSensors" => 5,
                        "raw" => array(1,2,3,4,5),
                        "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                        "Units" => array("A", "B", null, "B", "B"),
                        "dType" => array("raw", "diff", "diff", "ignore", "raw"),
                    ),
                    array(
                        "Date" => "2007-01-02 03:00:00",
                        "DeviceKey" => 2,
                        "ActiveSensors" => 5,
                        "raw" => array(11,12,13,14,15),
                        "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                        "Units" => array("E", "E", "E", "B", "B"),
                        "dType" => array("diff", "diff", "diff", "ignore", "raw"),
                    ),
                    array(
                        "Date" => "2007-01-02 03:10:00",
                        "DeviceKey" => 1,
                        "ActiveSensors" => 5,
                        "raw" => array(2,3,4,5,6),
                        "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                        "Units" => array("A", "B", null, "B", "B"),
                        "dType" => array("raw", "diff", "diff", "ignore", "raw"),
                    ),
                    array(
                        "Date" => "2007-01-02 03:10:00",
                        "DeviceKey" => 2,
                        "ActiveSensors" => 5,
                        "raw" => array(12,13,14,25,26),
                        "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                        "Units" => array("E", "E", "E", "B", "B"),
                        "dType" => array("diff", "diff", "diff", "ignore", "raw"),
                    ),
                    array(
                        "Date" => "2007-01-02 03:20:00",
                        "DeviceKey" => 1,
                        "ActiveSensors" => 5,
                        "raw" => array(3,5,5,6,7),
                        "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                        "Units" => array("A", "B", null, "B", "B"),
                        "dType" => array("raw", "diff", "diff", "ignore", "raw"),
                    ),
                    array(
                        "Date" => "2007-01-02 03:20:00",
                        "DeviceKey" => 2,
                        "ActiveSensors" => 5,
                        "raw" => array(13,15,15,16,17),
                        "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                        "Units" => array("E", "E", "E", "B", "B"),
                        "dType" => array("diff", "diff", "diff", "ignore", "raw"),
                    ),
                ),
                "expectInfo" => array(
                    1 => array(
                        "params" => array(
                            "sensorType" => array("testSensor1", "testSensor1", "testSensor2", "testSensor3", "testSensor2"),
                            "Extra" => array(5,4,3,2,1),
                        ),
                    ),
                    2 => array(
                        "params" => array(
                            "sensorType" => array("testSensor2", "testSensor2", "testSensor1", "testSensor1", "testSensor1"),
                            "Extra" => array(11,12,2,3,4),
                        ),
                    ),
                ),
                "expectData" => array(
                    1 => array(
                        array(
                            "Date" => "2007-01-02 03:00:00",
                            "DeviceKey" => 1,
                            "ActiveSensors" => 5,
                            "raw" => array(1,2,3,4,5),
                            "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                            "Units" => array("A", "B", "E", "B", "B"),
                            "dType" => array("raw", "diff", "raw", "ignore", "raw"),
                            "unitType" => array("Test", "Test", "Test2", "Test"),
                            "Data0" => 5,
                            "data" => array(5, null, 6, 8),
                            "Data2" => 6,
                            "Data3" => 8,
                            "deltaT" => 0,
                            "Status" => "GOOD",
                        ),
                        array(
                            "Date" => "2007-01-02 03:10:00",
                            "DeviceKey" => 1,
                            "ActiveSensors" => 5,
                            "raw" => array(2,3,4,5,6),
                            "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                            "Units" => array("A", "B", "E", "B", "B"),
                            "dType" => array("raw", "diff", "raw", "ignore", "raw"),
                            "unitType" => array("Test", "Test", "Test2", "Test"),
                            "Data0" => 10,
                            "data" => array(10, 4, 8, 10),
                            "deltaT" => 600,
                            "Data1" => 4,
                            "Data2" => 8,
                            "Data3" => 10,
                            "Status" => "GOOD",
                        ),
                        array(
                            "Date" => "2007-01-02 03:20:00",
                            "DeviceKey" => 1,
                            "ActiveSensors" => 5,
                            "raw" => array(3,5,5,6,7),
                            "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                            "Units" => array("A", "B", "E", "B", "B"),
                            "dType" => array("raw", "diff", "raw", "ignore", "raw"),
                            "unitType" => array("Test", "Test", "Test2", "Test"),
                            "Data0" => 15,
                            "data" => array(15, 8, 10, 12),
                            "deltaT" => 600,
                            "Data1" => 8,
                            "Data2" => 10,
                            "Data3" => 12,
                            "Status" => "GOOD",
                        ),
                    ),
                    2 => array(
                        array(
                            "Date" => "2007-01-02 03:00:00",
                            "DeviceKey" => 2,
                            "ActiveSensors" => 5,
                            "raw" => array(11,12,13,14,15),
                            "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                            "Units" => array("E", "E", "E", "B", "B"),
                            "dType" => array("raw", "raw", "ignore", "diff", "diff"),
                            "unitType" => array("Test2", "Test2", "Test", "Test", "Test"),
                            "Data0" => 22,
                            "data" => array(22, 24, 26, null, null),
                            "Data1" => 24,
                            "Data2" => 26,
                            "deltaT" => 0,
                            "Status" => "GOOD",
                        ),
                        array(
                            "Date" => "2007-01-02 03:10:00",
                            "DeviceKey" => 2,
                            "ActiveSensors" => 5,
                            "raw" => array(12,13,14,25,26),
                            "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                            "Units" => array("E", "E", "E", "B", "B"),
                            "dType" => array("raw", "raw", "ignore", "diff", "diff"),
                            "unitType" => array("Test2", "Test2", "Test", "Test", "Test"),
                            "Data0" => 24,
                            "data" => array(24, 26, 28, 33, 44),
                            "Data1" => 26,
                            "Data2" => 28,
                            "deltaT" => 600,
                            "Data3" => 33,
                            "Data4" => 44,
                            "Status" => "GOOD",
                        ),
                        array(
                            "Date" => "2007-01-02 03:20:00",
                            "DeviceKey" => 2,
                            "ActiveSensors" => 5,
                            "raw" => array(13,15,15,16,17),
                            "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100),
                            "Units" => array("E", "E", "E", "B", "B"),
                            "dType" => array("raw", "raw", "ignore", "diff", "diff"),
                            "unitType" => array("Test2", "Test2", "Test", "Test", "Test"),
                            "Data0" => 26,
                            "data" => array(26, 30, 30, -27, -36),
                            "Data1" => 30,
                            "Data2" => 30,
                            "deltaT" => 600,
                            "Data3" => -27,
                            "Data4" => -36,
                            "Status" => "GOOD",
                        ),
                    ),
                ),
                "run" => 2,
            ),
  
        );
    }
    /**
     * @dataProvider dataDecodeData
      */
    public function testDecodeData($info, $data, $expectInfo, $expectData, $run) {
        // Run is not used.  It is there so that the particular run is printed.
        // Otherwise we don't know which run failed.  It should be incremented
        // for each test case.
        $o = new sensor();
        $o->registerSensor("testSensor");
        $expectType = $type;
        $newData = array();
        foreach ($data as $d) {
            $o->DecodeData($info[$d['DeviceKey']], $d);
            $newData[$d['DeviceKey']][] = $d;
        }
        $this->assertSame($expectInfo, $info, "Info is not the same");
        $this->assertSame($expectData, $newData, "Data is not the same");
    }


    /**
     * Data provider for testcheckRecord
      */
    public static function datacheckRecord() {
        return array(
            array(
                "data" => array(
                    "ActiveSensors" => 3,
                    "Data0" => 1,
                    "Data1" => 2,
                    "Data2" => 3,
                    "data" => array(1,2,3),
                    "Types" => array(0x100, 0x100, 0x100),
                    "params" => array(
                        "sensorType" => array("testSensor1", "testSensor1", "testSensor2"),
                    ),
                    "Units" => array("A", "B", "D"),
                    "dType" => array("raw", "diff", "raw"),
                ),
                "expect" => array(
                    "ActiveSensors" => 3,
                    "Data0" => 1,
                    "Data1" => 2,
                    "Data2" => null,
                    "data" => array(1,2,null),
                    "Types" => array(0x100, 0x100, 0x100),
                    "params" => array(
                        "sensorType" => array("testSensor1", "testSensor1", "testSensor2"),
                    ),
                    "Units" => array("A", "B", "D"),
                    "dType" => array("raw", "diff", "raw"),
                    "Status" => "GOOD",
                ),
                run => 1,
             ),
        );
    }
    /**
     * @dataProvider datacheckRecord
      */
    public function testcheckRecord($data, $expect, $run) {
        $o = new sensor();
        $o->registerSensor("testSensor");
        $o->checkRecord($data);
        $this->assertSame($expect, $data);
    }


    /**
     * Data provider for testCheckPoint
      */
    public static function dataCheckPoint() {
        return array(
            array(1, 0x100, "testSensor1", "diff", true),
        );
    }
    /**
     * @dataProvider dataCheckPoint
      */
    public function testCheckPoint($value, $type, $sensor, $mode, $expect) {
        $o = new sensor();
        $o->registerSensor("testSensor");
        $ret = $o->checkPoint($value, $type, $sensor, $mode);
        $this->assertSame($expect, $ret);
    }

}

// Call sensorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "sensorTest::main") {
    sensorTest::main();
}

/**
 *  This is a test sensor.  It is not used for anything else.
 */
class testSensor extends sensor_base {
    var $sensors = array(
        0x100 => array(
            "testSensor1" => array(
                "longName" => "Generic Test Sensor 1",
                "unitType" => "Test",
                "validUnits" => array('A', 'B', 'C'),
                "storageUnit" =>  'B',
                "function" => "test1",
                "extraText" => "extraTest",
                "extraDefault" => "extraDefaultTest",
                "unitModes" => array(
                    'A' => 'raw,diff',
                    'B' => 'diff',
                    'C' => 'raw',
                ),
            ),
            "testSensor2" => array(
                "longName" => "Generic Test Sensor 2",
                "unitType" => "Test2",
                "validUnits" => array('D', 'E', 'F'),
                "storageUnit" =>  'E',
                "mult" => 2,
                "function" => "test",
                "checkFunction" => "s2Check",
                "unitModes" => array(
                    'E' => 'raw,diff',
                    'D' => 'diff',
                    'F' => 'raw',
                ),
            ),
            "testSensor3" => array(
                "longName" => "Generic Test Sensor 3",
                "unitType" => "Test",
                "validUnits" => array('A'),
                "storageUnit" =>  'A',
                "function" => "test1",
                "extraText" => array("extraTest1", "extraTest2"),
                "extraDefault" => array("extraDefaultTest1", "extraDefaultTest2"),
                "unitModes" => array(
                    'A' => 'raw,diff',
                ),
                "inputSize" => 3,
                "doTotal" => true,
            ),
        ),
        0x101 => array(
            "test1" => array(
                "longName" => "Invalid1",
            ),
        ),
        0x102 => array(
            "test2" => array(
                "longName" => "Invalid2",
            ),
        ),
        0x103 => array(
            "test3" => array(
                "longName" => "Invalid3",
            ),
        ),
    );

    /**
     * 
     */    
    public function Test1($val, $sensor, $TC, $extra) {
        // This must stay the same. 
        return $val*$extra;
    }
    public function Test2($val, $sensor, $TC, $extra) {
        return $val;
    }

    public function s2Check($val, $sensor, $sensorType, $mode) {
        if ($val == 3) return false;
        return true;
    }

}
/**
 * This class is to test how things handle not having a sensors variable;
 */
class testSensorExtraSensors extends sensor_base {
    var $sensors = array(
        0x102 => array(
            "test4" => array(
                "longName" => "Invalid4",
            ),
        ),
    );
}
/**
 * This class is to test how things handle not having a sensors variable;
 */
class testSensorNoSensors extends sensor_base {
    function __construct() {
        // Make absolutely sure that there are no sensors
        unset($this->sensors);
    }
}
?>
