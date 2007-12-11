<?php
/**
 * This runs all of the tests associated with HUGnetLib.
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
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @version SVN: $Id$    
 *
 */

if (!defined('PHPUNIT_MAIN_METHOD')) {
    define('PHPUNIT_MAIN_METHOD', 'HUGnetLibTests::main');
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'otherTest.php';

// Main Files
require_once 'devInfoTest.php'; 
require_once 'deviceTest.php'; 
require_once 'deviceCacheTest.php'; 
require_once 'driverTest.php'; 
require_once 'EPacketTest.php'; 
require_once 'dbsocketTest.php'; 
require_once 'epsocketTest.php'; 
require_once 'filterTest.php'; 
require_once 'firmwareTest.php'; 
require_once 'gatewayTest.php'; 
require_once 'processTest.php'; 
require_once 'sensorTest.php'; 
require_once 'plogTest.php'; 
require_once 'unitConversionTest.php';

// Drivers
require_once 'drivers/HUGnetLibDriverTests.php';

/**
 *  This class runs all of the tests.  This must be done with no errors
 * before the software is ever released.
 */
class HUGnetLibTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
 
    public static function suite()
    {
        PHPUnit_Util_Filter::addDirectoryToFilter('*adodb/', '.php');
        PHPUnit_Util_Filter::addDirectoryToFilter('HUGnetLib/test/', '.php');
        $suite = new PHPUnit_Framework_TestSuite('HUGnetLib');

        $suite->addTestSuite('otherTest');
        // Main file Tests 
        $suite->addTestSuite('devInfoTest');
        $suite->addTestSuite('driverTest');
        $suite->addTestSuite('deviceTest');
        $suite->addTestSuite('deviceCacheTest');
        $suite->addTestSuite('EPacketTest');
        $suite->addTestSuite('dbsocketTest');
        $suite->addTestSuite('epsocketTest');
        $suite->addTestSuite('filterTest');
        $suite->addTestSuite('firmwareTest');
        $suite->addTestSuite('gatewayTest');
        $suite->addTestSuite('processTest');
        $suite->addTestSuite('sensorTest');
        $suite->addTestSuite('plogTest');
        $suite->addTestSuite('unitConversionTest');
 
        // Driver Tests 
        $suite->addTest(HUGnetLibDriverTests::suite());
 
        return $suite;
    }
}
 
if (PHPUNIT_MAIN_METHOD == 'HUGnetLibTests::main') {
    HUGnetLibTests::main();
}
?>
