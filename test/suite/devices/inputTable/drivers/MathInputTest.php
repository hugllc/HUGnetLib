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
namespace HUGnet\devices\inputTable\drivers;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBase.php";
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/drivers/MathInput.php';

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
class MathInputTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "MathInput";
    /** This is the input class */
    protected $input;
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
        $this->input = new \HUGnet\DummyBase("Input");
        $this->input->resetMock(
            array(
                "Input" => array(
                    "device" => new \HUGnet\DummyBase("Device"),
                ),
                "Device" => array(
                    "dataChannels" => new \HUGnet\DummyBase("dataChannels"),
                    "controlChannels" => new \HUGnet\DummyBase("controlChannels"),
                ),
                "dataChannels" => array(
                    "select" => array(),
                ),
                "controlChannels" => array(
                    "select" => array(),
                ),
            )
        );
        $this->o = \HUGnet\devices\inputTable\Driver::factory(
            "MathInput", $this->input
        );
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
     * Data provider for testGetReading
     *
     * testGetReading($sensor, $A, $deltaT, $data, $prev, $expect)
     *
     * @return array
     */
    public static function dataGetReading()
    {
        return array(
            array(
                array(),
                256210,
                1,
                array(),
                array(),
                256210,
            ),
            array(
                array(),
                0xFFFFFFFF,
                1,
                array(),
                array(),
                -1,
            ),
        );
    }
    /**
     * Data provider for testGetReading
     *
     * testGetReading($sensor, $A, $deltaT, $data, $prev, $expect)
     *
     * @return array
     */
    public static function dataEncodeDataPoint()
    {
        return array(
            array(
                array(),
                "D2E80300",
                1,
                array(),
                array(),
                0x03E8D2,
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
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                    ),
                    "Channels" => array(
                        "dataChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                        "epChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan3"),
                            "3" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                    ),
                    "DataChan0" => array(
                        "get" => array(
                            "channel" => 0,
                            "decimals" => 0,
                        ),
                    ),
                    "DataChan1" => array(
                        "get" => array(
                            "channel" => 1,
                            "decimals" => 0,
                        ),
                    ),
                    "DataChan2" => array(
                        "get" => array(
                            "channel" => 2,
                            "decimals" => 0,
                        ),
                    ),
                    "DataChan3" => array(
                        "decode" => array(
                            "12345678" => "12",
                            "11223344" => "14",
                        ),
                        "get" => array(
                            "channel" => 3,
                            "decimals" => 0,
                        ),
                    ),
                    "DataChan4" => array(
                        "get" => array(
                            "channel" => 4,
                            "decimals" => 0,
                        ),
                    ),
                ),
                "13010203",
                array(
                    "get" => array(
                        array('extra'),
                    ),
                    "set" => array(
                        array('extra', array(19, 1, 2, 4)),
                    ),
                    "device" => array(
                        array(),
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
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                    ),
                    "Channels" => array(
                        "dataChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                        "epChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan3"),
                            "3" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                    ),
                    "DataChan0" => array(
                        "get" => array(
                            "channel" => 0,
                            "decimals" => 0,
                            "epChannel" => 0,
                        ),
                    ),
                    "DataChan1" => array(
                        "get" => array(
                            "channel" => 1,
                            "decimals" => 0,
                            "epChannel" => 1,
                        ),
                    ),
                    "DataChan2" => array(
                        "get" => array(
                            "channel" => 2,
                            "decimals" => 0,
                            "epChannel" => 2,
                        ),
                    ),
                    "DataChan3" => array(
                        "decode" => array(
                            "12345678" => "12",
                            "11223344" => "14",
                        ),
                        "get" => array(
                            "channel" => 3,
                            "decimals" => 0,
                            "epChannel" => 3,
                        ),
                    ),
                    "DataChan4" => array(
                        "get" => array(
                            "channel" => 4,
                            "decimals" => 0,
                            "epChannel" => 4,
                        ),
                    ),
                ),
                "01000100",
            ),
            array( // #1
                array(
                    "Input" => array(
                        "get" => array(
                            "extra" => array(19, 1, 2, 3),
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                    ),
                    "Channels" => array(
                        "dataChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                        "epChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan3"),
                            "3" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                    ),
                    "DataChan0" => array(
                        "get" => array(
                            "channel" => 0,
                            "decimals" => 0,
                            "epChannel" => 1,
                        ),
                    ),
                    "DataChan1" => array(
                        "get" => array(
                            "channel" => 1,
                            "decimals" => 0,
                            "epChannel" => 2,
                        ),
                    ),
                    "DataChan2" => array(
                        "get" => array(
                            "channel" => 2,
                            "decimals" => 0,
                            "epChannel" => 3,
                        ),
                    ),
                    "DataChan3" => array(
                        "decode" => array(
                            "12345678" => "12",
                            "11223344" => "14",
                        ),
                        "get" => array(
                            "channel" => 3,
                            "decimals" => 0,
                            "epChannel" => 4,
                        ),
                    ),
                    "DataChan4" => array(
                        "get" => array(
                            "channel" => 4,
                            "decimals" => 0,
                            "epChannel" => 5,
                        ),
                    ),
                ),
                "13020204",
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
