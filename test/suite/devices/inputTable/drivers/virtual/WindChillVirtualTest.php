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
namespace HUGnet\devices\inputTable\drivers\virtual;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBaseVirtual.php";
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/drivers/virtual/WindChillVirtual.php';

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
class WindChillVirtualTest extends DriverTestBaseVirtual
{
    /** This is the class we are testing */
    protected $class = "WindChillVirtual";
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
            "WindChillVirtual", $sensor
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
                    0 => array("value" => 5),
                    1 => array("value" => 5),
                ),
                0,
                3.1372
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
                    0 => array("value" => 11),
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
                    0 => array("value" => -1),
                    1 => array("value" => 2),
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
                    0 => array("value" => -10),
                    1 => array("value" => 10),
                ),
                0,
                -16.9903
            ),
        );
    }
    /**
    * data provider for testChannels
    *
    * @return array
    */
    public static function dataChannels()
    {
        return array(
            array(
                array(
                ),
                "",
                array(
                    array(
                        'decimals' => 4,
                        'units' => '&#176;C',
                        'maxDecimals' => 4,
                        'storageUnit' => '&#176;C',
                        'unitType' => 'Temperature',
                        'dataType' => 'raw',
                        'label' => '',
                        'index' => 0,
                        'epChannel' => false,
                        'port' => null,
                    ),
                ),
            ),
        );
    }

}
?>
