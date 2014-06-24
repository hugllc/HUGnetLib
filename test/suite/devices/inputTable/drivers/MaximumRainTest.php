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
require_once CODE_BASE.'devices/inputTable/drivers/MaximumRain.php';

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
class MaximumRainTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "MaximumRain";
    /** This is our input to use */
    protected $input;
    
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
                "Input" => array(
                    "device" => new \HUGnet\DummyBase("Device"),
                ),
                "Device" => array(
                    "get" => array(
                        "DigitalInputs" => array(1, 2, 3),
                    ),
                ),
            )
        );
        $this->o = \HUGnet\devices\inputTable\Driver::factory(
            "MaximumRain", $this->input
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
                    "Input" => array(
                        "get" => array(
                            "id" => 0x70,
                            "type" => "maximumRainGauge",
                        ),
                    ),
                ),
                500,
                300,
                array(
                    "timeConstant" => 1,
                ),
                array(
                    "raw" => 0,
                ), 
                5
            ),
            array(
                array(
                    "Input" => array(
                        "get" => array(
                            "id" => 0x70,
                            "type" => "maximumRainGauge",
                        ),
                    ),
                ),
                1000,
                300,
                array(
                    "timeConstant" => 1,
                ),
                array(
                    "raw" => 0,
                ), 
                10
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
                    "Input" => array(
                        "get" => array(
                            "id" => 0x70,
                            "type" => "maximumRainGauge",
                        ),
                    ),
                ),
                "F40100",
                300,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                5
            ),
            array(
                array(
                    "Input" => array(
                        "get" => array(
                            "id" => 0x70,
                            "type" => "maximumRainGauge",
                        ),
                    ),
                ),
                "E80300",
                300,
                array(
                    "timeConstant" => 1,
                ),
                array(),
                10
            ),

        );
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
                array(
                    array(
                        'decimals' => 2,
                        'units' => 'inches',
                        'maxDecimals' => 2,
                        'storageUnit' => 'inches',
                        'unitType' => 'Length',
                        'dataType' => 'diff',
                        'label' => '',
                        'index' => 0,
                        'epChannel' => true,
                        'port' => '1',
                    ),
                ),
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
                    "Device" => array(
                        "input" => new \HUGnet\DummyBase("Input"),
                    )
                ),
                "061320100001",
                array(
                    "Input" => array(
                        "get" => array(
                            array('extra'),
                            array('type'),
                        ),
                        "set" => array(
                            array('type', "MaximumRain"),
                            array('extra', array(19, 32, 16)),
                        ),
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
        $ret = $this->input->retrieve();
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
                            "type" => "MaximumRain",
                        ),
                    ),
                ),
                "06000003",
            ),
            array( // #1
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(0x100, 1, 2, 3),
                            "type" => "MaximumRain",
                        ),
                    ),
                ),
                "06000102",
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

}

?>
