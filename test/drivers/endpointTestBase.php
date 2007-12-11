<?php
/**
 * This is the basis for all endpoint driver test classes.
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
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @version SVN: $Id$
 *
 */

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../driverTest.php';
require_once dirname(__FILE__).'/../EPacketTest.php';
require_once dirname(__FILE__).'/../unitConversionTest.php';

/**
 * Test class for endpoints.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:06:08.
 */
abstract class endpointTestBase extends PHPUnit_Framework_TestCase {

    static $socket = 1;

    var $goodEndpoints = array(
        array(
            "DeviceID" => "123456",
            "DeviceName" => "Test 1",
        ),
    );
    
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
      */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("endpointTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
      */
    protected function setUp() {
        $this->o = driverTest::createDriver();
        if (is_object($this->o->drivers[$this->class])) {
            $this->o->packet->socket[self::$socket] = new epsocketMock;
            $this->o->packet->ReplyTimeout=1;  // The reply timeout can be short becuase we should get an instant reply.
        }
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
      */
    protected function tearDown() {
        unset($this->o);
    }

    protected function setUpPacket($preload) {
        if (is_array($preload)) {
            foreach ($preload as $data => $reply) {
                $this->o->packet->socket[self::$socket]->setReply($data, $reply);
            }
        }
    }
    /**
     *
      */
    function testDriver() {
        $this->assertType("object", $this->o->drivers[$this->class], "This class '".$this->class."' did not register as a plugin");
    }

    /**
     *  A subclass should call this routine in the dataDevicesArray function
     * parent::devicesCheckVersion($class)
      */
    public static function devicesArrayDataSource($class, $var) {
        $o = driverTest::createDriver();        
        $return = array();
        if (isset($o->drivers[$class]->devices)) {
            foreach ($o->drivers[$class]->devices as $fw => $Firm) {
                if ($var == "fw") {
                    $return[] = array($fw, $Firm);
                } else {
                    foreach ($Firm as $hw => $ver) {
                        if ($var == "hw") {
                            $return[] = array($fw, $hw, $ver);
                        } else {
                            $dev = explode(",", $ver);
                            foreach ($dev as $v) {
                                $return[] = array($fw, $hw, $v);
                            }
                        }
                    }
                }
                
            }
        } else {
            $return = array();
        }        
        return $return;
    }



    /**
     * data provider for testDevicesArray
     */    
    public static function dataDevicesVersion() {
        return array();
    }
    /**
     * @dataProvider dataDevicesVersion
      */
    function testDevicesArrayVersion($fw, $hw, $version) {
        $this->assertRegExp("/([0-9]{2}\.[0-9]{2}\.[0-9]{2}|DEFAULT|BAD)/", $version);
    }
    /**
     * data provider for testDevicesArray
     */    
    public static function dataDevicesFirmware() {
        return array();
    }
    /**
     * @dataProvider dataDevicesFirmware
      */
    function testDevicesArrayFirmware($fw, $Firm) {
        $this->assertRegExp("/([0-9]{4}-[0-9]{2}-[0-9]{2}-[A-Z]|DEFAULT)/", $fw);
        $this->assertType("array", $Firm);
    }
    /**
     * data provider for testDevicesArray
     */    
    public static function dataDevicesHardware() {
        return array();
    }
    /**
     * @dataProvider dataDevicesHardware
      */
    function testDevicesArrayHardware($fw, $hw, $Ver) {
        $this->assertRegExp("/[0-9]{4}-[0-9]{2}-[0-9]{2}-[A-Z]/", $hw);
        $this->assertType("string", $Ver);
    }
    /**
     *
      */
    function testConfigDefault() {
        $this->assertType("array", $this->o->drivers[$this->class]->config['DEFAULT'], "Driver '".$this->class."' has no DEFAULT config");
    }
    
    /**
     *
      */
    function testAverageTable() {
        $table = $this->o->drivers[$this->class]->getAverageTable();
        $this->assertType("string", $table, "Driver '".$this->class."' has no HWName attribute");
        $this->assertThat(strlen($table), $this->greaterThan(0), "Driver '".$this->class."' has blank HWName");
    }
    /**
     *
      */
    function testHistoryTable() {
        $table = $this->o->drivers[$this->class]->getHistoryTable();
        $this->assertType("string", $table, "Driver '".$this->class."' has no HWName attribute");
        $this->assertThat(strlen($table), $this->greaterThan(0), "Driver '".$this->class."' has blank HWName");
    }
    /**
     *
      */
    function testAtoDMax() {
        $this->assertType("int", $this->o->drivers[$this->class]->AtoDMax, "Driver '".$this->class."': AtoDMax must be an integer.");                
    }

    /**
     * data provider for dataConfigArray* functions
      */
    public static function dataConfigArray($class=null) {
        $o = driverTest::createDriver();
        if (empty($class)) return array();
        $return = array();
        if (is_array($o->drivers[$class]->config)) {
            foreach ($o->drivers[$class]->config as $fw => $params) {
                $return[] = array($class, $fw, $params);
            }
        } else {
            $return = array();
        }
        return $return;
    }
    
    /**
     * @dataProvider dataConfigArray
      */
    function testConfigArray($class, $fw, $params) {
        $this->assertRegExp("/([0-9]{4}-[0-9]{2}-[0-9]{2}-[A-Z]|DEFAULT)/", $fw);
        $this->assertType("array", $params, "'$fw':Parameters are not an array");
        $this->assertType("array", $this->o->drivers[$this->class]->devices[$fw], "'$fw' not found in devices array");
    }
    
    /**
     * @dataProvider dataConfigArray
      */
    function testConfigArrayFunction($class, $fw, $params) {
        $this->assertType("string", $params["Function"], "'$fw': Parameter 'Function' must be a string");
        $this->assertFalse(empty($params["Function"]), "'$fw': Parameter 'Function' can not be empty");
    }
    
    /**
     * @dataProvider dataConfigArray
     *
      */
    function testConfigArraySensors($class, $fw, $params) {
        $this->assertType("int", $params["Sensors"], "'$fw': Parameter 'Sensors' must be a int");
        $this->assertThat($params["Sensors"], $this->greaterThanOrEqual(0), "'$fw': The number of sensors must be greater than 0");
    }
    /**
     * @dataProvider dataConfigArray
     *
      */
    function testConfigArrayDisplayOrder($class, $fw, $params) {
        // This is not required so we only check it if it is present
        if (isset($params['DisplayOrder'])) {
            $this->assertType("string", $params["DisplayOrder"], "'$fw': Parameter 'DisplayOrder' must be a int");
            $do = explode(",", $params['DisplayOrder']);
            $this->assertEquals(count($do), $params["Sensors"], "'$fw': Number of display items needs to be identical to the number of sensors.");
            $doTmp = array();
            foreach ($do as $order) {
                $this->assertThat($order, $this->lessThan($params["Sensors"]), "'$fw': Display order items must be less than the number of sensors.");
                $this->assertThat($order, $this->greaterThanOrEqual(0), "'$fw': Display order items must be greater than or equal to 0");
                $this->assertFalse(isset($doTmp[$order]), "'$fw': $order already duplicated!  All entries in display order must be unique");
                $doTmp[$order] = true;
            }
        }
    }
    /**
     * @dataProvider dataConfigArray
     *
      */
    function testConfigArrayBad($class, $fw, $params) {
        $this->assertFalse(isset($params["SensorLength"]), "'$fw': Parameter 'SensorLength' is not used anymore and should be removed.");
    }
    /**
     * Test the read sensors routine
     * @todo implement testReadSensors()
      */
    function testReadSensors() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * @todo implement testsaveSensorData()
      */
    function testsaveSensorData() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * @todo implement testupdateConfig()
      */
    function testupdateConfig() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     *
      */
    public static function dataCheckRecord() {
        return array(
            array(array(), array("Status" => "GOOD"), array("Status" => 'BAD', "StatusOld" => "GOOD"), 1),
            array(
                array(),
                array("sendCommand" => PACKET_COMMAND_GETDATA, "RawData" => "00010203", "Data0" => null, "Data1" => null, "Data2" => null, "Data3" => null, "Data4" => null, "NumSensors" => 5),
                array("sendCommand" => PACKET_COMMAND_GETDATA, "RawData" => "00010203", "Data0" => null, "Data1" => null, "Data2" => null, "Data3" => null, "Data4" => null, "NumSensors" => 5, "Status" => "BAD", "StatusCode" => "All Bad"),
                2,
            ),
            array(
                array(),
                array("sendCommand" => PACKET_COMMAND_GETDATA, "RawData" => "00010203", "Data0" => 1, "NumSensors" => 1),
                array("sendCommand" => PACKET_COMMAND_GETDATA, "RawData" => "00010203", "Data0" => 1, "NumSensors" => 1, "Status" => "BAD", "StatusCode" => "Bad TC"),
                3,
            ),
        );
    }

    /**
     * @dataProvider dataCheckRecord()
      */
    function testCheckRecord($Info, $Rec, $expect) {
        $this->o->drivers[$this->class]->checkRecord($Info, $Rec);
        $this->assertSame($expect, $Rec);
    }


    /**
     * @todo implement testReadMem()
      */
    function testReadMem() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }
    
    /**
     * @todo implement testGetConfigVars()
      */
    function testGetConfigVars() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }
    
