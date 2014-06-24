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
require_once CODE_BASE.'db/tables/Datacollectors.php';
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
class DataCollectorsTest extends TableTestBase
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
            $this->system, $data, "Datacollectors", $this->connect
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
            TEST_CONFIG_BASE.'files/DataCollectorsTableTest.xml'
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
            $system, $data, "Datacollectors", $connect
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
            $system, $data, "Datacollectors", $connect
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
                    $system, $data, "Datacollectors", $connect
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
            array(
                "LastContact", "2003-02-28 02:00:00", gmmktime(02, 0, 0, 2, 28, 2003)
            ),
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
    * data provider for testSet
    *
    * @return array
    */
    public static function dataOnGateway()
    {
        return array(
            array(
                1,
                true,
                array(
                    0 => array(
                        'group' => 'default',
                        'GatewayKey' => '1',
                        'uuid' => '48aa1c44-85be-4179-beab-8bbad51c1824',
                        'name' => 'Test2',
                        'ip' => '192.168.192.125',
                        'LastContact' => 123456789,
                        'SetupString' => "c",
                        'Config' => 'd',
                        'Runtime' => 'e',
                    ),
                    1 => array(
                        'group' => 'default',
                        'GatewayKey' => '1',
                        'uuid' => '46ba8126-57a1-4038-b5e4-2e2585f9f5a5',
                        'name' => 'Test1',
                        'ip' => '192.168.192.5',
                        'LastContact' => 12345678,
                        'SetupString' => "a",
                        'Config' => 'b',
                        'Runtime' => 'c',
                    ),
                ),
            ),
            array(
                8,
                false,
                array(
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param int   $GatewayKey The id to use
    * @param bool  $return     The expected return
    * @param mixed $expect     The expected data
    *
    * @return null
    *
    * @dataProvider dataOnGateway
    */
    public function testOnGateway($GatewayKey, $return, $expect)
    {
        $ret = $this->o->onGateway($GatewayKey);
        $this->assertSame($return, $ret, "Return Wrong");
        if ($ret) {
            $data = array();
            do {
                $data[] = $this->o->toArray();
            } while ($this->o->nextInto());
            $this->assertEquals($expect, $data, "Data Wrong");
        }
    }
    /**
    * data provider for testForceTable
    *
    * @return array
    */
    public static function dataFromAny()
    {
        return array(
            array(
                array(
                    "System" => array(
                        "config" => array(1, 2, 3, 4, 5),
                        "get" => array(
                            "uuid" => "fa7d187c-9b3f-4c3e-983d-d1311dd6d3cf",
                        ),
                    ),
                    "Devices" => array(
                        "get" => array(
                            "GatewayKey" => 5,
                            "DeviceName" => "fa7d187c-9b3f-4c3e-983d-d1311dd6d3cf",
                            "DeviceLocation" => "1.2.3.4",
                            "id" => 0xC,
                            "HWPartNum" => "0039-26-02-P",
                            "FWPartNum" => "0039-26-00-P",
                            "FWVersion" => "0.1.2",
                            "DeviceGroup" => "FFFFFF",
                            "params" => "",
                        ),
                    ),
                ),
                array(
                    'GatewayKey' => 5,
                    "ip" => '1.2.3.4',
                    "uuid" => "fa7d187c-9b3f-4c3e-983d-d1311dd6d3cf",
                    'SetupString' => '000000000C00392602500039260050000102'
                        .'FFFFFFFFFA7D187C9B3F4C3E983DD1311DD6D3CF01020304000500',
                    'Config' => json_encode(array(1, 2, 3, 4, 5)),
                ),
            ),
        );
    }

    /**
    * test the forceTable routine
    *
    * @param array $mock   The mocks to load
    * @param mixed $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataFromAny
    */
    public function testFromAny($mock, $expect)
    {
        $this->system->resetMock($mock);
        $device = \HUGnet\Device::factory($this->system);
        $this->o->fromAny($device);
        $this->assertEquals($expect, $this->o->toArray(false));
    }
}

?>
