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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
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
                    'dev' => 1,
                    'output' => 1,
                    'id' => 2,
                ),
                "1300341278560099FFFFFFFFA5A55A5A01020304",
                array(
                    'extra' => array(
                        10 => -1,
                        11 => 1515890085,
                        12 => 67305985,
                    ),
                    'dev' => 1,
                    'output' => 1,
                    'id' => 2,
                    'driver' => 'ADuCPWM',
                    'tableEntry' => array(
                        "PWM0LEN" => 4660,
                        "PWM1LEN" => 22136,
                        "PWM2LEN" => 39168,
                        "SYNC" => 0,
                        "PWM5INV" => 0,
                        "PWM3INV" => 0,
                        "PWM1INV" => 0,
                        "PWMCP" => 0,
                        "POINV" => 0,
                        "HOFF" => 1,
                        "DIR" =>0 
                    ),
                    'type' => 'ADuCPWM',
                    'params' => array()
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
        $this->output->load($mocks);
        $this->o->decode($string);
        $this->assertEquals($expect, $this->output->toArray(false));
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
                    "extra" => array(
                        0, 0, 0, 0, 0, 1, 0, 0x1234, 0x5678, 0x9900,
                        0xABCD, 0x4321, 0x5A5AA5A5
                    ),
                ),
                "1100341278560099CDAB000021430000A5A55A5A",
            ),
            array( // #1 Strings
                array(
                    "extra" => array(
                        "0", "0", "0", "0", "0", "1", "0",
                        "65535", "65535", "65535", 
                        -1, -1, -1
                    ),
                ),
                "1100FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF",
            ),
            array( // #2
                array(
                    "tableEntry" => array(
                        "PWM5INV" => 0, 
                        "PWM3INV" => 0, 
                        "PWM1INV" => 0, 
                        "PWMCP" => 0, 
                        "POINV" => 0, 
                        "HOFF" => 1, 
                        "DIR" => 0, 
                        "PWM0LEN" => 0x1234, 
                        "PWM1LEN" => 0x5678, 
                        "PWM2LEN" => 0x9900,
                    ),
                    "extra" => array(
                        10 => 0xABCD, 
                        11 => 0x4321, 
                        12 => 0x5A5AA5A5
                    ),
                ),
                "1100341278560099CDAB000021430000A5A55A5A",
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
        $this->output->load($mocks);
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
                    10 => 'Initial Value 0',
                    11 => 'Initial Value 1',
                    12 => 'Initial Value 2',
                    0 => 'Invert PWM5',
                    1 => 'Invert PWM3',
                    2 => 'Invert PWM1',
                    3 => 'Clock Prescaler',
                    4 => 'Invert All Channels',
                    5 => 'High Side Off',
                    6 => 'Direction Control',
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
                    "storageUnit" => "unknown",
                    "maxDecimals" => 2,
                    "unitType" => "asdf",
                    "location" => "Hello",
                ),
                array(
                    array(
                        'min' => -65535,
                        'max' => 0xFFFF,
                        'label' => 'Hello 1',
                        'index' => 0,
                        'port' => 'PWM1',
                    ),
                    array(
                        'min' => -65535,
                        'max' => 0xFFFF,
                        'label' => 'Hello 3',
                        'index' => 1,
                        'port' => 'PWM3',
                    ),
                    array(
                        'min' => -65535,
                        'max' => 0xFFFF,
                        'label' => 'Hello 5',
                        'index' => 2,
                        'port' => 'PWM5',
                    ),
                ),
            ),
        );
    }

}
?>
