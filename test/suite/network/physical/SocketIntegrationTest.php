<?php
/**
 * Tests the filter class
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
 * @subpackage SuiteSockets
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is our namespace */
namespace HUGnet\network\physical;

/** This is a required class */
require_once CODE_BASE.'network/physical/SocketServer.php';
/** This is a required class */
require_once CODE_BASE.'network/physical/Socket.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteSockets
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class SocketIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /** @var This is the pid file for the socket server */
    protected $pidfile = array();
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
    }
    /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    *
    * @return null
    */
    protected static function findPort()
    {
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        $port = 0;
        socket_bind($sock, "127.0.0.1", 0);
        socket_getsockname($sock, $address, $port);
        socket_close($sock);
        return $port;
    }

    /**
    * data provider for testConnect
    *
    * @return array
    */
    public static function dataServer()
    {
        $port = self::findPort();
        $file = sys_get_temp_dir()."/serverInteg".mt_rand();
        $file2 = sys_get_temp_dir()."/serverInteg2".mt_rand();
        touch($file2);
        return array(
            array( // #0
                array(
                    "type" => "AF_INET",
                    "location" => "127.0.0.1",
                    "port" => $port,
                    "bus" => false,
                ),
                array(
                    "default" => array(
                        "type" => "AF_INET",
                        "location" => "127.0.0.1",
                        "port" => $port,
                        "name" => "default",
                    ),
                    "default2" => array(
                        "type" => AF_INET,
                        "location" => "127.0.0.1",
                        "port" => $port,
                        "name" => "default2",
                    ),
                ),
                10,
                // There must be data from each client before they actually connect
                // therefore, numbers should be lower in the client array.
                array(
                    6 => "070809",
                ),
                array(
                    "default" => array(
                        4 => "040506",
                        5 => "",
                        6 => "",
                        7 => "",
                    ),
                    "default2" => array(
                        2 => "010203",
                        3 => "",
                        7 => "",
                    ),
                ),
                // The ending is on twice because it is echoed back by each client
                "010203040506",
                array(
                    "default2" => "",
                    "default" => "070809",
                ),
                null,
            ),
            array( // #1
                array(
                    "type" => AF_UNIX,
                    "location" => $file,
                    "bus" => true,
                    "perms" => 0644,
                ),
                array(
                    "default" => array(
                        "type" => AF_UNIX,
                        "location" => $file,
                        "name" => "default",
                    ),
                ),
                10,
                // There must be data from each client before they actually connect
                // therefore, numbers should be lower in the client array.
                array(
                    2 => "02",
                    4 => "04",
                    6 => "06",
                ),
                array(
                    "default" => array(
                        0 => "",
                        1 => "01",
                        2 => "",
                        3 => "03",
                        4 => "",
                        5 => "05",
                        6 => "",
                        7 => "07",
                        8 => "",
                    ),
                ),
                "01030507",
                array(
                    "default" => "020406",
                ),
                null,
            ),
            array( // #2 this should fail silently
                array(
                    "type" => AF_INET,
                    "location" => "127.0.0.1",
                    "port" => self::findPort(),
                    "bus" => false,
                ),
                array(
                    "default" => array(
                        "type" => AF_UNIX,
                        "location" => sys_get_temp_dir()."/serverInteg".mt_rand(),
                        "name" => "default",
                        "quiet" => true,
                    ),
                ),
                1,
                array(
                ),
                array(
                    "default" => array(""),
                ),
                "",
                array(
                    "default" => "",
                ),
                null,
            ),
            array( // #3 File already exists
                array(
                    "type" => AF_UNIX,
                    "location" => $file2,
                    "bus" => true,
                    "force" => true,
                ),
                array(
                    "default" => array(
                        "type" => AF_UNIX,
                        "location" => $file2,
                        "name" => "default",
                    ),
                ),
                10,
                // There must be data from each client before they actually connect
                // therefore, numbers should be lower in the client array.
                array(
                    2 => "02",
                    4 => "04",
                    6 => "06",
                ),
                array(
                    "default" => array(
                        0 => "",
                        1 => "01",
                        2 => "",
                        3 => "03",
                        4 => "",
                        5 => "05",
                        6 => "",
                        7 => "07",
                        8 => "",
                    ),
                ),
                "01030507",
                array(
                    "default" => "020406",
                ),
                null,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $server      Server configs
    * @param array $clients     Client Configs
    * @param int   $loops       The number of loops to go through.
    * @param array $servWrite   Array of strings to write from the server
    * @param array $clientWrite The string to writ
    * @param array $expect      The expected return
    * @param array $clientRead  The read from each client
    * @param mixed $exception   Expected exception
    *
    * @return null
    *
    * @dataProvider dataServer
    * @large
    */
    public function testServer(
        $server, $clients, $loops, $servWrite, $clientWrite,
        $expect, $clientRead, $exception
    ) {
        if (is_string($exception)) {
            $this->setExpectedException($exception);
        }
        $sys = $this->getMock(
            "\\HUGnet\\System",
            array("now")
        );
        $sys->expects($this->any())
            ->method('now')
            ->will($this->returnValue(123456));
        if (!empty($server)) {
            $serv = SocketServer::factory($sys, $server);
        }
        $clien = array();
        $sread = "";
        $cread = array();
        for ($i = 0; $i < $loops; $i++) {
            foreach (array_keys($clients) as $key) {
                if (!is_null($clientWrite[$key][$i]) && !is_object($clien[$key])) {
                    // Create the client if there is something to write
                    $clien[$key] = &Socket::factory($sys, $clients[$key]);
                } else if (is_null($clientWrite[$key][$i])) {
                    // Destroy the client if there is not
                    unset($clien[$key]);
                    continue;
                }
                // This accepts the client
                $sread .= $serv->read();
                // Read from the client
                $read = $clien[$key]->read();
                // Save the read
                $cread[$key] .= $read;
                // Clients just return what they were given
                $clien[$key]->write($clientWrite[$key][$i]);
            }
            $serv->write($servWrite[$i]);
        }
        $this->assertSame($expect, $sread, "Server read wrong");
        $this->assertSame($clientRead, $cread, "Client read wrong");
    }
    /**
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testAvailable()
    {
        $sock = sys_get_temp_dir()."/test".md5(mt_rand());
        $sys = $this->getMock(
            "\\HUGnet\\System",
            array("now")
        );
        $sys->expects($this->any())
            ->method('now')
            ->will($this->returnValue(123456));
        // This sets up the server
        $obj = SocketServer::factory(
            $sys,
            array(
                "type" => AF_UNIX,
                "location" => $sock,
                "port" => 0,
                "bus" => false,
            )
        );
        // This sets up the client
        $obj2 = Socket::factory(
            $sys,
            array(
                "type" => AF_UNIX,
                "location" => $sock,
                "port" => 0,
                "bus" => false,
            )
        );
        $this->assertTrue($obj->available());
        $this->assertTrue($obj2->available());
        unset($obj);
        unset($obj2);
    }

}

?>
