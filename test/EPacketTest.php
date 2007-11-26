<?php
/**
 *   Tests the EPacket class
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Test
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$
 *
 */

// Call EPacketTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "EPacketTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../EPacket.php';

/**
 * Test class for EPacket.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:45:46.
 */
class EPacketTest extends PHPUnit_Framework_TestCase {
    private $Info = array(
        "GatewayKey" => 1,
        "socketType" => "test",
    );
    /** 
     * Test packets in array form 
     *
     * This array needs to have the same keys as $testPacketStr and $testPacketReplyStr     
     * @see EPacketTest::testPacketStr, EPacketTest::testPacketReplyStr    
     */
    var $testPacketArray = array(
        array(
            "Command" => "55",
            "To" => "ABC",
            "From" => "000020",
            "Data" => "01020304",
            "RawData" => "01020304",
            "Length" => 4,
            "Checksum" => "C3",
        ),
        array(
            "Command" => "5C",
            "To" => "000ABC",
            "From" => "000020",
            "RawData" => "",
            "Length" => 0,
            "Checksum" => "CA",
        ),
        array(
            "Command" => "5C",
            "To" => "ABCDEF12345",
            "From" => "000020",
            "RawData" => "",
            "Length" => 0,
            "Checksum" => "F5",
        ),
    );
    /** 
     * Test packets in array form 
     *
     * This array needs to have the same keys as $testPacketStr and $testPacketReplyStr     
     * @see EPacketTest::testPacketStr, EPacketTest::testPacketReplyStr    
     */
    var $testPacketReplyArray = array(
        array(
            "Command" => "01",
            "To" => "000020",
            "From" => "000ABC",
            "Data" => "01020304",
            "Length" => 4,
            "Checksum" => "97",
        ),
        array(
            "Command" => "01",
            "From" => "000ABC",
            "To" => "000020",
            "Data" => "",
            "Length" => 0,
            "Checksum" => "97",
        ),
        array(
            "Command" => "01",
            "From" => "ABCDEF",
            "To" => "000020",
            "Data" => "",
            "Length" => 0,
            "Checksum" => "A8",
        ),
    );
    /** 
     *   Test Packets in string form 
     *
     * This array needs to have the same keys as $testPacketArray and $testPacketReplyStr     
     * @see EPacketTest::testPacketReplyStr, EPacketTest::testPacketArray    
     */
    var $testPacketStr = array(
        "5A5A5A55000ABC0000200401020304C3",
        "5A5A5A5C000ABC00002000CA",
        "5A5A5A5CABCDEF00002000F5",
    );
    /** 
     * Test Packets in string form 
     *
     * This array needs to have the same keys as $testPacketStr and $testPacketArray
     * @see EPacketTest::testPacketStr, EPacketTest::testPacketArray    
     */
    var $testPacketReplyStr = array(
        "5A5A5A01000020000ABC040102030497",
        "5A5A5A01000020000ABC0097",
        "5A5A5A01000020ABCDEF00A8",
    );
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("EPacketTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
        $db = $this->getMock("ADONewConnection", array(), array("sqlite"));
        $this->o = new EPacket(array("GatewayKey" => 1, "socketType" => "test"), FALSE, $db, FALSE);
        $this->o->socket[2] = $this->getMock('epsocketMock', array("Connect", "Close", "ReadChar", "Write"), array("socketType"=>"test"));
//        $this->o->socket[1] = new epsocketMock();
        $this->o->ReplyTimeout = 1; // This is a fast system.  It doesn't need a long timeout
//        $this->o->verbose = TRUE;
        
        $this->txrxMock = new EPacketTXRXMock();
//        ob_start();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
        unset($this->o);
//print ob_get_clean();
//        ob_end_clean();
    }

    private function deHexifyArray($string) {
        $index = 0;
        $array = array();
        while (($index * 2) < strlen($string)) {
            $array[$index] = chr(hexdec(substr($string, $index * 2, 2)));
        }
    }

