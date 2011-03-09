<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @category   Tables
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/HistoryTableBase.php";
/** This is for the configuration */
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Tables
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class AverageTableBase extends HistoryTableBase
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
            "Name" => "id",
            "Type" => "int",
            "Default" => 0,
        ),
        "Date" => array(
            "Name" => "Date",
            "Type" => "bigint",
            "Default" => 0,
        ),
        "Type" => array(
            "Name" => "Type",
            "Type" => "varchar(16)",
            "Default" => "15MIN",
        ),
    );
    /** @car array This is where the columns will actually reside. */
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
    * @param mixed $data    This is an array or string to create the object from
    * @param int   $columns The number of columns to create
    */
    function __construct($data="", $columns=null)
    {
        $this->setupColumns($columns);
        parent::__construct($data);
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
    public function calcAverage(HistoryTableBase &$data, $type)
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
    protected function calc15MinAverage(HistoryTableBase &$data)
    {
        if ($data->isEmpty()) {
            return false;
        }
        $this->clearData();
        $this->Type = self::AVERAGE_15MIN;
        $this->_getTimePeriod($data->Date, self::AVERAGE_15MIN);
        $this->Date = $this->startTime;
        $tooOld = $this->endTime + 900;// After another 900 seconds we don't use this
        $this->divisors = array();
        $this->id = $data->id;
        $ret = true;
        $last = array();
        while (($data->Date < $tooOld) && $ret) {
            // This is the difference in seconds between this record and the start
            for ($i = 0; $i < $this->datacols; $i++) {
                $col = "Data".$i;
                if (empty($last[$col])) {
                    $last[$col] = $this->Date;
                }
                $mult = $this->calc15MinAverageMult($data, $last[$col], $i);
                if (!is_null($data->$col)) {
                    $this->$col += ($mult * $data->$col);
                    $this->divisors[$col] += $mult;
                    $last[$col] = $data->Date;
                }
            }
            if ($data->Date <= $this->endTime) {
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
    protected function calc15MinAverageMult(HistoryTableBase &$data, $last, $col)
    {
        if ($this->device->sensors->sensor($col)->total()) {
            if ($data->Date > $this->endTime) {
                $mult = ($this->endTime - $last);
                $denom = $data->Date - $last;
                $mult = $mult / $denom;
            } else {
                $mult = 1;
            }
        } else {
            if ($data->Date <= $this->endTime) {
                $mult = $data->Date - $last;
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
    protected function calcOtherAverage(HistoryTableBase &$data, $type)
    {
        if ($data->isEmpty()) {
            return false;
        }
        $this->clearData();
        if (empty($this->baseType)) {
            $this->baseType = $data->Type;
        }
        $ret = $this->_getTimePeriod($data->Date, $type);
        if (!$ret) {
            return false;
        }
        $this->id = $data->id;
        $this->Type = $type;
        $this->Date = $this->startTime;
        $this->divisors = array();
        $ret = true;
        while (($data->Date < $this->endTime) && $ret) {
            if ($data->Type == $this->baseType) {
                for ($i = 0; $i < $this->datacols; $i++) {
                    $col = "Data".$i;
                    if (!is_null($data->$col)) {
                        $this->$col += $data->$col;
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
            if (!is_null($this->$col)) {
                if (!$this->device->sensors->sensor($i)->total()) {
                    $this->$col = $this->$col / $this->divisors[$col];
                }
                $this->$col = round(
                    $this->$col,
                    $this->device->sensors->sensor($i)->maxDecimals
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
        $H = gmdate("H", $time);
        $m = gmdate("m", $time);
        $d = gmdate("d", $time);
        $Y = gmdate("Y", $time);
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
            $this->startTime = gmmktime($H, $min, 0, $m, $d, $Y);
            $this->endTime = gmmktime($H, $min + 15, 0, $m, $d, $Y);
        } else if ($type == self::AVERAGE_HOURLY) {
            $this->startTime = gmmktime($H, 0, 0, $m, $d, $Y);
            $this->endTime = gmmktime($H + 1, 0, 0, $m, $d, $Y);
            return true;
        } else if ($type == self::AVERAGE_DAILY) {
            $this->startTime = gmmktime(0, 0, 0, $m, $d, $Y);
            $this->endTime = gmmktime(0, 0, 0, $m, $d + 1, $Y);
            return true;
        } else if ($type == self::AVERAGE_WEEKLY) {
            $w = gmdate("w", $time);
            $this->startTime = gmmktime(0, 0, 0, $m, ($d - $w), $Y);
            $this->endTime = gmmktime(0, 0, 0, $m, ($d - $w + 7), $Y);
            return true;
        } else if ($type == self::AVERAGE_MONTHLY) {
            $this->startTime = gmmktime(0, 0, 0, $m, 1, $Y);
            $this->endTime = gmmktime(0, 0, 0, $m + 1, 1, $Y);
            return true;
        } else if ($type == self::AVERAGE_YEARLY) {
            $this->startTime = gmmktime(0, 0, 0, 1, 1, $Y);
            $this->endTime = gmmktime(0, 0, 0, 1, 1, $Y + 1);
            return true;
        }
        return false;
    }
    /**
    * Sets the extra attributes field
    *
    * @param int    $start The start of the time
    * @param int    $end   The end of the time
    * @param mixed  $id    The ID to use.  None if null
    * @param string $type  The type of record
    *
    * @return mixed The value of the attribute
    */
    public function getPeriod($start, $end = null, $id = null, $type = "15MIN")
    {
        return parent::getPeriod(
            $start, $end, $id, "id", "Type = ?", array($type)
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
        if (($this->Type == self::AVERAGE_MONTHLY)
            || ($this->Type == self::AVERAGE_YEARLY)
        ) {
            $tzoffset = (int)date("Z") + ((int)date("I", $this->$field)*3600);
        }
        return $this->$field - $tzoffset;
    }
}
?>
