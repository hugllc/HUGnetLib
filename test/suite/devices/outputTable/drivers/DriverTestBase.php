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
/** This is the HUGnet namespace */
namespace HUGnet\devices\outputTable\drivers;
/** This is the base class */
require_once CODE_BASE."devices/outputTable/Driver.php";
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is a required class */
require_once CODE_BASE.'util/VPrint.php';

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
abstract class DriverTestBase extends \PHPUnit_Framework_TestCase
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
        unset($this->o);
    }

    /**
    * data provider for testType
    *
    * @return array
    */
    final public static function dataInternalType()
    {
        return array(
            array("longName", "string"),
            array("shortName", "string"),
            array("extraText", "array"),
            array("extraValues", "array"),
            array("extraDefault", "array"),
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
    final public static function dataStringSize()
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
                200,
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
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mock);
        $this->assertSame($expect, $this->o->getExtra($extra));
    }
    /**
     * Data provider for testGetReading
     *
     * testGetReading($sensor, $A, $deltaT, $data, $prev, $expect)
     *
     * @return array
     */
    public static function dataGetReading()
    {
        return array();
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @param array $sensor The sensor data array
    * @param mixed $A      Data for the sensor to work on
    * @param float $deltaT The time differenct
    * @param array $data   The data array being built
    * @param array $prev   The previous record
    * @param mixed $expect The return data to expect
    *
    * @return null
    *
    * @dataProvider dataGetReading()
    */
    public function testGetReading($sensor, $A, $deltaT, $data, $prev, $expect)
    {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock($sensor);
        $ret = $this->o->getReading($A, $deltaT, $data, $prev);
        $this->assertEquals($expect, $ret, 0.00001);
    }
}
?>
