<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2015 Hunt Utilities Group, LLC
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
 * @subpackage SuiteCLI
 * @author     Scott Price <prices@hugllc.com>
 * @author     Jeff Liesmaki <jeffl@hugllc.com>
 * @copyright  2015 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\ui;
/** Displays class */
require_once CODE_BASE."ui/Displays.php";
/** This is a required class */
require_once CODE_BASE.'system/Error.php';
/** This is a required class */
require_once CODE_BASE.'ui/Args.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteCLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DisplaysTest extends \PHPUnit_Framework_TestCase
{

    protected $display;


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
        $this->display = \HUGnet\ui\Displays::factory($config);

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
        unset($this->display);

    }

    /**
    * data provider for testClearScreen
    *
    * @return array
    */
    public static function dataTestClearScreen()
    {
        return array(
            array(
                true,
            ),

        );
    }

    /**
    * Tests the clearScreen function
    *
    * @param $expect
    *
    * @return null
    *
    * @dataProvider dataTestClearScreen()
    */
    public function testClearScreen($expect)
    {
        ob_start();
        $this->display->clearScreen();
        $newHeading = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expect,(boolean)$newHeading);

    }

    /**
    * data provider for testDisplayHeader
    *
    * @return array
    */
    public static function dataTestDisplayMLHeader()
    {
        return array(
            array(
                array(
                    0 => "Test Headings",
                    1 => "Test Heading",
                    2 => "Here is a really really long heading that is way too long for the box",
                ),
                "************************************************************
*                                                          *
*                      Test Headings                       *
*                       Test Heading                       *
* Here is a really really long heading that is way too lon *
*                                                          *
************************************************************
\n\r\n",
             )
        );
    }

    /**
    * Tests the displayMLHeader function
    *
    * @param array  $headingArray   array of heading strings
    * @param string $expect         The display we are expecting
    *
    * @return null
    *
    * @dataProvider dataTestDisplayMLHeader()
    */
    public function testDisplayMLHeader($heading=array(), $expect)
    {
        ob_start();
        $this->display->displayMLHeader($heading);
        $newHeading = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expect, $newHeading);
    }


    /**
    * data provider for testDisplayHeader
    *
    * @return array
    */
    public static function dataTestDisplayHeader()
    {
        return array(
            array(
                "Test Headings",
                "************************************************************
*                                                          *
*                      Test Headings                       *
*                                                          *
************************************************************
\n\r\n",
             )
        );
    }

    /**
    * Tests the displayHeader function
    *
    * @param string $heading   heading string
    * @param string $expect    The display we are expecting
    *
    * @return null
    *
    * @dataProvider dataTestDisplayHeader()
    */
    public function testDisplayHeader($heading, $expect)
    {
        ob_start();
        $this->display->displayHeader($heading);
        $newHeading = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expect, $newHeading);
    }

    /**
    * data provider for testDisplayHeader2
    *
    * @return array
    */
    public static function dataTestDisplayHeader2()
    {
        return array(
            array(
                "This will be part of Test Headings for a really long heading.",
                "************************************************************
*                                                          *
* This will be part of Test Headings for a really long hea *
*                                                          *
************************************************************
\n\r\n",
             )
        );
    }

    /**
    * Tests the displayHeader function
    *
    * @param string $heading   heading string
    * @param string $expect    The display we are expecting
    *
    * @return null
    *
    * @dataProvider dataTestDisplayHeader2()
    */
    public function testDisplayHeader2($heading, $expect)
    {
        ob_start();
        $this->display->displayHeader($heading);
        $newHeading = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expect, $newHeading);
    }


    /**
    * data provider for testSetHeaderWidth
    *
    * @return array
    */
    public static function dataTestSetHeaderWidth()
    {
        return array(
            array(
                "Test Headings",
                "******************************************************************
*                                                                *
*                         Test Headings                          *
*                                                                *
******************************************************************
\n\r\n",
             )
        );
    }

    /**
    * Tests the setHeaderWidth function
    *
    * @param string $heading   heading string
    * @param string $expect    The display we are expecting
    *
    * @return null
    *
    * @dataProvider dataTestSetHeaderWidth()
    */
    public function testSetHeaderWidth($heading, $expect)
    {
        ob_start();
        $this->display->displaySetHeaderWidth(66);
        $this->display->displayHeader($heading);
        $newHeading = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expect, $newHeading);
    }


/************************************************************************************
* This test commented out because the displayMenu routine does a readline, prompting
* user input.  This stops the test and waits for user input and currently their is 
* no way to programmatically stuff a keyboard response.
*
    /**
    * data provider for testDisplayMenu
    *
    * @return array
    *
    public static function dataTestDisplayMenu()
    {
        return array(
            array(
                "Test Headings",
                array(
                    0 => "Menu Item 1",
                    1 => "Menu Item 2",
                    2 => "Menu Item 3",
                ),
                "************************************************************
*                                                          *
*                      Test Headings                       *
*                                                          *
************************************************************
\n\r
A ) Menu Item 1
B ) Menu Item 2
C ) Menu Item 3
D ) Exit
\r\n",
             )
        );
    }
    
    /**
    * Test the displayMenu function
    * 
    * @param string $heading
    * @param array  $menu
    * @param string $expect
    *
    * @return null
    *
    * @dataProvider dataTestDisplayMenu()
    *
    public function testDisplayMenu($heading, $menuArray, $expect)
    {
        ob_start();
        $this->display->displayMenu($heading, $menuArray);
        $menuDisplay = ob_get_contents();
        ob_end_clean();


        /***
        print "\n\rMenuDisplay Contents\n\r";
        var_dump($menuDisplay);

        print "expect contents\n\r";
        var_dump($expect);


        for ($i=0; $i < strlen($menuDisplay); $i++) {
            if ($menuDisplay[$i] != $expect[$i]) {
                print "mismatch at ".$i."\n\r";
                print "the ".ord($menuDisplay[$i])."does not match".
                            ord($expect[$i])."\n\r";
            }
        }  

        $this->assertEquals($expect, $menuDisplay);

    } 
*
* End of commented out menuDisplay test.
************************************************************************************/

    /**
    * data provider for testDisplayPassed
    *
    * @return array
    */
    public static function dataTestDisplayPassed()
    {
        return array(
            array(
                "\n\r
**************************************************
*                                                *
*                P A S S E D !                   *
*                                                *
**************************************************
\n\r\n",
             )
        );
    }

    /**
    * Tests the displayPassed function
    *
    * @param string $expect
    *
    * @return null
    *
    * @dataProvider dataTestDisplayPassed()
    *
    */
    public function testDisplayPassed($expect)
    {
        ob_start();
        $this->display->displayPassed();
        $passedDisplay = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expect, $passedDisplay);
    }

    /**
    * data provider for testDisplayFailed
    *
    * @return array
    */
    public static function dataTestDisplayFailed()
    {
        return array(
            array(
                "\n\r
**************************************************
*                                                *
*                F A I L E D !                   *
*                                                *
**************************************************
\n\r\n",
             )
        );
    }

    /**
    * Tests the displayFailed function
    *
    * @param string $expect
    *
    * @return null
    *
    * @dataProvider dataTestDisplayFailed()
    *
    */
    public function testDisplayFailed($expect)
    {
        ob_start();
        $this->display->displayFailed();
        $failedDisplay = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expect, $failedDisplay);
    }



}
?>