    /**
     *
     */
    private function cleanTo($to) {
        $to = trim($to);
        $to = substr($to, 0, 6);
        $to = str_pad($to, 6, "0", STR_PAD_LEFT);
        $to = strtoupper($to);
    
        return $to;
    }
    /**
     *
     */
    public function validPacketArray($a, $test=NULL, $keys = NULL) {
        $this->assertTrue(is_array($a), "This is not an array!");
        $this->assertTrue(is_string($a['Command']), "'".$a['Command']."': Packet command must be a string");
        $this->assertEquals(2, strlen($a['Command']), "'".$a['Command']."': Packet command must contain exactly 2 characters");
        $this->assertTrue(is_string($a['To']), "'".$a['To']."': Packet to must be a string");
        $this->assertEquals(6, strlen($a['To']), "'".$a['To']."': Packet to must contain exactly 6 characters");
        if (is_array($test)) {
            foreach($test as $key => $var) {
                // Check to see if the key was specified
                if (is_array($keys)) {
                    // If it was not continue.
                    if (array_search($key, $keys) === FALSE) continue;
                    // Drop through if it was specified, or if nothing was specified.
                }
                if (trim(strtolower($key)) == "to") {
                    $var = $this->cleanTo($var);
                }
                $this->assertEquals($var, $a[$key], "'$key' field is not the same");
            }
        }
    }
    /**
     *
     */
    public function validPacketString($s, $a=NULL) {
        $this->assertTrue(is_string($s), "This is not a string!");
        $s = strtoupper($s);
        $preambleLength = EPacketTest::getPreambleLength($s);
        $this->assertTrue((($preambleLength >= 2) && ($preambleLength <= 3)), "Preamble must be 2 or 3 characters");
        $length = hexdec(substr($s, ($preambleLength + 7) * 2, 2));
        $totalLength = $preambleLength + 8 + $length + 1;
        $this->assertEquals(($totalLength * 2), strlen($s), count($a['command']), "Packet command must contain exactly 2 characters");
        if (is_array($a)) {
            $a = array_change_key_case($a, CASE_LOWER);
            $to = $this->cleanTo($a["to"]);
            $pTo = substr($s, ($preambleLength+1) * 2, 6);
            $this->assertEquals($to, $pTo, "To field is Wrong.  '$to' != '$pTo'");
            $this->assertEquals($length * 2, strlen(trim($a['data'])), "Wrong length parameter.");
        }
    }
    /**
     *
     */
    public function getPreambleLength($pkt) {
        $length = 0;
        while (substr($pkt, ($length*2), 2) == "5A") $length++;    
        return $length;
    }

    
    public static function dataPacketBuild() {
        return array(
            array(
                array(
                    "Command" => "55",
                    "To" => "ABC",
                    "From" => "000020",
                    "Data" => "01020304",
                    "RawData" => "01020304",
                    "Length" => 4,
                    "Checksum" => "C3",
                ),
                "5A5A5A55000ABC0000200401020304C3",
            ),
            array(
                array(
                    "Command" => "5C",
                    "To" => "000ABC",
                    "From" => "000020",
                    "RawData" => "",
                    "Length" => 0,
                    "Checksum" => "CA",
                ),
                "5A5A5A5C000ABC00002000CA",
            ),
            array(
                array(
                    "Command" => "5C",
                    "To" => "ABCDEF12345",
                    "From" => "000020",
                    "RawData" => "",
                    "Length" => 0,
                    "Checksum" => "F5",
                ),
                "5A5A5A5CABCDEF00002000F5",
            ),
        );
    }

    /**
     * @dataProvider dataPacketBuild
     */
    public function testPacketBuild($packet, $expect) {
        $pkt = $this->o->PacketBuild($packet);
        $this->assertSame($expect, $pkt);
    }


