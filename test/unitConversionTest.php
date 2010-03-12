<?php
/**
 * Tests the unit conversion class
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
 * @category   UnitConversion
 * @package    HUGnetLibTest
 * @subpackage UnitConversion
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

require_once dirname(__FILE__).'/../unitConversion.php';
require_once dirname(__FILE__).'/../base/UnitBase.php';
require_once dirname(__FILE__).'/../lib/plugins.inc.php';
require_once dirname(__FILE__).'/unitConversionMocks.php';

/**
 * Test class for unitConversion.
 *
 * @category   UnitConversion
 * @package    HUGnetLibTest
 * @subpackage UnitConversion
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
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
                    '&#176;K' => 'shift:3',
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
            '&#176;K' => array(
                'longName' => '&#176;K',
                'varType' => 'float',
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
            array("Bogus", false),
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
    public static function dataRegisterUnits()
    {
        return array(
            array(
                array("Name" => "Bogus Name", "Class" => "Bogus Class"),
                array(),
            ),
            array(
                array("Name" => "Bogus Name", "Class" => "Test3Units"),
                array(),
            ),
            array(
                array("Name" => "Stuff", "Class" => "Test2Units"),
                array(
                    "Stuff" => array(
                        'D' => array(
                            'longName' => 'D',
                            'varType' => 'float',
                            'convert' => array(
                                'E' => 'aToB',
                                'F' => 'aToC',
                            ),
                            "class" => "Test2Units",
                        ),
                        'E' => array(
                            'longName' => 'E',
                            'varType' => 'float',
                            'convert' => array(
                                'D' => 'bToA',
                                'F' => 'bToC',
                            ),
                            'preferred' => 'D',
                            "class" => "Test2Units",
                        ),
                        'F' => array(
                            'longName' => 'C',
                            'varType' => 'float',
                            'convert' => array(
                                'D' => 'cToA',
                                'E' => 'cToB',
                            ),
                            "class" => "Test2Units",
                        ),
                    ),
                ),
            ),
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
     * @dataProvider dataRegisterUnits
     */
    public function testRegisterUnits($units, $expect)
    {
        // Start with a clean slate
        $this->o->units = array();
        $this->o->registerUnits($units);
        $this->assertSame($expect, $this->o->units);
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
            array("Direction", "&#176;C", "ignore", "ignore"),
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
            array(32, "&#176;C", "&#176;K", 0, "raw", null, (double)32000, "&#176;K"),
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
    public static function dataGetAllUnits()
    {
        return array(
            array(
                null,
                false,
                array(
                    "Temperature" => array(
                        "&#176;C" => "&#176;C",
                        "&#176;F" => "&#176;F",
                        "&#176;K" => "&#176;K",
                    ),
                    "Direction" => array(
                        "&#176;" => "&#176;",
                        "Direction" => "Direction",
                    ),
                )
            ),
            array(
                "Temperature",
                false,
                array(
                    "&#176;C" => "&#176;C",
                    "&#176;F" => "&#176;F",
                    "&#176;K" => "&#176;K",
                )
            ),
            array(
                null,
                true,
                array(
                    "&#176;C" => "Temperature:&#176;C",
                    "&#176;F" => "Temperature:&#176;F",
                    "&#176;K" => "Temperature:&#176;K",
                    "&#176;" => "Direction:&#176;",
                    "Direction" => "Direction:Direction",
                )
            ),
        );
    }
    /**
     * Tests getAllUnits()
     *
     * @param string $type   The data type to use if none is specified
     * @param bool   $flat   Flat format or not
     * @param array  $expect What expect returned
     *
     * @return null
     *
     * @dataProvider dataGetAllUnits
     */
    public function testGetAllUnits($type, $flat, $expect)
    {
        $this->assertSame($expect, $this->o->getAllUnits($type, $flat));
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
                    "&#176;C" => array("&#176;F", "&#176;K"),
                    "&#176;F" => array("&#176;C"),
                    "&#176;" => array("Direction"),
                    "Direction" => array("&#176;")
                )
            ),
            array(
                "diff",
                null,
                array(
                    "&#176;C" => array("&#176;F","&#176;K" ),
                    "&#176;F" => array("&#176;C")
                )
            ),
            array("raw", "&#176;C", array("&#176;F", "&#176;K", "&#176;C")),
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
            // Test #0
            array(
                array(
                    0 => array(
                        "Data0" => 1.0,
                        "Data1" => 2,
                        "Data2" => 3,
                        "Data3" => 4,
                        "Data4" => 6.5,
                        "Data5" => 3,
                        "data" => array(1.0,2,3,4,6.5,4),
                        "Date" => "2007-11-12 16:05:00",
                        "Units" => array(3 => "D"),
                    ),
                    1 => array(
                        "Data0" => 3.0,
                        "Data1" => 2,
                        "Data2" => 4,
                        "Data3" => 6,
                        "Data4" => 6.5,
                        "Data5" => 8,
                        "data" => array(2.0,2,4,6,6.5,8),
                        "Date" => "2007-11-12 16:10:00"
                    ),
                    2 => "This is not an array",
                ), // History
                array(
                    "ActiveSensors" => 6,
                    "dType" => array("raw","diff","diff","raw","diff", "raw"),
                    "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100, 0x100),
                    "params"=> array(
                        "sensorType"=>array(
                            "TestSensor2", "TestSensor1", "TestSensor2",
                            "TestSensor2", "TestSensor2", "TestSensor2",
                        )
                    ),
                    "Units" => array("E", "B", "E", "D", "E", "B"),
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
                        "Data5" => 4.0,
                        "data" => array(3.0,2,4.0,-2.0, 6.5, 4.0),
                        "Date" => "2007-11-12 16:10:00",
                        "deltaT" => 300
                    ),
                ), // expectHistory
                array(
                    "ActiveSensors" => 6,
                    "dType" => array("raw","ignore","diff","diff","diff", "raw"),
                    "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100, 0x100),
                    "params"=> array(
                        "sensorType"=>array(
                            "TestSensor2", "TestSensor1", "TestSensor2",
                            "TestSensor2", "TestSensor2", "TestSensor2",
                        )
                    ),
                    "Units" => array("E", "B", "E", "D", "E", "A"),
                    "modifyUnits" => 1,
                ), // expectDevInfo
                array("raw", "ignore", "diff", "diff", "diff", "raw"), // expectType
                array("E", "B", "E", "D", "E", "A"), // expectUnits
            ),
            // Test #1
            array(
                "Hello", // This is not an array
                array(
                    "TotalSensors" => 6,
                    "ActiveSensors" => 6,
                    "dType" => array("raw","diff","diff","raw","diff", "raw"),
                    "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100, 0x100),
                    "params"=> array(
                        "sensorType"=>array(
                            "TestSensor2", "TestSensor1", "TestSensor2",
                            "TestSensor2", "TestSensor2", "TestSensor2",
                        )
                    ),
                    "Units" => array("E", "B", "E", "D", "E", "B"),
                ), // DevInfo
                2, // dPlaces
                array("raw", "ignore", "diff", "diff", "raw"), // Type
                array("E", "B", "E", "D", "E"), // Units
                array(), // expectHistory
                array(
                    "TotalSensors" => 6,
                    "ActiveSensors" => 6,
                    "dType" => array("raw","ignore","diff","diff","raw", null),
                    "Types" => array(0x100, 0x100, 0x100, 0x100, 0x100, 0x100),
                    "params"=> array(
                        "sensorType"=>array(
                            "TestSensor2", "TestSensor1", "TestSensor2",
                            "TestSensor2", "TestSensor2", "TestSensor2",
                        )
                    ),
                    "Units" => array("E", "B", "E", "D", "E", "B"),
                    "modifyUnits" => 1,
                ), // expectDevInfo
                array("raw", "ignore", "diff", "diff", "raw"), // expectType
                array("E", "B", "E", "D", "E"), // expectUnits
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
        $this->o = new unitConversionMock();
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
        $this->o = new unitConversionMock();
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
        $this->o = new unitConversionMock();
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
        $this->o = new unitConversionMock();
        $ret = $this->o->modifyUnits($history, $devInfo, $dPlaces, $type, $units);
        $this->assertSame($expectUnits, $units);
    }


}

?>
