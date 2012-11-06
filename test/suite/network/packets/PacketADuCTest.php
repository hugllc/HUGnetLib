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
namespace HUGnet\network\packets;
/** This is a required class */
require_once CODE_BASE.'network/packets/PacketADuC.php';

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
class PacketADuCTest extends \PHPUnit_Framework_TestCase
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
            array( // #0 All strings
                "070E09571122334401020304EC",
                array(
                    "Address" => "11223344",
                    "Command" => "57",
                    "Data" => "01020304",
                    "Length" => "09",
                    "Type" => "WRITE",
                ),
            ),
            array( // #1 all ints
                array(
                    "Address" => 0x11223344,
                    "Command" => 0x51,
                    "Data" => array(0x01,0x02,0x03,0x04),
                ),
                array(
                    "Address" => "11223344",
                    "Command" => "51",
                    "Data" => "01020304",
                    "Length" => "09",
                    "Type" => "UNKNOWN",
                ),
            ),
            array( // #2 Erase Page
                array(
                    "Address" => 0x11223344,
                    "Command" => 0x45,
                    "Data" => array(0x01,0x02,0x03,0x04),
                ),
                array(
                    "Address" => "11223344",
                    "Command" => "45",
                    "Data" => "01020304",
                    "Length" => "09",
                    "Type" => "ERASEPAGE",
                ),
            ),
            array( // #3 Verify
                array(
                    "Address" => 0x11223344,
                    "Command" => 0x56,
                    "Data" => array(0x01,0x02,0x03,0x04),
                ),
                array(
                    "Address" => "11223344",
                    "Command" => "56",
                    "Data" => "01020304",
                    "Length" => "09",
                    "Type" => "VERIFY",
                ),
            ),
            array( // #4 Protect
                array(
                    "Address" => 0x11223344,
                    "Command" => 0x50,
                    "Data" => array(0x01,0x02,0x03,0x04),
                ),
                array(
                    "Address" => "11223344",
                    "Command" => "50",
                    "Data" => "01020304",
                    "Length" => "09",
                    "Type" => "PROTECT",
                ),
            ),
            array( // #5 Run
                array(
                    "Address" => 0x11223344,
                    "Command" => 0x52,
                    "Data" => array(0x01,0x02,0x03,0x04),
                ),
                array(
                    "Address" => "11223344",
                    "Command" => "52",
                    "Data" => "01020304",
                    "Length" => "09",
                    "Type" => "RUN",
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
        $pkt = PacketADuC::factory($string);
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
                    "Address" => "11223344",
                    "Command" => "WRITE",
                    "Data" => "01020304",
                ),
                "070E09571122334401020304EC",
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
        $pkt = PacketADuC::factory($array);
        $this->assertSame($expect, (string)$pkt);
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataChecksum()
    {
        return array(
            array( // #0 All strings
                "070E09571122334401020304EC",
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param string $array The string to give to the class
    *
    * @return null
    *
    * @dataProvider dataChecksum()
    */
    public function testChecksum($array)
    {
        $pkt = PacketADuC::factory($array);
        $total = 0;
        // Strip off the preamble (not part of the checksum)
        $string = substr((string)$pkt, 4);
        foreach (str_split($string, 2) as $hex) {
            $total += hexdec($hex);
            $total &= 0xFF;
        }
        $this->assertSame(0, $total, "The checksum is wrong");
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
                    "Address" => "11223344",
                    "Command" => "57",
                    "Data" => "01020304",
                    "Checksum" => "EC",
                ),
                true,
            ),
            array(
                "070E09571122334401020304",
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
        $pkt = PacketADuC::factory($preload);
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
                    "Address" => "00000ABC",
                    "Command" => "57",
                    "Length"  => 9,
                    "Data" => "01020304",
                    "Reply" => "06",
                ),
                true,
                array(1,2,3,4),
                true,
            ),
            array(
                array(
                    "Address" => "00000ABC",
                    "Command" => "57",
                    "Length"  => 9,
                    "Data" => "01020304",
                ),
                true,
                array(1,2,3,4),
                null,
            ),
            array(
                array(
                    "Address" => "00000ABC",
                    "Command" => "57",
                    "Length"  => 9,
                    "Data" => "01020304",
                    "Reply" => 0x07,
                ),
                true,
                array(1,2,3,4),
                false,
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
        $pkt = PacketADuC::factory($preload);
        $this->assertSame($expect, $pkt->Data(null, $raw));
        $this->assertSame($reply, $pkt->Reply(null, $raw));
    }
    /**
    * Tests the iteration and preload functions
    *
    * @return null
    */
    public function testFactoryWithObject()
    {
        $pkt = &PacketADuC::factory(
            array(
                "Address" => "00001211",
                "Command" => "WRITE",
                "Data" => array(1,2,3,4,5),
            )
        );
        $pkt2 = &PacketADuC::factory($pkt);
        $this->assertSame($pkt, $pkt2);
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
                "1234070E09571122334401020304EC0A0B0C0D",
                "0A0B0C0D",
            ),
            array( // #1 Preamble present, but not full packet
                "1234070E0957112233440102",
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
        $pkt = PacketADuC::factory($preload);
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
                array(
                    "baud" => 38400, "block" => true, "tries" => 1, "find" => false
                ),
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
        $pkt = PacketADuC::factory($preload);
        $this->assertEquals($expect, $pkt->config($config));
    }
}
?>
