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
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetDBTable.php";
/** This is for the configuration */
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";
require_once dirname(__FILE__)."/../containers/DeviceContainer.php";

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
class DevicesHistoryTable extends HUGnetDBTable
{
    /** @var string This is the table we should use */
    public $sqlTable = "devicesHistory";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = null;
    /** @var string The orderby clause for this table */
    public $sqlOrderBy = "SaveDate DESC";
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
            "Type" => "INTEGER",
        ),
        "SaveDate" => array(
            "Name" => "SaveDate",
            "Type" => "bigint",
        ),
        "SetupString" => array(
            "Name" => "SetupString",
            "Type" => "varchar(255)",
            "Default" => "",
        ),
        "SensorString" => array(
            "Name" => "SensorString",
            "Type" => "text",
            "Default" => "",
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
    *
    *  To add a length to the column, simply add a comma and then the length to
    *  the column name in the "Columns" array.  For a column named "colName" with
    *  a index length of 15 would be:
    *
    *  "colName,15"
    */
    public $sqlIndexes = array(
        "DeviceID" => array(
            "Name"    => "DeviceID",
            "Unique"  => true,
            "Columns" => array("id", "SaveDate", "SetupString", "SensorString,255"),
        ),
        "idDate" => array(
            "Name"    => "idDate",
            "Unique"  => true,
            "Columns" => array("id", "SaveDate"),
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
    );
    /** @var This is the device */
    public $device = null;

    /**
    * This is the constructor
    *
    * @param mixed  $data  This is an array or string to create the object from
    * @param string $table The table to use
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    function __construct($data="", $table="")
    {
        parent::__construct($data);
        $this->create();
    }
    /**
    * This function updates the record currently in this table
    *
    * @param bool $replace Replace any records found that collide with this one.
    *
    * @return bool True on success, False on failure
    */
    public function insertRow($replace = false)
    {
        if (!$this->checkRecord()) {
            return false;
        }
        $ret = $this->select(
            "id = ? AND SetupString = ? and SensorString = ?",
            array(
                $this->id,
                $this->SetupString,
                $this->SensorString,
            )
        );
        if (is_object($ret[0])) {
            $this->SaveDate = (int)$ret[0]->SaveDate;
            return true;
        }
        return parent::insertRow($replace);
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param DeviceContainer &$dev The device container to use
    *
    * @return null
    */
    public function fromDeviceContainer(DeviceContainer &$dev)
    {
        $this->id = $dev->id;
        $this->SensorString = $dev->sensors->toDevHistString();
        $this->SetupString = $dev->toSetupString();
        $this->SaveDate = time();
    }
    /**
    * returns a device with the stuff here
    *
    * @return null
    */
    public function &toDeviceContainer()
    {
        $dev = new DeviceContainer(array("group" => $this->group));
        $dev->fromSetupString($this->SetupString);
        $dev->sensors->fromString($this->SensorString);
        if (empty($dev->ActiveSensors)) {
            $dev->ActiveSensors = $dev->sensors->Sensors;
        }
        return $dev;
    }
    /**
    * returns a device with the stuff here
    *
    * @return null
    */
    public function checkRecord()
    {
        if (substr($this->SetupString, 10, 4) !== "0039") {
            return false;
        }
        if (substr($this->SetupString, 20, 4) !== "0039") {
            return false;
        }
        $data = self::fromStringDecode($this->SensorString);
        if ($data["Sensors"] <= 0) {
            return false;
        }
        return true;
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param int $devId The id of the record to get
    * @param int $date  The date to use
    * @param int $data  The data to load into the history table
    *
    * @return null
    */
    static public function &deviceFactory($devId, $date = 0, $data = array())
    {
        $hist = new DevicesHistoryTable($data);
        if (empty($date)) {
            $date = time();
        }
        $hist->sqlLimit = 1;
        $hist->sqlOrderBy = "SaveDate DESC";
        $hist->selectOneInto(
            "id = ? AND SaveDate <= ?",
            array($devId, $date)
        );
        return $hist->toDeviceContainer();
    }
    /**
    * Creates the object from a string or array
    *
    * @param mixed &$data This is whatever you want to give the class
    *
    * @return null
    */
    public function fromAny(&$data)
    {
        if (is_object($data) && is_a($data, "DeviceContainer")) {
            $this->fromDeviceContainer($data);
        } else {
            parent::fromAny($data);
        }
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/
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
