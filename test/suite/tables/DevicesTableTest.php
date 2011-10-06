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
 * @subpackage SuiteTables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** This is a required class */
require_once CODE_BASE.'tables/DevicesTable.php';
/** This is a required class */
require_once TEST_BASE."tables/HUGnetDBTableTestBase.php";

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteTables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DevicesTableTest extends HUGnetDBTableTestBase
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
        $this->pdo = &$this->config->servers->getPDO();
        $this->o = new DevicesTable();
        $this->o->create();
        parent::Setup();
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
    * This gets us our database preload
    *
    * @access protected
    *
    * @return null
    */
    protected function getDataSet()
    {
        return $this->createXMLDataSet(
            TEST_CONFIG_BASE.'files/DevicesTableTest.xml'
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataColumns()
    {
        $obj = new DevicesTable();
        return HUGnetDBTableTestBase::splitObject($obj, "sqlColumns");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIndexes()
    {
        $obj = new DevicesTable();
        return HUGnetDBTableTestBase::splitObject($obj, "sqlIndexes");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataVars()
    {
        return array(
            array(new DevicesTable()),
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
            array("FWVersion", "1.2.3", "1.2.3"),
            array("FWVersion", "01.02.03", "1.2.3"),
            array("FWVersion", "011005", "1.10.5"),
            array("FWPartNum", "0039280241", "0039-28-02-A"),
            array("FWPartNum", "00392802A", "0039-28-02-A"),
            array("HWPartNum", "00039-28-02-AQ", "0039-28-02-A"),
            array("DeviceID", 12, "00000C"),
            array("DeviceID", "12", "000012"),
            array("DeviceGroup", "345", "000345"),
            array("DeviceGroup", 345, "000159"),
            array("DeviceGroup", 0x345, "000345"),
            array("id", 0x159, 345),
            array("id", 1.3, 1),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $var    The variable to set
    * @param mixed  $value  The value to set
    * @param mixed  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataSet
    */
    public function testSet($var, $value, $expect)
    {
        $this->o->$var = $value;
        $data = $this->readAttribute($this->o, "data");
        $this->assertSame($expect, $data[$var]);
    }
    /**
    * data provider for testIsEmpty
    *
    * @return array
    */
    public static function dataIsEmpty()
    {
        return array(
            array(
                array(
                    "DeviceID" => "000000",
                ),
                true,
            ),
            array(
                array(
                    "DeviceID" => "000001",
                ),
                false,
            ),
            array(
                array(
                    "DeviceID" => "0000E1",
                ),
                false,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $preload The values to preload
    * @param mixed  $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataIsEmpty
    */
    public function testIsEmpty($preload, $expect)
    {
        $this->o->fromAny($preload);
        $this->assertSame($expect, $this->o->isEmpty());
    }
    /**
    * Data provider for testInsertRow
    *
    * @return array
    */
    public static function dataInsertDeviceID()
    {
        return array(
            array(
                array(
                    "DeviceID" => 156,
                    "GatewayKey" => 23,
                ),
                array(
                ),
                array(
                    array(
                        "id" => "156",
                        "DeviceID" => "00009C",
                        "DeviceName" => "",
                        "HWPartNum" => "",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup" => "",
                        "Active" => "1",
                        "GatewayKey" => "23",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "",
                        "params" => "",
                    ),
                ),
                true,
            ),
            array(
                array(
                    "DeviceID" => 156,
                    "GatewayKey" => 23,
                ),
                array(
                    array(
                        "id" => "156",
                        "DeviceID" => "00009C",
                        "DeviceName" => "",
                        "HWPartNum" => "",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup" => "",
                        "Active" => "1",
                        "GatewayKey" => "23",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "",
                        "params" => "",
                    ),
                ),
                array(
                    array(
                        "id" => "156",
                        "DeviceID" => "00009C",
                        "DeviceName" => "",
                        "HWPartNum" => "",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup" => "",
                        "Active" => "1",
                        "GatewayKey" => "23",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "",
                        "params" => "",
                    ),
                ),
                false,
            ),
            array(
                array(
                    "DeviceID" => DevicesTable::MAX_GROUP_SN,
                    "GatewayKey" => 23,
                ),
                array(
                ),
                array(
                ),
                false,
            ),
            array(
                array(
                    "DeviceID" => DevicesTable::MIN_GROUP_SN,
                    "GatewayKey" => 23,
                ),
                array(
                ),
                array(
                ),
                false,
            ),
            array(
                array(
                    "DeviceID" => DevicesTable::MIN_TEMP_SN,
                    "GatewayKey" => 23,
                ),
                array(
                ),
                array(
                ),
                false,
            ),
            array(
                array(
                    "DeviceID" => DevicesTable::MIN_TEMP_SN,
                    "GatewayKey" => 23,
                ),
                array(
                ),
                array(
                ),
                false,
            ),
            array(
                array(
                    "GatewayKey" => 23,
                ),
                array(
                ),
                array(
                ),
                false,
            ),
        );
    }
    /**
    * Tests the insert of a DeviceID
    *
    * @param mixed $data   The data to use
    * @param array $devs   Other devices to load
    * @param array $expect The expected table row
    * @param bool  $ret    The expected return
    *
    * @dataProvider dataInsertDeviceID
    *
    * @return null
    */
    public function testInsertDeviceID($data, $devs, $expect, $ret)
    {
        $dev = new DevicesTable();
        foreach ((array)$devs as $d) {
            $dev->clearData();
            $dev->fromAny($d);
            $dev->insertRow();
        }
        $return = DevicesTable::insertDeviceID($data);
        $stmt = $this->pdo->query("SELECT * FROM `devices`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows);
        $this->assertSame($ret, $return);
    }
    /**
    * Data provider for testInsertRow
    *
    * @return array
    */
    public static function dataInsertVirtual()
    {
        return array(
            array(
                array(
                    "id" => 0x156,
                ),
                array(
                ),
                array(
                    array(
                        "id" => (string)0x156,
                        "DeviceID" => "000156",
                        "DeviceName" => "",
                        "HWPartNum" => "0039-24-02-P",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup" => "",
                        "Active" => "1",
                        "GatewayKey" => "-1",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "",
                        "params" => "",
                    ),
                ),
                0x156,
            ),
            array(
                array(
                    "id" => DevicesTable::MIN_TEMP_SN,
                ),
                array(
                ),
                array(
                ),
                false,
            ),
            array(
                array(
                    "id" => 0x156,
                ),
                array(
                    array(
                        "id" => (string)0x156,
                        "DeviceID" => "000156",
                        "DeviceName" => "",
                        "HWPartNum" => "0039-24-02-P",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup" => "",
                        "Active" => "1",
                        "GatewayKey" => "-1",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "",
                        "params" => "",
                    ),
                    array(
                        "id" => (string)0x157,
                        "DeviceID" => "000157",
                        "DeviceName" => "",
                        "HWPartNum" => "0039-24-02-P",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup" => "",
                        "Active" => "1",
                        "GatewayKey" => "-1",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "",
                        "params" => "",
                    ),
                ),
                array(
                    array(
                        "id" => (string)0x156,
                        "DeviceID" => "000156",
                        "DeviceName" => "",
                        "HWPartNum" => "0039-24-02-P",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup" => "",
                        "Active" => "1",
                        "GatewayKey" => "-1",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "",
                        "params" => "",
                    ),
                    array(
                        "id" => (string)0x157,
                        "DeviceID" => "000157",
                        "DeviceName" => "",
                        "HWPartNum" => "0039-24-02-P",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup" => "",
                        "Active" => "1",
                        "GatewayKey" => "-1",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "",
                        "params" => "",
                    ),
                    array(
                        "id" => (string)0x158,
                        "DeviceID" => "000158",
                        "DeviceName" => "",
                        "HWPartNum" => "0039-24-02-P",
                        "FWPartNum" => "",
                        "FWVersion" => "",
                        "RawSetup" => "",
                        "Active" => "1",
                        "GatewayKey" => "-1",
                        "ControllerKey" => "0",
                        "ControllerIndex" => "0",
                        "DeviceLocation" => "",
                        "DeviceJob" => "",
                        "Driver" => "eDEFAULT",
                        "PollInterval" => "0",
                        "ActiveSensors" => "0",
                        "DeviceGroup" => "FFFFFF",
                        "sensors" => "",
                        "params" => "",
                    ),
                ),
                0x158,
            ),
        );
    }
    /**
    * Tests the insert of a DeviceID
    *
    * @param mixed $data   The data to use
    * @param array $devs   Other devices to load
    * @param array $expect The expected table row
    * @param bool  $ret    The expected return
    *
    * @dataProvider dataInsertVirtual
    *
    * @return null
    */
    public function testInsertVirtual($data, $devs, $expect, $ret)
    {
        $dev = new DevicesTable();
        foreach ((array)$devs as $d) {
            $dev->clearData();
            $dev->fromAny($d);
            $dev->insertRow();
        }
        $return = DevicesTable::insertVirtual($data);
        $stmt = $this->pdo->query("SELECT * FROM `devices`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame($expect, $rows);
        $this->assertSame($ret, $return);
    }
    /**
    * Data provider for testInsertRow
    *
    * @return array
    */
    public static function dataExists()
    {
        return array(
            array(
                array(
                    array(
                        "DeviceID" => 156,
                        "GatewayKey" => 23,
                    ),
                ),
                array(
                    "DeviceID" => 156,
                    "GatewayKey" => 23,
                ),
                true
            ),
            array(
                array(
                    array(
                        "DeviceID" => 158,
                        "GatewayKey" => 23,
                    ),
                ),
                array(
                    "DeviceID" => 156,
                    "GatewayKey" => 23,
                ),
                false
            ),
            array(
                array(
                ),
                array(
                    "DeviceID" => 156,
                    "GatewayKey" => 23,
                ),
                false
            ),
        );
    }
    /**
    * Tests the insert of a DeviceID
    *
    * @param array $preload The data to load into the database
    * @param mixed $data    The data to use
    * @param array $expect  The expected return
    *
    * @dataProvider dataExists
    *
    * @return null
    */
    public function testExists($preload, $data, $expect)
    {
        foreach ((array)$preload as $load) {
            $this->o->fromAny($load);
            $this->o->insertRow();
        }
        $this->o->clearData();
        $this->o->fromAny($data);
        $ret = $this->o->exists();
        $this->assertSame($expect, $ret);
    }
}

?>
