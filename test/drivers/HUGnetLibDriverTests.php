<?php
/**
 *   This runs all of the tests associated with HUGnetLib.
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *   
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *   
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *   
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Test
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id: AllTests.php 442 2007-11-12 23:03:55Z prices $    
 *
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'HUGnetLibDriverTests::main');
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// Drivers
// Endpoints
require_once 'endpoints/e00392800Test.php';
require_once 'endpoints/e00392100Test.php';
require_once 'endpoints/e00391200Test.php';
require_once 'endpoints/e00391201Test.php';
require_once 'endpoints/eDEFAULTTest.php';
// Filters
require_once 'filters/medianFilterTest.php';
// Sensors
require_once 'sensors/capacitiveSensorTest.php';
require_once 'sensors/currentSensorTest.php';
require_once 'sensors/lightSensorTest.php';
require_once 'sensors/pulseSensorTest.php';
require_once 'sensors/resistiveSensorTest.php';
require_once 'sensors/voltageSensorTest.php';
require_once 'sensors/windDirectionSensorTest.php';

/**
 *  This class runs all of the tests.  This must be done with no errors
 * before the software is ever released.
 */
class HUGnetLibDriverTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
 
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('HUGnetLibDriver');

        // Driver Tests 
        // Endpoints
        $suite->addTestSuite('eDEFAULTTest');
        $suite->addTestSuite('e00392800Test');
        $suite->addTestSuite('e00392100Test');
        $suite->addTestSuite('e00391200Test');
        $suite->addTestSuite('e00391201Test');
 
        // Filters
        $suite->addTestSuite('medianFilterTest');
         
        // Sensors
        $suite->addTestSuite('capacitiveSensorTest');
        $suite->addTestSuite('currentSensorTest');
        $suite->addTestSuite('lightSensorTest');
        $suite->addTestSuite('pulseSensorTest');
        $suite->addTestSuite('resistiveSensorTest');
        $suite->addTestSuite('voltageSensorTest');
        $suite->addTestSuite('windDirectionSensorTest');
 
        return $suite;
    }
}
 
if (PHPUnit_MAIN_METHOD == 'HUGnetLibDriverTests::main') {
    HUGnetLibDriverTests::main();
}
?>
