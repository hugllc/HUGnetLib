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
require_once CODE_BASE.'network/Application.php';
/** This is a required class */
require_once CODE_BASE.'network/packets/Packet.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTransport.php';
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
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
    public static function dataApplication()
    {
        return array(
            array(  // #0 // No sockets
                array(
                    "Transport" => array(
                        "receive" => array(
                            packets\Packet::factory(
                                array(
                                    "From"    => "000001",
                                    "To"      => "000002",
                                    "Command" => "23",
                                    "Data"    => "01",
                                )
                            ),
                            null,
                            null,
                            null,
                            null,
                            false,
                            null,
                            null,
                            null,
                            null,
                        ),
                        "unsolicited" => array(
                            packets\Packet::factory(
                                array(
                                    "From"    => "000003",
                                    "To"      => "000000",
                                    "Command" => "POWERUP",
                                    "Data"    => "0102",
                                )
                            ),
                            null,
                        ),
                        "send" => array(
                            "thisIsAToken",
                            "anotherToken",
                        ),
                    ),
                ),
                array(
                    "from" => "000002",
                ),
                10,
                array(
                    "mon",
                ),
                array(
                    "unsol",
                ),
                array(
                    "mat",
                ),
                array(
                    0 => array(
                        "name" => "bad",
                        "packet" => array(
                            "From"    => "000002",
                            "To"      => "000001",
                            "Command" => "23",
                            "Data"    => "010203",
                        ),
                        "config" => array(),
                    ),
                    // This should not be callable
                    3 => array(
                        "name" => "oneShot",
                        "packet" => packets\Packet::factory(
                            array(
                                "From"    => "000002",
                                "To"      => "000004",
                                "Command" => "23",
                                "Data"    => "01020304",
                            )
                        ),
                        "config" => array("find" => false, "retries" => 1),
                    ),
                ),
                array(
                    "Transport" => array(
                        "send" => array(
                            array(
                                packets\Packet::factory(
                                    array(
                                        "From"    => "000002",
                                        "To"      => "000001",
                                        "Command" => "23",
                                        "Data"    => "010203",
                                        "Reply"   => "01",
                                    )
                                ),
                                array(),
                            ),
                            array(
                                packets\Packet::factory(
                                    array(
                                        "From"    => "000002",
                                        "To"      => "000004",
                                        "Command" => "23",
                                        "Data"    => "01020304",
                                    )
                                ),
                                array(
                                    "find" => false,
                                    "retries" => 1,
                                ),
                            ),
                        ),
                        "mon" => array(
                            array(
                                packets\Packet::factory(
                                    array(
                                        "From"    => "000002",
                                        "To"      => "000001",
                                        "Command" => "23",
                                        "Data"    => "010203",
                                        "Reply"   => "01",
                                    )
                                ),
                            ),
                            array(
                                packets\Packet::factory(
                                    array(
                                        "From"    => "000003",
                                        "To"      => "000000",
                                        "Command" => "POWERUP",
                                        "Data"    => "0102",
                                    )
                                ),
                            ),
                            array(
                                packets\Packet::factory(
                                    array(
                                        "From"    => "000001",
                                        "To"      => "000002",
                                        "Command" => "23",
                                        "Data"    => "01",
                                    )
                                ),
                            ),
                            array(
                                packets\Packet::factory(
                                    array(
                                        "From"    => "000002",
                                        "To"      => "000004",
                                        "Command" => "23",
                                        "Data"    => "01020304",
                                    )
                                ),
                            ),
                        ),
                        "receive" => array(
                            array(
                                "thisIsAToken",
                            ),
                            array(
                                "anotherToken",
                            ),
                            array(
                                "anotherToken",
                            ),
                            array(
                                "anotherToken",
                            ),
                            array(
                                "anotherToken",
                            ),
                            array(
                                "anotherToken",
                            ),
                        ),
                        "bad" => array(
                            array(
                                packets\Packet::factory(
                                    array(
                                        "From"    => "000002",
                                        "To"      => "000001",
                                        "Command" => "23",
                                        "Data"    => "010203",
                                        "Reply"   => "01",
                                    )
                                ),
                            ),
                        ),
                        "unsolicited" => array(
                            array(),
                            array(),
                            array(),
                            array(),
                            array(),
                            array(),
                            array(),
                            array(),
                            array(),
                            array(),
                            array(),
                        ),
                        "unsol" => array(
                            array(
                                packets\Packet::factory(
                                    array(
                                        "From"    => "000003",
                                        "To"      => "000000",
                                        "Command" => "POWERUP",
                                        "Data"    => "0102",
                                    )
                                ),
                            ),
                        ),
                        "oneShot" => array(
                            array(
                                packets\Packet::factory(
                                    array(
                                        "From"    => "000002",
                                        "To"      => "000004",
                                        "Command" => "23",
                                        "Data"    => "01020304",
                                    )
                                ),
                            )
                        ),
                    ),
                ),
                null,
            ),
            array( // #1 Normal operation
                array(
                    "Transport" => array(
                        "receive" => array(
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
                        "packet1" => true,
                        "packet2" => false,
                        "send" => array(
                            "one",
                            "two",
                            "three",
                            "four",
                        ),
                    ),
                ),
                array(
                    "from" => "000012",
                    "timeout" => 0.5,
                ),
                10,
                array(),
                array(),
                array(),
                array(
                    array(
                        "name" => "packet1",
                        "packet" => array(
                            packets\Packet::factory(
                                array(
                                    "To" => "000001",
                                    "From" => "000012",
                                    "Command" => "5C",
                                )
                            ),
                            packets\Packet::factory(
                                array(
                                    "To" => "000001",
                                    "From" => "000012",
                                    "Command" => "4C",
                                )
                            ),
                        ),
                    ),
                    array(
                        "name" => "packet2",
                        "packet" => array(
                            array(
                                "To" => "000002",
                                "From" => "000012",
                                "Command" => "5C",
                            ),
                            array(
                                "To" => "000002",
                                "From" => "000012",
                                "Command" => "4C",
                            ),
                        ),
                    ),
                    array(
                        "name" => "packet3",
                        "packet" => array(
                        ),
                    ),
                ),
                array(
                    "Transport" => array(
                        "send" => array(
                            array(
                                packets\Packet::factory(
                                    array(
                                        "To"      => "000001",
                                        "From"    => "000012",
                                        "Command" => "5C",
                                    )
                                ),
                                array(),
                            ),
                            array(
                                packets\Packet::factory(
                                    array(
                                        "To"      => "000002",
                                        "From"    => "000012",
                                        "Command" => "5C",
                                    )
                                ),
                                array(),
                            ),
                            array(
                                packets\Packet::factory(
                                    array(
                                        "To"      => "000001",
                                        "From"    => "000012",
                                        "Command" => "4C",
                                    )
                                ),
                                array(),
                            ),
                        ),
                        "unsolicited" => array(
                            array(),
                            array(),
                            array(),
                            array(),
                            array(),
                            array(),
                            array(),
                            array(),
                            array(),
                            array(),
                        ),
                        "receive" => array(
                            array("one"),
                            array("one"),
                            array("two"),
                            array("one"),
                            array("two"),
                            array("one"),
                            array("two"),
                            array("one"),
                            array("three"),
                        ),
                        "packet1" => array(
                            array(
                                packets\Packet::factory(
                                    array(
                                        "To"      => "000001",
                                        "From"    => "000012",
                                        "Command" => "5C",
                                    )
                                ),
                            ),
                            array(
                                packets\Packet::factory(
                                    array(
                                        "To"      => "000001",
                                        "From"    => "000012",
                                        "Command" => "4C",
                                    )
                                ),
                            ),
                        ),
                        "packet2" => array(
                            array(
                                packets\Packet::factory(
                                    array(
                                        "To"      => "000002",
                                        "From"    => "000012",
                                        "Command" => "5C",
                                    )
                                ),
                            ),
                        ),

                    ),
                ),
                null,
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array  $mock        The string to give to the class
    * @param array  $config      The configuration to send the class
    * @param int    $loops       The number of times to call read and write
    * @param array  $monitor     Array of strings to monitor
    * @param array  $unsolicited Array of strings to get unsolicited packets
    * @param array  $match       The match callbacks to put in
    * @param array  $send        Array of "function" => Packets to send out
    * @param array  $expect      The info to expect returned
    * @param string $exception   The exception to expect.  Null for none
    *
    * @return null
    *
    * @dataProvider dataApplication()
    */
    public function testApplication(
        $mock, $config, $loops, $monitor, $unsolicited, $match, $send,
        $expect, $exception
    ) {
        if (!is_null($exception)) {
            $this->setExpectedException($exception);
        }
        $transport = new \HUGnet\network\DummyTransport();
        $system = new \HUGnet\DummySystem();
        $transport->resetMock($mock);
        $application = &Application::factory($transport, $system, $config);
        foreach ((array)$monitor as $mon) {
            $application->monitor(array($transport, $mon));
        }
        foreach ((array)$unsolicited as $unsol) {
            $application->unsolicited(array($transport, $unsol));
        }
        foreach ((array)$match as $mat) {
            $application->match(array($transport, $mat));
        }
        for ($i = 0; $i < $loops; $i++) {
            if (isset($send[$i]) && !is_null($send[$i])) {
                $application->send(
                    $send[$i]["packet"],
                    array($transport, $send[$i]["name"]),
                    $send[$i]["config"]
                );
            }
            $application->main();
        }
        $this->assertEquals($expect, $transport->retrieve(), "Calls wrong");
    }

    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataBlocking()
    {
        return array(
            array(  // #0 // No sockets
                array(
                    "Transport" => array(
                        "receive" => array(
                            null,
                            "thisIsAToken" => packets\Packet::factory(
                                array(
                                    "From"    => "000100",
                                    "To"      => "000200",
                                    "Command" => "23",
                                    "Data"    => "01",
                                )
                            ),
                        ),
                        "send" => array(
                            "thisIsAToken",
                        ),
                    ),
                ),
                array(
                    "timeout" =>  1,
                    "from" => "000200",
                ),
                null,
                packets\Packet::factory(
                    array(
                        "From"    => "000200",
                        "To"      => "000100",
                        "Command" => "23",
                        "Data"    => "010203",
                    )
                ),
                packets\Packet::factory(
                    array(
                        "From"    => "000200",
                        "To"      => "000100",
                        "Command" => "23",
                        "Data"    => "010203",
                        "Reply"   => "01",
                    )
                ),
                array(
                    "Transport" => array(
                        "send" => array(
                            array(
                                packets\Packet::factory(
                                    array(
                                        "From"    => "000200",
                                        "To"      => "000100",
                                        "Command" => "23",
                                        "Data"    => "010203",
                                        "Reply"   => "01",
                                    )
                                ),
                                array(
                                    'block' => true,
                                ),
                            ),
                        ),
                        "receive" => array(
                            array(
                                "thisIsAToken",
                            ),
                            array(
                                "thisIsAToken",
                            ),
                        ),
                        "unsolicited" => array(array()),
                    ),
                ),
                null,
            ),
            array(  // #0 // No sockets
                array(
                    "Transport" => array(
                        "receive" => array(
                            null,
                            "thisIsAToken" => packets\Packet::factory(
                                array(
                                    "From"    => "000100",
                                    "To"      => "000200",
                                    "Command" => "23",
                                    "Data"    => "01",
                                )
                            ),
                        ),
                        "send" => array(
                            "thisIsAToken",
                        ),
                    ),
                ),
                array(
                    "block" => true,
                    "timeout" =>  1,
                    "from" => "000200",
                ),
                "get_class", // This will take a single arguemnt of an object
                packets\Packet::factory(
                    array(
                        "From"    => "000200",
                        "To"      => "000100",
                        "Command" => "23",
                        "Data"    => "010203",
                    )
                ),
                packets\Packet::factory(
                    array(
                        "From"    => "000200",
                        "To"      => "000100",
                        "Command" => "23",
                        "Data"    => "010203",
                        "Reply"   => "01",
                    )
                ),
                array(
                    "Transport" => array(
                        "send" => array(
                            array(
                                packets\Packet::factory(
                                    array(
                                        "From"    => "000200",
                                        "To"      => "000100",
                                        "Command" => "23",
                                        "Data"    => "010203",
                                        "Reply"   => "01",
                                    )
                                ),
                                array(
                                ),
                            ),
                        ),
                        "receive" => array(
                            array(
                                "thisIsAToken",
                            ),
                            array(
                                "thisIsAToken",
                            ),
                        ),
                        "unsolicited" => array(array()),
                    ),
                ),
                null,
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array  $mock      The string to give to the class
    * @param array  $config    The configuration to send the class
    * @param string $callback  The function to call
    * @param array  $send      Array of "function" => Packets to send out
    * @param array  $expect    The info to expect returned
    * @param mixed  $calls     The expected mock calls
    * @param string $exception The exception to expect.  Null for none
    *
    * @return null
    *
    * @dataProvider dataBlocking()
    */
    public function testBlocking(
        $mock, $config, $callback, $send, $expect, $calls, $exception
    ) {
        if (!is_null($exception)) {
            $this->setExpectedException($exception);
        }
        $transport = new \HUGnet\network\DummyTransport();
        $system = new \HUGnet\DummySystem();
        $transport->resetMock($mock);
        $application = &Application::factory($transport, $system, $config);
        $ret = $application->send($send, $callback);
        $this->assertEquals($expect, $ret, "The return was wrong");
        $this->assertEquals($calls, $transport->retrieve(), "Calls wrong");
    }
    /**
    * Tests to make sure that the from address is okay
    *
    * @return null
    */
    public function testFrom()
    {
        $system = new \HUGnet\DummySystem();
        $transport = new \HUGnet\network\DummyTransport();
        $transport->resetMock($mock);
        $application = &Application::factory(
            $transport, $system,
            array("block" => 1)
        );
        $ret = $application->send(
            packets\Packet::factory(
                array(
                    "to" => "123456",
                    "command" => "55",
                )
            ),
            null,
            array(
                "timeout" => 0.1,
            )
        );
        $this->assertTrue(hexdec($ret->from()) >= 0xFD0000, "Too low!");
        $this->assertTrue(hexdec($ret->from()) <= 0xFDFFFF, "Too high!");
    }
    /**
    * Tests to make sure that the from address is okay
    *
    * @return null
    */
    public function testDeviceFrom()
    {
        $system = new \HUGnet\DummySystem();
        $transport = new \HUGnet\network\DummyTransport();
        $transport->resetMock($mock);
        $application = &Application::factory(
            $transport, $system,
            array("block" => 1)
        );
        $application->device(array("id" => "000001"));
        $ret = $application->send(
            packets\Packet::factory(
                array(
                    "to" => "123456",
                    "command" => "55",
                )
            ),
            array(
                "timeout" => 0.1,
            )
        );
        $this->assertSame("000001", $ret->from());
    }
    /**
    * Tests to make sure that the from address is okay
    *
    * @return null
    */
    public function testDevice()
    {
        $system = new \HUGnet\DummySystem();
        $transport = new \HUGnet\network\DummyTransport();
        $transport->resetMock($mock);
        $application = &Application::factory(
            $transport, $system,
            array("block" => 1)
        );
        $ret = &$application->device();
        $ret2 = &$application->device();
        $this->assertTrue(is_a($ret, "\\HUGnet\\network\\Device"), "Wrong class");
        $this->assertSame($ret, $ret2, "Wrong device returned");
    }

}
?>
