<?php
/**
 *   Everything to do with Units.
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
 *   @subpackage unitConversion
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id: unitConversion.php 411 2007-10-29 21:27:43Z prices $    
 *
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}
 
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// Main Files
require_once 'deviceTest.php'; 
require_once 'driverTest.php'; 
require_once 'EPacketTest.php'; 
require_once 'epsocketTest.php'; 
require_once 'filterTest.php'; 
require_once 'firmwareTest.php'; 
require_once 'gatewayTest.php'; 
require_once 'processTest.php'; 
require_once 'sensorTest.php'; 
require_once 'unitConversionTest.php';

// Drivers
// Endpoints
require_once 'drivers/endpoints/e00392800Test.php';
require_once 'drivers/endpoints/e00392100Test.php';
require_once 'drivers/endpoints/e00391200Test.php';
require_once 'drivers/endpoints/e00391201Test.php';
// Filters
require_once 'drivers/filters/medianFilterTest.php';
// Sensors
require_once 'drivers/sensors/capacitiveSensorTest.php';
require_once 'drivers/sensors/currentSensorTest.php';
require_once 'drivers/sensors/lightSensorTest.php';
require_once 'drivers/sensors/pulseSensorTest.php';
require_once 'drivers/sensors/resistiveSensorTest.php';
require_once 'drivers/sensors/voltageSensorTest.php';
require_once 'drivers/sensors/windDirectionSensorTest.php';


class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
 
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('HUGnetLib');

        // Main file Tests 
        $suite->addTestSuite('deviceTest');
        $suite->addTestSuite('driverTest');
        $suite->addTestSuite('EPacketTest');
        $suite->addTestSuite('epsocketTest');
        $suite->addTestSuite('filterTest');
        $suite->addTestSuite('firmwareTest');
        $suite->addTestSuite('gatewayTest');
        $suite->addTestSuite('processTest');
        $suite->addTestSuite('sensorTest');
        $suite->addTestSuite('unitConversionTest');
 
        // Driver Tests 
        // Endpoints
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
 
if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
?>
