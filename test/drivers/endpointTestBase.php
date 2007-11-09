<?php
/**
 *   This is the basis for all endpoint driver test classes.
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

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../driverTest.php';
require_once dirname(__FILE__).'/../EPacketTest.php';
require_once dirname(__FILE__).'/../unitConversionTest.php';

/**
 * Test class for endpoints.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:06:08.
 */
class endpointTestBase extends PHPUnit_Framework_TestCase {

    var $goodEndpoints = array(
        array(
            "DeviceID" => "123456",
            "DeviceName" => "Test 1",
        ),
    );


    function __construct() {
        $this->driver = get_class($this);
        $this->driver = str_replace("Test", "", $this->driver);
    }

    
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("endpointTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    }

    /**
     *
     */
    private function &setupDriver() {
        $o = driverTest::createDriver();
        if (is_object($o->drivers[$this->driver])) {
            $o->packet->socket[1] = new epsocketMock;
            return $o;
        } else {
            return FALSE;
        }
    }
    /**
     *
     */
    function testDriver() {
        $o = $this->setupDriver();
        $this->assertType("object", $o->drivers[$this->driver], "This class '".$this->driver."' did not register as a plugin");
    }
    /**
     *
     */
    function testDevicesArray() {
        $o = $this->setupDriver();
        $d = &$o->drivers[$this->driver];
        $this->assertType("array", $d->devices, "Driver '".$this->driver."' has no devices array");
        foreach($d->devices as $fw => $Firm) {
            $this->checkPartNum($fw);
            $this->assertType("array", $d->config[$fw], "'$fw' not found in config array");
            foreach($Firm as $hw => $ver) {
                $this->checkPartNum($hw);
                $dev = explode(",", $ver);
                foreach($dev as $v) {
                    $this->checkVersion($v);
                }
            }
        }
    }
    /**
     *
     */
    function testConfigDefault() {
        $o = $this->setupDriver();
        $d = &$o->drivers[$this->driver];
        $this->assertType("array", $d->config['DEFAULT'], "Driver '".$this->driver."' has no DEFAULT config");
    }
    
    /**
     *
     */
    function testVars() {
        $o = $this->setupDriver();
        $d = &$o->drivers[$this->driver];
        $this->assertType("string", $d->HWName, "Driver '".$this->driver."' has no HWName attribute");
        $this->assertThat(strlen($d->HWName), $this->greaterThan(0), "Driver '".$this->driver."' has blank HWName");
        $this->assertType("string", $d->average_table, "Driver '".$this->driver."' has no HWName attribute");
        $this->assertThat(strlen($d->average_table), $this->greaterThan(0), "Driver '".$this->driver."' has blank HWName");
        $this->assertType("string", $d->history_table, "Driver '".$this->driver."' has no HWName attribute");
        $this->assertThat(strlen($d->history_table), $this->greaterThan(0), "Driver '".$this->driver."' has blank HWName");
        $this->assertType("int", $d->AtoDMax, "Driver '".$this->driver."': AtoDMax must be an integer.");                

    }

    /**
     * Check to make sure the part number is in the correct format
     */
    private function checkPartNum($part) {
        if ($part == "DEFAULT") return TRUE;
        $p = explode("-", $part);
        $this->assertEquals(count($p), 4, "'$part' is not in XXXX-XX-XX-X format");
        $this->assertEquals(strlen($p[0]), 4, "'".$p[0]."' is not 4 characters");
        $this->assertType("numeric", $p[0], "'".$p[0]."' is not a number");
        $this->assertEquals(strlen($p[1]), 2, "'".$p[1]."' is not 2 characters");
        $this->assertType("numeric", $p[1], "'".$p[1]."' is not a number");
        $this->assertEquals(strlen($p[2]), 2, "'".$p[2]."' is not 2 characters");
        $this->assertType("numeric", $p[2], "'".$p[2]."' is not a number");
        $this->assertEquals(strlen($p[3]), 1, "'".$p[3]."' is not 1 character");
        $this->assertNotType("numeric", $p[3], "'".$p[2]."' can not be a number");
        
        return TRUE;
    }
    /**
     *
     */    
    private function checkVersion($ver) {
        if ($ver == "DEFAULT") return TRUE;
        if ($ver == "BAD") return TRUE;
        $v = explode(".", $ver);
        $this->assertEquals(count($v), 3, "'$ver' is not in X.Y.Z format");
        $this->assertType("numeric", $v[0], "'".$v[0]."' is not a number in '$ver'");
        $this->assertType("numeric", $v[1], "'".$v[1]."' is not a number in '$ver'");
        $this->assertType("numeric", $v[2], "'".$v[2]."' is not a number in '$ver'");
        foreach($v as $val) {
            $this->assertThat($val, $this->greaterThanOrEqual(0));
            $this->assertThat($val, $this->lessThanOrEqual(255));
        }        
    }
    
    /**
     *
     */
    function testConfigArray() {
        $o = $this->setupDriver();
        $d = &$o->drivers[$this->driver];
        $this->assertType("array", $d->config, "Driver '".$this->driver."' has no config array");
        foreach($d->config as $fw => $params) {
            $this->checkPartNum($fw);
            $this->assertType("array", $params, "'$fw':Parameters are not an array");
            $this->assertType("array", $d->devices[$fw], "'$fw' not found in devices array");
        }        
    }
    
    /**
     *
     */
    function testConfigArrayFunction() {
        $o = $this->setupDriver();
        $d = &$o->drivers[$this->driver];
        $this->assertType("array", $d->config, "Driver '".$this->driver."' has no config array");
        foreach($d->config as $fw => $params) {
            // Function
            $this->assertType("string", $params["Function"], "'$fw': Parameter 'Function' must be a string");
        }        
    }
    
    /**
     *
     */
    function testConfigArraySensors() {
        $o = $this->setupDriver();
        $d = &$o->drivers[$this->driver];
        $this->assertType("array", $d->config, "Driver '".$this->driver."' has no config array");
        foreach($d->config as $fw => $params) {
            // Sensors
            $this->assertType("int", $params["Sensors"], "'$fw': Parameter 'Sensors' must be a int");
            $this->assertThat($params["Sensors"], $this->greaterThanOrEqual(0), "'$fw': The number of sensors must be greater than 0");

        }        
    }
    /**
     *
     */
    function testConfigArrayDisplayOrder() {
        $o = $this->setupDriver();
        $d = &$o->drivers[$this->driver];
        $this->assertType("array", $d->config, "Driver '".$this->driver."' has no config array");
        foreach($d->config as $fw => $params) {
            // This is not required so we only check it if it is present
            if (isset($params['DisplayOrder'])) {
                $this->assertType("string", $params["DisplayOrder"], "'$fw': Parameter 'DisplayOrder' must be a int");
                $do = explode(",", $params['DisplayOrder']);
                $this->assertEquals(count($do), $params["Sensors"], "'$fw': Number of display items needs to be identical to the number of sensors.");
                $doTmp = array();
                foreach($do as $order) {
                    $this->assertThat($order, $this->lessThan($params["Sensors"]), "'$fw': Display order items must be less than the number of sensors.");
                    $this->assertThat($order, $this->greaterThanOrEqual(0), "'$fw': Display order items must be greater than or equal to 0");
                    $this->assertFalse(isset($doTmp[$order]), "'$fw': $order already duplicated!  All entries in display order must be unique");
                    $doTmp[$order] = TRUE;
                }
            }
        }
    }
    /**
     *
     */
    function testConfigArrayBad() {
        $o = $this->setupDriver();
        $d = &$o->drivers[$this->driver];
        $this->assertType("array", $d->config, "Driver '".$this->driver."' has no config array");
        foreach($d->config as $fw => $params) {
            $this->assertFalse(isset($params["SensorLength"]), "'$fw': Parameter 'SensorLength' is not used anymore and should be removed.");
        }
    }
    /**
     * Test the read sensors routine
     * @todo implement testReadSensors()
     */
    function testReadSensors() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            /* Put test here */
            // Remove the following line when you implement this test.
            $this->markTestIncomplete("This test has not been implemented yet.");
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }

