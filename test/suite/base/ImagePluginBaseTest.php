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
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** This is a required class */
require_once CODE_BASE.'containers/ConfigContainer.php';
/** This is a required class */
require_once CODE_BASE.'base/ImagePluginBase.php';
/** This is a required class */
require_once CODE_BASE.'containers/ImageContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ImagePluginBaseTest extends PHPUnit_Framework_TestCase
{
    /** @var array Array of file names to delete */
    private $_files = array();
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
        $this->image = new ImageContainer();
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->o = new ImagePluginBaseTestClass($this->image);
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
        // This removes all created files
        foreach ((array)$this->_files as $key => $file) {
            unlink($file);
            unset($this->_files[$key]);
        }
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
                array(),
                realpath(
                    CODE_BASE."contrib/fonts/bitstream-vera/Vera.ttf"
                ),
            ),
            array(
                array("p1" => "2"),
                array("fontFile" => "test"),
                "test",
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The data to create the image with
    * @param array  $data    The data to use
    * @param string $font    The font to expect
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $data, $font)
    {
        $img = new ImageContainer($preload);
        $obj = new ImagePluginBaseTestClass($img, $data);
        $this->assertAttributeSame($img, "image", $obj, "Image wrong");
        $this->assertAttributeSame($font, "_fontFile", $obj, "Font wrong");
    }
    /**
     * Data provider for testConvertTo
     *
     * @return array
     */
    public static function dataOutput()
    {
        return array(
            array(
                array(),
                array(),
                "Please replace this function",
            ),
        );
    }
    /**
    * test Row()
    *
    * @param array  $preload the stuff to preload into the plugin
    * @param array  $data    The data to use
    * @param string $expect  The value to expect
    *
    * @return null
    *
    * @dataProvider dataOutput
    */
    public function testOutput($preload, $data, $expect)
    {
        $img = new ImageContainer($preload);
        $obj = new ImagePluginBaseTestClass($img, $data);
        $body = $obj->output();
        $this->assertSame($expect, $body);
    }
    /**
    * Data provider for testOutputTest
    *
    * @return array
    */
    public static function dataOutputTest()
    {
        return array(
            array( // #0
                array("height" => 100, "width" => 100),
                array(),
                realpath(
                    TEST_CONFIG_BASE."files/images/ImagePluginsBaseTestBlank.png"
                ),
                0,
            ),
            array( // #1
                array(
                    "height" => 640,
                    "width" => 640,
                    "imageLoc" => realpath(
                        TEST_CONFIG_BASE."files/images/pink.png"
                    ),
                ),
                array(),
                realpath(
                    TEST_CONFIG_BASE."files/images/pinkSq.png"
                ),
                0,
            ),
            array( // #2
                array(
                    "height" => 100,
                    "width" => 150,
                    "imageLoc" => realpath(
                        TEST_CONFIG_BASE."files/images/pink.png"
                    ),
                    "points" => array(
                        array(
                            "x" => 10,
                            "y" => 50,
                            "text" => "Hello",
                            "color" => "#000000",
                            "fill" => "#FFFFFF",
                        ),
                        array(
                            "x" => 10,
                            "y" => 75,
                            "text" => "Where are we?",
                            "color" => "#0000FF",
                        ),
                    ),
                ),
                array(),
                TEST_CONFIG_BASE."files/images/ImagePluginsBaseTestText1.png",
                600,
            ),
            array( // #3
                array(
                    "height" => 100,
                    "width" => 150,
                    "imageLoc" => realpath(
                        TEST_CONFIG_BASE."files/images/pink.png"
                    ),
                    "points" => array(
                        array(
                            "x" => 10,
                            "y" => 50,
                            "text" => "Hello",
                            "color" => "#000000",
                            "fill" => "#FFFFFF",
                            "outline" => "#0000FF",
                        ),
                        array(
                            "x" => 10,
                            "y" => 75,
                            "text" => "Try Again?",
                            "color" => "#0000FF",
                        ),
                    ),
                ),
                array(
                    "fontFile" => CODE_BASE."contrib/fonts/bitstream-vera/Vera.ttf"
                ),
                TEST_CONFIG_BASE."files/images/ImagePluginsBaseTestText2.png",
                400,
            ),
        );
    }
    /**
    * test Row()
    *
    * The extra gd stuff is because GD imbeds version information and other things
    * into the files, so this normalizes the file to whatever version of GD is being
    * used to create them.
    *
    * @param array  $preload the stuff to preload into the plugin
    * @param array  $data    The data to use
    * @param string $expect  The value to expect
    * @param int    $allowed The allowed number of different pixels
    *
    * @return null
    *
    * @dataProvider dataOutputTest
    */
    public function testOutputTest($preload, $data, $expect, $allowed)
    {
        $img = new ImageContainer($preload);
        $obj = new ImagePluginBaseTestClass($img, $data);
        $ret = $obj->outputTest();
        $image = imagecreatefrompng($expect);
        $x = imagesx($image);
        $y = imagesy($image);
        $retx = imagesx($ret);
        $rety = imagesy($ret);
        $count = 0;
        // Check to see if all of the pixels are the same color
        for ($i = 0; $i < $x; $i++) {
            for ($j = 0; $j < $y; $j++) {
                if (imagecolorat($image, $i, $j) !== imagecolorat($ret, $i, $j)
                ) {
                    $count++;
                }
            }
        }
        //$name = tempnam(sys_get_temp_dir(), "ImagePluginBaseTest");
        //imagepng($ret, $name);
        imagedestroy($image);
        $this->assertSame($x, $retx, "Different widths");
        $this->assertSame($y, $rety, "Different widths");
        $this->assertLessThanOrEqual(
            $allowed, $count, "$count pixels are different"
        );
    }

}
/**
* Driver for the polling script (0039-26-01-P)
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
class ImagePluginBaseTestClass extends ImagePluginBase
{
    /** @var  These are the graph colors that will be used, in order */
    public $params = array(
        "p1" => "hello",
        "p2" => "there",
    );
    /**
    * Returns the object as a string
    *
    * @return string
    */
    public function outputTest()
    {
        $this->gdBuildImage();
        //$name = tempnam(sys_get_temp_dir(), "ImagePluginBaseTest");
        //imagegd($this->img, $name);
        return $this->img;
    }
}
?>
