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
namespace HUGnet\db;
/** This is a required class */
require_once CODE_BASE.'db/Connection.php';
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
class ConnectionTest extends \PHPUnit_Framework_TestCase
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
    * Data provider for testCreateDBO
    *
    * @return array
    */
    public static function dataGetDBO()
    {
        return array(
            array(
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(),
                        ),
                    ),
                ),
                null,
                "PDO",
                "sqlite"
            ),
            array(
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "sqlite",
                                    "file" => ":memory:"
                               ),
                           ),
                        ),
                    ),
                ),
                null,
                "PDO",
                "sqlite",
            ),
            array(
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "badPDODriver",
                                    "file" => ":memory:"
                                )
                            ),
                        ),
                    ),
                ),
                null,
                "PDO",
                "sqlite"
            ),
            array(
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "db" => "MyNewDb",
                                ),
                            ),
                        ),
                    ),
                ),
                null,
                null,
                "sqlite"
            ),
            // Non default group name with group in call unset
            array(
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "group" => "somegroup",
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "db" => "MyNewDb",
                                ),
                                array("driver" => "sqlite", "file" => ":memory:"),
                            ),
                        ),
                    ),
                ),
                null,
                "PDO",
                "sqlite"
            ),
            // Non default group name with group in call
            array(
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "group" => "somegroup",
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "db" => "MyNewDb",
                                ),
                                array(
                                    "group" => "somegroup",
                                    "driver" => "sqlite",
                                    "file" => ":memory:"
                                ),
                            ),
                        ),
                    ),
                ),
                "somegroup",
                "PDO",
                "sqlite"
            ),
            // Non default group name with group in call
            array(
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "group" => "somegroup",
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "db" => "MyNewDb",
                                ),
                                array(
                                    "driver" => "sqlite",
                                    "file" => ":memory:"
                                ),
                            ),
                        ),
                    ),
                ),
                "somegroup",
                null,
                "sqlite"
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * sqlite instance.
    *
    * @param string $preload      The configuration to use
    * @param string $group        The group to check
    * @param mixed  $expect       The expected value.  Set to FALSE or the class name
    * @param mixed  $expectDriver The expected driver
    *
    * @return null
    *
    * @dataProvider dataGetDBO()
    */
    public function testGetDBO($preload, $group, $expect, $expectDriver)
    {
        $system = new \HUGnet\DummySystem("System");
        $system->resetMock($preload);
        $obj = Connection::factory($system);
        if (is_null($group)) {
            $pdo = $obj->getDBO();
        } else {
            $pdo = $obj->getDBO($group);
        }
        if ($expect === false) {
            $this->assertFalse($pdo);
        } else if (is_null($expect)) {
            $this->assertNull($pdo);
        } else {
            $this->assertInternalType("object", $pdo);
            $this->assertSame($expect, get_class($pdo));
            $this->assertSame(
                $expectDriver,
                $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME)
            );
        }
    }
    /**
    * Data provider for testCreatePDO
    *
    * @return array
    */
    public static function dataDriver()
    {
        return array(
            array( // #0
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(),
                        ),
                    ),
                ),
                null,
                "sqlite"
            ),
            array( // #1
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "sqlite",
                                    "file" => ":memory:"
                               ),
                           ),
                        ),
                    ),
                ),
                null,
                "sqlite",
            ),
            array( // #2
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "badPDODriver",
                                    "file" => ":memory:"
                                )
                            ),
                        ),
                    ),
                ),
                null,
                "sqlite"
            ),
            array( // #3
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "db" => "MyNewDb",
                                ),
                            ),
                        ),
                    ),
                ),
                null,
                "sqlite"
            ),
            // Non default group name with group in call unset
            array( // #4
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "group" => "somegroup",
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "db" => "MyNewDb",
                                ),
                                array("driver" => "sqlite", "file" => ":memory:"),
                            ),
                        ),
                    ),
                ),
                null,
                "sqlite"
            ),
            // Non default group name with group in call
            array( // #5
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "group" => "somegroup",
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "db" => "MyNewDb",
                                ),
                                array(
                                    "group" => "somegroup",
                                    "driver" => "sqlite",
                                    "file" => ":memory:"
                                ),
                            ),
                        ),
                    ),
                ),
                "somegroup",
                "sqlite"
            ),
            // Non default group name with group in call
            array( // #6
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "group" => "somegroup",
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "db" => "MyNewDb",
                                ),
                                array(
                                    "driver" => "sqlite",
                                    "file" => ":memory:"
                                ),
                            ),
                        ),
                    ),
                ),
                "somegroup",
                "sqlite"
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * sqlite instance.
    *
    * @param string $preload The configuration to use
    * @param string $group   The group to check
    * @param mixed  $expect  The expected value.  The expected driver
    *
    * @return null
    *
    * @dataProvider dataDriver()
    */
    public function testDriver($preload, $group, $expect)
    {
        $system = new \HUGnet\DummySystem("System");
        $system->resetMock($preload);
        $obj = Connection::factory($system);
        if (is_null($group)) {
            $driver = $obj->driver();
        } else {
            $driver = $obj->driver($group);
        }
        $this->assertSame($expect, $driver);
    }
    /**
    * Data provider for testGroup
    *
    * @return array
    */
    public static function dataGroup()
    {
        return array(
            // Non default group name with group in call
            array(
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "group" => "somegroup",
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "db" => "MyNewDb",
                                ),
                                array(
                                    "group" => "someothergroup",
                                    "driver" => "sqlite",
                                    "file" => ":memory:"
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    "somegroup" => "somegroup",
                    "someothergroup" => "someothergroup",
                    "null" => "null",
                    "tmp"  => "tmp",
                ),
            ),
            // Non default group name with group in call
            array(
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "group" => "somegroup",
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "db" => "MyNewDb",
                                ),
                                array(
                                    "driver" => "sqlite",
                                    "file" => ":memory:"
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    "somegroup" => "somegroup",
                    "default" => "default",
                    "null" => "null",
                    "tmp"  => "tmp",
                ),
            ),
        );
    }
    /**
    * Tests the return of what groups are available.
    *
    * @param string $preload The configuration to use
    * @param mixed  $expect  The expected value.
    *
    * @return null
    *
    * @dataProvider dataGroup()
    */
    public function testGroup($preload, $expect)
    {
        $system = new \HUGnet\DummySystem("System");
        $system->resetMock($preload);
        $obj = Connection::factory($system);
        $ret = $obj->groups();
        $this->assertSame($expect, $ret);
    }


    /**
    * Data provider for testConnect
    *
    * @return array
    */
    public static function dataConnect()
    {
        return array(
            array( // #0
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(),
                        ),
                    ),
                ),
                null,
                "default",
                true
            ),
            array( // #1
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "sqlite",
                                    "file" => "",
                               )
                           ),
                        ),
                    ),
                ),
                null,
                false,
                true,
            ),
            array( // #2
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "sqlite",
                                    "file" => ":memory:"
                               )
                           ),
                        ),
                    ),
                ),
                null,
                false,
                true,
            ),
            array( // #3
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "sqlite",
                                    "file" => sys_get_temp_dir()."/TestFile",
                                    "filePerm" => 0644,
                                )
                            ),
                        ),
                    ),
                ),
                null,
                false,
                true,
            ),
            array( // #4
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "sqlite",
                                    "file" => ":memory:"
                                )
                            ),
                        ),
                    ),
                ),
                "somegroup",
                "group",
                false,
            ),
            array( // #5
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "badPDODriver",
                                    "file" => ":memory:"
                               )
                           ),
                        ),
                    ),
                ),
                null,
                false,
                true
            ),
            array( // #6
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "db" => "MyNewDb",
                                ),
                            ),
                        ),
                    ),
                ),
                "default",
                false,
                false,
            ),
            array( // #7
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "socket" => "/this/file/wont/exist",
                                    "db" => "MyNewDb",
                                ),
                            ),
                        ),
                    ),
                ),
                "default",
                false,
                false,
            ),
            array( // #8
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "db" => "MyNewDb",
                                ),
                                array("driver" => "sqlite", "file" => ":memory:"),
                            ),
                        ),
                    ),
                ),
                "default",
                "default",
                true,
            ),
            // Test preconnect
            array( // #9
                array(
                    "System" => array(
                        "get" => array(
                            "servers" => array(
                                array(
                                    "driver" => "mysql",
                                    "user" => "NotAGoodUserNameToUse",
                                    "password" => "Secret Password",
                                    "db" => "MyNewDb",
                                ),
                                array("driver" => "sqlite", "file" => ":memory:"),
                            ),
                        ),
                    ),
                ),
                "nondefault",
                "default",
                false,
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * sqlite instance.
    *
    * @param string $preload    The configuration to use
    * @param string $group      The group to check
    * @param mixed  $preconnect Connect before the test connect
    * @param bool   $expect     The expected return
    *
    * @return null
    *
    * @dataProvider dataConnect()
    */
    public function testConnect($preload, $group, $preconnect, $expect)
    {
        $system = new \HUGnet\DummySystem("System");
        $system->resetMock($preload);
        $obj = Connection::factory($system);
        if (!is_bool($preconnect)) {
            $obj->connect($preconnect);
        }
        if (is_null($group)) {
            $ret = $obj->connect();
        } else {
            $ret = $obj->connect($group);
        }
        if (empty($group)) {
            $group = "default";
        }
        $this->assertSame($expect, $ret);
        $check = $this->readAttribute($obj, "_server");
        if ($ret) {
            $this->assertTrue(is_object($check[$group]), "_server not found");
        }
        foreach ((array)$preload as $load) {
            if (file_exists($load["file"])) {
                $perms = fileperms($load["file"]) & 0777;
                $this->assertSame(
                    $load["filePerm"], $perms, "File Permissions Wrong"
                );
            }
        }
    }

    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * sqlite instance.
    *
    * @param string $preload    The configuration to use
    * @param string $group      The group to check
    * @param mixed  $preconnect Connect before the test connect
    * @param bool   $expect     The expected return
    *
    * @return null
    *
    * @dataProvider dataConnect()
    */
    public function testAvailable($preload, $group, $preconnect, $expect)
    {
        $system = new \HUGnet\DummySystem("System");
        $system->resetMock($preload);
        $obj = Connection::factory($system);
        if (!is_bool($preconnect)) {
            $obj->connect($preconnect);
        }
        if (is_null($group)) {
            $ret = $obj->available();
        } else {
            $ret = $obj->available($group);
        }
        if (empty($group)) {
            $group = "default";
        }
        $this->assertSame($expect, $ret);
        $check = $this->readAttribute($obj, "_server");
        if ($ret) {
            $this->assertTrue(is_object($check[$group]), "_server not found");
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
                "default",
                "default",
                false,
                false,
            ),
            array(
                array(),
                "Nondefault",
                "default",
                false,
                true,
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * sqlite instance.
    *
    * @param string $preload   The configuration to use
    * @param string $groupDis  The group for disconnect
    * @param string $groupCon  The group for connect
    * @param bool   $expectDis The expected return
    * @param bool   $expectCon The expected return
    *
    * @return null
    *
    * @dataProvider dataDisconnect()
    */
    public function testDisconnect(
        $preload,
        $groupDis,
        $groupCon,
        $expectDis,
        $expectCon
    ) {
        $system = new \HUGnet\DummySystem("System");
        $system->resetMock($preload);
        $obj = Connection::factory($system);
        $ret = $obj->connect($groupCon);
        $obj->disconnect($groupDis);
        $check = $this->readAttribute($obj, "_server");
        $this->assertSame(
            $expectDis,
            is_object($check[$groupDis]),
            "_server is not correct on $groupDis"
        );
        $this->assertSame(
            $expectCon,
            is_object($check[$groupCon]),
            "_server is not correct on $groupCon"
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
                "default",
                "default",
                true,
            ),
            array(
                array(),
                "Nondefault",
                "default",
                false,
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * sqlite instance.
    *
    * @param string $preload  The configuration to use
    * @param string $groupDis The group for disconnect
    * @param string $groupCon The group for connect
    * @param bool   $expect   The expected return
    *
    * @return null
    *
    * @dataProvider dataConnected()
    */
    public function testConnected(
        $preload,
        $groupDis,
        $groupCon,
        $expect
    ) {
        $system = new \HUGnet\DummySystem("System");
        $system->resetMock($preload);
        $obj = Connection::factory($system);
        $obj->connect($groupCon);
        $ret = $obj->connected($groupDis);
        $this->assertSame($expect, $ret);
    }

}

?>
