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


require_once dirname(__FILE__).'/../../base/DeviceSensorBase.php';
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
class DeviceSensorBaseTest extends PHPUnit_Framework_TestCase
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
                "dir" => realpath(
                    dirname(__FILE__)."/../files/plugins/"
                ),
            ),
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->d = new DummyDeviceContainer();
        $this->o = new TestDeviceSensor($data, $this->d);
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
                "TestDeviceSensor",
                array(),
                array(
                    "location" => "f",
                    "id" => 0,
                    "type" => "sensor",
                    "units" => "testUnit",
                    "dataType" => "raw",
                    "extra" => array(),
                    "rawCalibration" => "cali",
                ),
            ),
            array(
                "TestDeviceSensor2",
                array(),
                array(
                    "units" => "testUnit",
                ),
            ),
            array(
                "TestDeviceSensor3",
                array(),
                array(
                    "units" => "moreUnit",
                ),
            ),
            array(
                "TestDeviceSensor",
                array(
                    "location" => "f",
                    "id" => 0,
                    "type" => "sensor",
                    "units" => "badUnit",
                    "dataType" => "raw",
                    "extra" => array(),
                    "rawCalibration" => "cali",
                    "decimals" => 10,
                ),
                array(
                    "location" => "f",
                    "id" => 0,
                    "type" => "sensor",
                    "units" => "testUnit",
                    "dataType" => "raw",
                    "extra" => array(),
                    "rawCalibration" => "cali",
                    "decimals" => 2,
                ),
            ),
            array(
                "TestDeviceSensor",
                array(
                    "location" => "f",
                    "id" => 0,
                    "type" => "sensor",
                    "units" => "badUnit",
                    "dataType" => "raw",
                    "extra" => array(),
                    "rawCalibration" => "cali",
                ),
                array(
                    "location" => "f",
                    "id" => 0,
                    "type" => "sensor",
                    "units" => "testUnit",
                    "dataType" => "raw",
                    "extra" => array(),
                    "rawCalibration" => "cali",
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $class   The class to use
    * @param mixed  $preload The stuff to give to the constructor
    * @param string $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($class, $preload, $expect)
    {
        $o = new $class($preload, $this->d);
        foreach ($expect as $key => $value) {
            $this->assertSame($value, $o->$key, "Bad Value in key $key");
        }
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
                3,
                3
            ),
            array(
                array(
                    "extra" => array(6,5,4),
                ),
                100,
                null
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to give to the constructor
    * @param int    $index   The index to use for the extra array
    * @param string $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataGetExtra
    */
    public function testGetExtra($preload, $index, $expect)
    {
        $this->o->fromAny($preload);
        $this->assertSame($expect, $this->o->getExtra($index));
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
                array(
                    "extra" => array(6,5,4),
                ),
                false,
                false,
                array(
                    "id" => 0,
                    "type" => "sensor",
                    "extra" => array(6,5,4),
                ),
            ),
            array(
                array(
                    "extra" => array(6,5,4),
                ),
                false,
                true,
                array(
                    "id" => 0,
                    "type" => "sensor",
                    "longName" => "Unknown Sensor",
                    "unitType" => "firstUnit",
                    "storageUnit" => "firstUnit",
                    "storageType" => "raw",
                    "extraText" => array(),
                    "extraDefault" => array(0,1,2,3,4,5,6,7),
                    "maxDecimals" => 2,
                    "extra" => array(6,5,4),
                ),
            ),
            array(
                array(
                    "extra" => array(6,5,4),
                ),
                true,
                true,
                array(
                    "id" => 0,
                    "type" => "sensor",
                    "longName" => "Unknown Sensor",
                    "unitType" => "firstUnit",
                    "storageUnit" => "firstUnit",
                    "storageType" => "raw",
                    "extraText" => array(),
                    "extraDefault" => array(0,1,2,3,4,5,6,7),
                    "maxDecimals" => 2,
                    "location" => "f",
                    "units" => "testUnit",
                    "dataType" => "raw",
                    "extra" => array(6,5,4),
                    "rawCalibration" => "cali",
                    "decimals" => 2,
                ),
            ),
            array(
                array(
                    "extra" => array(6,5,4),
                ),
                true,
                false,
                array(
                    "id" => 0,
                    "type" => "sensor",
                    "location" => "f",
                    "units" => "testUnit",
                    "dataType" => "raw",
                    "extra" => array(6,5,4),
                    "rawCalibration" => "cali",
                    "decimals" => 2,
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to give to the constructor
    * @param bool   $default Whether to include default values or not
    * @param bool   $fixed   Return items in the fixed array?
    * @param string $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataToArray
    */
    public function testToArray($preload, $default, $fixed, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $this->assertSame($expect, $this->o->toArray($default, $fixed));
    }
    /**
    * data provider for testGetExtra
    *
    * @return array
    */
    public static function dataGetUnits()
    {
        return array(
            array(
                "TestDeviceSensor",
                array(
                ),
                1,
                "d",
                "c",
                array(
                ),
                array(
                    "value" => 1,
                    "units" => "firstUnit",
                    "unitType" => "firstUnit",
                    "dataType" => UnitsBase::TYPE_RAW,
                ),
            ),
            array(
                "TestDeviceSensor2",
                array(
                    "dataType" => UnitsBase::TYPE_RAW,
                ),
                1,
                2,
                5,
                array(
                ),
                array(
                    "value" => -4,
                    "raw" => 1,
                    "units" => "firstUnit",
                    "unitType" => "firstUnit",
                    "dataType" => UnitsBase::TYPE_DIFF,
                ),
            ),
            array(
                "TestDeviceSensor3",
                array(
                ),
                1,
                2,
                5,
                array(
                    0 => array("value" => 4),
                ),
                array(
                    "value" => 10.0,
                    "units" => "moreUnit",
                    "unitType" => "moreUnit",
                    "dataType" => UnitsBase::TYPE_RAW,
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $class   The class to use
    * @param mixed  $preload The stuff to give to the constructor
    * @param mixed  $A       The value to send
    * @param int    $deltaT  The delta Time
    * @param mixed  $prev    The previous reading
    * @param array  $data    The previous data
    * @param string $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataGetUnits
    */
    public function testGetUnits(
        $class, $preload, $A, $deltaT, $prev, $data, $expect
    ) {
        $o = new $class($preload, $this->d);
        $this->assertSame($expect, $o->getUnits($A, $deltaT, $prev, $data));
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
                    "units" => "firstUnit",
                ),
                12,
                12.0,
                false,
                "firstUnit",
            ),
            array(
                array(
                    "units" => "badUnit",
                ),
                12,
                12.0,
                false,
                "firstUnit",
            ),
            array(
                array(
                ),
                12,
                24.0,
                true,
                "testUnit",
            ),
            array(
                array(
                ),
                null,
                null,
                true,
                "testUnit",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to give to the constructor
    * @param mixed  $data    The value to send
    * @param string $expect  The expected data
    * @param bool   $ret     The return value expected
    * @param string $units   The expected units
    *
    * @return null
    *
    * @dataProvider dataConvertUnits
    */
    public function testConvertUnits($preload, $data, $expect, $ret, $units)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $this->assertSame(
            $ret,
            $this->o->convertUnits($data),
            "The return value is wrong"
        );
        $this->assertSame($expect, $data, "Data is wrong");
        $this->assertSame($units, $this->o->units, "Units are wrong");
    }

    /**
    * data provider for testConvertUnits
    *
    * @return array
    */
    public static function dataGetAllUnits()
    {
        return array(
            array(
                array(
                    "units" => "firstUnit",
                ),
                "TestDeviceSensor",
                array(
                    "firstUnit" => "firstUnit",
                    "testUnit" => "testUnit",
                ),
            ),
            array(
                array(
                    "units" => "firstUnit",
                ),
                "TestDeviceSensor2",
                array(
                    "firstUnit" => "firstUnit",
                    "testUnit" => "testUnit",
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to give to the constructor
    * @param mixed  $class   The class to use for this test
    * @param string $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataGetAllUnits
    */
    public function testGetAllUnits($preload, $class, $expect)
    {
        $o = new $class($preload, $this->d);
        $this->assertSame(
            $expect,
            $o->getAllUnits()
        );
    }

    /**
    * data provider for testGetAllDataTypes
    *
    * @return array
    */
    public static function dataGetAllDataTypes()
    {
        return array(
            array(
                array(
                    "units" => "firstUnit",
                ),
                "TestDeviceSensor",
                array(
                    DeviceSensorBase::TYPE_RAW => DeviceSensorBase::TYPE_RAW,
                    DeviceSensorBase::TYPE_DIFF => DeviceSensorBase::TYPE_DIFF,
                    DeviceSensorBase::TYPE_IGNORE => DeviceSensorBase::TYPE_IGNORE,
                ),
            ),
            array(
                array(
                    "units" => "firstUnit",
                ),
                "TestDeviceSensor2",
                array(
                    DeviceSensorBase::TYPE_RAW => DeviceSensorBase::TYPE_RAW,
                    DeviceSensorBase::TYPE_DIFF => DeviceSensorBase::TYPE_DIFF,
                    DeviceSensorBase::TYPE_IGNORE => DeviceSensorBase::TYPE_IGNORE,
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to give to the constructor
    * @param mixed  $class   The class to use for this test
    * @param string $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataGetAllDataTypes
    */
    public function testGetAllDataTypes($preload, $class, $expect)
    {
        $o = new $class($preload, $this->d);
        $this->assertSame(
            $expect,
            $o->getAllDataTypes()
        );
    }

    /**
    * data provider for testGetAllTypes
    *
    * @return array
    */
    public static function dataGetAllTypes()
    {
        return array(
            array(
                array(
                ),
                "TestDeviceSensor2",
                array(
                    "DEFAULT" => "Test1Sensor",
                    "Hello" => "Test2Sensor",
                ),
            ),
            array(
                array(
                ),
                "TestDeviceSensor3",
                array(
                    "" => "Test2Sensor",
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to give to the constructor
    * @param mixed  $class   The class to use for this test
    * @param string $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataGetAllTypes
    */
    public function testGetAllTypes($preload, $class, $expect)
    {
        $o = new $class($preload, $this->d);
        $this->assertSame(
            $expect,
            $o->getAllTypes()
        );
    }

    /**
    * data provider for testTotal
    *
    * @return array
    */
    public static function dataTotal()
    {
        return array(
            array(
                array(
                ),
                "TestDeviceSensor",
                false,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to give to the constructor
    * @param mixed  $class   The class to use for this test
    * @param string $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataTotal
    */
    public function testTotal($preload, $class, $expect)
    {
        $o = new $class($preload, $this->d);
        $this->assertSame(
            $expect,
            $o->total()
        );
    }

    /**
    * data provider for testNumeric
    *
    * @return array
    */
    public static function dataNumeric()
    {
        return array(
            array(
                array(
                ),
                "TestDeviceSensor",
                "asdf",
                false,
            ),
            array(
                array(
                ),
                "TestDeviceSensor",
                null,
                true,
            ),
            array(
                array(
                ),
                "TestDeviceSensor",
                "firstUnit",
                true,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to give to the constructor
    * @param mixed  $class   The class to use for this test
    * @param string $units   The units to use
    * @param string $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataNumeric
    */
    public function testNumeric($preload, $class, $units, $expect)
    {
        $o = new $class($preload, $this->d);
        $this->assertSame(
            $expect,
            $o->numeric($units)
        );
    }

    /**
    * data provider for testSet
    *
    * @return array
    */
    public static function dataSet()
    {
        return array(
            array("dataType", "raw", "raw"),
            array("dataType", "Ignore", "ignore"),
            array("dataType", "diff", "diff"),
            array("dataType", "SomethingElse", "raw"),
            array("type", "SomethingElse", "sensor"),
            array("type", "j", "sensor"),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $var    The variable to set
    * @param mixed  $value  The value to set
    * @param mixed  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataSet
    */
    public function testSet($var, $value, $expect)
    {
        $this->o->$var = $value;
        $this->assertSame($expect, $this->o->$var);
    }


}
/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Endpoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2011 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class TestDeviceSensor extends DeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "testSensor",
        "Type" => "sensor",
        "Class" => "TestSensor",
        "Sensors" => array(),
    );
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "location" => "f",                // The location of the sensors
        "id" => 0,                      // The id of the sensor.  This is the value
                                         // Stored in the device  It will be an int
        "type" => "sensor",                    // The type of the sensors
        "units" => "testUnit",             // The units the values are stored in
        "dataType" => "raw",             // The datatype of each sensor
        "extra" => array(),              // Extra input for crunching numbers
        "rawCalibration" => "cali",          // The raw calibration string
    );
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Unknown Sensor",
        "unitType" => "firstUnit",
        "storageUnit" => 'firstUnit',
        "storageType" => UnitsBase::TYPE_RAW,
        "extraText" => array(),
        "extraDefault" => array(0,1,2,3,4,5,6,7),
        "maxDecimals" => 2,
    );
    /** @var object This is where we store our configuration */
    protected $unitTypeValues = array("b");
    /** @var object This is where we store our configuration */
    protected $typeValues = array("j");
    /**
    * Gets the extra values
    *
    * @param array $index The extra index to use
    *
    * @return mixed the extra value
    */
    public function getExtra($index)
    {
        return parent::getExtra($index);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    */
    public function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        return $A;
    }
}
/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Endpoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2011 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class TestDeviceSensor2 extends DeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "testSensor",
        "Type" => "sensor",
        "Class" => "TestSensor",
        "Sensors" => array(),
    );
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Unknown Sensor",
        "unitType" => "firstUnit",
        "storageUnit" => 'firstUnit',
        "storageType" => UnitsBase::TYPE_DIFF,
        "extraText" => array(),
        "extraDefault" => array(0,1,2,3,4,5,6,7),
        "maxDecimals" => 2,
    );
    /** @var object This is where we store our configuration */
    protected $unitTypeValues = array("b");
    /** @var object This is where we store our configuration */
    protected $typeValues = array("j");
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 3;
        parent::__construct($data, $device);
    }
    /**
    * Gets the extra values
    *
    * @param array $index The extra index to use
    *
    * @return mixed the extra value
    */
    public function getExtra($index)
    {
        return parent::getExtra($index);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    */
    public function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        return $A;
    }
}
/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Endpoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2011 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class TestDeviceSensor3 extends DeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "testSensor",
        "Type" => "sensor",
        "Class" => "TestSensor",
        "Sensors" => array(),
    );
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Unknown Sensor",
        "unitType" => "moreUnit",
        "storageUnit" => 'moreUnit',
        "storageType" => UnitsBase::TYPE_RAW,
        "extraText" => array(),
        "extraDefault" => array(0,1,2,3,4,5,6,7),
        "maxDecimals" => 2,
    );
    /** @var object This is where we store our configuration */
    protected $unitTypeValues = array("b");
    /** @var object This is where we store our configuration */
    protected $typeValues = array("j");
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 5;
        parent::__construct($data, $device);
    }
    /**
    * Gets the extra values
    *
    * @param array $index The extra index to use
    *
    * @return mixed the extra value
    */
    public function getExtra($index)
    {
        return parent::getExtra($index);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    */
    public function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        return (float)($A + $prev + $data[0]["value"]);
    }
}

?>
