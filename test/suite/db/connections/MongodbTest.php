<?php
/**
 * Tests the filter class
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
 * @subpackage SuiteContainers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
namespace HUGnet\db\connections;
/** This is a required class */
require_once CODE_BASE.'db/connections/Mongodb.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteContainers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class MongodbTest extends \PHPUnit_Framework_TestCase
{
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
        if (!class_exists("\MongoClient")) {
            $this->markTestSkipped("No Mongodb server available");
        }
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
    }


    /**
    * Data provider for testCreateMongodb
    *
    * @return array
    */
    public static function datagetDBO()
    {
        return array(
            array( // #0
                array(
                ),
                "MongoDB",
            ),
            array( // #1
                array(
                    array(
                        "driver" => "mongodb",
                        "file" => ":memory:"
                    ),
                ),
                "MongoDB",
            ),
            array( // #2
                array(
                    array(
                        "driver" => "badMongodbDriver",
                        "file" => ":memory:"
                    )
                ),
                "MongoDB",
            ),
            array( // #3
                array(
                    array(
                        "driver" => "mongodb",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "db" => "MyNewDb",
                    ),
                ),
                null,
            ),
            // Non default group name with group in call
            array( // #4
                array(
                    array(
                        "group" => "somegroup",
                        "driver" => "mongodb",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "db" => "MyNewDb",
                    ),
                ),
                null,
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * mongodb instance.
    *
    * @param string $preload The configuration to use
    * @param mixed  $expect  The expected value.  Set to FALSE or the class name
    *
    * @return null
    *
    * @dataProvider datagetDBO()
    */
    public function testgetDBO($preload, $expect)
    {
        $system = $this->getMock("\HUGnet\System", array("now"));
        $obj = Mongodb::factory($system, $preload);
        $pdo = $obj->getDBO();
        if ($expect === false) {
            $this->assertFalse($pdo);
        } else if (is_null($expect)) {
            $this->assertNull($pdo);
        } else {
            $this->assertInternalType("object", $pdo);
            $this->assertSame($expect, get_class($pdo));
        }
    }
    /**
    * Data provider for testCreateMongodb
    *
    * @return array
    */
    public static function dataDriver()
    {
        return array(
            array(
                array(
                ),
                "mongodb"
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * mongodb instance.
    *
    * @param string $preload The configuration to use
    * @param mixed  $expect  The expected value.  The expected driver
    *
    * @return null
    *
    * @dataProvider dataDriver()
    */
    public function testDriver($preload, $expect)
    {
        $system = $this->getMock("\HUGnet\System", array("now"));
        $obj = Mongodb::factory($system, $preload);
        $driver = $obj->driver();
        $this->assertSame($expect, $driver);
    }

    /**
    * Data provider for testConnect
    *
    * @return array
    */
    public static function dataConnect()
    {
        return array(
            array(  // #0
                array(
                ),
                "default",
                true
            ),
            array(  // #1
                array(
                    array(
                        "driver" => "mongodb",
                        "file" => "",
                    )
                ),
                false,
                true,
            ),
            array(  // #2
                array(
                    array(
                        "driver" => "mongodb",
                        "file" => ":memory:"
                    )
                ),
                false,
                true,
            ),
            array(  // #3
                array(
                    array(
                        "driver" => "mongodb",
                        "file" => sys_get_temp_dir()."/TestFile",
                        "filePerm" => 0644,
                    )
                ),
                false,
                true,
            ),
            array(  // #4
                array(
                    array(
                        "driver" => "mongodb",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "db" => "MyNewDb",
                    ),
                ),
                false,
                false,
            ),
            array(  // #5
                array(
                    array(
                        "driver" => "mongodb",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "socket" => "/this/file/wont/exist",
                        "db" => "MyNewDb",
                    ),
                ),
                false,
                false,
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * mongodb instance.
    *
    * @param string $preload    The configuration to use
    * @param mixed  $preconnect Connect before the test connect
    * @param bool   $expect     The expected return
    *
    * @return null
    *
    * @dataProvider dataConnect()
    */
    public function testConnect($preload, $preconnect, $expect)
    {
        $system = $this->getMock("\HUGnet\System", array("now"));
        $obj = Mongodb::factory($system, $preload);
        if ($preconnect) {
            $obj->connect();
        }
        $ret = $obj->connect();
        $this->assertSame($expect, $ret);
        $check = $this->readAttribute($obj, "_client");
        if ($ret) {
            $this->assertTrue(is_object($check), "_client not found");
        } else {
            $this->assertNull($check, "_client should be null");
        }
    }

    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * mongodb instance.
    *
    * @param string $preload    The configuration to use
    * @param mixed  $preconnect Connect before the test connect
    * @param bool   $expect     The expected return
    *
    * @return null
    *
    * @dataProvider dataConnect()
    */
    public function testAvailable($preload, $preconnect, $expect)
    {
        $system = $this->getMock("\HUGnet\System", array("now"));
        $obj = Mongodb::factory($system, $preload);
        if ($preconnect) {
            $obj->connect();
        }
        $ret = $obj->available();
        $this->assertSame($expect, $ret);
        $check = $this->readAttribute($obj, "_client");
        if ($ret) {
            $this->assertTrue(is_object($check), "_client not found");
        } else {
            $this->assertNull($check, "_client should be null");
        }
    }

    /**
    * Data provider for testDisconnect
    *
    * @return array
    */
    public static function dataDisconnect()
    {
        return array(
            array(
                array(),
                false,
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * mongodb instance.
    *
    * @param string $preload The configuration to use
    * @param bool   $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataDisconnect()
    */
    public function testDisconnect($preload, $expect)
    {
        $system = $this->getMock("\HUGnet\System", array("now"));
        $obj = Mongodb::factory($system, $preload);
        $ret = $obj->connect();
        $obj->disconnect();
        $check = $this->readAttribute($obj, "_client");
        $this->assertSame(
            $expect,
            is_object($check),
            "_client is not correct"
        );
    }

    /**
    * Data provider for testDisconnect
    *
    * @return array
    */
    public static function dataConnected()
    {
        return array(
            array(
                array(),
                false,
                false,
            ),
            array(
                array(),
                true,
                true,
            ),
            array(  // #2
                array(
                    array(
                        "driver" => "mongodb",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "socket" => "/this/file/wont/exist",
                        "db" => "MyNewDb",
                    ),
                ),
                true,
                false,
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * mongodb instance.
    *
    * @param string $preload The configuration to use
    * @param bool   $connect Whether to preconnect or not
    * @param bool   $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataConnected()
    */
    public function testConnected($preload, $connect, $expect)
    {
        $system = $this->getMock("\HUGnet\System", array("now"));
        $obj = Mongodb::factory($system, $preload);
        if ($connect) {
            $obj->connect();
        }
        $ret = $obj->connected();
        $this->assertSame($expect, $ret);
    }

}

?>
