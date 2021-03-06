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
namespace HUGnet\base;
/** This is a required class */
require_once CODE_BASE.'base/BaseChan.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';

/**
 * Test class for BaseChan
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.13.0
 */
class BaseChanTest extends \PHPUnit_Framework_TestCase
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
    * Data provider for testCreate
    *
    * @return array
    */
    public static function data2Array()
    {
        return array(
            array(
                array(
                ),
                array(
                    "storageUnit" => "&#176;C",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Temperature",
                    "units" => "asdf",
                    "label" => "Empty",
                    "decimals" => 5,
                    "dataType" => "raw",
                ),
                array(
                    "storageUnit" => "asdf",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Generic",
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 5,
                    "dataType" => "diff",
                ),
                true,
                array(
                    "storageUnit" => "&#176;C",
                    "storageType" => "raw",
                    "maxDecimals" => 5,
                    "unitType" => "Temperature",
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 5,
                    "dataType" => "diff",
                ),
            ),
            array(
                array(
                ),
                array(
                    "storageUnit" => "&#176;C",
                    "storageType" => "raw",
                    "unitType" => "Temperature",
                    "units" => "asdf",
                    "label" => "Empty",
                    "decimals" => 5,
                    "dataType" => "raw",
                ),
                array(
                    "storageUnit" => "asdf",
                    "storageType" => "raw",
                    "unitType" => "Generic",
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 8,
                    "dataType" => "diff",
                ),
                false,
                array(
                    "units" => "&#176;F",
                    "label" => "This is a label",
                    "decimals" => 8,
                    "dataType" => "diff",
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $mocks   The configuration to use
    * @param mixed $driver  The driver data to use
    * @param mixed $data    The data to use
    * @param bool  $default Whether to rerturn the default items
    * @param mixed $expect  The value we expect back
    *
    * @return null
    *
    * @dataProvider data2Array
    */
    public function test2Array(
        $mocks, $driver, $data, $default, $expect
    ) {
        $dev = new \HUGnet\DummyBase("Device");
        $dev->resetMock($mocks);
        $obj = TestChan::factory($dev, $driver, $data);
        $ret = $obj->toArray($default);
        $this->assertEquals($expect, $ret);
        unset($obj);
    }
    /**
     * This tests the device function
     *
     * @return null
     */
    public function testdevice()
    {
        $dev = new \HUGnet\DummyBase("Device");
        $dev->resetMock($mocks);
        $obj = TestChan::factory($dev, $driver, $data);
        $this->assertSame($dev, $obj->device());
        unset($obj);
    }
}

/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.13.0
 */
class TestChan extends \HUGnet\base\BaseChan
{
    /** @var array The configuration that we are going to use */
    protected $setable = array("units", "label", "decimals", "dataType");
}


?>
