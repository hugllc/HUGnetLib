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
namespace HUGnet\devices\processTable\drivers;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBase.php";
/** This is a required class */
require_once CODE_BASE.'devices/processTable/drivers/LevelHolderProcess.php';

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
class LevelHolderProcessTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "LevelHolderProcess";
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
            "LevelHolderProcess", $this->process
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
                "22010214060000C20D0000021234567811223344",
                array(
                    "get" => array(
                        array('extra'),
                    ),
                    "set" => array(
                        array(
                            'extra',
                            array(
                                0 => 34,
                                1 => 1,
                                2 => 2,
                                3 => 3,
                                4 => 13.0,
                                5 => 1.0,
                            )
                        ),
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
                        "getExtra" => array(
                        ),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Device" => array(
                        "dataChannels" => new \HUGnet\DummyBase("Channels"),
                        "controlChannels" => new \HUGnet\DummyBase("cChannels"),
                    ),
                    "Channels" => array(
                        "get" => array(
                            "epChannel" => 1,
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
                "22000214060000C20D0000",
            ),
            array( // #1
                array(
                    "Process" => array(
                        "get" => array(
                            "extra" => array(
                                1 => 1,
                                3 => 2,
                                4 => 13,
                                5 => 1,
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
                        ),
                    ),
                    "DataChan0" => array(
                    ),
                    "DataChan1" => array(
                    ),
                    "DataChan2" => array(
                        "encode" => array(
                            "12" => "12345678",
                            "14" => "11223344",
                        ),
                        "get" => array(
                            "epChannel" => 1,
                        ),
                    ),
                    "DataChan3" => array(
                    ),
                    "DataChan4" => array(
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
                "22010214060000C20D0000011234567811223344",
            ),
            array( // #2 DataChan return too big
                array(
                    "Process" => array(
                        "get" => array(
                            "extra" => array(
                                1 => 1,
                                3 => 2,
                                4 => 13,
                                5 => 1,
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
                        ),
                    ),
                    "DataChan0" => array(
                    ),
                    "DataChan1" => array(
                    ),
                    "DataChan2" => array(
                        "encode" => array(
                            "12" => "1234567890",
                            "14" => "1122334412",
                        ),
                        "get" => array(
                            "epChannel" => 1,
                        ),
                    ),
                    "DataChan3" => array(
                    ),
                    "DataChan4" => array(
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
                "22010214060000C20D0000011234567811223344",
            ),
            array( // #3 DataChan return too short
                array(
                    "Process" => array(
                        "get" => array(
                            "extra" => array(
                                1 => 1,
                                3 => 2,
                                4 => 13,
                                5 => 1,
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
                        ),
                    ),
                    "DataChan0" => array(
                    ),
                    "DataChan1" => array(
                    ),
                    "DataChan2" => array(
                        "encode" => array(
                            "12" => "12",
                            "14" => "11",
                        ),
                        "get" => array(
                            "epChannel" => 1,
                        ),
                    ),
                    "DataChan3" => array(
                    ),
                    "DataChan4" => array(
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
                "22010214060000C20D0000011200000011000000",
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
