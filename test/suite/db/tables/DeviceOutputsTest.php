<?php
/**
 * Tests the filter class
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
 * @subpackage SuiteTables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
namespace HUGnet\db\tables;
/** This is a required class */
require_once CODE_BASE.'db/Table.php';
/** This is a required class */
require_once CODE_BASE.'db/Connection.php';
/** This is a required class */
require_once CODE_BASE.'db/tables/DeviceOutputs.php';
/** This is a required class */
require_once TEST_BASE."db/tables/TableTestBase.php";
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once CODE_BASE.'system/Device.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteTables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceOutputsTest extends TableTestBase
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
        $this->config = array(
            "System" => array(
                "get" => array(
                    "servers" => array(
                        array(
                            "driver" => "sqlite",
                            "file" => ":memory:",
                            "group" => "default",
                        ),
                    ),
                    "verbose" => 0,
                ),
            ),
        );
        $this->system = new \HUGnet\DummySystem("System");
        $this->system->resetMock($this->config);
        $this->connect = \HUGnet\db\Connection::factory($this->system);
        $this->pdo = &$this->connect->getDBO("default");
        $data = array(
        );
        $this->o = \HUGnet\db\Table::factory(
            $this->system, $data, "DeviceOutputs", $this->connect
        );
        parent::Setup();
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
        $this->o = null;
        $this->config = null;
    }
    /**
    * This gets us our database preload
    *
    * @access protected
    *
    * @return null
    */
    protected function getDataSet()
    {
        return $this->createXMLDataSet(
            TEST_CONFIG_BASE.'files/DeviceOutputsTableTest.xml'
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataColumns()
    {
        $system = new \HUGnet\DummySystem("System");
        $connect = \HUGnet\db\Connection::factory($system);
        $obj = \HUGnet\db\Table::factory(
            $system, $data, "DeviceOutputs", $connect
        );
        return TableTestBase::splitObject($obj, "sqlColumns");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIndexes()
    {
        $system = new \HUGnet\DummySystem("System");
        $connect = \HUGnet\db\Connection::factory($system);
        $obj = \HUGnet\db\Table::factory(
            $system, $data, "DeviceOutputs", $connect
        );
        return TableTestBase::splitObject($obj, "sqlIndexes");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataVars()
    {
        $system = new \HUGnet\DummySystem("System");
        $connect = \HUGnet\db\Connection::factory($system);
        return array(
            array(
                \HUGnet\db\Table::factory(
                    $system, $data, "DeviceOutputs", $connect
                )
            ),
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
            array("params", "asdfasdfasdfasdf", "asdfasdfasdfasdf"),
            array("params", array(1,2,3,4), "[1,2,3,4]"),
            array("tableEntry", "asdfasdfasdfasdf", "asdfasdfasdfasdf"),
            array("tableEntry", array(1,2,3,4), "[1,2,3,4]"),
            array("extra", array(1,2,3,4), array(1,2,3,4)),
            array("id", "123", 123),
            array("dev", "123", 123),
            array("output", "123", 123),
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
        $this->o->set($var, $value);
        $data = $this->readAttribute($this->o, "data");
        $this->assertSame($expect, $this->o->get($var));
    }
    /**
    * Data provider for testInsertRow
    *
    * @return array
    */
    public static function dataExists()
    {
        return array(
            array(
                array(
                    array(
                        "dev" => 156,
                        "output" => 23,
                        "id" => 1,
                    ),
                ),
                array(
                    "dev" => 156,
                    "output" => 23,
                    "id" => 1,
                ),
                true
            ),
            array(
                array(
                    array(
                        "dev" => 158,
                        "output" => 23,
                        "id" => 1,
                    ),
                ),
                array(
                    "dev" => 156,
                    "output" => 23,
                    "id" => 1,
                ),
                false
            ),
            array(
                array(
                ),
                array(
                    "dev" => 156,
                    "output" => 23,
                    "id" => 1,
                ),
                false
            ),
        );
    }
    /**
    * Tests the insert of a DeviceID
    *
    * @param array $preload The data to load into the database
    * @param mixed $data    The data to use
    * @param array $expect  The expected return
    *
    * @dataProvider dataExists
    *
    * @return null
    */
    public function testExists($preload, $data, $expect)
    {
        foreach ((array)$preload as $load) {
            $this->o->fromAny($load);
            $this->o->insertRow();
        }
        $this->o->clearData();
        $this->o->fromAny($data);
        $ret = $this->o->exists();
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testFromArray
    *
    * @return array
    */
    public static function dataFromArray()
    {
        return array(
            array(
                array(
                    "dev" => 156,
                    "output" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "units" => "&deg;C",
                    "decimals" => 4,
                    "storageUnits" => "&deg;C",
                    "unitType" => "Temperature",
                    "driver" => "asdf",
                    "min" => 5,
                    "max" => 15,
                    "extra" => array(),
                ),
                array(
                    "group" => 'default',
                    "dev" => 156,
                    "output" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "driver" => "asdf",
                    "params" => json_encode(
                        array("extra" => array())
                    ),
                    'tableEntry' => '',
                ),
            ),
            array(
                array(
                    "dev" => 156,
                    "output" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "units" => "&deg;C",
                    "decimals" => 4,
                    "params" => '{"storageUnits":"&deg;C","unitType":"Temperature"}',
                ),
                array(
                    "group" => 'default',
                    "dev" => 156,
                    "output" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "driver"  => "EmptyOutput",
                    "params" => '{"storageUnits":"&deg;C","unitType":"Temperature"}',
                    'tableEntry' => '',
                ),
            ),
            array(
                array(
                    "dev" => 156,
                    "output" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "units" => "&deg;C",
                    "decimals" => 4,
                    "storageUnits" => "&deg;C",
                    "unitType" => "Temperature",
                    "params" => array(1,2,3,4, "extra" => array(1,2,3,4)),
                ),
                array(
                    "group" => 'default',
                    "dev" => 156,
                    "output" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "driver"  => "EmptyOutput",
                    "params" => '{"extra":[1,2,3,4]}',
                    'tableEntry' => '',
                ),
            ),
        );
    }
    /**
    * Tests the insert of a DeviceID
    *
    * @param mixed $data   The data to use
    * @param array $expect The expected return
    *
    * @dataProvider dataFromArray
    *
    * @return null
    */
    public function testFromArray($data, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($data);
        $data = $this->readAttribute($this->o, "data");
        $this->assertEquals($expect, $data);
    }
    /**
    * Data provider for testFromArray
    *
    * @return array
    */
    public static function data2Array()
    {
        return array(
            array(
                array(
                    "dev" => 156,
                    "output" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "units" => "&deg;C",
                    "decimals" => 4,
                    "storageUnits" => "&deg;C",
                    "unitType" => "Temperature",
                    "params" => '{"storageUnits":"&deg;C","unitType":"Temperature"}',
                ),
                true,
                array(
                    "group" => 'default',
                    "dev" => 156,
                    "output" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "storageUnits" => "&deg;C",
                    "driver"  => "EmptyOutput",
                    "unitType" => "Temperature",
                    'tableEntry' => '',
                ),
            ),
        );
    }
    /**
    * Tests the insert of a DeviceID
    *
    * @param mixed $data    The data to use
    * @param bool  $default Whether to return default items
    * @param array $expect  The expected return
    *
    * @dataProvider data2Array
    *
    * @return null
    */
    public function test2Array($data, $default, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($data);
        $this->assertEquals($expect, $this->o->toArray($default));
    }
}

?>
