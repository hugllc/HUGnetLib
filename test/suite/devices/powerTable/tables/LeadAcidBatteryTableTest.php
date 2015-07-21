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
namespace HUGnet\devices\powerTable\tables;
/** This is a required class */
require_once CODE_BASE.'devices/powerTable/tables/LeadAcidBatteryTable.php';
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
class LeadAcidBatteryTableTest extends \PHPUnit_Framework_TestCase
{
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
        parent::setUp();
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
        parent::tearDown();
    }


    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataEncode()
    {
        return array(
            array( // #0 Array in
                array(
                ),
                array(
                ),
                array(
                    'BulkChargeDwellTime' => 1800,
                    'BulkChargeCoeff' => 10,
                    'FloatCoeff' => -10,
                    'BulkChargeVoltage' => 13500,
                    'FloatVoltage' => 12500,
                    'BulkChargeTriggerVoltage' => 13500,
                    'ResumeVoltage' => 14000,
                    'CutoffVoltage' => 11500,
                    'MinimumVoltage' => 2000,
                ),
                "08070A00BC340000BC340000F6FFD4300000B0360000EC2C0000D0070000",
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array  $mock    The mocks to preload
    * @param string $preload The string to give to the class
    * @param array  $array   The array to load into the class
    * @param array  $expect  The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataEncode
    */
    public function testEncode($mock, $preload, $array, $expect)
    {
        $power = new \HUGnet\DummyTable("Power");
        $power->resetMock($mock);
        $obj = LeadAcidBatteryTable::factory($power, $preload);
        $obj->fromArray($array);
        $ret = $obj->encode();
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataDecode()
    {
        return array(
            array( // #0 Feed a string in normally
                array(
                ),
                null,
                "08070A00BC340000BC340000F6FFD4300000B0360000EC2C0000D0070000",
                true,
                array(
                    'BulkChargeDwellTime' => 1800,
                    'BulkChargeCoeff' => 10,
                    'FloatCoeff' => -10,
                    'BulkChargeVoltage' => 13500,
                    'FloatVoltage' => 12500,
                    'BulkChargeTriggerVoltage' => 13500,
                    'ResumeVoltage' => 14000,
                    'CutoffVoltage' => 11500,
                    'MinimumVoltage' => 2000,
                ),
            ),
            array( // #1 String too short
                array(
                ),
                null,
                "01",
                false,
                array(
                    'BulkChargeDwellTime' => 1,
                    'BulkChargeCoeff' => 1,
                    'FloatCoeff' => 1,
                    'BulkChargeVoltage' => 12500,
                    'FloatVoltage' => 13500,
                    'BulkChargeTriggerVoltage' => 12500,
                    'ResumeVoltage' => 11000,
                    'CutoffVoltage' => 10500,
                    'MinimumVoltage' => 1000,
                ),
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array  $mock    The mocks to preload
    * @param string $preload The string to give to the class
    * @param string $string  The string to give to decode
    * @param array  $expect  The info to expect returned
    * @param array  $array   The array that should be built
    *
    * @return null
    *
    * @dataProvider dataDecode
    */
    public function testDecode($mock, $preload, $string, $expect, $array)
    {
        $power = new \HUGnet\DummyTable("Power");
        $power->resetMock($mock);
        $obj = LeadAcidBatteryTable::factory($power, $preload);
        $ret = $obj->decode($string);
        $this->assertSame($expect, $ret);
        $this->assertEquals($array, $obj->toArray());
    }
    /**
    * Tests the order the params are in
    *
    * @return null
    *
    */
    public function testParamOrderSet()
    {
        $power = new \HUGnet\DummyTable("Power");
        $power->resetMock(array());
        $obj = LeadAcidBatteryTable::factory($power, $preload);
        $paramOrder = $this->readAttribute($obj, "paramOrder");
        $params = $obj->fullArray();
        $this->assertInternalType(
            "array", $paramOrder, "paramOrder must be an array"
        );
        foreach($paramOrder as $key) {
            $this->assertTrue(
                isset($params[$key]), "$key is not set in params"
            );
        }
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataParamTests()
    {
        $power = new \HUGnet\DummyTable("Power");
        $power->resetMock(array());
        $obj = LeadAcidBatteryTable::factory($power, $preload);
        $params = (array)$obj->fullArray();
        $return = array();
        foreach ($params as $key => $value) {
            $return[] = array(
                $key, $value
            );
        }
        return $return;
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param string $name  The name of the param
    * @param array  $param The param array
    *
    * @return null
    *
    * @dataProvider dataParamTests
    */
    public function testParamValue($name, $param)
    {
        if (!is_string($param["value"])) {
            $this->assertInternalType(
                "int", $param["value"], "Default value for $name must be an int"
            );
        }
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param string $name  The name of the param
    * @param array  $param The param array
    *
    * @return null
    *
    * @dataProvider dataParamTests
    */
    public function testParamOrder($name, $param)
    {
        $power = new \HUGnet\DummyTable("Power");
        $power->resetMock(array());
        $obj = LeadAcidBatteryTable::factory($power, $preload);
        $paramOrder = (array)$this->readAttribute($obj, "paramOrder");
        $this->assertTrue(
            in_array($name, $paramOrder), "Param $name is not in paramOrder"
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param string $name  The name of the param
    * @param array  $param The param array
    *
    * @return null
    *
    * @dataProvider dataParamTests
    */
    public function testParamMin($name, $param)
    {
        $this->assertInternalType(
            "int", $param["min"], "min value for $name must be an int"
        );
        $this->assertTrue(
            $param["min"] <= $param["max"], 
            "min value for $name must be less than or equal to the max value"
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param string $name  The name of the param
    * @param array  $param The param array
    *
    * @return null
    *
    * @dataProvider dataParamTests
    */
    public function testParamMax($name, $param)
    {
        $this->assertInternalType(
            "int", $param["max"], "max value for $name must be an int"
        );
        $this->assertTrue(
            $param["min"] <= $param["max"], 
            "max value for $name must be greater than or equal to the min value"
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param string $name  The name of the param
    * @param array  $param The param array
    *
    * @return null
    *
    * @dataProvider dataParamTests
    */
    public function testParamDesc($name, $param)
    {
        $min = 5;
        $max = 35;
        $this->assertInternalType(
            "string", $param["desc"], "Description for $name must be a string"
        );
        $this->assertGreaterThan(
            $min, strlen($param["desc"]),
            "Description for $name must be more than $min characters"
        );
        $this->assertLessThan(
            $max, strlen($param["desc"]),
            "Description for $name must be less than $max characters"
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param string $name  The name of the param
    * @param array  $param The param array
    *
    * @return null
    *
    * @dataProvider dataParamTests
    */
    public function testParamValid($name, $param)
    {
        if (is_array($param["valid"])) {
            $this->assertGreaterThan(
                0, count($param["valid"]), "There must be at least 1 valid value"
            );
        } else if (is_null($param["valid"])) {
        } else if (is_string($param["valid"])) {
            $power = new \HUGnet\DummyTable("Power");
            $power->resetMock(array());
            $obj = LeadAcidBatteryTable::factory($power, $preload);
            $this->assertTrue(
                method_exists($obj, $param["valid"]),
                $param["valid"]." is not a method of class LeadAcidBatteryTable"
            );
        } else {
            $this->fail(
                "Valid must be null, an array, or a function name"
            );
        }
    }
}
?>
