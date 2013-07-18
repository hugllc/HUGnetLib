<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\outputTable\drivers;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBase.php";
/** This is a required class */
require_once CODE_BASE.'devices/outputTable/drivers/HUGnetPower.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetPowerTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "HUGnetPower";
    /** This is the object under test */
    protected $o;
    /** This is the output */
    protected $output;
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
        $this->o = \HUGnet\devices\outputTable\Driver::factory(
            "HUGnetPower", $this->output
        );
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
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecode()
    {
        return array(
            array( // #0
                array(
                    'dev' => 1,
                    'output' => 1,
                    'id' => 0x30,
                ),
                "0078563412",
                array(
                    'dev' => 1,
                    'output' => 1,
                    'id' => 0x30,
                    'extra' => array(0, 0x12345678),
                    'driver' => "HUGnetPower",
                    'type' => "HUGnetPower",
                    'params' => array(),
                ),
            ),
            array( // #1 Negative Numbers
                array(
                    'dev' => 1,
                    'output' => 1,
                    'id' => 0x30,
                ),
                "01FFFFFFFF",
                array(
                    'dev' => 1,
                    'output' => 1,
                    'id' => 0x30,
                    'extra' => array(1, -1),
                    'driver' => "HUGnetPower",
                    'type' => "HUGnetPower",
                    'params' => array(),
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $string The setup string to test
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDecode
    */
    public function testDecode($mocks, $string, $expect)
    {
        $this->output->load($mocks);
        $this->o->decode($string);
        $this->assertEquals($expect, $this->output->toArray(false));
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
                    "extra" => array(
                        0,
                        0x12345678
                    ),
                ),
                "0078563412",
            ),
            array( // #1 Negative number
                array(
                    "extra" => array(
                        1, -1
                    ),
                ),
                "01FFFFFFFF",
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
        $this->output->load($mocks);
        $ret = $this->o->encode();
        $this->assertSame($expect, $ret);
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
                array(),
                null,
            ),
            array(
                "max",
                array(
                    "extra" => array(0, 1, 2, 3)
                ),
                1,
            ),
            array(
                "min",
                array(
                    "extra" => array(0, 1, 2, 3)
                ),
                -1,
            ),
            array(
                "zero",
                array(
                    "extra" => array(0, 1, 2, 3)
                ),
                0,
            ),
        );
    }

}
?>
