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
namespace HUGnet\images\drivers;
/** This is the base class */
require_once CODE_BASE."images/Driver.php";
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';

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
 */
abstract class DriverTestBase extends \PHPUnit_Framework_TestCase
{
    /** This is the class we are testing */
    protected $class = "";
    /** This is the object we are testing */
    protected $o = null;
    /** This is our system object */
    protected $system;
    /** This is our output object */
    protected $output;

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
        $this->system = $this->getMock("\HUGnet\System", array("now"));
        $this->system->expects($this->any())
            ->method('now')
            ->will($this->returnValue(123456));
        $this->output = $this->system->device()->output(0);
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
        unset($this->o);
    }

    /**
    * data provider for testType
    *
    * @return array
    */
    final public static function dataInternalType()
    {
        return array(
            array("mimetype", "string"),
        );
    }
    /**
    * Check the variable type
    *
    * @param string $field The field to check
    * @param string $type  The type it should be
    *
    * @return null
    *
    * @dataProvider dataInternalType
    */
    final public function testInternalType($field, $type)
    {
        $name = $this->o->get($field, 1);
        $this->assertInternalType($type, $name, "$field must be a $type");
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    final public static function dataStringSize()
    {
        return array(
            array("mimetype", 30, 1),
        );
    }
    /**
    * Check the string size
    *
    * @param string $field The field to check
    * @param int    $max   The largest it can be
    * @param int    $min   The smallest it can be
    *
    * @return null
    *
    * @dataProvider dataStringSize
    */
    final public function testStringMaxSize($field, $max, $min)
    {
        $name = (string)$this->o->get($field, 1);
        $this->assertLessThanOrEqual(
            $max, strlen($name), "$field must be less than $max chars"
        );
    }
    /**
    * Check the string size
    *
    * @param string $field The field to check
    * @param int    $max   The largest it can be
    * @param int    $min   The smallest it can be
    *
    * @return null
    *
    * @dataProvider dataStringSize
    */
    final public function testStringMinSize($field, $max, $min)
    {
        $name = (string)$this->o->get($field, 1);
        $this->assertGreaterThanOrEqual(
            $min, strlen($name), "$field must be more than $min characters"
        );
    }
    /**
    * Check the extraText value size
    *
    * @return null
    */
    public function testMimeTypeValue()
    {
        $mimetype = $this->o->get("mimetype");
        $this->assertTrue(
            (preg_match('#^[-\w]+/[-\w\+]+$#', $mimetype) === 1),
            "Mimetype is not a valid format"
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGet()
    {
        return array(
            array(
                "ThisIsABadName",
                array(),
                null,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The name of the variable to test.
    * @param array  $mock   The mocks to set up
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet($name, $mock, $expect)
    {
        $this->output->table()->fromArray($mock);
        $this->assertSame($expect, $this->o->get($name));
    }
}
?>
