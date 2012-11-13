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
 * @subpackage SuiteCLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is a required class */
require_once CODE_BASE.'HUGnetLib.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteCLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DaemonTest extends \PHPUnit_Framework_TestCase
{
    /** Files that have been created */
    private $_files = array();
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
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataArgs()
    {
        return array(
            array(
                array(
                ),
                "HTML",
                array(
                ),
                "HUGnet\ui\HTMLArgs",
                array(
                    'quiet' => false,
                    'verbose' => 0,
                    'debug' => false,
                    'test' => false,
                ),
            ),
            array(
                null,
                null,
                null,
                "HUGnet\ui\Args",
                array(
                    'quiet' => false,
                    'verbose' => 0,
                    'debug' => false,
                    'test' => false,
                    'file' => '',
                ),
            ),
            array(
                null,
                "html",
                null,
                "HUGnet\ui\HTMLArgs",
                array(
                    'quiet' => false,
                    'verbose' => 0,
                    'debug' => false,
                    'test' => false,
                ),
            ),
            array(
                array(
                    "i" => array(
                        "name" => "DeviceID", "type" => "string", "args" => true
                    ),
                ),
                "NotRight",
                array(
                ),
                "HUGnet\ui\Args",
                array(
                    'quiet' => false,
                    'verbose' => 0,
                    'debug' => false,
                    'test' => false,
                    'file' => '',
                    "DeviceID" => null,
                ),
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param mixed  $config  The config to use
    * @param string $type    The type to use
    * @param array  $args    The args to give it.
    * @param string $expect  The system object we are expecting
    * @param mixed  $expArgs The expected arguments
    *
    * @return null
    *
    * @dataProvider dataArgs
    */
    public function testArgs($config, $type, $args, $expect, $expArgs)
    {
        $ret = HUGnetLib::args($config, $type, $args);
        $this->assertSame($expect, get_class($ret), "Class Wrong");
        $this->assertEquals($expArgs, $ret->config(), "Config wrong");
    }
    /**
    * Data provider for testUi
    *
    * @return array
    */
    public static function dataUi()
    {
        return array(
            array(
                null,
                "html",
                "HUGnet\ui\HTML",
            ),
            array(
                null,
                "daemon",
                "HUGnet\ui\Daemon",
            ),
            array(
                null,
                "webapi",
                "HUGnet\ui\WebAPI",
            ),
            array(
                array(
                ),
                "NotRight",
                "HUGnet\ui\CLI",
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param mixed  $config The config to use
    * @param string $type   The type to use
    * @param string $expect The system object we are expecting
    *
    *
    * @return null
    *
    * @dataProvider dataUi
    */
    public function testUi($config, $type, $expect)
    {
        $ret = HUGnetLib::ui($config, $type);
        $this->assertSame($expect, get_class($ret), "Class Wrong");
    }

}
?>
