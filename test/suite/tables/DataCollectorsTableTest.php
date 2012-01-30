<?php
/**
 * Tests the filter class
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
 * @subpackage SuiteTables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is a required class */
require_once CODE_BASE.'tables/DataCollectorsTable.php';
/** This is a required class */
require_once CODE_BASE.'containers/DeviceContainer.php';
/** This is a required class */
require_once TEST_BASE."tables/HUGnetDBTableTestBase.php";

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteTables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DataCollectorsTableTest extends HUGnetDBTableTestBase
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
        $config = array(
            "hugnet_database" => "HUGNet",
            "script_gateway" => 4,
            "pluginData" => array(
                5 => "h",
            ),
            "admin_email" => "me@mydomain.com",
            "useSocket" => "dummy",

        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->pdo = &$this->config->servers->getPDO();
        $this->o = new DataCollectorsTable();
        $this->o->create();
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
        $obj = new DataCollectorsTable();
        return HUGnetDBTableTestBase::splitObject($obj, "sqlColumns");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIndexes()
    {
        $obj = new DataCollectorsTable();
        return HUGnetDBTableTestBase::splitObject($obj, "sqlIndexes");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataVars()
    {
        return array(
            array(new DataCollectorsTable()),
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
        $this->o->$var = $value;
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
                        'id' => '484',
                        'GatewayKey' => '1',
                        'name' => 'Test2',
                        'ip' => '192.168.192.125',
                        'LastContact' => 123456789,
                        'SetupString' => "c",
                        'Config' => 'd',
                    ),
                    1 => array(
                        'group' => 'default',
                        'id' => '404',
                        'GatewayKey' => '1',
                        'name' => 'Test1',
                        'ip' => '192.168.192.5',
                        'LastContact' => 12345678,
                        'SetupString' => "a",
                        'Config' => 'b',
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
            $this->assertSame($expect, $data, "Data Wrong");
        }
    }
    /**
    * Data provider for testRegisterMe
    *
    * @return array
    */
    public static function dataRegisterMe()
    {
        return array(
            array(
                array(
                    "id" => 156,
                    "GatewayKey" => 23,
                    "name" => "hello",
                    "ip" => "192.168.54.2",
                    "SetupString" => "there",
                    "Config" => "Hello",
                ),
                array(
                    0 => array(
                        'id' => '404',
                        'GatewayKey' => '1',
                        'name' => 'Test1',
                        'ip' => '192.168.192.5',
                        'LastContact' => '12345678',
                        'SetupString' => "a",
                        'Config' => "b",
                    ),
                    1 => array(
                        'id' => '484',
                        'GatewayKey' => '1',
                        'name' => 'Test2',
                        'ip' => '192.168.192.125',
                        'LastContact' => '123456789',
                        'SetupString' => "c",
                        'Config' => "d",
                    ),
                    2 => array(
                        'id' => '848',
                        'GatewayKey' => '2',
                        'name' => 'Test3',
                        'ip' => '192.168.192.82',
                        'LastContact' => '123456789',
                        'SetupString' => "e",
                        'Config' => "f",
                    ),
                    3 => array(
                        'id' => '156',
                        'GatewayKey' => '23',
                        'name' => 'hello',
                        'ip' => '192.168.54.2',
                        'SetupString' => "there",
                        'Config' => "Hello",
                    ),
                ),
                true,
            ),
        );
    }
    /**
    * Tests the insert of a DeviceID
    *
    * @param mixed $data   The data to use
    * @param array $expect The expected table row
    * @param bool  $ret    The expected return
    *
    * @dataProvider dataRegisterMe
    *
    * @return null
    */
    public function testRegisterMe($data, $expect, $ret)
    {
        $time = time();
        $this->o->clearData();
        $this->o->fromAny($data);
        $return = $this->o->registerMe();
        $stmt = $this->pdo->query("SELECT * FROM `datacollectors`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $k => $v) {
            if ($v["id"] == $data["id"]) {
                $this->assertGreaterThanOrEqual(
                    $time, $v["LastContact"], "LastContact not set properly"
                );
                unset ($rows[$k]["LastContact"]);
            }
        }
        $this->assertSame($expect, $rows);
        $this->assertSame($ret, $return);
    }
    /**
    * Data provider for testRegisterMe
    *
    * @return array
    */
    public static function dataGetMine()
    {
        return array(
            array(
                array(
                    "id" => 0x156,
                    "DeviceID" => "000156",
                    "GatewayKey" => 1,
                    "DeviceLocation" => "192.168.192.5",
                ),
                404,
            ),
        );
    }
    /**
    * Tests the insert of a DeviceID
    *
    * @param mixed $data   The data to use
    * @param array $expect The expected table row
    *
    * @dataProvider dataGetMine
    *
    * @return null
    */
    public function testGetMine($data, $expect)
    {
        $dev = new DeviceContainer($data);
        $ret = $this->o->getMine($dev);
        $this->assertSame($expect, $ret);
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
                new DeviceContainer(
                    array(
                        "id" => 12,
                        "HWPartNum" => "0039-21-02-A",
                        "FWPartNum" => "0039-20-01-A",
                        "FWVersion" => "0.1.2",
                        "GatewayKey" => 5,
                        "DeviceName" => "This is a Name",
                    )
                ),
                array(
                    "id" => 12,
                    'GatewayKey' => 5,
                    'name' => 'This is a Name',
                    'LastContact' => 0,
                    'SetupString' => '000000000C00392102410039200141000102FFFFFF00',
                    'Config' => "YTo1OntzOjE1OiJodWduZXRfZGF0YWJhc2UiO3M6NjoiSFVHT"
                        ."mV0IjtzOjE0OiJzY3JpcHRfZ2F0ZXdheSI7aTo0O3M6MTA6InBsdWdpb"
                        ."kRhdGEiO2E6MTp7aTo1O3M6MToiaCI7fXM6MTE6ImFkbWluX2VtYWlsI"
                        ."jtzOjE1OiJtZUBteWRvbWFpbi5jb20iO3M6OToidXNlU29ja2V0IjtzO"
                        ."jU6ImR1bW15Ijt9",
                ),
            ),
        );
    }

    /**
    * test the forceTable routine
    *
    * @param array $dev    The device to use
    * @param mixed $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataFromAny
    */
    public function testFromAny($dev, $expect)
    {
        $this->o->fromAny($dev);
        $this->assertSame($expect, $this->o->toArray(false));
    }
}

?>
