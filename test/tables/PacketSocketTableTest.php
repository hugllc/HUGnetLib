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


require_once dirname(__FILE__).'/../../tables/PacketSocketTable.php';
require_once dirname(__FILE__).'/../../containers/PacketContainer.php';
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
class PacketSocketTableTest extends HUGnetDBTableTestBase
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
        $this->pdo = &$this->config->servers->getPDO();
        $this->o = new PacketSocketTable();
        $this->o->create();
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
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataColumns()
    {
        $o = new PacketSocketTable();
        return HUGnetDBTableTestBase::splitObject($o, "sqlColumns");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIndexes()
    {
        $o = new PacketSocketTable();
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
            array(new PacketSocketTable()),
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
                "Date" => "2003-03-24 02:21:24",
                "Command" => PacketContainer::COMMAND_GETSETUP,
                "Length" => 2,
                "Data" => "0102",
                "Time" => 23.0,
            )
        );
        return array(
            array(
                array(),
                array(
                    "TimeoutPeriod" => 5,
                    "group" => "default",
                    "id" => null,
                    "Date" => "0000-00-00 00:00:00",
                    "Command" => null,
                    "PacketFrom" => "000000",
                    "PacketTo" => null,
                    "RawData" => "",
                    "Type" => "UNSOLICITED",
                    "ReplyTime" => 0,
                    "Checked" => 0,
                    "Timeout" => 0,
                    "PacketTime" => 0.0,
                    "senderID" => 0,
                ),
            ),
            array(
                new PacketContainer(),
                array(
                    "TimeoutPeriod" => 5,
                    "group" => "default",
                    "id" => null,
                    "Date" => "0000-00-00 00:00:00",
                    "Command" => null,
                    "PacketFrom" => "000000",
                    "PacketTo" => null,
                    "RawData" => "",
                    "Type" => "UNSOLICITED",
                    "ReplyTime" => 0,
                    "Checked" => 0,
                    "Timeout" => 0,
                    "PacketTime" => 0.0,
                    "senderID" => 0,
                ),
            ),
            array(
                $pkt1,
                array(
                    "TimeoutPeriod" => 5,
                    "group" => "default",
                    "id" => null,
                    "Date" => "2003-03-24 02:21:24",
                    "Command" => "5C",
                    "PacketFrom" => "654321",
                    "PacketTo" => "123456",
                    "RawData" => "0102",
                    "Type" => "CONFIG",
                    "ReplyTime" => 0.0,
                    "Checked" => 0,
                    "Timeout" => 0,
                    "PacketTime" => 0.0,
                    "senderID" => 0,
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
        $o = new PacketSocketTable($preload);
        $this->assertAttributeSame($expect, "data", $o);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataFromArray()
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
    * @dataProvider dataFromArray
    */
    public function testFromArray($preload, $expect)
    {
        $this->o->fromArray($preload);
        $this->assertAttributeSame($expect, "data", $this->o);
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
                    "TimeoutPeriod" => 5,
                    "group" => "default",
                    "id" => null,
                    "Date" => "2003-03-24 02:21:24",
                    "Command" => "5C",
                    "PacketFrom" => "654321",
                    "PacketTo" => "123456",
                    "RawData" => "0102",
                    "Type" => "CONFIG",
                    "ReplyTime" => 0.0,
                    "Checked" => 0,
                    "Timeout" => 0,
                ),
                false,
                array(
                    array(
                        "id" => "1",
                        "Date" => "2003-03-24 02:21:24",
                        "Command" => "5C",
                        "PacketFrom" => "654321",
                        "PacketTo" => "123456",
                        "RawData" => "0102",
                        "Type" => "CONFIG",
                        "ReplyTime" => "0.0",
                        "Checked" => "0",
                        "senderID" => "0",
                    ),
                ),
            ),
            array(
                array(
                ),
                true,
                array(
                ),
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param bool  $replace Replace any records that collide with this one.
    * @param array $expect  The expected return
    *
    * @dataProvider dataInsertRow
    *
    * @return null
    */
    public function testInsertRow($preload, $replace, $expect)
    {
        $start = time();
        $this->o->fromAny($preload);
        $this->o->insertRow($replace);
        $stmt = $this->pdo->query("SELECT * FROM `PacketSocket`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach (array_keys((array)$rows) as $key) {
            $this->assertThat($rows[$key]["Timeout"], $this->greaterThan($start));
            $this->assertThat($rows[$key]["PacketTime"], $this->greaterThan($start));
            unset($rows[$key]["Timeout"]);
            unset($rows[$key]["PacketTime"]);
        }
        $this->assertSame($expect, $rows);
    }
    /**
    * Data provider for testDeleteOld
    *
    * @return array
    */
    public static function dataDeleteOld()
    {
        return array(
            // Auto increment
            array(
                array(
                    array(
                        "TimeoutPeriod" => 5,
                        "group" => "default",
                        "id" => null,
                        "Date" => "2003-03-24 02:21:24",
                        "Command" => "5C",
                        "PacketFrom" => "654321",
                        "PacketTo" => "123456",
                        "RawData" => "0102",
                        "Type" => "CONFIG",
                        "ReplyTime" => 0.0,
                        "Checked" => 0,
                        "Timeout" => 2234234234,
                    ),
                    array(
                        "TimeoutPeriod" => 5,
                        "group" => "default",
                        "id" => null,
                        "Date" => "2003-03-24 02:21:24",
                        "Command" => "5C",
                        "PacketFrom" => "654000",
                        "PacketTo" => "123000",
                        "RawData" => "0102030405",
                        "Type" => "CONFIG",
                        "ReplyTime" => 0.0,
                        "Checked" => 0,
                        "Timeout" => (time() - 100),
                    ),
                ),
                array(
                    array(
                        "id" => "1",
                        "Date" => "2003-03-24 02:21:24",
                        "Command" => "5C",
                        "PacketFrom" => "654321",
                        "PacketTo" => "123456",
                        "RawData" => "0102",
                        "Type" => "CONFIG",
                        "ReplyTime" => "0.0",
                        "Checked" => "0",
                        "Timeout" => "2234234234",
                        "senderID" => "0",
                    ),
                ),
            ),
            array(
                array(
                ),
                array(
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
    * @dataProvider dataDeleteOld
    *
    * @return null
    */
    public function testDeleteOld($preload, $expect)
    {
        foreach ((array)$preload as $load) {
            $this->o->fromAny($load);
            $this->o->insertRow();
        }
        $this->o->deleteOld();
        $stmt = $this->pdo->query("SELECT * FROM `PacketSocket`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach (array_keys((array)$rows) as $key) {
            unset($rows[$key]["PacketTime"]);
        }
        $this->assertSame($expect, $rows);
    }

}

?>