    /**
     * data provider for testReadConfig
      */
    public static function dataReadConfig() {
        return array(
            array(
                array(
                    "5A5A5A5C0000250000200059" => "5A5A5A0100002000002520000000002500391202420039200343000002FFFFFF50010000000000000000009F",
                    "5A5A5A4C0000250000200049" => "5A5A5AFF00002000002500FA",
                ),
                array("DeviceID" => "000025", "GatewayKey" => self::$socket),
                array(
                    "GetReply" => true,
                    "SentFrom" => "000020",
                    "SentTo" => "000025",
                    "sendCommand" => "5C",
                    "group" => false,
                    "packet" => array(
                        "to" => "000025",
                        "command" => "5C",
                        "data" => "",
                    ),
                    "PacketTo" => "000025",
                    "GatewayKey" => self::$socket,
                    "DeviceKey" => null,
                    "Type" => "OUTGOING",
                    "RawData" => "000000002500391202420039200343000002FFFFFF5001000000000000000000",
                    "sentRawData" => "",
                    "Parts" => 1,
                    "Command" => "01",
                    "To" => "000020",
                    "From" => "000025",
                    "Length" => 32,
                    "Data" => array(0, 0, 0, 0, 37, 0, 57, 18, 2, 66, 0, 57, 32, 3, 67, 0, 0, 2, 255, 255, 255, 80, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0),
                    "Checksum" => "9F",
                    "CalcChecksum" => "9F",
                    "RawPacket" => "0100002000002520000000002500391202420039200343000002FFFFFF50010000000000000000009F",
                    "Socket" => 1,
                    "Reply" => true,
                    "toMe" => true,
                    "isGateway" => false,
                ),
                1,
            ),
        );
    }
    /**
     * @dataProvider dataReadConfig()
      */
    function testReadConfig($preload, $Info, $expect) {
        $this->setUpPacket($preload);
        $ret = $this->o->drivers[$this->class]->ReadConfig($Info);
        $ret = $ret[0];
        unset($ret["pktTimeout"]);
        unset($ret["SentTime"]);
        unset($ret["Date"]);
        unset($ret["Time"]);
        unset($ret["ReplyTime"]);
        $this->assertSame($expect, $ret);
    }
        
