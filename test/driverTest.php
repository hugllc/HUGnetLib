<?php
/**
 * Tests the driver class
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

// Call driverTest::main() if this source file is executed directly.
if (!defined("PHPUNIT_MAIN_METHOD")) {
    define("PHPUNIT_MAIN_METHOD", "driverTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../hugnet.inc.php';
require_once dirname(__FILE__).'/unitConversionTest.php';
require_once dirname(__FILE__).'/epsocketTest.php';
require_once dirname(__FILE__).'/EPacketTest.php';
require_once dirname(__FILE__).'/gatewayTest.php';
require_once 'adodb/adodb.inc.php';

/**
 * Test class for driver.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:25.
 */
class driverTest extends PHPUnit_Framework_TestCase {
    static $socket = 1;
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
      */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("driverTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
      */
    protected function setUp() {
        $this->o = &$this->createDriver();
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

    public function &createDriver($socket=null) {
        if (!is_numeric($socket)) $socket = self::$socket;
        $db = &ADONewConnection('mysqli');
        $driver = new driver($db);
        $driver->unit = new unitConversionMock();
        $driver->gateway = new gatewayMock($driver);
        $driver->packet->socket[$socket] = new epsocketMock();
        $driver->packet->ReplyTimeout=1;  // The reply timeout can be short becuase we should get an instant reply.
        $driver->sensors->registerSensor("testSensor");
        return $driver;
    }

    private function setUpPacket($preload) {
        if (is_array($preload)) {
            foreach ($preload as $data => $reply) {
                $this->o->packet->socket[self::$socket]->setReply($data, $reply);
            }
        }
    }

    /**
     * dataProvider for testRegisterSensor
      */
    public static function dataRegisterDriver() {
        $classTest = new testDriver();
        return array(
            array("testDriver", true),
            array("testDriverBad", false),
            array("testDriverNoDrivers", false),
            array($classTest, true),
        );
    }
    /**
     *
     * @dataProvider dataRegisterDriver
     * @covers driver::RegisterDriver
      */
    public function testRegisterDriver($class, $expect) {
        $ret = $this->o->registerDriver($class);
        $this->assertSame($expect, $ret);
    }
    /**
     * dataProvider for testRegisterSensor
      */
    public static function dataRegisterDriverGood() {
        // These should all be good drivers.
        $classTest = new testDriver();
        return array(
            array("testDriver"),
            array($classTest),
        );
    }
    /**
     *
     * @dataProvider dataRegisterDriverGood
     * @covers driver::RegisterDriver
      */
    public function testRegisterDriverObject($class) {
        $ret = $this->o->registerDriver($class);
        if (is_object($class)) {
            $this->assertSame($class, $this->o->drivers[get_class($class)]);
        } else {
            $this->assertThat($this->o->drivers[$class], $this->isInstanceOf($class));
        }
    }

    /**
     *
     * @dataProvider dataRegisterDriverGood
     * @covers driver::RegisterDriver
      */
    public function testRegisterDriverDevArray($class) {
        $ret = $this->o->registerDriver($class);
        if (is_object($class)) $class = get_class($class);
        $this->assertType("array", $this->o->drivers[$class]->devices, "Devices array missing");
        foreach ($this->o->drivers[$class]->devices as $fw => $Firm) {
            foreach ($Firm as $hw => $ver) {
                $dev = explode(",", $ver);
                foreach ($dev as $d) {
                    $this->assertSame($this->o->dev[$hw][$fw][$d], $class, "'$hw->$fw->$d': entry not found");
                }
            }
        }
    }

    /**
     * data provider for testDriversRegistered
      */
    public static function dataDriversRegistered() {
        return array(
            array("eDEFAULT"),
            array("e00391200"),
            array("e00391201"),
            array("e00392100"),
            array("e00392800"),
        );
    }
    
    /**
     * @dataProvider dataDriversRegistered
      */
    public function testDriversRegistered($class) {
        $this->assertThat($this->o->drivers[$class], $this->isInstanceOf($class));
    }


    /**
     * @dataProvider dataRunFunction().
     * @covers driver::RunFunction
      */
    public function testRunFunctionDefaultCall() {
        $Info = array();
        $this->o->registerDriver($this->getMock("eDEFAULT", array("interpConfig"), array(&$this->o)), "eDEFAULT");
        $this->o->drivers['eDEFAULT']->expects($this->once())
                               ->method('interpConfig')
                               ->with($this->arrayHasKey("Driver"));
        $ret = $this->o->RunFunction($Info, "interpConfig");
    }
    /**
     * @dataProvider dataRunFunction().
     * @covers driver::RunFunction
      */
    public function testRunFunctionBadDriverCall() {
        $Info = array("Driver" => "BadDriver");
//        $this->o->drivers['eDEFAULT'] = $this->getMock("eDEFAULT", array("interpConfig"), array(&$this->o));
        $this->o->registerDriver($this->getMock("eDEFAULT", array("interpConfig"), array(&$this->o)), "eDEFAULT");
        $this->o->drivers['eDEFAULT']->expects($this->once())
                               ->method('interpConfig')
                               ->with($this->arrayHasKey("Driver"));
        $ret = $this->o->RunFunction($Info, "interpConfig");
    }
    /**
     * @dataProvider dataRunFunction().
     * @covers driver::RunFunction
      */
    public function testRunFunctionGoodDriverCall() {
        $Info = array("Driver" => "testDriver");
//        $this->o->drivers['testDriver'] = $this->getMock("testDriver", array("interpConfig"), array(&$this->o));
        $this->o->registerDriver($this->getMock("testDriver", array("interpConfig"), array(&$this->o)), "testDriver");
        $this->o->drivers['testDriver']->expects($this->once())
                               ->method('interpConfig')
                               ->with($this->arrayHasKey("Driver"));
        $ret = $this->o->RunFunction($Info, "interpConfig");
    }
    /**
     * @dataProvider dataRunFunction().
     * @covers driver::RunFunction
      */
    public function testRunFunctionMultiArgsCall() {
        $Info = array("Driver" => "testDriver");
//        $this->o->drivers['testDriver'] = $this->getMock("testDriver", array("Test"), array(&$this->o));
        $this->o->registerDriver($this->getMock("testDriver", array("Test"), array(&$this->o)), "testDriver");
        $this->o->drivers['testDriver']->expects($this->once())
                               ->method('Test')
                               ->with($this->arrayHasKey("Driver"));
        $ret = $this->o->RunFunction($Info, "Test", "1", "2");
    }
    /**
     * @dataProvider dataRunFunction().
     * @covers driver::RunFunction
      */
    public function testRunFunctionMissingFunctionCall() {
        $Info = array("Driver" => "testDriver");
//        $this->o->drivers['testDriver'] = $this->getMock("testDriver", array("Test"), array(&$this->o));
        $this->o->registerDriver($this->getMock("testDriver", array("Test"), array(&$this->o)), "testDriver");
        $ret = $this->o->RunFunction($Info, "Test", "1", "2");
        $this->assertEquals(null, $ret);
    }

    /**
     * @dataProvider dataRunFunction().
     * @covers driver::RunFunction
      */
    public function testRunFunctionBadFunctionCall() {
        $Info = array("Driver" => "asdf");
//        $this->o->drivers['testDriver'] = $this->getMock("testDriver", array("Test"), array(&$this->o));
        $this->o->registerDriver($this->getMock("testDriver", array("Test"), array(&$this->o)), "testDriver");
        $ret = $this->o->RunFunction($Info, "TestBad", "1", "2");
        $this->assertEquals(false, $ret);
    }
    /**
     * @dataProvider dataRunFunction().
     * @covers driver::RunFunction
      */
    public function testRunFunctionGotError() {
        $Info = array("Driver" => "testDriver");
//        $this->o->drivers['testDriver'] = $this->getMock("testDriver", array("Test"), array(&$this->o));
        $this->o->registerDriver($this->getMock("testDriver", array("Test"), array(&$this->o)), "testDriver");
        $ret = $this->o->RunFunction($Info, "getError", "1", "2");
        $this->assertEquals(false, $ret);
    }

    /**
     * @dataProvider datareadConfig().
     * @covers driver::__call
      */
    public function test__CallCall() {
        $Info = array('Driver' => 'testDriver');
//        $this->o->drivers['testDriver'] = $this->getMock("testDriver", array("Test"), array(&$this->o));
        $this->o->registerDriver($this->getMock("testDriver", array("Test"), array(&$this->o)), "testDriver");
        $this->o->drivers['testDriver']->expects($this->once())
                               ->method('Test')
                               ->with($this->arrayHasKey("Driver"), $this->equalTo("1"), $this->equalTo("2"));
        $ret = $this->o->Test($Info, "1", "2");
    }

    /**
     * @dataProvider datareadConfig().
     * @covers driver::__call
      */
    public function test__Call() {
        $Info = array('Driver' => 'testDriver');
        $arg2 = "1";
        $arg3 = "2";
//        $this->o->drivers['testDriver'] = new testDriver(&$this->o);
        $this->o->registerDriver("testDriver");
        $ret = $this->o->Test($Info, $arg2, $arg3);
        $this->assertEquals($ret['arg2'], $arg2, "Arg2 mangled: '".$ret['arg2']."' != '$arg2'");
        $this->assertEquals($ret['arg3'], $arg3, "Arg3 mangled: '".$ret['arg3']."' != '$arg3'");
    }

    /**
     * @dataProvider datareadConfig().
     * @covers driver::__call
      */
    public function test__CallNoArgsCall() {
        // This has to go to eDEFAULT since it has no args.
//        $this->o->drivers['eDEFAULT'] = $this->getMock("testDriver", array("TestCall"), array(&$this->o));
        $this->o->registerDriver($this->getMock("testDriver", array("TestCall"), array(&$this->o)), "eDEFAULT");
        $this->o->drivers['eDEFAULT']->expects($this->once())
                               ->method('TestCall')
                               ->with($this->arrayHasKey("Driver"));
        $ret = $this->o->TestCall();
    }


    /**
     * @dataProvider dataSetConfig().
     * @covers driver::SetConfig
      */
    public function testSetConfig() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @dataProvider dataDone().
     * @covers driver::Done
      */
    public function testDone() {
        $Info = array("GatewayKey" => 1);
        // This has to go to eDEFAULT since it has no args.
        $this->o->packet = $this->getMock("EPacket", array(), array("socketType" => "test"));
        $this->o->packet->expects($this->once())
                  ->method('Close')
                  ->with($this->arrayHasKey("GatewayKey"));
        $this->o->done($Info);
    }

    /**
     * @dataProvider dataUpdateDevice().
     * @covers driver::UpdateDevice
      */
    public function testUpdateDevice() {
        $Info = array("DeviceID" => 1);
        
        // This has to go to eDEFAULT since it has no args.
        $this->o->device = $this->getMock("device", array("updateDevice"), array(&$this->o));
        $this->o->device->expects($this->once())
                  ->method('updateDevice')
                  ->with($this->arrayHasKey("DeviceID"));
        $this->o->UpdateDevice($Info);
    }

    /**
     * @dataProvider dataUnsolicitedConfigCheck().
     * @covers driver::UnsolicitedConfigCheck
      */
    public function testUnsolicitedConfigCheck() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @dataProvider dataGetDevice().
     * @covers driver::GetDevice
      */
    public function testGetDevice() {
        $Info = array("DeviceID" => 1);
        
        // This has to go to eDEFAULT since it has no args.
        $this->o->device = $this->getMock("device", array("getDevice"), array(&$this->o));
        $this->o->device->expects($this->once())
                  ->method('getDevice')
                  ->with($this->arrayHasKey("DeviceID"));
        $this->o->getDevice($Info, "KEY");
    }


    /**
     * @dataProvider dataGetInfo().
     * @covers driver::GetInfo
      */
    public function testGetInfo() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    public static function datainterpConfig() {
        return array(
            array("Bad", false, 1),
            array(array(), array(), 2),
            array(
                array(
                    array(
                        "DeviceKey" => 1,
                        "sendCommand" => PACKET_COMMAND_GETSETUP,
                        "RawSetup" => "00000000E8ABCDEF01410123456743000005FFFFFF500102020202020202027070707070707070",
                        "Date" => "2007-11-12 12:34:04",
                        "From" => "0000E8",
                    ),
                    array(
                        "PacketFrom" => "wrongOne",
                        "RawData" => "12345678",
                        "sendCommand" => "55",
                        "Date" => "2007-11-13 12:34:04",
                    ),
                    array(
                        "From" => "wrongOne",
                        "RawData" => "12345678",
                        "sendCommand" => "56",
                        "Date" => "2007-11-14 12:34:04",
                    ),
                    array(
                        "DeviceID" => "wrongOne",
                        "RawData" => "12345678",
                        "sendCommand" => "57",
                        "Date" => "2007-11-15 12:34:04",
                    ),
                    array(
                        "DeviceID" => "0000E8",
                        "Data" => "12345678",
                        "sendCommand" => PACKET_COMMAND_GETCALIBRATION,
                        "Date" => "2007-11-16 12:34:04",
                    ),
                ),
                array(
                    "DeviceID" => "0000E8",
                    "DeviceKey" => 1,
                    "CurrentGatewayKey" => null,
                    "Date" => "2007-11-12 12:34:04",
                    "RawData" => Array(
                        "5C" => "00000000E8ABCDEF01410123456743000005FFFFFF500102020202020202027070707070707070",
                        "4C" => "12345678",
                    ),
                    "SerialNum" => 232,
                    "HWPartNum" => "ABCD-EF-01-A",
                    "FWPartNum" => "0123-45-67-C",
                    "FWVersion" => "0.0.5",
                    "DeviceGroup" => "FFFFFF",
                    "BoredomThreshold" => 80,
                    "RawSetup" => "00000000E8ABCDEF01410123456743000005FFFFFF500102020202020202027070707070707070",
                    "LastConfig" => "2007-11-12 12:34:04",
                    "DriverInfo" => "0102020202020202027070707070707070",
                    "RawCalibration" => "12345678",
                    "Driver" => "testDriver",
                    "HWName" => "Phantom Test Hardware",
                ),
                3,
            ),      

        );
    }

    /**
     * @dataProvider datainterpConfig().
     * @covers driver::interpConfig
      */
    public function testinterpConfig($packets, $expect) {
        
        $this->o->registerDriver("testDriver");
        $ret = $this->o->interpConfig ($packets);
        $this->assertSame($expect, $ret);
    }


    
    /**
     * data provider for testGetLocationTable
      */
    public static function dataDriverInfo() {
        // DeviceID and Driver must be present and not empty
        return array(
            array(array("DeviceID" => "123456", "Driver" => "testDriver"), "history_table", "testhistory"),
            array(array("DeviceID" => "123456", "Driver" => "testDriver"), "average_table", "testaverage"),
        );
    }
    /**
     * @dataProvider dataDriverInfo().
     * @covers driver::DriverInfo
      */
    public function testDriverInfo($Info, $field, $expect) {

        $infoSave = $Info;        
        $this->o->registerDriver($Info["Driver"]);
        $Info = $this->o->DriverInfo($Info);
        $this->assertSame($expect, $Info[$field]);
        foreach (array("DeviceID", "Driver") as $f) {
            $this->assertSame($infoSave[$f], $Info[$f], "$f not the same");
        }
    }

    /**
     * data provider for testFindDriver
      */
    public static function dataFindDriver() {
        return array(
            array(array("HWPartNum" => 1, "FWPartNum" => 2, "FWVersion" =>3), "eDEFAULT", 1),
            array(array("HWPartNum" => "testHW2", "FWPartNum" => "testFW", "FWVersion" => "0.2.3"), "testDriver", 2),
            array(array("HWPartNum" => "testHW1", "FWPartNum" => "testFW", "FWVersion" => "otherVersion"), "testDriver", 3),
            array(array("HWPartNum" => "testHW3", "FWPartNum" => "otherFW", "FWVersion" => "otherVersion"), "testDriver", 4),
            array(array("HWPartNum" => "testHW4", "FWPartNum" => "testFW2", "FWVersion" => "otherVersion"), "eDEFAULT", 5),
        );
    }
    /**
     * @dataProvider dataFindDriver().
     * @covers driver::FindDriver
      */
    public function testFindDriver($Info, $expect) {
        
        $this->o->registerDriver("testDriver");
        $driver = $this->o->findDriver($Info);
        $this->assertSame($expect, $driver);
    }

    public function &modifyUnitsSetup() {        
        return $this->o;
    }

    /**
     * data provider for testModifyUnits
      */
    public static function dataModifyUnits() {
        return array(
            array(
                array(
                    0 => array("Data0" => 1.0, "Data1" => 2, "Data2" => 3, "Data3" => 4, "Data4" => 6.5, "data" => array(1.0,2,3,4,6.5), "Date" => "2007-11-12 16:05:00"),
                    1 => array("Data0" => 3.0, "Data1" => 2, "Data2" => 4, "Data3" => 6, "Data4" => 6.5, "data" => array(2.0,2,4,6,6.5), "Date" => "2007-11-12 16:10:00"),
                ), // History
                array(
                    "ActiveSensors" => 5, 
                    "dType" => array("raw","diff","diff","raw","diff"), 
                    "Types" => array(0x100, 0x100, 0x100, 0x100,0x100), 
                    "params"=> array("sensorType"=>array("testSensor2", "testSensor1", "testSensor2", "testSensor2", "testSensor2")),
                    "Units" => array("E", "B", "E", "E", "E"),
                ), // DevInfo
                2, // dPlaces
                array("raw", "ignore", "diff", "diff", "raw"), // Type
                array("E", "B", "E", "D", "E"), // Units
                array(
                    1 => array("Data0" => null,"Data2" => 4.0, "Data3" => -1.0, "Data4" => 6.5, "data" => array(null,null,4.0,-1.0, 6.5), "Date" => "2007-11-12 16:10:00", "deltaT" => 300),
                ), // expectHistory
                array(
                    "ActiveSensors" => 5, 
                    "dType" => array("raw","diff","diff","raw","diff"), 
                    "Types" => array(0x100, 0x100, 0x100, 0x100,0x100), 
                    "params"=> array("sensorType"=>array("testSensor2", "testSensor1", "testSensor2", "testSensor2", "testSensor2")),
                    "Units" => array("E", "B", "E", "D", "E"),
                ), // expectDevInfo
                array("raw", "ignore", "diff", "diff", "diff"), // expectType
                array("E", "B", "E", "D","E"), // expectUnits
            ),
        );
    }
    /**
     * @dataProvider dataModifyUnits().
     * @covers driver::ModifyUnits
      */
    public function testModifyUnitsHistory($history, $devInfo, $dPlaces, $type, $units, $expectHistory, $expectDevInfo, $expectType, $expectUnits) {
        $ret = $this->o->modifyUnits($history, $devInfo, $dPlaces, $type, $units);
        $this->assertSame($expectHistory, $history);
    }
    /**
     * @dataProvider dataModifyUnits().
     * @covers driver::ModifyUnits
      */
    public function testModifyUnitsDevInfo($history, $devInfo, $dPlaces, $type, $units, $expectHistory, $expectDevInfo, $expectType, $expectUnits) {
        $ret = $this->o->modifyUnits($history, $devInfo, $dPlaces, $type, $units);
        $this->assertSame($expectDevInfo, $devInfo);
    }
    /**
     * @dataProvider dataModifyUnits().
     * @covers driver::ModifyUnits
      */
    public function testModifyUnitsType($history, $devInfo, $dPlaces, $type, $units, $expectHistory, $expectDevInfo, $expectType, $expectUnits) {
        $ret = $this->o->modifyUnits($history, $devInfo, $dPlaces, $type, $units);
        $this->assertSame($expectType, $type);
    }
    /**
     * @dataProvider dataModifyUnits().
     * @covers driver::ModifyUnits
      */
    public function testModifyUnitsUnits($history, $devInfo, $dPlaces, $type, $units, $expectHistory, $expectDevInfo, $expectType, $expectUnits) {
        $ret = $this->o->modifyUnits($history, $devInfo, $dPlaces, $type, $units);
        $this->assertSame($expectUnits, $units);
    }

}

// Call driverTest::main() if this source file is executed directly.
if (PHPUNIT_MAIN_METHOD == "driverTest::main") {
    driverTest::main();
}
/**
 * This is a dummy endpoint driver to test the driver class with
 *
 * @see driver, eDEFAULT
 */
class testDriver extends eDEFAULT {

    /** history table */
    protected $history_table = "testhistory";
    /** location table
     *  @deprecated This is now stored in the 'params' field in the devices table
      */
    protected $location_table = "testlocation";
    /** Average Table */
    protected $average_table = "testaverage";
    /** Raw history Table */
    protected $raw_history_table = "testhistory_raw";
    var $devices = array(    
        "testFW" => array(
            "testHW1" => "DEFAULT",
            "testHW2" => "0.1.2,0.2.3",
        ),
        "DEFAULT" => array(
            "testHW3" => "DEFAULT",
            "ABCD-EF-01-A" => "DEFAULT",
        ),
        "testFW2" => array(
            "testHW4" => "BAD",
        ),
    );        
    
    public function Test($arg1, $arg2, $arg3) {
        if (is_array($arg1)) {
            $arg1['arg2'] = $arg2;
            $arg1['arg3'] = $arg3;
        }
        return $arg1;
    }

    public function TestCall($arg1) {
        return $arg1;
    }
    
    public function interpConfig(&$Info) {
        $Info['HWName'] = "Phantom Test Hardware";
        return $Info;
    }
    
    public function getError() {
        return array("Errno" => 1, "Error" => "Test Error");
    }
    
    public function __construct(&$driver = false) {
        if (is_object($driver)) {
            parent::__construct($driver);
        }
    }
    
    public function __toString() {
        return "object(".get_class($this).")";
    }
}
/**
 * This is a dummy endpoint driver to test the driver class with
 *
 * @see driver, eDEFAULT
 */
class testDriverNoDrivers extends eDEFAULT {
    public function __construct() {
        unset($this->devices);
    }
}

?>
