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
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteTables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is a required class */
require_once CODE_BASE.'tables/DevicesHistoryTable.php';
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DevicesHistoryTableTest extends HUGnetDBTableTestBase
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
            TEST_CONFIG_BASE.'files/DevicesHistoryTableTest.xml'
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataColumns()
    {
        $obj = new DevicesHistoryTable();
        return HUGnetDBTableTestBase::splitObject($obj, "sqlColumns");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIndexes()
    {
        $obj = new DevicesHistoryTable();
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
            array(new DevicesHistoryTable()),
        );
    }

    /**
    * data provider for testForceTable
    *
    * @return array
    */
    public static function dataInsertRow()
    {
        return array(
            array( // #0 inserted normally
                array(
                    "id" => "401",
                    "SaveDate" => "1280251876",
                    "SetupString" => "000000019400392102410039201443000008FFFFFF50",
                    "SensorString" => "YTo4OntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiIi"
                        ."O3M6NzoiU2Vuc29ycyI7aTo2O2k6MDthOjEyOntzOjI6ImlkIjtpOjI7"
                        ."czo0OiJ0eXBlIjtzOjE0OiJCQ1RoZXJtMjMyMjY0MCI7czo4OiJsb2Nh"
                        ."dGlvbiI7czowOiIiO3M6ODoiZGF0YVR5cGUiO3M6MzoicmF3IjtzOjU6"
                        ."ImV4dHJhIjthOjA6e31zOjE0OiJyYXdDYWxpYnJhdGlvbiI7czowOiIi"
                        ."O3M6MTI6InRpbWVDb25zdGFudCI7aToxO3M6MjoiQW0iO2k6MTAyMztz"
                        ."OjI6IlRmIjtpOjY1NTM2O3M6MToiRCI7aTo2NTUzNjtzOjE6InMiO2k6"
                        ."NjQ7czozOiJWY2MiO2k6NTt9aToxO2E6MTI6e3M6MjoiaWQiO2k6Mjtz"
                        ."OjQ6InR5cGUiO3M6MTQ6IkJDVGhlcm0yMzIyNjQwIjtzOjg6ImxvY2F0"
                        ."aW9uIjtzOjA6IiI7czo4OiJkYXRhVHlwZSI7czozOiJyYXciO3M6NToi"
                        ."ZXh0cmEiO2E6MDp7fXM6MTQ6InJhd0NhbGlicmF0aW9uIjtzOjA6IiI7"
                        ."czoxMjoidGltZUNvbnN0YW50IjtpOjE7czoyOiJBbSI7aToxMDIzO3M6"
                        ."MjoiVGYiO2k6NjU1MzY7czoxOiJEIjtpOjY1NTM2O3M6MToicyI7aTo2"
                        ."NDtzOjM6IlZjYyI7aTo1O31pOjI7YToxMjp7czoyOiJpZCI7aToyO3M6"
                        ."NDoidHlwZSI7czoxNDoiQkNUaGVybTIzMjI2NDAiO3M6ODoibG9jYXRp"
                        ."b24iO3M6MDoiIjtzOjg6ImRhdGFUeXBlIjtzOjM6InJhdyI7czo1OiJl"
                        ."eHRyYSI7YTowOnt9czoxNDoicmF3Q2FsaWJyYXRpb24iO3M6MDoiIjtz"
                        ."OjEyOiJ0aW1lQ29uc3RhbnQiO2k6MTtzOjI6IkFtIjtpOjEwMjM7czoy"
                        ."OiJUZiI7aTo2NTUzNjtzOjE6IkQiO2k6NjU1MzY7czoxOiJzIjtpOjY0"
                        ."O3M6MzoiVmNjIjtpOjU7fWk6MzthOjEyOntzOjI6ImlkIjtpOjI7czo0"
                        ."OiJ0eXBlIjtzOjE0OiJCQ1RoZXJtMjMyMjY0MCI7czo4OiJsb2NhdGlv"
                        ."biI7czowOiIiO3M6ODoiZGF0YVR5cGUiO3M6MzoicmF3IjtzOjU6ImV4"
                        ."dHJhIjthOjA6e31zOjE0OiJyYXdDYWxpYnJhdGlvbiI7czowOiIiO3M6"
                        ."MTI6InRpbWVDb25zdGFudCI7aToxO3M6MjoiQW0iO2k6MTAyMztzOjI6"
                        ."IlRmIjtpOjY1NTM2O3M6MToiRCI7aTo2NTUzNjtzOjE6InMiO2k6NjQ7"
                        ."czozOiJWY2MiO2k6NTt9aTo0O2E6MTI6e3M6MjoiaWQiO2k6MjtzOjQ6"
                        ."InR5cGUiO3M6MTQ6IkJDVGhlcm0yMzIyNjQwIjtzOjg6ImxvY2F0aW9u"
                        ."IjtzOjA6IiI7czo4OiJkYXRhVHlwZSI7czozOiJyYXciO3M6NToiZXh0"
                        ."cmEiO2E6MDp7fXM6MTQ6InJhd0NhbGlicmF0aW9uIjtzOjA6IiI7czox"
                        ."MjoidGltZUNvbnN0YW50IjtpOjE7czoyOiJBbSI7aToxMDIzO3M6Mjoi"
                        ."VGYiO2k6NjU1MzY7czoxOiJEIjtpOjY1NTM2O3M6MToicyI7aTo2NDtz"
                        ."OjM6IlZjYyI7aTo1O31pOjU7YToxMjp7czoyOiJpZCI7aToyO3M6NDoi"
                        ."dHlwZSI7czoxNDoiQkNUaGVybTIzMjI2NDAiO3M6ODoibG9jYXRpb24i"
                        ."O3M6MDoiIjtzOjg6ImRhdGFUeXBlIjtzOjM6InJhdyI7czo1OiJleHRy"
                        ."YSI7YTowOnt9czoxNDoicmF3Q2FsaWJyYXRpb24iO3M6MDoiIjtzOjEy"
                        ."OiJ0aW1lQ29uc3RhbnQiO2k6MTtzOjI6IkFtIjtpOjEwMjM7czoyOiJU"
                        ."ZiI7aTo2NTUzNjtzOjE6IkQiO2k6NjU1MzY7czoxOiJzIjtpOjY0O3M6"
                        ."MzoiVmNjIjtpOjU7fX0=",
                ),
                TEST_CONFIG_BASE.'files/DevicesHistoryTableTest2.xml',
                12,
            ),
            array( // #1 This won't be inserted because it is already there
                array(
                    "id" => "404",
                    "SaveDate" => "1280251875",
                    "SetupString" => "000000019400392102410039201443000008FFFFFF50",
                    "SensorString" => "YToxNjp7czoxNDoiUmF3Q2FsaWJyYXRpb24iO3M6M"
                        ."DoiIjtzOjc6IlNlbnNvcnMiO2k6MTA7czoxMzoiQWN0aXZlU2Vuc29"
                        ."ycyI7aTo2O3M6MTU6IlBoeXNpY2FsU2Vuc29ycyI7aTo2O3M6MTQ6I"
                        ."lZpcnR1YWxTZW5zb3JzIjtpOjQ7czoxMjoiZm9yY2VTZW5zb3JzIjt"
                        ."iOjA7aTowO2E6MTQ6e3M6MjoiaWQiO2k6NjQ7czo0OiJ0eXBlIjtzO"
                        ."jEwOiJDb250cm9sbGVyIjtzOjg6ImxvY2F0aW9uIjtzOjE2OiJIVUd"
                        ."uZXQgMSBWb2x0YWdlIjtzOjg6ImRhdGFUeXBlIjtzOjM6InJhdyI7c"
                        ."zo1OiJleHRyYSI7YToyOntpOjA7aToxODA7aToxO2k6Mjc7fXM6NTo"
                        ."idW5pdHMiO3M6MToiViI7czoxNDoicmF3Q2FsaWJyYXRpb24iO3M6M"
                        ."DoiIjtzOjEyOiJ0aW1lQ29uc3RhbnQiO2k6MTtzOjI6IkFtIjtpOjE"
                        ."wMjM7czoyOiJUZiI7aTo2NTUzNjtzOjE6IkQiO2k6NjU1MzY7czoxO"
                        ."iJzIjtpOjY0O3M6MzoiVmNjIjtpOjU7czo4OiJkZWNpbWFscyI7aTo"
                        ."0O31pOjE7YToxNDp7czoyOiJpZCI7aTo4MDtzOjQ6InR5cGUiO3M6M"
                        ."TA6IkNvbnRyb2xsZXIiO3M6ODoibG9jYXRpb24iO3M6MTY6IkhVR25"
                        ."ldCAxIEN1cnJlbnQiO3M6ODoiZGF0YVR5cGUiO3M6MzoicmF3IjtzO"
                        ."jU6ImV4dHJhIjthOjI6e2k6MDtkOjAuNTtpOjE7aTo3O31zOjU6InV"
                        ."uaXRzIjtzOjI6Im1BIjtzOjE0OiJyYXdDYWxpYnJhdGlvbiI7czowO"
                        ."iIiO3M6MTI6InRpbWVDb25zdGFudCI7aToxO3M6MjoiQW0iO2k6MTA"
                        ."yMztzOjI6IlRmIjtpOjY1NTM2O3M6MToiRCI7aTo2NTUzNjtzOjE6I"
                        ."nMiO2k6NjQ7czozOiJWY2MiO2k6NTtzOjg6ImRlY2ltYWxzIjtpOjQ"
                        ."7fWk6MjthOjE0OntzOjI6ImlkIjtpOjI7czo0OiJ0eXBlIjtzOjE0O"
                        ."iJCQ1RoZXJtMjMyMjY0MCI7czo4OiJsb2NhdGlvbiI7czoyNDoiSFV"
                        ."HbmV0IDEgRkVUIFRlbXBlcmF0dXJlIjtzOjg6ImRhdGFUeXBlIjtzO"
                        ."jM6InJhdyI7czo1OiJleHRyYSI7YToyOntpOjA7aToxMDA7aToxO2k"
                        ."6MTA7fXM6NToidW5pdHMiO3M6NzoiJiMxNzY7RiI7czoxNDoicmF3Q"
                        ."2FsaWJyYXRpb24iO3M6MDoiIjtzOjEyOiJ0aW1lQ29uc3RhbnQiO2k"
                        ."6MTtzOjI6IkFtIjtpOjEwMjM7czoyOiJUZiI7aTo2NTUzNjtzOjE6I"
                        ."kQiO2k6NjU1MzY7czoxOiJzIjtpOjY0O3M6MzoiVmNjIjtpOjU7czo"
                        ."4OiJkZWNpbWFscyI7aToyO31pOjM7YToxNDp7czoyOiJpZCI7aTo2N"
                        ."DtzOjQ6InR5cGUiO3M6MTA6IkNvbnRyb2xsZXIiO3M6ODoibG9jYXR"
                        ."pb24iO3M6MTY6IkhVR25ldCAyIFZvbHRhZ2UiO3M6ODoiZGF0YVR5c"
                        ."GUiO3M6MzoicmF3IjtzOjU6ImV4dHJhIjthOjI6e2k6MDtpOjE4MDt"
                        ."pOjE7aToyNzt9czo1OiJ1bml0cyI7czoxOiJWIjtzOjE0OiJyYXdDY"
                        ."WxpYnJhdGlvbiI7czowOiIiO3M6MTI6InRpbWVDb25zdGFudCI7aTo"
                        ."xO3M6MjoiQW0iO2k6MTAyMztzOjI6IlRmIjtpOjY1NTM2O3M6MToiR"
                        ."CI7aTo2NTUzNjtzOjE6InMiO2k6NjQ7czozOiJWY2MiO2k6NTtzOjg"
                        ."6ImRlY2ltYWxzIjtpOjQ7fWk6NDthOjE0OntzOjI6ImlkIjtpOjgwO"
                        ."3M6NDoidHlwZSI7czoxMDoiQ29udHJvbGxlciI7czo4OiJsb2NhdGl"
                        ."vbiI7czoxNjoiSFVHbmV0IDIgQ3VycmVudCI7czo4OiJkYXRhVHlwZ"
                        ."SI7czozOiJyYXciO3M6NToiZXh0cmEiO2E6Mjp7aTowO2Q6MC41O2k"
                        ."6MTtpOjc7fXM6NToidW5pdHMiO3M6MjoibUEiO3M6MTQ6InJhd0Nhb"
                        ."GlicmF0aW9uIjtzOjA6IiI7czoxMjoidGltZUNvbnN0YW50IjtpOjE"
                        ."7czoyOiJBbSI7aToxMDIzO3M6MjoiVGYiO2k6NjU1MzY7czoxOiJEI"
                        ."jtpOjY1NTM2O3M6MToicyI7aTo2NDtzOjM6IlZjYyI7aTo1O3M6ODo"
                        ."iZGVjaW1hbHMiO2k6NDt9aTo1O2E6MTQ6e3M6MjoiaWQiO2k6MjtzO"
                        ."jQ6InR5cGUiO3M6MTQ6IkJDVGhlcm0yMzIyNjQwIjtzOjg6ImxvY2F"
                        ."0aW9uIjtzOjI0OiJIVUduZXQgMiBGRVQgVGVtcGVyYXR1cmUiO3M6O"
                        ."DoiZGF0YVR5cGUiO3M6MzoicmF3IjtzOjU6ImV4dHJhIjthOjI6e2k"
                        ."6MDtpOjEwMDtpOjE7aToxMDt9czo1OiJ1bml0cyI7czo3OiImIzE3N"
                        ."jtGIjtzOjE0OiJyYXdDYWxpYnJhdGlvbiI7czowOiIiO3M6MTI6InR"
                        ."pbWVDb25zdGFudCI7aToxO3M6MjoiQW0iO2k6MTAyMztzOjI6IlRmI"
                        ."jtpOjY1NTM2O3M6MToiRCI7aTo2NTUzNjtzOjE6InMiO2k6NjQ7czo"
                        ."zOiJWY2MiO2k6NTtzOjg6ImRlY2ltYWxzIjtpOjI7fWk6NjthOjM6e"
                        ."3M6MjoiaWQiO2k6MjU0O3M6NDoidHlwZSI7czoxMToiUGxhY2Vob2x"
                        ."kZXIiO3M6ODoibG9jYXRpb24iO047fWk6NzthOjM6e3M6MjoiaWQiO"
                        ."2k6MjU0O3M6NDoidHlwZSI7czoxMToiUGxhY2Vob2xkZXIiO3M6ODo"
                        ."ibG9jYXRpb24iO047fWk6ODthOjM6e3M6MjoiaWQiO2k6MjU0O3M6N"
                        ."DoidHlwZSI7czoxMToiUGxhY2Vob2xkZXIiO3M6ODoibG9jYXRpb24"
                        ."iO047fWk6OTthOjM6e3M6MjoiaWQiO2k6MjU0O3M6NDoidHlwZSI7c"
                        ."zoxMToiUGxhY2Vob2xkZXIiO3M6ODoibG9jYXRpb24iO047fX0=",
                ),
                TEST_CONFIG_BASE.'files/DevicesHistoryTableTest.xml',
                404,
            ),
            array( // #2 This won't be inserted because it is a bad record
                array(
                    "id" => "923",
                    "SaveDate" => "1280251876",
                    "SetupString" => "00000000BC00392801410039201343000006FFFFFF50",
                    "SensorString" => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOiI"
                        ."iO3M6NzoiU2Vuc29ycyI7aTowO30=",
                ),
                TEST_CONFIG_BASE.'files/DevicesHistoryTableTest.xml',
                401,
            ),
        );
    }

    /**
    * test the forceTable routine
    *
    * @param array $preload The data to preoload into the table
    * @param mixed $expect  The expected return
    * @param int   $devId   The ID we expect to get back
    *
    * @return null
    *
    * @dataProvider dataInsertRow
    */
    public function testInsertRow($preload, $expect, $devId)
    {
        $this->o->fromAny($preload);
        $this->o->insertRow();
        $dataset = $this->createXMLDataSet($expect);
        $this->assertTablesEqual(
            $dataset->getTable('devicesHistory'),
            $this->getConnection()->createDataSet()->getTable('devicesHistory')
        );
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
                        "DriverInfo" => array(
                            "TimeConstant" => 1,
                            "PhysicalSensors"   => 6,
                            "VirtualSensors"   => 4,
                        ),
                        "id" => 404,
                        "DeviceID" => "000194",
                        "HWPartNum" => "0039-21-02-A",
                        "FWPartNum" => "0039-20-14-C",
                        "FWVersion" => "0.0.8",
                        "RawSetup" => "000000019400392102410039201443000008FFFFFF50",
                        "Driver" => "e00392100",
                        "ActiveSensors" => 10,
                        "sensors" => array(
                            "Sensors" => 10,
                            "ActiveSensors" => 6,
                            "PhysicalSensors" => 6,
                            "VirtualSensors" => 4,
                            0 => array(
                                "id" => 0x40,
                                "type" => "Controller",
                                "location" => "HUGnet 1 Voltage",
                                "extra" => array(180, 27),
                                "bound" => true,
                            ),
                            1 => array(
                                "id" => 0x50,
                                "type" => "Controller",
                                "location" => "HUGnet 1 Current",
                                "extra" => array(0.5, 7),
                                "bound" => true,
                            ),
                            2 => array(
                                "id" => 2,
                                "type" => "BCTherm2322640",
                                "location" => "HUGnet 1 FET Temperature",
                                "extra" => array(100, 10),
                                "bound" => true,
                            ),
                            3 => array(
                                "id" => 0x40,
                                "type" => "Controller",
                                "location" => "HUGnet 2 Voltage",
                                "extra" => array(180, 27),
                                "bound" => true,
                            ),
                            4 => array(
                                "id" => 0x50,
                                "type" => "Controller",
                                "location" => "HUGnet 2 Current",
                                "extra" => array(0.5, 7),
                                "bound" => true,
                            ),
                            5 => array(
                                "id" => 2,
                                "type" => "BCTherm2322640",
                                "location" => "HUGnet 2 FET Temperature",
                                "extra" => array(100, 10),
                                "bound" => true,
                            ),
                            6 => array(
                                "id" => 0xFE,
                                "type" => "Placeholder",
                                "location" => "Sensor 1",
                            ),
                            7 => array(
                                "id" => 0xFE,
                                "type" => "Placeholder",
                                "location" => "Sensor 2",
                            ),
                            8 => array(
                                "id" => 0xFE,
                                "type" => "Placeholder",
                                "location" => "Sensor 3",
                            ),
                            9 => array(
                                "id" => 0xFE,
                                "type" => "Placeholder",
                                "location" => "Sensor 4",
                            ),
                        ),
                        "params" => array(
                            "DriverInfo" => array(
                                "BoredomThreshold" => 80,
                            ),
                        ),
                    )
                ),
                array(
                    "group" => "default",
                    "id" => 404,
                    "SaveDate" => 0,
                    "SetupString" => "000000019400392102410039201443000008FFFFFF50",
                    "SensorString" => "YToxNjp7czoxNDoiUmF3Q2FsaWJyYXRpb24iO3M6MD"
                        ."oiIjtzOjc6IlNlbnNvcnMiO2k6MTA7czoxMzoiQWN0aXZlU2Vuc29yc"
                        ."yI7aToxMDtzOjE1OiJQaHlzaWNhbFNlbnNvcnMiO2k6NjtzOjE0OiJW"
                        ."aXJ0dWFsU2Vuc29ycyI7aTo0O3M6MTI6ImZvcmNlU2Vuc29ycyI7Yjo"
                        ."wO2k6MDthOjExOntzOjI6ImlkIjtpOjY0O3M6NDoidHlwZSI7czoxMD"
                        ."oiQ29udHJvbGxlciI7czo4OiJkYXRhVHlwZSI7czozOiJyYXciO3M6N"
                        ."ToiZXh0cmEiO2E6Mjp7aTowO2k6MTgwO2k6MTtpOjI3O31zOjE0OiJy"
                        ."YXdDYWxpYnJhdGlvbiI7czowOiIiO3M6MTI6InRpbWVDb25zdGFudCI"
                        ."7aToxO3M6MjoiQW0iO2k6MTAyMztzOjI6IlRmIjtpOjY1NTM2O3M6MT"
                        ."oiRCI7aTo2NTUzNjtzOjE6InMiO2k6NjQ7czozOiJWY2MiO2k6NTt9a"
                        ."ToxO2E6MTE6e3M6MjoiaWQiO2k6ODA7czo0OiJ0eXBlIjtzOjEwOiJD"
                        ."b250cm9sbGVyIjtzOjg6ImRhdGFUeXBlIjtzOjM6InJhdyI7czo1OiJ"
                        ."leHRyYSI7YToyOntpOjA7ZDowLjU7aToxO2k6Nzt9czoxNDoicmF3Q2"
                        ."FsaWJyYXRpb24iO3M6MDoiIjtzOjEyOiJ0aW1lQ29uc3RhbnQiO2k6M"
                        ."TtzOjI6IkFtIjtpOjEwMjM7czoyOiJUZiI7aTo2NTUzNjtzOjE6IkQi"
                        ."O2k6NjU1MzY7czoxOiJzIjtpOjY0O3M6MzoiVmNjIjtpOjU7fWk6Mjt"
                        ."hOjExOntzOjI6ImlkIjtpOjI7czo0OiJ0eXBlIjtzOjE0OiJCQ1RoZX"
                        ."JtMjMyMjY0MCI7czo4OiJkYXRhVHlwZSI7czozOiJyYXciO3M6NToiZ"
                        ."Xh0cmEiO2E6Mjp7aTowO2k6MTAwO2k6MTtpOjEwO31zOjE0OiJyYXdD"
                        ."YWxpYnJhdGlvbiI7czowOiIiO3M6MTI6InRpbWVDb25zdGFudCI7aTo"
                        ."xO3M6MjoiQW0iO2k6MTAyMztzOjI6IlRmIjtpOjY1NTM2O3M6MToiRC"
                        ."I7aTo2NTUzNjtzOjE6InMiO2k6NjQ7czozOiJWY2MiO2k6NTt9aTozO"
                        ."2E6MTE6e3M6MjoiaWQiO2k6NjQ7czo0OiJ0eXBlIjtzOjEwOiJDb250"
                        ."cm9sbGVyIjtzOjg6ImRhdGFUeXBlIjtzOjM6InJhdyI7czo1OiJleHR"
                        ."yYSI7YToyOntpOjA7aToxODA7aToxO2k6Mjc7fXM6MTQ6InJhd0NhbG"
                        ."licmF0aW9uIjtzOjA6IiI7czoxMjoidGltZUNvbnN0YW50IjtpOjE7c"
                        ."zoyOiJBbSI7aToxMDIzO3M6MjoiVGYiO2k6NjU1MzY7czoxOiJEIjtp"
                        ."OjY1NTM2O3M6MToicyI7aTo2NDtzOjM6IlZjYyI7aTo1O31pOjQ7YTo"
                        ."xMTp7czoyOiJpZCI7aTo4MDtzOjQ6InR5cGUiO3M6MTA6IkNvbnRyb2"
                        ."xsZXIiO3M6ODoiZGF0YVR5cGUiO3M6MzoicmF3IjtzOjU6ImV4dHJhI"
                        ."jthOjI6e2k6MDtkOjAuNTtpOjE7aTo3O31zOjE0OiJyYXdDYWxpYnJh"
                        ."dGlvbiI7czowOiIiO3M6MTI6InRpbWVDb25zdGFudCI7aToxO3M6Mjo"
                        ."iQW0iO2k6MTAyMztzOjI6IlRmIjtpOjY1NTM2O3M6MToiRCI7aTo2NT"
                        ."UzNjtzOjE6InMiO2k6NjQ7czozOiJWY2MiO2k6NTt9aTo1O2E6MTE6e"
                        ."3M6MjoiaWQiO2k6MjtzOjQ6InR5cGUiO3M6MTQ6IkJDVGhlcm0yMzIy"
                        ."NjQwIjtzOjg6ImRhdGFUeXBlIjtzOjM6InJhdyI7czo1OiJleHRyYSI"
                        ."7YToyOntpOjA7aToxMDA7aToxO2k6MTA7fXM6MTQ6InJhd0NhbGlicm"
                        ."F0aW9uIjtzOjA6IiI7czoxMjoidGltZUNvbnN0YW50IjtpOjE7czoyO"
                        ."iJBbSI7aToxMDIzO3M6MjoiVGYiO2k6NjU1MzY7czoxOiJEIjtpOjY1"
                        ."NTM2O3M6MToicyI7aTo2NDtzOjM6IlZjYyI7aTo1O31pOjY7TjtpOjc"
                        ."7TjtpOjg7TjtpOjk7Tjt9"
                ),
            ),
        );
    }

    /**
    * data provider for testForceTable
    *
    * @return array
    */
    public static function dataDeviceFactory()
    {
        return array(
            array(
                404,
                1280251875,
                array(
                    "DriverInfo" => array(
                        "TimeConstant" => 1,
                        "PhysicalSensors"   => 6,
                        "VirtualSensors"   => 4,
                    ),
                    "id" => 404,
                    "DeviceID" => "000194",
                    "HWPartNum" => "0039-21-02-A",
                    "FWPartNum" => "0039-20-14-C",
                    "FWVersion" => "0.0.8",
                    "RawSetup" => "000000019400392102410039201443000008FFFFFF50",
                    "Driver" => "e00392100",
                    "ActiveSensors" => 10,
                    "sensors" => array(
                        "Sensors" => 10,
                        "ActiveSensors" => 6,
                        "PhysicalSensors" => 6,
                        "VirtualSensors" => 4,
                        0 => array(
                            "id" => 0x40,
                            "type" => "Controller",
                            "location" => "HUGnet 1 Voltage",
                            "extra" => array(180, 27),
                            "bound" => true,
                        ),
                        1 => array(
                            "id" => 0x50,
                            "type" => "Controller",
                            "location" => "HUGnet 1 Current",
                            "extra" => array(0.5, 7),
                            "bound" => true,
                        ),
                        2 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "location" => "HUGnet 1 FET Temperature",
                            "extra" => array(100, 10),
                            "bound" => true,
                        ),
                        3 => array(
                            "id" => 0x40,
                            "type" => "Controller",
                            "location" => "HUGnet 2 Voltage",
                            "extra" => array(180, 27),
                            "bound" => true,
                        ),
                        4 => array(
                            "id" => 0x50,
                            "type" => "Controller",
                            "location" => "HUGnet 2 Current",
                            "extra" => array(0.5, 7),
                            "bound" => true,
                        ),
                        5 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "location" => "HUGnet 2 FET Temperature",
                            "extra" => array(100, 10),
                            "bound" => true,
                        ),
                        6 => array(
                            "id" => 0xFE,
                            "type" => "Placeholder",
                            "location" => null,
                        ),
                        7 => array(
                            "id" => 0xFE,
                            "type" => "Placeholder",
                            "location" => null,
                        ),
                        8 => array(
                            "id" => 0xFE,
                            "type" => "Placeholder",
                            "location" => null,
                        ),
                        9 => array(
                            "id" => 0xFE,
                            "type" => "Placeholder",
                            "location" => null,
                        ),
                    ),
                    "params" => array(
                        "DriverInfo" => array(
                            "BoredomThreshold" => 80,
                        ),
                    ),
                ),
            ),
            array(
                404,
                0,
                array(
                    "DriverInfo" => array(
                        "TimeConstant" => 1,
                        "PhysicalSensors"   => 6,
                        "VirtualSensors"   => 4,
                    ),
                    "id" => 404,
                    "DeviceID" => "000194",
                    "HWPartNum" => "0039-21-02-A",
                    "FWPartNum" => "0039-20-14-C",
                    "FWVersion" => "0.0.8",
                    "RawSetup" => "000000019400392102410039201443000008FFFFFF50",
                    "Driver" => "e00392100",
                    "ActiveSensors" => 10,
                    "sensors" => array(
                        "Sensors" => 10,
                        "ActiveSensors" => 6,
                        "PhysicalSensors" => 6,
                        "VirtualSensors" => 4,
                        0 => array(
                            "id" => 0x40,
                            "type" => "Controller",
                            "location" => "HUGnet 1 Voltage",
                            "extra" => array(180, 27),
                            "bound" => true,
                        ),
                        1 => array(
                            "id" => 0x50,
                            "type" => "Controller",
                            "location" => "HUGnet 1 Current",
                            "extra" => array(0.5, 7),
                            "bound" => true,
                        ),
                        2 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "location" => "HUGnet 1 FET Temperature",
                            "extra" => array(100, 10),
                            "bound" => true,
                        ),
                        3 => array(
                            "id" => 0x40,
                            "type" => "Controller",
                            "location" => "HUGnet 2 Voltage",
                            "extra" => array(180, 27),
                            "bound" => true,
                        ),
                        4 => array(
                            "id" => 0x50,
                            "type" => "Controller",
                            "location" => "HUGnet 2 Current",
                            "extra" => array(0.5, 7),
                            "bound" => true,
                        ),
                        5 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "location" => "HUGnet 2 FET Temperature",
                            "extra" => array(100, 10),
                            "bound" => true,
                        ),
                        6 => array(
                            "id" => 0xFE,
                            "type" => "Placeholder",
                            "location" => null,
                        ),
                        7 => array(
                            "id" => 0xFE,
                            "type" => "Placeholder",
                            "location" => null,
                        ),
                        8 => array(
                            "id" => 0xFE,
                            "type" => "Placeholder",
                            "location" => null,
                        ),
                        9 => array(
                            "id" => 0xFE,
                            "type" => "Placeholder",
                            "location" => null,
                        ),
                    ),
                    "params" => array(
                        "DriverInfo" => array(
                            "BoredomThreshold" => 80,
                        ),
                    ),
                ),
            ),
        );
    }

    /**
    * test the forceTable routine
    *
    * @param int   $devId  The ID of the device to use
    * @param int   $date   The date to use
    * @param mixed $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDeviceFactory
    */
    public function testDeviceFactory($devId, $date, $expect)
    {
        $dev = &DevicesHistoryTable::deviceFactory($devId, $date);
        $this->assertTrue(
            is_a($dev, "DeviceContainer"), "Not a DeviceContainer"
        );
        $this->assertSame($expect, $dev->toArray(false), "Wrong data");
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
    /**
    * data provider for testCheckRecord
    *
    * @return array
    */
    public static function dataCheckRecord()
    {
        return array(
            array( // #0 a good record
                array(
                    "id" => "404",
                    "group" => "default",
                    "SaveDate" => "1280251875",
                    "SetupString" => "000000019400392102410039201443000008FFFFFF50",
                    "SensorString" => (string) new DeviceSensorsContainer(
                        array(
                            "Sensors" => 20,
                            "PhysicalSensors" => 16,
                            "VirtualSensors" => 4,
                            "ActiveSensors" => 10,
                            "forceSensors" => true,
                        ),
                        new DeviceContainer()
                    ),
                ),
                true,
            ),
            array( // #1 a record with no sensors
                array(
                    "id" => "404",
                    "group" => "default",
                    "SaveDate" => "1280251875",
                    "SetupString" => "000000019400392102410039201443000008FFFFFF50",
                    "SensorString" => (string) new DeviceSensorsContainer(
                        array(
                            "forceSensors" => true,
                        ),
                        new DeviceContainer()
                    ),
                ),
                false,
            ),
            array( // #2 a bad record from the field
                array(
                    "id" => "404",
                    "group" => "default",
                    "SaveDate" => "1280251875",
                    "SetupString" => "00000000BC00392801410039201343000006FFFFFF50",
                    "SensorString" => "YToyOntzOjE0OiJSYXdDYWxpYnJhdGlvbiI7czowOi"
                        ."IiO3M6NzoiU2Vuc29ycyI7aTowO30=",
                ),
                false,
            ),
            array( // #3 a bad setup string
                array(
                    "id" => "404",
                    "group" => "default",
                    "SaveDate" => "1280251875",
                    "SetupString" => "000000019410392102410039201443000008FFFFFF50",
                    "SensorString" => (string) new DeviceSensorsContainer(
                        array(
                            "Sensors" => 20,
                            "PhysicalSensors" => 16,
                            "VirtualSensors" => 4,
                            "ActiveSensors" => 10,
                            "forceSensors" => true,
                        ),
                        new DeviceContainer()
                    ),
                ),
                false,
            ),
            array( // #4 another bad setup string
                array(
                    "id" => "404",
                    "group" => "default",
                    "SaveDate" => "1280251875",
                    "SetupString" => "000000019400392102411039201443000008FFFFFF50",
                    "SensorString" => (string) new DeviceSensorsContainer(
                        array(
                            "Sensors" => 20,
                            "PhysicalSensors" => 16,
                            "VirtualSensors" => 4,
                            "ActiveSensors" => 10,
                            "forceSensors" => true,
                        ),
                        new DeviceContainer()
                    ),
                ),
                false,
            ),
        );
    }

    /**
    * test the forceTable routine
    *
    * @param array $preload The device to use
    * @param mixed $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataCheckRecord
    */
    public function testCheckRecord($preload, $expect)
    {
        $this->o->fromAny($preload);
        $this->assertSame($expect, $this->o->checkRecord());
    }

}

?>
