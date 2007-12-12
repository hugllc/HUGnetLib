<?php
/**
 * Tests the median filter class
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

// Call medianFilterTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "medianFilterTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../filterTestBase.php';
require_once dirname(__FILE__).'/../../../drivers/filters/medianFilter.php';

/**
 * Test class for medianFilter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:09:44.
 */
class medianFilterTest extends filterTestBase {
    public $class = "medianFilter";
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
      */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("medianFilterTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
      */
    protected function setUp() {
        $this->o = new MedianFilter();
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
     * data provider for dataFilterVariable* 
      */
    public static function dataFilterVariable() {
        return parent::filterArrayDataSource("MedianFilter");
    }

    public static function dataMedian() {
        return array(
            array(
                array(
                    array("Date" => "2007-12-12 4:00:05", "Data0" => 5, "Data1" => 2, "data" => array(5,2)),
                    array("Date" => "2007-12-12 4:05:05", "Data0" => 6, "Data1" => 3, "data" => array(6,3)),
                    array("Date" => "2007-12-12 4:10:05", "Data0" => 7, "Data1" => 4, "data" => array(7,4)),
                    array("Date" => "2007-12-12 4:15:05", "Data0" => 8, "Data1" => 5, "data" => array(8,5)),
                    array("Date" => "2007-12-12 4:20:05", "Data0" => 9, "Data1" => 6, "data" => array(9,6)),
                    array("Date" => "2007-12-12 4:25:05", "Data0" => 10, "Data1" => 20, "data" => array(10,20)),
                    array("Date" => "2007-12-12 4:30:05", "Data0" => 11, "Data1" => 8, "data" => array(11,8)),
                    array("Date" => "2007-12-12 4:35:05", "Data0" => 12, "Data1" => 9, "data" => array(12,9)),
                    array("Date" => "2007-12-12 4:40:05", "Data0" => 13, "Data1" => 10, "data" => array(13,10)),
                    array("Date" => "2007-12-12 4:45:05", "Data0" => 14, "Data1" => 2, "data" => array(14,2)),
                    array("Date" => "2007-12-12 4:50:05", "Data0" => 15, "Data1" => 12, "data" => array(15,12)),
                ),                                      // $history
                1,                                      // $index
                array("extraDefault" => array(3, 1)),   // $filter
                array(),                                // $extra
                300,                                    // $deltaT
                array(
                    array("Date" => "2007-12-12 4:00:05", "Data0" => 5, "Data1" => 2, "data" => array(5,2)),
                    array("Date" => "2007-12-12 4:05:05", "Data0" => 6, "Data1" => 3, "data" => array(6,3)),
                    array("Date" => "2007-12-12 4:10:05", "Data0" => 7, "Data1" => 4, "data" => array(7,4)),
                    array("Date" => "2007-12-12 4:15:05", "Data0" => 8, "Data1" => 5, "data" => array(8,5)),
                    array("Date" => "2007-12-12 4:20:05", "Data0" => 9, "Data1" => 6, "data" => array(9,6)),
                    array("Date" => "2007-12-12 4:25:05", "Data0" => 10, "Data1" => 8, "data" => array(10,8)),
                    array("Date" => "2007-12-12 4:30:05", "Data0" => 11, "Data1" => 8, "data" => array(11,8)),
                    array("Date" => "2007-12-12 4:35:05", "Data0" => 12, "Data1" => 9, "data" => array(12,9)),
                    array("Date" => "2007-12-12 4:40:05", "Data0" => 13, "Data1" => 9, "data" => array(13,9)),
                    array("Date" => "2007-12-12 4:45:05", "Data0" => 14, "Data1" => 9, "data" => array(14,9)),
                    array("Date" => "2007-12-12 4:50:05", "Data0" => 15, "Data1" => 12, "data" => array(15,12)),
                ),                                      // $expect                
            ),
            array(
                array(
                    array("Date" => "2007-12-12 4:00:05", "Data0" => 5, "Data1" => 2, "data" => array(5,2)),
                    array("Date" => "2007-12-12 4:05:05", "Data0" => 6, "Data1" => 3, "data" => array(6,3)),
                    array("Date" => "2007-12-12 4:10:05", "Data0" => 7, "Data1" => 4, "data" => array(7,4)),
                    array("Date" => "2007-12-12 4:15:05", "Data0" => 8, "Data1" => 5, "data" => array(8,5)),
                    array("Date" => "2007-12-12 4:20:05", "Data0" => 9, "Data1" => 6, "data" => array(9,6)),
                    array("Date" => "2007-12-12 4:25:05", "Data0" => 10, "Data1" => 20, "data" => array(10,20)),
                    array("Date" => "2007-12-12 4:30:05", "Data0" => 11, "Data1" => 8, "data" => array(11,8)),
                    array("Date" => "2007-12-12 4:35:05", "Data0" => 12, "Data1" => 9, "data" => array(12,9)),
                    array("Date" => "2007-12-12 4:40:05", "Data0" => 13, "Data1" => 2, "data" => array(13,2)),
                    array("Date" => "2007-12-12 4:45:05", "Data0" => 14, "Data1" => 11, "data" => array(14,11)),
                    array("Date" => "2007-12-12 4:50:05", "Data0" => 15, "Data1" => 12, "data" => array(15,12)),
                ),                                      // $history
                1,                                      // $index
                array("extraDefault" => array(7, 5)),   // $filter
                array(3, 1),                            // $extra
                300,                                    // $deltaT
                array(
                    array("Date" => "2007-12-12 4:00:05", "Data0" => 5, "Data1" => 2, "data" => array(5,2)),
                    array("Date" => "2007-12-12 4:05:05", "Data0" => 6, "Data1" => 3, "data" => array(6,3)),
                    array("Date" => "2007-12-12 4:10:05", "Data0" => 7, "Data1" => 4, "data" => array(7,4)),
                    array("Date" => "2007-12-12 4:15:05", "Data0" => 8, "Data1" => 5, "data" => array(8,5)),
                    array("Date" => "2007-12-12 4:20:05", "Data0" => 9, "Data1" => 6, "data" => array(9,6)),
                    array("Date" => "2007-12-12 4:25:05", "Data0" => 10, "Data1" => 8, "data" => array(10,8)),
                    array("Date" => "2007-12-12 4:30:05", "Data0" => 11, "Data1" => 8, "data" => array(11,8)),
                    array("Date" => "2007-12-12 4:35:05", "Data0" => 12, "Data1" => 8, "data" => array(12,8)),
                    array("Date" => "2007-12-12 4:40:05", "Data0" => 13, "Data1" => 8, "data" => array(13,8)),
                    array("Date" => "2007-12-12 4:45:05", "Data0" => 14, "Data1" => 11, "data" => array(14,11)),
                    array("Date" => "2007-12-12 4:50:05", "Data0" => 15, "Data1" => 12, "data" => array(15,12)),
                ),                                      // $expect                
            ),
        );
    }

    /**
     * @dataProvider dataMedian
     */
    public function testMedianHistory($history, $index, $filter, $extra, $deltaT, $expect) {
        $ret = $this->o->median($history, $index, $filter, $extra, $deltaT);
        $this->assertSame($expect, $history);
    }

    /**
     * @dataProvider dataMedian
     */
    public function testMedianReturn($history, $index, $filter, $extra, $deltaT, $expect) {
        $ret = $this->o->median($history, $index, $filter, $extra, $deltaT);
        $this->assertSame($expect, $history, "history is wrong");
        $this->assertSame($expect, $ret);
    }

}

// Call medianFilterTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "medianFilterTest::main") {
    medianFilterTest::main();
}
?>
