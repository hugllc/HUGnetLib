<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once CODE_BASE.'base/sensors/CurrentDeviceSensorBase.php';
require_once CODE_BASE.'containers/ConfigContainer.php';
require_once TEST_BASE.'stubs/DummyDeviceContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class CurrentDeviceSensorBaseTest extends PHPUnit_Framework_TestCase
{

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
        $config = array(
            "sockets" => array(
                array(
                    "dummy" => true,
                ),
            ),
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->d = new DummyDeviceContainer();
        $this->o = new TestCurrentDeviceSensor($data, $this->d);
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
        unset($this->o);
    }



    /**
    * data provider for testSet
    *
    * @return array
    */
    public static function dataSet()
    {
        return array(
            array("location", "six", "six"),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $var    The variable to set
    * @param mixed  $value  The value to set
    * @param mixed  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataSet
    */
    public function testSet($var, $value, $expect)
    {
        $this->o->$var = $value;
        $data = $this->readAttribute($this->o, "data");
        $this->assertSame($expect, $data[$var]);
    }


    /**
    * Data provider for GetVoltage
    *
    * @return array
    */
    public static function dataGetCurrent()
    {
        return array(
            array(500, array("timeConstant" => 1), 0.5, 1, 0.0764),
            array(0, array("timeConstant" => 3), 1, 2, 0.0),
            array(1, array("timeConstant" => 3), 0, 2, 0.0),
            array(1, array("timeConstant" => 3), 2, 0, 0.0),
            array(1, array("timeConstant" => 0), 2, 3, 0.0),
        );
    }
    /**
    * test
    *
    * @param int   $A       The AtoD reading
    * @param int   $preload The values to preload into the object
    * @param float $R       The resistance of the current sensing resistor
    * @param float $G       The gain of the circuit
    * @param mixed $expect  The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetCurrent
    */
    public function testGetCurrent($A, $preload, $R, $G, $expect)
    {
        $this->o->fromAny($preload);
        $ret = $this->o->getCurrent($A, $R, $G);
        $this->assertSame($expect, $ret);
    }

    /**
    * Data provider for testchsMss
    *
    * @return array
    */
    public static function dataDirect()
    {
        return array(
            array(
                500,
                array('extra'=>array(1, 1)),
                38.2
            ),
            array(
                500,
                array('extra'=>array(0.5, 1)),
                76.4
            ),
            array(
                500,
                array('extra'=>array(0.5, 1)),
                76.4
            ),
            array(
                500,
                array('extra'=>array(0, 0)),
                0.0
            ),
            array(
                null,
                array('extra'=>array(.5, 2)),
                null,
            ),
        );
    }
    /**
    * test
    *
    * @param float $val     The incoming value
    * @param array $preload The value to preload into the object
    * @param mixed $expect  The expected return value
    *
    * @return null
    *
    * @dataProvider dataDirect
    */
    public function testDirect($val, $preload, $expect)
    {
        $this->o->fromAny($preload);
        $ret = $this->o->direct($val);
        $this->assertSame($expect, $ret);
    }
    /**
    * Data provider for testLinearBounded
    *
    * @return array
    */
    public static function dataLinearBounded()
    {
        return array(
            array(20000, array("extra" => array(4, 20, -1.5, 1.5, 249, 1)), -1.1063),
            array(null,  array("extra" => array(.5, 4.5, 0, 5, 249, 1)), null),
            array(0, array("extra" => array(.5, 4.5, 0, 5, 249, 1)), null),
            array(65535, array("extra" => array(.5, 4.5, 0, 5, 249, 1)), null),
            array(10000, array("extra" => array(5, 5, 0, 5, 249, 1)), null),
            array(null, array('extra' => array(1, 4, 0, 100, 249, 1)), null),
            array(0, array('extra' => array(1, 4, 0, 100, 249, 1)), null),
            array(65535, array('extra' => array(1, 4, 0, 100, 249, 1)), null),
            array(65536, array("extra" => array(5, 5, 0, 5, 249, 1)), null),

        );
    }
    /**
    * test
    *
    * @param float $val     The incoming value
    * @param array $preload The value to preload into the object
    * @param mixed $expect  The expected return value
    *
    * @return null
    *
    * @dataProvider dataLinearBounded
    */
    public function testLinearBounded($val, $preload, $expect)
    {
        $this->o->fromAny($preload);
        $ret = $this->o->linearBounded($val);
        $this->assertSame($expect, $ret);
    }

}
/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Endpoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2011 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class TestCurrentDeviceSensor extends CurrentDeviceSensorBase
{
    /** @var object This is where we store our configuration */
    protected $unitTypeValues = array("b");
    /** @var object This is where we store our configuration */
    protected $unitsValues = array("d");
    /** @var object This is where we store our configuration */
    protected $typeValues = array("j");


    /**
    * This sensor returns us 10mV / % humidity
    *
    * @param float $A The incoming value
    *
    * @return direct voltage rounded to 4 places
    */
    public function direct($A)
    {
        return parent::direct($A);
    }

    /**
    * Gets the units for a sensor
    *
    * @param int   $A The AtoD reading
    * @param float $R The resistance of the current sensing resistor
    * @param float $G The gain of the circuit
    *
    * @return The units for a particular sensor type
    */
    public function getCurrent($A, $R, $G)
    {
        return parent::getCurrent($A, $R, $G);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    */
    public function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
    }
    /**
    * This will work with sensors that are linear and bounded
    *
    * Basically if we have a sensor that is linear and the ends
    * of the line are specified (max1,max2) and (min1,min2) then this
    * is the routine for you.
    *
    * Take the case of a pressure sensor.  We are give that at Vmax the
    * pressure is Pmax and at Vmin the pressure is Vmin.  That gives us
    * the boundries of the line.  The pressure has to be between Pmax and Pmin
    * and the voltage has to be between Vmax and Vmin.  If it is not null
    * is returned.
    *
    * Given the formula I am using, P MUST be in bounds.
    *
    * @param float $A The incoming value
    *
    * @return output rounded to 4 places
    */
    public function linearBounded($A)
    {
        return parent::linearBounded($A);
    }
}

?>