    public static function dataPacketGetChecksum() {
        return array(
            array("55000ABC0000200401020304", "C3"),
            array("5C000ABC00002000", "CA"),
            array("5CABCDEF00002000", "F5"),
            array("0300002000000100", "22"),
            array("0300002000000200", "21"),
            array("0300002000000300", "20"),
            array("0300002000000400", "27"),
            array("0300000400002000", "27"),
            array("5E000000000DEF0401020304", "BC"),
        );
    }

    /**
     * @dataProvider dataPacketGetChecksum
     */
    public function testPacketGetChecksum($packet, $expect) {
        $cksum = $this->o->PacketGetChecksum($packet);
        $this->assertSame($expect, $cksum);
    }

    public static function dataBuildPacket() {
        return array(
            array("123", "AB", "12345678", array("To"=>"123", "Command"=>"AB", "Data"=>"12345678")),
            array("3456", "CD", NULL, array("To"=>"3456", "Command"=>"CD", "Data"=>"")),
        );    
    }

    /**
     * @dataProvider dataBuildPacket
     */
    public function testBuildPacket($command, $to, $data, $expect) {
        if (is_null($data)) {
            $pkt = $this->o->buildPacket($command, $to);
        } else {
            $pkt = $this->o->buildPacket($command, $to, $data);
        }
        $this->assertSame($expect, $pkt);
    }

    /**
     * @todo Implement testPacketCallBack().
     */
    public function testPacketCallBackMethod() {
        $t = $this->getMock('EPacketTest_CallBack_Class');
        $string = "ABCDE";
        $t->expects($this->once())
          ->method('Test')
          ->with($this->equalTo($string));
        $this->o->PacketSetCallBack("Test", $t);
        $this->o->PacketCallBack($string);
    }
    /**
     * @todo Implement testPacketCallBack().
     */
    public function testPacketCallBackFunction() {
        $string = "ABCDE";

        $this->o->PacketSetCallBack("EPacketTest_CallBack_Function");
        $this->o->PacketCallBack($string);
        $this->assertEquals($_SESSION['EPacketTest_CallBack_Function'], $string);
    }

