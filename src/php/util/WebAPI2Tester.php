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
    /** This is the test location */
    private $_api = "";
    /** This is our UI object */
    private $_ui = null;
    /** This is our UI object */
    private $_result = true;
    /**
    * This returns a WebAPI2Tester object
    *
    * @param string &$ui  The UI to output our findings to
    * @param string $url  The URL to test
    *
    * @return reference to the table class object
    */
    private function __construct(&$ui, $url)
    {
        $this->_api = $url;
        $this->_ui  = &$ui;
    }
    /**
    * This returns a WebAPI2Tester object
    *
    * @param string &$ui The UI to output our findings to
    * @param string $url The URL to test
    *
    * @return reference to the table class object
    */
    public static function &factory(&$ui, $url) 
    {
        return new WebAPI2Tester($ui, $url);
    }
    /**
    * This runs the tests
    *
    * @return true if all tests pass.  False otherwise
    */
    public function execute()
    {
        $this->outputHeader();
        $this->outputResult($this->_api, "Hello", true);
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
        $this->_ui->out("Running tests against ".$this->_url);
        $this->_ui->out();
    }
    /**
    * This returns a WebAPI2Tester object
    *
    * @param string $test The test 
    *
    * @return reference to the table class object
    */
    public function outputResult($test, $notes, $result)
    {
        $line = "";
        if (is_bool($result)) {
            $result = ($result) ? "Pass" : "Fail";
        } else {
            $result = "N/A";
        }
        
        $line = $test."\t\t\t".$result;
        $this->_ui->out($line);
        $this->_ui->out($notes);
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
        $this->_ui->out("Result: ".(($this->_result) ? "Pass" : "Fail"));
        $this->_ui->out();
    }
}


?>
