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


require_once CODE_BASE.'tables/GenericTable.php';
require_once TEST_BASE."tables/HUGnetDBTableTestBase.php";

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
class GenericTableTest extends HUGnetDBTableTestBase
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
        $this->pdo->query("DROP TABLE IF EXISTS `myTable`");
        $this->pdo->query(
            "CREATE TABLE `myTable` ("
            ." `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,"
            ." `name` varchar(32) NOT NULL,"
            ." `value` float NULL"
            ." )"
        );
        $data = array(
        );
        $this->o = new GenericTable($data, "myTable");
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
            TEST_CONFIG_BASE.'files/GenericTableTest.xml'
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataColumns()
    {
        $obj = new GenericTable();
        return HUGnetDBTableTestBase::splitObject($obj, "sqlColumns");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIndexes()
    {
        $obj = new GenericTable();
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
            array(new GenericTable()),
        );
    }

    /**
    * data provider for testForceTable
    *
    * @return array
    */
    public static function dataForceTable()
    {
        return array(
            array(
                "myTable",
                "myTable",
                array(
                    "id" => array(
                        "Name" => "id",
                        "Type" => "INTEGER",
                        "Default" => null,
                        "Null" => false,
                    ),
                    "name" => array(
                        "Name" => "name",
                        "Type" => "varchar(32)",
                        "Default" => null,
                        "Null" => false,
                    ),
                    "value" => array(
                        "Name" => "value",
                        "Type" => "float",
                        "Default" => null,
                        "Null" => true,
                    ),
                ),
                array(
                    "group" => "default",
                    "id" => null,
                    "name" => null,
                    "value" => null,
                ),
            ),
            array(
                "",
                "table",
                array(
                    "id" => array(
                        "Name" => "id",
                        "Type" => "int",
                    ),
                ),
                array(
                    "group" => "default",
                    "id" => null,
                ),
            ),
        );
    }

    /**
    * test the forceTable routine
    *
    * @param string $table       The table to use
    * @param string $tableExpect The expected table
    * @param mixed  $expect      The expected return
    * @param array  $default     What the default array should be.
    *
    * @return null
    *
    * @dataProvider dataForceTable
    */
    public function testForceTable($table, $tableExpect, $expect, $default)
    {
        $obj = new GenericTable();
        $obj->forceTable($table);
        $this->assertAttributeSame($tableExpect, "sqlTable", $obj, "table wrong");
        $this->assertAttributeSame($expect, "sqlColumns", $obj, "columns wrong");
        $this->assertAttributeSame($default, "default", $obj, "default wrong");
    }
    /**
    * data provider for testCheckTables
    *
    * @return array
    */
    public static function dataCheckTables()
    {
        return array(
            array(
                array("myTable"),
                "table",
            ),
            array(
                array(),
                "table",
            ),
        );
    }

    /**
    * test the forceTable routine
    *
    * @param array  $tables      The tables to use
    * @param string $tableExpect The expected table
    *
    * @return null
    *
    * @dataProvider dataCheckTables
    */
    public function testCheckTables($tables, $tableExpect)
    {
        $obj = new GenericTable();
        $obj->checkTables($tables);
        $this->assertAttributeSame($tableExpect, "sqlTable", $obj);
    }

    /**
    * data provider for testGetTables
    *
    * @return array
    */
    public static function dataGetTables()
    {
        return array(
            array(
                array("myTable" => "myTable"),
            ),
        );
    }

    /**
    * test the forceTable routine
    *
    * @param string $expect The expected table
    *
    * @return null
    *
    * @dataProvider dataGetTables
    */
    public function testGetTables($expect)
    {
        $obj = new GenericTable();
        $ret = $obj->getTables();
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testUpdate
    *
    * @return array
    */
    public static function dataUpdate()
    {
        return array(
            // This one would just set the query up
            array( // #0
                array(),
                "",
                array(),
                array(),
                array(
                    array(
                        "id" => "-5",
                        "name" => "Something Negative",
                        "value" => "-25.0",
                    ),
                    array(
                        "id" => "1",
                        "name" => "Something Here",
                        "value" => "25.0",
                    ),
                    array(
                        "id" => "2",
                        "name" => "Another THing",
                        "value" => "22.0",
                    ),
                    array(
                        "id" => "32",
                        "name" => "A way up here thing",
                        "value" => "23.0",
                    ),
                ),
                true,
            ),
            // Normal update
            array(
                array("id" => 2, "name" => "a name", "value" => 10),
                "id = ?",
                array(2),
                array(),
                array(
                    array(
                        "id" => "-5",
                        "name" => "Something Negative",
                        "value" => "-25.0",
                    ),
                    array(
                        "id" => "1",
                        "name" => "Something Here",
                        "value" => "25.0",
                    ),
                    array(
                        "id" => "2",
                        "name" => "a name",
                        "value" => "10.0",
                    ),
                    array(
                        "id" => "32",
                        "name" => "A way up here thing",
                        "value" => "23.0",
                    ),
                ),
                true,
            ),
        );
    }
    /**
    * test
    *
    * @param array  $data      The data to use.  It just sets up the query if this is
    *                          empty.
    * @param string $where     The where clause to use
    * @param array  $whereData The data to use for the where clause
    * @param array  $keys      The columns to insert.  Uses all of this is blank.
    * @param string $expect    The query created
    * @param bool   $ret       The expected return value
    * @param bool   $ret2      The expected return value of the second call
    * @param string $sqlId     The id column to use
    * @param array  $indexes   The indexes array to use
    *
    * @return null
    *
    * @dataProvider dataUpdate
    */
    public function testUpdate(
        $data,
        $where,
        $whereData,
        $keys,
        $expect,
        $ret,
        $ret2 = true,
        $sqlId = "id",
        $indexes = array()
    ) {
        $this->table->sqlId = $sqlId;
        $this->table->sqlIndexes = $indexes;
        $r = $this->o->update($data, $where, $whereData, $keys);
        $this->assertSame($ret, $r);
        $stmt = $this->pdo->query("SELECT * FROM `myTable`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows);
    }

}

?>
