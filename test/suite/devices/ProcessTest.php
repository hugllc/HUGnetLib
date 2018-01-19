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
require_once CODE_BASE.'devices/Process.php';
/** This is a required class */
require_once CODE_BASE.'devices/processTable/Driver.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is our units class */
require_once CODE_BASE."devices/datachan/Driver.php";
/** This is our interface */
require_once CODE_BASE."devices/processTable/DriverInterface.php";

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
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ProcessTest extends \PHPUnit_Framework_TestCase
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
        \HUGnet\devices\processTable\Driver::register(
            "FD:DEFAULT", "TestProcessDriver1"
        );
        \HUGnet\devices\processTable\Driver::register(
            "FC:DEFAULT", "TestProcessDriver2"
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
                array("dev" => 2, "sensor" => 0),
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
        $obj = Process::factory($config, $gateway, $class, $dev);
        // Make sure we have the right object
        $this->assertTrue(
            (get_class($obj) === "HUGnet\devices\Process"), "Class wrong"
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
                    'longName' => 'Silly Process Driver 1',
                    'shortName' => 'SSD1',
                    'id' => 253,
                    'asdf' => 3,
                    'params' => Array (
                        0 => 1,
                        1 => 2,
                        2 => 3,
                        3 => 4,
                    ),
                    'type' => 'TestProcessDriver1',
                    'otherTypes' => Array (
                        'DEFAULT' => 'TestProcessDriver1'
                    ),
                    'validIds' => Array (
                        255 => 'Empty Slot'
                    ),
                    "extraText" => array(),
                    "extraDesc" => array(),
                    "extraNames" => array(),
                    "extraDefault" => array(),
                    // Integer is the size of the field needed to edit
                    // Array   is the values that the extra can take
                    // Null    nothing
                    "extraValues" => array(),
                    "otherTables" => array(),
                    "tableEntry" => array(),
                    "requires" => array(),
                    "provides" => array(),
                ),
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 0xFF,
                        ),
                        "toArray" => array(
                            "id" => 0xFF,
                            "asdf" => 3,
                            "params" => json_encode(array(1,2,3,4)),
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                array(
                    'longName' => 'Empty Process',
                    'shortName' => 'Empty',
                    'id' => 255,
                    'asdf' => 3,
                    'params' => Array (
                        0 => 1,
                        1 => 2,
                        2 => 3,
                        3 => 4,
                    ),
                    'type' => 'EmptyProcess',
                    'otherTypes' => Array (
                        'DEFAULT' => 'EmptyProcess'
                    ),
                    'validIds' => Array (
                        255 => 'Empty Slot'
                    ),
                    "extraText" => array(),
                    "extraDesc" => array(),
                    "extraNames" => array(),
                    "extraDefault" => array(),
                    // Integer is the size of the field needed to edit
                    // Array   is the values that the extra can take
                    // Null    nothing
                    "extraValues" => array(),
                    "otherTables" => array(),
                    "tableEntry" => array(),
                    "requires" => array(),
                    "provides" => array(),
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
    * @dataProvider data2Array
    */
    public function test2Array(
        $config, $class, $expect
    ) {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyBase("Device");
        $sys->resetMock($config);
        $obj = Process::factory($sys, null, $class, $dev);
        $json = $obj->toArray();
        $this->assertEquals($expect, $json);
        unset($obj);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataPush()
    {
        return array(
            array( // #0
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 7,
                        ),
                        "send" => new \HUGnet\DummyBase("Packet"),
                    ),
                    "Process" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Packet" => array(
                        "reply" => "FF",
                    ),
                ),
                true,
            ),
            array( // #1
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 7,
                        ),
                        "send" => new \HUGnet\DummyBase("Packet"),
                    ),
                    "Process" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Packet" => array(
                        "reply" => "AD",
                    ),
                ),
                false,
            ),
            array( // #2
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 7,
                        ),
                        "send" => "asdf",
                    ),
                    "Process" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                ),
                false,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $mocks  The value to preload into the mocks
    * @param array $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataPush
    */
    public function testPush($mocks, $expect)
    {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyBase("Device");
        $sys->resetMock($mocks);
        $obj = Process::factory($sys, null, $class, $dev);
        $ret = $obj->push();
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataPull()
    {
        return array(
            array( // #0
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 7,
                        ),
                        "send" => new \HUGnet\DummyBase("Packet"),
                    ),
                    "Process" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Packet" => array(
                        "reply" => "FF",
                    ),
                ),
                true,
            ),
            array( // #1
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 7,
                        ),
                        "send" => "asdf",
                    ),
                    "Process" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                ),
                false,
            ),
            array( // #2
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 7,
                        ),
                        "send" => "asdf",
                    ),
                    "Process" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                ),
                false,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $mocks  The value to preload into the mocks
    * @param array $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataPull
    */
    public function testPull($mocks, $expect)
    {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyBase("Device");
        $sys->resetMock($mocks);
        $obj = Process::factory($sys, null, $class, $dev);
        $ret = $obj->pull();
        $this->assertSame($expect, $ret);
    }

}

namespace HUGnet\devices\processTable\drivers;

/**
 * Default sensor driver
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Processs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class TestProcessDriver1 extends \HUGnet\devices\processTable\Driver
    implements \HUGnet\devices\processTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Silly Process Driver 1",
        "shortName" => "SSD1",
    );
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Process of the A to D converter
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
        return $A * 2;
    }

}
/**
 * Default sensor driver
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Processs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class TestProcessDriver2 extends \HUGnet\devices\processTable\Driver
    implements \HUGnet\devices\processTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Silly Process Driver 2",
        "shortName" => "SSD2",
    );
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Process of the A to D converter
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
        return $A / 2;
    }

}


?>
