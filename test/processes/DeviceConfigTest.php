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
 * @category   Processes
 * @package    HUGnetLibTest
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../processes/DeviceConfig.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Processes
 * @package    HUGnetLibTest
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceConfigTest extends PHPUnit_Framework_TestCase
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
            "script_gateway" => 1,
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->config->sockets->forceDeviceID("000019");
        $this->socket = &$this->config->sockets->getSocket();
        $this->pdo = &$this->config->servers->getPDO();
        $this->d = new DeviceContainer(
            array(
                "DeviceID"   => "000019",
            )
        );
        $this->o = new DeviceConfig(array(), $this->d);
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
        $this->o = null;
        $this->config = null;
    }
    /**
    * Tests for exceptions
    *
    * @expectedException Exception
    *
    * @return null
    */
    public function testConstructTableExec()
    {
        $config = array(

        );
        $this->config->forceConfig($config);
        $o = new DeviceConfig(array(), $this->d);
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
                    "group" => "default",
                    "GatewayKey" => 1,
                ),
            ),
           array(
                array(
                    "group" => "test",
                    "GatewayKey" => 3,
                ),
                array(
                    "group" => "test",
                    "GatewayKey" => 3,
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The value to preload
    * @param array $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $expect)
    {
        $o = new DeviceConfig($preload, $this->d);
        $ret = $this->readAttribute($o, "data");
        $this->assertSame($expect, $ret);
        // Check the configuration is set correctly
        $config = $this->readAttribute($o, "myConfig");
        $this->assertSame("ConfigContainer", get_class($config));
        // Check the configuration is set correctly
        $device = $this->readAttribute($o, "device");
        $this->assertSame("DeviceContainer", get_class($device));
    }

    /**
    * data provider for testConfig
    *
    * @return array
    */
    public static function dataConfig()
    {
        return array(
            array(
                array(
                    array(
                        "DeviceID" => "123456",
                        "GatewayKey" => 1,
                    ),
                    array(
                        "DeviceID" => "654321",
                        "GatewayKey" => 2,
                    ),
                    array(
                        "DeviceID" => "000019",
                        "GatewayKey" => 1,
                    ),
                ),
                (string)new PacketContainer(array(
                    "From" => "123456",
                    "To" => "000019",
                    "Command" => PacketContainer::COMMAND_REPLY,
                    "Data" => "000012345600391101410039201343000009FFFFFF50",
                )),
                (string)new PacketContainer(array(
                    "To" => "123456",
                    "From" => "000019",
                    "Command" => PacketContainer::COMMAND_GETSETUP,
                    "Data" => "",
                )),
            ),
            array(
                array(
                ),
                "",
                "",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The data to preload into the devices table
    * @param string $read    The read string for the socket
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataConfig
    */
    public function testConfig($preload, $read, $expect)
    {
        $d = new DeviceContainer();
        foreach ((array)$preload as $load) {
            $d->fromArray($load);
            $d->insertRow(true);
        }
        $this->socket->readString = $read;
        $this->o->config();
        $this->assertSame($expect, $this->socket->writeString);
    }
}


?>
