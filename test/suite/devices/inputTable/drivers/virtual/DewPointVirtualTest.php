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
require_once CODE_BASE.'devices/inputTable/drivers/virtual/DewPointVirtual.php';

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
class DewPointVirtualTest extends DriverTestBaseVirtual
{
    /** This is the class we are testing */
    protected $class = "DewPointVirtual";
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
            "DewPointVirtual", $sensor
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
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0, 1
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 15),
                    1 => array("value" => 100),
                ),
                0,
                15.0
            ),
            array( // #1
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0, 1
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => -1),
                    1 => array("value" => 50),
                ),
                0,
                null
            ),
            array( // #2
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0, 1
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 61),
                    1 => array("value" => 50),
                ),
                0,
                null
            ),
            array( // #3
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0, 1
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 15),
                    1 => array("value" => 0.9),
                ),
                0,
                null
            ),
            array( // #4
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0, 1
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 25),
                    1 => array("value" => 20),
                ),
                0,
                0.4712
            ),
            array( // #5
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0, 1
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 30),
                    1 => array("value" => 40),
                ),
                0,
                14.9068
            ),
            array( // #6
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0, 1
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 12.91),
                    1 => array("value" => 9.89),
                ),
                0,
                null
            ),
        );
    }

}
?>
