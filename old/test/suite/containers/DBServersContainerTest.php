<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @subpackage SuiteContainers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** This is a required class */
require_once CODE_BASE.'containers/DBServersContainer.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTableContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteContainers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DBServersContainerTest extends PHPUnit_Framework_TestCase
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
    * Data provider for testSerialize
    *
    * @return array
    */
    public static function dataSerialize()
    {
        return array(
            array(
                array(array("group" => "one", "driver" => "asdf", "db" => "hello")),
                "default",
                array(array("group" => "one", "driver" => "asdf", "db" => "hello")),
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array  $preload The array to preload into the class
    * @param string $group   The group to connect to
    * @param array  $expect  The expected return
    *
    * @dataProvider dataSerialize
    *
    * @return null
    */
    public function testSerialize($preload, $group, $expect)
    {
        $obj = new DBServersContainer($preload);
        $data = serialize($obj);
        $obj2 = unserialize($data);
        $this->assertSame(get_class($obj), get_class($obj2), "Class is wrong");
        $this->assertSame($expect, $obj2->toArray(), "Data is wrong");
    }


    /**
    * Data provider for testCreatePDO
    *
    * @return array
    */
    public static function dataGetPDO()
    {
        return array(
            array(array(), null, "PDO", "sqlite"),
            array(
                array(array("driver" => "sqlite", "file" => ":memory:")),
                null,
                "PDO",
                "sqlite",
            ),
            array(
                array(array("driver" => "badPDODriver", "file" => ":memory:")),
                null,
                "PDO",
                "sqlite"
            ),
            array(
                array(
                    array(
                        "driver" => "mysql",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "db" => "MyNewDb",
                    ),
                ),
                null,
                null,
                "sqlite"
            ),
            // Non default group name with group in call unset
            array(
                array(
                    array(
                        "group" => "somegroup",
                        "driver" => "mysql",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "db" => "MyNewDb",
                    ),
                    array("driver" => "sqlite", "file" => ":memory:"),
                ),
                null,
                "PDO",
                "sqlite"
            ),
            // Non default group name with group in call
            array(
                array(
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
                "somegroup",
                "PDO",
                "sqlite"
            ),
            // Non default group name with group in call
            array(
                array(
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
    * @dataProvider dataGetPDO()
    */
    public function testGetPDO($preload, $group, $expect, $expectDriver)
    {
        $obj = new DBServersContainer($preload);
        if (is_null($group)) {
            $pdo = $obj->getPDO();
        } else {
            $pdo = $obj->getPDO($group);
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
                $pdo->getAttribute(PDO::ATTR_DRIVER_NAME)
            );
        }
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
                array(
                    "somegroup" => "somegroup",
                    "someothergroup" => "someothergroup"
                ),
            ),
            // Non default group name with group in call
            array(
                array(
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
                array(
                    "somegroup" => "somegroup",
                    "default" => "default"
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
        $obj = new DBServersContainer($preload);
        $ret = $obj->groups();
        $this->assertSame($expect, $ret);
    }

    /**
    * Data provider for testCreatePDO
    *
    * @return array
    */
    public static function dataGetDriver()
    {
        return array(
            array(array(), new DummyTableContainer(), null, "SqliteDriver"),
            array(
                array(array("driver" => "sqlite", "file" => ":memory:")),
                new DummyTableContainer(),
                null,
                "SqliteDriver",
            ),
            array(
                array(array("driver" => "badPDODriver", "file" => ":memory:")),
                new DummyTableContainer(),
                null,
                "SqliteDriver",
            ),
            array(
                array(
                    array(
                        "driver" => "mysql",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "db" => "MyNewDb",
                    ),
                ),
                new DummyTableContainer(),
                null,
                null,
            ),
            // Non default group name with group in call unset
            array(
                array(
                    array(
                        "group" => "somegroup",
                        "driver" => "mysql",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "db" => "MyNewDb",
                    ),
                    array("driver" => "sqlite", "file" => ":memory:"),
                ),
                new DummyTableContainer(),
                null,
                "SqliteDriver",
            ),
            // Non default group name with group in call
            array(
                array(
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
                new DummyTableContainer(),
                "somegroup",
                "SqliteDriver",
            ),
            // Non default group name with group in call
            array(
                array(
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
                new DummyTableContainer(),
                "somegroup",
                null,
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * sqlite instance.
    *
    * @param string $preload The configuration to use
    * @param object $table   The table object to use
    * @param string $group   The group to check
    * @param mixed  $expect  The expected value.  Set to FALSE or the class name
    *
    * @return null
    *
    * @dataProvider dataGetDriver()
    */
    public function testGetDriver($preload, $table, $group, $expect)
    {
        $obj = new DBServersContainer($preload);
        if (is_null($group)) {
            $pdo = $obj->getDriver($table);
        } else {
            $pdo = $obj->getDriver($table, $group);
        }
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
    * Data provider for testConnect
    *
    * @return array
    */
    public static function dataConnect()
    {
        return array(
            array(array(), null, "default", true),
            array(
                array(array("driver" => "sqlite", "file" => ":memory:")),
                null,
                false,
                true,
            ),
            array(
                array(
                    array(
                        "driver" => "sqlite",
                        "file" => sys_get_temp_dir()."/TestFile",
                        "filePerm" => 0644,
                    )
                ),
                null,
                false,
                true,
                ),
                array(
                array(array("driver" => "sqlite", "file" => ":memory:")),
                "somegroup",
                "group",
                false,
            ),
            array(
                array(array("driver" => "badPDODriver", "file" => ":memory:")),
                null,
                false,
                true
            ),
            array(
                array(
                    array(
                        "driver" => "mysql",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "db" => "MyNewDb",
                    ),
                ),
                "default",
                false,
                false,
            ),
            array(
                array(
                    array(
                        "driver" => "mysql",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "db" => "MyNewDb",
                    ),
                    array("driver" => "sqlite", "file" => ":memory:"),
                ),
                "default",
                "default",
                true,
            ),
            // Test preconnect
            array(
                array(
                    array(
                        "driver" => "mysql",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "db" => "MyNewDb",
                    ),
                    array("driver" => "sqlite", "file" => ":memory:"),
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
        $obj = new DBServersContainer($preload);
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
        foreach (array("server", "pdo") as $var) {
            $check = $this->readAttribute($obj, $var);
            if ($ret) {
                $this->assertTrue(is_object($check[$group]), "$var not found");
            } else {
                $this->assertNull($check[$group], "$var should be null");
            }
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
        $obj = new DBServersContainer($preload);
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
        foreach (array("server", "pdo") as $var) {
            $check = $this->readAttribute($obj, $var);
            if ($ret) {
                $this->assertTrue(is_object($check[$group]), "$var not found");
            } else {
                $this->assertNull($check[$group], "$var should be null");
            }
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
        $obj = new DBServersContainer($preload);
        $ret = $obj->connect($groupCon);
        $obj->disconnect($groupDis);
        foreach (array("server", "pdo") as $var) {
            $check = $this->readAttribute($obj, $var);
            $pdo = $this->readAttribute($obj, $var);
            $this->assertSame(
                $expectDis,
                is_object($check[$groupDis]),
                "$var is not correct on $groupDis"
            );
            $this->assertSame(
                $expectCon,
                is_object($check[$groupCon]),
                "$var is not correct on $groupCon"
            );
        }
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
        $obj = new DBServersContainer($preload);
        $obj->connect($groupCon);
        $ret = $obj->connected($groupDis);
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testDisconnect
    *
    * @return array
    */
    public static function dataToArray()
    {
        return array(
            array(
                array(),
                array(
                ),
            ),
            array(
                array(
                    array(
                        "driver" => "mysql",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "db" => "MyNewDb",
                    ),
                    array(
                        "group" => "local",
                        "driver" => "sqlite",
                        "file" => ":memory:"
                    ),
                ),
                array(
                    array(
                        "driver" => "mysql",
                        "db" => "MyNewDb",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                    ),
                    array(
                        "group" => "local",
                    ),
                ),
            ),
            array(
                array(
                    array(
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
                array(
                    array(
                        "driver" => "mysql",
                        "db" => "MyNewDb",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                    ),
                ),
            ),
            array(
                array(
                    array(
                        "driver" => "mysql",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                        "db" => "MyNewDb",
                    ),
                ),
                array(
                    array(
                        "driver" => "mysql",
                        "db" => "MyNewDb",
                        "user" => "NotAGoodUserNameToUse",
                        "password" => "Secret Password",
                    ),
                ),
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * sqlite instance.
    *
    * @param string $preload The configuration to use
    * @param bool   $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataToArray()
    */
    public function testToArray($preload, $expect)
    {
        $obj = new DBServersContainer($preload);
        $ret = $obj->toArray();
        $this->assertSame($expect, $ret);
    }

    /**
    * This is to test the singleton item
    *
    * @return null
    */
    public function testSingleton()
    {
        $obj = DBServersContainer::singleton($preload);
        $p = DBServersContainer::singleton($preload);
        $this->assertSame($obj, $p);
    }

}

?>
