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
/** This is for some constants */
require_once dirname(__FILE__)."/../base/UnitsBase.php";

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
class SensorsTable extends HUGnetDBTable
{
    /** @var string This is the table we should use */
    public $sqlTable = "sensors";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = "";
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
        "dev" => array(
            "Name" => "dev",
            "Type" => "INTEGER",
        ),
        "sensor" => array(
            "Name" => "sensor",
            "Type" => "INTEGER",
        ),
        "id" => array(
            "Name" => "id",
            "Type" => "INTEGER",
        ),
        "type" => array(
            "Name" => "type",
            "Type" => "varchar(128)",
            "Default" => '',
        ),
        "location" => array(
            "Name" => "location",
            "Type" => "varchar(128)",
            "Default" => '',
        ),
        "dataType" => array(
            "Name" => "dataType",
            "Type" => "varchar(32)",
            "Default" => UnitsBase::TYPE_RAW,
        ),
        "units" => array(
            "Name" => "units",
            "Type" => "varchar(32)",
            "Default" => '',
        ),
        "decimals" => array(
            "Name" => "decimals",
            "Type" => "smallint",
            "Default" => '2',
        ),
        "params" => array(
            "Name" => "params",
            "Type" => "longtext",
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
    */
    public $sqlIndexes = array(
        "DevSensor" => array(
            "Name" => "DevSensor",
            "Unique" => true,
            "Columns" => array("dev", "sensor"),
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
    * Checks to see if our deviceID exists in the database
    *
    * @return bool True if it exists, false otherwise
    */
    public function exists()
    {

        $ret = (bool) $this->dbDriver()->countWhere(
            "dev = ? AND sensor = ?", array($this->dev, $this->sensor), "dev"
        );
        $this->dbDriver()->reset();
        return $ret;
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
    protected function setParams($value)
    {
        if (is_array($value)) {
            $this->data["params"] = json_encode($value);
        } else if (is_string($value)) {
            $this->data["params"] = $value;
        }
    }
    /**
    * function to set LastHistory
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setDataType($value)
    {
        if (($value == UnitsBase::TYPE_RAW)
            || ($value == UnitsBase::TYPE_DIFF)
            || ($value == UnitsBase::TYPE_IGNORE)
        ) {
            $this->data["dataType"] = $value;
        }
    }

}
?>
