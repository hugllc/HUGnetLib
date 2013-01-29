<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This is a required class */
require_once CODE_BASE.'devices/DataChan.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DataChanTest extends \PHPUnit_Framework_TestCase
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
    * Data provider for testCreate
    *
    * @return array
    */
    public static function data2Array()
    {
        return array(
            array(
                array(
                ),
                array(
                    "storageUnit" => "asdf",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Generic",
                    "units" => "asdf",
                    "label" => "Empty",
                    "decimals" => 5,
                    "dataType" => "raw",
                ),
                array(
                    "storageUnit" => "asdf",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Generic",
                    "units" => "fsda",
                    "label" => "This is a label",
                    "decimals" => 3,
                    "dataType" => "diff",
                ),
                true,
                array(
                    "storageUnit" => "asdf",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Generic",
                    "units" => "asdf",
                    "label" => "This is a label",
                    "decimals" => 3,
                    "dataType" => "diff",
                ),
            ),
            array(
                array(
                ),
                array(
                    "storageUnit" => "&#176;C",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Temperature",
                    "units" => "asdf",
                    "label" => "Empty",
                    "decimals" => 5,
                    "dataType" => "raw",
                ),
                array(
                    "storageUnit" => "asdf",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Generic",
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 8,
                    "dataType" => "diff",
                ),
                true,
                array(
                    "storageUnit" => "&#176;C",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Temperature",
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 5,
                    "dataType" => "diff",
                ),
            ),
            array(
                array(
                ),
                array(
                    "storageUnit" => "&#176;C",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Temperature",
                    "units" => "asdf",
                    "label" => "Empty",
                    "decimals" => 5,
                    "dataType" => "raw",
                ),
                array(
                    "storageUnit" => "asdf",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Generic",
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 8,
                    "dataType" => "diff",
                ),
                false,
                array(
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 5,
                    "dataType" => "diff",
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $mocks   The configuration to use
    * @param mixed $driver  The driver data to use
    * @param mixed $data    The data to use
    * @param bool  $default Whether to rerturn the default items
    * @param mixed $expect  The value we expect back
    *
    * @return null
    *
    * @dataProvider data2Array
    */
    public function test2Array(
        $mocks, $driver, $data, $default, $expect
    ) {
        $dev = new \HUGnet\DummyBase("Device");
        $dev->resetMock($mocks);
        $obj = DataChan::factory($dev, $driver, $data);
        $ret = $obj->toArray($default);
        $this->assertEquals($expect, $ret);
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataConvert()
    {
        return array(
            array(
                array(
                ),
                array(
                    "storageUnit" => "&#176;C",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Temperature",
                ),
                array(
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 8,
                    "dataType" => "raw",
                ),
                0.0,
                32.0
            ),
            array(
                array(
                ),
                array(
                    "storageUnit" => "&#176;C",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Temperature",
                ),
                array(
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 8,
                    "dataType" => "raw",
                ),
                100.0,
                212.0
            ),
            array(
                array(
                ),
                array(
                    "storageUnit" => "&#176;C",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Temperature",
                ),
                array(
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 8,
                    "dataType" => "raw",
                ),
                null,
                null
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $mocks  The configuration to use
    * @param mixed $driver The driver data to use
    * @param mixed $data   The data to use
    * @param float $value  The value to convert
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataConvert
    */
    public function testConvert(
        $mocks, $driver, $data, $value, $expect
    ) {
        $dev = new \HUGnet\DummyBase("Device");
        $dev->resetMock($mocks);
        $obj = DataChan::factory($dev, $driver, $data);
        $obj->convert($value);
        $this->assertEquals($expect, $value);
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataInput()
    {
        return array(
            array(
                array(
                    "Device" => array(
                        "input" => new \HUGnet\DummyBase("Input"),
                    ),
                    "Input" => array(
                        "id" => 5,
                    ),
                ),
                array(
                    "input" => 5,
                ),
                array(
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 8,
                    "dataType" => "raw",
                ),
                5
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $mocks  The configuration to use
    * @param mixed $driver The driver data to use
    * @param mixed $data   The data to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataInput
    */
    public function testInput(
        $mocks, $driver, $data, $expect
    ) {
        $dev = new \HUGnet\DummyBase("Device");
        $dev->resetMock($mocks);
        $obj = DataChan::factory($dev, $driver, $data);
        $input = $obj->input();
        $this->assertInternalType(
            "object",
            $input,
            "Return is not an object"
        );
        $this->assertEquals($expect, $input->id());
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataEncode()
    {
        return array(
            array(
                array(
                    "Device" => array(
                        "input" => new \HUGnet\DummyBase("Input"),
                    ),
                    "Input" => array(
                        "encodeDataPoint" => "asdf",
                    ),
                ),
                array(
                    "input" => 5,
                ),
                array(
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 8,
                    "dataType" => "raw",
                ),
                5,
                "asdf"
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $mocks  The configuration to use
    * @param mixed $driver The driver data to use
    * @param mixed $data   The data to use
    * @param float $value  The value to convert
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataEncode
    */
    public function testEncode(
        $mocks, $driver, $data, $value, $expect
    ) {
        $dev = new \HUGnet\DummyBase("Device");
        $dev->resetMock($mocks);
        $obj = DataChan::factory($dev, $driver, $data);
        $input = $obj->encode($value);
        $this->assertEquals($expect, $input);
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataDecodeRaw()
    {
        return array(
            array(
                array(
                    "Device" => array(
                        "input" => new \HUGnet\DummyBase("Input"),
                    ),
                    "Input" => array(
                        "getRawData" => array(
                            "5" => "asdf",
                        ),
                    ),
                ),
                array(
                    "input" => 5,
                ),
                array(
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 8,
                    "dataType" => "raw",
                ),
                5,
                "asdf"
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $mocks  The configuration to use
    * @param mixed $driver The driver data to use
    * @param mixed $data   The data to use
    * @param float $value  The value to convert
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataDecodeRaw
    */
    public function testDecodeRaw(
        $mocks, $driver, $data, $value, $expect
    ) {
        $dev = new \HUGnet\DummyBase("Device");
        $dev->resetMock($mocks);
        $obj = DataChan::factory($dev, $driver, $data);
        $input = $obj->decodeRaw($value);
        $this->assertEquals($expect, $input);
        unset($obj);
    }
}


?>
