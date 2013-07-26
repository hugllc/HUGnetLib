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
namespace HUGnet\devices\inputTable\drivers\virtual;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBaseVirtual.php";
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/drivers/virtual/CloneVirtual.php';
/** This is a required class */
require_once CODE_BASE.'devices/Input.php';
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/tables/ADuCInputTable.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';

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
class CloneVirtualTest extends DriverTestBaseVirtual
{
    /** This is the class we are testing */
    protected $class = "CloneVirtual";
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
                    "system" => new \HUGnet\DummySystem("System"),
                ),
            )
        );
        $this->o = \HUGnet\devices\inputTable\DriverVirtual::factory(
            "CloneVirtual", $sensor
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
                        "id" => 1,
                        "get" => array(
                            "sensor" => 1,
                            "extra" => array(1008, 1),
                            "location" => "asdf",
                        ),
                        "system" => new \HUGnet\DummySystem("System"),
                        "table" =>  new \HUGnet\DummyTable("InputTable")
                    ),
                    "System" => array(
                        "device" => array(
                            0x1008 => new \HUGnet\DummyTable("Device1008"),
                        ),
                    ),
                    "Device1008" => array(
                        "input" => new \HUGnet\DummyTable("Input1")
                    ),
                    "Input1" => array(
                        "id" => 2,
                        "get" => array(
                            "sensor" => 2,
                            "driver" => "ADuCVoltage",
                            "extra" => array(1008, 1),
                            "location" => "asdf 1",
                        ),
                        "channels" => array(
                        ),
                        "table" =>  new \HUGnet\DummyTable("Input1Table")
                    ),
                    "Input1Table" => array(
                        "get" => array(
                            "tableEntry" => array(),
                        ),
                    ),
                    "InputTable" => array(
                        "get" => array(
                            "tableEntry" => array(),
                        ),
                    ),
                ),
                null,
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => null,
                        "decimals" => 8,
                        "units" => "V",
                        'maxDecimals' => 8,
                        'storageUnit' => 'V',
                        "unitType" => "Voltage",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf 1",
                        "index" => 0,
                        "epChannel" => false,
                    ),
                ),
            ),
            array( // #1
                array(
                    "Input" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 1,
                            "extra" => array(1008, 1),
                            "location" => "asdf",
                        ),
                        "system" => new \HUGnet\DummySystem("System"),
                        "table" =>  new \HUGnet\DummyTable("InputTable")
                    ),
                    "System" => array(
                        "device" => array(
                            0x1008 => new \HUGnet\DummyTable("Device1008"),
                        ),
                    ),
                    "Device1008" => array(
                        "input" => new \HUGnet\DummyTable("Input1")
                    ),
                    "Input1" => array(
                        "id" => 1,
                        "get" => array(
                            "driver" => "ADuCPower",
                            "sensor" => 1,
                            "location" => "asdf1",
                        ),
                        "channelStart" => 4,
                        "table" =>  new \HUGnet\DummyTable("Input1Table")
                    ),
                    "Input1Table" => array(
                        "get" => array(
                            "tableEntry" => array(),
                        ),
                    ),
                    "InputTable" => array(
                        "get" => array(
                            "tableEntry" => array(),
                        ),
                    ),
                ),
                array(
                    "Data4" => 0.314713,
                    "Data5" => 14.448166,
                    "Data6" => 4.547026,
                    "Data7" => 45.909022,
                ),
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => 0.314713,
                        "decimals" => 8,
                        "units" => "A",
                        "maxDecimals" => 8,
                        'storageUnit' => 'A',
                        "unitType" => "Current",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf1 0",
                        "index" => 0,
                        "epChannel" => false,
                    ),
                    array(
                        "value" => 14.448166,
                        "decimals" => 8,
                        "units" => "V",
                        "maxDecimals" => 8,
                        'storageUnit' => 'V',
                        "unitType" => "Voltage",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf1 1",
                        "index" => 1,
                        "epChannel" => false,
                    ),
                    array(
                        "value" => 4.547026,
                        "decimals" => 8,
                        "units" => "W",
                        "maxDecimals" => 8,
                        'storageUnit' => 'W',
                        "unitType" => "Power",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf1 2",
                        "index" => 2,
                        "epChannel" => false,
                    ),
                    array(
                        "value" => 45.909022,
                        "decimals" => 8,
                        "units" => "Ohms",
                        "maxDecimals" => 8,
                        'storageUnit' => 'Ohms',
                        "unitType" => "Impedance",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf1 3",
                        "index" => 3,
                        "epChannel" => false,
                    ),
                ),
            ),
            array( // #1
                array(
                    "Input" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 1,
                            "extra" => array(1008, 3),
                            "location" => "asdf",
                        ),
                        "system" => new \HUGnet\DummySystem("System"),
                        "table" =>  new \HUGnet\DummyTable("InputTable"),
                    ),
                    "System" => array(
                        "device" => array(
                            0x1008 => new \HUGnet\DummyTable("Device1008"),
                        ),
                    ),
                    "Device1008" => array(
                        "input" => new \HUGnet\DummyTable("Input1")
                    ),
                    "Input1" => array(
                        "id" => 3,
                        "get" => array(
                            "driver" => "ADuCPower",
                            "sensor" => 3,
                            "location" => "asdf1",
                        ),
                        "channelStart" => 4,
                        "table" =>  new \HUGnet\DummyTable("Input1Table"),
                    ),
                ),
                array(
                    "Data4" => 0.314713,
                    "Data5" => 14.448166,
                    "Data6" => 4.547026,
                    "Data7" => 45.909022,
                ),
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => 0.314713,
                        "decimals" => 8,
                        "units" => "A",
                        "maxDecimals" => 8,
                        'storageUnit' => 'A',
                        "unitType" => "Current",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf1 0",
                        "index" => 0,
                        "epChannel" => false,
                    ),
                    array(
                        "value" => 14.448166,
                        "decimals" => 8,
                        "units" => "V",
                        "maxDecimals" => 8,
                        'storageUnit' => 'V',
                        "unitType" => "Voltage",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf1 1",
                        "index" => 1,
                        "epChannel" => false,
                    ),
                    array(
                        "value" => 4.547026,
                        "decimals" => 8,
                        "units" => "W",
                        "maxDecimals" => 8,
                        'storageUnit' => 'W',
                        "unitType" => "Power",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf1 2",
                        "index" => 2,
                        "epChannel" => false,
                    ),
                    array(
                        "value" => 45.909022,
                        "decimals" => 8,
                        "units" => "Ohms",
                        "maxDecimals" => 8,
                        'storageUnit' => 'Ohms',
                        "unitType" => "Impedance",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf1 3",
                        "index" => 3,
                        "epChannel" => false,
                    ),
                ),
            ),
            array( // #1
                array(
                    "Input" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 1,
                            "extra" => array(1008, 1),
                            "location" => "asdf",
                        ),
                        "system" => new \HUGnet\DummySystem("System"),
                        "table" =>  new \HUGnet\DummyTable("InputTable"),
                    ),
                    "System" => array(
                        "device" => array(
                            0x1008 => new \HUGnet\DummyTable("Device1008"),
                        ),
                    ),
                    "Device1008" => array(
                        "input" => new \HUGnet\DummyTable("Input1")
                    ),
                    "Input1" => array(
                        "id" => 1,
                        "get" => array(
                            "driver" => "ADuCPower",
                            "sensor" => 1,
                            "location" => "asdf1",
                        ),
                        "channelStart" => 4,
                        "table" =>  new \HUGnet\DummyTable("Input1Table"),
                    ),
                    "History" => array(
                        "get" => array(
                            "Data4" => 0.314713,
                            "Data5" => 14.448166,
                            "Data6" => 4.547026,
                            "Data7" => 45.909022,
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("History"),
                1,
                array(),
                array(),
                array(
                    array(
                        "value" => 0.314713,
                        "decimals" => 8,
                        "units" => "A",
                        "maxDecimals" => 8,
                        'storageUnit' => 'A',
                        "unitType" => "Current",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf1 0",
                        "index" => 0,
                        "epChannel" => false,
                    ),
                    array(
                        "value" => 14.448166,
                        "decimals" => 8,
                        "units" => "V",
                        "maxDecimals" => 8,
                        'storageUnit' => 'V',
                        "unitType" => "Voltage",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf1 1",
                        "index" => 1,
                        "epChannel" => false,
                    ),
                    array(
                        "value" => 4.547026,
                        "decimals" => 8,
                        "units" => "W",
                        "maxDecimals" => 8,
                        'storageUnit' => 'W',
                        "unitType" => "Power",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf1 2",
                        "index" => 2,
                        "epChannel" => false,
                    ),
                    array(
                        "value" => 45.909022,
                        "decimals" => 8,
                        "units" => "Ohms",
                        "maxDecimals" => 8,
                        'storageUnit' => 'Ohms',
                        "unitType" => "Impedance",
                        "dataType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        "label" => "asdf1 3",
                        "index" => 3,
                        "epChannel" => false,
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
        $sen = new \HUGnet\DummyBase("Input");
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
                array(
                    "Input" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "get" => array(
                            "extra" => array("0000AC", 3),
                        ),
                    ),
                    "System" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "input" => new \HUGnet\DummyBase("Input2"),
                    ),
                    "Input2" => array(
                        "get" => array(
                            "driver" => "AVRBC2322640",
                        ),
                    ),
                ),
                "storageUnit",
                '&#176;C',
            ),
            array(
                array(
                    "Input" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "get" => array(
                            "extra" => array("0000AC", 3),
                        ),
                    ),
                    "System" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "input" => new \HUGnet\DummyBase("Input2"),
                    ),
                    "Input2" => array(
                        "get" => array(
                            "driver" => "AVRBC2322640_0",
                        ),
                    ),
                ),
                "unitType",
                'Temperature',
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
    * @dataProvider dataGet
    */
    public function testGet($mocks, $name, $expect)
    {
        $sens = new \HUGnet\DummyTable("Input");
        $sens->resetMock($mocks);
        $this->assertSame($expect, $this->o->get($name));
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function data2Array()
    {
        return array(
            array(
                array(
                    "Input" => array(
                        "system" => new \HUGnet\DummySystem("System"),
                        "get" => array(
                            "extra" => array("0000AC", 3),
                        ),
                    ),
                    "System" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "input" => new \HUGnet\DummyBase("Input2"),
                    ),
                    "Input2" => array(
                        "get" => array(
                            "driver" => "AVRBC2322640",
                        ),
                    ),
                ),
                array(
                    'longName'     => 'BC Components Thermistor #2322640',
                    'shortName'    => 'CloneVirtual',
                    'unitType'     => 'Temperature',
                    'bound'        => false,
                    'virtual'      => true,
                    'total'        => false,
                    'extraText'    => array('Device ID', 'Input'),
                    'extraDefault' => array('', ''),
                    'extraValues'  => array(8, 3),
                    "extraDesc"    => array(
                        "The DeviceID of the board (in hexidecimal)",
                        "The INPUT to clone.  Zero based."
                    ),
                    'storageUnit'  => '&#176;C',
                    'storageType'  => 'raw',
                    'maxDecimals'  => 2,
                    'dataTypes'    => array(
                        'raw'    => 'raw',
                        'diff'   => 'diff',
                        'ignore' => 'ignore',
                    ),
                    'inputSize'    => 3,
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $mocks  The mocks to set
    * @param array $expect The expected return
    *
    * @return null
    *
    * @dataProvider data2Array
    */
    public function test2Array($mocks, $expect)
    {
        $sens = new \HUGnet\DummyTable("Input");
        $sens->resetMock($mocks);
        $this->assertEquals($expect, $this->o->toArray());
    }

}
?>
