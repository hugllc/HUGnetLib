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


require_once CODE_BASE.'containers/SocketsContainer.php';

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
class SocketsContainerTest extends PHPUnit_Framework_TestCase
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
    public static function dataGetSocket()
    {
        return array(
            array(array(array("dummy" => true)), null, "DummySocketContainer"),
            // Non default group name with group in call
            array(
                array(
                    array(
                        "GatewayIP" => "127.0.0.2",
                        "GatewayPort" => 6574,
                        "group" => "somegroup",
                    ),
                    array(
                        "dummy" => true,
                        "group" => "somegroup",
                    ),
                    array(
                        "stuff" => "Unknown gateway type",
                        "group" => "somegroup",
                    ),
                ),
                "somegroup",
                "DummySocketContainer",
            ),
            // Non default group name with group in call
            array(
                array(
                    array(
                        "dummy" => true,
                    ),
                    array(
                        "GatewayIP" => "127.0.0.2",
                        "GatewayPort" => 6574,
                        "group" => "somegroup",
                    ),
                ),
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
    * @param string $group   The group to check
    * @param mixed  $expect  The expected value.  Set to FALSE or the class name
    *
    * @return null
    *
    * @dataProvider dataGetSocket()
    */
    public function testGetSocket($preload, $group, $expect)
    {
        $obj = new SocketsContainer($preload);
        if (is_null($group)) {
            $pdo = $obj->getSocket();
        } else {
            $pdo = $obj->getSocket($group);
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
    * Data provider for testGroup
    *
    * @return array
    */
    public static function dataGroup()
    {
        return array(
            array(array(array("dummy" => true)), array("default" => "default")),
            // Non default group name with group in call
            array(
                array(
                    array(
                        "GatewayIP" => "127.0.0.2",
                        "GatewayPort" => 6574,
                        "group" => "somegroup",
                    ),
                    array(
                        "dummy" => true,
                        "group" => "somethirdgroup",
                    ),
                    array(
                        "stuff" => "Unknown gateway type",
                        "group" => "someothergroup",
                    ),
                ),
                array(
                    "somegroup" => "somegroup",
                    "somethirdgroup" => "somethirdgroup",
                    "someothergroup" => "someothergroup"
                )
            ),
            // Non default group name with group in call
            array(
                array(
                    array(
                        "dummy" => true,
                    ),
                    array(
                        "GatewayIP" => "127.0.0.2",
                        "GatewayPort" => 6574,
                        "group" => "somegroup",
                    ),
                ),
                array("default" => "default", "somegroup" => "somegroup"),
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
        $obj = new SocketsContainer($preload);
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
            array(array(), null, "default", false),
            array(
                array(
                    array(
                        "dummy" => true,
                    ),
                ),
                null,
                false,
                true,
            ),
            array(
                array(
                    array(
                        "dummy" => true,
                        "group" => "SomeOtherGroup",
                    ),
                    array(
                        "dummy" => true,
                    ),
                ),
                null,
                false,
                true,
            ),
            array(
                array(
                    array(
                        "dummy" => true,
                        "group" => "SomeOtherGroup",
                    ),
                    array(
                        "dummy" => true,
                    ),
                ),
                "default",
                "default",
                true,
            ),
            array(
                array(
                    array(
                        "dummy" => true,
                        "group" => "SomeOtherGroup",
                    ),
                    array(
                        "dummy" => true,
                    ),
                ),
                "badGroup",
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
        $obj = new SocketsContainer($preload);
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
        foreach (array("socket") as $var) {
            $check = $this->readAttribute($obj, $var);
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
        $obj = new SocketsContainer($preload);
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
        foreach (array("socket") as $var) {
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
                array(
                    array(
                        "dummy" => true,
                    ),
                ),
                "default",
                "default",
                false,
                false,
            ),
            array(
                array(
                    array(
                        "dummy" => true,
                    ),
                ),
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
        $obj = new SocketsContainer($preload);
        $ret = $obj->connect($groupCon);
        $obj->disconnect($groupDis);
        foreach (array("socket") as $var) {
            $check = $this->readAttribute($obj, $var);
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
                array(
                    array(
                        "dummy" => true,
                    ),
                ),
                "default",
                "default",
                true,
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
        $obj = new SocketsContainer($preload);
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
                array(
                    array(
                        "GatewayIP" => "127.0.0.1",
                    ),
                ),
                array(
                ),
            ),
            array(
                array(
                    array(
                        "GatewayKey" => 1,
                        "GatewayIP" => "127.0.0.2",
                        "GatewayPort" => 201,
                    ),
                ),
                array(
                    array(
                        "GatewayKey" => 1,
                        "GatewayIP" => "127.0.0.2",
                        "GatewayPort" => 201,
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
        $obj = new SocketsContainer($preload);
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
        $obj = SocketsContainer::singleton($preload);
        $p = SocketsContainer::singleton($preload);
        $this->assertSame($obj, $p);
    }
    /**
    * Data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDeviceID()
    {
        return array(
            array(
                array(
                    "sockets" => array(
                        array(
                            "dummy" => true,
                        ),
                        array(
                            "dummy" => true,
                            "group" => "other",
                        ),
                    ),
                ),
                array(
                    "default" => "5A5A5A0100002000000100205A5A5A010000200000020023",
                    "other" => "5A5A5A0100002000000300225A5A5A010000200000040025",
                ),
                array("default", "other", "another"),
                "000005",
            ),
            array(
                array(
                    "sockets" => array(
                        array(
                            "dummy" => true,
                        ),
                    ),
                ),
                array(
                    "default" => "5A5A5A0100002000000100205A5A5A010000200000020023",
                ),
                "",
                "000003",
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * sqlite instance.
    *
    * @param string $config The configuration to use
    * @param string $read   The group to check
    * @param mixed  $groups Connect before the test connect
    * @param bool   $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDeviceID()
    */
    public function testDeviceID($config, $read, $groups, $expect)
    {
        $conf = ConfigContainer::singleton();
        $conf->forceConfig($config);
        foreach ($read as $group => $read) {
            $sock =& $conf->sockets->getSocket($group);
            $sock->readString = $read;
        }
        $conf->sockets->PacketTimeout = 2;
        $id = $conf->sockets->deviceID($groups);

        $this->assertThat(
            hexdec($id), $this->greaterThanOrEqual(SocketsContainer::MIN_SN)
        );
        $this->assertThat(
            hexdec($id), $this->lessThanOrEqual(SocketsContainer::MAX_SN)
        );

        //$this->assertSame($expect, $id);
        if (is_string($id)) {
            if (!is_array($groups)) {
                $groups = array($groups => $groups);
            }
            foreach ($groups as $group) {
                $sock =& $conf->sockets->getSocket($group);
                if (is_object($sock)) {
                    $this->assertSame(
                        $id, $sock->DeviceID, "DeviceID wrong on group $group"
                    );
                }
            }
        }
    }

    /**
    * Data provider for testForceDeviceID
    *
    * @return array
    */
    public static function dataForceDeviceID()
    {
        return array(
            array(
                array(
                    "sockets" => array(
                        array(
                            "dummy" => true,
                        ),
                        array(
                            "dummy" => true,
                            "group" => "other",
                        ),
                    ),
                ),
                "000005",
                array("default", "other", "another"),
                "000005",
            ),
            array(
                array(
                    "sockets" => array(
                        array(
                            "dummy" => true,
                        ),
                    ),
                ),
                "000003",
                array(),
                "000003",
            ),
        );
    }
    /**
    * Tests to make sure this function fails if
    * someone tries to make a cache from a memory
    * sqlite instance.
    *
    * @param string $config The configuration to use
    * @param string $id     The ID to use
    * @param mixed  $groups Connect before the test connect
    * @param bool   $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataForceDeviceID()
    */
    public function testForceDeviceID($config, $id, $groups, $expect)
    {
        $conf = ConfigContainer::singleton();
        $conf->forceConfig($config);
        $conf->sockets->forceDeviceID($id, $groups);
        foreach ($groups as $group) {
            $sock =& $conf->sockets->getSocket($group);
            if (is_object($sock)) {
                $this->assertSame(
                    $id, $sock->DeviceID, "DeviceID wrong on group $group"
                );
            }
        }
    }

}

?>
