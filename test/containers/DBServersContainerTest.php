<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../containers/DBServersContainer.php';
require_once dirname(__FILE__).'/../stubs/DummyTableContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
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
        $o = new DBServersContainer($preload);
        if (is_null($group)) {
            $pdo = $o->getPDO();
        } else {
            $pdo = $o->getPDO($group);
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
        $o = new DBServersContainer($preload);
        $ret = $o->groups();
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
        $o = new DBServersContainer($preload);
        if (is_null($group)) {
            $pdo = $o->getDriver($table);
        } else {
            $pdo = $o->getDriver($table, $group);
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
        $o = new DBServersContainer($preload);
        if (!is_bool($preconnect)) {
            $o->connect($preconnect);
        }
        if (is_null($group)) {
            $ret = $o->connect();
        } else {
            $ret = $o->connect($group);
        }
        if (empty($group)) {
            $group = "default";
        }
        $this->assertSame($expect, $ret);
        foreach (array("server", "pdo") as $var) {
            $check = $this->readAttribute($o, $var);
            if ($ret) {
                $this->assertTrue(is_object($check[$group]), "$var not found");
            } else {
                $this->assertNull($check[$group], "$var should be null");
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
        $o = new DBServersContainer($preload);
        if (!is_bool($preconnect)) {
            $o->connect($preconnect);
        }
        if (is_null($group)) {
            $ret = $o->available();
        } else {
            $ret = $o->available($group);
        }
        if (empty($group)) {
            $group = "default";
        }
        $this->assertSame($expect, $ret);
        foreach (array("server", "pdo") as $var) {
            $check = $this->readAttribute($o, $var);
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
        $o = new DBServersContainer($preload);
        $ret = $o->connect($groupCon);
        $o->disconnect($groupDis);
        foreach (array("server", "pdo") as $var) {
            $check = $this->readAttribute($o, $var);
            $pdo = $this->readAttribute($o, $var);
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
        $o = new DBServersContainer($preload);
        $o->connect($groupCon);
        $ret = $o->connected($groupDis);
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
        $o = new DBServersContainer($preload);
        $ret = $o->toArray();
        $this->assertSame($expect, $ret);
    }

    /**
    * This is to test the singleton item
    *
    * @return null
    */
    public function testSingleton()
    {
        $o = DBServersContainer::singleton($preload);
        $p = DBServersContainer::singleton($preload);
        $this->assertSame($o, $p);
    }

}

?>
