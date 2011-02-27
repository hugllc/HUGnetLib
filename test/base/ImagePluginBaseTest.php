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


require_once dirname(__FILE__).'/../../containers/ConfigContainer.php';
require_once dirname(__FILE__).'/../../base/ImagePluginBase.php';
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
                dirname(__FILE__)."/../files/plugins/"
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
                    dirname(__FILE__)."/../../contrib/fonts/bitstream-vera/Vera.ttf"
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
        $o = new ImagePluginBaseTestClass($img, $data);
        $this->assertAttributeSame($img, "image", $o, "Image wrong");
        $this->assertAttributeSame($font, "_fontFile", $o, "Font wrong");
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
        $o = new ImagePluginBaseTestClass($img, $data);
        $body = $o->output();
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
                    dirname(__FILE__)
                    ."/../files/images/ImagePluginsBaseTestBlank.png"
                ),
            ),
            array( // #1
                array(
                    "height" => 640,
                    "width" => 640,
                    "imageLoc" => realpath(
                        dirname(__FILE__)."/../files/images/pink.png"
                    ),
                ),
                array(),
                realpath(
                    dirname(__FILE__)."/../files/images/pinkSq.png"
                ),
            ),
            array( // #2
                array(
                    "height" => 100,
                    "width" => 150,
                    "imageLoc" => realpath(
                        dirname(__FILE__)."/../files/images/pink.png"
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
                realpath(
                    dirname(__FILE__)
                    ."/../files/images/ImagePluginsBaseTestText1.png"
                ),
            ),
            array( // #3
                array(
                    "height" => 100,
                    "width" => 150,
                    "imageLoc" => realpath(
                        dirname(__FILE__)."/../files/images/pink.png"
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
                    "fontFile" => realpath(
                        dirname(__FILE__)
                        ."/../../contrib/fonts/bitstream-vera/Vera.ttf"
                    )
                ),
                realpath(
                    dirname(__FILE__)
                    ."/../files/images/ImagePluginsBaseTestText2.png"
                ),
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
    *
    * @return null
    *
    * @dataProvider dataOutputTest
    */
    public function testOutputTest($preload, $data, $expect)
    {
        $img = new ImageContainer($preload);
        $o = new ImagePluginBaseTestClass($img, $data);
        $ret = $o->outputTest();
        $this->_files[] = $ret;
        $image = imagecreatefrompng($expect);
        $name = tempnam(sys_get_temp_dir(), "ImagePluginBaseTest");
        imagegd($image, $name);
        imagedestroy($image);
        $this->_files[] = $name;
        $image2 = imagecreatefromgd($ret);
        $name2 = tempnam(sys_get_temp_dir(), "ImagePluginBaseTest");
        imagegd($image2, $name2);
        imagedestroy($image2);
        $this->_files[] = $name2;
        $this->assertFileEquals($name, $name2);
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
        $name = tempnam(sys_get_temp_dir(), "ImagePluginBaseTest");
        imagegd($this->img, $name);
        return $name;
    }
}
?>
