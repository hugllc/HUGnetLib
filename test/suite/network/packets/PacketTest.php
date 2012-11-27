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
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\network;
/** This is a required class */
require_once CODE_BASE.'network/packets/Packet.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PacketTest extends \PHPUnit_Framework_TestCase
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
    public static function dataFactory()
    {
        return array(
            array(  // #0 given a string
                "5A5A5A55000ABC0000200401020304C3",
                array(
                    "To" => "000ABC",
                    "From" => "000020",
                    "Command" => "55",
                    "Length"  => "04",
                    "Data" => "01020304",
                    "Checksum" => "C3",
                    "Type" => "SENSORREAD",
                    "Extra" => "",
                ),
            ),
            array( // #1 Given an array with strings
                array(
                    "To" => "000ABC",
                    "From" => "000020",
                    "Command" => "SENSORREAD",
                    "Length"  => "04",
                    "Data" => "01020304",
                    "Checksum" => "C3",
                ),
                array(
                    "To" => "000ABC",
                    "From" => "000020",
                    "Command" => "55",
                    "Length"  => "04",
                    "Data" => "01020304",
                    "Checksum" => "C3",
                    "Type" => "SENSORREAD",
                    "Extra" => "",
                ),
            ),
            array( // #2 Given an array with integers and arrays
                array(
                    "To" => 0xABC,
                    "From" => 0x20,
                    "Command" => 0x00,
                    "Length"  => 0x04,
                    "Data" => array(1,2,3,4),
                    "Checksum" => 0xC3,
                ),
                array(
                    "To" => "000ABC",
                    "From" => "000020",
                    "Command" => "00",
                    "Length"  => "04",
                    "Data" => "01020304",
                    "Checksum" => "96",
                    "Type" => "UNKNOWN",
                    "Extra" => "",
                ),
            ),
            array(  // #3 given a string with garbage before and after
                "5A12825938475A5A55000ABC0000200401020304C34819C9A2",
                array(
                    "To" => "000ABC",
                    "From" => "000020",
                    "Command" => "55",
                    "Length"  => "04",
                    "Data" => "01020304",
                    "Checksum" => "C3",
                    "Type" => "SENSORREAD",
                    "Extra" => "4819C9A2",
                ),
            ),
            array(  // #4 given a string with no preamble.
                "55000ABC0000200401020304C3",
                array(
                    "Extra" => "55000ABC0000200401020304C3",
                ),
            ),
            array(  // #5 Two packets given.
                "5A5A5A55000ABC0000200401020304C3"
                ."5A5A5A01000ABC000020040102030497",
                array(
                    "To" => "000ABC",
                    "From" => "000020",
                    "Command" => "55",
                    "Length"  => "04",
                    "Data" => "01020304",
                    "Checksum" => "C3",
                    "Type" => "SENSORREAD",
                    "Extra" => "5A5A5A01000ABC000020040102030497",
                ),
            ),
            array( // #6 string with no data
                "5A5A5A5C000181FDE01400D5",
                array(
                    "To" => "000181",
                    "From" => "FDE014",
                    "Command" => "5C",
                    "Data" => "",
                    "Length"  => "00",
                    "Checksum" => "D5",
                    "Type" => "CONFIG",
                ),
            ),
            array( // #7 Data given as empty string
                array(
                    "To" => "ABC",
                    "From" => "20",
                    "Command" => "01",
                    "Data" => "",
                    "Iface" => "asdf",
                ),
                array(
                    "To" => "000ABC",
                    "From" => "000020",
                    "Command" => "01",
                    "Length"  => "00",
                    "Data" => "",
                    "Checksum" => "97",
                    "Type" => "REPLY",
                    "Extra" => "",
                    "Iface" => "asdf",
                ),
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param string $string The string to give to the class
    * @param array  $expect The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataFactory()
    */
    public function testFactory($string, $expect)
    {
        $pkt = packets\Packet::factory($string);
        foreach ((array)$expect as $key => $value) {
            $this->assertTrue(
                method_exists($pkt, $key), $key."() doesn't exist"
            );
            $this->assertSame($value, $pkt->$key(), "Key '".$key."' wrong");
        }
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataString()
    {
        return array(
            array( // #0 All strings
                array(
                    "To" => "ABC",
                    "From" => "20",
                    "Command" => "SENSORREAD",
                    "Data" => "01020304",
                ),
                "5A5A5A55000ABC0000200401020304C3",
            ),
            array( // #1 All ints and arrays
                array(
                    "To" => 0xABC,
                    "From" => 0x20,
                    "Command" => 0x55,
                    "Data" => array(1,2,3,4),
                ),
                "5A5A5A55000ABC0000200401020304C3",
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param string $array  The string to give to the class
    * @param array  $expect The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataString()
    */
    public function testString($array, $expect)
    {
        $pkt = packets\Packet::factory($array);
        $this->assertSame($expect, (string)$pkt);
    }

    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataIsValid()
    {
        return array(
            array(
                array(
                    "To" => "000ABC",
                    "From" => "000020",
                    "Command" => "SENSORREAD",
                    "Length"  => 4,
                    "Data" => "01020304",
                    "Checksum" => "C3",
                ),
                true,
            ),
            array(
                "5A5A5A55000ABC0000200401020304C3",
                true,
            ),
            array(
                "5A5A5A55000ABC0000200401020304F4",
                false,
            ),
            array(
                "5A5A5A010000201234560F0102030405060708090A0B0C0D0E0F5E",
                true,
            ),
            array(
                "",
                null,
            ),
            array(
                "5A5A5A5C000181FDE01400D5",
                true,
            ),
            array(
                "5A5A5A5A01FDE01400018116000000018100392102410039201443000102"
                ."FFFFFF50A7",
                true,
            ),
            array(
                "5A5A5A56000181FDE0140100DE",
                true,
            ),
            array(
                "5A5A5A5A01FDE0140001815A000000000000000000000000000000000000"
                ."00000000000000000000000000000000000000000000000000000000000"
                ."00000000000000000000000000000000000000000000000000000000000"
                ."00000000000000000000000000D2",
                true,
            ),
            array(
                "5A5A5A56000181FDE0140101DF",
                true,
            ),
            array(
                "5A5A5A5C000181FDE01400",
                null,
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param string $preload The string to give to the class
    * @param array  $expect  The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataIsValid()
    */
    public function testIsValid($preload, $expect)
    {
        $pkt = packets\Packet::factory($preload);
        $this->assertSame($expect, $pkt->isValid());
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataData()
    {
        return array(
            array(
                array(
                    "To" => "000ABC",
                    "From" => "000020",
                    "Command" => "55",
                    "Length"  => 4,
                    "Data" => "01020304",
                    "Reply" => "04030201",
                    "Checksum" => "C3",
                ),
                true,
                array(1,2,3,4),
                array(4,3,2,1),
            ),
            array(
                array(
                    "To" => "000ABC",
                    "From" => "000020",
                    "Command" => "55",
                    "Length"  => 4,
                    "Data" => "01020304",
                    "Reply" => "04030201",
                    "Checksum" => "C3",
                ),
                false,
                "01020304",
                "04030201",
            ),
            array(
                array(
                    "To" => "000ABC",
                    "From" => "000020",
                    "Command" => "55",
                    "Length"  => 4,
                    "Data" => "01020304",
                    "Reply" => "",
                    "Checksum" => "C3",
                ),
                false,
                "01020304",
                "",
            ),
            array(
                "5A5A5A55000ABC0000200401020304F4",
                false,
                "01020304",
                null
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param string $preload The string to give to the class
    * @param mixed  $raw     Whether to use the raw value or not
    * @param array  $expect  The info to expect returned
    * @param array  $reply   The expected stuff returned
    *
    * @return null
    *
    * @dataProvider dataData()
    */
    public function testData($preload, $raw, $expect, $reply)
    {
        $pkt = packets\Packet::factory($preload);
        $this->assertSame($expect, $pkt->Data(null, $raw));
        $this->assertSame($reply, $pkt->Reply(null, $raw));
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataExtra()
    {
        return array(
            array( // #0 Packet present + extra stuff
                "12345A5A5A56000181FDE0140101DF0A0B0C0D",
                "0A0B0C0D",
            ),
            array( // #1 Preamble present, but not full packet
                "12345A5A0957112233440102",
                "",
            ),
            array( // #2 No preamble present
                "12340757112233440102",
                "12340757112233440102",
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param string $preload The string to give to the class
    * @param array  $expect  The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataExtra()
    */
    public function testExtra($preload, $expect)
    {
        $pkt = packets\Packet::factory($preload);
        $this->assertSame($expect, $pkt->extra());
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataConfig()
    {
        return array(
            array(
                array(),
                array(),
                array(),
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param mixed $preload The string to give to the class
    * @param array $config  The configuration array given
    * @param array $expect  The expected returned
    *
    * @return null
    *
    * @dataProvider dataConfig()
    */
    public function testConfig($preload, $config, $expect)
    {
        $pkt = packets\Packet::factory($preload);
        $this->assertEquals($expect, $pkt->config($config));
    }
    /**
    * Tests the iteration and preload functions
    *
    * @return null
    */
    public function testFactoryWithObject()
    {
        $pkt = &packets\Packet::factory(
            array(
                "To" => "000012",
                "From" => "123456",
                "Command" => "41",
                "Data" => array(1,2,3,4,5),
            )
        );
        $pkt2 = &packets\Packet::factory($pkt);
        $this->assertSame($pkt, $pkt2);
    }
}
?>
