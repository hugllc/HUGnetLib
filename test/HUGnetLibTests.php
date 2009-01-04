<?php
/**
 * This runs all of the tests associated with HUGnetLib.
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
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'HUGnetLibTests::main');
}
/** Test framework */
require_once 'PHPUnit/Framework.php';
/** This is for running tests */
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'otherTest.php';

// Main Files
require_once 'devInfoTest.php'; 
require_once 'driverTest.php'; 
require_once 'EPacketTest.php'; 
require_once 'filterTest.php'; 
require_once 'sensorTest.php'; 
require_once 'unitConversionTest.php';

// Drivers
require_once 'drivers/HUGnetLibDriverTests.php';

// Base
require_once 'base/HUGnetLibBaseTests.php';

// database
require_once 'database/HUGnetLibDatabaseTests.php';

// Library
require_once 'lib/HUGnetLibLibTests.php';

/**
 *  This class runs all of the tests.  This must be done with no errors
 * before the software is ever released.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetLibTests
{
    /**
     * main function
     *
     * @return null
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
 
    /**
     * test suite
     *
     * @return null
     */
    public static function suite()
    {
        PHPUnit_Util_Filter::addDirectoryToFilter(dirname(__FILE__), '.php');
        $suite = new PHPUnit_Framework_TestSuite('HUGnetLib');

        $suite->addTestSuite('otherTest');
        // Main file Tests 
        $suite->addTestSuite('devInfoTest');
        $suite->addTestSuite('driverTest');
        $suite->addTestSuite('EPacketTest');
        $suite->addTestSuite('filterTest');
        $suite->addTestSuite('sensorTest');
        $suite->addTestSuite('unitConversionTest');
 
        // Base class tests
        $suite->addTest(HUGnetLibBaseTests::suite());
        // Database Class Tests 
        $suite->addTest(HUGnetLibDatabaseTests::suite());
        // Driver Tests
        $suite->addTest(HUGnetLibDriverTests::suite());
        // Library Tests
        $suite->addTest(HUGnetLibLibTests::suite());
 
        return $suite;
    }
}
 
if (PHPUnit_MAIN_METHOD == 'HUGnetLibTests::main') {
    HUGnetLibTests::main();
}
?>
