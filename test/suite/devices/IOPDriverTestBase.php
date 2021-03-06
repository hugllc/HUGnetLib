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
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';

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
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class IOPDriverTestBase extends \PHPUnit_Framework_TestCase
{
    /** This is the class we are testing */
    protected $class = "";
    /** This is the object we are testing */
    protected $o = null;

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
        unset($this->input);
        unset($this->o);
    }

    /**
    * data provider for testType
    *
    * @return array
    */
    public static function dataInternalType()
    {
        return array(
            array("longName", "string"),
            array("shortName", "string"),
            array("extraText", "array"),
            array("extraValues", "array"),
            array("extraDefault", "array"),
            array("extraDesc", "array"),
            array("extraNames", "array"),
        );
    }
    /**
    * Check the variable type
    *
    * @param string $field The field to check
    * @param string $type  The type it should be
    *
    * @return null
    *
    * @dataProvider dataInternalType
    */
    final public function testInternalType($field, $type)
    {
        $name = $this->o->get($field, 1);
        $this->assertInternalType($type, $name, "$field must be a $type");
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    public static function dataStringSize()
    {
        return array(
            array("longName", 40, 10),
            array("shortName", 15, 1),
        );
    }
    /**
    * Check the string size
    *
    * @param string $field The field to check
    * @param int    $max   The largest it can be
    * @param int    $min   The smallest it can be
    *
    * @return null
    *
    * @dataProvider dataStringSize
    */
    final public function testStringMaxSize($field, $max, $min)
    {
        $name = (string)$this->o->get($field, 1);
        $this->assertLessThanOrEqual(
            $max, strlen($name), "$field must be less than $max chars"
        );
    }
    /**
    * Check the string size
    *
    * @param string $field The field to check
    * @param int    $max   The largest it can be
    * @param int    $min   The smallest it can be
    *
    * @return null
    *
    * @dataProvider dataStringSize
    */
    final public function testStringMinSize($field, $max, $min)
    {
        $name = (string)$this->o->get($field, 1);
        $this->assertGreaterThanOrEqual(
            $min, strlen($name), "$field must be more than $min characters"
        );
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    public static function dataStringRegex()
    {
        return array(
            array(null, null),
        );
    }
    /**
    * Check the string size
    *
    * @param string $field The field to check
    * @param string $regex The regular expression
    *
    * @return null
    *
    * @dataProvider dataStringRegex
    */
    final public function testStringRegex($field, $regex)
    {
        if (!is_null($field)) {
            $value   = $this->o->get($field);
            $matches = array();
            preg_match_all($regex, $value, $matches);
            $match = implode("", $matches[0]);
            $this->assertSame(
                $match,
                $value,
                "$field must follow regex $regex"
                ."('$value' should be '$match')"
            );
        }
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    public static function dataIntSize()
    {
        return array(
            array(null, null, null),
        );
    }
    /**
    * Check the string size
    *
    * @param string $field The field to check
    * @param int    $max   The largest it can be
    * @param int    $min   The smallest it can be
    *
    * @return null
    *
    * @dataProvider dataIntSize
    */
    final public function testIntMaxSize($field, $max, $min)
    {
        if (!is_null($max)) {
            $name = (string)$this->o->get($field, 1);
            $this->assertLessThanOrEqual(
                $max, $name, "$field must be less than $max chars"
            );
        }
    }
    /**
    * Check the string size
    *
    * @param string $field The field to check
    * @param int    $max   The largest it can be
    * @param int    $min   The smallest it can be
    *
    * @return null
    *
    * @dataProvider dataIntSize
    */
    final public function testIntMinSize($field, $max, $min)
    {
        if (!is_null($min)) {
            $name = (string)$this->o->get($field, 1);
            $this->assertGreaterThanOrEqual(
                $min, $name, "$field must be more than $min characters"
            );
        }
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    public static function dataValues()
    {
        return array(
            array(null, null),
        );
    }
    /**
    * Check the variable type
    *
    * @param string $field  The field to check
    * @param array  $values The type it should be
    *
    * @return null
    *
    * @dataProvider dataValues
    */
    final public function testValues($field, $values)
    {
        if (!is_null($field)) {
            $name = $this->o->get($field, 1);
            $this->assertTrue(
                in_array($name, (array)$values),
                "$field must be one of ".implode(",", (array)$values)
            );
        }
    }

    /**
    * Check the extraText value size
    *
    * @return null
    */
    public function testExtraTextValueSize()
    {
        $size = 26;
        $extra = $this->o->get("extraText", 1);
        $this->assertInternalType("array", $extra);
        foreach ($extra as $key => $value) {
            $this->assertTrue(
                (strlen($value) < $size),
                "extraText[$key] must be less than $size chars"
            );
        }
    }
    /**
    * Check the number of entries in extraText
    *
    * @return null
    */
    public function testExtraTextCount()
    {
        $extra   = (array)$this->o->get("extraText", 1);
        $default = (array)$this->o->get("extraDefault", 1);
        $this->assertSame(
            count($default),
            count($extra),
            "extraText needs to have the same number of entries as extraDefault"
        );
    }
    /**
    * Check the number of entries in extraText
    *
    * @return null
    */
    public function testExtraDescCount()
    {
        $extra   = (array)$this->o->get("extraDesc", 1);
        $default = (array)$this->o->get("extraDefault", 1);
        $this->assertSame(
            count($default),
            count($extra),
            "extraDesc needs to have the same number of entries as extraDefault"
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testExtraValuesElementTypes()
    {
        $size = 26;
        $extra = $this->o->get("extraValues", 1);
        $this->assertInternalType("array", $extra);
        foreach ($extra as $key => $value) {
            $ret = is_null($value);
            $ret = $ret || is_array($value);
            $ret = $ret || is_int($value);
            $this->assertTrue(
                $ret,
                "extraValues[$key] must be null, an array, or an int"
            );
        }
    }
    /**
    * Check the number of entries in extraValues
    *
    * @return null
    */
    public function testExtraValuesCount()
    {
        $extra   = (array)$this->o->get("extraValues", 1);
        $default = (array)$this->o->get("extraDefault", 1);
        $this->assertSame(
            count($default),
            count($extra),
            "extraValues needs to have the same number of entries as extraDefault"
        );
    }
    /**
    * Check the number of entries in extraValues
    *
    * @return null
    */
    public function testExtraNamesValues()
    {
        $names   = (array)$this->o->get("extraNames", 1);
        $default = (array)$this->o->get("extraDefault", 1);
        foreach ($names as $key => $value) {
            $this->assertTrue(
                isset($default[$value]),
                "extraNames must point to a valid key in extraDefault"
                ." $value not in extraDefault.  Name $key "
            );
        }
    }
    /**
    * Check the number of entries in extraValues
    *
    * @return null
    */
    public function testExtraNamesNumeric()
    {
        $names   = (array)$this->o->get("extraNames", 1);
        foreach ($names as $key => $value) {
            $this->assertFalse(
                is_numeric($key),
                "extraNames array keys can not be numeric ($key => $value)"
            );
        }
    }
    /**
    * Check the number of entries in extraValues
    *
    * @return null
    */
    public function testExtraNamesCase()
    {
        $names   = (array)$this->o->get("extraNames", 1);
        foreach ($names as $key => $value) {
            $newkey = preg_replace("/[^a-z0-9]/", "", $key);
            $this->assertTrue(
                $key === $newkey,
                "extraNames keys can only contain numbers and lower case letters"
                ."('$key' should be '$newkey')"
            );
        }
    }
    /**
    * Check the number of entries in extraValues
    *
    * @return null
    */
    public function testUsesArray()
    {
        $this->assertInternalType("array", $this->o->uses());
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGet()
    {
        return array(
            array(
                "ThisIsABadName",
                array(),
                null,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The name of the variable to test.
    * @param array  $mock   The mocks to set up
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet($name, $mock, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mock);
        $this->assertSame($expect, $this->o->get($name));
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataExtra()
    {
        return array(
            array(
                null,
                array(),
                null,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $extra  The name of the variable to test.
    * @param array  $mock   The mocks to set up
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataExtra
    */
    public function testGetExtra($extra, $mock, $expect)
    {
        if (!is_null($extra)) {
            $sensor = new \HUGnet\DummyBase("Sensor");
            $sensor->resetMock($mock);
            $this->assertSame($expect, $this->o->getExtra($extra));
        }
    }
    /**
    * data provider for testProvidesRequires
    *
    * @return array
    */
    public static function dataProvidesRequires()
    {
        return array(
            array("provides"),
            array("requires"),
        );
    }
    /**
    * Check the string size
    *
    * @param string $field The field to check
    *
    * @return null
    *
    * @dataProvider dataProvidesRequires
    */
    final public function testProvidesRequires($field)
    {
        $names  = (array)$this->o->get($field, 1);
        $values = array(
            "CC", "DC", "DO", "DI", "AO", "AI", "HVAI", "FREQ", "ATODREF"
        );
        $index  = 0;
        foreach ($names as $key => $value) {
            $search = array_search($value, $values);
            $this->assertTrue(
                false !== $search,
                "$field must only contain the following values:"
                ."(".implode(", ", $values).") $value is not on the list"
                ." at key $key."
            );
        }
    }
    /**
    * Check the string size
    *
    * @param string $field The field to check
    *
    * @return null
    *
    * @dataProvider dataProvidesRequires
    */
    final public function testProvidesRequires2($field)
    {
        $names  = (array)$this->o->get($field, 1);
        $index  = 0;
        foreach ($names as $key => $value) {
            $this->assertSame(
                $index++, 
                $key, 
                "$field must have incrementing integers"
                ." for keys starting at 0.  $key => $value is the first problem."
            );
        }
    }
}
?>
