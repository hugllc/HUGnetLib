<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Sockets
 * @package    HUGnetLibTest
 * @subpackage Sockets
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../sockets/PacketSocket.php';
require_once dirname(__FILE__).'/../../containers/PacketContainer.php';
require_once 'PHPUnit/Extensions/Database/TestCase.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PacketSocketTest extends PHPUnit_Extensions_Database_TestCase
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
        $config = array(
            "servers" => array(
                array(
                    "driver" => "sqlite",
                    "file" => ":memory:",
                ),
            ),
            "sockets" => array(
                array(
                    "dbGroup" => "default",
                ),
            ),
        );
        $this->myConfig = &ConfigContainer::singleton();
        $this->myConfig->forceConfig($config);
        $this->pdo = &$this->myConfig->servers->getPDO();
        $this->pdo->query("DROP TABLE IF EXISTS `PacketSocket`");
        $this->mySocket = &$this->myConfig->sockets->getSocket();
        $this->myTable = new PacketSocketTable();
        $this->myTable->create();
        $this->o = new PacketSocket();
        parent::setUp();
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
         if (is_object($this->pdo)) {
            $this->pdo->query("DROP TABLE IF EXISTS `PacketSocket`");
        }
        $this->o   = null;
        $this->pdo = null;
    }
    /**
    * This sets up our database connection
    *
    * @return null
    */
    protected function getConnection()
    {
        return $this->createDefaultDBConnection($this->pdo, "sqlite");
    }

    /**
    * This gets us our database preload
    *
    * @access protected
    *
    * @return null
    */
    protected function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__).'/../files/PacketSocketTest.xml'
        );
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
                    "dbGroup" => "default",
                    "group" => "default",
                    "Timeout" => 10,
                    "readIndex" => 0,
                    "DeviceID" => "000020",
                    "senderID" => 0,
                ),
            ),
            array(
                array(
                    "dbGroup" => "myDBGroup",
                    "group" => "myGroup",
                    "Timeout" => 15,
                    "readIndex" => 5,
                    "DeviceID" => "000021",
                    "senderID" => "12345",
                ),
                array(
                    "dbGroup" => "myDBGroup",
                    "group" => "myGroup",
                    "Timeout" => 15,
                    "readIndex" => 5,
                    "DeviceID" => "000021",
                    "senderID" => 0,
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
        $o = new PacketSocket($preload);
        $this->assertThat($o->senderID, $this->greaterThan(0));
        $o->senderID = 0;
        $this->assertAttributeSame($expect, "data", $o);
    }
    /**
    * data provider for testConnectExc
    *
    * @return array
    */
    public static function dataConnectExc()
    {
        return array(
            array(
                array(
                    "dbGroup" => "nondefault",
                    "group" => "alsonondefault",
                    "Timeout" => 5,
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * This is for testing what happens when there is no database connection
    * available.  It should cause an exception.
    *
    * @param array $preload The value to preload
    *
    * @return null
    *
    * @expectedException Exception
    *
    * @dataProvider dataConnectExc
    */
    public function testConnectExc($preload)
    {
        $this->o->fromArray($preload);
        $ret = $this->o->connect();
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
                array(),
                true,
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
    * @dataProvider dataConnect
    */
    public function testConnect($preload, $expect)
    {
        $this->o->fromArray($preload);
        $ret = $this->o->connect();
        $this->assertSame($expect, $ret);
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
                array(),
                null,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The value to preload
    * @param array $expect  The expected return
    * @param mixed $socket  What to expect in the socket
    *
    * @return null
    *
    * @dataProvider dataDisconnect
    */
    public function testDisconnect($preload, $expect)
    {
        $this->o->fromArray($preload);
        $this->o->connect();
        $this->o->disconnect();
        $ret = $this->readAttribute($this->o, "myTable");
        if (is_null($expect)) {
            $this->assertNull($ret);
        } else {
            $this->assertSame($expect, get_class($ret));
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
            // No packets
            array(
                array(
                ),
                array(
                    new PacketContainer(),
                ),
                false,
                array(
                ),
            ),
            // One packet
            array(
                array(
                ),
                array(
                    new PacketContainer(array(
                        "To" => "123456",
                        "From" => "654321",
                        "Command" => "01",
                        "Data" => "010203040506",
                        "Date" => "2003-02-28 01:59:00",
                    )),
                ),
                true,
                array(
                    array(
                        "id" => "1",
                        "Date" => "2003-02-28 01:59:00",
                        "Command" => "01",
                        "PacketFrom" => "654321",
                        "PacketTo" => "123456",
                        "RawData" => "010203040506",
                        "Type" => "REPLY",
                        "ReplyTime" => "0.0",
                        "Checked" => "0",
                    ),
                ),
            ),
            // Many packets
            array(
                array(
                ),
                array(
                    new PacketContainer(array(
                        "To" => "000001",
                        "Command" => "55",
                        "Data" => "010203040506",
                        "Date" => "2003-02-28 01:59:00",
                    )),
                    new PacketContainer(array(
                        "To" => "000002",
                        "Command" => "55",
                        "Data" => "010203040506",
                        "Date" => "2003-02-28 01:59:00",
                    )),
                    new PacketContainer(array(
                        "To" => "000003",
                        "Command" => "55",
                        "Data" => "010203040506",
                        "Date" => "2003-02-28 01:59:00",
                    )),
                    new PacketContainer(array(
                        "To" => "000004",
                        "Command" => "55",
                        "Data" => "010203040506",
                        "Date" => "2003-02-28 01:59:00",
                    )),
                    new PacketContainer(array(
                        "To" => "000005",
                        "Command" => "55",
                        "Data" => "010203040506",
                        "Date" => "2003-02-28 01:59:00",
                    )),
                    new PacketContainer(array(
                        "To" => "000006",
                        "Command" => "55",
                        "Data" => "010203040506",
                        "Date" => "2003-02-28 01:59:00",
                    )),
                    new PacketContainer(array(
                        "To" => "000007",
                        "Command" => "55",
                        "Data" => "010203040506",
                        "Date" => "2003-02-28 01:59:00",
                    )),
                    new PacketContainer(array(
                        "To" => "000008",
                        "Command" => "55",
                        "Data" => "010203040506",
                        "Date" => "2003-02-28 01:59:00",
                    )),
                    new PacketContainer(array(
                        "To" => "000009",
                        "Command" => "55",
                        "Data" => "010203040506",
                        "Date" => "2003-02-28 01:59:00",
                    )),
                    new PacketContainer(array(
                        "To" => "00000A",
                        "Command" => "55",
                        "Data" => "010203040506",
                        "Date" => "2003-02-28 01:59:00",
                    )),
                ),
                true,
                array(
                    array(
                        "id" => "1",
                        "Date" => "2003-02-28 01:59:00",
                        "Command" => "55",
                        "PacketFrom" => "000000",
                        "PacketTo" => "000001",
                        "RawData" => "010203040506",
                        "Type" => "SENSORREAD",
                        "ReplyTime" => "0.0",
                        "Checked" => "0",
                    ),
                    array(
                        "id" => "2",
                        "Date" => "2003-02-28 01:59:00",
                        "Command" => "55",
                        "PacketFrom" => "000000",
                        "PacketTo" => "000002",
                        "RawData" => "010203040506",
                        "Type" => "SENSORREAD",
                        "ReplyTime" => "0.0",
                        "Checked" => "0",
                    ),
                    array(
                        "id" => "3",
                        "Date" => "2003-02-28 01:59:00",
                        "Command" => "55",
                        "PacketFrom" => "000000",
                        "PacketTo" => "000003",
                        "RawData" => "010203040506",
                        "Type" => "SENSORREAD",
                        "ReplyTime" => "0.0",
                        "Checked" => "0",
                    ),
                    array(
                        "id" => "4",
                        "Date" => "2003-02-28 01:59:00",
                        "Command" => "55",
                        "PacketFrom" => "000000",
                        "PacketTo" => "000004",
                        "RawData" => "010203040506",
                        "Type" => "SENSORREAD",
                        "ReplyTime" => "0.0",
                        "Checked" => "0",
                    ),
                    array(
                        "id" => "5",
                        "Date" => "2003-02-28 01:59:00",
                        "Command" => "55",
                        "PacketFrom" => "000000",
                        "PacketTo" => "000005",
                        "RawData" => "010203040506",
                        "Type" => "SENSORREAD",
                        "ReplyTime" => "0.0",
                        "Checked" => "0",
                    ),
                    array(
                        "id" => "6",
                        "Date" => "2003-02-28 01:59:00",
                        "Command" => "55",
                        "PacketFrom" => "000000",
                        "PacketTo" => "000006",
                        "RawData" => "010203040506",
                        "Type" => "SENSORREAD",
                        "ReplyTime" => "0.0",
                        "Checked" => "0",
                    ),
                    array(
                        "id" => "7",
                        "Date" => "2003-02-28 01:59:00",
                        "Command" => "55",
                        "PacketFrom" => "000000",
                        "PacketTo" => "000007",
                        "RawData" => "010203040506",
                        "Type" => "SENSORREAD",
                        "ReplyTime" => "0.0",
                        "Checked" => "0",
                    ),
                    array(
                        "id" => "8",
                        "Date" => "2003-02-28 01:59:00",
                        "Command" => "55",
                        "PacketFrom" => "000000",
                        "PacketTo" => "000008",
                        "RawData" => "010203040506",
                        "Type" => "SENSORREAD",
                        "ReplyTime" => "0.0",
                        "Checked" => "0",
                    ),
                    array(
                        "id" => "9",
                        "Date" => "2003-02-28 01:59:00",
                        "Command" => "55",
                        "PacketFrom" => "000000",
                        "PacketTo" => "000009",
                        "RawData" => "010203040506",
                        "Type" => "SENSORREAD",
                        "ReplyTime" => "0.0",
                        "Checked" => "0",
                    ),
                    array(
                        "id" => "10",
                        "Date" => "2003-02-28 01:59:00",
                        "Command" => "55",
                        "PacketFrom" => "000000",
                        "PacketTo" => "00000A",
                        "RawData" => "010203040506",
                        "Type" => "SENSORREAD",
                        "ReplyTime" => "0.0",
                        "Checked" => "0",
                    ),
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
    * @param array  $packet  The packet as it is in
    *
    * @return null
    *
    * @dataProvider dataSendPkt
    */
    public function testSendPkt($preload, $write, $expect, $packet)
    {
        $start = time();
        $this->o->fromArray($preload);
        $this->o->connect();
        foreach ($write as $pkt) {
            $ret = $this->o->sendPkt($pkt);
        }
        $this->assertSame($expect, $ret);
        $ret = $this->pdo->query("select * from `PacketSocket`");
        $res = $ret->fetchAll(PDO::FETCH_ASSOC);
        foreach (array_keys((array)$res) as $key) {
            $this->assertThat(
                $res[$key]["Timeout"], $this->greaterThan($start)
            );
            $this->assertThat(
                $res[$key]["PacketTime"], $this->greaterThan($start)
            );
            $this->assertThat($res[$key]["senderID"], $this->greaterThan(0));
            unset($res[$key]["senderID"]);
            unset($res[$key]["Timeout"]);
            unset($res[$key]["PacketTime"]);
        }
        $this->assertSame($packet, $res);
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
                ),
                "",
                false,
                new PacketContainer(array("Timeout" => 1)),
            ),
            array(
                array(
                ),
                array(
                    "group" => "default",
                    "id" => null,
                    "Date" => date("Y-m-d H:i:s"),
                    "Command" => "01",
                    "PacketTo" => "654321",
                    "PacketFrom" => "123456",
                    "RawData" => "010203040506",
                    "Type" => "Reply",
                    "Checked" => 0,
                    "ReplyTo" => 0,
                ),
                true,
                new PacketContainer(array(
                    "Timeout" => 5,
                    "From" => "654321",
                    "To" => "123456",
                    "Command" => "5C",
                )),
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
    *
    * @return null
    *
    * @dataProvider dataRecvPkt
    */
    public function testRecvPkt($preload, $write, $expect, $pkt)
    {
        $this->o->fromArray($preload);
        $this->myTable->fromArray($write);
        $this->myTable->insertRow();
        $ret = $this->o->recvPkt($pkt);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testPacketSend
    *
    * @return array
    */
    public static function dataPacketSend()
    {
        return array(
            array(
                array(
                    array(
                        "To" => "654321",
                        "From" => "123456",
                        "Date" => "2010-04-21 18:24:56",
                        "Data" => "010203040506",
                        "Command" => "03",
                        "Timeout" => 1,
                    ),
                ),
                array(
                    array(
                        "To" => "123456",
                        "From" => "654321",
                        "Date" => "2010-04-21 18:24:52",
                        "Data" => "010203040506",
                        "Command" => "01",
                    ),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload  The value to preload
    * @param array $preload2 The value to preload
    * @param mixed $expect   The expected return
    *
    * @return null
    *
    * @dataProvider dataPacketSend
    */
    public function testPacketSend($preload, $preload2)
    {
        $count = 10;
        for ($i = 0; $i < $count; $i++) {
            foreach ($preload as $key => $load) {
                $pkt = new PacketContainer($preload[$key]);
                $pkt2 = new PacketContainer($preload2[$key]);
                $this->mySocket->sendPkt($pkt);
                $this->myTable->fromAny($pkt2);
                $this->myTable->insertRow();
                $this->mySocket->recvPkt($pkt);
                if (!is_object($pkt->Reply)) {
                    $failures++;
                    $this->assertThat(
                        $failures,
                        $this->lessThan($count*0.01),
                        ($count*0.01)." failures in $i attempts ($count total tries)"
                    );
                }
            }
        }
    }

}

?>
