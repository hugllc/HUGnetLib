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
 * @category   Test
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


require_once CODE_BASE.'containers/ConfigContainer.php';
require_once CODE_BASE.'base/OutputFilterBase.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
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
class OutputFilterBaseTest extends PHPUnit_Framework_TestCase
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
        $config = array(
            "plugins" => array(
                "dir" => realpath(
                    TEST_CONFIG_BASE."files/plugins/"
                ),
            ),
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
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
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataConstructor()
    {
        return array(
            array(
                array(
                    "setup" => "array",
                ),
                array(
                    array("test" => "hello"),
                    array("test" => "there"),
                    array("test" => "world"),
                ),
                array(
                    array("test" => "world"),
                    array("test" => "there"),
                    array("test" => "hello"),
                ),
                array(
                    "setup" => "array",
                ),
            ),
            array(
                null,
                array(
                    array("test" => "hello"),
                    array("test" => "there"),
                    array("test" => "world"),
                ),
                array(
                    array("test" => "world"),
                    array("test" => "there"),
                    array("test" => "hello"),
                ),
                array(
                ),
            ),
        );
    }


    /**
    * test the set routine when an extra class exists
    *
    * @param array $setup       The setup to use
    * @param array $data        The data to use
    * @param array $expect      The expected return
    * @param mixed $setupExpect The expected setup value
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($setup, $data, $expect, $setupExpect)
    {
        $obj = new OutputFilterBaseTestClass($setup, $data);
        // If I change this, it should also change in the object.
        // This tests if it is indeed a reference
        $data = $expect;
        $this->assertAttributeSame(
            $expect,
            "data",
            $obj,
            "Data is wrong"
        );
        $this->assertAttributeSame(
            $setupExpect,
            "setup",
            $obj,
            "Setup is wrong"
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testName()
    {
        $obj = new OutputFilterBaseTestClass($setup, $data);
        $this->assertSame("Test", $obj->name());
    }


}
/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Endpoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2011 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class OutputFilterBaseTestClass extends OutputFilterBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Test",
        "Type" => "outputFilter2",
        "Class" => "This is a bad class",
        "Flags" => array("DEFAULT"),
    );
    /**
    * Does the actual conversion
    *
    * @param mixed $field The field to execute this on
    *
    * @return bool True on success, false on failure
    */
    public function execute($field)
    {
        return true;
    }

}
?>
