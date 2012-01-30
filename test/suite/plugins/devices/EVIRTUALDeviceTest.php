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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** Get our classes */
require_once CODE_BASE.'plugins/devices/EVIRTUALDevice.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyDeviceContainer.php';
/** This is a required class */
require_once TEST_BASE.'plugins/devices/DevicePluginTestBase.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class EVIRTUALDeviceTest extends DevicePluginTestBase
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
        $this->socket = &$this->config->sockets->getSocket("default");
         $this->d = new DummyDeviceContainer();
        $this->o = new EVIRTUALDevice($this->d);
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
            array("EVIRTUALDevice"),
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataConstructor()
    {
        return array(
            array(
                array(),
                array(
                    "PhysicalSensors" => 0,
                    "VirtualSensors" => 20,
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload This is the attribute to set
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $expect)
    {
        $this->o = new EVIRTUALDevice($this->d, $preload);
        $this->assertSame($expect, $this->d->DriverInfo);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataFromSetupString()
    {
        return array(
            array(
                "",
                array(
                    "Sensors" => 20,
                    "VirtualSensors" => 20,
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                    array("id" => 0xFE, "type" => "Placeholder", "location" => null),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload This is the attribute to set
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataFromSetupString
    */
    public function testFromSetupString($preload, $expect)
    {
        $this->o->fromSetupString($preload);
        $this->assertSame($expect, $this->d->sensors->toArray(false));
    }

}

?>
