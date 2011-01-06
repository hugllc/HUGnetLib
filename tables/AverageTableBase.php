<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @copyright  2007-2010 Hunt Utilities Group, LLC
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
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class AverageTableBase extends HistoryTableBase
{
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
        "DateIDIndex" => array(
            "Name" => "DateIDIndex",
            "Unique" => true,
            "Columns" => array("Date", "id", "Type"),
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
    * @param HistoryTableBase $data This is the data to use to calculate the averages
    * 
    * @return bool True on success, false on failure
    */
    function calcAverage(HistoryTableBase $data)
    {
        if ($data->isEmpty()) {
            return false;
        }
        $this->clearData();
        $this->Date = $this->_get15MinTimePeriod($data->Date);
        $end = $this->Date + 900;   // 900 seconds in 15 minutes
        $tooOld = $end + 900;       // After another 900 seconds we don't use this
        $divisors = array();
        $ret = true;
        $last = array();
        while (($data->Date < $tooOld) && $ret) {
            // This is the difference in seconds between this record and the start
            for ($i = 0; $i < $this->datacols; $i++) {
                $col = "Data".$i;
                if (empty($last[$col])) {
                    $last[$col] = $this->Date;
                }
                if ($last[$col] >= $end) {
                    continue;
                }
                if ($data->Date <= $end) {
                    $mult = $data->Date - $last[$col];
                } else {
                    $mult = $end - $last[$col];
                }
                if (!is_null($data->$col)) {
                    $this->$col += ($mult * $data->$col);
                    $divisors[$col] += $mult;
                    $last[$col] = $data->Date;
                }
            }
            if ($data->Date <= $end) {
                $ret = $data->nextInto();
            } else {
                break;
            }
        }
        // Settle  out the multipliers
        for ($i = 0; $i < $this->datacols; $i++) {
            $col = "Data".$i;
            if (!is_null($this->$col)) {
                $this->$col = round(
                    $this->$col / $divisors[$col],
                    $this->device->sensors->sensor($i)->maxDecimals
                );
            }
        }
        return true;
    }

    /**
    * This sets the time correctly
    *
    * @param int $time The time we are currently at
    *
    * @return bool True on success, false on failure
    */
    private function _get15MinTimePeriod($time)
    {
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
        return gmmktime(
            gmdate("H", $time),
            $min,
            0,
            gmdate("m", $time),
            gmdate("d", $time),
            gmdate("Y", $time)
        );
    }

}
?>
