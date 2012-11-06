<?php
/**
 * This runs all of the tests associated with HUGnetLib.
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
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\sensors\drivers;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBase.php";
/** This is a required class */
require_once CODE_BASE.'sensors/drivers/ComputationVirtual.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ComputationVirtualTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "ComputationVirtual";
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
        $this->o = \HUGnet\sensors\DriverVirtual::factory(
            "ComputationVirtual", $sensor
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
            array( // #0
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "(({1} - {2}) / 3) + 1",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_RAW,
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 2),
                ),
                0,
                2.0
            ),
            array( // #1
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "({1} + {2}) + 1",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_RAW,
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 2),
                ),
                0,
                8.0
            ),
            array( // #2
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "({1} / {2})",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_RAW,
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 3),
                ),
                0,
                1.667
            ),
            array( // #3
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "({1} / ",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_RAW,
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 3),
                ),
                0,
                null
            ),
            array( // #4
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "badfunction();",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_RAW,
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 3),
                ),
                0,
                null
            ),
            array( // #5
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "sqrt({1});",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_RAW,
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 4),
                    1 => array("value" => 3),
                ),
                0,
                2,
            ),
            array( // #6
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "{1} + {2}",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_RAW,
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 4.1576),
                    1 => array("value" => 3.8253),
                ),
                0,
                7.983
            ),
            array( // #7
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "{1} - {2}",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_DIFF,
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 4.1),
                    1 => array("value" => 4.1),
                ),
                0,
                0.0
            ),
            array( // #8 Division by 0
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "({1} - {2}) / 0",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_DIFF,
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 4.1),
                    1 => array("value" => 4.1),
                ),
                0,
                null,
            ),
            array( // #9 Division by null
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "{1} / {2}",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_DIFF,
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 4.1),
                    1 => array("value" => null),
                ),
                0,
                null,
            ),
            array( // #10 Null input
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "{1} + {2}",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_DIFF,
                                "3",
                                1,
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => null),
                    1 => array("value" => 4.1),
                ),
                0,
                4.1,
            ),
            array( // #11 Null input #2
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "{1} + {2}",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_DIFF,
                                "3",
                                0
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => null),
                    1 => array("value" => 4.1),
                ),
                0,
                null,
            ),
            array( // #12 Null input #3 defaults to the old behaviour
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "{1} + {2}",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_DIFF,
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => null),
                    1 => array("value" => 4.1),
                ),
                0,
                null,
            ),
            array( // #13 Null input #3 defaults to the old behaviour
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                "{1}^{2}",
                                "&#176;C",
                                "Temperature",
                                \HUGnet\channels\Driver::TYPE_DIFF,
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 4),
                    1 => array("value" => 2),
                ),
                0,
                16,
            ),
        );
    }

}
?>
