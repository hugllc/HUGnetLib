<?php
/**
 * Tests the EPacket class
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
 * @category   PacketStructure
 * @package    HUGnetLibTest
 * @subpackage PacketStructure
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** The test case class */
require_once "PHPUnit/Framework/TestCase.php";
/** The test suite class */
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../EPacket.php';

/**
 * Test class for EPacket.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:45:46.
 *
 * @category   PacketStructure
 * @package    HUGnetLibTest
 * @subpackage PacketStructure
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class EPacketTest extends PHPUnit_Framework_TestCase
{
    /** default devInfo array */
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
     * Test Packets in string form 
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
     * @return null
     *
     * @access public
     * @static
     */
    public static function main() 
    {
        include_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("EPacketTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

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
        $db      = $this->getMock("ADONewConnection", array(), array("sqlite"));
        $this->o = new EPacket(array("GatewayKey" => 1, "socketType" => "test"), false, $db, false);


        // This is a fast system.  It doesn't need a long timeout
        $this->o->socket[2] = $this->getMock('epsocketMock', array("connect", "close", "readChar", "write"), array("socketType"=>"test"));

        $this->o->ReplyTimeout = 1; 
        
        $this->txrxMock = new EPacketTXRXMock();
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
        unset($this->o);
    }

    /**
     * deHexifies a string
     *
     * @param string $string The string to dehexify
     *
     * @return array
     */
    private function _deHexifyArray($string) 
    {
        $index = 0;
        $array = array();
        while (($index * 2) < strlen($string)) {
            $array[$index] = chr(hexdec(substr($string, $index * 2, 2)));
        }
        return $array;
    }

    /**
     * Cleans the 'to' field to make it presentable.  Will also work on 'from'
     * 
     * @param string $to The 'to' string
     *
     * @return string
     */
    private function _cleanTo($to) 
    {
        $to = trim($to);
        $to = substr($to, 0, 6);
        $to = str_pad($to, 6, "0", STR_PAD_LEFT);
        $to = strtoupper($to);
    
        return $to;
    }
    /**
     * Checks to see if the packet array is valid
     *
     * @param array $a    The array to test
     * @param array $test array of fields and values to check
     * @param array $keys array of keys to test
     *
     * @return null
     */
    public function validPacketArray($a, $test=null, $keys = null) 
    {
        $this->assertTrue(is_array($a), "This is not an array!");
        $this->assertTrue(is_string($a['Command']), "'".$a['Command']."': Packet command must be a string");
        $this->assertEquals(2, strlen($a['Command']), "'".$a['Command']."': Packet command must contain exactly 2 characters");
        $this->assertTrue(is_string($a['To']), "'".$a['To']."': Packet to must be a string");
        $this->assertEquals(6, strlen($a['To']), "'".$a['To']."': Packet to must contain exactly 6 characters");
        if (is_array($test)) {
            foreach ($test as $key => $var) {
                // Check to see if the key was specified
                if (is_array($keys)) {
                    // If it was not continue.
                    if (array_search($key, $keys) === false) continue;
                    // Drop through if it was specified, or if nothing was specified.
                }
                if (trim(strtolower($key)) == "to") {
                    $var = self::_cleanTo($var);
                }
                $this->assertEquals($var, $a[$key], "'$key' field is not the same");
            }
        }
    }
    /**
     * Checks to see if a packet string is valid
     *
     * @param string $s The packet string to check
     * @param array  $a Array of values to check
     *
     * @return null
     */
    public function validPacketString($s, $a=null) 
    {
        $this->assertTrue(is_string($s), "This is not a string!");
        $s              = strtoupper($s);
        $preambleLength = EPacketTest::getPreambleLength($s);
        $this->assertTrue((($preambleLength >= 2) && ($preambleLength <= 3)), "Preamble must be 2 or 3 characters");
        $length      = hexdec(substr($s, ($preambleLength + 7) * 2, 2));
        $totalLength = $preambleLength + 8 + $length + 1;
        $this->assertEquals(($totalLength * 2), strlen($s), count($a['command']), "Packet command must contain exactly 2 characters");
        if (is_array($a)) {
            $a   = array_change_key_case($a, CASE_LOWER);
            $to  = $this->_cleanTo($a["to"]);
            $pTo = substr($s, ($preambleLength+1) * 2, 6);
            $this->assertEquals($to, $pTo, "To field is Wrong.  '$to' != '$pTo'");
            $this->assertEquals($length * 2, strlen(trim($a['data'])), "Wrong length parameter.");
        }
    }
    /**
     * returns the preamble length
     *
     * @param string $pkt The packet string to check
     *
     * @return int
     */
    public function getPreambleLength($pkt) 
    {
        $length = 0;
        while (substr($pkt, ($length*2), 2) == "5A") $length++;    
        return $length;
    }





    /**
     * Test packetCallBackMethod()
     *
     * @return null
     */
    public function testPacketCallBackMethod() 
    {
        $t      = $this->getMock('EPacketTest_CallBack_Class');
        $string = "ABCDE";
        $t->expects($this->once())
          ->method('test')
          ->with($this->equalTo($string));
        $this->o->PacketSetCallBack("test", $t);
        $this->o->PacketCallBack($string);
    }
    /**
     * Test packetCallBackFunction()
     *
     * @return null
     */
    public function testPacketCallBackFunction() 
    {
        $string = "ABCDE";

        $this->o->PacketSetCallBack("EPacketTest_CallBack_function");
        $this->o->PacketCallBack($string);
        $this->assertEquals($_SESSION['EPacketTest_CallBack_Function'], $string);
    }

    /**
     * data provider for testSendPacket()
     *
     * @return array
     */    
    public static function datasendPacket() 
    {
        return array(
            array(
                // Info
                array("socketType" => "test", "GatewayKey" => 1),
                // pkt
                array(
                    array(
                        "Command" => "55",
                        "To" => "000FGH",
                        "From" => "000020",
                        "Data" => "01020304",
                    ),
                    array(
                        "Command" => "55",
                        "To" => "000ABC",
                        "From" => "000020",
                        "Data" => "01020304",
                    ),
                ),
                // GetReply
                true,
                // pktStr
                array(array("Command" => "55", "To" => "000ABC", "From" => "000020", "Data" => "01020304")),

                // replyStr
                array(
                    array(
                        "Command"      => "01",
                        "To"           => "000020",
                        "From"         => "000ABC",
                        "Length"       => 4,
                        "RawData"      => "01020304",
                        "Data"         => array(1,2,3,4),
                        "Checksum"     => "97",
                        "CalcChecksum" => "97",
                        "RawPacket"    => "01000020000ABC040102030497",
                    ),
                ),
                // expect
                array(
                    array(
                        "pktTimeout" => 1,
                        "GetReply" => true,
                        "SentFrom" => "000020",
                        "SentTo" => "000ABC",
                        "sendCommand" => "55",
                        "group" => false,
                        "packet" => array(
                            "command" => "55",
                            "to" => "000ABC",
                            "from" => "000020",
                            "data" => "01020304",
                        ),
                        "PacketTo" => "000ABC",
                        "GatewayKey" => 1,
                        "DeviceKey" => null,
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
                        "Reply" => true,
                        "toMe" => true,
                        "isGateway" => false
                    ),
                ),
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
                // GetReply
                true,
                // pktStr
                "5A5A5A5C000ABC00002000CA",
                // replyStr
                "5A5A5A01000020ABCDEF0401020304A8",
                // expect
                false,
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
                // GetReply
                true,
                // pktStr
                "5A5A5A5C000ABC00002000CA",
                // replyStr
                "019823561284756129487561",
                // expect
                false,
            ),
            array(
                // Info
                array("socketType" => "test"),
                // pkt
                array(
                    array(
                        "Command" => "01",
                        "To" => "000123",
                        "Data" => "01020304",
                    ),
                ),
                // GetReply
                false,
                // pktStr
                "",
                // replyStr
                "",
                // expect
                true,
           ),
        );
    }

    /**
     * Tests sendPacket
     *
     * @param array $Info     devInfo array of the device
     * @param array $pkt      The packet to send
     * @param array $pktStr   array of packet strings
     * @param array $replyStr array of packet replies
     * @param mixed $expect   what to expect
     * @param bool  $getAll   Whether to get all of the packets returned, or only the reply
     *
     * @return null
     *
     * @dataProvider datasendPacket().
     */
    public function testsendPacket($Info, $pkt, $GetReply, $pktStr, $replyStr, $expect, $getAll = false) 
    {
        // This preloads our fake socket to send back the data we want
                if (is_array($pktStr)) {
            foreach ($pktStr as $k => $p) {
                $this->o->socket[1]->setReply($p, $replyStr[$k]);
            }
        } else {
            $this->o->socket[1]->setReply($pktStr, $replyStr);
        }
        if ($getAll) $this->o->getAll($getAll);
        $rep = $this->o->sendPacket($Info, $pkt, $GetReply, null);
        self::_packetRemoveDates($rep);
        $this->assertSame($expect, $rep);
    }

    /**
     * Removes the dates from the array so it can be checked
     *
     * @param array &$rep The array to remove the dates from
     *
     * @return null
     */
    private function _packetRemoveDates(&$rep) 
    {
        if (is_array($rep)) {
            foreach ($rep as $key => $val) {
                unset($rep[$key]["Time"]);
                unset($rep[$key]["ReplyTime"]);
                unset($rep[$key]["SentTime"]);
                unset($rep[$key]["Date"]);
            }
        }
    
    }



    /**
     * data provider for testArrayToData()
     *
     * @return array
     */    
    public static function dataArrayToData() 
    {
        return array(
            array("0102030405", "0102030405"),
            array(array(1,2,3,4,5), "0102030405"),
            array(array(), ""),
            array(null, ""),
            array(45, ""),
        );
    }
    /**
     * Tests arrayToData()
     *
     * @param mixed  $data   The key to try
     * @param string $expect The return to expect
     *
     * @return null
     *
     * @dataProvider dataArrayToData
     */
    public function testArrayToDataValid($data, $expect) 
    {
        $s = $this->o->arrayToData($data);
        $this->assertEquals($s, $expect);
    }
    /**
     * Test calling it directly
     *
     * @param mixed  $data   The key to try
     * @param string $expect The return to expect
     *
     * @return null
     *
     * @dataProvider dataArrayToData
     */
    public function testArrayToDataDirect($data, $expect) 
    {
        $s = EPacket::arrayToData($data);
        $this->assertEquals($s, $expect);
    }

    /**
     * data provider for testChangeSN()
     *
     * @return array
     */    
    public static function dataChangeSN() 
    {
        return array(
//            array("000004", array("000001", "000002", "000003", "000004", "000005", "000006", "000007", "000008", "000009"), "000004"),
//            array("", array("000001", "000002", "000003", "000004"), "000020"),
        );
    }

    /**
     * Test ChangeSN
     *
     * @param string $SN      The SN to test for
     * @param array  $SNArray The serial number array
     * @param mixed  $expect  The return to expect
     *
     * @return null
     *
     * @dataProvider dataChangeSN($SN)
     */
    public function testChangeSN($SN, $SNArray, $expect) 
    {
        $this->o->SNArray = $SNArray;
        $Info             = array("GatewayKey" => 1);
        // Load these packets up so that it always chooses $SN.
        foreach ($SNArray as $s) {
            if ($s != $SN) {
                $pkt   = EPacket::PacketBuild(array("Command"=>"03", "To"=>$s), "000020");
                $reply = EPacket::PacketBuild(array("Command"=>"01", "To"=>"000020"), $s);
                $this->o->socket[1]->setReply($pkt, $reply);
            }
        }
        $this->o->ChangeSN($Info);
        $this->assertSame($expect, $this->o->SN);
    }

    /**
     * Test packetTime()
     *
     * @return null
     */
    public function testPacketTime() 
    {
        $t = EPacket::PacketTime();
        $this->assertTrue(is_float($t), "Time not a floating point number");
    }

    /**
     * data provider for testMonitor
     *
     * @return array
     */    
    public static function dataIsGateway() 
    {
        return array(
            array("000010", true),
            array(10, true),
            array("FFFFFF", false),
            array(0xFFFFFF, false),
            array(-1, false),
        );
    }
    /**
     * Test isGateway
     *
     * @param mixed $key    The key to try
     * @param bool  $expect The return to expect
     *
     * @return null
     *
     * @dataProvider dataIsGateway()
     */
    public function testIsGateway($key, $expect) 
    {
        $this->assertSame($expect, $this->o->isGateway($key));
    }

    /**
     * Test getAll
     *
     * @return null
     */
    public function testGetAll1() 
    {
        $this->o->getAll(false);
        $this->assertFalse($this->readAttribute($this->o, "_getAll"));
    }
    /**
     * Test getAll
     *
     * @return null
     */
    public function testGetAll2() 
    {
        $this->o->getAll(true);
        $this->assertTrue($this->readAttribute($this->o, "_getAll"));
    }
    /**
     * Test getAll
     *
     * @return null
     */
    public function testGetAll3() 
    {
        $this->o->getAll(0);
        $this->assertFalse($this->readAttribute($this->o, "_getAll"));
    }
    /**
     * Test getAll
     *
     * @return null
     */
    public function testGetAll4() 
    {
        $this->o->getAll(1);
        $this->assertTrue($this->readAttribute($this->o, "_getAll"));
    }



    /**
     * data provider for testMonitor
     *
     * @return array
     */    
    public static function dataMonitor() 
    {
        return array(
            array(
                array("GatewayKey" => 1, "socketType" => "test"), 
                null, 
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
     * data provider for test testBuildPacket()
     *
     * @return array
     */    
    public static function dataBuildPacket() 
    {
        return array(
            array("123", "AB", "12345678", array("To"=>"123", "From" => '000020', "Command"=>"AB", "Data"=>"12345678")),
            array("3456", "CD", null, array("To"=>"3456", "From" => '000020', "Command"=>"CD", "Data"=>"")),
        );    
    }

    /**
     * Test buildPacket()
     *
     * @param string $command The command to use
     * @param string $to      Who to send the packet to
     * @param string $data    The data to send
     * @param array  $expect  What to expect in return
     *
     * @return null
     *
     * @dataProvider dataBuildPacket
     */
    public function testBuildPacket($command, $to, $data, $expect) 
    {
        if (is_null($data)) {
            $pkt = $this->o->buildPacket($command, $to);
        } else {
            $pkt = $this->o->buildPacket($command, $to, $data);
        }
        $this->assertSame($expect, $pkt);
    }
    /**
     * Test Ping
     *
     * @param array $Info    The devInfo array of the device
     * @param int   $timeout The timeout value to use
     * @param array $expect  The return to expect
     *
     * @return null
     *
     * @dataProvider dataMonitor
     */
    public function testMonitor($Info, $timeout, $expect) 
    {
        if (is_null($timeout)) {
            $ret = $this->txrxMock->monitor($Info);
        } else {
            $ret = $this->txrxMock->monitor($Info, $timeout);
        }
        $this->assertSame($expect, $ret);
    }

    /**
     * data provider for testPing()
     *
     * @return array
     */    
    public static function dataPing() 
    {
        return array(
            array(
                array("DeviceID" => "ABCDEF", "DeviceKey" => 1), 
                false, 
                array(
                    "Info" => array("DeviceID" => "ABCDEF", "DeviceKey" => 1), 
                    "PacketList" => array("Command" => "02", "To" => "ABCDEF"),
                    "GetReply" => true,
                    "pktTimeout" => null,
              ),
           ),
            array(
                array("DeviceID" => "ABCDEF", "DeviceKey" => 1), 
                null, 
                array(
                    "Info" => array("DeviceID" => "ABCDEF", "DeviceKey" => 1), 
                    "PacketList" => array("Command" => "02", "To" => "ABCDEF"),
                    "GetReply" => true,
                    "pktTimeout" => null,
               ),
           ),
            array(
                array("DeviceID" => "ABCDEF", "DeviceKey" => 1), 
                true, 
                array(
                    "Info" => array("DeviceID" => "ABCDEF", "DeviceKey" => 1), 
                    "PacketList" => array("Command" => "03", "To" => "ABCDEF"),
                    "GetReply" => true,
                    "pktTimeout" => null,
               ),
           ),
        );
    }

    /**
     * Test Ping
     *
     * @param array $Info   The devInfo array of the device
     * @param bool  $find   Whether to use a 'find' ping or not
     * @param array $expect The return to expect
     *
     * @return null
     *
     * @dataProvider dataPing
     */
    public function testPing($Info, $find, $expect) 
    {
        if (is_null($find)) {
            $ret = $this->txrxMock->ping($Info);
        } else {
            $ret = $this->txrxMock->ping($Info, $find);
        }
        $this->assertSame($expect, $ret);
    }


    /**
     * Test Close
     *
     * @return null
     */
    public function testClose() 
    {
        $this->o->socket[2] = $this->getMock('epsocketMock');
        $this->o->socket[2]->expects($this->once())
                     ->method('close');
        $Info = array("GatewayKey" => 2);
        $rep  = $this->o->Close($Info);
    }

    /**
     * Test SNCheck
     *
     * @return null
     */
    public function testSNCheck1() 
    {
        $this->o->SNCheck(false);
        $this->assertFalse($this->readAttribute($this->o, "_DeviceIDCheck"));
    }
    /**
     * Test SNCheck
     *
     * @return null
     */
    public function testSNCheck2() 
    {
        $this->o->SNCheck(true);
        $this->assertTrue($this->readAttribute($this->o, "_DeviceIDCheck"));
    }
    /**
     * Test SNCheck
     *
     * @return null
     */
    public function testSNCheck3() 
    {
        $this->o->SNCheck(0);
        $this->assertFalse($this->readAttribute($this->o, "_DeviceIDCheck"));
    }
    /**
     * Test SNCheck
     *
     * @return null
     */
    public function testSNCheck4() 
    {
        $this->o->SNCheck(1);
        $this->assertTrue($this->readAttribute($this->o, "_DeviceIDCheck"));
    }
}

// Call EPacketTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "EPacketTest::main") {
    EPacketTest::main();
}

/**
 * This class is for testing callback
 *
 * @category   PacketStructure
 * @package    HUGnetLibTest
 * @subpackage PacketStructure
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class EPacketTest_CallBack_Class
{
    /**
     * This function is for testing callback
     *
     * @param array $pkt The packet
     *
     * @return null
     */
    public function test($pkt)
    {
        $this->TestVar = $pkt;
    }
}

/**
 * This class overrides epsocket so that we can test EPacket without
 * actually using a socket connection.
 *
 * @category   PacketStructure
 * @package    HUGnetLibTest
 * @subpackage PacketStructure
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class EPacketTXRXMock extends EPacket
{
    /**
     * constructor
     */
    function __construct() 
    {
    
    }
    /**
     * Some Function
     *
     * @param array &$Info      The array with the device information in it
     * @param array $PacketList Array with packet information in it.
     * @param bool  $GetReply   Whether or not to wait for a reply.
     * @param int   $pktTimeout The timeout value to use
     *
     * @return null
     */
    public function sendPacket(&$Info, $PacketList, $GetReply=true, $pktTimeout = null)
    {
        return array(
            "Info" => $Info,
            "PacketList" => $PacketList,
            "GetReply" => $GetReply,
            "pktTimeout" => $pktTimeout,
        );
    }
    /**
     * Some Function
     *
     * @param int $socket  The socket to send it out of.  0 is the default.
     * @param int $timeout Timeout for waiting.  Default is used if timeout == 0    
     *
     * @return null
     */
    public function recvPacket($socket, $timeout = 0)
    {
        return array(
            "socket" => $socket,
            "timeout" => $timeout,
        );
    }
}
/**
 * This class overrides epsocket so that we can test EPacket without
 * actually using a socket connection.
 *
 * @category   PacketStructure
 * @package    HUGnetLibTest
 * @subpackage PacketStructure
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class EpSocketMock extends EpSocket
{
    /** Socket to use */
    var $socket = false;
    /** Current socket index */
    var $index = 0;

    /**
     * Sets replies for packets received
     *
     * @param string $data  The data we will receive
     * @param string $reply The data to return
     *
     * @return null
     */
    public function setReply($data, $reply) {
        if (is_array($data)) $data = array_change_key_case($data, CASE_LOWER);
        $this->reply[serialize($data)] = $reply;
    }    
        
    /**
     * Connects to the server
     * 
     *
     * @param string $server  Name or IP address of the server to connect to
     * @param int    $port    The TCP port on the server to connect to
     * @param int    $timeout The time to wait before giving up on a bad connection
     *
     * @return bool true if the connection is good, false otherwise
     *
     * @see epsocket::Connect()
     */
    public function connect($config=array()) 
    {
        if (!empty($config["GatewayIP"])) $this->Server = $config["GatewayIP"];
        if (!empty($config["GatewayPort"])) $this->Port = $config["GatewayPort"];

        if (!empty($this->Server) && !empty($this->Port)) {
            $this->socket = true;
            return true;
        } else {
            $this->Errno = -1;
            $this->Error = "No server specified";
            return false;
        }

    }
    /**
     * Checks to make sure that all we are connected to the server
     * 
     * This routine only checks the connection.  It does nothing else.  If you want to
     * have the script automatically connect if it is not connected already then use
     * epsocket::Connect().
     *
     * @return bool true if the connection is good, false otherwise
     */
    function checkConnect() 
    {
        return true;
    }

    /**
     * Closes the socket connection
     * 
     * @return null
     */
    function close() 
    {
        $this->socket = false;
    }

    /**
     * Sends out a packet
     *
     * @param array $packet the packet to send out
     *
     * @return bool false on failure, true on success
     */
    function sendPacket($packet) 
    {
        $this->lastPacket = serialize($packet["packet"]);
        return true;

    }
    /**
     * Receives a packet from the socket interface
     *
     * @param int $timeout Timeout for waiting.  Default is used if timeout == 0    
     *
     * @return bool false on failure, the Packet array on success
     */
    function RecvPacket($timeout=0) 
    {
        if (isset($this->reply[$this->lastPacket])) return $this->reply[$this->lastPacket];
        return false;
    }

    /**
     * Constructor
     * 
     * @param string $server  The name or IP of the server to connect to
     * @param int    $port The TCP port to connect to on the server. Set to 0 for
     *     the default port.
     *
     * @return null
     */
    function __construct($config=array()) 
    {
        $config["verbose"] = false;
        $this->Connect();
    }
}

/**
 * This function is for testing callback
 *
 * @param array $pkt The packet array
 *
 * @return null
 *
 */
function EPacketTest_CallBack_function($pkt) 
{
    $_SESSION['EPacketTest_CallBack_Function'] = $pkt;
}

?>
