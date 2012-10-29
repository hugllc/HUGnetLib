<?php
/**
 * Tests the light sensor class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** This is a required class */
require_once 'UnitsPluginTestBase.php';
/** This is a required class */
require_once CODE_BASE.'plugins/units/PercentUnits.php';

/**
 * Test class for sensor.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:08:37.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class PercentUnitsTest extends UnitsPluginTestBase
{

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
        parent::setUp();
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
    * Data provider for testRegisterPlugin
    *
    * @return array
    */
    public static function dataRegisterPlugin()
    {
        return array(
            array("PercentUnits"),
        );
    }

    /**
     * Data provider for testConvert
     *
     * @return array
     */
    public static function dataConvert()
    {
        return array(
            array(array(), 0.1, "%", "decimal", 10.0, true),
            array(array(), 42.13, "decimal", "%", 0.4213, true),
            array(array(), 42.13, "%", "%", 42.13, true),
            array(array(), 0.42, "decimal", "decimal", 0.42, true),
        );
    }
    /**
    * test CtoF()
    *
    * @param array  $preload the stuff to preload into the Units
    * @param mixed  $data    The data to use
    * @param string $to      The units to convert to
    * @param string $from    The units to convert from
    * @param mixed  $expect  The value to expect
    * @param bool   $return  The expected return value
    *
    * @return null
    *
    * @dataProvider dataConvert
    */
    public function testConvert($preload, $data, $to, $from, $expect, $return)
    {
        $this->o = new PercentUnits($preload);
        $ret = $this->o->convert($data, $to, $from);
        $this->assertSame($expect, $data, "The data is wrong");
        $this->assertSame($return, $ret, "Return Wrong");
    }
}

?>