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
 * @subpackage SuiteContainers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


/** This is a required class */
require_once CODE_BASE.'containers/ImagePointContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteContainers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ImagePointContainerTest extends PHPUnit_Framework_TestCase
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
                "dir" => realpath(TEST_CONFIG_BASE."files/plugins/"),
            ),
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->o = new ImagePointContainer(array());
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
    * data provider for testAutoColor
    *
    * @return array
    */
    public static function dataAutoFill()
    {
        return array(
            array(
                array(
                    "color" => "#000000",
                    "colorMin" => "#0000FF",
                    "colorMax" => "#FF0001",
                ),
                30,
                20,
                40,
                "#000000",
                "#FF00FF",
            ),
            array(
                array(
                    "color" => "#FFFFFF",
                    "colorMin" => "#0000FF",
                    "colorMax" => "#FF0001",
                ),
                30,
                20,
                40,
                "#000000",
                "#FF00FF",
            ),
            array(
                array(
                    "color" => "#000000",
                    "colorValueMin" => 20,
                    "colorValueMax" => 40,
                    "colorMin" => "#0000FF",
                    "colorMax" => "#FF0001",
                ),
                30,
                null,
                null,
                "#000000",
                "#FF00FF",
            ),
            array(
                array(
                    "fill" => "#000000",
                    "colorValueMin" => 20,
                    "colorValueMax" => 20,
                    "colorMin" => "#0000FF",
                    "colorMax" => "#FF0001",
                ),
                20,
                20,
                20,
                "#FFFFFF",
                "#FF0001",
            ),
            array(
                array(
                ),
                0,
                40,
                60,
                "#FFFFFF",
                "#0000FF",
            ),
            array(
                array(
                ),
                1000,
                40,
                60,
                "#FFFFFF",
                "#FF0001",
            ),
            array(
                array(
                    "color" => "#000000",
                ),
                47.44,
                60,
                80,
                "#FFFFFF",
                "#0000FF",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload  Data to preload
    * @param mixed  $value    The value to use
    * @param mixed  $valueMin The max value to use
    * @param mixed  $valueMax The min value to use
    * @param string $color    The expected color
    * @param string $fill     The expected fill color
    *
    * @return null
    *
    * @dataProvider dataAutoFill
    */
    public function testAutoFill(
        $preload, $value, $valueMin, $valueMax, $color, $fill
    ) {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $this->o->autoFill($value, $valueMin, $valueMax);
        $this->assertSame(
            $color,
            $this->o->color,
            "Color is wrong"
        );
        $this->assertSame(
            $fill,
            $this->o->fill,
            "Fill is wrong"
        );
    }
    /**
    * data provider for testAutoColor
    *
    * @return array
    */
    public static function dataAutoColor()
    {
        return array(
            array(
                array(
                    "colorMin" => "#0000FF",
                    "colorMax" => "#FF0001",
                ),
                30,
                20,
                40,
                "#FF00FF",
                "transparent",
            ),
            array(
                array(
                    "fill" => "#000000",
                    "colorMin" => "#0000FF",
                    "colorMax" => "#FF0001",
                ),
                30,
                20,
                40,
                "#FF00FF",
                "#000000",
            ),
            array(
                array(
                    "fill" => "#000000",
                    "colorValueMin" => 20,
                    "colorValueMax" => 40,
                    "colorMin" => "#0000FF",
                    "colorMax" => "#FF0001",
                ),
                30,
                null,
                null,
                "#FF00FF",
                "#000000",
            ),
            array(
                array(
                    "fill" => "#000000",
                    "colorValueMin" => 20,
                    "colorValueMax" => 20,
                    "colorMin" => "#0000FF",
                    "colorMax" => "#FF0001",
                ),
                30,
                null,
                null,
                "#FF0001",
                "#FFFFFF",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload  Data to preload
    * @param mixed  $value    The value to use
    * @param mixed  $valueMin The max value to use
    * @param mixed  $valueMax The min value to use
    * @param string $color    The expected color
    * @param string $fill     The expected fill color
    *
    * @return null
    *
    * @dataProvider dataAutoColor
    */
    public function testAutoColor(
        $preload, $value, $valueMin, $valueMax, $color, $fill
    ) {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $this->o->autoColor($value, $valueMin, $valueMax);
        $this->assertSame(
            $color,
            $this->o->color,
            "Color is wrong"
        );
        $this->assertSame(
            $fill,
            $this->o->fill,
            "Fill is wrong"
        );
    }


}
?>
