<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

namespace HUGnet\db\tables;
/** This is a required class */
require_once TEST_BASE."db/tables/history/HistoryTestBase.php";
/** This is a required class */
require_once CODE_BASE.'db/Table.php';
/** This is a required class */
require_once CODE_BASE.'db/Connection.php';
/** This is a required class */
require_once CODE_BASE.'db/tables/history/E10460100History.php';
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
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class E10460100HistoryTest extends HistoryTestBase
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
        $this->system = new \HUGnet\DummySystem("System");
        $this->system->resetMock($this->config);
        $this->connect = \HUGnet\db\Connection::factory($this->system);
        $this->pdo = &$this->connect->getDBO("default");
        $data = array(
        );
        $this->o = \HUGnet\db\Table::factory(
            $this->system, $data, "E10460100History", $this->connect
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
        unset($this->o);
    }

    /**
    * Data provider for lots of tests.
    *
    * @return array
    */
    public static function dataClassName()
    {
        return array(
            array("E10460100History"),
        );
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @return null
    */
    public function testSqlTable()
    {
        $this->assertSame("e10460100_history", $this->o->sqlTable);
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @return null
    */
    public function testDatacols()
    {
        $this->assertSame(20, $this->o->datacols);
    }

}

?>