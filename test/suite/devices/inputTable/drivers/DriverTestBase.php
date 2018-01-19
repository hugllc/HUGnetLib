<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers;
/** This is the base class */
require_once CODE_BASE."/devices/inputTable/Driver.php";
/** This is a required class */
require_once TEST_CONFIG_BASE.'suite/devices/IOPDriverTestBase.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class DriverTestBase extends \HUGnet\devices\IOPDriverTestBase
{
    /** This is the class we are testing */
    protected $class = "";
    /** This is the object we are testing */
    protected $o = null;

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
        parent::setUp();
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
        parent::tearDown();
    }

    /**
    * test the set routine when an extra class exists
    *
    * @return null
    */
    public function testFactory()
    {
        $this->assertSame(
            "HUGnet\devices\inputTable\drivers\\".$this->class, get_class($this->o)
        );
    }



    /**
    * data provider for testType
    *
    * @return array
    */
    final public static function dataInternalType()
    {
        return array(
            array("longName", "string"),
            array("shortName", "string"),
            array("extraText", "array"),
            array("extraValues", "array"),
            array("extraDefault", "array"),
            array("extraDesc", "array"),
            array("extraNames", "array"),
            array("bound", "boolean"),
            array("virtual", "boolean"),
            array("total", "boolean"),
            array("storageUnit", "string"),
            array("maxDecimals", "int"),
            array("unitType", "string"),
            array("dataTypes", "array"),
            array("provides", "array"),
            array("requires", "array"),
        );
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    final public static function dataStringSize()
    {
        return array(
            array("longName", 40, 10),
            array("shortName", 15, 1),
            array("storageUnit", 15, 1),
            array("unitType", 20, 1),
        );
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    public static function dataStringRegex()
    {
        return array(
            array("unitType", "/[A-Za-z0-9]*/"),
        );
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    final public static function dataIntSize()
    {
        return array(
            array("maxDecimals", 10, 0),
        );
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    final public static function dataValues()
    {
        return array(
            array(
                "storageType", array(
                    \HUGnet\devices\datachan\Driver::TYPE_RAW,
                    \HUGnet\devices\datachan\Driver::TYPE_DIFF,
                    \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
                ),
            ),
        );
    }
    /**
    * Check the extraText value size
    *
    * @return null
    */
    public function testDataTypesKeys()
    {
        $validTypes = array(
            \HUGnet\devices\datachan\Driver::TYPE_RAW
                => '\HUGnet\devices\datachan\Driver::TYPE_RAW',
            \HUGnet\devices\datachan\Driver::TYPE_DIFF
                => '\HUGnet\devices\datachan\Driver::TYPE_DIFF',
            \HUGnet\devices\datachan\Driver::TYPE_IGNORE
                => '\HUGnet\devices\datachan\Driver::TYPE_IGNORE',
        );
        $extra = $this->o->get("dataTypes", 1);
        $this->assertInternalType("array", $extra);
        foreach ($extra as $key => $value) {
            $this->assertTrue(
                isset($validTypes[$key]),
                "Valid dataTypes keys are ".implode(", ", $validTypes)
            );
            $this->assertSame(
                $key,
                $value,
                "In dataTypes, key must equal value, ".$validTypes[$key]
                ." != ".$validTypes[$value]
            );
        }
    }
    /**
    * Check the number of entries in extraText
    *
    * @return null
    */
    public function testDataTypesCount()
    {
        $count = 3;
        $extra   = (array)$this->o->get("dataTypes", 1);
        $this->assertLessThanOrEqual(
            $count,
            count($dataTypes),
            "dataTypes must have $count or less entries"
        );
    }


    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGet()
    {
        return array(
            array(
                "ThisIsABadName",
                array(),
                null,
            ),
        );
    }
    /**
     * Data provider for testGetReading
     *
     * testGetReading($sensor, $A, $deltaT, $data, $prev, $expect, $channel = 0)
     *
     * @return array
     */
    public static function dataGetReading()
    {
        return array();
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @param array $sensor  The sensor data array
    * @param mixed $A       Data for the sensor to work on
    * @param float $deltaT  The time differenct
    * @param array $data    The data array being built
    * @param array $prev    The previous record
    * @param mixed $expect  The return data to expect
    * @param int   $channel The channel to test
    *
    * @return null
    *
    * @dataProvider dataGetReading()
    */
    public function testGetReading(
        $sensor, $A, $deltaT, $data, $prev, $expect, $channel = 0
    ) {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock($sensor);
        $ret = $this->o->decodeDataPoint($A, $channel, $deltaT, $prev, $data);
        $this->assertEquals($expect, $ret, "", 0.00001);
    }
    /**
    * DecodeData MUST return a value
    *
    * @return null
    *
    */
    public function testDecodeDataValue() 
    {
        $A = 0;
        $deltaT = 0;
        $prev = array();
        $data = array();
        $chan = 0;
        $ret = $this->o->decodeData(
            $A, 
            $chan,
            $deltaT, 
            $prev, 
            $data
        );
        $this->assertInternalType("array", $ret);
        foreach (array_keys($ret) as $key) {
            $this->assertArrayHasKey(
                "value",
                (array)$ret[$key],
                "Decoded array must have key 'value'"
            );
        }
    }
    /**
    * DecodeData MUST return a value
    *
    * @return null
    *
    */
    public function testDecodeDataRaw() 
    {
        $A = 0;
        $deltaT = 0;
        $prev = array();
        $data = array();
        $chan = 0;
        $ret = $this->o->decodeData(
            $A, 
            $chan,
            $deltaT, 
            $prev, 
            $data
        );
        $this->assertInternalType("array", $ret);
        foreach (array_keys($ret) as $key) {
            $this->assertArrayHasKey(
                "raw", 
                (array)$ret[$key],
                "Decoded array must have key 'raw'"
            );
        }
    }
    /**
     * Data provider for testGetReading
     *
     * testGetReading($sensor, $A, $deltaT, $data, $prev, $expect, $channel = 0)
     *
     * @return array
     */
    public static function dataEncodeDataPoint()
    {
        return array();
    }
    /**
    * Generic function for testing sensor routines
    *
    * This is called by using parent::sensorTest()
    *
    * @param array $sensor  The sensor data array
    * @param mixed $expect  Data for the sensor to work on
    * @param float $deltaT  The time differenct
    * @param array $data    The data array being built
    * @param array $prev    The previous record
    * @param mixed $A       The return data to expect
    * @param int   $channel The channel to test
    *
    * @return null
    *
    * @dataProvider dataEncodeDataPoint()
    */
    public function testEncodeDataPoint(
        $sensor, $expect, $deltaT, $data, $prev, $A, $channel = 0
    ) {
        $sen = new \HUGnet\DummyBase("Sensor");
        $sen->resetMock($sensor);
        $ret = $this->o->encodeDataPoint($A, $channel, $deltaT, $prev, $data);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataChannels()
    {
        return array(
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The mocks to use
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataChannels
    */
    public function testChannels($mocks, $name, $expect)
    {
        if (!empty($mocks)) {
            $this->input->resetMock($mocks);
        }
        $this->assertSame($expect, $this->o->channels());
    }
}
?>
