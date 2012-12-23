<?php
/**
 * Tests the filter class
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
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\avr;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBaseAVR.php";
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/drivers/avr/AVRDwyer616.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class AVRDwyer616Test extends DriverTestBaseAVR
{
    /** This is the class we are testing */
    protected $class = "AVRDwyer616";


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
            "AVRDwyer616", $sensor
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
     * Data provider for testGetReading
     *
     * testGetReading($sensor, $A, $deltaT, $data, $prev, $expect)
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
                            "id" => 0x50,
                            "type" => "Dwyer616",
                        ),
                    ),
                ),
                13100,
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                -1.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x50,
                            "type" => "Dwyer616",
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
                            "id" => 0x50,
                            "type" => "Dwyer616",
                        ),
                    ),
                ),
                39000,
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
                            "id" => 0x50,
                            "type" => "Dwyer616",
                        ),
                    ),
                ),
                65200,
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                1.5
            ),
        );
    }
    /**
     * Data provider for testGetReading
     *
     * testGetReading($sensor, $A, $deltaT, $data, $prev, $expect)
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
                            "id" => 0x50,
                            "type" => "Dwyer616",
                        ),
                    ),
                ),
                "F23200",
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                -1.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x50,
                            "type" => "Dwyer616",
                        ),
                    ),
                ),
                '',
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
                            "id" => 0x50,
                            "type" => "Dwyer616",
                        ),
                    ),
                ),
                "D69800",
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
                            "id" => 0x50,
                            "type" => "Dwyer616",
                        ),
                    ),
                ),
                "BAFE00",
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                1.5
            ),
        );
    }

}

?>
