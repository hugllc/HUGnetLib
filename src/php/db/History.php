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
namespace HUGnet\db;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is for some constants that it contains */
require_once dirname(__FILE__)."/../devices/datachan/Driver.php";
/** This is for some constants that it contains */
require_once dirname(__FILE__)."/Table.php";

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
abstract class History extends Table
{
    /** @var string This is the table we should use */
    protected $sqlTable = "";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = null;
    /** @var string This is the date field for the table.  Leave blank if none  */
    protected $dateField = "Date";
    /** @var string The orderby clause for this table */
    public $sqlOrderBy = "Date desc";
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
    protected $fixedColumns = array(
        "id" => array(
            "Name" => "id",
            "Type" => "int",
            "Default" => 0,
        ),
        "Date" => array(
            "Name" => "Date",
            "Type" => "bigint",
            "Default" => 0,
        ),
        "TestID" => array(
            "Name"    => "TestID",
            "Type"    => "int",
            "Default" => null,
            "Null"    => true,
        ),
        "deltaT" => array(
            "Name" => "deltaT",
            "Type" => "float",
            "Default" => 0.0,
        ),
    );
    /** @var array This is where the columns will actually reside. */
    public $sqlColumns = array();
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
        "IDDateIndex" => array(
            "Name" => "IDDateIndex",
            "Unique" => true,
            "Columns" => array(0 => "id", 1 => "Date"),
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
        "raw" => array(),
        "converted" => false,    //  Says whether the unit conversion has been done
    );
    /** @var This is the dataset */
    protected $datacols = 15;
    /** @var This is the  raw data for differential mode */
    public $raw = array();
    /** @var This is the  raw data for differential mode */
    public $device = null;
    /** @var This is where we store the previous record for differential mode */
    public $prev = array();
    /**
    * This is the constructor
    *
    * @param object &$system  The system object to use
    * @param mixed  $data     This is an array or string to create the object from
    * @param object &$connect The connection manager
    */
    protected function __construct(&$system, $data="", &$connect = null)
    {
        $this->setupColumns();
        parent::__construct($system, $data, $connect);
        $this->create();
    }
    /**
    * This is the destructor
    */
    function __destruct()
    {
        unset($this->device);
        parent::__destruct();
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromDataArray($array)
    {
        parent::fromArray($array);
        $raw = array();
        for ($i = 0; $i < $this->datacols; $i++) {
            $key = "Data$i";
            $this->set($key, $array[$i]["value"]);
            if (!is_null($array[$i]["raw"])) {
                $raw[$i] = $array[$i]["raw"];
            }
        }
        $this->set("raw", $raw);
    }
    /**
    * Creates the object from a string or array
    *
    * @param mixed $data This is whatever you want to give the class
    *
    * @return null
    */
    public function fromAny($data)
    {
        if (is_array($data) && isset($data[0])) {
            $this->fromDataArray($data);
        } else {
            parent::fromAny($data);
        }
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @return null
    */
    protected function setupColumns()
    {
        $this->sqlColumns = $this->fixedColumns;
        for ($i = 0; $i < $this->datacols; $i++) {
            $this->sqlColumns["Data$i"] = array(
                "Name" => "Data$i",
                "Type" => "double",
                "Default" => null,
                "Null" => true,
            );
        }
    }
    /**
    * Checks to see if our record exists in the database
    *
    * @param int $period The length of time to search in
    *
    * @return bool True if it exists, false otherwise
    */
    public function exists($period = 0)
    {

        $date  = $this->get("Date");
        $toler = (int)((int)$period / 2);
        $where = array(
            "id" => $this->get("id"),
            "Date" => array(
                '$gte' => (int)($date - $toler),
                '$lte' => (int)($date + $toler)
            ),
        );
        $ret = (bool) $this->dbDriver()->countWhere(
            $where,
            array(),
            "Date"
        );
        $this->dbDriver()->reset();
        return (bool)$ret;
    }
    /**
    * Sets the extra attributes field
    *
    * @param int    $start      The start of the time
    * @param int    $end        The end of the time
    * @param mixed  $devId      The ID to use.  None if null
    * @param string $type       Not used here.  This is to be compatible with
    *                              AverageTableBase::getPeriod()
    * @param string $extraWhere Extra where clause
    * @param array  $extraData  Data for the extraWhere clause
    *
    * @return mixed The value of the attribute
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getPeriod(
        $start,
        $end = null,
        $devId = null,
        $type = null,
        $extraWhere = null,
        $extraData = null
    ) {
        return parent::getTimePeriod(
            $start, $end, $devId, "id", $extraWhere, $extraData
        );
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/
    /**
    * function to set LastHistory
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setDate($value)
    {
        $this->data["Date"] = self::unixDate($value);
    }
    /**
    * function to set id
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setId($value)
    {
        $this->data["id"] = (int)$value;
    }

}
?>
