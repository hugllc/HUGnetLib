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
namespace HUGnet\base;
/** This is a required class */
require_once CODE_BASE.'base/IOPBase.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is our units class */
require_once CODE_BASE."devices/datachan/Driver.php";

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
class IOPBaseTest extends \PHPUnit_Framework_TestCase
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
        $obj = IOPBaseStub::factory($config, $gateway, $class, $dev);
        // Make sure we have the right object
        $this->assertInstanceOf(
            "HUGnet\base\IOPBase", $obj, "Class wrong"
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
                    "Entry" => array(
                        "fullArray" => array(
                            "a" => "b",
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                true,
                array(
                    'longName' => 'Silly IOPBase Driver 1',
                    'shortName' => 'SSD1',
                    'id' => 253,
                    'asdf' => 3,
                    'params' => Array (
                        0 => 1,
                        1 => 2,
                        2 => 3,
                        3 => 4,
                    ),
                    'type' => 'TestIOPBaseDriver1',
                    "extraText" => array(),
                    "extraDefault" => array(),
                    "extraValues" => array(),
                    "fullEntry" => array("a" => "b"),
                    "tableEntry" => array(),
                    "otherTables" => array(),
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
                    "Entry" => array(
                        "fullArray" => array(
                            "a" => "b",
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                true,
                array(
                    'longName' => 'Silly IOPBase Driver 1',
                    'shortName' => 'SSD1',
                    'id' => 255,
                    'asdf' => 3,
                    'params' => Array (
                        0 => 1,
                        1 => 2,
                        2 => 3,
                        3 => 4,
                    ),
                    'type' => 'TestIOPBaseDriver1',
                    "extraText" => array(),
                    "extraDefault" => array(),
                    "extraValues" => array(),
                    "fullEntry" => array("a" => "b"),
                    "tableEntry" => array(),
                    "otherTables" => array(),
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
                    "Entry" => array(
                        "fullArray" => array(
                            "a" => "b",
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                false,
                array(
                    'id' => 255,
                    'asdf' => 3,
                    'params' => Array (
                        0 => 1,
                        1 => 2,
                        2 => 3,
                        3 => 4,
                    ),
                    'type' => 'TestIOPBaseDriver1',
                    'tableEntry' => array(),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $config The configuration to use
    * @param mixed  $class  This is either the name of a class or an object
    * @param string $table  The table to use
    * @param mixed  $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider data2Array
    */
    public function test2Array(
        $config, $class, $table, $expect
    ) {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyBase("Device");
        $sys->resetMock($config);
        $obj = IOPBaseStub::factory($sys, null, $class, $dev);
        $json = $obj->toArray($table);
        $this->assertEquals($expect, $json);
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataMix()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "Driver" => "EDEFAULT",
                            "extra" => array(12, 34, 56, 78, 90, 11),
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                "extra",
                array(2 => 3, 5 => 6),
                array(array("extra", array(12, 34, 3, 78, 90, 6))),
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "location" => "",
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                "location",
                "Hello There",
                array(array("location", "Hello There")),
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "location" => "asdf",
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                "location",
                "Hello There",
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
    * @param mixed  $value  The value to set
    * @param mixed  $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataMix
    */
    public function testMix(
        $config, $class, $field, $value, $expect
    ) {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyBase("Device");
        $sys->resetMock($config);
        $obj = IOPBaseStub::factory($sys, null, $class, $dev);
        $obj->mix($field, $value);
        $ret = $sys->retrieve();
        $this->assertSame($expect, $ret["Table"]["set"]);
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
                            "driver" => "EmptyIOPBase",
                            "id" => 2,
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                "id",
                2,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "driver" => "ADuCDAC",
                            "id" => 1,
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                "longName",
                "Silly IOPBase Driver 1",
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
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyBase("Device");
        $sys->resetMock($config);
        $obj = IOPBaseStub::factory($sys, null, $class, $dev);
        $this->assertSame($expect, $obj->get($field));
        unset($obj);
    }
    /**
    * Data provider for testLoad
    *
    * @return array
    */
    public static function dataLoad()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "selectOneInto" => true,
                        "sanitizeWhere" => array(
                            "dev" => 2,
                            "sensor" => 0,
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                array("dev" => 2, "sensor" => 0),
                array(
                    "selectOneInto" => array(
                        array(
                            array("dev" => 2, "sensor" => 0),
                        ),
                    ),
                    "clearData" => array(array()),
                    "sanitizeWhere" => array(
                        array(
                            array(
                                "dev" => 2,
                                "sensor" => 0,
                            ),
                        ),
                    ),
                ),
                true,
            ),
            array(
                array(
                    "Table" => array(
                        "selectOneInto" => false,
                        "get" => array(
                            "id" => 0xFA,
                            "type" => "raw",
                            "sensor" => 5,
                        ),
                        "sanitizeWhere" => array(
                            "dev" => 2,
                            "sensor" => 0,
                        ),
                        "insertRow" => true,
                        "updateRow" => true,
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                array("dev" => 2, "sensor" => 0),
                array(
                    "fromAny" => array(
                        array(
                            array(
                                "dev" => 2,
                                "sensor" => 0,
                            ),
                        ),
                    ),
                    "selectOneInto" => array(
                        array(
                            array("dev" => 2, "sensor" => 0),
                        ),
                    ),
                ),
                true,
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param object $config      The configuration to use
    * @param object $class       The table class to use
    * @param mixed  $data        The gateway data to set
    * @param array  $expectTable The table to expect
    * @param bool   $return      The expected return
    *
    * @return null
    *
    * @dataProvider dataLoad
    */
    public function testLoad($config, $class, $data, $expectTable, $return)
    {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyBase("Device");
        $sys->resetMock($config);
        $obj = IOPBaseStub::factory($sys, null, $class, $dev);
        $ret = $obj->load($data);
        $this->assertSame($return, $ret, "Return Wrong");
        $ret = $class->retrieve("Table");
        foreach ((array)$expectTable as $key => $expect) {
            $this->assertEquals($expect, $ret[$key], "$key Data Wrong");
        }
    }
    /**
    * Data provider for testLoad
    *
    * @return array
    */
    public static function dataChange()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 0xFA,
                            "type" => "raw",
                            "sensor" => 5,
                        ),
                    ),
                ),
                new \HUGnet\DummyTable(),
                array(
                    "id" => 5,
                    "name" => 3,
                    "value" => 1,
                ),
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
                        "updateRow" => array(array()),
                    ),
                ),
                true,
            ),
            array(
                array(
                    "Table" => array(
                        "selectOneInto" => true,
                        "get" => array(
                            'extra' => array(1,2,3,4,5,6,7,8),
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                array("dev" => 2, "sensor" => 0, "extra" => array(1,2,3,4,5,6,7,8)),
                array(
                    "Table" => array(
                        'fromAny' => array(
                            array(
                                array(
                                    'dev' => 2,
                                    'sensor' => 0,
                                    "extra" => array(1,2,3,4,5,6,7,8),
                                ),
                            )
                        ),
                        'updateRow' => array(array()),
                    ),
                ),
                true,
            ),
            array(
                array(
                    "Table" => array(
                        "selectOneInto" => false,
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                false,
                array(
                ),
                false,
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param object $config      The configuration to use
    * @param object $class       The table class to use
    * @param mixed  $data        The gateway data to set
    * @param array  $expectTable The table to expect
    * @param bool   $return      The expected return
    *
    * @return null
    *
    * @dataProvider dataChange
    */
    public function testChange($config, $class, $data, $expectTable, $return)
    {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyBase("Device");
        $obj = IOPBaseStub::factory($sys, null, $class, $dev);
        $sys->resetMock($config);
        $ret = $obj->change($data);
        $this->assertSame($return, $ret, "Return Wrong");
        $this->assertEquals($expectTable, $class->retrieve(), "Data Wrong");
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataEncode()
    {
        return array(
            array(
                new \HUGnet\DummySystem(),
                null,
                array(
                    "id" => 0xFD,
                ),
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 0xFD,
                            "HWPartNum"    => "0039-12-01-C",
                            "FWPartNum"    => "0039-20-03-C",
                            "FWVersion"    => "1.2.3",
                            "DeviceGroup"  => "FFFFFF",
                            "TimeConstant" => "01",
                        ),
                    ),
                ),
                "FD",
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $config The configuration to use
    * @param mixed  $device The device to set
    * @param mixed  $class  This is either the name of a class or an object
    * @param array  $mocks  The mocks to use
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataEncode
    */
    public function testEncode(
        $config, $device, $class, $mocks, $expect
    ) {
        $config->resetMock($mocks);
        $dev = new \HUGnet\DummyBase("Device");
        $obj = IOPBaseStub::factory($config, $device, $class, $dev);
        $this->assertEquals(
            $expect, $obj->encode(), "Return Wrong"
        );
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataDecode()
    {
        return array(
            array(
                new \HUGnet\DummySystem(),
                null,
                array(
                    "id" => 5,
                ),
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 5,
                            "HWPartNum"    => "0039-12-01-C",
                            "FWPartNum"    => "0039-20-03-C",
                            "FWVersion"    => "1.2.3",
                            "DeviceGroup"  => "FFFFFF",
                            "TimeConstant" => 1,
                        ),
                    ),
                ),
                "051234",
                array(
                    array("id", 5),
                    array("RawSetup", "1234"),
                    array("driver", "TestIOPBaseDriver1"),
                ),
            ),
            array(
                new \HUGnet\DummySystem(),
                null,
                array(
                    "id" => 5,
                ),
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 5,
                            "HWPartNum"    => "0039-12-01-C",
                            "FWPartNum"    => "0039-20-03-C",
                            "FWVersion"    => "1.2.3",
                            "DeviceGroup"  => "FFFFFF",
                            "TimeConstant" => 1,
                        ),
                    ),
                ),
                "05",
                array(
                    array("id", 5),
                    array("RawSetup", ""),
                ),
            ),
            array(
                new \HUGnet\DummySystem(),
                null,
                array(
                    "id" => 5,
                ),
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 5,
                            "HWPartNum"    => "0039-12-01-C",
                            "FWPartNum"    => "0039-20-03-C",
                            "FWVersion"    => "1.2.3",
                            "DeviceGroup"  => "FFFFFF",
                            "TimeConstant" => 1,
                        ),
                    ),
                ),
                "", // This is a bad input.
                null,
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $config The configuration to use
    * @param mixed  $device The device to set
    * @param mixed  $class  This is either the name of a class or an object
    * @param array  $mocks  The mocks to use
    * @param string $string The string to feed into the decode
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDecode
    */
    public function testDecode(
        $config, $device, $class, $mocks, $string, $expect
    ) {
        $config->resetMock($mocks);
        $dev = new \HUGnet\DummyBase("Device");
        $obj = IOPBaseStub::factory($config, $device, $class, $dev);
        $obj->decode($string);
        $ret = $config->retrieve();
        $this->assertEquals(
            $expect, $ret["Table"]["set"], "Calls Wrong"
        );
        unset($obj);
    }
    /**
    * This tests the object creation
    *
    * @return null
    */
    public function testIsNewTrue() 
    {
        $sys = $this->getMock('\HUGnet\System', array('now'));
        $dev = new \HUGnet\DummyBase("Device");
        $obj = IOPBaseStub2::factory(
            $sys, array("dev" => 5, "input" => 0), null, $dev
        );
        $this->assertTrue(
            $obj->isNew()
        );
        unset($obj);
    }
    /**
    * This tests the object creation
    *
    * @return null
    *
    */
    public function testIsNewfalse() 
    {
        $sys = $this->getMock('\HUGnet\System', array('now'));
        $dev = new \HUGnet\DummyBase("Device");
        $obj = IOPBaseStub2::factory(
            $sys, array("dev" => 5, "input" => 0), null, $dev
        );
        unset($obj);
        $obj = IOPBaseStub2::factory(
            $sys, array("dev" => 5, "input" => 0), null, $dev
        );
        $this->assertFalse(
            $obj->isNew()
        );
        unset($obj);
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
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class IOPBaseStub extends IOPBase
{
}
/**
 * Base driver class for devices.
 *
 * This class deals with loading the drivers and figuring out what driver needs
 * to be loaded.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class IOPBaseStub2 extends IOPBase
{
    /** These are our keys to search for.  Null means search everything given */
    protected $keys = array("dev", "input");
    /**
    * This is the cache for the drivers.
    */
    protected $driverLoc = "inputTable";

    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $dbtable The table to use
    * @param object &$device The device object to use
    * @param array  $table   The table to use.  This forces the table, instead of
    *                        using the database to find it
    *
    * @return null
    */
    public static function &factory(
        &$system, $data=null, $dbtable=null, &$device = null, $table = null
    ) {
        if (empty($dbtable)) {
            $dbtable = "DeviceInputs";
        }
        $object = parent::factory($system, $data, $dbtable, $device, $table);
        return $object;
    }
}
namespace HUGnet\devices\replaceme;
/**
 * Base driver class for devices.
 *
 * This class deals with loading the drivers and figuring out what driver needs
 * to be loaded.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
    );
    /**
    * This is where all of the defaults are stored.
    */
    private $_default = array(
        "longName" => "Unknown Output",
        "shortName" => "Unknown",
        "extraText" => array(),
        "extraDefault" => array(),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(),
    );
    /**
    * This is where all of the driver information is stored.
    *
    * Drivers must be registered here, otherwise they will never get loaded.  The
    * index in the root array is the driver name.  It should be exactly the same
    * as the driver class name.
    */
    private static $_drivers = array(
        "FC:DEFAULT"                 => "TestIOPBaseDriver1",
        "FD:DEFAULT"                 => "TestIOPBaseDriver2",
        "FF:DEFAULT"                 => "TestIOPBaseDriver1",
    );
    /**
    * This is where the correlation between the drivers and the arch is stored.
    *
    * If a driver is not registered here, it will not be in the list of drivers
    * that can be chosen.
    *
    */
    private $_arch = array(
        "AVR" => array(
        ),
        "ADuC" => array(
        ),
        "all" => array(
            0xFC => "TestIOPBaseDriver1",
            0xFD => "TestIOPBaseDriver2",
            0xFF => "TestIOPBaseDriver1"
        ),
    );
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$process The process in question
    *
    * @return null
    */
    protected function __construct(&$process)
    {
        $this->_process = &$process;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_process);
    }
    /**
    * This is the destructor
    *
    * @return object
    */
    public function process()
    {
        return $this->_process;
    }
    /**
    * This function creates the system.
    *
    * @param string $driver   The driver to load
    * @param object &$process The process object
    *
    * @return null
    */
    public static function &factory($driver, &$process)
    {
        $class = '\\HUGnet\\devices\\processTable\\drivers\\'.$driver;
        $file = dirname(__FILE__)."/drivers/".$driver.".php";
        if (file_exists($file)) {
            include_once $file;
        }
        if (class_exists($class)) {
            return new $class($process);
        }
        return new \HUGnet\devices\replaceme\drivers\TestIOPBaseDriver1(
            $process
        );
    }
    /**
    * Checks to see if a piece of data exists
    *
    * @param string $name The name of the property to check
    *
    * @return true if the property exists, false otherwise
    */
    public function present($name)
    {
        return !is_null($this->get($name, $this->process()));
    }
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    public function get($name)
    {
        $ret = null;
        if (isset($this->params[$name])) {
            $ret = $this->params[$name];
        } else if (isset($this->_default[$name])) {
            $ret = $this->_default[$name];
        }
        if (is_string($ret) && (strtolower(substr($ret, 0, 8)) === "getextra")) {
            $key = (int)substr($ret, 8);
            $ret = $this->getExtra($key);
        }
        return $ret;
    }
    /**
    * Returns all of the parameters and defaults in an array
    *
    * @return array of data from the process
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function toArray()
    {
        $return = array();
        $keys = array_merge(array_keys($this->_default), array_keys($this->params));
        foreach ($keys as $key) {
            $return[$key] = $this->get($key);
        }
        return $return;
    }
    /**
    * Returns the driver that should be used for a particular device
    *
    * @param mixed  $sid  The ID of the process
    * @param string $type The type of the process
    *
    * @return string The driver to use
    */
    public static function getDriver($sid, $type = "DEFAULT")
    {
        $try = array(
            sprintf("%02X", (int)$sid).":".$type,
            sprintf("%02X", (int)$sid),
            sprintf("%02X", (int)$sid).":DEFAULT",
        );
        foreach ($try as $mask) {
            if (isset(self::$_drivers[$mask])) {
                return self::$_drivers[$mask];
            }
        }
        return "TestIOPBaseDriver1";
    }
    /**
    * Returns an array of types that this process could be
    *
    * @param int $sid The ID to check
    *
    * @return The extra value (or default if empty)
    */
    public static function getTypes($sid)
    {
        $array = array();
        $process = sprintf("%02X", (int)$sid);
        foreach ((array)self::$_drivers as $key => $driver) {
            $k = explode(":", $key);
            if (trim(strtoupper($k[0])) == $process) {
                $array[$k[1]] = $driver;
            }
        }
        return (array)$array;
    }
    /**
    * Registers an extra driver to be used by the class
    *
    * The new class will only be registered if it doesn't already exist
    *
    * @param string $key   The key to use for the class
    * @param string $class The class to use for the key
    *
    * @return null
    */
    public static function register($key, $class)
    {
        $driver = '\\HUGnet\\devices\\processTable\\drivers\\'.$class;
        if (class_exists($driver) && !isset(self::$_drivers[$key])) {
            self::$_drivers[$key] = $class;
        }
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
        $extra = (array)$this->process()->get("extra");
        if (!isset($extra[$index])) {
            $extra = $this->get("extraDefault");
        }
        return $extra[$index];
    }

    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string The string to decode
    *
    * @return array
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function decode($string)
    {
        /* Do nothing by default */
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function encode()
    {
        $string  = "";
        return $string;
    }

    /**
    * Returns the driver that should be used for a particular device
    *
    * @return array The array of drivers that will work
    */
    public function getDrivers()
    {
        return (array)$this->_arch[$this->process()->device()->get("arch")]
            + (array)$this->_arch["all"];
    }
    /**
    * Returns the driver object
    *
    * @param array $table The table to use.  This only works on the first call
    *
    * @return object The driver requested
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function &entry($table = null)
    {
        return new \HUGnet\DummyBase("Entry");
    }
}

namespace HUGnet\devices\replaceme\drivers;

/**
 * Default sensor driver
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage IOPBases
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class TestIOPBaseDriver1 extends \HUGnet\devices\replaceme\Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Silly IOPBase Driver 1",
        "shortName" => "SSD1",
    );
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      IOPBase of the A to D converter
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
 * @subpackage IOPBases
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class TestIOPBaseDriver2 extends \HUGnet\devices\replaceme\Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Silly IOPBase Driver 2",
        "shortName" => "SSD2",
    );
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      IOPBase of the A to D converter
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
