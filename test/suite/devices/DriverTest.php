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
namespace HUGnet\devices;
/** This is a required class */
require_once CODE_BASE.'devices/Driver.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is a required class */
require_once CODE_BASE.'util/VPrint.php';

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
class DriverTest extends \PHPUnit_Framework_TestCase
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
                array(),
                "ThisIsABadName",
                false,
            ),
            array(
                array(),
                "packetTimeout",
                true,
            ),
            array(
                array(),
                "testParam",
                true,
            ),
            array(
                array(),
                "virtualSensors",
                true,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataPresent
    */
    public function testPresent($mocks, $name, $expect)
    {
        $device  = new \HUGnet\DummyTable("Device");
        $device->resetMock($mocks);
        $o = &DriverTestClass::factory($device);
        $this->assertSame($expect, $o->present($name));
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
                "packetTimeout",
                6,
            ),
            array(
                array(),
                "testParam",
                "12345",
            ),
            array(
                array(),
                "virtualSensors",
                4,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet($mocks, $name, $expect)
    {
        $device  = new \HUGnet\DummyTable("Device");
        $device->resetMock($mocks);
        $o = &DriverTestClass::factory($device);
        $this->assertSame($expect, $o->get($name));
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
                array(),
                "asdf",
                "HUGnet\devices\drivers\EDEFAULT",
            ),
            array(
                array(),
                "EDEFAULT",
                "HUGnet\devices\drivers\EDEFAULT",
            ),
            array(
                array(),
                "EVIRTUAL",
                "HUGnet\devices\drivers\EVIRTUAL",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataFactory
    */
    public function testFactory($mocks, $name, $expect)
    {
        $device  = new \HUGnet\DummyTable("Device");
        $device->resetMock($mocks);
        $o = &Driver::factory($name, $device);
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
                "0039-12-02-C",
                "0039-38-01-C",
                "5.6.7",
                "E00391200",
            ),
            array(
                "0039-11-02-C",
                "0039-38-01-C",
                "5.6.7",
                "EDEFAULT",
            ),
            array(
                "0039-24-02-P",
                "0039-24-02-P",
                "5.6.7",
                "EVIRTUAL",
            ),
            array(
                "0039-12-02-C",
                "0039-13-01-C",
                "5.6.7",
                "E00391200",
            ),
            array(
                "0039-12-02-C",
                "0039-38-01-C",
                "5.6.7",
                "E00391200",
            ),
            array(
                "0039-28-01-A",
                "0039-38-01-C",
                "1.2.3",
                "E00392800",
            ),
            array(
                "0039-28-01-A",
                "0039-20-18-C",
                "1.2.3",
                "E00392801",
            ),
            array(
                "0039-21-01-A",
                "0039-38-02-C",
                "5.6.7",
                "E00393802",
            ),
            array(
                "0039-21-02-A",
                "0039-38-02-C",
                "5.6.7",
                "E00393802",
            ),
            array(
                "0039-21-01-A",
                "0039-38-01-C",
                "5.6.7",
                "E00392100",
            ),
            array(
                "0039-21-02-A",
                "0039-38-01-C",
                "5.6.7",
                "E00392100",
            ),
            array(
                "0039-37-01-A",
                "0039-38-02-C",
                "5.6.7",
                "E00393802",
            ),
            array(
                "0039-21-01-A",
                "0039-38-02-C",
                "1.2.3",
                "E00393802",
            ),
            array(
                "0039-21-02-A",
                "0039-38-02-C",
                "1.2.3",
                "E00393802",
            ),
            array(
                "0039-37-01-A",
                "0039-38-01-C",
                "5.6.7",
                "E00393700",
            ),
            array(
                "0039-12-02-B",
                "0039-11-08-A",
                "0.0.2",
                "E00391201",
            ),
            array(
                "0039-28-FF-A",
                "0039-38-01-C",
                "0.0.2",
                "EDEFAULT",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $HWPartNum The hardware part number
    * @param string $FWPartNum The firmware part number
    * @param string $FWVersion The firmware version
    * @param array  $expect    The expected return
    *
    * @return null
    *
    * @dataProvider dataGetDriver
    */
    public function testGetDriver($HWPartNum, $FWPartNum, $FWVersion, $expect)
    {
        $driver = Driver::getDriver($HWPartNum, $FWPartNum, $FWVersion);
        $this->assertSame($expect, $driver);
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $HWPartNum The hardware part number
    * @param string $FWPartNum The firmware part number
    * @param string $FWVersion The firmware version
    * @param array  $expect    The expected return
    *
    * @return null
    *
    * @dataProvider dataGetDriver
    */
    public function testDriverExists($HWPartNum, $FWPartNum, $FWVersion, $expect)
    {
        $driver = Driver::getDriver($HWPartNum, $FWPartNum, $FWVersion);
        $this->assertFileExists(CODE_BASE."/devices/drivers/".$driver.".php");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGetSensorID()
    {
        return array(
            array(
                0,
                "000000100800393701410039380143000004FFFFFFFF01404142434445464748",
                0x40,
            ),
            array(
                4,
                "000000100800393701410039380143000004FFFFFFFF01404142434445464748",
                0x44,
            ),
            array(
                2,
                "000000100800393701410039380143000004FFFFFFFF01404142434445464748",
                0x42,
            ),
            array(
                8,
                "000000100800393701410039380143000004FFFFFFFF01404142434445464748",
                0x48,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param int    $sensor   The sensor number
    * @param string $RawSetup The raw setup to use
    * @param array  $expect   The expected return
    *
    * @return null
    *
    * @dataProvider dataGetSensorID
    */
    public function testGetSensorID($sensor, $RawSetup, $expect)
    {
        $this->assertSame(
            $expect, Driver::getSensorID($sensor, $RawSetup)
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecodeSensorString()
    {
        return array(
            array(
                array(),
                "ThisIsString",
                array(
                    "DataIndex" => 0,
                    "timeConstant" => 0,
                    "String" => "String",
                ),
            ),
            array(
                array(),
                "012805100000200000300000400000500000600000700000800000900000",
                array(
                    "DataIndex" => 1,
                    "timeConstant" => 5,
                    "String" => "100000200000300000400000500000600000"
                    ."700000800000900000",
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $string The string to decode
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDecodeSensorString
    */
    public function testDecodeSensorString($mocks, $string, $expect)
    {
        $device  = new \HUGnet\DummyTable("Device");
        $device->resetMock($mocks);
        $obj = &DriverTestClass::factory($device);
        $this->assertEquals($expect, $obj->decodeSensorString($string));
    }
    /**
    * data provider for testHistoryTable
    *
    * @return array
    */
    public static function dataHistoryTable()
    {
        return array(
            array(
                array(),
                "\HUGnet\devices\DriverTestClass",
                true,
                "E00392800HistoryTable"
            ),
            array(
                array(),
                "\HUGnet\devices\DriverTestClass",
                false,
                "E00392800AverageTable"
            ),
            array(
                array(),
                "\HUGnet\devices\DriverTestClass2",
                true,
                "EDEFAULTHistoryTable"
            ),
            array(
                array(),
                "\HUGnet\devices\DriverTestClass2",
                false,
                "EDEFAULTAverageTable"
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks   The value to preload into the mocks
    * @param string $class   The class to use
    * @param bool   $history Whether or not to get a history table
    * @param array  $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataHistoryTable
    */
    public function testHistoryTable($mocks, $class, $history, $expect)
    {
        $device  = new \HUGnet\DummyTable("Device");
        $device->resetMock($mocks);
        $obj = &$class::factory($device);
        $this->assertEquals($expect, $obj->historyTable($history));
    }
    /**
    * data provider for testToArray
    *
    * @return array
    */
    public static function dataToArray()
    {
        return array(
            array(
                array(),
                "\HUGnet\devices\DriverTestClass",
                array(
                    'packetTimeout' => 6,
                    'totalSensors' => 13,
                    'physicalSensors' => 9,
                    'virtualSensors' => 4,
                    'historyTable' => 'E00392800HistoryTable',
                    'averageTable' => 'E00392800AverageTable',
                    'loadable' => false,
                    'bootloader' => false,
                    'testParam' => '12345',
                    'ConfigInterval' => 43200,
                    'type' => 'unknown',
                    'actionClass' => 'Action',
                ),
            ),
            array(
                array(),
                "\HUGnet\devices\DriverTestClass2",
                array(
                    'packetTimeout' => 9,
                    'totalSensors' => 13,
                    'physicalSensors' => 9,
                    'virtualSensors' => 4,
                    'historyTable' => 'ABADHistoryTable',
                    'averageTable' => 'ABADAverageTable',
                    'loadable' => false,
                    'bootloader' => false,
                    'testParam' => '54321',
                    'ConfigInterval' => 43200,
                    'type' => 'unknown',
                    'actionClass' => 'Action',
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $class  The class to use
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataToArray
    */
    public function testToArray($mocks, $class, $expect)
    {
        $device  = new \HUGnet\DummyTable("Device");
        $device->resetMock($mocks);
        $obj = &$class::factory($device);
        $this->assertEquals($expect, $obj->toArray());
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecode()
    {
        return array(
            array( // #0
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Sensor"),
                    )
                ),
                "0102030405060708090A",
                array(
                    "Device" => array(
                        "set" => array(
                            array("TimeConstant", 1),
                        ),
                        "sensor" => array(
                            array(0),
                            array(1),
                            array(2),
                            array(3),
                            array(4),
                            array(5),
                            array(6),
                            array(7),
                            array(8),
                        ),
                    ),
                    'Sensor' => array(
                        'change' => array(
                            array(array("id" => 2)),
                            array(array("id" => 3)),
                            array(array("id" => 4)),
                            array(array("id" => 5)),
                            array(array("id" => 6)),
                            array(array("id" => 7)),
                            array(array("id" => 8)),
                            array(array("id" => 9)),
                            array(array("id" => 10)),
                        ),
                    ),
                ),
            ),
            array( // #1 String not big enough
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Sensor"),
                    )
                ),
                "010203040506",
                array(
                    "Device" => array(
                        "set" => array(
                            array("TimeConstant", 1),
                        ),
                        "sensor" => array(
                            array(0),
                            array(1),
                            array(2),
                            array(3),
                            array(4),
                        ),
                    ),
                    'Sensor' => array(
                        'change' => array(
                            array(array("id" => 2)),
                            array(array("id" => 3)),
                            array(array("id" => 4)),
                            array(array("id" => 5)),
                            array(array("id" => 6)),
                        ),
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
        $device  = new \HUGnet\DummyTable("Device");
        $device->resetMock($mocks);
        $obj = DriverTestClass::factory($device);
        $obj->decode($string, $device);
        $ret = $device->retrieve();
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
                        "sensor" => array(
                            0 => new \HUGnet\DummyBase("Sensor0"),
                            1 => new \HUGnet\DummyBase("Sensor1"),
                            2 => new \HUGnet\DummyBase("Sensor2"),
                            3 => new \HUGnet\DummyBase("Sensor3"),
                            4 => new \HUGnet\DummyBase("Sensor4"),
                            5 => new \HUGnet\DummyBase("Sensor5"),
                            6 => new \HUGnet\DummyBase("Sensor6"),
                            7 => new \HUGnet\DummyBase("Sensor7"),
                            8 => new \HUGnet\DummyBase("Sensor8"),
                        ),
                        "get" => array(
                            "TimeConstant" => 7,
                        ),
                    ),
                    'Sensor0' => array(
                        'get' => array(
                            "id" => 3,
                        ),
                    ),
                    'Sensor1' => array(
                        'get' => array(
                            "id" => 4,
                        ),
                    ),
                    'Sensor2' => array(
                        'get' => array(
                            "id" => 5,
                        ),
                    ),
                    'Sensor3' => array(
                        'get' => array(
                            "id" => 6,
                        ),
                    ),
                    'Sensor4' => array(
                        'get' => array(
                            "id" => 7,
                        ),
                    ),
                    'Sensor5' => array(
                        'get' => array(
                            "id" => 8,
                        ),
                    ),
                    'Sensor6' => array(
                        'get' => array(
                            "id" => 9,
                        ),
                    ),
                    'Sensor7' => array(
                        'get' => array(
                            "id" => 10,
                        ),
                    ),
                    'Sensor8' => array(
                        'get' => array(
                            "id" => 11,
                        ),
                    ),
                ),
                true,
                "07030405060708090A0B",
            ),
            array( // #0
                array(
                    "Device" => array(
                        "sensor" => array(
                            0 => new \HUGnet\DummyBase("Sensor0"),
                            1 => new \HUGnet\DummyBase("Sensor1"),
                            2 => new \HUGnet\DummyBase("Sensor2"),
                            3 => new \HUGnet\DummyBase("Sensor3"),
                            4 => new \HUGnet\DummyBase("Sensor4"),
                            5 => new \HUGnet\DummyBase("Sensor5"),
                            6 => new \HUGnet\DummyBase("Sensor6"),
                            7 => new \HUGnet\DummyBase("Sensor7"),
                            8 => new \HUGnet\DummyBase("Sensor8"),
                        ),
                        "get" => array(
                            "TimeConstant" => 7,
                        ),
                    ),
                    'Sensor0' => array(
                        'get' => array(
                            "id" => 3,
                        ),
                    ),
                    'Sensor1' => array(
                        'get' => array(
                            "id" => 4,
                        ),
                    ),
                    'Sensor2' => array(
                        'get' => array(
                            "id" => 5,
                        ),
                    ),
                    'Sensor3' => array(
                        'get' => array(
                            "id" => 6,
                        ),
                    ),
                    'Sensor4' => array(
                        'get' => array(
                            "id" => 7,
                        ),
                    ),
                    'Sensor5' => array(
                        'get' => array(
                            "id" => 8,
                        ),
                    ),
                    'Sensor6' => array(
                        'get' => array(
                            "id" => 9,
                        ),
                    ),
                    'Sensor7' => array(
                        'get' => array(
                            "id" => 10,
                        ),
                    ),
                    'Sensor8' => array(
                        'get' => array(
                            "id" => 11,
                        ),
                    ),
                ),
                false,
                "07",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $mocks     The value to preload into the mocks
    * @param bool  $showFixed Show the fixed portion of the data
    * @param array $expect    The expected return
    *
    * @return null
    *
    * @dataProvider dataEncode
    */
    public function testEncode($mocks, $showFixed, $expect)
    {
        $device  = new \HUGnet\DummyTable("Device");
        $device->resetMock($mocks);
        $obj = DriverTestClass::factory($device);
        $ret = $obj->encode($device, $showFixed);
        $this->assertSame($expect, $ret);
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
        "packetTimeout" => 6, /* This is for test value only */
        "testParam" => "12345", /* This is for test value only */
        "historyTable" => "E00392800HistoryTable",
        "averageTable" => "E00392800AverageTable",
    );
    /**
    * This function creates the system.
    *
    * @param object &$device The device record we are attached to
    *
    * @return null
    */
    public static function &factory(&$device)
    {
        return parent::intFactory($device);
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
class DriverTestClass2 extends Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "packetTimeout" => 9, /* This is for test value only */
        "testParam" => "54321", /* This is for test value only */
        "historyTable" => "ABADHistoryTable",
        "averageTable" => "ABADAverageTable",
    );
    /**
    * This function creates the system.
    *
    * @param object &$device The device record we are attached to
    *
    * @return null
    */
    public static function &factory(&$device)
    {
        return parent::intFactory($device);
    }
}
?>
