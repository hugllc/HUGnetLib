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
require_once CODE_BASE.'devices/outputTable/drivers/ADuCDAC.php';

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
class ADuCDACTest extends DriverTestBase
{
    /** This is the class we are testing */
    protected $class = "ADuCDAC";
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
            "ADuCDAC", $this->output
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
                "1300",
                array(
                    "Output" => array(
                        "get" => array(
                            array('extra'),
                        ),
                        "set" => array(
                            array('extra', array(0, 0, 0, 0, 0, 3)),
                        ),
                    ),
                ),
            ),
            array( // #1
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Output"),
                    )
                ),
                "1001",
                array(
                    "Output" => array(
                        "get" => array(
                            array('extra'),
                        ),
                        "set" => array(
                            array('extra', array(1, 0, 0, 0, 0, 0)),
                        ),
                    ),
                ),
            ),
            array( // #2
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Output"),
                    )
                ),
                "9000",
                array(
                    "Output" => array(
                        "get" => array(
                            array('extra'),
                        ),
                        "set" => array(
                            array('extra', array(0, 1, 0, 0, 0, 0)),
                        ),
                    ),
                ),
            ),
            array( // #3
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Output"),
                    )
                ),
                "5000",
                array(
                    "Output" => array(
                        "get" => array(
                            array('extra'),
                        ),
                        "set" => array(
                            array('extra', array(0, 0, 1, 0, 0, 0)),
                        ),
                    ),
                ),
            ),
            array( // #4
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Output"),
                    )
                ),
                "1800",
                array(
                    "Output" => array(
                        "get" => array(
                            array('extra'),
                        ),
                        "set" => array(
                            array('extra', array(0, 0, 0, 1, 0, 0)),
                        ),
                    ),
                ),
            ),
            array( // #5
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Output"),
                    )
                ),
                "1400",
                array(
                    "Output" => array(
                        "get" => array(
                            array('extra'),
                        ),
                        "set" => array(
                            array('extra', array(0, 0, 0, 0, 1, 0)),
                        ),
                    ),
                ),
            ),
            array( // #6
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Output"),
                    )
                ),
                "1100",
                array(
                    "Output" => array(
                        "get" => array(
                            array('extra'),
                        ),
                        "set" => array(
                            array('extra', array(0, 0, 0, 0, 0, 1)),
                        ),
                    ),
                ),
            ),
            array( // #7
                array(
                    "Device" => array(
                        "sensor" => new \HUGnet\DummyBase("Output"),
                    )
                ),
                "1200",
                array(
                    "Output" => array(
                        "get" => array(
                            array('extra'),
                        ),
                        "set" => array(
                            array('extra', array(0, 0, 0, 0, 0, 2)),
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
                        "getExtra" => array(
                        ),
                    ),
                ),
                "1300",
            ),
            array( // #1
                array(
                    "Output" => array(
                        "get" => array(
                            "extra" => array(
                                1, 0, 0, 0, 0, 0
                            ),
                        ),
                    ),
                ),
                "1001",
            ),
            array( // #2
                array(
                    "Output" => array(
                        "get" => array(
                            "extra" => array(
                                0, 1, 0, 0, 0, 0
                            ),
                        ),
                    ),
                ),
                "9000",
            ),
            array( // #3
                array(
                    "Output" => array(
                        "get" => array(
                            "extra" => array(
                                0, 0, 1, 0, 0, 0
                            ),
                        ),
                    ),
                ),
                "5000",
            ),
            array( // #4
                array(
                    "Output" => array(
                        "get" => array(
                            "extra" => array(
                                0, 0, 0, 1, 0, 0
                            ),
                        ),
                    ),
                ),
                "1800",
            ),
            array( // #5
                array(
                    "Output" => array(
                        "get" => array(
                            "extra" => array(
                                0, 0, 0, 0, 1, 0
                            ),
                        ),
                    ),
                ),
                "1400",
            ),
            array( // #6
                array(
                    "Output" => array(
                        "get" => array(
                            "extra" => array(
                                0, 0, 0, 0, 0, 1
                            ),
                        ),
                    ),
                ),
                "1100",
            ),
            array( // #7
                array(
                    "Output" => array(
                        "get" => array(
                            "extra" => array(
                                0, 0, 0, 0, 0, 2
                            ),
                        ),
                    ),
                ),
                "1200",
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

}
?>
