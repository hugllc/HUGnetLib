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
require_once CODE_BASE.'network/Matcher.php';
/** This is a required class */
require_once CODE_BASE.'network/TransportPacket.php';
/** This is a required class */
require_once CODE_BASE.'network/Packet.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyBase.php';

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
class MatcherTest extends \PHPUnit_Framework_TestCase
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
    public static function dataMatcher()
    {
        return array(
            array(  // #0 Everything correct.  Some packets got no reply
                array(
                    "timeout" => 0.5,
                ),
                "myFunc",
                array(
                    Packet::factory(
                        array(
                            "To"      => "000002",
                            "From"    => "000001",
                            "Command" => "55",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000003",
                            "From"    => "000001",
                            "Command" => "55",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000004",
                            "From"    => "000001",
                            "Command" => "55",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000001",
                            "From"    => "000003",
                            "Command" => "01",
                            "Data"    => "01020304",
                        )
                    ),
                    // before packet request packet
                    Packet::factory(
                        array(
                            "To"      => "000001",
                            "From"    => "000005",
                            "Command" => "01",
                            "Data"    => "01020304",
                        )
                    ),
                    // after reply packet
                    Packet::factory(
                        array(
                            "To"      => "000005",
                            "From"    => "000001",
                            "Command" => "55",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000006",
                            "From"    => "000001",
                            "Command" => "55",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000007",
                            "From"    => "000001",
                            "Command" => "55",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000001",
                            "From"    => "000002",
                            "Command" => "01",
                            "Data"    => "01020304",
                        )
                    ),
                ),
                .8,
                array(
                    Packet::factory(
                        array(
                            "To"      => "000001",
                            "From"    => "000006",
                            "Command" => "01",
                            "Data"    => "01020304",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000001",
                            "From"    => "000007",
                            "Command" => "01",
                            "Data"    => "01020304",
                        )
                    ),
                    Packet::factory(
                        array(
                            "To"      => "000008",
                            "From"    => "000001",
                            "Command" => "55",
                        )
                    ),
                ),
                array(
                    "Class" => array(
                        "myFunc" => array(
                            array(
                                Packet::factory(
                                    array(
                                        "To"      => "000003",
                                        "From"    => "000001",
                                        "Command" => "55",
                                        "Reply"    => "01020304",
                                    )
                                ),
                            ),
                            array(
                                Packet::factory(
                                    array(
                                        "To"      => "000002",
                                        "From"    => "000001",
                                        "Command" => "55",
                                        "Reply"    => "01020304",
                                    )
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array  $config The configuration array
    * @param string $fct    The function to call
    * @param array  $before Array of packets for before pause
    * @param float  $pause  The pause in seconds
    * @param array  $after  Array of packets for after pause
    * @param array  $expect The expected return from the reply command
    *
    * @return null
    *
    * @dataProvider dataMatcher()
    */
    public function testMatcher(
        $config, $fct, $before, $pause, $after, $expect
    ) {
        $net = new \HUGnet\DummyBase("Class");
        $net->resetMock($network);
        $matcher = Matcher::factory($config, array($net, $fct));
        foreach ((array)$before as $pkt) {
            $matcher->match($pkt);
        }
        for ($i = 0; $i < 10; $i++) {
            $matcher->main();
            usleep($pause * 100000);
        }
        foreach ((array)$after as $pkt) {
            $matcher->match($pkt);
        }
        $this->assertEquals(
            $expect, $net->retrieve(), "The return is wrong"
        );
    }
}
?>
