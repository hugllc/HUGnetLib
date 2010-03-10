<?php
/**
 * Tests the firmware class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2009 Hunt Utilities Group, LLC
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
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/DatabaseTest.php';
require_once dirname(__FILE__).'/../../database/Firmware.php';

/**
 * Test class for firmware.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:45:05.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class FirmwareTest extends databaseTest
{
    /** The table to use */
    protected $table = "firmware";
    /** Preload data */
    public static $preload = array(
        array(
            "FirmwareKey" => 1,
            "FirmwareVersion" => "01.02.03",
            "FirmwareCode" => "ABCDEF",
            "FirmwareData" => "010203040506070809",
            "FWPartNum" => "0039-20-01-A",
            "HWPartNum" => "0039-12",
            "Date" => "2007-12-03 02:04:21",
            "FirmwareFileType" => "SREC",
            "FirmwareStatus" => "DEV",
            "FirmwareCVSTag" => "a",
            "Target" => "attiny26",
            "FirmwareActive" => 1,
        ),
        array(
            "FirmwareKey" => 2,
            "FirmwareVersion" => "02.13.15",
            "FirmwareCode" => "0109",
            "FirmwareData" => "ABCD",
            "FWPartNum" => "0039-20-03-C",
            "HWPartNum" => "0039-28",
            "Date" => "2007-12-03 02:04:21",
            "FirmwareFileType" => "SREC",
            "FirmwareStatus" => "DEV",
            "FirmwareCVSTag" => "a",
            "Target" => "atmega168",
            "FirmwareActive" => 1,
        ),
        array(
            "FirmwareKey" => 3,
            "FirmwareVersion" => "02.10.15",
            "FirmwareCode" => "0109",
            "FirmwareData" => "ABCD",
            "FWPartNum" => "0039-20-03-C",
            "HWPartNum" => "0039-28",
            "Date" => "2007-11-25 02:04:21",
            "FirmwareFileType" => "SREC",
            "FirmwareStatus" => "DEV",
            "FirmwareCVSTag" => "a",
            "Target" => "atmega168",
            "FirmwareActive" => 1,
       ),
        array(
            "FirmwareKey" => 4,
            "FirmwareVersion" => "06.13.15",
            "FirmwareCode" => "0109",
            "FirmwareData" => "ABCD",
            "FWPartNum" => "0039-20-03-C",
            "HWPartNum" => "0039-28",
            "Date" => "2007-11-25 01:04:21",
            "FirmwareFileType" => "SREC",
            "FirmwareStatus" => "DEV",
            "FirmwareCVSTag" => "a",
            "Target" => "atmega168",
            "FirmwareActive" => 0,
       ),
    );

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
        parent::setUp();
        $this->o =& HUGnetDB::getInstance("Firmware", $this->config);
        $this->o->createTable();

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

        parent::tearDown();
        unset($this->o);
    }
    /**
     * data provider
     *
     * @return array
     */
    public static function dataInterpSREC()
    {
        return array(
            array(
                "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                512,
                512,
                "FF",
                array(
                    "FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF500102020202101002026F6F6F6F6F70707001000000000000000200000000000000102700102700000010270010270000001027001027000000102700102700000010270010270000001027001027000000202700102700000020270010270000002027001027000000202700102700000020270010270000002027001027000000202700102700000020270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF",
               ),
           ),
            array(
                "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                512,
                0,
                "FF",
                "FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF500102020202101002026F6F6F6F6F70707001000000000000000200000000000000102700102700000010270010270000001027001027000000102700102700000010270010270000001027001027000000202700102700000020270010270000002027001027000000202700102700000020270010270000002027001027000000202700102700000020270010270000002027001027000000",
           ),
            array(
                "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                512,
                64,
                "FF",
                array(
                    0 => "FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF500102020202101002026F6F6F6F6F70707001000000000000000200000000000000102700102700000010",
                    1 => "27001027000000102700102700000010270010270000001027001027000000102700102700000020270010270000002027001027000000202700102700000020",
                    2 => "2700102700000020270010270000002027001027000000202700102700000020270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF",
               ),
           ),
        );
    }

    /**
     * test
     *
     * @param string $srec           The S record to change.
     * @param int    $MemBufferSize  The total available space in the memory buffer
     * @param int    $PageSize       The total number of bytes in 1 page of memory.
     *                               0 means no pages
     * @param string $MemBufferEmpty This is what a byte looks like when it is
     *                               erased. The default is for flash memory (FF);
     * @param mixed  $expect         The return value to expect.
     *
     * @return null
     *
     * @dataProvider dataInterpSREC().
     */
    public function testInterpSREC($srec,
                                   $MemBufferSize,
                                   $PageSize,
                                   $MemBufferEmpty,
                                   $expect)
    {
        $ret = $this->o->interpSREC($srec,
                                    $MemBufferSize,
                                    $PageSize,
                                    $MemBufferEmpty);
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testGetLatestFirmware
     *
     * @return array
     */
    public static function dataGetLatestFirmware()
    {
        return array(
            array(
                firmwareTest::$preload,
                "0039-20-03-C",
                null,
                false,
                array(
                    "FirmwareKey" => "2",
                    "FirmwareVersion" => "02.13.15",
                    "FirmwareCode" => "0109",
                    "FirmwareData" => "ABCD",
                    "FWPartNum" => "0039-20-03-C",
                    "HWPartNum" => "0039-28",
                    "Date" => "2007-12-03 02:04:21",
                    "FirmwareFileType" => "SREC",
                    "FirmwareStatus" => "DEV",
                    "FirmwareCVSTag" => "a",
                    "Target" => "atmega168",
                    "FirmwareActive" => '1',
               ),
           ),
            array(
                firmwareTest::$preload,
                "0039-20-01-C",
                null,
                false,
                null,
           ),
            array(
                firmwareTest::$preload,
                "0039-20-03-C",
                "DEV",
                false,
                array(
                    "FirmwareKey" => "2",
                    "FirmwareVersion" => "02.13.15",
                    "FirmwareCode" => "0109",
                    "FirmwareData" => "ABCD",
                    "FWPartNum" => "0039-20-03-C",
                    "HWPartNum" => "0039-28",
                    "Date" => "2007-12-03 02:04:21",
                    "FirmwareFileType" => "SREC",
                    "FirmwareStatus" => "DEV",
                    "FirmwareCVSTag" => "a",
                    "Target" => "atmega168",
                    "FirmwareActive" => '1',
               ),
           ),
        );
    }

    /**
     * Test firmware::getLastestFirmware
     *
     * @param array  $preload   The database data to preload into the test fixture
     * @param string $FWPartNum This is the part number of the firmware wanted
     * @param string $Status    This is the status of the firmware
     * @param bool   $All       If this is true any firmware not listed as BAD is
     *                          returned
     * @param array  $expect    The output to expect
     *
     * @return array The array of firmware information
     *
     * @dataProvider dataGetLatestFirmware().
     */
    public function testGetLatestFirmware($preload,
                                          $FWPartNum,
                                          $Status,
                                          $All,
                                          $expect)
    {
        $this->load($preload);
        $ret = $this->o->getLatestFirmware($FWPartNum, $Status, $All);
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testGetLatestFirmware
     *
     * @return array
     */
    public static function dataGetFirmwareFor()
    {
        return array(
            array(
                firmwareTest::$preload,
                "0039-28-01-A",
                null,
                1,
                array(
                    array(
                        "FirmwareKey" => "2",
                        "FirmwareVersion" => "02.13.15",
                        "FirmwareCode" => "0109",
                        "FirmwareData" => "ABCD",
                        "FWPartNum" => "0039-20-03-C",
                        "HWPartNum" => "0039-28",
                        "Date" => "2007-12-03 02:04:21",
                        "FirmwareFileType" => "SREC",
                        "FirmwareStatus" => "DEV",
                        "FirmwareCVSTag" => "a",
                        "Target" => "atmega168",
                        "FirmwareActive" => '1',
                    ),
                    array(
                        "FirmwareKey" => "3",
                        "FirmwareVersion" => "02.10.15",
                        "FirmwareCode" => "0109",
                        "FirmwareData" => "ABCD",
                        "FWPartNum" => "0039-20-03-C",
                        "HWPartNum" => "0039-28",
                        "Date" => "2007-11-25 02:04:21",
                        "FirmwareFileType" => "SREC",
                        "FirmwareStatus" => "DEV",
                        "FirmwareCVSTag" => "a",
                        "Target" => "atmega168",
                        "FirmwareActive" => '1',
                    ),
                ),
            ),
            array(
                firmwareTest::$preload,
                "0039-20",
                null,
                1,
                array(),
           ),
            array(
                firmwareTest::$preload,
                "0039-28-01-A",
                "DEV",
                1,
                array(
                    array(
                        "FirmwareKey" => "2",
                        "FirmwareVersion" => "02.13.15",
                        "FirmwareCode" => "0109",
                        "FirmwareData" => "ABCD",
                        "FWPartNum" => "0039-20-03-C",
                        "HWPartNum" => "0039-28",
                        "Date" => "2007-12-03 02:04:21",
                        "FirmwareFileType" => "SREC",
                        "FirmwareStatus" => "DEV",
                        "FirmwareCVSTag" => "a",
                        "Target" => "atmega168",
                        "FirmwareActive" => '1',
                    ),
                    array(
                        "FirmwareKey" => "3",
                        "FirmwareVersion" => "02.10.15",
                        "FirmwareCode" => "0109",
                        "FirmwareData" => "ABCD",
                        "FWPartNum" => "0039-20-03-C",
                        "HWPartNum" => "0039-28",
                        "Date" => "2007-11-25 02:04:21",
                        "FirmwareFileType" => "SREC",
                        "FirmwareStatus" => "DEV",
                        "FirmwareCVSTag" => "a",
                        "Target" => "atmega168",
                        "FirmwareActive" => '1',
                    ),
                ),
            ),
            array(
                firmwareTest::$preload,
                "0039-28-01-A",
                "DEV",
                0,
                array(
                    array(
                        "FirmwareKey" => "4",
                        "FirmwareVersion" => "06.13.15",
                        "FirmwareCode" => "0109",
                        "FirmwareData" => "ABCD",
                        "FWPartNum" => "0039-20-03-C",
                        "HWPartNum" => "0039-28",
                        "Date" => "2007-11-25 01:04:21",
                        "FirmwareFileType" => "SREC",
                        "FirmwareStatus" => "DEV",
                        "FirmwareCVSTag" => "a",
                        "Target" => "atmega168",
                        "FirmwareActive" => '0',
                   ),
                ),
            ),
        );
    }
    /**
     * This get the firmware for a particular piece of hardware.
     *
     * @param array  $preload   The database data to preload into the test fixture
     * @param string $HWPartNum This is the part number of the firmware wanted
     * @param string $Status    This is the status of the firmware
     * @param array  $expect    The output to expect
     *
     * @return array The array of firmware information
     *
     * @dataProvider dataGetFirmwareFor().
     */
    public function testGetFirmwareFor($preload, $HWPartNum, $Status, $Active, $expect)
    {
        $this->load($preload);
        $ret = $this->o->getFirmwareFor($HWPartNum, $Status, $Active);
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testGetLatestFirmware
     *
     * @return array
     */
    public static function dataGetFirmware()
    {
        return array(
            array(
                firmwareTest::$preload,
                "0039-20-03-C",
                null,
                null,
                1,
                array(
                    array(
                        "FirmwareKey" => "2",
                        "FirmwareVersion" => "02.13.15",
                        "FirmwareCode" => "0109",
                        "FirmwareData" => "ABCD",
                        "FWPartNum" => "0039-20-03-C",
                        "HWPartNum" => "0039-28",
                        "Date" => "2007-12-03 02:04:21",
                        "FirmwareFileType" => "SREC",
                        "FirmwareStatus" => "DEV",
                        "FirmwareCVSTag" => "a",
                        "Target" => "atmega168",
                        "FirmwareActive" => '1',
                    ),
                    array(
                        "FirmwareKey" => "3",
                        "FirmwareVersion" => "02.10.15",
                        "FirmwareCode" => "0109",
                        "FirmwareData" => "ABCD",
                        "FWPartNum" => "0039-20-03-C",
                        "HWPartNum" => "0039-28",
                        "Date" => "2007-11-25 02:04:21",
                        "FirmwareFileType" => "SREC",
                        "FirmwareStatus" => "DEV",
                        "FirmwareCVSTag" => "a",
                        "Target" => "atmega168",
                        "FirmwareActive" => '1',
                   ),
               ),
           ),
            array(
                firmwareTest::$preload,
                "0039-20-01-C",
                null,
                false,
                null,
                array(),
           ),
            array(
                firmwareTest::$preload,
                "0039-20-03-C",
                "02.13.15",
                "DEV",
                1,
                array(
                    array(
                        "FirmwareKey" => "2",
                        "FirmwareVersion" => "02.13.15",
                        "FirmwareCode" => "0109",
                        "FirmwareData" => "ABCD",
                        "FWPartNum" => "0039-20-03-C",
                        "HWPartNum" => "0039-28",
                        "Date" => "2007-12-03 02:04:21",
                        "FirmwareFileType" => "SREC",
                        "FirmwareStatus" => "DEV",
                        "FirmwareCVSTag" => "a",
                        "Target" => "atmega168",
                        "FirmwareActive" => '1',
                   ),
               ),
           ),
            array(
                firmwareTest::$preload,
                "0039-20-03-C",
                "02.13.15",
                null,
                null,
                array(
                    array(
                        "FirmwareKey" => "2",
                        "FirmwareVersion" => "02.13.15",
                        "FirmwareCode" => "0109",
                        "FirmwareData" => "ABCD",
                        "FWPartNum" => "0039-20-03-C",
                        "HWPartNum" => "0039-28",
                        "Date" => "2007-12-03 02:04:21",
                        "FirmwareFileType" => "SREC",
                        "FirmwareStatus" => "DEV",
                        "FirmwareCVSTag" => "a",
                        "Target" => "atmega168",
                        "FirmwareActive" => '1',
                   ),
               ),
           ),
            array(
                firmwareTest::$preload,
                "0039-20-03-C",
                "06.13.15",
                null,
                0,
                array(
                    array(
                        "FirmwareKey" => "4",
                        "FirmwareVersion" => "06.13.15",
                        "FirmwareCode" => "0109",
                        "FirmwareData" => "ABCD",
                        "FWPartNum" => "0039-20-03-C",
                        "HWPartNum" => "0039-28",
                        "Date" => "2007-11-25 01:04:21",
                        "FirmwareFileType" => "SREC",
                        "FirmwareStatus" => "DEV",
                        "FirmwareCVSTag" => "a",
                        "Target" => "atmega168",
                        "FirmwareActive" => '0',
                   ),
               ),
           ),
        );
    }

    /**
     * test
     *
     * @param array  $preload   Data to preload into the database
     * @param string $FWPartNum This is the part number of the firmware wanted
     * @param string $version   The particular version to get
     * @param string $Status    This is the status of the firmware
     * @param array  $expect    The data we expect returned
     *
     * @return null
     *
     * @dataProvider dataGetFirmware().
     */
    public function testGetFirmware($preload, $FWPartNum, $version, $Status, $Active, $expect)
    {
        $this->load($preload);
        $ret = $this->o->getFirmware($FWPartNum, $version, $Status, $Active);
        $this->assertSame($expect, $ret);
    }

}

?>
