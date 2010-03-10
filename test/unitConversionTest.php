<?php
/**
 * Tests the unit conversion class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2009 Hunt Utilities Group, LLC
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
 * @category   UnitConversion
 * @package    HUGnetLibTest
 * @subpackage UnitConversion
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** The test case class */
require_once "PHPUnit/Framework/TestCase.php";
/** The test suite class */
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../unitConversion.php';
require_once dirname(__FILE__).'/../base/UnitBase.php';
require_once dirname(__FILE__).'/../lib/plugins.inc.php';

/**
 * Test class for unitConversion.
 *
 * @category   UnitConversion
 * @package    HUGnetLibTest
 * @subpackage UnitConversion
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class UnitConversionTest extends PHPUnit_Framework_TestCase
{
    /** Test unit array */
    var $testUnits = array(
        'Temperature' => array(
            '&#176;C' => array(
                'longName' => '&#176;C',
                'varType' => 'float',
                'convert' => array(
                    '&#176;F' => 'CtoF',
                ),
                'preferred' => '&#176;F',
                'class' => 'temperatureUnits',
            ),
            '&#176;F' => array(
                'longName' => '&#176;F',
                'varType' => 'float',
                'convert' => array(
                    '&#176;C' => 'FtoC',
                ),
                'class' => 'temperatureUnits',
            ),
        ),
        'Direction' => array(
            '&#176;' => array(
                'longName' => 'Compass Degrees',
                'varType' => 'float',
                'mode' => 'raw',
                'convert' => array(
                    'Direction' => 'numDirtoDir',
                ),
                'class' => 'directionUnits',
            ),
            'Direction' => array(
                'longName' => 'Direction',
                'varType' => 'text',
                'mode' => 'raw',
                'convert' => array(
                    '&#176;' => 'DirtonumDir',
                ),
                'class' => 'directionUnits',
            ),
        ),
    );

    /**
     * Runs the test methods of this class.
     *
     * @return null
     *
     * @access public
     * @static
     */
    public static function main()
    {
        include_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("unitConversionTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

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
        $this->o = new unitConversion();
        $this->o->units = $this->testUnits;
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
     * Returns true if it finds the units.
     *
     * @param string $cat   The category to check in
     * @param string $units The unit to check
     *
     * @return bool
     */
    public function findUnits($cat, $units)
    {
        $o = new unitConversion;
        if (is_null($cat)) {
            if (is_array($o->units)) {
                foreach ($o->units as $catName => $cat) {
                    return isset($cat[$units]);
                }
            }
        } else {
            return is_array($o->units[$cat][$units]);
        }
        return false;
    }
    /**
     * Finds a unit and returns whether the mode is valid for the units.
     *
     * @param string $cat  The category to check in
     * @param string $unit The unit to check
     * @param string $mode The mode to check
     *
     * @return bool
     */
    public function findUnitMode($cat, $unit, $mode)
    {
        $o = new unitConversion;
        if (is_null($cat)) {
            if (is_array($o->units)) {
                foreach ($array as $catName => $cat) {
                    if (isset($cat[$unit])) {
                        return unitConversionTest::_checkUnitModeRaw(
                            $cat[$unit]['mode'],
                            $mode
                        );
                    }
                }
            }
        } else {
            return unitConversionTest::_checkUnitModeRaw(
                $o->units[$cat][$unit]['mode'],
                $mode
            );
        }
        return false;
    }
    /**
     * Checks to make sure a mode is valid
     *
     * @param string $modes The list of modes to check against
     * @param string $mode  The mode to check
     *
     * @return bool
     */
    private function _checkUnitModeRaw($modes, $mode)
    {
        if (is_null($modes)) {
            return true;
        } else {
            if (stristr($modes, $mode) === false) {
                return false;
            } else {
                return true;
            }
        }

    }


    /**
     * Data provider for testPreferredUnit
     *
     * @return array
     */
    public static function dataPreferredUnit()
    {
        return array(
            array("&#176;F", "&#176;F"),
            array("&#176;C", "&#176;F"),
        );
    }
    /**
     * Test preferredUnit()
     *
     * @param string $unit   The unit to find
     * @param array  $expect What expect returned
     *
     * @return null
     *
     * @dataProvider dataPreferredUnit
     */
    public function testPreferredUnit($unit, $expect)
    {
        $this->assertSame($expect, $this->o->preferredUnit($unit));
    }

    /**
     * Data provider for testGraphable
     *
     * @return array
     */
    public static function dataGraphable()
    {
        return array(
            array("&#176;C", true),
            array("Direction", false),
        );
    }
    /**
     * Test Graphable()
     *
     * @param string $unit   The unit to find
     * @param array  $expect What expect returned
     *
     * @return null
     *
     * @dataProvider dataGraphable
     */
    public function testGraphable($unit, $expect)
    {
        $this->assertSame($expect, $this->o->graphable($unit));
    }
    /**
     * Data provider for testFindUnit
     *
     * @return array
     */
    public static function dataFindUnit()
    {
        return array(
            array(
                '&#176;F',
                array(
                    'longName' => '&#176;F',
                    'varType' => 'float',
                    'convert' => array('&#176;C' => 'FtoC'),
                    'class' => 'temperatureUnits'
                )
            ),
            array("ASDF", false),
        );
    }
    /**
     * Test findUnit()
     *
     * @param string $unit   The unit to find
     * @param array  $expect What expect returned
     *
     * @return null
     *
     * @dataProvider dataFindUnit
     */
    public function testfindUnit($unit, $expect)
    {
        $this->assertSame($expect, $this->o->findUnit($unit));
    }

    /**
     * Data provider for testFindUnit
     *
     * @return array
     */
    public static function dataDataType()
    {
        return array(
            array("&#176;C", "", "asdf", "asdf"),
            array("Direction", "", "asdf", "raw"),
        );
    }
    /**
     * Tests getDataType()
     *
     * @param string $from    The starting unit
     * @param string $to      The unit to be converted into
     * @param string $default The data type to use if none is specified
     * @param array  $expect  What expect returned
     *
     * @return null
     *
     * @dataProvider dataDataType
     */
    public function testgetDataType($from, $to, $default, $expect)
    {
        $this->assertSame($expect, $this->o->getDataType($from, $to, $default));
    }

    /**
     * Data provider for testFindUnit
     *
     * @return array
     */
    public static function dataConvert()
    {
        return array(
            array(32, "&#176;F", "&#176;C", 0, "raw", null, 0.0, "&#176;C"),
            array(32, "&#176;F", "&#176;F", 0, "diff", null, 32, "&#176;F"),
            array(32, "&#176;F", "Direction", 0, "diff", null, 32, "&#176;F"),
        );
    }
    /**
     * Tests Convert()
     *
     * @param mixed  $val      The value to convert
     * @param string $from     The starting unit
     * @param string $to       The unit to be converted into
     * @param int    $time     The time in seconds between this record and the last.
     * @param string $type     The data type to use
     * @param mixed  $extra    Any extra stuff we might need.
     * @param array  $expect   What expect returned
     * @param array  $toExpect What expect returned
     *
     * @return null
     *
     * @dataProvider dataConvert
     */
    public function testConvert(
        $val,
        $from,
        $to,
        $time,
        $type,
        $extra,
        $expect,
        $toExpect
    ) {
        $ret = $this->o->convert($val, $from, $to, $time, $type, $extra);
        $this->assertSame($expect, $ret);
    }
    /**
     * Tests Convert()
     *
     * @param mixed  $val      The value to convert
     * @param string $from     The starting unit
     * @param string $to       The unit to be converted into
     * @param int    $time     The time in seconds between this record and the last.
     * @param string $type     The data type to use
     * @param mixed  $extra    Any extra stuff we might need.
     * @param array  $expect   What expect returned
     * @param array  $toExpect What expect returned
     *
     * @return null
     *
     * @dataProvider dataConvert
     */
    public function testConvertTo(
        $val,
        $from,
        $to,
        $time,
        $type,
        $extra,
        $expect,
        $toExpect
    ) {
        $ret = $this->o->convert($val, $from, $to, $time, $type, $extra);
        $this->assertSame($toExpect, $to);
    }

    /**
     * Data provider for testFindUnit
     *
     * @return array
     */
    public static function dataGetPossConv()
    {
        return array(
            array(
                "raw",
                null,
                array(
                    "&#176;C" => array("&#176;F"),
                    "&#176;F" => array("&#176;C"),
                    "&#176;" => array("Direction"),
                    "Direction" => array("&#176;")
                )
            ),
            array(
                "diff",
                null,
                array("&#176;C" => array("&#176;F"), "&#176;F" => array("&#176;C"))
            ),
            array("raw", "&#176;C", array("&#176;F", "&#176;C")),
            array("diff", "&#176;F", array("&#176;C", "&#176;F")),
            array("raw", "Direction", array("&#176;", "Direction")),
        );
    }
    /**
     * Tests getPossConv()
     *
     * @param string $type   The data type to use if none is specified
     * @param string $from   The starting unit
     * @param array  $expect What expect returned
     *
     * @return null
     *
     * @dataProvider dataGetPossConv
     */
    public function testGetPossConv($type, $from, $expect)
    {
        $this->assertSame($expect, $this->o->getPossConv($type, $from));
    }

    /**
     * Setup modifyUnits
     *
     * @return object
     */
    public function &modifyUnitsSetup()
    {
        return $this->o;
    }

    /**
     * Data provider for testFindUnit
     *
     * @return array
     */
    public static function dataModifyUnits()
    {
        return array(
            array(
                array(
                    0 => array(
                        "Data0" => 1.0,
                        "Data1" => 2,
                        "Data2" => 3,
                        "Data3" => 4,
                        "Data4" => 6.5,
                        "data" => array(1.0,2,3,4,6.5),
                        "Date" => "2007-11-12 16:05:00"
                    ),
                    1 => array(
                        "Data0" => 3.0,
                        "Data1" => 2,
                        "Data2" => 4,
                        "Data3" => 6,
                        "Data4" => 6.5,
                        "data" => array(2.0,2,4,6,6.5),
                        "Date" => "2007-11-12 16:10:00"
                    ),
               ), // History
                array(
                    "ActiveSensors" => 5,
                    "dType" => array("raw","diff","diff","raw","diff"),
                    "Types" => array(0x100, 0x100, 0x100, 0x100,0x100),
                    "params"=> array(
                        "sensorType"=>array(
                            "TestSensor2", "TestSensor1", "TestSensor2",
                            "TestSensor2", "TestSensor2"
                        )
                    ),
                    "Units" => array("E", "B", "E", "D", "E"),
               ), // DevInfo
                2, // dPlaces
                array("raw", "ignore", "diff", "diff", "raw"), // Type
                array("E", "B", "E", "D", "E"), // Units
                array(
                    1 => array(
                        "Data0" => 3.0,
                        "Data1" => 2,
                        "Data2" => 4.0,
                        "Data3" => -2.0,
                        "Data4" => 6.5,
                        "data" => array(3.0,2,4.0,-2.0, 6.5),
                        "Date" => "2007-11-12 16:10:00",
                        "deltaT" => 300
                    ),
               ), // expectHistory
                array(
                    "ActiveSensors" => 5,
                    "dType" => array("raw","ignore","diff","diff","diff"),
                    "Types" => array(0x100, 0x100, 0x100, 0x100,0x100),
                    "params"=> array(
                        "sensorType"=>array(
                            "TestSensor2", "TestSensor1", "TestSensor2",
                            "TestSensor2", "TestSensor2"
                        )
                    ),
                    "Units" => array("E", "B", "E", "D", "E"),
                    "modifyUnits" => 1,
               ), // expectDevInfo
                array("raw", "ignore", "diff", "diff", "diff"), // expectType
                array("E", "B", "E", "D","E"), // expectUnits
           ),
        );
    }
    /**
     * Test the history from modifyUnits
     *
     * @param array $history       The history to modify.
     * @param array $devInfo       The devInfo array to modify.
     * @param int   $dPlaces       The maximum number of decimal places to show.
     * @param array $type          The types to change to
     * @param array $units         The units to change to
     * @param array $expectHistory The history we expect after mofication
     * @param array $expectDevInfo The devInfo array we expect after mofication
     * @param array $expectType    The types we expect after mofication
     * @param array $expectUnits   The units we expect after mofication
     *
     * @return null
     *
     * @dataProvider dataModifyUnits().
     */
    public function testModifyUnitsHistory(
        $history,
        $devInfo,
        $dPlaces,
        $type,
        $units,
        $expectHistory,
        $expectDevInfo,
        $expectType,
        $expectUnits
    ) {
        $ret = $this->o->modifyUnits($history, $devInfo, $dPlaces, $type, $units);
        $this->assertSame($expectHistory, $history);
    }
    /**
     * Test the history from modifyUnits
     *
     * @param array $history       The history to modify.
     * @param array $devInfo       The devInfo array to modify.
     * @param int   $dPlaces       The maximum number of decimal places to show.
     * @param array $type          The types to change to
     * @param array $units         The units to change to
     * @param array $expectHistory The history we expect after mofication
     * @param array $expectDevInfo The devInfo array we expect after mofication
     * @param array $expectType    The types we expect after mofication
     * @param array $expectUnits   The units we expect after mofication
     *
     * @return null
     *
     * @dataProvider dataModifyUnits().
     */
    public function testModifyUnitsDevInfo(
        $history,
        $devInfo,
        $dPlaces,
        $type,
        $units,
        $expectHistory,
        $expectDevInfo,
        $expectType,
        $expectUnits
    ) {
        $ret = $this->o->modifyUnits($history, $devInfo, $dPlaces, $type, $units);
        $this->assertSame($expectDevInfo, $devInfo);
    }
    /**
     * Test the history from modifyUnits
     *
     * @param array $history       The history to modify.
     * @param array $devInfo       The devInfo array to modify.
     * @param int   $dPlaces       The maximum number of decimal places to show.
     * @param array $type          The types to change to
     * @param array $units         The units to change to
     * @param array $expectHistory The history we expect after mofication
     * @param array $expectDevInfo The devInfo array we expect after mofication
     * @param array $expectType    The types we expect after mofication
     * @param array $expectUnits   The units we expect after mofication
     *
     * @return null
     *
     * @dataProvider dataModifyUnits().
     */
    public function testModifyUnitsType(
        $history,
        $devInfo,
        $dPlaces,
        $type,
        $units,
        $expectHistory,
        $expectDevInfo,
        $expectType,
        $expectUnits
    ) {
        $ret = $this->o->modifyUnits($history, $devInfo, $dPlaces, $type, $units);
        $this->assertSame($expectType, $type);
    }
    /**
     * Test the history from modifyUnits
     *
     * @param array $history       The history to modify.
     * @param array $devInfo       The devInfo array to modify.
     * @param int   $dPlaces       The maximum number of decimal places to show.
     * @param array $type          The types to change to
     * @param array $units         The units to change to
     * @param array $expectHistory The history we expect after mofication
     * @param array $expectDevInfo The devInfo array we expect after mofication
     * @param array $expectType    The types we expect after mofication
     * @param array $expectUnits   The units we expect after mofication
     *
     * @return null
     *
     * @dataProvider dataModifyUnits().
     */
    public function testModifyUnitsUnits(
        $history,
        $devInfo,
        $dPlaces,
        $type,
        $units,
        $expectHistory,
        $expectDevInfo,
        $expectType,
        $expectUnits
    ) {
        $ret = $this->o->modifyUnits($history, $devInfo, $dPlaces, $type, $units);
        $this->assertSame($expectUnits, $units);
    }


}


/**
 *  This is a mock class to test the rest of the system.
 *
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class UnitConversionMock extends unitConversion
{
    /** The units array */
    var $units = array();

    /**
     * This registers the sensor Plugins so we know what code we have available.
     *
     * @param object &$plugins This is a object of type plugin
     *
     * @see plugin
      */
    function __construct(&$plugins = "")
    {
        $this->registerUnits(array("Class" => "test1Units", "Name" => "Test"));
        $this->registerUnits(array("Class" => "test2Units", "Name" => "Test2"));
    }


}
/**
 *  This is a mock class to test the rest of the system.
 *
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Test1Units extends UnitBase
{
    var $units = array(
        'A' => array(
            'longName' => 'A',
            'varType' => 'float',
            'convert' => array(
                'B' => 'aToB',
                'C' => 'aToC',
           ),
       ),
        'B' => array(
            'longName' => 'B',
            'varType' => 'float',
            'convert' => array(
                'A' => 'bToA',
                'C' => 'bToC',
           ),
            'preferred' => 'A',
       ),
        'C' => array(
            'longName' => 'C',
            'varType' => 'float',
            'convert' => array(
                'A' => 'cToA',
                'B' => 'cToB',
           ),
            'preferred' => 'A',
       ),
    );
    /**
     * Converts units A to B
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function aToB($W, $time, $type)
    {
        return 2*$W;
    }

    /**
     * Converts units B to A
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function bToA($W, $time, $type)
    {
        return $W/2;
    }
    /**
     * Converts units A to C
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function aToC($W, $time, $type)
    {
        return 4*$W;
    }

    /**
     * Converts units C to A
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function cToA($W, $time, $type)
    {
        return $W/4;
    }
    /**
     * Converts units B to C
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function bToC($W, $time, $type)
    {
        return 10*$W;
    }

    /**
     * Converts units C to B
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function cToB($W, $time, $type)
    {
        return $W/10;
    }

}

/**
 *  This is a mock class to test the rest of the system.
 *
 * @category   Test
 * @package    HUGnetLib
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Test2Units extends UnitBase
{
    var $units = array(
        'D' => array(
            'longName' => 'D',
            'varType' => 'float',
            'convert' => array(
                'E' => 'aToB',
                'F' => 'aToC',
           ),
       ),
        'E' => array(
            'longName' => 'E',
            'varType' => 'float',
            'convert' => array(
                'D' => 'bToA',
                'F' => 'bToC',
           ),
            'preferred' => 'D',
       ),
        'F' => array(
            'longName' => 'C',
            'varType' => 'float',
            'convert' => array(
                'D' => 'cToA',
                'E' => 'cToB',
           ),
       ),
    );
    /**
     * Converts units A to B
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function aToB($W, $time, $type)
    {
        return 2*$W;
    }

    /**
     * Converts units B to A
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function bToA($W, $time, $type)
    {
        return $W/2;
    }
    /**
     * Converts units A to C
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function aToC($W, $time, $type)
    {
        return 4*$W;
    }

    /**
     * Converts units C to A
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function cToA($W, $time, $type)
    {
        return $W/4;
    }
    /**
     * Converts units B to C
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function bToC($W, $time, $type)
    {
        return 10*$W;
    }

    /**
     * Converts units C to B
     *
     * @param float  $W    The input
     * @param int    $time The delta time
     * @param string $type The mode
     *
     * @return float
     */
    public function cToB($W, $time, $type)
    {
        return $W/10;
    }


}
?>
