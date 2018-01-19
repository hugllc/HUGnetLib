<?php
/**
 * This runs all of the tests associated with HUGnetLib.
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
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
namespace HUGnet\db;
/** This is a required class */
require_once CODE_BASE.'db/DriverBase.php';
/** This is a required class */
require_once CODE_BASE.'db/Table.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is a required class */
require_once 'PHPUnit/Extensions/Database/TestCase.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DriverBaseTest extends \PHPUnit_Extensions_Database_TestCase
{
    /** @var object This is our database object */
    protected $pdo;
    /** @var array Default config */
    protected $config;
    /** @var object System Class  */
    protected $system;
    /** @var object Table Class  */
    protected $table;
    /** @var object Object under test  */
    protected $o;
    /** @var boolean This will cause some tests to be skipped  */
    protected $skipPDOTests = true;
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
        $this->skipPDOTests = true;
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
            "Table" => array(
                "get" => array(
                    "group" => "default",
                ),
                "factory" => new \HUGnet\DummyTable("FactoryTable"),
            ),
        );
        $this->system = new \HUGnet\DummySystem("System");
        $this->system->resetMock($this->config);
        $this->connect = Connection::factory($this->system);
        $this->pdo = &$this->connect->getDBO("default");
        $this->pdo->query("DROP TABLE IF EXISTS `myTable`");
        $this->pdo->query(
            "CREATE TABLE `myTable` ("
            ." `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,"
            ." `name` varchar(32) NOT NULL,"
            ." `value` float NULL"
            ." )"
        );
        parent::setUp();
        $this->table = new \HUGnet\DummyTable("Table");
        $this->o = \HUGnet\db\drivers\DriverBaseTestStub::factory(
            $this->system, $this->table, $this->connect, "DriverBaseTestStub"
        );
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
        if (is_a($this->pdo, "PDO")) {
            $this->pdo->query("DROP TABLE IF EXISTS `myTable`");
        }
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
            TEST_CONFIG_BASE.'files/DBDriverBaseTest.xml'
        );
    }

    /**
    * Tests for exceptions
    *
    * @expectedException InvalidArgumentException
    *
    * @return null
    */
    public function testConstructTableExec()
    {
        $obj = \HUGnet\db\drivers\DriverBaseTestStub::factory(
            $empty, $this->table
        );
    }
    /**
    * Tests for exceptions
    *
    * @expectedException RuntimeException
    *
    * @return null
    */
    public function testConstructTableExec2()
    {
        $obj = \HUGnet\db\drivers\DriverBaseTestStub::factory(
            $this->system, $empty
        );
    }
    /**
    * Tests for exceptions
    *
    * @expectedException RuntimeException
    *
    * @return null
    */
    public function testConnectPDOExec()
    {
        $this->config["Table"]["get"]["group"] = "BogusGroup";
        $this->system->resetMock($this->config);
        $this->o->Connect();
    }

    /**
    * Data provider for testAddColumnQuery
    *
    * @return array
    */
    public static function dataConstructVerbose()
    {
        return array(
            array(6, \PDO::ERRMODE_WARNING),
            array(2, \PDO::ERRMODE_WARNING),
            array(0, \PDO::ERRMODE_SILENT),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param int $verbose the verbosity level to use
    * @param int $expect  The expected return
    *
    * @dataProvider dataConstructVerbose
    *
    * @return null
    */
    public function testConstructVerbose($verbose, $expect)
    {
        $this->config["System"]["get"]["verbose"] = $verbose;
        $this->system->resetMock($this->config);
        $obj = drivers\DriverBaseTestStub::factory(
            $this->system, $this->table
        );;
        $ret = $obj->qpdo->getAttribute(\PDO::ATTR_ERRMODE);
        $this->assertSame($expect, $ret);
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
                    "Type" => "INT(12)",
                    "Default" => "0",
                    "Null" => false,
                ),
                "ALTER TABLE `myTable` ADD "
                ."`ColumnName` INT(12) NOT NULL DEFAULT '0'"
            ),
            array(
                array(
                    "Name" => "ColumnName",
                    "Type" => "NUMERIC(12)",
                    "Default" => null,
                    "Null" => true,
                ),
                "ALTER TABLE `myTable` ADD `ColumnName` NUMERIC(12) NULL"
            ),
        );
    }
    /**
    * test
    *
    * @param array  $column The database key to get the record from
    * @param string $expect The query created
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
        $cols = $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
    * Data provider for testModifyColumnQuery
    *
    * @return array
    */
    public static function dataModifyColumnQuery()
    {
        return array(
            array(
                array(
                    "Name" => "name",
                    "Type" => "INT(12)",
                    "Default" => "0",
                    "Null" => false,
                ),
                "ALTER TABLE `myTable` MODIFY COLUMN "
                ."`name` INT(12) NOT NULL DEFAULT '0'"
            ),
            array(
                array(
                    "Name" => "value",
                    "Type" => "NUMERIC(12)",
                    "Default" => null,
                    "Null" => true,
                ),
                "ALTER TABLE `myTable` MODIFY COLUMN `value` NUMERIC(12) NULL"
            ),
        );
    }
    /**
    * test
    *
    * @param array  $column The database key to get the record from
    * @param string $expect The query created
    *
    * @return null
    *
    * @dataProvider dataModifyColumnQuery
    */
    public function testModifyColumnQuery($column, $expect)
    {
        $this->o->modifyColumn($column);
        $this->assertAttributeSame($expect, "query", $this->o);
    }
    /**
    * Data provider for testAddColumnQuery
    *
    * @return array
    */
    public static function dataRemoveColumnQuery()
    {
        return array(
            array(
                "ColumnName",
                "ALTER TABLE `myTable` DROP COLUMN `ColumnName`"
            ),
        );
    }
    /**
    * test
    *
    * @param array  $column The database key to get the record from
    * @param string $expect The query created
    *
    * @return null
    *
    * @dataProvider dataRemoveColumnQuery
    */
    public function testRemoveColumnQuery($column, $expect)
    {
        $this->o->removeColumn($column);
        $this->assertAttributeSame($expect, "query", $this->o);
        $stmt = $this->pdo->query("PRAGMA table_info(".$this->table->sqlTable.")");
        $cols = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ((array)$cols as $row) {
            $this->assertFalse($row["name"] === $column, "Column not deleted");
        }
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
                        "Type" => "INTEGER",
                        "Default" => 0,
                        "Null" => false,
                        "AutoIncrement" => true,
                    ),
                    array(
                        "Name" => "Column2",
                        "Type" => "bigint(12)",
                        "Default" => 1,
                        "Null" => false,
                    ),
                ),
                "CREATE TABLE IF NOT EXISTS `myTable` (\n"
                ."     `ColumnName` INTEGER PRIMARY KEY AUTOINCREMENT "
                ."NOT NULL DEFAULT '0',\n"
                ."     `Column2` BIGINT(12) NOT NULL DEFAULT '1'\n"
                .")",
                array(
                    array(
                        "cid" => "0",
                        "name" => "ColumnName",
                        "type" => "INTEGER",
                        "notnull" => "1",
                        "dflt_value" => "'0'",
                        "pk" => "1",
                    ),
                    array(
                        "cid" => "1",
                        "name" => "Column2",
                        "type" => "BIGINT(12)",
                        "notnull" => "1",
                        "dflt_value" => "'1'",
                        "pk" => "0",
                    ),
                ),
            ),
            array(
                array(
                ),
                "CREATE TABLE IF NOT EXISTS `myTable` (\n"
                ."     `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL DEFAULT "
                ."'0',\n"
                ."     `name` VARCHAR(32) NOT NULL DEFAULT '',\n"
                ."     `value` FLOAT NOT NULL DEFAULT '0'\n"
                .")",
                array(
                    array(
                        "cid" => "0",
                        "name" => "id",
                        "type" => "INTEGER",
                        "notnull" => "1",
                        "dflt_value" => "'0'",
                        "pk" => "1",
                    ),
                    array(
                        "cid" => "1",
                        "name" => "name",
                        "type" => "VARCHAR(32)",
                        "notnull" => "1",
                        "dflt_value" => "''",
                        "pk" => "0",
                    ),
                    array(
                        "cid" => "2",
                        "name" => "value",
                        "type" => "FLOAT",
                        "notnull" => "1",
                        "dflt_value" => "'0'",
                        "pk" => "0",
                    ),
                ),
            ),
            array(
                array(
                    array(
                        "Name" => "Column1",
                        "Type" => "float",
                        "Default" => 1.0,
                        "Primary" => true,
                    ),
                    array(
                        "Name" => "Column2",
                        "Type" => "double",
                        "Default" => 0.0,
                        "Unique" => true,
                        "Collate" => "BINARY",
                    ),
                ),
                "CREATE TABLE IF NOT EXISTS `myTable` (\n"
                ."     `Column1` FLOAT PRIMARY KEY NOT NULL DEFAULT '1',\n"
                ."     `Column2` DOUBLE UNIQUE COLLATE BINARY NOT NULL DEFAULT '0'\n"
                .")",
                array(
                    array(
                        "cid" => "0",
                        "name" => "Column1",
                        "type" => "FLOAT",
                        "notnull" => "1",
                        "dflt_value" => "'1'",
                        "pk" => "1",
                    ),
                    array(
                        "cid" => "1",
                        "name" => "Column2",
                        "type" => "DOUBLE",
                        "notnull" => "1",
                        "dflt_value" => "'0'",
                        "pk" => "0",
                    ),
                ),
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
                ."     `ColumnName` VARCHAR(32) NOT NULL DEFAULT 'a'\n)",
                array(
                    array(
                        "cid" => "0",
                        "name" => "ColumnName",
                        "type" => "VARCHAR(32)",
                        "notnull" => "1",
                        "dflt_value" => "'a'",
                        "pk" => "0",
                    ),
                ),
            ),
        );
    }
    /**
    * test
    *
    * @param array  $columns The columns to put in the table
    * @param string $expect  The query created
    * @param array  $table   The expected table
    *
    * @return null
    *
    * @dataProvider dataCreateTable
    */
    public function testCreateTable($columns, $expect, $table)
    {
        $this->pdo->query("DROP TABLE `myTable`");
        $this->o->CreateTable($columns);
        $this->assertAttributeSame($expect, "query", $this->o, "Query is wrong");
        $stmt = $this->pdo->query("PRAGMA table_info(".$this->table->sqlTable.")");
        $cols = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // Different versions of sqlite return different notnull values.
        foreach ($cols as $key => $col) {
            if ($col["notnull"]) {
                $cols[$key]["notnull"] = "1";
            }
        }
        $this->assertSame($table, $cols, "Columns are wrong");
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
        $this->assertAttributeSame("", "query", $this->o);
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
                    "Columns" => array("id", "name"),
                ),
                "CREATE INDEX `IndexName_myTable` ON `myTable` "
                ."(`id`, `name`)",
            ),
            array(
                array(
                    "Name" => "IndexName",
                    "Unique" => true,
                    "Columns" => array("id", "value"),
                ),
                "CREATE UNIQUE INDEX `IndexName_myTable` ON `myTable` "
                ."(`id`, `value`)",
            ),
        );
    }
    /**
    * test
    *
    * @param array  $column The database key to get the record from
    * @param string $expect The query created
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
    /**
    * Data provider for testAddColumn
    *
    * @return array
    */
    public static function dataRemoveIndex()
    {
        return array(
            array(
                "IndexName",
                "DROP INDEX `IndexName` ON `myTable`"
            ),
        );
    }
    /**
    * test
    *
    * @param array  $column The database key to get the record from
    * @param string $expect The query created
    *
    * @return null
    *
    * @dataProvider dataRemoveIndex
    */
    public function testRemoveIndex($column, $expect)
    {
        $this->o->removeIndex($column);
        $this->assertAttributeSame($expect, "query", $this->o);
    }

    /**
    * Data provider for testAddColumn
    *
    * @return array
    */
    public static function dataGetAttribute()
    {
        return array(
            array(
                constant("PDO::ATTR_DRIVER_NAME"),
                "sqlite"
            ),
        );
    }
    /**
    * test
    *
    * @param array  $attrib The attribute to get
    * @param string $expect The query created
    *
    * @return null
    *
    * @dataProvider dataGetAttribute
    */
    public function testGetAttribute($attrib, $expect)
    {
        $ret = $this->o->getAttribute($attrib);
        $this->assertSame($expect, $ret);
    }

    /**
    * Data provider for testPrepareExecute
    *
    * @return array
    */
    public static function dataPrepareExecute()
    {
        return array(
            array(
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
            ),
            array(
                array(),
                "This is not valid SQL",
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
            ),
            array(
                array(
                    "Table" => array(
                        "duplicate" => array(
                            "id" => "-5",
                            "name" => "Something Negative",
                            "value" => "-25.0",
                        ),
                    ),
                ),
                "SELECT * FROM `myTable` WHERE id = ?",
                array(-5),
                array(
                    array(
                        "id" => "-5",
                        "name" => "Something Negative",
                        "value" => "-25.0",
                    ),
                ),
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
            ),
            array(
                array(
                    "Table" => array(
                        "duplicate" => array(
                            "id" => "-5",
                            "name" => "Something Negative",
                            "value" => "-25.0",
                        ),
                    ),
                ),
                "SELECT * FROM `myTable` WHERE id = ?",
                array(1,2,3,4),
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
            ),
        );
    }
    /**
    * test
    *
    * @param array  $mocks  The mocks to use
    * @param string $query  The query to use
    * @param array  $data   The data that goes with the query
    * @param mixed  $ret    The expected return value
    * @param string $expect What the database looks like when we are done
    *
    * @return null
    *
    * @dataProvider dataPrepareExecute
    */
    public function testPrepareExecuteReset($mocks, $query, $data, $ret, $expect)
    {
        $this->o->reset();
        $this->o->prepare($query);
        $this->o->execute($data);
        $rows = $this->o->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($ret, $rows);

        $stmt = $this->pdo->query("SELECT * FROM `myTable`");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expect, $rows);
        $this->o->reset();
        $this->assertAttributeSame("", "query", $this->o);
    }
    /**
    * test
    *
    * @param array  $mocks  The mocks to use
    * @param string $query  The query to use
    * @param array  $data   The data that goes with the query
    * @param mixed  $ret    The expected return value
    * @param string $expect What the database looks like when we are done
    *
    * @return null
    *
    * @dataProvider dataPrepareExecute
    */
    public function testPrepareExecuteResetObj($mocks, $query, $data, $ret, $expect)
    {
        foreach ($mocks as $name => $value) {
            $this->config[$name] = array_merge(
                (array)$this->config[$name], $mocks[$name]
            );
        }
        $this->system->resetMock($this->config);
        $this->o->reset();
        $this->o->prepare($query);
        $this->o->execute($data);
        $rows = $this->o->fetchAll(\PDO::FETCH_CLASS);
        $this->assertEquals($ret, $rows);

        $stmt = $this->pdo->query("SELECT * FROM `myTable`");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expect, $rows);
        $this->o->reset();
        $this->assertAttributeSame("", "query", $this->o);
    }
    /**
    * Data provider for testPrepareExecute
    *
    * @return array
    */
    public static function dataFetchAll()
    {
        return array(
            array(
                array(
                ),
                "SELECT * FROM `myTable` WHERE id = ?",
                array(-5),
                true,
                array(
                    array(
                        array(
                            "id" => "-5",
                            "name" => "Something Negative",
                            "value" => "-25.0"
                        ),
                    ),
                ),
            ),
            array(
                array(
                ),
                "SELECT * FROM `myTable` WHERE id = ?",
                array(1,2,3,4),
                false,
                null,
            ),
            array(
                array(
                ),
                "This is not valid SQL",
                array(-5),
                false,
                null,
            ),
        );
    }
    /**
    * test
    *
    * @param array  $mocks  The mocks to use
    * @param string $query  The query to use
    * @param array  $data   The data that goes with the query
    * @param mixed  $ret    The expected return value
    * @param string $expect What the database looks like when we are done
    *
    * @return null
    *
    * @dataProvider dataFetchAll
    */
    public function testFetchAll($mocks, $query, $data, $ret, $expect)
    {
        $this->o->reset();
        $this->o->prepare($query);
        $this->o->execute($data);
        $rows = $this->o->fetchInto();
        $this->assertEquals($ret, $rows);
        $table = $this->system->retrieve("Table");
        $this->assertEquals($expect, $table["fromArray"]);
    }
    /**
    * Data provider for testQuery
    *
    * @return array
    */
    public static function dataQuery()
    {
        return array(
            array(
                "SELECT * FROM `myTable` WHERE id = ?",
                array(-5),
                array(
                    array(
                        "id" => "-5",
                        "name" => "Something Negative",
                        "value" => "-25.0",
                    ),
                ),
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
            ),
            array(
                "SELECT * FROM `badTable` WHERE id = ?",
                array(-5),
                false,
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
            ),
            array(
                "SELECT * FROM `myTable` WHERE id = ?",
                array(-10, 11, 12),
                false,
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
            ),
        );
    }
    /**
    * test
    *
    * @param string $query  The query to use
    * @param array  $data   The data that goes with the query
    * @param mixed  $ret    The expected return value
    * @param string $expect What the database looks like when we are done
    *
    * @return null
    *
    * @dataProvider dataQuery
    */
    public function testQuery($query, $data, $ret, $expect)
    {
        $res = $this->o->query($query, $data);
        $this->assertSame($ret, $res);
        $stmt = $this->pdo->query("SELECT * FROM `myTable`");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows);
    }
    /**
    * Data provider for testQuery
    *
    * @return array
    */
    public static function dataPrepareData()
    {
        return array(
            array(
                array(
                ),
                array(
                    "id" => "-5",
                    "name" => "Something Negative",
                    "value" => "-25.0",
                ),
                array("-5", "Something Negative", "-25.0"),
            ),
            array(
                array(
                    "id"
                ),
                array(
                    "id" => "-5",
                    "name" => "Something Negative",
                    "value" => "-25.0",
                ),
                array("-5", "Something Negative", "-25.0", "-5"),
            ),
        );
    }
    /**
    * test
    *
    * @param array  $idWhere The idWhere to use
    * @param array  $data    The data that goes with the query
    * @param string $expect  What the database looks like when we are done
    *
    * @return null
    *
    * @dataProvider dataPrepareData
    */
    public function testPrepareData($idWhere, $data, $expect)
    {
        $this->o->idWhere = $idWhere;
        $res = $this->o->prepareData($data);
        $this->assertSame($expect, $res);
    }
}

