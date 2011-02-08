<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../tables/HistoryTableBase.php';
require_once dirname(__FILE__).'/../../containers/DeviceContainer.php';
require_once dirname(__FILE__).'/../../base/UnitsBase.php';
require_once dirname(__FILE__)."/../tables/HUGnetDBTableTestBase.php";

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HistoryTableBaseTest extends HUGnetDBTableTestBase
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
            "plugins" => array(
                "dir" => realpath(dirname(__FILE__)."/../files/plugins/"),
            ),

        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->pdo = &$this->config->servers->getPDO();
        $this->o = new HistoryTableBaseTestStub();
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
            dirname(__FILE__).'/../files/HistoryTableBaseTest.xml'
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataColumns()
    {
        $o = new HistoryTableBaseTestStub();
        return HUGnetDBTableTestBase::splitObject($o, "sqlColumns");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIndexes()
    {
        $o = new HistoryTableBaseTestStub();
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
            array(new HistoryTableBaseTestStub()),
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
            array(
                array(
                    "id"  => 41,
                    "Date"   => "2003-02-28 01:59:00",
                    "deltaT"  => 5.2,
                    "Data0"  => 0,
                    "Data1"  => 1,
                    "Data2"  => 2,
                    "Data3"  => 3,
                    "Data4"  => 4,
                    "Data5"  => 5,
                    "Data6"  => 6,
                    "Data7"  => 7,
                    "Data8"  => 8,
                    "Data9"  => 9,
                    "Data10"  => 10,
                    "Data11"  => 11,
                    "Data12"  => 12,
                    "Data13"  => 13,
                    "Data14"  => 14,
                ),
                10,
                array(
                    "group" => "default",
                    "raw" => array(),
                    "converted" => false,
                    "id"  => 41,
                    "Date"   => 1046397540,
                    "deltaT"  => 5.2,
                    "Data0"  => 0,
                    "Data1"  => 1,
                    "Data2"  => 2,
                    "Data3"  => 3,
                    "Data4"  => 4,
                    "Data5"  => 5,
                    "Data6"  => 6,
                    "Data7"  => 7,
                    "Data8"  => 8,
                    "Data9"  => 9,
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The value to preload
    * @param int   $cols    The number of data columns to use
    * @param array $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $cols, $expect)
    {
        $o = new HistoryTableBaseTestStub($preload, $cols);
        $this->assertSame($expect, $o->toArray());
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataFromDataArray()
    {
        return array(
            array(
                array(
                    "group" => "remote",
                    "deltaT" => 1,
                    0 => array(
                        "value" => 1,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    1 => array(
                        "value" => 2,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    2 => array(
                        "value" => 4,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    3 => array(
                        "value" => 8,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "diff",
                        "raw" => 1834,
                    ),
                    4 => array(
                        "value" => 16,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "diff",
                        "raw" => 4842,
                    ),
                    5 => array(
                        "value" => 32,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    "DataIndex" => 127,
                    "timeConstant" => 5,
                ),
                array(
                    "group" => "remote",
                    "raw" => array(
                        0  => null,
                        1  => null,
                        2  => null,
                        3  => 1834,
                        4  => 4842,
                        5  => null,
                        6  => null,
                        7  => null,
                        8  => null,
                        9  => null,
                        10  => null,
                        11  => null,
                        12  => null,
                        13  => null,
                        14  => null,
                    ),
                    "converted" => false,
                    "id"  => 0,
                    "Date"   => 0,
                    "deltaT"  => 1,
                    "Data0"  => 1,
                    "Data1"  => 2,
                    "Data2"  => 4,
                    "Data3"  => 8,
                    "Data4"  => 16,
                    "Data5"  => 32,
                    "Data6"  => null,
                    "Data7"  => null,
                    "Data8"  => null,
                    "Data9"  => null,
                    "Data10"  => null,
                    "Data11"  => null,
                    "Data12"  => null,
                    "Data13"  => null,
                    "Data14"  => null,
                ),
            ),
            array(
                array(
                    "id" => 21,
                    "Date" => 1234,
                    "deltaT" => 1,
                    0 => array(
                        "value" => 1,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    1 => array(
                        "value" => 2,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    2 => array(
                        "value" => 4,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    3 => array(
                        "value" => 8,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    4 => array(
                        "value" => 16,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    5 => array(
                        "value" => 32,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    "DataIndex" => 127,
                    "timeConstant" => 5,
                ),
                array(
                    "group" => "default",
                    "raw" => array(
                        0  => null,
                        1  => null,
                        2  => null,
                        3  => null,
                        4  => null,
                        5  => null,
                        6  => null,
                        7  => null,
                        8  => null,
                        9  => null,
                        10  => null,
                        11  => null,
                        12  => null,
                        13  => null,
                        14  => null,
                    ),
                    "converted" => false,
                    "id"  => 21,
                    "Date"   => 1234,
                    "deltaT"  => 1,
                    "Data0"  => 1,
                    "Data1"  => 2,
                    "Data2"  => 4,
                    "Data3"  => 8,
                    "Data4"  => 16,
                    "Data5"  => 32,
                    "Data6"  => null,
                    "Data7"  => null,
                    "Data8"  => null,
                    "Data9"  => null,
                    "Data10"  => null,
                    "Data11"  => null,
                    "Data12"  => null,
                    "Data13"  => null,
                    "Data14"  => null,
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
    * @dataProvider dataFromDataArray
    */
    public function testFromDataArray($preload, $expect)
    {
        $this->o->fromDataArray($preload);
        $this->assertSame($expect, $this->o->toArray());
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataFromAny()
    {
        return array(
            array(
                array(
                    "deltaT" => 1,
                    0 => array(
                        "value" => 1,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    1 => array(
                        "value" => 2,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    2 => array(
                        "value" => 4,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    3 => array(
                        "value" => 8,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    4 => array(
                        "value" => 16,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    5 => array(
                        "value" => 32,
                        "units" => "testUnit",
                        "unitType" => "firstUnit",
                        "dataType" => "raw",
                    ),
                    "DataIndex" => 127,
                    "timeConstant" => 5,
                ),
                array(
                    "group" => "default",
                    "converted" => false,
                    "id"  => 0,
                    "Date"   => 0,
                    "deltaT"  => 1,
                    "Data0"  => 1,
                    "Data1"  => 2,
                    "Data2"  => 4,
                    "Data3"  => 8,
                    "Data4"  => 16,
                    "Data5"  => 32,
                    "Data6"  => null,
                    "Data7"  => null,
                    "Data8"  => null,
                    "Data9"  => null,
                    "Data10"  => null,
                    "Data11"  => null,
                    "Data12"  => null,
                    "Data13"  => null,
                    "Data14"  => null,
                ),
            ),
            array(
                array(
                    "id"  => 41,
                    "Date"   => "2003-02-28 01:59:00",
                    "deltaT"  => 5.2,
                    "Data0"  => 0,
                    "Data1"  => 1,
                    "Data2"  => 2,
                    "Data3"  => 3,
                    "Data4"  => 4,
                    "Data5"  => 5,
                    "Data6"  => 6,
                    "Data7"  => 7,
                    "Data8"  => 8,
                    "Data9"  => 9,
                    "Data10"  => 10,
                    "Data11"  => 11,
                    "Data12"  => 12,
                    "Data13"  => 13,
                    "Data14"  => 14,
                ),
                array(
                    "group" => "default",
                    "converted" => false,
                    "id"  => 41,
                    "Date"   => 1046397540,
                    "deltaT"  => 5.2,
                    "Data0"  => 0,
                    "Data1"  => 1,
                    "Data2"  => 2,
                    "Data3"  => 3,
                    "Data4"  => 4,
                    "Data5"  => 5,
                    "Data6"  => 6,
                    "Data7"  => 7,
                    "Data8"  => 8,
                    "Data9"  => 9,
                    "Data10"  => 10,
                    "Data11"  => 11,
                    "Data12"  => 12,
                    "Data13"  => 13,
                    "Data14"  => 14,
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
    * @dataProvider dataFromAny
    */
    public function testFromAny($preload, $expect)
    {
        $this->o->fromAny($preload);
        $this->assertAttributeSame($expect, "data", $this->o);
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
    public static function dataInsertRow()
    {
        return array(
            array(
                array(
                    "id"  => 41,
                    "Date"   => "2003-02-28 01:59:00",
                    "deltaT"  => 5.2,
                    "Data0"  => 0,
                    "Data1"  => 1,
                    "Data2"  => 2,
                    "Data3"  => 3,
                    "Data4"  => 4,
                    "Data5"  => 5,
                    "Data6"  => 6,
                    "Data7"  => 7,
                    "Data8"  => 8,
                    "Data9"  => 9,
                    "Data10"  => 10,
                    "Data11"  => 11,
                    "Data12"  => 12,
                    "Data13"  => 13,
                    "Data14"  => 14,
                ),
                array(
                    array(
                        "id"  => "41",
                        "Date"   => "1046397540",
                        "deltaT"  => "5.2",
                        "Data0"  => "0.0",
                        "Data1"  => "1.0",
                        "Data2"  => "2.0",
                        "Data3"  => "3.0",
                        "Data4"  => "4.0",
                        "Data5"  => "5.0",
                        "Data6"  => "6.0",
                        "Data7"  => "7.0",
                        "Data8"  => "8.0",
                        "Data9"  => "9.0",
                        "Data10"  => "10.0",
                        "Data11"  => "11.0",
                        "Data12"  => "12.0",
                        "Data13"  => "13.0",
                        "Data14"  => "14.0",
                    ),
                ),
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param array $expect  The expected return
    *
    * @dataProvider dataInsertRow
    *
    * @return null
    */
    public function testInsertRecord($preload, $expect)
    {
        HistoryTableBaseTestStub::insertRecord($preload);
        $stmt = $this->pdo->query(
            "SELECT * FROM `History` WHERE `id` = ".$preload["id"]
        );
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataToOutput()
    {
        return array(
            array(   // #0
                array(
                    "id"  => 41,
                    "Date"   => "2003-02-28 01:59:00",
                    "deltaT"  => 5.2,
                    "Data0"  => 0,
                    "Data1"  => 1,
                    "Data2"  => 2,
                    "Data3"  => 3,
                    "Data4"  => 4,
                    "Data5"  => 5,
                    "Data6"  => 6,
                    "Data7"  => 7,
                    "Data8"  => 8,
                    "Data9"  => 9,
                    "Data10"  => 10,
                    "Data11"  => 11,
                    "Data12"  => 12,
                    "Data13"  => 13,
                    "Data14"  => 14,
                ),
                null,
                null,
                array(),
                array(
                    "group" => "default",
                    "raw" => "Array",
                    "converted" => "",
                    "id"  => "41",
                    "Date"   => "2003-02-27 19:59:00",
                    "deltaT"  => "5.2",
                    "Data0"  => "0",
                    "Data1"  => "1",
                    "Data2"  => "2",
                    "Data3"  => "3",
                    "Data4"  => "4",
                    "Data5"  => "5",
                    "Data6"  => "6",
                    "Data7"  => "7",
                    "Data8"  => "8",
                    "Data9"  => "9",
                    "Data10"  => "10",
                    "Data11"  => "11",
                    "Data12"  => "12",
                    "Data13"  => "13",
                    "Data14"  => "14",
                ),
            ),
            array(   // #1
                array(
                    "id"  => 41,
                    "Date"   => "2003-02-28 01:59:00",
                    "deltaT"  => 5.2,
                    "Data0"  => 0,
                    "Data1"  => 1,
                    "Data2"  => 2,
                    "Data3"  => 3,
                    "Data4"  => 4,
                    "Data5"  => 5,
                    "Data6"  => 6,
                    "Data7"  => 7,
                    "Data8"  => 8,
                    "Data9"  => 9,
                    "Data10"  => 10,
                    "Data11"  => 11,
                    "Data12"  => 12,
                    "Data13"  => 13,
                    "Data14"  => 14,
                ),
                array(),
                null,
                array(),
                array(
                    "group" => "default",
                    "raw" => "Array",
                    "converted" => "",
                    "id"  => "41",
                    "Date"   => "2003-02-27 19:59:00",
                    "deltaT"  => "5.2",
                    "Data0"  => "0",
                    "Data1"  => "1",
                    "Data2"  => "2",
                    "Data3"  => "3",
                    "Data4"  => "4",
                    "Data5"  => "5",
                    "Data6"  => "6",
                    "Data7"  => "7",
                    "Data8"  => "8",
                    "Data9"  => "9",
                    "Data10"  => "10",
                    "Data11"  => "11",
                    "Data12"  => "12",
                    "Data13"  => "13",
                    "Data14"  => "14",
                ),
            ),
            array(   // #2
                array(
                    "id"  => 41,
                    "Date"   => "2003-02-28 01:59:00",
                    "deltaT"  => 5.2,
                    "Data0"  => 2,
                    "Data1"  => 4,
                    "Data2"  => 6,
                    "Data3"  => 8,
                    "Data4"  => 10,
                    "Data5"  => 12,
                    "Data6"  => 14,
                    "Data7"  => 16,
                    "Data8"  => 18,
                    "Data9"  => 20,
                    "Data10"  => 22,
                    "Data11"  => 24,
                    "Data12"  => 26,
                    "Data13"  => 28,
                    "Data14"  => 30,

                ),
                array("id", "Date", "Data0", "Data1", "Data2"),
                array(),
                array(),
                array(
                    "id"  => "41",
                    "Date"   => "2003-02-27 19:59:00",
                    "Data0"  => "1",
                    "Data1"  => "2",
                    "Data2"  => "3",
                ),
            ),
            array(   // #3
                array(
                    "id"  => 41,
                    "Date"   => "2003-02-28 01:59:00",
                    "deltaT"  => 5.2,
                    "Data0"  => 2,
                    "Data1"  => 4,
                    "Data2"  => 6,
                    "Data3"  => 8,
                    "Data4"  => 10,
                    "Data5"  => 12,
                    "Data6"  => 14,
                    "Data7"  => 16,
                    "Data8"  => 18,
                    "Data9"  => 20,
                    "Data10"  => 22,
                    "Data11"  => 24,
                    "Data12"  => 26,
                    "Data13"  => 28,
                    "Data14"  => 30,

                ),
                array("id", "Date", "Data0", "Data1", "Data2"),
                array(
                    "sensors" => array(
                        "Sensors" => 1,
                        0 => array(
                            "id" => 0,
                            "dataType" => UnitsBase::TYPE_DIFF,
                        ),
                    ),
                ),
                array(),
                array(
                    "id"  => "41",
                    "Date"   => "2003-02-27 19:59:00",
                    "Data0"  => "",
                    "Data1"  => "2",
                    "Data2"  => "3",
                ),
            ),
            array(   // #4
                array(
                    "id"  => 41,
                    "Date"   => "2003-02-28 01:59:00",
                    "deltaT"  => 5.2,
                    "Data0"  => 8,
                    "Data1"  => 4,
                    "Data2"  => 6,
                    "Data3"  => 8,
                    "Data4"  => 10,
                    "Data5"  => 12,
                    "Data6"  => 14,
                    "Data7"  => 16,
                    "Data8"  => 18,
                    "Data9"  => 20,
                    "Data10"  => 22,
                    "Data11"  => 24,
                    "Data12"  => 26,
                    "Data13"  => 28,
                    "Data14"  => 30,
                ),
                array("id", "Date", "Data0", "Data1", "Data2"),
                array(
                    "sensors" => array(
                        "Sensors" => 1,
                        0 => array(
                            "id" => 0,
                            "dataType" => UnitsBase::TYPE_DIFF,
                        ),
                    ),
                ),
                array(
                    "id"  => 41,
                    "Date"   => "2003-02-28 01:54:00",
                    "deltaT"  => 5.2,
                    "Data0"  => 2,
                    "Data1"  => 2,
                    "Data2"  => 4,
                    "Data3"  => 6,
                    "Data4"  => 8,
                    "Data5"  => 10,
                    "Data6"  => 12,
                    "Data7"  => 14,
                    "Data8"  => 16,
                    "Data9"  => 18,
                    "Data10"  => 20,
                    "Data11"  => 22,
                    "Data12"  => 24,
                    "Data13"  => 26,
                    "Data14"  => 28,
                ),
                array(
                    "id"  => "41",
                    "Date"   => "2003-02-27 19:59:00",
                    "Data0"  => "3",
                    "Data1"  => "2",
                    "Data2"  => "3",
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $preload The data to preload into the class
    * @param array  $cols    The columns to use
    * @param array  $device  The device to use.  None if null
    * @param array  $prev    The previous record
    * @param int    $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataToOutput
    */
    public function testToOutput($preload, $cols, $device, $prev, $expect)
    {
        $this->o->clearData();
        $this->o->device = null;
        if (!is_null($device)) {
            $this->o->device = new DeviceContainer($device);
        }
        if (!empty($prev)) {
            // This preloads the previous data
            $this->o->fromAny($prev);
            $this->o->toOutput($cols);
            $this->o->converted = false;
        }
        $this->o->fromAny($preload);
        $ret = $this->o->toOutput($cols);
        $this->assertSame(
            $expect,
            $ret
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataOutputParams()
    {
        return array(
            array(
                array(),
                "JPGraphDatLin",
                null,
                array(),
                array(
                    "units" => array(
                        1 => "firstUnit",
                        2 => "",
                    ),
                    "unitTypes" => array(
                        1 => "firstUnit",
                        2 => "",
                    ),
                    "dateField" => "Date",
                    "fields" => array(
                        1 => array(
                            0 => "Data0",
                            1 => "Data1",
                            2 => "Data2",
                            3 => "Data3",
                            4 => "Data4",
                            5 => "Data5",
                            6 => "Data6",
                            7 => "Data7",
                            8 => "Data8",
                            9 => "Data9",
                            10 => "Data10",
                            11 => "Data11",
                            12 => "Data12",
                            13 => "Data13",
                            14 => "Data14",
                        ),
                        2 => array(),
                    )
                ),
            ),
            array(
                array(),
                "JPGraphDatLin",
                array("id", "Date", "Data0", "Data1", "Data2", "Data3"),
                array(
                    "sensors" => array(
                        "Sensors" => 4,
                        array("id" => 0),
                        array("id" => 2),
                        array("id" => 0),
                        array("id" => 2),
                    ),
                ),
                array(
                    "units" => array(
                        1 => "firstUnit",
                        2 => "anotherUnit",
                    ),
                    "unitTypes" => array(
                        1 => "firstUnit",
                        2 => "secondUnit",
                    ),
                    "dateField" => "Date",
                    "fields" => array(
                        1 => array("Data0", "Data2"),
                        2 => array("Data1", "Data3"),
                    )
                ),
            ),
            array(
                array(),
                "FlotDatLin",
                array("id", "Date", "Data0", "Data1", "Data2", "Data3"),
                array(
                    "sensors" => array(
                        "Sensors" => 4,
                        array("id" => 0),
                        array("id" => 2),
                        array("id" => 0),
                        array("id" => 2),
                    ),
                ),
                array(
                    "units" => array(
                        1 => "firstUnit",
                        2 => "anotherUnit",
                    ),
                    "unitTypes" => array(
                        1 => "firstUnit",
                        2 => "secondUnit",
                    ),
                    "dateField" => "Date",
                    "fields" => array(
                        1 => array("Data0", "Data2"),
                        2 => array("Data1", "Data3"),
                    )
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $preload The data to preload into the class
    * @param string $type    The output type
    * @param array  $cols    The columns to use
    * @param array  $device  The device to use.  None if null
    * @param int    $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataOutputParams
    */
    public function testOutputParams($preload, $type, $cols, $device, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $this->o->device = null;
        if (!is_null($device)) {
            $this->o->device = new DeviceContainer($device);
        }
        $ret = $this->o->outputParams($type, $cols);
        $this->assertSame(
            $expect,
            $ret
        );
    }
    /**
    * Data provider for testGetPeriod
    *
    * @return array
    */
    public static function dataGetPeriod()
    {
        return array(
            array(
                array(
                ),
                1292648500,
                1292648601,
                2,
                array(
                    array(
                        "group" => "default",
                        "raw" => array(),
                        "converted" => false,
                        "id"  => 2,
                        "Date"   => 1292648600,
                        "deltaT"  => "1.3",
                        "Data0"  => "1.0",
                        "Data1"  => "2.0",
                        "Data2"  => "3.0",
                        "Data3"  => "4.0",
                        "Data4"  => "5.0",
                        "Data5"  => "6.0",
                        "Data6"  => "7.0",
                        "Data7"  => "8.0",
                        "Data8"  => "9.0",
                        "Data9"  => "10.0",
                        "Data10"  => "11.0",
                        "Data11"  => "12.0",
                        "Data12"  => "13.0",
                        "Data13"  => "14.0",
                        "Data14"  => "15.0",
                    ),
                    array(
                        "group" => "default",
                        "raw" => array(),
                        "converted" => false,
                        "id"  => 2,
                        "Date"   => 1292648500,
                        "deltaT"  => "1.3",
                        "Data0"  => "1.0",
                        "Data1"  => "2.0",
                        "Data2"  => "3.0",
                        "Data3"  => "4.0",
                        "Data4"  => "5.0",
                        "Data5"  => "6.0",
                        "Data6"  => "7.0",
                        "Data7"  => "8.0",
                        "Data8"  => "9.0",
                        "Data9"  => "10.0",
                        "Data10"  => "11.0",
                        "Data11"  => "12.0",
                        "Data12"  => "13.0",
                        "Data13"  => "14.0",
                        "Data14"  => "15.0",
                    ),
                ),
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param int   $start   The first date
    * @param int   $end     The last date
    * @param mixed $key     The key to use
    * @param array $expect  The expected return
    *
    * @dataProvider dataGetPeriod
    *
    * @return null
    */
    public function testGetPeriod($preload, $start, $end, $key, $expect)
    {
        $ret = $this->o->getPeriod($start, $end, $key);
        if ($ret !== false) {
            $ret = array();
            do {
                $ret[] = $this->o->toArray();
            } while ($this->o->nextInto());
        }
        $this->assertSame($expect, $ret);
    }
}
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HistoryTableBaseTestStub extends HistoryTableBase
{
    /** @var string This is the table we should use */
    public $sqlTable = "History";
}
?>
