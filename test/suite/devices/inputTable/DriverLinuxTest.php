<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable;
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/DriverLinux.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is our base class */
require_once dirname(__FILE__)."/drivers/DriverTestBase.php";
/** This is our interface */
require_once CODE_BASE."devices/inputTable/DriverInterface.php";

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DriverLinuxTest extends drivers\DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "DriverLinuxTestClass";
    /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    *
    * @access protected
    *
    * @return null
    */
    protected function setUp()
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock(array());
        $this->o = \HUGnet\devices\inputTable\DriverLinux::factory(
            "DriverLinuxTestClass", $sensor
        );
    }

    /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    *
    * @access protected
    *
    * @return null
    */
    protected function tearDown()
    {
        unset($this->o);
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataPresent()
    {
        return array(
            array(
                "ThisIsABadName",
                false,
            ),
            array(
                "unitType",
                true,
            ),
            array(
                "storageType",
                true,
            ),
            array(
                "testParam",
                true,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataPresent
    */
    public function testPresent($name, $expect)
    {
        $this->assertSame($expect, $this->o->present($name, 1));
    }
    /**
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testToArray()
    {
        $expect = array(
            'longName' => 'Unknown Sensor',
            'shortName' => 'Unknown',
            'unitType' => 'asdf',
            'virtual' => false,
            'bound' => false,
            'total' => false,
            'extraText' => Array ("a", "b", "c", "d", "e"),
            'extraDefault' => Array (2,3,5,7,11),
            'extraValues' => Array (5, 5, 5, 5, 5),
            'storageUnit' => 'unknown',
            'storageType' => 'raw',
            'maxDecimals' => 2,
            'testParam' => '12345',
            "dataTypes" => array(
                \HUGnet\devices\datachan\Driver::TYPE_RAW
                    => \HUGnet\devices\datachan\Driver::TYPE_RAW,
                \HUGnet\devices\datachan\Driver::TYPE_DIFF
                    => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                \HUGnet\devices\datachan\Driver::TYPE_IGNORE
                    => \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
            ),
            'inputSize' => 3,
        );
        $this->assertEquals($expect, $this->o->toArray(1));
    }
    /**
     * Data provider for testGetReading
     *
     * testGetReading($sensor, $A, $deltaT, $data, $prev, $expect)
     *
     * @return array
     */
    public static function dataGetReading()
    {
        return array(
            array(
                array(),
                256210,
                1,
                array(),
                array(),
                null,
            ),
        );
    }
    /**
    * Data provider for testDriversTest
    *
    * This extracts all of the drivers and puts them into an array to test.
    *
    * @return array
    */
    public static function dataDriversTest()
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock(array());
        $obj = Driver::factory("DriverLinuxTestClass", $sensor);
        $ret = array();
        $types = $obj->getTypesTest($i);
        foreach ($types as $name => $class) {
            $ret[] = array($i, $name, $class);
        }
        return $ret;
    }
    /**
    * test
    *
    * @param int    $sid   The sensor id
    * @param string $type  The type of sensor
    * @param string $class The driver class
    *
    * @return null
    *
    * @dataProvider dataDriversTest
    */
    public function testDriversTest($sid, $type, $class)
    {
        $file = CODE_BASE."devices/inputTable/drivers/linux/$class.php";
        $this->assertFileExists(
            $file, "File for $sid:$type and class $class not found"
        );
        include_once CODE_BASE."devices/inputTable/drivers/linux/$class.php";
        $this->assertTrue(
            class_exists(
                "\\HUGnet\\devices\\inputTable\\drivers\\linux\\".$class
            ),
            "Class $class doesn't exist for type $sid:$type in file $file"
        );
    }
    /**
     * Data provider for testEncodeData
     *
     * @return array
     */
    public static function dataEncodeDataPoint()
    {
        return array(
            array( // #0
                array(
                    "Sensor" => array(
                        "id" => 1,
                        "get" => array(
                            "sensor" => 2,
                            "extra" => array(),
                        ),
                    ),
                ),
                "0E0000",
                1,
                array(),
                array(),
                14.314713,
                0,
            ),
        );
    }
}
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers;
/**
 * Base driver class for devices.
 *
 * This class deals with loading the drivers and figuring out what driver needs
 * to be loaded.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DriverLinuxTestClass extends \HUGnet\devices\inputTable\DriverLinux
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Unknown Sensor",
        "shortName" => "Unknown",
        "unitType" => "asdf", /* This is for test value only */
        "testParam" => "12345", /* This is for test value only */
        "extraDefault" => array(2,3,5,7,11),
        "extraText" => array("a","b","c","d","e"),
        "extraValues" => array(5, 5, 5, 5, 5),
    );
    /**
    * Gets the extra values
    *
    * @param int $index The extra index to use
    *
    * @return The extra value (or default if empty)
    */
    public function getExtra($index)
    {
        return parent::getExtra($index);
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
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        return null;
    }
    /**
    * This returns the voltage on the upper side of a voltage divider if the
    * AtoD input is in the middle of the divider
    *
    * @param int   $A    The incoming value
    * @param float $R1   The resistor to the voltage
    * @param float $R2   The resistor to ground
    * @param float $Vref The voltage reveference
    * @param int   $Tc   The time constant
    *
    * @return float Voltage rounded to 4 places
    */
    public function getDividerVoltage($A, $R1, $R2, $Vref, $Tc)
    {
        return parent::getDividerVoltage($A, $R1, $R2, $Vref, $Tc);
    }
    /**
    * This returns the voltage on the upper side of a voltage divider if the
    * AtoD input is in the middle of the divider
    *
    * @param int   $V    The incoming value
    * @param float $R1   The resistor to the voltage
    * @param float $R2   The resistor to ground
    * @param float $Vref The voltage reveference
    * @param int   $Tc   The time constant
    *
    * @return float Voltage rounded to 4 places
    */
    public function revDividerVoltage($V, $R1, $R2, $Vref, $Tc)
    {
        return parent::revDividerVoltage($V, $R1, $R2, $Vref, $Tc);
    }
    /**
    * This returns the voltage that the port is seeing
    *
    * @param int   $A    The AtoD reading
    * @param float $Vref The voltage reference
    * @param int   $Tc   The time constant
    *
    * @return The units for a particular sensor type
    */
    public function getVoltage($A, $Vref, $Tc)
    {
        return parent::getVoltage($A, $Vref, $Tc);
    }
    /**
    * This returns the voltage that the port is seeing
    *
    * @param int   $V    The Voltage
    * @param float $Vref The voltage reference
    * @param int   $Tc   The time constant
    *
    * @return The units for a particular sensor type
    */
    public function revVoltage($V, $Vref, $Tc)
    {
        return parent::revVoltage($V, $Vref, $Tc);
    }

    /**
    * Volgate for the FET board voltage dividers
    *
    * @param float $val The incoming value
    * @param int   $Tc  The time constant
    *
    * @return float Voltage rounded to 4 places
    */
    public function indirectVoltage($val, $Tc)
    {
        return parent::indirectVoltage($val, $Tc);
    }

    /**
    * This sensor returns us 10mV / % humidity
    *
    * @param float $A  The incoming value
    * @param int   $Tc The time constant
    *
    * @return float Relative Humidity rounded to 4 places
    */
    public function directVoltage($A, $Tc)
    {
        return parent::directVoltage($A, $Tc);
    }
    /**
    * This takes in a raw AtoD reading and returns the current.
    *
    * This is further documented at: {@link
    * https://dev.hugllc.com/index.php/Project:HUGnet_Current_Sensors Current
    * Sensors }
    *
    * @param int   $A    The raw AtoD reading
    * @param float $R    The resistance of the current sensing resistor
    * @param float $G    The gain of the circuit
    * @param float $Vref The voltage reference
    * @param int   $Tc   The time constant
    *
    * @return float The current sensed
    */
    public function getCurrent($A, $R, $G, $Vref, $Tc)
    {
        return parent::getCurrent($A, $R, $G, $Vref, $Tc);
    }
    /**
    * This takes in a raw AtoD reading and returns the current.
    *
    * This is further documented at: {@link
    * https://dev.hugllc.com/index.php/Project:HUGnet_Current_Sensors Current
    * Sensors }
    *
    * @param int   $I    The raw AtoD reading
    * @param float $R    The resistance of the current sensing resistor
    * @param float $G    The gain of the circuit
    * @param float $Vref The voltage reference
    * @param int   $Tc   The time constant
    *
    * @return float The current sensed
    */
    public function revCurrent($I, $R, $G, $Vref, $Tc)
    {
        return parent::revCurrent($I, $R, $G, $Vref, $Tc);
    }

    /**
    *  This is specifically for the current sensor in the FET board.
    *
    * @param float $val The incoming value
    * @param int   $Tc  The time constant
    *
    * @return float Current in amps rounded to 1 place
    */
    public function directCurrent($val, $Tc)
    {
        return parent::directCurrent($val, $Tc);
    }
    /**
    * Converts a raw AtoD reading into resistance
    *
    * This function takes in the AtoD value and returns the calculated
    * resistance of the sensor.  It does this using a fairly complex
    * formula.  This formula and how it was derived is detailed in
    *
    * @param int   $A    Integer The AtoD reading
    * @param float $Bias Float The bias resistance in kOhms
    * @param int   $Tc   The time constant
    *
    * @return The resistance corresponding to the values given in k Ohms
    */
    public function getResistance($A, $Bias, $Tc)
    {
        return parent::getResistance($A, $Bias, $Tc);
    }
    /**
    * Converts a raw AtoD reading into resistance
    *
    * This function takes in the AtoD value and returns the calculated
    * resistance of the sensor.  It does this using a fairly complex
    * formula.  This formula and how it was derived is detailed in
    *
    * @param int   $R    Integer The AtoD reading
    * @param float $Bias Float The bias resistance in kOhms
    * @param int   $Tc   The time constant
    *
    * @return The value corresponding the the resistance given
    */
    public function revResistance($R, $Bias, $Tc)
    {
        return parent::revResistance($R, $Bias, $Tc);
    }
    /**
    * Converts a raw AtoD reading into resistance
    *
    * If you connect the two ends of a pot up to Vcc and ground, and connect the
    * sweep terminal to the AtoD converter, this function returns the
    * resistance between ground and the sweep terminal.
    *
    * This function takes in the AtoD value and returns the calculated
    * resistance that the sweep is at.  It does this using a fairly complex
    * formula.  This formula and how it was derived is detailed in
    *
    * @param int   $A  Integer The AtoD reading
    * @param float $R  Float The overall resistance in kOhms
    * @param int   $Tc The time constant
    *
    * @return The resistance corresponding to the values given in k Ohms
    */
    public function getSweep($A, $R, $Tc)
    {
        return parent::getSweep($A, $R, $Tc);
    }
    /**
    * Converts a raw AtoD reading into resistance
    *
    * If you connect the two ends of a pot up to Vcc and ground, and connect the
    * sweep terminal to the AtoD converter, this function returns the
    * resistance between ground and the sweep terminal.
    *
    * This function takes in the AtoD value and returns the calculated
    * resistance that the sweep is at.  It does this using a fairly complex
    * formula.  This formula and how it was derived is detailed in
    *
    * @param int   $Rs Integer The AtoD reading
    * @param float $R  Float The overall resistance in kOhms
    * @param int   $Tc The time constant
    *
    * @return The resistance corresponding to the values given in k Ohms
    */
    public function revSweep($Rs, $R, $Tc)
    {
        return parent::revSweep($Rs, $R, $Tc);
    }
    /**
    * This function should be called with the values set for the specific
    * thermistor that is used.
    *
    * @param float $R      The current resistance of the thermistor in ohms
    * @param array &$table The table to use
    *
    * @return float The Temperature in degrees C
    */
    public function tableInterpolate($R, &$table)
    {
        return parent::tableInterpolate($R, $table);
    }
    /**
    * Returns an array of types that this sensor could be
    *
    * @param int $sid The ID to check
    *
    * @return The extra value (or default if empty)
    */
    public static function getTypesTest($sid)
    {
        return static::$drivers;
    }
}
?>
