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
namespace HUGnet;
/** This is a required class */
require_once CODE_BASE.'util/Util.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
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
class UtilTest extends \PHPUnit_Framework_TestCase
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
    * Data provider for testThrowException
    *
    * @return array
    */
    public static function dataFindClass()
    {
        return array(
            array("Error", "system", false, "\HUGnet", "\HUGnet\Error", null),
            array(
                "Driver", "devices", true, "\\HUGnet\\devices",
                "\\HUGnet\\devices\\Driver", null
            ),
            array(
                "ThisIsAVeryBadClass", "NoWhere", false, "\HUGnet", null,
                "InvalidArgumentException"
            ),
            array("ThisIsAVeryBadClass", "NoWhere", true, "\HUGnet", null, null),
        );
    }
    /**
    * This tests the object creation
    *
    * @param string $class     The class to find
    * @param string $dir       The directory to look in
    * @param bool   $quiet     If true no exceptions are thrown
    * @param string $namespace The namespace to try
    * @param string $expect    The class as we found it
    * @param string $exception If it is a string we are expecting an exception
    *
    * @return null
    *
    * @dataProvider dataFindClass
    */
    public function testFindClass(
        $class, $dir, $quiet, $namespace, $expect, $exception
    ) {
        if (is_string($exception)) {
            $this->setExpectedException($exception);
        }
        $this->assertSame(
            $expect, Util::findClass($class, $dir, $quiet, $namespace)
        );
    }

    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataCrc8()
    {
        return array(
            array(
                "55000ABC0000200401020304",
                0x82,
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param string $preload The string to give to the class
    * @param array  $expect  The info to expect returned
    *
    * @return null
    *
    * @dataProvider dataCrc8()
    */
    public function testCrc8($preload, $expect)
    {
        $this->assertSame($expect, Util::crc8($preload));
    }

}
?>
