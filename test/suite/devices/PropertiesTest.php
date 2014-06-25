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
        $this->m = Properties::factory(TEST_CONFIG_BASE."/files/devices.xml","0039-14-01-A","0039-23-01");
        $this->n = Properties::factory(TEST_CONFIG_BASE."/files/devices.xml","0039-12-01-A","");
        $this->o = Properties::factory(TEST_CONFIG_BASE."/files/devices.xml","0039-12-02-C","");
        $this->p = Properties::factory(TEST_CONFIG_BASE."/files/devices.xml","0039-37-01-E","");
        $this->q = Properties::factory(TEST_CONFIG_BASE."/files/devices.xml","0039-28-01-A","0039-23-01");
        $this->r = Properties::factory(TEST_CONFIG_BASE."/files/devices.xml","0039-12-02-C","0039-15-01");
        $this->s = Properties::factory(TEST_CONFIG_BASE."/files/devices.xml","","");
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
                    0 => '0039-12-01-A',
                    1 => '0039-12-02-A',
                    2 => '0039-12-02-B',
                    3 => '0039-12-02-C',
                    4 => '0039-21-01-A',
                    5 => '0039-21-02-A',
                    6 => '0039-28-01-A',
                    7 => '0039-37-01-A',
                    8 => '0039-37-01-B',
                    9 => '0039-37-01-C',
                    10 => '0039-37-01-D',
                    11 => '0039-37-01-E',
                    12 => '0039-37-01-F',
                    13 => '0039-37-01-G',
                    14 => '0039-37-01-H',
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
                    0 => '0039-15-01',
                    1 => '0039-16-01',
                    2 => '0039-23-01',
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
    * data provider for testGetEndpointNum
    *
    * @return array
    *
    */
    public static function dataGetEndpointNum()
    {
        return array(
            array(
                true,
                '0039-12-02-C',
            ),
        );
    }

    /**
    * test routine for get endpoint number 
    *
    * @param string $name   The name of the variable to test
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGetEndpointNum
    */
    public function testGetEndpointNum($name, $expect)
    {
        $this->assertSame($expect, $this->o->getEndpointNum());
    }

    /**
    * data provider for testGetDaughterboardNum
    *
    * @return array
    *
    */
    public static function dataGetDaughterboardNum()
    {
        return array(
            array(
                true,
                '0039-23-01',
            ),
        );
    }

    /**
    * test routine for get daughterboard number 
    *
    * @param string $name   The name of the variable to test
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGetDaughterboardNum
    */
    public function testGetDaughterboardNum($name, $expect)
    {
        $this->assertSame($expect, $this->m->getDaughterboardNum());
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
                true,
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
                ),
            ),
        );
    }




    /**
    *************************************************************
    * test routine for get endpoint pins list
    *
    * @param string $name   The name of the variable to test
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataEpPinList
    */
    public function testEpPinList($name, $expect)
    {
        $this->assertSame($expect, $this->o->getEpPinList());
    }


    /**
    * data provider for testGetPinList error condition
    *
    * @return array
    *
    */
    public static function dataGetEpPinList()
    {
        return array(
            array(
                true,
                array(
                    0 => 'Error',
                    1 => 'Endpoint not found!',
                ),
            ),
        );
    }


    /**
    * test routine for get pin list error condition
    * 
    * @param string $name   The name of the variable to test
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGetEpPinList
    */
    public function testGetEpPinList($name, $expect)
    {
        $this->assertSame($expect, $this->s->getEpPinList());
    }
   
    /**
    * data provider for testEpPinListError 
    *
    * @return array
    *
    */
    public static function dataEpPinListError()
    {
        return array(
            array(
                true,
                array(
                    0 => 'Error',
                    1 => 'No Pins to display!',
                ),
            ),
        );
    }

    /**
    * test routine for ep pin list error condition
    * 
    * @param string $name   The name of the variable to test
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataEpPinListError
    */
    public function testEpPinListError($name, $expect)
    {
        $this->assertSame($expect, $this->n->getEpPinList());
    }

    
    /**
    * data provider for testGetEpPinPropertiesError 
    *
    * @return array
    *
    */
    public static function dataGetEpPinPropertiesError()
    {
        return array(
            array(
                true,
                "Port4",
                array(
                    0 => 'Error',
                    1 => 'No Pins to display!',
                ),
            ),
        );
    }
    /**
    * test routine for get endpoint pin properties error condition
    * 
    * @param string $name   The name of the variable to test
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGetEpPinPropertiesError
    */
    public function testGetEpPinPropertiesError($name, $pin, $expect)
    {
        $this->assertSame($expect, $this->n->getEpPinProperties($pin));
    }
        
 

    /**
    * data provider for testGetEpPinProperties error condition
    *
    * @return array
    *
    */
    public static function dataGetEpPinProperties()
    {
        return array(
            array(
                true,
                "Port4",
                array(
                    0 => 'Error',
                    1 => 'Endpoint not found!',
                ),
            ),
        );
    }

    /**
    * test routine for get endpoint pin properties error condition
    * 
    * @param string $name   The name of the variable to test
    * @param array  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataGetEpPinProperties
    */
    public function testGetEpPinProperties($name, $pin, $expect)
    {
        $this->assertSame($expect, $this->s->getEpPinProperties($pin));
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
                true,
                "Port7",
                array(
                    0 => 'AI',
                    1 => '100K',
                    2 => '1K',
                    3 => 'R21',
                    4 => 'CGND',
                    5 => 'Y',
                ),
            ),
            array(
                true,
                "Port1",
                array(
                    0 => 'AI',
                    1 => '1K',
                    2 => 'none',
                    3 => 'none',
                    4 => 'none',
                    5 => 'N',
                ),
            ),
            array(
                true,
                "Port9",
                array(
                    0 => 'Error',
                    1 => 'Pin not found!',
                ),
            ),
        );
    }


    /**
    *************************************************************
    * test routine for get endpoint pin properties
    *
    * @param string $name   The name of the variable to test
    * @param string $pin    The name of the pin to test
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataEpPinProperties
    */
    public function testEpPinProperties($name, $pin, $expect)
    {
        $this->assertSame($expect, $this->p->getEpPinProperties($pin));
    }



    /**
    * data provider for testDbPinListError
    *
    * @return array
    */
    public static function dataDbPinListError()
    {
        return array(
            array(
                true,
                array(
                    0 => 'Error',
                    1 => 'Daughterboard not found!',
                ),
            ),
        );
    }

    /**
    *************************************************************
    * test routine for get daughterboard pins list error
    *
    * @param string $name   The name of the variable to test
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataDbPinListError
    */
    public function testDbPinListError($name, $expect)
    {
        $this->assertSame($expect, $this->n->getDbPinList());
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
                true,
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
        );
    }


    /**
    *************************************************************
    * test routine for get daughterboard pins list
    *
    * @param string $name   The name of the variable to test
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataDbPinList
    */
    public function testDbPinList($name, $expect)
    {
        $this->assertSame($expect, $this->q->getDbPinList());
    }


    /**
    * data provider for testDbPinPropertiesError
    *
    * @return array
    */
    public static function dataDbPinPropertiesError()
    {
        return array(
            array(
                true,
                'Port2',
                array(
                    0 => array(
                            0 => 'Error',
                            1 => 'Daughterboard not found!',
                        ),
                ),
            ),
        );
    }

    /**
    *************************************************************
    * test routine for get daughterboard pin properties error
    *
    * @param string $name   The name of the variable to test
    * @param string $pin    The name of the pin to test
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataDbPinPropertiesError
    */
    public function testDbPinPropertiesError($name, $pin, $expect)
    {
        $this->assertSame($expect, $this->n->getDbPinProperties($pin));
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
                true,  /* 0039-15-01 */
                "Port2",
                array(
                    0 => array(
                            0 => 'AI',
                        ),
                    1 => array(
                            0 => 'Connect',
                            1 => '0039-12-02-C',
                            2 => 'Port2',
                         ),
                    2 => array(
                            0 => 'Connect',
                            1 => '0039-28-01-A',
                            2 => 'Port2',
                        ),
                ),
            ),
            array(
                true,
                "Port10",
                array(
                    0 => array(
                            0 => 'Error',
                            1 => 'Pin not found!',
                        ),
                ),
            ),
        );
    }


    /**
    *************************************************************
    * test routine for get daughterboard pin properties
    *
    * @param string $name   The name of the variable to test
    * @param string $pin    The name of the pin to test
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataDbPinProperties
    */
    public function testDbPinProperties($name, $pin, $expect)
    {
        $this->assertSame($expect, $this->r->getDbPinProperties($pin));
    }


    /**
    * data provider for testDbToEpConnectionsError
    *
    * @return array
    */
    public static function dataDbToEpConnectionsError()
    {
        return array(
            array(
                    true,
                    array(
                        0 => array(
                            0 => 'Error',
                            1 => 'Daughterboard not found!',
                            ),
                    ),
            )
        );
    }

 

    /**
    ******************************************************************
    * test routine for getDbtoEpConnections error condition
    * 
    * @param string $dbname   The name of the daughterboard to test
    * @param array  $expect   The expected return
    *
    * @return null
    *
    * @dataProvider dataDbToEpConnectionsError
    */
    public function testDbToEpConnectionsError($dbname, $expect)
    {
        $this->assertSame($expect, $this->s->getDbToEpConnections());
    }

    /**
    * data provider for testDbToEpConnectionsError2
    *
    * @return array
    */
    public static function dataDbToEpConnectionsError2()
    {
        return array(
            array(
                true,
                array(
                    0 => array(
                            0 => 'Error',
                            1 => 'Endpoint not found in connections',
                        ),
                    1 => array(
                            0 => 'Port2',
                        ),
                    2 => array(
                            0 => 'Port3',
                        ),
                    3 => array(
                            0 => 'Port4',
                        ),
                    4 => array(
                            0 => 'Port5',
                        ),
                    5 => array(
                            0 => 'Port6',
                        ),
                    6 => array( 
                            0 => 'Port7',
                        ),
                    7 => array(
                            0 => 'Port8',
                        ),
                    8 => array(
                            0 => 'Port9',
                        ),
                    9 => array(
                            0 => 'Port10',
                        ),
                    10 => array(
                            0 => 'Port11',
                        ),
                    11 => array(
                            0 => 'Port12',
                        ),
                    12 => array(
                            0 => 'Port13',
                        ),
                    13 => array(
                            0 => 'Port14',
                        ),
                    14 => array(
                            0 => 'Port15',
                        ),
                    15 => array(
                            0 => 'Port16',
                        ),
                ),
            ),
        );
    }

    /**
    ******************************************************************
    * test routine for getDbtoEpConnections error 2 condition
    * 
    * @param string $dbname   The name of the daughterboard to test
    * @param array  $expect   The expected return
    *
    * @return null
    *
    * @dataProvider dataDbToEpConnectionsError2
    */
    public function testDbToEpConnectionsError2($dbname, $expect)
    {
        $this->assertSame($expect, $this->m->getDbToEpConnections());
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
                true,  /* 0039-23-01 */
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
        );
    }

    /**
    *************************************************************
    * test routine for get daughterboard to endpoint connections
    *
    * @param string $dbname   The name of the daughterboard test
    * @param string $epname  The name of the endpoint test
    * @param array  $expect The expected return.
    *
    * @return null
    *
    * @dataProvider dataDbToEpConnections
    */
    public function testDbToEpPConnections($dbname, $expect)
    {
        $this->assertSame($expect, $this->q->getDbToEpConnections());
    }


    /**
    *************************************************************
    * test routine for constructor using default filename
    *
    * @param string $name - name of the variable to test
    * @param array  $expect - the expected return
    *
    * @return null
    *
    * @dataProvider dataGetDaughterboards
    */
    public function testConstructor($name, $expect)
    {
       
        unset($this->o);
        $this->o = Properties::factory(null,"0039-28-01-A","0039-23-01");   
        $this->assertSame($expect, $this->o->getDaughterboards());
    }


}
?>
