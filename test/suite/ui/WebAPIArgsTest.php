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
require_once CODE_BASE.'ui/WebAPIArgs.php';
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
class WebAPIArgsTest extends \PHPUnit_Framework_TestCase
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
    public static function dataWebAPIArgs()
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
                array(),
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
                    'task' => null,
                    'action' => null,
                    'id' => null,
                    'cuuid' => null,
                    'data' => Array (),
                    'subtask' => null,
                    'sid' => null,
                    'object' => null,
                    'subobject' => null,
                    'method' => "GET",
                    'restextra' => Array (),
                    'since' => null,
                    'until' => null,
                    'limit' => null,
                    'start' => null,
                    'order' => null,
                ),
                "",
            ),
            array(  // #1 REST Test #1
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
                    "SCRIPT_NAME" => "/this/is/a.php",
                    "REQUEST_URI" => "/this/is/a.php/url",
                    "REQUEST_METHOD" => "GET",
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
                    "object" => "url",
                    "method" => "GET",
                    "id" => null,
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
                    'task' => null,
                    'subtask' => null,
                    'action' => null,
                    'id' => null,
                    'sid' => null,
                    'object' => 'url',
                    'subobject' => null,
                    'method' => 'GET',
                    'cuuid' => null,
                    'data' => array(),
                    'restextra' => Array (),
                    'since' => null,
                    'until' => null,
                    'limit' => null,
                    'start' => null,
                    'order' => null,
                ),
                "",
            ),
            array(  // #2 REST Test #2
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
                    "SCRIPT_NAME" => "/this/is/a.php",
                    "REQUEST_URI" => "/this/is/a.php/url/10",
                    "REQUEST_METHOD" => "GET",
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
                    "object" => "url",
                    "id" => '10',
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
                    'task' => null,
                    'subtask' => null,
                    'action' => null,
                    'id' => "10",
                    'sid' => null,
                    'object' => 'url',
                    'subobject' => null,
                    'method' => 'GET',
                    'cuuid' => null,
                    'data' => array(),
                    'restextra' => Array (),
                    'since' => null,
                    'until' => null,
                    'limit' => null,
                    'start' => null,
                    'order' => null,
                ),
                "",
            ),
            array(  // #3 REST Test #3
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
                    "SCRIPT_NAME" => "/this/is/a.php",
                    "REQUEST_URI" => "/this/is/a.php/url/10/sub",
                    "REQUEST_METHOD" => "GET",
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
                    "object" => "url",
                    "method" => "GET",
                    "id" => '10',
                    "subobject" => "sub",
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
                    'task' => null,
                    'subtask' => null,
                    'action' => null,
                    'id' => "10",
                    'sid' => null,
                    'object' => 'url',
                    'subobject' => 'sub',
                    'method' => 'GET',
                    'cuuid' => null,
                    'data' => array(),
                    'restextra' => Array (),
                    'since' => null,
                    'until' => null,
                    'limit' => null,
                    'start' => null,
                    'order' => null,
                ),
                "",
            ),
            array(  // #4 REST Test #4
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
                    "SCRIPT_NAME" => "/this/is/a.php",
                    "REQUEST_URI" => "/this/is/a.php/url/10/sub/12",
                    "REQUEST_METHOD" => "GET",
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
                    "object" => "url",
                    "action" => null,
                    "id" => '10',
                    "subobject" => "sub",
                    "sid" => '12',
                    "method" => "GET",
                    "restextra" => array(),
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
                    'task' => null,
                    'subtask' => null,
                    'action' => null,
                    'id' => "10",
                    'sid' => "12",
                    'object' => 'url',
                    'subobject' => 'sub',
                    'method' => 'GET',
                    'cuuid' => null,
                    'data' => array(),
                    'restextra' => Array (),
                    'since' => null,
                    'until' => null,
                    'limit' => null,
                    'start' => null,
                    'order' => null,
                ),
                "",
            ),
            array(  // #5 REST Test #5
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
                    "SCRIPT_NAME" => "/this/is/a.php",
                    "REQUEST_URI" => "/this/is/a.php/url/10/sub/12/other/stuff/here",
                    "REQUEST_METHOD" => "GET",
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
                    "object" => "url",
                    "action" => null,
                    "id" => '10',
                    "subobject" => "sub",
                    "sid" => '12',
                    "method" => 'GET',
                    "restextra" => array('other', 'stuff', 'here'),
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
                    'task' => null,
                    'subtask' => null,
                    'action' => null,
                    'id' => "10",
                    'sid' => "12",
                    'object' => 'url',
                    'subobject' => 'sub',
                    'method' => 'GET',
                    'cuuid' => null,
                    'data' => array(),
                    'restextra' => Array ('other', 'stuff', 'here'),
                    'since' => null,
                    'until' => null,
                    'limit' => null,
                    'start' => null,
                    'order' => null,
                ),
                "",
            ),
            array(  // #6 REST Test #6
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
                    "SCRIPT_NAME" => "/this/is/a.php",
                    "REQUEST_URI" => "/this/is/a.php/url/10/sub/5",
                    "REQUEST_METHOD" => "POST",
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
                    "object" => "url",
                    "action" => null,
                    "id" => '10',
                    "subobject" => "sub",
                    "method" => "POST",
                    "sid" => "5",
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
                    'task' => null,
                    'subtask' => null,
                    'action' => null,
                    'id' => "10",
                    'sid' => "5",
                    'object' => 'url',
                    'subobject' => 'sub',
                    'method' => 'POST',
                    'cuuid' => null,
                    'data' => array(),
                    'restextra' => Array (),
                    'since' => null,
                    'until' => null,
                    'limit' => null,
                    'start' => null,
                    'order' => null,
                ),
                "",
            ),
            array(  // #7 REST Test #7
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
                    "SCRIPT_NAME" => "/this/is/a.php",
                    "REQUEST_URI" => "/this/is/a.php/url/10/sub/5",
                    "REQUEST_METHOD" => "DELETE",
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
                    "object" => "url",
                    "action" => null,
                    "id" => '10',
                    "subobject" => "sub",
                    "method" => "DELETE",
                    "sid" => '5',
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
                    'task' => null,
                    'subtask' => null,
                    'action' => null,
                    'id' => "10",
                    'sid' => "5",
                    'object' => 'url',
                    'subobject' => 'sub',
                    'method' => 'DELETE',
                    'cuuid' => null,
                    'data' => array(),
                    'restextra' => Array (),
                    'since' => null,
                    'until' => null,
                    'limit' => null,
                    'start' => null,
                    'order' => null,
                ),
                "",
            ),
            array(  // #8 REST Test #8
                array(
                    "v" => 3,
                    "q" => 1,
                    "i" => "asdf",
                    "klong" => "wwww",
                    "since" => 12,
                    "until" => 34,
                    "limit" => 56,
                    "start" => 78,
                    "order" => "asc",
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
                    "SCRIPT_NAME" => "/this/is/a.php",
                    "REQUEST_URI" => "/this/is/a.php/url/0/sub/0",
                    "REQUEST_METHOD" => "DELETE",
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
                    "object" => "url",
                    "action" => null,
                    "id" => '0',
                    "subobject" => "sub",
                    "method" => "DELETE",
                    "sid" => '0',
                    "since" => '12',
                    "until" => '34',
                    "limit" => '56',
                    "start" => '78',
                    "order" => "asc",
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
                    'task' => null,
                    'subtask' => null,
                    'action' => null,
                    'id' => "0",
                    'sid' => "0",
                    'object' => 'url',
                    'subobject' => 'sub',
                    'method' => 'DELETE',
                    'cuuid' => null,
                    'data' => array(),
                    'restextra' => Array (),
                    'since' => 12,
                    'until' => 34,
                    'limit' => 56,
                    'start' => 78,
                    'order' => "asc",
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
    * @param array  $sysargs   The system args to use
    * @param array  $set       Values to set
    * @param array  $arguments The arguments we expect to be set
    * @param string $file      The file to copy to this directory.  Null for no file
    * @param array  $expect    The config array we are expecting
    * @param string $output    The expected printout
    *
    * @return null
    *
    * @dataProvider dataWebAPIArgs()
    */
    public function testWebAPIArgs(
        $argv, $argc, $config, $sysargs, $set, $arguments, $file, $expect, $output
    ) {
        if (!is_null($file)) {
            copy($file, "./".basename($file));
            $this->_files[] = "./".basename($file);
        }
        $args = &WebAPIArgs::factory($argv, $argc, $config, $sysargs);
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
