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
require_once CODE_BASE.'sensors/DriverADuC.php';
/** This is a required class */
require_once CODE_BASE.'sensors/ADuCInputTable.php';
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
class DriverADuCTest extends drivers\DriverTestBase
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
        $sensor->resetMock(array());
        $this->o = &\HUGnet\sensors\drivers\DriverADuCTestClass::factory($sensor);
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
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGetExtra()
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
    * @dataProvider dataGetExtra
    */
    public function testGetExtra($mock, $index, $offset, $expect)
    {
        unset($this->o);
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mock);
        $this->o = &\HUGnet\sensors\drivers\DriverADuCTestClass::factory(
            $sensor, $offset
        );
        $this->assertSame($expect, $this->o->getExtra($index));
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
                0,
                null,
                0,
                "HUGnet\sensors\drivers\SDEFAULT",
            ),
            array(
                "SDEFAULT",
                0,
                null,
                0,
                "HUGnet\sensors\drivers\SDEFAULT",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name    The name of the variable to test.
    * @param int    $offset  The offset to use
    * @param object $entry   The table entry
    * @param int    $channel The channel in that entry
    * @param array  $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataFactory
    */
    public function testFactory($name, $offset, $entry, $channel, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock(array());
        $o = &DriverADuC::factory($name, $sensor, $offset, $entry, $channel);
        $this->assertSame($expect, get_class($o));
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
                -8388608,
            ),
            array(
                0xFFFFFF,
                24,
                -1,
            ),
            array(
                0xFFFFFFFFFFFF,
                24,
                -1,
            ),
            array(
                0,
                24,
                0,
            ),
            array(
                8388607,
                24,
                8388607,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param int $value  The integer to feed to the function
    * @param int $bits   The number of bits to use
    * @param int $expect The expected data
    *
    * @return null
    *
    * @dataProvider dataGetTwosCompliment
    */
    public function testGetTwosCompliment($value, $bits, $expect)
    {
        $val = $this->o->getTwosCompliment($value, $bits);
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
            array(
                array(
                    "Entry" => array(
                        "gain" => 5
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                0,
                0,
                5,
            ),
            array(
                array(
                    "Entry" => array(
                        "gain" => array(
                            "0" => 5,
                            "1" => 3,
                        ),
                    ),
                ),
                new \HUGnet\DummyBase("Entry"),
                1,
                1,
                null,
                3,
            ),
            array(
                array(
                    "Entry" => array(
                        "gain" => array(
                            "0" => 5,
                            "1" => 3,
                        ),
                    ),
                ),
                null,
                1,
                1,
                null,
                1,
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
}

/** This is the HUGnet namespace */
namespace HUGnet\sensors\drivers;
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
class DriverADuCTestClass extends \HUGnet\sensors\DriverADuC
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
    );
    /**
    * This function creates the system.
    *
    * @param object &$sensor The sensor object
    * @param int    $offset  The offset for getExtra
    *
    * @return null
    */
    public static function &factory(&$sensor, $offset = 0)
    {
        return parent::intFactory($sensor, $offset);
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
        return null;
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
}
?>
