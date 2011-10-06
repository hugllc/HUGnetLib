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
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    git: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is a required class */
require_once CODE_BASE.'base/sensors/VoltageDeviceSensorBase.php';
/** This is a required class */
require_once CODE_BASE.'containers/ConfigContainer.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyDeviceContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class VoltageDeviceSensorBaseTest extends PHPUnit_Framework_TestCase
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
        $this->o = new TestVoltageDeviceSensor($data, $this->d);
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
    * Data provider for testGetDividerVoltage
    *
    * @return array
    */
    public static function dataGetDividerVoltage()
    {
        return array(
            array(1, 1, 0, 5, array(), 0.0),
            array(1, 1, 1, 5, array(), 0.0002),
            array(1000, 1, 1, 5, array(), 0.1527),
            array(1000, 1, 1, null, array("Vcc" => 5), 0.1527),
            array(null, 1, 1, 5, array(), null),
        );
    }
    /**
    * test
    *
    * @param int   $A       The incoming value
    * @param float $R1      The resistor to the voltage
    * @param float $R2      The resistor to ground
    * @param float $Vref    The reference voltage
    * @param array $preload The values to preload into the object
    * @param mixed $expect  The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetDividerVoltage
    */
    public function testGetDividerVoltage($A, $R1, $R2, $Vref, $preload, $expect)
    {
        $this->o->fromAny($preload);
        $ret = $this->o->getDividerVoltage($A, $R1, $R2, $Vref);
        $this->assertSame($expect, $ret);
    }


    /**
    * Data provider for testfetBoard
    *
    * @return array
    */
    public static function dataIndirect()
    {
        return array(
            array(
                1000,
                array("extra"=>array(1, 1, 5)),
                0.1527
            ),
            array(
                1000,
                array("extra"=>array(-10, 5, 5)),
                null
            ),
        );
    }
    /**
    * test
    *
    * @param float $val     The incoming value
    * @param int   $preload The values to preload into the object
    * @param mixed $expect  The expected return value
    *
    * @return null
    *
    * @dataProvider dataIndirect
    */
    public function testIndirect($val, $preload, $expect)
    {
        $this->o->fromAny($preload);
        $ret = $this->o->indirect($val);
        $this->assertSame($expect, $ret);
    }

    /**
    * Data provider for GetVoltage
    *
    * @return array
    */
    public static function dataGetVoltage()
    {
        return array(
            array(null, array(), 1, null),
            array(1, array("Vcc" => 1), null, 0.0),
            array(1, array(), 1, 0.0),
            array(4000, array(), 10, 0.6109),
            array(4000, array("s" => 0), 10, 0.0),
        );
    }
    /**
    * test
    *
    * @param int   $A       The AtoD reading
    * @param int   $preload The values to preload into the object
    * @param float $Vref    The voltage reference
    * @param mixed $expect  The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetVoltage
    */
    public function testGetVoltage($A, $preload, $Vref, $expect)
    {
        $this->o->fromAny($preload);
        $ret = $this->o->getVoltage($A, $Vref);
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
            array(10000, array("extra"=>array(5)), .7637),
            array(65535, array("extra"=>array(5)), null),
            array(-10000, array("extra"=>array(5)), null),
            array(null, array("extra"=>array(5)), null),
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
            array(10000, array("extra" => array(.5, 4.5, 0, 5, 5)), 0.3296),
            array(null,  array("extra" => array(.5, 4.5, 0, 5, 5)), null),
            array(0, array("extra" => array(.5, 4.5, 0, 5, 5)), null),
            array(65535, array("extra" => array(.5, 4.5, 0, 5, 5)), null),
            array(10000, array("extra" => array(5, 5, 0, 5, 5)), null),
            array(30000, array('extra' => array(1, 4, 0, 100, 5)), 43.0367),
            array(null, array('extra' => array(1, 4, 0, 100, 5)), null),
            array(0, array('extra' => array(1, 4, 0, 100, 5)), null),
            array(65535, array('extra' => array(1, 4, 0, 100, 5)), null),
            array(65536, array("extra" => array(5, 5, 0, 5, 5)), null),

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

     /**
    * Data provider for testLinearBoundedIndirect
    *
    * @return array
     */
    public static function dataLinearBoundedIndirect()
    {
        return array(
            array(
                10000,
                array("extra" => array(100, 10, 5, 45, 0, 1000, 5)),
                85.0125
            ),
            array(null, array("extra" => array(100, 10, 5, 45, 0, 1000, 5)), null),
            array(0, array("extra" => array(100, 10, 5, 45, 0, 1000, 5)), null),
            array(65535, array("extra" => array(100, 10, 5, 45, 0, 1000, 5)), null),
            array(
                10000, array('extra' => array(1000, 10, 5, 300, 0, 100, 5)), 24.4516
            ),
            array(null, array('extra' => array(1000, 10, 5, 300, 0, 100, 5)), null),
            array(0, array('extra' => array(1000, 10, 5, 300, 0, 100, 5)), null),
            array(65535, array('extra' => array(1000, 10, 5, 300, 0, 100, 5)), null),
            array(65535, array("extra" => array(100, 10, 25, 25, 0, 1000, 5)), null),

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
    * @dataProvider dataLinearBoundedIndirect
    */
    public function testLinearBoundedIndirect($val, $preload, $expect)
    {
        $this->o->fromAny($preload);
        $ret = $this->o->linearBoundedIndirect($val);
        $this->assertSame($expect, $ret);
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

}
/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Libraries
* @package    HUGnetLibTest
* @subpackage SuiteBase
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2011 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class TestVoltageDeviceSensor extends VoltageDeviceSensorBase
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
    * @param int   $A    The incoming value
    * @param float $R1   The resistor to the voltage
    * @param float $R2   The resistor to ground
    * @param float $Vref The voltage reveference
    *
    * @return float Voltage rounded to 4 places
    */
    public function getDividerVoltage($A, $R1, $R2, $Vref = null)
    {
        return parent::getDividerVoltage($A, $R1, $R2, $Vref);
    }

    /**
    * Volgate for the FET board voltage dividers
    *
    * @param float $val The incoming value
    *
    * @return float Voltage rounded to 4 places
    */
    public function indirect($val)
    {
        return parent::indirect($val);
    }

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
    * @param int   $A    The AtoD reading
    * @param float $Vref The voltage reference
    *
    * @return The units for a particular sensor type
    */
    public function getVoltage($A, $Vref)
    {
        return parent::getVoltage($A, $Vref);
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
    * @return output to 4 places
    */
    public function linearBoundedIndirect($A)
    {
        return parent::linearBoundedIndirect($A);
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
}

?>
