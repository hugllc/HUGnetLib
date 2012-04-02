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
namespace HUGnet;
/** This is a required class */
require_once CODE_BASE.'system/Device.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once CODE_BASE.'system/Sensor.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyNetwork.php';
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
class DeviceTest extends \PHPUnit_Framework_TestCase
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
                new DummySystem(),
                null,
                "DummyTable",
                array(
                    "Table" => array(
                        "clearData" => array(array()),
                    ),
                ),
            ),
            array(
                new DummySystem(),
                array(
                    "id" => 5,
                    "name" => 3,
                    "value" => 1,
                ),
                "DummyTable",
                array(
                    "Table" => array(
                        "fromAny" => array(
                            array(
                                array(
                                    "id" => 5,
                                    "name" => 3,
                                    "value" => 1,
                                ),
                            ),
                        ),
                        "get" => array(
                            array("HWPartNum"),
                            array("FWPartNum"),
                            array("FWVersion"),
                        ),
                        "set" => array(
                            array("Driver", "EDEFAULT"),
                        ),
                        "clearData" => array(array()),
                    ),
                ),
            ),
            array(
                new DummySystem(),
                2,
                new DummyTable(),
                array(
                    "Table" => array(
                        "getRow" => array(
                            array(0 => 2),
                        ),
                        "set" => array(
                            array("id", 2),
                        ),
                        "clearData" => array(array()),
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config      The configuration to use
    * @param mixed $device      The gateway to set
    * @param mixed $class       This is either the name of a class or an object
    * @param array $expectTable The table to expect
    *
    * @return null
    *
    * @dataProvider dataCreate
    */
    public function testCreate($config, $device, $class, $expectTable)
    {
        $table = new DummyTable();
        // This just resets the mock
        $table->resetMock();
        $obj = Device::factory($config, $device, $class);
        // Make sure we have the right object
        $table = $this->readAttribute($obj, "_table");
        if (is_object($table)) {
            $this->assertEquals($expectTable, $table->retrieve(), "Data Wrong");
        }
        unset($obj);
    }

    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataNetwork()
    {
        return array(
            array(
                new DummySystem(),
                array(
                    "id" => 5,
                    "name" => 3,
                    "value" => 1,
                ),
                "DummyTable",
                array(
                    "System" => array(
                        "network" => new \HUGnet\network\DummyNetwork("Network"),
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $device The device to set
    * @param mixed $class  This is either the name of a class or an object
    * @param array $mocks  The mocks to use
    *
    * @return null
    *
    * @dataProvider dataNetwork
    */
    public function testNetwork(
        $config, $device, $class, $mocks
    ) {
        $config->resetMock($mocks);
        $obj = Device::factory($config, $device, $class);
        $this->assertEquals(
            "HUGnet\devices\Network", get_class($obj->network()), "Wrong Class"
        );
        $this->assertSame($obj->network(), $obj->network(), "Wrong Object Returned");
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataConfig()
    {
        return array(
            array(
                new DummySystem(),
                array(
                    "id" => 5,
                    "name" => 3,
                    "value" => 1,
                ),
                "DummyTable",
                array(
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $device The device to set
    * @param mixed $class  This is either the name of a class or an object
    * @param array $mocks  The mocks to use
    *
    * @return null
    *
    * @dataProvider dataConfig
    */
    public function testConfig(
        $config, $device, $class, $mocks
    ) {
        $config->resetMock($mocks);
        $obj = Device::factory($config, $device, $class);
        $this->assertEquals(
            "HUGnet\devices\Config", get_class($obj->config()), "Wrong Class"
        );
        $this->assertSame($obj->config(), $obj->config(), "Wrong Object Returned");
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataGet()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "Driver" => "EDEFAULT",
                            "id" => 2,
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "id",
                2,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "Driver" => "EDEFAULT",
                            "id" => 2,
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "packetTimeout",
                5,
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $config The configuration to use
    * @param mixed  $class  This is either the name of a class or an object
    * @param string $field  The field to get
    * @param mixed  $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet(
        $config, $class, $field, $expect
    ) {
        $sys = new DummySystem("System");
        $sys->resetMock($config);
        $obj = Device::factory($sys, null, $class);
        $this->assertSame($expect, $obj->get($field));
        unset($obj);
    }

    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataJson()
    {
        $sensors = array();
        $obj = Sensor::factory(
            new DummySystem("TestStuff"),
            array(
            )
        );
        $obj2 = Sensor::factory(
            new DummySystem("TestStuff"),
            array(
                'id' => 0xFE,
            )
        );
        /* Physical Sensors */
        for ($i = 0; $i < 9; $i++) {
            $sensors[$i] = json_decode($obj->json(), true);
        }
        /* Virtual Sensors */
        for ($i = 9; $i < 13; $i++) {
            $sensors[$i] = json_decode($obj2->json(), true);
        }
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "Driver" => "EDEFAULT",
                            "id" => 2,
                        ),
                        "toArray" => array(
                            "id" => 2,
                            "asdf" => 3,
                            "params" => json_encode(array(1,2,3,4)),
                        ),
                    ),
                ),
                new DummyTable("Table"),
                json_encode(
                    array(
                        'packetTimeout' => 5,
                        'totalSensors' => 13,
                        'physicalSensors' => 9,
                        'virtualSensors' => 4,
                        'historyTable' => 'EDEFAULTHistoryTable',
                        'averageTable' => 'EDEFAULTAverageTable',
                        'loadable' => false,
                        'bootloader' => false,
                        'outputSize' => 3,
                        'id' => 2,
                        'asdf' => 3,
                        'params' => array(1,2,3,4),
                        'sensors' => $sensors,
                    )
                ),
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "Driver" => "EDEFAULT",
                            "id" => 2,
                        ),
                        "toArray" => array(
                            "id" => 2,
                            "asdf" => 3,
                            "params" => base64_encode(
                                serialize(
                                    array("DriverInfo" => array(1,2,3,4))
                                )
                            ),
                        ),
                    ),
                ),
                new DummyTable("Table"),
                json_encode(
                    array(
                        'packetTimeout' => 5,
                        'totalSensors' => 13,
                        'physicalSensors' => 9,
                        'virtualSensors' => 4,
                        'historyTable' => 'EDEFAULTHistoryTable',
                        'averageTable' => 'EDEFAULTAverageTable',
                        'loadable' => false,
                        'bootloader' => false,
                        'outputSize' => 3,
                        'id' => 2,
                        'asdf' => 3,
                        'params' => array(1,2,3,4),
                        'sensors' => $sensors,
                    )
                ),
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "Driver" => "EDEFAULT",
                            "id" => 2,
                        ),
                        "toArray" => array(
                            "id" => 2,
                            "asdf" => 3,
                            "params" => base64_encode(
                                serialize(
                                    array("DriverInfo" => array(1,2,3,4))
                                )
                            ),
                            "loadable" => true,
                        ),
                    ),
                ),
                new DummyTable("Table"),
                json_encode(
                    array(
                        'packetTimeout' => 5,
                        'totalSensors' => 13,
                        'physicalSensors' => 9,
                        'virtualSensors' => 4,
                        'historyTable' => 'EDEFAULTHistoryTable',
                        'averageTable' => 'EDEFAULTAverageTable',
                        'loadable' => true,
                        'bootloader' => false,
                        'outputSize' => 3,
                        'id' => 2,
                        'asdf' => 3,
                        'params' => array(1,2,3,4),
                        'sensors' => $sensors,
                    )
                ),
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
    * @dataProvider dataJson
    */
    public function testJson(
        $config, $class, $expect
    ) {
        $sys = new DummySystem("System");
        $sys->resetMock($config);
        $obj = Device::factory($sys, null, $class);
        $json = $obj->json();
        $this->assertSame($expect, $json);
        unset($obj);
    }
    /**
    * Data provider for testGetParam
    *
    * @return array
    */
    public static function dataGetParam()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => base64_encode(
                                serialize(
                                    array(
                                        "A" => 1,
                                        "B" => 2,
                                        "C" => 3,
                                    )
                                )
                            ),
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "B",
                2,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => base64_encode(
                                serialize(
                                    array(
                                        "DriverInfo" => array(
                                            "A" => 1,
                                            "B" => 2,
                                            "C" => 3,
                                        ),
                                    )
                                )
                            ),
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "B",
                2,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => json_encode(
                                array(
                                    "A" => 1,
                                    "B" => 2,
                                    "C" => 3,
                                )
                            ),
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "B",
                2,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "B",
                null,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => json_encode(
                                array(
                                    "A" => 1,
                                    "B" => 2,
                                    "C" => 3,
                                )
                            ),
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "Q",
                null,
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $config The configuration to use
    * @param mixed  $class  This is either the name of a class or an object
    * @param string $field  The field to get
    * @param mixed  $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataGetParam
    */
    public function testGetParam(
        $config, $class, $field, $expect
    ) {
        $sys = new DummySystem("System");
        $sys->resetMock($config);
        $obj = Device::factory($sys, null, $class);
        $this->assertSame($expect, $obj->getParam($field));
        unset($obj);
    }
    /**
    * Data provider for testGetParam
    *
    * @return array
    */
    public static function dataSetParam()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => json_encode(
                                array(
                                    "A" => 1,
                                    "B" => 2,
                                    "C" => 3,
                                )
                            ),
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "B",
                4,
                array(
                    'Table' => array(
                        'get' => array(array("params"), array("params"),),
                        'set' => array(
                            array(
                                'params',
                                json_encode(
                                    array("A" => 1, "B" => 4, "C" => 3,)
                                ),
                            ),
                        ),
                        "clearData" => array(array()),
                    ),
                ),
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "B",
                5,
                array(
                    'Table' => array(
                        'get' => array(array("params"), array("params"),),
                        'set' => array(
                            array(
                                'params',
                                json_encode(
                                    array("B" => 5,)
                                ),
                            ),
                        ),
                        "clearData" => array(array()),
                    ),
                ),
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => json_encode(
                                array(
                                    "A" => 1,
                                    "B" => 2,
                                    "C" => 3,
                                )
                            ),
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "Q",
                8,
                array(
                    'Table' => array(
                        'get' => array(array("params"), array("params"),),
                        'set' => array(
                            array(
                                'params',
                                json_encode(
                                    array("A" => 1, "B" => 2, "C" => 3, "Q" => 8)
                                ),
                            ),
                        ),
                        "clearData" => array(array()),
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $config The configuration to use
    * @param mixed  $class  This is either the name of a class or an object
    * @param string $field  The field to set
    * @param mixed  $value  The value to set the field to
    * @param mixed  $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataSetParam
    */
    public function testSetParam(
        $config, $class, $field, $value, $expect
    ) {
        $sys = new DummySystem("System");
        $sys->resetMock($config);
        $obj = Device::factory($sys, null, $class);
        $obj->setParam($field, $value);
        $this->assertEquals($expect, $sys->retrieve());
        unset($obj);
    }
    /**
    * data provider for testSensor
    *
    * @return array
    */
    public static function dataDecodeSensorData()
    {
        return array(
            array(
                array(
                    "SDTable" => array(
                        "get" => array(
                            "id" => 5,
                        ),
                    ),
                ),
                new DummyTable("SDTable"),
                array(
                    "deltaT" => 1,
                    0 => 10,
                    1 => 20,
                    2 => 30,
                    3 => 40,
                    4 => 50,
                    5 => 60,
                    6 => 70,
                    7 => 80,
                    8 => 90,
                    9 => 100,
                    10 => 110,
                    11 => 120,
                    12 => 130,
                ),
                array(
                    null, 10, null, null
                ),
                array(
                    "deltaT" => 1,
                    array(
                        "value" => 10,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 20,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 30,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 40,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 50,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 60,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 70,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 80,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 90,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 100,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 110,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 120,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 130,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $config The configuration to use
    * @param mixed  $class  This is either the name of a class or an object
    * @param string $data   The data to use
    * @param array  $prev   The previous reading
    * @param array  $expect The expected data
    *
    * @return null
    *
    * @dataProvider dataDecodeSensorData
    */
    public function testDecodeSensorData(
        $config, $class, $data, $prev, $expect
    ) {
        $sys = new DummySystem("System");
        $sys->resetMock($config);
        $obj = Device::factory($sys, null, $class);
        $data = $obj->decodeSensorData($data, $prev);
        $this->assertSame($expect, $data);
    }
    /**
    * data provider for testSensor
    *
    * @return array
    */
    public static function dataDecodeData()
    {
        return array(
            array(
                array(
                    "SDTable" => array(
                        "get" => array(
                            "id" => 5,
                        ),
                    ),
                ),
                new DummyTable("SDTable"),
                "010001100000200000300000400000500000600000700000800000900000",
                0x55,
                300,
                array(
                    null, 10, null, null
                ),
                array(
                    "deltaT" => 300,
                    "DataIndex" => 1,
                    "timeConstant" => 1,
                    array(
                        "value" => 0x10,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 0x20,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 0x30,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 0x40,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 0x50,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 0x60,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 0x70,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 0x80,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => 0x90,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => null,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => null,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => null,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                    array(
                        "value" => null,
                        "units" => "unknown",
                        "unitType" => "unknown",
                        "dataType" => \UnitsBase::TYPE_RAW,
                    ),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $config  The configuration to use
    * @param mixed  $class   This is either the name of a class or an object
    * @param string $data    The data to use
    * @param int    $command The command that was used
    * @param float  $deltaT  The number of seconds between readings
    * @param array  $prev    The previous reading
    * @param array  $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataDecodeData
    */
    public function testDecodeData(
        $config, $class, $data, $command, $deltaT, $prev, $expect
    ) {
        $sys = new DummySystem("System");
        $sys->resetMock($config);
        $obj = Device::factory($sys, null, $class);
        $data = $obj->decodeData($data, $command, $deltaT, $prev);
        $this->assertEquals($expect, $data);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataSensor()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 5,
                            "sensors" => array(array("id" => 0x15)),
                        ),
                    ),
                ),
                "DummyTable",
                0,
                "\HUGnet\Sensor",
                0x15,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 5,
                            "sensors" => base64_encode(
                                serialize(
                                    array(
                                        array("id" => 0x18),
                                    )
                                )
                            ),
                        ),
                    ),
                ),
                "DummyTable",
                0,
                "\HUGnet\Sensor",
                0x18,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 5,
                            "sensors" => true,
                        ),
                    ),
                ),
                "DummyTable",
                10,
                "\HUGnet\Sensor",
                0xFE,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 5,
                            "sensors" => true,
                            "RawSetup" => "000000100800393701410039380143000004"
                            ."FFFFFFFF01044242424241414141"
                        ),
                    ),
                ),
                "DummyTable",
                1,
                "\HUGnet\Sensor",
                0x42,
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $config       The configuration to use
    * @param mixed  $class        This is either the name of a class or an object
    * @param string $sensor       The driver to tell it to load
    * @param string $driverExpect The driver we expect to be loaded
    * @param int    $expect       The expected sensor id
    *
    * @return null
    *
    * @dataProvider dataSensor
    */
    public function testSensor(
        $config, $class, $sensor, $driverExpect, $expect
    ) {
        $sys = new DummySystem("System");
        $sys->resetMock($config);
        $obj = Device::factory($sys, null, $class);
        $sen = $obj->sensor($sensor);
        $this->assertTrue(
            is_a($sen, $driverExpect),
            "Return is not a ".$driverExpect
        );
        $this->assertSame($sen->get("id"), $expect, "Wrong sensor returned");
        unset($obj);
    }
    /**
    * Data provider for testHistoryFactory
    *
    * @return array
    */
    public static function dataHistoryFactory()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "Driver" => "EDEFAULT",
                            "id" => 2,
                        ),
                    ),
                ),
                new DummyTable("Table"),
                array(
                ),
                true,
                'EDEFAULTHistoryTable',
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "Driver" => "",
                            "id" => 2,
                        ),
                    ),
                ),
                new DummyTable("Table"),
                array(
                ),
                false,
                'EDEFAULTAverageTable',
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config  The configuration to use
    * @param mixed $class   This is either the name of a class or an object
    * @param array $data    The data to build the history record with.
    * @param bool  $history History if true, average if false
    * @param mixed $expect  The value we expect back
    *
    * @return null
    *
    * @dataProvider dataHistoryFactory
    */
    public function testHistoryFactory(
        $config, $class, $data, $history, $expect
    ) {
        $sys = new DummySystem("System");
        $sys->resetMock($config);
        $obj = Device::factory($sys, null, $class);
        $hist = $obj->historyFactory($data, $history);
        $this->assertSame($expect, get_class($hist));
        unset($obj);
    }
}

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
class DeviceTestDriver1
{
    /**
    * This function creates the system.
    *
    * @param object &$device The device class
    * @param string $driver  The driver to load
    *
    * @return null
    */
    public static function &factory(&$device, $driver)
    {
        $object = new DeviceTestDriver1;
        $object->device = &$device;
        $object->driver = $driver;
        return $object;
    }
}
?>
