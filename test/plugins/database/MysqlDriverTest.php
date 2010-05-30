<?php
/**
 * Tests the light sensor class
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
 * @category   Sensors
 * @package    HUGnetLibTest
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

require_once dirname(__FILE__).'/../../../plugins/database/MysqlDriver.php';
require_once dirname(__FILE__).'/../../stubs/DummyTableContainer.php';
require_once 'PHPUnit/Extensions/Database/TestCase.php';

/**
 * Test class for sensor.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:08:37.
 *
 * @category   Sensors
 * @package    HUGnetLibTest
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class MysqlDriverTest extends PHPUnit_Extensions_Database_TestCase
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
        $this->skipPDOTests = false;
        try {
            $this->pdo = PHPUnit_Util_PDO::factory(
                "mysql://test:test@localhost/test"
            );
            $this->pdo->query("DROP TABLE IF EXISTS `myTable`");
            $this->pdo->query(
                "CREATE TABLE `myTable` ("
                ." `id` int(11) PRIMARY KEY NOT NULL,"
                ." `name` varchar(32) NOT NULL,"
                ." `value` float NULL"
                ." ) TABLESPACE MEMORY;"
            );
        } catch (PDOException $e) {
            $this->skipPDOTests = true;
            $this->pdo = PHPUnit_Util_PDO::factory("sqlite::memory:");
            $this->pdo->query("DROP TABLE IF EXISTS `myTable`");
            $this->pdo->query(
                "CREATE TABLE `myTable` ("
                ." `id` int(11) PRIMARY KEY NOT NULL,"
                ." `name` varchar(32) NOT NULL,"
                ." `value` float NULL"
                ." ) "
            );
        }
        parent::setUp();
        $this->table = new DummyTableContainer();
        $this->o = new MysqlDriver($this->table, $this->pdo);
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
        if (is_object($this->pdo)) {
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
            dirname(__FILE__).'/../../files/HUGnetDBDriverTest.xml'
        );
    }
    /**
    * Data provider for testFindUnit
    *
    * @return array
    */
    public static function dataColumns()
    {
        return array(
            array(
                "",
                array(
                    "id" => array(
                        "Name" => "id",
                        "Type" => "int(11)",
                        "Default" => null,
                        "Null" => false,
                        "Primary" => true,
                        "Unique" => false,
                        "AutoIncrement" => false
                    ),
                    "name" => array(
                        "Name" => "name",
                        "Type" => "varchar(32)",
                        "Default" => null,
                        "Null" => false,
                        "Primary" => false,
                        "Unique" => false,
                        "AutoIncrement" => false
                    ),
                    "value" => array(
                        "Name" => "value",
                        "Type" => "float",
                        "Default" => null,
                        "Null" => true,
                        "Primary" => false,
                        "Unique" => false,
                        "AutoIncrement" => false
                    ),
                ),
            ),
        );
    }
    /**
    * Tests galtol
    *
    * @param string $preload The query to preload the database with
    * @param array  $expect  The expeced return array
    *
    * @return null
    *
    * @dataProvider dataColumns
    */
    public function testColumns($preload, $expect)
    {
        $this->pdo->query($preload);
        $cols = $this->o->columns();
        if ($this->skipPDOTests) {
            $this->markTestSkipped("No MySQL server available");
        } else {
            $this->assertSame($expect, $cols);
        }
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
                    "Default" => 0,
                    "Null" => false,
                    "AutoIncrement" => true,
                    "CharSet" => "",
                    "Collate" => "",
                    "Unsigned" => true,
                    "Unique" => true,
                ),
                "ALTER TABLE `myTable` ADD `ColumnName` int(12) UNSIGNED"
                ." AUTO_INCREMENT PRIMARY KEY NOT NULL DEFAULT '0'"
            ),
            array(
                array(
                    "Name" => "ColumnName",
                    "Type" => "numeric(12)",
                    "Default" => null,
                    "Null" => true,
                    "AutoIncrement" => false,
                    "CharSet" => "",
                    "Collate" => "",
                    "Unsigned" => false,
                    "Unique" => true,
                ),
                "ALTER TABLE `myTable` ADD `ColumnName` numeric(12)"
                ." UNIQUE NULL"
            ),
            array(
                array(
                    "Name" => "ColumnName",
                    "Type" => "set('a', 'b', 'c')",
                    "Default" => "a",
                    "Null" => false,
                    "AutoIncrement" => false,
                    "CharSet" => "asdf",
                    "Collate" => "fdsa",
                    "Unsigned" => false,
                    "Primary" => true,
                ),
                "ALTER TABLE `myTable` ADD `ColumnName` set('a', 'b', 'c')"
                ." CHARACTER SET asdf COLLATE fdsa PRIMARY KEY NOT NULL DEFAULT 'a'"
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
    }
    /**
    * test the lock routine.
    *
    * @return null
    */
    public function testLock()
    {
        $this->assertTrue($this->o->lock());
    }
    /**
    * test the unlock routine.
    *
    * @return null
    */
    public function testUnlock()
    {
        $this->assertTrue($this->o->unlock());
    }
    /**
    * test the check routine.
    *
    * @return null
    */
    public function testCheck()
    {
        if ($this->skipPDOTests) {
            $this->markTestSkipped("No MySQL server available");
        } else {
            $this->assertTrue($this->o->check(true));
        }
    }
    /**
    * Data provider for testAddColumnQuery
    *
    * @return array
    */
    public static function dataTables()
    {
        return array(
            array(
                array(
                    "CREATE TABLE `anotherTable` ("
                    ." `id` int(11) PRIMARY KEY NOT NULL,"
                    ." `name` varchar(32) NOT NULL,"
                    ." `value` float NULL"
                    ." ) TABLESPACE MEMORY;",
                    "CREATE TABLE `myTable2` ("
                    ." `id` int(11) PRIMARY KEY NOT NULL,"
                    ." `name` varchar(32) NOT NULL,"
                    ." `value` float NULL"
                    ." ) TABLESPACE MEMORY;",
                ),
                array(
                    "anotherTable" => "anotherTable",
                    "myTable" => "myTable",
                    "myTable2" => "myTable2",
                ),
                array(
                    "anotherTable" => "anotherTable",
                    "myTable" => "myTable",
                    "myTable2" => "myTable2",
                ),
            ),
        );
    }
    /**
    * Tests tables
    *
    * @param array $queries Array of SQL queryies to send
    * @param array $expect  The expected return value
    * @param array $tables  Tables to drop at the end of it
    *
    * @return null
    *
    * @dataProvider dataTables
    */
    public function testTables($queries, $expect, $tables)
    {
        if ($this->skipPDOTests) {
            $this->markTestSkipped("No MySQL server available");
        } else {
            foreach ((array)$queries as $query) {
                $this->pdo->query($query);
            }
            $cols = $this->o->tables();
            $this->assertSame($expect, $cols);
            foreach ((array)$tables as $table) {
                $this->pdo->query("DROP TABLE `".$table."`");
            }
        }
    }

    /**
    * Tests errorHandler
    *
    * @return null
    */
    /*
    public function testErrorHandler()
    {
        $o = new MysqlDriverTestStub($this->table, $this->pdo);
        $config = &ConfigContainer::singleton();
        $config->servers->connect($this->table->group);
        $this->assertTrue($config->servers->connected($this->table->group));
        $o->errorHandler(
            array(
                "HY000",
                2006,
                "MySQL server has gone away"
            ),
            "test",
            ErrorTable::SEVERITY_WARNING
        );
        $this->assertFalse($config->servers->connected($this->table->group));
    }
    */
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
class MysqlDriverTestStub extends MysqlDriver
{
    /**
    * Register this database object
    *
    * @param object &$table The table object
    * @param PDO    &$pdo   The PDO object
    */
    public function __construct(&$table, PDO &$pdo)
    {
        parent::__construct($table, $pdo);
    }
    /**
    * This function deals with errors
    *
    * @param array  $errorInfo The output of any of the pdo errorInfo() functions
    * @param string $method    The function or method the error was in
    * @param string $severity  The severity of the error.  This should be fed with
    *                          ErrorTable::SEVERITY_WARNING, et al.
    *
    * @return mixed
    */
    public function errorHandler($errorInfo, $method, $severity)
    {
        parent::errorHandler($errorInfo, $method, $severity);
    }
}
?>
