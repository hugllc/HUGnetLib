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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\ui;
/** This is a required class */
require_once CODE_BASE.'ui/HTMLArgs.php';
require_once CODE_BASE.'util/VPrint.php';
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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HTMLArgsTest extends \PHPUnit_Framework_TestCase
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
    public static function dataHTMLArgs()
    {
        return array(
            array(  // #0 // Simple example
                array(
                    "v" => 3,
                    "q" => 1,
                    "i" => "asdf",
                    "klong" => "wwww",
                ),
                8,
                array(
                    "i" => array(
                        "name" => "ilong", "type" => "string", "args" => true
                    ),
                    "j" => array(
                        "name" => "jlong", "default" => "1234",
                    ),
                    "k" => array(
                        "name" => "klong", "default" => "qqqq",
                    ),
                ),
                array(
                    "i" => "asdf",
                    "v" => 3,
                    "q" => true,
                    "d" => false,
                    "t" => false,
                    "ilong" => "asdf",
                    "jlong" => "1234",
                    "k" => "wwww",
                ),
                array(
                ),
                null,
                array(
                    "quiet" => true,
                    "verbose" => 3,
                    "debug" => false,
                    "test" => false,
                    "ilong" => "asdf",
                    "jlong" => "1234",
                    "klong" => "wwww",
                ),
                "",
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
    * @param array  $arguments The arguments we expect to be set
    * @param string $file      The file to copy to this directory.  Null for no file
    * @param array  $expect    The config array we are expecting
    * @param string $output    The expected printout
    *
    * @return null
    *
    * @dataProvider dataHTMLArgs()
    */
    public function testHTMLArgs(
        $argv, $argc, $config, $set, $arguments, $file, $expect, $output
    ) {
        if (!is_null($file)) {
            copy($file, "./".basename($file));
            $this->_files[] = "./".basename($file);
        }
        $args = &HTMLArgs::factory($argv, $argc, $config);
        foreach ((array)$set as $name => $value) {
            $args->set($name, $value);
        }
        foreach ($arguments as $key => $value) {
            $this->assertSame($value, $args->$key, "Argument $key wrong");
        }
        ob_start();
        $config = $args->config();
        $ret = ob_get_contents();
        ob_end_clean();
        $this->assertEquals($expect, $config);
        $this->assertSame($output, trim($ret));
    }

}
?>
