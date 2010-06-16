<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../tables/DevicesHistoryTable.php';
require_once dirname(__FILE__)."/HUGnetDBTableTestBase.php";

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DevicesHistoryTableTest extends HUGnetDBTableTestBase
{
    static $preload = array(
        array(
            "id" => "15",
            "SaveDate" => "123456789",
            "SetupString" => "aassddff",
            "SensorString" => "ffssddaa",
        ),
        array(
            "id" => "15",
            "SaveDate" => "123456795",
            "SetupString" => "aaasssdddfff",
            "SensorString" => "fffsssdddaaa",
        ),
        array(
            "id" => "15",
            "SaveDate" => "123456799",
            "SetupString" => "aaaassssddddffff",
            "SensorString" => "ffffssssddddaaaa",
        ),
    );
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
        $data = array(
        );
        $this->o = new DevicesHistoryTable($data);
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
            dirname(__FILE__).'/../files/DevicesHistoryTableTest.xml'
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataColumns()
    {
        $o = new DevicesHistoryTable();
        return HUGnetDBTableTestBase::splitObject($o, "sqlColumns");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIndexes()
    {
        $o = new DevicesHistoryTable();
        return HUGnetDBTableTestBase::splitObject($o, "sqlIndexes");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataVars()
    {
        return array(
            array(new DevicesHistoryTable()),
        );
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataConstructor()
    {
        return array(
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
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $expect)
    {
        $o = new DevicesHistoryTable($preload);
        $this->assertSame($expect, $o->toArray());
    }
    /**
    * data provider for testSet
    *
    * @return array
    */
    public static function dataSet()
    {
        return array(
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
    * data provider for testForceTable
    *
    * @return array
    */
    public static function dataInsertRow()
    {
        return array(
            array(
                array(
                    "id" => 12,
                    "SetupString" => "asdf",
                    "SensorString" => "fdsa",
                    "SaveDate" => 123456,
                ),
                array_merge(
                    self::$preload,
                    array(
                        array(
                            "id" => "12",
                            "SaveDate" => "123456",
                            "SetupString" => "asdf",
                            "SensorString" => "fdsa",
                        ),
                    )
                ),
                12,
            ),
            array(
                array(
                    "id" => 15,
                    "SaveDate" => 123456,
                    "SetupString" => "aassddff",
                    "SensorString" => "ffssddaa",
                ),
                self::$preload,
                15,
            ),
        );
    }

    /**
    * test the forceTable routine
    *
    * @param array $preload The data to preoload into the table
    * @param mixed $expect  The expected return
    * @param int   $id      The ID we expect to get back
    *
    * @return null
    *
    * @dataProvider dataInsertRow
    */
    public function testInsertRow($preload, $expect, $id)
    {
        $this->o->fromAny($preload);
        $this->o->insertRow();
        $stmt = $this->pdo->query("SELECT * FROM `devicesHistory`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows, "Database is wrong");
        $this->assertSame($id, $this->o->id, "ID is wrong");
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
                new DeviceContainer(array(
                    "id" => 12,
                    "HWPartNum" => "0039-21-02-A",
                    "FWPartNum" => "0039-20-01-A",
                    "FWVersion" => "0.1.2",
                )),
                array(
                    "group" => "default",
                    "id" => 12,
                    "SaveDate" => 0,
                    "SetupString" => "000000000C00392102410039200141000102FFFFFF00",
                    "SensorString" => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiI"
                        ."iO3M6NzoiU2Vuc29ycyI7aTowO30=",
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
        $this->assertThat($this->o->SaveDate, $this->greaterThan(time() - 10000));
        $this->o->SaveDate = 0;
        $this->assertAttributeSame($expect, "data", $this->o);
    }

}

?>
