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
namespace HUGnet\devices\functions\drivers;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBase.php";
/** This is a required class */
    require_once CODE_BASE.'devices/functions/drivers/Temperature.php';

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
class TemperatureTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "Temperature";
    /** This is the output object */
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
        parent::setUp();
        $this->o = \HUGnet\devices\functions\Driver::factory(
            "Temperature", $this->fct
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
            array(
                "extraValues",
                array(
                ),
                array(
                    array(
                        'ADuCThermocouple' => 'Thermocouple',
                        'ADuCMF51E' => 'Cantherm MF51E Thermistor',
                        'ADuCScaledTemp' => 'Scaled Temperature Sensor',
                        'ADuCUSSensorRTD' => 'USSensor RTD',
                        'ADuCVishayRTD' => 'Vishay RTD',
                    ),
                ),
            ),
            array(
                "extraDefault",
                array(
                ),
                array(
                    "ADuCThermocouple"
                ),
            ),
            array(
                "extraValues",
                array(
                    "system" => array(
                    ),
                    "device" => array(
                        "arch" => "0039-12",
                    ),
                    "fct" => array(
                    ),
                ),
                array(
                    array(
                        'AVRBC2322640' => 'BC Components 2322640 Thermistor',
                        'AVRB57560G0103F000' => 'EPCOS B57560G0103F000 Thermistor',
                        'AVRIMCSolar' => 'IMC Solar Thermistor',
                    ),
                ),
            ),
            array(
                "extraDefault",
                array(
                    "system" => array(
                    ),
                    "device" => array(
                        "arch" => "0039-12",
                    ),
                    "fct" => array(
                    ),
                ),
                array(
                    "AVRBC2322640"
                ),
            ),
        );
    }
    /**
    * data provider for testType
    *
    * @return array
    */
    public static function dataExecute()
    {
        return array(
            array(
                "Name",
                array(
                    "device" => array(
                        "id" => 8,
                        "HWPartNum" => "0039-37-01-B",
                        "DaughterBoard" => "",
                        "arch" => "0039-37",
                    ),
                ), 
                true,
                array(
                    'input' => array(
                        0 => array(
                            'dev' => 8,
                            'input' => 0,
                            'id' => 249,
                            'driver' => 'ADuCInputTable',
                            'tableEntry' => array(
                                'ADC0EN' => 1,
                                'ADC1EN' => 0,
                                'driver0' => 66,
                                'ADC0CH' => 1,
                            ),
                            'type' => 'ADuCInputTable',
                            'params' => array(),
                        )
                    )
                ),
            ),
            array(
                "Name",
                array(
                    "device" => array(
                        "id" => 8,
                        "HWPartNum" => "0039-28-01-A",
                        "DaughterBoard" => "0039-23-01-A",
                        "arch" => "0039-28",
                    ),
                ), 
                true,
                array(
                    'input' => array(
                        0 => array(
                            'dev' => 8,
                            'input' => 0,
                            'id' => 249,
                            'driver' => 'ADuCInputTable',
                            'tableEntry' => array(
                                'driver' => 2,
                                'MUX' => 1,
                            ),
                            'type' => 'ADuCInputTable',
                            'params' => array(),
                        )
                    )
                ),
            ),
        );
    }

}
?>
