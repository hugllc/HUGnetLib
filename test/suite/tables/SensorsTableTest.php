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
require_once CODE_BASE.'tables/SensorsTable.php';
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
class SensorsTableTest extends HUGnetDBTableTestBase
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
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->pdo = &$this->config->servers->getPDO();
        $this->o = new SensorsTable();
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
            TEST_CONFIG_BASE.'files/SensorsTableTest.xml'
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataColumns()
    {
        $obj = new SensorsTable();
        return HUGnetDBTableTestBase::splitObject($obj, "sqlColumns");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIndexes()
    {
        $obj = new SensorsTable();
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
            array(new SensorsTable()),
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
            array("dataType", UnitsBase::TYPE_DIFF, UnitsBase::TYPE_DIFF),
            array("dataType", "asdffdsas", UnitsBase::TYPE_RAW),
            array("params", "asdfasdfasdfasdf", "asdfasdfasdfasdf"),
            array("params", array(1,2,3,4), "[1,2,3,4]"),
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
                        "sensor" => 23,
                        "id" => 1,
                    ),
                ),
                array(
                    "dev" => 156,
                    "sensor" => 23,
                    "id" => 1,
                ),
                true
            ),
            array(
                array(
                    array(
                        "dev" => 158,
                        "sensor" => 23,
                        "id" => 1,
                    ),
                ),
                array(
                    "dev" => 156,
                    "sensor" => 23,
                    "id" => 1,
                ),
                false
            ),
            array(
                array(
                ),
                array(
                    "dev" => 156,
                    "sensor" => 23,
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
                    "sensor" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "units" => "&deg;C",
                    "decimals" => 4,
                    "storageUnits" => "&deg;C",
                    "unitType" => "Temperature",
                ),
                array(
                    "group" => 'default',
                    "dev" => 156,
                    "sensor" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "dataType" => UnitsBase::TYPE_RAW,
                    "units" => '&deg;C',
                    "decimals" => 4,
                    "params" => '{"storageUnits":"&deg;C","unitType":"Temperature"}',
                ),
            ),
            array(
                array(
                    "dev" => 156,
                    "sensor" => 23,
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
                    "sensor" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "dataType" => UnitsBase::TYPE_RAW,
                    "units" => '&deg;C',
                    "decimals" => 4,
                    "params" => '{"storageUnits":"&deg;C","unitType":"Temperature"}',
                ),
            ),
            array(
                array(
                    "dev" => 156,
                    "sensor" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "units" => "&deg;C",
                    "decimals" => 4,
                    "storageUnits" => "&deg;C",
                    "unitType" => "Temperature",
                    "params" => array(1,2,3,4),
                ),
                array(
                    "group" => 'default',
                    "dev" => 156,
                    "sensor" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "dataType" => UnitsBase::TYPE_RAW,
                    "units" => '&deg;C',
                    "decimals" => 4,
                    "params" => '{"storageUnits":"&deg;C","unitType":"Temperature"}',
                ),
            ),
        );
    }
    /**
    * Tests the insert of a DeviceID
    *
    * @param mixed $data    The data to use
    * @param array $expect  The expected return
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
                    "sensor" => 23,
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
                    "sensor" => 23,
                    "id" => 1,
                    "type" => "asdf",
                    "location" => "HERE",
                    "dataType" => UnitsBase::TYPE_RAW,
                    "units" => '&deg;C',
                    "decimals" => 4,
                    "storageUnits" => "&deg;C",
                    "unitType" => "Temperature",
                ),
            ),
        );
    }
    /**
    * Tests the insert of a DeviceID
    *
    * @param mixed $data    The data to use
    * @param
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
