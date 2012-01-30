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

/** Get our classes */
require_once CODE_BASE.'plugins/devices/E00392101Device.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyDeviceContainer.php';
/** This is a required class */
require_once TEST_BASE.'plugins/devices/DevicePluginTestBase.php';

/**
 * Test class for the boot loaders for the contoller boards
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
class E00392101DeviceTest extends DevicePluginTestBase
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
        $this->pdo = &$this->config->servers->getPDO();
        $this->socket = &$this->config->sockets->getSocket("default");
        $this->firmware = new FirmwareTable();
        $this->d = new DummyDeviceContainer();
        $this->o = new E00392101Device($this->d);
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
    * Data provider for testRegisterPlugin
    *
    * @return array
    */
    public static function dataRegisterPlugin()
    {
        return array(
            array("E00392101Device"),
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
                ),
                "",
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
    * test the loadable routine.
    *
    * @return null
    */
    public function testController()
    {
        $this->assertTrue($this->o->controller());
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
                "040001E000380000809891",
                array(
                    "PhysicalSensors" => 0,
                    "VirtualSensors" => 0,
                    "SRAM" => 1024,
                    "E2" => 480,
                    "FLASH" => 14336,
                    "FLASHPAGE" => 128,
                    "PAGES" => 112,
                    "CRC" => "9891",
                ),
            ),
            array(
                "040001E000380000009891",
                array(
                    "PhysicalSensors" => 0,
                    "VirtualSensors" => 0,
                    "SRAM" => 1024,
                    "E2" => 480,
                    "FLASH" => 14336,
                    "FLASHPAGE" => 128,
                    "PAGES" => 112,
                    "CRC" => "9891",
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
    * data provider for testWriteProgram
    *
    * @return array
    */
    public static function dataWriteProgram()
    {
        return array(
            array(  // #0
                array(
                    "Code" => "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                    "CodeHash" => "",
                    "Data" => "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                    "DataHash" => "",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "Version" => "1.2.3",
                    "RelStatus" => FirmwareTable::RELEASE,
                ),
                array(
                    "PhysicalSensors" => 0,
                    "SRAM" => 1024,
                    "E2" => 480,
                    "FLASH" => 14336,
                    "FLASHPAGE" => 128,
                    "PAGES" => 112,
                    "CRC" => "9891",
                ),
                "0039-21-01-A",
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                            ."500102020202101002026F6F6F6F6F707070010000000000000"
                            ."002000000000000001027001027000000102700102700000010"
                            ."270010270000001027001027000000102700102700000010270"
                            ."010270000002027001027000000202700102700000020270010"
                            ."2700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "2700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "0039201343000008FFFFFF50010202020210100202"
                            ."6F6F6F6F6F70707001000000000000000200000000000000102"
                            ."700102700000010270010270000001027001027000000102700"
                            ."102700000010270010270000001027001027000000202700102"
                            ."70000002027001027000000202700102700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "2700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "ABCD",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "0000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                            ."500102020202101002026F6F6F6F6F707070010000000000000"
                            ."002000000000000001027001027000000102700102700000010"
                            ."270010270000001027001027000000102700102700000010270"
                            ."010270000002027001027000000202700102700000020270010"
                            ."2700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "00802700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "000A0039201343000008FFFFFF50010202020210100202"
                            ."6F6F6F6F6F70707001000000000000000200000000000000102"
                            ."700102700000010270010270000001027001027000000102700"
                            ."102700000010270010270000001027001027000000202700102"
                            ."70000002027001027000000202700102700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "00802700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_WRITECRC,
                        "Data" => "",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_RUNAPPLICATION,
                        "Data" => "",
                    )
                ),
                true,
            ),
            array(   // #1
                array(
                    "Code" => "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                    "CodeHash" => "",
                    "Data" => "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                    "DataHash" => "",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "Version" => "1.2.3",
                    "RelStatus" => FirmwareTable::RELEASE,
                ),
                array(
                    "PhysicalSensors" => 0,
                    "SRAM" => 1024,
                    "E2" => 480,
                    "FLASH" => 14336,
                    "FLASHPAGE" => 128,
                    "PAGES" => 112,
                    "CRC" => "9891",
                ),
                "0039-21-01-A",
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                            ."500102020202101002026F6F6F6F6F707070010000000000000"
                            ."002000000000000001027001027000000102700102700000010"
                            ."270010270000001027001027000000102700102700000010270"
                            ."010270000002027001027000000202700102700000020270010"
                            ."2700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "2700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "FF0039201343000008FFFFFF50010202020210100202"
                            ."6F6F6F6F6F70707001000000000000000200000000000000102"
                            ."700102700000010270010270000001027001027000000102700"
                            ."102700000010270010270000001027001027000000202700102"
                            ."70000002027001027000000202700102700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "2700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "0000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                            ."500102020202101002026F6F6F6F6F707070010000000000000"
                            ."002000000000000001027001027000000102700102700000010"
                            ."270010270000001027001027000000102700102700000010270"
                            ."010270000002027001027000000202700102700000020270010"
                            ."2700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "00802700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "000A0039201343000008FFFFFF50010202020210100202"
                            ."6F6F6F6F6F70707001000000000000000200000000000000102"
                            ."700102700000010270010270000001027001027000000102700"
                            ."102700000010270010270000001027001027000000202700102"
                            ."70000002027001027000000202700102700000020",
                    )
                ),
                false,
            ),
            array(      // #2   -- E2 fails
                array(
                    "Code" => "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                    "CodeHash" => "",
                    "Data" => "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                    "DataHash" => "",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "Version" => "1.2.3",
                    "RelStatus" => FirmwareTable::RELEASE,
                ),
                array(
                    "PhysicalSensors" => 0,
                    "SRAM" => 1024,
                    "E2" => 480,
                    "FLASH" => 14336,
                    "FLASHPAGE" => 128,
                    "PAGES" => 112,
                    "CRC" => "9891",
                ),
                "0039-21-01-A",
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                            ."500102020202101002026F6F6F6F6F707070010000000000000"
                            ."002000000000000001027001027000000102700102700000010"
                            ."270010270000001027001027000000102700102700000010270"
                            ."010270000002027001027000000202700102700000020270010"
                            ."2700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "FF2700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "FF0039201343000008FFFFFF50010202020210100202"
                            ."6F6F6F6F6F70707001000000000000000200000000000000102"
                            ."700102700000010270010270000001027001027000000102700"
                            ."102700000010270010270000001027001027000000202700102"
                            ."70000002027001027000000202700102700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "2700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "0000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                            ."500102020202101002026F6F6F6F6F707070010000000000000"
                            ."002000000000000001027001027000000102700102700000010"
                            ."270010270000001027001027000000102700102700000010270"
                            ."010270000002027001027000000202700102700000020270010"
                            ."2700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "00802700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ),
                false,
            ),
            // Bad firmware.
            array( // #3
                array(
                    "Code" => "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                    "CodeHash" => "3424705308",
                    "Data" => "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                    "DataHash" => "3424705308",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "Version" => "1.2.3",
                    "RelStatus" => FirmwareTable::RELEASE,
                ),
                array(
                    "PhysicalSensors" => 0,
                    "SRAM" => 1024,
                    "E2" => 480,
                    "FLASH" => 14336,
                    "FLASHPAGE" => 128,
                    "PAGES" => 112,
                    "CRC" => "9891",
                ),
                "0039-21-01-A",
                "",
                "",
                false,
            ),
            array(  // #4   Running program fails
                array(
                    "Code" => "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                    "CodeHash" => "",
                    "Data" => "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                    "DataHash" => "",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "Version" => "1.2.3",
                    "RelStatus" => FirmwareTable::RELEASE,
                ),
                array(
                    "PhysicalSensors" => 0,
                    "SRAM" => 1024,
                    "E2" => 480,
                    "FLASH" => 14336,
                    "FLASHPAGE" => 128,
                    "PAGES" => 112,
                    "CRC" => "9891",
                ),
                "0039-21-01-A",
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                            ."500102020202101002026F6F6F6F6F707070010000000000000"
                            ."002000000000000001027001027000000102700102700000010"
                            ."270010270000001027001027000000102700102700000010270"
                            ."010270000002027001027000000202700102700000020270010"
                            ."2700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "2700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "0039201343000008FFFFFF50010202020210100202"
                            ."6F6F6F6F6F70707001000000000000000200000000000000102"
                            ."700102700000010270010270000001027001027000000102700"
                            ."102700000010270010270000001027001027000000202700102"
                            ."70000002027001027000000202700102700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "2700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000123",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "ABCD",
                    )
                ),
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "0000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                            ."500102020202101002026F6F6F6F6F707070010000000000000"
                            ."002000000000000001027001027000000102700102700000010"
                            ."270010270000001027001027000000102700102700000010270"
                            ."010270000002027001027000000202700102700000020270010"
                            ."2700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "00802700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "000A0039201343000008FFFFFF50010202020210100202"
                            ."6F6F6F6F6F70707001000000000000000200000000000000102"
                            ."700102700000010270010270000001027001027000000102700"
                            ."102700000010270010270000001027001027000000202700102"
                            ."70000002027001027000000202700102700000020",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "00802700102700000020270010270000002027001027000"
                            ."000202700102700000020270010270000002027001027000000F"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_WRITECRC,
                        "Data" => "",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_RUNAPPLICATION,
                        "Data" => "",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_RUNAPPLICATION,
                        "Data" => "",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command"
                            => PacketContainer::COMMAND_FINDECHOREQUEST,
                        "Data" => "",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000123",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_RUNAPPLICATION,
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
    * @param array  $firmware   This is the firmware to save
    * @param array  $driverInfo The driver info to use
    * @param string $HWPartNum  The hardware part number to use
    * @param string $read       The read string
    * @param string $write      The write string
    * @param bool   $expect     The expected return value
    *
    * @return null
    *
    * @dataProvider dataWriteProgram
    */
    public function testWriteProgram(
        $firmware, $driverInfo, $HWPartNum, $read, $write, $expect
    ) {
        // Set the packet timeout
        $this->d->DriverInfo = $driverInfo;
        $this->d->HWPartNum = $HWPartNum;
        // Set the read
        $this->socket->readString = $read;
        // Put the firmware into the database
        $this->firmware->fromAny($firmware);
        $this->firmware->insertRow();

        $ret = $this->o->writeProgram();
        $this->assertSame($write, $this->socket->writeString, "Write string Wrong");
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testReadSetup, testReadConfig
    *
    * @return array
    */
    public static function dataReadSetup()
    {
        return array(
            array(  // #0
                array(
                    array(
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "Version" => "0.1.0",
                        "Target" => "atmega16",
                    ),
                ),
                array(
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "FWVersion" => "0.0.9",
                    "Driver" => "e00392100",
                ),
                "000025",
                "000000002500392101410039200143000009FFFFFF50",
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200143000009FFFFFF50",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200143000009FFFFFF50",
                    )
                ),
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                ),
                null,
            ),
            array(   // #1
                array(
                    array(
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "Version" => "0.0.8",
                        "Target" => "atmega16",
                    ),
                ),
                array(
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "FWVersion" => "0.0.9",
                    "Driver" => "e00392100",
                ),
                "000025",
                "000000002500392101410039200143000009FFFFFF50",
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200143000009FFFFFF50",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200143000009FFFFFF50",
                    )
                ),
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                ),
                null,
            ),
            array(   // #2
                array(
                    array(
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "Version" => "0.0.8",
                        "Target" => "atmega16",
                        "RelStatus" => FirmwareTable::RELEASE,
                    ),
                ),
                array(
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "FWVersion" => "0.0.9",
                    "Driver" => "e00392101",
                    "DriverInfo" => array(
                        "SRAM" => 1024,
                        "E2" => 480,
                        "FLASH" => 14336,
                        "FLASHPAGE" => 128,
                        "PAGES" => 112,
                        "CRC" => "9891",
                    ),
                ),
                "000025",
                "000000002500392101410039200143000009FFFFFF50",
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200643000009FFFFFF50",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000025",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000025",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000025",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "ABCD",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000025",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200143000009FFFFFF50",
                    )
                ),
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000025",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "0000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000025",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "000AFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000025",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_WRITECRC,
                        "Data" => "",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000025",
                        "Command"
                            => DeviceDriverLoadableBase::COMMAND_RUNAPPLICATION,
                        "Data" => "",
                    )
                ).
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                ),
                true,
            ),
            array(  // #3
                array(),
                array(
                    "DriverInfo" => array("PacketTimeout" => 1),
                ),
                "000025",
                "000000000100392601500039260150010203FFFFFF10",
                "",
                "5A5A5A5C00002500002000595A5A5A5C0000250000200059"
                    ."5A5A5A0300002500002000065A5A5A5C0000250000200059",
                false,
            ),
            array(   // #4
                array(
                    array(
                        "HWPartNum" => "0039-21-01-A",
                        "FWPartNum" => "0039-20-01-C",
                        "Version" => "0.0.8",
                        "Target" => "atmega16",
                        "RelStatus" => FirmwareTable::RELEASE,
                    ),
                ),
                array(
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "FWVersion" => "0.0.9",
                    "Driver" => "e00392101",
                    "DriverInfo" => array(
                        "SRAM" => 1024,
                        "E2" => 480,
                        "FLASH" => 14336,
                        "FLASHPAGE" => 128,
                        "PAGES" => 112,
                        "CRC" => "9891",
                    ),
                ),
                "000025",
                "000000002500392101410039200643000009FFFFFF50",
                (string)new PacketContainer(
                    array(
                        "From" => "000025",
                        "To" => "000020",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "000000002500392101410039200643000009FFFFFF50",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "To" => "000020",
                        "From" => "000025",
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ),
                (string)new PacketContainer(
                    array(
                        "To" => "000025",
                        "From" => "000020",
                        "Command" => PacketContainer::COMMAND_GETSETUP,
                        "Data" => "",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000025",
                        "Command" => PacketContainer::COMMAND_WRITEFLASH,
                        "Data" => "0000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000025",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "000AFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000025",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "000AFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000025",
                        "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                        "Data" => "",
                    )
                ).
                (string) new PacketContainer(
                    array(
                        "From" => "000020",
                        "To" => "000025",
                        "Command" => PacketContainer::COMMAND_WRITEE2,
                        "Data" => "000AFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                            ."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF",
                    )
                ),
                false,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $firmware Firmware to load into the database
    * @param array  $device   Parameters to load into the device
    * @param string $devId    The Device ID to pretend to be
    * @param string $string   The string for the dummy device to return
    * @param string $read     The read string to put in
    * @param string $write    The write string expected
    * @param string $expect   The expected return
    *
    * @return null
    *
    * @dataProvider dataReadSetup
    */
    public function testReadSetup(
        $firmware, $device, $devId, $string, $read, $write, $expect
    ) {
        foreach ((array)$firmware as $firm) {
            $this->firmware->fromAny($firm);
            $this->firmware->insertRow();
        }
        foreach ((array)$device as $key => $val) {
            $this->d->$key = $val;
        }
        $this->d->id = hexdec($devId);
        $this->d->DeviceID = $devId;
        $this->socket->readString = $read;
        $ret = $this->o->readSetup();
        $this->assertSame($write, $this->socket->writeString, "Wrong writeString");
        $this->assertSame($string, $this->d->string, "Wrong Setup String");
        $this->assertSame($expect, $ret, "Wrong return value");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataToOutput()
    {
        return array(
            array(
                array(
                ),
                "",
                null,
                array(
                    'PhysicalSensors' => 0,
                    'VirtualSensors' => 0,
                    'CPU' => 'Atmel Mega16',
                    'SensorConfig' => 'None',
                    'bootloader' => 'Yes',
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $preload The data to preload into the class
    * @param string $setup   The setup string to use
    * @param array  $cols    The columns to use
    * @param int    $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataToOutput
    */
    public function testToOutput($preload, $setup, $cols, $expect)
    {
        $this->d->DriverInfo = array_merge(
            (array)$this->d->DriverInfo, (array)$preload
        );
        $this->o->fromSetupString($setup);
        $ret = $this->o->toOutput($cols);
        $this->assertSame(
            $expect,
            $ret
        );
    }

}

?>
