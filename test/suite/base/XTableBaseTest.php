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
namespace HUGnet\base;
/** This is a required class */
require_once CODE_BASE.'base/XTableBase.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';

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
class XTableBaseTest extends \PHPUnit_Framework_TestCase
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
        parent::setUp();
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
        parent::tearDown();
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataCreate()
    {
        return array(
            array(
                new \HUGnet\DummySystem(),
                null,
                "DummyTable",
                array(
                ),
            ),
            array(
                new \HUGnet\DummySystem(),
                array(
                    "id" => 5,
                    "name" => 3,
                    "value" => 1,
                ),
                "DummyTable",
                array(
                ),
            ),
            array(
                new \HUGnet\DummySystem(),
                array("dev" => 2, "input" => 0),
                new \HUGnet\DummyTable(),
                array(
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $data   The gateway to set
    * @param mixed $class  This is either the name of a class or an object
    * @param array $table  The table to use
    *
    * @return null
    *
    * @dataProvider dataCreate
    */
    public function testCreate($config, $data, $class, $table)
    {
        $table = new \HUGnet\DummyTable();
        $dev = new \HUGnet\DummyBase("Device");
        // This just resets the mock
        $table->resetMock();
        $obj = XTableBaseStub::factory($config, $data, $class, $dev);
        // Make sure we have the right object
        $this->assertInstanceOf(
            "HUGnet\\base\\XTableBase", $obj, "Class wrong"
        );
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function data2Array()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "id" => 0xFD,
                        ),
                        "toArray" => array(
                            "id" => 0xFD,
                            "asdf" => 3,
                            "params" => array(1,2,3,4),
                        ),
                    ),
                ),
                new \HUGnet\DummyTable("Table"),
                array(
                    'id' => 0xFD,
                    'asdf' => 3,
                    'params' => array(1,2,3,4),
                    'archs' => array(
                        "Unknown" => "Unknown",
                    ),
                )
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $class  This is either the name of a class or an object
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider data2Array
    */
    public function test2Array(
        $config, $class, $expect
    ) {
        $sys = new \HUGnet\DummySystem("System");
        $dev = new \HUGnet\DummyBase("Device");
        $sys->resetMock($config);
        $obj = XTableBaseStub::factory($sys, null, $class, $dev);
        $json = $obj->toArray();
        $this->assertEquals($expect, $json);
        unset($obj);
    }

}
/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class XTableBaseStub extends \HUGnet\base\XTableBase
{
    /** This is the table we are using */
    protected $xTable = "XTable";
    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $dbtable The table to use
    *
    * @return null
    */
    public static function &factory(&$system, $data=null, $dbtable=null)
    {
        if (empty($dbtable)) {
            $dbtable = "XTable";
        }
        $object = parent::factory($system, $data, $dbtable);
        return $object;
    }

}

?>
