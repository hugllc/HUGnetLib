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
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** This is a required class */
require_once CODE_BASE.'containers/ConfigContainer.php';
/** This is a required class */
require_once CODE_BASE.'base/OutputPluginBase.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'files/containers/TestOutputContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class OutputPluginBaseTest extends PHPUnit_Framework_TestCase
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
            "PluginDir" => realpath(
                TEST_CONFIG_BASE."files/plugins/"
            ),
        );
        $this->output = new TestOutputContainer();
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->o = new OutputPluginBaseTestClass($this->output);
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
                array("p1" => "2"),
                array(
                    "params" => array("p1" => "2", "p2" => "there"),
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $data   The data to use
    * @param array $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($data, $expect)
    {
        $obj = new OutputPluginBaseTestClass($data);
        foreach ((array)$expect as $key => $value) {
            $this->assertAttributeSame($value, $key, $obj);
        }
    }
    /**
     * Data provider for testConvertTo
     *
     * @return array
     */
    public static function dataBody()
    {
        return array(
            array(
                array(),
                array("1" => "head"),
                array("1" => "2"),
                "Array\n(\n    [1] => 2\n)\n",
            ),
        );
    }
    /**
    * test Row()
    *
    * @param array  $preload the stuff to preload into the plugin
    * @param array  $header  The output header to use
    * @param array  $output  The output to load into the plugin
    * @param string $expect  The value to expect
    *
    * @return null
    *
    * @dataProvider dataBody
    */
    public function testBody($preload, $header, $output, $expect)
    {
        $this->o = new OutputPluginBaseTestClass($preload, $header);
        $this->o->row($output);
        $body = $this->o->body();
        $this->assertSame($expect, $body);
    }
    /**
     * Data provider for test2String
     *
     * @return array
     */
    public static function data2String()
    {
        return array(
            array(
                array(),
                array("1" => "head"),
                array("1" => "2"),
                "preArray\n(\n    [1] => 2\n)\npost",
            ),
        );
    }
    /**
    * test Row()
    *
    * @param array  $preload the stuff to preload into the plugin
    * @param array  $header  The output header to use
    * @param array  $output  The output to load into the plugin
    * @param string $expect  The value to expect
    *
    * @return null
    *
    * @dataProvider data2String
    */
    public function test2String($preload, $header, $output, $expect)
    {
        $this->o = new OutputPluginBaseTestClass($preload, $header);
        $this->o->row($output);
        $body = $this->o->toString();
        $this->assertSame($expect, $body);
    }

    /**
    * test Pre()
    *
    * @return null
    */
    public function testPre()
    {
        $this->assertSame("pre", $this->o->pre());
    }

    /**
    * test Row()
    *
    * @return null
    */
    public function testPost()
    {
        $this->assertSame("post", $this->o->post());
    }

}
/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Libraries
* @package    HUGnetLibTest
* @subpackage SuiteBase
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class OutputPluginBaseTestClass extends OutputPluginBase
{
    /** @var  These are the graph colors that will be used, in order */
    public $params = array(
        "p1" => "hello",
        "p2" => "there",
    );
    /**
    * Returns the object as a string
    *
    * @param array $array The array of header information.
    *
    * @return string
    */
    public function row($array = array())
    {
        $this->text .= print_r($array, true);
    }

    /**
    * Returns the object as a string
    *
    * @param array $array The array of header information.
    *
    * @return string
    */
    public function header($array = array())
    {
        $this->text .= print_r($array, true);
    }
}
?>
