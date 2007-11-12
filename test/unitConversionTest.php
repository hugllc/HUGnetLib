<?php
/**
 *   Tests the unit conversion class
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *   
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *   
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *   
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Test
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$    
 *
 */
// Call unitConversionTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "unitConversionTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../unitConversion.php';

/**
 * Test class for unitConversion.
 */
class unitConversionTest extends PHPUnit_Framework_TestCase {
    var $testUnits = array(
        'Temperature' => array(
            '&#176;C' => array(
                'longName' => '&#176;C',
                'varType' => 'float',
                'convert' => array(
                    '&#176;F' => 'CtoF',
                ),
                'preferred' => '&#176;F',
            ),
            '&#176;F' => array(
                'longName' => '&#176;F',
                'varType' => 'float',
                'convert' => array(
                    '&#176;C' => 'FtoC',
                ),
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
            ),
            'Direction' => array(
                'longName' => 'Direction',
                'varType' => 'text',
                'mode' => 'raw',
                'convert' => array(
                    '&#176;' => 'DirtonumDir',
                ),
            ),  
        ),
    );

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("unitConversionTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    }

    /**
     * data provider for testUnitArrayLongName, testUnitArrayVarType,
     */
    public static function dataUnitArray() {
        $o = new unitConversion;
        $return = array();
        foreach($o->units as $catName => $cat) {
            foreach($cat as $shortName => $unit) {
                $return[] = array($catName, $shortName, $unit);
            }
        }
        return $return;
    }
    
    /**
     * @dataProvider dataUnitArray
     */
    public function testUnitArrayLongName($catName, $shortName, $unit) {
        $o = new unitConversion;
        // Long Name
        $this->assertType("string", $unit['longName'], $catName.":".$shortName.": Long name is not a string");
        $this->assertThat(strlen($unit['longName']), $this->greaterThan(0), $catName.":".$shortName.": Long name is not a set");            
    }
    
    /**
     * @dataProvider dataUnitArray
     */
    public function testUnitArrayVarType($catName, $shortName, $unit) {
        $o = new unitConversion;
        // Var Type
        $this->assertType("string", $unit['varType'], $catName.":".$shortName.": Variable type is not a string");
        $this->assertTrue($this->checkvarType($unit['varType']), $catName.":".$shortName.": Variable type '".$unit['varType']."'is not valid");
    }

    /**
     * @dataProvider dataUnitArray
     */
    public function testUnitArrayConvert($catName, $shortName, $unit) {
        if (isset($unit["convert"])) {
            $this->assertType("array", $unit["convert"], $catName.":".$shortName.": Mode is not a string");
        }
    }
    
    /**
     * @dataProvider dataUnitArray
     */
    public function testUnitArrayMode($catName, $shortName, $unit) {
        if (isset($unit["mode"])) {
            $this->assertType("string", $unit["mode"], $catName.":".$shortName.": Mode is not a string");
            $this->assertTrue($this->checkMode($unit["mode"]), $catName.":".$shortName.": Mode '".$unit['varType']."'is not valid");
        }
    }
    /**
     * @dataProvider dataUnitArray
     */
    public function testUnitArrayPreferred($catName, $shortName, $unit) {
        if (isset($unit["preferred"])) {
            $this->assertType("string", $unit["preferred"], $catName.":".$shortName.": Mode is not a string");
            $this->assertTrue($this->findUnits($catName, $unit["preferred"]), $catName.":".$shortName.": Unit ".$to." doesn't exist");
        }
    }
    /**
     * @dataProvider dataUnitArray
     */
    public function testUnitArrayValid($catName, $shortName, $unit) {
        $valid = array("mode", "convert", "longName", "varType", "preferred");
        foreach($valid as $key) {
            unset($unit[$key]);
        }
        $this->assertSame(array(), $unit);
    }
    
    /**
     * data provider for testUnitArrayConvertFunct
     */
    public static function dataUnitArrayConvertFunct() {
        $o = new unitConversion;
        $return = array();
        foreach($o->units as $catName => $cat) {
            foreach($cat as $shortName => $unit) {
                if (is_array($unit['convert'])) {
                    foreach($unit['convert'] as $to => $function) {
                        $return[] = array($catName, $shortName, $to, $function);
                    }
                }
            }
        }
        return $return;
    }
    /**
     * @dataProvider dataUnitArrayConvertFunct
     */
    public function testUnitArrayConvertFunct($catName, $shortName, $to, $function) {
        $o = new unitConversion;
        $this->assertTrue(method_exists($o, $function), $catName.":".$shortName.": conversion function ".$function." doesn't exist");
        $this->assertTrue($this->findUnits($catName, $to), $catName.":".$shortName.": Unit ".$to." doesn't exist");
    }
    /**
     */
    public function findUnits($cat, $units) {
        $o = new unitConversion;
        if (is_null($cat)) {
            if (is_array($o->units)) {
                foreach($array as $catName => $cat) {
                    return isset($cat[$units]);
                }
            }
        } else {
            return is_array($o->units[$cat][$units]);
        }
        return FALSE;
    }
    /**
     */
    public function findUnitMode($cat, $unit, $mode) {
        $o = new unitConversion;
        if (is_null($cat)) {
            if (is_array($o->units)) {
                foreach($array as $catName => $cat) {
                    if (isset($cat[$unit])) {
                        return unitConversionTest::checkUnitModeRaw($cat[$unit]['mode'], $mode);
                    }
                }
            }
        } else {
            return unitConversionTest::checkUnitModeRaw($o->units[$cat][$unit]['mode'], $mode);
        }
        return FALSE;
    }
    /**
     */
    private function checkUnitModeRaw($modes, $mode) {
        if (is_null($modes)) {                        
            return TRUE;
        } else {
            if (stristr($modes, $mode) === FALSE) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
    
    }

    /**
     */
    private function checkvarType($vartype) {
        if ($vartype == 'float') return TRUE;
        if ($vartype == 'int') return TRUE;
        if ($vartype == 'text') return TRUE;
        return FALSE;
    }
    /**
    */
    private function checkMode($mode) {
        if ($mode == 'raw') return TRUE;
        if ($mode == 'diff') return TRUE;
        return FALSE;
    }
    /************************************************************
     *   Here we start checking functions
     ************************************************************/
    /**
     *
     */
    public static function dataPreferredUnit() {
        return array(
            array("&#176;F", "&#176;F"),
            array("&#176;C", "&#176;F"),
        );
    }
    /**
     * @dataProvider dataPreferredUnit
     */
    public function testPreferredUnit($unit, $expect) {
        $o = new unitConversion;
        $o->units = $this->testUnits;
        $this->assertSame($expect, $o->preferredUnit($unit));
    }    
    
    /**
     *
     */
    public static function dataGraphable() {
        return array(
            array("&#176;C", TRUE),
            array("Direction", FALSE),
        );
    }
    /**
     * @dataProvider dataGraphable
     */
    public function testGraphable($unit, $expect) {
        $o = new unitConversion;
        $o->units = $this->testUnits;
        $this->assertSame($expect, $o->graphable($unit));
    }    
    /**
     *
     */
    public static function dataFindUnit() {
        return array(
            array('&#176;F',array('longName' => '&#176;F','varType' => 'float','convert' => array('&#176;C' => 'FtoC'))),
            array("ASDF", FALSE),
        );
    }
    /**
     * @dataProvider dataFindUnit
     */
    public function testfindUnit($unit, $expect) {
        $o = new unitConversion;
        $o->units = $this->testUnits;
        $this->assertSame($expect, $o->findUnit($unit));
    }

    /**
     *
     */
    public static function dataDataType() {
        return array(
            array("&#176;C", "", "asdf", "asdf"),
            array("Direction", "", "asdf", "raw"),
        );
    }
    /**
     * @dataProvider dataDataType
     */
    public function testgetDataType($from, $to, $default, $expect) {
        $o = new unitConversion;
        $o->units = $this->testUnits;
        $this->assertSame($expect, $o->getDataType($from, $to, $default));
    }

    /**
     *
     */
    public static function dataGetConvFunct() {
        return array(
            array("&#176;F", "&#176;C", "diff", "FtoC"),
            array("&#176;F", "&#176;F", "diff", NULL),
            array("&#176;F", "Direction", "diff", NULL),
        );
    }
    /**
     * @dataProvider dataGetConvFunct
     */
    public function testGetConvFunct($to, $from, $type, $expect) {
        $o = new unitConversion;
        $o->units = $this->testUnits;
        $this->assertSame($expect, $o->getConvFunct($to, $from, $type));
    }

    /**
     *
     */
    public static function dataGetPossConv() {
        return array(
            array("raw", NULL, array("&#176;C" => "&#176;F", "&#176;F" => "&#176;C", "&#176;" => "Direction", "Direction" => "&#176;")),
            array("diff", NULL, array("&#176;C" => "&#176;F", "&#176;F" => "&#176;C")),
            array("raw", "&#176;C", array("&#176;C" => "&#176;F", "&#176;C" => "&#176;C")),
            array("diff", "&#176;F", array("&#176;F" => "&#176;C", "&#176;F" => "&#176;F")),
            array("raw", "Direction", array("Direction" => "&#176;", "Direction" => "Direction")),
        );
    }
    /**
     * @dataProvider dataGetPossConv
     */
    public function testGetPossConv($type, $from, $expect) {
        $o = new unitConversion;
        $o->units = $this->testUnits;
        $this->assertSame($expect, $o->getPossConv($type, $from));
    }

    /**
     *
     */
    public static function dataTemperature() {
        return array(
            array(100, 212, 0, "raw"),
            array(0, 32, 0, "raw"),
            array(-40, -40, 0, "raw"),
            array(100, 180, 0, "diff"),
            array(0, 0, 0, "diff"),
        );
    }
    /**
     * @dataProvider dataTemperature
     */
    public function testCtoF($c, $f, $time, $type) {
        $o = new unitConversion;
        $this->assertEquals($f, $o->CtoF($c, $time, $type));        
	}

    /**
     * @dataProvider dataTemperature
     */
	public function testFtoC($c, $f, $time, $type) {
        $o = new unitConversion;
        $this->assertEquals($c, $o->FtoC($f, $time, $type));        
	}


    /**
     *
     */
    public static function dataMilli() {
        return array(
            array(1, 1000, 0, "raw"),
            array(0.001, 1, 0, "raw"),
            array(0, 0, 0, "raw"),
        );
    }
    /**
     * @dataProvider dataMilli
     */
	public function testtoMilli($v, $m, $time, $type) {
        $o = new unitConversion;
        $this->assertEquals($m, $o->toMilli($v, $time, $type));
	}
    /**
     * @dataProvider dataMilli
    */
	public function testfromMilli($v, $m, $time, $type) {
        $o = new unitConversion;
        $this->assertEquals($v, $o->fromMilli($m, $time, $type));
	}

    /**
     *
     */
    public static function dataCenti() {
        return array(
            array(1, 100, 0, "raw"),
            array(0.01, 1, 0, "raw"),
            array(0, 0, 0, "raw"),
        );
    }
    /**
     * @dataProvider dataCenti
     */
	public function testfromCenti($v, $c, $time, $type) {
        $o = new unitConversion;
        $this->assertEquals($v, $o->fromCenti($c, 0, 'raw'));
	}


    /**
     *
     */
    public static function dataCnttoRPM() {
        return array(
            array(0, NULL, 0, "raw", 0),
            array(50, 50, 60, "diff", 1),
            array(50, 25, 60, "diff", 2),
            array(50, 50, 60, "diff", 0),
        );
    }
    /**
     * @dataProvider dataCnttoRPM
     */
    public function testCnttoRPM($cnt, $rpm, $time, $type, $cntperrev) {
        $o = new unitConversion;
        $this->assertEquals($rpm, $o->CnttoRPM($cnt, $time, $type, $cntperrev));
    }

    /**
     *
     */
    public static function dataDirection() {
        // Put stuff that works either direction here.
        return array(
            array(0, "N"),
            array(22.5, "NNE"),
            array(45, "NE"),
            array(67.5, "ENE"),
            array(90, "E"),
            array(112.5, "ESE"),
            array(135, "SE"),
            array(157.5, "SSE"),
            array(180, "S"),
            array(202.5, "SSW"),
            array(225, "SW"),
            array(247.5, "WSW"),
            array(270, "W"),
            array(292.5, "WNW"),
            array(315, "NW"),
            array(337.5, "NNW"),
        );
    }
    /**
     *
     */
    public static function dataDirtoNumDir() {
        $newStuff = array(
            array(0, "ASDF"),
        );
        return array_merge(unitConversionTest::dataDirection(), $newStuff);
    }
    /**
     * @dataProvider dataDirtoNumDir
     */
    public function testDirtonumDir($numdir, $dir) {
        $o = new unitConversion;
        $this->assertSame($numdir, $o->DirtonumDir($dir, 0, 0));
    }

    /**
     *
     */
    public static function dataNumDirtoDir() {
        $newStuff = array(
            array(360, "N"),
            array(-1, "N"),
        );
        return array_merge(unitConversionTest::dataDirection(), $newStuff);
    }
    /**
     * @dataProvider dataNumDirtoDir
     */
    public function testnumDirtoDir($numdir, $dir) {
        $o = new unitConversion;
        $this->assertSame($dir, $o->numDirtoDir($numdir, 0, 0));
    }
    
    /**
     *
     */
    public static function datakWhTokW() {
        return array(
            array(100, 0, "diff", 0, NULL),
            array(100, 0, "raw", 0, NULL),
            array(1, 360, "diff", 0, 10),
            array(100, -360, "diff", 0, 1000),
        );
    }
    /**
     * @dataProvider datakWhTokW
     */
    public function testkWhTokW ($val, $time, $type, $extra, $expect) {
        $o = new unitConversion;
        $this->assertEquals($expect, $o->kWhTokW($val, $time, $type, $extra));
    }

    /**
     *
     */
    public static function datakWhToW() {
        return array(
            array(100, 0, "diff", 0, NULL),
            array(100, 0, "raw", 0, NULL),
            array(1, 360, "diff", 0, 10000),
            array(100, -360, "diff", 0, 1000000),
        );
    }
    /**
     * @dataProvider datakWhToW
     */
    public function testkWhToW ($val, $time, $type, $extra, $expect) {
        $o = new unitConversion;
        $this->assertEquals($expect, $o->kWhToW($val, $time, $type, $extra));
    }

}

// Call unitConversionTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "unitConversionTest::main") {
    unitConversionTest::main();
}


/**
 *  This is a mock class to test the rest of the system.
 */
class unitConversionMock extends unitConversion {
    var $units = array(
        'Test' => array(
            'A' => array(
                'longName' => 'A',
                'varType' => 'float',
                'convert' => array(
                    'B' => 'AtoB',
                    'C' => 'AtoC',
                ),
            ),
            'B' => array(
                'longName' => 'B',
                'varType' => 'float',
                'convert' => array(
                    'A' => 'BtoA',
                    'C' => 'BtoC',
                ),
                'preferred' => 'A',
            ),
            'C' => array(
                'longName' => 'C',
                'varType' => 'float',
                'convert' => array(
                    'A' => 'CtoA',
                    'B' => 'CtoB',
                ),
                'preferred' => 'A',
            ),
        ),
        'Test2' => array(
            'D' => array(
                'longName' => 'D',
                'varType' => 'float',
                'convert' => array(
                    'E' => 'AtoB',
                    'F' => 'AtoC',
                ),
            ),
            'E' => array(
                'longName' => 'E',
                'varType' => 'float',
                'convert' => array(
                    'D' => 'BtoA',
                    'F' => 'BtoC',
                ),
                'preferred' => 'D',
            ),
            'F' => array(
                'longName' => 'C',
                'varType' => 'float',
                'convert' => array(
                    'D' => 'CtoA',
                    'E' => 'CtoB',
                ),
            ),
        ),
    );
    
    public function AtoB($W, $time, $type) {
        return 2*$W;
    }

    public function BtoA($W, $time, $type) {
        return $W/2;
    }
    public function AtoC($W, $time, $type) {
        return 4*$W;
    }

    public function CtoA($W, $time, $type) {
        return $W/4;
    }
    public function BtoC($W, $time, $type) {
        return 10*$W;
    }

    public function CtoB($W, $time, $type) {
        return $W/10;
    }

}

?>
