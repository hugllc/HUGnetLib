<?php
/**
 * Tests the 00392800 endpoint class
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
require_once dirname(__FILE__).'/../../../drivers/endpoints/e00392800.php';

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
class E00392800Test extends EndpointTestBase
{
    public $class = "e00392800";
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
                    "RawSetup" => "00000000E800392801410039201343000005FFFFFF"
                        ."500102020202020202027070707070707070",
                    "DriverInfo" => "0102020202020202027070707070707070",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "FWVersion" => "00.00.05",
                    "DeviceID" => "0000E8",
                    "SerialNum" => 232,
                    "Driver" => "e00392800",
                    "DeviceGroup" => "FFFFFF",
                    "params" => array(
                         "VSensors" => 2,
                    ),
                ),
                "Return" => array(
                    "RawSetup" => "00000000E800392801410039201343000005FFFFFF"
                        ."500102020202020202027070707070707070",
                    "DriverInfo" => "0102020202020202027070707070707070",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "FWVersion" => "00.00.05",
                    "DeviceID" => "0000E8",
                    "SerialNum" => 232,
                    "Driver" => "e00392800",
                    "DeviceGroup" => "FFFFFF",
                    "HWName" => "0039-28 Endpoint",
                    "NumSensors" => 16,
                    "Function" => "Sensor Board",
                    "TimeConstant" => 1,
                    "DriverInfo" => "0102020202020202027070707070707070",
                    "Types" => array(
                        2,2,2,2,2,2,2,2,
                        112,112,112,112,112,112,112,112
                    ),
                    "TotalSensors" => 18,
                ),
            ),
            array(
                "Info" => array(
                    "RawSetup" => "00000000E800392801410039201343000005FFFFFF"
                        ."5000102020202020202026F6F6F6F6F707070",
                    "DriverInfo" => "0102020202020202026F6F6F6F6F707070",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "FWVersion" => "00.00.08",
                    "DeviceID" => "0000E8",
                    "SerialNum" => 232,
                    "Driver" => "e00392800",
                    "DeviceGroup" => "FFFFFF",
                ),
                "Return" => array(
                    "RawSetup" => "00000000E800392801410039201343000005FFFFFF"
                        ."5000102020202020202026F6F6F6F6F707070",
                    "DriverInfo" => "0102020202020202026F6F6F6F6F707070",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "FWVersion" => "00.00.08",
                    "DeviceID" => "0000E8",
                    "SerialNum" => 232,
                    "Driver" => "e00392800",
                    "DeviceGroup" => "FFFFFF",
                    "HWName" => "0039-28 Endpoint",
                    "NumSensors" => 16,
                    "Function" => "Sensor Board",
                    "TimeConstant" => 1,
                    "Types" => array(
                        2,2,2,2,2,2,2,2,
                        111,111,111,111,111,112,112,112
                    ),
                    "dType" => array(
                        "raw", "raw", "raw", "raw", "raw", "raw", "raw", "raw",
                        "raw", "ignore", "ignore", "ignore", "ignore", "diff",
                        "diff", "diff"
                    ),
                    "params" => array(
                        "sensorType" => array(
                            "BCTherm2322640",
                            "BCTherm2322640",
                            "BCTherm2322640",
                            "BCTherm2322640",
                            "BCTherm2322640",
                            "BCTherm2322640",
                            "BCTherm2322640",
                            "BCTherm2322640",
                            "maximum-inc",
                            "maximum-inc",
                            "maximum-inc",
                            "maximum-inc",
                            "maximum-inc",
                            "generic",
                            "generic",
                            "generic"
                        ),
                        "dType" => array(
                            9 => "ignore",
                            10 => "ignore",
                            11 => "ignore",
                            12 => "ignore"
                        ),
                    ),
                    "TotalSensors" => 16,
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
                    "DeviceKey" => 182,
                    "RawSetup" => "00000000E800392801410039201343000005FFFFFF"
                        ."500102020202020202027070707070707070",
                    "DriverInfo" => "0102020202020202027070707070707070",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "FWVersion" => "00.00.05",
                    "DeviceID" => "0000E8",
                    "SerialNum" => 232,
                    "Driver" => "e00392800",
                    "DeviceGroup" => "FFFFFF",
                    "HWName" => "0039-28 Endpoint",
                    "NumSensors" => 16,
                    "ActiveSensors" => 7,
                    "Function" => "Sensor Board",
                    "TimeConstant" => 1,
                    "DriverInfo" => "0102020202020202027070707070707070",
                    "Types" => array(
                        2,2,2,2,2,2,2,2,
                        112,112,112,112,112,112,112,112
                    ),
                ),
                "Packets" => array(
                    array(
                        "RawData" => "070001369E00F09C00919D00C89C00EE9"
                            ."C000A9D00BEFF00BFFF00000000",
                        "sendCommand" => "55",
                        "Date" => "2007-02-23 22:38:01",
                    ),
                    array(
                        "RawData" => "130001309E00F09C00909D00CA9C00F19C"
                            ."000A9D00BFFF00BFFF00000000",
                        "sendCommand" => "55",
                        "Date" => "2007-02-23 22:48:01",
                   ),
               ),
                "Return" => array(
                    array(
                        "RawData" => "070001369E00F09C00919D00C89C00EE9"
                            ."C000A9D00BEFF00BFFF00000000",
                        "sendCommand" => "55",
                        "Date" => "2007-02-23 22:38:01",
                        "Data" => array(
                            7, 0, 1, 54, 158, 0, 240, 156, 0, 145, 157, 0, 200,
                            156, 0, 238, 156, 0, 10, 157, 0, 190, 255, 0, 191, 255,
                            0, 0, 0, 0
                        ),
                        "NumSensors" => 16,
                        "ActiveSensors" => 7,
                        "Driver" => "e00392800",
                        "DeviceKey" => 182,
                        "Types" => array(
                            2,2,2,2,2,2,2,2,
                            112,112,112,112,112,112,112,112
                        ),
                        "DataIndex" => 7,
                        "TimeConstant" => 1,
                        "raw" => array(
                            40502, 40176, 40337, 40136, 40174, 40202, 65470, 65471,
                            0, 0, 0, 0, 0, 0, 0, 0
                        ),
                        "Data0" => 14.2962,
                        "data" => array(
                            14.2962, 14.7482, 14.5252, 14.8037, 14.751, 14.7122,
                            null, null, null, null, null, null, null, null,
                            null, null
                        ),
                        "Data1" => 14.7482,
                        "Data2" => 14.5252,
                        "Data3" => 14.8037,
                        "Data4" => 14.751,
                        "Data5" => 14.7122,
                        "Data6" => null,
                        "Data7" => null,
                        "Data8" => null,
                        "deltaT" => 0,
                        "Status" => "GOOD",
                        "StatusOld" => "GOOD",
                    ),
                    array(
                        "RawData" => "130001309E00F09C00909D00CA9C00F19C0"
                            ."00A9D00BFFF00BFFF00000000",
                        "sendCommand" => "55",
                        "Date" => "2007-02-23 22:48:01",
                        "Data" => array(
                            19, 0, 1, 48, 158, 0, 240, 156, 0, 144, 157, 0, 202,
                            156, 0, 241, 156, 0, 10, 157, 0, 191, 255, 0, 191,
                            255, 0, 0, 0, 0
                        ),
                        "NumSensors" => 16,
                        "ActiveSensors" => 7,
                        "Driver" => "e00392800",
                        "DeviceKey" => 182,
                        "Types" => array(
                            2, 2, 2, 2, 2, 2, 2, 2,
                            112,112,112, 112,112,112,112,112
                        ),
                        "DataIndex" => 19,
                        "TimeConstant" => 1,
                        "raw" => array(
                            40496, 40176, 40336, 40138, 40177, 40202, 65471, 65471,
                            0, 0, 0, 0, 0, 0, 0, 0
                        ),
                        "Data0" => 14.3045,
                        "data" => array(
                            14.3045, 14.7482, 14.5265, 14.8009, 14.7469, 14.7122,
                            null, null, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0
                        ),
                        "Data1" => 14.7482,
                        "Data2" => 14.5265,
                        "Data3" => 14.8009,
                        "Data4" => 14.7469,
                        "Data5" => 14.7122,
                        "Data6" => null,
                        "Data7" => null,
                        "deltaT" => 600,
                        "Data8" => 0.0,
                        "Status" => "GOOD",
                        "StatusOld" => "GOOD",
                   ),
               ),
            ),
            array(
                "Info" => array(
                     "DeviceKey" => 178,
                     "RawSetup" => "00000000E400392801410039201343000008FFFFFF"
                        ."500102020202101002026F6F6F6F6F707070",
                     "DriverInfo" => "0102020202020202027070707070707070",
                     "HWPartNum" => "0039-28-01-A",
                     "FWPartNum" => "0039-20-13-C",
                     "FWVersion" => "00.00.08",
                     "DeviceID" => "0000E4",
                     "SerialNum" => 228,
                     "Driver" => "e00392800",
                     "DeviceGroup" => "FFFFFF",
                     "HWName" => "0039-28 Endpoint",
                     "NumSensors" => 16,
                     "ActiveSensors" => 15,
                     "Function" => "Sensor Board",
                     "TimeConstant" => 1,
                     "DriverInfo" => "0102020202101002026F6F6F6F6F707070",
                     "Types" => array(
                        2,2,2,2,16,16,2,2,
                        111,111,111,111,111,112,112,112
                    ),
                ),
                "Packets" => array(
                    array(
                        "RawData" => "330001C0FF00C0FF00C0FF00C0FF00C0FF00C0FF00C0"
                            ."FF00C0FF001010100000000000000000000000000401"
                            ."00000000000000",
                        "sendCommand" => "55",
                        "Date" => "2007-02-23 22:38:01",
                   ),
               ),
                "Return" => array(
                    array(
                        "RawData" => "330001C0FF00C0FF00C0FF00C0FF00C0FF00C0FF00C0"
                            ."FF00C0FF001010100000000000000000000000000401"
                            ."00000000000000",
                        "sendCommand" => "55",
                        "Date" => "2007-02-23 22:38:01",
                        "Data" => array(
                            51, 0, 1, 192, 255, 0, 192, 255, 0, 192, 255, 0, 192,
                            255, 0, 192, 255, 0, 192, 255, 0, 192, 255, 0, 192,
                            255, 0, 16, 16, 16, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
                            0, 4, 1, 0, 0, 0, 0, 0, 0, 0
                        ),
                        "NumSensors" => 16,
                        "ActiveSensors" => 15,
                        "Driver" => "e00392800",
                        "DeviceKey" => 178,
                        "Types" => array(
                            2, 2, 2, 2, 16, 16, 2, 2,
                            111, 111, 111, 111, 111, 112, 112, 112
                        ),
                        "DataIndex" => 51,
                        "TimeConstant" => 1,
                        "raw" => array(
                            65472, 65472, 65472, 65472, 65472, 65472, 65472, 65472,
                            16, 0, 0, 0, 0, 260, 0, 0
                        ),
                        "Data0" => null,
                        "data" => array(
                            null, null, null, null, 110.0, 110.0,
                            null, null, 180.0,
                            13 => null,
                            14 => null,
                            15 => null
                        ),
                        "Data1" => null,
                        "Data2" => null,
                        "Data3" => null,
                        "Data4" => 110.0,
                        "Data5" => 110.0,
                        "Data6" => null,
                        "Data7" => null,
                        "Data8" => 180.0,
                        "deltaT" => 0,
                        "Status" => "GOOD",
                        "StatusOld" => "GOOD",
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
        return parent::devicesArrayDataSource("e00392800", "fw");
    }
    /**
     * data provider
     *
     * @return array
     */
    public static function dataDevicesHardware()
    {
        return parent::devicesArrayDataSource("e00392800", "hw");
    }
    /**
     * data provider
     *
     * @return array
     */
    public static function dataDevicesVersion()
    {
        return parent::devicesArrayDataSource("e00392800", "ver");
    }
    /**
     * data provider
     *
     * @return array
     */
    public static function dataConfigArray()
    {
        return parent::dataConfigArray("e00392800");
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
               ),
           ),
        );
    }

}

?>
