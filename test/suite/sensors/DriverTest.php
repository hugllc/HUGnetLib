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
namespace HUGnet\sensors;
/** This is a required class */
require_once CODE_BASE.'sensors/Driver.php';
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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DriverTest extends drivers\DriverTestBase
{
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
        $this->o = &DriverTestClass::factory($sensor);
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
                \HUGnet\units\Driver::TYPE_RAW,
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
                \HUGnet\units\Driver::TYPE_RAW => \HUGnet\units\Driver::TYPE_RAW,
                \HUGnet\units\Driver::TYPE_DIFF => \HUGnet\units\Driver::TYPE_DIFF,
                \HUGnet\units\Driver::TYPE_IGNORE
                    => \HUGnet\units\Driver::TYPE_IGNORE,
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
    public static function dataFactory()
    {
        return array(
            array(
                "asdf",
                "HUGnet\sensors\drivers\SDEFAULT",
            ),
            array(
                "SDEFAULT",
                "HUGnet\sensors\drivers\SDEFAULT",
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
    * @dataProvider dataFactory
    */
    public function testFactory($name, $expect)
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
                1,
                "test",
                "SDEFAULT",
            ),
            array(
                4,
                "test",
                "ADuCVishayRTD",
            ),
            array(
                0x41,
                "",
                "ADuCVoltage",
            ),
            array(
                0x41,
                "DEFAULT",
                "ADuCVoltage",
            ),
            array(
                0x41,
                "ADuCPressure",
                "ADuCPressure",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $sid    The hardware part number
    * @param string $type   The firmware part number
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGetDriver
    */
    public function testGetDriver($sid, $type, $expect)
    {
        $this->assertSame(
            $expect, Driver::getDriver($sid, $type, $FWVersion)
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
                256210,
                1,
                array(),
                array(),
                null,
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
        $obj = DriverTestClass::factory($sensor);
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
        $obj = DriverTestClass::factory($sensor);
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
        $obj = DriverTestClass::factory($sensor);
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
                            "storageType" => \HUGnet\units\Driver::TYPE_RAW,
                        ),
                    ),
                ),
                "\HUGnet\sensors\DriverTestClass",
                array(
                    array(
                        "decimals" => 2,
                        "units" => 'unknown',
                        "maxDecimals" => 2,
                        "storageUnit" => 'unknown',
                        "unitType" => 'asdf',
                        "dataType" => \HUGnet\units\Driver::TYPE_RAW,
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
        $obj = &$name::factory($sensor);
        $this->assertSame($expect, $obj->channels());
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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DriverTestClass extends Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "unitType" => "asdf", /* This is for test value only */
        "testParam" => "12345", /* This is for test value only */
        "extraDefault" => array(2,3,5,7,11),
        "extraText" => array("a","b","c","d","e"),
        "extraValues" => array(5, 5, 5, 5, 5),
        "maxDecimals" => "getExtra3",
    );
    /**
    * This function creates the system.
    *
    * @param object &$sensor The sensor object
    *
    * @return null
    */
    public static function &factory(&$sensor)
    {
        return parent::intFactory($sensor);
    }
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
}
?>
