<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is a required class */
require_once CODE_BASE.'base/ProcessBase.php';
/** This is the dummy socket container */
require_once TEST_CONFIG_BASE.'stubs/DummySocketContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ProcessBaseTest extends PHPUnit_Framework_TestCase
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
            "sockets" => array(
                array(
                    "dummy" => true,
                ),
            ),
            "script_gateway" => 1,
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->config->sockets->forceDeviceID("000019");
        $this->socket = &$this->config->sockets->getSocket();
        $this->pdo = &$this->config->servers->getPDO();
        $this->devArray =  array(
            "id"        => 0x19,
            "DeviceID"  => "000019",
            "HWPartNum" => "0039-26-01-P",
            "FWPartNum" => "0039-26-01-P",
            "FWVersion" => "0.6.99",
            "GatewayKey" => "1",
        );
        $this->o = new ProcessBaseClassTest(array(), $this->devArray);
        $this->d = &$this->o->myDevice;
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
        $this->o = null;
        $this->config = null;
        // Trap the exit signal and exit gracefully
        if (function_exists("pcntl_signal")) {
            pcntl_signal(SIGINT, SIG_DFL);
        }
        if (is_object($this->pdo)) {
            $this->pdo->query("drop table devices;");
        }
    }
    /**
    * Tests for exceptions
    *
    * @expectedException RuntimeException
    *
    * @return null
    */
    public function testConstructTableExec()
    {
        $config = array(

        );
        $this->config->forceConfig($config);
        $obj = new ProcessBaseClassTest(array(), $this->devArray);
    }
    /**
    * Tests for exceptions
    *
    * @return null
    */
    public function testConstructTableExec2()
    {
        $config = array(
        );
        $this->config->forceConfig($config);
        $obj = new ProcessBaseClassTest(
            array("GatewayKey" => "all"), $this->devArray
        );
    }
    /**
    * Tests for exceptions
    *
    * @return null
    */
    public function testPacketConsumer()
    {
        $this->o->packetConsumer(new PacketContainer());
    }
    /**
    * data provider for testConstructor
    *
    * @return array
    */
    public static function dataConstructor()
    {
        $LastConfig = time() - (11 * 60 * 60 * 24);
        $LastConfig2 = time() + (11 * 60 * 60 * 24);
        return array(
            array(
                array(),
                array(
                ),
                array(
                    "group" => "default",
                    "GatewayKey" => 1,
                ),
                array(
                    "RawSetup" => "000000000000000000000000000000000000FFFFFF00",
                    "sensors" => array(),
                    "params" => array(),
                ),
                array(
                ),
                null,
                array(
                ),
            ),
            array(
                array(
                    "group" => "test",
                    "GatewayKey" => 3,
                ),
                array(
                    "id"       => 0x19,
                    "HWPartNum" => "0039-26-02-P",
                    "FWPartNum" => "0039-26-02-P",
                    "FWVersion" => "0.6.99",
                    "params" => array(
                        "DriverInfo" => array(
                            "LastConfig" => $LastConfig,
                        ),
                    ),
                ),
                array(
                    "group" => "test",
                    "GatewayKey" => 3,
                ),
                array(
                    "HWPartNum" => "0039-26-02-P",
                    "FWPartNum" => "0039-26-02-P",
                    "FWVersion" => "0.6.99",
                    "Driver" => "e00392600",
                    "id" => 0x19,
                    "RawSetup" => "000000001900392602500039260250000699FFFFFF000"
                        ."000000000000000000000000000000000000000000000000000000"
                        ."000000000000000000000",
                    "sensors" => array(),
                    "params" => array(
                        "DriverInfo" => array(
                            "LastConfig" => $LastConfig,
                        ),
                    ),
                ),
                array(
                    0 => array(
                        'group' => 'default',
                        'id' => '1',
                        'class' => 'ProcessBaseClassTest',
                        'method' => 'checkTime',
                        'errno' => '-100',
                        'Severity' => '8',
                    ),
                ),
                "000019",
                array(
                ),
            ),
            array(
                array(
                    "group" => "test",
                    "GatewayKey" => 3,
                ),
                array(
                    "id"       => 0x19,
                    "HWPartNum" => "0039-26-02-P",
                    "FWPartNum" => "0039-26-02-P",
                    "FWVersion" => "0.6.99",
                    "params" => array(
                        "DriverInfo" => array(
                            "LastConfig" => $LastConfig2,
                        ),
                    ),
                ),
                array(
                    "group" => "test",
                    "GatewayKey" => 3,
                ),
                array(
                    "HWPartNum" => "0039-26-02-P",
                    "FWPartNum" => "0039-26-02-P",
                    "FWVersion" => "0.6.99",
                    "Driver" => "e00392600",
                    "id" => 0x19,
                    "RawSetup" => "000000001900392602500039260250000699FFFFFF000"
                        ."000000000000000000000000000000000000000000000000000000"
                        ."000000000000000000000",
                    "sensors" => array(),
                    "params" => array(
                        "DriverInfo" => array(
                            "LastConfig" => $LastConfig2,
                        ),
                    ),
                ),
                array(
                    0 => array(
                        'group' => 'default',
                        'id' => '1',
                        'class' => 'ProcessBaseClassTest',
                        'method' => 'checkTime',
                        'errno' => '-100',
                        'Severity' => '8',
                    ),
                ),
                "000019",
                array(
                    "id" => 19,
                    "ip" => ProcessBase::getIP(),
                    "GatewayKey" => 3,
                ),
            ),
            array(
                array(
                    "group" => "test",
                    "GatewayKey" => 1,
                ),
                array(
                    "HWPartNum" => "0039-26-01-P",
                ),
                array(
                    "group" => "test",
                    "GatewayKey" => 1,
                ),
                array(
                    "id" => 0x19,
                    "HWPartNum" => "0039-26-01-P",
                    "FWPartNum" => "0039-26-01-P",
                    "FWVersion" => "0.6.99",
                    "Driver" => "e00392600",
                    "sensors" => array(),
                    "params" => array(),
                ),
                array(
                ),
                "000019",
                array(
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload   The value to preload
    * @param array  $device    The device array to use
    * @param array  $expect    The expected data
    * @param array  $expectDev The expected device
    * @param array  $err       The expected errors posted
    * @param string $DeviceID  The device id it should have
    * @param array  $dcLoad    The datacollector to insert
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor(
        $preload, $device, $expect, $expectDev, $err, $DeviceID, $dcLoad
    ) {
        if (!empty($dcLoad)) {
            $dc = new DataCollectorsTable($dcLoad);
            $dc->insertRow(true);
        }
        $obj = new ProcessBaseClassTest($preload, $device);
        $ret = $this->readAttribute($obj, "data");
        $this->assertSame($expect, $ret, "Data is wrong");
        // Check the configuration is set correctly
        $config = $this->readAttribute($obj, "myConfig");
        $this->assertSame(
            "ConfigContainer",
            get_class($config),
            "Config wrong class"
        );
        // Check the configuration is set correctly
        $device = $this->readAttribute($obj, "device");
        $this->assertSame(
            "DeviceContainer",
            get_class($device),
            "Device wrong class"
        );
        // Check the configuration is set correctly
        $myDevice = $this->readAttribute($obj, "myDevice");
        $this->assertSame(
            "DeviceContainer",
            get_class($myDevice),
            "myDevice wrong class"
        );
        $dev = $myDevice->toArray(false);
        foreach ($expectDev as $key => $value) {
            $this->assertSame($value, $dev[$key], "$key in myDevice is wrong");
        }
        $errors = new ErrorTable();
        $ret = $errors->selectInto("errno = ?", array(-100));
        $e = array();
        while ($ret) {
            $temp = $errors->toArray();
            // Date will always change
            unset($temp["Date"]);
            // There is a date in here, also
            unset($temp["error"]);
            $e[] = $temp;
            $ret = $errors->nextInto();
        }
        $this->assertSame($err, $e, "Error table wrong");
        if (is_null($DeviceID)) {
            // If it is random, it must be within this:
            $this->assertGreaterThanOrEqual(
                0xFE0000, hexdec($this->socket->DeviceID), "DeviceID Low"
            );
            $this->assertLessThan(
                0xFF0000, hexdec($this->socket->DeviceID), "DeviceID High"
            );
        } else {
            // If it is set we can test it directly
            $this->assertSame($DeviceID, $this->socket->DeviceID, "DeviceID wrong");
        }
    }

    /**
    * data provider for testPowerup
    *
    * @return array
    */
    public static function dataPowerup()
    {
        return array(
            // Nothing
            array(
                array(),
                "",
                "5A5A5A5E0000000000190047",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The value to preload
    * @param array $read    The packet strings for the function to read
    * @param array $write   The packet strings that the function will write
    *
    * @return null
    *
    * @dataProvider dataPowerup
    */
    public function testPowerup($preload, $read, $write)
    {
        $this->o->fromAny($preload);
        $i = 0;
        $this->o->powerup();
        $this->socket->readString = $read;
        $this->assertSame(
            $write, $this->socket->writeString,
            "$group has the wrong string"
        );
        $dev = $this->readAttribute($this->o, "myDevice");
        $cmd = PacketContainer::COMMAND_POWERUP;
        $this->assertSame(
            1,
            $dev->params->ProcessInfo["unsolicited"][$cmd],
            "Incrementing powerup count failed"
        );
    }
    /**
    * data provider for testPacketConsumer
    *
    * @return array
    */
    public static function dataWait()
    {
        return array(
            array(
                2,
                false,
                "",
                "",
                null,
            ),
            array(
                2,
                true,
                "",
                "",
                null,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param int    $timeout     The timeout to use
    * @param bool   $loop        What to set the loop variable to
    * @param string $read        The read string for the socket
    * @param string $expect      The expected return
    * @param string $unsolicited The deviceID of unsolicited packet to check for
    *
    * @return null
    *
    * @dataProvider dataWait
    */
    public function testWait($timeout, $loop, $read, $expect, $unsolicited)
    {
        $this->socket->readString = $read;
        $this->o->loop = $loop;
        $start = time();
        // This is called twice because it will wait the full time only the second
        // run.  The first run it sets the timeout begining.
        $this->o->wait($timeout);
        $this->o->wait($timeout);
        $end = time();
        if ($loop) {
            $this->assertThat(($end - $start), $this->greaterThanOrEqual($timeout));
        }
        $this->assertSame($expect, $this->socket->writeString);
        if (is_string($unsolicited)) {
            $u = $this->readAttribute($this->o, "unsolicited");
            $this->assertSame($unsolicited, $u->DeviceID);
        }
    }
    /**
    * data provider for testLoopEnd
    *
    * @return array
    */
    public static function dataLoopEnd()
    {
        return array(
            array(
                2,
                false,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param int    $signo  The signal to simulate
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataLoopEnd
    */
    public function testLoopEnd($signo, $expect)
    {
        $this->o->loopEnd($signo);
        $this->assertSame($expect, $this->o->loop);
    }

    /**
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testGetIP()
    {
        $ret = ProcessBaseClassTest::getIP();
        $this->assertNotRegExp(
            "/127\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/",
            $ret,
            "returned localhost"
        );
        $this->assertNotRegExp(
            "/255/",
            $ret,
            "returned netmask or broadcast"
        );
        $this->assertRegExp(
            "/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/",
            $ret
        );
    }

}

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ProcessBaseClassTest extends ProcessBase implements PacketConsumerInterface
{
    /**
    * Builds the class
    *
    * @param array $data   The data to build the class with
    * @param array $device This is the class to send packets to me to.
    *
    * @return null
    */
    public function __construct($data, $device)
    {
        parent::__construct($data, $device);
        $this->registerHooks();
        $this->requireGateway();
    }
}
?>
