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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../base/DeviceDriverBase.php';
require_once dirname(__FILE__).'/../stubs/DummyDeviceContainer.php';
require_once dirname(__FILE__).'/../../containers/PacketContainer.php';

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
class DeviceDriverBaseTest extends PHPUnit_Framework_TestCase
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
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->socket = &$this->config->sockets->getSocket("default");
        $this->d = new DummyDeviceContainer();
        $this->o = new TestDevice($this->d);
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
    * data provider for testReadSetup, testReadConfig
    *
    * @return array
    */
    public static function dataReadSetup()
    {
        return array(
            array(
                "000025",
                "000000002500391101410039201343000009FFFFFF50",
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000020",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000000002500391101410039201343000009FFFFFF50",
                )),
                (string)new PacketContainer(array(
                    "To" => "000025",
                    "From" => "000020",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                )),
                true,
            ),
            array(
                "000025",
                "000000000100392601500039260150010203FFFFFF10",
                "",
                "5A5A5A5C00002500002000595A5A5A5C0000250000200059"
                    ."5A5A5A0300002500002000065A5A5A5C0000250000200059",
                false,
            ),
        );
    }
    /**
    * test the loadable routine.
    *
    * @return null
    */
    public function testLoadable()
    {
        $this->assertFalse($this->o->loadable());
    }
    /**
    * test the loadable routine.
    *
    * @return null
    */
    public function testGateway()
    {
        $this->assertFalse($this->o->gateway());
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $id     The Device ID to pretend to be
    * @param string $string The string for the dummy device to return
    * @param string $read   The read string to put in
    * @param string $write  The write string expected
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataReadSetup
    */
    public function testReadSetup($id, $string, $read, $write, $expect)
    {
        $this->d->DeviceID = $id;
        $this->d->DriverInfo["PacketTimeout"] = 1;
        $this->socket->readString = $read;
        $ret = $this->o->readSetup();
        $this->assertSame($write, $this->socket->writeString, "Wrong writeString");
        $this->assertSame($string, $this->d->string, "Wrong Setup String");
        $this->assertSame($expect, $ret, "Wrong return value");
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $id     The Device ID to pretend to be
    * @param string $string The string for the dummy device to return
    * @param string $read   The read string to put in
    * @param string $write  The write string expected
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataReadSetup
    */
    public function testReadConfig($id, $string, $read, $write, $expect)
    {
        $this->d->DeviceID = $id;
        $this->d->DriverInfo["PacketTimeout"] = 1;
        $this->socket->readString = $read;
        $ret = $this->o->readConfig();
        $this->assertSame($write, $this->socket->writeString, "Wrong writeString");
        $this->assertSame($string, $this->d->string, "Wrong Setup String");
        $this->assertSame($expect, $ret, "Wrong return value");
    }
    /**
    * data provider for testReadCalibration
    *
    * @return array
    */
    public static function dataReadCalibration()
    {
        return array(
            array(
                "000025",
                (string)new PacketContainer(array(
                    "From" => "000025",
                    "To" => "000020",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "06070809",
                )),
                (string)new PacketContainer(array(
                    "To" => "000025",
                    "From" => "000020",
                    "Command" => PacketContainer::COMMAND_GETCALIBRATION,
                    "Data" => "",
                )),
                true,
            ),
            array(
                "000025",
                "",
                "5A5A5A4C00002500002000495A5A5A4C0000250000200049"
                    ."5A5A5A0300002500002000065A5A5A4C0000250000200049",
                false,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $id     The Device ID to pretend to be
    * @param string $read   The read string to put in
    * @param string $write  The write string expected
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataReadCalibration
    */
    public function testReadCalibration($id, $read, $write, $expect)
    {
        $this->d->DeviceID = $id;
        $this->d->DriverInfo["PacketTimeout"] = 1;
        $this->socket->readString = $read;
        $ret = $this->o->readCalibration();
        $this->assertSame($write, $this->socket->writeString, "Wrong writeString");
        $this->assertSame($expect, $ret, "Wrong return value");
    }

    /**
    * test the return of toString
    *
    * @return null
    */
    public function testToString()
    {
        $this->assertSame("", $this->o->toString());
    }
    /**
    * data provider for testCompareFWVesrion
    *
    * @return array
    */
    public static function dataReadSetupTime()
    {
        return array(
            array(date("Y-m-d H:i:s"), array(), 10, false),
            array("2004-01-01 00:00:00", array(), 12, true),
            array(date("Y-m-d H:i:s", time()-3600), array(), 1, true),
            array(
                date("Y-m-d H:i:s", time()-86400),
                array("ConfigFail" => 60, "LastConfig" => time()),
                12,
                false,
            ),
        );
    }
    /**
    * test
    *
    * @param string $lastConfig The last config date
    * @param array  $persist    The persistant information from the driver
    * @param int    $interval   The second version
    * @param bool   $expect     What to expect
    *
    * @return null
    *
    * @dataProvider dataReadSetupTime
    */
    function testReadSetupTime($lastConfig, $persist, $interval, $expect)
    {
        $this->d->params->DriverInfo = $persist;
        $this->d->LastConfig = $lastConfig;
        $ret = $this->o->readSetupTime($interval);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testReadTimeReset
    *
    * @return array
    */
    public static function dataReadTimeReset()
    {
        return array(
            array(date("Y-m-d H:i:s"), array()),
            array("2004-01-01 00:00:00", array()),
            array(date("Y-m-d H:i:s", time()-3600), array()),
        );
    }
    /**
    * test
    *
    * @param string $lastConfig The last config date
    * @param array  $persist    The persistant information from the driver
    *
    * @return null
    *
    * @dataProvider dataReadTimeReset
    */
    function testReadTimeReset($lastConfig, $persist)
    {
        $this->d->params->DriverInfo = $persist;
        $this->d->LastConfig = $lastConfig;
        $this->o->readTimeReset();
        $this->assertSame("1970-01-01 00:00:00", $this->d->LastConfig);
        $this->assertSame(0, $this->d->params->DriverInfo["LastConfig"]);
        $this->assertSame(0, $this->d->params->DriverInfo["ConfigFail"]);
        $this->assertSame("1970-01-01 00:00:00", $this->d->LastPoll);
        $this->assertSame(0, $this->d->params->DriverInfo["LastPoll"]);
        $this->assertSame(0, $this->d->params->DriverInfo["PollFail"]);
    }
    /**
    * data provider for testSendPkt
    *
    * @return array
    */
    public static function dataSendPkt()
    {
        return array(
            // Everything works
            array(
                PacketContainer::COMMAND_READFLASH,
                "010203040506",
                true,
                "000123",
                (string) new PacketContainer(
                    array(
                        "From" => "000123",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "060504030201",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READFLASH,
                        "Data" => "010203040506",
                    )
                ),
                "060504030201",
            ),
            // reply not expected
            array(
                PacketContainer::COMMAND_READFLASH,
                "010203040506",
                false,
                "000123",
                "",
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READFLASH,
                        "Data" => "010203040506",
                    )
                ),
                true,
            ),
            // No Reply
            array(
                PacketContainer::COMMAND_READFLASH,
                "000020",
                true,
                "000123",
                "",
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READFLASH,
                        "Data" => "000020",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READFLASH,
                        "Data" => "000020",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                        "Data" => "000020",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READFLASH,
                        "Data" => "000020",
                    )
                ),
                false,
                1,
            ),
        );
    }
    /**
    * test
    *
    * @param string $command The address to write to
    * @param string $data    The hexified data string to send to it
    * @param bool   $reply   Whether to expect a reply or not
    * @param string $devID   The deviceID to use
    * @param string $read    The read string
    * @param string $write   The write string
    * @param bool   $expect  The expected return value
    * @param int    $timeout The packet timeout to use.  0 == default
    *
    * @return null
    *
    * @dataProvider dataSendPkt
    */
    function testSendPkt(
        $command, $data, $reply, $devID, $read, $write, $expect, $timeout = 0
    ) {
        $this->d->DriverInfo["PacketTimeout"] = $timeout;
        $this->d->DeviceID = $devID;
        $this->socket->readString = $read;
        $ret = $this->o->sendPkt($command, $data, $reply);
        $this->assertSame($write, $this->socket->writeString, "Write string wrong");
        $this->assertSame($expect, $ret, "Return value is wrong");
    }
    /**
    * data provider for testReadFlash
    *
    * @return array
    */
    public static function dataReadFlash()
    {
        return array(
            // Everything works
            array(
                10,
                6,
                "000123",
                (string) new PacketContainer(
                    array(
                        "From" => "000123",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "010203040506",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READFLASH,
                        "Data" => "000A06",
                    )
                ),
                "010203040506",
            ),
            // No Reply
            array(
                0,
                32,
                "000123",
                "",
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READFLASH,
                        "Data" => "000020",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READFLASH,
                        "Data" => "000020",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                        "Data" => "000020",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READFLASH,
                        "Data" => "000020",
                    )
                ),
                false,
                1,
            ),
        );
    }
    /**
    * test
    *
    * @param int    $addr    The address to write to
    * @param int    $length  The hexified data string to send to it
    * @param string $devID   The deviceID to use
    * @param string $read    The read string
    * @param string $write   The write string
    * @param bool   $expect  The expected return value
    * @param int    $timeout The packet timeout to use.  0 == default
    *
    * @return null
    *
    * @dataProvider dataReadFlash
    */
    function testReadFlash(
        $addr, $length, $devID, $read, $write, $expect, $timeout = 0
    ) {
        $this->d->DriverInfo["PacketTimeout"] = $timeout;
        $this->d->DeviceID = $devID;
        $this->socket->readString = $read;
        $ret = $this->o->readFlash($addr, $length);
        $this->assertSame($write, $this->socket->writeString, "Write string wrong");
        $this->assertSame($expect, $ret, "Return value is wrong");
    }
    /**
    * data provider for testReadSRAM
    *
    * @return array
    */
    public static function dataReadSRAM()
    {
        return array(
            // Everything works
            array(
                10,
                6,
                "000123",
                (string) new PacketContainer(
                    array(
                        "From" => "000123",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "010203040506",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READSRAM,
                        "Data" => "000A06",
                    )
                ),
                "010203040506",
            ),
            // No Reply
            array(
                0,
                32,
                "000123",
                "",
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READSRAM,
                        "Data" => "000020",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READSRAM,
                        "Data" => "000020",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                        "Data" => "000020",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READSRAM,
                        "Data" => "000020",
                    )
                ),
                false,
                1,
            ),
        );
    }
    /**
    * test
    *
    * @param int    $addr    The address to write to
    * @param int    $length  The hexified data string to send to it
    * @param string $devID   The deviceID to use
    * @param string $read    The read string
    * @param string $write   The write string
    * @param bool   $expect  The expected return value
    * @param int    $timeout The packet timeout to use.  0 == default
    *
    * @return null
    *
    * @dataProvider dataReadSRAM
    */
    function testReadSRAM(
        $addr, $length, $devID, $read, $write, $expect, $timeout = 0
    ) {
        $this->d->DriverInfo["PacketTimeout"] = $timeout;
        $this->d->DeviceID = $devID;
        $this->socket->readString = $read;
        $ret = $this->o->readSRAM($addr, $length);
        $this->assertSame($write, $this->socket->writeString, "Write string wrong");
        $this->assertSame($expect, $ret, "Return value is wrong");
    }
    /**
    * data provider for testReadE2
    *
    * @return array
    */
    public static function dataReadE2()
    {
        return array(
            // Everything works
            array(
                10,
                6,
                "000123",
                (string) new PacketContainer(
                    array(
                        "From" => "000123",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "010203040506",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READE2,
                        "Data" => "000A06",
                    )
                ),
                "010203040506",
            ),
            // No Reply
            array(
                0,
                32,
                "000123",
                "",
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READE2,
                        "Data" => "000020",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READE2,
                        "Data" => "000020",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                        "Data" => "000020",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_READE2,
                        "Data" => "000020",
                    )
                ),
                false,
                1,
            ),
        );
    }
    /**
    * test
    *
    * @param int    $addr    The address to write to
    * @param int    $length  The hexified data string to send to it
    * @param string $devID   The deviceID to use
    * @param string $read    The read string
    * @param string $write   The write string
    * @param bool   $expect  The expected return value
    * @param int    $timeout The packet timeout to use.  0 == default
    *
    * @return null
    *
    * @dataProvider dataReadE2
    */
    function testReadE2(
        $addr, $length, $devID, $read, $write, $expect, $timeout = 0
    ) {
        $this->d->DriverInfo["PacketTimeout"] = $timeout;
        $this->d->DeviceID = $devID;
        $this->socket->readString = $read;
        $ret = $this->o->readE2($addr, $length);
        $this->assertSame($write, $this->socket->writeString, "Write string wrong");
        $this->assertSame($expect, $ret, "Return value is wrong");
    }

}
/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Endpoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2010 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class TestDevice extends DeviceDriverBase
    implements DeviceDriverInterface
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "testDevice",
        "Type" => "device",
        "Class" => "TestDevice",
        "Devices" => array(
            "DEFAULT" => array(
                "DEFAULT" => "DEFAULT",
            ),
        ),
    );
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
        $this->fromString($string);
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    public function readConfig()
    {
        return parent::readConfig();
    }
    /**
    * Reads the calibration out of the device
    *
    * @return bool True on success, False on failure
    */
    public function readCalibration()
    {
        return parent::readCalibration();
    }
    /**
    * reads a block of flash
    *
    * @param int $addr   The start address of this block
    * @param int $length The length to read.  0-255
    *
    * @return true on success, false on failure
    */
    public function readSRAM($addr, $length)
    {
        return parent::readSRAM($addr, $length);
    }
    /**
    * reads a block of flash
    *
    * @param int $addr   The start address of this block
    * @param int $length The length to read.  0-255
    *
    * @return true on success, false on failure
    */
    public function readFlash($addr, $length)
    {
        return parent::readFlash($addr, $length);
    }

    /**
    * Reads a block of E2
    *
    * @param int $addr   The start address of this block
    * @param int $length The length to read.  0-255
    *
    * @return true on success, false on failure
    */
    public function readE2($addr, $length)
    {
        return parent::readE2($addr, $length);
    }
    /**
    * Deals with memory.  This will read and write to any type of memory
    *
    * @param string $command The command to use
    * @param string $data    The data to use
    * @param bool   $reply   Wait for a reply
    *
    * @return true on success, false on failure
    */
    public function sendPkt($command, $data = "", $reply = true)
    {
        return parent::sendPkt($command, $data, $reply);
    }
}

?>
