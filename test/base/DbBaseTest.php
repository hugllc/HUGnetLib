<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
// Call DbBaseTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'DbBaseTest::main');
}
/** Test framework */
require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../base/DbBase.php';
require_once dirname(__FILE__).'/../database/databaseTest.php';

/**
 * Test class for DbBase.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DbBaseTest extends databaseTest
{
    /** @var object PDO object */
    protected $pdo;
    
    /**
     * @var    DbBase
     * @access protected
     */
    protected $o;

    /**
     * The name of the table we are using
     */
    protected $table = "DbBaseTest";
    
    /**
     * Runs the test methods of this class.
     *
     * @return none
     *
     * @access public
     * @static
     */
    public static function main()
    {
        include_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite($this->table);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        parent::setUp();
        $this->o = new DbBaseClassTest($this->pdo, $this->table, $this->id);
        $this->o->createTable();
        // Clear out the database
        $this->pdo->query("DELETE FROM ".$this->table);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        if (is_object($this->pdo) && (get_class($this->pdo) == "PDO")) {
            $this->pdo->query("DROP TABLE ".$this->table);
        }
        parent::tearDown();
        unset($this->o);
    }

    /**
     * Tests to make sure this function fails if
     * someone tries to make a cache from a memory
     * sqlite instance.
     *
     * @return none
     */
    public function testCreateCacheMemory() 
    {
        $file = ":MeMoRy:";
        $o = new DbBaseClassTest($file);
        $ret = $o->createCache();
        $this->assertFalse($ret);
        unset($o);
    }


    /**
     * Tests if getColumns works correctly
     *
     * @return none
     */
    public function testGetColumns() 
    {
        $expect = array(
            "id" => "int(11)",
            "name" => "varchar(16)",
            "value" => "text",
        );
        $actual = $this->readAttribute($this->o, "fields");
        $this->assertSame($expect, $actual);
    }

    /**
     * Data provider for testAddArray
     *
     * @return array
     */
    public static function dataAddArray() 
    {
        return array(
            array(
                array(),
                array(
                    array("id" => 1, "name" => "Hi", "value" => "There"),
                ),
                array(
                    array("id" => "1", "name" => "Hi", "value" => "There"),
                ),
            ),
            array(
                array(),
                array(
                    array("id" => 1, "name" => "Hi",        "value" => "There"   ),
                    array("id" => 2, "name" => "This",      "value" => "is"      ),
                    array("id" => 3, "name" => "Eddie",     "value" => "your"    ),
                    array("id" => 4, "name" => "shipboard", "value" => "computer"),
                ),
                array(
                    array("id" => "1", "name" => "Hi",        "value" => "There"   ),
                    array("id" => "2", "name" => "This",      "value" => "is"      ),
                    array("id" => "3", "name" => "Eddie",     "value" => "your"    ),
                    array("id" => "4", "name" => "shipboard", "value" => "computer"),
                ),
            ),
        );
    }
    /**
     * test
     *
     * @return none
     *
     * @dataProvider dataAddArray
     *
     * @param array $preload Data to preload into the database
     * @param array $info    The info to add to the database
     * @param array $expect  The info to expect returned
     */
    public function testAddArray($preload, $info, $expect) 
    {
        $this->load($preload);
        $this->o->addArray($info);
        $ret = $this->getAll();
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testAdd
     *
     * @return array
     */
    public static function dataAdd() 
    {
        return array(
            array(
                array(),
                array("id" => 3, "name" => "Hi", "value" => "There"),
                array("id" => "3", "name" => "Hi", "value" => "There"),
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                ),
                array("id" => 1, "name" => "Hi", "value" => "There"),
                array("id" => "1", "name" => "hello", "value" => "there"),
            ),
        );
    }
    /**
     * test
     *
     * @return none
     *
     * @dataProvider dataAdd
     *
     * @param array $preload Data to preload into the database
     * @param array $info    The info to add to the database
     * @param array $expect  The info to expect returned
     */
    public function testAdd($preload, $info, $expect) 
    {
        $this->load($preload);
        $this->o->add($info);
        $ret = $this->getSingle($expect["id"]);
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testAdd
     *
     * @return array
     */
    public static function dataReplace() 
    {
        return array(
            array(
                array(),
                array("id" => 3, "name" => "Hi", "value" => "There"),
                array("id" => "3", "name" => "Hi", "value" => "There"),
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                ),
                array("id" => 1, "name" => "Bye", "value" => "Now"),
                array("id" => "1", "name" => "Bye", "value" => "Now"),
            ),
        );
    }
    /**
     * test
     *
     * @return none
     *
     * @dataProvider dataReplace
     *
     * @param array $preload Data to preload into the database
     * @param array $info    The info to add to the database
     * @param array $expect  The info to expect returned
     */
    public function testReplace($preload, $info, $expect) 
    {
        $this->load($preload);
        $this->o->replace($info);
        $ret = $this->getSingle($expect["id"]);
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
            array(
                array(),
                array("id" => 3, "name" => "Hi", "value" => "There"),
                3,
                null,
                false,
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                ),
                array("id" => 1, "name" => "Bye", "value" => "Now"),
                1,
                array("id" => "1", "name" => "Bye", "value" => "Now"),
                true,
            ),
        );
    }
    /**
     * test
     *
     * @return none
     *
     * @dataProvider dataUpdate
     *
     * @param array $preload Data to preload into the database
     * @param array $info    The info to add to the database
     * @param int   $key     The database key to get the record from
     * @param array $expect  The info to expect returned
     * @param bool  $retExpect What the function should return
     */
    public function testUpdate($preload, $info, $key, $expect, $retExpect) 
    {
        $this->load($preload);
        $this->o->update($info);
        $ret = $this->getSingle($expect["id"]);
        $this->assertSame($expect, $ret);
    }
    /**
     * test
     *
     * @return none
     *
     * @dataProvider dataUpdate
     *
     * @param array $preload   Data to preload into the database
     * @param array $info      The info to add to the database
     * @param int   $key       The database key to get the record from
     * @param array $expect    The info to expect returned
     * @param bool  $retExpect What the function should return
     */
    /*
    public function testUpdateReturn($preload, $info, $key, $expect, $retExpect) 
    {
        $this->load($preload);
        $ret = $this->o->update($info);
        $this->assertSame($retExpect, $ret);
    }
    */

    /**
     * Data provider for testGetAll
     *
     * @return array
     */
    public static function dataGetAll() 
    {
        return array(
            array(
                array(),
                array(),
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                ),
                array(
                    array("id" => "1", "name" => "hello", "value" => "there"),
                ),
            ),
        );
    }
    /**
     * test
     *
     * @return none
     *
     * @dataProvider dataGetAll
     *
     * @param array $preload Data to preload into the database
     * @param array $expect  The info to expect returned
     */
    public function testGetAll($preload, $expect) 
    {
        $this->load($preload);
        $ret = $this->o->getAll();
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testGet
     *
     * @return array
     */
    public static function dataGet() 
    {
        return array(
            array(
                array(),
                1,
                array(),
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                ),
                1,
                array(
                    array("id" => "1", "name" => "hello", "value" => "there"),
                ),
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                    array("id" => 2, "name" => "I", "value" => "am"),
                    array("id" => 3, "name" => "taking", "value" => "the"),
                    array("id" => 4, "name" => "trouble","value" =>  "to"),
                    array("id" => 5, "name" => "change", "value" => "these"),
                ),
                3,
                array(
                    array("id" => "3", "name" => "taking", "value" => "the"),
                ),
            ),
        );
    }
    /**
     * test
     *
     * @param array $preload Data to preload into the database
     * @param int   $key     The database key to get the record from
     * @param array $expect  The info to expect returned
     *
     * @return none
     *
     * @dataProvider dataGet
     */
    public function testGet($preload, $key, $expect) 
    {
        $this->load($preload);
        $ret = $this->o->get($key);
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testGetWhere
     *
     * @return array
     */
    public static function dataGetWhere() 
    {
        return array(
            array(
                array(),
                "1",
                null,
                array(),
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                ),
                "name = 'hello'",
                null,
                array(
                    array("id" => "1", "name" => "hello", "value" => "there"),
                ),
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                    array("id" => 2, "name" => "I", "value" => "am"),
                    array("id" => 3, "name" => "taking", "value" => "the"),
                    array("id" => 4, "name" => "trouble", "value" => "to"),
                    array("id" => 5, "name" => "change", "value" => "these"),
                ),
                "id = ?",
                array(3),
                array(
                    array("id" => "3", "name" => "taking", "value" => "the"),
                ),
            ),
        );
    }
    /**
     * test
     *
     * @param array  $preload Data to preload into the database
     * @param string $where   The database key to get the record from
     * @param array  $data    The data to send with the query
     * @param array  $expect  The info to expect returned
     *
     * @return none
     *
     * @dataProvider dataGetWhere
     */
    public function testGetWhere($preload, $where, $data, $expect) 
    {
        $this->load($preload);
        $ret = $this->o->getWhere($where, $data);
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testGetWhere
     *
     * @return array
     */
    public static function dataGetOneWhere() 
    {
        return array(
            array(
                array(),
                "1",
                null,
                null,
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                ),
                "name = 'hello'",
                null,
                array("id" => "1", "name" => "hello", "value" => "there"),
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                    array("id" => 2, "name" => "I", "value" => "am"),
                    array("id" => 3, "name" => "taking", "value" => "the"),
                    array("id" => 4, "name" => "trouble", "value" => "to"),
                    array("id" => 5, "name" => "change", "value" => "these"),
                ),
                "id = ?",
                array(3),
                array("id" => "3", "name" => "taking", "value" => "the"),
            ),
        );
    }
    /**
     * test
     *
     * @param array  $preload Data to preload into the database
     * @param string $where   The database key to get the record from
     * @param array  $data    The data to send with the query
     * @param array  $expect  The info to expect returned
     *
     * @return none
     *
     * @dataProvider dataGetOneWhere
     */
    public function testGetOneWhere($preload, $where, $data, $expect) 
    {
        $this->load($preload);
        $ret = $this->o->getOneWhere($where, $data);
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testGetNextID
     *
     * @return array
     */
    public static function dataGetNextID() 
    {
        return array(
            array(
                array(),
                1,
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                ),
                2,
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                    array("id" => 2, "name" => "I", "value" => "am"),
                    array("id" => 3, "name" => "taking", "value" => "the"),
                    array("id" => 4, "name" => "trouble", "value" => "to"),
                    array("id" => 5, "name" => "change", "value" => "these"),
                ),
                6,
            ),
        );
    }
    /**
     * test
     *
     * @param array  $preload Data to preload into the database
     * @param string $where   The database key to get the record from
     * @param array  $data    The data to send with the query
     * @param array  $expect  The info to expect returned
     *
     * @return none
     *
     * @dataProvider dataGetNextID
     */
    public function testGetNextID($preload, $expect) 
    {
        $this->load($preload);
        $ret = $this->o->getNextID();
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for DbBaseTest::testQuery()
     *
     * @return array
     */
    public static function dataQuery() {
        return array(
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                    array("id" => 2, "name" => "I", "value" => "am"),
                    array("id" => 3, "name" => "taking", "value" => "the"),
                    array("id" => 4, "name" => "trouble", "value" => "to"),
                    array("id" => 5, "name" => "change", "value" => "these"),
                ),
                "SELECT * FROM BadTableName WHERE id = 3",
                null,
                true,
                array(),
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                    array("id" => 2, "name" => "I", "value" => "am"),
                    array("id" => 3, "name" => "taking", "value" => "the"),
                    array("id" => 4, "name" => "trouble", "value" => "to"),
                    array("id" => 5, "name" => "change", "value" => "these"),
                ),
                "SELECT * FROM BadTableName WHERE id = ?",
                array(1,2,3,4,5),
                false,
                false,
            ),
            array(
                array(),
                "CREATE TABLE IF NOT EXISTS `oneTestTable` ("
                  ." `id` int(11) NOT null, "
                  ." `name` varchar(16) NOT null default '', "
                  ." `value` text NOT null, "
                  ." PRIMARY KEY  (`id`) "
                  ." );",
                array(),
                false,
                true,
            ),
        );
    }
    /**
     * test
     *
     * @param array  $preload Data to preload into the database
     * @param string $query   The query to perform
     * @param array  $data    The data to send with the query
     * @param bool   $getRet  Whether to expect a return
     * @param array  $expect  The info to expect returned
     *
     * @return none
     *
     * @dataProvider dataQuery().
     */
    public function testQuery($preload, $query, $data, $getRet, $expect) 
    {
        $this->load($preload);
        $ret = $this->o->query($query, $data, $getRet);
        $this->assertSame($expect, $ret);        
    }

    /**
     * Data provider for DbBaseTest::testQueryNoDb()
     *
     * @return array
     */
    public static function dataQueryNoDb() {
        return array(
            array(
                "Hello",
                "SELECT * FROM BadTableName WHERE id = 3",
                null,
                true,
                array(),
            ),
            array(
                null,
                "SELECT * FROM BadTableName WHERE id = ?",
                array(1,2,3,4,5),
                false,
                false,
            ),
            array(
                new databaseTest(),
                "CREATE TABLE IF NOT EXISTS `oneTestTable` ("
                  ." `id` int(11) NOT null, "
                  ." `name` varchar(16) NOT null default '', "
                  ." `value` text NOT null, "
                  ." PRIMARY KEY  (`id`) "
                  ." );",
                array(),
                false,
                false,
            ),
        );
    }
    /**
     * test
     *
     * @param array  $value  The data to kill the db with
     * @param string $query  The query to perform
     * @param array  $data   The data to send with the query
     * @param bool   $getRet Whether to expect a return
     * @param array  $expect The info to expect returned
     *
     * @return none
     *
     * @dataProvider dataQueryNoDb().
     */
    public function testQueryNoDb($value, $query, $data, $getRet, $expect) 
    {
        $this->o->killDb($value);
        $ret = $this->o->query($query, $data, $getRet);
        $this->assertSame($expect, $ret);
        $this->assertSame("NODBE", $this->readAttribute($this->o, "errorState"));
    }

    /**
     * test
     *
     * @param array  $value  The data to kill the db with
     * @param string $query  The query to perform
     * @param array  $data   The data to send with the query
     * @param bool   $getRet Whether to expect a return
     * @param array  $expect The info to expect returned
     *
     * @return none
     *
     * @dataProvider dataQueryNoDb().
     */
    public function testQueryNoDbErrorState($value, $query, $data, $getRet, $expect) 
    {
        $this->o->killDb($value);
        $ret = $this->o->query($query, $data, $getRet);
        $this->assertSame("NODBE", $this->readAttribute($this->o, "errorState"));
    }
    /**
     * test
     *
     * @param array  $value  The data to kill the db with
     * @param string $query  The query to perform
     * @param array  $data   The data to send with the query
     * @param bool   $getRet Whether to expect a return
     * @param array  $expect The info to expect returned
     *
     * @return none
     *
     * @dataProvider dataQueryNoDb().
     */
    public function testQueryNoDbErrorMsg($value, $query, $data, $getRet, $expect) 
    {
        $this->o->killDb($value);
        $ret = $this->o->query($query, $data, $getRet);
        $this->assertSame("Database Not Connected", $this->readAttribute($this->o, "errorMsg"));
    }
    /**
     * test
     *
     * @param array  $value  The data to kill the db with
     * @param string $query  The query to perform
     * @param array  $data   The data to send with the query
     * @param bool   $getRet Whether to expect a return
     * @param array  $expect The info to expect returned
     *
     * @return none
     *
     * @dataProvider dataQueryNoDb().
     */
    public function testQueryNoDbError($value, $query, $data, $getRet, $expect) 
    {
        $this->o->killDb($value);
        $ret = $this->o->query($query, $data, $getRet);
        $this->assertSame(-1, $this->readAttribute($this->o, "error"));
    }

    /**
     * Data provider for DbBaseTest::testQueryCache()
     *
     * @return array
     */
    public static function dataQueryCache() {
        return array(
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                    array("id" => 2, "name" => "I", "value" => "am"),
                    array("id" => 3, "name" => "taking", "value" => "the"),
                    array("id" => 4, "name" => "trouble", "value" => "to"),
                    array("id" => 5, "name" => "change", "value" => "these"),
                ),
                "SELECT * FROM `DbBaseTest` WHERE id = 3",
                null,
                true,
                array(
                    array("id" => "3", "name" => "taking", "value" => "the"),
                ),
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                    array("id" => 2, "name" => "I", "value" => "am"),
                    array("id" => 3, "name" => "taking", "value" => "the"),
                    array("id" => 4, "name" => "trouble", "value" => "to"),
                    array("id" => 5, "name" => "change", "value" => "these"),
                ),
                "SELECT * FROM `DbBaseTest` WHERE id = ?",
                array(1),
                true,
                array(
                    array("id" => "1", "name" => "hello", "value" => "there"),
                ),
            ),
        );
    }
    /**
     * test
     *
     * @param array  $preload Data to preload into the database
     * @param string $query   The query to perform
     * @param array  $data    The data to send with the query
     * @param bool   $getRet  Whether to expect a return
     * @param array  $expect  The info to expect returned
     *
     * @return none
     *
     * @dataProvider dataQueryCache().
     */
    public function testQueryCache($preload, $query, $data, $getRet, $expect) 
    {
        $this->o->createCache();

        // Preload the database
        $this->load($preload);
        // Preload the cache
        $this->o->getAll();
        // Erase what is in the database without touching the cache
        $this->pdo->query("delete from ".$this->table);
        // Query from the database (this should hit the cache)
        $ret = $this->o->query($query, $data, $getRet);

        $this->assertSame($expect, $ret);        
    }

    /**
     * Data provider for testRemove
     *
     * @return array
     */
    public static function dataRemove() 
    {
        return array(
            array(
                array(),
                1,
                array(),
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                ),
                1,
                array(),
            ),
            array(
                array(
                    array("id" => 1, "name" => "hello", "value" => "there"),
                    array("id" => 2, "name" => "I", "value" => "am"),
                    array("id" => 3, "name" => "taking", "value" => "the"),
                    array("id" => 4, "name" => "trouble", "value" => "to"),
                    array("id" => 5, "name" => "change", "value" => "these"),
                ),
                3,
                array(
                    array("id" => "1", "name" => "hello",   "value" => "there"),
                    array("id" => "2", "name" => "I",       "value" => "am"   ),
                    array("id" => "4", "name" => "trouble", "value" => "to"   ),
                    array("id" => "5", "name" => "change",  "value" => "these"),
                ),
            ),
        );
    }
    /**
     * test
     *
     * @return none
     *
     * @dataProvider dataRemove
     *
     * @param array $preload Data to preload into the database
     * @param int   $key     The database key to get the record from
     * @param array $expect  The info to expect returned
     */
    public function testRemove($preload, $key, $expect) 
    {
        $this->load($preload);
        $this->o->remove($key);
        $ret = $this->getAll();
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testRemove
     *
     * @return array
     */
    public static function dataVerbose() 
    {
        return array(
            array(0, 0),
            array(15, 15),
            array(false, 0),
            array(true, 1),
        );
    }
    /**
     * test
     *
     * @return none
     *
     * @dataProvider dataVerbose
     *
     * @param int $val     The database key to get the record from
     * @param int $expect  The info to expect returned
     */
    public function testVerbose($val, $expect) 
    {
        $this->o->verbose($val);
        $this->assertSame($expect, $this->readAttribute($this->o, "verbose"));
    }

    /**
     * test
     *
     * @return none
     */
    public function testIsConnected() 
    {
        $ret = $this->o->isConnected();
        $this->assertTrue($ret);
    }

    /**
     * test
     *
     * @return none
     */
    public function testPrintError() 
    {
        $this->o->verbose(true);
        $this->o->errorState = "ABCDE";
        $this->o->error      = -1;
        $this->o->errorMsg   = "This is an error";
        ob_start();
        $this->o->printError();
        $ret = ob_get_contents();
        ob_end_clean();
        $file   = $this->readAttribute($this->o, "file");
        $class  = get_class($this->o);
        $driver = $this->readAttribute($this->o, "driver");
        $this->assertSame("(".$class." - ".$driver." ".$file.") Error State: ABCDE\n"
                         ."(".$class." - ".$driver." ".$file.") Error: -1\n"
                         ."(".$class." - ".$driver." ".$file.") Error Message: This is an error\n"
                          , $ret);
    }

    /**
     * Tests print out when there is no error
     *
     * @return none
     */
    public function testPrintErrorNone() 
    {
        $this->o->verbose(true);
        $this->o->errorState = "00000";
        $this->o->error      = 0;
        $this->o->errorMsg   = "";
        ob_start();
        $this->o->printError();
        $ret = ob_get_contents();
        ob_end_clean();
        $this->assertSame("", $ret);
    }

    /**
     * tests printout when verbose is off
     *
     * @return none
     */
    public function testPrintErrorNotVerbose() 
    {
        ob_start();
        $this->o->printError();
        $ret = ob_get_contents();
        ob_end_clean();
        $this->assertSame("", $ret);
    }

    /**
     * tests printout when verbose is off
     *
     * @return none
     */
    public function testCreateTable() 
    {
        $this->assertFalse(DbBase::createTable());
    }
    
    /**
     * Data provider for testSqlDate
     *
     * @return array
     */
    public static function dataSqlDate() 
    {
        return array(
            array("2007-12-25 12:13:14", "2007-12-25 12:13:14"),
            array(1523479275, "2018-04-11 15:41:15"),
            array("January 1, 2006 5:42pm", "2006-01-01 17:42:00"),
            array(true, true),
            array(array(), array()),
        );
    }
    /**
     * test
     *
     * @return none
     *
     * @dataProvider dataSqlDate
     *
     * @param mixed $date   The date 
     * @param int   $expect The info to expect returned
     */
    public function testSqlDate($date, $expect) 
    {
        $ret = $this->o->sqlDate($date);
        $this->assertSame($expect, $ret);
    }
    /**
     * Data provider for testFixType
     *
     * @return array
     */
    public static function dataFixType() 
    {
        return array(
            array("1", "Int(11)", 1),
            array("1", "FLoat", 1.0),
            array(1.0, "text(45)", "1"),
            array(1.0, "char(2)", "1"),
            array(1.0, "varchar(16)", "1"),
            array(1.0, "asdf(4)", 1.0),
        );
    }
    /**
     * test
     *
     * @return none
     *
     * @dataProvider dataFixType
     *
     * @param mixed $value  The value to fix 
     * @param mixed $type   The type of SQL column it is from 
     * @param int   $expect The info to expect returned
     */
    public function testFixType($value, $type, $expect) 
    {
        $ret = $this->o->fixType($value, $type);
        $this->assertSame($expect, $ret);
    }

}

// Call DbBaseTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'DbBaseTest::main') {
    DbBaseTest::main();
}

/**
 * Test class for DbBase.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DbBaseClassTest extends DbBase
{
    /**
     * The name of the table we are using
     */
    protected $table = "DbBaseTest";
    /** The number of columns */
    private $_columns = 3;
    
    /**
     * Creates the database table.
     *
     * Must be defined in child classes
     *
     * @return bool
     */
    public function createTable($table="") 
    {
        if (!empty($table)) $this->table = $table;
        $query = "CREATE TABLE IF NOT EXISTS `".$this->table."` (
              `id` int(11) NOT null,
              `name` varchar(16) NOT null default '',
              `value` text NOT null,
              PRIMARY KEY  (`id`)
            );";

        $ret = $this->query($query, false); 
        $this->getColumns();   
    }
    
    /**
     * kills the database so we can test the class when it doesn't ahve a database
     *
     * @param mixed $val The value to kill the database with
     *
     * @return none
     */
    public function killDb($val = null)
    {
        $this->_db = null;
        $this->_db = $val;
    }     
    
}
?>
