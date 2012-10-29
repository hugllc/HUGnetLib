<?php
/**
 * Tests the filter class
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
 * @subpackage SuiteSockets
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


/** This is a required class */
require_once CODE_BASE.'sockets/GatewaySocket.php';
/** This is a required class */
require_once CODE_BASE.'containers/PacketContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteSockets
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class GatewaySocketTest extends PHPUnit_Framework_TestCase
{
    /** @var This is the pid file for the socket server */
    protected $pidfile = "";
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
        $this->tearDownSocket();
    }
    /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    *
    * @param array &$preload the preload data to fix
    *
    * @access protected
    *
    * @return null
    */
    protected function setUpSocket(&$preload)
    {
        if (isset($preload["GatewayPort"]) && empty($preload["GatewayPort"])) {
            $sock = socket_create(AF_INET, SOCK_STREAM, 0);
            $port = 0;
            socket_bind($sock, "127.0.0.1", 0);
            socket_getsockname($sock, $address, $port);
            socket_close($sock);
            $this->pidfile = tempnam(sys_get_temp_dir(), 'SocketServer');
            exec(
                "php ".TEST_CONFIG_BASE."scripts/socketserver.php "
                .(int)$port." ".$this->pidfile." > /dev/null 2>&1 &"
            );
            sleep(1);
            $preload["GatewayPort"] = $port;
        }
    }
    /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    *
    * @access protected
    *
    * @return null
    */
    protected function tearDownSocket()
    {
        // This kills off the old process
        if (!empty($this->pidfile)) {
            $pid = (int)file_get_contents($this->pidfile);
            if (!empty($pid)) {
                posix_kill($pid, SIGINT);
            }
            unlink($this->pidfile);
            $this->pidfile = "";
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
                "",
            ),
            // This test will fail without a local web server
            array(
                array(
                    "GatewayIP" => "127.0.0.1",
                    "GatewayPort" => "",
                ),
                new PacketContainer(
                    array(
                        "To" => "123456",
                        "From" => "555555",
                        "Command" => "03",
                        "Data" => array(1,2,3,4),
                    )
                ),
                true,
                (string)new PacketContainer(
                    array(
                        "To" => "123456",
                        "From" => "555555",
                        "Command" => "03",
                        "Data" => array(1,2,3,4),
                    )
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The value to preload
    * @param string $write   The string to write
    * @param mixed  $expect  The expected return
    * @param string $buffer  What we expect to be written
    *
    * @return null
    *
    * @dataProvider dataSendPkt
    */
    public function testSendPkt($preload, $write, $expect, $buffer)
    {
        $this->setUpSocket($preload);
        $obj = new GatewaySocket($preload);
        $ret = $obj->sendPkt($write);
        $this->assertSame($expect, $ret);
        $ret = $obj->read(1024);
        $this->assertSame($buffer, $ret);
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
            /*
            array(
                array(
                    "GatewayIP" => "127.0.0.1",
                    "GatewayPort" => "",
                ),
                (string)new PacketContainer(
                    array(
                        "To" => "654321",
                        "From" => "000235",
                        "Command" => "01",
                        "Data" => array(5,4,3,2,1),
                    )
                ),
                true,
                new PacketContainer(
                    array(
                        "From" => "654321",
                        "To" => "000235",
                        "Command" => "55",
                        "Timeout" => 5
                    )
                ),
                (string)new PacketContainer(
                    array(
                        "To" => "654321",
                        "From" => "000235",
                        "Command" => "01",
                        "Data" => array(5,4,3,2,1),
                    )
                ),
            ),
            */
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
        $this->setUpSocket($preload);
        $obj = new GatewaySocket($preload);
        $obj->write($write);
        $ret = $obj->recvPkt($pkt);
        $this->assertSame($expect, $ret);
        if (!empty($buffer)) {
            $this->assertSame($buffer, (string)$pkt->Reply, "Reply Wrong");
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
     * @param array $IPaddr The IP address to test
     * @param array $expect The return value to expect
     *
     * @return null
     *
     * @dataProvider dataEncodeIP().
     */
    public function testEncodeIP($IPaddr, $expect)
    {
        $obj = new GatewaySocket(array());
        $ret = $obj->encodeIP($IPaddr);
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
     * @param array $IPaddr The IP address to test
     * @param array $expect The return value to expect
     *
     * @return null
     *
     * @dataProvider dataDecodeIP().
     */
    public function testDecodeIP($IPaddr, $expect)
    {
        $obj = new GatewaySocket(array());
        $ret = $obj->decodeIP($IPaddr);
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
        $obj = new GatewaySocket(array());
        $obj->$var = $value;
        $data = $this->readAttribute($obj, "data");
        $this->assertSame($expect, $data[$var]);
    }

}

?>