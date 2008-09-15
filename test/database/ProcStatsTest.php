<?php
/**
 * Tests the process class
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
 * @category   UnixProcess
 * @package    HUGnetLibTest
 * @subpackage UnixProcess
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

// Call processTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "processTest::main");
}

/** The test case class */
require_once "PHPUnit/Framework/TestCase.php";
/** The test suite class */
require_once "PHPUnit/Framework/TestSuite.php";

if (!defined("HUGNET_INCLUDE_PATH")) {
    include_once dirname(__FILE__).'/../../hugnet.inc.php';
}
require_once dirname(__FILE__).'/DatabaseTest.php';
require_once dirname(__FILE__).'/../../database/ProcStats.php';

/**
 * Test class for process.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:45:57.
 *
 * @category   UnixProcess
 * @package    HUGnetLibTest
 * @subpackage UnixProcess
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ProcStatsTest extends databaseTest
{
    /** Database table to use */
    var $table = "procStats";
    /** Database id to use */
    var $id = "PID";
    /** This is what to force the 'me' variable to by default */
    protected $me = array(
        "Host" => "thing2",
        "Domain" => ".int.hugllc.com",
        "OS" => "Linux",
        "PID" => 1234,
        "Program" => "phpunit",
        "File" => "/tmp/phpunit.pid",
        "Block" => "NORMAL",
        "Started" => "2007-12-26 13:34:43",
    );
    /** Data to preload into the database */
    static $preload = array(
        array("PID"=>1234, "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-23", "sname" => "testStat", "svalue" => 0),
        array("PID"=>1234, "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-24", "sname" => "testStat", "svalue" => 1),
        array("PID"=>1234, "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-25", "sname" => "testStat", "svalue" => 2),
        array("PID"=>1234, "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-26", "sname" => "testStat", "svalue" => 3),
        array("PID"=>1234, "Program" => "phpunit", "stype"=>"Monthly", "sdate" => "2007-12", "sname" => "testStat", "svalue" => 6),
        array("PID"=>1234, "Program" => "phpunit", "stype"=>"Yearly", "sdate" => "2007", "sname" => "testStat", "svalue" => 6),
        array("PID"=>1234, "Program" => "phpunit", "stype"=>"totals", "sdate" => "now", "sname" => "testStat", "svalue" => 6),
    );
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

        $suite  = new PHPUnit_Framework_TestSuite("processTest");
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
        $this->o =& HUGnetDB::getInstance("ProcStatsTestClass", $this->config); // new ProcStatsTestClass($this->file, $this->table, $this->id);
        // Clear out the database
        $this->pdo->query("DELETE FROM `".$this->table."`");
        $this->o->forceMe($this->me);
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
        $this->pdo->query("DROP TABLE `".$this->table."`");
        parent::tearDown();
        unset($this->o);
    }

    /**
     * Data provider for testIncStat
     *
     * @return array
     */
    public static function dataIncStat() 
    {
        return array(
            array(
                self::$preload,
                "testStat",
                strtotime("2007-12-25 13:13:13"),
                array(
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-23", "sname" => "testStat", "svalue" => "0"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-24", "sname" => "testStat", "svalue" => "1"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-26", "sname" => "testStat", "svalue" => "3"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"totals", "sdate" => "now", "sname" => "testStat", "svalue" => "7"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-25", "sname" => "testStat", "svalue" => "3"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Monthly", "sdate" => "2007-12", "sname" => "testStat", "svalue" => "7"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Yearly", "sdate" => "2007", "sname" => "testStat", "svalue" => "7"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"stat", "sdate" => "now", "sname" => "StatDate", "svalue" => "2007-12-25 13:13:13"),
               ),
           ),
            array(
                array(),
                "testStat",
                strtotime("2007-12-25 13:13:13"),
                array(
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"totals", "sdate" => "now", "sname" => "testStat", "svalue" => "1"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-25", "sname" => "testStat", "svalue" => "1"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Monthly", "sdate" => "2007-12", "sname" => "testStat", "svalue" => "1"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Yearly", "sdate" => "2007", "sname" => "testStat", "svalue" => "1"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"stat", "sdate" => "now", "sname" => "StatDate", "svalue" => "2007-12-25 13:13:13"),
               ),
           ),
        );
    }

    /**
     * test
     *
     * @param array  $preload array of data to preload into the system
     * @param string $stat    The statistic to increment
     * @param int    $date    The unix date to force the system to use
     * @param array  $expect  The array to expect to be in the database
     *
     * @return null
     *
     * @dataProvider dataIncStat().
     */
    public function testIncStat($preload, $stat, $date, $expect) 
    {
        $this->o->forceDate = $date;
        $this->load($preload);
        $this->o->incStat($stat);
        $ret = $this->getAll();
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testIncField
     *
     * @return array
     */
    public static function dataIncField() 
    {
        return array(
            array(
                self::$preload,
                "Daily",
                "testStat",
                "2007-12-25",
                array(
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-23", "sname" => "testStat", "svalue" => "0"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-24", "sname" => "testStat", "svalue" => "1"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-26", "sname" => "testStat", "svalue" => "3"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Monthly", "sdate" => "2007-12", "sname" => "testStat", "svalue" => "6"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Yearly", "sdate" => "2007", "sname" => "testStat", "svalue" => "6"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"totals", "sdate" => "now", "sname" => "testStat", "svalue" => "6"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-25", "sname" => "testStat", "svalue" => "3"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"stat", "sdate" => "now", "sname" => "StatDate", "svalue" => "2007-12-25 00:00:00"),
               ),
           ),
            array(
                array(),
                "Daily",
                "testStat",
                "2007-12-25",
                array(
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-25", "sname" => "testStat", "svalue" => "1"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"stat", "sdate" => "now", "sname" => "StatDate", "svalue" => "2007-12-25 00:00:00"),
               ),
           ),
        );
    }

    /**
     * test
     *
     * @param array  $preload array of data to preload into the system
     * @param string $type    The stat type to increment
     * @param string $name    The name of the stat to increment
     * @param int    $date    The unix date to force the system to use
     * @param array  $expect  The array to expect to be in the database
     *
     * @return null
     *
     * @dataProvider dataIncField().
     */
    public function testIncField($preload, $type, $name, $date, $expect) 
    {
        $this->o->forceDate = strtotime($date);
        $this->load($preload);
        $this->o->incField($type, $name, $date);
        $ret = $this->getAll();
        $this->assertEquals($expect, $ret);
    }

    /**
     * Data provider for testGetMyStat
     *
     * @return array
     */
    public static function dataGetMyStat() 
    {
        return array(
            array(
                self::$preload,
                "Daily",
                "testStat",
                "2007-12-25",
                2,
           ),
            array(
                array(),
                "Daily",
                "testStat",
                "2007-12-25",
                0,
           ),
        );
    }

    /**
     * test
     *
     * @param array  $preload array of data to preload into the system
     * @param string $type    The stat type to increment
     * @param string $name    The name of the stat to increment
     * @param int    $date    The unix date to force the system to use
     * @param array  $expect  The array to expect to be in the database
     *
     * @return null
     *
     * @dataProvider dataGetMyStat().
     */
    public function testIncGetMyStat($preload, $type, $name, $date, $expect) 
    {
        $this->load($preload);
        $ret = $this->o->getMyStat($name, $date, $type);
        $this->assertEquals($expect, $ret);
    }

    /**
     * Data provider for testGetMyStat
     *
     * @return array
     */
    public static function dataGetStat() 
    {
        return array(
            array(
                self::$preload,
                "phpunit",
                "Daily",
                "testStat",
                "2007-12-25",
                "1234",
                2,
           ),
            array(
                array(),
                "phpunit",
                "Daily",
                "testStat",
                "2007-12-25",
                "1234",
                0,
           ),
        );
    }

    /**
     * test
     *
     * @param array  $preload array of data to preload into the system
     * @param string $Program The name of the program
     * @param string $type    The stat type to increment
     * @param string $name    The name of the stat to increment
     * @param int    $date    The unix date to force the system to use
     * @param int    $PID     Unix process id
     * @param array  $expect  The array to expect to be in the database
     *
     * @return null
     *
     * @dataProvider dataGetStat().
     */
    public function testGetStat($preload, $Program, $type, $name, $date, $PID, $expect) 
    {
        $this->load($preload);
        $ret = $this->o->getStat($name, $Program, $date, $type, $PID);
        $this->assertEquals($expect, $ret);
    }

    /**
     * Data provider for testSetStat
     *
     * @return array
     */
    public static function dataSetStat() 
    {
        return array(
            array(
                self::$preload,
                "testStat",
                8,
                "2007-12-25",
                "Daily",
                array(
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-23", "sname" => "testStat", "svalue" => "0"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-24", "sname" => "testStat", "svalue" => "1"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-26", "sname" => "testStat", "svalue" => "3"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Monthly", "sdate" => "2007-12", "sname" => "testStat", "svalue" => "6"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Yearly", "sdate" => "2007", "sname" => "testStat", "svalue" => "6"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"totals", "sdate" => "now", "sname" => "testStat", "svalue" => "6"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-25", "sname" => "testStat", "svalue" => "8"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"stat", "sdate" => "now", "sname" => "StatDate", "svalue" => "2007-12-25 00:00:00"),
               ),
           ),
            array(
                array(),
                "testStat",
                16,
                "2007-12-25",
                "Daily",
                array(
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"Daily", "sdate" => "2007-12-25", "sname" => "testStat", "svalue" => "16"),
                    array("PID"=>"1234", "Program" => "phpunit", "stype"=>"stat", "sdate" => "now", "sname" => "StatDate", "svalue" => "2007-12-25 00:00:00"),
               ),
           ),
        );
    }

    /**
     * test
     *
     * @param array  $preload array of data to preload into the system
     * @param string $name    The name of the stat to increment
     * @param $sting $value   The value to set the stat to
     * @param string $date    The SQL date to force the system to use
     * @param string $type    The stat type to increment
     * @param array  $expect  The array to expect to be in the database
     *
     * @return null
     *
     * @dataProvider dataSetStat().
     */
    public function testSetStat($preload, $name, $value, $date, $type, $expect) 
    {
        $this->o->forceDate = strtotime($date);
        $this->load($preload);
        $this->o->setStat($name, $value, $date, $type);
        $ret = $this->getAll();
        $this->assertEquals($expect, $ret);
    }


    /**
     * test
     *
     * @return null
     *
     * @todo Implement testClearStats().
     */
    public function testClearStats() 
    {
        $this->load(self::$preload);
        $this->o->clearStats();
        $ret = $this->getAll();
        $this->assertSame(array(), $ret);
    }

    /**
     * Data provider for testGetPeridicStats
     *
     * @return array
     */
    public static function dataGetPeridicStats() 
    {
        return array(
            array(
                self::$preload,
                "phpunit",
                array(
                    "Daily" => array(
                        "2007-12-26" => array("testStat" => "3"),
                        "2007-12-25" => array("testStat" => "2"),
                        "2007-12-24" => array("testStat" => "1"),
                        "2007-12-23" => array("testStat" => "0"),
                   ),
                
                    "Monthly" => array(
                        "2007-12" => array("testStat" => "6")
                   ),
                    "Yearly" => array(
                        "2007" => array("testStat" => "6"),
                   ),
              ),
           ),
            array(
                self::$preload,
                "badProgram",
                array(),
           ),
            array(
                array(),
                "badProgram",
                array(),
           ),
        );
    }

    /**
     * test
     *
     * @param array  $preload array of data to preload into the system
     * @param string $Program The program to get the stats for
     * @param array  $expect  The array to expect to be in the database
     *
     * @return null
     *
     * @dataProvider dataGetPeridicStats().
     */
    public function testGetPeridicStats($preload, $Program, $expect) 
    {
        $this->load($preload);
        $ret = $this->o->getPeriodicStats($Program);
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testGetTotalStats
     *
     * @return array
     */
    public static function dataGetTotalStats() 
    {
        return array(
            array(
                self::$preload,
                "phpunit",
                array(
                    "testStat" => "6",
               ),
           ),
            array(
                self::$preload,
                "badProgram",
                array(),
           ),
            array(
                array(),
                "badProgram",
                array(),
           ),
        );
    }

    /**
     * test
     *
     * @param array  $preload array of data to preload into the system
     * @param string $Program The program to get the stats for
     * @param array  $expect  The array to expect to be in the database
     *
     * @return null
     *
     * @dataProvider dataGetTotalStats().
     */
    public function testGetTotalStats($preload, $Program, $expect) 
    {
        $this->load($preload);
        $ret = $this->o->getTotalStats($Program);
        $this->assertSame($expect, $ret);
    }

}

// Call processTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "processTest::main") {
    processTest::main();
}
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ProcStatsTestClass extends ProcStats
{
    /**
     * The name of the table we are using
     */
    protected $table = "HUGnetDBTest";
    /** The number of columns */
    private $_columns = 6;
   
    /**
     * Forces the 'me' variable in the class to be a certain thing.
     *
     * @param array $me What to set the me variable to
     *
     * @return null
     */    
    public function forceMe($me)
    {
        $this->me = $me;
    }     
}
?>
