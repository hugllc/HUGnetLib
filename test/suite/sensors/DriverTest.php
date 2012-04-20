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
        $this->o = &DriverTestClass::factory();
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
                "ThisIsABadName",
                null,
            ),
            array(
                "storageType",
                \HUGnet\units\Driver::TYPE_RAW,
            ),
            array(
                "testParam",
                "12345",
            ),
            array(
                "unitType",
                'asdf',
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
    * @dataProvider dataGet
    */
    public function testGet($name, $expect)
    {
        $this->assertSame($expect, $this->o->get($name, 1));
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGetParam($name, $expect)
    {
        $this->assertSame($expect, DriverTestClass::getParam($name, 1));
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
            'maxDecimals' => 2,
            'testParam' => '12345',
            "dataTypes" => array(
                \HUGnet\units\Driver::TYPE_RAW => \HUGnet\units\Driver::TYPE_RAW,
                \HUGnet\units\Driver::TYPE_DIFF => \HUGnet\units\Driver::TYPE_DIFF,
                \HUGnet\units\Driver::TYPE_IGNORE
                    => \HUGnet\units\Driver::TYPE_IGNORE,
            ),
            'defMin' => 0,
            'defMax' => 150,
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
        $o = &Driver::factory($name);
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
        $this->assertSame($expect, $this->o->getExtra($index, $sensor));
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
    protected static $params = array(
        "unitType" => "asdf", /* This is for test value only */
        "testParam" => "12345", /* This is for test value only */
        "extraDefault" => array(2,3,5,7,11),
        "extraText" => array("a","b","c","d","e"),
        "extraValues" => array(5, 5, 5, 5, 5),
    );
    /**
    * This function creates the system.
    *
    * @return null
    */
    public static function &factory()
    {
        return parent::intFactory();
    }
    /**
    * Gets the extra values
    *
    * @param int   $index The extra index to use
    * @param array $extra The extra array
    *
    * @return The extra value (or default if empty)
    */
    public function getExtra($index, $extra)
    {
        return parent::getExtra($index, $extra);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A       Output of the A to D converter
    * @param array &$sensor The sensor information
    * @param float $deltaT  The time delta in seconds between this record
    * @param array &$data   The data from the other sensors that were crunched
    * @param mixed $prev    The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getReading(
        $A, &$sensor, $deltaT = 0, &$data = array(), $prev = null
    ) {
        return null;
    }
}
?>
