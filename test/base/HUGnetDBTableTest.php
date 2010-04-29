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

require_once dirname(__FILE__).'/../../base/HUGnetDBTable.php';
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
class HUGnetDBTableTest extends PHPUnit_Extensions_Database_TestCase
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
        $this->myConfig = &ConfigContainer::singleton();
        $config = array(
        );
        $this->myConfig->forceConfig($config);
        $this->pdo = &$this->myConfig->servers->getPDO();
        $this->pdo->query("DROP TABLE IF EXISTS `myTable`");
        $this->pdo->query(
            "CREATE TABLE `myTable` ("
            ." `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,"
            ." `name` varchar(32) NOT NULL,"
            ." `value` float NULL"
            ." )"
        );
        parent::setUp();
        $this->o = new HUGnetDBTableTestStub();
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
            dirname(__FILE__).'/../files/HUGnetDBDriverTest.xml'
        );
    }
    /**
    * Tests for exceptions
    *
    * @expectedException Exception
    *
    * @return null
    */
    public function testConstructTableExec()
    {
        $config = array(
            "servers" => array(
                array(
                    "driver" => "mysql",
                    "host" => "127.0.0.250",
                ),
            ),
        );
        $this->myConfig->forceConfig($config);
        $o = new HUGnetDBTableTestStub($empty, $this->pdo);
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
                    "myDate" => "",
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
        $o = new HUGnetDBTableTestStub($preload);
        $o->getRow($key);
        if (is_array($expect)) {
            $this->assertSame($expect, $o->toArray());
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
        $o = new HUGnetDBTableTestStub($preload);
        $o->create();
        $myDriver = $this->readAttribute($o, "myDriver");
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
                        "name" => "Something Negative",
                        "value" => "-25.0",
                    ),
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "1",
                        "myDate" => "1970-01-01 00:00:00",
                        "name" => "Something Here",
                        "value" => "25.0",
                    ),
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "2",
                        "myDate" => "1970-01-01 00:00:00",
                        "name" => "Another THing",
                        "value" => "22.0",
                    ),
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "32",
                        "myDate" => "1970-01-01 00:00:00",
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
        $o = new HUGnetDBTableTestStub($preload);
        $res = $o->select($where, $data);
        foreach ($res as $val) {
            $ret[] = $val->toArray();
        }
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testSelectInto
    *
    * @return array
    */
    public static function dataSelectInto()
    {
        return array(
            array(
                array(),
                "",
                array(),
                array(
                    "group" => "default",
                    "fluff" => "nStuff",
                    "other" => "things",
                    "id" => "-5",
                    "myDate" => "",
                    "name" => "Something Negative",
                    "value" => "-25.0",
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
    * @dataProvider dataSelectInto
    *
    * @return null
    */
    public function testSelectInto($preload, $where, $data, $expect)
    {
        $o = new HUGnetDBTableTestStub($preload);
        $res = $o->selectInto($where, $data);
        $this->assertSame($expect, $o->toArray());
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
        $o = new HUGnetDBTableTestStub($preload);
        $o->updateRow();
        $stmt = $this->pdo->query("SELECT * FROM `myTable`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $o = new HUGnetDBTableTestStub($preload);
        $o->insertRow($replace);
        $stmt = $this->pdo->query("SELECT * FROM `myTable`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $o = new HUGnetDBTableTestStub($preload);
        $o->deleteRow();
        $stmt = $this->pdo->query("SELECT * FROM `myTable`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    "value" => new HUGnetDBTableTestStub(),
                ),
                array(
                    "id" => 10,
                    "name" => "This is a name",
                    "value" => "YTo3OntzOjU6Imdyb3VwIjtzOjc6ImRlZmF1bHQiO3M6NToiZ"
                        ."mx1ZmYiO3M6NjoiblN0dWZmIjtzOjU6Im90aGVyIjtzOjY6InRoaW5n"
                        ."cyI7czoyOiJpZCI7aTo1O3M6NjoibXlEYXRlIjtzOjA6IiI7czo0OiJu"
                        ."YW1lIjtzOjQ6Ik5hbWUiO3M6NToidmFsdWUiO2Q6MTI7fQ==",
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
        $o = new HUGnetDBTableTestStub($preload);
        $ret = $o->toDB($default);
        $this->assertSame(
            $expect,
            $ret
        );
    }
    /**
    * data provider for testFactory
    *
    * @return array
    */
    public static function dataFactory()
    {
        return array(
            array(
                array(
                ),
                array(
                ),
                array(
                    "group" => "default",
                    "fluff" => "nStuff",
                    "other" => "things",
                    "id" => 5,
                    "myDate" => "1970-01-01 00:00:00",
                    "name" => "Name",
                    "value" => 12.0,
                ),
            ),
            array(
                array(
                    "fluff" => "more",
                    "other" => "thing",
                    "id" => 7,
                    "name" => "here",
                    "value" => 35.0,
                ),
                array(
                    "fluff" => "things",
                    "other" => "nStuff",
                    "id" => 6,
                    "name" => "Obi-wan",
                    "value" => 325.0,
                ),
                array(
                    "group" => "default",
                    "fluff" => "things",
                    "other" => "nStuff",
                    "id" => 6,
                    "myDate" => "1970-01-01 00:00:00",
                    "name" => "Obi-wan",
                    "value" => 325.0,
                ),
            ),
            array(
                array(
                    "fluff" => "more",
                    "other" => "thing",
                    "id" => 7,
                    "name" => "here",
                    "value" => 35.0,
                ),
                array(
                    "name" => "Obi-wan",
                    "value" => 325.0,
                ),
                array(
                    "group" => "default",
                    "fluff" => "more",
                    "other" => "thing",
                    "id" => 7,
                    "myDate" => "1970-01-01 00:00:00",
                    "name" => "Obi-wan",
                    "value" => 325.0,
                ),
            ),
        );
    }
    /**
    * tests the factory
    *
    * @param array $preload What to preload the object with
    * @param array $load    What to load in the second object
    * @param array $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataFactory
    */
    public function testFactory($preload, $load, $expect)
    {
        $o = new HUGnetDBTableTestStub($preload);
        $ret = &$o->factory($load);
        $this->assertAttributeSame(
            $expect,
            "data",
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
            array("myDate", "2010-04-25 13:42:23", "2010-04-25 13:42:23"),
            array("myDate", "2010-04-25", "2010-04-25 00:00:00"),
            array(
                "myDate", "Sun, 25 April 2010, 1:42:23pm", "2010-04-25 13:42:23"
            ),
            array("myDate", 1234567890, "2009-02-13 17:31:30"),
            array("myDate", "This is not a date", "1970-01-01 00:00:00"),
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
        $this->o->$var = $value;
        $data = $this->readAttribute($this->o, "data");
        $this->assertSame($expect, $data[$var]);
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
class HUGnetDBTableTestStub extends HUGnetDBTable
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
        "myDate" => "",
    );
    /**
    * function to set To
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setMyDate($value)
    {
        $this->data["myDate"] = self::sqlDate($value);
    }



}
?>
