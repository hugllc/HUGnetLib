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
namespace HUGnet\network;
/** This is a required class */
require_once CODE_BASE.'network/Network.php';
/** This is a required class */
require_once CODE_BASE.'network/packets/Packet.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySocket.php';
/** This is a required class */
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
class NetworkTest extends \PHPUnit_Framework_TestCase
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
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataNetwork()
    {
        return array(
            array(  // #0 // No sockets
                array(
                ),
                array(
                    "noLocal" => true,
                ),
                1,
                array(
                    "asdf",
                ),
                array(
                ),
                "",
                "RuntimeException",
            ),
            array(  // #1 Already set up class
                array(
                    "defaultSocket" => array(
                        "read" => (string)packets\Packet::factory(
                            array(
                                "From" => 0x123456,
                                "To" => 0x000020,
                                "Command" => 0x01,
                                "Data" => "0102030405060708090A0B0C0D0E0F",
                            )
                        ),
                        "write" => 12,
                    ),
                ),
                array(
                    "default" => new \HUGnet\network\physical\DummySocket(
                        "defaultSocket"
                    ),
                ),
                1,
                array(
                    packets\Packet::factory(
                        array(
                            "To" => 0x123456,
                            "From" => 0x000020,
                            "Command" => 0x55,
                        )
                    ),
                ),
                array(
                    "defaultSocket" => array(
                        "write" => array(
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123456,
                                        "From" => 0x000020,
                                        "Command" => 0x55,
                                    )
                                ),
                            ),
                        ),
                        "read" => array(array()),
                    ),
                ),
                array(
                    packets\Packet::factory(
                        array(
                            "From" => 0x123456,
                            "To" => 0x000020,
                            "Command" => 0x01,
                            "Data" => "0102030405060708090A0B0C0D0E0F",
                            "Iface" => "default",
                        )
                    ),
                ),
                null,
            ),
            array(  // #2 No good read data
                array(
                    "default" => array(
                        "read" => "010203040506",
                        "write" => 12,
                    ),
                ),
                array(
                    "default" => array(
                        "driver" => "DummySocket",
                    ),
                ),
                1,
                array(
                    (string)packets\Packet::factory(
                        array(
                            "To" => 0x123456,
                            "From" => 0x000020,
                            "Command" => 0x55,
                        )
                    ),
                ),
                array(
                    "default" => array(
                        "write" => array(
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123456,
                                        "From" => 0x000020,
                                        "Command" => 0x55,
                                    )
                                ),
                            ),
                        ),
                        "read" => array(array()),
                    ),
                ),
                array(
                ),
                null,
            ),
            array(  // #3 Already set up class
                array(
                    "default" => array(
                        "read" => array(
                            "",
                            (string)packets\Packet::factory(
                                array(
                                    "From" => 0x123456,
                                    "To" => 0x000020,
                                    "Command" => 0x01,
                                    "Data" => "0102030405060708090A0B0C0D0E0F",
                                )
                            ),
                        ),
                        "write" => 12,
                    ),
                    "nondefault" => array(
                        "read" => array(
                            "",
                            "",
                            (string)packets\Packet::factory(
                                array(
                                    "From" => 0x123458,
                                    "To" => 0x000020,
                                    "Command" => 0x01,
                                    "Data" => "0102030405060708090A0B0C0D0E0F",
                                )
                            ),
                        ),
                        "write" => 12,
                    ),
                ),
                array(
                    "noLocal" => true,
                    "default" => array(
                        "driver" => "DummySocket"
                    ),
                    "nondefault" => array(
                        "driver" => "DummySocket"
                    ),
                    "forward" => 0,
                ),
                10,
                array(
                    packets\Packet::factory(
                        array(
                            "To" => 0x123456,
                            "From" => 0x000020,
                            "Command" => 0x55,
                        )
                    ),
                    packets\Packet::factory(
                        array(
                            "To" => 0x123458,
                            "From" => 0x000020,
                            "Command" => "FINDPING",
                        )
                    ),
                    null,
                    null,
                    // This packet is sent out nondefault only
                    packets\Packet::factory(
                        array(
                            "To" => 0x123458,
                            "From" => 0x000020,
                            "Command" => "PING",
                        )
                    ),
                    // This packet is sent out nondefault only
                    packets\Packet::factory(
                        array(
                            "To" => 0x123458,
                            "From" => 0x000020,
                            "Command" => "PING",
                        )
                    ),
                    // This packet is sent out both because no replies to the prev
                    // packets.
                    packets\Packet::factory(
                        array(
                            "To" => 0x123458,
                            "From" => 0x000020,
                            "Command" => "PING",
                        )
                    ),
                ),
                array(
                    "default" => array(
                        "write" => array(
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123456,
                                        "From" => 0x000020,
                                        "Command" => 0x55,
                                    )
                                )
                            ),
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123458,
                                        "From" => 0x000020,
                                        "Command" => "FINDPING",
                                    )
                                ),
                            ),
                            // This is the third of these packets
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123458,
                                        "From" => 0x000020,
                                        "Command" => "PING",
                                    )
                                ),
                            ),
                        ),
                        // 10 loops means 10 calls to read
                        "read" => array(
                            array(), array(), array(), array(), array(),
                            array(), array(), array(), array(), array(),
                        ),
                    ),
                    "nondefault" => array(
                        "write" => array(
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123456,
                                        "From" => 0x000020,
                                        "Command" => 0x55,
                                    )
                                )
                            ),
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123458,
                                        "From" => 0x000020,
                                        "Command" => "FINDPING",
                                    )
                                ),
                            ),
                            // All three packets are here
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123458,
                                        "From" => 0x000020,
                                        "Command" => "PING",
                                    )
                                ),
                            ),
                            // All three packets are here
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123458,
                                        "From" => 0x000020,
                                        "Command" => "PING",
                                    )
                                ),
                            ),
                            // All three packets are here
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123458,
                                        "From" => 0x000020,
                                        "Command" => "PING",
                                    )
                                ),
                            ),
                        ),
                        // 10 loops means 10 calls to read
                        "read" => array(
                            array(), array(), array(), array(), array(),
                            array(), array(), array(), array(), array(),
                        ),
                    ),
                ),
                array(
                    "",
                    packets\Packet::factory(
                        array(
                            "From" => 0x123456,
                            "To" => 0x000020,
                            "Command" => 0x01,
                            "Data" => "0102030405060708090A0B0C0D0E0F",
                            "Iface" => "default",
                        )
                    ),
                    packets\Packet::factory(
                        array(
                            "From" => 0x123458,
                            "To" => 0x000020,
                            "Command" => 0x01,
                            "Data" => "0102030405060708090A0B0C0D0E0F",
                            "Iface" => "nondefault",
                        )
                    ),
                ),
                null,
            ),
            array(  // #4 forwarding test
                array(
                    "default" => array(
                        "read" => (string)packets\Packet::factory(
                            array(
                                "From" => 0x123456,
                                "To" => 0x000020,
                                "Command" => 0x01,
                                "Data" => "0102030405060708090A0B0C0D0E0F",
                            )
                        ),
                    ),
                    "default2" => array(
                        "read" => (string)packets\Packet::factory(
                            array(
                                "From" => 0x123457,
                                "To" => 0x000021,
                                "Command" => 0x01,
                                "Data" => "0102030405060708090A0B0C0D0E0F",
                            )
                        ),
                    ),
                ),
                array(
                    "default" => array(
                        "driver" => "DummySocket",
                    ),
                    "default2" => array(
                        "driver" => "DummySocket",
                    ),
                    "forward" => true,
                ),
                3,
                array(
                    packets\Packet::factory(
                        array(
                            "To" => 0x123456,
                            "From" => 0x000020,
                            "Command" => 0x55,
                        )
                    ),
                ),
                array(
                    "default" => array(
                        "write" => array(
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123456,
                                        "From" => 0x000020,
                                        "Command" => 0x55,
                                    )
                                ),
                            ),
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "From" => 0x123457,
                                        "To" => 0x000021,
                                        "Command" => 0x01,
                                        "Data" => "0102030405060708090A0B0C0D0E0F",
                                    )
                                ),
                            ),
                        ),
                        "read" => array(array(), array(), array()),
                    ),
                    "default2" => array(
                        "write" => array(
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123456,
                                        "From" => 0x000020,
                                        "Command" => 0x55,
                                    )
                                ),
                            ),
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "From" => 0x123456,
                                        "To" => 0x000020,
                                        "Command" => 0x01,
                                        "Data" => "0102030405060708090A0B0C0D0E0F",
                                    )
                                ),
                            ),
                        ),
                        "read" => array(array(), array(), array()),
                    ),
                ),
                array(
                    packets\Packet::factory(
                        array(
                            "From" => 0x123456,
                            "To" => 0x000020,
                            "Command" => 0x01,
                            "Data" => "0102030405060708090A0B0C0D0E0F",
                            "Iface" => "default",
                        )
                    ),
                    packets\Packet::factory(
                        array(
                            "From" => 0x123457,
                            "To" => 0x000021,
                            "Command" => 0x01,
                            "Data" => "0102030405060708090A0B0C0D0E0F",
                            "Iface" => "default2",
                        )
                    ),
                ),
                null,
            ),
            array(  // #5 Bad packet in the queue
                array(
                    "defaultSocket" => array(
                        "read" => array(
                            "5A5A5A01000020123456011101",
                            (string)packets\Packet::factory(
                                array(
                                    "From" => 0x123456,
                                    "To" => 0x000020,
                                    "Command" => 0x01,
                                    "Data" => "0102030405060708090A0B0C0D0E0F",
                                )
                            ),
                        ),
                        //"write" => array(0, 12),
                    ),
                ),
                array(
                    "default" => new \HUGnet\network\physical\DummySocket(
                        "defaultSocket"
                    ),
                ),
                2,
                array(
                    packets\Packet::factory(
                        array(
                            "To" => 0x123456,
                            "From" => 0x000020,
                            "Command" => 0x55,
                        )
                    ),
                ),
                array(
                    "defaultSocket" => array(
                        "write" => array(
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123456,
                                        "From" => 0x000020,
                                        "Command" => 0x55,
                                    )
                                ),
                            ),
                        ),
                        "read" => array(array(), array()),
                    ),
                ),
                array(
                    null,
                    packets\Packet::factory(
                        array(
                            "From" => 0x123456,
                            "To" => 0x000020,
                            "Command" => 0x01,
                            "Data" => "0102030405060708090A0B0C0D0E0F",
                            "Iface" => "default",
                        )
                    ),
                ),
                null,
            ),
            array(  // #6 Many packets to read at once
                array(
                    "defaultSocket" => array(
                        "read" => (string)packets\Packet::factory(
                            array(
                                "From" => 0x123456,
                                "To" => 0x000020,
                                "Command" => 0x01,
                                "Data" => "0102030405060708090A0B0C0D0E0F",
                            )
                        ).
                        (string)packets\Packet::factory(
                            array(
                                "From" => 0x123457,
                                "To" => 0x000020,
                                "Command" => 0x01,
                                "Data" => "0102030405060708090A0B0C0D0E0F",
                            )
                        ).
                        (string)packets\Packet::factory(
                            array(
                                "From" => 0x123458,
                                "To" => 0x000020,
                                "Command" => 0x01,
                                "Data" => "0102030405060708090A0B0C0D0E0F",
                            )
                        ).
                        (string)packets\Packet::factory(
                            array(
                                "From" => 0x123459,
                                "To" => 0x000020,
                                "Command" => 0x01,
                                "Data" => "0102030405060708090A0B0C0D0E0F",
                            )
                        ).
                        (string)packets\Packet::factory(
                            array(
                                "From" => 0x12345A,
                                "To" => 0x000020,
                                "Command" => 0x01,
                                "Data" => "0102030405060708090A0B0C0D0E0F",
                            )
                        ),
                        "write" => 12,
                    ),
                ),
                array(
                    "noLocal" => true,
                    "default" => new \HUGnet\network\physical\DummySocket(
                        "defaultSocket"
                    ),
                ),
                6,
                array(
                    packets\Packet::factory(
                        array(
                            "To" => 0x123456,
                            "From" => 0x000020,
                            "Command" => 0x55,
                        )
                    ),
                ),
                array(
                    "defaultSocket" => array(
                        "write" => array(
                            array(
                                (string)packets\Packet::factory(
                                    array(
                                        "To" => 0x123456,
                                        "From" => 0x000020,
                                        "Command" => 0x55,
                                    )
                                ),
                            ),
                        ),
                        "read" => array(
                            array(), array(), array(), array(), array(), array()
                        ),
                    ),
                ),
                array(
                    packets\Packet::factory(
                        array(
                            "From" => 0x123456,
                            "To" => 0x000020,
                            "Command" => 0x01,
                            "Data" => "0102030405060708090A0B0C0D0E0F",
                            "Extra" => "5A5A5A010000201234570F0102030405060708090"
                                ."A0B0C0D0E0F5F5A5A5A010000201234580F0102030405060"
                                ."708090A0B0C0D0E0F505A5A5A010000201234590F0102030"
                                ."405060708090A0B0C0D0E0F515A5A5A0100002012345A0F0"
                                ."102030405060708090A0B0C0D0E0F52",
                            "Iface" => "default",
                        )
                    ),
                    packets\Packet::factory(
                        array(
                            "From" => 0x123457,
                            "To" => 0x000020,
                            "Command" => 0x01,
                            "Data" => "0102030405060708090A0B0C0D0E0F",
                            "Extra" => "5A5A5A010000201234580F0102030405060"
                                ."708090A0B0C0D0E0F505A5A5A010000201234590F0102030"
                                ."405060708090A0B0C0D0E0F515A5A5A0100002012345A0F0"
                                ."102030405060708090A0B0C0D0E0F52",
                            "Iface" => "default",
                        )
                    ),
                    packets\Packet::factory(
                        array(
                            "From" => 0x123458,
                            "To" => 0x000020,
                            "Command" => 0x01,
                            "Data" => "0102030405060708090A0B0C0D0E0F",
                            "Extra" => "5A5A5A010000201234590F0102030"
                                ."405060708090A0B0C0D0E0F515A5A5A0100002012345A0F0"
                                ."102030405060708090A0B0C0D0E0F52",
                            "Iface" => "default",
                        )
                    ),
                    packets\Packet::factory(
                        array(
                            "From" => 0x123459,
                            "To" => 0x000020,
                            "Command" => 0x01,
                            "Data" => "0102030405060708090A0B0C0D0E0F",
                            "Extra" => "5A5A5A0100002012345A0F0"
                                ."102030405060708090A0B0C0D0E0F52",
                            "Iface" => "default",
                        )
                    ),
                    packets\Packet::factory(
                        array(
                            "From" => 0x12345A,
                            "To" => 0x000020,
                            "Command" => 0x01,
                            "Data" => "0102030405060708090A0B0C0D0E0F",
                            "Extra" => "",
                            "Iface" => "default",
                        )
                    ),
                    false,
                ),
                null,
            ),
            array(  // #7 Bad Driver
                array(
                ),
                array(
                    "default" => array(
                        "driver" => "ThisIsNotAGoodDriverName",
                    ),
                ),
                1,
                array(
                    "asdf",
                ),
                array(
                ),
                "",
                "RuntimeException",
            ),
            array(  // #8 Only local
                array(
                ),
                array(
                ),
                1,
                array(
                    packets\Packet::factory(
                        array(
                            "To" => 0x123456,
                            "From" => 0x000020,
                            "Command" => 0x55,
                        )
                    ),
                ),
                array(
                ),
                array(
                    packets\Packet::factory(
                        array(
                            "To" => 0x123456,
                            "From" => 0x000020,
                            "Command" => 0x55,
                            "Data" => "",
                            "Iface" => "lo",
                        )
                    ),
                ),
                null,
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array  $system    The string to give to the class
    * @param array  $config    The configuration to send the class
    * @param int    $loops     The number of times to call read and write
    * @param array  $send      Array of strings to send out
    * @param array  $expect    The info to expect returned
    * @param array  $recv      Array of objects to expect received
    * @param string $exception The exception to expect.  Null for none
    *
    * @return null
    *
    * @dataProvider dataNetwork()
    */
    public function testNetwork(
        $system, $config, $loops, $send, $expect, $recv, $exception
    ) {
        if (!is_null($exception)) {
            $this->setExpectedException($exception);
        }
        $sys = new \HUGnet\DummySystem("HUGnetSystem");
        $sys->resetMock($system);
        $network = &Network::factory($sys, $config);
        for ($i = 0; $i < $loops; $i++) {
            if (isset($send[$i])) {
                $network->send($send[$i]);
            }
            $ret = $network->receive();
            if (isset($recv[$i])) {
                $this->assertEquals($recv[$i], $ret, "Return wrong ($i)");
            }
        }
        foreach ($expect as $name => $args) {
            $this->assertEquals($args, $sys->retrieve($name), "$name Calls wrong");
        }
    }

}
?>
