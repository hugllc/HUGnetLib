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
require_once CODE_BASE.'tables/PacketLogTable.php';
/** This is a required class */
require_once CODE_BASE.'containers/PacketContainer.php';
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
class PacketLogTableTest extends HUGnetDBTableTestBase
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
            "useSocket" => "dummy",
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->o = new PacketLogTable();
        $this->pdo = &$this->config->servers->getPDO();

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
            TEST_CONFIG_BASE.'files/PacketLogTableTest.xml'
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataColumns()
    {
        $obj = new PacketLogTable();
        return HUGnetDBTableTestBase::splitObject($obj, "sqlColumns");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIndexes()
    {
        $obj = new PacketLogTable();
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
            array(new PacketLogTable()),
        );
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataConstructor()
    {
        $pkt1 = new PacketContainer(
            array(
                "To" => "123456",
                "From" => "654321",
                "Date" => 1048472484,
                "Command" => PacketContainer::COMMAND_GETSETUP,
                "Length" => 2,
                "Data" => "0102",
                "Time" => 23.0,
            )
        );
        $pkt2 = clone $pkt1;
        $pkt2->Reply = new PacketContainer(
            array(
                "To" => "654321",
                "From" => "123456",
                "Date" => 1048472484,
                "Command" => PacketContainer::COMMAND_REPLY,
                "Length" => 6,
                "Data" => "010203040506",
                "Time" => 25.0,
            )
        );
        return array(
            array(
                array(),
                array(
                    "group" => "default",
                    "DeviceKey" => 0,
                    "GatewayKey" => 0,
                    "Date" => 0,
                    "Command" => null,
                    "sentCommand" => null,
                    "PacketFrom" => null,
                    "RawData" => "",
                    "sentRawData" => "",
                    "Type" => "UNSOLICITED",
                    "ReplyTime" => 0,
                ),
            ),
            array(
                $pkt1,
                array(
                    "group" => "default",
                    "DeviceKey" => 0,
                    "GatewayKey" => 0,
                    "Date" => 1048472484,
                    "Command" => "5C",
                    "sentCommand" => null,
                    "PacketFrom" => "654321",
                    "RawData" => "0102",
                    "sentRawData" => "",
                    "Type" => "CONFIG",
                    "ReplyTime" => 0,
                ),
            ),
            array(
                $pkt2,
                array(
                    "group" => "default",
                    "DeviceKey" => 0,
                    "GatewayKey" => 0,
                    "Date" => 1048472484,
                    "Command" => "01",
                    "sentCommand" => "5C",
                    "PacketFrom" => "123456",
                    "RawData" => "010203040506",
                    "sentRawData" => "0102",
                    "Type" => "CONFIG",
                    "ReplyTime" => 2.0,
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
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $expect)
    {
        $obj = new PacketLogTable($preload);
        $this->assertAttributeSame($expect, "data", $obj);
    }
}

?>
