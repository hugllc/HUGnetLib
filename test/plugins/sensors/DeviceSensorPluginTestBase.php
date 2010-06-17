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
PHPUnit_Util_Filter::addFileToFilter(__FILE__);
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
        $this->assertType(
            "array",
            $var["Flags"],
            "Flags is not an array"
        );
        foreach ($var["Flags"] as $key => $sensor) {
            $this->assertType("string", $sensor, "Sensor $key is not a string");
            $this->assertRegExp(
                "/([0-9]{2}[:]{0,1}[0-9A-Za-z]{0,})|DEFAULT/",
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
}

?>
