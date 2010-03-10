<?php
/**
 * This is the basis for all sensor driver test classes.
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
 * @category   Base
 * @package    HUGnetLibTest
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
if (!defined("HUGNET_INCLUDE_PATH")) {
    define("HUGNET_INCLUDE_PATH", dirname(__FILE__)."/../..");
}

require_once dirname(__FILE__).'/../../sensor.php';
require_once dirname(__FILE__).'/../unitConversionTest.php';
/**
 * This class is the basis for all sensor driver tests.  This class should be
 * inherited by all sensor test driver classes.  Tests in here can be overridden
 * if necessary, but this class should still be inherited.
 *
 * @category   Base
 * @package    HUGnetLibTest
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class SensorTestBase extends PHPUnit_Framework_TestCase
{

    /**
     *  This function makes sure the parent of the class is correct
     *
     * @return null
     */
    public function testSensorParent()
    {
        $o = new $this->class;
        // Long Name
        $this->assertEquals(
            "SensorBase",
            get_parent_class($o),
            $this->class." parent class must be 'SensorBase'"
        );
    }

    /**
     * data provider
     *
     * @return array
     */
    public static function dataSensorArray()
    {
        return array();
    }
    /**
     * Returns sensor data
     *
     * @param string $class The class to get the sensor array out of
     *
     * @return array
     */
    public static function sensorArrayDataSource($class)
    {
        $o = new $class();
        $return = array();
        foreach ($o->sensors as $catName => $cat) {
            foreach ($cat as $shortName => $sensor) {
                $return[] = array($catName, $shortName, $sensor);
            }
        }
        return $return;
    }
    /**
     * test
     *
     * @param string $catName   The category name
     * @param string $shortName The sensor short name
     * @param array  $sensor    The sensor array
     *
     * @return null
     *
     * @dataProvider dataSensorArray
     */
    public function testSensorArrayLongName($catName, $shortName, $sensor)
    {
        // Long Name
        $this->assertType(
            "string",
            $sensor['longName'],
            $catName.":".$shortName.": Long name is not a string"
        );
        $this->assertThat(
            strlen($sensor['longName']),
            $this->greaterThan(0),
            $catName.":".$shortName.": Long name is not a set"
        );
    }
    /**
     * test
     *
     * @param string $catName   The category name
     * @param string $shortName The sensor short name
     * @param array  $sensor    The sensor array
     *
     * @return null
     *
     * @dataProvider dataSensorArray
     */
    public function testSensorArrayFunction($catName, $shortName, $sensor)
    {
        $o = new $this->class;
        if (isset($sensor['function'])) {
            $this->assertTrue(
                method_exists($o, $sensor['function']),
                $this->class.":".$type.":".$shortName.": Method "
                .$sensor['function']." does not exist"
            );
        }
    }
    /**
     * test
     *
     * @param string $catName   The category name
     * @param string $shortName The sensor short name
     * @param array  $sensor    The sensor array
     *
     * @return null
     *
     * @dataProvider dataSensorArray
     */
    public function testSensorArrayCheckFunction($catName, $shortName, $sensor)
    {
        $o = new $this->class;
        if (isset($sensor['checkFunction'])) {
            $this->assertTrue(
                method_exists($o, $sensor['checkFunction']),
                $this->class.":".$type.":".$shortName.": Method "
                .$sensor['checkFunction']." does not exist"
            );
        }
    }
    /**
     * test
     *
     * @param string $catName   The category name
     * @param string $shortName The sensor short name
     * @param array  $sensor    The sensor array
     *
     * @return null
     *
     * @dataProvider dataSensorArray
     */
    public function testSensorArrayMult($catName, $shortName, $sensor)
    {
        if (isset($sensor['mult'])) {
            $this->assertType(
                "numeric",
                $sensor['mult'],
                $this->class.":".$type.":".$shortName.": Multiplier must be "
                ."numeric or not specified"
            );
        }
    }
    /**
     * test
     *
     * @param string $catName   The category name
     * @param string $shortName The sensor short name
     * @param array  $sensor    The sensor array
     *
     * @return null
     *
     * @dataProvider dataSensorArray
     */
    public function testSensorArrayDoTotal($catName, $shortName, $sensor)
    {
        if (isset($sensor['doTotal'])) {
            $this->assertType(
                "bool",
                $sensor['doTotal'],
                $this->class.":".$type.":".$shortName.": doTotal must be a "
                ."boolean or not specified"
            );
        }
    }

    /**
     * test
     *
     * @param string $catName   The category name
     * @param string $shortName The sensor short name
     * @param array  $sensor    The sensor array
     *
     * @return null
     *
     * @dataProvider dataSensorArray
     */
    public function testSensorArrayInputSize($catName, $shortName, $sensor)
    {
        if (isset($sensor['inputSize'])) {
            $this->assertType(
                "int",
                $sensor['inputSize'],
                $this->class.":".$type.":".$shortName.": inputSize must be an "
                ."integer or not specified"
            );
            $this->assertThat(
                $sensor['inputSize'],
                $this->greaterThan(0),
                $this->class.":".$type.":".$shortName.": inputSize must be greater "
                ."than 0 or not specified"
            );
        }
    }

    /**
     * test
     *
     * @param string $catName   The category name
     * @param string $shortName The sensor short name
     * @param array  $sensor    The sensor array
     *
     * @return null
     *
     * @dataProvider dataSensorArray
     */
    public function testSensorVariableExtra($catName, $shortName, $sensor)
    {
        if (isset($sensor["extraText"])) {
            if (is_array($sensor["extraText"])) {
                $this->assertType(
                    "array",
                    $sensor['extraDefault'],
                    $this->class.":".$type.":".$shortName.": If extraText is an "
                    ."array extraDefault must also be an array."
                );
                $this->assertEquals(
                    count($sensor['extraText']),
                    count($sensor['extraDefault']),
                    $this->class.":".$type.":".$shortName.": extraText and "
                    ."extraDefault must have the same number of elements"
                );
            } else {
                $this->assertType(
                    "string",
                    $sensor['extraText'],
                    $this->class.":".$type.":".$shortName
                    .": extraText must either be an array or a string"
                );
                $this->assertNotType(
                    "array",
                    $sensor['extraDefault'],
                    $this->class.":".$type.":".$shortName.": If extraText is not "
                    ."an array extraDefault must also not be an array."
                );
            }
        }
    }

    /**
     * test
     *
     * @param string $catName   The category name
     * @param string $shortName The sensor short name
     * @param array  $sensor    The sensor array
     *
     * @return null
     *
     * @dataProvider dataSensorArray
     */
    public function testSensorVariableUnitType($catName, $shortName, $sensor)
    {
        // Check unitType
        $this->assertType(
            "string",
            $sensor['unitType'],
            $this->class.":".$type.":".$shortName.": unitType must be a string"
        );

        $this->assertTrue(
            unitConversionTest::findUnits(
                $sensor['unitType'],
                $sensor['storageUnit']
            ),
            $this->class.":".$type.":".$shortName.": unit "
            .$sensor['storageUnit']." of type ".$sensor['unitType']
            ." not found in unitConversion"
        );
    }

    /**
     * test
     *
     * @param string $catName   The category name
     * @param string $shortName The sensor short name
     * @param array  $sensor    The sensor array
     *
     * @return null
     *
     * @dataProvider dataSensorArray
     */
    public function testSensorVariableStorageUnit($catName, $shortName, $sensor)
    {
        // Check storage Unit
        $this->assertType(
            "string",
            $sensor['storageUnit'],
            $this->class.":".$type.":".$shortName.": unitType must be a string"
        );
        // Check to make sure the storage unit is also a valid unit.
        $this->assertContains(
            $sensor['storageUnit'],
            $sensor['validUnits'],
            $this->class.":".$type.":".$shortName
            .": Unit '$unit' not found in valid units list"
        );
    }

    /**
     * test
     *
     * @param string $catName   The category name
     * @param string $shortName The sensor short name
     * @param array  $sensor    The sensor array
     *
     * @return null
     *
     * @dataProvider dataSensorArray
     */
    public function testSensorVariableValidUnits($catName, $shortName, $sensor)
    {
        // Check valid units
        $this->assertType(
            "array",
            $sensor['validUnits'],
            $this->class.":".$type.":".$shortName.": validUnits must be an array"
        );
        $this->assertTrue(
            count($sensor['validUnits']) > 0,
            $this->class.":".$type.":".$shortName
            .": At least one unit must be defined"
        );
        foreach ($sensor['validUnits'] as $unit) {
            $this->assertFalse(
                empty($unit),
                $this->class.":".$type.":".$shortName.": blank unit"
            );
            // Check to make sure the unit
            $this->assertTrue(
                unitConversionTest::findUnits($sensor['unitType'], $unit),
                $this->class.":".$type.":".$shortName.": unit "
                .$unit." not found in unitConversion"
            );
            // Check to make sure the unit is also in the modes list
            $this->assertType(
                "string",
                $sensor['unitModes'][$unit],
                $this->class.":".$type.":".$shortName
                .": '$unit' not found in mode list"
            );
        }
    }

    /**
     * test
     *
     * @param string $catName   The category name
     * @param string $shortName The sensor short name
     * @param array  $sensor    The sensor array
     *
     * @return null
     *
     * @dataProvider dataSensorArray
     */
    public function testSensorVariableUnitMode($catName, $shortName, $sensor)
    {
        // Check unit modes
        $this->assertType(
            "array",
            $sensor['unitModes'],
            $this->class.":".$type.":".$shortName.": unitModes must be an array"
        );
        $this->assertTrue(
            count($sensor['unitModes']) > 0,
            $this->class.":".$type.":".$shortName.": No modes defined"
        );
        foreach ($sensor['unitModes'] as $unit => $mode) {
            // Check to make sure each unit in the mode list is also in the
            //validUnits list
            $found = array_search($unit, $sensor['validUnits']);
            if ($found !== false) {
                $found = true;
            }
            $this->assertTrue(
                $found,
                $this->class.":".$type.":".$shortName.": Unit '$unit' not valid"
            );
            // Check the modes based on the units.
            $this->assertType(
                "string",
                $mode,
                $this->class.":".$type.":".$shortName.": '$unit' mode not a string"
            );
            $mode = explode("'", $mode);
            foreach ($mode as $m) {
                $m = trim($m);
                $this->assertTrue(
                    unitConversionTest::findUnitMode($sensor['unitType'], $unit, $m),
                    $this->class.":".$type.":".$shortName.": mode ".$m
                    ." not found for ".$unit.""
                );
            }
        }
    }

    /**
     * Generic function for testing sensor routines
     *
     * This is called by using parent::sensorTest()
     *
     * @param string $class  The name of the class
     * @param string $method The method to test
     * @param mixed  $A      Data for the sensor to work on
     * @param array  $sensor The sensor array
     * @param int    $TC     The time constant
     * @param mixed  $extra  The extra data for the sensor
     * @param float  $deltaT The time differenct
     * @param mixed  $expect The return data to expect
     *
     * @return null
     */
    public function sensorTest(
        $class,
        $method,
        $A,
        $sensor,
        $TC,
        $extra,
        $deltaT,
        $expect
    ) {
        $o = new $class();
        $ret = $o->$method($A, $sensor, $TC, $extra, $deltaT);
        $this->assertSame($expect, $ret);
    }

    /**
     * Generic function for testing sensor check routines.
     *
     * This is called by using parent::sensorCheckTest()
     *
     * @param string $class  The name of the class
     * @param string $method The method to test
     * @param mixed  $value  Data for the sensor to work on
     * @param array  $sensor The sensor array
     * @param int    $units  Units that $value is in
     * @param mixed  $dType  data type (mode) that $value is in
     * @param mixed  $expect The return data to expect
     *
     * @return null
     */
    public function sensorCheckTest(
        $class,
        $method,
        $value,
        $sensor,
        $units,
        $dType,
        $expect
    ) {
        $o = new $class();
        $ret = $o->$method($value, $sensor, $units, $dType);
        $this->assertType(
            "bool",
            $expect,
            "sensorCheck functions MUST return a boolean"
        );
        $this->assertSame($expect, $ret);
    }
}
?>
