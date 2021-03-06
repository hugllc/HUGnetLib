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
/** This is our namespace */
namespace HUGnet;

/** This is a required class */
require_once CODE_BASE.'system/System.php';
/** This is a required class */
require_once CODE_BASE.'system/Error.php';
/** This is the dummy table container */
require_once TEST_CONFIG_BASE.'stubs/DummyNetwork.php';
/** This is the dummy base */
require_once TEST_CONFIG_BASE.'stubs/DummyBase.php';

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
class SystemTest extends \PHPUnit_Framework_TestCase
{
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
        unset($this->o);
    }
    /**
    * This tests the now function
    *
    * @return null
    */
    public function testNow()
    {
        $time = time();
        $config = array();
        $obj = System::factory($config);
        $this->assertGreaterThanOrEqual($time, $obj->now());
    }
    /**
    * Data provider for testFactory
    *
    * @return array
    */
    public static function dataFactory()
    {
        return array(
            array(
                array("verbose" => 12),
                array("verbose" => 12, "min_log" => Error::ERROR)
            ),
            array(
                array(),
                array("verbose" => 0, "min_log" => Error::ERROR)
            ),
        );
    }
    /**
    * test
    *
    * @param array $config The configuration to use
    * @param array $expect Associative array of properties to check
    *
    * @return null
    *
    * @dataProvider dataFactory
    */
    public function testFactory($config, $expect)
    {
        $obj = System::factory($config);
        // Make sure we have the right object
        $this->assertTrue(get_class($obj) === "HUGnet\System");
        // Make sure the configuration is set.
        $this->assertAttributeSame($expect, "_config", $obj);
    }
    /**
    * Data provider for testThrowException
    *
    * @return array
    */
    public static function dataSystemMissing()
    {
        return array(
            array("Test Message", false, null),
            array("Test Message", true, "InvalidArgumentException"),
            array("Test Message", true, "InvalidArgumentException"),
        );
    }
    /**
    * This tests the object creation
    *
    * @param string $msg       The message
    * @param bool   $condition If true the exception is thrown.  On false it
    *                          is ignored.
    * @param array  $expect    The table to expect
    *
    * @return null
    *
    * @dataProvider dataSystemMissing
    */
    public function testSystemMissing($msg, $condition, $expect)
    {
        if (is_string($expect)) {
            $this->setExpectedException($expect, $msg);
        }
        System::systemMissing($msg, $condition);
        $this->assertTrue(!is_string($expect));
    }
    /**
    * Data provider for testThrowException
    *
    * @return array
    */
    public static function dataLogError()
    {
        return array(
            array(
                array(),
                "Test Message",
                Error::WARNING,
                false,
                false,
                array(
                ),
            ),
            array(
                array(
                    "Error" => array(
                        "log" => true,
                        "get" => array(
                            "group" => "default",
                        ),
                    ),
                ),
                "Test Message",
                Error::ERROR,
                true,
                true,
                array(
                    "syslog" => array(
                        array(
                            "Test Message", Error::ERROR,
                        ),
                    ),
                    "log" => array(
                        array(
                            0 => -1,
                            1 => 'Test Message',
                            2 => Error::ERROR,
                            3 => 'testLogError',
                            4 => 'HUGnet\SystemTest',
                        ),
                    ),
                ),
            ),
            array(
                array(
                    "Error" => array(
                        "log" => false,
                        "get" => array(
                            "group" => "default",
                        ),
                    ),
                ),
                "Test Message",
                Error::CRITICAL,
                true,
                false,
                array(
                    "syslog" => array(
                        array(
                            "Test Message", Error::CRITICAL,
                        ),
                    ),
                    "log" => array(
                        array(
                            0 => -1,
                            1 => 'Test Message',
                            2 => Error::CRITICAL,
                            3 => 'testLogError',
                            4 => 'HUGnet\SystemTest',
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $mocks     The mocks to use
    * @param string $msg       The message
    * @param int    $severity  The severity to send the function
    * @param bool   $condition If true the exception is thrown.  On false it
    *                          is ignored.
    * @param string $return    The expected return
    * @param array  $expect    The table to expect
    *
    * @return null
    *
    * @dataProvider dataLogError
    */
    public function testLogError(
        $mocks, $msg, $severity, $condition, $return, $expect
    ) {
        $error = new DummyBase("Error");
        $error->resetMock($mocks);
        $obj = System::factory(array(), null, $error);
        $ret = $obj->logError($msg, $severity, $condition);
        $this->assertSame($return, $ret, "Return Wrong");
        $this->assertEquals($expect, $error->retrieve("Error"), "Calls Wrong");
    }
    /**
    * Data provider for testThrowException
    *
    * @return array
    */
    public static function dataFatalError()
    {
        return array(
            array(
                array(),
                "Test Message",
                Error::WARNING,
                false,
                false,
                array(
                ),
            ),
            array(
                array(
                    "Error" => array(
                        "log" => true,
                        "get" => array(
                            "group" => "default",
                        ),
                    ),
                ),
                "Test Message",
                Error::ERROR,
                true,
                true,
                array(
                    "syslog" => array(
                        array(
                            "Test Message", Error::CRITICAL,
                        ),
                    ),
                    "log" => array(
                        array(
                            0 => -1,
                            1 => 'Test Message',
                            2 => Error::CRITICAL,
                            3 => 'testFatalError',
                            4 => 'HUGnet\SystemTest',
                        ),
                    ),
                    "exception" => array(
                        array(
                            "Test Message", "Runtime"
                        ),
                    ),
                ),
            ),
            array(
                array(
                    "Error" => array(
                        "log" => true,
                        "get" => array(
                            "group" => "default",
                        ),
                    ),
                ),
                "Test Message",
                Error::CRITICAL,
                true,
                false,
                array(
                    "syslog" => array(
                        array(
                            "Test Message", Error::CRITICAL,
                        ),
                    ),
                    "log" => array(
                        array(
                            0 => -1,
                            1 => 'Test Message',
                            2 => Error::CRITICAL,
                            3 => 'testFatalError',
                            4 => 'HUGnet\SystemTest',
                        ),
                    ),
                    "exception" => array(
                        array(
                            "Test Message", "Runtime"
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $mocks     The mocks to use
    * @param string $msg       The message
    * @param int    $severity  The severity to send the function
    * @param bool   $condition If true the exception is thrown.  On false it
    *                          is ignored.
    * @param string $return    The expected return
    * @param array  $expect    The table to expect
    *
    * @return null
    *
    * @dataProvider dataFatalError
    */
    public function testFatalError(
        $mocks, $msg, $severity, $condition, $return, $expect
    ) {
        $error = new DummyBase("Error");
        $error->resetMock($mocks);
        $obj = System::factory(array(), null, $error);
        $ret = $obj->fatalError($msg, $condition);
        $this->assertEquals($expect, $error->retrieve("Error"), "Calls Wrong");
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataGet()
    {
        $ret = array(
            array(
                array(
                    "hello" => "there",
                    "asdf" => array(1,2),
                ),
                "hello",
                "there",
            ),
            array(
                array(
                ),
                "phpversion",
                PHP_VERSION,
            ),
            array(
                array(
                    "hello" => "there",
                    "asdf" => array(1, 2),
                ),
                "asdf",
                array(1, 2),
            ),
            array(
                array(
                    "hello" => "there",
                    "asdf" => array(1,2),
                ),
                "badIdea",
                null,
            ),
            array( // A normal file argument
                array(
                    "hello" => "there",
                    "file" => '/var/lib/hugnet/config.ini',
                ),
                "confdir",
                '/var/lib/hugnet',
            ),
            array(  // No file argument
                array(
                    "hello" => "there",
                ),
                "confdir",
                '/etc/hugnet',
            ),
            array(
                array(),
                "version",
                trim(file_get_contents(CODE_BASE."VERSION.TXT")),
            ),
        );
        if (function_exists("posix_uname")) {
            $uname = posix_uname();
            foreach ($uname as $key => $value) {
                if ($key === "version") {
                    $key = "osversion";
                }
                $ret[] = array(
                    array(), $key, $value
                );
            }
        }

        return $ret;
    }
    /**
    * This tests the object creation
    *
    * @param array  $config The configuration to use
    * @param string $field  The field to get
    * @param mixed  $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataGet
    */
    public function testGet(
        $config, $field, $expect
    ) {
        $obj = \HUGnet\System::factory($config);
        $this->assertSame($expect, $obj->get($field));
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataConfig()
    {
        $default = array(
            "verbose" => 0,
            "min_log" => Error::ERROR
        );
        return array(
            array(
                array(
                    "hello" => "there",
                    "asdf" => array(1,2),
                ),
                array_merge(
                    $default,
                    array(
                        "hello" => "there",
                        "asdf" => array(1,2),
                    )
                ),
            ),
            array(
                array(
                ),
                $default,
            ),
            array(
                "ThisIsNotAnArray",
                $default,
            ),
            array(
                null,
                $default,
            ),
            array(
                new \stdClass(),
                $default,
            ),
            array(
                3.141592654,
                $default,
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataConfig
    */
    public function testConfig(
        $config, $expect
    ) {
        $obj = \HUGnet\System::factory();
        $this->assertEquals($expect, $obj->config($config));
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataRuntime()
    {
        return array(
            array(
                array(
                    "hello" => "there",
                    "asdf" => array(1,2),
                ),
                array(
                    "hello" => "there",
                    "asdf" => array(1,2),
                )
            ),
            array(
                array(
                ),
                array(
                ),
            ),
            array(
                "ThisIsNotAnArray",
                array(),
            ),
            array(
                null,
                array(),
            ),
            array(
                new \stdClass(),
                array(),
            ),
            array(
                3.141592654,
                array(),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataRuntime
    */
    public function testRuntime(
        $config, $expect
    ) {
        $obj = \HUGnet\System::factory();
        $this->assertEquals($expect, $obj->runtime($config));
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataNetwork()
    {
        return array(
            array(
                array(
                    "hello" => "there",
                    "asdf" => array(1,2),
                    "network" => array(
                        "default" => array(
                            "driver" => "SocketNull",
                        ),
                    ),
                ),
                null,
                "HUGnet\\network\\Application"
            ),
            array(
                array(
                    "hello" => "there",
                    "asdf" => array(1,2),
                    "network" => array(),
                ),
                null,
                "HUGnet\\network\\Dummy"
            ),
            array(
                array(
                    "hello" => "there",
                    "asdf" => array(1,2),
                ),
                new \HUGnet\network\DummyNetwork("Application"),
                "HUGnet\\network\\DummyNetwork"
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config      The configuration to use
    * @param mixed $application The network application to use
    * @param mixed $expect      The value we expect back
    *
    * @return null
    *
    * @dataProvider dataNetwork
    */
    public function testNetwork(
        $config, $application, $expect
    ) {
        $obj = \HUGnet\System::factory($config);
        $net = $obj->network($application);
        $this->assertSame($expect, get_class($net), "wrong class");
        $this->assertSame($net, $obj->network(), "Subsequent returns not identical");
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataDevice()
    {
        return array(
            array(
                array(
                    "hello" => "there",
                    "asdf"  => array(1,2),
                ),
                array(
                    "id"        => 5,
                    "DeviceID"  => "000005",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-38-01-C",
                    "FWVersion" => "1.2.3",
                ),
                array(
                    "id"        => 5,
                    "DeviceID"  => "000005",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-38-01-C",
                    "FWVersion" => "1.2.3",
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $device The network application to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataDevice
    */
    public function testDevice(
        $config, $device, $expect
    ) {
        $obj = \HUGnet\System::factory($config);
        $dev = $obj->device($device);
        $this->assertSame("HUGnet\Device", get_class($dev), "wrong class");
        foreach ($expect as $key => $value) {
            $this->assertEquals($value, $dev->get($key), "$key not $value");
        }
        unset($obj);
    }
    /**
    * Data provider for testAnnotation
    *
    * @return array
    */
    public static function dataAnnotation()
    {
        return array(
            array(
                array(
                    "hello" => "there",
                    "asdf"  => array(1,2),
                ),
                array(
                    "id"     => 5,
                ),
                array(
                    "id"     => 5,
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $device The network application to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataAnnotation
    */
    public function testAnnotation(
        $config, $device, $expect
    ) {
        $obj = \HUGnet\System::factory($config);
        $dev = $obj->annotation($device);
        $this->assertSame("HUGnet\Annotation", get_class($dev), "wrong class");
        foreach ($expect as $key => $value) {
            $this->assertEquals($value, $dev->get($key), "$key not $value");
        }
        unset($obj);
    }
    /**
    * Data provider for testTest
    *
    * @return array
    */
    public static function dataTest()
    {
        return array(
            array(
                array(
                    "hello" => "there",
                    "asdf"  => array(1,2),
                ),
                array(
                    "id"        => 5,
                ),
                array(
                    "id"        => 5,
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $device The network application to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataTest
    */
    public function testTest(
        $config, $device, $expect
    ) {
        $obj = \HUGnet\System::factory($config);
        $dev = $obj->test($device);
        $this->assertSame("HUGnet\Test", get_class($dev), "wrong class");
        foreach ($expect as $key => $value) {
            $this->assertEquals($value, $dev->get($key), "$key not $value");
        }
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataTable()
    {
        return array(
            array(
                array(
                    "hello" => "there",
                    "asdf"  => array(1,2),
                ),
                "Devices",
                array(
                    "id"        => 5,
                    "DeviceID"  => "000005",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-38-01-C",
                    "FWVersion" => "1.2.3",
                ),
                "HUGnet\\db\\tables\\Devices",
                array(
                    "id"        => 5,
                    "DeviceID"  => "000005",
                    "HWPartNum" => "0039-28-01-A",
                    "FWPartNum" => "0039-38-01-C",
                    "FWVersion" => "1.2.3",
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array  $config The configuration to use
    * @param string $table  The table to get
    * @param mixed  $data   The data to feed the table
    * @param string $class  The class to expect
    * @param mixed  $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataTable
    */
    public function testTable(
        $config, $table, $data, $class, $expect
    ) {
        $obj = \HUGnet\System::factory($config);
        $dev = $obj->table($table, $data);
        $this->assertSame($class, get_class($dev), "wrong class");
        foreach ($expect as $key => $value) {
            $this->assertEquals($value, $dev->get($key), "$key not $value");
        }
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataDataCollector()
    {
        return array(
            array(
                array(
                    "hello" => "there",
                    "asdf"  => array(1,2),
                ),
                array(
                    "uuid"   => "310354f4-0092-4b46-a198-92dadd42efb1",
                    "name"   => "What is in a name",
                    "Config" => "This is a config",
                ),
                array(
                    "uuid"   => "310354f4-0092-4b46-a198-92dadd42efb1",
                    "name"   => "What is in a name",
                    "Config" => "This is a config",
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $device The network application to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataDataCollector
    */
    public function testDataCollector(
        $config, $device, $expect
    ) {
        $obj = \HUGnet\System::factory($config);
        $dev = $obj->datacollector($device);
        $this->assertSame("HUGnet\DataCollector", get_class($dev), "wrong class");
        foreach ($expect as $key => $value) {
            $this->assertEquals($value, $dev->get($key), "$key not $value");
        }
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataGateway()
    {
        return array(
            array(
                array(
                    "hello" => "there",
                    "asdf"  => array(1,2),
                ),
                array(
                    "id"          => 1,
                    "name"        => "What is in a name",
                    "description" => "This is a config",
                ),
                array(
                    "id"          => 1,
                    "name"        => "What is in a name",
                    "description" => "This is a config",
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $device The network application to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataGateway
    */
    public function testGateway(
        $config, $device, $expect
    ) {
        $obj = \HUGnet\System::factory($config);
        $dev = $obj->gateway($device);
        $this->assertSame("HUGnet\Gateway", get_class($dev), "wrong class");
        foreach ($expect as $key => $value) {
            $this->assertEquals($value, $dev->get($key), "$key not $value");
        }
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataImage()
    {
        return array(
            array(
                array(
                    "hello" => "there",
                    "asdf"  => array(1,2),
                ),
                array(
                    "id"   => 1,
                    "name" => "What is in a name",
                    "desc" => "This is a config",
                ),
                array(
                    "id"   => 1,
                    "name" => "What is in a name",
                    "desc" => "This is a config",
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $device The network application to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataImage
    */
    public function testImage(
        $config, $device, $expect
    ) {
        $obj = \HUGnet\System::factory($config);
        $dev = $obj->image($device);
        $this->assertSame("HUGnet\Image", get_class($dev), "wrong class");
        foreach ($expect as $key => $value) {
            $this->assertEquals($value, $dev->get($key), "$key not $value");
        }
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataInputTable()
    {
        return array(
            array(
                array(
                    "hello" => "there",
                    "asdf"  => array(1,2),
                ),
                array(
                    "uuid"   => "310354f4-0092-4b46-a198-92dadd42efb1",
                    "name"   => "What is in a name",
                    "Config" => "This is a config",
                ),
                array(
                    "uuid"   => "310354f4-0092-4b46-a198-92dadd42efb1",
                    "name"   => "What is in a name",
                    "Config" => "This is a config",
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $device The network application to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataInputTable
    */
    public function testInputTable(
        $config, $device, $expect
    ) {
        $obj = \HUGnet\System::factory($config);
        $dev = $obj->inputTable($device);
        $this->assertInstanceOf("\HUGnet\InputTable", $dev, "wrong class");
        foreach ($expect as $key => $value) {
            $this->assertEquals($value, $dev->get($key), "$key not $value");
        }
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataOutputTable()
    {
        return array(
            array(
                array(
                    "hello" => "there",
                    "asdf"  => array(1,2),
                ),
                array(
                    "uuid"   => "310354f4-0092-4b46-a198-92dadd42efb1",
                    "name"   => "What is in a name",
                    "Config" => "This is a config",
                ),
                array(
                    "uuid"   => "310354f4-0092-4b46-a198-92dadd42efb1",
                    "name"   => "What is in a name",
                    "Config" => "This is a config",
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $device The network application to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataOutputTable
    */
    public function testOutputTable(
        $config, $device, $expect
    ) {
        $obj = \HUGnet\System::factory($config);
        $dev = $obj->outputTable($device);
        $this->assertInstanceOf("\HUGnet\OutputTable", $dev, "wrong class");
        foreach ($expect as $key => $value) {
            $this->assertEquals($value, $dev->get($key), "$key not $value");
        }
        unset($obj);
    }
    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public static function dataProcessTable()
    {
        return array(
            array(
                array(
                    "hello" => "there",
                    "asdf"  => array(1,2),
                ),
                array(
                    "uuid"   => "310354f4-0092-4b46-a198-92dadd42efb1",
                    "name"   => "What is in a name",
                    "Config" => "This is a config",
                ),
                array(
                    "uuid"   => "310354f4-0092-4b46-a198-92dadd42efb1",
                    "name"   => "What is in a name",
                    "Config" => "This is a config",
                ),
            ),
        );
    }
    /**
    * This tests the object creation
    *
    * @param array $config The configuration to use
    * @param mixed $device The network application to use
    * @param mixed $expect The value we expect back
    *
    * @return null
    *
    * @dataProvider dataProcessTable
    */
    public function testProcessTable(
        $config, $device, $expect
    ) {
        $obj = \HUGnet\System::factory($config);
        $dev = $obj->processTable($device);
        $this->assertInstanceOf("\HUGnet\ProcessTable", $dev, "wrong class");
        foreach ($expect as $key => $value) {
            $this->assertEquals($value, $dev->get($key), "$key not $value");
        }
        unset($obj);
    }

}

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
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class SystemTestStub extends System
{
}
?>
