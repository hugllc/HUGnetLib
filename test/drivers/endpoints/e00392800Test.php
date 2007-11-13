<?php
/**
 *   Tests the 00392800 endpoint class
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Test
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$
 *
 */

// Call e00392800Test::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "00392800Test::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../endpointTestBase.php';
require_once dirname(__FILE__).'/../../../drivers/endpoints/e00392800.php';

/**
 * Test class for endpoints.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:06:08.
 */
class e00392800Test extends endpointTestBase {
    public $class = "e00392800";
    /**
     *  Test cases for the InterpConfig routine
     */
     var $InterpConfigTestCases = array(
         array(
             "Info" => array(
                 "RawSetup" => "00000000E800392801410039201343000005FFFFFF500102020202020202027070707070707070",
                 "HWPartNum" => "0039-28-01-A",
                 "FWPartNum" => "0039-20-13-C",
                 "FWVersion" => "00.00.05",
                 "DeviceID" => "0000E8",
                 "SerialNum" => 232,
                 "Driver" => "e00392800",
                 "DeviceGroup" => "FFFFFF",
             ),
             "Return" => array(  
                 "RawSetup" => "00000000E800392801410039201343000005FFFFFF500102020202020202027070707070707070",
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
                 "Types" => array(2,2,2,2,2,2,2,2,112,112,112,112,112,112,112,112),
            ),
         ),
     );

     var $InterpSensorsTestCases = array(
        array(
            "Info" => array(
                 "DeviceKey" => 182,
                 "RawSetup" => "00000000E800392801410039201343000005FFFFFF500102020202020202027070707070707070",
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
                 "Types" => array(2,2,2,2,2,2,2,2,112,112,112,112,112,112,112,112),
            ),
            "Packets" => array(
                array(
                    "RawData" => "070001369E00F09C00919D00C89C00EE9C000A9D00BEFF00BFFF00000000",
                    "sendCommand" => "55",
                    "Date" => "2007-02-23 22:38:01",
                ),
                array(
                    "RawData" => "130001309E00F09C00909D00CA9C00F19C000A9D00BFFF00BFFF00000000",
                    "sendCommand" => "55",
                    "Date" => "2007-02-23 22:48:01",
                ),
            ),
            "Return" => array(
                array(
                    "RawData" => "070001369E00F09C00919D00C89C00EE9C000A9D00BEFF00BFFF00000000",
                    "sendCommand" => "55",
                    "Date" => "2007-02-23 22:38:01",
                    "Data" => array(7, 0, 1, 54, 158, 0, 240, 156, 0, 145, 157, 0, 200, 156, 0, 238, 156, 0, 10, 157, 0, 190, 255, 0, 191, 255, 0, 0, 0, 0),
                    "NumSensors" => 16,
                    "ActiveSensors" => 7,
                    "Driver" => "e00392800",
                    "DeviceKey" => 182,
                    "Types" => array(2,2,2,2,2,2,2,2,112,112,112,112,112,112,112,112),
                    "DataIndex" => 7,
                    "TimeConstant" => 1,
                    "raw" => array(40502, 40176, 40337, 40136, 40174, 40202, 65470, 65471, 0),
                    "Data0" => 14.2962,
                    "data" => array(14.2962, 14.7482, 14.5252, 14.8037, 14.751, 14.7122, NULL, NULL, NULL),
                    "Data1" => 14.7482,
                    "Data2" => 14.5252,
                    "Data3" => 14.8037,
                    "Data4" => 14.751,
                    "Data5" => 14.7122,
                    "Data6" => NULL,
                    "Data7" => NULL,
                    "Data8" => NULL,
                    "deltaT" => 0,
                    "Status" => "GOOD",
                    "StatusOld" => "GOOD",
                ),
                array(
                    "RawData" => "130001309E00F09C00909D00CA9C00F19C000A9D00BFFF00BFFF00000000",
                    "sendCommand" => "55",
                    "Date" => "2007-02-23 22:48:01",
                    "Data" => array(19, 0, 1, 48, 158, 0, 240, 156, 0, 144, 157, 0, 202, 156, 0, 241, 156, 0, 10, 157, 0, 191, 255, 0, 191, 255, 0, 0, 0, 0),
                    "NumSensors" => 16,
                    "ActiveSensors" => 7,
                    "Driver" => "e00392800",
                    "DeviceKey" => 182,
                    "Types" => array(2, 2, 2, 2, 2, 2, 2, 2, 112,112,112, 112,112,112,112,112),
                    "DataIndex" => 19,
                    "TimeConstant" => 1,
                    "raw" => array(40496, 40176, 40336, 40138, 40177, 40202, 65471, 65471, 0),
                    "Data0" => 14.3045,
                    "data" => array(14.3045, 14.7482, 14.5265, 14.8009, 14.7469, 14.7122, NULL, NULL, 0),
                    "Data1" => 14.7482,
                    "Data2" => 14.5265,
                    "Data3" => 14.8009,
                    "Data4" => 14.7469,
                    "Data5" => 14.7122,
                    "Data6" => NULL,
                    "Data7" => NULL,
                    "deltaT" => 600,
                    "Data8" => 0,
                    "Status" => "GOOD",
                    "StatusOld" => "GOOD",
                ),
            ),
        ),
    );

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("e00392800Test");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    public static function dataDevicesFirmware() {
        return parent::devicesArrayDataSource("e00392800", "fw");
    }

    public static function dataDevicesHardware() {
        return parent::devicesArrayDataSource("e00392800", "hw");
    }
    public static function dataDevicesVersion() {
        return parent::devicesArrayDataSource("e00392800", "ver");
    }
    public static function dataConfigArray() {
        return parent::dataConfigArray("e00392800");
    }


}

// Call e00392800Test::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "e00392800Test::main") {
    e00392800Test::main();
}
?>
