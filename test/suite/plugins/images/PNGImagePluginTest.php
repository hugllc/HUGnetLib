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
require_once CODE_BASE.'plugins/images/PNGImagePlugin.php';
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
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PNGImagePluginTest extends ImagePluginTestBase
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
        foreach ((array)$this->_files as $key => $file) {
            unlink($file);
            unset($this->_files[$key]);
        }
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
            array("PNGImagePlugin"),
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
                    "height" => 640,
                    "width"  => 640,
                ),
                file_get_contents(
                    TEST_CONFIG_BASE."files/images/pinkSq.png"
                ),
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * The extra gd stuff is because GD imbeds version information and other things
    * into the files, so this normalizes the file to whatever version of GD is being
    * used to create them.
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
        $obj = new PNGImagePlugin($cont);
        $ret = $obj->output();
        $image = imagecreatefromstring($expect);
        $name = tempnam(sys_get_temp_dir(), "PNGImagePluginTest");
        imagepng($image, $name);
        imagedestroy($image);
        $this->_files[] = $name;
        $image2 = imagecreatefromstring($ret);
        $name2 = tempnam(sys_get_temp_dir(), "PNGImagePluginTest");
        imagepng($image2, $name2);
        imagedestroy($image2);
        $this->_files[] = $name2;
        $this->assertSame(file_get_contents($name), file_get_contents($name2));
        //$this->assertSame($expect, $ret);
    }


}

?>
