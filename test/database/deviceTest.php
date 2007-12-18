<?php
/**
 * Tests the device class
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

// Call deviceTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "deviceTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../../database/device.php';
require_once dirname(__FILE__).'/databaseTest.php';

/**
 * Test class for device.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:06.
 */
class deviceTest extends databaseTest {
    /** The table to use */
    protected $table = "devices";
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
      */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("deviceTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
      */
    protected function setUp() {
        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
      */
    protected function tearDown() {
        parent::tearDown();
    }

    /**
     * @dataProvider dataHealth().
     * @covers driver::testHealth
      */
    public function testHealth() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * data provider dataDiagnose
      */
    public static function dataDiagnose() {
        return array(
        );
    }
    /**
     * @dataProvider dataDiagnose().
     * @covers device::testDiagnose
      */
    public function testDiagnose($Info, $expect) {
        $o = new driver();
        $ret = $o->device->Get_diagnose($Info);
        $this->assertSame($expect, $ret);
    }

    /**
     * @dataProvider dataDiagnose().
     * @covers driver::testDiagnose
      */
    public function testDiagnoseDriver($Info, $expect) {
        $o = new driver();
        $ret = $o->Get_diagnose($Info);
        $this->assertSame($expect, $ret);
    }

    /**
     * @todo Implement testSelectDevice().
      */
    public function testSelectDevice() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetDevice().
      */
    public function testGetDevice() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testUpdateDevice().
      */
    public function testUpdateDevice() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testSetParams().
      */
    public function testSetParams() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testIsController().
      */
    public function testIsController() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     *
      */
    public static function dataEncodeParams() {
        return array(
            array(array("this"=>"is","a"=>"test"), "YToyOntzOjQ6InRoaXMiO3M6MjoiaXMiO3M6MToiYSI7czo0OiJ0ZXN0Ijt9"),
            array("test String", "test String"),
            array(1234, ""),
        );
    }
    /**
     * @dataProvider dataEncodeParams
      */
    public function testEncodeParams($params, $expect) {
        $ret = device::encodeParams($params);
        $this->assertSame($expect, $params, "Input array passed by reference was not modified correctly");
        $this->assertSame($expect, $ret, "return array incorrect");
    }

    /**
     *
      */
    public static function dataDecodeParams() {
        return array(
            array("YToyOntzOjQ6InRoaXMiO3M6MjoiaXMiO3M6MToiYSI7czo0OiJ0ZXN0Ijt9", array("this"=>"is","a"=>"test")),
            array(array("this"=>"is","an"=>"array"),array("this"=>"is","an"=>"array")),
            array(1234, array()),
        );
    }
    /**
     * @dataProvider dataDecodeParams
      */
    public function testDecodeParams($params, $expect) {
        $ret = device::decodeParams($params);
        $this->assertSame($expect, $params, "Input array passed by reference was not modified correctly");
        $this->assertSame($expect, $ret, "return array incorrect");
    }
}

// Call deviceTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "deviceTest::main") {
    deviceTest::main();
}
?>
