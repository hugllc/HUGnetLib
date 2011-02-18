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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Default
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** Get our classes */
require_once dirname(__FILE__)
    .'/../../../plugins/sensors/ComputationVirtualSensor.php';
require_once dirname(__FILE__).'/../../stubs/DummyDeviceContainer.php';
require_once dirname(__FILE__).'/DeviceSensorPluginTestBase.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Default
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ComputationVirtualSensorTest extends DeviceSensorPluginTestBase
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
        $this->o = new ComputationVirtualSensor(
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
            array("ComputationVirtualSensor"),
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
                array('extra'=>array("", "&#176;C", "Temperature", 3)),
                array(
                    "id" => 0xFE,
                    "type" => "computation",
                    "location" => "",
                    "dataType" => UnitsBase::TYPE_RAW,
                    "extra" => array("", "&#176;C", "Temperature", 3),
                    "units" => "",
                    "rawCalibration" => "",
                    "units" => '&#176;F',
                    "decimals" => 3,
                ),
            ),
            array(
                array('extra'=>array("", "&#176;C", "Generic", 3)),
                array(
                    "id" => 0xFE,
                    "type" => "computation",
                    "location" => "",
                    "dataType" => UnitsBase::TYPE_RAW,
                    "extra" => array("", "&#176;C", "Generic", 3),
                    "units" => "",
                    "rawCalibration" => "",
                    "units" => '&#176;C',
                    "decimals" => 3,
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
        $o = new ComputationVirtualSensor($preload, $this->d);
        $this->assertSame($expect, $o->toArray());
    }
    /**
    * Data provider for testGetReading
    *
    * @return array
    */
    public static function dataGetVirtualReading()
    {
        return array(
            array(
                array(
                    "extra" => array(
                        "(({1} - {2}) / 3) + 1",
                        "&#176;C",
                        "Temperature",
                        "3"
                    ),
                ),
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 2),
                ),
                2.0
            ),
            array(
                array(
                    "extra" => array(
                        "({1} + {2}) + 1",
                        "&#176;C",
                        "Temperature",
                        "3"
                    ),
                ),
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 2),
                ),
                8.0
            ),
            array(
                array(
                    "extra" => array(
                        "({1} / {2})",
                        "&#176;C",
                        "Temperature",
                        "3"
                    ),
                ),
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 3),
                ),
                1.667
            ),
            array(
                array(
                    "extra" => array(
                        "({1} / ",
                        "&#176;C",
                        "Temperature",
                        "3"
                    ),
                ),
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 3),
                ),
                null
            ),
            array(
                array(
                    "extra" => array(
                        "badfunction();",
                        "&#176;C",
                        "Temperature",
                        "3"
                    ),
                ),
                array(
                    0 => array("value" => 5),
                    1 => array("value" => 3),
                ),
                null
            ),
            array(
                array(
                    "extra" => array(
                        "sqrt({1});",
                        "&#176;C",
                        "Temperature",
                        "3"
                    ),
                ),
                array(
                    0 => array("value" => 4),
                    1 => array("value" => 3),
                ),
                4.0
            ),
        );
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @param array $preload The data to preload into the class
    * @param mixed $data    The data for the sensor to work with 
    * @param mixed $expect  The return data to expect
    *
    * @return null
    *
    * @dataProvider dataGetVirtualReading()
    */
    public function testGetVirtualReading($preload, $data, $expect)
    {

        $o = new ComputationVirtualSensor($preload, $this->d);
        $ret = $o->getVirtualReading($data);
        $this->assertSame($expect, $ret);
    }

}

?>
