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


require_once dirname(__FILE__).'/../../../plugins/devices/E00392600Device.php';
require_once dirname(__FILE__).'/../../stubs/DummyDeviceContainer.php';
require_once dirname(__FILE__).'/DevicePluginTestBase.php';

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
class E00392600DeviceTest extends DevicePluginTestBase
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
        $this->config->sockets->forceDeviceID("000019");
        $this->socket = &$this->config->sockets->getSocket("default");
        $this->pdo = &$this->config->servers->getPDO();
        $this->d = new DummyDeviceContainer();
        $this->d->DeviceID = "000019";
        $this->o = new E00392600Device($this->d);
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
    * test the loadable routine.
    *
    * @return null
    */
    public function testGateway()
    {
        $this->assertTrue($this->o->gateway());
    }

    /**
    * Data provider for testRegisterPlugin
    *
    * @return array
    */
    public static function dataRegisterPlugin()
    {
        return array(
            array("E00392600Device"),
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataToSetupString()
    {
        return array(
            array(
                array(
                    "NumSensors" => 0,
                    "Job" => 6,
                    "GatewayKey" => 12,
                    "Name" => "Scott's Device Here",
                    "IP" => "1.2.3.4",
                    "Priority" => 231,
                ),
                "06000C53636F747427732044657669636520486572650000000000000000000000"
                    ."01020304E7"
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
    * @dataProvider dataToSetupString
    */
    public function testToSetupString($preload, $expect)
    {
        $this->d->DriverInfo = $preload;
        $this->d->GatewayKey = (int)$preload["GatewayKey"];
        $ret = $this->o->toSetupString();
        $this->assertSame($expect, $ret);
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
                "06000C53636F747427732044657669636520486572650000000000000000000000"
                    ."01020304E7",
                array(
                    "NumSensors" => 0,
                    "Job" => 6,
                    "Function" => "Config",
                    "CurrentGatewayKey" => 12,
                    "Name" => "Scott's Device Here",
                    "IP" => "1.2.3.4",
                    "Priority" => 231,
                ),
            ),
            array(
                "0A000C53636F747427732044657669636520486572650000000000000000000000"
                    ."0102030412",
                array(
                    "NumSensors" => 0,
                    "Job" => 10,
                    "Function" => "Unknown",
                    "CurrentGatewayKey" => 12,
                    "Name" => "Scott's Device Here",
                    "IP" => "1.2.3.4",
                    "Priority" => 18,
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
        $this->assertSame($expect, $this->d->DriverInfo);
    }
    /**
    * data provider for testCompareFWVesrion
    *
    * @return array
    */
    public static function dataReadSetupTime()
    {
        return array(
            array(time(), 60, false),
            array("2004-01-01 00:00:00", 12, true),
            array(time()-70, 1, true),
        );
    }
    /**
    * test
    *
    * @param string $lastConfig The last config date
    * @param int    $interval   The second version
    * @param bool   $expect     What to expect
    *
    * @return null
    *
    * @dataProvider dataReadSetupTime
    */
    function testReadSetupTime($lastConfig, $interval, $expect)
    {
        $this->d->params->DriverInfo["LastConfig"] = $lastConfig;
        $ret = $this->o->readSetupTime($interval);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testPacketConsumer
    *
    * @return array
    */
    public static function dataPacketConsumer()
    {
        return array(
            array(
                array(
                ),
                array(
                    "To" => "000000",
                    "From" => "123456",
                    "Command" => "5C",
                    "group" => "default",
                ),
                array(
                ),
                "",
            ),
            array(
                array(
                ),
                array(
                    "To" => "000019",
                    "From" => "123456",
                    "Command" => "5C",
                    "group" => "default",
                ),
                array(
                ),
                "5A5A5A0112345600001916000000000100392601500039260150010203"
                    ."FFFFFF1090",
            ),
            array(
                array(
                ),
                array(
                    "To" => "000019",
                    "From" => "123456",
                    "Command" => "03",
                    "Data" => "01020304",
                    "group" => "default",
                ),
                array(
                ),
                "5A5A5A01123456000019040102030468",
            ),
            array(
                array(
                ),
                array(
                    "To" => "000019",
                    "From" => "123456",
                    "Command" => "02",
                    "Data" => "01020304",
                    "group" => "default",
                ),
                array(
                ),
                "5A5A5A01123456000019040102030468",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The data to preload into the devices table
    * @param string $pkt     The packet string to use
    * @param string $expect  The expected return
    * @param string $write   The packet string expected to be written
    *
    * @return null
    *
    * @dataProvider dataPacketConsumer
    */
    public function testPacketConsumer($preload, $pkt, $expect, $write)
    {
        $d = new DeviceContainer();
        foreach ((array)$preload as $load) {
            $d->fromArray($load);
            $d->insertRow(true);
        }
        $p = new PacketContainer($pkt);
        $this->o->packetConsumer($p);
        $stmt = $this->pdo->query("SELECT * FROM `devices`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows);
        $this->assertSame($write, $this->socket->writeString);

    }

}

?>
