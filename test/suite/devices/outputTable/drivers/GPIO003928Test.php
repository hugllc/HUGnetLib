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
namespace HUGnet\devices\outputTable\drivers;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBase.php";
/** This is a required class */
require_once CODE_BASE.'devices/outputTable/drivers/GPIO003928.php';

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
class GPIO003928Test extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "GPIO003928";
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
            "GPIO003928", $this->output
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
                    'id' => 0x32,
                ),
                "130A",
                array(
                    'dev' => 1,
                    'output' => 1,
                    'id' => 0x32,
                    'extra' => array(6.74, 10),
                    'driver' => "GPIO003928",
                    'type' => "GPIO003928",
                    'params' => array(),
                    'tableEntry' => array(),
                ),
            ),
            array( // #1
                array(
                    'dev' => 1,
                    'output' => 1,
                    'id' => 0x32,
                ),
                "1001",
                array(
                    'dev' => 1,
                    'output' => 1,
                    'id' => 0x32,
                    'extra' => array(8, 1),
                    'driver' => "GPIO003928",
                    'type' => "GPIO003928",
                    'params' => array(),
                    'tableEntry' => array(),
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
                ),
                "0102",
            ),
            array( // #1
                array(
                    "extra" => array(
                        10, 11
                    ),
                ),
                "0D0B",
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
    public static function dataChannels()
    {
        return array(
            array(
                array(
                    "storageUnit" => "unknown",
                    "maxDecimals" => 2,
                    "unitType" => "asdf",
                    "location" => "Hello",
                    "extra"    => array(1, 5),
                ),
                "",
                array(
                    array(
                        'min' => -127,
                        'max' => 127,
                        'label' => 'Hello',
                        'index' => 0,
                        'port' => 'Port5',
                    ),
                ),
            ),
        );
    }

}
?>
