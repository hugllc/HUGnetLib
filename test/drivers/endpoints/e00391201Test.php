<?php
/**
 * Tests the 00391201 endpoint class
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
 * @subpackage Drivers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

require_once dirname(__FILE__).'/../endpointTestBase.php';
require_once dirname(__FILE__).'/../../../drivers/endpoints/e00391201.php';

/**
 * Test class for endpoints.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:06:08.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Drivers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class E00391201Test extends EndpointTestBase
{
    public $class = "e00391201";
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
                    "RawSetup" => "000000004E00391202420039200443000004FFFFFF"
                        ."50E48081828330405060",
                    "HWPartNum" => "0039-12-02-B",
                    "FWPartNum" => "0039-20-04-C",
                    "FWVersion" => "00.00.04",
                    "DeviceID" => "00004E",
                    "SerialNum" => 78,
                    "DeviceGroup" => "FFFFFF",
               ),
                "Return" => array(
                    "RawSetup" => "000000004E00391202420039200443000004FFFFFF"
                        ."50E48081828330405060",
                    "HWPartNum" => "0039-12-02-B",
                    "FWPartNum" => "0039-20-04-C",
                    "FWVersion" => "00.00.04",
                    "DeviceID" => "00004E",
                    "SerialNum" => 78,
                    "DeviceGroup" => "FFFFFF",
                    "HWName" => "0039-12 Endpoint",
                    "NumSensors" => 9,
                    "Function" => "Fan Controller",
                    "DriverInfo" => "E48081828330405060",
                    "ActiveSensors" => 9,
                    "Setup" => 228,
                    "FET0Mode" => 0,
                    "FET0pMode" => "Digital",
                    "FET1Mode" => 1,
                    "FET1pMode" => "Analog - High Z",
                    "FET2Mode" => 2,
                    "FET2pMode" => "Analog - Voltage",
                    "FET3Mode" => 3,
                    "FET3pMode" => "Analog - Current",
                    "FET0" => 128,
                    "FET1" => 129,
                    "FET2" => 130,
                    "FET3" => 131,
                    "FET0Mult" => 48,
                    "FET1Mult" => 64,
                    "FET2Mult" => 80,
                    "FET3Mult" => 96,
                    "Types" => array(80, 64, 80, 64, 80, 64, 80, 64, 64),
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
                    "DeviceKey" => 46,
                    "RawSetup" => "000000004E00391202420039200443000004FFFFFF"
                        ."50E48081828330405060",
                    "HWPartNum" => "0039-12-02-B",
                    "FWPartNum" => "0039-20-04-C",
                    "FWVersion" => "00.00.04",
                    "DeviceID" => "00004E",
                    "SerialNum" => 78,
                    "DeviceGroup" => "FFFFFF",
                    "HWName" => "0039-12 Endpoint",
                    "NumSensors" => 9,
                    "Function" => "Fan Controller",
                    "DriverInfo" => "E48081828330405060",
                    "ActiveSensors" => 9,
                    "Setup" => 228,
                    "FET0Mode" => 0,
                    "FET0pMode" => "Digital",
                    "FET1Mode" => 1,
                    "FET1pMode" => "Analog - High Z",
                    "FET2Mode" => 2,
                    "FET2pMode" => "Analog - Voltage",
                    "FET3Mode" => 3,
                    "FET3pMode" => "Analog - Current",
                    "FET0" => 128,
                    "FET1" => 129,
                    "FET2" => 130,
                    "FET3" => 131,
                    "FET0Mult" => 48,
                    "FET1Mult" => 64,
                    "FET2Mult" => 80,
                    "FET3Mult" => 96,
                    "Types" => array(80, 64, 80, 64, 80, 64, 80, 64, 64),
                    "params" => array(
                        "sensorType" => array(
                            "FETBoard", "FETBoard","FETBoard","FETBoard",
                            "FETBoard","FETBoard","FETBoard","FETBoard","FETBoard"
                        )
                    ),
               ),
                "Packets" => array(
                    array(
                        "RawData" => "078511bb00cb11b3006711c6004210ac00fc4d",
                        "sendCommand" => "55",
                        "Date" => "2007-02-23 22:38:01",
                   ),
                    array(
                        "RawData" => "4b9d11bc008311bb006111bf003810a200004e",
                        "sendCommand" => "55",
                        "Date" => "2007-02-23 22:48:01",
                   ),
               ),
                "Return" => array(
                    array(
                        "RawData" => "078511bb00cb11b3006711c6004210ac00fc4d",
                        "sendCommand" => "55",
                        "Date" => "2007-02-23 22:38:01",
                        "Data" => array(
                            7, 133, 17, 187, 0, 203, 17, 179, 0, 103,
                            17, 198, 0, 66, 16, 172, 0, 252, 77
                        ),
                        "NumSensors" => 9,
                        "ActiveSensors" => 9,
                        "Driver" => null,
                        "DeviceKey" => 46,
                        "Types" => array(80, 64, 80, 64, 80, 64, 80, 64, 64),
                        "DataIndex" => 7,
                        "TimeConstant" => 1,
                        "raw" => array(
                            4485, 187, 4555, 179, 4455,
                            198, 4162, 172, 19964
                        ),
                        "Data0" => 685.0,
                        "data" => array(
                            685.0, 24.1654, 695.7, 24.1752, 680.4,
                            24.152, 635.7, 24.1837, 24.3939
                        ),
                        "Data1" => 24.1654,
                        "Data2" => 695.7,
                        "Data3" => 24.1752,
                        "Data4" => 680.4,
                        "Data5" => 24.152,
                        "Data6" => 635.7,
                        "Data7" => 24.1837,
                        "Data8" => 24.3939,
                        "deltaT" => 0,
                        "Status" => "GOOD",
                        "StatusOld" => "GOOD",
                   ),
                    array(
                        "RawData" => "4b9d11bc008311bb006111bf003810a200004e",
                        "sendCommand" => "55",
                        "Date" => "2007-02-23 22:48:01",
                        "Data" => array(
                            75, 157, 17, 188, 0, 131, 17, 187, 0,
                            97, 17, 191, 0, 56, 16, 162, 0, 0, 78
                        ),
                        "NumSensors" => 9,
                        "ActiveSensors" => 9,
                        "Driver" => null,
                        "DeviceKey" => 46,
                        "Types" => array(80, 64, 80, 64, 80, 64, 80, 64, 64),
                        "DataIndex" => 75,
                        "TimeConstant" => 1,
                        "raw" => array(
                            4509, 188, 4483, 187, 4449,
                            191, 4152, 162, 19968
                        ),
                        "Data0" => 688.7,
                        "data" => array(
                            688.7, 24.1691, 684.7, 24.1703, 679.5,
                            24.1654, 634.2, 24.2009, 24.3988
                        ),
                        "Data1" => 24.1691,
                        "Data2" => 684.7,
                        "Data3" => 24.1703,
                        "Data4" => 679.5,
                        "Data5" => 24.1654,
                        "Data6" => 634.2,
                        "Data7" => 24.2009,
                        "Data8" => 24.3988,
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
        return parent::devicesArrayDataSource("e00391201", "fw");
    }
    /**
     * data provider
     *
     * @return array
     */
    public static function dataDevicesHardware()
    {
        return parent::devicesArrayDataSource("e00391201", "hw");
    }
    /**
     * data provider
     *
     * @return array
     */
    public static function dataDevicesVersion()
    {
        return parent::devicesArrayDataSource("e00391201", "ver");
    }
    /**
     * data provider
     *
     * @return array
     */
    public static function dataConfigArray()
    {
        return parent::dataConfigArray("e00391201");
    }

}

?>
