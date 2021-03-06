<?php
/**
 * Tests the light sensor class
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

namespace HUGnet\db\drivers;
/** This is a required class */
require_once CODE_BASE.'/system/System.php';
/** This is a required class */
require_once CODE_BASE.'/system/Error.php';
/** This is a required class */
require_once CODE_BASE.'/db/Driver.php';
/** This is a required class */
require_once CODE_BASE.'/db/drivers/Sqlite.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is a required class */
require_once 'PHPUnit/Extensions/Database/TestCase.php';

/**
 * Test class for sensor.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:08:37.
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
class SqliteTest extends \PHPUnit_Extensions_Database_TestCase
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
        $this->connect = \HUGnet\db\Connection::factory($this->system);
        $this->pdo = &$this->connect->getDBO("default");
        $this->pdo->query("DROP TABLE IF EXISTS `myTable`");
        $this->pdo->query(
            "CREATE TABLE `myTable` ("
            ." `id` int(11) PRIMARY KEY NOT NULL,"
            ." `name` varchar(32) NOT NULL,"
            ." `value` float NULL"
            ." )"
        );
        parent::setUp();
        $this->table = new \HUGnet\DummyTable();
        $this->o = Sqlite::factory(
            $this->system, $this->table, $this->connect, "Sqlite"
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
        $this->pdo->query("DROP TABLE IF EXISTS `myTable`");
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
            TEST_CONFIG_BASE.'files/HUGnetDBDriverTest.xml'
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
        if (!empty($preload)) {
            $this->pdo->query($preload);
        }
        $cols = $this->o->columns();
        $this->assertSame($expect, $cols);
    }
    /**
    * Data provider for testFindUnit
    *
    * @return array
    */
    public static function dataIndexes()
    {
        return array(
            array(
                "",
                array(
                ),
            ),
            array(
                "CREATE INDEX iddate ON `myTable` (id, name)",
                array(
                    'iddate' => array(
                        'Name' => 'iddate',
                        'Unique' => false,
                        'Columns' => array('id', 'name'),
                    ),
                ),
            ),
            array(
                "CREATE UNIQUE INDEX iddate ON `myTable` (id, name)",
                array(
                    'iddate' => array(
                        'Name' => 'iddate',
                        'Unique' => true,
                        'Columns' => array('id', 'name'),
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
     * @dataProvider dataIndexes
     */
    public function testIndexes($preload, $expect)
    {
        if (!empty($preload)) {
            $this->pdo->query($preload);
        }
        $cols = $this->o->indexes();
        $this->assertSame($expect, $cols);
    }
    /**
    * test the lock routine.
    *
    * @return null
    */
    public function testLock()
    {
        $this->assertTrue($this->o->lock());
        $this->o->unlock;
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
        $this->assertTrue($this->o->check());
    }
    /**
    * test the check routine.
    *
    * @return null
    */
    public function testTables()
    {
        $this->assertSame(array("myTable" => "myTable"), $this->o->tables());
    }
}

?>
