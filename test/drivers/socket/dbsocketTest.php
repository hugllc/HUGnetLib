<?php
/**
 * Tests the epsocket class
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
 * @category   Database
 * @package    HUGnetLibTest
 * @subpackage Socket
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
if (!defined("HUGNET_INCLUDE_PATH")) {
    define("HUGNET_INCLUDE_PATH", dirname(__FILE__)."/../../..");
}

require_once dirname(__FILE__).'/../../../drivers/socket/dbsocket.php';

/**
 * Test class for epsocket.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:48:38.
 *
 * @category   Database
 * @package    HUGnetLibTest
 * @subpackage Socket
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DbSocketTest extends PHPUnit_Framework_TestCase
{

    protected $table = "PacketSend";

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
        parent::setUp();
        if (!empty($this->id)) {
            $this->config["id"] = $this->id;
        }
        if (!empty($this->table)) {
            $this->config["table"] = $this->table;
        }
        $this->config["file"]        = ":memory:";
        $this->config["socketTable"] = $this->table;
        $this->config["servers"][0]  = array(
            'driver' => 'sqlite',
            'host'   => 'localhost',
            'user'   => '',
            'pass'   => '',
        );

        $this->plog = &HUGnetDB::getInstance("Plog", $this->config);
        $this->plog->createTable($this->table);

        $this->pdo =& $this->readAttribute($this->plog, "db");
        $this->pdo->query($query);
        $this->o = new dbsocket($this->config); //new dbsocket($this->pdo);
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
        $this->o->Close();
        unset($this->o);
        //        unset($this->oBadDB);
    }

    /**
    * data provider for testWrite
    *
    * @return array
    */
    public static function dataWrite()
    {
        return array(
            array(
                "pkt" => array(
                    "PacketTo" => "ABCDE",
                    "sendCommand" => "5C",
                    "Date" => "2007-11-23 05:02:01",
                    "GatewayKey" => 1,
                    "DeviceKey" => 5,
                    "Type" => "OUTGOING",
                    "RawData" => "01020304",
                    "sentRawData" => "01020304",
               ),
                "expect" => array(
                    "DeviceKey" => 5,
                    "GatewayKey" => 1,
                    "Date" => "2007-11-23 05:02:01",
                    "Command" => "",
                    "sendCommand" => "5C",
                    "PacketFrom" => "",
                    "PacketTo" => "ABCDE",
                    "RawData" => "01020304",
                    "sentRawData" => "01020304",
                    "Type" => "OUTGOING",
                    "Status" => "NEW",
                    "ReplyTime" => 0,
                    "Checked" => 0,
               ),
           ),

        );
    }

    /**
    * test
    *
    * @param array $pkt    The packet to test with
    * @param array $expect What we expect returned
    *
    * @return null
    *
    * @dataProvider dataWrite
    */
    public function testWrite($pkt, $expect)
    {
        /*
        $id = $this->o->Write($pkt);
        $query = "SELECT * FROM ".$this->table." WHERE id=".$id;
        $ret = $this->pdo->query($query);
        $res = $ret->fetchAll(PDO::FETCH_ASSOC);
        $res = $res[0];
        if (is_array($res)) {
            foreach ($res as $key => $rec) {
                if (is_numeric($key)) unset($res[$key]);
            }
        }
        unset($res["id"]);
        $this->assertEquals($expect, $res);
        */
    }
        /**
    * data provider for testWrite
    *
    * @return array
    */
    public static function dataRecvPacket()
    {
        return array(
            array(
                array(),
                2,
                true,
                false,
                null,
            ),
            array(
                array(
                    array(
                        "id" => 1,
                        "DeviceKey" => 42,
                        "GatewayKey" => 2,
                        "Date" => "2007-12-25 00:00:00",
                        "Command" => "01",
                        "SendCommand" => "5A",
                        "PacketFrom" => "123456",
                        "PacketTo" => "000011",
                        "RawData" => "00392802410039201340001231FFFFFF",
                        "sentRawData" => "",
                        "Type" => "REPLY",
                        "Status" => "NEW",
                        "ReplyTime" => 2.532123,
                        "Checked" => 0,
                    ),
                ),
                2,
                true,
                false,
                null,
            ),
        );
    }

    /**
    * test
    *
    * @param array $preload Data to preload into the database
    * @param int   $timeout The timeout value for the test
    * @param bool  $reply   Whether what we are seeing is a reply
    * @param bool  $getAll  Whether to return all packets or not
    * @param array $expect  What we expect returned
    *
    * @return null
    *
    * @dataProvider dataRecvPacket
    */
    public function testRecvPacket($preload, $timeout, $reply, $getAll, $expect)
    {
        $this->o->socket->addArray($preload);
        $this->o->getAll($getAll);
        $this->o->recvPacket($timeout, $reply);
        $this->assertSame($expect, $ret);
    }

    /**
    * data provider for testWrite
    *
    * @return array
    */
    public static function dataConfig()
    {
        return array(
            array(
                array(),
                array(
                    "table" => "PacketLog",
                ),
            ),
            array(
                array(
                    "socketTable" => "TestTable",
                    "asdf" => "dfsa",
                ),
                array(
                    "socketTable" => "TestTable",
                    "asdf" => "dfsa",
                    "table" => "TestTable",
                ),
            ),

        );
    }

    /**
    * test
    *
    * @param array $config The packet to test with
    * @param array $expect What we expect returned
    *
    * @return null
    *
    * @dataProvider dataConfig
    */
    public function testConfig($config, $expect)
    {
        $o = new dbsocket($config);
        $this->assertSame($expect, $o->config);
    }

    /**
    * data provider for testWrite
    *
    * @return array
    */
    public static function dataUnbuildPacket()
    {
        return array(
            array(
                array(),
                array(),
            ),
            array(
                "This is not an array",
                array(),
            ),
            array(
                array(
                    "sendCommand" => "5A",
                    "To" => "123456",
                    "From" => "678901",
                    "RawData" => "123456789012",
                ),
                array(
                    "Command" => "5A",
                    "To" => "123456",
                    "From" => "678901",
                    "Length" => 6,
                    "RawData" => "123456789012",
                    "Data" => array(18, 52, 86, 120, 144, 18),
                    "Checksum" => "8A",
                    "CalcChecksum" => "8A",
                ),
            ),
            array(
                array(
                    "Command" => "5A",
                    "PacketTo" => "123456",
                    "PacketFrom" => "678901",
                    "RawData" => "123456789012",
                ),
                array(
                    "Command" => "5A",
                    "To" => "123456",
                    "From" => "678901",
                    "Length" => 6,
                    "RawData" => "123456789012",
                    "Data" => array(18, 52, 86, 120, 144, 18),
                    "Checksum" => "8A",
                    "CalcChecksum" => "8A",
                ),
            ),
            array(
                array(
                    "Command" => "5A",
                    "sendCommand" => "5B",
                    "PacketTo" => "123456",
                    "To" => "678901",
                    "PacketFrom" => "678901",
                    "From" => "123456",
                    "RawData" => "123456789012",
                ),
                array(
                    "Command" => "5A",
                    "To" => "123456",
                    "From" => "678901",
                    "Length" => 6,
                    "RawData" => "123456789012",
                    "Data" => array(18, 52, 86, 120, 144, 18),
                    "Checksum" => "8A",
                    "CalcChecksum" => "8A",
                ),
            ),
        );
    }

    /**
    * test
    *
    * @param array $data   The packet to test with
    * @param array $expect What we expect returned
    *
    * @return null
    *
    * @dataProvider dataUnbuildPacket
    */
    public function testUnbuildPacket($data, $expect)
    {
        $ret = $this->o->unbuildPacket($data);
        $this->assertSame($expect, $ret);
    }


    /**
    * data provider for testWriteBadDB()
    *
    * @return array
    */
    public static function dataWriteBadDB()
    {
        return array(
            array(
                "str" => "",
                "pkt" => array(
                    "PacketTo" => "ABCDE",
                    "sendCommand" => "5C",
                    "Date" => "2007-11-23 05:02:01",
                    "GatewayKey" => 1,
                    "DeviceKey" => 5,
                    "Type" => "OUTGOING",
                    "RawData" => "01020304",
                    "sentRawData" => "01020304",
               ),
                "expect" => false,
           ),
        );
    }
    /**
    * test
    *
    * @param string $str    The packet string to send
    * @param array  $pkt    The packet to test with
    * @param array  $expect What we expect returned
    *
    * @return null
    *
    * @dataProvider dataWriteBadDB
    */
    public function testWriteBadDB($str, $pkt, $expect)
    {
        //        $id = $this->oBadDB->Write($str, $pkt);
        //        $this->assertEquals($expect, $id);
    }


    /**
    * test
    *
    * @return null
    *
    * @todo Implement testClose().
    */
    public function testClose()
    {
        $this->o->Close();
        $this->assertSame(false, $this->o->socket);
    }

    /**
    * test
    *
    * @return null
    *
    * @todo Implement testCheckConnect().
    */
    public function testCheckConnect()
    {
        $ret = $this->o->CheckConnect();
        $this->assertTrue($ret);
    }

    /**
    * test
    *
    * @return null
    *
    * @todo Implement testConnect().
    */
    public function testConnect()
    {
        $ret = $this->o->Connect();
        $this->assertTrue($ret);
    }

    /**
    * test
    *
    * @return null
    *
    * @todo Implement testConnect().
    */
    public function testConnectBadDB()
    {
        //        $ret = $this->oBadDB->Connect();
        //        $this->assertFalse($ret);
    }
}

?>
