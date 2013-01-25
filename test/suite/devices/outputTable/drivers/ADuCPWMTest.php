<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\outputTable\drivers;
/** This is the base class */
require_once dirname(__FILE__)."/DriverTestBase.php";
/** This is a required class */
require_once CODE_BASE.'devices/outputTable/drivers/ADuCPWM.php';

/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteBase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ADuCPWMTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "ADuCPWM";
    /** This is the object under test */
    protected $o;
    /** This is the output */
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
        $this->output = new \HUGnet\DummyBase("Output");
        $this->output->resetMock(array());
        $this->o = \HUGnet\devices\outputTable\Driver::factory(
            "ADuCPWM", $this->output
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
    public static function dataDecode()
    {
        return array(
            array( // #0
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Output"),
                    )
                ),
                "1300341278560099",
                array(
                    "Output" => array(
                        "get" => array(
                            array('extra'),
                        ),
                        "set" => array(
                            array(
                                'extra',
                                array(0, 0, 0, 0, 0, 1, 0, 0x1234, 0x5678, 0x9900)
                            ),
                        ),
                    ),
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
        $this->output->resetMock($mocks);
        $this->o->decode($string);
        $ret = $this->output->retrieve();
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
                    "Output" => array(
                        "get" => array(
                            "extra" => array(
                                0, 0, 0, 0, 0, 1, 0, 0x1234, 0x5678, 0x9900
                            ),
                        ),
                    ),
                ),
                "1100341278560099",
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
        $this->output->resetMock($mocks);
        $ret = $this->o->encode();
        $this->assertSame($expect, $ret);
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
                "extraText",
                array(),
                array(
                    0 => 'Invert PWM5',
                    1 => 'Invert PWM3',
                    2 => 'Invert PWM3',
                    3 => 'Clock Prescaler',
                    4 => 'Invert All Channels',
                    5 => 'Invert All Channels',
                    6 => 'Invert PWM3',
                    7 => 'Freq Counter 0',
                    8 => 'Freq Counter 1',
                    9 => 'Freq Counter 2',
                ),
            ),
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
                    "Sensor" => array(
                        "get" => array(
                            "storageUnit" => "unknown",
                            "maxDecimals" => 2,
                            "unitType" => "asdf",
                        ),
                    ),
                ),
                array(
                    array(
                        'min' => 0,
                        'max' => 0xFFFF,
                        'label' => 'PWM0',
                        'index' => 0,
                    ),
                    array(
                        'min' => 0,
                        'max' => 0xFFFF,
                        'label' => 'PWM1',
                        'index' => 1,
                    ),
                    array(
                        'min' => 0,
                        'max' => 0xFFFF,
                        'label' => 'PWM2',
                        'index' => 2,
                    ),
                ),
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param array $mocks  The mocks to use
    * @param array $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataChannels
    */
    public function testChannels($mocks, $expect)
    {
        $sensor = new \HUGnet\DummyBase("Sensor");
        $sensor->resetMock($mocks);
        $this->assertSame($expect, $this->o->channels());
    }

}
?>
