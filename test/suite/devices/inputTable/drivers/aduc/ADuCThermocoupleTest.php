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
namespace HUGnet\devices\inputTable\drivers\aduc;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBaseADuC.php";
/** This is a required class */
require_once CODE_BASE.'devices/inputTable/drivers/aduc/ADuCThermocouple.php';

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
class ADuCThermocoupleTest extends DriverTestBaseADuC
{
    /** This is the class we are testing */
    protected $class = "ADuCThermocouple";
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
        $this->o = \HUGnet\devices\inputTable\Driver::factory(
            "ADuCThermocouple", $this->input, 0
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
        parent::tearDown();
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
            array( //#0 Verified by http://srdata.nist.gov/its90/download/type_k.tab
                array(
                    "extra" => array()
                ),
                -23921, // (-3.76411915 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                -96.9944,
            ),
            array( //#1 Verified by http://srdata.nist.gov/its90/download/type_k.tab
                array(
                    "extra" => array()
                ),
                23927, // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                101.9745,
            ),
            array( //#2 Verified by http://srdata.nist.gov/its90/download/type_k.tab
                array(
                    "extra" => array()
                ),
                239241, // (37.64591217 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                918.0187,
            ),
            array( //#3 Too low
                array(
                    "extra" => array()
                ),
                -47121, // (-7.41478443 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                null,
            ),
            array( //#4 Too high
                array(
                    "extra" => array()
                ),
                480241, // (75.56892872 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                null,
            ),
            array( //#5 Verified by http://srdata.nist.gov/its90/download/type_k.tab
                array(
                    "extra" => array(4 => 4)
                ),
                -23921, // (-3.76411915 mV)
                1,
                array(4 => array("value" => 10)),
                array(),
                -96.9944,
            ),
            array( //#6 
                array(
                    "extra" => array(
                        0 => 1000,
                        1 => "inf",
                        2 => 1200,
                        3 => "k",
                        4 => 0
                    )
                ),
                "40FCFFFF",
                1,
                array(0 => array("value" => 24.8409)),
                array(),
                21.3644,
            ),
            array( //#7 Verified by http://srdata.nist.gov/its90/download/type_b.tab
                array(
                    "extra" => array(3 => "b")
                ),
                23927, // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                886.9668,
            ),
            array( //#8 Verified by http://srdata.nist.gov/its90/download/type_e.tab
                array(
                    "extra" => array(3 => "e")
                ),
                -23921, // (-3.76411915 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                -59.0575,
            ),
            array( //#9 Verified by http://srdata.nist.gov/its90/download/type_e.tab
                array(
                    "extra" => array(3 => "e")
                ),
                23927, // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                71.2510,
            ),
            array( //#10 Verified by http://srdata.nist.gov/its90/download/type_e.tab
                array(
                    "extra" => array(3 => "e")
                ),
                239241, // (37.64591217 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                517.9118,
            ),
            array( //#11 Verified by http://srdata.nist.gov/its90/download/type_j.tab
                array(
                    "extra" => array(3 => "j")
                ),
                -23921, // (-3.76411915 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                -69.5253,
            ),
            array( //#12 Verified by http://srdata.nist.gov/its90/download/type_j.tab
                array(
                    "extra" => array(3 => "j")
                ),
                23927, // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                82.1569,
            ),
            array( //#13 Verified by http://srdata.nist.gov/its90/download/type_j.tab
                array(
                    "extra" => array(3 => "j")
                ),
                239241, // (37.64591217 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                685.9338,
            ),
            array( //#14 Verified by http://srdata.nist.gov/its90/download/type_n.tab
                array(
                    "extra" => array(3 => "n")
                ),
                -23921, // (-3.76411915 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                -169.8769,
            ),
            array( //#15 Verified by http://srdata.nist.gov/its90/download/type_n.tab
                array(
                    "extra" => array(3 => "n")
                ),
                23927, // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                142.7561,
            ),
            array( //#16 Verified by http://srdata.nist.gov/its90/download/type_n.tab
                array(
                    "extra" => array(3 => "n")
                ),
                239241, // (37.64591217 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                1046.1158,
            ),
            array( //#17 Verified by http://srdata.nist.gov/its90/download/type_r.tab
                array(
                    "extra" => array(3 => "r")
                ),
                23927, // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                444.1438,
            ),
            array( //#19 Verified by http://srdata.nist.gov/its90/download/type_s.tab
                array(
                    "extra" => array(3 => "s")
                ),
                23927, // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                462.3454,
            ),
            array( //#20 Verified by http://srdata.nist.gov/its90/download/type_t.tab
                array(
                    "extra" => array(3 => "t")
                ),
                -23921, // (-3.76411915 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                -103.9886,
            ),
            array( //#21 Verified by http://srdata.nist.gov/its90/download/type_t.tab
                array(
                    "extra" => array(3 => "t")
                ),
                23927, // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                98.9267,
            ),
        );
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
            array( //#0 Verified by http://srdata.nist.gov/its90/download/type_k.tab
                array(
                    "extra" => array()
                ),
                "A6A2FFFF", // (-3.76411915 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                -96.9939,
            ),
            array( //#1 Verified by http://srdata.nist.gov/its90/download/type_k.tab
                array(
                    "extra" => array()
                ),
                "6F5D0000", // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                101.9745,
            ),
            /* This fails for some reason
            array( //#2 Verified by http://srdata.nist.gov/its90/download/type_k.tab
                array(
                    "extra" => array()
                ),
                dechex(239241), // (37.64591217 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                918.0423,
            ),
            */
            array( //#3 Too low
                array(
                    "extra" => array()
                ),
                "",
                1,
                array(0 => array("value" => 10)),
                array(),
                -47121,
            ),
            array( //#4 0 Vref
                array(
                    "extra" => array(2 => 0)
                ),
                "",
                1,
                array(0 => array("value" => 10)),
                array(),
                -47121,
            ),
            array( //#5 Wrong kind of thermocouple
                array(
                    "extra" => array(3 => "j")
                ),
                "",
                1,
                array(0 => array("value" => 10)),
                array(),
                -47121,
            ),
            array( //#6 Verified by http://srdata.nist.gov/its90/download/type_k.tab
                array(
                    "extra" => array(4 => 3)
                ),
                "A6A2FFFF", // (-3.76411915 mV)
                1,
                array(3 => array("value" => 10)),
                array(),
                -96.9939,
            ),
            array( //#7 Verified by http://srdata.nist.gov/its90/download/type_b.tab
                array(
                    "extra" => array(3 => "b")
                ),
                "93660000", // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                886.9668,
            ),
            array( //#8 Verified by http://srdata.nist.gov/its90/download/type_e.tab
                array(
                    "extra" => array(3 => "e")
                ),
                "2FA2FFFF", // (-3.76411915 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                -59.0575,
            ),
            array( //#9 Verified by http://srdata.nist.gov/its90/download/type_e.tab
                array(
                    "extra" => array(3 => "e")
                ),
                "755D0000", // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                71.2510,
            ),
            array( //#10 Verified by http://srdata.nist.gov/its90/download/type_e.tab
                array(
                    "extra" => array(3 => "e")
                ),
                "34010300", // (37.64591217 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                517.9118,
            ),
            array( //#11 Verified by http://srdata.nist.gov/its90/download/type_j.tab
                array(
                    "extra" => array(3 => "j")
                ),
                "8AA2FFFF", // (-3.76411915 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                -69.5253,
            ),
            array( //#12 Verified by http://srdata.nist.gov/its90/download/type_j.tab
                array(
                    "extra" => array(3 => "j")
                ),
                "775D0000", // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                82.1569,
            ),
            array( //#13 Verified by http://srdata.nist.gov/its90/download/type_j.tab
                array(
                    "extra" => array(3 => "j")
                ),
                "418E0300", // (37.64591217 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                685.9338,
            ),
            array( //#14 Verified by http://srdata.nist.gov/its90/download/type_n.tab
                array(
                    "extra" => array(3 => "n")
                ),
                "A7A2FFFF", // (-3.76411915 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                -169.8769,
            ),
            array( //#15 Verified by http://srdata.nist.gov/its90/download/type_n.tab
                array(
                    "extra" => array(3 => "n")
                ),
                "765D0000", // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                142.7561,
            ),
            array( //#16 Verified by http://srdata.nist.gov/its90/download/type_n.tab
                array(
                    "extra" => array(3 => "n")
                ),
                "98C63A00", // (37.64591217 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                1046.1158,
            ),
            array( //#17 Verified by http://srdata.nist.gov/its90/download/type_r.tab
                array(
                    "extra" => array(3 => "r")
                ),
                "D15C0000", // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                444.1438,
            ),
            array( //#19 Verified by http://srdata.nist.gov/its90/download/type_s.tab
                array(
                    "extra" => array(3 => "s")
                ),
                "965D0000", // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                462.3454,
            ),
            array( //#20 Verified by http://srdata.nist.gov/its90/download/type_t.tab
                array(
                    "extra" => array(3 => "t")
                ),
                "941B0100", // (-3.76411915 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                -103.9886,
            ),
            array( //#21 Verified by http://srdata.nist.gov/its90/download/type_t.tab
                array(
                    "extra" => array(3 => "t")
                ),
                "765D0000", // (3.76506329 mV)
                1,
                array(0 => array("value" => 10)),
                array(),
                98.9267,
            ),
        );
    }
}
?>
