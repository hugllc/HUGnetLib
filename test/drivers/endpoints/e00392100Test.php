<?php
/**
 * Tests the 00392100 endpoint class
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
 * @subpackage Drivers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */


require_once dirname(__FILE__).'/../endpointTestBase.php';
require_once dirname(__FILE__).'/../../../drivers/endpoints/e00392100.php';

/**
 * Test class for endpoints.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:06:08.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Drivers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class E00392100Test extends EndpointTestBase
{
    public $class = "e00392100";
    /**
     *  Test cases for the interpConfig routine
     */
    /**
     * Data provider
     *
     * @return array
     */
    static public function dataInterpConfig()
    {
        return array(
            array(
                "Info" => array(
                    "RawSetup" => "00000000CC00392101410039200143000007FFFFFF50FF",
                    "DriverInfo" => "FF",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "FWVersion" => "00.00.07",
                    "DeviceID" => "0000CC",
                    "SerialNum" => 204,
                    "DeviceGroup" => "FFFFFF",
                    "RawData" => array(
                        "5C" => "00000000CC00392101410039200143000007FFFFFF50FF",
                        "56" => "0000FE0000FC00000000000000000000000000000000000000"
                            ."00000000000000000000000000000000000000000000000000000"
                            ."00000000000000000000000000000000000000000000000000000"
                            ."00000000000000000000000000000000000000000000000000000"
                            ."00000000000000000000000000000000000000000000000000000"
                            ."00000000000000000000000000000000000000000000000000000"
                            ."000000000000000000000000000000000000000000000",
                        "60" => "0801",
                   ),
               ),
                "Return" => array(
                    "RawSetup" => "00000000CC00392101410039200143000007FFFFFF50FF",
                    "DriverInfo" => "FF",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "FWVersion" => "00.00.07",
                    "DeviceID" => "0000CC",
                    "SerialNum" => 204,
                    "DeviceGroup" => "FFFFFF",
                    "RawData" => array(
                        "5C" => "00000000CC00392101410039200143000007FFFFFF50FF",
                        "56" => "0000FE0000FC00000000000000000000000000000000000000"
                            ."00000000000000000000000000000000000000000000000000000"
                            ."00000000000000000000000000000000000000000000000000000"
                            ."00000000000000000000000000000000000000000000000000000"
                            ."00000000000000000000000000000000000000000000000000000"
                            ."00000000000000000000000000000000000000000000000000000"
                            ."000000000000000000000000000000000000000000000",
                        "60" => "0801"
                   ),
                    "HWName" => "Controller Board",
                    "Labels" => array(
                        "HUGnet1 Voltage", "HUGnet1 Current", "FET Temp",
                        "HUGnet2 Voltage", "HUGnet2 Current", "FET Temp"
                    ),
                    "PacketTimeout" => 2,
                    "NumSensors" => 6,
                    "Function" => "HUGnet Controller",
                    "ActiveSensors" => 6,
                    "bootLoader" => false,
                    "Types" => array(64, 80, 2, 64, 80, 2),
                    "subDevices" => array(
                        array("0000FE", "0000FC"),
                   ),
                    "HUGnetPower" => array(1, 1),
                ),
            ),
            array(
                "Info" => array(
                    "RawSetup" => "00000000B600392101410039200643000002EEEEEE"
                        ."50040001E000380000808055",
                    "DriverInfo" => "040001E000380000808055",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-06-C",
                    "FWVersion" => "00.00.02",
                    "DeviceID" => "0000B6",
                    "SerialNum" => 182,
                    "DeviceGroup" => "EEEEEE",
               ),
                "Return" => array(
                    "RawSetup" => "00000000B600392101410039200643000002EEEEEE"
                        ."50040001E000380000808055",
                    "DriverInfo" => "040001E000380000808055",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-06-C",
                    "FWVersion" => "00.00.02",
                    "DeviceID" => "0000B6",
                    "SerialNum" => 182,
                    "DeviceGroup" => "EEEEEE",
                    "HWName" => "Controller Board",
                    "Labels" => array(
                        "HUGnet1 Voltage", "HUGnet1 Current", "FET Temp",
                        "HUGnet2 Voltage", "HUGnet2 Current", "FET Temp"
                    ),
                    "PacketTimeout" => 2,
                    "NumSensors" => 6,
                    "Function" => "HUGnet Controller",
                    "ActiveSensors" => 6,
                    'mcu' => array(
                        "SRAM" => 0x400,
                        "E2" => 0x1E0,
                        "FLASH" => 0x3800,
                        "FLASHPAGE" => 128,
                        "PAGES" => 112,
                   ),
                    "CRC" => "8055",
                    "bootLoader" => true,
                    "Types" => array(64, 80, 2, 64, 80, 2),
                ),
            ),
        );
    }
    /**
     * Data provider
     *
     * @return array
     */
    static public function dataInterpSensors()
    {
        return array(
            array(
                "Info" => array(
                    "DeviceKey" => 150,
                    "RawSetup" => "00000000CC00392101410039200143000007FFFFFF50FF",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "FWVersion" => "00.00.07",
                    "DeviceID" => "0000CC",
                    "SerialNum" => 204,
                    "DeviceGroup" => "FFFFFF",
                    "HWName" => "Controller Board",
                    "Labels" => array(
                        "HUGnet1 Voltage", "HUGnet1 Current", "FET Temp",
                        "HUGnet2 Voltage", "HUGnet2 Current", "FET Temp"
                    ),
                    "PacketTimeout" => 2,
                    "NumSensors" => 6,
                    "Function" => "HUGnet Controller",
                    "ActiveSensors" => 6,
                    "params" => array(
                        "sensorType" => array(
                            "Controller", "Controller", "BCTherm2322640",
                            "Controller", "Controller", "BCTherm2322640"
                        ),
                    ),
                    "bootLoader" => false,
                    "Types" => array(64, 80, 2, 64, 80, 2),
                    "unitType" => array(
                        "Voltage", "Current", "Temperature",
                        "Voltage", "Current", "Temperature"
                    ),
                    "Labels" => array(
                        "Voltage", "Current", "Temperature",
                        "Voltage", "Current", "Temperature"
                    ),
                    "Units" => array("V", "mA", "&#176;C", "V", "mA", "&#176;C"),
                    "dType" => array("raw", "raw", "raw", "raw", "raw", "raw"),
                    "doTotal" => array(false, false, false, false, false, false),
               ),
                "Packets" => array(
                    array(
                        "RawData" => "0BBE003F1E0000004882470000BF1D0B01",
                        "sendCommand" => "55",
                        "Date" => "2007-02-23 22:38:01",
                   ),
                    array(
                        "RawData" => "000000000000000000000000000000000000000000000"
                            ."00000000000000000000000010000000000000001000000010001"
                            ."000000000000000D0000000000000044000C000000000000000D0"
                            ."000000D000D000000D5090000B602000000000000",
                        "sendCommand" => "57",
                        "Date" => "2007-02-23 22:38:01",
                   ),
               ),
                "Return" => array(
                    array(
                        "RawData" => "0BBE003F1E0000004882470000BF1D0B01",
                        "sendCommand" => "55",
                        "Date" => "2007-02-23 22:38:01",
                        "Data" => array(
                            11, 190, 0, 63, 30, 0, 0, 0, 72, 130,
                            71, 0, 0, 191, 29, 11, 1
                        ),
                        "Driver" => "e00392100",
                        "DeviceKey" => 150,
                        "ActiveSensors" => 6,
                        "NumSensors" => 6,
                        "TimeConstant" => 1,
                        "Types" => array(64, 80, 2, 64, 80, 2),
                        "Units" => array("V", "mA", "&#176;C", "V", "mA", "&#176;C"),
                        "unitType" => array(
                            "Voltage", "Current", "Temperature",
                            "Voltage", "Current", "Temperature"
                        ),
                        "DataIndex" => 11,
                        "raw" => array(190, 7743, 0, 18432, 18306, 0, 7615, 267),
                        "Data0" => 10.718,
                        "Data1" => 5.8,
                        "Data2" => 78.5627,
                        "Data3" => 10.7918,
                        "Data4" => 4.1,
                        "Data5" => 77.9861,
                        "Status" => "GOOD",
                        "StatusOld" => "GOOD",
                   ),
                    array(
                        "RawData" => "000000000000000000000000000000000000000000000"
                            ."00000000000000000000000010000000000000001000000010001"
                            ."000000000000000D0000000000000044000C000000000000000D0"
                            ."000000D000D000000D5090000B602000000000000",
                        "sendCommand" => "57",
                        "Date" => "2007-02-23 22:38:01",
                        "Data" => array(
                            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
                            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0,
                            0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0,
                            0, 0, 13, 0, 0, 0, 0, 0, 0, 0, 68, 0, 12, 0, 0, 0, 0,
                            0, 0, 0, 13, 0, 0, 0, 13, 0, 13, 0, 0, 0, 213, 9, 0,
                            0, 182, 2, 0, 0, 0, 0, 0, 0
                        ),
                        "Driver" => "e00392100",
                        "DeviceKey" => 150,
                        "Stats" => array(
                            array(
                                "PacketRX" => 0,
                                "PacketTX" => 0,
                                "PacketTimeout" => 0,
                                "PacketNoBuffer" => 0,
                                "PacketBadCSum" => 0,
                                "PacketSent" => 0,
                                "PacketGateway" => 0,
                                "PacketStartTX1" => 0,
                                "PacketStartTX2" => 0,
                                "PacketBadIface" => 0,
                                "ByteRX" => 0,
                                "ByteTX" => 0,
                                "ByteTX2" => 0,
                           ),
                            array(
                                "PacketRX" => 0,
                                "PacketTX" => 1,
                                "PacketTimeout" => 0,
                                "PacketNoBuffer" => 0,
                                "PacketBadCSum" => 0,
                                "PacketSent" => 1,
                                "PacketGateway" => 0,
                                "PacketStartTX1" => 1,
                                "PacketStartTX2" => 1,
                                "PacketBadIface" => 0,
                                "ByteRX" => 0,
                                "ByteTX" => 13,
                                "ByteTX2" => 0,
                           ),
                            array(
                                "PacketRX" => 68,
                                "PacketTX" => 12,
                                "PacketTimeout" => 0,
                                "PacketNoBuffer" => 0,
                                "PacketBadCSum" => 0,
                                "PacketSent" => 13,
                                "PacketGateway" => 0,
                                "PacketStartTX1" => 13,
                                "PacketStartTX2" => 13,
                                "PacketBadIface" => 0,
                                "ByteRX" => 2517,
                                "ByteTX" => 694,
                                "ByteTX2" => 0,
                           ),
                       ),
                        "Status" => "GOOD",
                   ),
               ),
           ),
        );
    }
    /**
     * data provider
     *
     * @return array
     */
    public static function dataDevicesFirmware()
    {
        return parent::devicesArrayDataSource("e00392100", "fw");
    }
    /**
     * data provider
     *
     * @return array
     */
    public static function dataDevicesHardware()
    {
        return parent::devicesArrayDataSource("e00392100", "hw");
    }
    /**
     * data provider
     *
     * @return array
     */
    public static function dataDevicesVersion()
    {
        return parent::devicesArrayDataSource("e00392100", "ver");
    }
    /**
     * data provider
     *
     * @return array
     */
    public static function dataConfigArray()
    {
        return parent::dataConfigArray("e00392100");
    }
    /**
     * data provider for test readConfig
     *
     * @return array
     */
    public static function datareadSensors()
    {
        return array(
            array(
                array("DeviceID" => "000025"),
                array(
                    array("To" => "000025", "Command" => "55"),
               ),
           ),
            array(
                array("DeviceID" => "000025", "FWPartNum" => "0039-20-06-C"),
                array(
                    array("To" => "000025", "Command" => "55"),
               ),
           ),
            array(
                array("DeviceID" => "000025", "FWPartNum" => "0039-20-01-C"),
                array(
                    array("To" => "000025", "Command" => "55"),
                    array("To" => "000025", "Command" => "57"),
               ),
           ),
        );
    }
    /**
     * data provider for test readConfig
     *
     * @return array
     */
    public static function datareadConfig()
    {
        return array(
            array(
                array("DeviceID" => "000025"),
                array(
                    array("To" => "000025", "Command" => "5C"),
                    array("To" => "000025", "Command" => "4C"),
               ),
           ),
            array(
                array("DeviceID" => "000025", "FWPartNum" => "0039-20-06-C"),
                array(
                    array("To" => "000025", "Command" => "5C"),
                    array("To" => "000025", "Command" => "4C"),
               ),
           ),
            array(
                array("DeviceID" => "000025", "FWPartNum" => "0039-20-01-C"),
                array(
                    array("To" => "000025", "Command" => "5C"),
                    array("To" => "000025", "Command" => "4C"),
                    array("To" => "000025", "Command" => "60"),
                    array("To" => "000025", "Command" => "56"),
               ),
           ),
        );
    }
    /**
     * data provider for testCheckDataArray
     *
     * @return array
     */
    public static function dataGetFWPartNum()
    {
        return array(
            array(
                array("HWPartNum" => "0039-21-01-A"),
                "0039-20-01-C",
            ),
            array(
                array("HWPartNum" => "0039-21-01-Q"),
                "0039-20-01-C",
            ),
            array(
                array("HWPartNum" => "0039-21-02-A"),
                "0039-20-14-C",
            ),
        );
    }
    /**
     * test
     *
     * @param array $Info   devInfo array for the device
     * @param array $expect return array expected
     *
     * @return null
     *
     * @dataProvider dataGetFWPartNum()
     */
    function testGetFWPartNum($Info, $expect)
    {
        $work = $this->o->getFWPartNum($Info);
        $this->assertSame($expect, $work);
    }

}

?>
