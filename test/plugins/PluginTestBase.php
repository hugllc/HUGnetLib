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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

// Need to make sure this file is not added to the code coverage
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class PluginTestBase extends PHPUnit_Framework_TestCase
{

    /**
    * Data provider for testRegisterPlugin
    *
    * @return array
    */
    abstract public static function dataRegisterPlugin();
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class The name of the class to test
    *
    * @return null
    *
    * @dataProvider dataRegisterPlugin
    */
    public function testRegisterPlugin($class)
    {
        $var = eval("return $class::\$registerPlugin;");
        $this->assertType(
            "array",
            $var,
            "registerPlugins is not an array"
        );
        $this->assertFalse(
            empty($var["Name"]),
            "Name is empty"
        );
        $this->assertType(
            "string",
            $var["Type"],
            "Type is not a string"
        );
        $this->assertFalse(
            empty($var["Type"]),
            "Type is empty"
        );
        $this->assertSame($class, $var["Class"], "Class must equal $class");

    }

}

?>
