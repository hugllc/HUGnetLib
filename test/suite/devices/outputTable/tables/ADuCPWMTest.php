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
namespace HUGnet\devices\outputTable\tables;
/** This is a required class */
require_once CODE_BASE.'devices/outputTable/tables/ADuCPWM.php';
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
class ADuCPWMTest extends \PHPUnit_Framework_TestCase
{
    /** This is our system object */
    protected $system;
    /** This is our output object */
    protected $output;
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
        $this->system = $this->getMock("\HUGnet\System", array("now"));
        $this->system->expects($this->any())
            ->method('now')
            ->will($this->returnValue(123456));
        $this->output = $this->system->device()->output(0);
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
        unset($this->system);
        unset($this->output);
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
                "PWMCON",
                null,
                "0001",
            ),
            array(
                array(
                ),
                null,
                "PWMCON",
                hexdec("FFFF"),
                "79F5",
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
        $this->output->load($mock);
        $obj = ADuCPWM::factory($this->output, $preload);
        $ret = $obj->register($reg, $set);
        $this->assertSame($expect, $ret);
    }

    /**
    * Data provider for testEncode
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
                    'PWM0LEN' => 0x1234,
                    'PWM1LEN' => 0x5678,
                    'PWM2LEN' => 0x9900,
                    'SYNC' => 1,
                    'PWM5INV' => 0,
                    'PWM3INV' => 0,
                    'PWM1INV' => 0,
                    'PWMTRIP' => 0,
                    'ENA' => 0,
                    'PWMCP' => 0,
                    'POINV' => 0,
                    'HOFF' => 0,
                    'LCOMP' => 0,
                    'DIR' => 0,
                    'HMODE' => 1,
                    'PWMEN' => 1,
                ),
                "0140341278560099",
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
        $this->output->load($mock);
        $obj = ADuCPWM::factory($this->output, $preload);
        $obj->fromArray($array);
        $ret = $obj->encode();
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testDecode
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
                "0140341278560099",
                true,
                array(
                    'PWM0LEN' => 0x1234,
                    'PWM1LEN' => 0x5678,
                    'PWM2LEN' => 0x9900,
                    'SYNC' => 1,
                    'PWM5INV' => 0,
                    'PWM3INV' => 0,
                    'PWM1INV' => 0,
                    'PWMCP' => 0,
                    'POINV' => 0,
                    'HOFF' => 0,
                    'DIR' => 0,
                ),
            ),
            array( // #1 String too short
                array(
                ),
                null,
                "01",
                false,
                array(
                    'PWM0LEN' => 0xFFFF,
                    'PWM1LEN' => 0xFFFF,
                    'PWM2LEN' => 0xFFFF,
                    'SYNC' => 0,
                    'PWM5INV' => 0,
                    'PWM3INV' => 0,
                    'PWM1INV' => 0,
                    'PWMCP' => 0,
                    'POINV' => 0,
                    'HOFF' => 0,
                    'DIR' => 0,
                ),
            ),
            array( // #2  All FF given
                array(
                ),
                null,
                "FFFFFFFFFFFFFFFF",
                true,
                array(
                    'PWM0LEN' => 0xFFFF,
                    'PWM1LEN' => 0xFFFF,
                    'PWM2LEN' => 0xFFFF,
                    'SYNC' => 1,
                    'PWM5INV' => 1,
                    'PWM3INV' => 1,
                    'PWM1INV' => 1,
                    'PWMCP' => 7,
                    'POINV' => 1,
                    'HOFF' => 1,
                    'DIR' => 1,
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
        $this->output->load($mock);
        $obj = ADuCPWM::factory($this->output, $preload);
        $ret = $obj->decode($string);
        $this->assertSame($expect, $ret);
        $this->assertSame($array, $obj->toArray());
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
        $obj = ADuCPWM::factory($sensor, $preload);
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
            $obj = ADuCPWM::factory($this->output, $preload);
            $this->assertTrue(
                method_exists($obj, $param["valid"]),
                $param["valid"]." is not a valid method of class ADuCPWM"
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
}
?>
