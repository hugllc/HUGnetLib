<?php
/**
 * Tests the epsocket class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

// Call plogTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'plogTest::main');
}
/** Test framework */
require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../database/plog.php';
require_once dirname(__FILE__).'/databaseTest.php';

/**
 * Test class for plog.
 * Generated by PHPUnit on 2007-11-21 at 10:29:05.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PlogTest extends databaseTest
{
    /** The table to use */
    protected $table = "PacketLog";

    /**
     * Runs the test methods of this class.
     *
     * @return void
     *
     * @access public
     * @static
     */
    public static function main() 
    {
        include_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('plogTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     *
     * @access protected
     */
    protected function setUp() 
    {
        parent::setUp();
        $this->o = new plog($this->pdo);
        $this->o->createTable();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     *
     * @access protected
     */
    protected function tearDown() 
    {
        parent::tearDown();
        unset($this->o);
    }

    /**
     * Data provider for testAdd
     *
     * @return array
     */
    public static function dataAdd() 
    {
        return array(
            array(
                array(),
                array(
                    "DeviceKey" => 1, 
                    "ReplyTime" => 2.54, 
                    "GatewayKey" => 5, 
                    "RawData" => "1234", 
                    "Date" => "2007-11-12 14:21:11", 
                    "PacketFrom" => "000020", 
                    "Command" => "01", 
                    "sendCommand" => "5C", 
                    "Type" => "POWERUP"
               ),
                array(
                    "id" => "1", 
                    "DeviceKey" => "1", 
                    "GatewayKey" => "5", 
                    "Date" => "2007-11-12 14:21:11", 
                    "Command" => "01", 
                    "sendCommand" => "5C", 
                    "PacketFrom" => "000020", 
                    "PacketTo" => "", 
                    "RawData" => "1234", 
                    "sentRawData" => "", 
                    "Type" => "POWERUP",
                    "Status" => "NEW",
                    "ReplyTime" => "2.54",
                    "Checked" => "0",
               ),
           ),
        );
    }
    /**
     * test
     *
     * @return void
     *
     * @dataProvider dataAdd
     *
     * @param array $preload Data to preload into the database
     * @param array $info    The info to add to the database
     * @param array $expect  The info to expect returned
     */
    public function testAdd($preload, $info, $expect) 
    {
        $this->load($preload);
        $this->o->add($info);
        $ret = $this->getSingle($expect["id"]);
        $this->assertSame($expect, $ret);
    }

    /**
     * data provider for testFindDriver
     *
     * @return array
     */
    public static function dataPacketLog() 
    {
        return array(
            array(
                array("DeviceKey" => 1, "ReplyTime" => 2.54, "RawData" => "1234", "Time" => 1194898871, "From" => "000020", "Command" => "01", "sendCommand" => "5C"),
                array("GatewayKey" => 5),
                "POWERUP",
                array("DeviceKey" => 1, "ReplyTime" => 2.54, "GatewayKey" => 5, "RawData" => "1234", "Date" => "2007-11-12 14:21:11", "PacketTo" => null, "PacketFrom" => "000020", "Command" => "01", "sendCommand" => "5C", "Type" => "POWERUP"),
           ),
            array(
                array("DeviceKey" => 1, "RawData" => "1234", "Time" => 1194898871, "From" => "000020", "Command" => "01", "sendCommand" => "55"),
                array("GatewayKey" => 5),
                null,
                array("DeviceKey" => 1, "ReplyTime" => 0.0, "GatewayKey" => 5, "RawData" => "1234", "Date" => "2007-11-12 14:21:11", "PacketTo" => null, "PacketFrom" => "000020", "Command" => "01", "sendCommand" => "55", "Type" => "UNSOLICITED"),
           ),
            array(
                array("DeviceKey" => 1, "RawData" => "1234", "Time" => 1194898871, "From" => "000020", "Command" => "01"),
                array("GatewayKey" => 5),
                false,
                array("DeviceKey" => 1, "ReplyTime" => 0.0, "GatewayKey" => 5, "RawData" => "1234", "Date" => "2007-11-12 14:21:11", "PacketTo" => null, "PacketFrom" => "000020", "Command" => '01', "sendCommand" => "  ", "Type" => "UNSOLICITED"),
           ),
            array(
                array("RawData" => "1234", "Time" => 1194898871, "From" => "000020", "Command" => "01", "To" => "000042"),
                array("GatewayKey" => 5, "DeviceKey" => 1),
                false,
                array("DeviceKey" => 1, "ReplyTime" => 0.0, "GatewayKey" => 5, "RawData" => "1234", "Date" => "2007-11-12 14:21:11", "PacketTo" => "000042", "PacketFrom" => "000020", "Command" => '01', "sendCommand" => "  ", "Type" => "UNSOLICITED"),
           ),
        );
    }
    /**
     * Test plog::packetLogSetup
     * 
     * @param array  $Packet  The packet array
     * @param array  $Gateway The gateway array
     * @param string $type    The type of packet it is
     * @param array  $expect  The return value to expect
     *
     * @return void
     *
     * @dataProvider dataPacketLog().
     */
    public function testPacketLogSetup($Packet, $Gateway, $type, $expect) 
    {
        if (is_null($type)) {
            $pkt = plog::PacketLogSetup($Packet, $Gateway);
        } else {
            $pkt = plog::PacketLogSetup($Packet, $Gateway, $type);
        }
        $this->assertSame($expect, $pkt);
    }
}

// Call plogTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'plogTest::main') {
    plogTest::main();
}
?>
