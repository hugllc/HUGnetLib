<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @subpackage SuiteCLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\processes;
/** This is a required class */
require_once CODE_BASE.'processes/Watchdog.php';
/** This is a required class */
require_once CODE_BASE.'system/Error.php';
/** This is a required class */
require_once CODE_BASE.'ui/Args.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummySystem.php';
/** This is a required class */
require_once TEST_CONFIG_BASE.'stubs/DummyNetwork.php';
/**
 * Test class for HUGnetDB.
 * Generated by PHPUnit on 2007-12-13 at 10:28:11.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteCLI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class WatchdogTest extends \PHPUnit_Framework_TestCase
{
    /** Files that have been created */
    private $_files = array();
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
        foreach ($this->_files as $file) {
            unlink($file);
        }
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataSystem()
    {
        $data = array();
        $htmlargs = \HUGnet\ui\Args::factory($argv, $argc, $data);
        return array(
            array(
                new \HUGnet\DummySystem("System"),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 5,
                            "other" => "stuff",
                        ),
                        "network" => new \HUGnet\network\DummyNetwork("Network"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Network" => array(
                        "device" => new \HUGnet\DummyBase("NetDevice"),
                    ),
                ),
                array(
                    "verbose" => 5,
                    "other" => "stuff",
                ),
            ),
            array(
                $htmlargs,
                null,
                array(
                    "verbose" => 1,
                    "html" => false,
                    "file" => "",
                    "quiet" => false,
                    "debug" => false,
                    "test" => false,
                    "min_log" => \HUGnet\Error::ERROR,
                )
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param mixed $config The config to use
    * @param array $mock   The mocks to use
    * @param mixed $expect The system object we are expecting
    *
    * @return null
    *
    * @dataProvider dataSystem()
    */
    public function testSystem($config, $mock, $expect)
    {
        if (is_array($mock)) {
            $config->resetMock($mock);
        }
        ob_start();
        $obj = Watchdog::factory($config);
        $conf = $obj->system()->config();
        ob_end_clean();
        if (!is_array($mock)) {
            $this->assertTrue(isset($conf["IPAddr"]), "IP Address missing");
        }
        unset($conf["IPAddr"]);
        $this->assertEquals($expect, $conf, "Config wrong");
    }
    /**
    * Data provider for testRemove
    *
    * @return array
    */
    public static function dataCriticalError()
    {
        return array(
            array(
                new \HUGnet\DummySystem("System"),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 5,
                            "other" => "stuff",
                        ),
                        "network" => new \HUGnet\network\DummyNetwork("Network"),
                        "device" => new \HUGnet\DummyBase("Device"),
                    ),
                    "Network" => array(
                        "device" => new \HUGnet\DummyBase("NetDevice"),
                    ),
                ),
                true,
                array(
                ),
                true,
                array(
                ),
            ),
            array(
                new \HUGnet\DummySystem("System"),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 5,
                            "other" => "stuff",
                        ),
                        "network" => new \HUGnet\network\DummyNetwork("Network"),
                        "device" => new \HUGnet\DummyBase("Device"),
                        "get" => array(
                            "fqdn" => "hugllc.com",
                            "nodename" => "asdf",
                            "phpversion" => "1.2.3",
                            "error_email" => "asdf@asdf.com",
                        ),
                    ),
                    "Network" => array(
                        "device" => new \HUGnet\DummyBase("NetDevice"),
                    ),
                ),
                true,
                array(
                    "Ouch!  An error occurred!",
                ),
                true,
                array(
                    'to' => "asdf@asdf.com",
                    'subject' => 'Critical Error on asdf',
                    'message' => "The following new errors have occurred:\n\n\n"
                        ." * Ouch!  An error occurred!\n",
                    'headers' => 'MIME-Version: 1.0
Content-type: text/plain; charset=UTF-8
From: HUGnet Admin <admin@hugllc.com>
X-Mailer: PHP/1.2.3',
                    'params' => ''
                ),
            ),
            array(
                new \HUGnet\DummySystem("System"),
                array(
                    "System" => array(
                        "config" => array(
                            "verbose" => 5,
                            "other" => "stuff",
                        ),
                        "network" => new \HUGnet\network\DummyNetwork("Network"),
                        "device" => new \HUGnet\DummyBase("Device"),
                        "get" => array(
                            "fqdn" => "hugllc.com",
                            "nodename" => "asdf",
                            "phpversion" => "1.2.3",
                            "error_email" => array("hello"),
                        ),
                    ),
                    "Network" => array(
                        "device" => new \HUGnet\DummyBase("NetDevice"),
                    ),
                ),
                true,
                array(
                    "Ouch!  An error occurred!",
                ),
                false,
                array(
                ),
            ),
        );
    }
    /**
    * Tests the iteration and preload functions
    *
    * @param mixed $config The config to use
    * @param array $mock   The mocks to use
    * @param bool  $ret    The return to set
    * @param array $errors The errors to send out
    * @param mixed $expect The return we are expecting
    * @param array $email  The email that gets sent out
    *
    * @return null
    *
    * @dataProvider dataCriticalError()
    */
    public function testCriticalError(
        $config, $mock, $ret, $errors, $expect, $email
    ) {
        if (is_array($mock)) {
            $config->resetMock($mock);
        }
        $obj = WatchdogTestClass::factory($config);
        $obj->mailRet = $ret;
        foreach ((array)$errors as $key => $error) {
            $obj->criticalError($key, $error);
        }
        $ret = $obj->criticalErrorMail();
        $this->assertSame($expect, $ret, "Return Wrong!");
        if ($ret) {
            $this->assertEquals($email, $obj->mailStuff, "Email Wrong");
        }
    }

}
/**
 * This code makes sure all of the other HUGnet stuff is running.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage UI
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class WatchdogTestClass extends Watchdog
{
    /** This is to store mail information */
    public $mailStuff = array();
    /** This is to store mail information */
    public $mailRet = true;
    /**
    * Creates the object
    *
    * @param array &$config The configuration to use
    *
    * @return null
    */
    static public function &factory(&$config = array())
    {
        $obj = new WatchdogTestClass($config);
        return $obj;
    }
    /**
    * This sends out a critical error email
    *
    * This creates our email
    *
    * @return true on success, false on failure, null on not time yet.
    */
    public function criticalErrorMail()
    {
        return parent::criticalErrorMail();
    }
    /**
    * Wrapper to send out an email
    *
    * This wrapper is just for testing purposes, so I can isolate the call to 'mail'
    *
    * @param string $to      The address to send the message to
    * @param string $subject The subject of the message
    * @param string $message The actual message
    * @param string $headers THe extra headers to send
    * @param string $params  Additional parameters to send
    *
    * @return mixed Array in test mode, bool in normal mode
    * @codeCoverageIgnoreStart
    * Can't test this call
    */
    protected function mail($to, $subject, $message, $headers, $params)
    {
        $this->mailStuff = array(
            "to" => $to,
            "subject" => $subject,
            "message" => $message,
            "headers" => $headers,
            "params" => $params,
        );
        return $this->mailRet;
    }
}
?>
