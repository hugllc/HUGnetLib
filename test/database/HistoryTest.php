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
/** The code to test */
require_once dirname(__FILE__).'/../../database/History.php';

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
class HistoryTest extends databaseTest
{
    /** The table to use */
    protected $table = "history";

    /** This is data to use for tests */
    protected static $preload = array(
        array(
            "DeviceKey" => 1,
            "Date" => "2007-12-20 02:20:02",
            "deltaT" => 0,
            "Data0" => 1,
            "Data1" => 2,
            "Data2" => 3
        ),
        array(
            "DeviceKey" => 1,
            "Date" => "2007-12-20 02:25:02",
            "deltaT" => 0,
            "Data0" => 2,
            "Data1" => 3,
            "Data2" => 4
        ),
        array(
            "DeviceKey" => 1,
            "Date" => "2007-12-20 02:30:02",
            "deltaT" => 0,
            "Data0" => 3,
            "Data1" => 4,
            "Data2" => 5
        ),
        array(
            "DeviceKey" => 1,
            "Date" => "2007-12-20 02:35:02",
            "deltaT" => 0,
            "Data0" => 4,
            "Data1" => 5,
            "Data2" => 6
        ),
        array(
            "DeviceKey" => 1,
            "Date" => "2007-12-20 02:40:02",
            "deltaT" => 0,
            "Data0" => 5,
            "Data1" => 6,
            "Data2" => 7
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
        $this->o =& HUGnetDB::getInstance("History", $this->config);
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
                array("DeviceKey" => 1),
                "2007-12-20 02:30:02",
                "2007-12-20 02:40:02",
                5,
                array(
                    array(
                        "DeviceKey" => 1,
                        "Date" => "2007-12-20 02:40:02",
                        "deltaT" => 0,
                        "Data0" => 5.0,
                        "Data1" => 6.0,
                        "Data2" => 7.0,
                        "data" => array(5.0, 6.0, 7.0),
                   ),
                    array(
                        "DeviceKey" => 1,
                        "Date" => "2007-12-20 02:35:02",
                        "deltaT" => 0,
                        "Data0" => 4.0,
                        "Data1" => 5.0,
                        "Data2" => 6.0,
                        "data" => array(4.0, 5.0, 6.0),
                   ),
                    array(
                        "DeviceKey" => 1,
                        "Date" => "2007-12-20 02:30:02",
                        "deltaT" => 0,
                        "Data0" => 3.0,
                        "Data1" => 4.0,
                        "Data2" => 5.0,
                        "data" => array(3.0, 4.0, 5.0),
                   ),
               ),
           ),
            array(
                self::$preload,
                array("DeviceKey" => 1),
                "2007-12-20 02:30:02",
                "NOW",
                2,
                array(
                    array(
                        "DeviceKey" => 1,
                        "Date" => "2007-12-20 02:40:02",
                        "deltaT" => 0,
                        "Data0" => 5.0,
                        "Data1" => 6.0,
                        "Data2" => 7.0,
                        "data" => array(5.0, 6.0, 7.0),
                   ),
                    array(
                        "DeviceKey" => 1,
                        "Date" => "2007-12-20 02:35:02",
                        "deltaT" => 0,
                        "Data0" => 4.0,
                        "Data1" => 5.0,
                        "Data2" => 6.0,
                        "data" => array(4.0, 5.0, 6.0),
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
     * @param array $devInfo   The key for the device to get the history for
     * @param mixed $startDate The first date chronoligically.  Either a unix date
     *                         or a string
     * @param mixed $endDate   The second date chronologically.  Either a unix date
     *                         or a string
     * @param int   $maxRec    The max number of records to return
     * @param int   $expect    The info to expect returned
     *
     * @return null
     *
     * @dataProvider dataGetDates
     */
    public function testGetDates(
        $preload,
        $devInfo,
        $startDate,
        $endDate,
        $maxRec,
        $expect
    ) {
        $this->load($preload);
        $ret = $this->o->GetDates($devInfo, $startDate, $endDate, $maxRec);
        $this->assertSame($expect, $ret);
    }


}

?>
