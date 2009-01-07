<?php
/**
 * Tests the voltage sensor class
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
 * @category   Sensors
 * @package    HUGnetLibTest
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** The test case class */
require_once "PHPUnit/Framework/TestCase.php";
/** The test suite class */
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../sensorTestBase.php';
require_once dirname(__FILE__).'/../../../drivers/sensors/voltageSensor.php';

/**
 * Test class for sensor.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:08:37.
 *
 * @category   Sensors
 * @package    HUGnetLibTest
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class VoltageSensorTest extends sensorTestBase
{
    var $class = "voltageSensor";
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

        $suite  = new PHPUnit_Framework_TestSuite("voltageSensorTest");
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
    }
    /**
    * data provider for testSensorArray*
    *
    * @return array
    */
    public static function dataSensorArray()
    {
        return sensorTestBase::sensorArrayDataSource("voltageSensor");
    }

    /**
    * Data provider for testGetDividerVoltage
    *
    * @return array
    */
    public static function dataGetDividerVoltage()
    {
        return array(
            array(1, 1, 0, 1, 0.0),
            array(1, 1, 1, 0, 0.0),
            array(1000, 1, 1, 1, 0.1527),
        );
    }
    /**
    * test
    *
    * @param int   $A      The incoming value
    * @param float $R1     The resistor to the voltage
    * @param float $R2     The resistor to ground
    * @param int   $T      The time constant
    * @param mixed $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetDividerVoltage
    * @covers voltageSensor::GetDividerVoltage
    */
    public function testGetDividerVoltage($A, $R1, $R2, $T, $expect)
    {
        $o   = new voltageSensor();
        $ret = $o->getDividerVoltage($A, $R1, $R2, $T);
        $this->assertSame($expect, $ret);
    }

    /**
    * Data provider for testFETBoard
    *
    * @return array
    */
    public static function dataFETBoard()
    {
        return array(
            array(1000,
                  array("extraDefault"=>array(1, 1)),
                  1,
                  array(1, 1),
                  0,
                  0.1527),
            array(1000,
                  array("extraDefault"=>array(1, 1)),
                  1,
                  array(1, 0),
                  0,
                  0.1527),
            array(1000,
                  array("extraDefault"=>array(1, 1)),
                  1,
                  array(0, 1),
                  0,
                  0.1527),
            array(1000,
                  array("extraDefault"=>array(1, 1)),
                  1,
                  null,
                  0,
                  0.1527),
        );
    }
    /**
    * test
    *
    * @param float $val    The incoming value
    * @param array $sensor The sensor setup array
    * @param int   $TC     The time constant
    * @param mixed $extra  Extra parameters for the sensor
    * @param float $deltaT The time difference
    * @param mixed $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataFETBoard
    * @covers voltageSensor::FETBoard
    */
    public function testFETBoard($val, $sensor, $TC, $extra, $deltaT, $expect)
    {
        parent::sensorTest("voltageSensor",
                           "FETBoard",
                           $val,
                           $sensor,
                           $TC,
                           $extra,
                           $deltaT,
                           $expect);
    }

    /**
    * Data provider for testFETBoard
    *
    * @return array
    */
    public static function dataIndirect()
    {
        return array(
            array(1000,
                  array("extraDefault"=>array(1, 1, 5)),
                  1,
                  array(1, 1, 5),
                  0,
                  0.1527),
            array(1000,
                  array("extraDefault"=>array(1, 1, 5)),
                  1,
                  array(1, 0, 5),
                  0,
                  0.1527),
            array(1000,
                  array("extraDefault"=>array(1, 1, 5)),
                  1,
                  array(0, 1, 5),
                  0,
                  0.1527),
            array(1000,
                  array("extraDefault"=>array(1, 1, 5)),
                  1,
                  null,
                  0,
                  0.1527),
        );
    }
    /**
    * test
    *
    * @param float $val    The incoming value
    * @param array $sensor The sensor setup array
    * @param int   $TC     The time constant
    * @param mixed $extra  Extra parameters for the sensor
    * @param float $deltaT The time difference
    * @param mixed $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataIndirect
    */
    public function testIndirect($val, $sensor, $TC, $extra, $deltaT, $expect)
    {
        parent::sensorTest("voltageSensor",
                           "Indirect",
                           $val,
                           $sensor,
                           $TC,
                           $extra,
                           $deltaT,
                           $expect);
    }

    /**
    * Data provider for GetVoltage
    *
    * @return array
    */
    public static function dataGetVoltage()
    {
        return array(
            array(null, 1, 1, null),
            array(1, 1, null, null),
            array(1, 0, 1, 0.0),
            array(4000, 1, 10, 0.6109),
        );
    }
    /**
    * test
    *
    * @param int   $A      The AtoD reading
    * @param int   $T      The time constant
    * @param float $Vref   The voltage reference
    * @param mixed $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetVoltage
    * @covers voltageSensor::GetVoltage
    */
    public function testGetVoltage($A, $T, $Vref, $expect)
    {
        $o   = new voltageSensor();
        $ret = $o->getVoltage($A, $T, $Vref);
        $this->assertSame($expect, $ret);
    }

    /**
    * Data provider for testCHSMSS
    *
    * @return array
    */
    public static function dataCHSMSS()
    {
        return array(
            array(10000, array("extraDefault"=>1.1), 1, 1.1, 0, 16.8),
            array(10000, array("extraDefault"=>1.1), 1, null, 0, 16.8),
            array(10000, array("extraDefault"=>1.1), 1, 0, 0, 16.8),
            array(65535, array("extraDefault"=>1.1), 1, 1.1, 0, 110.11),
            array(-10000, array("extraDefault"=>1.1), 1, 1.1, 0, null),
        );
    }
    /**
    * test
    *
    * @param float $val    The incoming value
    * @param array $sensor The sensor setup array
    * @param int   $TC     The time constant
    * @param mixed $extra  Extra parameters for the sensor
    * @param float $deltaT The time difference
    * @param mixed $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataCHSMSS
    * @covers voltageSensor::CHSMSS
    */
    public function testCHSMSS($val, $sensor, $TC, $extra, $deltaT, $expect)
    {
        parent::sensorTest("voltageSensor",
                           "CHSMSS",
                           $val,
                           $sensor,
                           $TC,
                           $extra,
                           $deltaT,
                           $expect);
    }

    /**
    * Data provider for testCHSMSS
    *
    * @return array
    */
    public static function dataDirect()
    {
        return array(
            array(10000, array("extraDefault"=>array(5)), 1, array(1.1), 0, .168),
            array(10000, array("extraDefault"=>array(5)), 1, null, 0, .7637),
            array(10000, array("extraDefault"=>array(5)), 1, array(0), 0, .7637),
            array(65535, array("extraDefault"=>array(5)), 1, array(1.1), 0, null),
            array(-10000, array("extraDefault"=>array(5)), 1, array(1.1), 0, null),
        );
    }
    /**
    * test
    *
    * @param float $val    The incoming value
    * @param array $sensor The sensor setup array
    * @param int   $TC     The time constant
    * @param mixed $extra  Extra parameters for the sensor
    * @param float $deltaT The time difference
    * @param mixed $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataDirect
    */
    public function testDirect($val, $sensor, $TC, $extra, $deltaT, $expect)
    {
        parent::sensorTest("voltageSensor",
                           "direct",
                           $val,
                           $sensor,
                           $TC,
                           $extra,
                           $deltaT,
                           $expect);
    }
    /**
    * Data provider for testLinearBounded
    *
    * @return array
    */
    public static function dataLinearBounded()
    {
        $extra  = array(.5, 4.5, 0, 5, 5);
        $extra2 = array(5, 5, 0, 5, 5);
        $extra3 = array();
        $sensor = array('extraDefault' => array(1, 4, 0, 100, 5));
        return array(
            array(10000, $sensor, 1, $extra, 0, 0.3296),
            array(null,  $sensor, 1, $extra, 0, null),
            array(0, $sensor, 1, $extra, 0, null),
            array(65535, $sensor, 1, $extra, 0, null),
            array(10000, $sensor, 1, $extra2, 0, null),
            array(30000, $sensor, 1, $extra3, 0, 43.0367),
            array(null,  $sensor, 1, $extra3, 0, null),
            array(0, $sensor, 1, $extra3, 0, null),
            array(65535, $sensor, 1, $extra3, 0, null),
            array(65536, $extra3, 1, $extra2, 0, null),

        );
    }
    /**
    * test
    *
    * @param float $val    The incoming value
    * @param array $sensor The sensor setup array
    * @param int   $TC     The time constant
    * @param mixed $extra  Extra parameters for the sensor
    * @param float $deltaT The time difference
    * @param mixed $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataLinearBounded
    */
    public function testLinearBounded($val, $sensor, $TC, $extra, $deltaT, $expect)
    {
        parent::sensorTest("voltageSensor",
                           "linearBounded",
                           $val,
                           $sensor,
                           $TC,
                           $extra,
                           $deltaT,
                           $expect);
    }

     /**
    * Data provider for testLinearBoundedIndirect
    *
    * @return array
     */
    public static function dataLinearBoundedIndirect()
    {
        $extra = array(100, 10, 5, 45, 0, 1000, 5);
        $extra2 = array(100, 10, 25, 25, 0, 1000, 5);
        $extra3 = array();
        $sensor = array('extraDefault' => array(1000, 10, 5, 300, 0, 100, 5));
        return array(
            array(10000, $sensor, 1, $extra, 0, 85.0125),
            array(null, $sensor, 1, $extra, 0, null),
            array(0, $sensor, 1, $extra, 0, null),
            array(65535, $sensor, 1, $extra, 0, null),
            array(10000, $sensor, 1, $extra3, 0, 24.4516),
            array(null, $sensor, 1, $extra3, 0, null),
            array(0, $sensor, 1, $extra3, 0, null),
            array(65535, $sensor, 1, $extra3, 0, null),

        );
    }
    /**
    * test
    *
    * @param float $val    The incoming value
    * @param array $sensor The sensor setup array
    * @param int   $TC     The time constant
    * @param mixed $extra  Extra parameters for the sensor
    * @param float $deltaT The time difference
    * @param mixed $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataLinearBoundedIndirect
    */
    public function testLinearBoundedIndirect($val,
                                              $sensor,
                                              $TC,
                                              $extra,
                                              $deltaT,
                                              $expect)
    {
        parent::sensorTest("voltageSensor",
                           "linearBoundedIndirect",
                           $val,
                           $sensor,
                           $TC,
                           $extra,
                           $deltaT,
                           $expect);
    }


}

?>
