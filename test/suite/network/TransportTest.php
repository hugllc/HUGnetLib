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
require_once CODE_BASE.'network/Transport.php';
/** This is a required class */
require_once CODE_BASE.'network/Packet.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyNetwork.php';

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
class TransportTest extends \PHPUnit_Framework_TestCase
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
    public static function dataReply()
    {
        return array(
            array(  // #0 everything correct
                array(
                    "timeout" => 1,
                ),
                array(
                    Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "01020304",
                        )
                    ),
                ),
                array(
                    "Network" => array(
                        "receive" => array(
                            null, null, null, null, null,
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "000ABD",
                                    "Command" => "POWERUP",
                                    "Data"    => "01020304",
                                )
                            ),
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "000ABC",
                                    "Command" => "01",
                                    "Data"    => "01020304",
                                )
                            ),
                        ),
                    ),
                ),
                5,
                array(
                    Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "000ABC",
                            "Command" => "01",
                            "Data"    => "01020304",
                        )
                    ),
                ),
                array(
                    Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "000ABD",
                            "Command" => "POWERUP",
                            "Data"    => "01020304",
                        )
                    ),
                ),
            ),
            array(  // #1 more requests than buffers
                array(
                    "timeout" => 1,
                    "channels" => 5,
                ),
                array(
                    Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "0102030400",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "001ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "0102030401",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "002ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "0102030402",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "003ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "0102030403",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "004ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "0102030404",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "005ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "0102030405",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "006ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "0102030406",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "007ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "0102030407",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "008ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "0102030408",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "009ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "0102030409",
                        )
                    ),
                ),
                array(
                    "Network" => array(
                        "receive" => array(
                            null, null, null, null, null,
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "000ABD",
                                    "Command" => "POWERUP",
                                    "Data"    => "01020304DD",
                                )
                            ),
                            null, null, null, null, null,
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "003ABC",
                                    "Command" => "01",
                                    "Data"    => "0102030403",
                                )
                            ),
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "002ABC",
                                    "Command" => "01",
                                    "Data"    => "0102030402",
                                )
                            ),
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "001ABC",
                                    "Command" => "01",
                                    "Data"    => "0102030401",
                                )
                            ),
                            null, null, null, null, null,
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "000ABC",
                                    "Command" => "01",
                                    "Data"    => "0102030400",
                                )
                            ),
                            null, null, null, null, null,
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "004ABC",
                                    "Command" => "01",
                                    "Data"    => "0102030404",
                                )
                            ),
                            null, null, null, null, null,
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "006ABC",
                                    "Command" => "01",
                                    "Data"    => "0102030406",
                                )
                            ),
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "005ABC",
                                    "Command" => "01",
                                    "Data"    => "0102030405",
                                )
                            ),
                            null, null, null, null, null,
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "007ABC",
                                    "Command" => "01",
                                    "Data"    => "0102030407",
                                )
                            ),
                            null, null, null, null, null,
                            null, null, null, null, null,
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "009ABC",
                                    "Command" => "01",
                                    "Data"    => "0102030409",
                                )
                            ),
                        ),
                    ),
                ),
                15,
                array(
                    // These are out of order because of the way they are
                    // retrieved from TransportPacket and how they are processed.
                    Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "001ABC",
                            "Command" => "01",
                            "Data"    => "0102030401",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "002ABC",
                            "Command" => "01",
                            "Data"    => "0102030402",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "003ABC",
                            "Command" => "01",
                            "Data"    => "0102030403",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "000ABC",
                            "Command" => "01",
                            "Data"    => "0102030400",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "004ABC",
                            "Command" => "01",
                            "Data"    => "0102030404",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "005ABC",
                            "Command" => "01",
                            "Data"    => "0102030405",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "006ABC",
                            "Command" => "01",
                            "Data"    => "0102030406",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "007ABC",
                            "Command" => "01",
                            "Data"    => "0102030407",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "009ABC",
                            "Command" => "01",
                            "Data"    => "0102030409",
                        )
                    ),
                ),
                array(
                    Packet::factory(
                        array(
                            "To"      => "000020",
                            "From"    => "000ABD",
                            "Command" => "POWERUP",
                            "Data"    => "01020304DD",
                        )
                    ),
                ),
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array $config      The configuration array
    * @param array $packet      The packet to give (array, string or object)
    * @param array $network     The reply packet (array, string or object)
    * @param int   $timeout     The number of seconds before the test times out
    * @param array $expect      The expected return from the reply command
    * @param array $unsolicited The expected unsolicited packets
    *
    * @return null
    *
    * @dataProvider dataReply()
    */
    public function testReply(
        $config, $packet, $network, $timeout, $expect, $unsolicited
    ) {
        $net = DummyNetwork::factory("Network");
        $net->resetMock($network);
        $transport = Transport::factory($net, $config);
        $return = array();
        $tokens = array();
        $index = 0;
        $time = time();
        do {
            do {
                $ret = $transport->send($packet[$index]);
                if (!is_bool($ret)) {
                    $tokens[$index] = $ret;
                    $index++;
                }
                if ((time() - $time) > $timeout) {
                    // This makes sure this is never an infinte loop
                    $this->fail("Timeout occurred!");
                }
            } while ($ret);
            foreach ($tokens as $k => $t) {
                $ret = &$transport->receive($t);
                if (is_object($ret)) {
                    $return[] = $ret;
                    unset($tokens[$k]);
                } else if ($ret === false) {
                    unset($tokens[$k]);
                }
            }
            if ((time() - $time) > $timeout) {
                // This make sure this is never an infinite loop
                $this->fail("Timeout occurred!");
            }
        } while (((count($tokens) > 0) || (count($packet) < ($index-1))));
        $this->assertEquals(
            $expect, $return, "The return is wrong"
        );
        while (!is_null($pkt = &$transport->unsolicited())) {
            $unsol[] = &$pkt;
        }
        $this->assertEquals(
            $unsolicited, $unsol, "The unsolicited packets are wrong"
        );
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataReceive()
    {
        return array(
            array(  // #0 everything correct
                array(
                    "timeout" => 1,
                ),
                array(
                    Packet::factory(
                        array(
                            "To"      => "000ABC",
                            "From"    => "000020",
                            "Command" => "03",
                            "Data"    => "01020304",
                        )
                    ),
                ),
                array(
                    "Network" => array(
                        "receive" => array(
                            null, null, null, null, null,
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "000ABD",
                                    "Command" => "POWERUP",
                                    "Data"    => "01020304",
                                )
                            ),
                            Packet::factory(
                                array(
                                    "To"      => "000020",
                                    "From"    => "000ABC",
                                    "Command" => "01",
                                    "Data"    => "01020304",
                                )
                            ),
                        ),
                    ),
                ),
                5,
                false,
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array $config  The configuration array
    * @param array $packet  The packet to give (array, string or object)
    * @param array $network The reply packet (array, string or object)
    * @param int   $token   The number of seconds before the test times out
    * @param array $expect  The expected return from the reply command
    *
    * @return null
    *
    * @dataProvider dataReceive()
    */
    public function testReceive(
        $config, $packet, $network, $token, $expect
    ) {
        $net = DummyNetwork::factory("Network");
        $net->resetMock($network);
        $transport = Transport::factory($net, $config);
        foreach ($packet as $pkt) {
            $transport->send($pkt);
        }
        $ret = &$transport->receive($token);
        $this->assertSame(
            $expect, $ret
        );
    }
}
?>
