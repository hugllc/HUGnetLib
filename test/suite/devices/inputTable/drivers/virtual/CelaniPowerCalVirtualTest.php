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
namespace HUGnet\devices\inputTable\drivers\virtual;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBaseVirtual.php";
/** This is a required class */
require_once CODE_BASE
    .'devices/inputTable/drivers/virtual/CelaniPowerCalVirtual.php';

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
class CelaniPowerCalVirtualTest extends DriverTestBaseVirtual
{
    /** This is the class we are testing */
    protected $class = "CelaniPowerCalVirtual";
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
        $sensor = new \HUGnet\DummyBase("Input");
        $sensor->resetMock(
            array(
                "Input" => array(
                    "device" => new \HUGnet\DummyBase("Device"),
                ),
                "Device" => array(
                    "dataChannels" => new \HUGnet\DummyBase("dataChannels"),
                ),
                "dataChannels" => array(
                    "select" => array(),
                ),

            )
        );
        $this->o = \HUGnet\devices\inputTable\DriverVirtual::factory(
            "CelaniPowerCalVirtual", $sensor
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
            array( // #0  lower boundry
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 0.21),
                ),
                0,
                0.010092
            ),
            array( // #1  upper boundry
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 313.39),
                ),
                0,
                163.170363
            ),
            array( // #2 random middle point
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 18.57),
                ),
                0,
                4.108724
            ),
            array( // #3 random middle point
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 249.14),
                ),
                0,
                110.529711
            ),
            array( // #4 random middle point
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 142.64),
                ),
                0,
                48.17877
            ),
            array( // #5 random middle point
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => null),
                ),
                0,
                null
            ),
            array( // #6 random middle point
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 4.2109260827983724E+02),
                ),
                0,
                null
            ),
        );
    }

}
?>
