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
namespace HUGnet\devices\inputTable\drivers;
/** This is the base class */
require_once CODE_BASE."/devices/inputTable/Driver.php";
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
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testFactory()
    {
        $this->assertSame(
            "HUGnet\devices\inputTable\drivers\\".$this->class, get_class($this->o)
        );
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
            array("bound", "boolean"),
            array("virtual", "boolean"),
            array("total", "boolean"),
            array("storageUnit", "string"),
            array("maxDecimals", "int"),
            array("unitType", "string"),
            array("dataTypes", "array"),
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
            array("storageUnit", 15, 1),
            array("unitType", 20, 1),
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
    final public static function dataIntSize()
    {
        return array(
            array("maxDecimals", 10, 0),
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
        ///if (!is_null($max)) {
            $name = (string)$this->o->get($field, 1);
            $this->assertLessThanOrEqual(
                $max, $name, "$field must be less than $max chars"
            );
        //}
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
        //if (!is_null($min)) {
            $name = (string)$this->o->get($field, 1);
            $this->assertGreaterThanOrEqual(
                $min, $name, "$field must be more than $min characters"
            );
        //}
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    final public static function dataValues()
    {
        return array(
            array(
                "storageType", array(
                    \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                    \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
                ),
            ),
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
        $name = $this->o->get($field, 1);
        $this->assertTrue(
            in_array($name, (array)$values),
            "$field must be one of ".implode(",", (array)$values)
        );
    }
    /**
    * Check the extraText value size
    *
    * @return null
    */
    public function testDataTypesKeys()
    {
        $validTypes = array(
            \HUGnet\devices\datachan\Driver::TYPE_RAW
                => '\HUGnet\devices\datachan\Driver::TYPE_RAW',
            \HUGnet\devices\datachan\Driver::TYPE_DIFF
                => '\HUGnet\devices\datachan\Driver::TYPE_DIFF',
            \HUGnet\devices\datachan\Driver::TYPE_IGNORE
                => '\HUGnet\devices\datachan\Driver::TYPE_IGNORE',
        );
        $extra = $this->o->get("dataTypes", 1);
        $this->assertInternalType("array", $extra);
        foreach ($extra as $key => $value) {
            $this->assertTrue(
                isset($validTypes[$key]),
                "Valid dataTypes keys are ".implode(", ", $validTypes)
            );
            $this->assertSame(
                $key,
                $value,
                "In dataTypes, key must equal value, ".$validTypes[$key]
                ." != ".$validTypes[$value]
            );
        }
    }
    /**
    * Check the number of entries in extraText
    *
    * @return null
    */
    public function testDataTypesCount()
    {
        $count = 3;
        $extra   = (array)$this->o->get("dataTypes", 1);
        $this->assertLessThanOrEqual(
            $count,
            count($dataTypes),
            "dataTypes must have $count or less entries"
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
     * testGetReading($sensor, $A, $deltaT, $data, $prev, $expect, $channel = 0)
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
    * @param array $sensor  The sensor data array
    * @param mixed $A       Data for the sensor to work on
    * @param float $deltaT  The time differenct
    * @param array $data    The data array being built
    * @param array $prev    The previous record
    * @param mixed $expect  The return data to expect
    * @param int   $channel The channel to test
    *
    * @return null
    *
    * @dataProvider dataGetReading()
    */
    public function testGetReading(
        $sensor, $A, $deltaT, $data, $prev, $expect, $channel = 0
    ) {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock($sensor);
        $ret = $this->o->decodeDataPoint($A, $channel, $deltaT, $prev, $data);
        $this->assertEquals($expect, $ret, 0.00001);
    }
    /**
     * Data provider for testGetReading
     *
     * testGetReading($sensor, $A, $deltaT, $data, $prev, $expect, $channel = 0)
     *
     * @return array
     */
    public static function dataEncodeDataPoint()
    {
        return array();
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @param array $sensor  The sensor data array
    * @param mixed $expect  Data for the sensor to work on
    * @param float $deltaT  The time differenct
    * @param array $data    The data array being built
    * @param array $prev    The previous record
    * @param mixed $A       The return data to expect
    * @param int   $channel The channel to test
    *
    * @return null
    *
    * @dataProvider dataEncodeDataPoint()
    */
    public function testEncodeDataPoint(
        $sensor, $expect, $deltaT, $data, $prev, $A, $channel = 0
    ) {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock($sensor);
        $ret = $this->o->encodeDataPoint($A, $channel, $deltaT, $prev, $data);
        $this->assertSame($expect, $ret);
    }
}
?>
