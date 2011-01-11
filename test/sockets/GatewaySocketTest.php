<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../sockets/GatewaySocket.php';
require_once dirname(__FILE__).'/../../containers/PacketContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class GatewaySocketTest extends PHPUnit_Framework_TestCase
{

    /**
    * Sets up the fixture, for example, open a network connection.
    * This method is called before a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function setUp()
    {
        $this->o = new GatewaySocket(array());
    }

    /**
    * Tears down the fixture, for example, close a network connection.
    * This method is called after a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function tearDown()
    {
        $this->o = null;
    }


    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataConstructor()
    {
        return array(
            array(
                array(
                ),
                array(
                    "GatewayKey" => 0,
                    "GatewayIP" => "127.0.0.1",
                    "GatewayPort" => "2000",
                    "GatewayName" => "Localhost",
                    "GatewayLocation" => "",
                    "database" => "",
                    "FirmwareStatus" => "RELEASE",
                    "isVisible" => 0,
                    "Timeout" => 2,
                    "group" => "default",
                ),
            ),
            array(
                array(
                    "GatewayKey" => 1,
                    "GatewayIP" => "10.2.0.5",
                    "GatewayPort" => "43",
                    "GatewayName" => "Put Name Here",
                    "GatewayLocation" => "Somewhere",
                    "database" => "HUGnet",
                    "FirmwareStatus" => "BETA",
                    "isVisible" => 1,
                    "Timeout" => 3,
                    "group" => "myGroup",
                    "DeviceID" => "000021",
                ),
                array(
                    "GatewayKey" => 1,
                    "GatewayIP" => "10.2.0.5",
                    "GatewayPort" => "43",
                    "GatewayName" => "Put Name Here",
                    "GatewayLocation" => "Somewhere",
                    "database" => "HUGnet",
                    "FirmwareStatus" => "BETA",
                    "isVisible" => 1,
                    "Timeout" => 3,
                    "group" => "myGroup",
                    "DeviceID" => "000021",
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The value to preload
    * @param array $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $expect)
    {
        $o = new GatewaySocket($preload);
        foreach ($expect as $key => $value) {
            $this->assertSame($value, $o->$key, "$key is wrong");
        }

    }
    /**
    * data provider for testConnect
    *
    * @return array
    */
    public static function dataConnect()
    {
        return array(
            array(
                array(
                    "GatewayKey" => 1,
                    "GatewayIP" => "10.2.0.5",
                    "GatewayPort" => "43",
                    "GatewayName" => "Put Name Here",
                    "GatewayLocation" => "Somewhere",
                    "database" => "HUGnet",
                    "FirmwareStatus" => "BETA",
                    "isVisible" => 1,
                    "Timeout" => 0.5,
                ),
                false,
                false,
                false,
            ),
            array(
                array(
                    "GatewayIP" => "127.0.0.1",
                    "GatewayPort" => "80",
                ),
                true,
                true,
                !(bool)@file_get_contents("http://127.0.0.1", 0, null, -1, 1),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The value to preload
    * @param array $expect  The expected return
    * @param bool  $socket  If true we expect a resource
    * @param bool  $skip    Skip the test (resources not available)
    *
    * @return null
    *
    * @dataProvider dataConnect
    */
    public function testConnect($preload, $expect, $socket, $skip)
    {
        if ($skip) {
            $this->markTestSkipped("HTTP Server not available");
        }
        $this->o->fromArray($preload);
        $ret = $this->o->connect();
        $this->assertSame($expect, $ret);
        $this->assertSame(
            $socket,
            is_resource($this->readAttribute($this->o, "socket"))
        );
    }

    /**
    * data provider for testConnect
    *
    * @return array
    */
    public static function dataDisconnect()
    {
        return array(
            array(
                array(
                    "GatewayKey" => 1,
                    "GatewayIP" => "10.2.0.5",
                    "GatewayPort" => "43",
                    "GatewayName" => "Put Name Here",
                    "GatewayLocation" => "Somewhere",
                    "database" => "HUGnet",
                    "FirmwareStatus" => "BETA",
                    "isVisible" => 1,
                    "Timeout" => 0.5,
                ),
                false,
                null,
                false,
            ),
            array(
                array(
                    "GatewayIP" => "127.0.0.1",
                    "GatewayPort" => "80",
                ),
                true,
                null,
                !(bool)@file_get_contents("http://127.0.0.1", 0, null, -1, 1),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The value to preload
    * @param array $expect  The expected return
    * @param mixed $socket  What to expect in the socket
    * @param bool  $skip    Skip the test (resources not available)
    *
    * @return null
    *
    * @dataProvider dataDisconnect
    */
    public function testDisconnect($preload, $expect, $socket, $skip)
    {
        if ($skip) {
            $this->markTestSkipped("HTTP Server not available");
        }
        $this->o->fromArray($preload);
        $ret = $this->o->connect();
        $this->o->disconnect();
        $this->assertSame($expect, $ret);
        $this->assertAttributeSame($socket, "socket", $this->o);
    }

    /**
    * data provider for testConnect
    *
    * @return array
    */
    public static function dataRead()
    {
        return array(
            array(
                array(
                    "GatewayKey" => 1,
                    "GatewayIP" => "10.2.0.5",
                    "GatewayPort" => "43",
                    "GatewayName" => "Put Name Here",
                    "GatewayLocation" => "Somewhere",
                    "database" => "HUGnet",
                    "FirmwareStatus" => "BETA",
                    "isVisible" => 1,
                    "Timeout" => 0.5,
                ),
                "",
                false,
                50,
            ),
            // This test will fail without a local web server
            array(
                array(
                    "GatewayIP" => "127.0.0.1",
                    "GatewayPort" => "80",
                ),
                HUGnetClass::hexifyStr("GET\r\n"),
                @file_get_contents("http://127.0.0.1", 0, null, -1, 50),
                50,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The value to preload
    * @param string $write   The string to write
    * @param mixed  $expect  The expected return
    * @param int    $chars   The number of characters to read
    *
    * @return null
    *
    * @dataProvider dataRead
    */
    public function testRead($preload, $write, $expect, $chars)
    {
        $this->o->fromArray($preload);
        $this->o->write($write);
        // With the new timeout on the select we have to give the web server
        // some time to respond.
        sleep(1);
        $read = $this->o->read($chars);
        if (is_string($expect)) {
            $read = HUGnetClass::dehexify($read);
            //$this->assertFalse(is_bool(stristr($read, $expect)));
            $this->assertSame($expect, $read);
        } else {
            $this->assertSame($expect, $read);
        }
    }

    /**
    * data provider for testConnect
    *
    * @return array
    */
    public static function dataSendPkt()
    {
        return array(
            array(
                array(
                    "GatewayKey" => 1,
                    "GatewayIP" => "10.2.0.5",
                    "GatewayPort" => "43",
                    "GatewayName" => "Put Name Here",
                    "GatewayLocation" => "Somewhere",
                    "database" => "HUGnet",
                    "FirmwareStatus" => "BETA",
                    "isVisible" => 1,
                    "Timeout" => 0.5,
                ),
                new PacketContainer(),
                false,
                false,
            ),
            // This test will fail without a local web server
            array(
                array(
                    "GatewayIP" => "127.0.0.1",
                    "GatewayPort" => "80",
                ),
                new PacketContainer(array("To" => "123456", "Command" => "03")),
                true,
                !(bool)@file_get_contents("http://127.0.0.1", 0, null, -1, 1),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The value to preload
    * @param string $write   The string to write
    * @param mixed  $expect  The expected return
    * @param bool   $skip    Skip the test (resources not available)
    *
    * @return null
    *
    * @dataProvider dataSendPkt
    */
    public function testSendPkt($preload, $write, $expect, $skip)
    {
        if ($skip) {
            $this->markTestSkipped("HTTP Server not available");
        }
        $this->o->fromArray($preload);
        $ret = $this->o->sendPkt($write);
        $this->assertSame($expect, $ret);
    }

    /**
    * data provider for testConnect
    *
    * @return array
    */
    public static function dataRecvPkt()
    {
        return array(
            array(
                array(
                    "GatewayKey" => 1,
                    "GatewayIP" => "10.2.0.5",
                    "GatewayPort" => "43",
                    "GatewayName" => "Put Name Here",
                    "GatewayLocation" => "Somewhere",
                    "database" => "HUGnet",
                    "FirmwareStatus" => "BETA",
                    "isVisible" => 1,
                    "Timeout" => 0.5,
                ),
                "",
                false,
                new PacketContainer(array("Timeout" => 1)),
                false,
            ),
            // This test will fail without a local web server
            array(
                array(
                    "GatewayIP" => "127.0.0.1",
                    "GatewayPort" => "80",
                ),
                HUGnetClass::hexifyStr("GET\r\n"),
                false,
                new PacketContainer(array("Timeout" => 5)),
                @file_get_contents("http://127.0.0.1", 0, null, -1, 50),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The value to preload
    * @param string $write   The string to write
    * @param mixed  $expect  The expected return
    * @param object $pkt     The packet to use
    * @param string $buffer  What the buffer should look like
    *
    * @return null
    *
    * @dataProvider dataRecvPkt
    */
    public function testRecvPkt($preload, $write, $expect, $pkt, $buffer)
    {
        $this->o->fromArray($preload);
        $this->o->write($write);
        $ret = $this->o->recvPkt($pkt);
        $this->assertSame($expect, $ret);
        // We are going to test the buffer here.  That is about the best we can
        // do without building a custom network server.
        if (strlen($buffer) > 0) {
            $read = $this->readAttribute($this->o, "buffer");
            // Since we can't control the read length, we will make the strings
            // The same size.
            $read = HUGnetClass::dehexify($read);
            $read = substr($read, 0, strlen($buffer));
            $buffer = substr($buffer, 0, strlen($read));
            $this->assertSame($buffer, $read);
        }
    }
    /**
     * Data provider for testEncodeIP
     *
     * @return array
     */
    public static function dataEncodeIP()
    {
        return array(
            array(
                array(
                    "hello" => "182.351.253.211",
                    "asdf" => "234.512.123.151",
                ),
                "hello:182.351.253.211\nasdf:234.512.123.151\n",
            ),
            array(
                "",
                "",
            ),
            array(
                "192.168.0.1",
                "192.168.0.1",
            ),
        );
    }
    /**
     * Tests gateway::find()
     *
     * @param array $IP     The IP address to test
     * @param array $expect The return value to expect
     *
     * @return null
     *
     * @dataProvider dataEncodeIP().
     */
    public function testEncodeIP($IP, $expect)
    {
        $ret = $this->o->encodeIP($IP);
        $this->assertSame($expect, $ret);
    }
    /**
     * Data provider for testDecideIP()
     *
     * @return array
     */
    public static function dataDecodeIP()
    {
        return array(
            array(
                "hello:182.351.253.211\nasdf:234.512.123.151\n\n",
                array(
                    "hello" => "182.351.253.211",
                    "asdf" => "234.512.123.151",
                ),
            ),
            array(
                array(),
                array(),
            ),
            array(
                "192.168.0.1",
                "192.168.0.1",
            ),
        );
    }
    /**
     * Tests gateway::find()
     *
     * @param array $IP     The IP address to test
     * @param array $expect The return value to expect
     *
     * @return null
     *
     * @dataProvider dataDecodeIP().
     */
    public function testDecodeIP($IP, $expect)
    {
        $ret = $this->o->decodeIP($IP);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataSet()
    {
        return array(
            array("DeviceID", 5, "000005"),
            array("DeviceID", "000005", "000005"),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $var    The variable to set
    * @param mixed  $value  The value to set
    * @param mixed  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataSet
    */
    public function testSet($var, $value, $expect)
    {
        $this->o->$var = $value;
        $data = $this->readAttribute($this->o, "data");
        $this->assertSame($expect, $data[$var]);
    }

}

?>
