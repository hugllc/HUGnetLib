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


require_once dirname(__FILE__).'/../../containers/ImageContainer.php';

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
class ImageContainerTest extends PHPUnit_Framework_TestCase
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
                "dir" => realpath(dirname(__FILE__)."/../files/plugins/"),
            ),
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->o = new ImageContainer(array(), $this->cont);
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
        $this->o = null;
    }

    /**
    * data provider for testAddPoint
    *
    * @return array
    */
    public static function dataAddPoint()
    {
        return array(
            array(
                array(
                ),
                array(
                ),
                "object",
                array(
                ),
            ),
            array(
                array(
                ),
                new ImagePointContainer(),
                "object",
                array(
                ),
            ),
            array(
                array(
                ),
                "Bad Container",
                "bool",
                false,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload Data to preload
    * @param array  $point   The information about the point
    * @param string $type    The type of the return value
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataAddPoint
    */
    public function testAddPoint($preload, $point, $type, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $ret = $this->o->addPoint($point);
        if ($expect === false) {
            $this->assertSame($expect, $ret, "Return is wrong");
        } else {
            $points = $this->readAttribute($this->o, "points");
            $this->assertInternalType($type, $points[$ret], "Type is wrong");
            if (is_object($points[$ret])) {
                $out = $points[$ret]->toArray(false);
            } else {
                $out = $points[$ret];
            }
            $this->assertSame(
                $expect,
                $out,
                "output is wrong"
            );
        }
    }
    /**
    * data provider for testAddPoint
    *
    * @return array
    */
    public static function dataPoint()
    {
        return array(
            array(
                array(
                ),
                0,
                "null",
                null,
            ),
            array(
                array(
                    "points" => array(
                        new ImagePointContainer(),
                    ),
                ),
                0,
                "object",
                array(),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload Data to preload
    * @param array  $point   The information about the point
    * @param string $type    The type of the return value
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataPoint
    */
    public function testPoint($preload, $point, $type, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $ret = &$this->o->point($point);
        $points = &$this->readAttribute($this->o, "points");
        $this->assertSame(
            $points[$point],
            $ret,
            "Return is not the same as the object"
        );
        if (is_null($expect)) {
            $this->assertNull($ret, "Return is wrong");
        } else {
            $points = $this->readAttribute($this->o, "points");
            $this->assertInternalType($type, $ret, "Type is wrong");
            if (is_object($ret)) {
                $out = $ret->toArray(false);
            } else {
                $out = $ret;
            }
            $this->assertSame(
                $expect,
                $out,
                "return is wrong"
            );
            $this->assertSame(
                $point,
                $ret->id,
                "id is wrong"
            );
        }
    }

    /**
    * data provider for test2String
    *
    * @return array
    */
    public static function data2String()
    {
        return array(
            array(
                array(),
                false,
                "",
            ),
            array(
                array(
                    "points" => array(
                        new ImagePointContainer(),
                    ),
                ),
                false,
                "Array\n(\n)\n",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload Data to preload
    * @param bool   $default Whether to return the default or not
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider data2String
    */
    public function test2String($preload, $default, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $ret = $this->o->toString($default);
        $this->assertSame(
            $expect,
            $ret
        );
    }

}
?>
