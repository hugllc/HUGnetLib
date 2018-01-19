<?php
/**
 * This runs all of the tests associated with HUGnetLib.
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
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\drivers;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBase.php";
/** This is a required class */
require_once CODE_BASE.'devices/drivers/E10460200.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class E10460200Test extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "E10460200";
    /** This is the device */
    protected $device = null;
    
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
        $this->device  = new \HUGnet\DummyTable("Device");
        $this->device->resetMock(array());
        $this->o = \HUGnet\devices\Driver::factory("E10460200", $this->device);
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
        parent::tearDown();
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGet()
    {
        return array(
            array(
                "ThisIsABadName",
                null,
            ),
            array(
                "packetTimeout",
                5,
            ),
            array(
                "virtualSensors",
                0,
            ),
            array(
                "physicalSensors",
                0,
            ),
            array(
                 "totalSensors",
                0,
            ),
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecode()
    {
        return array(
            array( // #0
                array(
                    "Device" => array(
                        "input" => new \HUGnet\DummyBase("Sensor"),
                    )
                ),
                "010203040506",
                array(
                    "Device" => array(
                        "input" => array(
                            array(0),
                            array(1),
                            array(2),
                            array(3),
                            array(4),
                            array(5),
                            array(6),
                            array(7),
                            array(8),
                            array(9),
                            array(10),
                            array(11),
                            array(12),
                            array(13),
                            array(14),
                            array(15),
                            array(16),
                            array(17),
                            array(18),
                            array(19),
                            array(20),
                            array(21),
                            array(22),
                            array(23),
                            array(24),
                            array(25),
                            array(26),
                            array(27),
                            array(28),
                            array(29),
                            array(30),
                            array(31),
                            array(32),
                            array(33),
                            array(34),
                            array(35),
                            array(36),
                            array(37),
                            array(38),
                            array(39),
                        ),
                    ),
                    'Sensor' => array(
                        'get' => array(
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                        ),
                    ),
                ),
            ),
            array( // #1 String not big enough
                array(
                    "Device" => array(
                        "input" => new \HUGnet\DummyBase("Sensor"),
                    )
                ),
                "0102030405",
                array(
                    "Device" => array(
                        "input" => array(
                            array(0),
                            array(1),
                            array(2),
                            array(3),
                            array(4),
                            array(5),
                            array(6),
                            array(7),
                            array(8),
                            array(9),
                            array(10),
                            array(11),
                            array(12),
                            array(13),
                            array(14),
                            array(15),
                            array(16),
                            array(17),
                            array(18),
                            array(19),
                            array(20),
                            array(21),
                            array(22),
                            array(23),
                            array(24),
                            array(25),
                            array(26),
                            array(27),
                            array(28),
                            array(29),
                            array(30),
                            array(31),
                            array(32),
                            array(33),
                            array(34),
                            array(35),
                            array(36),
                            array(37),
                            array(38),
                            array(39),
                        ),
                    ),
                    'Sensor' => array(
                        'get' => array(
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                            array("type"),
                        ),
                    ),
                ),
            ),
            array( // #2 String empty
                array(
                    "Device" => array(
                        "input" => new \HUGnet\DummyBase("Sensor"),
                    )
                ),
                "",
                array(
                ),
            ),
            array( // #3 Inputs Found
                array(
                    "Device" => array(
                        "input" => array(
                            0 => new \HUGnet\DummyBase("Sensor0"),
                            1 => new \HUGnet\DummyBase("Sensor1"),
                            2 => new \HUGnet\DummyBase("Sensor2"),
                            3 => new \HUGnet\DummyBase("Sensor3"),
                            4 => new \HUGnet\DummyBase("Sensor4"),
                            5 => new \HUGnet\DummyBase("Sensor5"),
                            6 => new \HUGnet\DummyBase("Sensor6"),
                            7 => new \HUGnet\DummyBase("Sensor7"),
                            8 => new \HUGnet\DummyBase("Sensor8"),
                        ),
                    ),
                    'Sensor0' => array(
                        'get' => array(
                            "type" => "asdf",
                        ),
                    ),
                    'Sensor1' => array(
                        'get' => array(
                            "type" => "XMegaTemp",
                        ),
                    ),
                    'Sensor2' => array(
                        'get' => array(
                            "type" => "XMegaTemp",
                        ),
                    ),
                    'Sensor3' => array(
                        'get' => array(
                            "id" => 6,
                        ),
                    ),
                    'Sensor4' => array(
                        'get' => array(
                            "id" => 7,
                        ),
                    ),
                    'Sensor5' => array(
                        'get' => array(
                            "id" => 8,
                        ),
                    ),
                    'Sensor6' => array(
                        'get' => array(
                            "id" => 9,
                        ),
                    ),
                    'Sensor7' => array(
                        'get' => array(
                            "id" => 10,
                        ),
                    ),
                    'Sensor8' => array(
                        'get' => array(
                            "id" => 11,
                        ),
                    ),
                ),
                "010203040506",
                array(
                    "Device" => array(
                        "input" => array(
                            array(0),
                            array(1),
                            array(2),
                        ),
                    ),
                    'Sensor0' => array(
                        'get' => array(
                            array("type"),
                        ),
                    ),
                    'Sensor1' => array(
                        'get' => array(
                            array("type"),
                        ),
                        'setExtra' => array(
                            array(0, 1),
                            array(1, 770),
                        ),
                        'store' => array(
                            array(),
                        ),
                    ),
                    'Sensor2' => array(
                        'get' => array(
                            array("type"),
                        ),
                        'setExtra' => array(
                            array(0, 4),
                            array(1, 1541),
                        ),
                        'store' => array(
                            array(),
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $string The setup string to test
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDecode
    */
    public function testDecode($mocks, $string, $expect)
    {
        $this->device->resetMock($mocks);
        $this->o->decode($string, $device);
        $ret = $this->device->retrieve();
        $this->assertEquals($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataEncode()
    {
        return array(
            array( // #0
                array(
                    "Device" => array(
                        "input" => array(
                            0 => new \HUGnet\DummyBase("Sensor0"),
                            1 => new \HUGnet\DummyBase("Sensor1"),
                            2 => new \HUGnet\DummyBase("Sensor2"),
                            3 => new \HUGnet\DummyBase("Sensor3"),
                            4 => new \HUGnet\DummyBase("Sensor4"),
                            5 => new \HUGnet\DummyBase("Sensor5"),
                            6 => new \HUGnet\DummyBase("Sensor6"),
                            7 => new \HUGnet\DummyBase("Sensor7"),
                            8 => new \HUGnet\DummyBase("Sensor8"),
                        ),
                        "getParam" => array(
                            "TimeConstant" => 7,
                        ),
                    ),
                    'Sensor0' => array(
                        'get' => array(
                            "id" => 3,
                        ),
                    ),
                    'Sensor1' => array(
                        'get' => array(
                            "id" => 4,
                        ),
                    ),
                    'Sensor2' => array(
                        'get' => array(
                            "id" => 5,
                        ),
                    ),
                    'Sensor3' => array(
                        'get' => array(
                            "id" => 6,
                        ),
                    ),
                    'Sensor4' => array(
                        'get' => array(
                            "id" => 7,
                        ),
                    ),
                    'Sensor5' => array(
                        'get' => array(
                            "id" => 8,
                        ),
                    ),
                    'Sensor6' => array(
                        'get' => array(
                            "id" => 9,
                        ),
                    ),
                    'Sensor7' => array(
                        'get' => array(
                            "id" => 10,
                        ),
                    ),
                    'Sensor8' => array(
                        'get' => array(
                            "id" => 11,
                        ),
                    ),
                ),
                true,
                "",
            ),
            array( // #0
                array(
                    "Device" => array(
                        "input" => array(
                            0 => new \HUGnet\DummyBase("Sensor0"),
                            1 => new \HUGnet\DummyBase("Sensor1"),
                            2 => new \HUGnet\DummyBase("Sensor2"),
                            3 => new \HUGnet\DummyBase("Sensor3"),
                            4 => new \HUGnet\DummyBase("Sensor4"),
                            5 => new \HUGnet\DummyBase("Sensor5"),
                            6 => new \HUGnet\DummyBase("Sensor6"),
                            7 => new \HUGnet\DummyBase("Sensor7"),
                            8 => new \HUGnet\DummyBase("Sensor8"),
                        ),
                        "getParam" => array(
                            "TimeConstant" => 7,
                        ),
                    ),
                    'Sensor0' => array(
                        'get' => array(
                            "id" => 3,
                        ),
                    ),
                    'Sensor1' => array(
                        'get' => array(
                            "id" => 4,
                        ),
                    ),
                    'Sensor2' => array(
                        'get' => array(
                            "id" => 5,
                        ),
                    ),
                    'Sensor3' => array(
                        'get' => array(
                            "id" => 6,
                        ),
                    ),
                    'Sensor4' => array(
                        'get' => array(
                            "id" => 7,
                        ),
                    ),
                    'Sensor5' => array(
                        'get' => array(
                            "id" => 8,
                        ),
                    ),
                    'Sensor6' => array(
                        'get' => array(
                            "id" => 9,
                        ),
                    ),
                    'Sensor7' => array(
                        'get' => array(
                            "id" => 10,
                        ),
                    ),
                    'Sensor8' => array(
                        'get' => array(
                            "id" => 11,
                        ),
                    ),
                ),
                false,
                "",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $mocks     The value to preload into the mocks
    * @param bool  $showFixed Show the fixed portion of the data
    * @param array $expect    The expected return
    *
    * @return null
    *
    * @dataProvider dataEncode
    */
    public function testEncode($mocks, $showFixed, $expect)
    {
        $this->device->resetMock($mocks);
        $ret = $this->o->encode($showFixed);
        $this->assertSame($expect, $ret);
    }
}
?>
