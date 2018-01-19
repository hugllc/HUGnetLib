<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\datachan\drivers;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBase.php";
/** This is a required class */
require_once CODE_BASE.'devices/datachan/drivers/Temperature.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class TemperatureTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "Temperature";
    /** This is the units that are valid */
    protected static $units = array('&#176;F', '&#176;C', 'K');
    /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    *
    * @access protected
    *
    * @return null
    */
    protected function setUp()
    {
        parent::setUp();
        $this->o = \HUGnet\devices\datachan\Driver::factory(
            "Temperature", "&#176;F"
        );
    }

    /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    *
    * @access protected
    *
    * @return null
    */
    protected function tearDown()
    {
        parent::tearDown();
    }
    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataConversions()
    {
        return self::dataConversionsData();
    }
    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataGetValid()
    {
        return array(
            array(
                "&deg;C",
                array(
                    '&#176;F' => '&#176;F',
                    '&#176;C' => '&#176;C',
                    'K' => 'K',
                ),
            ),
        );
    }

    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataValid()
    {
        return array(
            array(
                "&#176;C",
                "&#176;C",
                true,
            ),
            array(
                "&#176;C",
                "psi",
                false,
            ),
        );
    }
    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataNumeric()
    {
        return array(
            array(
                "&#176;C",
                "&#176;C",
                true,
            ),
            array(
                "&#176;C",
                "psi",
                false,
            ),
        );
    }
    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataConvert()
    {
        return array(
            array(
                273.15, "&#176;C", "K", \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 0.0
            ),
            array(
                273.15, "&#176;F", "K", \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 32.0
            ),
            array(
                373.15, "&#176;C", "K", \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 100.0
            ),
            array(
                373.15, "&#176;F", "K", \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 212.0
            ),
            array(
                0.0, "K", "&#176;C", \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 273.15
            ),
            array(
                32, "K", "&#176;F", \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 273.15
            ),
            array(
                100.0, "K", "&#176;C", \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 373.15
            ),
            array(
                212.0, "K", "&#176;F", \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 373.15
            ),
            array(
                12.312, "&#176;C", "&#176;F",
                \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, -10.937777777778
            ),
            array(
                12.312, "&#176;C", "&#176;F",
                \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                true, 6.84
            ),
            array(
                12.312, "K", "&#176;C", \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 285.462
            ),
            array(
                12.312, "K", "&#176;C", \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                true, 12.312
            ),
            array(
                -10.937777777778, "&#176;F", "&#176;C",
                \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 12.312
            ),
            array(
                6.84, "&#176;F", "&#176;C",
                \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                true, 12.312
            ),
            array(
                12.312, "&#176;C", "&#176;C",
                \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 12.312
            ),
            array(
                12.312, "&#176;C", "&#176;C",
                \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                true, 12.312
            ),
            array(
                100,
                "&#176;F",
                "&#176;C",
                \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true,
                212.0,
            ),
            array(
                -40,
                "&#176;C",
                "&#176;C",
                \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true,
                -40,
            ),
            array(
                100,
                "&#176;F",
                "&#176;C",
                \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                true,
                180.0,
            ),
            array(
                18,
                "&#176;C",
                "&#176;F",
                \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                true,
                10.0,
            ),
        );
    }

}
?>
