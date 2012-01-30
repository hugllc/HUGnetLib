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
 * @subpackage SuiteProcesses
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is a required class */
require_once CODE_BASE.'processes/PacketRouter.php';
/** This is a required class */
require_once CODE_BASE.'tables/PacketSocketTable.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySocketContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteProcesses
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
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
                    "group" => "default",
                ),
                array(
                    "dummy" => true,
                    "group" => "third",
                ),
            ),
            "script_gateway" => 4,
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->config->sockets->forceDeviceID("000019");
        $this->pdo = &$this->config->servers->getPDO();
        foreach ($this->config->sockets->groups() as $group) {
            $this->socket[$group] = &$this->config->sockets->getSocket($group);
        }
        $this->device = array(
            "id"         => 0x000019,
            "DeviceID"   => "000019",
            "HWPartNum"  => "0039-26-04-P",
            "FWPartNum"  => "0039-26-04-P",
        );

        $this->o = new PacketRouter(array(), $this->device);
        $this->d = $this->readAttribute($this->o, "myDevice");
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
        // Trap the exit signal and exit gracefully
        if (function_exists("pcntl_signal")) {
            pcntl_signal(SIGINT, SIG_DFL);
        }
        $stmt = $this->pdo->query("DELETE FROM `datacollectors`");
    }
    /**
    * Tests for exceptions
    *
    * @expectedException RuntimeException
    *
    * @return null
    */
    public function testConstructTableExec()
    {
        $config = array(

        );
        $this->config->forceConfig($config);
        $obj = new PacketRouter($array, $this->device);
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
                    "groups" => array(
                        "other" => "other",
                        "default" => "default",
                        "third" => "third",
                    ),
                    "GatewayKey" => 4,
                ),
                array(
                    array("id" => "25"),
                ),
            ),
           array(
                array(
                    "groups" => array(
                        "other" => "other",
                        "third" => "third",
                    ),
                    "GatewayKey" => 6,
                ),
                array(
                    "groups" => array(
                        "other" => "other",
                        "third" => "third",
                    ),
                    "GatewayKey" => 6,
                ),
                array(
                    array("id" => "25"),
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload       The value to preload
    * @param array $expect        The expected return
    * @param array $dataCollector The data collector id to expect
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $expect, $dataCollector)
    {
        $stmt = $this->pdo->query("DELETE FROM `datacollectors`");
        $obj = new PacketRouter($preload, $this->device);
        foreach ($expect as $key => $value) {
            $this->assertSame($value, $obj->$key, "Bad Value in key $key");
        }
        // Check the configuration is set correctly
        $config = $this->readAttribute($obj, "myConfig");
        $this->assertSame("ConfigContainer", get_class($config));
        $stmt = $this->pdo->query("SELECT `id` FROM `datacollectors`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame($dataCollector, $rows);

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
    }

    /**
    * data provider for testRead
    *
    * @return array
    */
    public static function dataRoute()
    {
        return array(
            // Unsolicited packet
            array(
                array(),
                true,
                array(
                    array(
                        "other" => (string)new PacketContainer(
                            array(
                                "To" => "000000",
                                "From" => "123456",
                                "Command" => "5E",
                            )
                        ),
                    ),
                ),
                array(
                    "other"   => "",
                    "third"   => (string)new PacketContainer(
                        array(
                            "To" => "000000",
                            "From" => "123456",
                            "Command" => "5E",
                        )
                    ),
                    "default" => (string)new PacketContainer(
                        array(
                            "To" => "000000",
                            "From" => "123456",
                            "Command" => "5E",
                        )
                    ),
                ),
                array(
                    "123456" => "other",
                ),
            ),
            // Packet from a gateway
            array(
                array(),
                true,
                array(
                    array(
                        "other" => (string)new PacketContainer(
                            array(
                                "To" => "000126",
                                "From" => "FE1234",
                                "Command" => "5C",
                            )
                        ),
                    ),
                ),
                array(
                    "other"   => "",
                    "third"   => (string)new PacketContainer(
                        array(
                            "To" => "000126",
                            "From" => "FE1234",
                            "Command" => "5C",
                        )
                    ),
                    "default" => (string)new PacketContainer(
                        array(
                            "To" => "000126",
                            "From" => "FE1234",
                            "Command" => "5C",
                        )
                    ),
                ),
                array(
                    "FE1234" => "other",
                ),
            ),
            // One Packet to me.  Sending a reply
            array(
                array(),
                true,
                array(
                    0 => array(
                        "other" => "5A5A5A03000019000000001A",
                    )
                ),
                array(
                    "other"   => "5A5A5A010000000000190018",
                    "third"   => "",
                    "default" => ""
                ),
                array(
                ),
            ),
            // One Packet to through then a reply
            array(
                array(),
                true,
                array(
                    0 => array(
                        "other" => "5A5A5A030000E10000200401020304C2",
                    ),
                    1 => array(
                        "third" => "5A5A5A010000200000E10401020304C0",
                    ),
                ),
                array(
                    "other"   => "5A5A5A010000200000E10401020304C0",
                    "third"   => "5A5A5A030000E10000200401020304C2",
                    "default" => "5A5A5A030000E10000200401020304C2",
                ),
                array(
                    "000020" => "other",
                    "0000E1" => "third",
                ),
            ),
            // Two packets, one each interface
            array(
                array(),
                true,
                array(
                    0 => array(
                        "other" => "5A5A5A5C1234566543210501020304052F",
                        "third" => "5A5A5A5500045665400005010203040526",
                    )
                ),
                array(
                    "other"   => "5A5A5A5500045665400005010203040526",
                    "third"   => "5A5A5A5C1234566543210501020304052F",
                    "default" => "5A5A5A5C1234566543210501020304052F"
                        ."5A5A5A5500045665400005010203040526",
                ),
                array(
                    "654321" => "other",
                    "654000" => "third",
                ),
            ),
            // Many Packets
            array(
                array(),
                true,
                array(
                    0 => array(
                        "other" => "5A5A5A5C1234566543210501020304052F", // P 1
                        "third" => "5A5A5A5500045665400005010203040526", // P 2
                    ),
                    1 => array(
                        "default" => "5A5A5A016543211234560601020304050677", // P 3
                        "other" => "5A5A5A016540000004560606050403020177", // P 4
                    ),
                    2 => array(
                        "other" => "5A5A5A036540000004560074",  // P 5
                    ),
                ),
                array(
                    "other"   => "5A5A5A5500045665400005010203040526" // P 2
                        ."5A5A5A016543211234560601020304050677",      // P 3
                    "third"   => "5A5A5A5C1234566543210501020304052F" // P 1
                        ."5A5A5A016540000004560606050403020177"       // P 4
                        ."5A5A5A036540000004560074",                  // P 5
                    "default" => "5A5A5A5C1234566543210501020304052F" // P 1
                        ."5A5A5A5500045665400005010203040526"         // P 2
                        ."5A5A5A036540000004560074",                  // P 5
                ),
                array(
                    "654321" => "other",
                    "654000" => "third",
                    "000456" => "other",
                    "123456" => "default",
                ),
            ),
            // Nothing
            array(
                array(),
                true,
                array(),
                array(),
                array(),
            ),
            // It should exit before it does anything.
            array(
                array(),
                false,
                array(
                    0 => array(
                        "other" => "5A5A5A5C1234566543210501020304052F", // P 1
                        "third" => "5A5A5A5500045665400005010203040526", // P 2
                    ),
                    1 => array(
                        "default" => "5A5A5A016543211234560601020304050677", // P 3
                        "other" => "5A5A5A016540000004560606050403020177", // P 4
                    ),
                    2 => array(
                        "other" => "5A5A5A036540000004560074",  // P 5
                    ),
                ),
                array(),
                array(),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The value to preload
    * @param bool  $loop    What to set the loop variable to
    * @param array $read    The packet strings for the function to read
    * @param array $write   The packet strings that the function will write
    * @param array $routes  The routes to expect
    *
    * @return null
    *
    * @dataProvider dataRoute
    */
    public function testRoute($preload, $loop, $read, $write, $routes)
    {
        $this->o->fromAny($preload);
        $this->o->loop = $loop;
        $index = 0;
        $start = time();
        do {
            foreach ((array)$read[$index] as $group => $string) {
                $this->socket[$group]->readString = $string;
            }
            $index++;
        } while ($this->o->route() > 0);
        foreach ($write as $group => $string) {
            $this->assertSame(
                $string, $this->socket[$group]->writeString,
                "$group has the wrong string"
            );
        }
        $this->assertAttributeSame($routes, "Routes", $this->o);
    }
    /**
    * data provider for testPowerup
    *
    * @return array
    */
    public static function dataPowerup()
    {
        return array(
            // Nothing
            array(
                array(),
                array(),
                array(
                    "default" => "5A5A5A5E0000000000190047",
                    "other" => "5A5A5A5E0000000000190047",
                    "third" => "5A5A5A5E0000000000190047",
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The value to preload
    * @param array $read    The packet strings for the function to read
    * @param array $write   The packet strings that the function will write
    *
    * @return null
    *
    * @dataProvider dataPowerup
    */
    public function testPowerup($preload, $read, $write)
    {
        $this->o->fromAny($preload);
        $index = 0;
        do {
            foreach ((array)$read[$index] as $group => $string) {
                $this->socket[$group]->readString = $string;
            }
            $index++;
        } while ($this->o->powerup() > 0);
        foreach ($write as $group => $string) {
            $this->assertSame(
                $string, $this->socket[$group]->writeString,
                "$group has the wrong string"
            );
        }
        $dev = $this->readAttribute($this->o, "myDevice");
        $cmd = PacketContainer::COMMAND_POWERUP;
        $this->assertSame(
            1,
            $dev->params->ProcessInfo["unsolicited"][$cmd],
            "Incrementing powerup count failed"
        );
    }


}

?>
