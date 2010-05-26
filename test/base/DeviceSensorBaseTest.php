<?php
/**
 * Tests the filter class
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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../base/DeviceSensorBase.php';
require_once dirname(__FILE__).'/../../containers/ConfigContainer.php';
require_once dirname(__FILE__).'/../stubs/DummyDeviceContainer.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceSensorBaseTest extends PHPUnit_Framework_TestCase
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
        $this->o = new TestDeviceSensor($data, $this->d);
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
    * data provider for testConstructor
    *
    * @return array
    */
    public static function dataConstructor()
    {
        return array(
            array(
                array(),
                array(
                    "location" => "f",
                    "id" => 0,
                    "type" => "sensor",
                    "units" => "q",
                    "unitType" => "unknown",
                    "dataType" => "raw",
                    "extra" => array(),
                    "rawCalibration" => "cali",
                ),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param mixed  $preload The stuff to give to the constructor
    * @param string $expect  The expected data
    *
    * @return null
    *
    * @dataProvider dataConstructor
    */
    public function testConstructor($preload, $expect)
    {
        $o = new TestDeviceSensor($data, $this->d);
        $this->assertAttributeSame($expect, "data", $o, "Wrong data in class");
        $config = $this->readAttribute($o, "myConfig");
        $this->assertSame(
            "ConfigContainer", get_class($config), "Wrong config class"
        );
        $device = $this->readAttribute($o, "myDevice");
        $this->assertSame(
            get_class($this->d), get_class($device), "Wrong device class"
        );
    }
    /**
    * data provider for testSet
    *
    * @return array
    */
    public static function dataSet()
    {
        return array(
            array("units", "a", "q"),
            array("units", "d", "d"),
            array("unitType", "b", "b"),
            array("unitType", "a", "unknown"),
            array("dataType", "raw", "raw"),
            array("dataType", "Ignore", "ignore"),
            array("dataType", "diff", "diff"),
            array("dataType", "SomethingElse", "raw"),
            array("type", "SomethingElse", "sensor"),
            array("type", "j", "j"),
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

}
/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Endpoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2010 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class TestDeviceSensor extends DeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "testSensor",
        "Type" => "sensor",
        "Class" => "TestSensor",
        "Sensors" => array(),
    );
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "location" => "f",                // The location of the sensors
        "id" => 0,                      // The id of the sensor.  This is the value
                                         // Stored in the device  It will be an int
        "type" => "sensor",                    // The type of the sensors
        "units" => "q",                   // The units the values are stored in
        "unitType" => "unknown",         // The type of units that this uses
        "dataType" => "raw",             // The datatype of each sensor
        "extra" => array(),              // Extra input for crunching numbers
        "rawCalibration" => "cali",          // The raw calibration string
    );
    /** @var object This is where we store our configuration */
    protected $unitTypeValues = array("b");
    /** @var object This is where we store our configuration */
    protected $unitsValues = array("d");
    /** @var object This is where we store our configuration */
    protected $typeValues = array("j");

}

?>
