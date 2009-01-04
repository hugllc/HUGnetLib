<?php
/**
 * Tests the device class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2009 Hunt Utilities Group, LLC
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
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

// Call deviceTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "deviceTest::main");
}

/** The test case class */
require_once "PHPUnit/Framework/TestCase.php";
/** The test suite class */
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../../database/Device.php';
require_once dirname(__FILE__).'/DatabaseTest.php';

/**
 * Test class for device.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:06.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceTest extends databaseTest
{
    /** The table to use */
    protected $table = "devices";
    /** The table to use */
    protected $id = "DeviceKey";
    
    /** This is the preload data we can use */
    protected static $preload = array(
        array(
            "DeviceKey" => 1,
            "DeviceID"  => "000030",
            "DeviceName" => "dev1",
            "HWPartNum" => "0039-28-01-A",
            "FWPartNum" => "0039-20-13-C",
            "SerialNum" => 48,
            "GatewayKey" => 1,
            "params"     => "",
            "PollInterval" => 15,
            "LastConfig" => "2006-11-02 00:00:00",
       ),
        array(
            "DeviceKey" => 2,
            "DeviceID"  => "000031",
            "DeviceName" => "dev2",
            "HWPartNum" => "0039-21-01-A",
            "FWPartNum" => "0039-20-01-C",
            "SerialNum" => 49,
            "GatewayKey" => 1,
            "params"     => "",
            "PollInterval" => 10,
            "LastConfig" => "2007-05-02 00:00:00",
       ),
        array(
            "DeviceKey" => 3,
            "DeviceID"  => "000032",
            "DeviceName" => "dev3",
            "HWPartNum" => "0039-28-01-A",
            "FWPartNum" => "0039-20-13-C",
            "SerialNum" => 50,
            "GatewayKey" => 2,
            "params"     => "",
            "PollInterval" => 5,
            "LastConfig" => "2007-11-02 00:00:00",
       ),
        array(
            "DeviceKey" => 4,
            "DeviceID"  => "000033",
            "DeviceName" => "dev4",
            "HWPartNum" => "0039-28-01-A",
            "FWPartNum" => "0039-20-13-C",
            "SerialNum" => 51,
            "GatewayKey" => 0,
            "params"     => "",
            "PollInterval" => 0,
            "LastConfig" => "2006-06-02 00:00:00",
       ),
    );
    /**
     * Runs the test methods of this class.
     *
     * @return null
     *
     * @access public
     * @static
     */
    public static function main() 
    {
        include_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("deviceTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

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
        parent::setUp();
        $this->o =& HUGnetDB::getInstance("Device", $this->config); // new device($this->pdo);
        $this->o->createTable();
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
        parent::tearDown();
        unset($this->o);
    }


    /**
     * data provider dataDiagnose
     *
     * @return array
     */
    public static function dataDiagnose() 
    {
        return array(
            array(array(), time(), array()),
            array(
                array(
                    "LastHistory" => "2007-11-20 22:23:25",
                    "LastPoll" => "2007-11-20 22:23:25",
                ),
                strtotime("2007-12-15 23:22:12"),
                array(),
            ),
            array(
                array(
                    "LastHistory" => "2007-11-20 22:23:25",
                    "LastPoll" => "2007-11-20 22:23:25",
                    "PollInterval" => -5,
                ),
                strtotime("2007-12-15 23:22:12"),
                array(),
            ),
            array(
                array(
                    "LastHistory" => "2007-11-20 22:23:25",
                    "LastPoll" => "2007-11-20 22:23:25",
                    "PollInterval" => 5,
                ),
                strtotime("2007-12-15 23:22:12"),
                array(
                    "Last Poll 25d 58m 47s ago\n",
                    "No Active Sensors\n",
                ),
            ),
            array(
                array(
                    "LastHistory" => "2007-11-20 22:23:25",
                    "LastPoll" => "2007-10-20 22:23:25",
                    "PollInterval" => 5,
                    "ActiveSensors" => 2,
                ),
                strtotime("2007-12-15 23:22:12"),
                array(
                    "Last Poll 56d 1h 58m 47s ago\n",
                    "History 31d 1h 0s old\n",
                ),
            ),
                
        );
    }
    /**
     * test
     *
     * @param array  $Info   Infomation about the device to get stylesheet information for
     * @param int    $time   The time we are diagnosing
     * @param string $expect The expected return value
     *
     * @return null
     *
     * @dataProvider dataDiagnose().
     * @covers device::testDiagnose
     */
    public function testDiagnose($Info, $time, $expect) 
    {
        $ret = $this->o->diagnose($Info, $time);
        $this->assertSame($expect, $ret);
    }


    /**
     * data provider
     *
     * @return array
     */
    public static function dataSelectDevice() 
    {
        return array(
            array(
                self::$preload,
                "test",
                1,
                2,
                array(
                    3 => "000032 dev3",
               ),
           ),
            array(
                self::$preload,
                "test",
                1,
                null,
                array(
                    1 => "000030 dev1",
                    2 => "000031 dev2",
                    3 => "000032 dev3",
               ),
           ),
            array(
                self::$preload,
                "test",
                1,
                0,
                array(
                    1 => "000030 dev1",
                    2 => "000031 dev2",
                    3 => "000032 dev3",
               ),
           ),
        );
    }

    /**
     * test
     *
     * @param array  $preload    Data to preload into the database
     * @param string $name       The name of the select list
     * @param mixed  $selected   The entry that is currently selected
     * @param int    $GatewayKey The key to use if only one gateway is to be selected
     * @param array  $expect     What we expect returned
     *
     * @return null
     *
     * @dataProvider dataSelectDevice().
     */
    public function testSelectDevice($preload, $name, $selected, $GatewayKey, $expect) 
    {
        $this->load($preload);
        $ret = $this->o->selectDevice($name, $selected, $GatewayKey);
        $this->assertSame($expect, $ret);
    }

    /**
     * data provider
     *
     * @return array
     */
    public static function dataGetDevice() 
    {
        return array(
            array(
                self::$preload,
                0,
                "ID",
                array(),
           ),
            array(
                self::$preload,
                1,
                "asdf",
                array(
                    "DeviceKey" => "1",
                    "DeviceID" => "000030",
                    "DeviceName" => "dev1",
                    "SerialNum" => "48",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "FWVersion" => "",
                    "RawSetup" => "",
                    "Active" => "YES",
                    "GatewayKey" => "1",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "",
                    "PollInterval" => "15",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "",
                    "BoredomThreshold" => "0",
                    "LastConfig" => "2006-11-02 00:00:00",
                    "LastPoll" => "0000-00-00 00:00:00",
                    "LastHistory" => "0000-00-00 00:00:00",
                    "LastAnalysis" => "0000-00-00 00:00:00",
                    "MinAverage" => "15MIN",
                    "CurrentGatewayKey" => "0",
                    "params" => array(),
               ),
           ),
            array(
                self::$preload,
                "dev3",
                "NAME",
                array(
                    "DeviceKey" => "3",
                    "DeviceID" => "000032",
                    "DeviceName" => "dev3",
                    "SerialNum" => "50",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "FWVersion" => "",
                    "RawSetup" => "",
                    "Active" => "YES",
                    "GatewayKey" => "2",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "",
                    "PollInterval" => "5",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "",
                    "BoredomThreshold" => "0",
                    "LastConfig" => "2007-11-02 00:00:00",
                    "LastPoll" => "0000-00-00 00:00:00",
                    "LastHistory" => "0000-00-00 00:00:00",
                    "LastAnalysis" => "0000-00-00 00:00:00",
                    "MinAverage" => "15MIN",
                    "CurrentGatewayKey" => "0",
                    "params" => array(),
               ),
           ),
            array(
                self::$preload,
                "000031",
                "ID",
                array(
                    "DeviceKey" => "2",
                    "DeviceID" => "000031",
                    "DeviceName" => "dev2",
                    "SerialNum" => "49",
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "FWVersion" => "",
                    "RawSetup" => "",
                    "Active" => "YES",
                    "GatewayKey" => "1",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "",
                    "PollInterval" => "10",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "",
                    "BoredomThreshold" => "0",
                    "LastConfig" => "2007-05-02 00:00:00",
                    "LastPoll" => "0000-00-00 00:00:00",
                    "LastHistory" => "0000-00-00 00:00:00",
                    "LastAnalysis" => "0000-00-00 00:00:00",
                    "MinAverage" => "15MIN",
                    "CurrentGatewayKey" => "0",
                    "params" => array(),
               ),
           ),
        );
    }

    /**
     * test
     *
     * @param array  $preload Data to preload into the database
     * @param mixed  $id      The ID to look for
     * @param string $type    The type of id "ID", "NAME", or "KEY"
     * @param array  $expect  What we expect returned
     *
     * @return null
     *
     * @dataProvider dataGetDevice().
     */
    public function testGetDevice($preload, $id, $type, $expect) 
    {
        $this->load($preload);
        $ret = $this->o->getDevice($id, $type);
        $this->assertSame($expect, $ret);
    }

    /**
     * data provider
     *
     * @return array
     */
    public static function dataUpdateDevice() 
    {
        return array(
            // Last Config old update
            array(
                self::$preload,
                array(
                    "DeviceKey" => 1,
                    "DeviceID"  => "000030",
                    "HWPartNum" => "0039-28-01-A",
                    "DeviceName" => "dev1",
                    "SerialNum" => 48,
                    "GatewayKey" => 4,
                    "params"     => "",
                    "PollInterval" => 10,
                    "LastConfig" => "2005-12-24 00:21:24",
                ),
                0,
                false,
                array(),
            ),
            // no Last Config update
            array(
                self::$preload,
                array(
                    "DeviceKey" => 1,
                    "DeviceID"  => "000030",
                    "HWPartNum" => "0039-28-01-A",
                    "DeviceName" => "dev1",
                    "SerialNum" => 48,
                    "GatewayKey" => 4,
                    "params"     => "",
                    "PollInterval" => 10,
                    "LastConfig" => "",
                ),
                0,
                false,
                array(),
            ),
            array(
                self::$preload,
                "Hello",
                0,
                false,
                array(),
            ),
            // Insert
            array(
                self::$preload,
                array(),
                0,
                false,
                array(),
            ),
            // Insert
            array(
                self::$preload,
                array("DeviceID" => "002345"),
                0,
                false,
                array(),
            ),
            // Insert
            array(
                self::$preload,
                array("DeviceID" => "002345", "HWPartNum" => "0039-28-01-A"),
                0,
                false,
                array(),
            ),
            // Insert
            array(
                self::$preload,
                array("DeviceID" => "002345", "SerialNum" => 9030, "HWPartNum" => "0039-28-01-A", "FWPartNum" => "0039-20-13-C"),
                0,
                false,
                array(),
            ),
            // Update
            array(
                self::$preload,
                array("DeviceID" => "000030", "SerialNum" => 48, "HWPartNum" => "0039-21-02-B", "LastConfig" => "2008-01-05"),
                0,
                false,
                array(),
            ),
            // Update
            array(
                self::$preload,
                array(
                    "DeviceKey" => 1,
                    "DeviceID"  => "000030",
                    "DeviceName" => "dev1",
                    "SerialNum" => 48,
                    "HWPartNum" => "0039-28-01-A",
                    "GatewayKey" => 4,
                    "params"     => "",
                    "PollInterval" => 10,
                    "LastConfig" => "2007-12-24 00:21:24",
                    "params" => "Hello there",
                ),
                1,
                true,
                array(
                    "DeviceKey" => "1",
                    "DeviceID"  => "000030",
                    "DeviceName" => "dev1",
                    "SerialNum" => "48",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "FWVersion" => "",
                    "RawSetup" => "",
                    "Active" => "YES",
                    "GatewayKey" => "4",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "",
                    "PollInterval" => "10",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "",
                    "BoredomThreshold" => "0",
                    "LastConfig" => "2007-12-24 00:21:24",
                    "LastPoll" => "0000-00-00 00:00:00",
                    "LastHistory" => "0000-00-00 00:00:00",
                    "LastAnalysis" => "0000-00-00 00:00:00",
                    "MinAverage" => "15MIN",
                    "CurrentGatewayKey" => "0",
                    "params"     => "",
               ),
            ),
            // Insert
            array(
                self::$preload,
                array(
                    "DeviceKey" => 15,
                    "DeviceName" => "dev1",
                    "SerialNum" => 64,
                    "DeviceID" => "000040",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "GatewayKey" => 4,
                    "params"     => "",
                    "PollInterval" => 10,
                    "LastConfig" => "2007-12-24 00:21:24",
                    "params" => "Hello there",
                ),
                5,
                true,
                array(
                    "DeviceKey" => "5",
                    "DeviceID"  => "000040",
                    "DeviceName" => "dev1",
                    "SerialNum" => "64",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "FWVersion" => "",
                    "RawSetup" => "",
                    "Active" => "YES",
                    "GatewayKey" => "4",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "",
                    "PollInterval" => "10",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "",
                    "BoredomThreshold" => "0",
                    "LastConfig" => "2007-12-24 00:21:24",
                    "LastPoll" => "0000-00-00 00:00:00",
                    "LastHistory" => "0000-00-00 00:00:00",
                    "LastAnalysis" => "0000-00-00 00:00:00",
                    "MinAverage" => "15MIN",
                    "CurrentGatewayKey" => "0",
                    "params"     => "",
               ),
           ),
            // Insert
            array(
                self::$preload,
                array(
                    "DeviceKey" => 15,
                    "DeviceName" => "dev1",
                    "SerialNum" => 64,
                    "DeviceID" => "000040",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "",
                    "GatewayKey" => 4,
                    "params"     => "",
                    "PollInterval" => 10,
                    "LastConfig" => "2007-12-24 00:21:24",
                    "params" => "Hello there",
                ),
                0,
                false,
                array(),
           ),

        );
    }

    /**
     * test
     *
     * @param array $preload   Data to preload into the database
     * @param array $Info      The name of the select list
     * @param int   $DeviceKey The deviceKey to use
     * @param bool  $retExpect The entry that is currently selected
     * @param array $expect    What we expect returned
     *
     * @return null
     *
     * @dataProvider dataUpdateDevice().
     */
    public function testUpdateDevice($preload, $Info, $DeviceKey, $retExpect, $expect) 
    {
        $this->load($preload);
        $ret = $this->o->updateDevice($Info);
        $this->assertSame($retExpect, $ret);
        if (empty($DeviceKey)) return;
        $ret = $this->getSingle($DeviceKey);
        $this->assertSame($expect, $ret);
    }

    /**
     * data provider
     *
     * @return array
     */
    public static function dataSetParams() 
    {
        return array(
            array(
                self::$preload,
                1,
                array("this" => "is", "an" => "array"),
                array(
                    "DeviceKey" => "1",
                    "DeviceID" => "000030",
                    "DeviceName" => "dev1",
                    "SerialNum" => "48",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "FWVersion" => "",
                    "RawSetup" => "",
                    "Active" => "YES",
                    "GatewayKey" => "1",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "",
                    "PollInterval" => "15",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "",
                    "BoredomThreshold" => "0",
                    "LastConfig" => "2006-11-02 00:00:00",
                    "LastPoll" => "0000-00-00 00:00:00",
                    "LastHistory" => "0000-00-00 00:00:00",
                    "LastAnalysis" => "0000-00-00 00:00:00",
                    "MinAverage" => "15MIN",
                    "CurrentGatewayKey" => "0",
                    "params" => "YToyOntzOjQ6InRoaXMiO3M6MjoiaXMiO3M6MjoiYW4iO3M6NToiYXJyYXkiO30=",
               ),
                true,
           ),
            array(
                self::$preload,
                1,
                array(1,2,3,4,5),
                array(
                    "DeviceKey" => "1",
                    "DeviceID" => "000030",
                    "DeviceName" => "dev1",
                    "SerialNum" => "48",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "FWVersion" => "",
                    "RawSetup" => "",
                    "Active" => "YES",
                    "GatewayKey" => "1",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "",
                    "PollInterval" => "15",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "",
                    "BoredomThreshold" => "0",
                    "LastConfig" => "2006-11-02 00:00:00",
                    "LastPoll" => "0000-00-00 00:00:00",
                    "LastHistory" => "0000-00-00 00:00:00",
                    "LastAnalysis" => "0000-00-00 00:00:00",
                    "MinAverage" => "15MIN",
                    "CurrentGatewayKey" => "0",
                    "params" => "YTo1OntpOjA7aToxO2k6MTtpOjI7aToyO2k6MztpOjM7aTo0O2k6NDtpOjU7fQ==",
               ),
                true,
           ),
            array(
                self::$preload,
                1,
                "This is a string",
                array(
                    "DeviceKey" => "1",
                    "DeviceID" => "000030",
                    "DeviceName" => "dev1",
                    "SerialNum" => "48",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "FWVersion" => "",
                    "RawSetup" => "",
                    "Active" => "YES",
                    "GatewayKey" => "1",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "",
                    "PollInterval" => "15",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "",
                    "BoredomThreshold" => "0",
                    "LastConfig" => "2006-11-02 00:00:00",
                    "LastPoll" => "0000-00-00 00:00:00",
                    "LastHistory" => "0000-00-00 00:00:00",
                    "LastAnalysis" => "0000-00-00 00:00:00",
                    "MinAverage" => "15MIN",
                    "CurrentGatewayKey" => "0",
                    "params" => "This is a string",
               ),
                true,
           ),
            array(
                self::$preload,
                1,
                array(),
                array(
                    "DeviceKey" => "1",
                    "DeviceID" => "000030",
                    "DeviceName" => "dev1",
                    "SerialNum" => "48",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-20-13-C",
                    "FWVersion" => "",
                    "RawSetup" => "",
                    "Active" => "YES",
                    "GatewayKey" => "1",
                    "ControllerKey" => "0",
                    "ControllerIndex" => "0",
                    "DeviceLocation" => "",
                    "DeviceJob" => "",
                    "Driver" => "",
                    "PollInterval" => "15",
                    "ActiveSensors" => "0",
                    "DeviceGroup" => "",
                    "BoredomThreshold" => "0",
                    "LastConfig" => "2006-11-02 00:00:00",
                    "LastPoll" => "0000-00-00 00:00:00",
                    "LastHistory" => "0000-00-00 00:00:00",
                    "LastAnalysis" => "0000-00-00 00:00:00",
                    "MinAverage" => "15MIN",
                    "CurrentGatewayKey" => "0",
                    "params" => "YTowOnt9",
               ),
                true,
           ),
            array(
                self::$preload,
                510,
                array(1,2,3,4,5),
                null,
                false,
           ),
        );
    }

    /**
     * test
     *
     * @param array $preload   Data to preload into the database
     * @param ing   $DeviceKey The ID to look for
     * @param mixed $params    The type of id "ID", "NAME", or "KEY"
     * @param array $expect    What we expect returned
     * @param bool  $retExpect What we expect returned
     *
     * @return null
     *
     * @dataProvider dataSetParams().
     */
    public function testSetParams($preload, $DeviceKey, $params, $expect, $retExpect)
    {
        $this->load($preload);
        $this->o->setParams($DeviceKey, $params);
        $ret = $this->getSingle($DeviceKey);
        $this->assertSame($expect, $ret);
    }

    /**
     * test
     *
     * @param array  $preload   Data to preload into the database
     * @param ing    $DeviceKey The ID to look for
     * @param mixed  $params    The type of id "ID", "NAME", or "KEY"
     * @param array  $expect    What we expect returned
     * @param bool   $retExpect What we expect returned
     *
     * @return null
     *
     * @dataProvider dataSetParams().
     */
    /*
    public function testSetParamsReturn($preload, $DeviceKey, $params, $expect, $retExpect) 
    {
        $this->load($preload);
        $ret = $this->o->setParams($DeviceKey, $params);
        $this->assertSame($retExpect, $ret);
    }
    */

    /**
     * data provider
     *
     * @return array
     */
    public static function dataEncodeParams() 
    {
        return array(
            array(array("this"=>"is","a"=>"test"), "YToyOntzOjQ6InRoaXMiO3M6MjoiaXMiO3M6MToiYSI7czo0OiJ0ZXN0Ijt9"),
            array("test String", "test String"),
            array(1234, ""),
            array(array(), "YTowOnt9"),
        );
    }
    /**
     * test
     *
     * @param array  $params Parameters to encode
     * @param string $expect The expected return value
     *
     * @return null
     *
     * @dataProvider dataEncodeParams
     */
    public function testEncodeParams($params, $expect)
    {
        $ret = device::encodeParams($params);
        $this->assertSame($expect, $params, "Input array passed by reference was not modified correctly");
    }
    /**
     * test
     *
     * @param array  $params Parameters to encode
     * @param string $expect The expected return value
     *
     * @return null
     *
     * @dataProvider dataEncodeParams
     */
    public function testEncodeParamsReturn($params, $expect)
    {
        $ret = device::encodeParams($params);
        $this->assertSame($expect, $ret, "return array incorrect");
    }

    /**
     * data provider
     *
     * @return array
     */
    public static function dataDecodeParams() 
    {
        return array(
            array("YToyOntzOjQ6InRoaXMiO3M6MjoiaXMiO3M6MToiYSI7czo0OiJ0ZXN0Ijt9", array("this"=>"is","a"=>"test")),
            array(array("this"=>"is","an"=>"array"),array("this"=>"is","an"=>"array")),
            array(1234, array()),
            array("", array()),
        );
    }
    /**
     * test
     *
     * @param string $params Parameters to decode
     * @param array  $expect The expected return value
     *
     * @return null
     *
     * @dataProvider dataDecodeParams
     */
    public function testDecodeParamsReturn($params, $expect) 
    {
        $ret = device::decodeParams($params);
        $this->assertSame($expect, $ret, "return array incorrect");
    }
    /**
     * test
     *
     * @param string $params Parameters to decode
     * @param array  $expect The expected return value
     *
     * @return null
     *
     * @dataProvider dataDecodeParams
     */
    public function testDecodeParams($params, $expect) 
    {
        $ret = device::decodeParams($params);
        $this->assertSame($expect, $params, "Input array passed by reference was not modified correctly");
    }

    /**
     * data provider
     *
     * @return array
     */
    public static function dataEncodeDecodeParams() 
    {
        return array(
            array(array()),
            array(array(1,2,3,4,5)),
            array(array("Hello" => "This", "is" => "an", "associative" => "array")), 
        );
    }
    /**
     * test
     *
     * @param string $params Parameters to decode
     *
     * @return null
     *
     * @dataProvider dataEncodeDecodeParams
     */
    public function testEncodeDecodeParams($params) 
    {
        $expect = $params;
        device::encodeParams($params);
        device::decodeParams($params);
        $this->assertSame($expect, $params);
    }


}

// Call deviceTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "deviceTest::main") {
    deviceTest::main();
}
?>
