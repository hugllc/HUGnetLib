<?php
/**
 * This runs all of the tests associated with HUGnetLib.
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
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** This is what we are testing */
require_once CODE_BASE.'base/PeriodicPluginBase.php';
/** This is a required class */
require_once CODE_BASE.'processes/PeriodicPlugins.php';
/** This is a required class */
require_once CODE_BASE.'containers/DeviceContainer.php';

/**
 * Test class for PeriodicPluginBase
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PeriodicPluginBaseTest extends PHPUnit_Framework_TestCase
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
        $this->config = ConfigContainer::singleton();
        $this->config->forceConfig(
            array(
                "script_gateway" => 13,
                "pluginData" => array(
                    "PeriodicPluginBaseTestStub" => array(
                        "enable" => true,
                    ),
                ),
            )
        );
        $this->device = array(
        );
        $this->p = new PeriodicPlugins($data, $this->device);
        $this->o = new PeriodicPluginBaseTestStub($stub, $this->p);
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
        unset($this->o);
    }
    /**
    * Data provider for testConstructor
    *
    * @return array
    */
    public static function dataConstructor()
    {
        return array(
            array(
                array("verbose" => 12),
                array("verbose" => 12),
                array("enable" => true, "this" => "that"),
            ),
            array(
                array(),
                array("verbose" => 0),
                array("enable" => true, "this" => "that"),
            ),
        );
    }
    /**
    * test
    *
    * @param array $config    The configuration to use
    * @param array $expect    Associative array of properties to check
    * @param array $expectCfg What the configuration should look like
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($config, $expect, $expectCfg)
    {
        $obj = new PeriodicPluginBaseTestStub($config, $this->p);
        foreach ($expect as $key => $value) {
            $this->assertAttributeSame($value, $key, $obj, "$key wrong");
        }
        $this->assertSame(
            $expectCfg,
            $this->config->pluginData["PeriodicPluginBaseTestStub"],
            "Config Wrong"
        );
    }
    /**
    * Data provider for testReady
    *
    * @return array
    */
    public static function dataReady()
    {
        return array(
            array(time(), false),
            array(time() - 86400, true),
            array(0, true),
        );
    }
    /**
    * test
    *
    * @param array $last   What to set 'last' to
    * @param bool  $expect The return value
    *
    * @return null
    *
    * @dataProvider dataReady
    */
    public function testReady($last, $expect)
    {
        $this->o->setLast($last);
        $this->assertSame($expect, $this->o->ready());
    }
}

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PeriodicPluginBaseTestStub extends PeriodicPluginBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Test Stub",
        "Type" => "periodic",
        "Class" => "PeriodicPluginBaseTestStub",
    );
    /** @var This is our configuration */
    protected $defConf = array(
        "enable" => true,
        "this" => "that",
    );
    /**
    * This function does the stuff in the class.
    *
    * @return bool True if ready to return, false otherwise
    */
    public function main()
    {
    }
    /**
    * This function does the stuff in the class.
    *
    * @param int $value The value to set last to
    *
    * @return none
    */
    public function setLast($value)
    {
        $this->last = (int)$value;
    }

}
?>
