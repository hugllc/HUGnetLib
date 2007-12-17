<?php
/**
 * Tests the epsocket class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: driver.php 529 2007-12-10 23:12:39Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @version SVN: $Id: dbsocketTest.php 429 2007-11-05 15:59:46Z prices $
 *
 */

// Call dbsocketTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "dbsocketTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../../../drivers/socket/dbsocket.php';

/**
 * Test class for epsocket.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:48:38.
 */
class dbsocketTest extends PHPUnit_Framework_TestCase {

    private $table = "PacketSend";
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
      */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("dbsocketTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
      */
    protected function setUp() {
        $this->db = new PDO('sqlite::memory');
        $this->plog = new plog($this->db);
        $this->plog->createTable($this->table);
        $this->db->query($query);
        $this->s = new dbsocket($this->db);

//        $this->BadDB = new PDO('mysql');
//        $this->sBadDB = new dbsocket($this->BadDB);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
      */
    protected function tearDown() {
        $this->s->Close();
        unset($this->s);
//        unset($this->sBadDB);
    }

    public static function dataWrite() {
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
     * @dataProvider dataWrite
      */
    public function testWrite($str, $pkt, $expect) {
        $id = $this->s->Write($str, $pkt);
        $query = "SELECT * FROM ".$this->table." WHERE id=".$id;
        $ret = $this->db->query($query);
        $res = $ret->fetchAll(PDO::FETCH_ASSOC);
        $res = $res[0];
        if (is_array($res)) {
            foreach ($res as $key => $rec) {
                if (is_numeric($key)) unset($res[$key]);
            }
        }
        unset($res["id"]);
        $this->assertEquals($expect, $res);
    }

    public static function dataWriteBadDB() {
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
     * @dataProvider dataWriteBadDB
      */
    public function testWriteBadDB($str, $pkt, $expect) {
//        $id = $this->sBadDB->Write($str, $pkt);
//        $this->assertEquals($expect, $id);
    }

    public static function dataReadChar() {
        return array(
            array(
                "id" => 123456,
                "queries" => array(
                    0 => "('id', 'DeviceKey', 'GatewayKey', 'Date', 'Command','sendCommand' "
                        .", 'PacketFrom', 'PacketTo', 'RawData', 'sentRawData' "
                        .", 'Type', 'ReplyTime', 'Checked') "
                        ." VALUES "
                        ."(3456, 5, 1, '2007-11-23 05:02:03', '01', '5C'"
                        .", 'ABCDEF', '000020', '01020304', '01020304'"
                        .", 'REPLY', 0.134, 2)",
                    1 => "('id', 'DeviceKey', 'GatewayKey', 'Date', 'Command','sendCommand' "
                        .", 'PacketFrom', 'PacketTo', 'RawData', 'sentRawData' "
                        .", 'Type', 'ReplyTime', 'Checked') "
                        ." VALUES "
                        ."(123456, 5, 1, '2007-11-23 05:02:03', '01', '5C'"
                        .", 'ABCDEF', '000020', '01020304', '01020304'"
                        .", 'REPLY', 0.134, 2)",
                ),
                "expect" => "5A5A5A01000020ABCDEF0401020304A8",
            ),
        );
    }

    /**
     * @dataProvider dataReadChar
      */
    public function testReadChar($id, $queries, $expect) {
        foreach ($queries as $query) {
            $this->db->query("INSERT INTO ".$this->table." ".$query);
        }
        $this->s->packet[$id] = array(1,2,3,4);
        $str = "";
        // This calls readChar the way it was meant to be called.
        // i.e. over and over until false is returned.
        do {
            $char = $this->s->readChar();
            if ($char === false) break;
            $str .= $char;
        } while ($char !== false);
        $this->assertSame($expect, devInfo::hexifyStr($str));

    }


    /**
     * @todo Implement testReadChar().
      */
    public function testReadCharNoChar() {
        $char = $this->s->readChar();
        $this->assertFalse($char);
    }

    /**
     * @todo Implement testClose().
      */
    public function testClose() {
        $this->s->packet = array(1,2,3,4);
        $this->s->Close();
        $this->assertSame(array(), $this->s->packet);
    }

    /**
     * @todo Implement testCheckConnect().
      */
    public function testCheckConnect() {
        $ret = $this->s->CheckConnect();
        $this->assertTrue($ret);
    }

    /**
     * @todo Implement testConnect().
      */
    public function testConnect() {
        $ret = $this->s->Connect();
        $this->assertTrue($ret);
    }

    /**
     * @todo Implement testConnect().
      */
    public function testConnectBadDB() {
//        $ret = $this->sBadDB->Connect();
//        $this->assertFalse($ret);
    }
}

// Call dbsocketTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "dbsocketTest::main") {
    dbsocketTest::main();
}
?>
