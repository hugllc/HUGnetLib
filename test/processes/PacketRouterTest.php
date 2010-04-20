<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Processes
 * @package    HUGnetLibTest
 * @subpackage Packet
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../processes/PacketRouter.php';
require_once dirname(__FILE__).'/../../tables/PacketSocketTable.php';
require_once dirname(__FILE__).'/../stubs/DummySocketContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Processes
 * @package    HUGnetLibTest
 * @subpackage Packet
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PacketRouterTest extends PHPUnit_Framework_TestCase
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
        $config = array(
            "sockets" => array(
                array(
                    "dummy" => true,
                    "group" => "other",
                ),
                array(
                    "dummy" => true,
                ),
                array(
                    "dummy" => true,
                    "group" => "third",
                ),
            ),
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        foreach ($this->config->sockets->groups() as $group) {
            $this->socket[$group] = &$this->config->sockets->getSocket($group);
        }
        $this->o = new PacketRouter();
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
        $this->o = null;
        $this->config = null;
    }
    /**
    * data provider for testConstructor
    *
    * @return array
    */
    public static function dataConstructor()
    {
        return array(
           array(
                array(),
                array(
                    "MaxPackets" => 5,
                    "groups" => array(
                        "other" => "other",
                        "default" => "default",
                        "third" => "third",
                    ),
                    "Timeout" => 5,
                    "Retries" => 3,
                ),
            ),
           array(
                array(
                    "MaxPackets" => 3,
                    "groups" => array(
                        "other" => "other",
                        "third" => "third",
                    ),
                    "Timeout" => 3,
                    "Retries" => 5,
                ),
                array(
                    "MaxPackets" => 3,
                    "groups" => array(
                        "other" => "other",
                        "third" => "third",
                    ),
                    "Timeout" => 3,
                    "Retries" => 5,
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The value to preload
    * @param array $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $expect)
    {
        $o = new PacketRouter($preload);
        $ret = $this->readAttribute($o, "data");
        $this->assertSame($expect, $ret);
        // Check the configuration is set correctly
        $config = $this->readAttribute($o, "myConfig");
        $this->assertSame("ConfigContainer", get_class($config));
        $packets = $this->readAttribute($o, "myPackets");
        foreach ($expect["groups"] as $group) {
            $this->assertSame("PacketContainer", get_class($packets[$group]));
            $this->assertSame($group, $packets[$group]->group);
            $this->assertFalse($packets[$group]->GetReply);
        }
    }
    /**
    * data provider for testSend
    *
    * @return array
    */
    public static function dataSend()
    {
        return array(
            array(
                array(),
                new PacketContainer(
                    array(
                        "To" => "123456",
                        "From" => "654321",
                        "Command" => "5C",
                        "Data" => "0102030405",
                        "group" => "default",
                    )
                ),
                array(
                    "other" => "other",
                    "third" => "third",
                ),
                "5A5A5A5C1234566543210501020304052F",
            ),
            array(
                array(),
                new PacketContainer(
                    array(
                        "To" => "000456",
                        "From" => "654000",
                        "Command" => "55",
                        "Data" => "0102030405",
                        "group" => "default",
                    )
                ),
                array(
                    "other" => "other",
                    "third" => "third",
                ),
                "5A5A5A5500045665400005010203040526",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The value to preload
    * @param object $pkt     The packet to send out
    * @param array  $groups  The groups to check
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataSend
    */
    public function testSend($preload, $pkt, $groups, $expect)
    {
        $retries = $pkt->Retries;
        $this->o->fromAny($preload);
        $oldGroup = $pkt->group;
        $this->o->send($pkt, $groups);
        foreach ($groups as $group) {
            $this->assertSame($expect, $this->socket[$group]->writeString);
        }
        $this->assertThat(
            $pkt->Timeout,
            $this->greaterThan(time()-100),
            "Timeout is not being set correctly"
        );
        // This makes sure that the group has not changed.
        $this->assertSame(
            $pkt->group,
            $oldGroup,
            "The group on the packet has changed!"
        );
        // This makes sure that the group has not changed.
        $this->assertSame(
            $retries - 1,
            $pkt->Retries,
            "The retries are wrong"
        );
    }

    /**
    * data provider for testRead
    *
    * @return array
    */
    public static function dataRead()
    {
        return array(
            // Two packets, one each interface
            array(
                array(),
                array(
                    "other" => "5A5A5A5C1234566543210501020304052F"
                    ."5A5A5A5500045665400005010203040526",
                    "third" => "5A5A5A5500045665400005010203040526",
                ),
                array(
                    "other" => "other",
                    "third" => "third",
                ),
                array(
                    array(
                        "To" => "123456",
                        "From" => "654321",
                        "Command" => "5C",
                        "Length" => 5,
                        "Data" => "0102030405",
                        "Type" => "CONFIG",
                        "Reply" => null,
                        "Checksum" => "2F",
                        "Retries" => 3,
                        "GetReply" => false,
                        "group" => "other",
                    ),
                    array(
                        "To" => "000456",
                        "From" => "654000",
                        "Command" => "55",
                        "Length" => 5,
                        "Data" => "0102030405",
                        "Type" => "SENSORREAD",
                        "Reply" => null,
                        "Checksum" => "26",
                        "Retries" => 3,
                        "GetReply" => false,
                        "group" => "third",
                    ),
                ),
                array(
                    "654321" => "other",
                    "654000" => "third",
                ),
            ),
            // Nothing
            array(
                array(),
                array(),
                array(),
                array(),
                array(),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The value to preload
    * @param array  $pkts    The packet strings for the function to read
    * @param array  $groups  The groups to check
    * @param string $expect  The expected return
    * @param array  $routes  The routes to expect
    *
    * @return null
    *
    * @dataProvider dataRead
    */
    public function testRead($preload, $pkts, $groups, $expect, $routes)
    {
        foreach ($groups as $group) {
            $this->socket[$group]->readString .= $pkts[$group];
        }
        $this->o->fromAny($preload);
        $this->o->read();
        $p = &$this->readAttribute($this->o, "PacketQueue");
        $this->assertType("array", $p);
        $ret = array();
        foreach (array_keys((array)$p) as $k) {
            $data = $this->readAttribute($p[$k], "data");
            unset($data["Date"]);
            unset($data["Time"]);
            $this->assertThat(
                $data["Timeout"],
                $this->greaterThan(time()-100),
                "Timeout is not being set correctly"
            );
            unset($data["Timeout"]);
            $ret[] = $data;
        }
        $this->assertSame($expect, $ret);
        $this->assertAttributeSame($routes, "Routes", $this->o);
    }

    /**
    * data provider for testRoute
    *
    * @return array
    */
    public static function dataRoute()
    {
        return array(
            // Overrun the buffer
            array(
                array(),
                array(
                    new PacketContainer(
                        array(
                            "To" => "123456",
                            "From" => "654321",
                            "Command" => "5C",
                            "Data" => "0102030405",
                            "group" => "default",
                        )
                    ),
                    new PacketContainer(
                        array(
                            "To" => "023456",
                            "From" => "054321",
                            "Command" => "5C",
                            "Data" => "0102030405",
                            "group" => "default",
                        )
                    ),
                    new PacketContainer(
                        array(
                            "To" => "103456",
                            "From" => "604321",
                            "Command" => "5C",
                            "Data" => "0102030405",
                            "group" => "default",
                        )
                    ),
                    new PacketContainer(
                        array(
                            "To" => "120456",
                            "From" => "650321",
                            "Command" => "5C",
                            "Data" => "0102030405",
                            "group" => "default",
                        )
                    ),
                    new PacketContainer(
                        array(
                            "To" => "123056",
                            "From" => "654021",
                            "Command" => "5C",
                            "Data" => "0102030405",
                            "group" => "default",
                        )
                    ),
                    new PacketContainer(
                        array(
                            "To" => "123406",
                            "From" => "654301",
                            "Command" => "5C",
                            "Data" => "0102030405",
                            "group" => "default",
                        )
                    ),
                ),
                array(
                    "other" => "other",
                    "third" => "third",
                ),
                "5A5A5A5C1234566543210501020304052F"
                ."5A5A5A5C0234560543210501020304055F"
                ."5A5A5A5C10345660432105010203040528"
                ."5A5A5A5C1204566503210501020304055F"
                ."5A5A5A5C12305665402105010203040528",
            ),
            array(
                array(),
                array(
                    new PacketContainer(
                        array(
                            "To" => "000456",
                            "From" => "654000",
                            "Command" => "55",
                            "Data" => "0102030405",
                            "group" => "default",
                        )
                    ),
                ),
                array(
                    "other" => "other",
                    "third" => "third",
                ),
                "5A5A5A5500045665400005010203040526",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload The value to preload
    * @param object $pkts    The packet to send out
    * @param array  $groups  The groups to check
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataRoute
    */
    public function testRoute($preload, $pkts, $groups, $expect)
    {
        $this->o->fromAny($preload);
        foreach (array_keys((array)$pkts) as $key) {
            $this->o->queue($pkts[$key]);
        }
        $this->o->route();
        foreach ($groups as $group) {
            $this->assertSame($expect, $this->socket[$group]->writeString);
        }
        $p = &$this->readAttribute($this->o, "PacketBuffer");
        $q = &$this->readAttribute($this->o, "PacketQueue");
        if (count($pkts) > $this->o->MaxPackets) {
            $this->assertSame(
                $this->o->MaxPackets,
                count($p),
                "There should be ".$this->o->MaxPackets." packet in the buffer"
            );
            $cq = count($pkts) - $this->o->MaxPackets;
            $this->assertSame(
                $cq,
                count($q),
                "There should be ".$cq." packet in the queue"
            );
        } else {
            $this->assertSame(
                count($pkts),
                count($p),
                "There should be ".count($pkts)." packet in the buffer"
            );

        }
    }

}

?>
