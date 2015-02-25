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
namespace HUGnet\devices;
/** This is a required class */
require_once CODE_BASE.'devices/Properties.php';
/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyTable.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is our interface */
require_once CODE_BASE.'devices/drivers/DriverInterface.php';

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
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PropertiesTest extends \PHPUnit_Framework_TestCase
{
    /** test objects */
    protected $m;
    protected $n;
    protected $o;
    protected $p;
    protected $q;
    protected $r;
    protected $s;

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
        $this->o = Properties::factory(
            "0039-28-01-A", "0039-23-01-A", ""
        );
        $this->n = Properties::factory(
            "0039-12-02-C", "0039-15-01-A",""
        );
        $this->m = Properties::factory(
            "0039-37-01-E", "0039-23-01-A",""
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
        unset($this->m);
        unset($this->n);
        unset($this->o);
        unset($this->p);
        unset($this->q);
        unset($this->r);
        unset($this->s);
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataGetEndpoints()
    {
        return array(
            array(
                true,
                array(
                    0 => '0039-28-01-A',
                ),
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
    * @dataProvider dataGetEndpoints
    */
    public function testGetEndpoints($name, $expect)
    {
        $this->assertSame($expect, $this->o->getEndpoints());
    }

    /**
    * data provider for testGetDaughterboards
    *
    * @return array
    */
    public static function dataGetDaughterboards()
    {
        return array(
            array(
                true,
                array(
                    0 => '0039-23-01-A',
                    1 => '0039-23-01-C',
                    2 => '0039-23-01-D',
                ),
            ),
        );
    }

    /**
    *************************************************************
    * test routine for get daughterboards list
    *
    * @param string $name   The name of the variable to test
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataGetDaughterboards
    */
    public function testGetDaughterboards($name, $expect)
    {
        $this->assertSame($expect, $this->o->getDaughterboards());
    }

    /**
    * data provider for testGetDaughterboardNum
    *
    * @return array
    *
    */
    public static function dataGetSetPN()
    {
        return array(
            array(
                '0039-28-01-A',
                '0039-23-01-A',
                '0039-28-01-A',
                '0039-23-01-A',
            ),
        );
    }

    /**
    * test routine for get daughterboard number 
    *
    * @param string $epNum    The endpoint number to set 
    * @param string $dbNum    The daughterboard number to set
    * @param string $expectEP The expected return from the getter (endpoint)
    * @param string $expectDB The expected return from the getter (daughterboard)
    *
    * @return null
    *
    * @dataProvider dataGetSetPN
    */
    public function testGetSetPN(
        $epNum, $dbNum, $expectEP, $expectDB
    ) {
        $this->assertSame(
            $expectDB, 
            $this->o->getDaughterboardNum(), 
            "Daughterboard Part Num wrong"
        );
        $this->assertSame(
            $expectEP, 
            $this->o->getEndpointNum(),
            "Endpoint Part Num wrong"
        );
    }



    /**
    * data provider for testEpPinList
    *
    * @return array
    */
    public static function dataEpPinList()
    {
        return array(
            array(
                "0039-12-02-C", 
                "0039-15-01-A",
                array(
                    0 => 'ADC0',
                    1 => 'ADC1',
                    2 => 'ADC2',
                    3 => 'ADC3',
                    4 => 'ADC4',
                    5 => 'ADC5',
                    6 => 'ADC6',
                    7 => 'ADC7',
                    8 => 'ADC8',
                    9 => 'PB0',
                    10 => 'PB1',
                    11 => 'PB3',
                ),
            ),
            array(
                "0039-12-01-A", 
                "0039-15-01-A",
                false,
            ),
        );
    }




    /**
    *************************************************************
    * test routine for get endpoint pins list
    *
    * @param string $epNum  The endpoint number to set 
    * @param string $dbNum  The daughterboard number to set
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataEpPinList
    */
    public function testEpPinList($epNum, $dbNum, $expect)
    {
        $this->n->setPartnumbers($epNum, $dbNum);
        $this->assertSame($expect, $this->n->getEpPinList());
    }

    /**
    * data provider for testPinList
    *
    * @return array
    */
    public static function dataPinList()
    {
        return array(
            array(
                "0039-12-02-C", 
                "0039-15-01-A",
                array(
                    0 => 'Port1',
                    1 => 'Port2',
                    2 => 'Port3',
                    3 => 'Port4',
                    4 => 'Port5',
                    5 => 'Port6',
                    6 => 'Port7',
                    7 => 'Port8',
                    8 => 'Port9',                ),
            ),
            array(
                "0039-12-02-C", 
                "0039-16-01-B",
                array(
                    0 => 'ADC0',
                    1 => 'ADC1',
                    2 => 'ADC2',
                    3 => 'ADC3',
                    4 => 'ADC4',
                    5 => 'ADC5',
                    6 => 'ADC6',
                    7 => 'ADC7',
                    8 => 'ADC8',
                    9 => 'PB0',
                    10 => 'PB1',
                    11 => 'PB3',
                ),
            ),
            array(
                "0039-28-01-B", 
                "0039-23-01-B",
                false,
            ),
        );
    }




    /**
    *************************************************************
    * test routine for get endpoint pins list
    *
    * @param string $epNum  The endpoint number to set 
    * @param string $dbNum  The daughterboard number to set
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataPinList
    */
    public function testPinList($epNum, $dbNum, $expect)
    {
        $this->n->setPartnumbers($epNum, $dbNum);
        $this->assertSame($expect, $this->n->getPinList());
    }

    /**
    * data provider for testEpPinProperties
    *      data from endpoint 0039-37-01-E
    *
    * @return array
    */
    public static function dataEpPinProperties()
    {
        return array(
            array(
                "0039-37-01-E", 
                "0039-23-01-A",
                "Port7",
                array(
                    "properties"  => 'AI',
                    "seriesRes"   => '100K',
                    "shuntRes"    => '1K',
                    "shuntLoc"    => 'R21',
                    "shuntPull"   => 'CGND',
                    "highVoltage" => 'Y',
                ),
            ),
            array(
                "0039-37-01-E", 
                "0039-23-01-A",
                "Port1",
                array(
                    "properties" => 'AI',
                    "seriesRes"   => '1K',
                    "shuntRes"    => 'none',
                    "highVoltage" => 'N',
                ),
            ),
            array(
                "0039-28-01-A", 
                "0039-16-01-B",
                "Port9",
                false,
            ),
            array(
                "0039-12-01-A", 
                "0039-15-01-A",
                "Port1",
                false,
            ),
        );
    }


    /**
    *************************************************************
    * test routine for get endpoint pin properties
    *
    * @param string $epNum  The endpoint number to set 
    * @param string $dbNum  The daughterboard number to set
    * @param string $pin    The name of the pin to test
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataEpPinProperties
    */
    public function testEpPinProperties($epNum, $dbNum, $pin, $expect)
    {
        $this->m->setPartnumbers($epNum, $dbNum);
        $this->assertSame($expect, $this->m->getEpPinProperties($pin));
    }



    /**
    * data provider for testDbPinList
    *
    * @return array
    */
    public static function dataDbPinList()
    {
        return array(
            array(
                "0039-28-01-A", 
                "0039-23-01-A",
                array(
                    0 => 'Port1',
                    1 => 'Port2',
                    2 => 'Port3',
                    3 => 'Port4',
                    4 => 'Port5',
                    5 => 'Port6',
                    6 => 'Port7',
                    7 => 'Port8',
                    8 => 'Port9',
                    9 => 'Port10',
                    10 => 'Port11',
                    11 => 'Port12',
                    12 => 'Port13',
                    13 => 'Port14',
                    14 => 'Port15',
                    15 => 'Port16',
                ),
            ),
            array(
                "0039-28-01-D", 
                "0039-15-01-D",
                false,
            ),
        );
    }


    /**
    *************************************************************
    * test routine for get daughterboard pins list
    *
    * @param string $epNum  The endpoint number to set 
    * @param string $dbNum  The daughterboard number to set
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataDbPinList
    */
    public function testDbPinList($epNum, $dbNum, $expect)
    {
        $this->o->setPartnumbers($epNum, $dbNum);
        $this->assertSame($expect, $this->o->getDbPinList());
    }


    /**
    * data provider for testDbPinProperties
    *
    * @return array
    */
    public static function dataDbPinProperties()
    {
        return array(
            array(
                "0039-12-02-C", 
                "0039-15-01-A",
                "Port2",
                array(
                    "properties" => 'AI',
                    "mbcon"      => 'ADC1',
                ),
            ),
            array(
                "0039-12-02-C", 
                "0039-15-01-A",
                "Port10",
                false,
            ),
            array(
                "0039-28-01-A", 
                "0039-16-01-B",
                "Port9",
                false,
            ),
            array(
                "0039-12-01-A", 
                "0039-15-01-A",
                "Port9",
                array(
                    "properties" => 'AI',
                ),
            ),
        );
    }


    /**
    *************************************************************
    * test routine for get daughterboard pin properties
    *
    * @param string $epNum  The endpoint number to set 
    * @param string $dbNum  The daughterboard number to set
    * @param string $pin    The name of the pin to test
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataDbPinProperties
    */
    public function testDbPinProperties($epNum, $dbNum, $pin, $expect)
    {
        $this->n->setPartnumbers($epNum, $dbNum);
        $this->assertSame($expect, $this->n->getDbPinProperties($pin));
    }

    /**
    * data provider for testPinProperties
    *
    * @return array
    */
    public static function dataPinProperties()
    {
        return array(
            array(
                "0039-12-02-C", 
                "0039-15-01-A",
                "Port2",
                array(
                    "properties" => 'AI',
                    "mbcon"      => 'ADC1',
                ),
            ),
            array(
                "0039-12-02-C", 
                "0039-15-01-A",
                "Port10",
                false,
            ),
            array(
                "0039-12-02-C", 
                "0039-16-01-B",
                "Port10",
                false,
            ),
            array(
                "0039-28-01-A", 
                "0039-16-01-B",
                "Port17",
                false,
            ),
       );
    }


    /**
    *************************************************************
    * test routine for get daughterboard pin properties
    *
    * @param string $epNum  The endpoint number to set 
    * @param string $dbNum  The daughterboard number to set
    * @param string $pin    The name of the pin to test
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataPinProperties
    */
    public function testPinProperties($epNum, $dbNum, $pin, $expect)
    {
        $this->n->setPartnumbers($epNum, $dbNum);
        $this->assertSame($expect, $this->n->getPinProperties($pin));
    }


    /**
    * data provider for testPinProperties2
    *
    * @return array
    */
    public static function dataPinProperties2()
    {
        return array(
             array(
                "0039-37-01-E", 
                "0039-23-01-A",
                "Port7",
                array(
                    'properties' => 'AI',
                    'seriesRes' => '100K',
                    'shuntRes' => '1K',
                    'shuntLoc' => 'R21',
                    'shuntPull' => 'CGND',
                    'highVoltage' => 'Y',
                ),
            ),
            array(
                "0039-37-01-E", 
                "0039-23-01-A",
                "Port1",
                array(
                    'properties' => 'AI',
                    'seriesRes' => '1K',
                    'shuntRes' => 'none',
                    'highVoltage' => 'N',
                ),
            ),
            array(
                "0039-37-01-E", 
                "0039-23-01-A",
                "Port9",
                false,
            ),
            array(
                "0039-37-02-L", 
                "0039-23-01-A",
                "Port12",
                false,
            ),
       );
    }


    /**
    *************************************************************
    * test routine for get daughterboard pin properties
    *
    * @param string $epNum  The endpoint number to set 
    * @param string $dbNum  The daughterboard number to set
    * @param string $pin    The name of the pin to test
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataPinProperties2
    */
    public function testPinProperties2($epNum, $dbNum, $pin, $expect)
    {
        $this->m->setPartnumbers($epNum, $dbNum);
        $this->assertSame($expect, $this->m->getEpPinProperties($pin));
    }





    /**
    * data provider for testDbToEpConnections
    *
    * @return array
    */
    public static function dataDbToEpConnections()
    {
        return array(
            array(
                "0039-28-01-A", 
                "0039-23-01-A",
                array(
                    0 => array(
                            0 => 'Port1',
                            1 => 'PC0',
                        ),
                    1 => array(
                            0 => 'Port2',
                            1 => 'PC1',
                        ),
                    2 => array(
                            0 => 'Port3',
                            1 => 'PC2',
                        ),
                    3 => array(
                            0 => 'Port4',
                            1 => 'PC3',
                        ),
                    4 => array(
                            0 => 'Port5',
                            1 => 'PC4',
                        ),
                    5 => array(
                            0 => 'Port6',
                            1 => 'PC5'
                        ),
                    6 => array( 
                            0 => 'Port7',
                            1 => 'ADC6',
                        ),
                    7 => array(
                            0 => 'Port8',
                            1 => 'ADC7'
                        ),
                    8 => array(
                            0 => 'Port9',
                            1 => 'PB0',
                        ),
                    9 => array(
                            0 => 'Port10',
                            1 => 'PB1',
                        ),
                    10 => array(
                            0 => 'Port11',
                            1 => 'PB2',
                        ),
                    11 => array(
                            0 => 'Port12',
                            1 => 'PB3',
                        ),
                    12 => array(
                            0 => 'Port13',
                            1 => 'PB4',
                        ),
                    13 => array(
                            0 => 'Port14',
                            1 => 'PB5',
                        ),
                    14 => array(
                            0 => 'Port15',
                            1 => 'PD5',
                        ),
                    15 => array(
                            0 => 'Port16',
                            1 => 'PD6',
                        ),
                ),
            ),
            array(
                "0039-28-01-A", 
                "0039-16-01-A",
                false,
            ),
            array(
                "0039-12-01-A", 
                "0039-15-01-A",
                false,
            ),
        );
    }

    /**
    *************************************************************
    * test routine for get daughterboard to endpoint connections
    *
    * @param string $epNum  The endpoint number to set 
    * @param string $dbNum  The daughterboard number to set
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataDbToEpConnections
    */
    public function testDbToEpPConnections($epNum, $dbNum, $expect)
    {
        $this->o->setPartnumbers($epNum, $dbNum);
        $this->assertSame($expect, $this->o->getDbToEpConnections());
    }


    /**
    *************************************************************
    * test routine for constructor using default filename
    *
    * @param string $name   name of the variable to test
    * @param array  $expect the expected return
    *
    * @return null
    *
    * @dataProvider dataGetDaughterboards
    */
    public function testConstructor($name, $expect)
    {
       
        unset($this->o);
        $this->o = Properties::factory("0039-28-01-A", "0039-23-01-A");
        $this->assertSame($expect, $this->o->getDaughterboards());
    }



    /**
    * data provider for testEmptyEpArray
    *
    * @return array
    */
    public static function dataTestEmptyEpArray()
    {
        return array(
            array(
                "0039-12-02-D", 
                "0039-15-01-A",
                false,
            ),
       );
    }

    /**
    ***************************************************************
    * test routine for emptyEpArray
    *
    * @param string $name name of variable to test
    * @param boolean $expect the expected return
    *
    * @return null
    *
    * @dataProvider dataTestEmptyEpArray()
    */
    public function testEmptyEpArray($endpoint, $daughterboard, $expect)
    {
        $this->o->setPartNumbers($endpoint, $daughterboard);
        $this->assertSame($expect, $this->o->getEpPinList());
    }


}
?>
