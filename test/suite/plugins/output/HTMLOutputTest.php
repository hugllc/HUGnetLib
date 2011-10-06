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
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** Get our classes */
require_once CODE_BASE.'plugins/output/HTMLOutput.php';
/** This is a required class */
require_once 'OutputPluginTestBase.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HTMLOutputTest extends OutputPluginTestBase
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
        unset($this->o);
    }

    /**
    * Data provider for testRegisterPlugin
    *
    * @return array
    */
    public static function dataRegisterPlugin()
    {
        return array(
            array("HTMLOutput"),
        );
    }

    /**
    * Data provider for testRow
    *
    * @return array
    */
    public static function dataRow()
    {
        return array(
            array(
                array(),
                array(),
                array(array()),
                "",
            ),
            array(
                array(
                    "rowStyle" => array('class="row3"', 'class="row4"'),
                    "dataStyle" => array(
                        array("a" => 'class="hello1"'),
                        array("DEFAULT" => 'class="hello2"'),
                        "c" => 'class="there"',
                        "DEFAULT" => 'class="world"',
                    ),
                    "headerStyle" => array(
                        "b" => 'class="where"',
                        "DEFAULT" => 'class="right"',
                    ),
                ),
                array("a" => 5, "b" => 6, "c" => 1, "d" => 8, "e" => 9),
                array("a" => 1, "b" => 2, "c" => 3, "d" => 4, "e" => 5),
                "    <tr class=\"row3\">
        <th class=\"right\">5</th>
        <td class=\"hello1\">1</td>
    </tr>
    <tr class=\"row4\">
        <th class=\"where\">6</th>
        <td class=\"hello2\">2</td>
    </tr>
    <tr class=\"row3\">
        <th class=\"right\">1</th>
        <td class=\"there\">3</td>
    </tr>
    <tr class=\"row4\">
        <th class=\"right\">8</th>
        <td class=\"hello2\">4</td>
    </tr>
    <tr class=\"row3\">
        <th class=\"right\">9</th>
        <td class=\"world\">5</td>
    </tr>
"
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $params  The parameters to use
    * @param array $preload The array to preload into the class
    * @param mixed $row     The row to use
    * @param array $expect  The expected return
    *
    * @dataProvider dataRow
    *
    * @return null
    */
    public function testRow($params, $preload, $row, $expect)
    {
        $obj = new HTMLOutput($params, $preload);
        $obj->row($row);
        $this->assertSame($expect, $obj->body());
    }

    /**
    * Data provider for testHeader
    *
    * @return array
    */
    public static function dataHeader()
    {
        return array(
            array(
                array(),
                array(),
                array("a" => "q", "b" => "b"),
                "",
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $params  The parameters to use
    * @param array $preload The array to preload into the class
    * @param mixed $array   The array to feed the header
    * @param array $expect  The expected return
    *
    * @dataProvider dataHeader
    *
    * @return null
    */
    public function testHeader($params, $preload, $array, $expect)
    {
        $obj = new HTMLOutput($params, $preload);
        $obj->header($array);
        $this->assertSame($expect, $obj->body());
    }

    /**
    * Data provider for testPre
    *
    * @return array
    */
    public static function dataPre()
    {
        return array(
            array(
                array(),
                "<table >\n",
            ),
            array(
                array("tableStyle" => 'class="hello"'),
                "<table class=\"hello\">\n",
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param array $expect  The expected return
    *
    * @dataProvider dataPre
    *
    * @return null
    */
    public function testPre($preload, $expect)
    {
        $obj = new HTMLOutput($preload);
        $this->assertSame($expect, $obj->pre());
    }

    /**
    * Data provider for testPost
    *
    * @return array
    */
    public static function dataPost()
    {
        return array(
            array(
                array(),
                "</table>\n",
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param array $expect  The expected return
    *
    * @dataProvider dataPost
    *
    * @return null
    */
    public function testPost($preload, $expect)
    {
        $obj = new HTMLOutput($preload);
        $this->assertSame($expect, $obj->post());
    }

}

?>
