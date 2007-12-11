<?php
/**
 * Tests the eDEFAULT endpoint class
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

// Call e00392800Test::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "eDEFAULTTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../endpointTestBase.php';
require_once dirname(__FILE__).'/../../../drivers/endpoints/eDEFAULT.php';

/**
 * Test class for endpoints.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:06:08.
 */
class eDEFAULTTest extends endpointTestBase {
    public $class = "eDEFAULT";
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
      */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("eDEFAULTTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testConfigArray() {
        // Do nothing here.  This test is not valid for the default driver
    }
    public function testreadConfig() {
        // Do nothing here.  This test is not valid for the default driver
    }
    public function testDevicesArray() {
        // Do nothing here.  This test is not valid for the default driver
    }
    public function testinterpConfig() {
        // Do nothing here.  This test is not valid for the default driver
    }
    public function testinterpSensors() {
        // Do nothing here.  This test is not valid for the default driver
    }
    public static function dataConfigArray() {
        return parent::dataConfigArray("eDEFAULT");
    }

    /**
     * data provider for testCheckDataArray
      */
    public static function dataCheckDataArray() {
        return array(
            array(
                array("RawData" => "000102030405060708090A0B0C0D0E0F"),
                array("RawData" => "000102030405060708090A0B0C0D0E0F", "Data" => array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15)),
            ),
        );
    }
    /**
     * @dataProvider dataCheckDataArray()
      */
    function testcheckDataArray($work, $expect) {
        $this->o->drivers[$this->class]->checkDataArray($work);
        $this->assertSame($expect, $work);
    }


    /**
     * @todo implement testGetCols()
      */
    function testGetCols() {
        $Info = array();
        $cols = $this->o->drivers[$this->class]->getCols($Info);
        $this->assertType("array", $cols, "Return must be an array");
        foreach ($cols as $key => $val) {
            $this->assertType("string", $key, "Array key must be an string");                
            $this->assertType("string", $val, "Array value must be an string");                
        }
    }

    /**
     * @todo implement testGetEditCols()
      */
    function testGetEditCols() {
        $Info = array();
        $cols = $this->o->drivers[$this->class]->getEditCols($Info);
        $this->assertType("array", $cols, "Return must be an array");
        foreach ($cols as $key => $val) {
            $this->assertType("string", $key, "Array key must be an string");                
            $this->assertType("string", $val, "Array value must be an string");                
        }
    }

    /**
     * data provider for testCompareFWVesrion
      */
    public static function dataCompareFWVersion() {
        return array(
            array("1.2.3", "1.2.3", 0),
            array("1.2.4", "1.2.3", 1),
            array("1.3.3", "1.2.3", 1),
            array("2.2.3", "1.2.3", 1),
            array("1.2.3", "1.2.4", -1),
            array("1.2.3", "1.3.3", -1),
            array("1.2.3", "2.2.3", -1),
        );
    }
    /**
     * @dataProvider dataCompareFWVersion
      */
    function testCompareFWVersion($v1, $v2, $expect) {
        $ret = $this->o->drivers[$this->class]->CompareFWVersion($v1, $v2);
        $this->assertEquals($expect, $ret);
    }        

    /**
     * @dataProvider datacheckRecord()
      */
    function testcheckRecord() {
        $Rec = array();
        $this->o->drivers[$this->class]->checkRecord(array(), $Rec);
        $this->assertSame(array("Status" => "UNRELIABLE"), $Rec);
    }

    /**
     * data provider for testGetHistoryTable
      */
    public static function dataGetHistoryTable() {
        return array(
            array("testDriver", "testhistory"),
        );
    }
    /**
     * @dataProvider dataGetHistoryTable().
     * @covers driver::GetHistoryTable
      */
    public function testGetHistoryTable($driver, $table) {
        $Info = array("Driver" => $driver);
        
        $this->o->registerDriver($driver);
        $this->assertSame($table, $this->o->getHistoryTable($Info));
    }

    /**
     * data provider for testGetHistoryTable
      */
    public static function dataGetAverageTable() {
        return array(
            array("testDriver", "testaverage"),
        );
    }
    /**
     * @dataProvider dataGetAverageTable().
      */
    public function testGetAverageTable($driver, $table) {
        $Info = array("Driver" => $driver);
        
        $this->o->registerDriver($driver);
        $this->assertSame($table, $this->o->getAverageTable($Info));
    }

    /**
     * data provider for testGetLocationTable
      */
    public static function dataGetLocationTable() {
        return array(
            array("testDriver", "testlocation"),
        );
    }
    /**
     * @dataProvider dataGetLocationTable().
      */
    public function testGetLocationTable($driver, $table) {
        $Info = array("Driver" => $driver);
        
        $this->o->registerDriver($driver);
        $this->assertSame($table, $this->o->getLocationTable($Info));
    }


}

// Call eDEFAULTTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "eDEFAULTTest::main") {
    eDEFAULTTest::main();
}

?>
