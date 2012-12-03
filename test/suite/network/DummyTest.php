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
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\network;
/** This is a required class */
require_once CODE_BASE.'network/Dummy.php';
/** This is a required class */
require_once CODE_BASE.'network/packets/Packet.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTransport.php';
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DummyTest extends \PHPUnit_Framework_TestCase
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
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataDevice()
    {
        return array(
            array(
                array(),
                array(),
                ""
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array $mock   The string to give to the class
    * @param array $config The configuration to send the class
    * @param array $expect The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataDevice
    */
    public function testDevice($mock, $config, $expect)
    {
        $system = new \HUGnet\DummySystem();
        $system->resetMock($mock);
        $application = &Dummy::factory($system);
        $dev = $application->device($config);
        $this->assertEquals("HUGnet\\network\\Dummy", get_class($dev));
        //$this->assertEquals($expect, $transport->retrieve(), "Calls wrong");
    }

    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataSend()
    {
        return array(
            array(
                array(),
                "trim",
                array(),
                false,
            ),
            array(
                array(),
                null,
                array(),
                packets\Packet::factory(array()),
            ),
            array(
                array("block" => true),
                "trim",
                array(),
                packets\Packet::factory(array()),
            ),
            array(
                array("block" => 1),
                "trim",
                array(),
                packets\Packet::factory(array()),
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param array  $config   The configuration to send the class
    * @param string $callback The function to call
    * @param array  $send     Array of "function" => Packets to send out
    * @param array  $expect   The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataSend()
    */
    public function testSend($config, $callback, $send, $expect)
    {
        $system = new \HUGnet\DummySystem();
        $system->resetMock($mock);
        $application = &Dummy::factory($system);
        $ret = $application->send($send, $callback, $config);
        $this->assertEquals($expect, $ret);
    }
    /**
    * Tests to make sure that the from address is okay
    *
    * @return null
    */
    public function testMatch()
    {
        $system = new \HUGnet\DummySystem();
        $application = &Dummy::factory($system);
        $this->assertFalse($application->match(null));
    }
    /**
    * Tests to make sure that the from address is okay
    *
    * @return null
    */
    public function testUnsolicited()
    {
        $system = new \HUGnet\DummySystem();
        $application = &Dummy::factory($system);
        $this->assertFalse($application->unsolicited(null));
    }
    /**
    * Tests to make sure that the from address is okay
    *
    * @return null
    */
    public function testMonitor()
    {
        $system = new \HUGnet\DummySystem();
        $application = &Dummy::factory($system);
        $this->assertFalse($application->monitor(null));
    }
    /**
    * Tests to make sure that the from address is okay
    *
    * @return null
    */
    public function testGetID()
    {
        $system = new \HUGnet\DummySystem();
        $application = &Dummy::factory($system);
        $this->assertSame(0, $application->getID());
    }
    /**
    * Tests to make sure that the from address is okay
    *
    * @return null
    */
    public function testMain()
    {
        $system = new \HUGnet\DummySystem();
        $application = &Dummy::factory($system);
        $this->assertTrue(method_exists($application, "main"));
        /* No asserts.  Main just has to exist */
    }

}
?>
