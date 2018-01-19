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
/** This is the database testing extension */
require_once TEST_BASE.'db/tables/TableTestBase.php';
/** This is a required class */
require_once CODE_BASE.'db/History.php';
/** This is a required class */
require_once CODE_BASE.'db/Average.php';
/**
 * Test class for device drivers
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class HistoryTestBase extends \PHPUnit_Framework_TestCase
{
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class The class to use
    *
    * @return null
    *
    * @dataProvider dataClassName
    */
    public function testSqlTableType($class)
    {
        $system = new \HUGnet\DummySystem("System");
        $connect = \HUGnet\db\Connection::factory($system);
        $obj = \HUGnet\db\Table::factory(
            $system, $data, $class, $connect
        );
        $this->assertInternalType("string", $obj->sqlTable, "sqlTable not a string");
        $this->assertFalse(
            empty($obj->sqlTable),
            "sqlTable is not set"
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class The class to use
    *
    * @return null
    *
    * @dataProvider dataClassName
    */
    public function testDatacolsType($class)
    {
        $system = new \HUGnet\DummySystem("System");
        $connect = \HUGnet\db\Connection::factory($system);
        $obj = \HUGnet\db\Table::factory(
            $system, $data, $class, $connect
        );
        $this->assertInternalType("int", $obj->datacols, "datacols not a int");
        $this->assertFalse(
            empty($obj->datacols),
            "datacols is not set"
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testParent()
    {
        $this->assertInstanceOf(
            "HUGnet\\db\\History",
            $this->o
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
            array("group", "default", "default"),
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
        $this->assertSame($expect, $this->o->get($var));
    }
}

?>
