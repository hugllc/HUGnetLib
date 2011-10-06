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
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** Get our classes */
require_once CODE_BASE.'plugins/images/SVGImagePlugin.php';
/** This is a required class */
require_once CODE_BASE.'containers/ImageContainer.php';
/** This is a required class */
require_once 'ImagePluginTestBase.php';

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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class SVGImagePluginTest extends ImagePluginTestBase
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
        $this->image = new ImageContainer();
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
            array("SVGImagePlugin"),
        );
    }

    /**
    * Data provider for testOutput
    *
    * @return array
    */
    public static function dataOutput()
    {
        return array(
            array(
                array(
                    "imageLoc" =>
                        realpath(TEST_CONFIG_BASE."files/images/pink.png"),
                    "height" => 100,
                    "width"  => 200,
                ),
                '<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/'
            .'1.1/DTD/svg11.dtd">
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://'
            .'www.w3.org/1999/xlink" width="200px" height="100px">
    <desc></desc>
    <rect id="" x="0" y="0" width="200px" height="100px" fill="#FFFFFF" '
            .'stroke="none" />
    <image xlink:href="'.realpath(TEST_CONFIG_BASE.'files/images/pink.png')
            .'" height="100" width="200" x="0" y="0" ></image>
</svg>

',
            ),
            array(
                array(
                    "imageLoc" =>
                        realpath(TEST_CONFIG_BASE."files/images/pink.png"),
                    "height" => 200,
                    "width"  => 100,
                ),
                '<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/'
            .'1.1/DTD/svg11.dtd">
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://'
            .'www.w3.org/1999/xlink" width="100px" height="200px">
    <desc></desc>
    <rect id="" x="0" y="0" width="100px" height="200px" fill="#FFFFFF" '
            .'stroke="none" />
    <image xlink:href="'.realpath(TEST_CONFIG_BASE.'files/images/pink.png')
            .'" height="200" width="100" x="0" y="0" ></image>
</svg>

',
            ),
            array(
                array(
                    "imageLoc" =>
                        realpath(TEST_CONFIG_BASE."files/images/pink.png"),
                    "height" => 1000,
                    "width"  => 2000,
                ),
                '<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/'
            .'1.1/DTD/svg11.dtd">
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://'
            .'www.w3.org/1999/xlink" width="2000px" height="1000px">
    <desc></desc>
    <rect id="" x="0" y="0" width="2000px" height="1000px" fill="#FFFFFF" '
            .'stroke="none" />
    <image xlink:href="'.realpath(TEST_CONFIG_BASE.'files/images/pink.png')
            .'" height="1000" width="2000" x="0" y="0" ></image>
</svg>

',
            ),
            array(
                array(
                    "imageLoc" => "bad image name",
                    "height" => 100,
                    "width"  => 200,
                ),
                '<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/'
            .'1.1/DTD/svg11.dtd">
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://'
            .'www.w3.org/1999/xlink" width="200px" height="100px">
    <desc></desc>
    <rect id="" x="0" y="0" width="200px" height="100px" fill="#FFFFFF" '
            .'stroke="none" />
</svg>

',
            ),
            array(
                array(
                    "imageLoc" => "bad image name",
                    "height" => 100,
                    "width"  => 200,
                    "points" => array(
                        array(
                            "x" => 5,
                            "y" => 6,
                            "text" => "Hello",
                            "link" => "asdf",
                            "linkTitle" => "Linky",
                        ),
                        array(
                            "x" => 29,
                            "y" => 16,
                            "text" => "",
                            "link" => "asdf",
                            "linkTitle" => "Bad Linky",
                        ),
                    ),
                ),
                '<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/'
            .'1.1/DTD/svg11.dtd">
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://'
            .'www.w3.org/1999/xlink" width="200px" height="100px">
    <desc></desc>
    <rect id="" x="0" y="0" width="200px" height="100px" fill="#FFFFFF" '
            .'stroke="none" />
    <a xlink:href="asdf" target="_top" xlink:title="Linky" >
        <rect id="point0box" x="5" y="6" width="0px" height="0px" fill="transparent"'
            .' stroke="none" />
        <text id="point0text" x="5" y="6"  font-size="9pt" fill="#000000"'
            .' stroke="none" >Hello</text>

    </a>
    <script>
        var Text=document.getElementById("point0text").getBBox();
        var Box =document.getElementById("point0box");
        Box.setAttributeNS(null, "x", Text.x-3);
        Box.setAttributeNS(null, "y", Text.y-3);
        Box.setAttributeNS(null, "width", Text.width+6);
        Box.setAttributeNS(null, "height", Text.height+6);
        Box.setAttributeNS(null, "fill", "transparent");
        //document.documentElement.appendChild(Box);
    </script>
</svg>

',
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param array $expect  The expected return
    *
    * @dataProvider dataOutput
    *
    * @return null
    */
    public function testOutput($preload, $expect)
    {
        $cont = new ImageContainer($preload);
        $obj = new SVGImagePlugin($cont);
        $ret = $obj->output();
        $this->assertSame($expect, $ret);
    }


}

?>
