<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../base/UnitsBase.php';
require_once dirname(__FILE__).'/../../containers/DeviceSensorsContainer.php';
require_once dirname(__FILE__).'/../../containers/DeviceParamsContainer.php';
require_once dirname(__FILE__).'/../../containers/ConfigContainer.php';
require_once dirname(__FILE__).'/../stubs/DummyDeviceContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceSensorsContainerTest extends PHPUnit_Framework_TestCase
{

    /**
    * Sets up the fixture, for example, open a network connection.
    * This method is called before a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function setUp()
    {
        $config = array(
            "sockets" => array(
                array(
                    "dummy" => true,
                ),
            ),
            "plugins" => array(
                "dir" => realpath(dirname(__FILE__)."/../files/plugins/"),
            ),
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->d = new DeviceContainer();
        $this->o = new DeviceSensorsContainer($data, $this->d);
    }

    /**
    * Tears down the fixture, for example, close a network connection.
    * This method is called after a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function tearDown()
    {
        unset($this->o);
    }


    /**
    * data provider for testConstructor
    *
    * @return array
    */
    public static function dataConstructor()
    {
        return array(
            array(
                array(),
                array(
                    "RawCalibration" => "",
                    "Sensors" => 0,
                    "ActiveSensors" => 0,
                    "PhysicalSensors" => 0,
                    "VirtualSensors" => 0,
                    "forceSensors" => false,
                ),
            ),
            array(
                array(
                    "RawCalibration" => "Hello There",
                    "Sensors" => 10,
                ),
                array(
                    "RawCalibration" => "Hello There",
                    "Sensors" => 10,
                    "ActiveSensors" => 0,
                    "PhysicalSensors" => 0,
                    "VirtualSensors" => 0,
                    "forceSensors" => false,
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to give to the constructor
    * @param string $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $expect)
    {
        $o = new DeviceSensorsContainer($preload, $this->d);
        $this->assertAttributeSame($expect, "data", $o, "Wrong data in class");
        $config = $this->readAttribute($o, "myConfig");
        $this->assertSame(
            "ConfigContainer", get_class($config), "Wrong config class"
        );
        $device = $this->readAttribute($o, "myDevice");
        $this->assertSame(
            get_class($this->d), get_class($device), "Wrong device class"
        );
    }
    /**
    * data provider for testConstructor
    *
    * @return array
    */
    public static function dataFromArray()
    {
        return array(
            array(
                array(
                    0 => array("id" => 0),
                    1 => array("id" => 2),
                ),
                array(
                    "ActiveSensors" => 2,
                    "DriverInfo" => array(
                        "PhysicalSensors" => 2,
                        "VirtualSensors" => 0,
                    ),
                    "params" => array(
                        "sensorType" => array("Test1Sensor", "a"),
                        "dType" => array("raw", "diff"),
                        "Loc" => array("Here", "There"),
                        "Extra" => array(array(1,2), array(3,4)),
                    )
                ),
                array(
                    "Sensors" => 2,
                    "ActiveSensors" => 2,
                    "PhysicalSensors" => 2,
                    array(
                        "id" => 0,
                        "type" => "Test1Sensor",
                        "location" => "Here",
                        "extra" => array(1,2),
                    ),
                    array(
                        "id" => 2,
                        "type" => "a",
                        "location" => "There",
                        "extra" => array(3,4),
                    ),
                ),
                array(
                    "Test1Sensor",
                    "Test2Sensor",
                ),
            ),
            array(
                array(
                    "Sensors" => 2,
                    0 => array("id" => 0),
                    1 => array("id" => 2),
                ),
                array(
                    "DriverInfo" => array(
                        "PhysicalSensors" => 3,
                        "VirtualSensors" => 0,
                    ),
                    "ActiveSensors" => 3,
                    "params" => array(
                        "sensorType" => array("Test1Sensor", "a"),
                        "dType" => array("raw", "diff"),
                        "Loc" => array("Here", "There"),
                        "Extra" => array(array(1,2), array(3,4)),
                    )
                ),
                array(
                    "Sensors" => 3,
                    "ActiveSensors" => 3,
                    "PhysicalSensors" => 3,
                    array(
                        "id" => 0,
                        "type" => "Test1Sensor",
                        "location" => "Here",
                        "extra" => array(1,2),
                    ),
                    array(
                        "id" => 2,
                        "type" => "a",
                        "location" => "There",
                        "extra" => array(3,4),
                    ),
                    array(
                        "id" => 0,
                        "type" => "",
                    ),
                ),
                array(
                    "Test1Sensor",
                    "Test2Sensor",
                    "Test1Sensor",
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed $preload    The stuff to give to the constructor
    * @param mixed $devPreload The device preload
    * @param array $expect     The expected data
    * @param array $sensors    The expected sensor data
    *
    * @return null
    *
    * @dataProvider dataFromArray
    */
    public function testFromArray(
        $preload, $devPreload, $expect, $sensors
    ) {
        $this->d->clearData();
        $this->d->fromArray($devPreload);
        $this->o->clearData();
        $this->o->fromArray($preload);
        $s = $this->readAttribute($this->o, "sensor");
        foreach (array_keys((array)$s) as $k) {
            $this->assertSame(
                $sensors[$k], get_class($s[$k]), "Sensor $k wrong"
            );
        }
        $this->assertSame($expect, $this->o->toArray());
    }
    /**
    * data provider for testConstructor
    *
    * @return array
    */
    public static function dataToArray()
    {
        return array(
            array(
                array(
                    "RawCalibration" => "",
                    "Sensors" => 2,
                    0 => array(
                        "id" => 0,
                        "type" => "",
                        "location" => "",
                        "dataType" => "raw",
                        "extra" => array(),
                        "rawCalibration" => "",
                        "units" => "testUnit",
                    ),
                    1 => array(
                        "id" => 0,
                        "type" => "",
                        "location" => "",
                        "dataType" => "raw",
                        "extra" => array(),
                        "rawCalibration" => "",
                        "units" => "testUnit",
                    ),
                ),
                2,
                true,
                array(
                    "RawCalibration" => "",
                    "Sensors" => 2,
                    "ActiveSensors" => 0,
                    "PhysicalSensors" => 2,
                    "VirtualSensors" => 0,
                    "forceSensors" => false,
                    0 => array(
                        "id" => 0,
                        "type" => "",
                        "location" => "",
                        "dataType" => "raw",
                        "extra" => array(),
                        "rawCalibration" => "",
                        "units" => "testUnit",
                        "decimals" => 2,
                    ),
                    1 => array(
                        "id" => 0,
                        "type" => "",
                        "location" => "",
                        "dataType" => "raw",
                        "extra" => array(),
                        "rawCalibration" => "",
                        "units" => "testUnit",
                        "decimals" => 2,
                    ),
                ),

            ),
            array(
                array(
                    "RawCalibration" => "abcd",
                    "Sensors" => 2,
                    0 => array(
                        "id" => 0,
                        "type" => "resistive",
                        "location" => "Here and there",
                        "dataType" => "raw",
                        "extra" => array(),
                        "rawCalibration" => "",
                        "units" => "testUnit",
                    ),
                    1 => array(
                        "id" => 8,
                        "type" => "",
                        "location" => "",
                        "dataType" => "diff",
                        "extra" => array("Here"),
                        "rawCalibration" => "12345",
                        "units" => "testUnit",
                    ),
                ),
                2,
                true,
                array(
                    "RawCalibration" => "abcd",
                    "Sensors" => 2,
                    "ActiveSensors" => 0,
                    "PhysicalSensors" => 2,
                    "VirtualSensors" => 0,
                    "forceSensors" => false,
                    0 => array(
                        "id" => 0,
                        "type" => "resistive",
                        "location" => "Here and there",
                        "dataType" => "raw",
                        "extra" => array(),
                        "rawCalibration" => "",
                        "units" => "testUnit",
                        "decimals" => 2,
                    ),
                    1 => array(
                        "id" => 8,
                        "type" => "",
                        "location" => "",
                        "dataType" => "diff",
                        "extra" => array("Here"),
                        "rawCalibration" => "12345",
                        "units" => "testUnit",
                        "decimals" => 2,
                    ),
                ),
            ),
            array(
                array(
                    "RawCalibration" => "",
                    "Sensors" => 2,
                    0 => array(
                        "id" => 0,
                        "type" => "",
                        "location" => "",
                        "dataType" => "raw",
                        "extra" => array(),
                        "rawCalibration" => "",
                    ),
                    1 => array(
                        "id" => 0,
                        "type" => "",
                        "location" => "",
                        "dataType" => "raw",
                        "extra" => array(),
                        "rawCalibration" => "",
                    ),
                ),
                2,
                false,
                array(
                    "Sensors" => 2,
                    "PhysicalSensors" => 2,
                    array(
                        "id" => 0,
                        "type" => "",
                    ),
                    array(
                        "id" => 0,
                        "type" => "",
                    ),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed $preload      The stuff to give to the constructor
    * @param int   $TotalSensors The total number of sensors
    * @param bool  $default      Whether to give default stuff
    * @param array $expect       The expected data
    *
    * @return null
    *
    * @dataProvider dataToArray
    */
    public function testToArray($preload, $TotalSensors, $default, $expect)
    {
        $this->d->DriverInfo["PhysicalSensors"] = $TotalSensors;
        $this->d->DriverInfo["VirtualSensors"] = 0;
        $this->o->clearData();
        $this->o->fromArray($preload);
        $this->assertSame($expect, $this->o->toArray($default));
    }
    /**
    * data provider for testConstructor
    *
    * @return array
    */
    public static function dataFromTypeString()
    {
        return array(
            array(
                array(
                    0 => array("id" => 3, "type" => "Hello"),
                    1 => array("id" => 8),
                ),
                "00020102",
                2,
                array(
                    "Sensors" => 2,
                    "PhysicalSensors" => 2,
                    array(
                        "id" => 0,
                        "type" => "Hello",
                        "dataType" => UnitsBase::TYPE_DIFF,
                    ),
                    array(
                        "id" => 2,
                        "type" => "",
                        "dataType" => UnitsBase::TYPE_RAW,
                        "units" => "firstUnit",
                    ),
                ),
                array(
                    "Test1Sensor",
                    "Test2Sensor",
                ),
                array(0, 2),
            ),
            array(
                array(
                    0 => array("id" => 3, "type" => "Hello", "location" => "here"),
                    1 => array("id" => 8),
                    2 => array("id" => 4),
                    3 => array("id" => 5),
                ),
                "10020102",
                4,
                array(
                    "Sensors" => 4,
                    "PhysicalSensors" => 4,
                    array(
                        "id" => 0x10,
                        "type" => "multiInput",
                        "location" => "here",
                        "dataType" => UnitsBase::TYPE_DIFF,
                        "units" => "anotherUnit",
                    ),
                    array(
                        "id" => 0xFF,
                        "type" => "",
                        "location" => "here",
                    ),
                    array(
                        "id" => 0xFF,
                        "type" => "",
                        "location" => "here",
                    ),
                    array(
                        "id" => 2,
                        "type" => "",
                    ),
                ),
                array(
                    "Test3Sensor",
                    "Test1Sensor",
                    "Test1Sensor",
                    "Test2Sensor",
                ),
                array(0x10, 0xFF, 0xFF, 2),
            ),
            array(
                array(
                    0 => array("id" => 3, "type" => "Hello"),
                    1 => array("id" => 8),
                    2 => array("id" => 4),
                ),
                false,
                3,
                array(
                    "Sensors" => 3,
                    "PhysicalSensors" => 3,
                    array(
                        "id" => 3,
                        "type" => "Hello",
                    ),
                    array(
                        "id" => 8,
                        "type" => "",
                    ),
                    array(
                        "id" => 4,
                        "type" => "",
                    ),
                ),
                array(
                    "Test2Sensor",
                    "Test1Sensor",
                    "Test2Sensor",
                ),
                array(3, 8, 4),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload      The stuff to give to the constructor
    * @param string $string       The string to use for the input
    * @param int    $TotalSensors The total number of sensors
    * @param array  $expect       The expected data
    * @param array  $sensors      The expected sensor data
    * @param array  $types        The types to expect
    *
    * @return null
    *
    * @dataProvider dataFromTypeString
    */
    public function testFromTypeString(
        $preload, $string, $TotalSensors, $expect, $sensors, $types
    ) {
        $this->d->DriverInfo["PhysicalSensors"] = $TotalSensors;
        $this->d->DriverInfo["VirtualSensors"] = 0;
        $this->o->clearData();
        $this->o->fromArray($preload);
        $this->o->fromTypeString($string);
        $this->assertSame($expect, $this->o->toArray());
        $s = $this->readAttribute($this->o, "sensor");
        foreach (array_keys((array)$s) as $k) {
            $this->assertSame(
                $types[$k],
                $s[$k]->id,
                "Sensor $k id is wrong ".$types[$k]." != ".$s[$k]->id
            );
            $this->assertSame(
                $sensors[$k],
                get_class($s[$k]),
                "Sensor $k class is wrong ".$sensors[$k]." != ".get_class($s[$k])
            );
        }

    }

    /**
    * data provider for testConstructor
    *
    * @return array
    */
    public static function dataFromTypeArray()
    {
        return array(
            array(
                array(
                    0 => array("id" => 2, "type" => "Hello", ),
                    1 => array("id" => 2, "rawCalibration" => "abcd"),
                ),
                array(
                    0 => array("id" => 0),
                    1 => array("id" => 3, "type" => "Hello"),
                ),
                2,
                array(
                    "Sensors" => 2,
                    "PhysicalSensors" => 2,
                    array(
                        "id" => 0,
                        "type" => "Hello",
                        "dataType" => UnitsBase::TYPE_DIFF,
                    ),
                    array(
                        "id" => 3,
                        "type" => "Hello",
                        "rawCalibration" => "abcd"
                    ),
                ),
                array(
                    "Test1Sensor",
                    "Test2Sensor",
                ),
                array(0, 3),
            ),
            array(
                array(
                    0 => array("id" => 2, "type" => "Hello", ),
                    1 => array("id" => 2, "rawCalibration" => "abcd"),
                ),
                array(
                    0 => 0,
                    1 => "03",
                ),
                2,
                array(
                    "Sensors" => 2,
                    "PhysicalSensors" => 2,
                    array(
                        "id" => 0,
                        "type" => "Hello",
                        "dataType" => UnitsBase::TYPE_DIFF,
                    ),
                    array(
                        "id" => 3,
                        "type" => "",
                        "dataType" => UnitsBase::TYPE_DIFF,
                        "rawCalibration" => "abcd",
                    ),
                ),
                array(
                    "Test1Sensor",
                    "Test1Sensor",
                ),
                array(0, 3),
            ),
            array(
                array(
                    0 => array("id" => 2, "type" => "Hello", ),
                    1 => array("id" => 2, "rawCalibration" => "abcd"),
                ),
                "This is not an array",
                2,
                array(
                    "Sensors" => 2,
                    "PhysicalSensors" => 2,
                    0 => array("id" => 2, "type" => "Hello", ),
                    1 => array("id" => 2, "type" => "", "rawCalibration" => "abcd"),
                ),
                array(
                    "Test2Sensor",
                    "Test2Sensor",
                ),
                array(2, 2),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed $preload      The stuff to give to the constructor
    * @param array $array        The array to use for the input
    * @param int   $TotalSensors The total number of sensors
    * @param array $expect       The expected data
    * @param array $sensors      The expected sensor data
    * @param array $types        The types to expect
    *
    * @return null
    *
    * @dataProvider dataFromTypeArray
    */
    public function testFromTypeArray(
        $preload, $array, $TotalSensors, $expect, $sensors, $types
    ) {
        $this->d->DriverInfo["PhysicalSensors"] = $TotalSensors;
        $this->d->DriverInfo["VirtualSensors"] = 0;
        $this->o->clearData();
        $this->o->fromArray($preload);
        $this->o->fromTypeArray($array);
        $this->assertSame($expect, $this->o->toArray());
        $s = $this->readAttribute($this->o, "sensor");
        foreach (array_keys((array)$s) as $k) {
            $this->assertSame(
                $types[$k],
                $s[$k]->id,
                "Sensor $k id is wrong ".$types[$k]." != ".$s[$k]->id
            );
            $this->assertSame(
                $sensors[$k],
                get_class($s[$k]),
                "Sensor $k class is wrong ".$sensors[$k]." != ".get_class($s[$k])
            );
        }

    }

    /**
    * data provider for testSensor
    *
    * @return array
    */
    public static function dataSensor()
    {
        return array(
            array(
                array(
                    0 => array("id" => 3, "type" => "Hello"),
                    1 => array("id" => 2),
                    2 => array("id" => 8),
                ),
                0,
                3,
                "Test2Sensor",
            ),
            array(
                array(
                    0 => array("id" => 3),
                    1 => array("id" => 2),
                    2 => array("id" => 8),
                ),
                0,
                3,
                "Test1Sensor",
            ),
            array(
                array(
                    0 => array("id" => 3),
                    1 => array("id" => 2),
                    2 => array("id" => 8),
                ),
                8,
                3,
                "Test1Sensor",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to give to the constructor
    * @param string $num     The string to use for the input
    * @param int    $sensors The total number of sensors
    * @param array  $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataSensor
    */
    public function testSensor($preload, $num, $sensors, $expect)
    {
        $this->d->DriverInfo["PhysicalSensors"] = $sensors;
        $this->d->DriverInfo["VirtualSensors"] = 0;
        $this->o->clearData();
        $this->o->fromArray($preload);
        $this->assertSame($expect, get_class($this->o->sensor($num)));
    }


    /**
    * data provider for testFromCalString
    *
    * @return array
    */
    public static function dataFromCalString()
    {
        return array(
            array(
                array(
                    0 => array("id" => 3, "type" => "Hello"),
                    1 => array("id" => 2),
                    2 => array("id" => 8),
                ),
                "",
                3,
                null,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to give to the constructor
    * @param string $string  The string to use for the input
    * @param int    $sensors The total number of sensors
    * @param array  $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataFromCalString
    */
    public function testFromCalString($preload, $string, $sensors, $expect)
    {
        $this->d->DriverInfo["PhysicalSensors"] = $sensors;
        $this->d->DriverInfo["VirtualSensors"] = 0;
        $this->o->clearData();
        $this->o->fromArray($preload);
        $this->assertSame($expect, $this->o->fromCalString($string));
    }

    /**
    * data provider for testUpdateSensor
    *
    * @return array
    */
    public static function dataUpdateSensor()
    {
        return array(
            array(
                array(
                    "Sensors" => 3,
                    0 => array("id" => 3, "type" => "Hello"),
                    1 => array("id" => 2),
                    2 => array("id" => 8),
                ),
                array(
                    "id" => 2,
                ),
                array(
                    "PhysicalSensors" => 3,
                    "VirtualSensors" => 1,
                ),
                2,
                array(
                    0 => array("id" => 3, "type" => "Hello"),
                    1 => array("id" => 2, "type" => ""),
                    2 => array(
                        "id" => 2,
                        "type" => "",
                        "dataType" => UnitsBase::TYPE_RAW,
                        "units" => "firstUnit",
                    ),
                    3 => array("id" => 0xFE, "type" => "Virtual"),
                ),
                2,
                array(
                    "Test2Sensor",
                    "Test2Sensor",
                    "Test2Sensor",
                    "TestVirtualSensor",
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed $preload    The stuff to give to the constructor
    * @param array $data       The data to use for the sensor
    * @param array $driverInfo The driver info for the device
    * @param int   $key        The key to put the sensor in
    * @param array $expect     The expected data
    * @param int   $expectKey  The expected key returned
    * @param array $classes    The classes our sensors should be
    *
    * @return null
    *
    * @dataProvider dataUpdateSensor
    */
    public function testUpdateSensor(
        $preload, $data, $driverInfo, $key, $expect, $expectKey, $classes
    ) {
        $this->d->DriverInfo = $driverInfo;
        $this->o->clearData();
        $this->o->fromArray($preload);
        $this->o->updateSensor($data, $key);
        $sensors = $this->readAttribute($this->o, "sensor");
        $ret = array();
        foreach (array_keys($sensors) as $k) {
            $this->assertSame(
                $classes[$k],
                get_class($sensors[$k]),
                "Sensor $k has the wrong class"
            );
            $ret[$k] = $sensors[$k]->toArray(false);
        }
        $this->assertSame(
            $expect, $ret, "Sensors are wrong"
        );
        $this->assertSame($expectKey, $key, "The key returned is wrong");
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
                    "Sensors" => 3,
                    0 => array("id" => 3),
                    1 => array("id" => 2),
                    2 => array("id" => 8),
                ),
                array(
                    "PhysicalSensors" => 3,
                    "VirtualSensors" => 0,
                ),
                array(
                    "deltaT" => 1,
                    0 => 10,
                    1 => 20,
                    2 => 30,
                ),
                array(
                    null, 10, null, null
                ),
                array(
                    "deltaT" => 1,
                    0 => array(
                        "value" => 5,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => UnitsBase::TYPE_RAW,
                    ),
                    1 => array(
                        "value" => 20,
                        "raw" => 20,
                        "units" => "anotherUnit",
                        "unitType" => "secondUnit",
                        "dataType" => UnitsBase::TYPE_DIFF,
                    ),
                    2 => array(
                        "value" => 15,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => UnitsBase::TYPE_RAW,
                    ),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload    The stuff to give to the constructor
    * @param array  $driverInfo The driver info for the device
    * @param string $data       The data to use
    * @param array  $prev       The previous reading
    * @param array  $expect     The expected data
    *
    * @return null
    *
    * @dataProvider dataDecodeSensorData
    */
    public function testDecodeSensorData(
        $preload, $driverInfo, $data, $prev, $expect
    ) {
        $this->d->DriverInfo = $driverInfo;
        $this->o->clearData();
        $this->o->fromArray($preload);
        $data = $this->o->decodeSensorData($data, $prev);
        $this->assertSame($expect, $data);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataToOutput()
    {
        return array(
            array(
                array(),
                null,
                array(
                    "id" => null,
                    "type" => "",
                    "longName" => "Unknown Sensor",
                    "unitType" => "firstUnit",
                    "storageUnit" => "testUnit",
                    "storageType" => "raw",
                    "extraText" => array(),
                    "extraDefault" => array(),
                    "maxDecimals" => 2,
                    "location" => "",
                    "dataType" => "raw",
                    "extra" => array(),
                    "rawCalibration" => "",
                    "units" => "firstUnit",
                    "decimals" => 2,
                    "num" => 0,
                ),
            ),
            array(
                array(),
                array(),
                array(
                    "id" => null,
                    "type" => "",
                    "longName" => "Unknown Sensor",
                    "unitType" => "firstUnit",
                    "storageUnit" => "testUnit",
                    "storageType" => "raw",
                    "extraText" => array(),
                    "extraDefault" => array(),
                    "maxDecimals" => 2,
                    "location" => "",
                    "dataType" => "raw",
                    "extra" => array(),
                    "rawCalibration" => "",
                    "units" => "firstUnit",
                    "decimals" => 2,
                    "num" => 0,
                ),
            ),
            array(
                array(),
                array("dataType", "decimals", "type", "units"),
                array(
                    "id" => null,
                    "type" => "",
                    "longName" => "Unknown Sensor",
                    "unitType" => "firstUnit",
                    "storageUnit" => "testUnit",
                    "storageType" => "raw",
                    "extraText" => array(),
                    "extraDefault" => array(),
                    "maxDecimals" => 2,
                    "location" => "",
                    "dataType" => "raw",
                    "extra" => array(),
                    "rawCalibration" => "",
                    "units" => "firstUnit",
                    "decimals" => 2,
                    "num" => 0,
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $preload The data to preload into the class
    * @param array  $cols    The columns to use
    * @param int    $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataToOutput
    */
    public function testToOutput($preload, $cols, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $ret = $this->o->toOutput($cols);
        $this->assertSame(
            $expect,
            $ret
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataToOutputHeader()
    {
        return array(
            array(
                array(),
                null,
                array(
                    "num" => "#",
                    "location" => "Location",
                    "type" => "Type",
                    "dataType" => "Data Type",
                    "units" => "Units",
                    "decimals" => "Decimal Places",
                ),
            ),
            array(
                array(),
                array(),
                array(
                    "num" => "#",
                    "location" => "Location",
                    "type" => "Type",
                    "dataType" => "Data Type",
                    "units" => "Units",
                    "decimals" => "Decimal Places",
                ),
            ),
            array(
                array(),
                array("action", "dataType", "decimals", "type", "units"),
                array(
                    "action" => "action",
                    "dataType" => "Data Type",
                    "decimals" => "Decimal Places",
                    "type" => "Type",
                    "units" => "Units",
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $preload The data to preload into the class
    * @param array  $cols    The columns to use
    * @param int    $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataToOutputHeader
    */
    public function testToOutputHeader($preload, $cols, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $ret = $this->o->toOutputHeader($cols);
        $this->assertSame(
            $expect,
            $ret
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataOutputParams()
    {
        return array(
            array(
                array(),
                null,
                array(
                ),
                array(),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $preload The data to preload into the class
    * @param string $type    The output type
    * @param array  $cols    The columns to use
    * @param int    $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataOutputParams
    */
    public function testOutputParams($preload, $type, $cols, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $ret = $this->o->outputParams($type, $cols);
        $this->assertSame(
            $expect,
            $ret
        );
    }
    /**
    * data provider for testNextInto
    *
    * @return array
    */
    public static function dataNextInto()
    {
        return array(
            array(
                array(
                    "Sensors" => 2,
                ),
                2
            ),
            array(
                array(
                    "Sensors" => 20,
                ),
                20
            ),
            array(
                array(
                    "Sensors" => 0,
                ),
                1
            ),
            array(
                array(
                    "Sensors" => 1,
                ),
                1
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $preload The data to preload into the class
    * @param int    $expect  The number of runs to expect
    *
    * @return null
    *
    * @dataProvider dataNextInto
    */
    public function testNextInto($preload, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $count = 1; // There is always the first record to start with
        while ($this->o->nextInto()) {
            $count++;
            // Just in case we have an infinite loop
            if ($count > 100) {
                break;
            }
        }
        $this->assertSame(
            $expect,
            $count,
            "Count is wrong"
        );
        $this->assertAttributeSame(
            $expect,
            "_sensorIndex", $this->o,
            "sensorIndex is wrong"
        );
    }
}

?>
