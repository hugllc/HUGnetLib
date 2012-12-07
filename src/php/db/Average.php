<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class Average extends History
{
    /** @var string This is the label for 15 Minute averages*/
    const AVERAGE_15MIN = "15MIN";
    /** @var string This is the label for hourly averages*/
    const AVERAGE_HOURLY = "HOURLY";
    /** @var string This is the label for daily averages*/
    const AVERAGE_DAILY = "DAILY";
    /** @var string This is the label for weekly averages*/
    const AVERAGE_WEEKLY = "WEEKLY";
    /** @var string This is the label for monthly averages*/
    const AVERAGE_MONTHLY = "MONTHLY";
    /** @var string This is the label for yearly averages*/
    const AVERAGE_YEARLY = "YEARLY";

    /** @var string This is the table we should use */
    public $sqlTable = "";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = null;
    /** @var string This is the date field for the table.  Leave blank if none  */
    public $dateField = "Date";
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
            "Default" => "15MIN",
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
    public $datacols = 15;
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
    * @param HistoryTableBase &$data This is the data to use to calculate the average
    * @param string           $type  The type of average to calculate
    *
    * @return bool True on success, false on failure
    */
    public function calcAverage(History &$data, $type)
    {
        if ($type == self::AVERAGE_15MIN) {
            return $this->calc15MinAverage($data);
        }
        return $this->calcOtherAverage($data, $type);
    }
    /**
    * This calculates the averages
    *
    * It will return once for each average that it calculates.  The average will be
    * stored in the instance this is called from.  If this is fed history table
    * then it will calculate 15 minute averages.
    *
    * @param HistoryTableBase &$data This is the data to use to calculate the average
    *
    * @return bool True on success, false on failure
    */
    protected function calc15MinAverage(History &$data)
    {
        if ($data->isEmpty()) {
            return false;
        }
        if (($this->get("Type") === self::AVERAGE_15MIN)
            && ($this->endTime >= ($data->get("Date") - 1800))
            && ($this->endTime <= ($data->get("Date") - 900))
        ) {
            $this->set("Date", $this->endTime);
            $this->startTime += 900;
            $this->endTime += 900;
            return true;
        }
        $this->clearData();
        $this->set("Type", self::AVERAGE_15MIN);
        $this->_getTimePeriod($data->get("Date"), self::AVERAGE_15MIN);
        $this->set("Date", $this->startTime);
        $tooOld = $this->endTime + 900;// After another 900 seconds we don't use this
        $this->divisors = array();
        $this->set("id", $data->get("id"));
        $ret = true;
        $last = array();
        while (($data->get("Date") < $tooOld) && $ret) {
            // This is the difference in seconds between this record and the start
            for ($i = 0; $i < $this->datacols; $i++) {
                $col = "Data".$i;
                if (empty($last[$col])) {
                    $last[$col] = $this->get("Date");
                }
                $mult = $this->calc15MinAverageMult($data, $last[$col], $i);
                $work = $data->get($col);
                if (!is_null($work)) {
                    $mine = $this->get($col);
                    $mine += ($mult * $work);
                    $this->divisors[$col] += $mult;
                    $last[$col] = $data->get("Date");
                    $this->set($col, $mine);
                }
            }
            if ($data->get("Date") <= $this->endTime) {
                $ret = $data->nextInto();
            } else {
                break;
            }
        }
        $this->settleDivisors();
        return true;
    }

    /**
    * This calculates the averages
    *
    * It will return once for each average that it calculates.  The average will be
    * stored in the instance this is called from.  If this is fed history table
    * then it will calculate 15 minute averages.
    *
    * @param HistoryTableBase &$data This is the data to use to calculate the average
    * @param int              $last  This is the last record that we used
    * @param int              $col   The column to use
    *
    * @return float The number to multiply by for the weighted average.
    */
    protected function calc15MinAverageMult(History &$data, $last, $col)
    {
        if ($this->device->input($col)->get("total")) {
            if ($data->get("Date") > $this->endTime) {
                $mult = ($this->endTime - $last);
                $denom = $data->get("Date") - $last;
                $mult = $mult / $denom;
            } else {
                $mult = 1;
            }
        } else {
            if ($data->get("Date") <= $this->endTime) {
                $mult = $data->get("Date") - $last;
            } else {
                $mult = $this->endTime - $last;
            }
        }
        return $mult;
    }
    /**
    * This calculates the averages
    *
    * It will return once for each average that it calculates.  The average will be
    * stored in the instance this is called from.  If this is fed history table
    * then it will calculate 15 minute averages.
    *
    * @param HistoryTableBase &$data This is the data to use to calculate the average
    * @param string           $type  The type of average to calculate
    *
    * @return bool True on success, false on failure
    */
    protected function calcOtherAverage(History &$data, $type)
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
        $this->set("Date", $this->startTime);
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
        return true;
    }
    /**
    * This settles the averages
    *
    * @return none
    */
    protected function settleDivisors()
    {
        // Settle  out the multipliers
        for ($i = 0; $i < $this->datacols; $i++) {
            $col = "Data".$i;
            if ($this->divisors[$col] == 0) {
                $this->divisors[$col] = 1;
            }
            $value = $this->get($col);
            if (!is_null($value)) {
                if (!$this->device->input($i)->get("total")) {
                    $value = $value / $this->divisors[$col];
                }
                $this->set(
                    $col,
                    round(
                        $value,
                        $this->device->input($i)->get("maxDecimals")
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
        $mon = gmdate("m", $time);
        $day = gmdate("d", $time);
        $Year = gmdate("Y", $time);
        if ($type == self::AVERAGE_15MIN) {
            $min = gmdate("i", $time);
            if ($min < 15) {
                $min = 0;
            } else if ($min < 30) {
                $min = 15;
            } else if ($min < 45) {
                $min = 30;
            } else {
                $min = 45;
            }
            $this->startTime = gmmktime($Hour, $min, 0, $mon, $day, $Year);
            $this->endTime = gmmktime($Hour, $min + 15, 0, $mon, $day, $Year);
            return true;
        } else if ($type == self::AVERAGE_HOURLY) {
            $this->startTime = gmmktime($Hour, 0, 0, $mon, $day, $Year);
            $this->endTime = gmmktime($Hour + 1, 0, 0, $mon, $day, $Year);
            return true;
        } else if ($type == self::AVERAGE_DAILY) {
            $this->startTime = gmmktime(0, 0, 0, $mon, $day, $Year);
            $this->endTime = gmmktime(0, 0, 0, $mon, $day + 1, $Year);
            return true;
        } else if ($type == self::AVERAGE_WEEKLY) {
            $weekday = gmdate("w", $time);
            $this->startTime = gmmktime(0, 0, 0, $mon, ($day - $weekday), $Year);
            $this->endTime = gmmktime(0, 0, 0, $mon, ($day - $weekday + 7), $Year);
            return true;
        } else if ($type == self::AVERAGE_MONTHLY) {
            $this->startTime = gmmktime(0, 0, 0, $mon, 1, $Year);
            $this->endTime = gmmktime(0, 0, 0, $mon + 1, 1, $Year);
            return true;
        } else if ($type == self::AVERAGE_YEARLY) {
            $this->startTime = gmmktime(0, 0, 0, 1, 1, $Year);
            $this->endTime = gmmktime(0, 0, 0, 1, 1, $Year + 1);
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
        $type = "15MIN",
        $extraWhere = null,
        $extraData = null
    ) {
        return parent::getTimePeriod(
            $start, $end, $devId, "id", "Type = ?", array($type)
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
        $tzoffset = 0;
        $date = $this->get($field);
        $type = $this->get("Type");
        if (($type == self::AVERAGE_MONTHLY)
            || ($type == self::AVERAGE_YEARLY)
        ) {
            $tzoffset = (int)date("Z", $date);
            // This does not seem to be needed.
            // + ((int)date("I", $this->$field)*3600);
        }
        return $date - $tzoffset;
    }
}
?>
