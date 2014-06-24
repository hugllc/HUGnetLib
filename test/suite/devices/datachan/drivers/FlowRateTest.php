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
require_once CODE_BASE.'devices/datachan/drivers/FlowRate.php';

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
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class FlowRateTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "FlowRate";
    /** This is the units that are valid */
    protected static $units = array(
        "gal/min", "gal/hr", "gal/day", "L/min", "L/hr", "L/day"
    );
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
        $this->o = \HUGnet\devices\datachan\Driver::factory("FlowRate", "Pa");
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
                    "gal/min" => "gal/min", 
                    "gal/hr" => "gal/hr", 
                    "gal/day" => "gal/day", 
                    "L/min" => "L/min", 
                    "L/hr" => "L/hr", 
                    "L/day" => "L/day",
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
                "gal/hr",
                "%",
                false,
            ),
            array(
                "gal/hr",
                "gal/hr",
                true,
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
                "gal/hr",
                "%",
                false,
            ),
            array(
                "gal/hr",
                "gal/hr",
                true,
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
                10, "gal/hr", "gal/min", \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 600
            ),
            array(
                1, "gal/min", "L/min", \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 0.264172052637
            ),
            array(
                1, "L/min", "gal/min", \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 3.78541178
            ),
        );
    }

}
?>
