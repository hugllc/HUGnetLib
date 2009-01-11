<?php
/**
 * Tests the process class
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
 * @category   UnixProcess
 * @package    HUGnetLibTest
 * @subpackage UnixProcess
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** The test case class */
require_once "PHPUnit/Framework/TestCase.php";
/** The test suite class */
require_once "PHPUnit/Framework/TestSuite.php";

/** This is our base class */
require_once dirname(__FILE__).'/DatabaseTest.php';
require_once dirname(__FILE__).'/../../database/Process.php';

/**
 * Test class for process.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:45:57.
 *
 * @category   UnixProcess
 * @package    HUGnetLibTest
 * @subpackage UnixProcess
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ProcessTest extends databaseTest
{
    /** Database table to use */
    var $table = "process";
    /** Database id to use */
    var $id = "ProcessKey";
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
        $this->o =& HUGnetDB::getInstance("Process", $this->config);
        $this->o->createTable();
        // Clear out the database
        $this->pdo->query("DELETE FROM ".$this->table);

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
        $this->pdo->query("DROP TABLE ".$this->table);
        parent::tearDown();
        unset($this->o);

    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testGetMyInfo().
     */
    public function testGetMyInfo()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testCheckProcess().
     */
    public function testCheckProcess()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testCheckAll().
     */
    public function testCheckAll()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testRegister().
     */
    public function testRegister()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testCheckin().
     */
    public function testCheckin()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testFastCheckin().
     */
    public function testFastCheckin()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testDbRegister().
     */
    public function testDbRegister()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testDbUnregister().
     */
    public function testDbUnregister()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testUnregister().
     */
    public function testUnregister()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testCreateTable().
     */
    public function testCreateTable()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testCheckRegistered().
     */
    public function testCheckRegistered()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testCheckUnregistered().
     */
    public function testCheckUnregistered()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testFileRegister().
     */
    public function testFileRegister()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testFileUnregister().
     */
    public function testFileUnregister()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testCheckFile().
     */
    public function testCheckFile()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }

    /**
     * test
     *
     * @return null
     *
     * @todo Implement testCheckDB().
     */
    public function testCheckDB()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete("This test has not been implemented yet.");
    }
}

?>
