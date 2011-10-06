<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    git: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** Get our classes */
require_once CODE_BASE.'plugins/virtualSensors/LinearTransformVirtualSensor.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyDeviceContainer.php';
/** This is a required class */
require_once 'VirtualSensorPluginTestBase.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class LinearTransformVirtualSensorTest extends VirtualSensorPluginTestBase
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
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->socket = &$this->config->sockets->getSocket("default");
        $this->d = new DummyDeviceContainer();
        $this->o = new LinearTransformVirtualSensor(
            array(
            ),
            $this->d
        );
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
    * Data provider for testRegisterPlugin
    *
    * @return array
    */
    public static function dataRegisterPlugin()
    {
        return array(
            array("LinearTransformVirtualSensor"),
        );
    }
    /**
    * data provider for testSet
    *
    * @return array
    */
    public static function dataSet()
    {
        return array(
            array("location", "raw", "raw"),
        );
    }
    /**
    * Data provider for testConstructor
    *
    * @return array
    */
    public static function dataConstructor()
    {
        return array(
            array(
                array(
                    'extra' => array(
                        1, 10, 5.5, 0, 60,
                        "&#176;C", "Temperature", UnitsBase::TYPE_RAW, 3
                    )
                ),
                array(
                    "id" => 0xFE,
                    "type" => "lineartransform",
                    "location" => "",
                    "dataType" => UnitsBase::TYPE_RAW,
                    'extra' => array(
                        1, 10, 5.5, 0, 60,
                        "&#176;C", "Temperature", UnitsBase::TYPE_RAW, 3
                    ),
                    "units" => '&#176;F',
                    "bound" => false,
                    "rawCalibration" => "",
                    "decimals" => 3,
                    "filter" => array(),
                ),
            ),
            array(
                array(
                    'extra'=>array(
                        "", "", "", "none", "none",
                        "&#176;C", "Generic", UnitsBase::TYPE_DIFF, 3
                    )
                ),
                array(
                    "id" => 0xFE,
                    "type" => "lineartransform",
                    "location" => "",
                    "dataType" => UnitsBase::TYPE_DIFF,
                    'extra' => array(
                        "", "", "", "none", "none",
                        "&#176;C", "Generic", UnitsBase::TYPE_DIFF, 3
                    ),
                    "units" => '&#176;C',
                    "bound" => false,
                    "rawCalibration" => "",
                    "decimals" => 3,
                    "filter" => array(),
                ),
            ),
        );
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @param array $preload The data to preload into the class
    * @param mixed $expect  The return data to expect
    *
    * @return null
    *
    * @dataProvider dataConstructor()
    */
    public function testConstructor($preload, $expect)
    {
        $o = new LinearTransformVirtualSensor($preload, $this->d);
        $this->assertSame($expect, $o->toArray(true));
    }
    /**
    * Data provider for testGetReading
    *
    * @return array
    */
    public static function dataGetReading()
    {
        return array(
            array( // #0
                array(
                    "extra" => array(
                        1, 10, 5.5, 0, 60,
                        "&#176;C",
                        "Temperature",
                        UnitsBase::TYPE_RAW,
                        "3"
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 2),
                ),
                55.5
            ),
            array( // #1
                array(
                    "extra" => array(
                        1, 10, 5.5, 0, 60,
                        "&#176;C",
                        "Temperature",
                        UnitsBase::TYPE_RAW,
                        "3"
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => null),
                    1 => array("value" => 2),
                ),
                null
            ),
            array( // #2
                array(
                    "extra" => array(
                        0, 10, 5.5, 0, 60,
                        "&#176;C",
                        "Temperature",
                        UnitsBase::TYPE_RAW,
                        "3"
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 2),
                ),
                null
            ),
            array( // #3
                array(
                    "extra" => array(
                        1, 20, 5.5, 0, 60,
                        "&#176;C",
                        "Temperature",
                        UnitsBase::TYPE_RAW,
                        "3"
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 2),
                ),
                60.0
            ),
            array( // #3
                array(
                    "extra" => array(
                        1, 1, -5.5, 0, 60,
                        "&#176;C",
                        "Temperature",
                        UnitsBase::TYPE_RAW,
                        "3"
                    ),
                ),
                0,
                0,
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 2),
                ),
                0.0
            ),
        );
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @param array $preload The data to preload into the class
    * @param int   $A       Output of the A to D converter
    * @param float $deltaT  The time delta in seconds between this record
    * @param mixed $data    The data for the sensor to work with
    * @param mixed $expect  The return data to expect
    *
    * @return null
    *
    * @dataProvider dataGetReading()
    */
    public function testGetReading($preload, $A, $deltaT, $data, $expect)
    {

        $o = new LinearTransformVirtualSensor($preload, $this->d);
        $ret = $o->getReading($A, $deltaT, $data);
        $this->assertSame($expect, $ret);
    }

}

?>
