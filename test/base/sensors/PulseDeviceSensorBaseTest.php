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


require_once dirname(__FILE__)
    .'/../../../base/sensors/PulseDeviceSensorBase.php';
require_once dirname(__FILE__).'/../../../containers/ConfigContainer.php';
require_once dirname(__FILE__).'/../../stubs/DummyDeviceContainer.php';

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
class PulseDeviceSensorBaseTest extends PHPUnit_Framework_TestCase
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
        $this->o = new TestPulseDeviceSensor($data, $this->d);
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
    * Data provider for testPulseCheck
    *
    * @return array
    */
    public static function dataPulseCheck()
    {
        return array(
            array(1, array(), true),
            array(0, array(), true),
            array(-1, array(),  false),
        );
    }

    /**
    * test
    *
    * @param int   $value   The current sensor value
    * @param array $preload The preload for the class
    * @param bool  $expect  The expected return value
    *
    * @return null
    *
    * @dataProvider dataPulseCheck
    */
    public function testPulseCheck($value, $preload, $expect)
    {
        $this->o->fromAny($preload);
        $this->assertSame($expect, $this->o->pulseCheck($value));
    }


    /**
    * Data provider for testWattNode
    *
    * @return array
    */
    public static function dataLiquidFlowMeter()
    {
        return array(
            array(500, array("extra" => array(5)), 300, 100.0),
            array(500, array("extra" => array(-5)), 300, null),
            array(500, array("extra" => array(0)), 300, 500.0),
        );
    }

    /**
    * test
    *
    * @param int   $val     Output of the A to D converter
    * @param array $preload The sensor information array
    * @param float $deltaT  The time delta in seconds between this record
    * @param float $expect  The expected return value
    *
    * @return null
    *
    * @dataProvider dataLiquidFlowMeter
    */
    public function testLiquidFlowMeter($val, $preload, $deltaT, $expect)
    {
        $this->o->fromAny($preload);
        $this->assertSame($expect, $this->o->liquidFlowMeter($val, $deltaT));
    }

    /**
    * Data provider for testGetPPM
    *
    * @return array
    */
    public static function dataGetPPM()
    {
        return array(
            array(array(), 500, 300, 100.0),
            array(array(), 500, 0, null),
            array(array(), 500, -1, null),
            array(array(), -1, 300, null),
        );
    }
    /**
    * test
    *
    * @param array $preload The values to preload into the object
    * @param int   $A       The a to d reading
    * @param float $deltaT  The bias resistance
    * @param mixed $expect  The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetPPM
    */
    public function testGetPPM($preload, $A, $deltaT, $expect)
    {
        $this->o->fromAny($preload);
        $this->assertSame($expect, $this->o->getPPM($A, $deltaT));
    }
    /**
    * Data provider for testGetPPM
    *
    * @return array
    */
    public static function dataFromArray()
    {
        return array(
            array(
                array("id" => 0x7F),
                array("longName" => "High Speed Unknown Sensor"),
            ),
        );
    }
    /**
    * test
    *
    * @param array $preload The values to preload into the object
    * @param mixed $expect  The expected return value
    *
    * @return null
    *
    * @dataProvider dataFromArray
    */
    public function testFromArray($preload, $expect)
    {
        $this->o->fromArray($preload);
        foreach ($expect as $var => $val) {
            $this->assertSame($val, $this->o->$var);
        }
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
class TestPulseDeviceSensor extends PulseDeviceSensorBase
{
    /** @var object This is where we store our configuration */
    protected $unitTypeValues = array("b");
    /** @var object This is where we store our configuration */
    protected $unitsValues = array("d");
    /** @var object This is where we store our configuration */
    protected $typeValues = array("j");
    /**
    * Returns whether the reading is valid
    *
    * @param int $value The current sensor value
    *
    * @return bool
    */
    public function pulseCheck($value)
    {
        return parent::pulseCheck($value);
    }

    /**
    * Crunches the numbers for the Liquid Flow Meter
    *
    * @param int   $val    Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    *                      and the last one
    *
    * @return float
    */
    public function liquidFlowMeter($val, $deltaT=null)
    {
        return parent::liquidFlowMeter($val, $deltaT);
    }

    /**
    * This is for a generic pulse counter
    *
    * @param int   $val    Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    *                      and the last one
    *
    * @return float
    */
    public function getPPM($val, $deltaT)
    {
        return parent::getPPM($val, $deltaT);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    *
    * @return mixed The value in whatever the units are in the sensor
    */
    public function getReading($A, $deltaT = 0, &$data = array())
    {
        return $val;
    }
    /**
    * function to set type
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setId($value)
    {
        $this->data["id"] = (int)$value;
    }


}

?>
