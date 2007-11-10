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
abstract class sensorTestBase extends PHPUnit_Framework_TestCase {

    /**
     * 
     */
    public function testSensorParent() {
        $o = new $this->class;
        // Long Name
        $this->assertEquals("sensor_base", get_parent_class($o), $this->class." parent class must be 'sensor_base'");
    }
    
    
    public static function dataSensorArray() {
        return array();
    }

    /**
     * @dataProvider dataSensorArray
     */
    public function testSensorArrayLongName($catName, $shortName, $sensor) {
        $o = new $this->class;
        // Long Name
        $this->assertType("string", $sensor['longName'], $catName.":".$shortName.": Long name is not a string");
        $this->assertThat(strlen($sensor['longName']), $this->greaterThan(0), $catName.":".$shortName.": Long name is not a set");            
    }

    /**
     * Test the sensor variable
     */
    public function testSensorVariable() {
        $o = new $this->class;
        $this->assertType("array", $o->sensors, $this->class." doesn't have a sensor variable");
        $this->assertTrue((count($o->sensors) > 0), $this->class." doesn't have any sensors defined");
        foreach($o->sensors as $type => $sensor) {
            $this->assertType("array", $sensor, $this->class.":".$type.": doesn't have a sensor variable");
            $this->assertTrue((count($sensor) > 0), $this->class.":".$type.": doesn't have any sensors defined");
            foreach($sensor as $shortName => $values) {
                $this->assertType("string", $values['longName'], $this->class.":".$type.":".$shortName.": Long name is not a string");
                $this->assertFalse(empty($values['longName']), $this->class.":".$type.":".$shortName.": Long name can not be empty");
                if (isset($values['function'])) {
                    $this->assertTrue(method_exists($o, $values['function']), $this->class.":".$type.":".$shortName.": Method ".$values['function']." does not exist");            
                }
                if (isset($values['checkFunction'])) {
                    $this->assertTrue(method_exists($o, $values['checkFunction']), $this->class.":".$type.":".$shortName.": Method ".$values['checkFunction']." does not exist");            
                }
                if (isset($values['mult'])) {
                    $this->assertType("numeric", $values['mult'], $this->class.":".$type.":".$shortName.": Multiplier must be numeric or not specified");            
                }
                if (isset($values['doTotal'])) {
                    $this->assertType("bool", $values['doTotal'], $this->class.":".$type.":".$shortName.": doTotal must be a boolean or not specified");            
                }
                if (isset($values['inputSize'])) {
                    $this->assertType("int", $values['inputSize'], $this->class.":".$type.":".$shortName.": inputSize must be an integer or not specified");            
                    $this->assertThat($values['inputSize'], $this->greaterThan(0), $this->class.":".$type.":".$shortName.": inputSize must be greater than 0 or not specified");
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
                        $this->assertType("array", $values['extraDefault'], $this->class.":".$type.":".$shortName.": If extraText is an array extraDefault must also be an array.");
                        $this->assertEqual(count($values['extraText']), count($values['extraDefault']), $this->class.":".$type.":".$shortName.": extraText and extraDefault must have the same number of elements");
                    } else {
                        $this->assertType("string", $values['extraText'], $this->class.":".$type.":".$shortName.": extraText must either be an array or a string");
                        $this->assertNotType("array", $values['extraDefault'], $this->class.":".$type.":".$shortName.": If extraText is not an array extraDefault must also not be an array.");
                    }
                }
            }
        }
    }

    /**
     * Test the units portion of the sensor variable
     */    
    public function testSensorVariableUnitType() {
        $o = new $this->class;
        foreach($o->sensors as $type => $sensor) {
            foreach($sensor as $shortName => $values) {
                // Check unitType
                $this->assertType("string", $values['unitType'],  $this->class.":".$type.":".$shortName.": unitType must be a string");
                $this->assertTrue(unitConversionTest::findUnits($values['unitType'], $values['storageUnit']), $this->class.":".$type.":".$shortName.": unit ".$values['storageUnit']." of type ".$values['unitType']." not found in unitConversion");
            }
        }
    }

    /**
     * Test the units portion of the sensor variable
     */    
    public function testSensorVariableStorageUnit() {
        $o = new $this->class;
        foreach($o->sensors as $type => $sensor) {
            foreach($sensor as $shortName => $values) {
                // Check storage Unit
                $this->assertType("string", $values['storageUnit'],  $this->class.":".$type.":".$shortName.": unitType must be a string");
                // Check to make sure the storage unit is also a valid unit.
                $this->assertContains($values['storageUnit'], $values['validUnits'], $this->class.":".$type.":".$shortName.": Unit '$unit' not found in valid units list");
            }
        }
    }

    /**
     * Test the units portion of the sensor variable
     */    
    public function testSensorVariableValidUnits() {
        $o = new $this->class;
        foreach($o->sensors as $type => $sensor) {
            foreach($sensor as $shortName => $values) {
                // Check valid units
                $this->assertType("array", $values['validUnits'],  $this->class.":".$type.":".$shortName.": validUnits must be an array");
                $this->assertTrue(count($values['validUnits']) > 0, $this->class.":".$type.":".$shortName.": At least one unit must be defined");
                foreach($values['validUnits'] as $unit) {
                    $this->assertFalse(empty($unit), $this->class.":".$type.":".$shortName.": blank unit");            
                    // Check to make sure the unit
                    $this->assertTrue(unitConversionTest::findUnits($values['unitType'], $unit), $this->class.":".$type.":".$shortName.": unit ".$unit." not found in unitConversion");
                    // Check to make sure the unit is also in the modes list
                    $this->assertType("string", $values['unitModes'][$unit], $this->class.":".$type.":".$shortName.": Unit '$unit' not found in mode list");
                }
            }
        }
    }

    /**
     * Test the units portion of the sensor variable
     */    
    public function testSensorVariableUnitMode() {
        $o = new $this->class;
        foreach($o->sensors as $type => $sensor) {
            foreach($sensor as $shortName => $values) {
                // Check unit modes
                $this->assertType("array", $values['unitModes'],  $this->class.":".$type.":".$shortName.": unitModes must be an array");
                $this->assertTrue(count($values['unitModes']) > 0, $this->class.":".$type.":".$shortName.": At least one mode must be defined");
                foreach($values['unitModes'] as $unit => $mode) {
                    // Check to make sure each unit in the mode list is also in the validUnits list
                    $found = array_search($unit, $values['validUnits']);
                    if ($found !== FALSE) $found = TRUE;
                    $this->assertTrue($found, $this->class.":".$type.":".$shortName.": Unit '$unit' not found in valid units list");
                    // Check the modes based on the units.
                    $this->assertType("string", $mode, $this->class.":".$type.":".$shortName.": Mode for unit '$unit' is not a string");
                    $mode = explode("'", $mode);
                    foreach($mode as $m) {
                        $m = trim($m);
                        $this->assertTrue(unitConversionTest::findUnitMode($values['unitType'], $unit, $m), $this->class.":".$type.":".$shortName.": mode ".$m." not found for ".$unit."");
                    }
                }
            }
        }
    }
    
}
?>
