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
 * @category   Analysis
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** This is our base class */
require_once dirname(__FILE__).'/DatabaseTest.php';
/** The gateway code to test */
require_once dirname(__FILE__).'/../../database/VirtualHistory.php';

/**
 * Test class for gateway.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:45:12.
 *
 * @category   Analysis
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class VirtualHistoryTest extends databaseTest
{
    /** The table to use */
    protected $table = "average";

    /** This is data to use for tests */
    protected static $preload = array(
        array(
            "DeviceKey" => 1,
            "Date" => "2007-12-20 02:00:00",
            "Type" => "15MIN",
            "Data0" => 1,
            "Data1" => 2,
            "Data2" => 3
        ),
        array(
            "DeviceKey" => 2,
            "Date" => "2007-12-20 02:00:00",
            "Type" => "15MIN",
            "Data0" => 2,
            "Data1" => 3,
            "Data2" => 4
        ),
        array(
            "DeviceKey" => 3,
            "Date" => "2007-12-20 02:00:00",
            "Type" => "15MIN",
            "Data0" => 3,
            "Data1" => 4,
            "Data2" => 5
        ),
        array(
            "DeviceKey" => 4,
            "Date" => "2007-12-20 02:00:00",
            "Type" => "15MIN", "Data0" => 4,
            "Data1" => 5,
            "Data2" => 6
        ),
        array(
            "DeviceKey" => 5,
            "Date" => "2007-12-20 02:00:00",
            "Type" => "15MIN",
            "Data0" => 5,
            "Data1" => 6,
            "Data2" => 7
        ),
        array(
            "DeviceKey" => 1,
            "Date" => "2007-12-20 02:15:00",
            "Type" => "15MIN",
            "Data0" => 6,
            "Data1" => 7,
            "Data2" => 8
        ),
        array(
            "DeviceKey" => 2,
            "Date" => "2007-12-20 02:15:00",
            "Type" => "15MIN",
            "Data0" => 7,
            "Data1" => 8,
            "Data2" => 9
        ),
        array(
            "DeviceKey" => 3,
            "Date" => "2007-12-20 02:15:00",
            "Type" => "15MIN",
            "Data0" => 8,
            "Data1" => 9,
            "Data2" => 10
        ),
        array(
            "DeviceKey" => 4,
            "Date" => "2007-12-20 02:15:00",
            "Type" => "15MIN",
            "Data0" => 9,
            "Data1" => 10,
            "Data2" => 11
        ),
        array(
            "DeviceKey" => 5,
            "Date" => "2007-12-20 02:15:00",
            "Type" => "15MIN",
            "Data0" => 10,
            "Data1" => 11,
            "Data2" => 12
        ),
    );

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
        $this->o =& HUGnetDB::getInstance("VirtualHistory", $this->config);
        $this->o->createTable($this->table, 3);
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
     * Data provider for testGetDates
     *
     * @return array
     */
    public static function dataGetDates()
    {
        return array(
            array(
                self::$preload,
                array(
                    "DeviceKey" => 6,
                    "ActiveSensors" => 3,
                    "params" => array(
                        "device" => array(1,2,3),
                        "input" => array(3,2,1),
                    ),
                ),
                "2007-12-20 01:30:02",
                "2007-12-20 02:40:02",
                5,
                array(
                    array(
                        "Data0" => 8.0,
                        "data" => array(8.0, 8.0, 8.0),
                        "Data1" => 8.0,
                        "Data2" => 8.0,
                        "DeviceKey" => 6,
                        "Date" => "2007-12-20 02:15:00",
                   ),
                    array(
                        "Data0" => 3.0,
                        "data" => array(3.0, 3.0, 3.0),
                        "Data1" => 3.0,
                        "Data2" => 3.0,
                        "DeviceKey" => 6,
                        "Date" => "2007-12-20 02:00:00",
                   ),
               ),
           ),
            array(
                self::$preload,
                array(
                    "DeviceKey" => 6,
                    "ActiveSensors" => 3,
                    "params" => array(
                        "device" => array(1,1,1),
                        "input" => array(3,2,1),
                    ),
                ),
                "2007-12-20 01:30:02",
                "NOW",
                2,
                array(
                    array(
                        "Data0" => 8.0,
                        "data" => array(8.0, 7.0, 6.0),
                        "Data1" => 7.0,
                        "Data2" => 6.0,
                        "DeviceKey" => 6,
                        "Date" => "2007-12-20 02:15:00",
                   ),
                    array(
                        "Data0" => 3.0,
                        "data" => array(3.0, 2.0, 1.0),
                        "Data1" => 2.0,
                        "Data2" => 1.0,
                        "DeviceKey" => 6,
                        "Date" => "2007-12-20 02:00:00",
                   ),
               ),
           ),
            array(
                self::$preload,
                array("DeviceKey" => 2),
                "2007-12-20 02:30:02",
                "NOW",
                2,
                array(),
           ),
        );
    }
    /**
     * test
     *
     * @param array $preload   Data to preload into the database.
     * @param int   $DeviceKey The key for the device to get the history for
     * @param mixed $startDate The first date chronoligically.  Either a unix date
     *                         or a string
     * @param mixed $endDate   The second date chronologically.  Either a unix
     *                         date or a string
     * @param int   $maxRec    The max number of records to return
     * @param int   $expect    The info to expect returned
     *
     * @return null
     *
     * @dataProvider dataGetDates
     */
    public function testGetDates($preload,
                                 $DeviceKey,
                                 $startDate,
                                 $endDate,
                                 $maxRec,
                                 $expect)
    {
        $this->load($preload);
        $ret = $this->o->GetDates($DeviceKey, $startDate, $endDate, $maxRec);
        $this->assertSame($expect, $ret);
    }


}

?>
