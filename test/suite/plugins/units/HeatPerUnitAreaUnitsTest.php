<?php
/**
 * Tests the light sensor class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** This is a required class */
require_once 'UnitsPluginTestBase.php';
/** This is a required class */
require_once CODE_BASE.'plugins/units/HeatPerUnitAreaUnits.php';

/**
 * Test class for sensor.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:08:37.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class HeatPerUnitAreaUnitsTest extends UnitsPluginTestBase
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
            array("HeatPerUnitAreaUnits"),
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
            array(
                array(
                    "to" => "Btu/hr ft^2",
                    "from" => "W/m^2",
                    "type"  => UnitsBase::TYPE_RAW,
                ),
                1,
                null,
                null,
                0.317,
                true,
            ),
            array(
                array(
                    "type"  => UnitsBase::TYPE_RAW,
                ),
                10,
                "W/m^2",
                "W/m^2",
                10,
                true,
            ),
            array(
                array(
                    "type"  => UnitsBase::TYPE_DIFF,
                ),
                1,
                "Btu/hr ft^2",
                "W/m^2",
                0.317,
                true,
            ),
            array(
                array(
                    "type"  => UnitsBase::TYPE_DIFF,
                ),
                1,
                "W/m^2",
                "Btu/hr ft^2",
                3.154,
                true,
            ),
            // Preferred units
            array(
                array(
                    "from" => "Btu/hr ft^2",
                    "to" => "W/m^2",
                ),
                1,
                null,
                null,
                3.154,
                true,
            ),
            array(
                array(
                ),
                10,
                "badUnit",
                "W/m^2",
                10,
                false,
            ),
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
        $this->o = new HeatPerUnitAreaUnits($preload);
        $ret = $this->o->convert($data, $to, $from);
        $this->assertSame($expect, $data, "The data is wrong");
        $this->assertSame($return, $ret, "Return Wrong");
    }
}

?>
