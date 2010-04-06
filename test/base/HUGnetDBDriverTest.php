<?php
/**
 * This runs all of the tests associated with HUGnetLib.
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
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

require_once dirname(__FILE__).'/../../base/HUGnetDBDriver.php';
require_once dirname(__FILE__).'/../stubs/DummyTableContainer.php';
require_once 'PHPUnit/Extensions/Database/TestCase.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetDBDriverTest extends PHPUnit_Extensions_Database_TestCase
{
    /** @var object This is our database object */
    protected $pdo;
    /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    *
    * @access protected
    *
    * @return null
    */
    protected function setUp()
    {
        $this->pdo = PHPUnit_Util_PDO::factory("sqlite::memory:");
        $this->pdo->query(
            "CREATE TABLE IF NOT EXISTS `myTable` ("
            ." `id` int(11) NOT NULL,"
            ." `name` varchar(32) NOT NULL,"
            ." `value` float NOT NULL"
            ." )"
        );
        parent::setUp();
        $this->table = new DummyTableContainer();
        $this->o = HUGnetDBDriverTestStub::singleton($this->table, $this->pdo);
    }

    /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    *
    * @access protected
    *
    * @return null
    */
    protected function tearDown()
    {
        unset($this->o);
        unset($this->pdo);
    }

    /**
    * This sets up our database connection
    *
    * @return null
    */
    protected function getConnection()
    {
        return $this->createDefaultDBConnection($this->pdo, "sqlite");
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
            dirname(__FILE__).'/../files/HUGnetDBDriverTest.xml'
        );
    }
    /**
    * Data provider for testAddColumnQuery
    *
    * @return array
    */
    public static function dataAddColumnQuery()
    {
        return array(
            array(
                array(
                    "Name" => "ColumnName",
                    "Type" => "int(12)",
                    "Default" => "0",
                    "Null" => false,
                ),
                "ALTER TABLE `myTable` ADD "
                ."`ColumnName` int(12)  NOT NULL  DEFAULT '0'"
            ),
            array(
                array(
                    "Name" => "ColumnName",
                    "Type" => "numeric(12)",
                    "Default" => null,
                    "Null" => true,
                ),
                "ALTER TABLE `myTable` ADD `ColumnName` numeric(12)  NULL "
            ),
        );
    }
    /**
    * test
    *
    * @param array  $column The database key to get the record from
    * @param string $expect  The query created
    *
    * @return null
    *
    * @dataProvider dataAddColumnQuery
    */
    public function testAddColumnQuery($column, $expect)
    {
        $this->o->addColumn($column);
        $this->assertAttributeSame($expect, "query", $this->o);
        $stmt = $this->pdo->query("PRAGMA table_info(".$this->table->sqlTable.")");
        $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ((array)$cols as $row) {
            if ($row["name"] == $column["Name"]) {
                if (is_string($row["dflt_value"])) {
                    $def = str_replace("'", "", $row["dflt_value"]);
                } else {
                    $def = $row["dflt_value"];
                }
                $ret = array(
                    "Name" => $row["name"],
                    "Type" => $row["type"],
                    "Default" => $def,
                    "Null" => !(bool)$row["notnull"],
                );
                break;
            }
        }
        $this->assertSame($column, $ret);
    }
    /**
    * Data provider for testCreateTable
    *
    * @return array
    */
    public static function dataCreateTable()
    {
        return array(
            array(
                array(
                    array(
                        "Name" => "ColumnName",
                        "Type" => "int(12)",
                        "Default" => 0,
                        "Null" => false,
                    ),
                    array(
                        "Name" => "Column2",
                        "Type" => "bigint(12)",
                        "Default" => 1,
                        "Null" => false,
                    ),
                ),
                "CREATE TABLE IF NOT EXISTS `myTable` (\n"
                ."     `ColumnName` int(12)  NOT NULL  DEFAULT '0',\n"
                ."     `Column2` bigint(12)  NOT NULL  DEFAULT '1'\n"
                .")",
            ),
            array(
                array(
                    array(
                        "Name" => "Column1",
                        "Type" => "float",
                        "Default" => 1.0,
                    ),
                    array(
                        "Name" => "Column2",
                        "Type" => "double",
                        "Default" => 0.0,
                    ),
                ),
                "CREATE TABLE IF NOT EXISTS `myTable` (\n"
                ."     `Column1` float  NOT NULL  DEFAULT '1',\n"
                ."     `Column2` double  NOT NULL  DEFAULT '0'\n"
                .")",
            ),
            array(
                array(
                    array(
                        "Name" => "ColumnName",
                        "Type" => "varchar(32)",
                        "Default" => "a",
                        "Null" => false,
                    ),
                ),
                "CREATE TABLE IF NOT EXISTS `myTable` (\n"
                ."     `ColumnName` varchar(32)  NOT NULL  DEFAULT 'a'\n)"
            ),
        );
    }
    /**
    * test
    *
    * @param array  $columns The columns to put in the table
    * @param string $expect  The query created
    *
    * @return null
    *
    * @dataProvider dataCreateTable
    */
    public function testCreateTable($columns, $expect)
    {
        $this->pdo->query("DROP TABLE `myTable`");
        $this->o->CreateTable($columns);
        $this->assertAttributeSame($expect, "query", $this->o);
    }
    /**
    * test
    *
    * @param array  $columns The columns to put in the table
    * @param string $expect  The query created
    *
    * @return null
    *
    * @dataProvider dataCreateTable
    */
    public function testCreateTableExists($columns, $expect)
    {
        $this->o->CreateTable($columns);
        $this->assertAttributeSame($expect, "query", $this->o);
    }

    /**
    * Data provider for testAddColumn
    *
    * @return array
    */
    public static function dataAddIndex()
    {
        return array(
            array(
                array(
                    "Name" => "IndexName",
                    "Type" => "UNIQUE",
                    "Columns" => array("asdf","fdas", "fdsafds"),
                ),
                "CREATE  UNIQUE INDEX `IndexName` ON `myTable` "
                ."(`asdf`, `fdas`, `fdsafds`)",
            ),
            array(
                array(
                    "Name" => "IndexName",
                    "Type" => "PRIMARY",
                    "Columns" => array("asdf","fdas", "fdsafds"),
                ),
                "CREATE  PRIMARY INDEX `IndexName` ON `myTable` "
                ."(`asdf`, `fdas`, `fdsafds`)",
            ),
        );
    }
    /**
    * test
    *
    * @param array  $column The database key to get the record from
    * @param string $expect  The query created
    *
    * @return null
    *
    * @dataProvider dataAddIndex
    */
    public function testAddIndex($column, $expect)
    {
        $this->o->addIndex($column);
        $this->assertAttributeSame($expect, "query", $this->o);
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
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetDBDriverTestStub extends HUGnetDBDriver
{
    public $defColumns = array();
    /**
    * Gets the instance of the class and
    *
    * @param object $table The table to attach myself to
    * @param object $pdo   The database object
    *
    * @return null
    */
    static public function &singleton(&$table, PDO &$pdo)
    {
        $class    = __CLASS__;
        $instance = new $class();
        $instance->myTable = &$table;
        $instance->pdo     = &$pdo;
        return $instance;
    }
    /**
    * Gets columns from a SQLite server
    *
    * @return null
    */
    protected function columns()
    {
        foreach ((array)$this->columns as $col) {
            $this->columns[$col['name']] = $col['type'];
        }
    }
}
?>
