<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
namespace HUGnet\devices;
/** This is a required class */
require_once CODE_BASE.'db/Table.php';
/** This is a required class */
require_once CODE_BASE.'db/Connection.php';
/** This is a required class */
require_once CODE_BASE.'devices/Average.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once CODE_BASE.'system/Device.php';
/** This is a required class */
require_once CODE_BASE.'db/FastAverage.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is a required class */
require_once TEST_CONFIG_BASE."files/mocks/AverageMock.php";
/** This is a required class */
require_once TEST_CONFIG_BASE."files/mocks/HistoryMock.php";

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class AverageTest extends \PHPUnit_Framework_TestCase
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
        $this->system = $this->getMock("\HUGnet\System", array('now'));
        $this->system->config(
            array(
                "servers" => array(
                    "default" => array(
                        "driver" => "sqlite",
                        "file" => ":memory:",
                        "group" => "default",
                    ),
                ),
            )
        );
        $this->system->expects($this->any())
            ->method('now')
            ->will($this->returnValue(1000000));
        $this->connect = $this->system->dbconnect();
        $this->pdo = &$this->connect->getDBO("default");
        $data = array(
        );
        $this->device = $this->system->device();
        $this->o = \HUGnet\devices\Average::factory(
            $this->system, $this->device
        );
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
        $this->connect->disconnect("default");
        unset($this->o);
        unset($this->pdo);
        unset($this->connect);
        unset($this->system);
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
                ),
                array(
                ),
                array(
                ),
                array(
                    "id" => 0x1000,
                    "DeviceID" => "001000",
                    "HWPartNum" => "0039-24-02-P",
                    "inputs" => array(
                        array(
                            "id" => 0x02,
                        ),
                        array(
                            "id" => 0x02,
                        ),
                        array(
                            "id" => 0xFE,
                            "type" => "ComputationVirtual",
                            "extra" => array(
                                "{1} - {0}", "&#176;C", "Temperature",
                                \HUGnet\devices\datachan\Driver::TYPE_DIFF, 4
                            ),
                        ),
                    ),
                ),
                array(
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(15, 15, 00, 1, 22, 2009),
                        "Data0" => 4.0,
                        "Data1" => 4.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(15, 30, 00, 1, 22, 2009),
                        "Data0" => 1.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                ),
                \HUGnet\db\Average::AVERAGE_HOURLY,
                array(
                    "id" => 0x1000,
                    "Date" => gmmktime(15, 00, 00, 1, 22, 2009),
                    "Type" => \HUGnet\db\Average::AVERAGE_HOURLY,
                    "Data0" => 2.5,
                    "Data1" => 3.0,
                    "Data2" => 1.0,
                ),
            ),
            array(  // #1 Nothing to do
                array(
                ),
                array(
                ),
                array(
                ),
                array(
                    "id" => 0x1000,
                    "DeviceID" => "001000",
                    "HWPartNum" => "0039-24-02-P",
                    "inputs" => array(
                        array(
                            "id" => 0xFE,
                            "type" => "CloneVirtual",
                            "extra" => array("000E20", 0),
                        ),
                        array(
                            "id" => 0xFE,
                            "type" => "CloneVirtual",
                            "extra" => array("000E10", 0),
                        ),
                        array(
                            "id" => 0xFE,
                            "type" => "ComputationVirtual",
                            "extra" => array(
                                "{1} - {0}", "&#176;C", "Temperature",
                                \HUGnet\devices\datachan\Driver::TYPE_DIFF, 4
                            ),
                        ),
                    ),
                    "params" => json_encode(
                        array(
                            "LastAverageHOURLY" => gmmktime(17, 00, 00, 1, 22, 2009),
                        )
                    ),
                ),
                array(
                ),
                \HUGnet\db\Average::AVERAGE_HOURLY,
                false,
            ),
            array(  // #2 basic input.  1MIN Fast Averages
                array(
                ),
                array(
                ),
                array(
                ),
                array(
                    "id" => 0x1000,
                    "DeviceID" => "001000",
                    "HWPartNum" => "0039-24-04-P",
                    "inputs" => array(
                        array(
                            "id" => 0x02,
                        ),
                        array(
                            "id" => 0x02,
                        ),
                        array(
                            "id" => 0xFE,
                            "type" => "ComputationVirtual",
                            "extra" => array(
                                "{1} - {0}", "&#176;C", "Temperature",
                                \HUGnet\devices\datachan\Driver::TYPE_DIFF, 4
                            ),
                        ),
                    ),
                ),
                array(
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\FastAverage::AVERAGE_30SEC,
                        "Date" => gmmktime(15, 00, 00, 1, 22, 2009),
                        "Data0" => 4.0,
                        "Data1" => 4.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\FastAverage::AVERAGE_30SEC,
                        "Date" => gmmktime(15, 00, 30, 1, 22, 2009),
                        "Data0" => 1.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\FastAverage::AVERAGE_30SEC,
                        "Date" => gmmktime(15, 01, 00, 1, 22, 2009),
                        "Data0" => 10.0,
                        "Data1" => 20.0,
                        "Data2" => 10.0,
                    ),
                ),
                \HUGnet\db\FastAverage::AVERAGE_1MIN,
                array(
                    "id" => 0x1000,
                    "Date" => gmmktime(15, 00, 60, 1, 22, 2009),
                    "Type" => \HUGnet\db\FastAverage::AVERAGE_1MIN,
                    "Data0" => 2.5,
                    "Data1" => 3.0,
                    "Data2" => 1.0,
                ),
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
    * @param array $mockData    Mock data to use
    * @param array $type        The type of array to calculate
    * @param array $expect      The expected average (from toArray())
    *
    * @return null
    *
    * @dataProvider dataCalc15MinAverage
    */
    public function testCalc15MinAverage(
        $devs, $preload, $preloadData, $device, $mockData, $type, $expect
    ) {
        $dev = $this->system->device();
        foreach ((array)$devs as $d) {
            $dev->table()->fromAny($d);
            $dev->store();
            foreach ((array)$d["inputs"] as $key => $inp) {
                $input = $dev->input($key);
                $input->table()->fromArray($inp);
                $input->store();
            }
            $avg = &$dev->historyFactory(array(), false);
            foreach ((array) $preloadData[$dev->get("id")] as $pd) {
                $avg->clearData();
                $avg->fromAny($pd);
                $avg->insertRow(true);
            }
        }
        if (!is_null($device)) {
            $this->device = $this->system->device($device);
            $this->device->store();
            foreach ((array)$device["inputs"] as $key => $inp) {
                $input = $this->device->input($key);
                $input->table()->fromArray($inp);
                $input->store();
            }
        }
        $data = new \HUGnet\db\HistoryMock($this->system, $mockData);
        $ret = $this->o->get($data, $type);
        if (is_object($ret)) {
            $ret = $ret->toArray(false);
        }
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testCalcAverage
    *
    * @return array
    */
    public static function dataCalc15MinAverageMulti()
    {
        return array(
            array(  // #0 basic input.  LastAverage15MIN not set
                array(
                ),
                array(
                ),
                array(
                ),
                array(
                    "id" => 0x1000,
                    "DeviceID" => "001000",
                    "HWPartNum" => "0039-24-02-P",
                    "inputs" => array(
                        array(
                            "id" => 0x02,
                        ),
                        array(
                            "id" => 0x02,
                        ),
                        array(
                            "id" => 0xFE,
                            "type" => "ComputationVirtual",
                            "extra" => array(
                                "{1} - {0}", "&#176;C", "Temperature",
                                \HUGnet\devices\datachan\Driver::TYPE_DIFF, 4
                            ),
                        ),
                    ),
                ),
                array(
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(15, 00, 00, 1, 22, 2009),
                        "Data0" => 4.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(15, 15, 00, 1, 22, 2009),
                        "Data0" => 3.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(15, 30, 00, 1, 22, 2009),
                        "Data0" => 2.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(15, 45, 00, 1, 22, 2009),
                        "Data0" => 1.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(16, 00, 00, 1, 22, 2009),
                        "Data0" => 2.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(16, 15, 00, 1, 22, 2009),
                        "Data0" => 3.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(16, 30, 00, 1, 22, 2009),
                        "Data0" => 4.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(16, 45, 00, 1, 22, 2009),
                        "Data0" => 3.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(17, 00, 00, 1, 22, 2009),
                        "Data0" => 2.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(17, 15, 00, 1, 22, 2009),
                        "Data0" => 1.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(17, 30, 00, 1, 22, 2009),
                        "Data0" => 2.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(17, 45, 00, 1, 22, 2009),
                        "Data0" => 3.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                ),
                \HUGnet\db\Average::AVERAGE_HOURLY,
                array(
                    array(
                        "id" => 0x1000,
                        "Date" => gmmktime(15, 00, 00, 1, 22, 2009),
                        "Type" => \HUGnet\db\Average::AVERAGE_HOURLY,
                        "Data0" => 2.5,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Date" => gmmktime(16, 00, 00, 1, 22, 2009),
                        "Type" => \HUGnet\db\Average::AVERAGE_HOURLY,
                        "Data0" => 3.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Date" => gmmktime(17, 00, 00, 1, 22, 2009),
                        "Type" => \HUGnet\db\Average::AVERAGE_HOURLY,
                        "Data0" => 2.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                ),
            ),
            array(  // #1 Gaps in the source averages
                array(
                ),
                array(
                ),
                array(
                ),
                array(
                    "id" => 0x1000,
                    "DeviceID" => "001000",
                    "HWPartNum" => "0039-24-02-P",
                    "inputs" => array(
                        array(
                            "id" => 0x02,
                        ),
                        array(
                            "id" => 0x02,
                        ),
                        array(
                            "id" => 0xFE,
                            "type" => "ComputationVirtual",
                            "extra" => array(
                                "{1} - {0}", "&#176;C", "Temperature",
                                \HUGnet\devices\datachan\Driver::TYPE_DIFF, 4
                            ),
                        ),
                    ),
                ),
                array(
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(15, 00, 00, 1, 22, 2009),
                        "Data0" => 4.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(15, 15, 00, 1, 22, 2009),
                        "Data0" => 3.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(15, 45, 00, 1, 22, 2009),
                        "Data0" => 2.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(17, 00, 00, 1, 22, 2009),
                        "Data0" => 2.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(17, 45, 00, 1, 22, 2009),
                        "Data0" => 3.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                ),
                \HUGnet\db\Average::AVERAGE_HOURLY,
                array(
                    array(
                        "id" => 0x1000,
                        "Date" => gmmktime(15, 00, 00, 1, 22, 2009),
                        "Type" => \HUGnet\db\Average::AVERAGE_HOURLY,
                        "Data0" => 3.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Date" => gmmktime(17, 00, 00, 1, 22, 2009),
                        "Type" => \HUGnet\db\Average::AVERAGE_HOURLY,
                        "Data0" => 2.5,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                ),
            ),
            array(  // #3 starting in the middle
                array(
                ),
                array(
                ),
                array(
                ),
                array(
                    "id" => 0x1000,
                    "DeviceID" => "001000",
                    "HWPartNum" => "0039-24-02-P",
                    "inputs" => array(
                        array(
                            "id" => 0x02,
                        ),
                        array(
                            "id" => 0x02,
                        ),
                        array(
                            "id" => 0xFE,
                            "type" => "ComputationVirtual",
                            "extra" => array(
                                "{1} - {0}", "&#176;C", "Temperature",
                                \HUGnet\devices\datachan\Driver::TYPE_DIFF, 4
                            ),
                        ),
                    ),
                ),
                array(
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(15, 45, 00, 1, 22, 2009),
                        "Data0" => 1.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(16, 30, 00, 1, 22, 2009),
                        "Data0" => 4.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(16, 45, 00, 1, 22, 2009),
                        "Data0" => 3.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(17, 15, 00, 1, 22, 2009),
                        "Data0" => 1.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(17, 30, 00, 1, 22, 2009),
                        "Data0" => 2.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                        "Date" => gmmktime(17, 45, 00, 1, 22, 2009),
                        "Data0" => 3.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                ),
                \HUGnet\db\Average::AVERAGE_HOURLY,
                array(
                    array(
                        "id" => 0x1000,
                        "Date" => gmmktime(15, 00, 00, 1, 22, 2009),
                        "Type" => \HUGnet\db\Average::AVERAGE_HOURLY,
                        "Data0" => 1.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Date" => gmmktime(16, 00, 00, 1, 22, 2009),
                        "Type" => \HUGnet\db\Average::AVERAGE_HOURLY,
                        "Data0" => 3.5,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                    array(
                        "id" => 0x1000,
                        "Date" => gmmktime(17, 00, 00, 1, 22, 2009),
                        "Type" => \HUGnet\db\Average::AVERAGE_HOURLY,
                        "Data0" => 2.0,
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                    ),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $devs        Array of device information
    * @param mixed  $preload     The data to preload into the object
    * @param mixed  $preloadData The data to feed the data object
    * @param array  $device      The device to do the averages with
    * @param array  $mockData    The data to use
    * @param string $type        The type of average to do
    * @param array  $expect      The expected average (from toArray())
    *
    * @return null
    *
    * @dataProvider dataCalc15MinAverageMulti
    */
    public function testCalc15MinAverageMulti(
        $devs, $preload, $preloadData, $device,
        $mockData, $type, $expect
    ) {
        $dev = $this->system->device();
        foreach ((array)$devs as $d) {
            $dev->table()->fromAny($d);
            $dev->table()->insertRow(true);
            foreach ((array)$d["inputs"] as $key => $inp) {
                $input = $dev->input($key);
                $input->table()->fromArray($inp);
                $input->table()->insertRow(true);
            }
            $avg = &$dev->historyFactory(array(), false);
            foreach ((array) $preloadData[$dev->get("id")] as $pd) {
                $avg->clearData();
                $avg->fromAny($pd);
                $avg->insertRow(true);
            }
        }
        if (!is_null($device)) {
            $this->device = $this->system->device($device);
            $this->device->store();
            foreach ((array)$device["inputs"] as $key => $inp) {
                $input = $this->device->input($key);
                $input->table()->fromArray($inp);
                $input->store();
            }
        }
        $data = new \HUGnet\db\HistoryMock($this->system, $mockData);
        $ret = array();
        $count = 0;
        do {
            $res = $this->o->get($data, $type);
            if (is_object($res)) {
                $ret[] = $res->toArray(false);
            }
        } while (is_object($res));
        $this->assertSame($expect, $ret, "Data Wrong");
        $this->assertSame(
            $lastHist,
            $this->device->getParam("LastHistory"),
            "LastHistory wrong"
        );
        $this->assertEquals(
            $lastPoll,
            $this->device->getParam("LastPoll"),
            "LastPoll wrong"
        );
    }
}

?>
