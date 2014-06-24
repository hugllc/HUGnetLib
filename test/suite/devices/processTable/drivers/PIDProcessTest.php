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
require_once CODE_BASE.'devices/processTable/drivers/PIDProcess.php';

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
class PIDProcessTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "PIDProcess";
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
            "PIDProcess", $this->process
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
                        "dataChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
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
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                    ),
                    "DataChan3" => array(
                        "decode" => array(
                            "05000000" => 5,
                        ),
                        "get" => array(
                            "channel" => 3,
                        ),
                    ),
                ),
                "0102030400000005000000060000000600000007000000080009000000"
                    ."000000000010000000FF",
                array(
                    array(
                        'extra',
                        array(
                            0 => 128,
                            1 => 2,
                            2 => 3,
                            3 => 4,
                            4 => 5,
                            5 => 6,
                            6 => 6.0,
                            7 => 7.0,
                            8 => 8.0,
                            9 => 9,
                            10 => 0,
                            11 => 4096,
                            12 => 0,
                            13 => 0xFF,
                        )
                    ),
                ),
            ),
            array( // #1
                array(
                    "Process" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                        "dataChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
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
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                    ),
                    "DataChan3" => array(
                        "decode" => array(
                            "05000000" => 5,
                        ),
                        "get" => array(
                            "channel" => 3,
                        ),
                    ),
                ),
                "0102030400000005000000060001000600100007000010080009000000"
                    ."00000000001000000102",
                array(
                    array(
                        'extra',
                        array(
                            0 => 128,
                            1 => 2,
                            2 => 3,
                            3 => 4,
                            4 => 5,
                            5 => 6,
                            6 => 6.000015,
                            7 => 7.000244,
                            8 => 8.0625,
                            9 => 9,
                            10 => 0,
                            11 => 4096,
                            12 => 1,
                            13 => 2,
                        )
                    ),
                ),
            ),
            array( // #2
                array(
                    "Process" => array(
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                        "dataChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
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
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                    ),
                    "DataChan3" => array(
                        "decode" => array(
                            "05000000" => 5,
                        ),
                        "get" => array(
                            "channel" => 3,
                        ),
                    ),
                ),
                "010203FFFFFFFF050000000600FFFFFFFFFFFFFFFFFFFFFFFF09000000"
                    ."000000000010000000FF",
                array(
                    array(
                        'extra',
                        array(
                            0 => 128,
                            1 => 2,
                            2 => 3,
                            3 => -1,
                            4 => 5,
                            5 => 6,
                            6 => -1.5E-5,
                            7 => -1.5E-5,
                            8 => -1.5E-5,
                            9 => 9,
                            10 => 0,
                            11 => 4096,
                            12 => 0,
                            13 => 0xFF,
                        )
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
        $this->assertEquals($expect, $ret["set"], "Return Wrong", 0.0001);
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
                            'extra' => array(
                                0 => 128,
                                1 => 2,
                                2 => 3,
                                3 => 4,
                                4 => 5,
                                5 => 6,
                                6 => 6,
                                7 => 7,
                                8 => 8,
                                9 => 9,
                                10 => -16,
                                11 => -1,
                            )
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                        "dataChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                        "controlChannels" => new \HUGnet\DummyBase("cChannels"),
                    ),
                    "Channels" => array(
                    ),
                    "DataChan3" => array(
                        "encode" => array(
                            "5" => "05000000",
                        ),
                        "get" => array(
                            "channel" => 3,
                            "epChannel" => 3,
                        ),
                    ),
                    "cChannels" => array(
                        "controlChannel" => new \HUGnet\DummyBase("cChannel"),
                    ),
                    "cChannel" => array(
                        "get" => array(
                            "min" => -16,
                            "max" => -1,
                        ),
                    )
                ),
                "0102030400000005000000060000000600000007000000080009000000"
                    ."F0FFFFFFFFFFFFFF00FF",
            ),
            array( // #1
                array(
                    "Process" => array(
                        "get" => array(
                            'extra' => array(
                                0 => 128,
                                1 => 2,
                                2 => 3,
                                3 => 4,
                                4 => 5,
                                5 => 6,
                                6 => 6.000016,
                                7 => 7.000254,
                                8 => 8.0625,
                                9 => 9,
                                10 => 100,
                                11 => 4000,
                                12 => 1,
                                13 => 2,
                            )
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                        "dataChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                        "controlChannels" => new \HUGnet\DummyBase("cChannels"),
                    ),
                    "DataChan3" => array(
                        "encode" => array(
                            "5" => "05000000",
                        ),
                        "get" => array(
                            "channel" => 3,
                            "epChannel" => 5,
                        ),
                    ),
                    "Channels" => array(
                    ),
                    "cChannels" => array(
                        "controlChannel" => new \HUGnet\DummyBase("cChannel"),
                    ),
                    "cChannel" => array(
                        "get" => array(
                            "min" => 0,
                            "max" => 4096,
                        ),
                    )
                ),
                "010205040000000500000006000100060010000700001008000900000064"
                    ."000000A00F00000102",
            ),
            array( // #2  DataChan return too short
                array(
                    "Process" => array(
                        "get" => array(
                            'extra' => array(
                                0 => 128,
                                1 => 2,
                                2 => 3,
                                3 => 4,
                                4 => 5,
                                5 => 6,
                                6 => 6.000016,
                                7 => 7.000254,
                                8 => 8.0625,
                                9 => 9,
                            )
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                        "dataChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                        "controlChannels" => new \HUGnet\DummyBase("cChannels"),
                    ),
                    "DataChan3" => array(
                        "encode" => array(
                            "5" => "05",
                        ),
                        "get" => array(
                            "channel" => 3,
                            "epChannel" => 5,
                        ),
                    ),
                    "Channels" => array(
                    ),
                    "cChannels" => array(
                        "controlChannel" => new \HUGnet\DummyBase("cChannel"),
                    ),
                    "cChannel" => array(
                        "get" => array(
                            "min" => 0,
                            "max" => 4096,
                        ),
                    )
                ),
                "0102050400000005000000060001000600100007000010080009000000"
                    ."000000000010000000FF",
            ),
            array( // #3  DataChan return too long
                array(
                    "Process" => array(
                        "get" => array(
                            'extra' => array(
                                0 => 128,
                                1 => 2,
                                2 => 3,
                                3 => 4,
                                4 => 5,
                                5 => 6,
                                6 => 6.000016,
                                7 => 7.000254,
                                8 => 8.0625,
                                9 => 9,
                            )
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                        "dataChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                        "controlChannels" => new \HUGnet\DummyBase("cChannels"),
                    ),
                    "DataChan3" => array(
                        "encode" => array(
                            "5" => "0500000000",
                        ),
                        "get" => array(
                            "channel" => 3,
                            "epChannel" => 5,
                        ),
                    ),
                    "Channels" => array(
                    ),
                    "cChannels" => array(
                        "controlChannel" => new \HUGnet\DummyBase("cChannel"),
                    ),
                    "cChannel" => array(
                        "get" => array(
                            "min" => 0,
                            "max" => 4096,
                        ),
                    )
                ),
                "0102050400000005000000060001000600100007000010080009000000"
                    ."000000000010000000FF",
            ),
            array( // #4
                array(
                    "Process" => array(
                        "get" => array(
                            'extra' => array(
                                0 => 128,
                                1 => 2,
                                2 => 3,
                                3 => -1,
                                4 => 5,
                                5 => 6,
                                6 => -1,
                                7 => -1,
                                8 => -1,
                                9 => 9,
                            )
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                        "dataChannel" => array(
                            "0" => new \HUGnet\DummyBase("DataChan0"),
                            "1" => new \HUGnet\DummyBase("DataChan1"),
                            "2" => new \HUGnet\DummyBase("DataChan2"),
                            "3" => new \HUGnet\DummyBase("DataChan3"),
                            "4" => new \HUGnet\DummyBase("DataChan4"),
                        ),
                        "controlChannels" => new \HUGnet\DummyBase("cChannels"),
                    ),
                    "Channels" => array(
                    ),
                    "DataChan3" => array(
                        "encode" => array(
                            "5" => "05000000",
                        ),
                        "get" => array(
                            "channel" => 3,
                            "epChannel" => 3,
                        ),
                    ),
                    "cChannels" => array(
                        "controlChannel" => new \HUGnet\DummyBase("cChannel"),
                    ),
                    "cChannel" => array(
                        "get" => array(
                            "min" => 0,
                            "max" => 4096,
                        ),
                    )
                ),
                "010203FFFFFFFF0500000006000000FFFF0000FFFF0000FFFF09000000"
                    ."000000000010000000FF",
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
