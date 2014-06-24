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
namespace HUGnet\images;
/** This is a required class */
require_once CODE_BASE.'images/Driver.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is our base class */
require_once dirname(__FILE__)."/drivers/DriverTestBase.php";
/** This is our interface */
require_once CODE_BASE."images/drivers/DriverInterface.php";

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
        parent::setUp();
        $this->o = Driver::factory("DriverTestClass", $this->output);
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
        parent::tearDown();
        unset($this->o);
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
                "min",
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
        $this->assertSame($expect, $this->o->present($name, 1));
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
                array(),
                "ThisIsABadName",
                null,
            ),
            array(
                array(),
                "mimetype",
                "application/unknown",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mock   The mocks to use
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet($mock, $name, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mock);
        $this->assertSame($expect, $this->o->get($name, 1));
    }
    /**
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testToArray()
    {
        $expect = array(
            "min" => 25,
            "max" => 81,
            "port" => "2Z",
            'mimetype' => 'application/unknown'
        );
        $this->assertEquals($expect, $this->o->toArray(1));
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
                array(),
                "HUGnet\images\drivers\PNG",
            ),
            array(
                "EmptyOutput",
                array(),
                "HUGnet\images\drivers\PNG",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The name of the variable to test.
    * @param array  $table  The table info to give the class
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataFactory
    */
    public function testFactory($name, $table, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($extra);
        $o = Driver::factory($name, $sensor, $table);
        $this->assertSame($expect, get_class($o));
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataEncode()
    {
        return array(
            array( // #0
                array(
                    "Device" => array(
                        "get" => array(
                            "id" => 7,
                        ),
                    ),
                ),
                array(
                    'mimetype' => 'application/unknown',
                    "min" => 25,
                    "max" => 81,
                    "port" => "2Z",
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $mocks  The value to preload into the mocks
    * @param array $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataEncode
    */
    public function testEncode($mocks, $expect)
    {
        $sensor  = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mocks);
        $obj = Driver::factory("DriverTestClass", $sensor);
        $ret = $obj->encode();
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataBrightness()
    {
        return array(
            array( // #0
                array(
                ),
                "#FFFFFF",
                255.0,
            ),
            array( // #1
                array(
                ),
                "#000000",
                0.0,
            ),
            array( // #2
                array(
                ),
                "#888888",
                136.0,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $color  The color to check against
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataBrightness
    */
    public function testBrightness($mocks, $color, $expect)
    {
        $sensor  = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mocks);
        $obj = Driver::factory("DriverTestClass", $sensor);
        $ret = $obj->brightness($color);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataMostReadable()
    {
        return array(
            array( // #0
                array(
                ),
                "#000000",
                array("#000000", "#888888", "#FFFFFF"),
                "#FFFFFF"
            ),
            array( // #1
                array(
                ),
                "#FFFFFF",
                array("#000000", "#888888", "#FFFFFF"),
                "#000000"
            ),
            array( // #2
                array(
                ),
                "#888888",
                array("#000000", "#888888", "#FFFFFF"),
                "#000000"
            ),
            array( // #3
                array(
                ),
                "#888888",
                array(null, "#888888", "#FFFFFF"),
                "#FFFFFF"
            ),
            array( // #4
                array(
                ),
                "#888888",
                array("#000", "#888888", "#FFFFFF"),
                "#FFFFFF"
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $color  The color to check against
    * @param array  $colors The color array to try
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataMostReadable
    */
    public function testMostReadable($mocks, $color, $colors, $expect)
    {
        $sensor  = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mocks);
        $obj = Driver::factory("DriverTestClass", $sensor);
        $ret = $obj->mostReadable($color, $colors);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataReadable()
    {
        return array(
            array( // #0
                array(
                ),
                "#000000",
                "#000000",
                false,
            ),
            array( // #1
                array(
                ),
                "#FFFFFF",
                "#000000",
                true,
            ),
            array( // #2
                array(
                ),
                "#888888",
                "#000000",
                true,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $color1 The color to check against
    * @param string $color2 The color array to try
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataReadable
    */
    public function testReadable($mocks, $color1, $color2, $expect)
    {
        $sensor  = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mocks);
        $obj = Driver::factory("DriverTestClass", $sensor);
        $ret = $obj->readable($color1, $color2);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataAutocolor()
    {
        return array(
            array( // #0
                array(
                ),
                "#FFFFFF",
                array(
                    "color"      => "#123456",
                    "color2"     => "#8E8E8E",
                    "background" => "#FFFFFF",
                ),
                "#123456",
            ),
            array( // #1
                array(
                ),
                "#888888",
                array(
                    "color"      => "#123456",
                    "color2"     => "#8E8E8E",
                    "background" => "#FFFFFF",
                ),
                "#000000",
            ),
            array( // #2
                array(
                ),
                "#FFFFFF",
                array(
                    "background" => "#FFFFFF",
                ),
                "#000000",
            ),
            array( // #2
                array(
                ),
                "#000000",
                array(
                    "background" => "#FFFFFF",
                ),
                "#FFFFFF",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $value  The value to use
    * @param array  $point  The point to use
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataAutocolor
    */
    public function testAutocolor($mocks, $value, $point, $expect)
    {
        $sensor  = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mocks);
        $obj = Driver::factory("DriverTestClass", $sensor);
        $ret = $obj->autocolor($value, $point);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataAutobackground()
    {
        return array(
            array( // #0
                array(
                ),
                5,
                array(
                    "background" => "#FFFFFF",
                    "backgroundmax" => "#000000",
                    "valmin" => 1,
                    "valmax" => 10,
                ),
                "#8E8E8E",
            ),
            array( // #1
                array(
                ),
                1,
                array(
                    "background" => "#FFFFFF",
                    "backgroundmax" => "#000000",
                    "valmin" => 1,
                    "valmax" => 10,
                ),
                "#FFFFFF",
            ),
            array( // #2
                array(
                ),
                10,
                array(
                    "background" => "#FFFFFF",
                    "backgroundmax" => "#000000",
                    "valmin" => 1,
                    "valmax" => 10,
                ),
                "#000000",
            ),
            array( // #3
                array(
                ),
                5,
                array(
                    "background" => "#FFFFFF",
                ),
                "#FFFFFF",
            ),
            array( // #4
                array(
                ),
                5,
                array(
                    "background" => "#FFFFFF",
                    "backgroundmax" => "#000000",
                    "valmin" => 10,
                    "valmax" => 1,
                ),
                "#FFFFFF",
            ),
            array( // #5
                array(
                ),
                0,
                array(
                    "background" => "#FFFFFF",
                    "backgroundmax" => "#000000",
                    "valmin" => 1,
                    "valmax" => 10,
                ),
                "#FFFFFF",
            ),
            array( // #6
                array(
                ),
                11,
                array(
                    "background" => "#FFFFFF",
                    "backgroundmax" => "#000000",
                    "valmin" => 1,
                    "valmax" => 10,
                ),
                "#000000",
            ),
            array( // #7
                array(
                ),
                "5 V",
                array(
                    "background" => "#FFFFFF",
                    "backgroundmax" => "#000000",
                    "valmin" => 1,
                    "valmax" => 10,
                ),
                "#8E8E8E",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $value  The value to use
    * @param array  $point  The point to use
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataAutobackground
    */
    public function testAutobackground($mocks, $value, $point, $expect)
    {
        $sensor  = new \HUGnet\DummyTable("Sensor");
        $sensor->resetMock($mocks);
        $obj = Driver::factory("DriverTestClass", $sensor);
        $ret = $obj->autobackground($value, $point);
        $this->assertSame($expect, $ret);
    }
}
/** This is the HUGnet namespace */
namespace HUGnet\images\drivers;
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
class DriverTestClass extends \HUGnet\images\Driver
    implements \HUGnet\images\drivers\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "min" => 25,
        "max" => 81,
        "port" => "2Z",
    );
    /**
    * Returns the object as a string
    *
    * @return string
    */
    public function encode()
    {
        return $this->toArray();
    }
    /**
     * Returns the brightness of the color
     *
     * @param string $color The color in HTML format (#RRGGBB)
     *
     * @return array The rgb information
     */
    public function brightness($color)
    {
        return parent::brightness($color);
    }
    /**
     * Returns whether a color combination will be readable
     *
     * @param string $color1 The first color in HTML format (#RRGGBB)
     * @param string $color2 The second color in HTML format (#RRGGBB)
     *
     * @return array The rgb information
     */
    public function readable($color1, $color2)
    {
        return parent::readable($color1, $color2);
    }
    /**
     * Converts an HTML color into RGB
     *
     * @param string $color  The color in HTML format (#RRGGBB)
     * @param array  $colors Array of colors in HTML format (#RRGGBB)
     *
     * @return array The rgb information
     */
    public function mostReadable($color, $colors)
    {
        return parent::mostReadable($color, $colors);
    }
    /**
     * Method to automatically pick a color
     *
     * @param mixed $value The value to use
     * @param array $point The point to use
     *
     * @return string
     */
    public function autobackground($value, $point)
    {
        return parent::autobackground($value, $point);
    }
    /**
     * Method to automatically pick a color
     *
     * @param string $background The background color to use
     * @param array  $point      The point to use
     *
     * @return string
     */
    public function autocolor($background, $point)
    {
        return parent::autocolor($background, $point);
    }
}
?>
