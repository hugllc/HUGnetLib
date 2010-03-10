<?php
/**
 * Tests the pulse sensor class
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

require_once dirname(__FILE__).'/../sensorTestBase.php';
require_once dirname(__FILE__).'/../../../drivers/sensors/pulseSensor.php';

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
class PulseSensorTest extends sensorTestBase
{
    var $class = "pulseSensor";

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
        return sensorTestBase::sensorArrayDataSource("pulseSensor");
    }

    /**
    * Data provider for testMaximumAnemometer
    *
    * @return array
    */
    public static function dataMaximumAnemometer()
    {
        return array(
            array(500, array(), 1, array(), 300, 2.7275),
        );
    }

    /**
    * test
    *
    * @param int   $val    Output of the A to D converter
    * @param array $sensor The sensor information array
    * @param int   $TC     The time constant
    * @param mixed $extra  Extra sensor information
    * @param float $deltaT The time delta in seconds between this record
    * @param float $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataMaximumAnemometer
    * @covers pulseSensor::maximumAnemometer
    */
    public function testMaximumAnemometer(
        $val,
        $sensor,
        $TC,
        $extra,
        $deltaT,
        $expect
    ) {
        parent::sensorTest(
            "pulseSensor",
            "maximumAnemometer",
            $val,
            $sensor,
            $TC,
            $extra,
            $deltaT,
            $expect
        );
    }

    /**
    * Data provider for testPulseCheck
    *
    * @return array
    */
    public static function dataPulseCheck()
    {
        return array(
            array(1, array(), "PPM", "diff", true),
            array(0, array(), "PPM", "diff", true),
            array(-1, array(), "PPM", "diff", false),
        );
    }

    /**
    * test
    *
    * @param int    $value  The current sensor value
    * @param array  $sensor The sensor information array
    * @param string $units  The units the current value are in
    * @param mixed  $dType  The data mode
    * @param bool   $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataPulseCheck
    * @covers pulseSensor::PulseCheck
    */
    public function testPulseCheck($value, $sensor, $units, $dType, $expect)
    {
        parent::sensorCheckTest(
            "pulseSensor",
            "pulseCheck",
            $value,
            $sensor,
            $units,
            $dType,
            $expect
        );
    }

    /**
    * Data provider for testWattNode
    *
    * @return array
    */
    public static function dataWattNode()
    {
        return array(
            array(500, array(), 1, 5, 300, 2.5),
        );
    }

    /**
    * test
    *
    * @param int   $val    Output of the A to D converter
    * @param array $sensor The sensor information array
    * @param int   $TC     The time constant
    * @param mixed $extra  Extra sensor information
    * @param float $deltaT The time delta in seconds between this record
    * @param float $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataWattNode
    * @covers pulseSensor::WattNode
    */
    public function testWattNode($val, $sensor, $TC, $extra, $deltaT, $expect)
    {
        parent::sensorTest(
            "pulseSensor",
            "WattNode",
            $val,
            $sensor,
            $TC,
            $extra,
            $deltaT,
            $expect
        );
    }

    /**
    * Data provider for testWattNode
    *
    * @return array
    */
    public static function dataLiquidFlowMeter()
    {
        return array(
            array(500, array(), 1, 5, 300, 100.0),
        );
    }

    /**
    * test
    *
    * @param int   $val    Output of the A to D converter
    * @param array $sensor The sensor information array
    * @param int   $TC     The time constant
    * @param mixed $extra  Extra sensor information
    * @param float $deltaT The time delta in seconds between this record
    * @param float $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataLiquidFlowMeter
    * @covers pulseSensor::liquidFlowMeter
    */
    public function testLiquidFlowMeter($val, $sensor, $TC, $extra, $deltaT, $expect)
    {
        parent::sensorTest(
            "pulseSensor",
            "liquidFlowMeter",
            $val,
            $sensor,
            $TC,
            $extra,
            $deltaT,
            $expect
        );
    }

    /**
    * Data provider for testGetPPM
    *
    * @return array
    */
    public static function dataGetPPM()
    {
        return array(
            array(500, array(), 1, 5, 300, 100.0),
        );
    }

    /**
    * test
    *
    * @param int   $val    Output of the A to D converter
    * @param array $sensor The sensor information array
    * @param int   $TC     The time constant
    * @param mixed $extra  Extra sensor information
    * @param float $deltaT The time delta in seconds between this record
    * @param float $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetPPM
    * @covers pulseSensor::GetPPM
    */
    public function testGetPPM($val, $sensor, $TC, $extra, $deltaT, $expect)
    {
        parent::sensorTest(
            "pulseSensor",
            "GetPPM",
            $val,
            $sensor,
            $TC,
            $extra,
            $deltaT,
            $expect
        );
    }

}

?>
