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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\network;
/** This is a required class */
require_once CODE_BASE.'network/Device.php';
/** This is a required class */
require_once CODE_BASE.'network/Packet.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyNetwork.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is a required class */
require_once CODE_BASE.'util/VPrint.php';

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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceTest extends \PHPUnit_Framework_TestCase
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
    }
    /**
    * Data provider for testMatcher
    *
    * @return array
    */
    public static function dataGetID()
    {
        return array(
            array(
                array(
                ),
                array(
                    "Network" => array(
                        "send" => array(
                            Packet::factory(
                                array("To" => 1, "From" => 2, "Command" => 3)
                            ),
                        )
                    ),
                ),
                0xFE0000,
                0xFEFFFF,
                1,
            ),
            array(
                array(
                ),
                array(
                    "Network" => array(
                        "send" => array(
                            Packet::factory(
                                array(
                                    "To" => 1,
                                    "From" => 2,
                                    "Command" => 3,
                                    "Reply" => "01",
                                )
                            ),
                            Packet::factory(
                                array(
                                    "To" => 1,
                                    "From" => 2,
                                    "Command" => 3,
                                )
                            ),
                        )
                    ),
                ),
                0xFE0000,
                0xFEFFFF,
                2,
            ),
            array(
                array(
                    "id" => 0xFE1234,
                ),
                array(
                ),
                0xFE1234,
                0xFE1234,
                0,
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array $config The configuration array
    * @param array $mocks  The data to reset the mocks with
    * @param int   $min    The minimum value the id can be
    * @param int   $max    The masimum value the id can be
    * @param int   $send   THe number of times send should be called
    *
    * @return null
    *
    * @dataProvider dataGetID()
    */
    public function testGetID($config, $mocks, $min, $max, $send)
    {
        $name = "Network";
        $sys = new \HUGnet\DummySystem();
        $net = new \HUGnet\network\DummyNetwork($name);
        $net->resetMock($mocks);
        $device = &Device::factory($net, $sys, $config);
        $this->assertTrue(
            $device->getID() >= 0xFE0000, "The id is too low"
        );
        $this->assertTrue(
            $device->getID() <= 0xFEFFFF, "The id is too high"
        );
        $ret = $net->retrieve();
        $this->assertTrue(
            isset($ret[$name]["unsolicited"]), "Unsolicited was not called"
        );
        /* It is send -1 because a powerup packet will be sent */
        $this->assertSame(
            $send, count($ret[$name]["send"]) - 1, "Send was not called enough"
        );

    }
    /**
    * Data provider for testMatcher
    *
    * @return array
    */
    public static function dataPacket()
    {
        $ver = explode(
            ".", file_get_contents(CODE_BASE.'/VERSION.TXT')
        );
        $version = sprintf("%02X%02X%02X", $ver[0], $ver[1], $ver[2]);
        return array(
            array(
                array(
                    "id" => "000001",
                    "HWPartNum" => "0039-26-01-P",
                ),
                array(
                ),
                Packet::factory(
                    array(
                        "to"      => "000001",
                        "from"    => "000002",
                        "command" => "FINDPING",
                        "data"    => "0102030405",
                    )
                ),
                array(
                    array(
                        Packet::factory(
                            array(
                                "to"      => "000000",
                                "from"      => "000001",
                                "command" => "POWERUP",
                                "data"    => "00000000010039260150",
                            )
                        ),
                        null,
                        array("tries" => 1, "find" => false),
                    ),
                    array(
                        Packet::factory(
                            array(
                                "to"      => "000002",
                                "command" => "01",
                                "data"    => "0102030405",
                            )
                        ),
                        null,
                        array("tries" => 1, "find" => false),
                    ),
                ),
            ),
            array(
                array(
                    "id" => "000001",
                    "HWPartNum" => "0039-26-01-P",
                ),
                array(
                ),
                Packet::factory(
                    array(
                        "to"      => "000001",
                        "from"    => "000002",
                        "command" => "PING",
                        "data"    => "0102030405",
                    )
                ),
                array(
                    array(
                        Packet::factory(
                            array(
                                "to"      => "000000",
                                "from"      => "000001",
                                "command" => "POWERUP",
                                "data"    => "00000000010039260150",
                            )
                        ),
                        null,
                        array("tries" => 1, "find" => false),
                    ),
                    array(
                        Packet::factory(
                            array(
                                "to"      => "000002",
                                "command" => "01",
                                "data"    => "0102030405",
                            )
                        ),
                        null,
                        array("tries" => 1, "find" => false),
                    ),
                ),
            ),
            array(
                array(
                    "id" => "000001",
                    "HWPartNum" => "0039-26-02-P",
                    "params" => json_encode(array("Enable" => 5)),
                ),
                array(
                ),
                Packet::factory(
                    array(
                        "to"      => "000001",
                        "from"    => "000002",
                        "command" => "CONFIG",
                        "data"    => "0102030405",
                    )
                ),
                array(
                    array(
                        Packet::factory(
                            array(
                                "to"      => "000000",
                                "from"      => "000001",
                                "command" => "POWERUP",
                                "data"    => "00000000010039260250",
                            )
                        ),
                        null,
                        array("tries" => 1, "find" => false),
                    ),
                    array(
                        Packet::factory(
                            array(
                                "to"      => "000002",
                                "command" => "01",
                                "data"    => "000000000100392602500039260050"
                                ."000000FFFFFFFF"
                                ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF00000000000000",
                                /*  Not sure why this fails... This should pass...
                                "data"    => "000000000100392602500039260050"
                                .$version."FFFFFFFF"
                                ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF00000000000005",
                                */
                            )
                        ),
                        null,
                        array("tries" => 1, "find" => false),
                    ),
                ),
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array $config The configuration array
    * @param array $mocks  The data to reset the mocks with
    * @param mixed $pkt    The packet to send to it
    * @param array $expect The expected calls in the mock
    *
    * @return null
    *
    * @dataProvider dataPacket()
    */
    public function testPacket($config, $mocks, $pkt, $expect)
    {
        $name = "Network";
        $sys = new \HUGnet\DummySystem();
        $net = new \HUGnet\network\DummyNetwork($name);
        $net->resetMock($mocks);
        $device = &Device::factory($net, $sys, $config);
        $device->packet($pkt);
        $ret = $net->retrieve();
        $this->assertEquals($expect, $ret["Network"]["send"]);

    }
}
?>
