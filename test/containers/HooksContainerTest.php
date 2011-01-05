<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Containers
 * @package    HUGnetLibTest
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../containers/HooksContainer.php';

/**
 * Test class for HooksContainer
 *
 * @category   Containers
 * @package    HUGnetLibTest
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HooksContainerTest extends PHPUnit_Framework_TestCase
{

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
    }


    /**
    * Data provider for testCreatePDO
    *
    * @return array
    */
    public static function dataFromArray()
    {
        return array(
            array(array(), array("hooks" => array())),
            array(
                array(
                    array("class" => "HooksContainer", "obj" => array()),
                ),
                array(
                    "hooks" => array(
                        array(
                            "obj" => new HooksContainer(array()),
                            "class" => "HooksContainer",
                        )
                    )
                )
            ),
        );
    }
    /**
    * Tests to make sure it builds arrays correctly
    *
    * @param string $preload The configuration to use
    * @param mixed  $expect  The expected value.  Set to FALSE or the class name
    *
    * @return null
    *
    * @dataProvider dataFromArray
    */
    public function testFromArray($preload, $expect)
    {
        $o = new HooksContainer($preload);
        foreach ($expect as $key => $value) {
            $this->assertEquals($value, $o->$key, "Bad Value in key $key");
        }
    }
    /**
    * Data provider for testGroup
    *
    * @return array
    */
    public static function dataRegisterHook()
    {
        return array(
            array(
                array(),
                "Hook",
                new HooksContainer(),
                array(
                    "hooks" => array(
                        "Hook" => array(
                            "obj" => array("hooks" => array()),
                            "class" => "HooksContainer",
                        ),
                    ),
                ),
                true,
            ),
            array(
                array(),
                "asdf",
                "This is not an object",
                array("hooks" => array()),
                false,
            ),
        );
    }
    /**
    * Tests the return of what groups are available.
    *
    * @param array  $preload The array to preload into the object
    * @param string $name    The hook to register
    * @param object $obj     The object to register
    * @param array  $expect  The expected data
    * @param book   $ret     The expected return
    *
    * @return null
    *
    * @dataProvider dataRegisterHook
    */
    public function testRegisterHook($preload, $name, $obj, $expect, $ret)
    {
        $o = new HooksContainer($preload);
        $this->assertSame($ret, $o->registerHook($name, $obj));
        $ret = $o->toArray();
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testGroup
    *
    * @return array
    */
    public static function dataHook()
    {
        return array(
            array(array(), "Invalid", "", array("hooks" => array())),
            // Wrong class.  Should return the base Hooks Container
            array(
                array(
                    "myHook" => array(
                        "obj" => new HooksContainer(array()),
                        "class" => "HooksContainer",
                    )
                ),
                "myHook",
                "PacketContainer",
                array(
                    "hooks" => array(
                        "myHook" => array(
                            "obj" => array("hooks" => array()),
                            "class" => "HooksContainer",
                        )
                    )
                ),
            ),
            // Right class.  Will return the myHook HooksContainer
            array(
                array(
                    "myHook" => array(
                        "obj" => new HooksContainer(array()),
                        "class" => "HooksContainer",
                    )
                ),
                "myHook",
                "HooksContainer",
                array(
                    "hooks" => array(
                    )
                ),
            ),
        );
    }
    /**
    * Tests the return of the object
    *
    * @param array  $preload   The array to preload into the object
    * @param string $name      The hook to register
    * @param string $interface The interface to ask for
    * @param array  $expect    The expected data
    *
    * @return null
    *
    * @dataProvider dataHook
    */
    public function testHook($preload, $name, $interface, $expect)
    {
        $o = new HooksContainer($preload);
        $ret = &$o->hook($name, $interface);
        $this->assertInternalType("object", $ret);
        $this->assertSame($expect, $ret->toArray());
    }
    /**
    * data provider for testConstructor
    *
    * @return array
    *
    * @static
    */
    public static function dataCall()
    {
        return array(
            array(
                array(),
                "DummyFct",
                HUGnetClass::VPRINT_VERBOSE,
                "(HooksContainer) No hook defined\n",
            ),
            array(
                array(),
                "DummyFct",
                HUGnetClass::VPRINT_VERBOSE - 1,
                "",
            ),
        );
    }
    /**
    * test
    *
    * @param array  $preload The array to preload into the object
    * @param string $fct     The function to call
    * @param int    $verbose The current value
    * @param int    $expect  The expected stuff printed
    *
    * @return null
    *
    * @dataProvider dataCall
    */
    public function testCall($preload, $fct, $verbose, $expect)
    {
        $o = new HooksContainer($preload);
        $o->verbose($verbose);
        ob_start();
        $o->$fct();
        $ret = ob_get_contents();
        ob_end_clean();
        $this->assertSame($expect, $ret);
    }
    /**
    * Check to make sure that two instances of the class from singleton are
    * identical
    *
    * @return null
    */
    public function testSingleton()
    {
        $this->assertSame(
            HooksContainer::singleton(),
            HooksContainer::singleton()
        );
    }

}

?>
