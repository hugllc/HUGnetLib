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
 * @version    0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** Get our classes */
require_once CODE_BASE.'plugins/sensors/MaximumWindDirectionDeviceSensor.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyDeviceContainer.php';
/** This is a required class */
require_once 'DeviceSensorPluginTestBase.php';

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
class MaximumWindDirectionDeviceSensorTest extends DeviceSensorPluginTestBase
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
        $this->o = new MaximumWindDirectionDeviceSensor(
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
            array("MaximumWindDirectionDeviceSensor"),
        );
    }
    /**
     * Data provider for testGetReading
     *
     * @return array
     */
    public static function dataGetReading()
    {
        return array(
            array(array(), 0x01, 0, 0.0),
            array(array(), 0x04, 0, 90.0),
            array(array(), 0x10, 0, 180.0),
            array(array(), 0x40, 0, 270.0),
            array(array(), 0x02, 0, 45.0),
            array(array(), 0x08, 0, 135.0),
            array(array(), 0x20, 0, 225.0),
            array(array(), 0x80, 0, 315.0),
            array(array(), 0x03, 0, 22.5),
            array(array(), 0x81, 0, 337.5),
            array(array(), 0x06, 0, 67.5),
            array(array(), 0x0C, 0, 112.5),
            array(array(), 0x18, 0, 157.5),
            array(array(), 0x30, 0, 202.5),
            array(array(), 0x60, 0, 247.5),
            array(array(), 0xC0, 0, 292.5),
            // Two cardinal directions
            array(array(), 0x05, 0, null),
            // No Bits set
            array(array(), 0x00, 0, null),
            // Cardinal and ordinal not 45deg from eachother
            array(array(), 0x21, 0, null),
            // Two ordinal directions
            array(array(), 0x0A, 0, null),
        );
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @param array $preload The data to preload into the class
    * @param mixed $A       Data for the sensor to work on
    * @param float $deltaT  The time differenct
    * @param mixed $expect  The return data to expect
    *
    * @return null
    *
    * @dataProvider dataGetReading()
    */
    public function testGetReading($preload, $A, $deltaT, $expect)
    {

        $this->o->fromAny($preload);
        $ret = $this->o->getReading($A, $deltaT);
        $this->assertSame($expect, $ret);
    }

}

?>
