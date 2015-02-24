<?php
/**
 * Tests the filter class
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
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBase.php";
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/drivers/MaximumWindDirection.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class MaximumWindDirectionTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "MaximumWindDirection";

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
        parent::setUp();
        $this->input = new \HUGnet\DummyBase("Input");
        $this->input->resetMock(
            array(
                "Input" => array(
                    "device" => new \HUGnet\DummyBase("Device"),
                ),
                "Device" => array(
                    "dataChannels" => new \HUGnet\DummyBase("dataChannels"),
                    "controlChannels" => new \HUGnet\DummyBase("controlChannels"),
                    "dataChannel" => new \HUGnet\DummyBase("dataChannel"),
                    "get" => array(
                        "DigitalInputs" => array(
                            1  => "Port 1",
                            2  => "Port 2",
                            3  => "Port 3",
                            4  => "Port 4",
                            5  => "Port 5",
                            6  => "Port 6",
                            9  => "Port 9",
                            10 => "Port 10",
                            11 => "Port 11",
                            12 => "Port 12",
                            13 => "Port 13",
                            14 => "Port 14",
                            15 => "Port 15",
                            16 => "Port 16",
                        ),
                    ),
                ),
                "dataChannels" => array(
                    "select" => array(),
                ),
                "controlChannels" => array(
                    "select" => array(),
                ),
                "dataChannel" => array(
                    "get" => array(
                        "storageUnit" => "asdf",
                    ),
                ),
            )
        );
        $this->o = \HUGnet\devices\inputTable\Driver::factory(
            "MaximumWindDirection", $this->input
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
                array(
                    "Sensor" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "get" => array(
                            "DigitalInputs" => array(1, 2, 3),
                        ),
                    ),
                ),
                null,
            ),
            array(
                "extraValues",
                array(
                    "Sensor" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Input" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "get" => array(
                            "DigitalInputs" => array(1, 2, 3),
                        ),
                    ),
                ),
                array(
                    5, 
                    array(1, 2, 3), 
                    array(1, 2, 3), 
                    array(1, 2, 3),
                    array(1, 2, 3), 
                    array(1, 2, 3), 
                ),
            ),
        );
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
    }

    /**
     * Data provider for testGetReading
     *
     * @return array
     */
    public static function dataGetReading()
    {
        return array(
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x01,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                0.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x04,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                90.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x10,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                180.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x40,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                270.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x02,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                45.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x08,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                135.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x20,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                225.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x80,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                315.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x03,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                22.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x81,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                337.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x06,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                67.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x0C,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                112.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x18,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                157.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x30,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                202.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x60,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                247.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0xC0,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                292.5
            ),
            // Two cardinal directions
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x05,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                null
            ),
            // No Bits set
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x00,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                null
            ),
            // Cardinal and ordinal not 45deg from eachother
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x21,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                null
            ),
            // Two ordinal directions
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                0x0A,
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                null
            ),
        );
    }
    /**
     * Data provider for testGetReading
     *
     * @return array
     */
    public static function dataEncodeDataPoint()
    {
        return array(
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "010000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                0.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "040000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                90.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "100000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                180.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "400000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                270.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "020000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                45.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "080000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                135.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "200000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                225.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "800000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                315.0
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "030000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                22.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "810000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                337.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "060000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                67.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "0C0000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                112.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "180000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                157.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "300000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                202.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "600000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                247.5
            ),
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "C00000",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                292.5
            ),
            // Two cardinal directions
            array(
                array(
                    "Sensor" => array(
                        "get" => array(
                            "id" => 0x6F,
                            "type" => "maximum-inc",
                        ),
                    ),
                ),
                "",
                0,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                292.0
            ),
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecode()
    {
        return array(
            array( // #0
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(),
                        ),
                    ),
                ),
                "00130102030405",
                array(
                    "get" => array(
                        array('extra'),
                    ),
                    "set" => array(
                        array('extra', array(6.74, 1, 2, 3, 4, 5)),
                    ),
                ),
            ),
            array( // #1
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(),
                        ),
                    ),
                ),
                "00011112131415",
                array(
                    "get" => array(
                        array('extra'),
                    ),
                    "set" => array(
                        array('extra', array(128.0, 0x11, 0x12, 0x13, 0x14, 0x15)),
                    ),
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $string The setup string to test
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDecode
    */
    public function testDecode($mocks, $string, $expect)
    {
        $this->input->resetMock($mocks);
        $this->o->decode($string);
        $ret = $this->input->retrieve("Input");
        $this->assertEquals($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataEncode()
    {
        return array(
            array( // #0
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(
                            ),
                        ),
                    ),
                ),
                "000108090C0A0B",
            ),
            array( // #1 Negative Offset
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(19, 1, 3, 5, 7, 9),
                        ),
                    ),
                ),
                "00070103050709",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $mocks  The value to preload into the mocks
    * @param array $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataEncode
    */
    public function testEncode($mocks, $expect)
    {
        $this->input->resetMock($mocks);
        $ret = $this->o->encode();
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testChannels
    *
    * @return array
    */
    public static function dataChannels()
    {
        return array(
            array(
                array(
                ),
                "",
                array(
                    array(
                        'decimals' => 2,
                        'units' => '&#176;',
                        'maxDecimals' => 2,
                        'storageUnit' => '&#176;',
                        'unitType' => 'Direction',
                        'dataType' => 'raw',
                        'label' => '',
                        'index' => 0,
                        'epChannel' => true,
                        'port' => 'Port1 Term_1,Port2 Term_2,'
                            .'Port3 Term_3,Port4 Term_4,Port5 Term_5',
                    ),
                ),
            ),
        );
    }

}

?>
