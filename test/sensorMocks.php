<?php
/**
 * Tests the sensor class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

require_once dirname(__FILE__).'/../sensor.php';

// Make sure this file isn't counted for code coverage
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 *  This is a test sensor.  It is not used for anything else.
 *
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class TestSensor extends SensorBase
{
    var $sensors = array(
        0x100 => array(
            "TestSensor1" => array(
                "longName" => "Generic Test Sensor 1",
                "unitType" => "Test",
                "validUnits" => array('A', 'B', 'C'),
                "storageUnit" =>  'B',
                "function" => "test1",
                "extraText" => "extraTest",
                "extraDefault" => "extraDefaultTest",
                "unitModes" => array(
                    'A' => 'raw,diff',
                    'B' => 'diff',
                    'C' => 'raw',
                ),
            ),
            "TestSensor2" => array(
                "longName" => "Generic Test Sensor 2",
                "unitType" => "Test2",
                "validUnits" => array('D', 'E', 'F'),
                "storageUnit" =>  'E',
                "mult" => 2,
                "function" => "test",
                "checkFunction" => "s2Check",
                "unitModes" => array(
                    'E' => 'raw,diff',
                    'D' => 'diff',
                    'F' => 'raw',
                ),
            ),
            "TestSensor3" => array(
                "longName" => "Generic Test Sensor 3",
                "unitType" => "Test",
                "validUnits" => array('A'),
                "storageUnit" =>  'A',
                "function" => "test1",
                "extraText" => array("extraTest1", "extraTest2"),
                "extraDefault" => array("extraDefaultTest1", "extraDefaultTest2"),
                "unitModes" => array(
                    'A' => 'raw,diff',
                ),
                "inputSize" => 3,
                "doTotal" => true,
            ),
        ),
        0x101 => array(
            "test1" => array(
                "longName" => "Invalid1",
            ),
        ),
        0x102 => array(
            "test2" => array(
                "longName" => "Invalid2",
            ),
        ),
        0x103 => array(
            "test3" => array(
                "longName" => "Invalid3",
                "inputSize" => 0,
            ),
        ),
    );

    /**
     * Some Function
     *
     * @param float $val    The reading
     * @param array $sensor Sensor information array
     * @param int   $TC     The time constant
     * @param array $extra  Extra information from the sensor
     *
     * @return null
     */
    public function test1($val, $sensor, $TC, $extra)
    {
        // This must stay the same.
        return $val*$extra;
    }
    /**
     * Some Function
     *
     * @param float $val    The reading
     * @param array $sensor Sensor information array
     * @param int   $TC     The time constant
     * @param array $extra  Extra information from the sensor
     *
     * @return null
     */
    public function test2($val, $sensor, $TC, $extra)
    {
        return $val;
    }
    /**
     * Some Function
     *
     * @param int    $val    The current sensor value
     * @param array  $sensor The sensor information array
     * @param string $units  The units the current value are in
     * @param mixed  $dType  The data mode
     *
     * @return null
     */
    public function s2Check($val, $sensor, $units, $dType)
    {
        if ($val == 3) {
            return false;
        }
        return true;
    }

}
/**
 * This class is to test how things handle not having a sensors variable;
 *
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class TestSensorExtraSensors extends SensorBase
{
    /** Sensors array */
    var $sensors = array(
        0x102 => array(
            "test4" => array(
                "longName" => "Invalid4",
           ),
       ),
    );
}
/**
 * This class is to test how things handle not having a sensors variable;
 *
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class TestSensorNoSensors extends SensorBase
{
    /**
     * This is here so that it won't run the sensor_base constructor
     */
    function __construct()
    {
        // Make absolutely sure that there are no sensors
        unset($this->sensors);
    }
}
?>
