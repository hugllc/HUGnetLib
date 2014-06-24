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
require_once CODE_BASE.'devices/inputTable/drivers/avr/AVRAnalogTable.php';
/** This is a required class */
require_once CODE_BASE.'db/Table.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';

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
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class AVRAnalogTableTest extends DriverTestBaseAVR
{
    /** This is the class we are testing */
    protected $class = "AVRAnalogTable";
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
        $sensor->resetMock(
            array(
                "Sensor" => array(
                    "table" => new \HUGnet\DummyBase("devInput"),
                    "device" => new \HUGnet\DummyBase("Device"),
                ),
            )
        );
        $this->o = AVRAnalogTable::testFactory($sensor, $table);
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
                        "get" => array(
                            "location" => "asdf",
                        ),
                        "table" => new \HUGnet\DummyBase("devInput"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "devInput" => array(
                        "get" => array(
                            "extra" => array(
                                1,
                            ),
                        ),
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "driver" => "02:DEFAULT",
                        ),
                    ),
                ),
                "\HUGnet\devices\inputTable\drivers\AVRAnalogTable",
                array(
                    0 => Array (
                        'decimals' => 2,
                        'units' => '&#176;C',
                        'maxDecimals' => 2,
                        'storageUnit' => '&#176;C',
                        'unitType' => 'Temperature',
                        'dataType' => 'raw',
                        "label" => "asdf",
                        "index" => 0,
                        "epChannel" => true,
                        "port" => "ADC0",
                    ),
                ),
            ),
            array(
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "get" => array(
                            "extra" => array(
                                1,
                            ),
                            "location" => "asdf",
                        ),
                        "table" => new \HUGnet\DummyBase("devInput"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "driver" => "40:DEFAULT",
                        ),
                    ),
                    "Device" => array(
                        "get" => array(
                            "arch" => "0039-28",
                        ),
                    ),
                ),
                "\HUGnet\devices\inputTable\drivers\AVRAnalogTable",
                array(
                    0 => Array (
                        'decimals' => 4,
                        'units' => 'V',
                        'maxDecimals' => 4,
                        'storageUnit' => 'V',
                        'unitType' => 'Voltage',
                        'dataType' => 'raw',
                        "label" => "asdf",
                        "index" => 0,
                        "epChannel" => true,
                        "port" => "Port1",
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
        $channels = $this->o->channels();
        $this->assertEquals($expect, $channels);
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
                            "extra" => array(41),
                            "location" => "asdf",
                            "timeConstant" => 1,
                        ),
                        "table" => new \HUGnet\DummyBase("devInput"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "driver" => "40:DEFAULT",
                        ),
                    ),
                ),
                "404200",
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                array(
                    array(
                        'decimals' => 4,
                        'units' => 'V',
                        'maxDecimals' => 4,
                        'storageUnit' => 'V',
                        'unitType' => 'Voltage',
                        'dataType' => 'raw',
                        'value' => 20.7234,
                        "label" => "asdf",
                        "index" => 0,
                        "epChannel" => true,
                        "port" => null,
                        "raw" => 16960,
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
        $ret = $this->o->decodeData($A, $deltaT, $prev, $data);
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
                            "extra" => array("40"),
                        ),
                        "table" => new \HUGnet\DummyBase("devInput"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "devInput" => array(
                        "get" => array(
                            "extra" => array("40"),
                        ),
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "driver" => "40:DEFAULT",
                        ),
                    ),
                    "Device" => array(
                        "get" => array(
                            "arch" => "0039-28",
                        ),
                    ),
                ),
                "404200",
                1,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                20.7234,
            ),
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGet()
    {
        $system = new \HUGnet\DummySystem("System");
        return array(
            array(
                "ThisIsABadName",
                array(
                    "Sensor" => array(
                        "id" => 5,
                        "table" => new \HUGnet\DummyBase("devInput"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "get" => array(
                            "arch" => "0039-28",
                       ),
                    ),
                ),
                null,
            ),
            array(
                "maxDecimals",
                array(
                    "Sensor" => array(
                        "id" => 5,
                        "table" => new \HUGnet\DummyBase("devInput"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "driver" => "40:DEFAULT",
                        ),
                    ),
                    "Device" => array(
                        "get" => array(
                            "arch" => "0039-28",
                       ),
                    ),
                ),
                4,
            ),
            array(
                "extraValues",
                array(
                    "Sensor" => array(
                        "id" => 5,
                        "table" => new \HUGnet\DummyBase("devInput"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "driver" => "40:DEFAULT",
                        ),
                        "select" => array(
                            1 => new \HUGnet\DummyBase("Table1"),
                            2 => new \HUGnet\DummyBase("Table2"),
                        ),
                    ),
                    "Table1" => array(
                        "get" => array(
                            "id" => 1,
                            "name" => "Hello",
                        ),
                    ),
                    "Table2" => array(
                        "get" => array(
                            "id" => 2,
                            "name" => "Again",
                        ),
                    ),
                    "Device" => array(
                        "get" => array(
                            "arch" => "0039-28",
                       ),
                    ),
                ),
                array(-1, 5, 5, 5),
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
                        "table" => new \HUGnet\DummyBase("devInput"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "get" => array(
                            "arch" => "0039-28",
                       ),
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
            array( // #0 Test no drivers
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Sensor"),
                    ),
                    "Sensor" => array(
                        "table" => new \HUGnet\DummyBase("devInput"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "devInput" => array(
                        "get" => array(
                            "extra" => array(1, 2, 3),
                        ),
                    ),
                    "Device" => array(
                        "get" => array(
                            "arch" => "0039-28",
                       ),
                    ),
                ),
                "FF00C0800086098041FF000102030405060708",
                "0200C0400086",
            ),
            array( // #1 Test extra[0] not set, but found
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Sensor"),
                    ),
                    "Sensor" => array(
                        "table" => new \HUGnet\DummyBase("devInput"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "devInput" => array(
                        "get" => array(
                            "extra" => array(1 => 2, 2 => 3),
                        ),
                    ),
                    "Table" => array(
                        "toArray" => array(
                            'driver' => "40:DEFAULT",
                        ),
                        "selectInto" => true,
                        "nextInto" => false,
                        "get" => array(
                            "id" => 5,
                        ),
                    ),
                ),
                "4001C0800086098041FF010102030405060708",
                "4001C0400086",
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
        $this->assertEquals($expect, $this->o->encode());
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
                        "table" => new \HUGnet\DummyBase("devInput"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Table" => array(
                        "toArray" => array(
                            "driver" => "40:DEFAULT",
                        ),
                    ),
                    "Device" => array(
                        "get" => array(
                            "arch" => "0039-28",
                       ),
                    ),
                ),
                "400000600000",
            ),
            array( // #1
                array(
                    "Sensor" => array(
                        "id" => 0xF9,
                        "get" => array(
                            "id" => 0xF9,
                            "extra" => array(1 => 10, 2 => 20),
                        ),
                        "table" => new \HUGnet\DummyBase("devInput"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Table" => array(
                        "toArray" => array(
                        ),
                    ),
                    "Device" => array(
                        "get" => array(
                            "arch" => "0039-28",
                       ),
                    ),
                ),
                "020000600000",
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
