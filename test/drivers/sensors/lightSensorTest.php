<?php
/**
 * Tests the light sensor class
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

// Call lightSensorTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "lightSensorTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../sensorTestBase.php';
require_once dirname(__FILE__).'/../../../drivers/sensors/lightSensor.php';

/**
 * Test class for sensor.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:08:37.
 */
class lightSensorTest extends sensorTestBase {
    var $class = "lightSensor";

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
      */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("lightSensorTest");
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
        return sensorTestBase::sensorArrayDataSource("lightSensor");
    }

    /**
     * Data provider for testGetLight
      */
    public static function dataGetLight() {
        return array(
            array(65472, 1, 0.0), //This is the maximum reading (minimum light)
            array(0, 1, 1500.0),  // This is the minimum reading (maximum light)
            array(65472, 0, 1500.0),  // Woops!  Time constant is 0
        );
    }
    /**
     * @dataProvider dataGetLight
     * @covers lightSensor::GetLight
      */
    public function testGetLight($A, $TC, $expect) {
        $o = new lightSensor();
        $ret = $o->getLight($A, $TC);
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testGetLight
      */
    public static function dataOSRAMBPW34() {
        return array(
            array(65472, array(), 1, array(), 0, 0.0), //This is the maximum reading (minimum light)
            array(0, array(), 1, array(), 0, 1500.0),  // This is the minimum reading (maximum light)
            array(65472, array(), 0, array(), 0, 1500.0),  // Woops!  Time constant is 0
        );
    }
    /**
     * @dataProvider dataOSRAMBPW34
     * @covers lightSensor::OSRAMBPW34
      */
    public function testOSRAMBPW34($A, $sensor, $TC, $extra, $deltaT, $expect) {
        parent::sensorTest("lightSensor", "OSRAMBPW34", $A, $sensor, $TC, $extra, $deltaT, $expect);
    }

}

// Call lightSensorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "lightSensorTest::main") {
    lightSensorTest::main();
}
?>
