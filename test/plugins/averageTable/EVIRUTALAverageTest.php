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
    .'/../../../plugins/averageTable/EVIRTUALAverageTable.php';
require_once dirname(__FILE__).'/../../../base/UnitsBase.php';
require_once dirname(__FILE__).'/AverageTablePluginTestBase.php';
require_once dirname(__FILE__)."/../../files/mocks/HistoryTableMock.php";

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
class EVIRTUALAverageTableTest extends AverageTablePluginTestBase
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
        $this->o = new EVIRTUALAverageTable(
            array(
            )
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
            array("EVIRTUALAverageTable"),
        );
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @return null
    */
    public function testSqlTable()
    {
        $this->assertSame("eVIRTUAL_average", $this->o->sqlTable);
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @return null
    */
    public function testDatacols()
    {
        $this->assertSame(20, $this->o->datacols);
    }
    /**
    * data provider for testCalcAverage
    *
    * @return array
    */
    public static function dataCalc15MinAverage()
    {
        return array(
            array(  // #0 basic input.  LastAverage15MIN not set
                array(
                    array(
                        "id" => 0xE10,
                        "DeviceID" => "000E10",
                        "sensors" => array(
                            "Sensors" => 3,
                            "PhysicalSensors" => 2,
                            "VirtualSensors" => 1,
                            array("id" => 0x02),
                            array("id" => 0x02),
                            array(
                                "id" => 0xFE,
                                "type" => "computation",
                                "extra" => array(
                                    "{2} - {1}", "&#176;C", "Temperature",
                                    UnitsBase::TYPE_DIFF, 4
                                ),
                            ),
                        ),
                        "params" => array(
                            "DriverInfo" => array(
                                "LastAverage15MIN" => gmmktime(
                                    15, 00, 00, 1, 22, 2009
                                ),
                            ),
                        ),
                    ),
                    array(
                        "id" => 0xE20,
                        "DeviceID" => "000E20",
                        "sensors" => array(
                            "Sensors" => 3,
                            "PhysicalSensors" => 2,
                            "VirtualSensors" => 1,
                            array("id" => 0x02),
                            array("id" => 0x02),
                            array(
                                "id" => 0xFE,
                                "type" => "computation",
                                "extra" => array(
                                    "{2} - {1}", "&#176;C", "Temperature",
                                    UnitsBase::TYPE_DIFF, 4
                                ),
                            ),
                        ),
                        "params" => array(
                            "DriverInfo" => array(
                                "LastAverage15MIN" => gmmktime(
                                    15, 00, 00, 1, 22, 2009
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                ),
                array(
                    0xE10 => array(
                        array(
                            "id" => 0xE10,
                            "Type" => AverageTableBase::AVERAGE_15MIN,
                            "Date" => gmmktime(15, 00, 00, 1, 22, 2009),
                            "Data0" => 4.0,
                            "Data1" => 2.0,
                            "Data2" => 1.0,
                        ),
                    ),
                    0xE20 => array(
                        array(
                            "id" => 0xE20,
                            "Type" => AverageTableBase::AVERAGE_15MIN,
                            "Date" => gmmktime(15, 00, 00, 1, 22, 2009),
                            "Data0" => 1.0,
                            "Data1" => 2.0,
                            "Data2" => 1.0,
                        ),
                    ),
                ),
                array(
                    "id" => 0x1000,
                    "DeviceID" => "001000",
                    "HWPartNum" => "0039-24-02-P",
                    "sensors" => array(
                        "Sensors" => 3,
                        "PhysicalSensors" => 0,
                        "VirtualSensors" => 3,
                        array(
                            "id" => 0xFE,
                            "type" => "physicalpoint",
                            "extra" => array("000E20", 1),
                        ),
                        array(
                            "id" => 0xFE,
                            "type" => "physicalpoint",
                            "extra" => array("000E10", 1),
                        ),
                        array(
                            "id" => 0xFE,
                            "type" => "computation",
                            "extra" => array(
                                "{2} - {1}", "&#176;C", "Temperature",
                                UnitsBase::TYPE_DIFF, 4
                            ),
                        ),
                    ),
                ),
                array(
                    "id" => 0x1000,
                    "Date" => gmmktime(15, 00, 00, 1, 22, 2009),
                    "Data0" => "1.0",
                    "Data1" => "4.0",
                    "Data2" => 3.0,
                ),
                true,
            ),
            array(  // #1 Nothing to do
                array(
                    array(
                        "id" => 0xE10,
                        "DeviceID" => "000E10",
                        "sensors" => array(
                            "Sensors" => 3,
                            "PhysicalSensors" => 2,
                            "VirtualSensors" => 1,
                            array("id" => 0x02),
                            array("id" => 0x02),
                            array(
                                "id" => 0xFE,
                                "type" => "computation",
                                "extra" => array(
                                    "{2} - {1}", "&#176;C", "Temperature",
                                    UnitsBase::TYPE_DIFF, 4
                                ),
                            ),
                        ),
                        "params" => array(
                            "DriverInfo" => array(
                                "LastAverage15MIN" => gmmktime(
                                    15, 00, 00, 1, 22, 2009
                                ),
                            ),
                        ),
                    ),
                    array(
                        "id" => 0xE20,
                        "DeviceID" => "000E20",
                        "sensors" => array(
                            "Sensors" => 3,
                            "PhysicalSensors" => 2,
                            "VirtualSensors" => 1,
                            array("id" => 0x02),
                            array("id" => 0x02),
                            array(
                                "id" => 0xFE,
                                "type" => "computation",
                                "extra" => array(
                                    "{2} - {1}", "&#176;C", "Temperature",
                                    UnitsBase::TYPE_DIFF, 4
                                ),
                            ),
                        ),
                        "params" => array(
                            "DriverInfo" => array(
                                "LastAverage15MIN" => gmmktime(
                                    15, 00, 00, 1, 22, 2009
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                ),
                array(
                    0xE10 => array(
                        array(
                            "id" => 0xE10,
                            "Type" => AverageTableBase::AVERAGE_15MIN,
                            "Date" => gmmktime(15, 00, 00, 1, 22, 2009),
                            "Data0" => 4.0,
                            "Data1" => 2.0,
                            "Data2" => 1.0,
                        ),
                    ),
                    0xE20 => array(
                        array(
                            "id" => 0xE20,
                            "Type" => AverageTableBase::AVERAGE_15MIN,
                            "Date" => gmmktime(15, 00, 00, 1, 22, 2009),
                            "Data0" => 1.0,
                            "Data1" => 2.0,
                            "Data2" => 1.0,
                        ),
                    ),
                ),
                array(
                    "id" => 0x1000,
                    "DeviceID" => "001000",
                    "HWPartNum" => "0039-24-02-P",
                    "sensors" => array(
                        "Sensors" => 3,
                        "PhysicalSensors" => 0,
                        "VirtualSensors" => 3,
                        array(
                            "id" => 0xFE,
                            "type" => "physicalpoint",
                            "extra" => array("000E20", 1),
                        ),
                        array(
                            "id" => 0xFE,
                            "type" => "physicalpoint",
                            "extra" => array("000E10", 1),
                        ),
                        array(
                            "id" => 0xFE,
                            "type" => "computation",
                            "extra" => array(
                                "{2} - {1}", "&#176;C", "Temperature",
                                UnitsBase::TYPE_DIFF, 4
                            ),
                        ),
                    ),
                    "params" => array(
                        "DriverInfo" => array(
                            "LastAverage15MIN" => gmmktime(15, 00, 00, 1, 22, 2009),
                        ),
                    ),
                ),
                array(
                ),
                false,
            ),

        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $devs        Array of device information
    * @param mixed $preload     The data to preload into the object
    * @param mixed $preloadData The data to feed the data object
    * @param array $device      The device to do the averages with
    * @param array $expect      The expected average (from toArray())
    * @param array $expectRet   The expected return value from calcAverage
    *
    * @return null
    *
    * @dataProvider dataCalc15MinAverage
    */
    public function testCalc15MinAverage(
        $devs, $preload, $preloadData, $device, $expect, $expectRet
    ) {
        $dev = new DeviceContainer();
        foreach ((array)$devs as $d) {
            $dev->clearData();
            $dev->fromAny($d);
            $dev->insertRow();
            $avg = &$dev->historyFactory(array(), false);
            foreach ((array) $preloadData[$dev->id] as $pd) {
                $avg->clearData();
                $avg->fromAny($pd);
                $avg->insertRow();
            }
        }
        $this->o->clearData();
        $this->o->fromAny($preload);
        $this->o->device = null;
        if (!is_null($device)) {
            $this->o->device = new DeviceContainer($device);
        }
        $data = new HistoryTableMock();
        $ret = $this->o->calcAverage($data, AverageTableBase::AVERAGE_15MIN);
        $this->assertSame($expectRet, $ret, "Return Wrong");
        $this->assertSame($expect, $this->o->toArray(false));
    }

}

?>
