<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
namespace HUGnet\db\tables;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our system interface */
require_once dirname(__FILE__)."/../../interfaces/DBTable.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceTests extends \HUGnet\db\Table
    implements \HUGnet\interfaces\DBTable
{
    /** @var string This is the table we should use */
    public $sqlTable = "devicetests";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = "id";
    /**
    * @var array This is the definition of the columns
    *
    * This should consist of the following structure:
    * array(
    *   "name" => array(
    *       "Name"          => string The name of the column
    *       "Type"          => string The type of the column
    *       "Default"       => mixed  The default value for the column
    *       "Null"          => bool   true if null is allowed, false otherwise
    *       "AutoIncrement" => bool   true if the column is auto_increment
    *       "CharSet"       => string the character set if the column is text or char
    *       "Collate"       => string colation if the table is text or char
    *       "Primary"       => bool   If we are a primary Key.
    *       "Unique"        => bool   If we are a unique column.
    *   ),
    *   "name2" => array(
    *   .
    *   .
    *   .
    * );
    *
    * Not all fields have to be filled in.  Name and Type are the only required
    * fields.  The index of the base array should be the same as the "Name" field.
    */
    public $sqlColumns = array(
        "id" => array(
            "Name" => "id",
            "Type" => "int",
            "Primary" => true,
        ),
        "HWPartNum" => array(
            "Name" => "HWPartNum",
            "Type" => "varchar(12)",
            "Default" => '',
        ),
        "FWPartNum" => array(
            "Name" => "FWPartNum",
            "Type" => "varchar(12)",
            "Default" => '',
        ),
        "FWVersion" => array(
            "Name" => "FWVersion",
            "Type" => "varchar(8)",
            "Default" => '',
        ),
        "BtldrVersion" => array(
            "Name" => "BtldrVersion",
            "Type" => "varchar(8)",
            "Default" => '',
        ),
        "MicroSN" => array(
            "Name" => "MicroSN",
            "Type" => "varchar(22)",
            "Default" => '',
        ),
        "TestDate" => array(
            "Name" => "TestDate",
            "Type" => "bigint",
            "Default" => 0,
        ),
        "TestResult" => array(
            "Name" => "TestResult",
            "Type" => "varchar(4)",
            "Default" => "FAIL",
        ),
        "TestData" => array(
            "Name" => "TestData",
            "Type" => "longtext",
            "Default" => "",
        ),
        "TestsFailed" => array(
            "Name" => "TestsFailed",
            "Type" => "longtext",
            "Default" => "None",
        ),
    );
    /**
    * @var array This is the definition of the indexes
    *
    *   array(
    *       "Name" => array (
    *           "Name"    => string The name of the index
    *           "Unique"  => bool   Create a Unique index
    *           "Columns" => array  Array of column names
    *       ),
    *       "name2" => array(
    *       .
    *       .
    *   ),
    */
    public $sqlIndexes = array(
        "id" => array(
            "Name" => "id",
            "Unique" => true,
            "Columns" => array("id", "TestDate"),
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
    );
    /**
    * This is the constructor
    *
    * @param object &$system  The system object to use
    * @param mixed  $data     This is an array or string to create the object from
    * @param object &$connect The connection manager
    */
    protected function __construct(&$system, $data="", &$connect = null)
    {
        parent::__construct($system, $data, $connect);
        $this->create();
    }

    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/
    /**
    * function to set Date
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setTestDate($value)
    {
        $this->data["TestDate"] = self::unixDate($value);
    }
    
    /**********************************************************/
    /* add set functions to json_encode test failure array,   */
    /* test data array, and test result.                      */
    /**********************************************************/
    
    /**
    * Sets the Test Result
    *
    * @param int  $value test result value
    *
    * @return null
    */
    protected function setTestResult($value)
    {
        if ($value == 1) {
            $this->data["TestResult"] = "PASS";
        } else if ($value < 1) {
            $this->data["TestResult"] = "FAIL";
        }
    }

    /**********************************************************/
    /* add set functions to json_encode test failure array,   */
    /* test data array, and test result.                      */
    /**********************************************************/
    
    /**
    * Sets the Test Data
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setTestData($value)
    {
        if (is_array($value)) {
            $this->data["TestData"] = json_encode($value);
        } else if (is_string($value)) {
            $this->data["TestData"] = $value;
        }
    }
    /**********************************************************/
    /* add set functions to json_encode test failure array,   */
    /* test data array, and test result.                      */
    /**********************************************************/
    
    /**
    * Sets the Tests Failed
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setTestsFailed($value)
    {
        if (is_array($value)) {
            $this->data["TestsFailed"] = json_encode($value);
        } else if (is_string($value)) {
            $this->data["TestsFailed"] = $value;
        }
    }

}
?>
