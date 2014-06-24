<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\roles;
/** This is a required class */
require_once CODE_BASE.'devices/roles/FETBoard.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is the base of our base class */
require_once dirname(__FILE__)."/RoleTestBase.php";

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.11.0
 */
class FETBoardTest extends RoleTestBase
{
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
        $this->o = FETBoard::factory();
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
        unset($this->o);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataInput()
    {
        return array(
            array(
                17, null
            ),
            array(
                0, array(
                    "id" => 0xF8,
                    "extra" => array(0, 0.5, 1, 5.0),
                    "location" => "Channel 1 Current",
                    "type" => "AVRAnalogTable",
                    "tableEntry" => array(
                        "driver" => "50:fetBoard",
                        "name" => "FET Board Current",
                        "MUX" => 0,
                        "id" => 0,
                        "ADLAR" => 1,
                        "REFS" => 0,
                    ),
                )
            ),
        );
    }
    /**
    * data provider for testProcess
    *
    * @return array
    */
    public static function dataProcess()
    {
        return array(
            array(
                0, null
            ),
            array(
                1, null
            ),
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataOutput()
    {
        return array(
            array(
                8, null
            ),
            array(
                1,
                array(
                    "location" => "Channel 2",
                    "extra"    => array(1 => 1),
                    "id"       => 0x31,
                    "type"     => "FET003912",
                    "tableEntry" => array(
                    ),
                )
            ),
        );
    }
}

?>
