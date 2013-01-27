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
namespace HUGnet\devices\inputTable\drivers\virtual;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBaseVirtual.php";
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/drivers/virtual/BinaryVirtual.php';

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
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class BinaryVirtualTest extends DriverTestBaseVirtual
{
    /** This is the class we are testing */
    protected $class = "BinaryVirtual";
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
            "BinaryVirtual", $sensor
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
                                0, 5, 3, 60,
                                "&#176;C",
                                "Temperature",
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
                60.0,
                60.0
            ),
            array( // #1
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0, 10, 5.5, 60,
                                "&#176;C",
                                "Temperature",
                                \HUGnet\devices\datachan\Driver::TYPE_RAW,
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => null),
                    1 => array("value" => 2),
                ),
                null,
                null
            ),
            array( // #2
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0, 20, 10, 60,
                                "&#176;C",
                                "Temperature",
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
                60.0,
                0.0
            ),
            array( // #3
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0, 20, 10, 60,
                                "&#176;C",
                                "Temperature",
                                "3"
                            ),
                        ),
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 15),
                    1 => array("value" => 2),
                ),
                60.0,
                60.0
            ),
            array( // #4
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                                0, 4, 0, 60,
                                "&#176;C",
                                "Temperature",
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
                60.0,
                60.0
            ),
        );
    }

}
?>
