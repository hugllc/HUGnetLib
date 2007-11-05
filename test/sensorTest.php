<?php
/**
 *   Tests the sensor class
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
/*
        $o = new unitConversion;
        foreach($o->units as $catName => $cat) {
            foreach($cat as $shortName => $unit) {
                // Long Name
                $this->assertTrue(is_string($unit['longName']), $catName.":".$shortName.": Long name is not a string");
                $this->assertThat(strlen($unit['longName']), $this->greaterThan(0), $catName.":".$shortName.": Long name is not a set");            
                // Var Type
                $this->assertTrue(is_string($unit['varType']), $catName.":".$shortName.": Variable type is not a string");
                $this->assertTrue($this->checkvarType($unit['varType']), $catName.":".$shortName.": Variable type '".$unit['varType']."'is not valid");
                // Mode (Mode is not required)
                if (isset($unit['mode'])) {
                    $this->assertTrue(is_string($unit['mode']), $catName.":".$shortName.": Mode is not a string");
                    $this->assertTrue($this->checkMode($unit['mode']), $catName.":".$shortName.": Mode '".$unit['varType']."'is not valid");
                }
                // Mode (Mode is not required)
                if (isset($unit['convert'])) {
                    $this->assertTrue(is_array($unit['convert']), $catName.":".$shortName.": convert is not an array");
                    foreach($unit['convert'] as $to => $function) {
                        $this->assertTrue(method_exists($o, $function), $catName.":".$shortName.": conversion function ".$function." doesn't exist");
                        $this->assertTrue($this->findUnits($o->units, $to), $catName.":".$shortName.": Unit ".$to." doesn't exist");
                    }
                }
            }
        }
*/
    }
    /**
     * @todo Implement testGetReading().
     */
    public function testGetReading() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testRunFunction().
     */
    public function testRunFunction() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetClass().
     */
    public function testGetClass() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetUnits().
     */
    public function testGetUnits() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetExtra().
     */
    public function testGetExtra() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetUnitType().
     */
    public function testGetUnitType() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetSize().
     */
    public function testGetSize() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testDoTotal().
     */
    public function testDoTotal() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetUnitMode().
     */
    public function testGetUnitMode() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetUnitDefMode().
     */
    public function testGetUnitDefMode() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetAllUnits().
     */
    public function testGetAllUnits() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetAllSensors().
     */
    public function testGetAllSensors() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testCheckUnits().
     */
    public function testCheckUnits() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testDecodeData().
     */
    public function testDecodeData() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testCheckRecord().
     */
    public function testCheckRecord() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testCheckPoint().
     */
    public function testCheckPoint() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }
}

// Call sensorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "sensorTest::main") {
    sensorTest::main();
}
?>
