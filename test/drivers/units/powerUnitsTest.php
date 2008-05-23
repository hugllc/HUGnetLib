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
 * @category   Sensors
 * @package    HUGnetLibTest
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


/** The test case class */
require_once "PHPUnit/Framework/TestCase.php";
/** The test suite class */
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../unitTestBase.php';
require_once dirname(__FILE__).'/../../../drivers/units/powerUnits.php';

/**
 * Test class for sensor.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:08:37.
 *
 * @category   Sensors
 * @package    HUGnetLibTest
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class powerUnitsTest extends UnitTestBase
{
    var $class = "powerUnits";

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

        $suite  = new PHPUnit_Framework_TestSuite("powerUnitsTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    /**
     * data provider for testUnitArrayLongName, testUnitArrayVarType,
     *
     * @return array
     */
    public static function dataUnitArray() 
    {
        return self::getDataUnitArray("powerUnits");
    }
    /**
     * data provider for testUnitArrayConvertFunct
     *
     * @return array
     */
    public static function dataUnitArrayConvertFunct() 
    {
        return self::getDataUnitArrayConvertFunct("powerUnits");
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
        $this->o = new powerUnits();
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
     * Data provider for testFindUnit
     *
     * @return array
     */
    public static function datakWhTokW() 
    {
        return array(
            array(100, 0, "diff", 0, null),
            array(100, 0, "raw", 0, null),
            array(1, 360, "diff", 0, 10),
            array(100, -360, "diff", 0, 1000),
        );
    }
    /**
     * Test kWhTokW
     *
     * @param float  $val    The input value
     * @param int    $time   The time in seconds between this record and the last.
     * @param string $type   The type of data (diff, raw, etc)
     * @param mixed  $extra  The extra information from the sensor.
     * @param float  $expect The extra information from the sensor.
     *
     * @return null
     *
     * @dataProvider datakWhTokW
     */
    public function testkWhTokW ($val, $time, $type, $extra, $expect) 
    {
        $this->assertEquals($expect, $this->o->kWhTokW($val, $time, $type, $extra));
    }

    /**
     * Data provider for testFindUnit
     *
     * @return array
     */
    public static function datakWhToW() 
    {
        return array(
            array(100, 0, "diff", 0, null),
            array(100, 0, "raw", 0, null),
            array(1, 360, "diff", 0, 10000),
            array(100, -360, "diff", 0, 1000000),
        );
    }
    /**
     * Test kWhToW
     *
     * @param float  $val    The input value
     * @param int    $time   The time in seconds between this record and the last.
     * @param string $type   The type of data (diff, raw, etc)
     * @param mixed  $extra  The extra information from the sensor.
     * @param float  $expect The extra information from the sensor.
     *
     * @return null
     *
     * @dataProvider datakWhToW
     */
    public function testkWhToW ($val, $time, $type, $extra, $expect) 
    {
        $this->assertEquals($expect, $this->o->kWhToW($val, $time, $type, $extra));
    }

}
?>
