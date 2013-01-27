<?php
/**
 * Tests the filter class
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
 * @subpackage SuiteTables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
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
require_once CODE_BASE.'db/tables/RawHistory.php';
/** This is a required class */
require_once TEST_BASE."db/tables/TableTestBase.php";
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once CODE_BASE.'system/Device.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteTables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class RawHistoryTest extends TableTestBase
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
        $this->pdo = &$this->connect->getPDO("default");
        $data = array(
        );
        $this->o = \HUGnet\db\Table::factory(
            $this->system, $data, "RawHistory", $this->connect
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
        $this->o->__destruct();
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
            TEST_CONFIG_BASE.'files/RawHistoryTableTest.xml'
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
            $system, $data, "RawHistory", $connect
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
            $system, $data, "RawHistory", $connect
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
                    $system, $data, "RawHistory", $connect
                )
            ),
        );
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataFromArray()
    {
        return array(
            array(
                array(
                    "id" => 100,
                    "packet" => array(
                        "To" => "000012",
                        "From" => "000283",
                        "Date" => 1048472484,
                        "Command" => "55",
                        "Data" => "01020304",
                    ),
                    "device" => new \HUGnet\DummyTable("Device"),
                    "command" => "55",
                    "dataIndex" => 123,
                ),
                array(
                    "group" => "default",
                    "id" => 100,
                    "Date" => '0',
                    "packet" => array(
                        "To" => "000012",
                        "From" => "000283",
                        "Date" => 1048472484,
                        "Command" => "55",
                        "Data" => "01020304",
                    ),
                    "devicesHistoryDate" => 0,
                    "command" => "55",
                    "dataIndex" => 123,
                ),
            ),
            array(
                array(
                    "id" => 100,
                    "packet" => array(
                        "To" => "000012",
                        "From" => "000283",
                        "Date" => 1048472484,
                        "Command" => "55",
                        "Data" => "01020304",
                    ),
                    "device" => array(),
                    "command" => "55",
                    "dataIndex" => 123,
                ),
                array(
                    "group" => "default",
                    "id" => 100,
                    "Date" => '0',
                    "packet" => array(
                        "To" => "000012",
                        "From" => "000283",
                        "Date" => 1048472484,
                        "Command" => "55",
                        "Data" => "01020304",
                    ),
                    "devicesHistoryDate" => 0,
                    "command" => "55",
                    "dataIndex" => 123,
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The value to preload
    * @param array $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataFromArray
    */
    public function testFromArray($preload, $expect)
    {
        //$date = time();
        $this->o->fromArray($preload);
        $data = $this->readAttribute($this->o, "data");
        $row = $this->o->toArray();
        /*
        $this->assertThat(
            $row["devicesHistoryDate"],
            $this->greaterThanOrEqual($date),
            "Date is wrong on key $key"
        );
        unset($row["devicesHistoryDate"]);
        */
        $this->assertSame($expect, $row);
    }
    /**
    * data provider for testSet
    *
    * @return array
    */
    public static function dataSet()
    {
        return array(
            array("Date", "2010-04-25 13:42:23", 1272202943),
            array("Date", "2010-04-25", 1272153600),
            array("Date", "Sun, 25 April 2010, 1:42:23pm", 1272202943),
            array("Date", 1234567890, 1234567890),
            array("Date", "This is not a date", 0),
            array("id", 71, 71),
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
        $this->assertSame($expect, $data[$var]);
    }
    /**
    * Data provider for testToDB
    *
    * @return array
    */
    public static function dataToDB()
    {
        return array(
            array(  // #0
                array(),
                array(
                    "id" => 18,
                    "Date" => 1048472484,
                    "packet" => array(
                        "To" => "000012",
                        "From" => "000283",
                        "Date" => 1048472484,
                        "Command" => "55",
                        "Data" => "",
                        "Reply" => array(
                            "To" => "000283",
                            "From" => "000012",
                            "Date" => 1048472485,
                            "Command" => "01",
                            "Data" => "631902BA100200B947124902008F0FA103",
                        ),
                    ),
                    "device" => array(
                        "id" => 18,
                        "DeviceID" => "000012",
                        "HWPartNum" => "0039-21-02-A",
                        "FWPartNum" => "0039-20-14-C",
                        "FWVerson" => "0.1.2",
                    ),
                    "command" => "55",
                    "dataIndex" => 123,
                ),
                array(
                    'packet' => '{"To":"000012","From":"000283",'
                        .'"Date":1048472484,"Command":"55","Data":"",'
                        .'"Reply":{"To":"000283","From":"000012",'
                        .'"Date":1048472485,"Command":"01",'
                        .'"Data":"631902BA100200B947124902008F0FA103"}}',
                    'devicesHistoryDate' => 0,
                    'command' => '55',
                    'dataIndex' => 123,
                    'id' => 18,
                    'Date' => 1048472484,
                ),
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $database The database stuff to load
    * @param array $preload  The array to preload into the class
    * @param array $expect   The expected return
    *
    * @dataProvider dataToDB
    *
    * @return null
    */
    public function testToDB($database, $preload, $expect)
    {
        foreach ($database as $p) {
            $this->o->clearData();
            $this->o->fromAny($p);
            $this->o->insertRow();
        }
        $this->o->clearData();
        $this->o->fromAny($preload);
        $array = $this->o->toDB();
        $this->assertEquals($expect, $array, "", 0.1);
    }

}

?>
