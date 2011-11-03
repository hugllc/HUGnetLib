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
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteSockets
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is our namespace */
namespace HUGnet\network\physical;

/** This is a required class */
require_once CODE_BASE.'network/SocketServer.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteSockets
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class SocketServerTest extends \PHPUnit_Framework_TestCase
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
        $this->tearDownSocket();
    }
    /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    *
    * This is a really bad routine.  It works for now, but it should be refactored.
    *
    * @param array  $preload The preload data to fix
    * @param string $send    The data (if any) to send on connect
    *
    * @access protected
    *
    * @return null
    */
    protected function setUpSender($preload, $send = "")
    {
        if ($preload["type"] == AF_UNIX) {
            $port = $preload["location"];
            $this->pidfile[] = $port;
        } else {
            $port = (int)$preload["port"];
        }
        if (is_array($send)) {
            $pidfile = tempnam(sys_get_temp_dir(), 'SocketServer');
            $fd = fopen($pidfile, "w");
            fwrite($fd, implode("\n", $send));
            fclose($fd);
            exec(
                "php ".TEST_CONFIG_BASE."scripts/oneshotclient.php "
                .$port." ".$pidfile." > /dev/null 2>&1 &"
            );
        } else if ("fork" === $send) {
            $pidfile = tempnam(sys_get_temp_dir(), 'SocketServer');
            exec(
                "php ".TEST_CONFIG_BASE."scripts/forkclient.php "
                .$port." ".$pidfile." > /dev/null 2>&1 &"
            );
            // This script takes longer to get going
            sleep(1);
        } else {
            $pidfile = tempnam(sys_get_temp_dir(), 'SocketServer');
            exec(
                "php ".TEST_CONFIG_BASE."scripts/socketclient.php "
                .$port." ".$pidfile." > /dev/null 2>&1 &"
            );
        }
        $this->pidfile[] = $pidfile;
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
        foreach ((array)$this->pidfile as $key => $file) {
            if (file_exists($file)) {
                $pid = (int)file_get_contents($file);
                if (!empty($pid)) {
                    posix_kill($pid, SIGINT);
                }
                unlink($file);
            }
            unset($this->pidfile[$key]);
        }
    }

    /**
    * data provider for testConnect
    *
    * @return array
    */
    public static function dataWrite()
    {
        return array(
            array( // #0
                array(
                    "type" => AF_INET,
                    "location" => "10.1.234.235",
                    "port" => 3289,
                    "bus" => true,
                ),
                "",
                "",
                false,
                false,
                "RuntimeException",
            ),
            array( // #1
                array(
                    "type" => AF_INET,
                    "location" => "127.0.0.1",
                    "port" => self::findPort(),
                    "bus" => true,
                ),
                "",
                "5A5A5A010102030405060401020304C3",
                32,
                "5A5A5A010102030405060401020304C3",
                null,
            ),
            /* Not sure why this one will never fork
            array( // #2
                array(
                    "type" => AF_INET,
                    "location" => "127.0.0.1",
                    "port" => self::findPort(),
                    "bus" => false,
                ),
                "fork",
                "5A5A5A010102030405060401020304C3",
                32,
                "5A5A5A010102030405060401020304C3"
                ."5A5A5A010102030405060401020304C3",
                null,
            ),
            array( // #3
                array(
                    "type" => AF_UNIX,
                    "location" => sys_get_temp_dir()."/test".md5(mt_rand()),
                    "port" => 0,
                    "bus" => false,
                ),
                "fork",
                "5A5A5A010102030405060401020304C3",
                32,
                "5A5A5A010102030405060401020304C3"
                ."5A5A5A010102030405060401020304C3",
                null,
            ),
            */
            array( // #4
                array(
                    "type" => AF_UNIX,
                    "location" => sys_get_temp_dir()."/test".md5(mt_rand()),
                    "port" => 0,
                    "bus" => false,
                ),
                "",
                "",
                0,
                "",
                null,
            ),
            array( // #5
                array(
                    "type" => AF_UNIX,
                    "location" => sys_get_temp_dir()."/test".md5(mt_rand()),
                    "port" => 0,
                    "bus" => false,
                ),
                array("Hello There"),
                "",
                0,
                "Hello There",
                null,
            ),
            array( // #6
                array(
                    "type" => AF_UNIX,
                    "location" => sys_get_temp_dir()."/test".md5(mt_rand()),
                    "port" => 0,
                    "bus" => false,
                ),
                // These all have to be the same because we can't be
                // sure of the order they will arrive in.  The order doesn't
                // matter anyway
                array(
                    "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A",
                    "A", "A", "A", "A", "A", "A", "A", "A", "A", "A"
                ),
                "",
                0,
                "AAAAAAAAAAAAAAAAAAAAA",
                null,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload   The value to preload
    * @param array  $clients   Array of what the clients should write to the server
    * @param string $write     The string to write
    * @param mixed  $expect    The expected return
    * @param string $buffer    What we expect to be written
    * @param string $exception The exception to expect.  Null for none
    *
    * @return null
    *
    * @dataProvider dataWrite
    */
    public function testWrite(
        $preload, $clients, $write, $expect, $buffer, $exception
    ) {
        if (is_string($exception)) {
            $this->setExpectedException($exception);
        }
        // This sets up the server
        $obj = SocketServer::factory($preload);
        $this->setupSender($preload, $clients);
        $string = "";
        // This allows time for the connections to happen
        // We start capturing the string here because prewrites will be missed
        // otherwise
        for ($i = 0; $i < 100; $i++) {
            $string .= $obj->read();
        }
        // This writes to the connection
        $ret = $obj->write($write);
        $this->assertSame($expect, $ret, "The return is wrong");
        // This reads what we have written
        $string .= $obj->read();
        $this->assertSame($buffer, $string, "The string is wrong");
        // This clean up everything
        unset($obj);
    }
    /**
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testAvailable()
    {
        // This sets up the server
        $obj = SocketServer::factory(
            array(
                "type" => AF_UNIX,
                "location" => sys_get_temp_dir()."/test".md5(mt_rand()),
                "port" => 0,
                "bus" => false,
            )
        );
        $this->assertTrue($obj->available());
        unset($obj);
    }

}

?>
