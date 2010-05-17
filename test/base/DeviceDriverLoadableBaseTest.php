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


require_once dirname(__FILE__).'/../../base/DeviceDriverLoadableBase.php';
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
class DeviceDriverLoadableBaseTest extends PHPUnit_Framework_TestCase
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
        $this->o = new TestDeviceLoadable($this->d);
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
    public function testLoadable()
    {
        $this->assertTrue($this->o->loadable());
    }

    /**
    * data provider for testCompareFWVesrion
    *
    * @return array
    */
    public static function dataReadSetupTime()
    {
        return array(
            array(date("Y-m-d H:i:s"), 60, false),
            array("2004-01-01 00:00:00", 12, true),
            array(date("Y-m-d H:i:s", time()-70), 1, true),
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
        $this->d->LastConfig = $lastConfig;
        $ret = $this->o->readSetupTime($interval);
        $this->assertSame($expect, $ret);
    }

    /**
    * data provider for testWriteE2
    *
    * @return array
    */
    public static function dataWriteE2()
    {
        return array(
            array(
                10,
                "010203040506",
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
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "000A010203040506",
                    )
                ),
                true,
            ),
            array(
                0,
                "000102030405060708090A0B0C0D0E0F",
                "000124",
                (string) new PacketContainer(
                    array(
                        "From" => "000124",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "0A0B0C0D0E0F",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "To" => "000124",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "000A0A0B0C0D0E0F",
                    )
                ),
                true,
            ),
            // Wrong value written
            array(
                10,
                "010203040506",
                "000123",
                (string) new PacketContainer(
                    array(
                        "From" => "000123",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "010203040507",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "000A010203040506",
                    )
                ),
                false,
            ),
            // No Reply
            array(
                10,
                "010203040506",
                "000123",
                "",
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "000A010203040506",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "000A010203040506",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                        "Data" => "",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "000A010203040506",
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
    * @param stirng $data    The hexified data string to send to it
    * @param string $devID   The deviceID to use
    * @param string $read    The read string
    * @param string $write   The write string
    * @param bool   $expect  The expected return value
    * @param int    $timeout The packet timeout to use.  0 == default
    *
    * @return null
    *
    * @dataProvider dataWriteE2
    */
    function testWriteE2(
        $addr, $data, $devID, $read, $write, $expect, $timeout = 0
    ) {
        $this->d->DriverInfo["PacketTimeout"] = $timeout;
        $this->d->id = hexdec($devID);
        $this->d->DeviceID = $devID;
        $this->socket->readString = $read;
        $ret = $this->o->writeE2($addr, $data);
        $this->assertSame($write, $this->socket->writeString, "Write string wrong");
        $this->assertSame($expect, $ret, "Return value is wrong");
    }
    /**
    * data provider for testWriteFlash
    *
    * @return array
    */
    public static function dataWriteFlash()
    {
        return array(
            // Everything works
            array(
                0,
                "010203040506",
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
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "0000010203040506",
                    )
                ),
                true,
            ),
            // Wrong value written
            array(
                0,
                "010203040506",
                "000123",
                (string) new PacketContainer(
                    array(
                        "From" => "000123",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "010203040507",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "0000010203040506",
                    )
                ),
                false,
            ),
            // No Reply
            array(
                0,
                "010203040506",
                "000123",
                "",
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "0000010203040506",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "0000010203040506",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                        "Data" => "",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "0000010203040506",
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
    * @param stirng $data    The hexified data string to send to it
    * @param string $devID   The deviceID to use
    * @param string $read    The read string
    * @param string $write   The write string
    * @param bool   $expect  The expected return value
    * @param int    $timeout The packet timeout to use.  0 == default
    *
    * @return null
    *
    * @dataProvider dataWriteFlash
    */
    function testWriteFlash(
        $addr, $data, $devID, $read, $write, $expect, $timeout = 0
    ) {
        $this->d->DriverInfo["PacketTimeout"] = $timeout;
        $this->d->id = hexdec($devID);
        $this->d->DeviceID = $devID;
        $this->socket->readString = $read;
        $ret = $this->o->writeFlash($addr, $data);
        $this->assertSame($write, $this->socket->writeString, "Write string wrong");
        $this->assertSame($expect, $ret, "Return value is wrong");
    }
    /**
    * data provider for testWriteCRC
    *
    * @return array
    */
    public static function dataWriteCRC()
    {
        return array(
            // Everything works
            array(
                "0102",
                "000123",
                (string) new PacketContainer(
                    array(
                        "From" => "000123",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "0102",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => DeviceDriverLoadableBase::COMMAND_WRITECRC,
                        "Data" => "0102",
                    )
                ),
                true,
            ),
            // Wrong value written
            array(
                "0102",
                "000123",
                (string) new PacketContainer(
                    array(
                        "From" => "000123",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "0103",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => DeviceDriverLoadableBase::COMMAND_WRITECRC,
                        "Data" => "0102",
                    )
                ),
                false,
            ),
            // No Reply
            array(
                "0102",
                "000123",
                "",
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => DeviceDriverLoadableBase::COMMAND_WRITECRC,
                        "Data" => "0102",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => DeviceDriverLoadableBase::COMMAND_WRITECRC,
                        "Data" => "0102",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                        "Data" => "",
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => DeviceDriverLoadableBase::COMMAND_WRITECRC,
                        "Data" => "0102",
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
    * @param int    $crc     The CRC to write
    * @param string $devID   The deviceID to use
    * @param string $read    The read string
    * @param string $write   The write string
    * @param bool   $expect  The expected return value
    * @param int    $timeout The packet timeout to use.  0 == default
    *
    * @return null
    *
    * @dataProvider dataWriteCRC
    */
    function testWriteCRC(
        $crc, $devID, $read, $write, $expect, $timeout = 0
    ) {
        $this->d->DriverInfo["PacketTimeout"] = $timeout;
        $this->d->id = hexdec($devID);
        $this->d->DeviceID = $devID;
        $this->socket->readString = $read;
        $ret = $this->o->writeCRC($crc);
        $this->assertSame($write, $this->socket->writeString, "Write string wrong");
        $this->assertSame($expect, $ret, "Return value is wrong");
    }
    /**
    * data provider for testReadCRC
    *
    * @return array
    */
    public static function dataReadCRC()
    {
        return array(
            // Everything works
            array(
                "000123",
                (string) new PacketContainer(
                    array(
                        "From" => "000123",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "0102",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => DeviceDriverLoadableBase::COMMAND_READCRC,
                    )
                ),
                "0102",
            ),
            // No Reply
            array(
                "000123",
                "",
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => DeviceDriverLoadableBase::COMMAND_READCRC,
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => DeviceDriverLoadableBase::COMMAND_READCRC,
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                    )
                )
                .(string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => DeviceDriverLoadableBase::COMMAND_READCRC,
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
    * @param string $devID   The deviceID to use
    * @param string $read    The read string
    * @param string $write   The write string
    * @param bool   $expect  The expected return value
    * @param int    $timeout The packet timeout to use.  0 == default
    *
    * @return null
    *
    * @dataProvider dataReadCRC
    */
    function testReadCRC(
        $devID, $read, $write, $expect, $timeout = 0
    ) {
        $this->d->DriverInfo["PacketTimeout"] = $timeout;
        $this->d->id = hexdec($devID);
        $this->d->DeviceID = $devID;
        $this->socket->readString = $read;
        $ret = $this->o->readCRC();
        $this->assertSame($write, $this->socket->writeString, "Write string wrong");
        $this->assertSame($expect, $ret, "Return value is wrong");
    }
    /**
    * data provider for testRunBootloader
    *
    * @return array
    */
    public static function dataRunBootloader()
    {
        return array(
            // Everything works
            array(
                "000123",
                (string) new PacketContainer(
                    array(
                        "From" => "000123",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => DeviceDriverLoadableBase::COMMAND_RUNBOOTLOADER,
                    )
                ),
                true,
                0,
            ),
            array(
                "000123",
                "",
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => DeviceDriverLoadableBase::COMMAND_RUNBOOTLOADER,
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => DeviceDriverLoadableBase::COMMAND_RUNBOOTLOADER,
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => DeviceDriverLoadableBase::COMMAND_RUNBOOTLOADER,
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
    * @param string $devID   The deviceID to use
    * @param string $read    The read string
    * @param string $write   The write string
    * @param bool   $expect  The expected return value
    * @param int    $timeout The timeout to use
    *
    * @return null
    *
    * @dataProvider dataRunBootloader
    */
    function testRunBootloader($devID, $read, $write, $expect, $timeout = 0)
    {
        $this->d->DriverInfo["PacketTimeout"] = $timeout;
        $this->d->id = hexdec($devID);
        $this->d->DeviceID = $devID;
        $this->socket->readString = $read;
        $ret = $this->o->runBootloader();
        $this->assertSame($write, $this->socket->writeString, "Write string wrong");
        $this->assertSame($expect, $ret, "Return value is wrong");
    }
    /**
    * data provider for testRunApplication
    *
    * @return array
    */
    public static function dataRunApplication()
    {
        return array(
            // Everything works
            array(
                "000123",
                (string) new PacketContainer(
                    array(
                        "From" => "000123",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_RUNAPPLICATION,
                    )
                ),
                true,
            ),
            array(
                "000123",
                "",
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_RUNAPPLICATION,
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_RUNAPPLICATION,
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000123",
                        "From" => "000020",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_RUNAPPLICATION,
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
    * @param string $devID   The deviceID to use
    * @param string $read    The read string
    * @param string $write   The write string
    * @param bool   $expect  The expected return value
    * @param int    $timeout The timeout to use
    *
    * @return null
    *
    * @dataProvider dataRunApplication
    */
    function testRunApplication($devID, $read, $write, $expect, $timeout=0)
    {
        $this->d->DriverInfo["PacketTimeout"] = $timeout;
        $this->d->id = hexdec($devID);
        $this->d->DeviceID = $devID;
        $this->socket->readString = $read;
        $ret = $this->o->runApplication();
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
class TestDeviceLoadable extends DeviceDriverLoadableBase
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
        $this->fromSetupString($string);
    }
    /**
    * Programs a page of flash
    *
    * Due to the nature of flash, $Val must contain the data for
    * a whole page of flash.
    *
    * @param int    $addr The start address of this block
    * @param string $data The data to program into E2 as a hex string
    *
    * @return true on success, false on failure
    */
    public function writeFlash($addr, $data)
    {
        return parent::writeFlash($addr, $data);
    }
    /**
    * Programs a block of E2
    *
    * This function won't let locations 0-9 be written.  They are reserved for the
    * serial number and shouldn't be overwritten
    *
    * @param int    $addr The start address of this block
    * @param string $data The data to program into E2 as a hex string
    *
    * @return true on success, false on failure
    */
    public function writeE2($addr, $data)
    {
        return parent::writeE2($addr, $data);
    }
    /**
    * Gets the CRC of the data
    *
    * @return The CRC on success, false on failure
    */
    public function readCRC()
    {
        return parent::readCRC();
    }

    /**
    * Gets the CRC of the data
    *
    * @param string $crc The CRC to write
    *
    * @return The CRC on success, false on failure
    */
    public function writeCRC($crc)
    {
        return parent::writeCRC($crc);
    }

}

?>
