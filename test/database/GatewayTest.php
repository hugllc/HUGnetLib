<?php
/**
 * Tests the gateway class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2009 Hunt Utilities Group, LLC
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
 * @category   Gateways
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** Test Case */
require_once "PHPUnit/Framework/TestCase.php";
/** Test Suite */
require_once "PHPUnit/Framework/TestSuite.php";

/** The gateway code to test */
require_once dirname(__FILE__).'/../../database/Gateway.php';
/** This is our base class */
require_once dirname(__FILE__).'/DatabaseTest.php';

/**
 * Test class for gateway.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:45:12.
 *
 * @category   Gateways
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class GatewayTest extends databaseTest
{
    /** The table to use */
    protected $table = "gateways";
    /** @var string The name of the id column */
    protected $id = "GatewayKey";

    /**
     * Runs the test methods of this class.
     *
     * @return null
     *
     * @access public
     * @static
     */
    public static function main()
    {
        include_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("gatewayTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

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
        parent::setUp();
        $this->o =& HUGnetDB::getInstance("Gateway", $this->config);
        $this->o->createTable();

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
        parent::tearDown();
        unset($this->o);
    }

    /**
     * Data provider for testFind()
     *
     * @return array
     */
    public static function dataFind()
    {
        $stuff = posix_uname();
        $ip    = gethostbyname($stuff["nodename"]);

        return array(
            array(array(), false),
            array(
                array(
                    array(
                        "GatewayKey" => 1,
                        "GatewayIP" => "127.0.0.2",
                        "GatewayName" => "crazyharry",
                        "GatewayLocation" => "asdf",
                        "database" => "HUGnet",
                        "FirmwareStatus" => "BAD",
                        "isVisible" => "1",
                   ),
                    array(
                        "GatewayKey" => 2,
                        "GatewayIP" => $ip,
                        "GatewayName" => "thing2",
                        "GatewayLocation" => "asdf",
                        "database" => "HUGnet",
                        "FirmwareStatus" => "BETA",
                        "isVisible" => "1",
                   ),
               ),
                array(
                    "GatewayKey" => "2",
                    "GatewayIP" => $ip,
                    "GatewayName" => "thing2",
                    "GatewayLocation" => "asdf",
                    "database" => "HUGnet",
                    "FirmwareStatus" => "BETA",
                    "isVisible" => "1",
               ),
           ),

        );
    }
    /**
     * Tests gateway::find()
     *
     * @param array $preload The data to preload into the database
     * @param array $expect  The return value to expect
     *
     * @return null
     *
     * @dataProvider dataFind().
     */
    public function testFind($preload, $expect)
    {
        $this->load($preload);
        $ret = $this->o->find();
        $this->assertSame($expect, $ret);
    }


    /**
     * Data provider for testFind()
     *
     * @return array
     */
    public static function dataEncodeIP()
    {
        return array(
            array(
                array(
                    "hello" => "182.351.253.211",
                    "asdf" => "234.512.123.151",
                ),
                "hello:182.351.253.211\nasdf:234.512.123.151\n",
            ),
            array(
                "",
                "",
            ),
            array(
                "192.168.0.1",
                "192.168.0.1",
            ),
        );
    }
    /**
     * Data provider for testFind()
     *
     * @return array
     */
    public static function dataDecodeIP()
    {
        return array(
            array(
                "hello:182.351.253.211\nasdf:234.512.123.151\n\n",
                array(
                    "hello" => "182.351.253.211",
                    "asdf" => "234.512.123.151",
                ),
            ),
            array(
                array(),
                array(),
            ),
            array(
                "192.168.0.1",
                "192.168.0.1",
            ),
        );
    }
    /**
     * Tests gateway::find()
     *
     * @param array $IP      The IP address to test
     * @param array $expect  The return value to expect
     *
     * @return null
     *
     * @dataProvider dataDecodeIP().
     */
    public function testDecodeIP($IP, $expect)
    {
        $ret = $this->o->decodeIP($IP);
        $this->assertSame($expect, $ret);
    }
    /**
     * Tests gateway::find()
     *
     * @param array $IP      The IP address to test
     * @param array $expect  The return value to expect
     *
     * @return null
     *
     * @dataProvider dataEncodeIP().
     */
    public function testEncodeIP($IP, $expect)
    {
        $ret = $this->o->encodeIP($IP);
        $this->assertSame($expect, $ret);
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
                    "GatewayKey" => "3",
                    "GatewayIP" => array("test" => "192.168.0.1"),
                    "GatewayName" => "There",
                    "GatewayLocation" => "Here",
                    "database" => "",
                    "FirmwareStatus" => "RELEASE",
                    "isVisible" => "1",
                ),
                array(
                    "GatewayKey" => "3",
                    "GatewayIP" => "test:192.168.0.1\n",
                    "GatewayName" => "There",
                    "GatewayLocation" => "Here",
                    "database" => "",
                    "FirmwareStatus" => "RELEASE",
                    "isVisible" => "1",
                ),
           ),
        );
    }
    /**
     * test
     *
     * @param array $preload Data to preload into the database
     * @param array $info    The info to add to the database
     * @param array $expect  The info to expect returned
     *
     * @return null
     *
     * @dataProvider dataAdd
     */
    public function testAdd($preload, $info, $expect)
    {
        $this->load($preload);
        $this->o->add($info);
        $ret = $this->getSingle($expect[$this->id]);
        $this->assertSame($expect, $ret);
    }
    /**
     * Data provider for testGetWhere
     *
     * @return array
     */
    public static function dataGetWhere()
    {
        return array(
            array(
                array(
                    array(
                        "GatewayKey" => "3",
                        "GatewayIP" => "test:192.168.0.1\n",
                        "GatewayName" => "There",
                        "GatewayLocation" => "Here",
                        "database" => "",
                        "FirmwareStatus" => "RELEASE",
                        "isVisible" => "1",
                    ),
                ),
                "GatewayKey = ?",
                array(3),
                array(
                    array(
                        "GatewayKey" => "3",
                        "GatewayIP" => array("test" => "192.168.0.1"),
                        "GatewayName" => "There",
                        "GatewayLocation" => "Here",
                        "database" => "",
                        "FirmwareStatus" => "RELEASE",
                        "isVisible" => "1",
                    ),
                ),
           ),
        );
    }
    /**
     * test
     *
     * @param array  $preload Data to preload into the database
     * @param string $where   The database key to get the record from
     * @param array  $data    The data to send with the query
     * @param array  $expect  The info to expect returned
     *
     * @return null
     *
     * @dataProvider dataGetWhere
     */
    public function testGetWhere($preload, $where, $data, $expect)
    {
        $this->load($preload);
        $ret = $this->o->getWhere($where, $data);
        $this->assertSame($expect, $ret);
    }
    /**
     * Data provider for testUpdate
     *
     * @return array
     */
    public static function dataUpdateWhere()
    {
        return array(
            array(
                array(
                    array(
                        "GatewayKey" => "3",
                        "GatewayIP" => "asdf:193.162.238.3",
                        "GatewayName" => "There",
                        "GatewayLocation" => "Here",
                        "database" => "",
                        "FirmwareStatus" => "RELEASE",
                        "isVisible" => "1",
                    ),
                ),
                array(
                    "GatewayKey" => "3",
                    "GatewayIP" => array("test" => "192.168.0.1"),
                    "GatewayName" => "There",
                    "GatewayLocation" => "Here",
                    "database" => "",
                    "FirmwareStatus" => "RELEASE",
                    "isVisible" => "1",
                ),
                "GatewayKey = ?",
                array(3),
                array(
                    "GatewayKey" => "3",
                    "GatewayIP" => "test:192.168.0.1\n",
                    "GatewayName" => "There",
                    "GatewayLocation" => "Here",
                    "database" => "",
                    "FirmwareStatus" => "RELEASE",
                    "isVisible" => "1",
                ),

           ),
        );
    }
    /**
     * test
     *
     * @param array  $preload Data to preload into the database
     * @param array  $info    The info to add to the database
     * @param string $where   The database key to get the record from
     * @param bool   $data    What the function should return
     * @param array  $expect  The info to expect returned
     *
     * @return null
     *
     * @dataProvider dataUpdateWhere
     */
    public function testUpdateWhere($preload, $info, $where, $data, $expect)
    {
        $this->load($preload);
        $this->o->updateWhere($info, $where, $data);
        $ret = $this->getSingle($expect[$this->id]);
        $this->assertSame($expect, $ret);
    }

}

/**
 * Mock gateway class
 *
 * @category   Gateways
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class GatewayMock extends gateway
{
    /**
     * Mock getAll that always returns localhost.
     *
     * @return array
     */
    function getAll()
    {
        return array(
            array(
                "GatewayKey" => 1,
                "GatewayName" => "Test Gateway",
                "GatewayIP" => "127.0.0.1",
                "GatewayPort" => "2000",
           ),
        );
    }
}
?>
