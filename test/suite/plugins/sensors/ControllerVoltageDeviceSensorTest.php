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
require_once CODE_BASE.'plugins/sensors/ControllerVoltageDeviceSensor.php';
require_once TEST_CONFIG_BASE.'stubs/DummyDeviceContainer.php';
require_once 'DeviceSensorPluginTestBase.php';

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
class ControllerVoltageDeviceSensorTest extends DeviceSensorPluginTestBase
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
        $this->o = new ControllerVoltageDeviceSensor(
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
            array("ControllerVoltageDeviceSensor"),
        );
    }
    /**
    * Data provider for testfetBoard
    *
    * @return array
    */
    public static function dataGetReading()
    {
        return array(
            array(
                1000,
                1,
                array(),
                0.5855
            ),
            array(
                null,
                1,
                array(),
                null
            ),
            array(
                0,
                1,
                array(),
                0.0
            ),
            array(
                65535,
                1,
                array(),
                38.3702
            ),
        );
    }
    /**
    * test
    *
    * @param float $val     The incoming value
    * @param float $deltaT  The time differenct
    * @param int   $preload The values to preload into the object
    * @param mixed $expect  The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetReading
    */
    public function testGetReading($val, $deltaT, $preload, $expect)
    {
        $this->o->fromAny($preload);
        $ret = $this->o->getReading($val, $deltaT);
        $this->assertSame($expect, $ret);
    }
}

?>