namespace HUGnet\db\drivers;
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DriverBaseTestStub extends \HUGnet\db\DriverBase
{
    /** These are our default columns */
    public $defColumns = array();
    /** @var bool This is where we store the ID columns we are currently using in
                   our where cause */
    public $idWhere = array();

    /**
    * Create the object
    *
    * @param object &$system  The system object
    * @param object &$table   The table object
    * @param object &$connect The connection manager
    * @param string $driver   The driver to use.  The right one is found if this
    *                         is left null.
    *
    * @return object The driver object
    */
    static public function &factory(
        &$system, &$table, &$connect = null, $driver=null
    ) {
        if (empty($driver)) {
            $driver = "DriverBaseTestStub";
        }
        $driver = "\\HUGnet\\db\\drivers\\".$driver;
        if (!is_a($connect, "ConnectionManager")) {
            $connect = \HUGnet\db\Connection::factory($system);
        }
        $connect->connect();
        $obj = new $driver($system, $table, $connect);
        $obj->qpdo =& $obj->pdo();
        return $obj;
    }
    /**
    * Gets columns from a SQLite server
    *
    * @return null
    */
    public function columns()
    {
        $columns = $this->query("PRAGMA table_info(".$this->table().")");
        $cols = array();
        if (is_array($columns)) {
            foreach ($columns as $col) {
                $cols[$col["name"]] = array(
                    "Name" => $col["name"],
                    "Type" => $col["type"],
                    "Default" => $col["dflt_value"],
                    "Null" => !(bool)$col["notnull"],
                );
            }
        }
        return (array)$cols;
    }
    /**
    * Gets indexes from a SQLite server
    *
    * @return null
    */
    public function indexes()
    {
        $indexes = $this->query("PRAGMA index_list(".$this->table().")");
        $inds = array();
        if (is_array($indexes)) {
            foreach ($indexes as $key) {
                $name = $key["name"];
                if (substr($name, 0, 16) !== "sqlite_autoindex") {
                    // Get info on this index
                    $info = $this->query("PRAGMA index_info(".$name.")");
                    foreach ($info as $ind) {
                        $seq = $ind["seqno"];
                        if (!is_array($inds[$name])) {
                            $inds[$name] = array(
                                "Name" => $name,
                                "Unique" => (bool)$key["unique"],
                                "Columns" => array($seq => $ind["name"]),
                            );
                        } else {
                            $inds[$name]["Columns"][$seq] = $ind["name"];
                        }
                    }
                }
            }
        }
        return (array)$inds;
    }
    /**
    * This gets a new PDO object
    *
    * @return null
    */
    public function connect()
    {
        return parent::connect();
    }
    /**
    * Checks the database table, repairs and optimizes it
    *
    * @param bool $force Force the repair
    *
    * @return mixed
    */
    public function check($force = false)
    {
        return true;
    }
    /**
    * Locks the table
    *
    * @return mixed
    */
    public function lock()
    {
        return true;
    }
    /**
    * Unlocks the table
    *
    * @return mixed
    */
    public function unlock()
    {
        return true;
    }
    /**
    * Get the names of all the tables in the current database
    *
    * @return array of table names
    */
    public function tables()
    {
        return array();
    }
}
?>
