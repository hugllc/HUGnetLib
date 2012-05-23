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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetDBTable.php";
/** This is for the configuration */
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";
require_once dirname(__FILE__)."/../containers/DeviceParamsContainer.php";
require_once dirname(__FILE__)."/../containers/DeviceSensorsContainer.php";

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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DevicesTable extends HUGnetDBTable
{
    /** This is the maximum our SN can be */
    const MAX_TEMP_SN = 0xFDFFFF;
    /** This is the minimum our SN can be */
    const MIN_TEMP_SN = 0xFD0000;
    /** This is the maximum our SN can be */
    const MAX_GROUP_SN = 0xFFFFFF;
    /** This is the minimum our SN can be */
    const MIN_GROUP_SN = 0xFF0000;

    /** @var string This is the table we should use */
    public $sqlTable = "devices";
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
            "Type" => "INTEGER",
            "Primary" => true,
        ),
        "DeviceID" => array(
            "Name" => "DeviceID",
            "Type" => "varchar(6)",
            "Default" => '000000',
        ),
        "DeviceName" => array(
            "Name" => "DeviceName",
            "Type" => "varchar(128)",
            "Default" => '',
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
        "RawSetup" => array(
            "Name" => "RawSetup",
            "Type" => "varchar(128)",
            "Default" => '',
        ),
        "Active" => array(
            "Name" => "Active",
            "Type" => "tinyint(4)",
            "Default" => 1,
        ),
        "GatewayKey" => array(
            "Name" => "GatewayKey",
            "Type" => "int(11)",
            "Default" => 0,
        ),
        "ControllerKey" => array(
            "Name" => "ControllerKey",
            "Type" => "int(11)",
            "Default" => 0,
        ),
        "ControllerIndex" => array(
            "Name" => "ControllerIndex",
            "Type" => "tinyint(4)",
            "Default" => 0,
        ),
        "DeviceLocation" => array(
            "Name" => "DeviceLocation",
            "Type" => "varchar(64)",
            "Default" => "",
        ),
        "DeviceJob" => array(
            "Name" => "DeviceJob",
            "Type" => "varchar(64)",
            "Default" => '',
        ),
        "Driver" => array(
            "Name" => "Driver",
            "Type" => "varchar(32)",
            "Default" => 'eDEFAULT',
        ),
        "PollInterval" => array(
            "Name" => "PollInterval",
            "Type" => "mediumint(9)",
            "Default" => 0,
        ),
        "ActiveSensors" => array(
            "Name" => "ActiveSensors",
            "Type" => "smallint(6)",
            "Default" => 0,
        ),
        "DeviceGroup" => array(
            "Name" => "DeviceGroup",
            "Type" => "varchar(6)",
            "Default" => 'FFFFFF',
        ),
        "sensors" => array(
            "Name" => "sensors",
            "Type" => "longtext",
            "Default" => '',
        ),
        "params" => array(
            "Name" => "params",
            "Type" => "longtext",
            "Default" => '',
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
        "IDIndex" => array(
            "Name" => "IDIndex",
            "Unique" => true,
            "Columns" => array("id"),
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
    );
    /**
    * This is the constructor
    *
    * @param mixed $data This is an array or string to create the object from
    */
    function __construct($data="")
    {
        parent::__construct($data);
        $this->create();
    }
    /**
    * Inserts a device ID into the database if it isn't there already
    *
    * @param mixed $data The string or data to use to insert this row
    *
    * @return null
    */
    static public function insertDeviceID($data)
    {
        $dev = new DevicesTable($data);
        if (!$dev->exists()) {
            return $dev->insertRow();
        }
        return false;
    }
    /**
    * Inserts a device ID into the database if it isn't there already
    *
    * @param mixed $data The string or data to use to insert this row
    *
    * @return null
    */
    static public function insertVirtual($data)
    {
        $data["HWPartNum"] = "0039-24-02-P";
        $data["GatewayKey"] = -1;
        $dev = new DevicesTable($data);
        $dev->DeviceID = dechex($dev->id);
        while ($dev->exists() && ($dev->id < self::MIN_TEMP_SN)) {
            $dev->id++;
            $dev->DeviceID = dechex($dev->id);
        }
        $ret = false;
        if ($dev->id < self::MIN_TEMP_SN) {
            $ret = $dev->insertRow();
            if ($ret) {
                $ret = $dev->id;
            }
        }
        return $ret;
    }
    /**
    * returns true if the container is empty.  False otherwise
    *
    * @return bool Whether this container is empty or not
    */
    public function isEmpty()
    {
        return (bool)(empty($this->data["DeviceID"])
            || ($this->data["DeviceID"] === '000000'));
    }
    /**
    * This function inserts this record in the table
    *
    * @param bool $replace Replace any records found that collide with this one.
    *
    * @return bool True on success, False on failure
    */
    public function insertRow($replace = false)
    {
        if (empty($this->id)) {
            $this->id = hexdec($this->DeviceID);
        }
        // This is so we don't insert bad DeviceIDs.
        // Group and temporary DeviceIDs are omitted, as well as the default one
        if ((($this->id >= self::MIN_GROUP_SN) && ($this->id <= self::MAX_GROUP_SN))
            || (($this->id >= self::MIN_TEMP_SN) && ($this->id <= self::MAX_TEMP_SN))
            || ($this->id == $this->default["id"])
        ) {
            return false;
        }
        return parent::insertRow($replace);
    }
    /**
    * Checks to see if our deviceID exists in the database
    *
    * @return bool True if it exists, false otherwise
    */
    public function exists()
    {

        $ret = (bool) $this->dbDriver()->countWhere(
            "DeviceID = ?", array($this->DeviceID), "DeviceID"
        );
        $this->dbDriver()->reset();
        return $ret;
    }
    /**
    * Changes the part number into XXXX-XX-XX-X form.
    *
    * @param mixed $value The value to set
    *
    * @return string PartNumber in ASCII hex
    */
    public static function formatPartNum($value)
    {
        if (empty($value)) {
            $value = "";
        } else if (stripos($value, "-") === false) {
            $PartNum = strtoupper($value);
            $str     = array();
            $str[]   = substr($PartNum, 0, 4);
            $str[]   = substr($PartNum, 4, 2);
            $str[]   = substr($PartNum, 6, 2);
            $assemb  = substr($PartNum, 8, 2);
            if (is_numeric($assemb)) {
                $str[] = chr(hexdec($assemb));
            } else {
                $str[] = $assemb;
            }
            $value   = implode("-", $str);
        } else {
            $ret = preg_match(
                "/[0-9]{4}-[0-9]{2}-[0-9]{2}-[A-Za-z]{1}/",
                $value,
                $match
            );
            if ($ret > 0) {
                $value = $match[0];
            }
        }
        return $value;
    }
    /**
    * Puts a version number into a standard form
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    public static function formatVersion($value)
    {
        if (empty($value)) {
            $value = "";
        } else if (stripos($value, ".") === false) {
            $version = strtoupper($value);
            $str     = array();
            for ($i = 0; $i < 3; $i++) {
                $str[] .= (int)substr($version, ($i*2), 2);
            }
            $value = implode(".", $str);
        } else {
            $ret = preg_match(
                "/[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}/",
                $value,
                $match
            );
            if ($ret > 0) {
                $ver = explode(".", $match[0]);
                $value = ((int)$ver[0]).".".((int)$ver[1]).".".((int)$ver[2]);
            }
        }
        return $value;
    }

    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/
    /**
    * function to set DeviceID
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setDeviceID($value)
    {
        if (is_int($value)) {
            $value = dechex($value);
        }
        $this->data["DeviceID"] = self::stringSize($value, 6);
    }
    /**
    * function to set DeviceID
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setId($value)
    {
        $this->data["id"] = (int) $value;
    }
    /**
    * function to set DeviceGroup
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setDeviceGroup($value)
    {
        if (is_int($value)) {
            $value = dechex($value);
        }
        $this->data["DeviceGroup"] = self::stringSize($value, 6);
    }
    /**
    * Hexifies a version in x.y.z form.
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setFWVersion($value)
    {
        $this->data["FWVersion"] = self::formatVersion($value);
    }
    /**
    * Sets the part number
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setFWPartNum($value)
    {
        $this->data["FWPartNum"] = self::formatPartNum($value);
    }
    /**
    * Sets the part number
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setHWPartNum($value)
    {
        $this->data["HWPartNum"] = self::formatPartNum($value);
    }

}
?>