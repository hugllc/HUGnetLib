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
namespace HUGnet\devices\inputTable\drivers\avr;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBaseAVR.php";
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/drivers/avr/AVRBC2322640.php';

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
class AVRBC2322640Test extends DriverTestBaseAVR
{
    /** This is the class we are testing */
    protected $class = "AVRBC2322640";
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
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock(array());
        $this->o = \HUGnet\devices\inputTable\DriverAVR::factory(
            "AVRBC2322640", $sensor
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
                            'extra' => array(10, 10),
                            'id' => 2,
                        ),
                    ),
                ),
                63570,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                null
            ), // -40.1 degrees
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            'extra' => array(10, 10),
                            'id' => 2,
                        ),
                    ),
                ),
                1150,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                null
            ),  // 150.9 degrees
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            'extra' => array(10, 10),
                            'id' => 2,
                        ),
                    ),
                ),
                5000,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                93.3234
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 2,
                        ),
                    ),
                ),
                5000,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                93.3234
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0,
                            "extra" => array(100, 10),
                        ),
                    ),
                ),
                5000,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                29.3996
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            'extra' => array(0, 10),
                            'id' => 2,
                        ),
                    ),
                ),
                5000,
                0,
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
                            'extra' => array(0, 0),
                            'id' => 2,
                        ),
                    ),
                ),
                5000,
                0,
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
                            'extra' => array(10, 0),
                            'id' => 2,
                        ),
                    ),
                ),
                5000,
                0,
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
                            'extra' => array(10, 10),
                            'id' => 2,
                        ),
                    ),
                ),
                1023 * 64,
                0,
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
                            'dataType'=>
                                \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
                            'id' => 2,
                        ),
                    ),
                ),
                5000,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                93.3234
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
                            'extra' => array(10, 10),
                            'id' => 2,
                        ),
                    ),
                ),
                "8A1300",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                93.3105
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 2,
                        ),
                    ),
                ),
                "8A1300",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                93.3105
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0,
                            "extra" => array(100, 10),
                        ),
                    ),
                ),
                "881300",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                29.3987
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            'dataType'=>
                                \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
                            'id' => 2,
                        ),
                    ),
                ),
                "8A1300",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                93.3105
            ),
        );
    }

}
?>
