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
namespace HUGnet\network\physical;
/** This is a required class */
require_once CODE_BASE.'network/physical/SocketNull.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySocket.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
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
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class SocketNullTest extends \PHPUnit_Framework_TestCase
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
    * This tests the exception when read is called on a null socket
    *
    * @return null
    */
    public function testReadException()
    {
        $this->setExpectedException("RuntimeException");
        $config = array();
        $sys = new \HUGnet\DummySystem("System");
        $socket = SocketNull::factory($sys, $config);
        $socket->read();
    }
    /**
    * This tests the exception when read is called on a null socket
    *
    * @return null
    */
    public function testWriteException()
    {
        $this->setExpectedException("RuntimeException");
        $config = array();
        $sys = new \HUGnet\DummySystem("System");
        $socket = SocketNull::factory($sys, $config);
        $socket->write("Hello");
    }
    /**
    * This tests the exception when read is called on a null socket
    *
    * @return null
    */
    public function testAvailable()
    {
        $config = array();
        $sys = new \HUGnet\DummySystem("System");
        $socket = SocketNull::factory($sys, $config);
        $this->assertFalse($socket->available());
    }

}
?>
