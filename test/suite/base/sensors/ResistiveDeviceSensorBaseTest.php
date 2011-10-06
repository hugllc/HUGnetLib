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
require_once CODE_BASE.'base/sensors/ResistiveDeviceSensorBase.php';
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
class ResistiveDeviceSensorBaseTest extends PHPUnit_Framework_TestCase
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
        $this->o = new TestResistiveDeviceSensor($data, $this->d);
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
     * Data provider for testGetResistance
     *
     * @return array
     */
    public static function dataGetResistance()
    {
        return array(
            array(array("timeConstant" => 0), 0, 1, 0.0),
            array(array(), 10000, 10, 1.8027),
            array(array("D" => 0), 10000, 10, 0.0),
        );
    }
    /**
    * test
    *
    * @param array $preload The values to preload into the object
    * @param int   $A       The a to d reading
    * @param float $Bias    The bias resistance
    * @param mixed $expect  The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetResistance
    */
    public function testGetResistance($preload, $A, $Bias, $expect)
    {
        $this->o->fromAny($preload);
        $this->assertSame($expect, $this->o->getResistance($A, $Bias));
    }

    /**
    * Data provider for testGetResistance
    *
    * @return array
    */
    public static function dataGetSweep()
    {
        return array(
            array(array(), 0, 1, 0.0),
            array(array(), 10000, 10, 1.5274),
            array(array(), 65535, 10, 10.0),
            array(array("D" => 0), 10000, 10, 0.0),
            array(array("Tf" => 0), 10000, 10, 10.0),
            array(array("Tf" => -1), 10000, 10, 0.0),
        );
    }
    /**
    * test
    *
    * @param array $preload The values to preload into the object
    * @param int   $A       The a to d reading
    * @param float $R       The bias resistance
    * @param mixed $expect  The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetSweep
    */
    public function testGetSweep($preload, $A, $R, $expect)
    {
        $this->o->fromAny($preload);
        $this->assertSame($expect, $this->o->getSweep($A, $R));
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
class TestResistiveDeviceSensor extends ResistiveDeviceSensorBase
{
    /** @var object This is where we store our configuration */
    protected $unitTypeValues = array("b");
    /** @var object This is where we store our configuration */
    protected $unitsValues = array("d");
    /** @var object This is where we store our configuration */
    protected $typeValues = array("j");
    /**
    * Converts a raw AtoD reading into resistance
    *
    * @param int   $A    Integer The AtoD reading
    * @param float $Bias Float The bias resistance in kOhms
    *
    * @return The resistance corresponding to the values given in k Ohms
    */
    public function getResistance($A, $Bias)
    {
        return parent::getResistance($A, $Bias);
    }

    /**
    * Converts a raw AtoD reading into resistance
    *
    * @param int   $A Integer The AtoD reading
    * @param float $R Float The overall resistance in kOhms
    *
    * @return The resistance corresponding to the values given in k Ohms
    */
    public function getSweep($A, $R)
    {
        return parent::getSweep($A, $R);
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
