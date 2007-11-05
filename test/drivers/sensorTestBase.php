<?php
/**
 *   This is the basis for all sensor driver test classes.
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

require_once dirname(__FILE__).'/../../sensor.php';
require_once dirname(__FILE__).'/../unitConversionTest.php';

/**
 * This class is the basis for all sensor driver tests.  This class should be
 * inherited by all sensor test driver classes.  Tests in here can be overridden
 * if necessary, but this class should still be inherited.
 */
class sensorTestBase extends PHPUnit_Framework_TestCase {

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("sensorTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    function __construct() {
        $this->class = get_class($this);
        $this->class = str_replace("Test", "", $this->class);
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
     * Test the sensor variable
     */
    public function testSensorVariable() {
        $o = new $this->class;
        $this->assertTrue(is_array($o->sensors), $this->class." doesn't have a sensor variable");
        $this->assertTrue((count($o->sensors) > 0), $this->class." doesn't have any sensors defined");
        foreach($o->sensors as $type => $sensor) {
            $this->assertTrue(is_array($sensor), $this->class.":".$type.": doesn't have a sensor variable");
            $this->assertTrue((count($sensor) > 0), $this->class.":".$type.": doesn't have any sensors defined");
            foreach($sensor as $shortName => $values) {
                $this->assertTrue(is_string($values['longName']), $this->class.":".$type.":".$shortName.": Long name is not a string");
                $this->assertFalse(empty($values['longName']), $this->class.":".$type.":".$shortName.": Long name can not be empty");
                if (isset($values['function'])) {
                    $this->assertTrue(method_exists($o, $values['function']), $this->class.":".$type.":".$shortName.": Method ".$values['function']." does not exist");            
                }
                if (isset($values['checkFunction'])) {
                    $this->assertTrue(method_exists($o, $values['checkFunction']), $this->class.":".$type.":".$shortName.": Method ".$values['checkFunction']." does not exist");            
                }
                if (isset($values['mult'])) {
                    $this->assertTrue(is_numeric($values['mult']), $this->class.":".$type.":".$shortName.": Multiplier must be numeric");            
                }
                if (isset($values['doTotal'])) {
                    $this->assertTrue(is_bool($values['doTotal']), $this->class.":".$type.":".$shortName.": doTotal must be a boolean");            
                }
                if (isset($values['inputSize'])) {
                    $this->assertTrue(is_int($values['inputSize']), $this->class.":".$type.":".$shortName.": inputSize must be an integer");            
                }
            }
        }                
    }

    /**
     * Test the extra portion of the sensor variable
     */    
    public function testSensorVariableExtra() {
        $o = new $this->class;
        foreach($o->sensors as $type => $sensor) {
            foreach($sensor as $shortName => $values) {
                if (isset($extraText)) {
                    if (is_array($extraText)) {
                        $this->assertTrue(is_array($values['extraDefault']), $this->class.":".$type.":".$shortName.": If extraText is an array extraDefault must also be an array.");
                        $this->assertEqual(count($values['extraText']), count($values['extraDefault']), $this->class.":".$type.":".$shortName.": extraText and extraDefault must have the same number of elements");
                    } else {
                        $this->assertTrue(is_string($values['extraText']), $this->class.":".$type.":".$shortName.": extraText must either be an array or a string");
                        $this->assertFalse(is_array($values['extraDefault']), $this->class.":".$type.":".$shortName.": If extraText is not an array extraDefault must also not be an array.");
                    }
                }
            }
        }
    }

    /**
     * Test the units portion of the sensor variable
     */    
    public function testSensorVariableUnits() {
        $o = new $this->class;
        foreach($o->sensors as $type => $sensor) {
            foreach($sensor as $shortName => $values) {
                $this->assertTrue(is_string($values['unitType']),  $this->class.":".$type.":".$shortName.": unitType must be a string");
                $this->assertTrue(is_array($values['validUnits']),  $this->class.":".$type.":".$shortName.": validUnits must be an array");
                $this->assertTrue(is_string($values['storageUnit']),  $this->class.":".$type.":".$shortName.": unitType must be a string");
                $this->assertTrue(is_array($values['unitModes']),  $this->class.":".$type.":".$shortName.": unitModes must be an array");
                $this->assertTrue(unitConversionTest::findUnits($values['unitType'], $values['storageUnit']), $this->class.":".$type.":".$shortName.": unit ".$values['storageUnit']." of type ".$values['unitType']." not found in unitConversion");
                foreach($values['validUnits'] as $unit) {
                    $this->assertFalse(empty($unit), $this->class.":".$type.":".$shortName.": blank unit");            
                    $this->assertTrue(unitConversionTest::findUnits($values['unitType'], $unit), $this->class.":".$type.":".$shortName.": unit ".$unit." not found in unitConversion");
                }
                foreach($values['unitModes'] as $unit => $mode) {
                    if (!is_array($mode)) $mode = array($mode);
                    foreach($mode as $m) {
                        $this->assertTrue(unitConversionTest::findUnitMode($values['unitType'], $unit, $m), $this->class.":".$type.":".$shortName.": mode ".$m." not found for ".$unit."");
                    }
                }
            }
        }
    }
    
}
?>
