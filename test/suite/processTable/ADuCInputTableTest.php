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
namespace HUGnet\processTable;
/** This is a required class */
require_once CODE_BASE.'processTable/ADuCInputTable.php';
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
class ADuCInputTableTest extends \PHPUnit_Framework_TestCase
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
    public static function dataParams()
    {
        return array(
            array(
                array(
                ),
                null,
                "driver0",
                null,
                "FF",
            ),
            array(
                array(
                ),
                null,
                "driver1",
                null,
                "FF",
            ),
            array(
                array(
                ),
                null,
                "driver0",
                "04",
                "04",
            ),
            array(
                array(
                ),
                null,
                "driver0",
                0x04,
                "04",
            ),
            array(
                array(
                ),
                null,
                "priority",
                5,
                "05",
            ),
            array(
                array(
                ),
                null,
                "priority",
                0x105,
                "05",
            ),
            array(
                array(
                ),
                null,
                "immediateProcessRoutine",
                4,
                "04",
            ),
            array(
                array(
                ),
                null,
                "immediateProcessRoutine",
                0xFE,
                "00",
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array  $mock    The mocks to preload
    * @param string $preload The string to give to the class
    * @param string $param   The driver number
    * @param string $set     The values to set the register to
    * @param array  $expect  The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataParams
    */
    public function testParams($mock, $preload, $param, $set, $expect)
    {
        $sensor = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mock);
        $obj = ADuCInputTable::factory($sensor, $preload);
        $ret = $obj->$param($set);
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataRegister()
    {
        return array(
            array(
                array(
                ),
                null,
                "ADCFLT",
                null,
                "8009",
            ),
            array(
                array(
                ),
                null,
                "ADC0CON",
                null,
                "80C0",
            ),
            array(
                array(
                ),
                null,
                "ADC1CON",
                null,
                "8600",
            ),
            array(
                array(
                ),
                null,
                "ADCFLT",
                "1234",
                "1234",
            ),
            array(
                array(
                ),
                null,
                "ADC0CON",
                "1234",
                "1234",
            ),
            array(
                array(
                ),
                null,
                "ADC1CON",
                "1234",
                "1234",
            ),
            array(
                array(
                ),
                null,
                "ADC1CON",
                0x1234,
                "1234",
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array  $mock    The mocks to preload
    * @param string $preload The string to give to the class
    * @param string $reg     The register to get
    * @param string $set     The values to set the register to
    * @param array  $expect  The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataRegister
    */
    public function testRegister($mock, $preload, $reg, $set, $expect)
    {
        $sensor = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mock);
        $obj = ADuCInputTable::factory($sensor, $preload);
        $ret = $obj->register($reg, $set);
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataFreq()
    {
        return array(
            array( // #0 Chop Enabled
                array(
                ),
                array(
                    "SF" => 9,
                    "AF" => 0,
                    "CHOPEN" => 1,
                ),
                266.2507,
            ),
            array(  // #1 Chop Disabled
                array(
                ),
                array(
                    "SF" => 9,
                    "AF" => 0,
                    "CHOPEN" => 0,
                ),
                800.0,
            ),
            array(  // #1 Chop Disabled
                array(
                ),
                array(
                    "SF" => 9,
                    "AF" => 7,
                    "CHOPEN" => 0,
                ),
                80.0,
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array  $mock    The mocks to preload
    * @param string $preload The string to give to the class
    * @param array  $expect  The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataFreq
    */
    public function testFreq($mock, $preload, $expect)
    {
        $sensor = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mock);
        $obj = ADuCInputTable::factory($sensor, $preload);
        $ret = $obj->freq();
        $this->assertEquals($expect, $ret, 0.0001);
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
                    'driver0' => 0x04,
                    'driver1' => 0x41,
                    'priority' => 3,
                    'process' => 4,
                    'ADC0EN' => 1,
                    'ADC0DIAG' => 0,
                    'HIGHEXTREF0' => 0,
                    'AMP_CM' => 0,
                    'ADC0CODE' => 0,
                    'ADC0CH' => 3,
                    'ADC0REF' => 0,
                    'ADC0PGA' => 0,
                    'ADC1EN' => 1,
                    'ADC1DIAG' => 0,
                    'HIGHEXTREF1' => 0,
                    'ADC1CODE' => 0,
                    'ADC1CH' => 12,
                    'ADC1REF' => 0,
                    'BUF_BYPASS' => 0,
                    'ADC1PGA' => 0,
                    'CHOPEN' => 1,
                    'RAVG2' => 0,
                    'AF' => 0,
                    'NOTCH2' => 0,
                    'SF' => 9,
                ),
                "0304C080008609800441",
            ),
            array( // #1 Array in
                array(
                ),
                array(
                    'driver0' => 0x04,
                    'driver1' => 0x41,
                    'priority' => 3,
                    'process' => 4,
                    'ADC0EN' => 1,
                    'ADC0DIAG' => 0,
                    'HIGHEXTREF0' => 0,
                    'AMP_CM' => 0,
                    'ADC0CODE' => 0,
                    'ADC0CH' => 3,
                    'ADC0REF' => 0,
                    'ADC0PGA' => 0,
                    'ADC1EN' => 1,
                    'ADC1DIAG' => 0,
                    'HIGHEXTREF1' => 0,
                    'ADC1CODE' => 0,
                    'ADC1CH' => 12,
                    'ADC1REF' => 0,
                    'BUF_BYPASS' => 0,
                    'ADC1PGA' => 0,
                    'CHOPEN' => 1,
                    'RAVG2' => 0,
                    'AF' => 0,
                    'NOTCH2' => 0,
                    'SF' => 9,
                ),
                array(
                ),
                "0304C080008609800441",
            ),
            array( // #2 Empty array
                array(
                ),
                array(
                ),
                array(
                ),
                "FF00C08000860980FFFF",
            ),
            array( // #3 SF 31, AF 63  --  Valid
                array(
                ),
                array(
                    "SF" => 31,
                ),
                array(
                    "AF" => 63,
                ),
                "FF00C08000861FBFFFFF",
            ),
            array( // #4 SF 63, AF 7  --  Valid
                array(
                ),
                array(
                    "SF" => 63,
                ),
                array(
                    "AF" => 7,
                ),
                "FF00C08000863F87FFFF",
            ),
            array( // #5 SF 127, AF 0  --  Valid
                array(
                ),
                array(
                    "SF" => 127,
                ),
                array(
                    "AF" => 0,
                ),
                "FF00C08000867F80FFFF",
            ),
            array( // #6 AF 5, SF 127  --  Not valid: Becomes AF 0, SF 127
                array(
                ),
                array(
                    "AF" => 5,
                ),
                array(
                    "SF" => 127,
                ),
                "FF00C08000867F80FFFF",
            ),
            array( // #7 AF 10, SF 63  --  Not valid: Becomes AF 7, SF 63
                array(
                ),
                array(
                    "AF" => 10,
                ),
                array(
                    "SF" => 63,
                ),
                "FF00C08000863F87FFFF",
            ),
            array( // #8 SF 63, AF 8  --  Not valid: Becomes AF 8, SF 31
                array(
                ),
                array(
                    "SF" => 63,
                ),
                array(
                    "AF" => 8,
                ),
                "FF00C08000861F88FFFF",
            ),
            array( // #8 SF 127, AF 7  --  Not valid: Becomes AF 7, SF 63
                array(
                ),
                array(
                    "SF" => 127,
                ),
                array(
                    "AF" => 7,
                ),
                "FF00C08000863F87FFFF",
            ),
            array( // #8 Real World test
                array(
                ),
                array(
                    'group' => 'default',
                    "id" => 1,
                    'name' => 'RTD',
                    'desc' => 'This is for the RTD.',
                    'arch' => 'ADuC',
                    'driver0' => 4,
                    'driver1' => 255,
                    'priority' => 1,
                    'process' => 0,
                    'ADC0EN' => 0,
                    'ADC0DIAG' => 0,
                    'HIGHEXTREF0' => 0,
                    'AMP_CM' => 0,
                    'ADC0CODE' => 0,
                    'ADC0CH' => 3,
                    'ADC0REF' => 0,
                    'ADC0PGA' => 0,
                    'ADC1EN' => 1,
                    'ADC1DIAG' => 0,
                    'HIGHEXTREF1' => 0,
                    'ADC1CODE' => 0,
                    'ADC1CH' => 6,
                    'ADC1REF' => 0,
                    'BUF_BYPASS' => 0,
                    'ADC1PGA' => 0,
                    'CHOPEN' => 1,
                    'RAVG2' => 0,
                    'AF' => 0,
                    'NOTCH2' => 0,
                    'SF' => 9
                ),
                array(
                ),
                "0100C0000083098004FF",
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
        $sensor = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mock);
        $obj = ADuCInputTable::factory($sensor, $preload);
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
                "0102C080008609800404",
                true,
                array(
                    'driver0' => 4,
                    'driver1' => 4,
                    'priority' => 1,
                    'process' => 2,
                    'ADC0EN' => 1,
                    'ADC0DIAG' => 0,
                    'HIGHEXTREF0' => 0,
                    'AMP_CM' => 0,
                    'ADC0CODE' => 0,
                    'ADC0CH' => 3,
                    'ADC0REF' => 0,
                    'ADC0PGA' => 0,
                    'ADC1EN' => 1,
                    'ADC1DIAG' => 0,
                    'HIGHEXTREF1' => 0,
                    'ADC1CODE' => 0,
                    'ADC1CH' => 12,
                    'ADC1REF' => 0,
                    'BUF_BYPASS' => 0,
                    'ADC1PGA' => 0,
                    'CHOPEN' => 1,
                    'RAVG2' => 0,
                    'AF' => 0,
                    'NOTCH2' => 0,
                    'SF' => 9,
                ),
            ),
            array( // #1 Feed the string in through the constructor
                array(
                ),
                "0102C080008609800404",
                "",
                false,
                array(
                    'driver0' => 4,
                    'driver1' => 4,
                    'priority' => 1,
                    'process' => 2,
                    'ADC0EN' => 1,
                    'ADC0DIAG' => 0,
                    'HIGHEXTREF0' => 0,
                    'AMP_CM' => 0,
                    'ADC0CODE' => 0,
                    'ADC0CH' => 3,
                    'ADC0REF' => 0,
                    'ADC0PGA' => 0,
                    'ADC1EN' => 1,
                    'ADC1DIAG' => 0,
                    'HIGHEXTREF1' => 0,
                    'ADC1CODE' => 0,
                    'ADC1CH' => 12,
                    'ADC1REF' => 0,
                    'BUF_BYPASS' => 0,
                    'ADC1PGA' => 0,
                    'CHOPEN' => 1,
                    'RAVG2' => 0,
                    'AF' => 0,
                    'NOTCH2' => 0,
                    'SF' => 9,
                ),
            ),
            array( // #2 Empty string
                array(
                ),
                null,
                "",
                false,
                array(
                    'driver0' => 0xFF,
                    'driver1' => 0xFF,
                    'priority' => 0xFF,
                    'process' => 0,
                    'ADC0EN' => 1,
                    'ADC0DIAG' => 0,
                    'HIGHEXTREF0' => 0,
                    'AMP_CM' => 0,
                    'ADC0CODE' => 0,
                    'ADC0CH' => 3,
                    'ADC0REF' => 0,
                    'ADC0PGA' => 0,
                    'ADC1EN' => 1,
                    'ADC1DIAG' => 0,
                    'HIGHEXTREF1' => 0,
                    'ADC1CODE' => 0,
                    'ADC1CH' => 12,
                    'ADC1REF' => 0,
                    'BUF_BYPASS' => 0,
                    'ADC1PGA' => 0,
                    'CHOPEN' => 1,
                    'RAVG2' => 0,
                    'AF' => 0,
                    'NOTCH2' => 0,
                    'SF' => 9,
                ),
            ),
            array( // #3 real world
                array(
                ),
                null,
                "0100C0000083098004FF",
                true,
                array(
                    'driver0' => 4,
                    'driver1' => 255,
                    'priority' => 1,
                    'process' => 0,
                    'ADC0EN' => 0,
                    'ADC0DIAG' => 0,
                    'HIGHEXTREF0' => 0,
                    'AMP_CM' => 0,
                    'ADC0CODE' => 0,
                    'ADC0CH' => 3,
                    'ADC0REF' => 0,
                    'ADC0PGA' => 0,
                    'ADC1EN' => 1,
                    'ADC1DIAG' => 0,
                    'HIGHEXTREF1' => 0,
                    'ADC1CODE' => 0,
                    'ADC1CH' => 6,
                    'ADC1REF' => 0,
                    'BUF_BYPASS' => 0,
                    'ADC1PGA' => 0,
                    'CHOPEN' => 1,
                    'RAVG2' => 0,
                    'AF' => 0,
                    'NOTCH2' => 0,
                    'SF' => 9
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
        $sensor = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mock);
        $obj = ADuCInputTable::factory($sensor, $preload);
        $ret = $obj->decode($string);
        $this->assertSame($expect, $ret);
        $this->assertSame($array, $obj->toArray());
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataGain()
    {
        return array(
            array( // #0  ipr= *128, gain = 8, channel 0
                array(
                ),
                array(
                    "process" => 1,
                    "ADC0PGA" => 3,
                ),
                0,
                1024,
            ),
            array( // #1  ipr= *128, gain = 8, channel 1
                array(
                ),
                array(
                    "process" => 1,
                    "ADC1PGA" => 3,
                ),
                1,
                1024,
            ),
            array( // #1  ipr= *128, gain = 1, channel 0
                array(
                ),
                array(
                    "process" => 1,
                    "ADC1PGA" => 3,
                ),
                0,
                128,
            ),
            array( // #2  ipr= /128, gain = 128, channel 0
                array(
                ),
                array(
                    "process" => 2,
                    "ADC0PGA" => 7,
                ),
                0,
                1,
            ),
            array( // #3  ipr=empty, gain = 128, channel 0
                array(
                ),
                array(
                    "process" => 0,
                    "ADC0PGA" => 7,
                ),
                0,
                128,
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array  $mock    The mocks to preload
    * @param string $preload The string to give to the class
    * @param int    $channel The channel to use
    * @param array  $expect  The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataGain
    */
    public function testGain($mock, $preload, $channel, $expect)
    {
        $sensor = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mock);
        $obj = ADuCInputTable::factory($sensor, $preload);
        $ret = $obj->gain($channel);
        $this->assertEquals($expect, $ret, 0.0001);
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataParamTests()
    {
        $sensor = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock(array());
        $obj = ADuCInputTable::factory($sensor, $preload);
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
        $this->assertInternalType(
            "int", $param["value"], "Default value for $name must be an int"
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
    public function testParamMask($name, $param)
    {
        $this->assertInternalType(
            "int", $param["mask"], "Mask value for $name must be an int"
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
        } else if (is_null($param["valid"])) {
            $this->assertNotNull(
                $param["mask"], "If valid is null, mask can't be"
            );
        } else if (is_string($param["valid"])) {
            $sensor = new \HUGnet\DummyTable("Sensor");
            $sensor->resetMock(array());
            $obj = ADuCInputTable::factory($sensor, $preload);
            $this->assertTrue(
                method_exists($obj, $param["valid"]),
                $param["valid"]." is not a valid method of class ADuCInputTable"
            );
        } else {
            $this->fail(
                "Valid must be null (with mask set), an array, or a function name"
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
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataFullArray()
    {
        return array(
            array(
                array(
                ),
                array(
                    "process" => 1,
                    "ADC0PGA" => 3,
                ),
                "array",
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array  $mock    The mocks to preload
    * @param string $preload The string to give to the class
    * @param array  $expect  The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataFullArray
    */
    public function testFullArray($mock, $preload, $expect)
    {
        $sensor = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mock);
        $obj = ADuCInputTable::factory($sensor, $preload);
        $this->assertInternalType(
            "array", $obj->fullArray(),
            "fullArray must return an array"
        );
    }
}
?>
