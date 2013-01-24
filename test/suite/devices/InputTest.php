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
namespace HUGnet\devices;
/** This is a required class */
require_once CODE_BASE.'devices/Input.php';
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/Driver.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is our units class */
require_once CODE_BASE."devices/datachan/Driver.php";

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
class InputTest extends \PHPUnit_Framework_TestCase
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
        \HUGnet\devices\inputTable\Driver::register(
            "FD:DEFAULT", "TestInputDriver1"
        );
        \HUGnet\devices\inputTable\Driver::register(
            "FC:DEFAULT", "TestInputDriver2"
        );
        parent::setUp();
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
        parent::tearDown();
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataCreate()
    {
        return array(
            array(
                new \HUGnet\DummySystem(),
                null,
                "DummyTable",
            ),
            array(
                new \HUGnet\DummySystem(),
                array(
                    "id" => 5,
                    "name" => 3,
                    "value" => 1,
                ),
                "DummyTable",
            ),
            array(
                new \HUGnet\DummySystem(),
                array("dev" => 2, "input" => 0),
                new \HUGnet\DummyTable(),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config  The configuration to use
    * @param mixed $gateway The gateway to set
    * @param mixed $class   This is either the name of a class or an object
    *
    * @return null
    *
    * @dataProvider dataCreate
    */
    public function testCreate($config, $gateway, $class)
    {
        $table = new \HUGnet\DummyTable();
        $dev = new \HUGnet\DummyBase("Device");
        // This just resets the mock
        $table->resetMock();
        $obj = Input::factory($config, $gateway, $class, $dev);
        // Make sure we have the right object
        $this->assertInstanceOf(
            "HUGnet\devices\Input", $obj, "Class wrong"
        );
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function data2Array()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 0xFD,
                        ),
                        "toArray" => array(
                            "id" => 0xFD,
                            "asdf" => 3,
                            "params" => json_encode(array(1,2,3,4)),
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                array(
                    "longName" => "Silly Input Driver 1",
                    "shortName" => "SSD1",
                    "unitType" => "Temperature",
                    "bound" => false,
                    "virtual" => false,
                    "total" => false,
                    "extraText" => array("Silliness Factor", "Storage Unit"),
                    "extraDefault" => array(2210, '&#176;C'),
                    "extraValues" => array(5, array('&#176;C', '&#176;F', 'K')),
                    "storageUnit" => "&#176;C",
                    "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    "maxDecimals" => 4,
                    "dataTypes" => array(
                        \HUGnet\devices\datachan\Driver::TYPE_RAW
                            => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        \HUGnet\devices\datachan\Driver::TYPE_DIFF
                            => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                        \HUGnet\devices\datachan\Driver::TYPE_IGNORE
                            => \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
                    ),
                    "inputSize" => 3,
                    'id' => 0xFD,
                    'asdf' => 3,
                    'params' => array(1,2,3,4),
                    'type' => "TestInputDriver1",
                    'otherTypes' => array(
                        "DEFAULT" => "TestInputDriver1",
                    ),
                    'validUnits' => array(
                        "&#176;F" => "&#176;F",
                        "&#176;C" => "&#176;C",
                        "K" => "K"
                    ),
                    'validIds' => array(
                        254 => "Virtual", 255 => "Empty Slot",
                        96 => 'Control Value Input'
                    ),
                )
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $class  This is either the name of a class or an object
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider data2Array
    */
    public function test2Array(
        $config, $class, $expect
    ) {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyBase("Device");
        $sys->resetMock($config);
        $obj = Input::factory($sys, null, $class, $dev);
        $json = $obj->toArray();
        $this->assertEquals($expect, $json);
        unset($obj);
    }

    /**
    * data provider for testInput
    *
    * @return array
    */
    public static function dataDecodeData()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 0xFD,
                            "type" => "TestInputDriver1",
                            "unitType" => "Pressure",
                            "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                            "storageUnit" => "psi",
                        ),
                        "toArray" => array(
                            "id" => 0xFD,
                            "type" => "TestInputDriver1",
                            "extra" => array(),
                            "unitType" => "Pressure",
                            "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                            "storageUnit" => "psi",
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                "6F4401ABCDEF",
                300,
                12345,
                array(
                    array(
                        "value" => 25.2134,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    ),
                    array(
                        "value" => 28.5282,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    ),
                    array(
                        "value" => 12.3455,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    ),
                    array(
                        "value" => 82.1253,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    ),
                ),
                array(
                    array(
                        "value" => 166110,
                        "units" => "&#176;C",
                        'maxDecimals' => 4,
                        'storageUnit' => '&#176;C',
                        "unitType" => "Temperature",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "decimals" => 4,
                        "index" => 0,
                        "epChannel" => true,
                    ),
                ),
                "ABCDEF",
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 0xFD,
                            "type" => "TestInputDriver1",
                            "dataType" => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                "AE0100123456",
                300,
                0x12345,
                array(
                    array(
                        "value" => 25.2134,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    ),
                    array(
                        "value" => 28.5282,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    ),
                    array(
                        "value" => 12.3455,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    ),
                    array(
                        "value" => 82.1253,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    ),
                ),
                array(
                    array(
                        "value" => 860,
                        "units" => "&#176;C",
                        'maxDecimals' => 4,
                        'storageUnit' => '&#176;C',
                        "unitType" => "Temperature",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "decimals" => 4,
                        "index" => 0,
                        "epChannel" => true,
                    ),
                ),
                "123456",
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 0xFC,
                            "type" => "TestInputDriver2",
                            "dataType" => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                "640000",
                300,
                array(
                    "value" => 12.5,
                    "units" => "&#176;C",
                    "unitType" => "Temperature",
                    "dataType" => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                    "raw" => 50,
                ),
                array(
                    array(
                        "value" => 25.2134,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    ),
                    array(
                        "value" => 28.5282,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    ),
                    array(
                        "value" => 12.3455,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    ),
                    array(
                        "value" => 82.1253,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    ),
                ),
                array(
                    array(
                        "value" => 25,
                        "units" => "&#176;C",
                        'maxDecimals' => 4,
                        'storageUnit' => '&#176;C',
                        "unitType" => "Temperature",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                        "raw" => 100,
                        "decimals" => 4,
                        "index" => 0,
                        "epChannel" => true,
                    ),
                ),
                "",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param object $config    The configuration to use
    * @param object $class     The table class to use
    * @param string $string    String returned by the device
    * @param float  $deltaT    The time delta in seconds between this record
    * @param array  $prev      The previous reading
    * @param array  $data      The data from the other inputs that were crunched
    * @param array  $expect    The expected data
    * @param array  $strExpect The expected string afterwards
    *
    * @return null
    *
    * @dataProvider dataDecodeData
    */
    public function testDecodeData(
        $config, $class, $string, $deltaT, $prev, $data, $expect, $strExpect
    ) {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyBase("Device");
        $sys->resetMock($config);
        $obj = Input::factory($sys, null, $class, $dev);
        $ret = $obj->decodeData($string, $deltaT, $prev, $data);
        $this->assertEquals($expect, $ret, "Return wrong");
        $this->assertSame($strExpect, $string, "String wrong");
    }
    /**
    * data provider for testConvertUnits
    *
    * @return array
    */
    public static function dataConvertUnits()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 0x04,
                            "type" => "",
                            "units" => "&#176;F",
                            "decimals" => 2,
                        )
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                array(
                    "value" => 0.0,
                    "units" => "&#176;C",
                    "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    "unitType" => "Temperature",
                ),
                "K",
                array(
                    "value" => 273.15,
                    "units" => "K",
                    "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    "unitType" => "Temperature",
                ),
                true,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 0x04,
                            "type" => "",
                            "units" => "&#176;F",
                            "decimals" => 2,
                        )
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                array(
                    "value" => 0.0,
                    "units" => "&#176;C",
                    "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    "unitType" => "Temperature",
                ),
                null,
                array(
                    "value" => 32.0,
                    "units" => "&#176;F",
                    "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    "unitType" => "Temperature",
                ),
                true,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 0x04,
                            "type" => "",
                            "units" => "&#176;F",
                            "decimals" => 2,
                        )
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                array(
                    "value" => 12.0,
                    "units" => "&#176;C",
                    "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    "unitType" => "Temperature",
                ),
                "psi",
                array(
                    "value" => 12.0,
                    "units" => "&#176;C",
                    "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    "unitType" => "Temperature",
                ),
                false,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 0x04,
                            "type" => "",
                            "units" => "&#176;F",
                            "decimals" => 2,
                        )
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                array(
                    "value" => null,
                    "units" => "&#176;C",
                    "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    "unitType" => "Temperature",
                ),
                "&#176;F",
                array(
                    "value" => null,
                    "units" => "&#176;C",
                    "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    "unitType" => "Temperature",
                ),
                true,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param object $config The configuration to use
    * @param object $class  The table class to use
    * @param mixed  $data   The value to send
    * @param string $units  The units to convert to
    * @param string $expect The expected data
    * @param bool   $ret    The return value expected
    *
    * @return null
    *
    * @dataProvider dataConvertUnits
    */
    public function testConvertUnits($config, $class, $data, $units, $expect, $ret)
    {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyBase("Device");
        $sys->resetMock($config);
        $obj = Input::factory($sys, null, $class, $dev);
        $this->assertSame(
            $ret,
            $obj->convertUnits($data, $units),
            "The return value is wrong"
        );
        $this->assertSame($expect, $data, "Data is wrong");
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataChannels()
    {
        return array(
            array(
                new \HUGnet\DummySystem(),
                null,
                array(
                    "id" => 5,
                ),
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 5,
                            "input" => 4,
                            "HWPartNum"    => "0039-12-01-C",
                            "FWPartNum"    => "0039-20-03-C",
                            "FWVersion"    => "1.2.3",
                            "DeviceGroup"  => "FFFFFF",
                            "TimeConstant" => "01",
                            "location" => "Test",
                        ),
                    ),
                ),
                array(
                    array(
                        "decimals" => 2,
                        "units"    => 'unknown',
                        "unitType" => 'unknown',
                        'maxDecimals' => 2,
                        'storageUnit' => 'unknown',
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "input"   => 4,
                        "label" => "Test",
                        "index" => 0,
                        "epChannel" => true,
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $config The configuration to use
    * @param mixed  $device The device to set
    * @param mixed  $class  This is either the name of a class or an object
    * @param array  $mocks  The mocks to use
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataChannels
    */
    public function testChannels(
        $config, $device, $class, $mocks, $expect
    ) {
        $config->resetMock($mocks);
        $dev = new \HUGnet\DummyBase("Device");
        $obj = Input::factory($config, $device, $class, $dev);
        $this->assertEquals(
            $expect, $obj->Channels(), "Return Wrong"
        );
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataChannelStart()
    {
        return array(
            array( // #0
                new \HUGnet\DummySystem("System"),
                null,
                array(
                    "id" => 5,
                ),
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 5,
                            "input" => 4,
                            "dev" => 1234,
                            "HWPartNum"    => "0039-12-01-C",
                            "FWPartNum"    => "0039-20-03-C",
                            "FWVersion"    => "1.2.3",
                            "DeviceGroup"  => "FFFFFF",
                            "TimeConstant" => "01",
                            "location" => "Test",
                        ),
                    ),
                    "Device" => array(
                        "input" => array(
                            "0" => new \HUGnet\DummyTable("Input0"),
                            "1" => new \HUGnet\DummyTable("Input1"),
                            "2" => new \HUGnet\DummyTable("Input2"),
                            "3" => new \HUGnet\DummyTable("Input3"),
                            "4" => new \HUGnet\DummyTable("Input4"),
                            "5" => new \HUGnet\DummyTable("Input5"),
                        ),
                    ),
                    "Input0" => array("channels" => array(0)),
                    "Input1" => array("channels" => array(0, 1)),
                    "Input2" => array("channels" => array(0, 2, 3)),
                    "Input3" => array("channels" => array(0)),
                    "Input4" => array("channels" => array(0)),
                    "Input5" => array("channels" => array(0)),
                ),
                7,
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $config The configuration to use
    * @param mixed  $device The device to set
    * @param mixed  $class  This is either the name of a class or an object
    * @param array  $mocks  The mocks to use
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataChannelStart
    */
    public function testChannelStart(
        $config, $device, $class, $mocks, $expect
    ) {
        $config->resetMock($mocks);
        $dev = new \HUGnet\DummyBase("Device");
        $obj = Input::factory($config, $device, $class, $dev);
        $this->assertEquals(
            $expect, $obj->ChannelStart(), "Return Wrong"
        );
        unset($obj);
    }

}

namespace HUGnet\devices\inputTable\drivers;

/**
 * Default input driver
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Inputs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class TestInputDriver1 extends \HUGnet\devices\inputTable\Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Silly Input Driver 1",
        "shortName" => "SSD1",
        "unitType" => "Temperature",
        "storageUnit" => 'getExtra1',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array("Silliness Factor", "Storage Unit"),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, array('&#176;C', '&#176;F', 'K')),
        "extraDefault" => array(2210, '&#176;C'),
        "maxDecimals" => 4,
    );
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other inputs that were crunched
    * @param mixed $prev   The previous value for this input
    *
    * @return mixed The value in whatever the units are in the input
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        return $A * 2;
    }

}
/**
 * Default input driver
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Inputs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class TestInputDriver2 extends \HUGnet\devices\inputTable\Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Silly Input Driver 2",
        "shortName" => "SSD2",
        "unitType" => "Temperature",
        "storageUnit" => 'getExtra1',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
        "extraText" => array("Silliness Factor", "Storage Unit"),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, array('&#176;C', '&#176;F', 'K')),
        "extraDefault" => array(2210, '&#176;C'),
        "maxDecimals" => 4,
    );
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other inputs that were crunched
    * @param mixed $prev   The previous value for this input
    *
    * @return mixed The value in whatever the units are in the input
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        return $A / 2;
    }

}


?>
