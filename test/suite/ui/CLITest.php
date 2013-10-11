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
 * @subpackage SuiteCLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\ui;
/** This is a required class */
require_once CODE_BASE.'ui/CLI.php';
/** This is a required class */
require_once CODE_BASE.'system/Error.php';
/** This is a required class */
require_once CODE_BASE.'ui/Args.php';
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class CLITest extends \PHPUnit_Framework_TestCase
{
    /** Files that have been created */
    private $_files = array();
    /** This is the system object */
    protected $system;
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
        $this->system = $this->getMock("\HUGnet\System", array('now'));
        $this->system->config(
            array(
                "servers" => array(
                    "default" => array(
                        "driver" => "sqlite",
                        "file" => ":memory:",
                        "group" => "default",
                    ),
                ),
            )
        );
        $this->system->expects($this->any())
            ->method('now')
            ->will($this->returnValue(1000000));
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
        unset($this->system);
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataSystem()
    {
        $data = array();
        $htmlargs = \HUGnet\ui\Args::factory($argv, $argc, $data);
        return array(
            array(
                new \HUGnet\DummySystem("System"),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 5,
                            "other" => "stuff",
                        ),
                    ),
                ),
                array(
                    "verbose" => 5,
                    "other" => "stuff",
                ),
            ),
            array(
                array(
                    "verbose" => 5,
                ),
                null,
                array(
                    "verbose" => 6,
                    "html" => false,
                    "min_log" => \HUGnet\Error::ERROR,
                )
            ),
            array(
                $htmlargs,
                null,
                array(
                    "verbose" => 1,
                    "html" => false,
                    "file" => "",
                    "quiet" => false,
                    "debug" => false,
                    "test" => false,
                    "min_log" => \HUGnet\Error::ERROR,
                )
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param mixed $config The config to use
    * @param array $mock   The mocks to use
    * @param mixed $expect The system object we are expecting
    *
    * @return null
    *
    * @dataProvider dataSystem()
    */
    public function testSystem($config, $mock, $expect)
    {
        if (is_array($mock)) {
            $config->resetMock($mock);
        }
        ob_start();
        $obj = CLI::factory($config);
        $conf = $obj->system()->config();
        ob_end_clean();
        if (!is_array($mock)) {
            $this->assertTrue(isset($conf["IPAddr"]), "IP Address missing");
        }
        unset($conf["IPAddr"]);
        $this->assertEquals($expect, $conf, "Config wrong");
    }
    /**
    * Data provider for testVerbose
    *
    * @return array
    */
    public static function dataVerbose()
    {
        return array(
            array(
                array(
                ),
                2,
                2
            ),
            array(
                array(
                    "verbose" => 2,
                ),
                null,
                3
            ),
            array(
                array(
                    "verbose" => 2,
                ),
                "asdf",
                3
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param mixed $config The config to use
    * @param mixed $value  The value to set it at
    * @param int   $expect The return we are expecting
    *
    * @return null
    *
    * @dataProvider dataVerbose()
    */
    public function testVerbose($config, $value, $expect)
    {
        $this->system->config($config);
        $obj = CLI::factory($this->system);
        $this->assertEquals($expect, $obj->verbose($value));
    }

}
?>
