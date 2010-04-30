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


require_once dirname(__FILE__).'/../../containers/DeviceSensorsContainer.php';
require_once dirname(__FILE__).'/../../containers/ConfigContainer.php';
require_once dirname(__FILE__).'/../stubs/DummyDeviceContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
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
class DeviceSensorsContainerTest extends PHPUnit_Framework_TestCase
{

    /**
    * Sets up the fixture, for example, open a network connection.
    * This method is called before a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function setUp()
    {
        $config = array(
            "sockets" => array(
                array(
                    "dummy" => true,
                ),
            ),
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->d = new DummyDeviceContainer();
        $this->o = new DeviceSensorsContainer($data, $this->d);
    }

    /**
    * Tears down the fixture, for example, close a network connection.
    * This method is called after a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function tearDown()
    {
        unset($this->o);
    }


    /**
    * data provider for testConstructor
    *
    * @return array
    */
    public static function dataConstructor()
    {
        return array(
            array(
                array(),
                array(
                    "RawCalibration" => "",
                    "Sensors" => 0,
                ),
            ),
            array(
                array(
                    "RawCalibration" => "Hello There",
                    "Sensors" => 10,
                ),
                array(
                    "RawCalibration" => "Hello There",
                    "Sensors" => 0,
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to give to the constructor
    * @param string $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $expect)
    {
        $o = new DeviceSensorsContainer($preload, $this->d);
        $this->assertAttributeSame($expect, "data", $o, "Wrong data in class");
        $config = $this->readAttribute($o, "myConfig");
        $this->assertSame(
            "ConfigContainer", get_class($config), "Wrong config class"
        );
        $device = $this->readAttribute($o, "myDevice");
        $this->assertSame(
            get_class($this->d), get_class($device), "Wrong device class"
        );
    }
    /**
    * data provider for testConstructor
    *
    * @return array
    */
    public static function dataFromArray()
    {
        return array(
            array(
                array(),
                2,
                array(
                    "RawCalibration" => "",
                    "Sensors" => 2,
                ),
                array(
                    0 => array(
                        "Loc" => "",
                        "sensorType" => "",
                        "Units" => "",
                        "dataType" => "raw",
                        "Extra" => array(),
                        "RawCalibration" => "",
                    ),
                    1 => array(
                        "Loc" => "",
                        "sensorType" => "",
                        "Units" => "",
                        "dataType" => "raw",
                        "Extra" => array(),
                        "RawCalibration" => "",
                    ),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed $preload      The stuff to give to the constructor
    * @param int   $TotalSensors The total number of sensors
    * @param array $expect       The expected data
    * @param array $sensors      The expected sensor data
    *
    * @return null
    *
    * @dataProvider dataFromArray
    */
    public function testFromArray($preload, $TotalSensors, $expect, $sensors)
    {
        $this->d->DriverInfo["TotalSensors"] = $TotalSensors;
        $this->o->clearData();
        $this->o->fromArray($preload);
        $s = $this->readAttribute($this->o, "sensor");
        foreach (array_keys((array)$s) as $k) {
            $this->assertSame(
                $sensors[$k], $s[$k]->toArray(), "Sensor $k wrong"
            );
        }
        $this->assertAttributeSame($expect, "data", $this->o);
    }
    /**
    * data provider for testConstructor
    *
    * @return array
    */
    public static function dataToArray()
    {
        return array(
            array(
                array(
                    "RawCalibration" => "",
                    "Sensors" => 2,
                    0 => array(
                        "Loc" => "",
                        "sensorType" => "",
                        "Units" => "",
                        "dataType" => "raw",
                        "Extra" => array(),
                        "RawCalibration" => "",
                    ),
                    1 => array(
                        "Loc" => "",
                        "sensorType" => "",
                        "Units" => "",
                        "dataType" => "raw",
                        "Extra" => array(),
                        "RawCalibration" => "",
                    ),
                ),
                2,
                true,
                array(
                    "RawCalibration" => "",
                    "Sensors" => 2,
                    0 => array(
                        "Loc" => "",
                        "sensorType" => "",
                        "Units" => "",
                        "dataType" => "raw",
                        "Extra" => array(),
                        "RawCalibration" => "",
                    ),
                    1 => array(
                        "Loc" => "",
                        "sensorType" => "",
                        "Units" => "",
                        "dataType" => "raw",
                        "Extra" => array(),
                        "RawCalibration" => "",
                    ),
                ),

            ),
            array(
                array(
                    "RawCalibration" => "abcd",
                    "Sensors" => 2,
                    0 => array(
                        "Loc" => "Here and There",
                        "sensorType" => "resistive",
                        "Units" => "Ohms",
                        "dataType" => "raw",
                        "Extra" => array(),
                        "RawCalibration" => "",
                    ),
                    1 => array(
                        "Loc" => "",
                        "sensorType" => "",
                        "Units" => "",
                        "dataType" => "diff",
                        "Extra" => array("here"),
                        "RawCalibration" => "12345",
                    ),
                ),
                2,
                true,
                array(
                    "RawCalibration" => "abcd",
                    "Sensors" => 2,
                    0 => array(
                        "Loc" => "Here and There",
                        "sensorType" => "resistive",
                        "Units" => "Ohms",
                        "dataType" => "raw",
                        "Extra" => array(),
                        "RawCalibration" => "",
                    ),
                    1 => array(
                        "Loc" => "",
                        "sensorType" => "",
                        "Units" => "",
                        "dataType" => "diff",
                        "Extra" => array("here"),
                        "RawCalibration" => "12345",
                    ),
                ),

            ),
            array(
                array(
                    "RawCalibration" => "",
                    "Sensors" => 2,
                    0 => array(
                        "Loc" => "",
                        "sensorType" => "",
                        "Units" => "",
                        "dataType" => "raw",
                        "Extra" => array(),
                        "RawCalibration" => "",
                    ),
                    1 => array(
                        "Loc" => "",
                        "sensorType" => "",
                        "Units" => "",
                        "dataType" => "raw",
                        "Extra" => array(),
                        "RawCalibration" => "",
                    ),
                ),
                2,
                false,
                array(
                    "Sensors" => 2,
                ),

            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed $preload      The stuff to give to the constructor
    * @param int   $TotalSensors The total number of sensors
    * @param bool  $default      Whether to give default stuff
    * @param array $expect       The expected data
    *
    * @return null
    *
    * @dataProvider dataToArray
    */
    public function testToArray($preload, $TotalSensors, $default, $expect)
    {
        $this->d->DriverInfo["TotalSensors"] = $TotalSensors;
        $this->o->clearData();
        $this->o->fromArray($preload);
        $this->assertSame($expect, $this->o->toArray($default));
    }

}

?>
