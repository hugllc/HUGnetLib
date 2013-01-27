<?php
/**
 * This runs all of the tests associated with HUGnetLib.
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
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
namespace HUGnet\db;
/** This is a required class */
require_once CODE_BASE.'db/TableBase.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class TableBaseTest extends \PHPUnit_Extensions_Database_TestCase
{
    /** @var object This is our database object */
    protected $pdo;
    /** @var array Default config */
    protected $config;
    /** @var object System Class  */
    protected $system;
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
                        array(
                            "driver" => "sqlite",
                            "file" => ":memory:",
                            "group" => "nonDefault",
                        ),
                    ),
                    "verbose" => 0,
                ),
            ),
        );
        $this->system = new \HUGnet\DummySystem("System");
        $this->system->resetMock($this->config);
        $this->connect = Connection::factory($this->system);
        $this->pdo = &$this->connect->getPDO("default");
        $this->pdo->query("DROP TABLE IF EXISTS `myTable`");
        $this->pdo->query("DROP TABLE IF EXISTS `myTable2`");
        $this->pdo->query(
            "CREATE TABLE `myTable` ("
            ." `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,"
            ." `name` varchar(32) NOT NULL,"
            ." `value` float NULL"
            ." )"
        );
        $this->pdo->query(
            "CREATE TABLE `myTable2` ("
            ." `id` INTEGER NOT NULL,"
            ." `name` varchar(32) NOT NULL,"
            ." `value` float NULL,"
            ." `myOtherDate` int NOT NULL default 0"
            ." )"
        );
        parent::setUp();
        $data = array();
        $this->o = \HUGnet\db\tables\HUGnetDBTableBaseTestStub::factory(
            $this->system, $data, "HUGnetDBTableBaseTestStub", $this->connect
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
        if (is_object($this->pdo)) {
            $this->pdo->query("DROP TABLE IF EXISTS `myTable`");
        }
        $this->o   = null;
        $this->pdo = null;
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
            TEST_CONFIG_BASE.'files/HUGnetDBTableBaseTest.xml'
        );
    }
    /**
    * Tests for exceptions
    *
    * @expectedException RuntimeException
    *
    * @return null
    */
    public function testConstructTableBaseExec()
    {
        $config = $this->config;
        $config["System"]["get"]["servers"] = array(
            array(
                "driver" => "mysql",
                "host" => "127.0.0.250",
            ),
        );
        $this->system->resetMock($config);
        $data = array();
        $obj = \HUGnet\db\tables\HUGnetDBTableBaseTestStub::factory(
            $this->system, $data, "HUGnetDBTableBaseTestStub"
        );
        // This causes it to try to connect to a bad database, and causes
        // the exception.
        $obj->getRow(5);
    }


    /**
    * Data provider for testGetRow
    *
    * @return array
    */
    public static function dataGetRow()
    {
        return array(
            array(
                array(),
                2,
                array(
                    "group" => "default",
                    "fluff" => "nStuff",
                    "other" => "things",
                    "id" => "2",
                    "myDate" => "1970-01-01 00:00:00",
                    "myOtherDate" => 0,
                    "name" => "Another THing",
                    "value" => "22.0",
                ),
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param mixed $key     The key to use
    * @param array $expect  The expected return
    *
    * @dataProvider dataGetRow
    *
    * @return null
    */
    public function testGetRow($preload, $key, $expect)
    {
        $obj = \HUGnet\db\tables\HUGnetDBTableBaseTestStub::factory(
            $this->system, $preload, "HUGnetDBTableBaseTestStub", $this->connect
        );
        $obj->getRow($key);
        if (is_array($expect)) {
            $this->assertSame($expect, $obj->toArray());
        } else {
            $this->assertNull($ret);
        }
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataCreate()
    {
        return array(
            array(
                array(),
                2,
                array(
                    "id" => array(
                        "Name" => "id",
                        "Type" => "INTEGER",
                        "Default" => "'0'",
                        "Null" => false,
                    ),
                    "name" => array(
                        "Name" => "name",
                        "Type" => "VARCHAR",
                        "Default" => "'Name'",
                        "Null" => false,
                    ),
                    "value" => array(
                        "Name" => "value",
                        "Type" => "FLOAT",
                        "Default" => "'12'",
                        "Null" => false,
                    ),
                ),
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param mixed $key     The key to use
    * @param array $expect  The expected return
    *
    * @dataProvider dataCreate
    *
    * @return null
    */
    public function testCreate($preload, $key, $expect)
    {
        $this->pdo->query("DROP TABLE IF EXISTS `myTable`");
        $obj = \HUGnet\db\tables\HUGnetDBTableBaseTestStub::factory(
            $this->system, $preload, "HUGnetDBTableBaseTestStub", $this->connect
        );
        $obj->create();
        $myDriver = $this->readAttribute($obj, "_driver");
        $ret = $myDriver->columns();
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testSelect
    *
    * @return array
    */
    public static function dataSelect()
    {
        return array(
            array(
                array(),
                "",
                array(),
                array(
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "-5",
                        "myDate" => "1970-01-01 00:00:00",
                        "myOtherDate" => 0,
                        "name" => "Something Negative",
                        "value" => "-25.0",
                    ),
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "1",
                        "myDate" => "1970-01-01 00:00:00",
                        "myOtherDate" => 0,
                        "name" => "Something Here",
                        "value" => "25.0",
                    ),
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "2",
                        "myDate" => "1970-01-01 00:00:00",
                        "myOtherDate" => 0,
                        "name" => "Another THing",
                        "value" => "22.0",
                    ),
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "32",
                        "myDate" => "1970-01-01 00:00:00",
                        "myOtherDate" => 0,
                        "name" => "A way up here thing",
                        "value" => "23.0",
                    ),
                ),
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array  $preload The array to preload into the class
    * @param string $where   The where clause
    * @param array  $data    The data to use with the where clause
    * @param array  $expect  The expected return
    *
    * @dataProvider dataSelect
    *
    * @return null
    */
    public function testSelect($preload, $where, $data, $expect)
    {
        $obj = \HUGnet\db\tables\HUGnetDBTableBaseTestStub::factory(
            $this->system, $preload, "HUGnetDBTableBaseTestStub", $this->connect
        );
        $res = $obj->select($where, $data);
        foreach ($res as $val) {
            $ret[] = $val->toArray();
        }
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testCount
    *
    * @return array
    */
    public static function dataCount()
    {
        return array(
            array(
                array(),
                "",
                array(),
                4,
            ),
            array(
                array(),
                "asdf = 'saser'",
                array(),
                false,
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array  $preload The array to preload into the class
    * @param string $where   The where clause
    * @param array  $data    The data to use with the where clause
    * @param mixed  $expect  false on failure, int on success
    *
    * @dataProvider dataCount
    *
    * @return null
    */
    public function testCount($preload, $where, $data, $expect)
    {
        $obj = \HUGnet\db\tables\HUGnetDBTableBaseTestStub::factory(
            $this->system, $preload, "HUGnetDBTableBaseTestStub", $this->connect
        );
        $res = $obj->count($where, $data);
        $this->assertSame($expect, $res);
    }
    /**
    * Tests for verbosity
    *
    * @param array  $preload The array to preload into the class
    * @param string $where   The where clause
    * @param array  $data    The data to use with the where clause
    * @param array  $expect  The expected return
    *
    * @dataProvider dataSelect
    *
    * @return null
    */
    public function testSelectInto($preload, $where, $data, $expect)
    {
        $obj = \HUGnet\db\tables\HUGnetDBTableBaseTestStub::factory(
            $this->system, $preload, "HUGnetDBTableBaseTestStub", $this->connect
        );
        $ret = $obj->selectInto($where, $data);
        foreach ($expect as $e) {
            $this->assertTrue($ret);
            $this->assertSame($e, $obj->toArray());
            $ret = $obj->nextInto();
        }
        $this->assertFalse($ret);

    }
    /**
    * Data provider for testSerialize
    *
    * @return array
    */
    public static function dataSerialize()
    {
        return array(
            array(
                array(
                    "group" => "default",
                    "fluff" => "nStuff",
                    "other" => "things",
                    "id" => "-5",
                    "myDate" => "1970-01-01 00:00:00",
                    "myOtherDate" => 0,
                    "name" => "Something Negative",
                    "value" => "-25.0",
                ),
                array(
                    "group" => "default",
                    "fluff" => "nStuff",
                    "other" => "things",
                    "id" => "-5",
                    "myDate" => "1970-01-01 00:00:00",
                    "myOtherDate" => 0,
                    "name" => "Something Negative",
                    "value" => "-25.0",
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
    * @dataProvider dataSerialize
    *
    * @return null
    */
    public function testSerialize($preload, $expect)
    {
        $obj = \HUGnet\db\tables\HUGnetDBTableBaseTestStub::factory(
            $this->system, $preload, "HUGnetDBTableBaseTestStub", $this->connect
        );
        $data = serialize($obj);
        $obj2 = unserialize($data);
        $this->assertSame(get_class($obj), get_class($obj2), "Class is wrong");
        $this->assertSame($expect, $obj2->toArray(), "Data is wrong");
    }

    /**
    * Data provider for testSelectIDs
    *
    * @return array
    */
    public static function dataSelectIDs()
    {
        return array(
            array(
                array(),
                "",
                array(),
                array(
                    "-5" => "-5", "1" => "1", "2" => "2", "32" => "32"
                ),
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array  $preload The array to preload into the class
    * @param string $where   The where clause
    * @param array  $data    The data to use with the where clause
    * @param array  $expect  The expected return
    *
    * @dataProvider dataSelectIds
    *
    * @return null
    */
    public function testSelectIds($preload, $where, $data, $expect)
    {
        $obj = \HUGnet\db\tables\HUGnetDBTableBaseTestStub::factory(
            $this->system, $preload, "HUGnetDBTableBaseTestStub", $this->connect
        );
        $ret = $obj->selectIDs($where, $data);
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testGetRow
    *
    * @return array
    */
    public static function dataUpdateRow()
    {
        return array(
            array(
                array(
                    "fluff" => "things",
                    "other" => "nStuff",
                    "id" => "2",
                    "name" => "Update Test",
                    "value" => "100.0",
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
                        "name" => "Update Test",
                        "value" => "100.0",
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
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param array $expect  The expected return
    *
    * @dataProvider dataUpdateRow
    *
    * @return null
    */
    public function testUpdateRow($preload, $expect)
    {
        $obj = \HUGnet\db\tables\HUGnetDBTableBaseTestStub::factory(
            $this->system, $preload, "HUGnetDBTableBaseTestStub", $this->connect
        );
        $obj->updateRow();
        $stmt = $this->pdo->query("SELECT * FROM `myTable`");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows);
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
                    "fluff" => "things",
                    "other" => "nStuff",
                    "name" => "Insert Test",
                    "value" => "101.0",
                ),
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
                    array(
                        "id" => "33",
                        "name" => "Insert Test",
                        "value" => "101.0",
                    ),
                ),
            ),
            array(
                array(
                ),
                true,
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
        $obj = \HUGnet\db\tables\HUGnetDBTableBaseTestStub::factory(
            $this->system, $preload, "HUGnetDBTableBaseTestStub", $this->connect
        );
        $obj->insertRow($replace);
        $stmt = $this->pdo->query("SELECT * FROM `myTable`");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows);
    }
    /**
    * Data provider for testDeleteRow
    *
    * @return array
    */
    public static function dataDeleteRow()
    {
        return array(
            // Auto increment
            array(
                array(
                    "fluff" => "things",
                    "other" => "nStuff",
                    "id" => 2,
                    "name" => "Insert Test",
                    "value" => "101.0",
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
                        "id" => "32",
                        "name" => "A way up here thing",
                        "value" => "23.0",
                    ),
                ),
            ),
            array(
                array(
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
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param array $expect  The expected return
    *
    * @dataProvider dataDeleteRow
    *
    * @return null
    */
    public function testDeleteRow($preload, $expect)
    {
        $obj = \HUGnet\db\tables\HUGnetDBTableBaseTestStub::factory(
            $this->system, $preload, "HUGnetDBTableBaseTestStub", $this->connect
        );
        $obj->deleteRow();
        $stmt = $this->pdo->query("SELECT * FROM `myTable`");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows);
    }
    /**
    * data provider for testToDB
    *
    * @return array
    */
    public static function dataToDB()
    {
        return array(
            array(
                array(
                ),
                array(
                    "id" => 5,
                    "name" => "Name",
                    "value" => 12.0,
                ),
            ),
            array(
                array(
                    "id" => 10,
                    "name" => "This is a name",
                    "value" => 1235.932,
                ),
                array(
                    "id" => 10,
                    "name" => "This is a name",
                    "value" => 1235.932,
                ),
            ),
            array(
                array(
                    "id" => 10,
                    "name" => "This is a name",
                    "value" => null,
                ),
                array(
                    "id" => 10,
                    "name" => "This is a name",
                    "value" => 12.0,
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload What to preload the object with
    * @param array $expect  The expected return
    * @param bool  $default Whether to return the default items or not
    *
    * @return null
    *
    * @dataProvider dataToDB
    */
    public function testToDB($preload, $expect, $default = true)
    {
        $obj = \HUGnet\db\tables\HUGnetDBTableBaseTestStub::factory(
            $this->system, $preload, "HUGnetDBTableBaseTestStub"
        );
        $ret = $obj->toDB($default);
        $this->assertSame(
            $expect,
            $ret
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
            array("name", "hello", "hello"),
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
}
namespace HUGnet\db\tables;

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetDBTableBaseTestStub extends \HUGnet\db\TableBase
{
    /** @var string This is the table we should use */
    public $sqlTable = "myTable";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = "id";
    /**
    * @var array This is the definition of the columns
    *
    * This should consist of the following structure:
    * array(
    *   "name" => array(
    *       "Name"          => string The name of the column
    *       "Type"          => string The type of the column
    *       "Default"       => mixed  The default value for the column
    *       "Null"          => bool   true if null is allowed, false otherwise
    *       "AutoIncrement" => bool   true if the column is auto_increment
    *       "CharSet"       => string the character set if the column is text or char
    *       "Collate"       => string colation if the table is text or char
    *       "Primary"       => bool   If we are a primary Key.
    *       "Unique"        => bool   If we are a unique column.
    *   ),
    *   "name2" => array(
    *   .
    *   .
    *   .
    * );
    *
    * Not all fields have to be filled in.  Name and Type are the only required
    * fields.  The index of the base array should be the same as the "Name" field.
    */
    public $sqlColumns = array(
        "id" => array(
            "Name" => "id",
            "Type" => "INTEGER",
            "Default" => 0,
            "AutoIncrement" => true,
        ),
        "name" => array("Name" => "name", "Type" => "varchar", "Default" => "Name"),
        "value" => array("Name" => "value", "Type" => "float", "Default" => 12.0),
    );
    /**
    * @var array This is the definition of the indexes
    *
    *   array(
    *       "Name" => array (
    *           "Name"    => string The name of the index
    *           "Unique"  => bool   Create a Unique index
    *           "Columns" => array  Array of column names
    *       ),
    *       "name2" => array(
    *       .
    *       .
    *   ),
    */
    public $sqlIndexes = array(
        "stuff" => array(
            "Name" => "stuff",
            "Unique" => true,
            "Columns" => array("id", "value"),
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",
        "fluff" => "nStuff",
        "other" => "things",
        "id" => 5,
        "myDate" => "1970-01-01 00:00:00",
        "myOtherDate" => 0,
    );
    /** These are the endpoint information bits */
    /** @var array This is the labels for the data */
    protected $labels = array(
        "id" => "First Column",
        "myDate" => "Next One",
        "myOtherDate" => "Skipped One",
    );
    /** @var This is the output parameters */
    protected $outputParams = array(
        "a" => array("b" => "b"),
        "b" => array("a" => "a"),
        "filters" => array("this is" => "a filter"),
    );
    /**
    * This function creates other tables that are identical to this one, except
    * for the data given.
    *
    * @param object &$system  The system object to use
    * @param mixed  $data     This is an array or string to create the object from
    * @param string $class    The class to use
    * @param object &$connect The connection manager
    *
    * @return object A reference to a table object
    */
    static public function &factory(
        &$system, $data = array(), $class = "Generic", &$connect = null
    ) {
        $class = "\\HUGnet\\db\\tables\\".$class;
        return new $class($system, $data, $connect);
    }
}
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetDBTableBaseTestStub2 extends \HUGnet\db\TableBase
{
    /** @var string This is the table we should use */
    public $sqlTable = "myTable2";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = "id";
    /** @var string This is the date field of the table.  Leave blank if none  */
    public $dateField = "myOtherDate";
    /**
    * @var array This is the definition of the columns
    *
    * This should consist of the following structure:
    * array(
    *   "name" => array(
    *       "Name"          => string The name of the column
    *       "Type"          => string The type of the column
    *       "Default"       => mixed  The default value for the column
    *       "Null"          => bool   true if null is allowed, false otherwise
    *       "AutoIncrement" => bool   true if the column is auto_increment
    *       "CharSet"       => string the character set if the column is text or char
    *       "Collate"       => string colation if the table is text or char
    *       "Primary"       => bool   If we are a primary Key.
    *       "Unique"        => bool   If we are a unique column.
    *   ),
    *   "name2" => array(
    *   .
    *   .
    *   .
    * );
    *
    * Not all fields have to be filled in.  Name and Type are the only required
    * fields.  The index of the base array should be the same as the "Name" field.
    */
    public $sqlColumns = array(
        "id" => array(
            "Name" => "id",
            "Type" => "INTEGER",
            "Default" => 0,
            "AutoIncrement" => true,
        ),
        "name" => array("Name" => "name", "Type" => "varchar", "Default" => "Name"),
        "value" => array("Name" => "value", "Type" => "float", "Default" => 12.0),
        "myOtherDate" => array(
            "Name" => "myOtherDate",
            "Type" => "int",
            "Default" => 0
        ),
    );
    /**
    * @var array This is the definition of the indexes
    *
    *   array(
    *       "Name" => array (
    *           "Name"    => string The name of the index
    *           "Unique"  => bool   Create a Unique index
    *           "Columns" => array  Array of column names
    *       ),
    *       "name2" => array(
    *       .
    *       .
    *   ),
    */
    public $sqlIndexes = array(
        "stuff" => array(
            "Name" => "stuff",
            "Unique" => true,
            "Columns" => array("id", "value", "myOtherDate"),
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",
        "fluff" => "nStuff",
        "other" => "things",
        "id" => 5,
        "myDate" => "1970-01-01 00:00:00",
        "myOtherDate" => 0,
    );
    /**
    * This function creates other tables that are identical to this one, except
    * for the data given.
    *
    * @param object &$system  The system object to use
    * @param mixed  $data     This is an array or string to create the object from
    * @param string $class    The class to use
    * @param object &$connect The connection manager
    *
    * @return object A reference to a table object
    */
    static public function &factory(
        &$system, $data = array(), $class = "Generic", &$connect = null
    ) {
        $class = "\\HUGnet\\db\\tables\\".$class;
        return new $class($system, $data, $connect);
    }
}
?>
