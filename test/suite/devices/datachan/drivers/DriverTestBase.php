<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\datachan\drivers;
/** This is the base class */
require_once CODE_BASE."/devices/datachan/Driver.php";
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class DriverTestBase extends \PHPUnit_Framework_TestCase
{
    /** This is the class we are testing */
    protected $class = "";
    /** This is the object we are testing */
    protected $o = null;
    /** This is the units that are valid */
    protected static $units = array(null);

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
        unset($this->o);
    }

    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataConversionsData()
    {
        $ret = array();
        foreach (static::$units as $unit1) {
            foreach (static::$units as $unit2) {
                $ret[] = array(
                    $unit1, $unit2, \HUGnet\devices\datachan\Driver::TYPE_RAW
                );
                $ret[] = array(
                    $unit1, $unit2, \HUGnet\devices\datachan\Driver::TYPE_DIFF
                );
            }
        }
        return $ret;
    }
    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataConversions()
    {
        return self::dataConversionsData();
    }
    /**
    * test all the valid units
    *
    * @param string $unit1 The first unit
    * @param string $unit2 The second unit
    * @param string $type  The data type
    *
    * @return null
    *
    * @dataProvider dataConversions
    */
    public function testConversions($unit1, $unit2, $type)
    {
        $data = 1;
        $this->o->convert($data, $unit1, $unit2, $type);
        $this->o->convert($data, $unit2, $unit1, $type);
        $this->assertEquals(
            1, $data, "$unit1 to $unit2 conversion failed", 0.01
        );
    }

    /**
    * test all the valid units
    *
    * @return null
    */
    public function testAllValid()
    {
        $valid = $this->o->getValid();
        $ret = array();
        foreach ($valid as $val) {
            $ret[] = $val;
        }
        $this->assertEquals(static::$units, $ret);
    }
    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataGetValid()
    {
        return array();
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $class  The class to use
    * @param string $expect The expected data
    *
    * @return null
    *
    * @dataProvider dataGetValid
    */
    public function testGetValid($class, $expect)
    {
        $this->assertEquals($expect, $this->o->getValid());
    }
    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataValid()
    {
        return array();
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $units  The units to setup up for
    * @param string $check  The units to check
    * @param string $expect The expected data
    *
    * @return null
    *
    * @dataProvider dataValid
    */
    public function testValid($units, $check, $expect)
    {
        $this->assertSame($expect, $this->o->valid($check));
    }
    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataNumeric()
    {
        return array();
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $units  The units to setup up for
    * @param string $check  The units to check
    * @param string $expect The expected data
    *
    * @return null
    *
    * @dataProvider dataNumeric
    */
    public function testNumeric($units, $check, $expect)
    {
        $this->assertSame($expect, $this->o->numeric($check));
    }

    /**
    * data provider for testGetTypes
    *
    * @return array
    */
    public static function dataConvert()
    {
        return array();
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
    * @dataProvider dataConvert
    */
    public function testConvert($data, $to, $from, $type, $return, $expect)
    {
        $this->assertSame(
            $return, $this->o->convert($data, $to, $from, $type), "Return is wrong"
        );
        $this->assertEquals($expect, $data, "Result is wrong", 0.000001);
    }

}
?>
