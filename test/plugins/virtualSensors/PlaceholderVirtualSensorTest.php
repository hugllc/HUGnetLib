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
    .'/../../../plugins/virtualSensors/PlaceholderVirtualSensor.php';
require_once dirname(__FILE__).'/../../stubs/DummyDeviceContainer.php';
require_once dirname(__FILE__).'/VirtualSensorPluginTestBase.php';

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
class PlaceholderVirtualSensorTest extends VirtualSensorPluginTestBase
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
        $this->o = new PlaceholderVirtualSensor(
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
            array("PlaceholderVirtualSensor"),
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
                array('extra'=>array(10)),
                array("id" => 0xFE, "type" => "Placeholder", "location" => null),
            ),
            array(
                array('location' => "Hello"),
                array("id" => 0xFE, "type" => "Placeholder", "location" => "Hello"),
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
        $o = new PlaceholderVirtualSensor($preload, $this->d);
        $this->assertSame($expect, $o->toArray(false));
    }
    /**
    * Data provider for testGetReading
    *
    * @return array
    */
    public static function dataGetReading()
    {
        return array(
            array(
                array('extra'=>array(10)),
                0,
                0,
                63630,
                null
            ),
            array(
                array('dataType' => DeviceSensorBase::TYPE_IGNORE),
                0,
                0,
                5000,
                null
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

        $o = new PlaceholderVirtualSensor($preload, $this->d);
        $ret = $o->getReading($A, $deltaT, $data);
        $this->assertSame($expect, $ret);
    }

}

?>
