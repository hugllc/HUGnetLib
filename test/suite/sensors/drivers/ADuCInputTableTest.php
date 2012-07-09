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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\sensors\drivers;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBase.php";
/** This is a required class */
require_once CODE_BASE.'sensors/drivers/ADuCInputTable.php';
/** This is a required class */
require_once CODE_BASE.'tables/InputTableTable.php';

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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ADuCInputTableTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "ADuCInputTable";
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
        $table = new \HUGnet\DummyBase("Table");
        $sensor->resetMock(array());
        $this->o = &ADuCInputTable::factory($sensor, $table);
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
                        "extra" => array(
                            1,
                        ),
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "driver0" => 0x04,
                            "driver1" => 0x41,
                        ),
                    ),
                ),
                "\HUGnet\sensors\drivers\ADuCInputTable",
                array(
                    0 => Array (
                        'decimals' => 4,
                        'units' => '&#176;C',
                        'maxDecimals' => 4,
                        'storageUnit' => '&#176;C',
                        'unitType' => 'Temperature',
                        'dataType' => 'raw',
                    ),
                    1 => Array (
                        'decimals' => 8,
                        'units' => 'V',
                        'maxDecimals' => 8,
                        'storageUnit' => 'V',
                        'unitType' => 'Voltage',
                        'dataType' => 'raw',
                    ),
                ),
            ),
            array(
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "extra" => array(
                            1,
                        ),
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "driver0" => 0x04,
                            "driver1" => 0xFF,
                        ),
                    ),
                ),
                "\HUGnet\sensors\drivers\ADuCInputTable",
                array(
                    0 => Array (
                        'decimals' => 4,
                        'units' => '&#176;C',
                        'maxDecimals' => 4,
                        'storageUnit' => '&#176;C',
                        'unitType' => 'Temperature',
                        'dataType' => 'raw',
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
        $this->assertSame($expect, $this->o->channels());
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
                            "extra" => array("41:DEFAULT", "41:DEFAULT"),
                        ),
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "driver0" => 0x41,
                        ),
                    ),
                ),
                "40420F0040420F00",
                1,
                array(),
                array(),
                array(
                    array(
                        'decimals' => 8,
                        'units' => 'V',
                        'maxDecimals' => 8,
                        'storageUnit' => 'V',
                        'unitType' => 'Voltage',
                        'dataType' => 'raw',
                        'value' => 0.14305115,
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
    * @dataProvider dataGetReading()
    */
    public function testGetReading($sensor, $A, $deltaT, $data, $prev, $expect)
    {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock($sensor);
        $ret = $this->o->decodeData($A, $deltaT, $data, $prev);
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
                "maxDecimals",
                array(
                    "Sensor" => array(
                        "id" => 5,
                    ),
                ),
                6,
            ),
            array(
                "extraValues",
                array(
                    "Sensor" => array(
                        "id" => 5,
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "driver0" => 0x41,
                        ),
                        "select" => array(
                            "1" => new \InputTableTable(
                                array("id" => 1, "name" => "Hello")
                            ),
                            "2" => new \InputTableTable(
                                array("id" => 2, "name" => "Again")
                            ),
                        ),
                    ),
                ),
                array(
                    array(1 => "Hello", 2 => "Again"),
                    5, 5, 5
                ),
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
                0,
            ),
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecode()
    {
        return array(
            array(
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Sensor"),
                    )
                ),
                "010203040506",
                array(
                    "Sensor" => array(
                        "get" => array(array("extra")),
                    ),
                    "Table" => array(
                        "getRow" => array(array(0)),
                        "toArray" => array(array()),
                    ),
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $string The setup string to test
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDecode
    */
    public function testDecode($mocks, $string, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mocks);
        $this->o->decode($string);
        $ret = $sensor->retrieve();
        $this->assertEquals($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataEncode()
    {
        return array(
            array( // #0
                array(
                    "Sensor" => array(
                        "id" => 0xF9,
                        "get" => array(
                            "id" => 0xF9,
                        ),
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "driver0" => 0x41,
                        ),
                    ),
                ),
                "FF00C0800086098041FF",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $mocks  The value to preload into the mocks
    * @param array $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataEncode
    */
    public function testEncode($mocks, $expect)
    {
        $sensor  = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mocks);
        $ret = $this->o->encode();
        $this->assertSame($expect, $ret);
    }
}
?>
