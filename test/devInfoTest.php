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
 *   @version $Id: devInfoTest.php 442 2007-11-12 23:03:55Z prices $
 *
 */

// Call devInfoTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "devInfoTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../devInfo.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 */
class devInfoTest extends PHPUnit_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("devInfoTest");
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

    public static function dataDeviceID() {
        return array(
            array(array("DeviceID" => "12345", "From" => "54321"), array("DeviceID" => "012345", "From" => "54321")),
            array(array("From" => "12345"), array("From" => "12345", "DeviceID" => "012345")),
            array(array("PacketFrom" => "12345"), array("PacketFrom" => "12345", "DeviceID" => "012345")),
        );
    }

    /**
     * @dataProvider dataDeviceID
     */
    public function testDeviceID($Info, $expect) {
        $ret = devInfo::DeviceID($Info);
        $this->assertSame($expect, $Info, "Info modified incorrectly");
        $this->assertSame($expect["DeviceID"], $ret, "Return not correct");
    }

    public static function dataRawData() {
        return array(
            array(array("RawData" => "12345", "Data" => "54321"), array("RawData" => "12345", "Data" => "54321")),
            array(array("Data" => "12345"), array("Data" => "12345", "RawData" => "12345")),
            array(array("rawdata" => "12345"), array("rawdata" => "12345", "RawData" => "12345")),
            array(array("RawSetup" => "12345"), array("RawSetup" => "12345", "RawData" => "12345")),
        );
    }

    /**
     * @dataProvider dataRawData
     */
    public function testRawData($Info, $expect) {
        $ret = devInfo::RawData($Info);
        $this->assertSame($expect, $Info, "Info modified incorrectly");
        $this->assertSame($expect["RawData"], $ret, "Return not correct");
    }

    public static function dataSetDate() {
        return array(
            array(array("Date" => "2007-11-27 12:23:47"), "LastConfig", array("Date" => "2007-11-27 12:23:47", "LastConfig" => "2007-11-27 12:23:47")),
            array(array("Date" => "2007-11-27 12:23:47", "LastConfig" => "2007-11-20 12:23:47"), "LastConfig", array("Date" => "2007-11-27 12:23:47", "LastConfig" => "2007-11-27 12:23:47")),
        );
    }

    /**
     * @dataProvider dataSetDate
     */
    public function testSetDate($Info, $Field, $expect) {
        $ret = devInfo::SetDate($Info, $Field);
        $this->assertSame($expect, $Info, "Info modified incorrectly");
        $this->assertSame($expect[$Field], $ret, "Return not correct");
        $this->assertRegExp("/[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]/", $Info[$Field]);
    }

    /**
     * Tests the date as it is returned if no date is in Info
     */
    public function testSetDateNotGiven() {
        $Info = array();
        $Field = "LastConfig";
        $ret = devInfo::SetDate($Info, $Field);
        $this->assertRegExp("/[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]/", $Info[$Field]);
    }


    public static function dataSetStringSize() {
        return array(
            array("", 6, "0", "000000"),
            array("12345678", 5, "0", "45678"),
            array("12", 4, NULL, "0012"),
        );
    }

    /**
     * @dataProvider dataSetStringSize
     */
    public function testSetStringSize($value, $size, $pad, $expect) {
        if (is_null($pad)) {
            devInfo::setStringSize($value, $size);
        } else {
            devInfo::setStringSize($value, $size, $pad);
        }
        $this->assertSame($expect, $value);
    }   

    public static function dataHexifyPartNum() {
        return array(
            array("0039-20-06-C", "0039200643"),
        );
    }

    /**
     * @dataProvider dataHexifyPartNum
     */
    public function testHexifyPartNum($partNum, $expect) {
        $value = devInfo::hexifyPartNum($partNum);
        $this->assertSame($expect, $value);
    }   



}

// Call devInfoTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "devInfoTest::main") {
    devInfoTest::main();
}

?>
