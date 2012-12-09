<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
namespace HUGnet\db;
/** This is a required class */
require_once CODE_BASE.'db/TableDate.php';
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class TableDateTest extends \PHPUnit_Extensions_Database_TestCase
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
        $this->o = \HUGnet\db\TableDate::factory(
            $this->system, $data, "HUGnetDBTableDateTestStub", $this->connect
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
            TEST_CONFIG_BASE.'files/HUGnetDBTableDateTest.xml'
        );
    }
    /**
    * Data provider for testGetPeriod
    *
    * @return array
    */
    public static function dataGetPeriod()
    {
        return array(
            array(
                array(
                ),
                3,
                6,
                32,
                null,
                array(
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "32",
                        "myDate" => "1970-01-01 00:00:00",
                        "myOtherDate" => 4,
                        "name" => "A way up here thing",
                        "value" => "23.0",
                    ),
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "32",
                        "myDate" => "1970-01-01 00:00:00",
                        "myOtherDate" => 5,
                        "name" => "A way up here thing",
                        "value" => "24.0",
                    ),
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "32",
                        "myDate" => "1970-01-01 00:00:00",
                        "myOtherDate" => 6,
                        "name" => "A way up here thing",
                        "value" => "25.0",
                    ),
                ),
            ),
            array(
                array(
                ),
                5,
                6,
                32,
                "id",
                array(
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "32",
                        "myDate" => "1970-01-01 00:00:00",
                        "myOtherDate" => 5,
                        "name" => "A way up here thing",
                        "value" => "24.0",
                    ),
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "32",
                        "myDate" => "1970-01-01 00:00:00",
                        "myOtherDate" => 6,
                        "name" => "A way up here thing",
                        "value" => "25.0",
                    ),
                ),
            ),
            array(
                array(
                ),
                5,
                null,
                32,
                null,
                array(
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "32",
                        "myDate" => "1970-01-01 00:00:00",
                        "myOtherDate" => 5,
                        "name" => "A way up here thing",
                        "value" => "24.0",
                    ),
                ),
            ),
            array(
                array(
                ),
                5,
                6,
                32,
                "id",
                array(
                    array(
                        "group" => "default",
                        "fluff" => "nStuff",
                        "other" => "things",
                        "id" => "32",
                        "myDate" => "1970-01-01 00:00:00",
                        "myOtherDate" => 6,
                        "name" => "A way up here thing",
                        "value" => "25.0",
                    ),
                ),
                "value = ?",
                array(25.0),
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array  $preload    The array to preload into the class
    * @param int    $start      The first date
    * @param int    $end        The last date
    * @param mixed  $key        The key to use
    * @param string $sqlId      The id field to use
    * @param array  $expect     The expected return
    * @param string $extraWhere The extra where clause to use
    * @param array  $extraData  The data for the extra where clause
    *
    * @dataProvider dataGetPeriod
    *
    * @return null
    */
    public function testGetPeriod(
        $preload,
        $start,
        $end,
        $key,
        $sqlId,
        $expect,
        $extraWhere = null,
        $extraData = null
    ) {
        $obj = \HUGnet\db\TableDate::factory(
            $this->system, $preload, "HUGnetDBTableDateTestStub2", $this->connect
        );
        $ret = $obj->getPeriod($start, $end, $key, $sqlId, $extraWhere, $extraData);
        if ($ret !== false) {
            $ret = array();
            do {
                $ret[] = $obj->toArray();
            } while ($obj->nextInto());
        }
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testGetPeriod2
    *
    * @return array
    */
    public static function dataGetPeriod2()
    {
        return array(
            array(
                array(
                ),
                1,
                3,
                2,
                false,
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param int   $start   The first date
    * @param int   $end     The last date
    * @param mixed $key     The key to use
    * @param array $expect  The expected return
    *
    * @dataProvider dataGetPeriod2
    *
    * @return null
    */
    public function testGetPeriod2($preload, $start, $end, $key, $expect)
    {
        $obj = \HUGnet\db\TableDate::factory(
            $this->system, $preload, "HUGnetDBTableDateTestStub", $this->connect
        );
        $ret = $obj->getPeriod($start, $end, $key);
        if ($ret !== false) {
            $ret = array();
            do {
                $ret[] = $obj->toArray();
            } while ($obj->nextInto());
        }
        $this->assertSame($expect, $ret);
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetDBTableDateTestStub extends \HUGnet\db\TableDate
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
    /**
    * function to set To
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setMyOtherDate($value)
    {
        $this->data["myOtherDate"] = self::unixDate($value);
    }
    /**
    * By default it outputs the date in the format specified in myConfig
    *
    * @param string $field The field to output
    *
    * @return string The date as a formatted string
    */
    public function outputDate($field)
    {
        return parent::outputDate($field);
    }
    /**
    * Sets the extra attributes field
    *
    * @param int    $start      The start of the time
    * @param int    $end        The end of the time
    * @param mixed  $rid        The ID to use.  None if null
    * @param string $idField    The ID Field to use.
    * @param string $extraWhere Extra where clause
    * @param array  $extraData  Data for the extraWhere clause
    *
    * @return mixed The value of the attribute
    */
    public function getPeriod(
        $start,
        $end = null,
        $rid = null,
        $idField = null,
        $extraWhere = null,
        $extraData = null
    ) {
        return parent::getTimePeriod(
            $start, $end, $rid, $idField, $extraWhere, $extraData
        );
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetDBTableDateTestStub2 extends \HUGnet\db\TableDate
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
    *       "CharSet"       => string the character set if the column is text
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
    /**
    * function to set To
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setMyOtherDate($value)
    {
        $this->data["myOtherDate"] = self::unixDate($value);
    }
    /**
    * Sets the extra attributes field
    *
    * @param int    $start      The start of the time
    * @param int    $end        The end of the time
    * @param mixed  $rid        The ID to use.  None if null
    * @param string $idField    The ID Field to use.  Table Primary id if left blank
    * @param string $extraWhere Extra where clause
    * @param array  $extraData  Data for the extraWhere clause
    *
    * @return mixed The value of the attribute
    */
    public function getPeriod(
        $start,
        $end = null,
        $rid = null,
        $idField = null,
        $extraWhere = null,
        $extraData = null
    ) {
        return parent::getTimePeriod(
            $start, $end, $rid, $idField, $extraWhere, $extraData
        );
    }

}
?>