    /**
     * @todo implement testsaveSensorData()
     */
    function testsaveSensorData() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            /* Put test here */
            // Remove the following line when you implement this test.
            $this->markTestIncomplete("This test has not been implemented yet.");
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }

    /**
     * @todo implement testupdateConfig()
     */
    function testupdateConfig() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            /* Put test here */
            // Remove the following line when you implement this test.
            $this->markTestIncomplete("This test has not been implemented yet.");
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }

    /**
     * @todo implement testcheckDataArray()
     */
    function testcheckDataArray() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            /* Put test here */
            // Remove the following line when you implement this test.
            $this->markTestIncomplete("This test has not been implemented yet.");
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }

    /**
     * @todo implement testCheckRecord()
     */
    function testCheckRecord() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            /* Put test here */
            // Remove the following line when you implement this test.
            $this->markTestIncomplete("This test has not been implemented yet.");
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }


    /**
     * @todo implement testReadMem()
     */
    function testReadMem() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            /* Put test here */
            // Remove the following line when you implement this test.
            $this->markTestIncomplete("This test has not been implemented yet.");
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }
    
    /**
     * @todo implement testGetConfigVars()
     */
    function testGetConfigVars() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            /* Put test here */
            // Remove the following line when you implement this test.
            $this->markTestIncomplete("This test has not been implemented yet.");
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }
    
    
    /**
     * @todo implement testReadConfig()
     */
    function testReadConfig() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            /* Put test here */
            // Remove the following line when you implement this test.
            $this->markTestIncomplete("This test has not been implemented yet.");
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }
        
    /**
     * @todo implement testUnsolicited()
     */
    function testUnsolicited() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            /* Put test here */
            // Remove the following line when you implement this test.
            $this->markTestIncomplete("This test has not been implemented yet.");
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }
    /**
     */
    function testInterpConfig() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            if (is_array($this->InterpConfigTestCases) && (count($this->InterpConfigTestCases) > 0)) {
                foreach($this->InterpConfigTestCases as $key => $params) {
                    $ret = $o->drivers[$this->driver]->InterpConfig($params["Info"]);
                    $this->checkInterpConfigReturn($ret, $params["Return"]);
                }
            } else {
                $this->markTestSkipped("Skipped do to lack of driver"); 
            }
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }
    /**
     *
     */    
    private function checkInterpConfigReturn($ret, $expected) {
        // Check the stuff we can predict
        foreach($expected as $key => $val) {
            if (is_array($val)) {
                foreach($val as $k => $v) {
                    $this->assertEquals($ret[$key][$k], $v, $this->driver.": InterpConfig Failure in key $key, index $k");
                }
            } else {
                $this->assertEquals($ret[$key], $val, $this->driver.": InterpConfig Failure in key $key");
            }
        }
        // Check the stuff we can't predict (it might change witout a change in this code)
        foreach(array("Labels", "Units", "unitType", "dType", "doTotal") as $type) {
            $this->assertType("array", $ret[$type], "Missing array '$type'");
            $this->assertEquals(count($ret[$type]), $ret['NumSensors'], "$type doesn't have ".$ret['NumSensors']." elements");
        }
        for($key = 0; $key < $ret['NumSensors']; $key++) {
            foreach(array("Labels", "Units", "unitType", "dType") as $type) {
                $this->assertType("string", $ret[$type][$key], "$type element $key is not a string");
            }
            $this->assertType("bool", $ret["doTotal"][$key], "doTotal element $key is not a boolean");
        }
        $this->assertType("array", $ret['params'], 'params element must be an array');
    }
    /**
     *
     */
    function testBadDriver() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            $this->assertFalse($o->drivers[$this->driver]->BadDriver($Info, "Test"));
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }    
    
    /**
     * @todo implement testInterpSensors()
     */
    function testInterpSensors() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            /* Put test here */
            // Remove the following line when you implement this test.
            $this->markTestIncomplete("This test has not been implemented yet.");
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }


    /**
     * @todo implement testGetCols()
     */
    function testDefCols(){
        $o = $this->setupDriver();
        if (is_object($o)) {
            $Info = array();
            $cols = $o->drivers[$this->driver]->defcols;
            $this->assertType("array", $cols, "Variable must be an array");
            foreach($cols as $key => $val) {
                $this->assertType("string", $key, "Array key must be an string");                
                $this->assertType("string", $val, "Array value must be an string");                
            }
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }
    /**
     * @todo implement testGetCols()
     */
    function testCols(){
        $o = $this->setupDriver();
        if (is_object($o)) {
            $Info = array();
            $cols = $o->drivers[$this->driver]->cols;
            $this->assertType("array", $cols, "Variable must be an array");
            foreach($cols as $key => $val) {
                $this->assertFalse(isset($o->drivers[$this->driver]->defcols[$key]), "Column already defined as a default in variable defcols");                
                $this->assertType("string", $key, "Array key must be an string");                
                $this->assertType("string", $val, "Array value must be an string");                
            }
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }

    /**
     * @todo implement testGetCols()
     */
    function testGetCols(){
        $o = $this->setupDriver();
        if (is_object($o)) {
            $Info = array();
            $cols = $o->drivers[$this->driver]->getCols($Info);
            $this->assertType("array", $cols, "Return must be an array");
            foreach($cols as $key => $val) {
                $this->assertType("string", $key, "Array key must be an string");                
                $this->assertType("string", $val, "Array value must be an string");                
            }
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }

    /**
     * @todo implement testGetEditCols()
     */
    function testGetEditCols(){
        $o = $this->setupDriver();
        if (is_object($o)) {
            $Info = array();
            $cols = $o->drivers[$this->driver]->getEditCols($Info);
            $this->assertType("array", $cols, "Return must be an array");
            foreach($cols as $key => $val) {
                $this->assertType("string", $key, "Array key must be an string");                
                $this->assertType("string", $val, "Array value must be an string");                
            }
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }
    
    /**
     * @todo implement testSetAllConfig()
     */
    function testSetAllConfig() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            /* Put test here */
            // Remove the following line when you implement this test.
            $this->markTestIncomplete("This test has not been implemented yet.");
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }
    /**
     * @todo implement testGetCalibration()
     */
    function testGetCalibration() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            /* Put test here */
            // Remove the following line when you implement this test.
            $this->markTestIncomplete("This test has not been implemented yet.");
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }

    /**
     * @todo implement testSetConfig()
     */
    function testSetConfig() {
        $o = $this->setupDriver();    
        if (is_object($o)) {
            /* Put test here */
            // Remove the following line when you implement this test.
            $this->markTestIncomplete("This test has not been implemented yet.");
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }
    
    /**
     * @todo implement testCompareFWVersion()
     */
    function testCompareFWVersion() {
        $o = $this->setupDriver();
        if (is_object($o)) {
            $this->assertEquals(0, $o->drivers[$this->driver]->CompareFWVersion("1.2.3", "1.2.3"));
            $this->assertEquals(1, $o->drivers[$this->driver]->CompareFWVersion("1.2.4", "1.2.3"));
            $this->assertEquals(1, $o->drivers[$this->driver]->CompareFWVersion("1.3.3", "1.2.3"));
            $this->assertEquals(1, $o->drivers[$this->driver]->CompareFWVersion("2.2.3", "1.2.3"));
            $this->assertEquals(-1, $o->drivers[$this->driver]->CompareFWVersion("1.2.3", "1.2.4"));
            $this->assertEquals(-1, $o->drivers[$this->driver]->CompareFWVersion("1.2.3", "1.3.3"));
            $this->assertEquals(-1, $o->drivers[$this->driver]->CompareFWVersion("1.2.3", "2.2.3"));
        } else {
            $this->markTestSkipped("Skipped do to lack of driver"); 
        }
    }        

}

?>
