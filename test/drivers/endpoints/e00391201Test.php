<?php
/**
 *   Tests the 00391201 endpoint class
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

// Call e00391201Test::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "00391201Test::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../endpointTestBase.php';

/**
 * Test class for endpoints.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:06:08.
 */
class e00391201Test extends endpointTestBase {
    /**
     *  Test cases for the InterpConfig routine
     */
    var $InterpConfigTestCases = array(
        array(
            "Info" => array(
                "RawSetup" => "000000004E00391202420039200443000004FFFFFF50E48081828330405060",
                "HWPartNum" => "0039-12-02-B",
                "FWPartNum" => "0039-20-04-C",
                "FWVersion" => "00.00.04",
                "DeviceID" => "00004E",
                "SerialNum" => 78,
                "DeviceGroup" => "FFFFFF",
            ),
            "Return" => array(  
                "HWName" => "0039-12 Endpoint",
                "NumSensors" => 9,
                "Function" => "Fan Controller",
                "DriverInfo" => "E48081828330405060",
                "Types" => array(0x50, 0x40, 0x50, 0x40, 0x50, 0x40, 0x50, 0x40, 0x40),
				"FET0Mode" => 0,
				"FET0pMode" => "Digital",
				"FET1Mode" => 1,
				"FET1pMode" => "Analog - High Z",
				"FET2Mode" => 2,
				"FET2pMode" => "Analog - Voltage",
				"FET3Mode" => 3,
				"FET3pMode" => "Analog - Current",
    			"FET0" => 0x80,
    			"FET1" => 0x81,
    			"FET2" => 0x82,
    			"FET3" => 0x83,
    			"FET0Mult" => 0x30,
    			"FET1Mult" => 0x40,
    			"FET2Mult" => 0x50,
    			"FET3Mult" => 0x60,

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

        $suite  = new PHPUnit_Framework_TestSuite("e00391201Test");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

}

// Call e00391201Test::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "e00391201Test::main") {
    e00391201Test::main();
}
?>
