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
namespace HUGnet\devices\inputTable\drivers\avr;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBaseAVR.php";
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/drivers/avr/ControllerCurrent.php';

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
class ControllerCurrentTest extends DriverTestBaseAVR
{
    /** This is the class we are testing */
    protected $class = "ControllerCurrent";
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
            "ControllerCurrent", $sensor
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
                ),
                0,
                0,
                array(
                ),
                0,
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
            array( // #0  first current
                array(
                    "Input" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 1,
                            "extra" => array(),
                        ),
                    ),
                ),
                "6D06",
                1,
                array(),
                array(),
                35.9,
            ),
            array( // #1  second current
                array(
                    "Input" => array(
                        "id" => 4,
                        "get" => array(
                            "sensor" => 4,
                            "extra" => array(),
                        ),
                    ),
                ),
                "7503",
                1,
                array(),
                array(),
                19.3,
            ),
            array( // #2  No Input
                array(
                    "Input" => array(
                        "id" => 4,
                        "get" => array(
                            "sensor" => 4,
                            "extra" => array(),
                        ),
                    ),
                ),
                "",
                1,
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
    public static function dataDecodeData()
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $driver = \HUGnet\devices\inputTable\DriverAVR::factory(
            "ControllerCurrent", $sensor
        );
        return array(
            array( // #0  first current
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 1,
                            "extra" => array(),
                            "maxDecimals" => $driver->get("maxDecimals"),
                            "storageUnit" => $driver->get("storageUnit"),
                            "unitType" => $driver->get("unitType"),
                            "storageType" => $driver->get("storageType"),
                            "location" => "asdf",
                        ),
                    ),
                ),
                "6E06E3FC00002448C8480000C2FD7503",
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => 35.9,
                        "decimals" => 1,
                        "units" => "mA",
                        'maxDecimals' => 1,
                        'storageUnit' => 'mA',
                        "unitType" => "Current",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf",
                        "index" => 0,
                        "epChannel" => true,
                    ),
                ),
            ),
            array( // #1  second current
                array(
                    "Sensor" => array(
                        "id" => 4,
                        "get" => array(
                            "sensor" => 4,
                            "extra" => array(),
                            "maxDecimals" => $driver->get("maxDecimals"),
                            "storageUnit" => $driver->get("storageUnit"),
                            "unitType" => $driver->get("unitType"),
                            "storageType" => $driver->get("storageType"),
                            "location" => "asdf",
                        ),
                    ),
                ),
                "C2FD7503",
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => 19.3,
                        "decimals" => 1,
                        "units" => "mA",
                        'maxDecimals' => 1,
                        'storageUnit' => 'mA',
                        "unitType" => "Current",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf",
                        "index" => 0,
                        "epChannel" => true,
                    ),
                ),
            ),
            array( // #2  No Input
                array(
                    "Sensor" => array(
                        "id" => 4,
                        "get" => array(
                            "sensor" => 4,
                            "extra" => array(),
                            "maxDecimals" => $driver->get("maxDecimals"),
                            "storageUnit" => $driver->get("storageUnit"),
                            "unitType" => $driver->get("unitType"),
                            "storageType" => $driver->get("storageType"),
                            "location" => "asdf",
                        ),
                    ),
                ),
                "",
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => null,
                        "decimals" => 1,
                        "units" => "mA",
                        'maxDecimals' => 1,
                        'storageUnit' => 'mA',
                        "unitType" => "Current",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf",
                        "index" => 0,
                        "epChannel" => true,
                    ),
                ),
            ),
        );
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @param array $sensor The sensor data array
    * @param mixed $A      Data for the sensor to work on
    * @param float $deltaT The time differenct
    * @param array $data   The data array being built
    * @param array $prev   The previous record
    * @param mixed $expect The return data to expect
    *
    * @return null
    *
    * @dataProvider dataDecodeData()
    */
    public function testDecodeData($sensor, $A, $deltaT, $data, $prev, $expect)
    {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock($sensor);
        $ret = $this->o->decodeData($A, $deltaT, $data, $prev);
        $this->assertEquals($expect, $ret, 0.00001);
    }

}
?>
