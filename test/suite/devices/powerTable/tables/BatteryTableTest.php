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
require_once CODE_BASE.'devices/powerTable/tables/BatteryTable.php';
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
class BatteryTableTest extends \PHPUnit_Framework_TestCase
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
                    'AbsorbDwellTime' => 0,
                    'AbsorbCoefficient' => 0,
                    'FloatCoefficient' => 0,
                    'AbsorbVoltage' => 0,
                    'FloatVoltage' => 0,
                    'BulkChargeTriggerVoltage' => 0,
                    'ResumeVoltage' => 0,
                    'CutoffVoltage' => 0,
                    'MinimumVoltage' => 0,
                ),
                "",
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
        $obj = BatteryTable::factory($power, $preload);
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
                "400010C43412",
                false,
                array(
                    'AbsorbDwellTime' => 0,
                    'AbsorbCoefficient' => 0,
                    'FloatCoefficient' => 0,
                    'AbsorbVoltage' => 0,
                    'FloatVoltage' => 0,
                    'BulkChargeTriggerVoltage' => 0,
                    'ResumeVoltage' => 0,
                    'CutoffVoltage' => 0,
                    'MinimumVoltage' => 0,
                ),
            ),
            array( // #1 String too short
                array(
                ),
                null,
                "01",
                false,
                array(
                    'AbsorbDwellTime' => 0,
                    'AbsorbCoefficient' => 0,
                    'FloatCoefficient' => 0,
                    'AbsorbVoltage' => 0,
                    'FloatVoltage' => 0,
                    'BulkChargeTriggerVoltage' => 0,
                    'ResumeVoltage' => 0,
                    'CutoffVoltage' => 0,
                    'MinimumVoltage' => 0,
                ),
            ),
            array( // #2  All FF given
                array(
                ),
                null,
                "FFFFFFFFFFFFFFFF",
                false,
                array(
                    'AbsorbDwellTime' => 0,
                    'AbsorbCoefficient' => 0,
                    'FloatCoefficient' => 0,
                    'AbsorbVoltage' => 0,
                    'FloatVoltage' => 0,
                    'BulkChargeTriggerVoltage' => 0,
                    'ResumeVoltage' => 0,
                    'CutoffVoltage' => 0,
                    'MinimumVoltage' => 0,
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
        $obj = BatteryTable::factory($power, $preload);
        $ret = $obj->decode($string);
        $this->assertSame($expect, $ret);
        $this->assertEquals($array, $obj->toArray());
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
        $obj = BatteryTable::factory($power, $preload);
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
    public function testParamDesc($name, $param)
    {
        $min = 5;
        $max = 30;
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
        } else if (is_string($param["valid"])) {
            $power = new \HUGnet\DummyTable("Power");
            $power->resetMock(array());
            $obj = BatteryTable::factory($power, $preload);
            $this->assertTrue(
                method_exists($obj, $param["valid"]),
                $param["valid"]." is not a method of class BatteryTable"
            );
        } else {
        /*
            $this->fail(
                "Valid must be null (with mask set), an array, or a function name"
            );
            */
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
    public function testParamBits($name, $param)
    {
        if (is_numeric($param["bit"]) || is_numeric($param["bits"])) {
            $min = 5;
            $max = 30;
            $this->assertInternalType(
                "int", $param["bit"], "Bit for $name must be an int"
            );
            $this->assertInternalType(
                "int", $param["bits"], "Bits for $name must be a int"
            );
        } else {
            $this->assertNull($param["bit"]);
            $this->assertNull($param["bits"]);
        }
    }
}
?>
