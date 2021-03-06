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
namespace HUGnet\devices\powerTable;
/** This is a required class */
require_once CODE_BASE.'devices/powerTable/Driver.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is our base class */
require_once dirname(__FILE__)."/drivers/DriverTestBase.php";
/** This is our interface */
require_once CODE_BASE."devices/powerTable/DriverInterface.php";

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2015 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
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
        parent::setUp();
        $this->o = Driver::factory("DriverTestClass", $this->power);
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
                "shortName",
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
                "shortName",
                "Unknown",
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
            'longName' => 'Unknown Power',
            'shortName' => 'Unknown',
            'extraText' => array(
                0 => 'Type',
                1 => 'Priority',
                2 => 'Default State'
            ),
            'extraDesc' => array(
                0 => "The type of this power port",
                1 => "The priority of this power port",
                2 => "Whether the port is on or off when the device boots up"
            ),
            'extraDefault' => array(
                0 => 0,
                1 => 0,
                2 => 1
            ),
            'extraValues' => array(
                0 => array(0 => 'None'),
                1 => array(0 => 'Highest'),
                2 => array(1 => 'On', 0 => 'Off'),
            ),
            'extraNames' => array(
                'type' => 0,
                'priority' => 1,
                'defstate' => 2
            ),
            "chars" => 23,
            'port' => '2Z',
            "requires" => array(),
            "provides" => array(),
        );
        $this->assertEquals($expect, $this->o->toArray(1));
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
+        0 => 'Type'
+        1 => 'Priority'
    */
    public static function dataFactory()
    {
        return array(
            array(
                "asdf",
                array(),
                "HUGnet\devices\powerTable\drivers\EmptyPower",
            ),
            array(
                "EmptyOutput",
                array(),
                "HUGnet\devices\powerTable\drivers\EmptyPower",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The name of the variable to test.
    * @param array  $table  The table info to give the class
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataFactory
    */
    public function testFactory($name, $table, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($extra);
        $o = Driver::factory($name, $sensor, $table);
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
                0x41,
                "DEFAULT",
                "EmptyPower",
            ),
            array(
                0x41,
                "ADuCPressure",
                "EmptyPower",
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
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGetDrivers()
    {
        return array(
            array(
                array(
                ),
                array(
                    0xFF => 'Empty Slot'
                ),
            ),
            array(
                array(
                    "HWPartNum" => "1042-02-01-A",
                ),
                array(
                    0xFF => 'Empty Slot'
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
        $this->power->device()->load($mocks);
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
                    "extra" => array(6,5,4),
                ),
                1,
                5
            ),
            array(
                array(
                    "extra" => array(6,5,4),
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
        $this->power->load($extra);
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
                ),
            ),
            array(
                0x01,
                array(
                ),
            ),
            array(
                0xFF,
                array(
                    'DEFAULT' => 'EmptyPower'
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
                    ),
                    "Sensor" => array(
                        "get" => array(
                            "id" => 7,
                            "extra" => array(1, 2, 0x11223344),
                            "location" => "ABCDEFGHIJKLMNOPQRSTUVXYZ",
                        ),
                        "table" => new \HUGnet\DummyTable("Table"),
                    ),
                    "Table" => array(
                        "get" => array(
                            "tableEntry" => array(),
                        ),
                    )
                ),
                "0102444142434445464748494A4B4C4D4E4F5051525354555600FFFFFFFFFFFF"
                    ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                    ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                    ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF",
                array(
                    'get' => array(
                        0 => array('extra'),
                    ),
                    'set' => array(
                        0 => array('location', ""),
                        1 => array('extra', array(1, 2, 68)),
                    ),
                    'table' => array(array(), array(), array()),
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
        $ret = $sensor->retrieve("Sensor");
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
                        "get" => array(
                            "id" => 7,
                            "extra" => array(1, 2, 0x11223344),
                            "location" => "ABCDEFGHIJKLMNOPQRSTUVXYZ",
                        ),
                        "table" => new \HUGnet\DummyTable("Table"),
                    ),
                    "Table" => array(
                        "get" => array(
                            "tableEntry" => array(),
                        ),
                    ),
                ),
                "0102444142434445464748494A4B4C4D4E4F5051525354555600FFFFFFFFFFFF"
                    ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                    ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                    ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF",
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
                            "location" => "Hello",
                        ),
                    ),
                ),
                "DriverTestClass",
                array(
                    array(
                        'label' => 'Hello',
                        'index' => 0,
                        'port' => '2Z',
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
}
/** This is the HUGnet namespace */
namespace HUGnet\devices\powerTable\drivers;
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DriverTestClass extends \HUGnet\devices\powerTable\Driver
    implements \HUGnet\devices\powerTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "port" => "2Z",
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
    * @param float $Omin  The power minimum
    * @param float $Omax  The power maximum
    *
    * @return power rounded to 4 places
    */
    public function linearBounded($value, $Imin, $Imax, $Omin, $Omax)
    {
        return parent::linearBounded($value, $Imin, $Imax, $Omin, $Omax);
    }
}
?>
