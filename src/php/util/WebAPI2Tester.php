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
 * @version    Release: 0.14.8
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
            "GET", http_build_query($data), $url, "text/html", $format, $timeout
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
            "POST", 
            http_build_query($data), 
            $url, 
            "application/x-www-form-urlencoded", 
            $format, 
            $timeout
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
    public function delete($url, $data, $timeout=10)
    {
        $return = false;
        $url = $this->_api.$url;
        $return = $this->httpMethod(
            "DELETE", json_encode($data), $url, "application/json", $format, $timeout
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
            "PUT", json_encode($data), $url, "application/json", $format, $timeout
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
    protected function httpMethod($method, $data, $url, $type, $format, $timeout=10)
    {
        $return = array();
        $params = array(
            'http' => array(
                'method' => $method,
                'content' => $data,
                'timeout' => $timeout,
                'header'=>"Connection: close\r\nContent-Type: $type\r\n",
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
    protected function testDVT1_1()
    {
        $this->setupTest("1.1", "SW-0001-02", "Case shall not matter", self::REQUIRED);
        $ret = $this->get("/VeRsIoN", array());
        $this->checkReturnCode(200, $ret["code"]);
        $ret = $this->get("/VERSION", array());
        $this->checkReturnCode(200, $ret["code"]);
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
        $time = time();
        $ret = $this->put("/time/".$time, array());
        $this->checkReturnCode(200, $ret["code"]);
        if ($ret["code"] == 200) {
            $this->checkTrue(
                ($ret["response"] - $time) < 5,
                "Time was not set properly '$time' != '".$ret["response"]."'"
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
    protected function testDVT4_0()
    {
        $this->setupTest("4.0", "SW-0004-01", "Return the device list", self::REQUIRED);
        $ret = $this->get("/device", array());
        $this->checkReturnCode(200, $ret["code"]);
        $data = json_decode((string)$ret["response"], true);
        $this->checkTrue(
            is_array($data), "Device list must be an array"
        );
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT4_1()
    {
        $ret = $this->delete("/device/1", array());
        $this->setupTest("4.1", "SW-0004-02", "Create a new device", self::REQUIRED);
        $ret = $this->post(
            "/device", 
            array(
                "id" => 1, 
                "DeviceName" => "hello", 
                "HWPartNum" => "0039-26-08-P",
                "Driver" => "E00392608"
            )
        );
        $this->checkReturnCode(200, $ret["code"]);
        $data = json_decode((string)$ret["response"], true);
        $this->checkTrue(
            is_array($data), "Device creation return must be an array"
        );
        $this->checkReturn("hello", $data["DeviceName"]);
        $this->checkReturn(1, $data["id"]);
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT4_2()
    {
        $this->setupTest("4.2", "SW-0004-03", "Retrieve a device", self::REQUIRED);
        $ret = $this->get("/device/1", array());
        $this->checkReturnCode(200, $ret["code"]);
        $data = json_decode((string)$ret["response"], true);
        $this->checkTrue(
            is_array($data), "Device creation return must be an array"
        );
        $this->checkReturn("hello", $data["DeviceName"]);
        $this->checkReturn(1, $data["id"]);
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT4_3()
    {
        $this->setupTest("4.3", "SW-0004-04", "Get return 404 if the device doesn't exist", self::REQUIRED);
        $ret = $this->delete("/device/2", array());
        $ret = $this->get("/device/2", array());
        $this->checkReturnCode(404, $ret["code"]);
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT4_4()
    {
        $this->setupTest("4.4", "SW-0004-05", "Update a device", self::REQUIRED);
        $ret = $this->put("/device/1", array("DeviceName" => "world"));
        $this->checkReturnCode(202, $ret["code"]);
        $data = json_decode((string)$ret["response"], true);
        $this->checkTrue(
            is_array($data), "Device creation return must be an array"
        );
 
        if (is_array($data)) {
            $this->checkReturn("world", $data["DeviceName"]);
            $this->checkReturn(1, $data["id"], "The id returned is not 1");
        }
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT4_5()
    {
        $this->setupTest("4.5", "SW-0004-06", "Update return 404 if the device doesn't exist", self::REQUIRED);
        $ret = $this->put("/device/2", array("DeviceName" => "world"));
        $this->checkReturnCode(404, $ret["code"]);
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT4_6()
    {
        $this->setupTest("4.6", "SW-0004-07", "Delete a device", self::REQUIRED);
        $ret = $this->delete("/device/1", array());
        $this->checkReturnCode(202, $ret["code"]);
        $this->checkTrue(
            $ret["response"] == 1, "The return is not '1'"
        );
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT4_7()
    {
        $this->setupTest("4.7", "SW-0004-08", "Delete return 404 if the device doesn't exist", self::REQUIRED);
        $ret = $this->delete("/device/2", array());
        $this->checkReturnCode(404, $ret["code"]);
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function devIOPListTest($type)
    {
        $this->delete("/device/1", array());
        $this->post(
            "/device", 
            array(
                "id" => 1, 
                "DeviceName" => "hello", 
                "HWPartNum" => "0039-26-08-P",
                "Driver" => "E00392608"
            )
        );
        $ret = $this->get("/device/1/$type", array());
        $this->checkReturnCode(200, $ret["code"]);
        $data = json_decode((string)$ret["response"], true);
        $this->checkTrue(
            is_array($data), "Device $type list return must be an array"
        );
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function devIOPGetTest($type)
    {
        $ret = $this->get("/device/1/$type/0", array());
        $this->checkReturnCode(200, $ret["code"]);
        $data = json_decode((string)$ret["response"], true);
        $this->checkTrue(
            is_array($data), "Device $type get return must be an array"
        );
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function devIOPPutTest($type)
    {
        $ret = $this->put("/device/1/$type/0", array("location" => "hello"));
        $this->checkReturnCode(202, $ret["code"]);
        $data = json_decode((string)$ret["response"], true);
        $this->checkTrue(
            is_array($data), "Device creation return must be an array"
        );

        if (is_array($data)) {
            $this->checkReturn(1, $data["dev"]);
            $this->checkReturn(0, $data[$type]);
            $this->checkReturn("hello", $data["location"]);
        }
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function devIOPDeleteTest($type)
    {
        $ret = $this->delete("/device/1/$type/0", array());
        $this->checkReturnCode(202, $ret["code"]);
        $this->checkTrue(
            $ret["response"] == 1, "The return is not '1'"
        );
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function devIOP404Test($type, $fct)
    {
        $ret = $this->$fct("/device/1/$type/2", array());
        $this->checkReturnCode(404, $ret["code"]);
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT5_0()
    {
        $this->setupTest("5.0", "SW-0005-01", "Return the device input list", self::REQUIRED);
        $this->devIOPListTest("input");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT5_2()
    {
        $this->setupTest("5.2", "SW-0005-03", "Retrieve a device input", self::REQUIRED);
        $this->devIOPGetTest("input");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT5_3()
    {
        $this->setupTest("5.3", "SW-0005-04", "Get return 404 if the device input doesn't exist", self::REQUIRED);
        $this->devIOP404Test("input", "get");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT5_4()
    {
        $this->setupTest("5.4", "SW-0005-05", "Update a device input", self::REQUIRED);
        $this->devIOPPutTest("input");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT5_5()
    {
        $this->setupTest("5.5", "SW-0005-06", "Update return 404 if the device input doesn't exist", self::REQUIRED);
        $this->devIOP404Test("input", "put");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT5_6()
    {
        $this->setupTest("5.6", "SW-0005-07", "Delete a device input", self::REQUIRED);
        $this->devIOPDeleteTest("input");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT5_7()
    {
        $this->setupTest("5.7", "SW-0005-08", "Delete return 404 if the device input doesn't exist", self::REQUIRED);
        $this->devIOP404Test("input", "delete");
    }

    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT6_0()
    {
        $this->setupTest("6.0", "SW-0006-01", "Return the device output list", self::REQUIRED);
        $this->devIOPListTest("output");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT6_2()
    {
        $this->setupTest("6.2", "SW-0006-03", "Retrieve a device output", self::REQUIRED);
        $this->devIOPGetTest("output");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT6_3()
    {
        $this->setupTest("6.3", "SW-0006-04", "Get return 404 if the device output doesn't exist", self::REQUIRED);
        $this->devIOP404Test("output", "get");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT6_4()
    {
        $this->setupTest("6.4", "SW-0006-05", "Update a device output", self::REQUIRED);
        $this->devIOPPutTest("output");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT6_5()
    {
        $this->setupTest("6.5", "SW-0006-06", "Update return 404 if the device output doesn't exist", self::REQUIRED);
        $this->devIOP404Test("output", "put");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT6_6()
    {
        $this->setupTest("6.6", "SW-0006-07", "Delete a device output", self::REQUIRED);
        $this->devIOPDeleteTest("output");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT6_7()
    {
        $this->setupTest("6.7", "SW-0006-08", "Delete return 404 if the device output doesn't exist", self::REQUIRED);
        $this->devIOP404Test("output", "delete");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT7_0()
    {
        $this->setupTest("7.0", "SW-0007-01", "Return the device process list", self::REQUIRED);
        $this->devIOPListTest("process");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT7_2()
    {
        $this->setupTest("7.2", "SW-0007-03", "Retrieve a device process", self::REQUIRED);
        $this->devIOPGetTest("process");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT7_3()
    {
        $this->setupTest("7.3", "SW-0007-04", "Get return 404 if the device process doesn't exist", self::REQUIRED);
        $this->devIOP404Test("process", "get");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT7_4()
    {
        $this->setupTest("7.4", "SW-0007-05", "Update a device process", self::REQUIRED);
        $this->devIOPPutTest("process");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT7_5()
    {
        $this->setupTest("7.5", "SW-0007-06", "Update return 404 if the device process doesn't exist", self::REQUIRED);
        $this->devIOP404Test("process", "put");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT7_6()
    {
        $this->setupTest("7.6", "SW-0007-07", "Delete a device process", self::REQUIRED);
        $this->devIOPDeleteTest("process");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT7_7()
    {
        $this->setupTest("7.7", "SW-0007-08", "Delete return 404 if the device process doesn't exist", self::REQUIRED);
        $this->devIOP404Test("process", "delete");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT8_0()
    {
        $this->setupTest("8.0", "SW-0008-01", "Return the device power list", self::REQUIRED);
        $this->devIOPListTest("power");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT8_2()
    {
        $this->setupTest("8.2", "SW-0008-03", "Retrieve a device power", self::REQUIRED);
        $this->devIOPGetTest("power");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT8_3()
    {
        $this->setupTest("8.3", "SW-0008-04", "Get return 404 if the device power doesn't exist", self::REQUIRED);
        $this->devIOP404Test("power", "get");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT8_4()
    {
        $this->setupTest("8.4", "SW-0008-05", "Update a device power", self::REQUIRED);
        $this->devIOPPutTest("power");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT8_5()
    {
        $this->setupTest("8.5", "SW-0008-06", "Update return 404 if the device power doesn't exist", self::REQUIRED);
        $this->devIOP404Test("power", "put");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT8_6()
    {
        $this->setupTest("8.6", "SW-0008-07", "Delete a device power", self::REQUIRED);
        $this->devIOPDeleteTest("power");
    }
    /**
    * Gets the config and saves it
    *
    * @param int    $timeout  The timeout in seconds
    *
    * @return string The left over string
    */
    protected function testDVT8_7()
    {
        $this->setupTest("8.7", "SW-0008-08", "Delete return 404 if the device power doesn't exist", self::REQUIRED);
        $this->devIOP404Test("power", "delete");
    }

}


?>
