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
namespace HUGnet;
/** This is a required class */
require_once CODE_BASE.'system/Image.php';
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
class ImageTest extends \PHPUnit_Framework_TestCase
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
                new DummySystem(),
                array(),
                null,
                null,
                array(
                ),
            ),
            array(
                new DummySystem(),
                array(
                    "Table" => array(
                        "sanitizeWhere" => array(
                            "id" => 5,
                            "name" => 3,
                            "value" => 1,
                        ),
                    ),
                ),
                array(
                    "id" => 5,
                    "name" => 3,
                    "value" => 1,
                ),
                null,
                array(
                    "fromAny" => array(
                        array(
                            array(
                                "id" => 5,
                                "name" => 3,
                                "value" => 1,
                            ),
                        ),
                    ),
                    "clearData" => array(array()),
                    "selectOneInto" => array(
                        array(
                            array(
                                "id" => 5,
                                "name" => 3,
                                "value" => 1,
                            ),
                        ),
                    ),
                    "sanitizeWhere" => array(
                        array(
                            array(
                                "id" => 5,
                                "name" => 3,
                                "value" => 1,
                            ),
                        ),
                    ),
                ),
            ),
            array(
                new DummySystem(),
                array(),
                2,
                new DummyTable("Table"),
                array(
                    "getRow" => array(
                        array(0 => 2),
                    ),
                    "isEmpty" => array(
                        array(),
                    ),
                    "clearData" => array(array()),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config      The configuration to use
    * @param array $mocks       The mocks to use
    * @param mixed $gateway     The gateway to set
    * @param mixed $class       This is either the name of a class or an object
    * @param array $expectTable The table to expect
    *
    * @return null
    *
    * @dataProvider dataCreate
    */
    public function testCreate($config, $mocks, $gateway, $class, $expectTable)
    {
        $table = new DummyTable();
        // This just resets the mock
        $table->resetMock($mocks);
        $obj = Image::factory($config, $gateway, $class);
        // Make sure we have the right object
        $this->assertTrue((get_class($obj) === "HUGnet\Image"), "Class wrong");
        if (is_object($table)) {
            $this->assertEquals(
                $expectTable, $table->retrieve("Table"), "Data Wrong"
            );
        }
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataToArray()
    {
        return array(
            array( //  #0 Everything Normal
                array(
                    'id' => 5,
                    'name' => 'Test',
                    'image' => '1234',
                    'imagetype' => 'text/plain',
                    'height' => 10,
                    'width' => 20,
                    'desc' => 'This is a desc',
                    'baseavg' => '15MIN',
                    'points' => array(1,2,3,4),
                    'params' => array(4, 3, 2, 1),
                ),
                new DummyTable("Table"),
                true,
                array(
                    'group' => 'default',
                    'id' => 5,
                    'name' => 'Test',
                    'image' => '1234',
                    'imagetype' => 'text/plain',
                    'height' => 10,
                    'width' => 20,
                    'desc' => 'This is a desc',
                    'baseavg' => '15MIN',
                    'points' => array(1,2,3,4),
                    'params' => array(4, 3, 2, 1),
                    'averageTypes' => array(
                        '15MIN' => '15 Minute Average',
                        'HOURLY' => 'Hourly Average',
                        'DAILY' => 'Daily Average',
                        'WEEKLY' => 'Weekly Average',
                        'MONTHLY' => 'Monthly Average',
                        'YEARLY' => 'Yearly Average',
                    ),
                    'publish' => 1,
                )
            ),
            array( //  #1 No Defaults
                array(
                    'id' => 5,
                    'name' => 'Test',
                    'image' => '1234',
                    'imagetype' => 'text/plain',
                    'height' => 10,
                    'width' => 20,
                    'desc' => 'This is a desc',
                    'baseavg' => '30SEC',
                    'points' => array(1, 2, 3, 4),
                    'params' => array(4, 3, 2, 1),
                ),
                new DummyTable("Table"),
                false,
                array(
                    'id' => 5,
                    'name' => 'Test',
                    'image' => '1234',
                    'imagetype' => 'text/plain',
                    'height' => 10,
                    'width' => 20,
                    'desc' => 'This is a desc',
                    'baseavg' => '30SEC',
                    'points' => array(1, 2, 3, 4),
                    'params' => array(4, 3, 2, 1),
                )
            ),
            array( //  #2 30SEC average
                array(
                    'id' => 5,
                    'name' => 'Test',
                    'image' => '1234',
                    'imagetype' => 'text/plain',
                    'height' => 10,
                    'width' => 20,
                    'desc' => 'This is a desc',
                    'baseavg' => '30SEC',
                    'points' => array(1,2,3,4),
                ),
                new DummyTable("Table"),
                true,
                array(
                    'group' => 'default',
                    'id' => 5,
                    'name' => 'Test',
                    'image' => '1234',
                    'imagetype' => 'text/plain',
                    'height' => 10,
                    'width' => 20,
                    'desc' => 'This is a desc',
                    'baseavg' => '30SEC',
                    'points' => array(1,2,3,4),
                    'params' => array(),
                    'averageTypes' => array(
                        '30SEC' => '30 Second Average',
                        '1MIN' => '1 Minute Average',
                        '5MIN' => '5 Minute Average',
                        '15MIN' => '15 Minute Average',
                    ),
                    'publish' => 1,
                )
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config  The configuration to use
    * @param mixed $class   This is either the name of a class or an object
    * @param bool  $default Whether to show the default stuff or not
    * @param mixed $expect  The value we expect back
    *
    * @return null
    *
    * @dataProvider dataToArray
    * @large
    */
    public function testToArray(
        $config, $class, $default, $expect
    ) {
        $sys = $this->getMock('\HUGnet\System', array('now'));
        $sys->expects($this->any())
            ->method('now')
            ->will($this->returnValue(1000000));
        $obj = Image::factory($sys, $config);
        $json = $obj->toArray($default);
        $this->assertEquals($expect, $json);
        unset($obj);
    }
    /**
    * Data provider for testGetParam
    *
    * @return array
    */
    public static function dataSetParam()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => json_encode(
                                array(
                                    "A" => 1,
                                    "B" => 2,
                                    "C" => 3,
                                )
                            ),
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "B",
                4,
                array(
                    array(
                        'params',
                        json_encode(
                            array("A" => 1, "B" => 4, "C" => 3,)
                        ),
                    ),
                ),
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "B",
                5,
                array(
                    array(
                        'params',
                        json_encode(
                            array("B" => 5,)
                        ),
                    ),
                ),
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => json_encode(
                                array(
                                    "A" => 1,
                                    "B" => 2,
                                    "C" => 3,
                                )
                            ),
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "Q",
                8,
                array(
                    array(
                        'params',
                        json_encode(
                            array("A" => 1, "B" => 2, "C" => 3, "Q" => 8)
                        ),
                    ),
                ),
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => json_encode(
                                array(
                                    "A" => 1,
                                    "B" => 2,
                                    "C" => 3,
                                )
                            ),
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "B",
                null,
                array(
                    array(
                        'params',
                        json_encode(
                            array("A" => 1, "C" => 3,)
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $config The configuration to use
    * @param mixed  $class  This is either the name of a class or an object
    * @param string $field  The field to set
    * @param mixed  $value  The value to set the field to
    * @param mixed  $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataSetParam
    */
    public function testSetParam(
        $config, $class, $field, $value, $expect
    ) {
        $sys = new DummySystem("System");
        $sys->resetMock($config);
        $obj = Image::factory($sys, null, $class);
        $obj->setParam($field, $value);
        $ret = $sys->retrieve("Table");
        $this->assertEquals($expect, $ret["set"]);
        unset($obj);
    }
    /**
    * Data provider for testGetParam
    *
    * @return array
    */
    public static function dataGetParam()
    {
        return array(
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => json_encode(
                                array(
                                    "A" => 1,
                                    "B" => 2,
                                    "C" => 3,
                                )
                            ),
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "B",
                2,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "B",
                null,
            ),
            array(
                array(
                    "Table" => array(
                        "get" => array(
                            "params" => json_encode(
                                array(
                                    "A" => 1,
                                    "B" => 2,
                                    "C" => 3,
                                )
                            ),
                        ),
                    ),
                ),
                new DummyTable("Table"),
                "Q",
                null,
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $config The configuration to use
    * @param mixed  $class  This is either the name of a class or an object
    * @param string $field  The field to get
    * @param mixed  $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataGetParam
    * @large
    */
    public function testGetParam(
        $config, $class, $field, $expect
    ) {
        $sys = new DummySystem("System");
        $sys->resetMock($config);
        $obj = Image::factory($sys, null, $class);
        $this->assertSame($expect, $obj->getParam($field));
        unset($obj);
    }
}
?>
