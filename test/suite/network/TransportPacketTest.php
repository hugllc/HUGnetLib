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
namespace HUGnet\network;
/** This is a required class */
require_once CODE_BASE.'network/TransportPacket.php';
/** This is a required class */
require_once CODE_BASE.'network/packets/Packet.php';

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
class TransportPacketTest extends \PHPUnit_Framework_TestCase
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
    public static function dataTransportPacket()
    {
        return array(
            array(  // #0 everything correct
                array(
                    "timeout" => 1,
                ),
                packets\Packet::factory(
                    array(
                        "To"      => "000ABC",
                        "From"    => "000020",
                        "Command" => "03",
                        "Data"    => "01020304",
                    )
                ),
                array(
                    array(
                        "To"      => "000020",
                        "From"    => "000ABC",
                        "Command" => "01",
                        "Data"    => "01020304",
                    ),
                ),
                1,
                1,
                array(
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "01020304",
                        )
                    ),
                ),
                array(
                    packets\Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "000ABC",
                            "Command" => "01",
                            "Data"    => "01020304",
                        )
                    ),
                ),
                1.1,
            ),
            array(  // #1 No Reply
                array(
                    "ident" => "ABCDEF",
                    "timeout" => 0.9,
                ),
                array(
                    "To"      => "000ABC",
                    "From"    => "000020",
                    "Command" => "55",
                    "Data"    => "01020304",
                ),
                array(
                ),
                0.5,
                10,
                array(
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "55",
                            "Data"    => "01020304",
                        )
                    ),
                    "",
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "55",
                            "Data"    => "01020304",
                        )
                    ),
                    "",
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "FINDPING",
                            "Data"    => "ABCDEF",
                        )
                    ),
                    "",
                    false,
                    false,
                ),
                array(
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    false,
                    false,
                    false,
                    false,
                ),
                false,
            ),
            array(  // #2 Reply on 3rd & 4th try
                array(
                    "ident" => "ABCDEF",
                    "timeout" => 0.9,
                ),
                array(
                    "To"      => "000ABC",
                    "From"    => "000020",
                    "Command" => "55",
                    "Data"    => "01020304",
                ),
                array(
                    "",
                    "",
                    packets\Packet::factory(
                        array(
                            "To"        => "000020",
                            "From"      => "000ABC",
                            "Command"   => "REPLY",
                            "Data"      => "ABCDEF",
                        )
                    ),
                    packets\Packet::factory(
                        array(
                            "To"        => "000020",
                            "From"      => "000ABC",
                            "Command"   => "REPLY",
                            "Data"      => "01020304",
                        )
                    ),
                    "",
                ),
                1,
                5,
                array(
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "55",
                            "Data"    => "01020304",
                        )
                    ),
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "55",
                            "Data"    => "01020304",
                        )
                    ),
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "FINDPING",
                            "Data"    => "ABCDEF",
                        )
                    ),
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "55",
                            "Data"    => "01020304",
                        )
                    ),
                    false,
                ),
                array(
                    null,
                    null,
                    true,
                    packets\Packet::factory(
                        array(
                            "To"        => "000020",
                            "From"      => "000ABC",
                            "Command"   => "01",
                            "Data"      => "01020304",
                        )
                    ),
                    false,
                ),
                4.1,
            ),
            array(  // #3 Reply on 3rd try, no find packet
                array(
                    "ident" => "ABCDEF",
                    "find"  => false,
                    "timeout" => 0.9,
                ),
                array(
                    "To"      => "000ABC",
                    "From"    => "000020",
                    "Command" => "55",
                    "Data"    => "01020304",
                ),
                array(
                    "",
                    "",
                    packets\Packet::factory(
                        array(
                            "To"        => "000020",
                            "From"      => "000ABC",
                            "Command"   => "REPLY",
                            "Data"      => "01020304",
                        )
                    ),
                    "",
                    "",
                ),
                1,
                5,
                array(
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "55",
                            "Data"    => "01020304",
                        )
                    ),
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "55",
                            "Data"    => "01020304",
                        )
                    ),
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "55",
                            "Data"    => "01020304",
                        )
                    ),
                    false,
                ),
                array(
                    null,
                    null,
                    packets\Packet::factory(
                        array(
                            "To"        => "000020",
                            "From"      => "000ABC",
                            "Command"   => "01",
                            "Data"      => "01020304",
                        )
                    ),
                    false,
                    false,
                ),
                3.1,
            ),
            array(  // #4 valid reply data == ident
                array(
                    "ident" => "ABCDEF",
                    "timeout" => 1,
                ),
                packets\Packet::factory(
                    array(
                        "To"      => "000ABC",
                        "From"    => "000020",
                        "Command" => "03",
                        "Data"    => "01020304",
                    )
                ),
                array(
                    array(
                        "To"      => "000020",
                        "From"    => "000ABC",
                        "Command" => "01",
                        "Data"    => "ABCDEF",
                    ),
                ),
                1,
                1,
                array(
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "01020304",
                        )
                    ),
                ),
                array(
                    packets\Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "000ABC",
                            "Command" => "01",
                            "Data"    => "ABCDEF",
                        )
                    ),
                ),
                1.1,
            ),
            array(  // #5 No Reply to findping (No extra find ping sent)
                array(
                    "ident" => "ABCDEF",
                    "timeout" => 0.9,
                ),
                array(
                    "To"      => "000ABC",
                    "From"    => "000020",
                    "Command" => "FINDPING",
                    "Data"    => "01020304",
                ),
                array(
                ),
                0.5,
                10,
                array(
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "FINDPING",
                            "Data"    => "01020304",
                        )
                    ),
                    "",
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "FINDPING",
                            "Data"    => "01020304",
                        )
                    ),
                    "",
                    packets\Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "FINDPING",
                            "Data"    => "01020304",
                        )
                    ),
                    false,
                    false,
                    false,
                ),
                array(
                    null,
                    null,
                    null,
                    null,
                    null,
                    false,
                    false,
                    false,
                    false,
                    false,
                ),
                false,
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array $config The configuration array
    * @param array $packet The packet to give (array, string or object)
    * @param array $reply  The reply packet (array, string or object)
    * @param int   $pause  The number of seconds to pause
    * @param int   $loops  The number of times to loop through the software
    * @param array $send   The object returnd from send
    * @param array $expect The expected return from the reply command
    * @param float $time   The number of SECONDS to check for
    *
    * @return null
    *
    * @dataProvider dataTransportPacket()
    * @large
    */
    public function testTransportPacket(
        $config, $packet, $reply, $pause, $loops, $send, $expect, $time
    ) {
        $transPacket = TransportPacket::factory($config, $packet);
        for ($i = 0; $i < $loops; $i++) {
            $ret = $transPacket->send();
            $this->assertEquals(
                $send[$i], $ret, "The return from send is wrong.  Iteration $i"
            );
            usleep($pause * 1000000);
            $ret = &$transPacket->reply($reply[$i]);
            if (is_object($expect[$i])) {
                $this->assertEquals(
                    $expect[$i], $ret, "The return from reply is wrong. Iteration $i"
                );
            } else {
                $this->assertSame(
                    $expect[$i], $ret, "The return from reply is wrong. Iteration $i"
                );
            }
        }
        if (is_float($time)) {
            $this->assertEquals(
                $time, $transPacket->time(), "The time is wrong", 3.0
            );
        } else {
            $this->assertSame(
                $time, $transPacket->time(), "The time is wrong"
            );
        }
    }
}
?>
