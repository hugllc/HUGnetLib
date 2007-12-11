<?php
/**
 * Tests the resistive sensor class
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

// Call resistiveSensorTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "resistiveSensorTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../sensorTestBase.php';
require_once dirname(__FILE__).'/../../../drivers/sensors/resistiveSensor.php';

/**
 * Test class for sensor.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:08:37.
 */
class resistiveSensorTest extends sensorTestBase {
    var $class = "resistiveSensor";
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
      */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("resistiveSensorTest");
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
     * data provider for testSensorArray*
     */    
    public static function dataSensorArray() {
        return parent::sensorArrayDataSource("resistiveSensor");
    }

    /**
     * Data provider for testGetResistance
      */
    public static function dataGetResistance() {
        return array(
            array(0, 0, 1, 0.0),
            array(10000, 1, 10, 1.8027),
        );
    }
    /**
     * @dataProvider dataGetResistance
     * @covers resistiveSensor::GetResistance
      */
    public function testGetResistance($A, $TC, $Bias, $expect) {
        $o = new resistiveSensor();
        $ret = $o->getResistance($A, $TC, $Bias);
        $this->assertSame($expect, $ret);
    }
    /**
     * Data provider for testBCTherm2381_640_66103
      */
    public static function dataBCTherm2381_640_66103() {
        return array(
            array(63570, array('extraDefault'=>array(10, 10)), 1, array(10, 10), 0, null), // -40.1 degrees
            array(1150, array('extraDefault'=>array(10, 10)), 1, array(10, 10), 0, null),  // 150.9 degrees
            array(5000, array('extraDefault'=>array(10, 10)), 1, array(10, 10), 0, 93.3105),
            array(5000, array('extraDefault'=>array(10, 10)), 1, array(0, 10), 0, 93.3105),
            array(5000, array('extraDefault'=>array(10, 10)), 1, array(10, 0), 0, 93.3105),
            array(5000, array('extraDefault'=>array(0, 0)), 1, array(0, 0), 0, null),
        );
    }
    /**
     * @dataProvider dataBCTherm2381_640_66103
     * @covers resistiveSensor::BCTherm2381_640_66103
     * @covers resistiveSensor::BCTherm2322640Interpolate
      */
    public function testBCTherm2381_640_66103($A, $sensor, $TC, $extra, $deltaT, $expect) {
        parent::sensorTest("resistiveSensor", "BCTherm2381_640_66103", $A, $sensor, $TC, $extra, $deltaT, $expect);
    }

    /**
     * Data provider for testResisDoor
      */
    public static function dataResisDoor() {
        return array(
            array(5000, array('extraDefault'=>array(10, 10, 10)), 1, array(10, 10, 10), 0, null), // R < Fixed
            array(50000, array('extraDefault'=>array(10, 10, 10)), 1, array(10, 10, 10), 0, null), // % > 100
            array(40000, array('extraDefault'=>array(10, 10, 10)), 1, array(10, 10, 10), 0, 57.04), 
            array(40001, array('extraDefault'=>array(10, 10, 10)), 1, array(0, 10, 10), 0, 57.04),
            array(40002, array('extraDefault'=>array(10, 10, 10)), 1, array(10, 0, 10), 0, 57.06),
            array(40003, array('extraDefault'=>array(10, 10, 10)), 1, array(10, 10, 0), 0, 57.07),
            array(40004, array('extraDefault'=>array(0, 0, 0)), 1, array(0, 10, 10), 0, null),
            array(40005, array('extraDefault'=>array(0, 0, 0)), 1, array(10, 0, 10), 0, null),
            array(40006, array('extraDefault'=>array(0, 0, 0)), 1, array(10, 10, 0), 0, null),
        );
    }
    /**
     * @dataProvider dataResisDoor
     * @covers resistiveSensor::ResisDoor
      */
    public function testResisDoor($A, $sensor, $TC, $extra, $deltaT, $expect) {
        parent::sensorTest("resistiveSensor", "resisDoor", $A, $sensor, $TC, $extra, $deltaT, $expect);
    }

    /**
     * Data provider for testgetMoistureV2
      */
    public static function datagetMoistureV2() {
        return array(
            array(40000, array('extraDefault'=>array(1000, 10, 1000)), 1, array(1000, 10, 1000), 0, 1570.3518), 
            array(40001, array('extraDefault'=>array(1000, 10, 1000)), 1, array(0, 10, 1000), 0, 1570.4527),
            array(40002, array('extraDefault'=>array(1000, 10, 1000)), 1, array(1000, 0, 1000), 0, 1570.5536),
            array(40003, array('extraDefault'=>array(1000, 10, 1000)), 1, array(1000, 10, 0), 0, 1570.6545),
            array(40006, array('extraDefault'=>array(0, 0, 0)), 1, array(1000, 10, 0), 0, null),
        );
    }
    /**
     * @dataProvider datagetMoistureV2
     * @covers resistiveSensor::GetMoistureV2
      */
    public function testgetMoistureV2($A, $sensor, $TC, $extra, $deltaT, $expect) {
        parent::sensorTest("resistiveSensor", "getMoistureV2", $A, $sensor, $TC, $extra, $deltaT, $expect);
    }

    /**
     * Data provider for testgetMoistureV1
      */
    public static function datagetMoistureV1() {
        return array(
            array(4000, array('extraDefault'=>array(1000, 10, 1000)), 1, array(1000, 10, 1000), 0, 6.56), 
            array(4001, array('extraDefault'=>array(1000, 10, 1000)), 1, array(0, 10, 1000), 0, 6.56),
            array(4002, array('extraDefault'=>array(1000, 10, 1000)), 1, array(1000, 0, 1000), 0, 6.56),
            array(4003, array('extraDefault'=>array(1000, 10, 1000)), 1, array(1000, 10, 0), 0, 6.56),
            array(4006, array('extraDefault'=>array(0, 0, 0)), 1, array(0, 0, 0), 0, null),
            array(0, array('extraDefault'=>array(1000, 10, 1000)), 1, array(1000, 10, 1000), 0, 35.0), 
        );
    }
    /**
     * @dataProvider datagetMoistureV1
     * @covers resistiveSensor::GetMoistureV1
      */
    public function testgetMoistureV1($A, $sensor, $TC, $extra, $deltaT, $expect) {
        parent::sensorTest("resistiveSensor", "getMoistureV1", $A, $sensor, $TC, $extra, $deltaT, $expect);
    }

}

// Call resistiveSensorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "resistiveSensorTest::main") {
    resistiveSensorTest::main();
}
?>