    /**
     * @todo implement testUnsolicited()
      */
    function testUnsolicited() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }
    /**
      */
    function testInterpConfig() {
        if (is_array($this->InterpConfigTestCases) && (count($this->InterpConfigTestCases) > 0)) {
            foreach ($this->InterpConfigTestCases as $key => $params) {
                $ret = $this->o->drivers[$this->class]->InterpConfig($params["Info"]);
                $this->checkInterpConfigReturn($params["Info"], $params['Return']);
            }
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }

    /**
     *
      */
    private function checkInterpConfigReturn($ret, $expected) {
        // Check the stuff we can predict
        foreach ($expected as $key => $val) {
            $this->assertSame($val, $ret[$key], $this->class.": InterpConfig Failure in key $key");
        }
        // Check the stuff we can't predict (it might change witout a change in this code)
        foreach (array("Labels", "Units", "unitType", "dType", "doTotal") as $type) {
            $this->assertType("array", $ret[$type], $this->class."Missing array '$type'");
            $this->assertEquals(count($ret[$type]), $ret['NumSensors'], $this->class.": $type doesn't have ".$ret['NumSensors']." elements");
        }
        for ($key = 0; $key < $ret['NumSensors']; $key++) {
            foreach (array("Labels", "Units", "unitType", "dType") as $type) {
                $this->assertType("string", $ret[$type][$key], $this->class.": $type element $key is not a string");
            }
            $this->assertType("bool", $ret["doTotal"][$key], $this->class.": doTotal element $key is not a boolean");
            $this->assertType("string", $ret["params"]["sensorType"][$key], $this->class.": params[sensorType] element $key is not a string");
        }
        $this->assertType("array", $ret['params'], $this->class.': params element must be an array');
    }



    function printArray($ret) {
        if (is_array($ret)) {
            foreach ($ret as $name => $val) {
                print '"'.$name.'" => ';
                endpointTestBase::printArrayVal($val);
                print ",\n";
            }
        }
    }
    
    function printArrayVal($val) {
        if (is_array($val)) {
            print "array(";
            $sep = "";
            foreach ($val as $key => $v) {
                if (is_array($v)) {
                    print "\n    ";
                    if (is_string($key)) print '"'.$key.'" => ';
                    endpointTestBase::printArrayVal($v);
                    print ",\n";
                } else {
                    if (is_string($key)) {
                        print '"'.$key.'" => ';
                    } else {
                        print $sep;
                        $sep = ", ";                    
                    }
                    endpointTestBase::printArrayVal($v);
                    if (is_string($key)) print ",\n";
                }
            }                
            print ")";
        } else if (is_string($val)) {
            print '"'.$val.'"';
        } else if (is_bool($val)) {
            if ($val) {
                print "true";
            } else {
                print "false";
            }
        } else if (is_null($val)) {
            print "null";
        } else {
            print $val;
        }
    }    
    
    /**
     *
      */
    function testBadDriver() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }    
    
    /**
     *
      */
    function testInterpSensors() {
        if (is_array($this->InterpSensorsTestCases)) {
            foreach ($this->InterpSensorsTestCases as $key => $params) {
                $ret = $this->o->drivers[$this->class]->InterpSensors($params["Info"], $params["Packets"]);
                if (is_array($params["Return"])) {
                    $this->assertType("array", $ret, "Return was not an array");
                    foreach ($ret as $p => $pkt) {
                        $this->checkInterpSensorsReturn($pkt, $params["Return"][$p], $p);
                    }
                } else {
                    $this->assertSame($params["Return"], $ret);
                }
            }
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }

    /**
     *
      */
    private function checkInterpSensorsReturn($ret, $expected, $p) {
        $this->assertType("array", $ret, $this->class." run $p: return is not an array");
        // Check the stuff we can predict
        foreach ($expected as $key => $val) {
            $this->assertSame($val, $ret[$key], $this->class." run $p: InterpSensors Failure in key $key");
        }
        if (is_array($expected["Types"])) {
            $nSensors = (isset($expected['ActiveSensors'])) ? $expected['ActiveSensors'] : $expected['NumSensors'];
            for ($key = 0; $key < $nSensors; $key++) {
                foreach (array("Units", "unitType") as $type) {
                    $this->assertType("string", $ret[$type][$key], $this->class."run $p : $type element $key is not a string");
                }
            }
        } else {
            $function = "checkInterpSensorsReturn".$expected["sendCommand"];
            if (method_exists($this, $function)) {
                $this->$function($ret, $expected, $p);
            }
        }
    }


   
    /**
     * @todo implement testSetAllConfig()
      */
    function testSetAllConfig() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }
    /**
     * @todo implement testGetCalibration()
      */
    function testGetCalibration() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * @todo implement testSetConfig()
      */
    function testSetConfig() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }


}

/**
 * Mock class for testing drivers.
 */
class driverMock {
    public $packet = "packet";
    public $device = "device";
    public $history = "history";
    public $location = "location";
    public $average = "average";
}

?>
