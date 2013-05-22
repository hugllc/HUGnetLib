<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\aduc;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBaseADuC.php";
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/drivers/aduc/ADuCMF51E.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ADuCMF51ETest extends DriverTestBaseADuC
{
    /** This is the class we are testing */
    protected $class = "ADuCMF51E";
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
        $this->o = \HUGnet\devices\inputTable\Driver::factory(
            "ADuCMF51E", $sensor, 0
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
                            "extra" => array(),
                        ),
                    ),
                ),
                -4841350,
                0,
                array(),
                array(),
                18.24900402,
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(),
                        ),
                    ),
                ),
                -1630178,
                0,
                array(),
                array(),
                60.69713051,
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(),
                        ),
                    ),
                ),
                (0 - 0x54B68B),
                0,
                array(),
                array(),
                10.50693547,
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            'extra' => array(2210),
                        ),
                    ),
                ),
                491755,
                0,
                array(),
                array(),
                -25.42756507,
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            'extra' => array(2210),
                        ),
                    ),
                ),
                4591755,
                0,
                array(),
                array(),
                null,
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            'extra' => array(2210),
                        ),
                    ),
                ),
                pow(2, 23),
                0,
                array(),
                array(),
                null,
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
                            "extra" => array(),
                        ),
                    ),
                ),
                "1D20E7FF",
                1,
                array(),
                array(),
                60.69711538
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(),
                        ),
                    ),
                ),
                "7549ABFF",
                1,
                array(),
                array(),
                10.50693547
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                4 => 21.1,
                            ),
                        ),
                    ),
                ),
                "56AD86FF",
                1,
                array(),
                array(),
                -30.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(
                                4 => 21.1,
                            ),
                        ),
                    ),
                ),
                "",
                1,
                array(),
                array(),
                null
            ),
        );
    }

}
?>
