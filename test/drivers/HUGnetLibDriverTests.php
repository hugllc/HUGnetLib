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
 * @package    HUGnetLibTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'HUGnetLibDriverTests::main');
}
/** Test framework */
require_once 'PHPUnit/Framework.php';
/** This is for running tests */
require_once 'PHPUnit/TextUI/TestRunner.php';

// Drivers
// Endpoints
require_once 'endpoints/e00392800Test.php';
require_once 'endpoints/e00392100Test.php';
require_once 'endpoints/e00391200Test.php';
require_once 'endpoints/e00391201Test.php';
require_once 'endpoints/e00392601Test.php';
require_once 'endpoints/eDEFAULTTest.php';
require_once 'endpoints/eVIRTUALTest.php';
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
// Sockets
require_once 'socket/dbsocketTest.php'; 
require_once 'socket/epsocketTest.php'; 
// Units
require_once 'units/temperatureUnitsTest.php';
require_once 'units/heatConductanceUnitsTest.php';
require_once 'units/heatInsulationUnitsTest.php';
require_once 'units/baleMoistureUnitsTest.php';
require_once 'units/currentUnitsTest.php';
require_once 'units/doorUnitsTest.php';
require_once 'units/lengthUnitsTest.php';
require_once 'units/powerUnitsTest.php';
require_once 'units/pulsesUnitsTest.php';
require_once 'units/voltageUnitsTest.php';
require_once 'units/capacitanceUnitsTest.php';
require_once 'units/directionUnitsTest.php';
require_once 'units/humidityUnitsTest.php';
require_once 'units/lightUnitsTest.php';
require_once 'units/pressureUnitsTest.php';
require_once 'units/speedUnitsTest.php';
/**
 *  This class runs all of the tests.  This must be done with no errors
 * before the software is ever released.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetLibDriverTests
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
        $suite = new PHPUnit_Framework_TestSuite('HUGnetLibDriver');

        // Driver Tests 
        // Endpoints
        $suite->addTestSuite('eDEFAULTTest');
        $suite->addTestSuite('eVIRTUALTest');
        $suite->addTestSuite('e00392800Test');
        $suite->addTestSuite('e00392100Test');
        $suite->addTestSuite('e00391200Test');
        $suite->addTestSuite('e00391201Test');
        $suite->addTestSuite('e00392601Test');
 
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
        
        // Sockets
        $suite->addTestSuite('dbsocketTest');
        $suite->addTestSuite('epsocketTest');
        
        // Units
        $suite->addTestSuite('heatInsulationUnitsTest');
        $suite->addTestSuite('heatConductanceUnitsTest');
        $suite->addTestSuite('TemperatureUnitsTest');
        $suite->addTestSuite('baleMoistureUnitsTest');
        $suite->addTestSuite('currentUnitsTest');
        $suite->addTestSuite('doorUnitsTest');
        $suite->addTestSuite('lengthUnitsTest');
        $suite->addTestSuite('powerUnitsTest');
        $suite->addTestSuite('pulsesUnitsTest');
        $suite->addTestSuite('voltageUnitsTest');
        $suite->addTestSuite('capacitanceUnitsTest');
        $suite->addTestSuite('directionUnitsTest');
        $suite->addTestSuite('humidityUnitsTest');
        $suite->addTestSuite('lightUnitsTest');
        $suite->addTestSuite('pressureUnitsTest');
        $suite->addTestSuite('speedUnitsTest');
        return $suite;
    }
}
 
if (PHPUnit_MAIN_METHOD == 'HUGnetLibDriverTests::main') {
    HUGnetLibDriverTests::main();
}
?>
