<?php
/**
 * Tests the filter class
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
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the base of the test */
require_once TEST_BASE.'plugins/PluginTestBase.php';
/**
 * Test class for device drivers
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class ImagePluginTestBase extends PluginTestBase
{
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class The class to use
    *
    * @return null
    *
    * @dataProvider dataRegisterPlugin
    */
    public function testRegisterPluginDevices($class)
    {
        $var = $class::$registerPlugin;
        $this->assertInternalType(
            "array",
            $var["Flags"],
            "Flags is not an array"
        );
        foreach ($var["Flags"] as $key => $units) {
            $this->assertInternalType(
                "string",
                $units,
                "hardware $key is not a string"
            );
            $this->assertFalse(
                empty($units),
                "Units $key can't be empty"
            );
        }
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $class The class to use
    *
    * @return null
    *
    * @dataProvider dataRegisterPlugin
    */
    public function testParent($class)
    {
        $this->assertTrue(is_subclass_of($class, "ImagePluginBase"));
    }
}

?>
