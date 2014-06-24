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
require_once CODE_BASE.'devices/outputTable/drivers/FET003912.php';

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
class FET003912Test extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "FET003912";
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
            "FET003912", $this->output
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
                    'id' => 0x31,
                ),
                "130102",
                array(
                    'dev' => 1,
                    'output' => 1,
                    'id' => 0x31,
                    'extra' => array(6.74, 2, 1),
                    'driver' => "FET003912",
                    'type' => "FET003912",
                    'params' => array(),
                    'tableEntry' => array(),
                ),
            ),
            array( // #1
                array(
                    'dev' => 1,
                    'output' => 1,
                    'id' => 0x31,
                ),
                "100103",
                array(
                    'dev' => 1,
                    'output' => 1,
                    'id' => 0x31,
                    'extra' => array(8.0, 3, 1),
                    'driver' => "FET003912",
                    'type' => "FET003912",
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
                "010000",
            ),
            array( // #1
                array(
                    "extra" => array(
                        10, 11, 12
                    ),
                ),
                "0D0C0B",
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
                    "extra"    => array(1, 2),
                ),
                array(
                    array(
                        'min' => -256,
                        'max' => 256,
                        'label' => 'Hello',
                        'index' => 0,
                        'port' => 'Port3',
                    ),
                ),
            ),
        );
    }

}
?>
