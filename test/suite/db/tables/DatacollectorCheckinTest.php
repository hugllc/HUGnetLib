<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
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
require_once CODE_BASE.'db/tables/DatacollectorCheckin.php';
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DataCollectorCheckinTest extends TableTestBase
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
        $this->system = $this->getMock(
            "\\HUGnet\\system",
            array("now")
        );
        //new \HUGnet\DummySystem("System");
        //$this->system->resetMock($this->config);
        $this->connect = \HUGnet\db\Connection::factory($this->system);
        $this->pdo = &$this->connect->getPDO("default");
        $data = array(
        );
        $this->o = \HUGnet\db\Table::factory(
            $this->system, $data, "DatacollectorCheckin", $this->connect
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
            TEST_CONFIG_BASE.'files/DataCollectorCheckinTableTest.xml'
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
            $system, $data, "DatacollectorCheckin", $connect
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
            $system, $data, "DatacollectorCheckin", $connect
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
                    $system, $data, "DatacollectorCheckin", $connect
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
                "Date", "2003-02-28 02:00:00", gmmktime(02, 0, 0, 2, 28, 2003)
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
    public static function dataCheckin()
    {
        return array(
            array( // #0 Normal checkin
                "48aa1c44-85be-4179-beab-8bbad51c1824",
                123456,
                true,
                array(
                    0 => array(
                        'uuid' => '48aa1c44-85be-4179-beab-8bbad51c1824',
                        'Date' => 123456,
                    ),
                    1 => array(
                        'uuid' => '48aa1c44-85be-4179-beab-8bbad51c1824',
                        'Date' => 123456789,
                    ),
                ),
            ),
            array( // #1 Double checkin
                "48aa1c44-85be-4179-beab-8bbad51c1824",
                123456789,
                true,
                array(
                    0 => array(
                        'uuid' => '48aa1c44-85be-4179-beab-8bbad51c1824',
                        'Date' => 123456789,
                    ),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param int   $uuid   The uuid to use
    * @param int   $now    The time to use
    * @param bool  $return The expected return
    * @param mixed $expect The expected data
    *
    * @return null
    *
    * @dataProvider dataCheckin
    */
    public function testCheckin($uuid, $now, $return, $expect)
    {
        $this->system->expects($this->any())
            ->method('now')
            ->will($this->returnValue($now));
        $ret = $this->o->checkin($uuid);
        $this->assertSame($return, $ret, "Return Wrong");
        $stmt = $this->pdo->query(
            "SELECT * FROM `datacollectorcheckin` where `uuid` = '$uuid'"
        );
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expect, $rows);
    }
}

?>