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
require_once CODE_BASE.'db/tables/Devices.php';
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
class DeviceTestsTest extends TableTestBase
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
            $this->system, $data, "DeviceTests", $this->connect
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
            TEST_CONFIG_BASE.'files/DeviceTestsTableTest.xml'
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
            $system, $data, "DeviceTests", $connect
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
            $system, $data, "DeviceTests", $connect
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
                    $system, $data, "DeviceTests", $connect
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
            array("FWVersion", "1.2.3", "1.2.3"),
            array("FWVersion", "01.02.03", "1.2.3"),
            array("FWVersion", "011005", "1.10.5"),
            array("FWVersion", "", ""),
            array("BtldrVersion", "0.1.2", "0.1.2"),
            array("BtldrVersion", "01.02.03", "1.2.3"),
            array("BtldrVersion", "020406", "2.4.6"),
            array("FWPartNum", "0039380141", "0039-38-01-A"),
            array("FWPartNum", "00393801A", "0039-38-01-A"),
            array("HWPartNum", "01046-03-01-AQ", "1046-03-01-A"),
            array("HWPartNum", null, ""),
            array("id", 0x159, 345),
            array("id", 1.3, 1),
            array("id", "1", 1),
            array("MicroSN", "0011223344556677889933", "0011223344556677889933"),
            array("MicroSN", "1020304050607080901020", "1020304050607080901020"),
            array("TestDate", "0", 0),
            array("TestDate", "12", 12),
            array("TestResult", 1, "PASS"),
            array("TestResult", 0, "FAIL"),
            array("TestResult", -1, "FAIL"),
            array("TestData", 1.2, ""),
            array("TestData", "asdf", "asdf"),
            array("TestData", array("hello" => "there"), '{"hello":"there"}'),
            array("TestsFailed", array("hello" => "there"), '{"hello":"there"}'),
            array("TestsFailed", "none", "none"),
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
    * Data provider for testInsertRow
    *
    * @return array
    */
    public static function dataInsertRow()
    {
        return array(
            // Auto increment
            array(
                array(
                    "id" => (string)0x008012,
                    "HWPartNum" => "1046-03-01-A",
                    "FWPartNum" => "0039-38-01-C",
                    "FWVersion" => "0.3.1",
                    "BtldrVersion" => "0.3.0",
                    "MicroSN" => "0011223344556677889933",
                    "TestDate" => "-1",
                    "TestResult" => "FAIL",
                    "TestData" => "0",
                    "TestsFailed" => "",
                ),
                false,
                array(
                    array(
                        "id" => (string)0x008012,
                        "HWPartNum" => "1046-03-01-A",
                        "FWPartNum" => "0039-38-01-C",
                        "FWVersion" => "0.3.1",
                        "BtldrVersion" => "0.3.0",
                        "MicroSN" => "0011223344556677889933",
                        "TestDate" => "-1",
                        "TestResult" => "FAIL",
                        "TestData" => "0",
                        "TestsFailed" => "",
                    ),
                ),
                true,
            ),
            array(
                array(
                    "id" => 0,
                ),
                true,
                array(
                    array( 
                    "id" => "0",
                    "HWPartNum" => "",
                    "FWPartNum" => "",
                    "FWVersion" => "",
                    "BtldrVersion" => "",
                    "MicroSN" => "",
                    "TestDate" => "0",
                    "TestResult" => "FAIL",
                    "TestData" => "",
                    "TestsFailed" => "None",
                    ),
                ),
                true,
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param bool  $replace Replace any records that collide with this one.
    * @param array $expect  The expected rows
    * @param array $return  The expected return
    *
    * @dataProvider dataInsertRow
    *
    * @return null
    */
    public function testInsertRow($preload, $replace, $expect, $return)
    {
        $obj = \HUGnet\db\Table::factory(
            $this->system, $preload, "DeviceTests", $this->connect
        );
        $ret = $obj->insertRow($replace);
        $this->assertSame($return, $ret, "Return Wrong");
        $stmt = $this->pdo->query("SELECT * FROM `devicetests`");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows, "Rows wrong");
    }
}

?>
