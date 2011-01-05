<?php
/**
 * Tests the filter class
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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

// Need to make sure this file is not added to the code coverage
PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);
require_once dirname(__FILE__)."/../PluginTestBase.php";
require_once dirname(__FILE__).'/../../../containers/DeviceContainer.php';
/**
 * Test class for device drivers
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class DeviceSensorPluginTestBase extends PluginTestBase
{
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class The class to use
    *
    * @return null
    *
    * @dataProvider dataRegisterPlugin
    */
    public function testRegisterPluginDevices($class)
    {
        $d = new DeviceContainer();
        $var = eval("return $class::\$registerPlugin;");
        $obj = new $class($data, $d);
        $this->assertInternalType(
            "array",
            $var["Flags"],
            "Flags is not an array"
        );
        foreach ($var["Flags"] as $key => $sensor) {
            $this->assertInternalType("string", $sensor, "Sensor $key is not a string");
            $this->assertRegExp(
                "/([0-9A-F]{2}[:]{0,1}[0-9A-Za-z]{0,})|DEFAULT/",
                $sensor,
                "Sensor string is not of the form 'id:sensorType'"
            );
            if ($sensor !== "DEFAULT") {
                // These need to be put here
                $idValues = $this->readAttribute($obj, "idValues");
                $typeValues = $this->readAttribute($obj, "typeValues");
                $sen = explode(":", $sensor);
                $this->assertTrue(
                    in_array(hexdec($sen[0]), $idValues),
                    hexdec($sen[0])." is not in idValues"
                );
                if (strlen($sen[1])) {
                    $this->assertTrue(
                        in_array($sen[1], $typeValues),
                        $sen[1]." is not in typeValues"
                    );
                }
            }
        }
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class The class to use
    *
    * @return null
    *
    * @dataProvider dataRegisterPlugin
    */
    public function testParent($class)
    {
        $this->assertTrue(is_subclass_of($class, "DeviceSensorBase"));
    }
    /**
    * data provider for testSet
    *
    * @return array
    */
    public static function dataSet()
    {
        return array(
            array("location", "test", "test"),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $var    The variable to set
    * @param mixed  $value  The value to set
    * @param mixed  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataSet
    */
    public function testSet($var, $value, $expect)
    {
        $this->o->$var = $value;
        $data = $this->readAttribute($this->o, "data");
        $this->assertSame($expect, $data[$var]);
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class The class to use
    *
    * @return null
    *
    * @dataProvider dataRegisterPlugin
    */
    public function testDefaultFixed($class)
    {
        $d = new DeviceContainer();
        $var = eval("return $class::\$registerPlugin;");
        $obj = new $class($data, $d);
        $default = $this->readAttribute($obj, "default");
        $fixed = $this->readAttribute($obj, "fixed");
        $this->assertInternalType("array", $default, "default is not an array");
        $this->assertInternalType("array", $fixed, "fixed is not an array");
        $this->assertSame(
            array(),
            array_intersect(array_keys($fixed), array_keys($default)),
            "Array keys in fixed and default should not overlap"
        );
        $fields = array(
            "id",
            "type",
            "location",
            "dataType",
            "extra",
            "units",
            "rawCalibration",
            "longName",
            "unitType",
            "units",
            "extraText",
            "extraDefault",
            "storageUnit",
        );
        foreach ($fields as $f) {
            $this->assertTrue(
                isset($default[$f]) || isset($fixed[$f]),
                "field $f is missing from default or fixed"
            );
        }
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class The class to use
    *
    * @return null
    *
    * @dataProvider dataRegisterPlugin
    */
    public function testDefaultFixedArray($class)
    {
        $d = new DeviceContainer();
        $var = eval("return $class::\$registerPlugin;");
        $obj = new $class($data, $d);
        $fields = array(
            "extraText",
            "extraDefault",
        );
        foreach ($fields as $f) {
            $val = $obj->$f;
            $this->assertInternalType(
                "array",
                $val,
                "field $f is not an array"
            );
        }
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class The class to use
    *
    * @return null
    *
    * @dataProvider dataRegisterPlugin
    */
    public function testDefaultFixedNotEmpty($class)
    {
        $d = new DeviceContainer();
        $var = eval("return $class::\$registerPlugin;");
        $obj = new $class($data, $d);
        $fields = array(
            "type",
            "dataType",
            "longName",
            "unitType",
            "storageUnit",
        );
        foreach ($fields as $f) {
            $val = $obj->$f;
            $this->assertFalse(
                empty($val),
                "field $f can not be empty"
            );
        }
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class The class to use
    *
    * @return null
    *
    * @dataProvider dataRegisterPlugin
    */
    public function testDefaultFixedInt($class)
    {
        $d = new DeviceContainer();
        $var = eval("return $class::\$registerPlugin;");
        $obj = new $class($data, $d);
        $default = $this->readAttribute($obj, "default");
        $fixed = $this->readAttribute($obj, "fixed");
        $fields = array(
            "id",
        );
        foreach ($fields as $f) {
            $val = $obj->$f;
            $this->assertTrue(
                is_int($val),
                "field $f can not be empty"
            );
        }
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class The class to use
    *
    * @return null
    *
    * @dataProvider dataRegisterPlugin
    */
    public function testDefaultFixedExtra($class)
    {
        $d = new DeviceContainer();
        $var = eval("return $class::\$registerPlugin;");
        $obj = new $class($data, $d);
        $this->assertSame(
            count($obj->extraText),
            count($obj->extraDefault),
            "ExtraDefault and extraText need to have the same number of entries"
        );
    }
}

?>