    public static function dataSendPacket() {
        return array(
            array(
                // Info
                array("socketType" => "test"),
                // pkt
                array(
                    array(
                        "Command" => "55",
                        "To" => "FGH",
                        "Data" => "01020304",
                    ),
                    array(
                        "Command" => "55",
                        "To" => "ABC",
                        "Data" => "01020304",
                    ),
                ),
                // pktStr
                "5A5A5A55000ABC0000200401020304C3",
                // replyStr
                "5A5A5A01000020000ABC040102030497",
                // expect
                array(
                    array(
                        "pktTimeout" => 1,
                        "GetReply" => TRUE,
                        "SentFrom" => "000020",
                        "SentTo" => "000ABC",
                        "sendCommand" => "55",
                        "group" => FALSE,
                        "packet" => array(
                            "command" => "55",
                            "to" => "000ABC",
                            "data" => "01020304",
                        ),
                        "PacketTo" => "000ABC",
                        "GatewayKey" => 1,
                        "DeviceKey" => NULL,
                        "Type" => "OUTGOING",
                        "RawData" => "01020304",
                        "sentRawData" => "01020304",
                        "Parts" => 1,
                        "Command" => "01",
                        "To" => "000020",
                        "From" => "000ABC",
                        "Length" => 4,
                        "Data" => array(1,2,3,4),
                        "Checksum" => "97",
                        "CalcChecksum" => "97",
                        "RawPacket" => "01000020000ABC040102030497",
                        "Socket" => 1,
                        "Reply" => TRUE,
                        "toMe" => TRUE,
                        "isGateway" => FALSE
                    ),
                ),
            ),
            array(
                // Info
                array("GatewayKey" => 1, "DeviceKey" => 1, "socketType" => "test"),
                // pkt
                array(
                    "Command" => "5C",
                    "To" => "000ABC",
                ),
                // pktStr
                "5A5A5A5C000ABC00002000CA",
                // replyStr
                "5A5A5A7562387523499401787878785A5A5A01000020000ABC0097",
                // expect
                array(
                    array(
                        "pktTimeout" => 1,
                        "GetReply" => TRUE,
                        "SentFrom" => "000020",
                        "SentTo" => "000ABC",
                        "sendCommand" => "5C",
                        "group" => FALSE,
                        "packet" => array(
                            "command" => "5C",
                            "to" => "000ABC",
                            "data" => "",
                        ),
                        "PacketTo" => "000ABC",
                        "GatewayKey" => 1,
                        "DeviceKey" => 1,
                        "Type" => "OUTGOING",
                        "RawData" => "",
                        "sentRawData" => "",
                        "Parts" => 1,
                        "Command" => "01",
                        "To" => "000020",
                        "From" => "000ABC",
                        "Length" => 0,
                        "Data" => NULL,
                        "Checksum" => "97",
                        "CalcChecksum" => "97",
                        "RawPacket" => "01000020000ABC0097",
                        "Socket" => 1,
                        "Reply" => TRUE,
                        "toMe" => TRUE,
                        "isGateway" => FALSE,
                    ),
                ),
            ),
            array(
                // This one gets a good packet from the wrong endpoint
                // back first.
                // Info
                array("GatewayKey" => 1, "DeviceKey" => 1, "socketType" => "test"),
                // pkt
                array(
                    "Command" => "5C",
                    "To" => "12345ABCDEF",
                ),
                // pktStr
                "5A5A5A5CABCDEF00002000F5",
                // replyStr
                "5A5A5A5E000000000DEF0401020304BC5A5A5A01000020ABCDEF0401020304A8",
                // expect
                array(
                    array(
                        "Command" => "5E",
                        "To" => "000000",
                        "From" => "000DEF",
                        "Length" => 4,
                        "RawData" => "01020304",
                        "Data" => array(1,2,3,4),
                        "Checksum" => "BC",
                        "CalcChecksum" => "BC",
                        "RawPacket" => "5E000000000DEF0401020304BC",
                        "Unsolicited" => TRUE,
                        "Socket" => 1,
                        "GatewayKey" => 1,
                        "Reply" => FALSE,
                        "toMe" => FALSE,
                        "isGateway" => FALSE,
                    ),
                    array(
                        "pktTimeout" => 1,
                        "GetReply" => TRUE,
                        "SentFrom" => "000020",
                        "SentTo" => "ABCDEF",
                        "sendCommand" => "5C",
                        "group" => FALSE,
                        "packet" => array(
                            "command" => "5C",
                            "to" => "ABCDEF",
                            "data" => "",
                        ),
                        "PacketTo" => "ABCDEF",
                        "GatewayKey" => 1,
                        "DeviceKey" => 1,
                        "Type" => "OUTGOING",
                        "RawData" => "01020304",
                        "sentRawData" => "",
                        "Parts" => 1,
                        "Command" => "01",
                        "To" => "000020",
                        "From" => "ABCDEF",
                        "Length" => 4,
                        "Data" => array(1,2,3,4),
                        "Checksum" => "A8",
                        "CalcChecksum" => "A8",
                        "RawPacket" => "01000020ABCDEF0401020304A8",
                        "Socket" => 1,
                        "Reply" => TRUE,
                        "toMe" => TRUE,
                        "isGateway" => FALSE,
                    ),
                ),
                // GetAll
                TRUE,
            ),
            array(
                // Info
                array("GatewayKey" => 1, "DeviceKey" => 1, "socketType" => "test"),
                // pkt
                array(
                    array(
                        "Command" => "5C",
                        "To" => "000ABC",
                    ),
                    array(
                        "Command" => "5C",
                        "To" => "000DEF",
                    ),
                ),
                // pktStr
                "5A5A5A5C000ABC00002000CA",
                // replyStr
                "5A5A5A01000020ABCDEF0401020304A8",
                // expect
                FALSE,
            ),
            array(
                // Info
                array("GatewayKey" => 1, "DeviceKey" => 1, "socketType" => "test"),
                // pkt
                array(
                    array(
                        "Command" => "5C",
                        "To" => "000ABC",
                    ),
                ),
                // pktStr
                "5A5A5A5C000ABC00002000CA",
                // replyStr
                "019823561284756129487561",
                // expect
                FALSE,
            ),
            array(
                // Info
                array("DeviceKey" => 1, "socketType" => "test"),
                // pkt
                array(
                    array(
                        "Command" => "5C",
                        "To" => "ABCDEF",
                    ),
                    array(
                        "Command" => "55",
                        "To" => "ABC",
                        "Data" => "01020304",
                    ),
                ),
                // pktStr
                array("5A5A5A55000ABC0000200401020304C3", "5A5A5A5CABCDEF00002000F5"),
                // replyStr
                array("5A5A5A01000020000ABC040102030497", "5A5A5A01000020ABCDEF0401020304A8"),
                // expect
                array(
                    array(
                        "pktTimeout" => 1,
                        "GetReply" => TRUE,
                        "SentFrom" => "000020",
                        "SentTo" => "ABCDEF",
                        "sendCommand" => "5C",
                        "group" => FALSE,
                        "packet" => array(
                            "command" => "5C",
                            "to" => "ABCDEF",
                            "data" => "",
                        ),
                        "PacketTo" => "ABCDEF",
                        "GatewayKey" => 1,
                        "DeviceKey" => 1,
                        "Type" => "OUTGOING",
                        "RawData" => "01020304",
                        "sentRawData" => "",
                        "Parts" => 1,
                        "Command" => "01",
                        "To" => "000020",
                        "From" => "ABCDEF",
                        "Length" => 4,
                        "Data" => array(1,2,3,4),
                        "Checksum" => "A8",
                        "CalcChecksum" => "A8",
                        "RawPacket" => "01000020ABCDEF0401020304A8",
                        "Socket" => 1,
                        "Reply" => TRUE,
                        "toMe" => TRUE,
                        "isGateway" => FALSE,
                    ),
                    array(
                        "pktTimeout" => 1,
                        "GetReply" => TRUE,
                        "SentFrom" => "000020",
                        "SentTo" => "000ABC",
                        "sendCommand" => "55",
                        "group" => FALSE,
                        "packet" => array(
                            "command" => "55",
                            "to" => "000ABC",
                            "data" => "01020304",
                        ),
                        "PacketTo" => "000ABC",
                        "GatewayKey" => 1,
                        "DeviceKey" => 1,
                        "Type" => "OUTGOING",
                        "RawData" => "01020304",
                        "sentRawData" => "01020304",
                        "Parts" => 1,
                        "Command" => "01",
                        "To" => "000020",
                        "From" => "000ABC",
                        "Length" => 4,
                        "Data" => array(1,2,3,4),
                        "Checksum" => "97",
                        "CalcChecksum" => "97",
                        "RawPacket" => "01000020000ABC040102030497",
                        "Socket" => 1,
                        "Reply" => TRUE,
                        "toMe" => TRUE,
                        "isGateway" => FALSE
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider dataSendPacket().
     */
    public function testSendPacket($Info, $pkt, $pktStr, $replyStr, $expect, $getAll = FALSE) {
        // This preloads our fake socket to send back the data we want
        if (is_array($pktStr)) {
            foreach($pktStr as $k => $p) {
                $this->o->socket[1]->setReply($p, $replyStr[$k]);
            }
        } else {
            $this->o->socket[1]->setReply($pktStr, $replyStr);
        }
        if ($getAll) $this->o->getAll($getAll);
        $rep = $this->o->SendPacket($Info, $pkt, TRUE, NULL);
        self::packetRemoveDates($rep);
        $this->assertSame($expect, $rep, "Return is not the same as expected");
    }

    private function packetRemoveDates(&$rep) {
        if (is_array($rep)) {
            foreach($rep as $key => $val) {
                unset($rep[$key]["Time"]);
                unset($rep[$key]["ReplyTime"]);
                unset($rep[$key]["SentTime"]);
                unset($rep[$key]["Date"]);
            }
        }
    
    }

    /**
     * @todo Implement testSendPacket().
     */
    public function testSendPacketWriteSocket() {
        $Info = array("GatewayKey" => 2, "socketType" => "test");
        $this->o->socket[2]->expects($this->exactly(2))
                     ->method('Write')
                     ->with($this->equalTo(devInfo::deHexify($this->testPacketStr[0])));
        $rep = $this->o->SendPacket($Info, array($this->testPacketArray[0]), FALSE, NULL);
    }

    /**
     * @todo Implement testSendPacket().
     */
    public function testSendPacketWriteRetry() {
        $Info = array("GatewayKey" => 2, "socketType" => "test");
        $this->o->Retries = 4;
        $this->o->socket[2]->expects($this->any())
                     ->method('Write')
                     ->with($this->equalTo(devInfo::deHexify($this->testPacketStr[0])));
        $rep = $this->o->SendPacket($Info, array($this->testPacketArray[0]), FALSE, NULL);
    }

    /**
     * @todo Implement testSendReply().
     */
    public function testSendReply() {
        $Info = array("GatewayKey" => 2, "socketType" => "test");
        $this->o->socket[2]->expects($this->exactly($this->o->Retries))
                     ->method('Write')
                     ->with($this->equalTo(devInfo::deHexify("5A5A5A01000ABC000020040102030497")));
        $rep = $this->o->SendReply($Info, "000ABC", "01020304");
    }


    public static function dataArrayToData() {
        return array(
            array("0102030405", "0102030405"),
            array(array(1,2,3,4,5), "0102030405"),
            array(array(), ""),
            array(NULL, ""),
            array(45, ""),
        );
    }
    /**
     * @dataProvider dataArrayToData
     */
    public function testArrayToDataValid($data, $expect) {
        $s = $this->o->arrayToData($data);
        $this->assertEquals($s, $expect);
    }
    /**
     * Test calling it directly
     * @dataProvider dataArrayToData
     */
    public function testArrayToDataDirect($data, $expect) {
        $s = EPacket::arrayToData($data);
        $this->assertEquals($s, $expect);
    }

    public static function dataChangeSN() {
        return array(
            array("000004", array("000001", "000002", "000003", "000004", "000005", "000006", "000007", "000008", "000009"), "000004"),
            array("", array("000001", "000002", "000003", "000004"), "000020"),
        );
    }

    /**
     * @dataProvider dataChangeSN($SN)
     */
    public function testChangeSN($SN, $SNArray, $expect) {
        $this->o->SNArray = $SNArray;
        $Info = array("GatewayKey" => 1);
        // Load these packets up so that it always chooses $SN.
        foreach($SNArray as $s) {
            if ($s != $SN) {
                $pkt = EPacket::PacketBuild(array("Command"=>"03", "To"=>$s), "000020");
                $reply = EPacket::PacketBuild(array("Command"=>"01", "To"=>"000020"), $s);
                $this->o->socket[1]->setReply($pkt, $reply);
            }
        }
        $this->o->ChangeSN($Info);
        $this->assertSame($expect, $this->o->SN);
    }

    /**
     * @todo Implement testPacketTime().
     */
    public function testPacketTime() {
        $t = EPacket::PacketTime();
        $this->assertTrue(is_float($t), "Time not a floating point number");
    }


    /**
     * @todo Implement testIsGateway().
     */
    public function testIsGateway() {
        $o = new EPacket();
        $this->assertTrue($o->isGateway("000010"), "Doesn't take strings well inside range");
        $this->assertTrue($o->isGateway(10), "Doesn't take integers well inside range");
        $this->assertFalse($o->isGateway("FFFFFF"), "Doesn't take strings well above range");
        $this->assertFalse($o->isGateway(0xFFFFFF), "Doesn't take integers well above range");
        $this->assertFalse($o->isGateway(-1), "Doesn't take integers well below range");
    }

    /**
     * @todo Implement testSNCheck().
     */
    public function testGetAll1() {
        $this->txrxMock->getAll(FALSE);
        $this->assertFalse($this->txrxMock->_getAll);
    }
    public function testGetAll2() {
        $this->txrxMock->getAll(TRUE);
        $this->assertTrue($this->txrxMock->_getAll);
    }
    public function testGetAll3() {
        $this->txrxMock->getAll(0);
        $this->assertFalse($this->txrxMock->_getAll);
    }
    public function testGetAll4() {
        $this->txrxMock->getAll(1);
        $this->assertTrue($this->txrxMock->_getAll);
    }


    /**
     * @todo Implement testUnbuildPacket().
     */
    public function testUnbuildPacket() {
        $check = array("Command", "To", "From", "RawData", "Checksum", "Length");
        $o = new EPacket();
        foreach($this->testPacketStr as $key => $str) {
            $pkt = $o->unbuildPacket($str);
            $this->validPacketArray($pkt, $this->testPacketArray[$key], $check);
        }
    }


    public static function dataMonitor() {
        return array(
            array(
                array("GatewayKey" => 1, "socketType" => "test"), 
                NULL, 
                array(
                    "socket" => 1,
                    "timeout" => 0,
                ),
            ),
            array(
                array("GatewayKey" => 3, "socketType" => "test"), 
                2, 
                array(
                    "socket" => 3,
                    "timeout" => 2,
                ),
            ),
        );    
    }

    /**
     * @dataProvider dataMonitor
     */
    public function testMonitor($Info, $timeout, $expect) {
        if (is_null($timeout)) {
            $ret = $this->txrxMock->monitor($Info);
        } else {
            $ret = $this->txrxMock->monitor($Info, $timeout);
        }
        $this->assertSame($expect, $ret);
    }

    public static function dataPing() {
        return array(
            array(
                array("DeviceID" => "ABCDEF", "DeviceKey" => 1), 
                FALSE, 
                array(
                    "Info" => array("DeviceID" => "ABCDEF", "DeviceKey" => 1), 
                    "PacketList" => array("Command" => "02", "To" => "ABCDEF"),
                    "GetReply" => TRUE,
                    "pktTimeout" => NULL,
               ),
            ),
            array(
                array("DeviceID" => "ABCDEF", "DeviceKey" => 1), 
                NULL, 
                array(
                    "Info" => array("DeviceID" => "ABCDEF", "DeviceKey" => 1), 
                    "PacketList" => array("Command" => "02", "To" => "ABCDEF"),
                    "GetReply" => TRUE,
                    "pktTimeout" => NULL,
                ),
            ),
            array(
                array("DeviceID" => "ABCDEF", "DeviceKey" => 1), 
                TRUE, 
                array(
                    "Info" => array("DeviceID" => "ABCDEF", "DeviceKey" => 1), 
                    "PacketList" => array("Command" => "03", "To" => "ABCDEF"),
                    "GetReply" => TRUE,
                    "pktTimeout" => NULL,
                ),
            ),
        );
    }

    /**
     * @dataProvider dataPing
     */
    public function testPing($Info, $find, $expect) {
        if (is_null($find)) {
            $ret = $this->txrxMock->ping($Info);
        } else {
            $ret = $this->txrxMock->ping($Info, $find);
        }
        $this->assertSame($expect, $ret);
    }


    /**
     * @todo Implement testClose().
     */
    public function testClose() {
        $o = new EPacket();
        $o->socket[1] = $this->getMock('epsocketMock');
        $o->socket[1]->expects($this->once())
                     ->method('Close');
        $Info = array("GatewayKey" => 1);
        $rep = $o->Close($Info);
    }

    /**
     * @todo Implement testSNCheck().
     */
    public function testSNCheck1() {
        $this->txrxMock->SNCheck(FALSE);
        $this->assertFalse($this->txrxMock->_DeviceIDCheck);
    }
    public function testSNCheck2() {
        $this->txrxMock->SNCheck(TRUE);
        $this->assertTrue($this->txrxMock->_DeviceIDCheck);
    }
    public function testSNCheck3() {
        $this->txrxMock->SNCheck(0);
        $this->assertFalse($this->txrxMock->_DeviceIDCheck);
    }
    public function testSNCheck4() {
        $this->txrxMock->SNCheck(1);
        $this->assertTrue($this->txrxMock->_DeviceIDCheck);
    }
}

// Call EPacketTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "EPacketTest::main") {
    EPacketTest::main();
}

/**
 * This class is for testing callback
 */
class EPacketTest_CallBack_Class {
/**
 * This function is for testing callback
 */
    public function Test($pkt) {
        $this->TestVar = $pkt;
    }
}

/**
 * This class overrides epsocket so that we can test EPacket without
 * actually using a socket connection.
 */
class EPacketTXRXMock extends EPacket {
    /** Check to see if we are a unique serial number on this net */                                                        
    public $_DeviceIDCheck = TRUE;
    public $_getAll = FALSE;
    function __construct() {
    
    }
    
    public function SendPacket(&$Info, $PacketList, $GetReply=TRUE, $pktTimeout = NULL) {
        return array(
            "Info" => $Info,
            "PacketList" => $PacketList,
            "GetReply" => $GetReply,
            "pktTimeout" => $pktTimeout,
        );
    }
    public function RecvPacket($socket, $timeout = 0) {
        return array(
            "socket" => $socket,
            "timeout" => $timeout,
        );
    }
}
/**
 * This class overrides epsocket so that we can test EPacket without
 * actually using a socket connection.
 */
class epsocketMock extends epsocket {

    var $socket = FALSE;
    var $index = 0;

    public function setReply($data, $reply) {
        $this->reply[trim($data)] = trim($reply);
    }    
    /**
     *
     */
    public function Connect($server = "", $port = 0, $timeout=0) {
        $return = FALSE;
        if ($return === FALSE) {
            if (!empty($server)) $this->Server = $server;
            if (!empty($port)) $this->Port = $port;

            if (!empty($this->Server) && !empty($this->Port)) {
                $this->socket = TRUE;
                $return = TRUE;
            } else {
                $this->Errno = -1;
                $this->Error = "No server specified";
                $return = FALSE;
            }
        }        
        return($return);

    }
    /**
     *
     */
    function CheckConnect() {
        return TRUE;
    }

    /**
     *
     */
    function Close() {
        $this->socket = FALSE;
    }

    /**
     *
     */
    function readChar($timeout=-1) {
        if ($timeout < 0) $timeout = $this->PacketTimeout;
        
        $char = FALSE;
        if (($this->index == 0) && (!empty($this->lastPacket))) {
            if (is_string($this->reply[$this->lastPacket])) {
                $char = chr(hexdec(substr($this->reply[$this->lastPacket], $this->index, 2)));
                $this->index+=2;
            }
        } else {
            if ($this->index < strlen($this->reply[$this->lastPacket])) { 
                $char = chr(hexdec(substr($this->reply[$this->lastPacket], $this->index, 2)));
                $this->index+=2;
            } else {
                $this->index = 0;
                $this->lastPacket = "";
            }
        }
        return $char;
    }
    /**
     *
     */
    function Write($data) {
        $this->lastPacket = devInfo::hexifyStr($data);

        if (!isset($this->reply[$this->lastPacket])) {
//            print "\nGot: ".$this->lastPacket."\n";
        }
        $this->index = 0;
        return TRUE;
    }

    function __construct($server="", $port="") {
        if (empty($server)) $server = "127.0.0.1";
        if (empty($port)) $port = "2000";
        $this->Connect($server, $port);
    }
}

/**
 * This function is for testing callback
 */
function EPacketTest_CallBack_Function($pkt) {
    $_SESSION['EPacketTest_CallBack_Function'] = $pkt;
}

?>
