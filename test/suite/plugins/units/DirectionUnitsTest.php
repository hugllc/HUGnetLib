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
require_once CODE_BASE.'plugins/units/DirectionUnits.php';

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
class DirectionUnitsTest extends UnitsPluginTestBase
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
            array("DirectionUnits"),
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
            array(array(), 0, "Direction", "&#176;", "N", true),
            array(array(), 22.5, "Direction", "&#176;", "NNE", true),
            array(array(), 45, "Direction", "&#176;", "NE", true),
            array(array(), 67.5, "Direction", "&#176;", "ENE", true),
            array(array(), 90, "Direction", "&#176;", "E", true),
            array(array(), 112.5, "Direction", "&#176;", "ESE", true),
            array(array(), 135, "Direction", "&#176;", "SE", true),
            array(array(), 157.5, "Direction", "&#176;", "SSE", true),
            array(array(), 180, "Direction", "&#176;", "S", true),
            array(array(), 202.5, "Direction", "&#176;", "SSW", true),
            array(array(), 225, "Direction", "&#176;", "SW", true),
            array(array(), 247.5, "Direction", "&#176;", "WSW", true),
            array(array(), 270, "Direction", "&#176;", "W", true),
            array(array(), 292.5, "Direction", "&#176;", "WNW", true),
            array(array(), 315, "Direction", "&#176;", "NW", true),
            array(array(), 337.5, "Direction", "&#176;", "NNW", true),
            array(array(), 900, "Direction", "&#176;", "N", true),

            array(array(), "N", "&#176;", "Direction", 0, true),
            array(array(), "NNE", "&#176;", "Direction", 22.5, true),
            array(array(), "NE", "&#176;", "Direction", 45, true),
            array(array(), "ENE", "&#176;", "Direction", 67.5, true),
            array(array(), "E", "&#176;", "Direction", 90, true),
            array(array(), "ESE", "&#176;", "Direction", 112.5, true),
            array(array(), "SE", "&#176;", "Direction", 135, true),
            array(array(), "SSE", "&#176;", "Direction", 157.5, true),
            array(array(), "S", "&#176;", "Direction", 180, true),
            array(array(), "SSW", "&#176;", "Direction", 202.5, true),
            array(array(), "SW", "&#176;", "Direction", 225, true),
            array(array(), "WSW", "&#176;", "Direction", 247.5, true),
            array(array(), "W", "&#176;", "Direction", 270, true),
            array(array(), "WNW", "&#176;", "Direction", 292.5, true),
            array(array(), "NW", "&#176;", "Direction", 315, true),
            array(array(), "NNW", "&#176;", "Direction", 337.5, true),
            array(array(), "ASDF", "&#176;", "Direction", 0, true),

            array(array(), 0, "&#176;", "&#176;", 0, true),
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
        $this->o = new DirectionUnits($preload);
        $ret = $this->o->convert($data, $to, $from);
        $this->assertSame($expect, $data, "The data is wrong");
        $this->assertSame($return, $ret, "Return Wrong");
    }
    /**
     * Data provider for testValid
     *
     * @return array
     */
    public static function dataNumeric()
    {
        return array(
            array(
                array(
                    "to" => "&#176;",
                    "from" => "Direction",
                    "type"  => UnitsBase::TYPE_RAW,
                ),
                "Direction",
                false,
            ),
            array(
                array(
                    "to" => "Direction",
                    "from" => "&#176;",
                    "type"  => UnitsBase::TYPE_DIFF,
                ),
                "&#176;",
                true,
            ),
            array(
                array(
                    "to" => "&#176;",
                    "from" => "&#176;",
                    "type"  => UnitsBase::TYPE_RAW,
                ),
                "&#176;Q",
                false,
            ),
        );
    }
    /**
    * test CtoF()
    *
    * @param array $preload The stuff to preload into the Units
    * @param mixed $units   The units to check
    * @param mixed $expect  The value to expect
    *
    * @return null
    *
    * @dataProvider dataNumeric
    */
    public function testNumeric($preload, $units, $expect)
    {
        $this->o = new DirectionUnits($preload);
        $this->assertSame($expect, $this->o->numeric($units));
    }

}

?>
