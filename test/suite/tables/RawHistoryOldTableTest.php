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
 * @version    0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is a required class */
require_once CODE_BASE.'tables/RawHistoryOldTable.php';
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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class RawHistoryOldTableTest extends HUGnetDBTableTestBase
{

    static $config = array(
        "servers" => array(
            array(
                "driver" => "sqlite",
                "file" => ":memory:",
                "group" => "default",
            ),
            array(
                "driver" => "sqlite",
                "file" => ":memory:",
                "group" => "old",
            ),
            array(
                "driver" => "sqlite",
                "file" => ":memory:",
                "group" => "other",
            ),
        ),
    );
    static $createQuery = "CREATE TABLE `history_raw` (
        `HistoryRawKey` int(11) NOT NULL,
        `DeviceKey` int(11) NOT NULL DEFAULT '0',
        `Date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
        `RawData` varchar(255) NOT NULL DEFAULT '',
        `ActiveSensors` tinyint(4) NOT NULL DEFAULT '0',
        `Driver` varchar(32) NOT NULL DEFAULT 'eDEFAULT',
        `RawSetup` varchar(128) NOT NULL,
        `RawCalibration` varchar(255) NOT NULL,
        `Status` varchar(16) NOT NULL DEFAULT 'GOOD',
        `ReplyTime` float NOT NULL DEFAULT '0',
        `sendCommand` char(2) NOT NULL DEFAULT '',
        `UTCOffset` tinyint(4) NOT NULL DEFAULT '0'
        )";

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
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig(self::$config);
        $this->pdo = &$this->config->servers->getPDO("old");
        $this->pdo->query(self::$createQuery);
        $this->o = new RawHistoryOldTable();
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
            TEST_CONFIG_BASE.'files/RawHistoryOldTableTest.xml'
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataColumns()
    {
        $config = ConfigContainer::singleton();
        $config->forceConfig(self::$config);
        $pdo = &$config->servers->getPDO("old");
        $pdo->query(self::$createQuery);
        $o = new RawHistoryOldTable();
        return HUGnetDBTableTestBase::splitObject($o, "sqlColumns");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIndexes()
    {
        $config = ConfigContainer::singleton();
        $config->forceConfig(self::$config);
        $pdo = &$config->servers->getPDO("old");
        $pdo->query(self::$createQuery);
        $o = new RawHistoryOldTable();
        return HUGnetDBTableTestBase::splitObject($o, "sqlIndexes");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataVars()
    {
        $config = ConfigContainer::singleton();
        $config->forceConfig(self::$config);
        $pdo = &$config->servers->getPDO("old");
        $pdo->query(self::$createQuery);
        return array(
            array(new RawHistoryOldTable()),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $obj The object to work with
    *
    * @return null
    *
    * @dataProvider dataVars
    */
    public function testGroupVar($obj)
    {
        $default = $this->readAttribute($obj, "default");
        $this->assertSame(
            "old",
            $default["group"],
            '$obj->default["group"] must be set to "old"'
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
            array("Date", 1272202943, "2010-04-25 08:42:23"),
            array("Date", "2010-04-25", "2010-04-25 00:00:00"),
            array("Date", "This is not a date", "1970-01-01 00:00:00"),
            array("DeviceKey", 71, 71),
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
    * Data provider for testToRaw
    *
    * @return array
    */
    public static function dataToRaw()
    {
        return array(
            array(  // #0 Nothing given
                array(
                ),
                array(
                ),
                "default",
                false,
            ),
            array(  // #1 Normal record
                array(
                    "id" => 56,
                    "SaveDate" => 1280251875,
                    "SetupString" => "00000000380039120041003911034200000140000000",
                    "SensorString" => "YToxOTp7czoxNDoiUmF3Q2FsaWJyYXRpb24iO3M6MD"
                        ."oiIjtzOjc6IlNlbnNvcnMiO2k6MTM7czoxMzoiQWN0aXZlU2Vuc29yc"
                        ."yI7aTo5O3M6MTU6IlBoeXNpY2FsU2Vuc29ycyI7aTo5O3M6MTQ6IlZp"
                        ."cnR1YWxTZW5zb3JzIjtpOjQ7czoxMjoiZm9yY2VTZW5zb3JzIjtiOjA"
                        ."7aTowO2E6MTY6e3M6MjoiaWQiO2k6MDtzOjQ6InR5cGUiO3M6MTQ6Ik"
                        ."JDVGhlcm0yMzIyNjQwIjtzOjg6ImxvY2F0aW9uIjtzOjA6IiI7czo4O"
                        ."iJkYXRhVHlwZSI7czozOiJyYXciO3M6NToiZXh0cmEiO2E6MDp7fXM6N"
                        ."ToidW5pdHMiO3M6NzoiJiMxNzY7RiI7czo1OiJib3VuZCI7YjowO3M6"
                        ."MTQ6InJhd0NhbGlicmF0aW9uIjtzOjA6IiI7czoxMjoidGltZUNvbnN"
                        ."0YW50IjtpOjE7czoyOiJBbSI7aToxMDIzO3M6MjoiVGYiO2k6NjU1Mz"
                        ."Y7czoxOiJEIjtpOjY1NTM2O3M6MToicyI7aTo2NDtzOjM6IlZjYyI7aT"
                        ."o1O3M6NjoiZmlsdGVyIjthOjA6e31zOjg6ImRlY2ltYWxzIjtpOjI7f"
                        ."Wk6MTthOjE2OntzOjI6ImlkIjtpOjA7czo0OiJ0eXBlIjtzOjE0OiJCQ"
                        ."1RoZXJtMjMyMjY0MCI7czo4OiJsb2NhdGlvbiI7czowOiIiO3M6ODoi"
                        ."ZGF0YVR5cGUiO3M6MzoicmF3IjtzOjU6ImV4dHJhIjthOjA6e31zOjU6"
                        ."InVuaXRzIjtzOjc6IiYjMTc2O0YiO3M6NToiYm91bmQiO2I6MDtzOjE0"
                        ."OiJyYXdDYWxpYnJhdGlvbiI7czowOiIiO3M6MTI6InRpbWVDb25zdGFu"
                        ."dCI7aToxO3M6MjoiQW0iO2k6MTAyMztzOjI6IlRmIjtpOjY1NTM2O3M"
                        ."6MToiRCI7aTo2NTUzNjtzOjE6InMiO2k6NjQ7czozOiJWY2MiO2k6NT"
                        ."tzOjY6ImZpbHRlciI7YTowOnt9czo4OiJkZWNpbWFscyI7aToyO31pO"
                        ."jI7YToxNjp7czoyOiJpZCI7aTowO3M6NDoidHlwZSI7czoxNDoiQkNU"
                        ."aGVybTIzMjI2NDAiO3M6ODoibG9jYXRpb24iO3M6MDoiIjtzOjg6ImR"
                        ."hdGFUeXBlIjtzOjM6InJhdyI7czo1OiJleHRyYSI7YTowOnt9czo1Oi"
                        ."J1bml0cyI7czo3OiImIzE3NjtGIjtzOjU6ImJvdW5kIjtiOjA7czoxN"
                        ."DoicmF3Q2FsaWJyYXRpb24iO3M6MDoiIjtzOjEyOiJ0aW1lQ29uc3Rh"
                        ."bnQiO2k6MTtzOjI6IkFtIjtpOjEwMjM7czoyOiJUZiI7aTo2NTUzNjtz"
                        ."OjE6IkQiO2k6NjU1MzY7czoxOiJzIjtpOjY0O3M6MzoiVmNjIjtpOjU7"
                        ."czo2OiJmaWx0ZXIiO2E6MDp7fXM6ODoiZGVjaW1hbHMiO2k6Mjt9aTo"
                        ."zO2E6MTY6e3M6MjoiaWQiO2k6MDtzOjQ6InR5cGUiO3M6MTQ6IkJDV"
                        ."Ghlcm0yMzIyNjQwIjtzOjg6ImxvY2F0aW9uIjtzOjA6IiI7czo4OiJ"
                        ."kYXRhVHlwZSI7czozOiJyYXciO3M6NToiZXh0cmEiO2E6MDp7fXM6NT"
                        ."oidW5pdHMiO3M6NzoiJiMxNzY7RiI7czo1OiJib3VuZCI7YjowO3M6M"
                        ."TQ6InJhd0NhbGlicmF0aW9uIjtzOjA6IiI7czoxMjoidGltZUNvbnN0"
                        ."YW50IjtpOjE7czoyOiJBbSI7aToxMDIzO3M6MjoiVGYiO2k6NjU1MzY"
                        ."7czoxOiJEIjtpOjY1NTM2O3M6MToicyI7aTo2NDtzOjM6IlZjYyI7a"
                        ."To1O3M6NjoiZmlsdGVyIjthOjA6e31zOjg6ImRlY2ltYWxzIjtpOjI"
                        ."7fWk6NDthOjE2OntzOjI6ImlkIjtpOjA7czo0OiJ0eXBlIjtzOjE0Oi"
                        ."JCQ1RoZXJtMjMyMjY0MCI7czo4OiJsb2NhdGlvbiI7czowOiIiO3M6O"
                        ."DoiZGF0YVR5cGUiO3M6MzoicmF3IjtzOjU6ImV4dHJhIjthOjA6e31"
                        ."zOjU6InVuaXRzIjtzOjc6IiYjMTc2O0YiO3M6NToiYm91bmQiO2I6M"
                        ."DtzOjE0OiJyYXdDYWxpYnJhdGlvbiI7czowOiIiO3M6MTI6InRpbWV"
                        ."Db25zdGFudCI7aToxO3M6MjoiQW0iO2k6MTAyMztzOjI6IlRmIjtpO"
                        ."jY1NTM2O3M6MToiRCI7aTo2NTUzNjtzOjE6InMiO2k6NjQ7czozOiJ"
                        ."WY2MiO2k6NTtzOjY6ImZpbHRlciI7YTowOnt9czo4OiJkZWNpbWFsc"
                        ."yI7aToyO31pOjU7YToxNjp7czoyOiJpZCI7aTowO3M6NDoidHlwZSI"
                        ."7czoxNDoiQkNUaGVybTIzMjI2NDAiO3M6ODoibG9jYXRpb24iO3M6M"
                        ."DoiIjtzOjg6ImRhdGFUeXBlIjtzOjM6InJhdyI7czo1OiJleHRyYSI"
                        ."7YTowOnt9czo1OiJ1bml0cyI7czo3OiImIzE3NjtGIjtzOjU6ImJvd"
                        ."W5kIjtiOjA7czoxNDoicmF3Q2FsaWJyYXRpb24iO3M6MDoiIjtzOjE"
                        ."yOiJ0aW1lQ29uc3RhbnQiO2k6MTtzOjI6IkFtIjtpOjEwMjM7czoyO"
                        ."iJUZiI7aTo2NTUzNjtzOjE6IkQiO2k6NjU1MzY7czoxOiJzIjtpOjY"
                        ."0O3M6MzoiVmNjIjtpOjU7czo2OiJmaWx0ZXIiO2E6MDp7fXM6ODoiZ"
                        ."GVjaW1hbHMiO2k6Mjt9aTo2O2E6MTY6e3M6MjoiaWQiO2k6MDtzOjQ6"
                        ."InR5cGUiO3M6MTQ6IkJDVGhlcm0yMzIyNjQwIjtzOjg6ImxvY2F0aW"
                        ."9uIjtzOjA6IiI7czo4OiJkYXRhVHlwZSI7czozOiJyYXciO3M6NToi"
                        ."ZXh0cmEiO2E6MDp7fXM6NToidW5pdHMiO3M6NzoiJiMxNzY7RiI7cz"
                        ."o1OiJib3VuZCI7YjowO3M6MTQ6InJhd0NhbGlicmF0aW9uIjtzOjA6"
                        ."IiI7czoxMjoidGltZUNvbnN0YW50IjtpOjE7czoyOiJBbSI7aToxMD"
                        ."IzO3M6MjoiVGYiO2k6NjU1MzY7czoxOiJEIjtpOjY1NTM2O3M6MTo"
                        ."icyI7aTo2NDtzOjM6IlZjYyI7aTo1O3M6NjoiZmlsdGVyIjthOjA6e"
                        ."31zOjg6ImRlY2ltYWxzIjtpOjI7fWk6NzthOjE2OntzOjI6ImlkIj"
                        ."tpOjA7czo0OiJ0eXBlIjtzOjE0OiJCQ1RoZXJtMjMyMjY0MCI7czo"
                        ."4OiJsb2NhdGlvbiI7czowOiIiO3M6ODoiZGF0YVR5cGUiO3M6Mzoic"
                        ."mF3IjtzOjU6ImV4dHJhIjthOjA6e31zOjU6InVuaXRzIjtzOjc6IiY"
                        ."jMTc2O0YiO3M6NToiYm91bmQiO2I6MDtzOjE0OiJyYXdDYWxpYnJhd"
                        ."GlvbiI7czowOiIiO3M6MTI6InRpbWVDb25zdGFudCI7aToxO3M6Mj"
                        ."oiQW0iO2k6MTAyMztzOjI6IlRmIjtpOjY1NTM2O3M6MToiRCI7aTo"
                        ."2NTUzNjtzOjE6InMiO2k6NjQ7czozOiJWY2MiO2k6NTtzOjY6ImZp"
                        ."bHRlciI7YTowOnt9czo4OiJkZWNpbWFscyI7aToyO31pOjg7YToxN"
                        ."jp7czoyOiJpZCI7aTowO3M6NDoidHlwZSI7czoxNDoiQkNUaGVyb"
                        ."TIzMjI2NDAiO3M6ODoibG9jYXRpb24iO3M6MDoiIjtzOjg6ImRhdG"
                        ."FUeXBlIjtzOjM6InJhdyI7czo1OiJleHRyYSI7YTowOnt9czo1OiJ"
                        ."1bml0cyI7czo3OiImIzE3NjtGIjtzOjU6ImJvdW5kIjtiOjA7czox"
                        ."NDoicmF3Q2FsaWJyYXRpb24iO3M6MDoiIjtzOjEyOiJ0aW1lQ29uc"
                        ."3RhbnQiO2k6MTtzOjI6IkFtIjtpOjEwMjM7czoyOiJUZiI7aTo2NTU"
                        ."zNjtzOjE6IkQiO2k6NjU1MzY7czoxOiJzIjtpOjY0O3M6MzoiVmNj"
                        ."IjtpOjU7czo2OiJmaWx0ZXIiO2E6MDp7fXM6ODoiZGVjaW1hbHMiO"
                        ."2k6Mjt9aTo5O2E6Mzp7czoyOiJpZCI7aToyNTQ7czo0OiJ0eXBlIj"
                        ."tzOjExOiJQbGFjZWhvbGRlciI7czo4OiJsb2NhdGlvbiI7Tjt9aTo"
                        ."xMDthOjM6e3M6MjoiaWQiO2k6MjU0O3M6NDoidHlwZSI7czoxMToi"
                        ."UGxhY2Vob2xkZXIiO3M6ODoibG9jYXRpb24iO047fWk6MTE7YTozO"
                        ."ntzOjI6ImlkIjtpOjI1NDtzOjQ6InR5cGUiO3M6MTE6IlBsYWNlaG"
                        ."9sZGVyIjtzOjg6ImxvY2F0aW9uIjtOO31pOjEyO2E6Mzp7czoyOiJ"
                        ."pZCI7aToyNTQ7czo0OiJ0eXBlIjtzOjExOiJQbGFjZWhvbGRlciI7c"
                        ."zo4OiJsb2NhdGlvbiI7Tjt9fQ==",
                ),
                array(
                    'HistoryRawKey' => 10020,
                    'DeviceKey' => 24,
                    'Date' => '2003-11-06 18:30:10',
                    'RawData' => '5a004021690eb59c0ee743075f1f07531c07e01c079f1d'
                        .'07a32607fe2f070070',
                    'ActiveSensors' => 9,
                    'Driver' => 'e00391200',
                    'RawSetup' => '0000000038003912004100391103420000014000000000'
                        .'00000000000000',
                    'RawCalibration' => '',
                    'Status' => 'GOOD',
                    'ReplyTime' => 0,
                    'sendCommand' => 55,
                    'UTCOffset' => 0
                ),
                "other",
                array(
                    'id' => 56,
                    'Date' => 1068143410,
                    'packet' => array(
                        'To' => '000038',
                        'From' => '000000',
                        'Date' => 1068143410,
                        'Command' => '55',
                        'Length' => 0,
                        'Time' => 1068143410,
                        'Data' => array(),
                        'RawData' => '',
                        'Type' => 'SENSORREAD',
                        'Reply' => array(
                            'To' => '000000',
                            'From' => '000038',
                            'Date' => 1068143410,
                            'Command' => '01',
                            'Length' => 32,
                            'Time' => 1068143410,
                            'Data' => array(
                                0 => 90,
                                1 => 0,
                                2 => 64,
                                3 => 33,
                                4 => 105,
                                5 => 14,
                                6 => 181,
                                7 => 156,
                                8 => 14,
                                9 => 231,
                                10 => 67,
                                11 => 7,
                                12 => 95,
                                13 => 31,
                                14 => 7,
                                15 => 83,
                                16 => 28,
                                17 => 7,
                                18 => 224,
                                19 => 28,
                                20 => 7,
                                21 => 159,
                                22 => 29,
                                23 => 7,
                                24 => 163,
                                25 => 38,
                                26 => 7,
                                27 => 254,
                                28 => 47,
                                29 => 7,
                                30 => 0,
                                31 => 112,
                            ),
                            'RawData' => '5a004021690eb59c0ee743075f1f07531c07e01'
                                .'c079f1d07a32607fe2f070070',
                            'Type' => 'REPLY',
                            'Reply' => null,
                            'Checksum' => '94',
                            'CalcChecksum' => '94',
                        ),
                        'Checksum' => '6D',
                        'CalcChecksum' => '6D',
                    ),
                    'devicesHistoryDate' => 1068143410,
                    'command' => 55,
                ),
            ),
            array(  // #2 id too high
                array(
                    "id" => 56,
                    "SaveDate" => 1280251875,
                    "SetupString" => "00000000380039120041003911034200000140000000",
                    "SensorString" => "YToxOTp7czoxNDoiUmF3Q2FsaWJyYXRpb24iO3M6MD"
                        ."oiIjtzOjc6IlNlbnNvcnMiO2k6MTM7czoxMzoiQWN0aXZlU2Vuc29yc"
                        ."yI7aTo5O3M6MTU6IlBoeXNpY2FsU2Vuc29ycyI7aTo5O3M6MTQ6IlZp"
                        ."cnR1YWxTZW5zb3JzIjtpOjQ7czoxMjoiZm9yY2VTZW5zb3JzIjtiOjA"
                        ."7aTowO2E6MTY6e3M6MjoiaWQiO2k6MDtzOjQ6InR5cGUiO3M6MTQ6Ik"
                        ."JDVGhlcm0yMzIyNjQwIjtzOjg6ImxvY2F0aW9uIjtzOjA6IiI7czo4O"
                        ."iJkYXRhVHlwZSI7czozOiJyYXciO3M6NToiZXh0cmEiO2E6MDp7fXM6N"
                        ."ToidW5pdHMiO3M6NzoiJiMxNzY7RiI7czo1OiJib3VuZCI7YjowO3M6"
                        ."MTQ6InJhd0NhbGlicmF0aW9uIjtzOjA6IiI7czoxMjoidGltZUNvbnN"
                        ."0YW50IjtpOjE7czoyOiJBbSI7aToxMDIzO3M6MjoiVGYiO2k6NjU1Mz"
                        ."Y7czoxOiJEIjtpOjY1NTM2O3M6MToicyI7aTo2NDtzOjM6IlZjYyI7aT"
                        ."o1O3M6NjoiZmlsdGVyIjthOjA6e31zOjg6ImRlY2ltYWxzIjtpOjI7f"
                        ."Wk6MTthOjE2OntzOjI6ImlkIjtpOjA7czo0OiJ0eXBlIjtzOjE0OiJCQ"
                        ."1RoZXJtMjMyMjY0MCI7czo4OiJsb2NhdGlvbiI7czowOiIiO3M6ODoi"
                        ."ZGF0YVR5cGUiO3M6MzoicmF3IjtzOjU6ImV4dHJhIjthOjA6e31zOjU6"
                        ."InVuaXRzIjtzOjc6IiYjMTc2O0YiO3M6NToiYm91bmQiO2I6MDtzOjE0"
                        ."OiJyYXdDYWxpYnJhdGlvbiI7czowOiIiO3M6MTI6InRpbWVDb25zdGFu"
                        ."dCI7aToxO3M6MjoiQW0iO2k6MTAyMztzOjI6IlRmIjtpOjY1NTM2O3M"
                        ."6MToiRCI7aTo2NTUzNjtzOjE6InMiO2k6NjQ7czozOiJWY2MiO2k6NT"
                        ."tzOjY6ImZpbHRlciI7YTowOnt9czo4OiJkZWNpbWFscyI7aToyO31pO"
                        ."jI7YToxNjp7czoyOiJpZCI7aTowO3M6NDoidHlwZSI7czoxNDoiQkNU"
                        ."aGVybTIzMjI2NDAiO3M6ODoibG9jYXRpb24iO3M6MDoiIjtzOjg6ImR"
                        ."hdGFUeXBlIjtzOjM6InJhdyI7czo1OiJleHRyYSI7YTowOnt9czo1Oi"
                        ."J1bml0cyI7czo3OiImIzE3NjtGIjtzOjU6ImJvdW5kIjtiOjA7czoxN"
                        ."DoicmF3Q2FsaWJyYXRpb24iO3M6MDoiIjtzOjEyOiJ0aW1lQ29uc3Rh"
                        ."bnQiO2k6MTtzOjI6IkFtIjtpOjEwMjM7czoyOiJUZiI7aTo2NTUzNjtz"
                        ."OjE6IkQiO2k6NjU1MzY7czoxOiJzIjtpOjY0O3M6MzoiVmNjIjtpOjU7"
                        ."czo2OiJmaWx0ZXIiO2E6MDp7fXM6ODoiZGVjaW1hbHMiO2k6Mjt9aTo"
                        ."zO2E6MTY6e3M6MjoiaWQiO2k6MDtzOjQ6InR5cGUiO3M6MTQ6IkJDV"
                        ."Ghlcm0yMzIyNjQwIjtzOjg6ImxvY2F0aW9uIjtzOjA6IiI7czo4OiJ"
                        ."kYXRhVHlwZSI7czozOiJyYXciO3M6NToiZXh0cmEiO2E6MDp7fXM6NT"
                        ."oidW5pdHMiO3M6NzoiJiMxNzY7RiI7czo1OiJib3VuZCI7YjowO3M6M"
                        ."TQ6InJhd0NhbGlicmF0aW9uIjtzOjA6IiI7czoxMjoidGltZUNvbnN0"
                        ."YW50IjtpOjE7czoyOiJBbSI7aToxMDIzO3M6MjoiVGYiO2k6NjU1MzY"
                        ."7czoxOiJEIjtpOjY1NTM2O3M6MToicyI7aTo2NDtzOjM6IlZjYyI7a"
                        ."To1O3M6NjoiZmlsdGVyIjthOjA6e31zOjg6ImRlY2ltYWxzIjtpOjI"
                        ."7fWk6NDthOjE2OntzOjI6ImlkIjtpOjA7czo0OiJ0eXBlIjtzOjE0Oi"
                        ."JCQ1RoZXJtMjMyMjY0MCI7czo4OiJsb2NhdGlvbiI7czowOiIiO3M6O"
                        ."DoiZGF0YVR5cGUiO3M6MzoicmF3IjtzOjU6ImV4dHJhIjthOjA6e31"
                        ."zOjU6InVuaXRzIjtzOjc6IiYjMTc2O0YiO3M6NToiYm91bmQiO2I6M"
                        ."DtzOjE0OiJyYXdDYWxpYnJhdGlvbiI7czowOiIiO3M6MTI6InRpbWV"
                        ."Db25zdGFudCI7aToxO3M6MjoiQW0iO2k6MTAyMztzOjI6IlRmIjtpO"
                        ."jY1NTM2O3M6MToiRCI7aTo2NTUzNjtzOjE6InMiO2k6NjQ7czozOiJ"
                        ."WY2MiO2k6NTtzOjY6ImZpbHRlciI7YTowOnt9czo4OiJkZWNpbWFsc"
                        ."yI7aToyO31pOjU7YToxNjp7czoyOiJpZCI7aTowO3M6NDoidHlwZSI"
                        ."7czoxNDoiQkNUaGVybTIzMjI2NDAiO3M6ODoibG9jYXRpb24iO3M6M"
                        ."DoiIjtzOjg6ImRhdGFUeXBlIjtzOjM6InJhdyI7czo1OiJleHRyYSI"
                        ."7YTowOnt9czo1OiJ1bml0cyI7czo3OiImIzE3NjtGIjtzOjU6ImJvd"
                        ."W5kIjtiOjA7czoxNDoicmF3Q2FsaWJyYXRpb24iO3M6MDoiIjtzOjE"
                        ."yOiJ0aW1lQ29uc3RhbnQiO2k6MTtzOjI6IkFtIjtpOjEwMjM7czoyO"
                        ."iJUZiI7aTo2NTUzNjtzOjE6IkQiO2k6NjU1MzY7czoxOiJzIjtpOjY"
                        ."0O3M6MzoiVmNjIjtpOjU7czo2OiJmaWx0ZXIiO2E6MDp7fXM6ODoiZ"
                        ."GVjaW1hbHMiO2k6Mjt9aTo2O2E6MTY6e3M6MjoiaWQiO2k6MDtzOjQ6"
                        ."InR5cGUiO3M6MTQ6IkJDVGhlcm0yMzIyNjQwIjtzOjg6ImxvY2F0aW"
                        ."9uIjtzOjA6IiI7czo4OiJkYXRhVHlwZSI7czozOiJyYXciO3M6NToi"
                        ."ZXh0cmEiO2E6MDp7fXM6NToidW5pdHMiO3M6NzoiJiMxNzY7RiI7cz"
                        ."o1OiJib3VuZCI7YjowO3M6MTQ6InJhd0NhbGlicmF0aW9uIjtzOjA6"
                        ."IiI7czoxMjoidGltZUNvbnN0YW50IjtpOjE7czoyOiJBbSI7aToxMD"
                        ."IzO3M6MjoiVGYiO2k6NjU1MzY7czoxOiJEIjtpOjY1NTM2O3M6MTo"
                        ."icyI7aTo2NDtzOjM6IlZjYyI7aTo1O3M6NjoiZmlsdGVyIjthOjA6e"
                        ."31zOjg6ImRlY2ltYWxzIjtpOjI7fWk6NzthOjE2OntzOjI6ImlkIj"
                        ."tpOjA7czo0OiJ0eXBlIjtzOjE0OiJCQ1RoZXJtMjMyMjY0MCI7czo"
                        ."4OiJsb2NhdGlvbiI7czowOiIiO3M6ODoiZGF0YVR5cGUiO3M6Mzoic"
                        ."mF3IjtzOjU6ImV4dHJhIjthOjA6e31zOjU6InVuaXRzIjtzOjc6IiY"
                        ."jMTc2O0YiO3M6NToiYm91bmQiO2I6MDtzOjE0OiJyYXdDYWxpYnJhd"
                        ."GlvbiI7czowOiIiO3M6MTI6InRpbWVDb25zdGFudCI7aToxO3M6Mj"
                        ."oiQW0iO2k6MTAyMztzOjI6IlRmIjtpOjY1NTM2O3M6MToiRCI7aTo"
                        ."2NTUzNjtzOjE6InMiO2k6NjQ7czozOiJWY2MiO2k6NTtzOjY6ImZp"
                        ."bHRlciI7YTowOnt9czo4OiJkZWNpbWFscyI7aToyO31pOjg7YToxN"
                        ."jp7czoyOiJpZCI7aTowO3M6NDoidHlwZSI7czoxNDoiQkNUaGVyb"
                        ."TIzMjI2NDAiO3M6ODoibG9jYXRpb24iO3M6MDoiIjtzOjg6ImRhdG"
                        ."FUeXBlIjtzOjM6InJhdyI7czo1OiJleHRyYSI7YTowOnt9czo1OiJ"
                        ."1bml0cyI7czo3OiImIzE3NjtGIjtzOjU6ImJvdW5kIjtiOjA7czox"
                        ."NDoicmF3Q2FsaWJyYXRpb24iO3M6MDoiIjtzOjEyOiJ0aW1lQ29uc"
                        ."3RhbnQiO2k6MTtzOjI6IkFtIjtpOjEwMjM7czoyOiJUZiI7aTo2NTU"
                        ."zNjtzOjE6IkQiO2k6NjU1MzY7czoxOiJzIjtpOjY0O3M6MzoiVmNj"
                        ."IjtpOjU7czo2OiJmaWx0ZXIiO2E6MDp7fXM6ODoiZGVjaW1hbHMiO"
                        ."2k6Mjt9aTo5O2E6Mzp7czoyOiJpZCI7aToyNTQ7czo0OiJ0eXBlIj"
                        ."tzOjExOiJQbGFjZWhvbGRlciI7czo4OiJsb2NhdGlvbiI7Tjt9aTo"
                        ."xMDthOjM6e3M6MjoiaWQiO2k6MjU0O3M6NDoidHlwZSI7czoxMToi"
                        ."UGxhY2Vob2xkZXIiO3M6ODoibG9jYXRpb24iO047fWk6MTE7YTozO"
                        ."ntzOjI6ImlkIjtpOjI1NDtzOjQ6InR5cGUiO3M6MTE6IlBsYWNlaG"
                        ."9sZGVyIjtzOjg6ImxvY2F0aW9uIjtOO31pOjEyO2E6Mzp7czoyOiJ"
                        ."pZCI7aToyNTQ7czo0OiJ0eXBlIjtzOjExOiJQbGFjZWhvbGRlciI7c"
                        ."zo4OiJsb2NhdGlvbiI7Tjt9fQ==",
                ),
                array(
                    'HistoryRawKey' => 10020,
                    'DeviceKey' => 24,
                    'Date' => '2003-11-06 18:30:10',
                    'RawData' => '5a004021690eb59c0ee743075f1f07531c07e01c079f1d'
                        .'07a32607fe2f070070',
                    'ActiveSensors' => 9,
                    'Driver' => 'e00391200',
                    'RawSetup' => 'FF00000038003912004100391103420000014000000000'
                        .'00000000000000',
                    'RawCalibration' => '',
                    'Status' => 'GOOD',
                    'ReplyTime' => 0,
                    'sendCommand' => 55,
                    'UTCOffset' => 0
                ),
                "other",
                false,
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $dev     Entry into the devices history table
    * @param array $preload The array to preload into the class
    * @param array $group   The database group to use
    * @param array $expect  The expected return
    *
    * @dataProvider dataToRaw
    *
    * @return null
    */
    public function testToRaw($dev, $preload, $group, $expect)
    {
        $devHist = new DevicesHistoryTable($dev);
        $devHist->insertRow();
        $this->o->clearData();
        $this->o->fromAny($preload);
        $raw = $this->o->toRaw($group);
        /*
        $devHist->selectInto("1");
        do {
            var_export($devHist->toArray());
        } while ($devHist->nextInto());
        */
        if (!is_object($raw)) {
            $this->assertSame($expect, $raw);
        } else {
            //$pdo = &$this->config->servers->getPDO();
            //$stmt = $pdo->query("Select * from devicesHistory");
            //var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
            $this->assertEquals($expect, $raw->toArray(false));
        }
    }

}

?>
