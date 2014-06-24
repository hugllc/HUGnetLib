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
 * @subpackage SuiteCLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\ui;
/** This is a required class */
require_once CODE_BASE.'ui/Args.php';
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteCLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ArgsTest extends \PHPUnit_Framework_TestCase
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
        foreach ($this->_files as $file) {
            unlink($file);
        }
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataArgs()
    {
        return array(
            array(  // #0 // Simple example
                array(
                    "test", "-i", "000ABC", "-v", "-v", "-v", "-q", "-n"
                ),
                8,
                array(
                    "i" => array("args" => true),
                ),
                array(
                    "i" => "000ABC",
                    "v" => 3,
                    "q" => true,
                    "d" => false,
                    "t" => false,
                    "n" => true,
                ),
                "",
                array(
                ),
                null,
                array(
                    "quiet" => true,
                    "verbose" => 3,
                    "debug" => false,
                    "test" => false,
                    "file" => '',
                    "program" => "test",
                ),
            ),
            array(  // #1 // Another Simple example
                array(
                    "test", "-vvvvvvi", "000ABC", "123456", "-n"
                ),
                5,
                array(
                    "i" => array("args" => true),
                ),
                array(
                    "loose" => array("123456"),
                    "i" => "000ABC",
                    "n" => true,
                    "test" => false,
                ),
                "",
                array(
                ),
                null,
                array(
                    "quiet" => false,
                    "verbose" => 6,
                    "debug" => false,
                    "test" => false,
                    "file" => '',
                    "program" => "test",
                ),
            ),
            array(  // #2 Stringing multiple switches together.
                array(
                    "test", "-dqtf", "/here/there"
                ),
                4,
                array(),
                array(
                    "f" => "/here/there",
                    "v" => 0,
                    "d" => true,
                    "q" => true,
                    "t"  => true,
                ),
                "",
                array(
                ),
                null,
                array(
                    "quiet" => true,
                    "verbose" => 0,
                    "debug" => true,
                    "test"  => true,
                    "file" => "",
                    "program" => "test",
                ),
            ),
            array(  // #3 File test.
                array(
                    "test", "-f", TEST_CONFIG_BASE."files/config.ini"
                ),
                4,
                array(),
                array(
                    "f" => TEST_CONFIG_BASE."files/config.ini",
                ),
                "",
                array(
                ),
                null,
                array(
                    "hugnet_database" => "MyDatabase",
                    "script_gatewaykey" => "2",
                    "poll_enable" => "1",
                    "config_enable" => "",
                    "control_enable" => "",
                    "check_enable" => "1",
                    "check_send_daily" => "1",
                    "analysis_enable" => "0",
                    "admin_email" => "you@yourdomain.com",
                    "verbose" => 0,
                    "servers" => array(
                        "default" => array(
                            "driver" => "mysql",
                            "host" => "10.2.5.23",
                            "user" => "user",
                            "password" => "password",
                        ),
                    ),
                    "network" => array(
                        "default" => array(
                            "driver" => "Socket",
                            "location" => "10.2.3.5",
                            "port" => 2001,
                        ),
                    ),
                    "file" => TEST_CONFIG_BASE."files/config.ini",
                    "quiet" => false,
                    "debug" => false,
                    "test" => false,
                    "program" => "test",
                ),
            ),
            array(  // #4 another file test
                array(
                    "test",
                ),
                4,
                array(),
                array(
                ),
                "",
                array(
                ),
                TEST_CONFIG_BASE."files/config.ini",
                array(
                    "hugnet_database" => "MyDatabase",
                    "script_gatewaykey" => "2",
                    "poll_enable" => "1",
                    "config_enable" => "",
                    "control_enable" => "",
                    "check_enable" => "1",
                    "check_send_daily" => "1",
                    "analysis_enable" => "0",
                    "admin_email" => "you@yourdomain.com",
                    "verbose" => 0,
                    "servers" => array(
                        "default" => array(
                            "driver" => "mysql",
                            "host" => "10.2.5.23",
                            "user" => "user",
                            "password" => "password",
                        ),
                    ),
                    "network" => array(
                        "default" => array(
                            "driver" => "Socket",
                            "location" => "10.2.3.5",
                            "port" => 2001,
                        ),
                    ),
                    "file" => "./config.ini",
                    "quiet" => false,
                    "debug" => false,
                    "test" => false,
                    "program" => "test",
                ),
            ),
            array(  // #5 // Simple example
                array(
                    "programname",
                ),
                0,
                array(
                    "i" => array("args" => true),
                ),
                array(
                    "i" => "000ABC",
                    "v" => 3,
                    "q" => true,
                    "d" => false,
                    "t" => false,
                    "n" => true,
                ),
                "",
                array(
                    "i" => "000ABC",
                    "v" => 3,
                    "q" => true,
                    "n" => true,
                ),
                null,
                array(
                    "quiet" => true,
                    "verbose" => 3,
                    "debug" => false,
                    "test" => false,
                    "file" => '',
                    "program" => "programname",
                ),
            ),
            array(  // #6 File test with addLocation.
                array(
                    "test", "-v"
                ),
                4,
                array(),
                array(
                ),
                TEST_CONFIG_BASE."files/config.ini",
                array(
                ),
                null,
                array(
                    "hugnet_database" => "MyDatabase",
                    "script_gatewaykey" => "2",
                    "poll_enable" => "1",
                    "config_enable" => "",
                    "control_enable" => "",
                    "check_enable" => "1",
                    "check_send_daily" => "1",
                    "analysis_enable" => "0",
                    "admin_email" => "you@yourdomain.com",
                    "verbose" => 1,
                    "servers" => array(
                        "default" => array(
                            "driver" => "mysql",
                            "host" => "10.2.5.23",
                            "user" => "user",
                            "password" => "password",
                        ),
                    ),
                    "network" => array(
                        "default" => array(
                            "driver" => "Socket",
                            "location" => "10.2.3.5",
                            "port" => 2001,
                        ),
                    ),
                    "file" => TEST_CONFIG_BASE."files/config.ini",
                    "quiet" => false,
                    "debug" => false,
                    "test" => false,
                    "program" => "test",
                ),
            ),
            array(  // #7 // Simple example with negative numbers
                array(
                    "test", "-z", "-45", "-v", "-v", "-v", "-q", "-n"
                ),
                8,
                array(
                    "z" => array(
                        "name" => "Value",
                        "args" => true,
                        "type" => "int",
                        "default" => 20
                    ),
                ),
                array(
                ),
                "",
                array(
                ),
                null,
                array(
                    "quiet" => true,
                    "verbose" => 3,
                    "debug" => false,
                    "test" => false,
                    "file" => '',
                    "program" => "test",
                    "Value" => -45,
                ),
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array  $argv      The array of arguments (first should be name)
    * @param int    $argc      The number of arguments
    * @param array  $config    The config array to feed it
    * @param array  $set       Values to set
    * @param string $location  Extra file location to add
    * @param array  $arguments The arguments we expect to be set
    * @param string $file      The file to copy to this directory.  Null for no file
    * @param array  $expect    The config array we are expecting
    *
    * @return null
    *
    * @dataProvider dataArgs()
    */
    public function testArgs(
        $argv, $argc, $config, $set, $location, $arguments, $file, $expect
    ) {
        if (!is_null($file)) {
            copy($file, "./".basename($file));
            $this->_files[] = "./".basename($file);
        }
        $args = &Args::factory($argv, $argc, $config);
        foreach ((array)$set as $name => $value) {
            $args->set($name, $value);
        }
        $args->addLocation($location);
        foreach ($arguments as $key => $value) {
            $this->assertSame($value, $args->$key, "Argument $key wrong");
        }
        $config = $args->config();
        $this->assertEquals($expect, $config, "Config wrong");
    }

}
?>
