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
    * data provider for testForceTable
    *
    * @return array
    */
    public static function dataInsertRow()
    {
        return array(
            array(
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
                dirname(__FILE__).'/../files/DevicesHistoryTableTest2.xml',
                12,
            ),
            array(
                array(
                    "id" => "404",
                    "SaveDate" => "1280251875",
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
                dirname(__FILE__).'/../files/DevicesHistoryTableTest.xml',
                404,
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
    /**
    * data provider for testForceTable
    *
    * @return array
    */
    public static function dataToDeviceContainer()
    {
        return array(
            array(
                array(
                    "id" => "404",
                    "SaveDate" => "1280251875",
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
                array(
                    "DriverInfo" => array(
                        "TimeConstant" => 1,
                        "NumSensors"   => 6,
                    ),
                    "id" => 404,
                    "DeviceID" => "000194",
                    "HWPartNum" => "0039-21-02-A",
                    "FWPartNum" => "0039-20-14-C",
                    "FWVersion" => "0.0.8",
                    "RawSetup" => "000000019400392102410039201443000008FFFFFF50",
                    "Driver" => "e00392100",
                    "sensors" => array(
                        "Sensors" => 6,
                        0 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "decimals" => 0,
                        ),
                        1 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "decimals" => 0,
                        ),
                        2 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                        ),
                        3 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "decimals" => 0,
                        ),
                        4 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "decimals" => 0,
                        ),
                        5 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
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
    * @param array $preload The device to use
    * @param mixed $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataToDeviceContainer
    */
    public function testToDeviceContainer($preload, $expect)
    {
        $this->o->fromAny($preload);
        $dev = &$this->o->toDeviceContainer();
        $this->assertTrue(
            is_a($dev, "DeviceContainer"), "Not a DeviceContainer"
        );
        $this->assertSame($expect, $dev->toArray(false), "Wrong data");
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
                        "NumSensors"   => 6,
                    ),
                    "id" => 404,
                    "DeviceID" => "000194",
                    "HWPartNum" => "0039-21-02-A",
                    "FWPartNum" => "0039-20-14-C",
                    "FWVersion" => "0.0.8",
                    "RawSetup" => "000000019400392102410039201443000008FFFFFF50",
                    "Driver" => "e00392100",
                    "sensors" => array(
                        "Sensors" => 6,
                        0 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "decimals" => 0,
                        ),
                        1 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "decimals" => 0,
                        ),
                        2 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                        ),
                        3 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "decimals" => 0,
                        ),
                        4 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "decimals" => 0,
                        ),
                        5 => array("id" => 2, "type" => "BCTherm2322640"),
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
                        "NumSensors"   => 6,
                    ),
                    "id" => 404,
                    "DeviceID" => "000194",
                    "HWPartNum" => "0039-21-02-A",
                    "FWPartNum" => "0039-20-14-C",
                    "FWVersion" => "0.0.8",
                    "RawSetup" => "000000019400392102410039201443000008FFFFFF50",
                    "Driver" => "e00392100",
                    "sensors" => array(
                        "Sensors" => 6,
                        0 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "decimals" => 0,
                        ),
                        1 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "decimals" => 0,
                        ),
                        2 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                        ),
                        3 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "decimals" => 0,
                        ),
                        4 => array(
                            "id" => 2,
                            "type" => "BCTherm2322640",
                            "decimals" => 0,
                        ),
                        5 => array("id" => 2, "type" => "BCTherm2322640"),
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
    * @param int   $id     The ID of the device to use
    * @param int   $date   The date to use
    * @param mixed $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDeviceFactory
    */
    public function testDeviceFactory($id, $date, $expect)
    {
        $dev = &DevicesHistoryTable::deviceFactory($id, $date);
        $this->assertTrue(
            is_a($dev, "DeviceContainer"), "Not a DeviceContainer"
        );
        $this->assertSame($expect, $dev->toArray(false), "Wrong data");
    }

}

?>
