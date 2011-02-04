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
 * @subpackage Endpoint
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** Get our classes */
require_once dirname(__FILE__).'/../../../plugins/devices/E00391201Device.php';
require_once dirname(__FILE__).'/../../../base/UnitsBase.php';
require_once dirname(__FILE__).'/../../stubs/DummyDeviceContainer.php';
require_once dirname(__FILE__).'/DevicePluginTestBase.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Endpoint
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class E00391201DeviceTest extends DevicePluginTestBase
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
        $this->o = new E00391201Device($this->d);
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
            array("E00391201Device"),
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
                ),
                "",
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
                "0102020202020202027070707070707070",
                array(
                    "NumSensors" => 9,
                    "TimeConstant" => 1,
                ),
                array(
                    "Sensors" => 9,
                    0 => array(
                        "id" => 0x50,
                        "type" => "fetBoard",
                        "location" => "Out1 Current",
                    ),
                    1 => array(
                        "id" => 0x40,
                        "type" => "fetBoard",
                        "location" => "Out1 Voltage",
                    ),
                    2 => array(
                        "id" => 0x50,
                        "type" => "fetBoard",
                        "location" => "Out2 Current",
                    ),
                    3 => array(
                        "id" => 0x40,
                        "type" => "fetBoard",
                        "location" => "Out2 Voltage",
                    ),
                    4 => array(
                        "id" => 0x50,
                        "type" => "fetBoard",
                        "location" => "Out3 Current",
                    ),
                    5 => array(
                        "id" => 0x40,
                        "type" => "fetBoard",
                        "location" => "Out3 Voltage",
                    ),
                    6 => array(
                        "id" => 0x50,
                        "type" => "fetBoard",
                        "location" => "Out4 Current",
                    ),
                    7 => array(
                        "id" => 0x40,
                        "type" => "fetBoard",
                        "location" => "Out4 Voltage",
                    ),
                    8 => array(
                        "id" => 0x40,
                        "type" => "fetBoard",
                        "location" => "Main Voltage",
                    ),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload This is the attribute to set
    * @param string $expect  The expected return
    * @param array  $sensor  The sensor setup to expect
    *
    * @return null
    *
    * @dataProvider dataFromSetupString
    */
    public function testFromSetupString($preload, $expect, $sensor)
    {
        $this->o->fromSetupString($preload);
        $this->assertSame($expect, $this->d->DriverInfo, "DriverInfo Wrong");
        $this->assertSame($sensor, $this->d->sensors->toArray(), "Sensor Wrong");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecodeData()
    {
        return array(
            array(
                array(
                ),
                "37B701E31CEE00CF1FE5013B1B3101D61CE42A",
                PacketContainer::COMMAND_GETDATA,
                1,
                array(null,null, 2, null, null, null),
                array(
                    "deltaT" => 1,
                    0 => array(
                        "value" => 67.1,
                        "units" => "mA",
                        "unitType" => "Current",
                        "dataType" => UnitsBase::TYPE_RAW,
                    ),
                    1 => array(
                        "value" => 4.3805,
                        "units" => "V",
                        "unitType" => "Voltage",
                        "dataType" => UnitsBase::TYPE_RAW,
                    ),
                    2 => array(
                        "value" => 36.4,
                        "units" => "mA",
                        "unitType" => "Current",
                        "dataType" => UnitsBase::TYPE_RAW,
                    ),
                    3 => array(
                        "value" => 3.4665,
                        "units" => "V",
                        "unitType" => "Voltage",
                        "dataType" => UnitsBase::TYPE_RAW,
                    ),
                    4 => array(
                        "value" => 74.1,
                        "units" => "mA",
                        "unitType" => "Current",
                        "dataType" => UnitsBase::TYPE_RAW,
                    ),
                    5 => array(
                        "value" => 4.8986,
                        "units" => "V",
                        "unitType" => "Voltage",
                        "dataType" => UnitsBase::TYPE_RAW,
                    ),
                    6 => array(
                        "value" => 46.6,
                        "units" => "mA",
                        "unitType" => "Current",
                        "dataType" => UnitsBase::TYPE_RAW,
                    ),
                    7 => array(
                        "value" => 4.3964,
                        "units" => "V",
                        "unitType" => "Voltage",
                        "dataType" => UnitsBase::TYPE_RAW,
                    ),
                    8 => array(
                        "value" => 13.4164,
                        "units" => "V",
                        "unitType" => "Voltage",
                        "dataType" => UnitsBase::TYPE_RAW,
                    ),
                    "DataIndex" => 55,
                    "timeConstant" => 1,
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $device  The device preload
    * @param string $data    The raw data
    * @param string $command The command that was used to get the data
    * @param float  $deltaT  The time difference between this packet and the next
    * @param float  $prev    The previous record
    * @param array  $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataDecodeData
    */
    public function testDecodeData($device, $data, $command, $deltaT, $prev, $expect)
    {
        $this->d = new DeviceContainer($device);
        $this->o = new E00391201Device($this->d);
        $ret = $this->o->decodeData($data, $command, $deltaT, $prev);
        $this->assertSame($expect, $ret, "Arrays are not the same");
    }

}

?>
