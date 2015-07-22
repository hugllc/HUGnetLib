<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2015 Hunt Utilities Group, LLC
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
 * @package    HUGnetLib
 * @subpackage Util
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2015 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;

/**
 * This class controls all error messages and exceptions
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Util
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2015 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.5
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.14.5
 */
class WebAPI2Tester
{
    const REQUIRED = "Required";
    const OPTIONAL = "Optional";
    const PASS = "Pass";
    const FAIL = "Fail";
    const NA = "Not Implemented";
    /** This is the test location */
    private $_api = "";
    /** This is our UI object */
    private $_ui = null;
    /** This is our UI object */
    private $_result = array();
    /** This is our UI object */
    private $_readonly = true;
    /** This is where we store the results */
    private $_test = array();
    /**
    * This returns a WebAPI2Tester object
    *
    * @param string &$ui  The UI to output our findings to
    * @param string $url  The URL to test
    *
    * @return reference to the table class object
    */
    private function __construct(&$ui, $url, $readonly)
    {
        $this->_api      = $url;
        $this->_ui       = &$ui;
        $this->_readonly = $readonly;
    }
    /**
    * This returns a WebAPI2Tester object
    *
    * @param string &$ui The UI to output our findings to
    * @param string $url The URL to test
    *
    * @return reference to the table class object
    */
    public static function &factory(&$ui, $url, $readonly = false) 
    {
        return new WebAPI2Tester($ui, $url, $readonly);
    }
    /**
    * This runs the tests
    *
    * @return true if all tests pass.  False otherwise
    */
    public function execute()
    {
        $this->outputHeader();
        $methods = get_class_methods($this);
        $this->_result = array(self::PASS => 0, self::FAIL => 0, self::NA => 0);
        foreach ($methods as $method) {
            if (strtolower(substr($method, 0, 4)) == "test") {
                $this->_errors = array();
                // This is a test method, so call it.
                $this->$method();
                $this->outputResult();
            }
        }
        $this->outputFooter();
    }
    /**
    * This returns a WebAPI2Tester object
    *
    * @param string $test The test 
    *
    * @return reference to the table class object
    */
    public function outputHeader()
    {
        $this->_ui->out("Running tests against ".$this->_api);
        $this->_ui->out();
    }
    /**
    * This returns a WebAPI2Tester object
    *
    * @param string $test The test 
    *
    * @return reference to the table class object
    */
    public function setupTest($case, $tag, $desc, $level)
    {
        $this->_test = array(
            "case" => $case,
            "tag" => $tag,
            "desc" => $desc,
            "level" => $level,
            "errors" => array(),
            "implemented" => true,
            "result" => true,
        );
    }
    /**
    * This returns a WebAPI2Tester object
    *
    * @param string $test The test 
    *
    * @return reference to the table class object
    */
    protected function outputResult()
    {
        $result = ($this->_test["result"]) ? self::PASS : self::FAIL;
        if ((($this->_test["level"] == self::OPTIONAL) && ($result == self::FAIL))
            || !$this->_test["implemented"]
        ) {
            $result = self::NA;
        }
        $this->_result[$result]++;
        $this->_ui->out($this->_test["case"].": ".$this->_test["desc"]);
        $this->_ui->out($this->_test["tag"]." (".$this->_test["level"].")\t\t".$result);
        foreach ($this->_test["errors"] as $error) {
            $this->_ui->out(" -> ".$error);
        }
        $this->_ui->out();
    }
    /**
    * This returns a WebAPI2Tester object
    *
    * @param string $test The test 
    *
    * @return reference to the table class object
    */
    public function outputFooter()
    {
        $this->_ui->out("All tests done.");
        $this->_ui->out(
            "Passed: ".$this->_result[self::PASS]
            ."  Failed: ".$this->_result[self::FAIL]
            ."  Not Implemented: ".$this->_result[self::NA]
        );
        $this->_ui->out("Result: ".(($this->_result[self::FAIL] == 0) ? "Pass" : "Fail"));
        $this->_ui->out();
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    public function get($url, $data, $timeout=10)
    {
        $url = $this->_api.$url;
        $return = $this->httpMethod(
            "GET", http_build_query($data), $url, $format, $timeout
        );
        return $return;

    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    public function post($url, $data, $timeout=10)
    {
        $return = false;
        $url = $this->_api.$url;
        $return = $this->httpMethod(
            "POST", http_build_query($data), $url, $format, $timeout
        );
        return $return;
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    public function put($url, $data, $timeout=10)
    {
        $return = false;
        $url = $this->_api.$url;
        $return = $this->httpMethod(
            "PUT", json_encode($data), $url, $format, $timeout
        );
        return $return;
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function httpMethod($method, $data, $url, $format, $timeout=10)
    {
        $return = array();
        $params = array(
            'http' => array(
                'method' => $method,
                'content' => $data,
                'timeout' => $timeout,
                'header'=>"Connection: close\r\n",
            )
        );
        if (!empty($format)) {
            $params["http"]["header"] .= "Accept: $format\r\n";
        }
        $this->_postError = array();
        $ctx = stream_context_create($params);
        try {
            $return["response"] = @file_get_contents($url, false, $ctx);
            foreach ((array)$http_response_header as $head) {
                if (substr($head, 0, 4) == "HTTP") {
                    $c = explode(" ", $head);
                    $return["code"] = intval($c[1]);
                }
            }

        } catch (Exception $e) {
            var_dump($e);
            //handle error here
        }
        unset($params);
        unset($ctx);
        return $return;
    }

    /**
    * Checks the return code
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function checkReturnCode($expect, $got)
    {
        $pass = (bool)($expect == $got);
        if (!$pass && ($this->_test["level"] == self::OPTIONAL)) {
            if ($got == 501) {
                $this->_test["implemented"] = false;
                $pass = true;
            }
        }
        if (!$pass) {
            $this->_test["errors"][] = "'$got' does not equal expected return code '$expect'";
            $this->_test["result"] = false;
        }
    }
    /**
    * Checks the return code
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function checkReturn($expect, $got, $message = null)
    {
        if (empty($message)) {
            $message = "'$got' does not equal expected return '$expect'";
        }
        $pass = (bool)($expect == $got);
        if (!$pass) {
            $this->_test["errors"][] = $message;
            $this->_test["result"] = false;
        }
    }
    /**
    * Checks the return code
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function checkTrue($check, $message = null)
    {
        if (empty($message)) {
            $message = "Expression is not true";
        }
        if (!(bool)$check) {
            $this->_test["errors"][] = $message;
            $this->_test["result"] = false;
        }
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT2_0()
    {
        $this->setupTest("2.0", "SW-0002-01", "Return API version", self::REQUIRED);
        $ret = $this->get("/version", array());
        $this->checkReturnCode(200, $ret["code"]);
        $this->checkReturn(
            "1.0", 
            $ret["response"], 
            "Version returned '".$ret["response"]."' must return '1.0'"
        );
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT3_0()
    {
        $this->setupTest("3.0", "SW-0003-01", "Return current UNIX timestamp", self::REQUIRED);
        $ret = $this->get("/time", array());
        $this->checkReturnCode(200, $ret["code"]);
        if (200 == $ret["code"]) {
            $this->checkTrue(
                is_numeric($ret["response"]), "Time check response must be an integer"
            );
        }
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT3_1()
    {
        $this->setupTest("3.1", "SW-0003-02", "Set UNIX timestamp", self::OPTIONAL);
        $time = 946688800;
        $ret = $this->put("/time/".$time, array());
        $this->checkReturnCode(200, $ret["code"]);
        if ($ret["code"] == 200) {
            $this->checkTrue(
                ($ret["response"] - $time) < 5,
                "Time was not set properly '$time' != '".$ret["response"]."'"
            );
        }
    }
}


?>
