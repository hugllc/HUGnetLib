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
namespace HUGnet\devices\processTable\drivers;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBase.php";
/** This is a required class */
require_once CODE_BASE.'devices/processTable/drivers/QuadFetProcess.php';

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
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class QuadFetProcessTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "QuadFetProcess";
    /** This is the process */
    protected $process;
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
        $this->process = new \HUGnet\DummyBase("Process");
        $this->process->resetMock(
            array(
                "Process" => array(
                    "device" => new \HUGnet\DummyBase("Device")
                ),
                "Device" => array(
                    "controlChannels" => new \HUGnet\DummyBase("ControlChannels"),
                    "dataChannels" => new \HUGnet\DummyBase("DataChannels"),
                ),
                "ControlChannels" => array(
                    "select" => array("asdf", "fdsa"),
                ),
                "DataChannels" => array(
                    "select" => array("asdf", "fdsa"),
                ),
            )
        );
        $this->o = \HUGnet\devices\processTable\Driver::factory(
            "QuadFetProcess", $this->process
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
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecode()
    {
        return array(
            array( // #0
                array(
                    "Process" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                        "controlChannels" => new \HUGnet\DummyBase("cChannels"),
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
                        "decode" => array(
                            "12345678" => "9",
                            "11223344" => "7",
                            "87654321" => "8",
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
                            "87654321" => "13",
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
                    "cChannels" => array(
                        "controlChannel" => new \HUGnet\DummyBase("cChannel"),
                    ),
                    "cChannel" => array(
                        "get" => array(
                            "min" => 0,
                            "max" => 100,
                        ),
                    )
                ),
                "010102000D0001000000E8030000A00F0000401F0000",
                array(
                    "get" => array(
                        array('extra'),
                    ),
                    "set" => array(
                        array(
                            'extra',
                            array(
                                0 => 128.0,
                                1 => 1,
                                2 => 2,
                                3 => 13,
                                4 => 1,
                                5 => 1000,
                                6 => 4000,
                                7 => 8000,
                            )
                        ),
                    ),
                ),
            ),
            array( // #1 Negative Step
                array(
                    "Process" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                        "controlChannels" => new \HUGnet\DummyBase("cChannels"),
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
                        "decode" => array(
                            "12345678" => "9",
                            "11223344" => "7",
                            "87654321" => "8",
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
                            "87654321" => "13",
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
                    "cChannels" => array(
                        "controlChannel" => new \HUGnet\DummyBase("cChannel"),
                    ),
                    "cChannel" => array(
                        "get" => array(
                            "min" => 0,
                            "max" => 1000,
                        ),
                    )
                ),
                "010102000D0001000000E8030000A00F0000C20D0000",
                array(
                    "get" => array(
                        array('extra'),
                    ),
                    "set" => array(
                        array(
                            'extra',
                            array(
                                0 => 128.0,
                                1 => 1,
                                2 => 2,
                                3 => 13,
                                4 => 1,
                                5 => 1000,
                                6 => 4000,
                                7 => 3522
                            )
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
        $this->process->resetMock($mocks);
        $this->o->decode($string);
        $ret = $this->process->retrieve("Process");
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
                    "Process" => array(
                        "get" => array(
                            "extra" => array(
                                1 => 0,
                                2 => 12,
                                3 => 0,
                                4 => 12,
                                5 => 0,
                                6 => 12,
                                7 => 0,
                                8 => 12,
                            ),
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                        "controlChannels" => new \HUGnet\DummyBase("cChannels"),
                    ),
                    "Channels" => array(
                        "dataChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                            "5" => new \HUGnet\DummyBase("DataChan5"),
                            "6" => new \HUGnet\DummyBase("DataChan6"),
                            "7" => new \HUGnet\DummyBase("DataChan7"),
                            "8" => new \HUGnet\DummyBase("DataChan8"),
                        ),
                    ),
                    "DataChan0" => array(
                        "get" => array(
                            "epChannel" => 1,
                        ),
                        "encode" => array(
                            "12" => 0x1111,
                            "14" => 0x1212,
                            "13" => 0x1313,
                        ),
                    ),
                    "DataChan1" => array(
                        "get" => array(
                            "epChannel" => 2,
                        ),
                        "encode" => array(
                            "12" => 0x2121,
                            "14" => 0x2222,
                            "13" => 0x2323,
                        ),
                    ),
                    "DataChan2" => array(
                        "get" => array(
                            "epChannel" => 3,
                        ),
                        "encode" => array(
                            "12" => 0x3131,
                            "14" => 0x3232,
                            "13" => 0x3333,
                        ),
                    ),
                    "DataChan3" => array(
                        "get" => array(
                            "epChannel" => 4,
                        ),
                        "encode" => array(
                            "12" => 0x4141,
                            "14" => 0x4242,
                            "13" => 0x4343,
                        ),
                    ),
                    "DataChan4" => array(
                        "get" => array(
                            "epChannel" => 5,
                        ),
                        "encode" => array(
                            "12" => 0x5151,
                            "14" => 0x5252,
                            "13" => 0x5353,
                        ),
                    ),
                    "DataChan5" => array(
                        "get" => array(
                            "epChannel" => 6,
                        ),
                        "encode" => array(
                            "12" => 0x6161,
                            "14" => 0x6262,
                            "13" => 0x6363,
                        ),
                    ),
                    "DataChan6" => array(
                        "get" => array(
                            "epChannel" => 7,
                        ),
                        "encode" => array(
                            "12" => 0x7171,
                            "14" => 0x7272,
                            "13" => 0x7373,
                        ),
                    ),
                    "DataChan7" => array(
                        "get" => array(
                            "epChannel" => 8,
                        ),
                        "encode" => array(
                            "12" => 0x8181,
                            "14" => 0x8282,
                            "13" => 0x8383,
                        ),
                    ),
                    "DataChan8" => array(
                        "get" => array(
                            "epChannel" => 9,
                        ),
                        "encode" => array(
                            "12" => 0x9191,
                            "14" => 0x9292,
                            "13" => 0x9393,
                        ),
                    ),
                    "cChannels" => array(
                        "controlChannel" => new \HUGnet\DummyBase("cChannel"),
                    ),
                    "cChannel" => array(
                        "get" => array(
                            "min" => 1556,
                            "max" => 3522,
                        ),
                    )
                ),
                "010204060821214141616181811406C20D",
            ),
            array( // #0
                array(
                    "Process" => array(
                        "get" => array(
                            "extra" => array(
                                1 => 1,
                                2 => 12,
                                3 => 1,
                                4 => 12,
                                5 => 1,
                                6 => 12,
                                7 => 1,
                                8 => 12,
                            ),
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                        "controlChannels" => new \HUGnet\DummyBase("cChannels"),
                    ),
                    "Channels" => array(
                        "dataChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                            "5" => new \HUGnet\DummyBase("DataChan5"),
                            "6" => new \HUGnet\DummyBase("DataChan6"),
                            "7" => new \HUGnet\DummyBase("DataChan7"),
                            "8" => new \HUGnet\DummyBase("DataChan8"),
                        ),
                    ),
                    "DataChan0" => array(
                        "get" => array(
                            "epChannel" => 1,
                        ),
                        "encode" => array(
                            "12" => 0x1111,
                            "14" => 0x1212,
                            "13" => 0x1313,
                        ),
                    ),
                    "DataChan1" => array(
                        "get" => array(
                            "epChannel" => 2,
                        ),
                        "encode" => array(
                            "12" => 0x2121,
                            "14" => 0x2222,
                            "13" => 0x2323,
                        ),
                    ),
                    "DataChan2" => array(
                        "get" => array(
                            "epChannel" => 3,
                        ),
                        "encode" => array(
                            "12" => 0x3131,
                            "14" => 0x3232,
                            "13" => 0x3333,
                        ),
                    ),
                    "DataChan3" => array(
                        "get" => array(
                            "epChannel" => 4,
                        ),
                        "encode" => array(
                            "12" => 0x4141,
                            "14" => 0x4242,
                            "13" => 0x4343,
                        ),
                    ),
                    "DataChan4" => array(
                        "get" => array(
                            "epChannel" => 5,
                        ),
                        "encode" => array(
                            "12" => 0x5151,
                            "14" => 0x5252,
                            "13" => 0x5353,
                        ),
                    ),
                    "DataChan5" => array(
                        "get" => array(
                            "epChannel" => 6,
                        ),
                        "encode" => array(
                            "12" => 0x6161,
                            "14" => 0x6262,
                            "13" => 0x6363,
                        ),
                    ),
                    "DataChan6" => array(
                        "get" => array(
                            "epChannel" => 7,
                        ),
                        "encode" => array(
                            "12" => 0x7171,
                            "14" => 0x7272,
                            "13" => 0x7373,
                        ),
                    ),
                    "DataChan7" => array(
                        "get" => array(
                            "epChannel" => 8,
                        ),
                        "encode" => array(
                            "12" => 0x8181,
                            "14" => 0x8282,
                            "13" => 0x8383,
                        ),
                    ),
                    "DataChan8" => array(
                        "get" => array(
                            "epChannel" => 9,
                        ),
                        "encode" => array(
                            "12" => 0x9191,
                            "14" => 0x9292,
                            "13" => 0x9393,
                        ),
                    ),
                    "cChannels" => array(
                        "controlChannel" => new \HUGnet\DummyBase("cChannel"),
                    ),
                    "cChannel" => array(
                        "get" => array(
                            "min" => 1556,
                            "max" => 3522,
                        ),
                    )
                ),
                "010103050711113131515171711406C20D",
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
        $this->process->resetMock($mocks);
        $ret = $this->o->encode();
        $this->assertSame($expect, $ret);
    }

}
?>
