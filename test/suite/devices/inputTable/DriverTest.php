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
namespace HUGnet\devices\inputTable;
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/Driver.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is a required class */
require_once CODE_BASE.'util/VPrint.php';
/** This is our base class */
require_once dirname(__FILE__)."/drivers/DriverTestBase.php";

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
class DriverTest extends drivers\DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "DriverTestClass";
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
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($extra);
        $this->o = Driver::factory("DriverTestClass", $sensor);
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
        unset($this->o);
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataPresent()
    {
        return array(
            array(
                "ThisIsABadName",
                false,
            ),
            array(
                "unitType",
                true,
            ),
            array(
                "storageType",
                true,
            ),
            array(
                "testParam",
                true,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataPresent
    */
    public function testPresent($name, $expect)
    {
        $this->assertSame($expect, $this->o->present($name, 1));
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
                array(),
                "ThisIsABadName",
                null,
            ),
            array(
                array(),
                "storageType",
                \HUGnet\devices\datachan\Driver::TYPE_RAW,
            ),
            array(
                array(),
                "testParam",
                "12345",
            ),
            array(
                array(),
                "unitType",
                'asdf',
            ),
            array(
                array(
                ),
                "maxDecimals",
                7,
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(0, 0, 0, 3),
                        ),
                    ),
                ),
                "maxDecimals",
                3,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mock   The mocks to use
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet($mock, $name, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mock);
        $this->assertSame($expect, $this->o->get($name, 1));
    }
    /**
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testToArray()
    {
        $expect = array(
            'longName' => 'Unknown Sensor',
            'shortName' => 'Unknown',
            'unitType' => 'asdf',
            'virtual' => false,
            'bound' => false,
            'total' => false,
            'extraText' => Array ("a", "b", "c", "d", "e"),
            'extraDefault' => Array (2,3,5,7,11),
            'extraValues' => Array (5, 5, 5, 5, 5),
            'storageUnit' => 'unknown',
            'storageType' => 'raw',
            'maxDecimals' => 7,
            'testParam' => '12345',
            "dataTypes" => array(
                \HUGnet\devices\datachan\Driver::TYPE_RAW
                    => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                \HUGnet\devices\datachan\Driver::TYPE_DIFF
                    => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                \HUGnet\devices\datachan\Driver::TYPE_IGNORE
                    => \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
            ),
            'defMin' => 0,
            'defMax' => 150,
            'inputSize' => 3,
        );
        $this->assertEquals($expect, $this->o->toArray(1));
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataFactory2()
    {
        return array(
            array(
                "asdf",
                "HUGnet\devices\inputTable\drivers\SDEFAULT",
            ),
            array(
                "SDEFAULT",
                "HUGnet\devices\inputTable\drivers\SDEFAULT",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataFactory2
    */
    public function testFactory2($name, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($extra);
        $o = &Driver::factory($name, $sensor);
        $this->assertSame($expect, get_class($o));
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGetDriver()
    {
        return array(
            array(
                array(),
                1,
                "test",
                "SDEFAULT",
            ),
            array(
                array(),
                4,
                "test",
                "ADuCVishayRTD",
            ),
            array(
                array(),
                0x41,
                "",
                "ADuCVoltage",
            ),
            array(
                array(),
                0x41,
                "DEFAULT",
                "ADuCVoltage",
            ),
            array(
                array(),
                0x41,
                "ADuCPressure",
                "ADuCPressure",
            ),
            array(
                array(
                    "38:thisIsATest" => "DriverTestClass",
                ),
                0x38,
                "thisIsATest",
                "DriverTestClass",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $registers Drivers to register
    * @param mixed  $sid       The hardware part number
    * @param string $type      The firmware part number
    * @param array  $expect    The expected return
    *
    * @return null
    *
    * @dataProvider dataGetDriver
    */
    public function testGetDriver($registers, $sid, $type, $expect)
    {
        foreach ($registers as $key => $class) {
            Driver::register($key, $class);
        }
        $this->assertSame(
            $expect, Driver::getDriver($sid, $type)
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGetDrivers()
    {
        return array(
            array(
                array(
                    "Sensor" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "get" => array(
                            "arch" => "1",
                        ),
                    ),
                ),
                array(
                    254 => 'Virtual',
                    255 => 'Empty Slot'
                ),
            ),
            array(
                array(
                    "Sensor" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "get" => array(
                            "arch" => "AVR",
                        ),
                    ),
                ),
                array(
                    2 => 'Generic Analog',
                    254 => 'Virtual',
                    255 => 'Empty Slot'
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $mocks  The architecture
    * @param array $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGetDrivers
    */
    public function testGetDrivers($mocks, $expect)
    {
        $dev = new \HUGnet\DummyBase("Sensor");
        $dev->resetMock($mocks);
        $this->assertSame(
            $expect, $this->o->getDrivers()
        );
    }
    /**
    * data provider for testGetExtra
    *
    * @return array
    */
    public static function dataGetExtra()
    {
        return array(
            array(
                array(
                    "Sensor" => array(
                        "get" => array("sensor" => 1, "extra" => array(6,5,4)),
                    ),
                ),
                1,
                5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array("sensor" => 1, "extra" => array(6,5,4)),
                    ),
                ),
                3,
                7
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array("sensor" => 1, "extra" => array(6,5,4)),
                    ),
                ),
                100,
                null
            ),
            array(
                array(),
                100,
                null
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $extra  The extra array
    * @param int    $index  The index to use for the extra array
    * @param string $expect The expected data
    *
    * @return null
    *
    * @dataProvider dataGetExtra
    */
    public function testGetExtra($extra, $index, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($extra);
        $this->assertSame($expect, $this->o->getExtra($index));
    }
    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataGetTypes()
    {
        return array(
            array(
                0x41,
                array(
                    "DEFAULT" => "ADuCVoltage",
                    "ADuCPressure" => "ADuCPressure",
                ),
            ),
            array(
                0x01,
                array(),
            ),
            array(
                0x42,
                array(
                    "DEFAULT" => "ADuCThermocouple"
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param int    $sid    The sensor ID to check
    * @param string $expect The expected data
    *
    * @return null
    *
    * @dataProvider dataGetTypes
    */
    public function testGetTypes($sid,$expect)
    {
        $this->assertSame($expect, Driver::getTypes($sid));
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
                array(),
                null,
                1,
                array(),
                array(),
                null,
            ),
            array(
                array(),
                256210,
                1,
                array(),
                array(),
                256210,
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
        $obj = Driver::factory("DriverTestClass", $sensor);
        $obj->decode($string);
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
                    "Device" => array(
                        "get" => array(
                            "id" => 7,
                        ),
                    ),
                ),
                "",
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
        $obj = Driver::factory("DriverTestClass", $sensor);
        $ret = $obj->encode();
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataStrToInt()
    {
        return array(
            array( // #0
                "563412123456",
                0x123456,
                "123456",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $string    The string to use
    * @param int    $retExpect The expected return
    * @param string $expect    The expected string after the function call
    *
    * @return null
    *
    * @dataProvider dataStrToInt
    */
    public function testStrToInt($string, $retExpect, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock(array());
        $obj = Driver::factory("DriverTestClass", $sensor);
        $ret = $obj->strToInt($string);
        $this->assertSame($retExpect, $ret, "Return is wrong");
        $this->assertSame($expect, $string, "String is wrong");
    }

    /**
    * data provider for testNumeric
    *
    * @return array
    */
    public static function dataLinearBounded()
    {
        return array(
            array(
                10,
                0,
                20,
                0,
                100,
                50.0,
            ),
            array(
                5.4321,
                0,
                10,
                0,
                100,
                54.321,
            ),
            array(
                30,
                0,
                20,
                0,
                100,
                null,
            ),
            array(
                5,
                10,
                20,
                0,
                100,
                null,
            ),
            array(
                5,
                10,
                10,
                0,
                100,
                null,
            ),
            array(
                null,
                0,
                100,
                0,
                100,
                null,
            ),
            array( // Imin and Imax are the same
                10,
                10,
                10,
                0,
                100,
                null,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param int   $value  The integer to feed to the function
    * @param float $Imin   The Input minimum
    * @param float $Imax   The Input maximum
    * @param float $Omin   The Output minimum
    * @param float $Omax   The Output maximum
    * @param int   $expect The expected data
    *
    * @return null
    *
    * @dataProvider dataLinearBounded
    */
    public function testLinearBounded($value, $Imin, $Imax, $Omin, $Omax, $expect)
    {
        bcscale(10);
        $val = $this->o->linearBounded($value, $Imin, $Imax, $Omin, $Omax);
        $this->assertEquals($expect, $val, 0.0001);
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
        return array(
            array( // #0 Raw Data
                array(),
                "DriverTestClass",
                "01020304050607080900",
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => 0x030201,
                        "decimals" => 7,
                        "units" => "unknown",
                        "maxDecimals" => 7,
                        "storageUnit" => "unknown",
                        "unitType" => "asdf",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 0,
                    ),
                ),
            ),
            array(  // #1 Differential data
                array(),
                "DriverTestClassDiff",
                "01020304050607080900",
                1,
                array(),
                array(
                    "raw" => 0x0201,
                ),
                array(
                    array(
                        "value" => 0x030000,
                        "decimals" => 2,
                        "units" => "unknown",
                        "maxDecimals" => 2,
                        "storageUnit" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                        "raw" => 0x030201,
                        "index" => 0,
                    ),
                ),
            ),
            array( // #2 No data.
                array(),
                "DriverTestClass",
                "",
                1,
                array(),
                array(
                    "raw" => 0x0201,
                ),
                array(
                    array(
                        "value" => null,
                        "decimals" => 7,
                        "units" => "unknown",
                        "maxDecimals" => 7,
                        "storageUnit" => "unknown",
                        "unitType" => "asdf",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 0,
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
    * @param array  $sensor The sensor data array
    * @param string $class  The class to use
    * @param string $string The string to use
    * @param float  $deltaT The time differenct
    * @param array  $data   The data array being built
    * @param array  $prev   The previous record
    * @param mixed  $expect The return data to expect
    *
    * @return null
    *
    * @dataProvider dataDecodeData()
    */
    public function testDecodeData(
        $sensor, $class, $string, $deltaT, $data, $prev, $expect
    ) {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock($sensor);
        $obj = Driver::factory($class, $sen);
        $ret = $obj->decodeData($string, $deltaT, $prev, $data);
        $this->assertEquals($expect, $ret, 0.00001);
    }
    /**
     * Data provider for testGetReading
     *
     * testGetReading($sensor, $A, $deltaT, $data, $prev, $expect)
     *
     * @return array
     */
    public static function dataDecodeDataPoint()
    {
        return array(
            array( // #0 Raw Data
                array(),
                "DriverTestClass",
                "01020304050607080900",
                0,
                1,
                array(),
                array(),
                0x030201,
            ),
            array(  // #1 Differential data
                array(),
                "DriverTestClassDiff",
                "01020304050607080900",
                0,
                1,
                array(),
                array(
                    "raw" => 0x0201,
                ),
                0x030000,
            ),
            array( // #2 No data.
                array(),
                "DriverTestClass",
                "",
                0,
                1,
                array(),
                array(
                    "raw" => 0x0201,
                ),
                null,
            ),
            array( // #3 null data.
                array(),
                "DriverTestClass",
                null,
                0,
                1,
                array(),
                array(
                    "raw" => 0x0201,
                ),
                null,
            ),
            array( // #4 Int Data
                array(),
                "DriverTestClass",
                0x030201,
                0,
                1,
                array(),
                array(),
                0x030201,
            ),
        );
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @param array  $sensor  The sensor data array
    * @param string $class   The class to use
    * @param string $string  The string to use
    * @param int    $channel The channel to use
    * @param float  $deltaT  The time differenct
    * @param array  $data    The data array being built
    * @param array  $prev    The previous record
    * @param mixed  $expect  The return data to expect
    *
    * @return null
    *
    * @dataProvider dataDecodeDataPoint()
    */
    public function testDecodeDataPoint(
        $sensor, $class, $string, $channel, $deltaT, $data, $prev, $expect
    ) {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock($sensor);
        $obj = Driver::factory($class, $sen);
        $ret = $obj->decodeDataPoint($string, $channel, $deltaT, $prev, $data);
        $this->assertEquals($expect, $ret, 0.00001);
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
                        "get" => array(
                            "storageUnit" => "unknown",
                            "maxDecimals" => 2,
                            "unitType" => "asdf",
                            "storageType" =>
                                \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        ),
                    ),
                ),
                "DriverTestClass",
                array(
                    array(
                        "decimals" => 7,
                        "units" => 'unknown',
                        "maxDecimals" => 7,
                        "storageUnit" => 'unknown',
                        "unitType" => 'asdf',
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "index" => 0,
                    ),
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The mocks to use
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
        $obj = Driver::factory($name, $sensor);
        $this->assertSame($expect, $obj->channels());
    }
    /**
    * Data provider for testGetPPM
    *
    * @return array
    */
    public static function dataGetPPM()
    {
        return array(
            array(500, 300, 100.0),
            array(500, 0, null),
            array(500, -1, null),
            array(-1, 300, null),
        );
    }
    /**
    * test
    *
    * @param int   $A      The a to d reading
    * @param float $deltaT The bias resistance
    * @param mixed $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetPPM
    */
    public function testGetPPM($A, $deltaT, $expect)
    {
        $this->assertSame($expect, $this->o->getPPM($A, $deltaT));
    }
    /**
    * Data provider for testDriversTest
    *
    * This extracts all of the drivers and puts them into an array to test.
    *
    * @return array
    */
    public static function dataDriversTest()
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock(array());
        $obj = Driver::factory("DriverTestClass", $sensor);
        $ret = array();
        $types = $obj->getTypesTest($i);
        foreach ($types as $name => $class) {
            $ret[] = array($i, $name, $class);
        }
        return $ret;
    }
    /**
    * test
    *
    * @param int    $sid   The sensor id
    * @param string $type  The type of sensor
    * @param string $class The driver class
    *
    * @return null
    *
    * @dataProvider dataDriversTest
    */
    public function testDriversTest($sid, $type, $class)
    {
        $file = CODE_BASE."devices/inputTable/drivers/$class.php";
        $this->assertFileExists(
            $file, "File for $sid:$type and class $class not found"
        );
        include_once CODE_BASE."devices/inputTable/drivers/$class.php";
        $this->assertTrue(
            class_exists("\\HUGnet\\devices\\inputTable\\drivers\\".$class),
            "Class $class doesn't exist for type $sid:$type in file $file"
        );
    }
    /**
     * Data provider for testEncodeData
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
                "0E0000",
                1,
                array(),
                array(),
                14.314713,
                0,
            ),
        );
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @param array $sensor  The sensor data array
    * @param mixed $expect  The return data to expect
    * @param float $deltaT  The time differenct
    * @param array $data    The data array being built
    * @param array $prev    The previous record
    * @param mixed $A       Data for the sensor to work on
    * @param int   $channel The data channel to use
    *
    * @return null
    *
    * @dataProvider dataEncodeDataPoint()
    */
    public function testEncodeDataPoint(
        $sensor, $expect, $deltaT, $data, $prev, $A, $channel
    ) {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock($sensor);
        $ret = $this->o->encodeDataPoint($A, $channel);
        $this->assertEquals($expect, $ret, 0.00001);
    }
}
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers;
/**
 * Base driver class for devices.
 *
 * This class deals with loading the drivers and figuring out what driver needs
 * to be loaded.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DriverTestClass extends \HUGnet\devices\inputTable\Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Unknown Sensor",
        "shortName" => "Unknown",
        "unitType" => "asdf", /* This is for test value only */
        "testParam" => "12345", /* This is for test value only */
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraDefault" => array(2,3,5,7,11),
        "extraText" => array("a","b","c","d","e"),
        "extraValues" => array(5, 5, 5, 5, 5),
        "maxDecimals" => "getExtra3",
    );
    /**
    * Gets the extra values
    *
    * @param int $index The extra index to use
    *
    * @return The extra value (or default if empty)
    */
    public function getExtra($index)
    {
        return parent::getExtra($index);
    }
    /**
    * Takes in a raw string from a sensor and makes an int out it
    *
    * The sensor data is stored little-endian, so it just takes that and adds
    * the bytes together.
    *
    * @param string &$string The string to convert
    *
    * @return int
    */
    public function strToInt(&$string)
    {
        return parent::strToInt($string);
    }
    /**
    * This makes a line of two ordered pairs, then puts $A on that line
    *
    * @param float $value The incoming value
    * @param float $Imin  The input minimum
    * @param float $Imax  The input maximum
    * @param float $Omin  The output minimum
    * @param float $Omax  The output maximum
    *
    * @return output rounded to 4 places
    */
    public function linearBounded($value, $Imin, $Imax, $Omin, $Omax)
    {
        return parent::linearBounded($value, $Imin, $Imax, $Omin, $Omax);
    }
    /**
    * This is for a generic pulse counter
    *
    * @param int   $val    Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    *                      and the last one
    *
    * @return float
    */
    public function getPPM($val, $deltaT)
    {
        return parent::getPPM($val, $deltaT);
    }
    /**
    * Returns an array of types that this sensor could be
    *
    * @param int $sid The ID to check
    *
    * @return The extra value (or default if empty)
    */
    public static function getTypesTest($sid)
    {
        return static::$drivers;
    }
}
/**
 * Base driver class for devices.
 *
 * This class deals with loading the drivers and figuring out what driver needs
 * to be loaded.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DriverTestClassDiff extends \HUGnet\devices\inputTable\Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Unknown Sensor",
        "shortName" => "Unknown",
        "unitType" => "unknown",
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
    );
}
?>
