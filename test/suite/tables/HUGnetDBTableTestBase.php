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
 * @subpackage SuiteTables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the packet loss */
require_once CODE_BASE.'tables/PacketLogTable.php';
/** This is the database testing extension */
require_once 'PHPUnit/Extensions/Database/TestCase.php';
/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteTables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class HUGnetDBTableTestBase extends PHPUnit_Extensions_Database_TestCase
{
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
    * test the set routine when an extra class exists
    *
    * @param array  &$obj The object to work with
    * @param string $var  The variable to use on the object
    *
    * @return null
    */
    static public function &splitObject(&$obj, $var)
    {
        $array = array();
        foreach ((array)$obj->$var as $name => $col) {
            $array[] = array(
                &$obj,
                $name,
                $col,
            );
        }
        return $array;
    }
    /**
    * Data provider for testColumns
    *
    * @return array
    */
    abstract public static function dataColumns();
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $obj  The object to work with
    * @param string $name The name of the index
    * @param array  $col  The column array
    *
    * @return null
    *
    * @dataProvider dataColumns
    */
    public function testColumns($obj, $name, $col)
    {
        $this->assertSame(
            $name,
            $col["Name"],
            "Column Name '$name' does not match 'Name' in array ('".$col["Name"]."')"
        );
        $this->assertInternalType(
            "string",
            $col["Name"],
            "In column '$name' Name is not a string"
        );
        $this->assertFalse(
            empty($col["Name"]),
            "In column '$name' Name is empty"
        );
        $this->assertInternalType(
            "string",
            $col["Type"],
            "In column '$name' Type is not a string"
        );
        $this->assertFalse(
            empty($col["Type"]),
            "In column '$name' Type is empty"
        );
        if (!is_null($col["AutoIncrement"])) {
            $this->assertInternalType(
                "bool",
                $col["AutoIncrement"],
                "In column '$name' AutoIncrement is not true, false, or null"
            );
            if ($col["AutoIncrement"]) {
                $this->assertSame(
                    $name,
                    $obj->sqlId,
                    "AutoIncrement Column '$name' should be set in \$obj->sqlId"
                );
            }
        }
        if (!is_null($col["Primary"])) {
            $this->assertInternalType(
                "bool",
                $col["Primary"],
                "In column '$name' Primary is not true, false, or null"
            );
        }
        if (!is_null($col["Unique"])) {
            $this->assertInternalType(
                "bool",
                $col["Unique"],
                "In column '$name' Unique is not true, false, or null"
            );
        }
    }
    /**
    * Data provider for testIndexes
    *
    * @return array
    */
    abstract public static function dataIndexes();
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $obj   The object to work with
    * @param string $name  The name of the index
    * @param array  $index The index array
    *
    * @return null
    *
    * @dataProvider dataIndexes
    */
    public function testIndexes($obj, $name, $index)
    {
        $this->assertSame(
            $name,
            $index["Name"],
            "Index Name '$name' does not match 'Name' in array ('".$col["Name"]."')"
        );
        $this->assertInternalType(
            "string",
            $index["Name"],
            "In index '$name' Name is not a string"
        );
        $this->assertFalse(
            empty($index["Name"]),
            "In index '$name' Name is empty"
        );
        if (!is_null($index["Unique"])) {
            $this->assertInternalType(
                "bool",
                $index["Unique"],
                "In index '$name' Unique is not true, false, or null"
            );
        }
        $this->assertInternalType(
            "array",
            $index["Columns"],
            "In index '$name' Columns is not an array"
        );
        foreach ($index["Columns"] as $col) {
            $c = explode(",", $col);
            $this->assertTrue(
                isset($obj->sqlColumns[$c[0]]),
                "In index '$name' has non-existant column ".$c[0]
            );
            if (!empty($c[1])) {
                $this->assertTrue(
                    is_numeric($c[1]),
                    "Column index length is not numeric"
                );
            }
        }
    }
    /**
    * Data provider for testIndexes
    *
    * @return array
    */
    abstract public static function dataVars();
    /**
    * test the set routine when an extra class exists
    *
    * @param array $obj The object to work with
    *
    * @return null
    *
    * @dataProvider dataVars
    */
    public function testSqlId($obj)
    {
        if (!empty($obj->sqlId)) {
            $this->assertInternalType(
                "string",
                $obj->sqlId,
                "sqlId is not a string"
            );
            $this->assertTrue(
                isset($obj->sqlColumns[$obj->sqlId]),
                "sqlId set to non-existant column ".$obj->sqlId
            );
        }
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $obj The object to work with
    *
    * @return null
    *
    * @dataProvider dataVars
    */
    public function testSqlTable($obj)
    {
        $this->assertInternalType(
            "string",
            $obj->sqlTable,
            "sqlTable is not a string"
        );
        $this->assertFalse(
            empty($obj->sqlTable),
            "sqlTable can not be empty"
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $obj The object to work with
    *
    * @return null
    *
    * @dataProvider dataVars
    */
    public function testGroupVar($obj)
    {
        $default = $this->readAttribute($obj, "default");
        $this->assertSame(
            "default",
            $default["group"],
            '$obj->default["group"] must be set to "default"'
        );
    }
}

?>
