<?php
/**
 * Tests the capacitive sensor class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2009 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
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
 * @category   Sensors
 * @package    HUGnetLibTest
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

// Call capacitiveSensorTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "capacitiveSensorTest::main");
}

/** The test case class */
require_once "PHPUnit/Framework/TestCase.php";
/** The test suite class */
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../sensorTestBase.php';
require_once dirname(__FILE__).'/../../../drivers/sensors/capacitiveSensor.php';


/**
 * Test class for sensor.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:08:37.
 *
 * @category   Sensors
 * @package    HUGnetLibTest
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class capacitiveSensorTest extends sensorTestBase
{
    var $class = "capacitiveSensor";

    /**
     * Runs the test methods of this class.
     *
     * @return null
     *
     * @access public
     * @static
     */
    public static function main() 
    {
        include_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("capacitiveSensorTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return null
     *
     * @access protected
     */
    protected function setUp() 
    {
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return null
     *
     * @access protected
     */
    protected function tearDown() 
    {
    }
    
    /**
     * data provider
     *
     * @return array
     */
    public static function dataSensorArray() 
    {
        $o = new capacitiveSensor();
        $return = array();
        foreach ($o->sensors as $catName => $cat) {
            foreach ($cat as $shortName => $sensor) {
                $return[] = array($catName, $shortName, $sensor);
            }
        }
        return $return;
    }

    /**
     * Data provider for testGetCapacitance
     */
    public static function dataGetCapacitance() 
    {
        return array(
            array(1, 2, 3, 4, 174591.3333),
            array(0, 1, 2, 3, 0),
            array(1, 0, 2, 3, 0),
            array(1, 2, 0, 3, 0),
        );
    }
    /**
     * test
     *
     * @return null
     *
     * @dataProvider dataGetCapacitance
     */
    public function testGetCapacitance($A, $T, $R, $t, $expect) 
    {
        $o = new capacitiveSensor();
        $ret = $o->getCapacitance($A, $T, $R, $t);
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testGetCapacitance
     */
    public static function dataGenericCap() 
    {
        return array(
            array(1, 2, 3, 4, 1),
            array(10, 20, 30, 40, 10),
        );
    }
    /**
     * test
     *
     * @return null
     *
     * @dataProvider dataGenericCap
     */
    public function testGenericCap($val, $sensor, $TC, $extra, $expect) 
    {
        $o = new capacitiveSensor();
        $ret = $o->genericCap($val, $sensor, $TC, $extra);
        $this->assertSame($expect, $ret);
    }


}
// Call capacitiveSensorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "capacitiveSensorTest::main") {
    capacitiveSensorTest::main();
}
?>
