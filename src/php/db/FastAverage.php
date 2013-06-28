<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
namespace HUGnet\db;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is for some constants that it contains */
require_once dirname(__FILE__)."/../devices/datachan/Driver.php";
/** This is for some constants that it contains */
require_once dirname(__FILE__)."/History.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class FastAverage extends History
{
    /** @var string This is the label for 15 Minute averages*/
    const AVERAGE_30SEC = "30SEC";
    /** @var string This is the label for 15 Minute averages*/
    const AVERAGE_1MIN = "1MIN";
    /** @var string This is the label for 15 Minute averages*/
    const AVERAGE_5MIN = "5MIN";
    /** @var string This is the label for 15 Minute averages*/
    const AVERAGE_15MIN = "15MIN";

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
            "Name"    => "id",
            "Type"    => "int",
            "Default" => 0,
        ),
        "Date" => array(
            "Name"    => "Date",
            "Type"    => "bigint",
            "Default" => 0,
        ),
        "TestID" => array(
            "Name"    => "TestID",
            "Type"    => "int",
            "Default" => null,
            "Null"    => true,
        ),
        "Type" => array(
            "Name"    => "Type",
            "Type"    => "varchar(16)",
            "Default" => "30SEC",
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
        "DateIDTypeIndex" => array(
            "Name" => "DateIDTypeIndex",
            "Unique" => true,
            "Columns" => array("Date", "id", "Type"),
        ),
        "DateIDIndex" => array(
            "Name" => "DateIDIndex",
            "Unique" => false,
            "Columns" => array("Date", "id"),
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
        "raw" => array(),
    );
    /** @var This is the dataset */
    protected $datacols = 15;
    /** @var This is the  raw data for differential mode */
    public $raw = array();
    /** @var This is the  raw data for differential mode */
    public $device = null;
    /** @var string The start time for the average */
    protected $startTime = null;
    /** @var string The end time for the average */
    protected $endTime = null;
    /** @var int The divisors for the average*/
    protected $divisors = array();
    /** @var string The base type for the averages */
    protected $baseType = "";
    /** @var string The device channels */
    private $_channels = null;

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
    * This calculates the averages
    *
    * It will return once for each average that it calculates.  The average will be
    * stored in the instance this is called from.  If this is fed history table
    * then it will calculate 15 minute averages.
    *
    * @param \HUGnet\db\History &$data This is the data to use to calc the average
    * @param string             $type  The type of average to calculate
    *
    * @return bool True on success, false on failure
    */
    public function calcAverage(\HUGnet\db\History &$data, $type)
    {
        if ($data->isEmpty()) {
            return false;
        }
        $this->clearData();
        if (empty($this->baseType)) {
            $this->baseType = $data->get("Type");
        }
        $ret = $this->_getTimePeriod($data->get("Date"), $type);
        if (!$ret) {
            return false;
        }
        $this->set("id", $data->get("id"));
        $this->set("Type", $type);
        $this->set("Date", $this->endTime);
        $this->divisors = array();
        $ret = true;
        while (($data->get("Date") < $this->endTime) && $ret) {
            if ($data->get("Type") == $this->baseType) {
                for ($i = 0; $i < $this->datacols; $i++) {
                    $col = "Data".$i;
                    $value = $data->get($col);
                    if (!is_null($value)) {
                        $mine = $this->get($col);
                        $mine += $value;
                        $this->set($col, $mine);
                        $this->divisors[$col]++;
                    }
                }
            }
            $ret = $data->nextInto();
        }
        $this->settleDivisors();
        if ($data->get("Date") >= $this->endTime) {
            // We passed our time, so this is a complete record
            return true;
        }
        // Not enough records to make this complete
        $this->clearData();
        return false;
    }
    /**
    * This settles the averages
    *
    * @return none
    */
    protected function settleDivisors()
    {
        // Settle  out the multipliers
        if (!is_array($this->_channels)) {
            $this->_channels = $this->device->dataChannels()->toArray();
        }
        for ($i = 0; $i < $this->datacols; $i++) {
            $col = "Data".$i;
            if ($this->divisors[$col] == 0) {
                $this->divisors[$col] = 1;
            }
            $value = $this->get($col);
            if (!is_null($value)) {
                if (!$this->_channels[$i]["total"]) {
                    $value = $value / $this->divisors[$col];
                }

                $this->set(
                    $col,
                    round(
                        $value,
                        $this->_channels[$i]["maxDecimals"]
                    )
                );
            }
        }
    }

    /**
    * This sets the time correctly
    *
    * @param int    $time The time we are currently at
    * @param string $type The type of average to calculate
    *
    * @return bool True on success, false on failure
    */
    private function _getTimePeriod($time, $type)
    {
        $Hour = gmdate("H", $time);
        $min = gmdate("i", $time);
        $mon = gmdate("m", $time);
        $day = gmdate("d", $time);
        $Year = gmdate("Y", $time);
        if ($type == self::AVERAGE_30SEC) {
            $sec = gmdate("s", $time);
            if ($sec >= 30) {
                $sec = 30;
            } else {
                $sec = 0;
            }
            $this->startTime = gmmktime($Hour, $min, $sec, $mon, $day, $Year);
            $this->endTime = gmmktime($Hour, $min, $sec + 30, $mon, $day, $Year);
            return true;
        } else if ($type == self::AVERAGE_1MIN) {
            $this->startTime = gmmktime($Hour, $min, 0, $mon, $day, $Year);
            $this->endTime = gmmktime($Hour, $min + 1, 0, $mon, $day, $Year);
            return true;
        } else if ($type == self::AVERAGE_5MIN) {
            for ($base = 55; $base >= 0; $base -= 5) {
                if ($min >= $base) {
                    $min = $base;
                    break;
                }
            }
            $this->startTime = gmmktime($Hour, $min, 0, $mon, $day, $Year);
            $this->endTime = gmmktime($Hour, $min + 5, 0, $mon, $day, $Year);
            return true;
        } else if ($type == self::AVERAGE_15MIN) {
            for ($base = 45; $base >= 0; $base -= 15) {
                if ($min >= $base) {
                    $min = $base;
                    break;
                }
            }
            $this->startTime = gmmktime($Hour, $min, 0, $mon, $day, $Year);
            $this->endTime = gmmktime($Hour, $min + 15, 0, $mon, $day, $Year);
            return true;
        }
        return false;
    }
    /**
    * Sets the extra attributes field
    *
    * @param int    $start      The start of the time
    * @param int    $end        The end of the time
    * @param mixed  $devId      The ID to use.  None if null
    * @param string $type       The type of record
    * @param string $extraWhere Extra where clause
    * @param array  $extraData  Data for the extraWhere clause
    *
    * @return mixed The value of the attribute
    */
    public function getPeriod(
        $start,
        $end = null,
        $devId = null,
        $type = "30SEC",
        $extraWhere = null,
        $extraData = null
    ) {
        return parent::getTimePeriod(
            $start, $end, $devId, "id",  array("Type" => $type)
        );
    }
    /**
    * This sets the time correctly
    *
    * @return array The set of averages that this average supports
    */
    public function averageTypes()
    {
        return array(
            self::AVERAGE_30SEC   => "30 Second Average",
            self::AVERAGE_1MIN   => "1 Minute Average",
            self::AVERAGE_5MIN   => "5 Minute Average",
            self::AVERAGE_15MIN   => "15 Minute Average",
        );
    }
    /**
    * By default it outputs the date in the format specified in myConfig
    *
    * This function fixes the time offset due to the time zone for
    * monthly and yearly averages.
    *
    * @param string $field The field to output
    *
    * @return string The date as a formatted string
    */
    protected function outputDate($field)
    {
        $date = $this->get($field);
        return $date;
    }
}
?>
