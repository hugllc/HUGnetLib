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
namespace HUGnet\devices\inputTable;
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/DriverADuC.php';
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/tables/ADuCInputTable.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is our base class */
require_once dirname(__FILE__)."/drivers/DriverTestBase.php";
/** This is our interface */
require_once CODE_BASE."devices/inputTable/DriverInterface.php";

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
class DriverADuCTest extends drivers\DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "DriverADuCTestClass";
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
        $sensor->resetMock(array());
        $this->o = &\HUGnet\devices\inputTable\DriverADuC::factory(
            "DriverADuCTestClass", $sensor
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
                array(),
                null,
            ),
            array(
                "storageType",
                array(),
                \HUGnet\devices\datachan\Driver::TYPE_RAW,
            ),
            array(
                "testParam",
                array(),
                "12345",
            ),
            array(
                "unitType",
                array(),
                'asdf',
            ),
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGetExtra2()
    {
        return array(
            array(
                array(
                ),
                0,
                0,
                2
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(3,4,5,6),
                        ),
                    ),
                ),
                0,
                0,
                3
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(3,4,5,6),
                        ),
                    ),
                ),
                0,
                1,
                4
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "extra" => array(3,4,5,6),
                        ),
                    ),
                ),
                1,
                1,
                5
            ),
            array(
                array(
                ),
                1,
                1,
                3
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $mock   The mocks to use
    * @param int   $index  The index to get
    * @param int   $offset The offset to use
    * @param array $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGetExtra2
    */
    public function testGetExtra2($mock, $index, $offset, $expect)
    {
        unset($this->o);
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mock);
        $this->o = \HUGnet\devices\inputTable\DriverADuC::factory(
            "DriverADuCTestClass", $sensor, $offset
        );
        $this->assertSame($expect, $this->o->getExtra($index));
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
                256210,
            ),
        );
    }
    /**
    * data provider for testNumeric
    *
    * @return array
    */
    public static function dataGetTwosCompliment()
    {
        return array(
            array(
                8388608,
                24,
                array(),
                -8388608,
            ),
            array(
                0xFFFFFF,
                24,
                array(),
                -1,
            ),
            array(
                0xFFFFFFFFFFFF,
                24,
                array(),
                -1,
            ),
            array(
                0,
                24,
                array(),
                0,
            ),
            array(
                8388607,
                24,
                array(),
                8388607,
            ),
            array(
                0xFFFFFFFFFFFF,
                24,
                array(
                    "ADC0CODE" => 1,
                    "ADC1CODE" => 1,
                ),
                0xFFFFFFFFFFFF,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param int   $value  The integer to feed to the function
    * @param int   $bits   The number of bits to use
    * @param array $entry  The input table entry to use
    * @param int   $expect The expected data
    *
    * @return null
    *
    * @dataProvider dataGetTwosCompliment
    */
    public function testGetTwosCompliment($value, $bits, $entry, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock(array());
        $ent = \HUGnet\devices\inputTable\tables\ADuCInputTable::factory(
            $sensor, $entry
        );
        $obj = DriverADuC::factory(
            "DriverADuCTestClass", $sensor, 0, $ent
        );
        $val = $obj->getTwosCompliment($value, $bits);
        $this->assertSame($expect, $val);
    }

    /**
    * data provider for testNumeric
    *
    * @return array
    */
    public static function dataInputBiasCompensation()
    {
        return array(
            array(
                8388608,
                10,
                0,
                null,
            ),
            array(
                123456,
                0,
                244.252,
                123456.0,
            ),
            array(
                0,
                24,
                1,
                0.0,
            ),
            array(
                1000,
                1,
                100,
                1010.0,
            ),
            array(
                123456,
                "asdf",
                244.252,
                123456,
            ),
            array(
                123456,
                244.252,
                "asdf",
                123456,
            ),
            array(
                123456,
                "",
                244.252,
                123456,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param int   $value  The integer to feed to the function
    * @param float $Rin    The input resistance to use
    * @param float $Rbias  The bias resistance to use
    * @param int   $expect The expected data
    *
    * @return null
    *
    * @dataProvider dataInputBiasCompensation
    */
    public function testInputBiasComensation($value, $Rin, $Rbias, $expect)
    {
        bcscale(10);
        $val = $this->o->inputBiasCompensation($value, $Rin, $Rbias);
        $this->assertEquals($expect, $val, 0.0001);
    }
    /**
    * data provider for testNumeric
    *
    * @return array
    */
    public static function dataGain()
    {
        return array(
            array(  // #0 Normal
                array(
                    "Entry" => array(
                        "gain" => 5,
                        "register" => array(
                            "ADC0EN" => 1,
                            "ADC1EN" => 1,
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                0,
                0,
                5.0,
            ),
            array(  // #1 No channel given
                array(
                    "Entry" => array(
                        "gain" => array(
                            "0" => 5,
                            "1" => 3,
                        ),
                        "register" => array(
                            "ADC0EN" => 1,
                            "ADC1EN" => 1,
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                1,
                null,
                3.0,
            ),
            array( // #2 No valid entry
                array(
                    "Entry" => array(
                        "gain" => array(
                            "0" => 5,
                            "1" => 3,
                        ),
                        "register" => array(
                            "ADC0EN" => 1,
                            "ADC1EN" => 1,
                        ),
                    ),
                ),
                null,
                1,
                1,
                null,
                1.0,
            ),
            array(  // #3 Asking for channel 0 when ADC0 is not enabled
                array(
                    "Entry" => array(
                        "gain" => array(
                            "0" => 5,
                            "1" => 3,
                        ),
                        "register" => array(
                            "ADC0EN" => 0,
                            "ADC1EN" => 1,
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                1,
                0,
                3.0,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks    The mocks to feed this
    * @param object $entry    The entry to send
    * @param int    $offset   The integer to feed to the function
    * @param int    $initchan The channel to initialize the object to
    * @param int    $channel  The channel to set
    * @param int    $expect   The expected data
    *
    * @return null
    *
    * @dataProvider dataGain
    */
    public function testGain($mocks, $entry, $offset, $initchan, $channel, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mocks);
        $obj = &DriverADuC::factory(
            "DriverADuCTestClass", $sensor, $offset, $entry, $initchan
        );

        $val = $obj->gain($channel);
        $this->assertSame($expect, $val);
    }
    /**
    * data provider for testNumeric
    *
    * @return array
    */
    public static function dataIpRoutine()
    {
        return array(
            array(  // #0 Normal
                array(
                    "Entry" => array(
                        "immediateProcessRoutine" => array(
                            "0" => "05",
                            "1" => "03",
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                0,
                0,
                5,
            ),
            array(  // #1 No channel given
                array(
                    "Entry" => array(
                        "immediateProcessRoutine" => array(
                            "0" => "05",
                            "1" => "03",
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                1,
                null,
                3,
            ),
            array( // #2 No valid entry
                array(
                    "Entry" => array(
                        "immediateProcessRoutine" => array(
                            "0" => 5,
                            "1" => 3,
                        ),
                    ),
                ),
                null,
                1,
                1,
                null,
                0,
            ),
            array(  // #3 Asking for channel 0 when ADC0 is not enabled
                array(
                    "Entry" => array(
                        "immediateProcessRoutine" => array(
                            "0" => "05",
                            "1" => "13",
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                1,
                1,
                19,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks    The mocks to feed this
    * @param object $entry    The entry to send
    * @param int    $offset   The integer to feed to the function
    * @param int    $initchan The channel to initialize the object to
    * @param int    $channel  The channel to set
    * @param int    $expect   The expected data
    *
    * @return null
    *
    * @dataProvider dataIpRoutine
    */
    public function testIpRoutine(
        $mocks, $entry, $offset, $initchan, $channel, $expect
    ) {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mocks);
        $obj = &DriverADuC::factory(
            "DriverADuCTestClass", $sensor, $offset, $entry, $initchan
        );

        $val = $obj->ipRoutine($channel);
        $this->assertSame($expect, $val);
    }
    /**
    * data provider for testNumeric
    *
    * @return array
    */
    public static function dataAdcOn()
    {
        return array(
            array(  // #0 Normal ch0
                array(
                    "Entry" => array(
                        "gain" => 5,
                        "enabled" => array(
                            "0" => true,
                            "1" => false,
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                0,
                0,
                true,
            ),
            array(  // #1 Wrong channel given
                array(
                    "Entry" => array(
                        "gain" => array(
                            "0" => 5,
                            "1" => 3,
                        ),
                        "enabled" => array(
                            "0" => true,
                            "1" => true,
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                1,
                21,
                false,
            ),
            array(  // #2 Normal ch1
                array(
                    "Entry" => array(
                        "gain" => 5,
                        "enabled" => array(
                            "0" => false,
                            "1" => true,
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                0,
                1,
                true,
            ),
            array(  // #3 Normal ch0
                array(
                    "Entry" => array(
                        "gain" => 5,
                        "enabled" => array(
                            "0" => false,
                            "1" => true,
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                0,
                0,
                false,
            ),
            array(  // #4 Wrong channel given
                array(
                    "Entry" => array(
                        "gain" => array(
                            "0" => 5,
                            "1" => 3,
                        ),
                        "enabled" => array(
                            "0" => false,
                            "1" => false,
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                1,
                21,
                false,
            ),
            array(  // #5 Normal ch1
                array(
                    "Entry" => array(
                        "gain" => 5,
                        "enabled" => array(
                            "0" => true,
                            "1" => false,
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                0,
                1,
                false,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks    The mocks to feed this
    * @param object $entry    The entry to send
    * @param int    $offset   The integer to feed to the function
    * @param int    $initchan The channel to initialize the object to
    * @param int    $channel  The channel to set
    * @param int    $expect   The expected data
    *
    * @return null
    *
    * @dataProvider dataAdcOn
    */
    public function testAdcOn($mocks, $entry, $offset, $initchan, $channel, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mocks);
        $obj = &DriverADuC::factory(
            "DriverADuCTestClass", $sensor, $offset, $entry, $initchan
        );
        $val = $obj->adcOn($channel);
        $this->assertSame($expect, $val);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecodeData()
    {
        return array(
            array( // #0 everything enabled, raw
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Sensor"),
                    ),
                    "Entry" => array(
                        "gain" => array(
                            "0" => 2,
                        ),
                        "enabled" => array(
                            "0" => true,
                            "1" => true,
                        ),
                    ),
                    "Sensor" => array(
                        "get" => array(
                            "location" => "asdf",
                        ),
                    ),
                ),
                "DriverADuCTestClass",
                new \HUGnet\DummyBase("Entry"),
                1,
                0,
                300,
                array(
                ),
                array(
                ),
                "15000000",
                array(
                    array(
                        'decimals' => 2,
                        'units' => 'unknown',
                        'maxDecimals' => 2,
                        'storageUnit' => 'unknown',
                        'unitType' => 'asdf',
                        'dataType' => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        'value' => 10.5,
                        "label" => "asdf",
                        "index" => 0,
                        "epChannel" => true,
                    ),
                ),
            ),
            array( // #0 everything enabled, differential
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Sensor"),
                    ),
                    "Entry" => array(
                        "gain" => array(
                            "0" => 2,
                        ),
                        "enabled" => array(
                            "0" => true,
                            "1" => true,
                        ),
                    ),
                    "Sensor" => array(
                        "get" => array(
                            "location" => "asdf",
                        ),
                    ),
                ),
                "DriverADuCTestClassDiff",
                new \HUGnet\DummyBase("Entry"),
                1,
                0,
                300,
                array(
                    "raw" => 5.25,
                ),
                array(
                ),
                "15000000",
                array(
                    array(
                        'decimals' => 2,
                        'units' => 'unknown',
                        'maxDecimals' => 2,
                        'storageUnit' => 'unknown',
                        'unitType' => 'unknown',
                        'dataType' => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                        'value' => 5.25,
                        'raw' => 10.5,
                        "label" => "asdf",
                        "index" => 0,
                        "epChannel" => true,
                    ),
                ),
            ),
            array( // #0 everything enabled, raw
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Sensor"),
                    ),
                    "Entry" => array(
                        "gain" => array(
                            "0" => 2,
                        ),
                        "enabled" => array(
                            "0" => false,
                            "1" => true,
                        ),
                    ),
                    "Sensor" => array(
                        "get" => array(
                            "location" => "asdf",
                        ),
                    ),
                ),
                "DriverADuCTestClass",
                new \HUGnet\DummyBase("Entry"),
                1,
                0,
                300,
                array(
                ),
                array(
                ),
                "15000000",
                array(
                    array(
                        'decimals' => 2,
                        'units' => 'unknown',
                        'maxDecimals' => 2,
                        'storageUnit' => 'unknown',
                        'unitType' => 'asdf',
                        'dataType' => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                        'value' => 21.0,
                        "label" => "asdf",
                        "index" => 0,
                        "epChannel" => true,
                    ),
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks    The value to preload into the mocks
    * @param string $class    The class to use
    * @param object $entry    The entry to send
    * @param int    $offset   The integer to feed to the function
    * @param int    $initchan The channel to initialize the object to
    * @param float  $deltaT   The time delta in seconds between this record
    * @param array  $prev     The previous reading
    * @param array  $data     The data from the other sensors that were crunched
    * @param string $string   The setup string to test
    * @param array  $expect   The expected return
    *
    * @return null
    *
    * @dataProvider dataDecodeData
    */
    public function testDecodeData(
        $mocks, $class, $entry, $offset, $initchan, $deltaT, $prev, $data,
        $string, $expect
    ) {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mocks);
        $obj = &DriverADuC::factory(
            $class, $sensor, $offset, $entry, $initchan
        );
        $ret = $obj->decodeData($string, $deltaT, $prev, $data);
        $this->assertEquals($expect, $ret);
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DriverADuCTestClass extends \HUGnet\devices\inputTable\DriverADuC
    implements \HUGnet\devices\inputTable\DriverInterface
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
        "extraDefault" => array(2,3,5,7,11),
        "extraText" => array("a","b","c","d","e"),
        "extraDesc" => array("A","B","C","D","E"),
        "extraValues" => array(5, 5, 5, 5, 5),
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
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        return $A;
    }
    /**
    * Changes an n-bit twos compliment number into a signed number PHP can use
    *
    * @param int   $value The incoming number
    * @param float $bits  The number of bits the incoming number is
    *
    * @return int A signed integer for PHP to use
    */
    public function getTwosCompliment($value, $bits = 24)
    {
        return parent::getTwosCompliment($value, $bits);
    }
    /**
    * Compensates for an input and bias resistance.
    *
    * The bias and input resistance values can be in Ohms, kOhms or even MOhms.  It
    * doesn't matter as long as they are both the same units.
    *
    * @param float $value The incoming number
    * @param float $Rin   The input resistor.
    * @param float $Rbias The bias resistor.
    *
    * @return float The compensated value
    */
    public function inputBiasCompensation($value, $Rin, $Rbias)
    {
        return parent::inputBiasCompensation($value, $Rin, $Rbias);
    }
    /**
    * Gets the total gain.
    *
    * @param int $channel The channel to get the gain for
    *
    * @return null
    */
    public function gain($channel = null)
    {
        return parent::gain($channel);
    }
    /**
    * Gets the immediate processing routine.
    *
    * @param int $channel The channel to get the gain for
    *
    * @return null
    */
    public function ipRoutine($channel = null)
    {
        return parent::ipRoutine($channel);
    }
    /**
    * Gets the total gain.
    *
    * @param int $channel The channel to get the gain for
    *
    * @return null
    */
    public function adcOn($channel)
    {
        return parent::adcOn($channel);
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DriverADuCTestClassDiff extends \HUGnet\devices\inputTable\DriverADuC
    implements \HUGnet\devices\inputTable\DriverInterface
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
