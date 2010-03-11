<?php
/**
 * This is the basis for all sensor driver test classes.
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
 * @category   Base
 * @package    HUGnetLibTest
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
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
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class UnitTestBase extends PHPUnit_Framework_TestCase
{

    /**
    *  This function makes sure the parent of the class is correct
    *
    * @return null
    */
    public function testSensorParent()
    {
        // Long Name
        $this->assertEquals(
            "UnitBase",
            get_parent_class($this->o),
            $this->class." parent class must be 'Sensor_Base'"
        );
    }

    /**
    * data provider for testUnitArrayLongName, testUnitArrayVarType,
    *
    * @param string $class The name of the class to use
    *
    * @return array
    */
    public static function getDataUnitArray($class)
    {
        $o      = new $class();
        $return = array();
        foreach ($o->units as $shortName => $unit) {
            $return[] = array($shortName, $unit);
        }
        return $return;
    }

    /**
    * Checks that LongName is set correctly
    *
    * @param string $shortName The short name of the unit
    * @param string $unit      The unit to check
    *
    * @return null
    *
    * @dataProvider dataUnitArray
    */
    public function testUnitArrayLongName($shortName, $unit)
    {
        // Long Name
        $this->assertType(
            "string",
            $unit['longName'],
            $shortName.": Long name is not a string"
        );
        $this->assertThat(
            strlen($unit['longName']),
            $this->greaterThan(0),
            $shortName.": Long name is not a set"
        );
    }

    /**
    * Checks that varType is set correctly
    *
    * @param string $shortName The short name of the unit
    * @param string $unit      The unit to check
    *
    * @return null
    *
    * @dataProvider dataUnitArray
    */
    public function testUnitArrayVarType($shortName, $unit)
    {
        // Var Type
        $this->assertType(
            "string",
            $unit['varType'],
            $shortName.": Variable type is not a string"
        );
        $this->assertTrue(
            $this->_checkvarType($unit['varType']),
            $shortName.": Variable type '".$unit['varType']."'is not valid"
        );
    }

    /**
    * Checks that the convert array is set correctly
    *
    * @param string $shortName The short name of the unit
    * @param string $unit      The unit to check
    *
    * @return null
    *
    * @dataProvider dataUnitArray
    */
    public function testUnitArrayConvert($shortName, $unit)
    {
        if (isset($unit["convert"])) {
            $this->assertType(
                "array",
                $unit["convert"],
                $shortName.": Mode is not a string"
            );
        }
    }

    /**
    * Checks that mode is set correctly
    *
    * @param string $shortName The short name of the unit
    * @param string $unit      The unit to check
    *
    * @return null
    *
    * @dataProvider dataUnitArray
    */
    public function testUnitArrayMode($shortName, $unit)
    {
        if (isset($unit["mode"])) {
            $this->assertType(
                "string",
                $unit["mode"],
                $shortName.": Mode is not a string"
            );
            $this->assertTrue(
                $this->_checkMode($unit["mode"]),
                $shortName.": Mode '".$unit['varType']."'is not valid"
            );
        }
    }
    /**
    * Checks that preferred unit is set correctly
    *
    * @param string $shortName The short name of the unit
    * @param string $unit      The unit to check
    *
    * @return null
    *
    * @dataProvider dataUnitArray
    */
    public function testUnitArrayPreferred($shortName, $unit)
    {
        if (isset($unit["preferred"])) {
            $this->assertType(
                "string",
                $unit["preferred"],
                $shortName.": Mode is not a string"
            );
            $this->assertTrue(
                $this->findUnits($unit["preferred"]),
                $shortName.": Unit ".$to." doesn't exist"
            );
        }
    }
    /**
    * Checks that only certain array keys are set
    *
    * @param string $shortName The short name of the unit
    * @param string $unit      The unit to check
    *
    * @return null
    *
    * @dataProvider dataUnitArray
    */
    public function testUnitArrayValid($shortName, $unit)
    {
        $valid = array(
            "mode",
            "convert",
            "longName",
            "varType",
            "preferred",
            "siPrefix",
            "class"
        );
        foreach ($valid as $key) {
            unset($unit[$key]);
        }
        $this->assertSame(array(), $unit);
    }

    /**
    * data provider for testUnitArrayConvertFunct
    *
    * @param string $class The name of the class to use
    *
    * @return array
    */
    public static function getDataUnitArrayConvertFunct($class)
    {
        $o      = new $class;
        $return = array();
        foreach ($o->units as $shortName => $unit) {
            if (is_array($unit['convert'])) {
                foreach ($unit['convert'] as $to => $function) {
                    $return[] = array($shortName, $to, $function);
                }
            }
        }
        return $return;
    }
    /**
    * Tests that the convert function exists and is actually correctly set up
    *
    * @param string $shortName The short name of the unit to convert from
    * @param string $to        The unit to convert to
    * @param string $function  The function to use to convert
    *
    * @return null
    *
    * @dataProvider dataUnitArrayConvertFunct
    */
    public function testUnitArrayConvertFunct($shortName, $to, $function)
    {
        if (substr(trim(strtolower($function)), 0, 6) != "shift:") {
            $this->assertTrue(
                method_exists($this->o, $function),
                $shortName.": conversion function ".$function." doesn't exist"
            );
        }
        $this->assertTrue(
            $this->findUnits($to),
            $shortName.": Unit ".$to." doesn't exist"
        );
    }
    /**
    * Checks to make sure a vartype is valid
    *
    * @param string $vartype The variable type to check
    *
    * @return bool
    */
    private function _checkvarType($vartype)
    {
        if ($vartype == 'float') {
            return true;
        }
        if ($vartype == 'int') {
            return true;
        }
        if ($vartype == 'text') {
            return true;
        }
        return false;
    }
    /**
    * Checks to make sure a mode is valid
    *
    * @param string $mode The mode to check
    *
    * @return bool
    */
    private function _checkMode($mode)
    {
        if ($mode == 'raw') {
            return true;
        }
        if ($mode == 'diff') {
            return true;
        }
        return false;
    }
    /**
    * Returns true if it finds the units.
    *
    * @param string $units The unit to check
    *
    * @return bool
    */
    public function findUnits($units)
    {
        return is_array($this->o->units[$units]);
    }

}
?>
