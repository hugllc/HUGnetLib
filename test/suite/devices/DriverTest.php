<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This is a required class */
require_once CODE_BASE.'devices/Driver.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is a required class */
require_once CODE_BASE.'util/VPrint.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DriverTest extends \PHPUnit_Framework_TestCase
{
    /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    *
    * @access protected
    *
    * @return null
    */
    protected function setUp()
    {
    }

    /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    *
    * @access protected
    *
    * @return null
    */
    protected function tearDown()
    {
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataPresent()
    {
        return array(
            array(
                "ThisIsABadName",
                false,
            ),
            array(
                "packetTimeout",
                true,
            ),
            array(
                "testParam",
                true,
            ),
            array(
                "virtualSensors",
                true,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataPresent
    */
    public function testPresent($name, $expect)
    {
        $o = &DriverTestClass::factory();
        $this->assertSame($expect, $o->present($name));
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGet()
    {
        return array(
            array(
                "ThisIsABadName",
                null,
            ),
            array(
                "packetTimeout",
                6,
            ),
            array(
                "testParam",
                "12345",
            ),
            array(
                "virtualSensors",
                4,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet($name, $expect)
    {
        $o = &DriverTestClass::factory();
        $this->assertSame($expect, $o->get($name));
    }
    /**
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testToArray()
    {
        $expect = array(
            'packetTimeout' => 6,
            'totalSensors' => 13,
            'physicalSensors' => 9,
            'virtualSensors' => 4,
            'historyTable' => 'EDEFAULTHistoryTable',
            'averageTable' => 'EDEFAULTAverageTable',
            'loadable' => false,
            'testParam' => '12345',
            'bootloader' => false,
        );
        $o = &DriverTestClass::factory();
        $this->assertEquals($expect, $o->toArray());
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataFactory()
    {
        return array(
            array(
                "asdf",
                "HUGnet\devices\drivers\EDEFAULT",
            ),
            array(
                "EDEFAULT",
                "HUGnet\devices\drivers\EDEFAULT",
            ),
            array(
                "EVIRTUAL",
                "HUGnet\devices\drivers\EVIRTUAL",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataFactory
    */
    public function testFactory($name, $expect)
    {
        $o = &Driver::factory($name);
        $this->assertSame($expect, get_class($o));
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGetDriver()
    {
        return array(
            array(
                "0039-12-02-C",
                "0039-38-01-C",
                "5.6.7",
                "E00391200",
            ),
            array(
                "0039-11-02-C",
                "0039-38-01-C",
                "5.6.7",
                "EDEFAULT",
            ),
            array(
                "0039-24-02-P",
                "0039-24-02-P",
                "5.6.7",
                "EVIRTUAL",
            ),
            array(
                "0039-12-02-C",
                "0039-13-01-C",
                "5.6.7",
                "E00391200",
            ),
            array(
                "0039-21-01-A",
                "0039-38-02-C",
                "5.6.7",
                "E00393802",
            ),
            array(
                "0039-21-02-A",
                "0039-38-02-C",
                "5.6.7",
                "E00393802",
            ),
            array(
                "0039-21-01-A",
                "0039-38-01-C",
                "5.6.7",
                "E00392100",
            ),
            array(
                "0039-21-02-A",
                "0039-38-01-C",
                "5.6.7",
                "E00392100",
            ),
            array(
                "0039-37-01-A",
                "0039-38-02-C",
                "5.6.7",
                "E00393802",
            ),
            array(
                "0039-37-01-A",
                "0039-38-01-C",
                "5.6.7",
                "E00393700",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $HWPartNum The hardware part number
    * @param string $FWPartNum The firmware part number
    * @param string $RWVersion The firmware version
    * @param array  $expect    The expected return
    *
    * @return null
    *
    * @dataProvider dataGetDriver
    */
    public function testGetDriver($HWPartNum, $FWPartNum, $FWVersion, $expect)
    {
        $this->assertSame(
            $expect, Driver::getDriver($HWPartNum, $FWPartNum, $FWVersion)
        );
    }
}
/**
 * Base driver class for devices.
 *
 * This class deals with loading the drivers and figuring out what driver needs
 * to be loaded.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DriverTestClass extends Driver
{
    /**
    * This function creates the system.
    *
    * @return null
    */
    public static function &factory()
    {
        return parent::intFactory();
    }
}
?>
