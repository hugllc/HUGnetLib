<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2015 Hunt Utilities Group, LLC
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
 * @copyright  2015 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once CODE_BASE.'devices/Error.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2015 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ErrorTest extends \PHPUnit_Framework_TestCase
{
    /** @var The system object */
    protected $sys = null;
    /** @var The system object */
    protected $dev = null;
    /** @var The table object */
    protected $table = null;
    /** @var The test object */
    protected $o = null;
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
        $this->sys = new \HUGnet\DummyBase("System");
        $this->sys->resetMock(
            array(
                "System" => array(
                    "now" => 1234,
                ),
            )
        );
        $this->dev = new \HUGnet\DummyBase("Device");
        $this->table = new \HUGnet\DummyTable("ErrorTable");
        $this->o = Error::factory($this->sys, null, $this->table, $this->dev);
        parent::setUp();
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
    * Data provider for testLog
    *
    * @return array
    */
    public static function dataLog()
    {
        return array(
            array(
                array(
                    "System" => array(
                        "now" => 1234,
                    ),
                    "Device" => array(
                        "get" => 0x5C,
                        "id" => 0x5C,
                    ),
                ),
                array(
                    "id" => 0x5C,
                ),
                array(
                    "ErrorTable" => array(
                        "fromArray" => array(
                            array(
                                array(
                                    "id" => 0x5C,
                                    "severity" => "E",
                                ),
                            ),
                        ),
                        "insertRow" => array(array(true)),
                    ),
                    "System" => array(
                        "fatalError" => array(
                            array(
                                0 => 'HUGnet\devices\Error needs to be passed'
                                    .' a device object',
                                1 => false,
                            )
                        ),
                    ),
                    "Device" => array(
                        "id" => array(array()),
                    ),
                ),
            ),
            array(
                array(
                    "System" => array(
                        "now" => 1234,
                    ),
                    "Device" => array(
                        "get" => 0x5C,
                        "id" => 0x5C,
                        "decodeRTC" => array(
                            "01020304" => 1234,
                        ),
                    ),
                ),
                "0102030405060708090A0B0C0D0E0F",
                array(
                    "ErrorTable" => array(
                        "fromArray" => array(
                            array(
                                array(
                                    "id" => 0x5C,
                                    "Date" => 1234,
                                    "errno" => 5,
                                    "extra" => '060708090A0B0C0D0E0F',
                                    "severity" => "E",
                                ),
                            ),
                        ),
                        "insertRow" => array(array(true)),
                    ),
                    "System" => array(
                        "fatalError" => array(
                            array(
                                0 => 'HUGnet\devices\Error needs to be passed'
                                    .' a device object',
                                1 => false,
                            )
                        ),
                    ),
                    "Device" => array(
                        "id" => array(array()),
                        "decodeRTC" => array(array("01020304")),
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $system The configuration to use
    * @param mixed  $error  The error.  Could be a string or array
    * @param array  $expect The table to expect
    *
    * @return null
    *
    * @dataProvider dataLog
    */
    public function testLog(
        $system, $error, $expect
    ) {
        $this->sys->resetMock($system);
        $obj = Error::factory($this->sys, null, $this->table, $this->dev);
        $obj->log($error);
        $this->assertEquals($expect, $this->table->retrieve(), "Data Wrong");
    }


}
?>
