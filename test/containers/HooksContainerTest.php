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
        $this->assertAttributeSame($expect, "data", $o);
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
            ),
        );
    }
    /**
    * Tests the return of what groups are available.
    *
    * @param array  $preload The array to preload into the object
    * @param string $name   The hook to register
    * @param object $obj    The object to register
    * @param array  $expect The expected data
    *
    * @return null
    *
    * @dataProvider dataRegisterHook
    */
    public function testRegisterHook($preload, $name, $obj, $expect)
    {
        $o = new HooksContainer($preload);
        $o->registerHook($name, $obj);
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
            array(array(), "Invalid", null),
        );
    }
    /**
    * Tests the return of the object
    *
    * @param array  $preload The array to preload into the object
    * @param string $name    The hook to register
    * @param array  $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataHook
    */
    public function testHook($preload, $name, $expect)
    {
        $o = new HooksContainer($preload);
        $ret = &$o->hook($name);
        $this->assertSame($expect, $ret);
    }

}

?>
