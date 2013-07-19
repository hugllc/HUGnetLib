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
namespace HUGnet\base;
/** This is a required class */
require_once CODE_BASE.'base/LoadableDriver.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is our units class */
require_once CODE_BASE."devices/datachan/Driver.php";

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class LoadableDriverTest extends \PHPUnit_Framework_TestCase
{
    /** This is the class we are testing */
    protected $class = "DriverTestClass";
    /** This is the object we are testing */
    protected $o = null;
    /** This is our system object */
    protected $system;
    /** This is our output object */
    protected $output;
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
        $this->system = $this->getMock("\HUGnet\System", array("now"));
        $this->system->expects($this->any())
            ->method('now')
            ->will($this->returnValue(123456));
        $this->output = $this->system->device()->output(0);
        $this->o = DriverTestClass::factory($this->output);
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
        unset($this->system);
        unset($this->output);
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
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGet()
    {
        return array(
            array(
                array(),
                "ThisIsABadName",
                null,
            ),
            array(
                array(),
                "storageType",
                \HUGnet\devices\datachan\Driver::TYPE_RAW,
            ),
            array(
                array(),
                "testParam",
                "12345",
            ),
            array(
                array(),
                "unitType",
                'asdf',
            ),
            array(
                array(
                ),
                "maxDecimals",
                7,
            ),
            array(
                array(
                    "extra" => array(0, 0, 0, 3),
            ),
                "maxDecimals",
                3,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array  $mock   The mocks to use
    * @param string $name   The name of the variable to test.
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet($mock, $name, $expect)
    {
        $this->output->load($mock);
        $this->assertSame($expect, $this->o->get($name, 1));
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
            'maxDecimals' => 7,
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
                null,
                1,
                array(),
                array(),
                null,
            ),
            array(
                array(),
                256210,
                1,
                array(),
                array(),
                256210,
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
            array(
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Sensor"),
                    )
                ),
                "010203040506",
                array(
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
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mocks);
        $this->o->decode($string);
        $ret = $sensor->retrieve();
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
                    "Device" => array(
                        "get" => array(
                            "id" => 7,
                        ),
                    ),
                ),
                "",
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
        $ret = $this->o->encode();
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider for testDecodeInt
    *
    * @return array
    */
    public static function dataDecodeInt()
    {
        return array(
            array( // #0
                "563412",
                3,
                false,
                0x123456,
            ),
            array( // #1
                "78563412",
                4,
                true,
                0x12345678,
            ),
            array( // #2
                "FFFFFFFF",
                4,
                true,
                -1,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $string The string to use
    * @param int    $bytes  The number of bytes to set
    * @param bool   $signed If the number is signed or not
    * @param int    $expect The expected int
    *
    * @return null
    *
    * @dataProvider dataDecodeInt
    */
    public function testDecodeInt($string, $bytes, $signed, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock(array());
        $ret = $this->o->decodeInt($string, $bytes, $signed);
        $this->assertSame($expect, $ret, "Return is wrong");
    }
    /**
    * data provider for testEncodeInt
    *
    * @return array
    */
    public static function dataEncodeInt()
    {
        return array(
            array( // #0
                0x123456,
                3,
                "563412",
            ),
            array( // #1
                0x12345678,
                4,
                "78563412",
            ),
            array( // #2
                -1,
                4,
                "FFFFFFFF",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param int $int    The string to use
    * @param int $bytes  The number of bytes to set
    * @param int $expect The expected int
    *
    * @return null
    *
    * @dataProvider dataEncodeInt
    */
    public function testEncodeInt($int, $bytes, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock(array());
        $ret = $this->o->encodeInt($int, $bytes);
        $this->assertSame($expect, $ret, "Return is wrong");
    }
    /**
    * data provider for testDecodeFloat
    *
    * @return array
    */
    public static function dataDecodeFloat()
    {
        return array(
            array( // #0 Comes from Wikipedia: Single_precision_floating-point_format
                "00004641",
                4,
                12.375,
            ),
            array( // #1 Bad number of bytes
                "00004641",
                5,
                null,
            ),
            array( // #0 Comes from Wikipedia: Single_precision_floating-point_format
                "00000000",
                4,
                0.0,
            ),
            array( // #0 Comes from Wikipedia: Single_precision_floating-point_format
                0x41460000,
                4,
                12.375,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $string The string to use
    * @param int    $bytes  The number of bytes to use
    * @param string $expect The expected float
    *
    * @return null
    *
    * @dataProvider dataDecodeFloat
    */
    public function testDecodeFloat($string, $bytes, $expect)
    {
        $ret = $this->o->decodeFloat($string, $bytes);
        $this->assertEquals($expect, $ret, "Return is wrong", 0.0001);
    }
    /**
    * data provider for testEncodeFloat
    *
    * @return array
    */
    public static function dataEncodeFloat()
    {
        return array(
            array( // #0 Comes from Wikipedia: Single_precision_floating-point_format
                12.375,
                4,
                "00004641",
            ),
            array( // #1 bad number of bytes
                12.375,
                5,
                "0000000000",
            ),
            array( // #2 small int
                0x123456,
                4,
                "B0A29149",
            ),
            array( // #3 big int
                0x1234567812345678,
                4,
                "B4A2915D",
            ),
            array( // #4 small negative int
                -0x123456,
                4,
                "B0A291C9",
            ),
            array( // #5 big negative int
                -0x1234567812345678,
                4,
                "B4A291DD",
            ),
            array( // #6 big negative int
                (1<<23) + 1,
                4,
                "0100004B",
            ),
            array( // #7 zero
                0,
                4,
                "00000000",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $string The string to use
    * @param int    $bytes  The number of bytes to use
    * @param string $expect The expected float
    *
    * @return null
    *
    * @dataProvider dataEncodeFloat
    */
    public function testEncodeFloat($string, $bytes, $expect)
    {
        $ret = $this->o->encodeFloat($string, $bytes);
        $this->assertEquals($expect, $ret, "Return is wrong", 0.0001);
    }
    /**
    * data provider for testDecodeInt
    *
    * @return array
    */
    public static function dataDecodePriority()
    {
        return array(
            array( // #0
                "80",
                1.0,
            ),
            array( // #1
                "01",
                128.0,
            ),
            array( // #2
                "0D",
                9.85,
            ),
            array( // #3
                "00",
                129,
            ),
            array( // #4
                "FF",
                0.5,
            ),
            array( // #5
                "FB",
                0.51,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $string The string to use
    * @param int    $expect The expected int
    *
    * @return null
    *
    * @dataProvider dataDecodePriority
    */
    public function testDecodePriority($string, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock(array());
        $ret = $this->o->decodePriority($string);
        $this->assertSame($expect, $ret, "Return is wrong");
    }
    /**
    * data provider for testEncodeInt
    *
    * @return array
    */
    public static function dataEncodePriority()
    {
        return array(
            array( // #0
                1,
                "80",
            ),
            array( // #1
                128,
                "01",
            ),
            array( // #2
                129,
                "00",
            ),
            array( // #3
                10,
                "0D",
            ),
            array( // #4
                0.51,
                "FB",
            ),
            array( // #5
                0.50,
                "FF",
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param int $int    The string to use
    * @param int $expect The expected int
    *
    * @return null
    *
    * @dataProvider dataEncodePriority
    */
    public function testEncodePriority($int, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock(array());
        $ret = $this->o->encodePriority($int);
        $this->assertSame($expect, $ret, "Return is wrong");
    }
    /**
    * data provider for testEncodeInt
    *
    * @return array
    */
    public static function dataEntry()
    {
        return array(
            array( // #0 No setup given
                array(
                    "dev" => 1,
                    "output" => 2,
                ),
                "ADuCDAC",
                array(
                    'DACBUFLP' => 0,
                    'OPAMP' => 0,
                    'DACBUFBYPASS' => 0,
                    'DACCLK' => 0,
                    'DACMODE' => 1,
                    'Rate' => 0,
                    'Range' => 3,
                ),
            ),
            array( // #1  Bad Class name
                array(
                    "dev" => 1,
                    "output" => 2,
                ),
                "ThisIsABadName",
                null,
            ),
            array( // #2 Class is not a string
                array(
                    "dev" => 1,
                    "output" => 2,
                ),
                null, // This should be a string.  ;)
                null,
            ),
            array( // #3 This one is good
                array(
                    "dev" => 1,
                    "output" => 2,
                    "tableEntry" => json_encode(
                        array(
                            'DACBUFLP' => 1,
                            'OPAMP' => 1,
                            'DACBUFBYPASS' => 1,
                            'DACCLK' => 1,
                            'DACMODE' => 0,
                            'Rate' => 1,
                            'Range' => 1,
                        )
                    )
                ),
                "ADuCDAC",
                array(
                    'DACBUFLP' => 1,
                    'OPAMP' => 1,
                    'DACBUFBYPASS' => 1,
                    'DACCLK' => 1,
                    'DACMODE' => 0,
                    'Rate' => 1,
                    'Range' => 1,
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The data to preload into the output
    * @param array $class   The table entry class to use
    * @param misc  $expect  The expected int
    *
    * @return null
    *
    * @dataProvider dataEntry
    */
    public function testEntry($preload, $class, $expect)
    {
        $this->output->load($preload);
        $this->o->entryClass = $class;
        $ret = $this->o->entry();
        $output = $this->output->toArray(false);
        if (is_null($expect)) {
            $this->assertNull($ret);
            $this->assertNull($output["tableEntry"]);
        } else {
            $this->assertEquals($expect, $ret->toArray(false));
            $this->assertInternalType("string", $output["tableEntry"]);
        }
    }
}
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
class DriverTestClass extends LoadableDriver
{
    /** This is our extra data */
    protected $extra = array();
    /**
    * This is the class to use for our entry object.
    */
    public $entryClass = "";
    /**
    * The location of our tables.
    */
    public $tableLoc = "outputTable";
    /**
    * This is where all of the defaults are stored.
    */
    protected $default = array(
        "longName" => "",
        "shortName" => "",
        "unitType" => "",
        "bound" => false,                // This says if this sensor is changeable
        "virtual" => false,              // This says if we are a virtual sensor
        "total"   => false,              // Whether to total instead of average
        "extraText" => array(),
        "extraDefault" => array(),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(),
        "storageUnit" => "unknown",
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "maxDecimals" => 2,
        "dataTypes" => array(
            \HUGnet\devices\datachan\Driver::TYPE_RAW
                => \HUGnet\devices\datachan\Driver::TYPE_RAW,
            \HUGnet\devices\datachan\Driver::TYPE_DIFF
                => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
            \HUGnet\devices\datachan\Driver::TYPE_IGNORE
                => \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
        ),
        "inputSize" => 3,
    );
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Unknown Sensor",
        "shortName" => "Unknown",
        "unitType" => "asdf", /* This is for test value only */
        "testParam" => "12345", /* This is for test value only */
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraDefault" => array(2,3,5,7,11),
        "extraText" => array("a","b","c","d","e"),
        "extraValues" => array(5, 5, 5, 5, 5),
        "maxDecimals" => "getExtra3",
    );
    /**
    * This function creates the system.
    *
    * @param object &$sensor The sensor object
    *
    * @return null
    */
    public static function &factory(&$sensor)
    {
        $obj = new DriverTestClass($sensor);
        return $obj;
    }
    /**
    * This builds the string for the levelholder.
    *
    * @param int $val   The value to use
    * @param int $bytes The number of bytes to set
    *
    * @return string The string
    */
    public function encodeInt($val, $bytes = 4)
    {
        return parent::encodeInt($val, $bytes);
    }
    /**
    * This builds the string for the levelholder.
    *
    * @param string $val    The value to use
    * @param int    $bytes  The number of bytes to set
    * @param bool   $signed If the number is signed or not
    *
    * @return string The string
    */
    public function decodeInt($val, $bytes = 4, $signed = false)
    {
        return parent::decodeInt($val, $bytes, $signed);
    }
    /**
    * This builds the string for the levelholder.
    *
    * @param string $val   The value to use
    * @param int    $bytes The number of bytes to use
    *
    * @return string The string
    */
    public function encodeFloat($val, $bytes = 4)
    {
        return parent::encodeFloat($val, $bytes);

    }
    /**
    * This builds the string for the levelholder.
    *
    * @param string $val   The value to use
    * @param int    $bytes The number of bytes to use
    *
    * @return string The string
    */
    public function decodeFloat($val, $bytes = 4)
    {
        return parent::decodeFloat($val, $bytes);
    }
    /**
    * This takes the runs/second and turns it into a priority
    *
    * @param int $value The value to encode
    *
    * @return string The priority, encoded for the device
    */
    public function encodePriority($value)
    {
        return parent::encodePriority($value);
    }
    /**
    * This decodes the priority from the endoint to runs/second
    *
    * @param string $string The setup string to decode
    *
    * @return Reference to the network object
    */
    public function decodePriority($string)
    {
        return parent::decodePriority($string);
    }
    /**
    * This is the destructor
    *
    * @return object
    */
    public function output()
    {
        return $this->iopobject();
    }
}
?>
