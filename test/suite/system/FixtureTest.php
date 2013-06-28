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
require_once CODE_BASE.'system/Fixture.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once CODE_BASE.'devices/Input.php';
/** This is a required class */
require_once CODE_BASE.'devices/datachan/Driver.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyNetwork.php';
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
class FixtureTest extends \PHPUnit_Framework_TestCase
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
    * Data provider for testGet
    *
    * @return array
    */
    public static function dataGet()
    {
        return array(
            array(
                array(
                ),
                array(
                    "id" => 1,
                    "dev" => 0x123456,
                    "fixture" => json_encode(
                        array(
                            "DeviceName" => "Hello There",
                        )
                    ),
                    "created" => 1234,
                    "modified" => 1234,
                ),
                "DeviceName",
                "Hello There"
            ),
        );
    }
    /**
    * This tests the get function
    *
    * @param array  $config The configuration to use
    * @param array  $data   The data to feed the object
    * @param string $key    The key to check
    * @param array  $expect The table to expect
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet($config, $data, $key, $expect)
    {
        $sys = $this->getMock('\HUGnet\System', array('now'));
        $sys->config($config);
        $obj = Fixture::factory($sys, $data);
        $this->assertSame($expect, $obj->get($key));
    }
    /**
    * Data provider for testGet
    *
    * @return array
    */
    public static function dataGetParam()
    {
        return array(
            array(
                array(
                ),
                array(
                    "id" => 1,
                    "dev" => 0x123456,
                    "fixture" => json_encode(
                        array(
                            "params" => array(
                                "LastModified" => 0x12345678,
                            ),
                        )
                    ),
                    "created" => 1234,
                    "modified" => 1234,
                ),
                "LastModified",
                0x12345678
            ),
        );
    }
    /**
    * This tests the getParam function
    *
    * @param array  $config The configuration to use
    * @param array  $data   The data to feed the object
    * @param string $key    The key to check
    * @param array  $expect The table to expect
    *
    * @return null
    *
    * @dataProvider dataGetParam
    */
    public function testGetParam($config, $data, $key, $expect)
    {
        $sys = $this->getMock('\HUGnet\System', array('now'));
        $sys->config($config);
        $obj = Fixture::factory($sys, $data);
        $this->assertSame($expect, $obj->getParam($key));
    }
    /**
    * This tests the getLocalParam function.  This function should ALWAYS return null
    *
    * @param array  $config The configuration to use
    * @param array  $data   The data to feed the object
    * @param string $key    The key to check
    * @param array  $expect The table to expect
    *
    * @return null
    *
    * @dataProvider dataGetParam
    */
    public function testGetLocalParam($config, $data, $key, $expect)
    {
        $sys = $this->getMock('\HUGnet\System', array('now'));
        $sys->config($config);
        $obj = Fixture::factory($sys, $data);
        $this->assertSame(null, $obj->getLocalParam($key));
    }
    /**
    * Data provider for testSet
    *
    * @return array
    */
    public static function dataSet()
    {
        return array(
            array(
                array(
                ),
                array(
                    "id" => 1,
                    "dev" => 0x123456,
                    "fixture" => json_encode(
                        array(
                            "params" => array(
                                "LastModified" => 0x12345678,
                            ),
                        )
                    ),
                    "created" => 1234,
                    "modified" => 1234,
                ),
                "LastModified",
                0x12345678
            ),
        );
    }
    /**
    * This tests the set function
    *
    * @param array  $config The configuration to use
    * @param array  $data   The data to feed the object
    * @param string $key    The key to check
    * @param array  $expect The table to expect
    *
    * @return null
    *
    * @dataProvider dataSet
    */
    public function testSet($config, $data, $key, $expect)
    {
        $sys = $this->getMock('\HUGnet\System', array('now'));
        $sys->config($config);
        $obj = Fixture::factory($sys, $data);
        $this->assertSame($expect, $obj->set($key, $expect));
    }
    /**
    * This tests the set function
    *
    * @param array  $config The configuration to use
    * @param array  $data   The data to feed the object
    * @param string $key    The key to check
    * @param array  $expect The table to expect
    *
    * @return null
    *
    * @dataProvider dataSet
    */
    public function testSetParam($config, $data, $key, $expect)
    {
        $sys = $this->getMock('\HUGnet\System', array('now'));
        $sys->config($config);
        $obj = Fixture::factory($sys, $data);
        $this->assertSame($expect, $obj->setParam($key, $expect));
    }
    /**
    * This tests the set function
    *
    * @param array  $config The configuration to use
    * @param array  $data   The data to feed the object
    * @param string $key    The key to check
    * @param array  $expect The table to expect
    *
    * @return null
    *
    * @dataProvider dataSet
    */
    public function testSetLocalParam($config, $data, $key, $expect)
    {
        $sys = $this->getMock('\HUGnet\System', array('now'));
        $sys->config($config);
        $obj = Fixture::factory($sys, $data);
        $this->assertSame($expect, $obj->setLocalParam($key, $expect));
    }
    /**
    * Data provider for testSet
    *
    * @return array
    */
    public static function dataToArray()
    {
        return array(
            array(
                array(
                ),
                array(
                    "id" => 1,
                    "dev" => 0x123456,
                    "fixture" => json_encode(
                        array(
                            "DeviceName" => "Hello",
                            "params" => array(
                                "LastModified" => 0x12345678,
                            ),
                        )
                    ),
                    "created" => 1234,
                    "modified" => 1234,
                ),
                true,
                array(
                    "DeviceName" => "Hello",
                    "params" => array(
                        "LastModified" => 0x12345678,
                    ),
                ),
            ),
            array(
                array(
                ),
                array(
                    "id" => 1,
                    "dev" => 0x123456,
                    "fixture" => json_encode(
                        array(
                            "DeviceName" => "Hello",
                            "params" => array(
                                "LastModified" => 0x12345678,
                            ),
                        )
                    ),
                    "created" => 1234,
                    "modified" => 1234,
                ),
                false,
                array(
                    "DeviceName" => "Hello",
                    "params" => array(
                        "LastModified" => 0x12345678,
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the set function
    *
    * @param array $config  The configuration to use
    * @param array $data    The data to feed the object
    * @param bool  $default This does nothing, but we have to prove that.
    * @param array $expect  The table to expect
    *
    * @return null
    *
    * @dataProvider dataToArray
    */
    public function testToArray($config, $data, $default, $expect)
    {
        $sys = $this->getMock('\HUGnet\System', array('now'));
        $sys->config($config);
        $obj = Fixture::factory($sys, $data);
        $this->assertSame($expect, $obj->toArray($default));
    }

    /**
    * Data provider for testSet
    *
    * @return array
    */
    public static function dataStore()
    {
        return array(
            array(
                array(
                ),
                array(
                    "id" => 1,
                    "dev" => 0x123456,
                    "fixture" => json_encode(
                        array(
                            "params" => array(
                                "LastModified" => 0x12345678,
                            ),
                        )
                    ),
                    "created" => 1234,
                    "modified" => 1234,
                ),
                true
            ),
        );
    }
    /**
    * This tests the set function
    *
    * @param array $config  The configuration to use
    * @param array $data    The data to feed the object
    * @param array $expect  The table to expect
    *
    * @return null
    *
    * @dataProvider dataStore
    */
    public function testStore($config, $data, $expect)
    {
        $sys = $this->getMock('\HUGnet\System', array('now'));
        $sys->config($config);
        $obj = Fixture::factory($sys, $data);
        $this->assertSame($expect, $obj->store());
    }

}

?>