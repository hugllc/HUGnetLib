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
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


/** This is a required class */
require_once CODE_BASE.'plugins/devices/E00392600Device.php';
/** This is a required class */
require_once CODE_BASE.'tables/LockTable.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyDeviceContainer.php';
/** This is a required class */
require_once TEST_BASE.'plugins/devices/DevicePluginTestBase.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class E00392600DeviceTest extends DevicePluginTestBase
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
                    "group" => "default",
                ),
                array(
                    "driver" => "sqlite",
                    "file" => ":memory:",
                    "group" => "volatile",
                ),
            ),
            "sockets" => array(
                array(
                    "dummy" => true,
                ),
            ),
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->config->sockets->forceDeviceID("000019");
        $this->socket = &$this->config->sockets->getSocket("default");
        $this->pdo = &$this->config->servers->getPDO();
        $this->d = new DummyDeviceContainer();
        $this->d->DeviceID = "000019";
        $this->o = new TestE00392600Device($this->d);
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
    * test the loadable routine.
    *
    * @return null
    */
    public function testGateway()
    {
        $this->assertTrue($this->o->gateway());
    }

    /**
    * Data provider for testRegisterPlugin
    *
    * @return array
    */
    public static function dataRegisterPlugin()
    {
        return array(
            array("E00392600Device"),
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataToSetupString()
    {
        return array(
            array(
                array(
                    "PhysicalSensors" => 0,
                    "VirtualSensors" => 0,
                    "Job" => 6,
                    "GatewayKey" => 12,
                    "Name" => "Scott's Device Here",
                    "IP" => "1.2.3.4",
                    "Priority" => 231,
                ),
                "06000C53636F747427732044657669636520486572650000000000000000000000"
                    ."01020304E7"
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload This is the attribute to set
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataToSetupString
    */
    public function testToSetupString($preload, $expect)
    {
        $this->d->DriverInfo = $preload;
        $this->d->GatewayKey = (int)$preload["GatewayKey"];
        $ret = $this->o->toSetupString();
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataFromSetupString()
    {
        return array(
            array(
                "06000C53636F747427732044657669636520486572650000000000000000000000"
                    ."01020304E7",
                array(
                    "PhysicalSensors" => 0,
                    "VirtualSensors" => 0,
                    "PacketTimeout" => 10,
                    "Job" => 6,
                    "Function" => "Device",
                    "CurrentGatewayKey" => 12,
                    "Name" => "Scott's Device Here",
                    "IP" => "1.2.3.4",
                    "Priority" => 231,
                ),
            ),
            array(
                "0A000C53636F747427732044657669636520486572650000000000000000000000"
                    ."0102030412",
                array(
                    "PhysicalSensors" => 0,
                    "VirtualSensors" => 0,
                    "PacketTimeout" => 10,
                    "Job" => 10,
                    "Function" => "Unknown",
                    "CurrentGatewayKey" => 12,
                    "Name" => "Scott's Device Here",
                    "IP" => "1.2.3.4",
                    "Priority" => 18,
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload This is the attribute to set
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataFromSetupString
    */
    public function testFromSetupString($preload, $expect)
    {
        $this->o->fromSetupString($preload);
        $this->assertSame($expect, $this->d->DriverInfo);
    }
    /**
    * data provider for testCompareFWVesrion
    *
    * @return array
    */
    public static function dataReadSetupTime()
    {
        return array(
            array(time(), 60, false),
            array("2004-01-01 00:00:00", 12, true),
            array(time()-70, 1, true),
            array(time()+86400, 1, true),
        );
    }
    /**
    * test
    *
    * @param string $lastConfig The last config date
    * @param int    $interval   The second version
    * @param bool   $expect     What to expect
    *
    * @return null
    *
    * @dataProvider dataReadSetupTime
    */
    function testReadSetupTime($lastConfig, $interval, $expect)
    {
        $this->d->params->DriverInfo["LastConfig"] = $lastConfig;
        $ret = $this->o->readSetupTime($interval);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testReadSetup, testReadConfig
    *
    * @return array
    */
    public static function dataReadRTC()
    {
        return array(
            array(
                "000025",
                5,
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000019",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000000000000D",
                    )
                ),
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000019",
                        "Command" => PacketContainer::READRTC_COMMAND,
                        "Data" => "",
                    )
                ),
                13,
            ),
            array(
                "000025",
                2,
                "",
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000019",
                        "Command" => PacketContainer::READRTC_COMMAND,
                        "Data" => "",
                    )
                )
                .(string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000019",
                        "Command" => PacketContainer::READRTC_COMMAND,
                        "Data" => "",
                    )
                )
                .(string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000019",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                        "Data" => "",
                    )
                )
                .(string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000019",
                        "Command" => PacketContainer::READRTC_COMMAND,
                        "Data" => "",
                    )
                ),
                false,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $devId   The Device ID to pretend to be
    * @param int    $timeout The packet timeout to use
    * @param string $read    The read string to put in
    * @param string $write   The write string expected
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataReadRTC
    */
    public function testReadRTC($devId, $timeout, $read, $write, $expect)
    {
        $this->d->id = hexdec($devId);
        $this->d->DeviceID = $devId;
        $this->d->DriverInfo["PacketTimeout"] = $timeout;
        $this->socket->readString = $read;
        $ret = $this->o->readRTC();
        $this->assertSame($write, $this->socket->writeString, "Wrong writeString");
        $this->assertSame($expect, $ret, "Wrong return value");
    }
    /**
    * data provider for testPacketConsumer
    *
    * @return array
    */
    public static function dataPacketConsumer()
    {
        return array(
            array(
                array(
                ),
                array(
                    "To" => "000000",
                    "From" => "123456",
                    "Command" => "5C",
                    "group" => "default",
                ),
                array(
                ),
                "",
            ),
            array(
                array(
                ),
                array(
                    "To" => "000019",
                    "From" => "123456",
                    "Command" => "5C",
                    "group" => "default",
                ),
                array(
                    array(
                        'id' => (string)0x123456,
                        'DeviceID' => '123456',
                        'DeviceName' => '',
                        'HWPartNum' => '',
                        'FWPartNum' => '',
                        'FWVersion' => '',
                        'RawSetup' => '',
                        'Active' => '1',
                        'GatewayKey' => '5',
                        'ControllerKey' => '0',
                        'ControllerIndex' => '0',
                        'DeviceLocation' => 'pktSetupEcho',
                        'DeviceJob' => '',
                        'Driver' => 'eDEFAULT',
                        'PollInterval' => '0',
                        'ActiveSensors' => '0',
                        'DeviceGroup' => 'FFFFFF',
                        'sensors' => '',
                        'params' => '',
                    )
                ),
                "5A5A5A0112345600001916000000000100392601500039260150010203"
                    ."FFFFFF1090",
            ),
            array(
                array(
                ),
                array(
                    "To" => "000019",
                    "From" => "123456",
                    "Command" => "03",
                    "Data" => "01020304",
                    "group" => "default",
                ),
                array(
                ),
                "5A5A5A01123456000019040102030468",
            ),
            array(
                array(
                ),
                array(
                    "To" => "000019",
                    "From" => "123456",
                    "Command" => "02",
                    "Data" => "01020304",
                    "group" => "default",
                ),
                array(
                ),
                "5A5A5A01123456000019040102030468",
            ),
            array(
                array(
                ),
                array(
                    "To" => "000019",
                    "From" => "123456",
                    "Command" => PacketContainer::READRTC_COMMAND,
                    "Data" => "",
                    "group" => "default",
                ),
                array(
                ),
                "5A5A5A0112345600001908000000000000000D6D",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The data to preload into the devices table
    * @param string $pkt     The packet string to use
    * @param string $expect  The expected return
    * @param string $write   The packet string expected to be written
    *
    * @return null
    *
    * @dataProvider dataPacketConsumer
    */
    public function testPacketConsumer($preload, $pkt, $expect, $write)
    {
        $dev = new DeviceContainer();
        foreach ((array)$preload as $load) {
            $dev->fromArray($load);
            $dev->insertRow(true);
        }
        $packet = new PacketContainer($pkt);
        $this->o->packetConsumer($packet);
        $stmt = $this->pdo->query("SELECT * FROM `devices`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows);
        $this->assertSame($write, $this->socket->writeString);

    }
    /**
    * Data provider for testGetMyDevLock
    *
    * @return array
    */
    public static function dataGetMyDevLock()
    {
        return array(
            array( // #0 Device for locking empty
                array(
                    "id" => 1,
                ),
                array(
                ),
                array(),
                null,
            ),
            array( // #1 Already locked!
                array(
                    "id" => 10,
                ),
                array(
                    array(
                        "id" => 0xAAAAAA,
                        "type" => E00392600Device::LOCKTYPE,
                        "lockData" => "000532",
                        "expiration" => 100000000000, // Way in the future
                    ),
                ),
                array(
                    "id" => 0x532,
                    "DeviceID" => "000532",
                ),
                false,
            ),
            array( // #2 Already locked by me!
                array(
                    "id" => 10,
                    "ControllerKey" => 0x13,
                ),
                array(
                    array(
                        "id" => 1,
                        "type" => E00392600Device::LOCKTYPE,
                        "lockData" => "000532",
                        "expiration" => 100000000000, // Way in the future
                    ),
                ),
                array(
                    "id" => 0x532,
                    "DeviceID" => "000532",
                ),
                true,
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the device for the class
    * @param array $locks   The locks that are in place
    * @param array $device  The device to get a lock for
    * @param array $expect  The expected return
    *
    * @dataProvider dataGetMyDevLock
    *
    * @return null
    */
    public function testGetMyDevLock($preload, $locks, $device, $expect)
    {
        $lock = new LockTable();
        foreach ((array)$locks as $key => $val) {
            $lock->clearData();
            $lock->fromAny($val);
            $lock->insertRow(true);
        }
        $dev = new DeviceContainer($preload);
        $obj = new TestE00392600Device($dev);
        $devO = new DeviceContainer($device);
        $ret = $obj->getMyDevLock($devO);
        $this->assertSame($expect, $ret, "Return Wrong");
    }

}
/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Libraries
* @package    HUGnetLibTest
* @subpackage SuitePlugins
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class TestE00392600Device extends E00392600Device
{
    /**
    * Builds the class
    *
    * @param object &$obj   The object that is registering us
    * @param mixed  $string The string we will use to build the object
    *
    * @return null
    */
    public function __construct(&$obj, $string = "")
    {
        parent::__construct($obj, $string);
        $this->devLocks = new E00392600DeviceLockTableTestStub();
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    public function readRTC()
    {
        return parent::readRTC();
    }
    /**
    * Returns the time
    *
    * @return string The binary string.
    */
    protected function now()
    {
        return 13;
    }
}
/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class E00392600DeviceLockTableTestStub extends LockTable
{
    /**
    * Returns the current time in seconds.  This is for testing purposes
    *
    * @return int
    */
    protected function now()
    {
        return 13;
    }
}

?>
