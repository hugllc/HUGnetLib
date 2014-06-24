<?php
/**
 * This runs all of the tests associated with HUGnetLib.
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
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\datachan;
/** This is a required class */
require_once CODE_BASE.'devices/datachan/Driver.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
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
                "HUGnet\devices\datachan\drivers\GENERIC",
            ),
            array(
                "GENERIC",
                "someOldUnit",
                "HUGnet\devices\datachan\drivers\GENERIC",
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
                "K",
                true,
            ),
            array(
                "K",
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
                "K",
                true,
            ),
            array(
                "K",
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
                12.312, "&#176;C", "&#176;F",
                \HUGnet\devices\datachan\Driver::TYPE_RAW,
                false, 12.312
            ),
            array(
                12.312, "&#176;C", "&#176;C",
                \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 12.312
            ),
        );
    }
    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataConvert2()
    {
        return array(
            array(
                12.312, "Pa", "bar",
                \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 1231200
            ),
            array(
                12.312, "mPa", "mbar",
                \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 1231200
            ),
            array(
                1.351, "Pa", "mbar", \HUGnet\devices\datachan\Driver::TYPE_RAW,
                true, 135.1
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $data   The data to convert
    * @param string $to     The unit to convert to
    * @param string $from   The unit to convert from
    * @param string $type   The data type
    * @param bool   $return The expected return
    * @param mixed  $expect The expected data after the conversion
    *
    * @return null
    *
    * @dataProvider dataConvert2
    */
    public function testConvert2($data, $to, $from, $type, $return, $expect)
    {
        $this->o = &Driver::factory("DriverTestClass2", "Pa");
        $this->assertSame(
            $return, $this->o->convert($data, $to, $from, $type), "Return is wrong"
        );
        $this->assertEquals($expect, $data, "Result is wrong", 0.000001);
    }
}
/** This is the HUGnet namespace */
namespace HUGnet\devices\datachan\drivers;
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DriverTestClass extends \HUGnet\devices\datachan\Driver
{
    /** @var The units that are valid for conversion */
    protected $valid = array("&#176;F", "&#176;C", "&#176;R", "K");
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DriverTestClass2 extends \HUGnet\devices\datachan\Driver
{
    /** @var The units that are valid for conversion */
    protected $valid = array("Pa", "bar");
    /** @var Unit conversion multipliers */
    protected $multiplier = array(
        "Pa" => array(
            "bar"  => 1E5,
        ),
        "bar" => array(
            "Pa"   => 1E-5,
        ),
    );
    /** @var Unit conversion prefixes */
    protected $prefix = array(
        "mbar" => array(
            "base" => "bar",
            "mult" => 1E-3,
        ),
        "mPa" => array(
            "base" => "Pa",
            "mult" => 1E-3,
        ),
    );
}
?>
