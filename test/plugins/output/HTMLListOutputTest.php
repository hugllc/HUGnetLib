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
 * @subpackage Default
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** Get our classes */
require_once dirname(__FILE__).'/../../../plugins/output/HTMLListOutput.php';
require_once dirname(__FILE__).'/OutputPluginTestBase.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Default
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HTMLListOutputTest extends OutputPluginTestBase
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
            array("HTMLListOutput"),
        );
    }

    /**
    * Data provider for test2string
    *
    * @return array
    */
    public static function data2String()
    {
        return array(
            array(
                array(),
                true,
                "    <tr>\n    </tr>\n",
            ),
            array(
                array("a" => 1, "b" => 2),
                true,
                "    <tr>\n        <td>1</td>\n        <td>2</td>\n    </tr>\n",
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param mixed $default Load default values
    * @param array $expect  The expected return
    *
    * @dataProvider data2String
    *
    * @return null
    */
    public function test2String($preload, $default, $expect)
    {
        $o = new HTMLListOutput($preload);
        $o->toString($default);
        $this->assertSame($expect, $o->body());
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
                array("a" => "First"),
                "    <tr>\n    </tr>\n",
            ),
            array(
                array("a" => 1, "b" => 2),
                array(),
                "    <tr>\n        <th>a</th>\n        <th>b</th>\n    </tr>\n",
            ),
            array(
                array("a" => 1, "b" => 2),
                array("a" => "q"),
                "    <tr>\n        <th>q</th>\n        <th>b</th>\n    </tr>\n",
            ),
            array(
                array("a" => 1, "b" => 2),
                array("a" => "0"),
                "    <tr>\n        <th>0</th>\n        <th>b</th>\n    </tr>\n",
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param mixed $array   The array to feed the header
    * @param array $expect  The expected return
    *
    * @dataProvider dataHeader
    *
    * @return null
    */
    public function testHeader($preload, $array, $expect)
    {
        $o = new HTMLListOutput($preload);
        $o->header($array);
        $this->assertSame($expect, $o->body());
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
                "<table>\n",
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
        $o = new HTMLListOutput($preload);
        $this->assertSame($expect, $o->pre());
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
        $o = new HTMLListOutput($preload);
        $this->assertSame($expect, $o->post());
    }

}

?>
