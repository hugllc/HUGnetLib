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
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\channels;
/** This is a required class */
require_once CODE_BASE.'channels/Driver.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is a required class */
require_once CODE_BASE.'util/VPrint.php';
/** This is our base class */
require_once dirname(__FILE__)."/drivers/DriverTestBase.php";

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
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DriverTest extends drivers\DriverTestBase
{
    /** This is the units that are valid */
    protected static $units = array('&#176;F', '&#176;C', '&#176;R', 'K');
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
        $this->o = &Driver::factory("DriverTestClass", "&#176;F");
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
        unset($this->o);
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
                "&#176;C",
                "HUGnet\channels\drivers\GENERIC",
            ),
            array(
                "GENERIC",
                "someOldUnit",
                "HUGnet\channels\drivers\GENERIC",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $unitType The name of the variable to test.
    * @param string $units    The units to use
    * @param array  $expect   The expected return
    *
    * @return null
    *
    * @dataProvider dataFactory
    */
    public function testFactory($unitType, $units, $expect)
    {
        $o = &Driver::factory($unitType, $units);
        $this->assertSame($expect, get_class($o));
    }
    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataGetValid()
    {
        return array(
            array(
                "\\HUGnet\\channels\\DriverTestClass",
                array(
                    '&#176;F' => '&#176;F',
                    '&#176;C' => '&#176;C',
                    '&#176;R' => '&#176;R',
                    'K' => 'K',
                ),
            ),
        );
    }

    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataValid()
    {
        return array(
            array(
                "K",
                true,
            ),
            array(
                "psi",
                false,
            ),
        );
    }
    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataNumeric()
    {
        return array(
            array(
                "K",
                true,
            ),
            array(
                "psi",
                false,
            ),
        );
    }
    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataConvert()
    {
        return array(
            array(
                12.312, "&#176;C", "&#176;F", \HUGnet\channels\Driver::TYPE_RAW,
                false, 12.312
            ),
            array(
                12.312, "&#176;C", "&#176;C", \HUGnet\channels\Driver::TYPE_RAW,
                true, 12.312
            ),
        );
    }
}
/** This is the HUGnet namespace */
namespace HUGnet\channels\drivers;
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
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DriverTestClass extends \HUGnet\channels\Driver
{
    /** @var The units that are valid for conversion */
    protected $valid = array("&#176;F", "&#176;C", "&#176;R", "K");
}
?>
