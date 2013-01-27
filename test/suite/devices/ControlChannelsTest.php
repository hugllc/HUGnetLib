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
namespace HUGnet\devices;
/** This is a required class */
require_once CODE_BASE.'devices/ControlChannels.php';
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ControlChannelsTest extends \PHPUnit_Framework_TestCase
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
        ControlChannels::factory($test, $test2);
    }
    /**
    * This tests the exception when a system object is not passed
    *
    * @return null
    */
    public function testCreateThrowException2()
    {
        $test = new \HUGnet\DummyTable();
        // This just resets the mock
        $test->resetMock();
        $this->setExpectedException("InvalidArgumentException");
        // This throws an exception because $test is not a object
        ControlChannels::factory($test, $test2);
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
        $obj = ControlChannels::factory($sys, $dev);
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
                new \HUGnet\DummySystem(),
                new \HUGnet\DummyTable("Device"),
                array(
                    "Device" => array(
                        "get" => array(
                            array("OutputTables"),
                            array("controlChannels"),
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
        $obj = ControlChannels::factory($config, $gateway);
        // Make sure we have the right object
        $this->assertInstanceOf(
            "HUGnet\devices\ControlChannels", $obj, "Class wrong"
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
                3
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataCount
    */
    public function testCount(
        $config, $expect
    ) {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyTable("Device");
        $sys->resetMock($config);
        $obj = ControlChannels::factory($sys, $dev);
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
                    "Device" => array(
                        "get" => array(
                            "OutputTables" => 5,
                            "controlChannels" => json_encode(
                                array(
                                    array(
                                        "label" => "Output 0",
                                    ),
                                    array(
                                        "label" => "Output 1",
                                    ),
                                    array(
                                    ),
                                    array(
                                        "label" => "Output 3",
                                    ),
                                    array(
                                        "label" => "Output 4",
                                    ),
                                )
                            ),
                        ),
                        "output" => array(
                            '0' => new \HUGnet\DummyTable("Output0"),
                            '1' => new \HUGnet\DummyTable("Output1"),
                            '2' => new \HUGnet\DummyTable("Output2"),
                            '3' => new \HUGnet\DummyTable("Output3"),
                            '4' => new \HUGnet\DummyTable("Output4"),
                        ),

                    ),
                    "Output0" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 0,
                                "min" => 0,
                                "max" => 5150,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                    "Output1" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 1,
                                "min" => 0,
                                "max" => 5151,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                    "Output2" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 2,
                                "min" => 0,
                                "max" => 5152,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                    "Output3" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 3,
                                "min" => 0,
                                "max" => 5153,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                    "Output4" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 4,
                                "min" => 0,
                                "max" => 5154,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                ),
                null,
                false,
                array(
                    array(
                        "label" => "Output 0",
                    ),
                    array(
                        "label" => "Output 1",
                    ),
                    array(
                        "label" => "PWM 2",
                    ),
                    array(
                        "label" => "Output 3",
                    ),
                    array(
                        "label" => "Output 4",
                    ),
                ),
            ),
            array(  // #2
                array(
                    "Device" => array(
                        "get" => array(
                            "OutputTables" => 5,
                            "controlChannels" => json_encode(
                                array(
                                    array(
                                        "label" => "Output 0",
                                    ),
                                    array(
                                        "label" => "Output 1",
                                    ),
                                    array(
                                        "label" => "Output 2",
                                    ),
                                    array(
                                        "label" => "Output 3",
                                    ),
                                    array(
                                        "label" => "Output 4",
                                    ),
                                )
                            ),
                        ),
                        "output" => array(
                            '0' => new \HUGnet\DummyTable("Output0"),
                            '1' => new \HUGnet\DummyTable("Output1"),
                            '2' => new \HUGnet\DummyTable("Output2"),
                            '3' => new \HUGnet\DummyTable("Output3"),
                            '4' => new \HUGnet\DummyTable("Output4"),
                        ),

                    ),
                    "Output0" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 0,
                                "min" => 0,
                                "max" => 5150,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                    "Output1" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 1,
                                "min" => 0,
                                "max" => 5151,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                    "Output2" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 2,
                                "min" => 0,
                                "max" => 5152,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                    "Output3" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 3,
                                "min" => 0,
                                "max" => 5153,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                    "Output4" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 4,
                                "min" => 0,
                                "max" => 5154,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                ),
                array(
                    array(
                        "index" => 2,
                        "output" => 4,
                        "min" => 8,
                        "max" => 8150,
                        "label" => "here",
                    ),
                    array(
                        "label" => "Output 1",
                    ),
                    array(
                    ),
                    array(
                        "label" => "Output 3",
                    ),
                    array(
                        "label" => "Output 4",
                    ),
                ),
                true,
                array(
                    array(
                        "label" => "here",
                        "index" => 0,
                        "output" => 0,
                        "min" => 0,
                        "max" => 5150,
                        "channel" => 0,
                    ),
                    array(
                        "label" => "Output 1",
                        "index" => 0,
                        "output" => 1,
                        "min" => 0,
                        "max" => 5151,
                        "channel" => 1,
                    ),
                    array(
                        "label" => "PWM 2",
                        "index" => 0,
                        "output" => 2,
                        "min" => 0,
                        "max" => 5152,
                        "channel" => 2,
                    ),
                    array(
                        "label" => "Output 3",
                        "index" => 0,
                        "output" => 3,
                        "min" => 0,
                        "max" => 5153,
                        "channel" => 3,
                    ),
                    array(
                        "label" => "Output 4",
                        "index" => 0,
                        "output" => 4,
                        "min" => 0,
                        "max" => 5154,
                        "channel" => 4,
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
        $obj = ControlChannels::factory($sys, $dev, $channels);
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
                    array(
                        "controlChannels",
                        json_encode(array()),
                    ),
                ),
            ),
            array(  // #1
                array(
                    "Device" => array(
                        "get" => array(
                            "OutputTables" => 5,
                            "controlChannels" => json_encode(
                                array(
                                    array(
                                        "label" => "Output 0",
                                    ),
                                    array(
                                        "label" => "Output 1",
                                    ),
                                    array(
                                        "label" => "Output 2",
                                    ),
                                    array(
                                    ),
                                    array(
                                        "label" => "Output 4",
                                    ),
                                )
                            ),
                        ),
                        "output" => array(
                            '0' => new \HUGnet\DummyTable("Output0"),
                            '1' => new \HUGnet\DummyTable("Output1"),
                            '2' => new \HUGnet\DummyTable("Output2"),
                            '3' => new \HUGnet\DummyTable("Output3"),
                            '4' => new \HUGnet\DummyTable("Output4"),
                        ),

                    ),
                    "Output0" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 0,
                                "min" => 0,
                                "max" => 5150,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                    "Output1" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 1,
                                "min" => 0,
                                "max" => 5151,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                    "Output2" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 2,
                                "min" => 0,
                                "max" => 5152,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                    "Output3" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 3,
                                "min" => 0,
                                "max" => 5153,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                    "Output4" => array(
                        "channels" => array(
                            array(
                                "index" => 0,
                                "output" => 4,
                                "min" => 0,
                                "max" => 5154,
                                "label" => "PWM",
                            ),
                        ),
                    ),
                ),
                null,
                array(
                    array(
                        "controlChannels",
                        json_encode(
                            array(
                                array(
                                    "label" => "Output 0",
                                ),
                                array(
                                    "label" => "Output 1",
                                ),
                                array(
                                    "label" => "Output 2",
                                ),
                                array(
                                    "label" => "PWM 3",
                                ),
                                array(
                                    "label" => "Output 4",
                                ),
                            )
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
        $obj = ControlChannels::factory($sys, $dev, $channels);
        $obj->store();
        $ret = $sys->retrieve("Device");
        $this->assertEquals($expect, $ret["set"]);
        unset($obj);
    }
    /**
    * Data provider for testSelect
    *
    * @return array
    */
    public static function dataSelect()
    {
        return array(
            array(
                array(
                    "Device" => array(
                        "get" => array(
                            "OutputTables" => 3,
                            "controlChannels" => array(
                                array(
                                    "label" => "Here",
                                ),
                                array(
                                    "label" => "I",
                                ),
                                array(
                                    "label" => "Come",
                                ),
                            ),
                        ),
                        "output" => array(
                            "0" => new \HUGnet\DummyTable("Sensor0"),
                            "1" => new \HUGnet\DummyTable("Sensor1"),
                            "2" => new \HUGnet\DummyTable("Sensor2"),
                        ),
                    ),
                    "Sensor0" => array(
                        "channels" => array(
                            array(
                                "unitType" => "Pressure",
                                "storageUnit" => "psi",
                                "storageType" =>
                                    \HUGnet\devices\datachan\Driver::TYPE_RAW,
                            ),
                        ),
                    ),
                    "Sensor1" => array(
                        "channels" => array(
                            array(
                                "unitType" => "Percent",
                                "storageUnit" => "decimal",
                                "storageType" =>
                                    \HUGnet\devices\datachan\Driver::TYPE_RAW,
                            ),
                        ),
                    ),
                    "Sensor2" => array(
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
                    "a" => "b",
                ),
                array(
                    "a" => "b",
                    0 => 'Here',
                    1 => 'I',
                    2 => 'Come',
                )
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param array $array  The array to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataSelect
    */
    public function testSelect(
        $config, $array, $expect
    ) {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyTable("Device");
        $sys->resetMock($config);
        $obj = ControlChannels::factory($sys, $dev);
        $this->assertEquals($expect, $obj->select($array));
        unset($obj);
    }

}
?>
