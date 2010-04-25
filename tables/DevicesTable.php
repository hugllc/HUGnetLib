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
require_once dirname(__FILE__)."/../base/HUGnetDBTable.php";
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
class DevicesTable extends HUGnetDBTable
{
    /** @var string This is the table we should use */
    public $sqlTable = "devices";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = "DeviceKey";
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
        "DeviceKey" => array(
            "Name" => "DeviceKey",
            "Type" => "INTEGER",
            "AutoIncrement" => true,
            "Primary" => true,
        ),
        "DeviceID" => array(
            "Name" => "DeviceID",
            "Type" => "varchar(6)",
            "Default" => '',
        ),
        "DeviceName" => array(
            "Name" => "DeviceName",
            "Type" => "varchar(128)",
            "Default" => '',
        ),
        "SerialNum" => array(
            "Name" => "SerialNum",
            "Type" => "bigint(20)",
            "Default" => 0,
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
            "Default" => '',
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
        "BoredomThreshold" => array(
            "Name" => "BoredomThreshold",
            "Type" => "tinyint(4)",
            "Default" => 0,
        ),
        "LastConfig" => array(
            "Name" => "LastConfig",
            "Type" => "datetime",
            "Default" => '0000-00-00 00:00:00',
        ),
        "LastPoll" => array(
            "Name" => "LastPoll",
            "Type" => "datetime",
            "Default" => '0000-00-00 00:00:00',
        ),
        "LastHistory" => array(
            "Name" => "LastHistory",
            "Type" => "datetime",
            "Default" => '0000-00-00 00:00:00',
        ),
        "LastAnalysis" => array(
            "Name" => "LastAnalysis",
            "Type" => "datetime",
            "Default" => '0000-00-00 00:00:00',
        ),
        "MinAverage" => array(
            "Name" => "MinAverage",
            "Type" => "varchar(16)",
            "Default" => '15MIN',
        ),
        "CurrentGatewayKey" => array(
            "Name" => "CurrentGatewayKey",
            "Type" => "int(11)",
            "Default" => '0',
        ),
        "params" => array(
            "Name" => "params",
            "Type" => "text",
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
        "SerialNum" => array(
            "Name" => "SerialNum",
            "Unique" => true,
            "Columns" => array("SerialNum"),
        ),
        "DeviceID" => array(
            "Name" => "DeviceID",
            "Unique" => true,
            "Columns" => array("DeviceID", "GatewayKey"),
        ),
    );

    /** @var object This is where we store our sqlDriver */
    protected $myDriver = null;
    /** @var object This is where we store our configuration object */
    protected $myConfig = null;
    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
    );
    /** @var array This is where the data is stored */
    protected $data = array();


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
    * function to set LastHistory
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setLastHistory($value)
    {
        $this->data["LastHistory"] = $this->sqlDate($value);
    }
    /**
    * function to set LastHistory
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setLastConfig($value)
    {
        $this->data["LastConfig"] = $this->sqlDate($value);
    }
    /**
    * function to set LastHistory
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setLastPoll($value)
    {
        $this->data["LastPoll"] = $this->sqlDate($value);
    }
    /**
    * function to set LastHistory
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setLastAnalysis($value)
    {
        $this->data["LastAnalysis"] = $this->sqlDate($value);
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
        if (stripos($value, ".") === false) {
            $version = strtoupper($value);
            $str     = array();
            for ($i = 0; $i < 3; $i++) {
                $str[] .= (int)substr($version, ($i*2), 2);
            }
            $value = implode(".", $str);
        }
        $this->data["FWVersion"] = $value;
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
        $this->data["FWPartNum"] = $this->formatPartNum($value);
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
        $this->data["HWPartNum"] = $this->formatPartNum($value);
    }
    /**
    * Changes the part number into XXXX-XX-XX-X form.
    *
    * @param mixed $value The value to set
    *
    * @return string PartNumber in ASCII hex
    */
    protected static function formatPartNum($value)
    {
        if (stripos($value, "-") === false) {
            $PartNum = strtoupper($value);
            $str     = array();
            $str[]   = substr($PartNum, 0, 4);
            $str[]   = substr($PartNum, 4, 2);
            $str[]   = substr($PartNum, 6, 2);
            $str[]   = chr(hexdec(substr($PartNum, 8, 2)));
            $value = implode("-", $str);
        }
        return $value;
    }

}
?>
