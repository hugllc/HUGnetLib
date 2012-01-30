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
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteContainers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** This is the stuff we need */
require_once CODE_BASE.'containers/PeriodContainer.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTableContainer.php';
/** This is a required class */
require_once 'PHPUnit/Extensions/Database/TestCase.php';

/**
 * Test class for PeriodContainer
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteContainers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PeriodContainerTest extends PHPUnit_Extensions_Database_TestCase
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
            "servers" => array(
                array(
                    "driver" => "sqlite",
                    "file" => ":memory:",
                    "group" => "default",
                ),
                array(
                    "driver" => "sqlite",
                    "file" => ":memory:",
                    "group" => "Hello",
                ),
            ),
        );
        $this->myConfig = &ConfigContainer::singleton();
        $this->myConfig->forceConfig($config);
        $this->pdo = &$this->myConfig->servers->getPDO("default");
        $this->pdo->query("DROP TABLE IF EXISTS `myTable`");
        $this->o = new PeriodContainer();
        $this->table = new DummyTableContainer();
        $this->table->create();
        parent::setUp();
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
        unset($this->o);
        parent::tearDown();
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
            TEST_CONFIG_BASE.'files/PeriodContainerTest.xml'
        );
    }
    /**
    * data provider for testSet
    *
    * @return array
    */
    public static function dataFromArray()
    {
        return array(
            array(
                // All records in range
                array(
                    "group" => "Hello",
                    "start" => 2,
                    "end"   => 6,
                    "dateField" => "value",
                    "class" => "DummyTableContainer",
                    "records" => array(
                        new DummyTableContainer(
                            array("id" => 2, "name" => "fdsa", "value" => 4)
                        ),
                        array("id" => 3, "name" => "jkl", "value" => 6),
                        array("id" => 1, "name" => "asdf", "value" => 2),
                        new PeriodContainer(),
                    ),
                ),
                "DummyTableContainer",
                3,
                array(
                    "group" => "Hello",
                    "start" => 2,
                    "end"   => 6,
                    "class" => "DummyTableContainer",
                    "dateField" => "value",
                    "records" => array(
                        2 => array("id" => 1, "name" => "asdf", "value" => 2),
                        4 => array("id" => 2, "name" => "fdsa", "value" => 4),
                        6 => array("id" => 3, "name" => "jkl", "value" => 6),
                    ),
                ),
            ),
            array(
                // Some records in range
                array(
                    "group" => "default",
                    "start" => 2,
                    "end"   => 4,
                    "class" => "DummyTableContainer",
                    "dateField" => "value",
                    "records" => array(
                        array("id" => 1, "name" => "asdf", "value" => 2),
                        array("id" => 2, "name" => "fdsa", "value" => 4),
                        array("id" => 3, "name" => "jkl", "value" => 6),
                    ),
                ),
                "DummyTableContainer",
                2,
                array(
                    "group" => "default",
                    "start" => 2,
                    "end"   => 4,
                    "class" => "DummyTableContainer",
                    "dateField" => "value",
                    "records" => array(
                        2 => array("id" => 1, "name" => "asdf", "value" => 2),
                        4 => array("id" => 2, "name" => "fdsa", "value" => 4),
                    ),
                ),
            ),
            array(
                // No records in range
                array(
                    "class" => "DummyTableContainer",
                    "dateField" => "value",
                    "records" => array(
                        array("id" => 1, "name" => "asdf", "value" => 2),
                        array("id" => 2, "name" => "fdsa", "value" => 4),
                        array("id" => 3, "name" => "jkl", "value" => 6),
                    ),
                ),
                "DummyTableContainer",
                0,
                array(
                    "group" => "default",
                    "start" => 0,
                    "end"   => 0,
                    "class" => "DummyTableContainer",
                    "dateField" => "value",
                    "records" => array(
                    ),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The data to preload into the object
    * @param string $class   The name of the expected class
    * @param int    $records The number of records to expect
    * @param mixed  $expect  The expected data set
    *
    * @return null
    *
    * @dataProvider dataFromArray
    */
    public function testFromArray($preload, $class, $records, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $this->assertSame($records, count($this->o->records), "Wrong record count");
        foreach (array_keys($expect["records"]) as $key) {
            $this->assertSame(
                $class, get_class($this->o->records[$key]), "$key has wrong class"
            );
        }
        $this->assertSame($expect, $this->o->toArray(), "Array is wrong");
    }

    /**
    * data provider for testSet
    *
    * @return array
    */
    public static function dataCall()
    {
        return array(
            array(array(), "bugusFunction", "here", false),
            array(
                array(
                    "group" => "default",
                    "start" => 2,
                    "end"   => 6,
                    "class" => "DummyTableContainer",
                    "dateField" => "value",
                    "records" => array(
                        2 => array("id" => 1, "name" => "asdf", "value" => 2),
                        4 => array("id" => 2, "name" => "fdsa", "value" => 4),
                        6 => array("id" => 3, "name" => "jkl", "value" => 6),
                    ),
                ),
                "someFunction",
                null,
                array(
                    2 => array("id" => 1, "name" => "asdf", "value" => 2),
                    4 => array("id" => 2, "name" => "fdsa", "value" => 4),
                    6 => array("id" => 3, "name" => "jkl", "value" => 6),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The data to preload into the object
    * @param string $name    The function to call
    * @param mixed  $value   The value to set
    * @param mixed  $expect  The expected data set
    *
    * @return null
    *
    * @dataProvider dataCall
    */
    public function testCall($preload, $name, $value, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $ret = $this->o->$name($value);
        $this->assertSame($expect, $ret);
    }

    /**
    * data provider for testStartEnd
    *
    * @return array
    */
    public static function dataStartEnd()
    {
        return array(
            array(
                // Remove the top half
                array(
                    "start" => 2,
                    "end"   => 15,
                    "dateField" => "value",
                    "class" => "DummyTableContainer",
                    "records" => array(
                        2 => array("id" => 1, "name" => "asdf", "value" => 2),
                        4 => array("id" => 2, "name" => "fdsa", "value" => 4),
                        6 => array("id" => 3, "name" => "jkl", "value" => 6),
                        8 => array("id" => 4, "name" => "asdf", "value" => 8),
                        10 => array("id" => 5, "name" => "fdsa", "value" => 10),
                        12 => array("id" => 6, "name" => "jkl", "value" => 12),
                    ),
                ),
                2,
                6,
                array(
                    "group" => "default",
                    "start" => 2,
                    "end"   => 6,
                    "class" => "DummyTableContainer",
                    "dateField" => "value",
                    "records" => array(
                        2 => array("id" => 1, "name" => "asdf", "value" => 2),
                        4 => array("id" => 2, "name" => "fdsa", "value" => 4),
                        6 => array("id" => 3, "name" => "jkl", "value" => 6),
                    ),
                ),
            ),
            array(
                // Remove the bottom half
                array(
                    "start" => 2,
                    "end"   => 15,
                    "dateField" => "value",
                    "class" => "DummyTableContainer",
                    "records" => array(
                        2 => array("id" => 1, "name" => "asdf", "value" => 2),
                        4 => array("id" => 2, "name" => "fdsa", "value" => 4),
                        6 => array("id" => 3, "name" => "jkl", "value" => 6),
                        8 => array("id" => 4, "name" => "asdf", "value" => 8),
                        10 => array("id" => 5, "name" => "fdsa", "value" => 10),
                        12 => array("id" => 6, "name" => "jkl", "value" => 12),
                    ),
                ),
                7,
                15,
                array(
                    "group" => "default",
                    "start" => 7,
                    "end"   => 15,
                    "class" => "DummyTableContainer",
                    "dateField" => "value",
                    "records" => array(
                        8 => array("id" => 4, "name" => "asdf", "value" => 8),
                        10 => array("id" => 5, "name" => "fdsa", "value" => 10),
                        12 => array("id" => 6, "name" => "jkl", "value" => 12),
                    ),
                ),
            ),
            array(
                // Remove the middle
                array(
                    "start" => 2,
                    "end"   => 15,
                    "dateField" => "value",
                    "class" => "DummyTableContainer",
                    "records" => array(
                        2 => array("id" => 1, "name" => "asdf", "value" => 2),
                        4 => array("id" => 2, "name" => "fdsa", "value" => 4),
                        6 => array("id" => 3, "name" => "jkl", "value" => 6),
                        8 => array("id" => 4, "name" => "asdf", "value" => 8),
                        10 => array("id" => 5, "name" => "fdsa", "value" => 10),
                        12 => array("id" => 6, "name" => "jkl", "value" => 12),
                    ),
                ),
                5,
                9,
                array(
                    "group" => "default",
                    "start" => 5,
                    "end"   => 9,
                    "class" => "DummyTableContainer",
                    "dateField" => "value",
                    "records" => array(
                        6 => array("id" => 3, "name" => "jkl", "value" => 6),
                        8 => array("id" => 4, "name" => "asdf", "value" => 8),
                    ),
                ),
            ),

        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The data to preload into the object
    * @param int   $start   The start of the time
    * @param int   $end     The end of the time
    * @param mixed $expect  The expected data set
    *
    * @return null
    *
    * @dataProvider dataStartEnd
    */
    public function testStartEnd($preload, $start, $end, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $this->o->start = $start;
        $this->o->end = $end;
        $this->assertSame($expect, $this->o->toArray(), "Array is wrong");
    }

    /**
    * data provider for testGetRecords
    *
    * @return array
    */
    public static function dataGetRecords()
    {
        return array(
            array(
                // Remove the top half
                array(
                    "start" => 2,
                    "end"   => 15,
                    "dateField" => "value",
                    "class" => "DummyTableContainer",
                ),
                null,
                null,
                array(
                    "group" => "default",
                    "start" => 2,
                    "end"   => 15,
                    "class" => "DummyTableContainer",
                    "dateField" => "value",
                    "records" => array(
                        "2.0" => array("id" => "2", "name" => "b", "value" => "2.0"),
                        "3.0" => array("id" => "3", "name" => "c", "value" => "3.0"),
                        "4.0" => array("id" => "4", "name" => "d", "value" => "4.0"),
                        "5.0" => array("id" => "5", "name" => "e", "value" => "5.0"),
                        "6.0" => array("id" => "6", "name" => "f", "value" => "6.0"),
                        "7.0" => array("id" => "7", "name" => "g", "value" => "7.0"),
                        "8.0" => array("id" => "8", "name" => "h", "value" => "8.0"),
                        "9.0" => array("id" => "9", "name" => "i", "value" => "9.0"),
                        "10.0" => array("id"=>"10", "name"=>"j", "value"=>"10.0"),
                        "11.0" => array("id"=>"11", "name"=>"k", "value"=>"11.0"),
                        "12.0" => array("id"=>"12", "name"=>"l", "value"=>"12.0"),
                        "13.0" => array("id"=>"13", "name"=>"m", "value"=>"13.0"),
                        "14.0" => array("id"=>"14", "name"=>"n", "value"=>"14.0"),
                        "15.0" => array("id"=>"15", "name"=>"o", "value"=>"15.0"),
                    ),
                ),
            ),

        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The data to preload into the object
    * @param int   $start   The start of the time
    * @param int   $end     The end of the time
    * @param mixed $expect  The expected data set
    *
    * @return null
    *
    * @dataProvider dataGetRecords
    */
    public function testGetRecords($preload, $start, $end, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $this->o->getPeriod($start, $end);
        $this->assertSame($expect, $this->o->toArray(), "Array is wrong");
    }

    /**
    * data provider for testGetRecords
    *
    * @return array
    */
    public static function dataAutoRetrieve()
    {
        return array(
            array(
                // Remove the top half
                array(
                    "start" => 6,
                    "end"   => 10,
                    "dateField" => "value",
                    "class" => "DummyTableContainer",
                ),
                true,
                2,
                15,
                array(
                    "group" => "default",
                    "start" => 2,
                    "end"   => 15,
                    "class" => "DummyTableContainer",
                    "dateField" => "value",
                    "records" => array(
                        "2.0" => array("id" => "2", "name" => "b", "value" => "2.0"),
                        "3.0" => array("id" => "3", "name" => "c", "value" => "3.0"),
                        "4.0" => array("id" => "4", "name" => "d", "value" => "4.0"),
                        "5.0" => array("id" => "5", "name" => "e", "value" => "5.0"),
                        "6.0" => array("id" => "6", "name" => "f", "value" => "6.0"),
                        "7.0" => array("id" => "7", "name" => "g", "value" => "7.0"),
                        "8.0" => array("id" => "8", "name" => "h", "value" => "8.0"),
                        "9.0" => array("id" => "9", "name" => "i", "value" => "9.0"),
                        "10.0" => array("id"=>"10", "name"=>"j", "value"=>"10.0"),
                        "11.0" => array("id"=>"11", "name"=>"k", "value"=>"11.0"),
                        "12.0" => array("id"=>"12", "name"=>"l", "value"=>"12.0"),
                        "13.0" => array("id"=>"13", "name"=>"m", "value"=>"13.0"),
                        "14.0" => array("id"=>"14", "name"=>"n", "value"=>"14.0"),
                        "15.0" => array("id"=>"15", "name"=>"o", "value"=>"15.0"),
                    ),
                ),
            ),

        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The data to preload into the object
    * @param bool  $set     Whether the autoloader is on or off
    * @param int   $start   The start of the time
    * @param int   $end     The end of the time
    * @param mixed $expect  The expected data set
    *
    * @return null
    *
    * @dataProvider dataAutoRetrieve
    */
    public function testAutoRetrieve($preload, $set, $start, $end, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $this->o->autoRetrieve($set);
        $this->o->start = $start;
        $this->o->end   = $end;
        $this->assertSame($expect, $this->o->toArray(), "Array is wrong");
    }

}

?>
