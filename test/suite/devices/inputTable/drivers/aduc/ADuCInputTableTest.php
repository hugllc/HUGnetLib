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
namespace HUGnet\devices\inputTable\drivers\aduc;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBaseADuC.php";
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/drivers/aduc/ADuCInputTable.php';
/** This is a required class */
require_once CODE_BASE.'db/Table.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';

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
class ADuCInputTableTest extends DriverTestBaseADuC
{
    /** This is the class we are testing */
    protected $class = "ADuCInputTable";
    /** This is our system object */
    protected $system;
    /** This is our output object */
    protected $input;
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
        $this->system = $this->getMock("\HUGnet\System", array("now"));
        $this->system->expects($this->any())
            ->method('now')
            ->will($this->returnValue(123456));
        $this->input = $this->system->device()->input(0);
        $this->o = ADuCInputTable::testFactory($this->input);
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
    * data provider for testType
    *
    * @return array
    */
    public static function dataArrayFieldType()
    {
        $ret = array();
        foreach (array("storageUnit", "unitType") as $field) {
            for ($i = 1; $i < 9; $i++) {
                $ret[] = array($field, $i, "string");
            }
        }
        return $ret;
    }
    /**
    * Check the variable type
    *
    * @param string $field  The field to check
    * @param int    $sensor The sensor number
    * @param string $type   The type it should be
    *
    * @return null
    *
    * @dataProvider dataArrayFieldType
    */
    public function testArrayFieldType($field, $sensor, $type)
    {
        $name = $this->o->get($field, $sensor);
        $this->assertInternalType($type, $name, "$field must be a $type");
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    public static function dataArrayFieldSize()
    {
        $ret = array();
        for ($i = 1; $i < 9; $i++) {
            $ret[] = array("storageUnit", $i, 1, 15);
            $ret[] = array("unitType", $i, 1, 20);
        }
        return $ret;
    }
    /**
    * Check the variable type
    *
    * @param string $field  The field to check
    * @param int    $sensor The sensor number
    * @param int    $min    The minimum size
    * @param int    $max    The maximum size
    *
    * @return null
    *
    * @dataProvider dataArrayFieldSize
    */
    public function testArrayFieldSizeMin($field, $sensor, $min, $max)
    {
        $name = $this->o->get($field, $sensor);
        $this->assertGreaterThanOrEqual(
            $min, strlen($name), "$field:$sensor must be at least $min characters"
        );
    }
    /**
    * Check the variable type
    *
    * @param string $field  The field to check
    * @param int    $sensor The sensor number
    * @param int    $min    The minimum size
    * @param int    $max    The maximum size
    *
    * @return null
    *
    * @dataProvider dataArrayFieldSize
    */
    public function testArrayFieldSizeMax($field, $sensor, $min, $max)
    {
        $name = $this->o->get($field, $sensor);
        $this->assertLessThanOrEqual(
            $max, strlen($name), "$field:$sensor must be at most $max characters"
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataChannels()
    {
        return array(
            array(
                array(
                    "id" => 1,
                    "extra" => array(),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                            "driver0" => 0x04,
                            "driver1" => 0x41,
                            "ADC0CH"  => 1,
                            "ADC1CH"  => 5,
                        )
                    ),
                ),
                "\HUGnet\devices\inputTable\drivers\ADuCInputTable",
                array(
                    0 => Array (
                        'decimals' => 4,
                        'units' => '&#176;C',
                        'maxDecimals' => 4,
                        'storageUnit' => '&#176;C',
                        'unitType' => 'Temperature',
                        'dataType' => 'raw',
                        "label" => "asdf",
                        "index" => 0,
                        "epChannel" => true,
                        "port" => "Port4",
                    ),
                    1 => Array (
                        'decimals' => 8,
                        'units' => 'V',
                        'maxDecimals' => 8,
                        'storageUnit' => 'V',
                        'unitType' => 'Voltage',
                        'dataType' => 'raw',
                        "label" => "asdf",
                        "index" => 1,
                        "epChannel" => true,
                        "port" => "Port1",
                    ),
                ),
            ),
            array(
                array(
                    "id" => 1,
                    "extra" => array(""),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                            "driver0" => 0x04,
                            "driver1" => 0xFF,
                            "ADC0CH"  => 1,
                            "ADC1CH"  => 5,
                        )
                    ),
                ),
                "\HUGnet\devices\inputTable\drivers\ADuCInputTable",
                array(
                    0 => Array (
                        'decimals' => 4,
                        'units' => '&#176;C',
                        'maxDecimals' => 4,
                        'storageUnit' => '&#176;C',
                        'unitType' => 'Temperature',
                        'dataType' => 'raw',
                        "label" => "asdf",
                        "index" => 0,
                        "epChannel" => true,
                        "port" => "Port4",
                    ),
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The mocks to set
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataChannels
    */
    public function testChannels($mocks, $name, $expect)
    {
        $this->input->load($mocks);
        $channels = $this->o->channels();
        $this->assertSame($expect, $channels);
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
            array( // #0
                array(
                    "id" => 1,
                    "input" => 2,
                    "extra" => array("41", 1, 0),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                            "driver0" => 0x41,
                            "ADC0CH" => 1,
                            "ADC1CH" => 1,
                        )
                    ),
                ),
                "40420F0040420F00",
                1,
                array(),
                array(),
                array(
                    array(
                        'decimals' => 8,
                        'units' => 'V',
                        'maxDecimals' => 8,
                        'storageUnit' => 'V',
                        'unitType' => 'Voltage',
                        'dataType' => 'raw',
                        'value' => 14.44816589,
                        "label" => "asdf",
                        "index" => 0,
                        "epChannel" => true,
                        "port" => "Port4",
                    ),
                ),
            ),
            array( // #1
                array(
                    "id" => 1,
                    "input" => 2,
                    "extra" => array("44", 1, 0, 0, 5, 0, 200, 1.2, 100000, 1000),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                            "driver0" => 0x44,
                        )
                    ),
                ),
                "00DAF9FF",
                1,
                array(),
                array(),
                array(
                    array(
                        'decimals' => 4,
                        'units' => 'psi',
                        'maxDecimals' => 4,
                        'storageUnit' => 'psi',
                        'unitType' => 'Pressure',
                        'dataType' => 'raw',
                        'value' => -232.8721,
                        "label" => "asdf",
                        "index" => 0,
                        "epChannel" => true,
                        "port" => 'VREF+,VREF-',
                    ),
                ),
            ),
        );
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
        $this->input->load($sensor);
        $ret = $this->o->decodeData($A, $deltaT, $data, $prev);
        $this->assertEquals($expect, $ret, 0.00001);
    }
    /**
     * Data provider for testGetReading
     *
     * testGetReading($sensor, $A, $deltaT, $data, $prev, $expect)
     *
     * @return array
     */
    public static function dataEncodeDataPoint()
    {
        return array(
            array( // #0
                array(
                    "id" => 0xF9,
                    "extra" => array(
                        "41", 1, 0
                    ),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                            "driver0" => 0x41,
                        )
                    ),
                ),
                "40420F00",
                1,
                array(),
                array(),
                14.44816589,
            ),
            array( // #1
                array(
                    "id" => 0xF9,
                    "extra" => array(
                        "44", 1, 0,
                        0, 5, 0, 200, 1.2, 100000, 1000
                    ),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                            "driver0" => 0x44,
                        )
                    ),
                ),
                "00DAF9FF",
                1,
                array(),
                array(),
                -232.8721,
            ),
        );
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
        $this->input->load($sensor);
        $ret = $this->o->encodeDataPoint($A, $channel, $deltaT, $prev, $data);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGet()
    {
        $system = new \HUGnet\DummySystem("System");
        return array(
            array(
                "ThisIsABadName",
                array(
                ),
                null,
            ),
            array(
                "maxDecimals",
                array(
                    "id" => 5,
                ),
                6,
            ),
            array(
                "extraValues",
                array(
                    "id" => 0xF9,
                    "extra" => array(1, 2, 3),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                            "driver0" => 0x41,
                        )
                    ),
                ),
                array(
                    -1,
                    10, 10, 5, 5, 5
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $name   The name of the variable to test.
    * @param array  $mock   The mocks to set up
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet($name, $mock, $expect)
    {
        $this->input->load($mock);
        $this->assertSame($expect, $this->o->get($name));
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataExtra()
    {
        return array(
            array(
                0,
                array(
                    "Sensor" => array(
                        "id" => 5,
                    ),
                ),
                "",
            ),
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDecode()
    {
        return array(
            array( // #0 Test no drivers
                array(
                    "id" => 0xF9,
                    "extra" => array(1, 2, 3),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                        )
                    ),
                ),
                "FF00C0800086098041FF000102030405060708",
                array(
                    "extra" => array(1, 972.44803691, 3),
                    "id" => 0xF9,
                    "location" => "asdf",
                    'driver' => 'ADuCInputTable',

                ),
            ),
            array( // #1 Test extra[0] not set, but found
                array(
                    "id" => 0xF9,
                    "extra" => array(1, 2, 3),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                            'driver0' => 0x41,
                            'driver1' => 255,
                            'priority' => 0,
                            'process' => 0,
                            'process1' => 1,
                            'ADC0EN' => 1,
                            'ADC0DIAG' => 0,
                            'HIGHEXTREF0' => 0,
                            'AMP_CM' => 0,
                            'ADC0CODE' => 0,
                            'ADC0CH' => 3,
                            'ADC0REF' => 0,
                            'ADC0PGA' => 0,
                            'ADC1EN' => 1,
                            'ADC1DIAG' => 0,
                            'HIGHEXTREF1' => 0,
                            'ADC1CODE' => 0,
                            'ADC1CH' => 12,
                            'ADC1REF' => 0,
                            'BUF_BYPASS' => 0,
                            'ADC1PGA' => 0,
                            'CHOPEN' => 1,
                            'RAVG2' => 0,
                            'AF' => 0,
                            'NOTCH2' => 0,
                            'SF' => 9,
                        )
                    ),
                ),
                "FF00C0800086098041FF010102030405060708",
                array(
                    "id" => 0xF9,
                    "location" => "asdf",
                    'driver' => 'ADuCInputTable',
                    "extra" => array(0 => 1, 1 => 972.44803691, 2 => 3),
                ),
            ),
            array( // #2 Test Voltages
                array(
                    "id" => 0xF9,
                    "extra" => array(1 => 2, 2 => 3),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                            "driver0" => 0x41,
                            "driver1" => 0x41,
                        )
                    ),
                ),
                "FF00C08000860980414100A18F0A00431F1500",
                array(
                    "id" => 0xF9,
                    "location" => "asdf",
                    'driver' => 'ADuCInputTable',
                    "extra" => array(1 => 9.99999461, 2 => 20.00000367),
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mocks  The value to preload into the mocks
    * @param string $string The setup string to test
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDecode
    */
    public function testDecode($mocks, $string, $expect)
    {
        $this->input->load($mocks);
        $this->o->decode($string);
        $ret = $this->input->table()->toArray(false);
        unset($ret["tableEntry"]);
        $this->assertEquals($expect, $ret);
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataEncode()
    {
        return array(
            array( // #0
                array(
                    "id" => 0xF9,
                    "extra" => array(),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                            "driver0" => 0x41,
                        )
                    ),
                ),
                "FF00C0800086098041FF000000000000000000",
            ),
            array( // #1
                array(
                    "id" => 0xF9,
                    "extra" => array(1 => 10, 2 => 20),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                            "driver0" => 0x41,
                            "driver1" => 0x41,
                        )
                    ),
                ),
                "FF00C08000860980414100A18F0A00431F1500",
            ),
            array( // #2
                array(
                    "id" => 0xF9,
                    "extra" => array(1 => -1, 2 => -2),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                            "driver0" => 0x41,
                            "driver1" => 0x41,
                        )
                    ),
                ),
                "FF00C08000860980414100A3F1FEFF46E3FDFF",
            ),
            array( // #3
                array(
                    "id" => 0xF9,
                    "extra" => array(),
                    "location" => "asdf",
                    "tableEntry" => json_encode(
                        array(
                            "driver0" => 0x4A,
                        )
                    ),
                ),
                "FF00C080008609804AFF00000000000000000000",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $mocks  The value to preload into the mocks
    * @param array $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataEncode
    */
    public function testEncode($mocks, $expect)
    {
        $this->input->load($mocks);
        $ret = $this->o->encode();
        $this->assertSame($expect, $ret);
    }
}
?>
