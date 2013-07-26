<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\aduc;
/** This is the base class */
require_once TEST_BASE."/devices/inputTable/drivers/DriverTestBase.php";

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class DriverTestBaseADuC extends
    \HUGnet\devices\inputTable\drivers\DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "";
    /** This is our system object */
    protected $system = null;
    /** This is our input object */
    protected $input = null;
    /** This is the object we are testing */
    protected $o = null;

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
        parent::setUp();
        $this->system = $this->getMock(
            '\HUGnet\System', 
            array('now'),
            array(
                array(
                    "servers" => array(
                        "hello" => array(
                            "driver" => "sqlite",
                            "file" => ":memory:",
                            "group" => "hello",
                        ),
                        "default" => array(
                            "driver" => "sqlite",
                            "file" => ":memory:",
                            "group" => "default",
                        ),
                    ),
                ),
            )
        );
        $this->system->expects($this->any())
            ->method('now')
            ->will($this->returnValue(1000000));
        $this->input = $this->system->device()->input(0);
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
        unset($this->system);
        unset($this->input);
        parent::tearDown();
    }
    /**
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testFactory()
    {
        $this->assertInstanceOf(
            "HUGnet\devices\inputTable\drivers\\aduc\\".$this->class,
            $this->o
        );
    }

}
?>
