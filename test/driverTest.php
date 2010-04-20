<?php
/**
 * Tests the driver class
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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
if (!defined("HUGNET_INCLUDE_PATH")) {
    define("HUGNET_INCLUDE_PATH", dirname(__FILE__)."/..");
}

require_once dirname(__FILE__).'/../hugnet.inc.php';
require_once dirname(__FILE__).'/driverMocks.php';
require_once HUGNET_INCLUDE_PATH."/driver.php";

/**
 * Test class for driver.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:25.
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
class DriverTest extends PHPUnit_Framework_TestCase
{
    static $socket = 1;

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
        $this->o = &$this->createDriver();
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
    * This crates the driver correctly for testing.  It mocks a number of classes
    * to make it easier to test.
    *
    * @param int $socket The socket to use
    *
    * @return object
    */
    public function &createDriver($socket = null)
    {
        if (!is_numeric($socket)) {
            $socket = self::$socket;
        }
        $config["file"]       = ":memory:";
        $config["servers"][0] = array(
            'host' => 'localhost',
            'user' => '',
            'pass' => '',
            'dsn' => "sqlite::memory:",
        );

        $driver = new HUGnetDriver($config);

        // The reply timeout can be short becuase we should get an instant reply.
        $driver->packet->ReplyTimeout = 1;
        $driver->sensors->registerSensor("TestSensor");
        return $driver;
    }

    /**
    * Sets up a packet to be returned if one is sent out
    *
    * @param array $preload The packets to set up
    *
    * @return null
    */
    protected function setUpPacket($preload)
    {
        if (is_array($preload)) {
            foreach ($preload as $data => $reply) {
                $this->o->packet->socket[self::$socket]->setReply($data, $reply);
            }
        }
    }

    /**
    * dataProvider for testRegisterSensor
    *
    * @return array
    */
    public static function dataRegisterDriver()
    {
        $classTest = new testDriver();
        return array(
            array("testDriver", true),
            array("testDriverBad", false),
            array("testDriverNoDrivers", false),
            array($classTest, true),
        );
    }
    /**
    * test registerDriver
    *
    * @param mixed $class  object or class name to register
    * @param bool  $expect The result to expect
    *
    * @return null
    *
    * @dataProvider dataRegisterDriver
    */
    public function testRegisterDriver($class, $expect)
    {
        $ret = $this->o->registerDriver($class);
        $this->assertSame($expect, $ret);
    }
    /**
    * dataProvider for testRegisterSensor
    *
    * @return array
    *
    */
    public static function dataRegisterDriverGood()
    {
        // These should all be good drivers.
        $classTest = new testDriver();
        return array(
            array("testDriver"),
            array($classTest),
        );
    }
    /**
    * test registerDriver
    *
    * @param mixed $class object or class name to register
    *
    * @return null
    *
    * @dataProvider dataRegisterDriverGood
    */
    public function testRegisterDriverObject($class)
    {
        $ret = $this->o->registerDriver($class);
        if (is_object($class)) {
            $this->assertSame($class, $this->o->drivers[get_class($class)]);
        } else {
            $this->assertThat(
                $this->o->drivers[$class],
                $this->isInstanceOf($class)
            );
        }
    }

    /**
    * test registerDriver
    *
    * @param mixed $class object or class name to register
    *
    * @return null
    *
    * @dataProvider dataRegisterDriverGood
    */
    public function testRegisterDriverDevArray($class)
    {
        $ret = $this->o->registerDriver($class);
        if (is_object($class)) {
            $class = get_class($class);
        }
        $this->assertType(
            "array",
            $this->o->drivers[$class]->devices,
            "Devices array missing"
        );
        foreach ($this->o->drivers[$class]->devices as $fw => $Firm) {
            foreach ($Firm as $hw => $ver) {
                $dev = explode(",", $ver);
                foreach ($dev as $d) {
                    $this->assertSame(
                        $this->o->dev[$hw][$fw][$d],
                        $class,
                        "'$hw->$fw->$d': entry not found"
                    );
                }
            }
        }
    }

    /**
    * data provider for testDriversRegistered
    *
    * @return array
    */
    public static function dataDriversRegistered()
    {
        return array(
            array("eDEFAULT"),
            array("e00391200"),
            array("e00391201"),
            array("e00392100"),
            array("e00392800"),
        );
    }

    /**
    * test registerDriver
    *
    * @param string $class class name to check
    *
    * @return null
    *
    * @dataProvider dataDriversRegistered
    */
    public function testDriversRegistered($class)
    {
        $this->assertThat($this->o->drivers[$class], $this->isInstanceOf($class));
    }


    /**
    * Test runFunction
    *
    * @return null
    *
    */
    public function testRunFunctionDefaultCall()
    {
        $Info = array();
        $this->o->registerDriver(
            $this->getMock("eDEFAULT", array("interpConfig"), array(&$this->o)),
            "eDEFAULT"
        );
        $this->o->drivers['eDEFAULT']->expects($this->once())
            ->method('interpConfig')
            ->with($this->arrayHasKey("Driver"));
        $ret = $this->o->RunFunction($Info, "interpConfig");
    }
    /**
    * Test runFunction
    *
    * @return null
    *
    */
    public function testRunFunctionBadDriverCall()
    {
        $Info = array("Driver" => "BadDriver");
        $this->o->registerDriver(
            $this->getMock("eDEFAULT", array("interpConfig"), array(&$this->o)),
            "eDEFAULT"
        );
        $this->o->drivers['eDEFAULT']->expects($this->once())
            ->method('interpConfig')
            ->with($this->arrayHasKey("Driver"));
        $ret = $this->o->RunFunction($Info, "interpConfig");
    }
    /**
    * Test runFunction
    *
    * @return null
    *
    */
    public function testRunFunctionGoodDriverCall()
    {
        $Info = array("Driver" => "testDriver");
        $this->o->registerDriver(
            $this->getMock("testDriver", array("interpConfig"), array(&$this->o)),
            "testDriver"
        );
        $this->o->drivers['testDriver']->expects($this->once())
            ->method('interpConfig')
            ->with($this->arrayHasKey("Driver"));
        $ret = $this->o->RunFunction($Info, "interpConfig");
    }
    /**
    * Test runFunction
    *
    * @return null
    *
    */
    public function testRunFunctionMultiArgsCall()
    {
        $Info = array("Driver" => "testDriver");
        $this->o->registerDriver(
            $this->getMock("testDriver", array("test"), array(&$this->o)),
            "testDriver"
        );
        $this->o->drivers['testDriver']->expects($this->once())
            ->method('test')
            ->with($this->arrayHasKey("Driver"));
        $ret = $this->o->RunFunction($Info, "test", "1", "2");
    }
    /**
    * Test runFunction
    *
    * @return null
    *
    */
    public function testRunFunctionMissingFunctionCall()
    {
        $Info = array("Driver" => "testDriver");
        $this->o->registerDriver(
            $this->getMock("testDriver", array("test"), array(&$this->o)),
            "testDriver"
        );
        $ret = $this->o->RunFunction($Info, "test", "1", "2");
        $this->assertEquals(null, $ret);
    }

    /**
    * Test runFunction
    *
    * @return null
    *
    */
    public function testRunFunctionBadFunctionCall()
    {
        $Info = array("Driver" => "asdf");
        $this->o->registerDriver(
            $this->getMock("testDriver", array("test"), array(&$this->o)),
            "testDriver"
        );
        $ret = $this->o->RunFunction($Info, "testBad", "1", "2");
        $this->assertEquals(false, $ret);
    }
    /**
    * Test runFunction
    *
    * @return null
    *
    */
    public function testRunFunctionGotError()
    {
        $Info = array("Driver" => "testDriver");
        $this->o->registerDriver(
            $this->getMock("testDriver", array("test"), array(&$this->o)),
            "testDriver"
        );
        $ret = $this->o->RunFunction($Info, "getError", "1", "2");
        $this->assertEquals(false, $ret);
    }

    /**
    * Test __call
    *
    * @return null
    *
    */
    public function testCallCall()
    {
        $Info = array('Driver' => 'testDriver');
        $this->o->registerDriver(
            $this->getMock("testDriver", array("Test"), array(&$this->o)),
            "testDriver"
        );
        $this->o->drivers['testDriver']->expects($this->once())
            ->method('test')
            ->with(
                $this->arrayHasKey("Driver"),
                $this->equalTo("1"),
                $this->equalTo("2")
            );
        $ret = $this->o->test($Info, "1", "2");
    }

    /**
    * Test __call
    *
    * @return null
    *
    */
    public function testCall()
    {
        $Info = array('Driver' => 'testDriver');
        $arg2 = "1";
        $arg3 = "2";
        $this->o->registerDriver("testDriver");
        $ret = $this->o->Test($Info, $arg2, $arg3);
        $this->assertEquals(
            $ret['arg2'],
            $arg2,
            "Arg2 mangled: '".$ret['arg2']."' != '$arg2'"
        );
        $this->assertEquals(
            $ret['arg3'],
            $arg3,
            "Arg3 mangled: '".$ret['arg3']."' != '$arg3'"
        );
    }

    /**
    * Test __call
    *
    * @return null
    *
    */
    public function testCallNoArgsCall()
    {
        // This has to go to eDEFAULT since it has no args.
        $this->o->registerDriver(
            $this->getMock(
                "testDriver",
                array("testCall"),
                array(&$this->o)
            ),
            "eDEFAULT"
        );
        $this->o->drivers['eDEFAULT']->expects($this->once())
            ->method('testCall')
            ->with($this->arrayHasKey("Driver"));
        $ret = $this->o->TestCall();
    }


    /**
    * test done()
    *
    * @return null
    *
    */
    public function testDone()
    {
        $Info = array("GatewayKey" => 1);
        // This has to go to eDEFAULT since it has no args.
        $this->o->packet = $this->getMock(
            "EPacket",
            array(),
            array("socketType" => "test")
        );
        $this->o->packet->expects($this->once())
            ->method('close')
            ->with($this->arrayHasKey("GatewayKey"));
        $this->o->done($Info);
    }

    /**
    * test UpdateDevice()
    *
    * @return null
    *
    */
    public function testUpdateDevice()
    {
        /*
        $Info = array("DeviceID" => 1);

        // This has to go to eDEFAULT since it has no args.
        $this->o->device = $this->getMock("device",
                                          array("updateDevice"),
                                          array(&$this->o));
        $this->o->device->expects($this->once())
                  ->method('updateDevice')
                  ->with($this->arrayHasKey("DeviceID"));
        $this->o->UpdateDevice($Info);
        */
    }

    /**
    * Data provider for testGetDevice()
    *
    * @return array
    */
    public static function dataGetDevice()
    {
        return array(
        );
    }

    /**
    * test getDevice
    *
    * @param mixed $id     This is either the DeviceID, DeviceName or DeviceKey
    * @param int   $type   The type of the 'id' parameter.  It is "ID" for DeviceID,
    *         "NAME" for   DeviceName or "KEY" for DeviceKey.  "KEY" is the default.
    * @param mixed $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetDevice
    */
    public function testGetDevice($id, $type, $expect)
    {
        $Info = array("DeviceID" => 1);
    }


    /**
    * Data provider for testInterpConfig()
    *
    * @return array
    */
    public static function datainterpConfig()
    {
        return array(
            array("Bad", false, 1),
            array(array(), array(), 2),
            array(
                array(
                    array(
                        "DeviceKey" => 1,
                        "sendCommand" => PACKET_COMMAND_GETSETUP,
                        "RawSetup" => "00000000E8ABCDEF01410123456743000005FFFFFF"
                            ."500102020202020202027070707070707070",
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
                        "5C" => "00000000E8ABCDEF01410123456743000005FFFFFF"
                            ."500102020202020202027070707070707070",
                        "4C" => "12345678",
                   ),
                    "SerialNum" => 232,
                    "HWPartNum" => "ABCD-EF-01-A",
                    "FWPartNum" => "0123-45-67-C",
                    "FWVersion" => "0.0.5",
                    "DeviceGroup" => "FFFFFF",
                    "BoredomThreshold" => 80,
                    "RawSetup" => "00000000E8ABCDEF01410123456743000005FFFFFF"
                        ."500102020202020202027070707070707070",
                    "LastConfig" => "2007-11-12 12:34:04",
                    "DriverInfo" => "0102020202020202027070707070707070",
                    "RawCalibration" => "12345678",
                    "unitType" => array(),
                    "Labels" => array(),
                    "Units" => array(),
                    "dType" => array(),
                    "doTotal" => array(),
                    "TotalSensors" => 0,
                    "Driver" => "testDriver",
                    "HWName" => "Phantom Test Hardware",
               ),
                3,
           ),

        );
    }

    /**
    * tests interpConfig
    *
    * @param array $packets The test packets to interpret
    * @param array $expect  The return to expect
    *
    * @return null
    *
    * @dataProvider datainterpConfig().
    */
    public function testinterpConfig($packets, $expect)
    {

        $this->o->registerDriver("testDriver");
        $ret = $this->o->interpConfig($packets);
        $this->assertSame($expect, $ret);
    }



    /**
    * data provider for testGetLocationTable
    *
    * @return array
    */
    public static function dataDriverInfo()
    {
        // DeviceID and Driver must be present and not empty
        return array(
            array(
                array("DeviceID" => "123456", "Driver" => "testDriver"),
                "history_table",
                "testhistory"
            ),
            array(
                array("DeviceID" => "123456", "Driver" => "testDriver"),
                "average_table",
                "testaverage"
            ),
        );
    }
    /**
    * tests driverInfo()
    *
    * @param array  $Info   The devInfo array
    * @param string $field  The field to check
    * @param array  $expect The return to expect
    *
    * @return null
    *
    * @dataProvider dataDriverInfo().
    */
    public function testDriverInfo($Info, $field, $expect)
    {

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
    *
    * @return array
    */
    public static function dataFindDriver()
    {
        return array(
            array(
                array(
                    "HWPartNum" => 1,
                    "FWPartNum" => 2,
                    "FWVersion" =>3
                ),
                "eDEFAULT",
                1
            ),
            array(
                array(
                    "HWPartNum" => "testHW2",
                    "FWPartNum" => "testFW",
                    "FWVersion" => "0.2.3"
                ),
                "testDriver",
                2
            ),
            array(
                array(
                    "HWPartNum" => "testHW1",
                    "FWPartNum" => "testFW",
                    "FWVersion" => "otherVersion"
                ),
                "testDriver",
                3
            ),
            array(
                array(
                    "HWPartNum" => "testHW3",
                    "FWPartNum" => "otherFW",
                    "FWVersion" => "otherVersion"
                ),
                "testDriver",
                4
            ),
            array(
                array(
                    "HWPartNum" => "testHW4",
                    "FWPartNum" => "testFW2",
                    "FWVersion" => "otherVersion"
                ),
                "eDEFAULT",
                5
            ),
        );
    }
    /**
    * tests findDriver()
    *
    * @param array  $Info   The devInfo array
    * @param string $expect The return to expect
    *
    * @return null
    *
    * @dataProvider dataFindDriver().
    */
    public function testFindDriver($Info, $expect)
    {

        $this->o->registerDriver("testDriver");
        $driver = $this->o->findDriver($Info);
        $this->assertSame($expect, $driver);
    }

    /**
    * Setup modifyUnits
    *
    * @return object
    *
    */
    public function &modifyUnitsSetup()
    {
        return $this->o;
    }

    /**
    * data provider for testModifyUnits
    *
    * @return array
    */
    public static function dataModifyUnits()
    {
        return array(
            array("a", "b", "c", "d", "e"),
        );
    }
    /**
    * Test the history from modifyUnits
    *
    * @param array $history The history to modify.
    * @param array $devInfo The devInfo array to modify.
    * @param int   $dPlaces The maximum number of decimal places to show.
    * @param array $type    The types to change to
    * @param array $units   The units to change to
    *
    * @return null
    *
    * @dataProvider dataModifyUnits().
    */
    public function testModifyUnits(
        $history,
        $devInfo,
        $dPlaces,
        $type,
        $units
    ) {
        $this->o->unit = $this->getMock("UnitConversion", array("modifyUnits"));
        $this->o->unit->expects($this->once())
            ->method("modifyUnits")
            ->with(
                $this->equalTo($history),
                $this->equalTo($devInfo),
                $this->equalTo($dPlaces),
                $this->equalTo($type),
                $this->equalTo($units)
            );
        $ret = $this->o->modifyUnits($history, $devInfo, $dPlaces, $type, $units);
    }


}

?>
