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
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\network\physical;
/** This is a required class */
require_once CODE_BASE.'network/physical/Local.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySocket.php';
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class LocalTest extends \PHPUnit_Framework_TestCase
{
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
    * data provider for testLocal
    *
    * @return array
    */
    public static function dataLocal()
    {
        return array(
            array( // #3
                array(
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
    * @dataProvider dataLocal
    */
    public function testLocal(
        $preload, $create, $write, $expect, $buffer, $available, $exception
    ) {
        if (is_string($exception)) {
            $this->setExpectedException($exception);
        }
        // This sets up the server
        $obj = Local::factory($preload);
        $this->assertSame($available, $obj->available(), "Available Wrong");
        // This writes to the connection
        $this->assertSame($expect, $obj->write($write), "The return is wrong");
        // This reads what we have written
        $this->assertSame($buffer, $obj->read(), "The string is wrong");
        // This clean up everything
        unset($obj);
    }

}
?>
