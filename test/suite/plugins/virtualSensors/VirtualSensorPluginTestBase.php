<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

// Need to make sure this file is not added to the code coverage
PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);
require_once TEST_BASE."plugins/sensors/DeviceSensorPluginTestBase.php";
require_once CODE_BASE.'containers/DeviceContainer.php';
/**
 * Test class for device drivers
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class VirtualSensorPluginTestBase extends DeviceSensorPluginTestBase
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
    /*
    public function testDefaultExtraTextSize($class)
    {
        $size = 26;
        $d = new DeviceContainer();
        $obj = new $class($data, $d);
        foreach ($obj->extraText as $key => $val) {
            $this->assertTrue(
                (strlen($val) < $size),
                "extraText[$key] must be less than $size characters"
            );
        }
    }
    */
}

?>
