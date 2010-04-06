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


require_once dirname(__FILE__).'/../../containers/ConfigContainer.php';
require_once dirname(__FILE__).'/../stubs/DummySocketContainer.php';

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
class ConfigContainerTest extends PHPUnit_Framework_TestCase
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
                array(),
                "DBServersContainer",
                "SocketsContainer"
            ),
            array(
                dirname(__FILE__)."/../files/config1.inc.php",
                array(
                    "servers" => array(
                        array(
                            "driver" => "mysql",
                            "host" => "10.2.5.23",
                            "user" => "user",
                            "password" => "password",
                        ),
                    ),
                    "sockets" => array(
                        array(
                            "GatewayIP" => "10.2.3.5",
                            "GatewayPort" => 2001,
                        ),
                    ),
                    "hugnet_database" => "MyDatabase",
                    "admin_email" => "you@yourdomain.com",
                ),
                "DBServersContainer",
                "SocketsContainer"
            ),
            array(
                dirname(__FILE__)."/../files/config2.inc.php",
                array(
                    "servers" => array(
                        array(
                            "driver" => "mysql",
                            "host" => "10.2.5.23",
                            "user" => "user",
                            "password" => "password",
                        ),
                    ),
                    "sockets" => array(
                        array(
                            "GatewayIP" => "10.2.3.5",
                            "GatewayPort" => 2001,
                        ),
                    ),
                    "hugnet_database" => "MyDatabase",
                    "admin_email" => "you@yourdomain.com",
                ),
                "DBServersContainer",
                "SocketsContainer"
            ),
            array(
                array(
                    "servers" => array(
                        array(
                            "driver" => "mysql",
                            "host" => "10.5.12.2",
                            "port" => 3306,
                            "db" => "HUGNet",
                            "socket" => "",
                            "user" => "",
                            "password" => "",
                            "options" => array(),
                            "file" => ":memory:",
                        ),
                    ),
                    "sockets" => array(
                        array(
                            "GatewayIP" => "10.5.12.8",
                            "GatewayPort" => "2001",
                        ),
                    ),
                    "hugnet_database" => "HUGNet",
                    "script_gateway" => 4,
                    "poll" => array(
                        "enable" => true,
                    ),
                    "config"          => array(
                        "enable" => true,
                    ),
                    "control" => array(
                        "enable" => true,
                    ),
                    "check" => array(
                        "enable" => true,
                        "send_daily" => true,
                    ),
                    "analysis" => array(
                        "enable" => false,
                    ),
                    "admin_email" => "example@yourdomain.com",
                    "useSocket" => "dummy",
                ),
                array(
                    "servers" => array(
                        array(
                            "driver" => "mysql",
                            "host" => "10.5.12.2",
                            "db" => "HUGNet",
                        ),
                    ),
                    "sockets" => array(
                        array(
                            "GatewayIP" => "10.5.12.8",
                            "GatewayPort" => "2001",
                        ),
                    ),
                    "hugnet_database" => "HUGNet",
                    "script_gateway" => 4,
                    "poll" => array(
                        "enable" => true,
                    ),
                    "config"          => array(
                        "enable" => true,
                    ),
                    "control" => array(
                        "enable" => true,
                    ),
                    "check" => array(
                        "enable" => true,
                        "send_daily" => true,
                    ),
                    "admin_email" => "example@yourdomain.com",
                    "useSocket" => "dummy",
                ),
                "DBServersContainer",
                "SocketsContainer"
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The values to preload into the constructor
    * @param array $expect  The expected return
    * @param array $servers The expected class under 'servers'
    * @param array $sockets The expected class under 'sockets'
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $expect, $servers, $sockets)
    {
        $o = new ConfigContainer($preload);
        $ret = $o->toArray(false);
        $this->assertSame($expect, $ret);
        $this->assertSame($servers, get_class($o->servers));
        $this->assertSame($sockets, get_class($o->sockets));
    }

    /**
    * Check to see if the license is here
    *
    * @return null
    */
    public function testLicense()
    {
        $path = realpath(dirname(__FILE__)."/../../LICENSE.TXT");
        $this->assertSame(file_get_contents($path), ConfigContainer::license());
    }
    /**
    * Check to see if the license is here
    *
    * @return null
    */
    public function testVersion()
    {
        $path = realpath(dirname(__FILE__)."/../../VERSION.TXT");
        $this->assertSame(file_get_contents($path), ConfigContainer::version());
    }

    /**
    * Check to make sure that two instances of the class from singleton are
    * identical
    *
    * @return null
    */
    public function testSingleton()
    {
        $this->assertSame(
            ConfigContainer::singleton(),
            ConfigContainer::singleton()
        );
    }
    /**
    * Check to make sure that two instances of the class from singleton are
    * identical
    *
    * @return null
    */
    public function testConfig()
    {
        $this->assertSame(
            ConfigContainer::config(),
            ConfigContainer::config()
        );
    }
    /**
    * Checks to make sure we get the correct object returned
    *
    * @return null
    */
    public function testServers()
    {
        $o = new ConfigContainer();
        $servers = &$o->dbServers();
        $this->assertAttributeSame(
            $servers,
            "servers",
            $o
        );
        $this->assertSame("DBServersContainer", get_class($servers));
    }

}

?>
