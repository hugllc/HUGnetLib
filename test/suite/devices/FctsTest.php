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
namespace HUGnet\devices;
/** This is a required class */
require_once CODE_BASE.'devices/Fcts.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once CODE_BASE.'devices/datachan/Driver.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class FctsTest extends \PHPUnit_Framework_TestCase
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
    * This tests the exception when a system object is not passed
    *
    * @return null
    */
    public function testCreateThrowException()
    {
        $this->setExpectedException("InvalidArgumentException");
        // This throws an exception because $test is not a object
        Fcts::factory($test, $test2, $channels);
    }
    /**
    * This tests the exception when a system object is not passed
    *
    * @return null
    */
    public function testCreateThrowException2()
    {
        $test = new \HUGnet\DummySystem();
        // This just resets the mock
        $test->resetMock();
        $this->setExpectedException("RuntimeException");
        // This throws an exception because $test2 is not a object
        Fcts::factory($test, $test2, $channels);
    }
    /**
    * This tests the exception when a system object is not passed
    *
    * @return null
    */
    public function testSystem()
    {
        $dev = new \HUGnet\DummyBase("Device");
        $sys = new \HUGnet\DummyBase("System");
        $obj = Fcts::factory($sys, $dev, $channels);
        $this->assertSame($sys, $obj->system());
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
                new \HUGnet\DummySystem("System"),
                new \HUGnet\DummyTable("Device"),
                array(
                    "System" => array(
                        "fatalError" => array(
                            array(
                                "HUGnet\devices\Fcts needs to be passed"
                                    ." a device object",
                                false,
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config      The configuration to use
    * @param mixed $gateway     The gateway to set
    * @param array $expectTable The table to expect
    *
    * @return null
    *
    * @dataProvider dataCreate
    */
    public function testCreate($config, $gateway, $expectTable)
    {
        $table = new \HUGnet\DummyTable();
        // This just resets the mock
        $table->resetMock();
        $obj = Fcts::factory($config, $gateway, $channels);
        // Make sure we have the right object
        $this->assertInstanceOf(
            "HUGnet\devices\Fcts", $obj, "Class wrong"
        );
        if (is_object($table)) {
            $this->assertEquals($expectTable, $table->retrieve(), "Data Wrong");
        }
    }
    /**
    * Data provider for testGetField
    *
    * @return array
    */
    public static function dataCount()
    {
        return array(
            array(
                array(
                    "Device" => array(
                        "get" => array(
                            "OutputTables" => 3,
                            "controlChannels" => array(
                                array(
                                    "units" => "bar",
                                    "decimals" => 4,
                                ),
                                array(
                                    "units" => "%",
                                    "decimals" => 4,
                                ),
                                array(
                                    "units" => "%",
                                    "decimals" => 4,
                                ),
                            ),
                        ),
                        "output" => array(
                            "0" => new \HUGnet\DummyTable("Output0"),
                            "1" => new \HUGnet\DummyTable("Output1"),
                            "2" => new \HUGnet\DummyTable("Output2"),
                        ),
                    ),
                    "Output0" => array(
                        "channels" => array(
                            array(
                                "unitType" => "Pressure",
                                "storageUnit" => "psi",
                                "storageType" =>
                                    \HUGnet\devices\datachan\Driver::TYPE_RAW,
                            ),
                        ),
                    ),
                    "Output1" => array(
                        "channels" => array(
                            array(
                                "unitType" => "Percent",
                                "storageUnit" => "decimal",
                                "storageType" =>
                                    \HUGnet\devices\datachan\Driver::TYPE_RAW,
                            ),
                        ),
                    ),
                    "Output2" => array(
                        "channels" => array(
                            array(
                                "unitType" => "Percent",
                                "storageUnit" => "decimal",
                                "storageType" =>
                                    \HUGnet\devices\datachan\Driver::TYPE_RAW,
                            ),
                        ),
                    ),
                ),
                array(
                    array(
                        "units" => "bar",
                        "decimals" => 4,
                    ),
                    array(
                        "units" => "%",
                        "decimals" => 4,
                    ),
                    array(
                        "units" => "%",
                        "decimals" => 4,
                    ),
                ),
                3
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config   The configuration to use
    * @param array $channels The channels to use
    * @param mixed $expect   The value we expect back
    *
    * @return null
    *
    * @dataProvider dataCount
    */
    public function testCount(
        $config, $channels, $expect
    ) {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyTable("Device");
        $sys->resetMock($config);
        $obj = Fcts::factory($sys, $dev, $channels);
        $this->assertEquals($expect, $obj->count());
        unset($obj);
    }
    /**
    * Data provider for testGetField
    *
    * @return array
    */
    public static function data2Array()
    {
        return array(
            array(   // #0
                array(
                ),
                array(
                ),
                false,
                array(
                ),
            ),
            array(  // #1
                array(
                ),
                array(
                    array(
                    ),
                    array(
                    ),
                    array(
                    ),
                    array(
                    ),
                    array(
                    ),
                ),
                false,
                array(
                    array(
                        "driver"     => "InputFunction",
                        "id"         => 0,
                    ),
                    array(
                        "driver"     => "InputFunction",
                        "id"         => 1,
                    ),
                    array(
                        "driver"     => "InputFunction",
                        "id"         => 2,
                    ),
                    array(
                        "driver"     => "InputFunction",
                        "id"         => 3,
                    ),
                    array(
                        "driver"     => "InputFunction",
                        "id"         => 4,
                    ),
                ),
            ),
            array(  // #2
                array(
                ),
                array(
                    array(
                        "index" => 2,
                        "output" => 4,
                        "min" => 8,
                        "max" => 8150,
                        "label" => "PWM",
                    ),
                    array(
                        "label" => "PWM",
                    ),
                    array(
                    ),
                    array(
                        "label" => "PWM",
                    ),
                    array(
                        "label" => "PWM",
                    ),
                ),
                true,
                array(
                    array(
                        'longName' => 'Input Function',
                        'shortName' => 'Input',
                        'type' => 'Simple',
                        'id' => 0,
                        'driver' => 'InputFunction',
                        'data' => array(),
                        'name' => 'New Function',
                    ),
                    array(
                        'longName' => 'Input Function',
                        'shortName' => 'Input',
                        'type' => 'Simple',
                        'id' => 1,
                        'driver' => 'InputFunction',
                        'data' => array(),
                        'name' => 'New Function',
                    ),
                    array(
                        'longName' => 'Input Function',
                        'shortName' => 'Input',
                        'type' => 'Simple',
                        'id' => 2,
                        'driver' => 'InputFunction',
                        'data' => array(),
                        'name' => 'New Function',
                    ),
                    array(
                        'longName' => 'Input Function',
                        'shortName' => 'Input',
                        'type' => 'Simple',
                        'id' => 3,
                        'driver' => 'InputFunction',
                        'data' => array(),
                        'name' => 'New Function',
                    ),
                    array(
                        'longName' => 'Input Function',
                        'shortName' => 'Input',
                        'type' => 'Simple',
                        'id' => 4,
                        'driver' => 'InputFunction',
                        'data' => array(),
                        'name' => 'New Function',
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config   The configuration to use
    * @param array $channels The channels to give it
    * @param mixed $default  Whether or not to show the default stuff
    * @param mixed $expect   The value we expect back
    *
    * @return null
    *
    * @dataProvider data2Array
    */
    public function test2Array(
        $config, $channels, $default, $expect
    ) {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyTable("Device");
        $sys->resetMock($config);
        $obj = Fcts::factory($sys, $dev, $channels);
        $ret = $obj->toArray($default);
        $this->assertEquals($expect, $ret);
        unset($obj);
    }
    /**
    * Data provider for testGetField
    *
    * @return array
    */
    public static function dataStore()
    {
        return array(
            array(   // #0
                array(
                ),
                array(
                ),
                array(
                    "fcts" => array(
                        array(array(), true),
                    ),
                ),
            ),
            array(  // #1
                array(
                ),
                array(
                    array(
                    ),
                    array(
                    ),
                    array(
                    ),
                    array(
                    ),
                    array(
                    ),
                ),
                array(
                    "fcts" => array(
                        array(
                            array(
                                array(
                                    "id"         => 0,
                                    "driver"     => "InputFunction",
                                ),
                                array(
                                    "id"         => 1,
                                    "driver"     => "InputFunction",
                                ),
                                array(
                                    "id"         => 2,
                                    "driver"     => "InputFunction",
                                ),
                                array(
                                    "id"         => 3,
                                    "driver"     => "InputFunction",
                                ),
                                array(
                                    "id"         => 4,
                                    "driver"     => "InputFunction",
                                ),
                            ),
                            true
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config   The configuration to use
    * @param array $channels The channels to give it
    * @param mixed $expect   The value we expect back
    *
    * @return null
    *
    * @dataProvider dataStore
    */
    public function testStore(
        $config, $channels, $expect
    ) {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyTable("Device");
        $sys->resetMock($config);
        $obj = Fcts::factory($sys, $dev, $channels);
        $obj->store();
        $ret = $sys->retrieve("Device");
        $this->assertEquals($expect, $ret);
        unset($obj);
    }
    /**
    * Data provider for testApply
    *
    * @return array
    */
    public static function dataApply()
    {
        return array(
            array(   // #0
                array(
                    "Device" => array(
                        "fcts" => array(
                            "asdf" => "HelloThere",
                        ),
                    ),
                ),
                array(
                    array("id" => 0),
                ),
                array(
                    "fcts" => array(
                        array(
                            array(
                                array(
                                    "id" => 0, 
                                    'driver' => 'InputFunction',
                                ),
                            ), 
                            true
                        ),
                    ),
                    "setParam" => array(
                        array(
                            "fctsApplied",
                            array(
                                array(
                                    "id" => 0, 
                                    'driver' => 'InputFunction',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(  // #1
                array(
                    "Device" => array(
                        "getParam" => array(
                            "fcts" => "Hello",
                        ),
                    ),
                ),
                array(
                    array(
                    ),
                    array(
                    ),
                    array(
                    ),
                    array(
                    ),
                    array(
                    ),
                ),
                array(
                    "fcts" => array(
                        array(
                            array(
                                array(
                                    "id" => 0, 
                                    'driver' => 'InputFunction',
                                ),
                                array(
                                    "id" => 1, 
                                    'driver' => 'InputFunction',
                                ),
                                array(
                                    "id" => 2, 
                                    'driver' => 'InputFunction',
                                ),
                                array(
                                    "id" => 3, 
                                    'driver' => 'InputFunction',
                                ),
                                array(
                                    "id" => 4, 
                                    'driver' => 'InputFunction',
                                ),
                            ), 
                            true
                        ),
                    ),
                    "setParam" => array(
                        array(
                            "fctsApplied",
                            array(
                                array(
                                    "id" => 0, 
                                    'driver' => 'InputFunction',
                                ),
                                array(
                                    "id" => 1, 
                                    'driver' => 'InputFunction',
                                ),
                                array(
                                    "id" => 2, 
                                    'driver' => 'InputFunction',
                                ),
                                array(
                                    "id" => 3, 
                                    'driver' => 'InputFunction',
                                ),
                                array(
                                    "id" => 4, 
                                    'driver' => 'InputFunction',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config   The configuration to use
    * @param array $channels The channels to give it
    * @param mixed $expect   The value we expect back
    *
    * @return null
    *
    * @dataProvider dataApply
    */
    public function testApply(
        $config, $channels, $expect
    ) {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyTable("Device");
        $sys->resetMock($config);
        $obj = Fcts::factory($sys, $dev, $channels);
        $obj->apply();
        $ret = $sys->retrieve("Device");
        $this->assertEquals($expect, $ret);
        unset($obj);
    }

}
?>
