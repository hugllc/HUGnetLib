<?php
/**
 * Tests the filter class
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
 * @subpackage SuiteSockets
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is our namespace */
namespace HUGnet\network\physical;

/** This is a required class */
require_once CODE_BASE.'network/physical/Serial.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySocket.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteSockets
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class SerialTest extends \PHPUnit_Framework_TestCase
{
    /** This is where we store our created files */
    protected $files = array();
    /**
    * Sets up the fixture, for example, open a network connection.
    * This method is called before a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function setUp()
    {
    }

    /**
    * Tears down the fixture, for example, close a network connection.
    * This method is called after a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function tearDown()
    {
        foreach ((array)$this->files as $file) {
            @unlink($file);
        }
    }
    /**
    * data provider for testSerial
    *
    * @return array
    */
    public static function dataSerial()
    {
        return array(
            array( // #0 File doesn't exist
                array(
                    "location" => TEST_CONFIG_BASE."thisIsAVeryBadSerialPort",
                    "quiet" => false,
                ),
                false,
                "",
                false,
                false,
                false,
                "RuntimeException",
            ),
            array( // #1 File doesn't exist but quite mode is in place
                array(
                    "location" => TEST_CONFIG_BASE."thisIsAVeryBadSerialPort",
                    "quiet" => true,
                ),
                false,
                "",
                0,
                "",
                false,
                null,
            ),
            array( // #2 File can't be opened read/write
                array(
                    "location" => TEST_CONFIG_BASE."files/readOnlyFile",
                    "quiet" => false,
                ),
                false,
                "",
                false,
                false,
                false,
                "RuntimeException",
            ),
            array( // #3
                array(
                    "location" => sys_get_temp_dir()."/ttyTest".mt_rand(),
                    "quiet" => true,
                ),
                true,
                "5A5A5A010102030405060401020304C3",
                16,
                "5A5A5A010102030405060401020304C3",
                true,
                null,
            ),
        );
    }

    /**
    * Tests the serial class
    *
    * Not a real good test, but good enough for now.  It doesn't test that the
    * stty functions work properly.  It runs them, but they have no affect on the
    * file.
    *
    * @param array  $preload   The value to preload
    * @param bool   $create    Create the file (serial port)
    * @param string $write     The string to write
    * @param mixed  $expect    The expected return
    * @param string $buffer    What we expect to be written
    * @param string $available What the return from available should be
    * @param string $exception The exception to expect.  Null for none
    *
    * @return null
    *
    * @dataProvider dataSerial
    */
    public function testSerial(
        $preload, $create, $write, $expect, $buffer, $available, $exception
    ) {
        if (is_string($exception)) {
            $this->setExpectedException($exception);
        }
        if ($create) {
            touch($preload["location"]);
            $this->files[] = $preload["location"];
        }
        $sys = $this->getMock(
            "\\HUGnet\\System",
            array("now")
        );
        $sys->expects($this->any())
             ->method('now')
             ->will($this->returnValue(123456));

        // This sets up the server
        $obj = Serial::factory($sys, $preload);
        $this->assertSame($available, $obj->available(), "Available Wrong");
        // This writes to the connection
        $this->assertSame($expect, $obj->write($write), "The return is wrong");
        // This is ugly, but it gets us the file pointer so we can seek 0
        $fd = $this->readAttribute($obj, "_port");
        if (is_resource($fd)) {
            fseek($fd, 0);
        }
        // This reads what we have written
        $this->assertSame($buffer, $obj->read(), "The string is wrong");
        // This clean up everything
        unset($obj);
    }

}

?>
