<?php
/**
 * Tests the filter class
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
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\avr;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBaseAVR.php";
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/drivers/avr/AVRVoltage.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class AVRVoltageTest extends DriverTestBaseAVR
{
    /** This is the class we are testing */
    protected $class = "AVRVoltage";

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
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock(array());
        $this->o = \HUGnet\devices\inputTable\DriverAVR::factory(
            "AVRVoltage", $sensor
        );
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
        unset($this->o);
    }

    /**
    * Data provider for testAVRVoltage
    *
    * @return array
    */
    public static function dataGetReading()
    {
        return array(
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x40,
                            "type" => "AVRVoltage",
                        ),
                    ),
                ),
                1000,
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                1.2219
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x40,
                            "type" => "AVRVoltage",
                        ),
                    ),
                ),
                null,
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                null
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x40,
                            "type" => "AVRVoltage",
                        ),
                    ),
                ),
                0,
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                0.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x40,
                            "type" => "AVRVoltage",
                        ),
                    ),
                ),
                65535,
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                80.077,
            ),
        );
    }
    /**
    * Data provider for testAVRVoltage
    *
    * @return array
    */
    public static function dataEncodeDataPoint()
    {
        return array(
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x40,
                            "type" => "AVRVoltage",
                        ),
                    ),
                ),
                "E80300",
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                1.2219
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x40,
                            "type" => "AVRVoltage",
                        ),
                    ),
                ),
                "",
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                null
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x40,
                            "type" => "AVRVoltage",
                        ),
                    ),
                ),
                '000000',
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                0.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x40,
                            "type" => "AVRVoltage",
                        ),
                    ),
                ),
                "FFFF00",
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                80.077,
            ),
        );
    }

}

?>
