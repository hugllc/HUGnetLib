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
namespace HUGnet\devices\inputTable\drivers\aduc;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBaseADuC.php";
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/drivers/aduc/ADuCPower.php';
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/ADuCInputTable.php';

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
class ADuCPowerTest extends DriverTestBaseADuC
{
    /** This is the class we are testing */
    protected $class = "ADuCPower";
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
            "ADuCPower", $sensor, 0
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
    * data provider for testType
    *
    * @return array
    */
    public static function dataArrayFieldType()
    {
        $ret = array();
        foreach (array("storageUnit", "unitType") as $field) {
            for ($i = 1; $i < 9; $i++) {
                $ret[] = array($field, $i, "string");
            }
        }
        return $ret;
    }
    /**
    * Check the variable type
    *
    * @param string $field  The field to check
    * @param int    $sensor The sensor number
    * @param string $type   The type it should be
    *
    * @return null
    *
    * @dataProvider dataArrayFieldType
    */
    public function testArrayFieldType($field, $sensor, $type)
    {
        $name = $this->o->get($field, $sensor);
        $this->assertInternalType($type, $name, "$field must be a $type");
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    public static function dataArrayFieldTypeNull()
    {
        $ret = array(
            array("storageUnit", null, 0),
            array("unitType", null, 0),
        );
        return $ret;
    }
    /**
    * Check the variable type
    *
    * @param string $field  The field to check
    * @param int    $sensor The sensor number
    * @param int    $expect The sensor it will register as
    *
    * @return null
    *
    * @dataProvider dataArrayFieldTypeNull
    */
    public function testFieldTypeNull($field, $sensor, $expect)
    {
        $name = $this->o->get($field, $sensor);
        $exp = $this->o->get($field, $expect);
        $this->assertSame($exp, $name);
    }
    /**
    * Check the variable type
    *
    * @param string $field  The field to check
    * @param int    $sensor The sensor number
    * @param int    $expect The sensor it will register as
    *
    * @return null
    *
    * @dataProvider dataArrayFieldTypeNull
    */
    public function testArrayFieldTypeNull($field, $sensor, $expect)
    {
        $name = $this->o->toArray($sensor);
        $exp = $this->o->toArray($expect);
        $this->assertSame($exp, $name);
    }
    /**
    * Check the variable type
    *
    * @param string $field  The field to check
    * @param int    $sensor The sensor number
    * @param string $type   The type it should be
    *
    * @return null
    *
    * @dataProvider dataArrayFieldType
    */
    public function testArrayFieldTypeArray($field, $sensor, $type)
    {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock(
            array(
                "Sensor" => array(
                    "id" => $sensor,
                ),
            )
        );
        $name = $this->o->get($field, $sensor);
        $array = $this->o->toArray($sensor);
        $this->assertSame(
            $name, $array[$field], "toArray is wrong for field $field"
        );
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    public static function dataArrayFieldSize()
    {
        $ret = array();
        for ($i = 1; $i < 9; $i++) {
            $ret[] = array("storageUnit", $i, 1, 15);
            $ret[] = array("unitType", $i, 1, 20);
        }
        return $ret;
    }
    /**
    * Check the variable type
    *
    * @param string $field  The field to check
    * @param int    $sensor The sensor number
    * @param int    $min    The minimum size
    * @param int    $max    The maximum size
    *
    * @return null
    *
    * @dataProvider dataArrayFieldSize
    */
    public function testArrayFieldSizeMin($field, $sensor, $min, $max)
    {
        $name = $this->o->get($field, $sensor);
        $this->assertGreaterThanOrEqual(
            $min, strlen($name), "$field:$sensor must be at least $min characters"
        );
    }
    /**
    * Check the variable type
    *
    * @param string $field  The field to check
    * @param int    $sensor The sensor number
    * @param int    $min    The minimum size
    * @param int    $max    The maximum size
    *
    * @return null
    *
    * @dataProvider dataArrayFieldSize
    */
    public function testArrayFieldSizeMax($field, $sensor, $min, $max)
    {
        $name = $this->o->get($field, $sensor);
        $this->assertLessThanOrEqual(
            $max, strlen($name), "$field:$sensor must be at most $max characters"
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataChannels()
    {
        return array(
            array(
                array(
                    "Sensor" => array(
                        "id" => 1,
                    ),
                ),
                "ADuCPower",
                array(
                    array(
                        "decimals" => 6,
                        "units" => 'A',
                        'maxDecimals' => 6,
                        'storageUnit' => 'A',
                        "unitType" => 'Current',
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 0,
                    ),
                    array(
                        "decimals" => 6,
                        "units" => 'V',
                        'maxDecimals' => 6,
                        'storageUnit' => 'V',
                        "unitType" => 'Voltage',
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 1,
                    ),
                    array(
                        "decimals" => 6,
                        "units" => 'W',
                        'maxDecimals' => 6,
                        'storageUnit' => 'W',
                        "unitType" => 'Power',
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 2,
                    ),
                    array(
                        "decimals" => 6,
                        "units" => 'Ohms',
                        'maxDecimals' => 6,
                        'storageUnit' => 'Ohms',
                        "unitType" => 'Impedance',
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 3,
                    ),
                ),
            ),
            array(
                array(
                    "Sensor" => array(
                        "id" => 3,
                    ),
                ),
                "ADuCPower",
                array(
                    array(
                        "decimals" => 6,
                        "units" => 'A',
                        'maxDecimals' => 6,
                        'storageUnit' => 'A',
                        "unitType" => 'Current',
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 0,
                    ),
                    array(
                        "decimals" => 6,
                        "units" => 'V',
                        'maxDecimals' => 6,
                        'storageUnit' => 'V',
                        "unitType" => 'Voltage',
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 1,
                    ),
                    array(
                        "decimals" => 6,
                        "units" => 'W',
                        'maxDecimals' => 6,
                        'storageUnit' => 'W',
                        "unitType" => 'Power',
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 2,
                    ),
                    array(
                        "decimals" => 6,
                        "units" => 'Ohms',
                        'maxDecimals' => 6,
                        'storageUnit' => 'Ohms',
                        "unitType" => 'Impedance',
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 3,
                    ),
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The mocks to set
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataChannels
    */
    public function testChannels($mocks, $name, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mocks);
        $obj = \HUGnet\devices\inputTable\Driver::factory(
            $name, $sensor, 0
        );
        $this->assertSame($expect, $obj->channels());
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
                        "id" => 1,
                        "get" => array(
                            "sensor" => 2,
                            "extra" => array(),
                        ),
                    ),
                ),
                "40420F0040420F00",
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => 0.314713,
                        "decimals" => 6,
                        "units" => "A",
                        'maxDecimals' => 6,
                        'storageUnit' => 'A',
                        "unitType" => "Current",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 0,
                    ),
                    array(
                        "value" => 14.448166,
                        "decimals" => 6,
                        "units" => "V",
                        'maxDecimals' => 6,
                        'storageUnit' => 'V',
                        "unitType" => "Voltage",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 1,
                    ),
                    array(
                        "value" => 4.547026,
                        "decimals" => 6,
                        "units" => "W",
                        'maxDecimals' => 6,
                        'storageUnit' => 'W',
                        "unitType" => "Power",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 2,
                    ),
                    array(
                        "value" => 45.909022,
                        "decimals" => 6,
                        "units" => "Ohms",
                        'maxDecimals' => 6,
                        'storageUnit' => 'Ohms',
                        "unitType" => "Impedance",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 3,
                    ),
                ),
            ),
            array( // #1
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 2,
                            "extra" => array(),
                        ),
                    ),
                ),
                "FFFFFFFF00000000",
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => 0.0,
                        "decimals" => 6,
                        "units" => "A",
                        'maxDecimals' => 6,
                        'storageUnit' => 'A',
                        "unitType" => "Current",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 0,
                    ),
                    array(
                        "value" => 0.0,
                        "decimals" => 6,
                        "units" => "V",
                        'maxDecimals' => 6,
                        'storageUnit' => 'V',
                        "unitType" => "Voltage",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 1,
                    ),
                    array(
                        "value" => 0.0,
                        "decimals" => 6,
                        "units" => "W",
                        'maxDecimals' => 6,
                        'storageUnit' => 'W',
                        "unitType" => "Power",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 2,
                    ),
                    array(
                        "value" => null,
                        "decimals" => 6,
                        "units" => "Ohms",
                        'maxDecimals' => 6,
                        'storageUnit' => 'Ohms',
                        "unitType" => "Impedance",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 3,
                    ),
                ),
            ),
            array( // #2
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 2,
                            "extra" => array(),
                        ),
                    ),
                ),
                "40420F00",
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => 0.314713,
                        "decimals" => 6,
                        "units" => "A",
                        'maxDecimals' => 6,
                        'storageUnit' => 'A',
                        "unitType" => "Current",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 0,
                    ),
                    array(
                        "value" => null,
                        "decimals" => 6,
                        "units" => "V",
                        'maxDecimals' => 6,
                        'storageUnit' => 'V',
                        "unitType" => "Voltage",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 1,
                    ),
                    array(
                        "value" => null,
                        "decimals" => 6,
                        "units" => "W",
                        'maxDecimals' => 6,
                        'storageUnit' => 'W',
                        "unitType" => "Power",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 2,
                    ),
                    array(
                        "value" => null,
                        "decimals" => 6,
                        "units" => "Ohms",
                        'maxDecimals' => 6,
                        'storageUnit' => 'Ohms',
                        "unitType" => "Impedance",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 3,
                    ),
                ),
            ),
            array( // #3
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 2,
                            "extra" => array(),
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
                        "decimals" => 6,
                        "units" => "A",
                        'maxDecimals' => 6,
                        'storageUnit' => 'A',
                        "unitType" => "Current",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 0,
                    ),
                    array(
                        "value" => null,
                        "decimals" => 6,
                        "units" => "V",
                        'maxDecimals' => 6,
                        'storageUnit' => 'V',
                        "unitType" => "Voltage",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 1,
                    ),
                    array(
                        "value" => null,
                        "decimals" => 6,
                        "units" => "W",
                        'maxDecimals' => 6,
                        'storageUnit' => 'W',
                        "unitType" => "Power",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 2,
                    ),
                    array(
                        "value" => null,
                        "decimals" => 6,
                        "units" => "Ohms",
                        'maxDecimals' => 6,
                        'storageUnit' => 'Ohms',
                        "unitType" => "Impedance",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 3,
                    ),
                ),
            ),
            array( // #4
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 2,
                            "extra" => array(1.2, 0, 100, 1, 1, 10),
                        ),
                    ),
                ),
                "40420F0040420F00",
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => null,
                        "decimals" => 6,
                        "units" => "A",
                        'maxDecimals' => 6,
                        'storageUnit' => 'A',
                        "unitType" => "Current",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 0,
                    ),
                    array(
                        "value" => 14.448166,
                        "decimals" => 6,
                        "units" => "V",
                        'maxDecimals' => 6,
                        'storageUnit' => 'V',
                        "unitType" => "Voltage",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 1,
                    ),
                    array(
                        "value" => null,
                        "decimals" => 6,
                        "units" => "W",
                        'maxDecimals' => 6,
                        'storageUnit' => 'W',
                        "unitType" => "Power",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 2,
                    ),
                    array(
                        "value" => null,
                        "decimals" => 6,
                        "units" => "Ohms",
                        'maxDecimals' => 6,
                        'storageUnit' => 'Ohms',
                        "unitType" => "Impedance",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 3,
                    ),
                ),
            ),
            array( // #5
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 2,
                            "extra" => array(),
                        ),
                    ),
                ),
                "40420F00C0BDF0FF",
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => 0.314713,
                        "decimals" => 6,
                        "units" => "A",
                        'maxDecimals' => 6,
                        'storageUnit' => 'A',
                        "unitType" => "Current",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 0,
                    ),
                    array(
                        "value" => -14.448166,
                        "decimals" => 6,
                        "units" => "V",
                        'maxDecimals' => 6,
                        'storageUnit' => 'V',
                        "unitType" => "Voltage",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 1,
                    ),
                    array(
                        "value" => -4.547026,
                        "decimals" => 6,
                        "units" => "W",
                        'maxDecimals' => 6,
                        'storageUnit' => 'W',
                        "unitType" => "Power",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 2,
                    ),
                    array(
                        "value" => 45.909022,
                        "decimals" => 6,
                        "units" => "Ohms",
                        'maxDecimals' => 6,
                        'storageUnit' => 'Ohms',
                        "unitType" => "Impedance",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 3,
                    ),
                ),
            ),
            array( // #6
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 2,
                            "extra" => array(),
                        ),
                    ),
                ),
                "4002000040480000",
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => 0.000181,
                        "decimals" => 6,
                        "units" => "A",
                        'maxDecimals' => 6,
                        'storageUnit' => 'A',
                        "unitType" => "Current",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 0,
                    ),
                    array(
                        "value" => 0.267233,
                        "decimals" => 6,
                        "units" => "V",
                        'maxDecimals' => 6,
                        'storageUnit' => 'V',
                        "unitType" => "Voltage",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 1,
                    ),
                    array(
                        "value" => 0.000048,
                        "decimals" => 6,
                        "units" => "W",
                        'maxDecimals' => 6,
                        'storageUnit' => 'W',
                        "unitType" => "Power",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 2,
                    ),
                    array(
                        "value" => 1476.425414,
                        "decimals" => 6,
                        "units" => "Ohms",
                        'maxDecimals' => 6,
                        'storageUnit' => 'Ohms',
                        "unitType" => "Impedance",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 3,
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
    * @param array $sensor  The sensor data array
    * @param mixed $A       Data for the sensor to work on
    * @param float $deltaT  The time differenct
    * @param array $data    The data array being built
    * @param array $prev    The previous record
    * @param mixed $expect  The return data to expect
    * @param int   $channel The channel to test
    *
    * @return null
    *
    * @dataProvider dataGetReading()
    */
    public function testGetReading(
        $sensor, $A, $deltaT, $data, $prev, $expect, $channel = 0
    ) {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock($sensor);
        $ret = $this->o->decodeData($A, $deltaT, $data, $prev);
        $this->assertEquals($expect, $ret, 0.00001);
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
            array( // #0
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 2,
                            "extra" => array(),
                        ),
                    ),
                ),
                "42420F00",
                1,
                array(),
                array(),
                0.314713,
                0,
            ),
            array( // #1
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 2,
                            "extra" => array(),
                        ),
                    ),
                ),
                "",
                1,
                array(),
                array(),
                null,
                0,
            ),
            array( // #2
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 2,
                            "extra" => array(1.2, 0, 100, 1, 1, 10),
                        ),
                    ),
                ),
                "40420F00",
                1,
                array(),
                array(),
                14.448166,
                1,
            ),
            array( // #3
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 2,
                            "extra" => array(),
                        ),
                    ),
                ),
                "00000000",
                1,
                array(),
                array(),
                null,
                10,
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
    public static function dataGetPower()
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
                null,
                1,
                array(),
                array(),
                null
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(),
                        ),
                    ),
                ),
                0x11251000,
                1,
                array(),
                array(),
                0.001308
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
    * @dataProvider dataGetPower()
    */
    public function testGetPower($sensor, $A, $deltaT, $data, $prev, $expect)
    {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock($sensor);
        $ret = $this->o->getPower($A, $deltaT, $data, $prev);
        $this->assertEquals($expect, $ret, 0.00001);
    }
    /**
     * Data provider for testGetReading
     *
     * testGetReading($sensor, $A, $deltaT, $data, $prev, $expect)
     *
     * @return array
     */
    public static function dataGetImpedance()
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
                null,
                1,
                array(),
                array(),
                null
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(),
                        ),
                    ),
                ),
                (0x4804 / 0x204),
                1,
                array(),
                array(),
                1606.818182
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
    * @dataProvider dataGetImpedance()
    */
    public function testGetImpedance($sensor, $A, $deltaT, $data, $prev, $expect)
    {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock($sensor);
        $ret = $this->o->getImpedance($A, $deltaT, $data, $prev);
        $this->assertEquals($expect, $ret, 0.00001);
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGet()
    {
        return array(
            array(
                "ThisIsABadName",
                array(
                ),
                null,
            ),
            array(
                "storageUnit",
                array(
                    "Sensor" => array(
                        "id" => 5,
                    ),
                ),
                'unknown',
            ),
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataExtra()
    {
        return array(
            array(
                0,
                array(
                    "Sensor" => array(
                        "id" => 5,
                    ),
                ),
                1.2,
            ),
            array(
                0,
                array(
                    "Sensor" => array(
                        "id" => 1,
                    ),
                ),
                1.2,
            ),
        );
    }
}
?>
