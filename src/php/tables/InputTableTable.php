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
require_once dirname(__FILE__)."/../units/Driver.php";

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
class InputTableTable extends HUGnetDBTable
{
    /** @var string This is the table we should use */
    public $sqlTable = "inputTable";
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
            "Default" => \HUGnet\units\Driver::TYPE_RAW,
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
        "driver" => array(
            "Name" => "driver",
            "Type" => "varchar(32)",
            "Default" => 'SDEFAULT',
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
    /** @var array These are reserved names that shouldn't be set */
    private $_set = array(
        "min",
        "max",
        "extra",
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
    * Overload the set attribute
    *
    * @param string $name  This is the attribute to set
    * @param mixed  $value The value to set it to
    *
    * @return mixed The value of the attribute
    */
    public function set($name, $value)
    {
        if (array_key_exists($name, $this->default)) {
            $ret = parent::set($name, $value);
        } else {
            if (in_array($name, $this->_set)) {
                $array = (array)json_decode(parent::get("params"), true);
                $array[$name] = $value;
                parent::set("params", $array);
            }
        }
        return $ret;
    }
    /**
    * Overload the get attribute
    *
    * @param string $name This is the attribute to get
    *
    * @return mixed The value of the attribute
    */
    public function get($name)
    {
        $ret = parent::get($name);
        if (is_null($ret)) {
            $array = (array)json_decode(parent::get("params"), true);
            $ret = $array[$name];
        }
        return $ret;
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
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($array)
    {
        parent::fromArray($array);
        if (!isset($array["params"]) || !is_string($array["params"])) {
            foreach ($this->getProperties() as $key) {
                unset($array[$key]);
            }
            $set = array();
            foreach ($this->_set as $key) {
                if (isset($array[$key])) {
                    $set[$key] = $array[$key];
                }
            }
            $this->set("params", $set);
        }
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toArray($default = true)
    {
        $data = parent::toArray($default);
        $params = json_decode($data["params"], true);
        unset($data["params"]);
        return array_merge((array)$params, (array)$data);
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
        if (($value == \HUGnet\units\Driver::TYPE_RAW)
            || ($value == \HUGnet\units\Driver::TYPE_DIFF)
            || ($value == \HUGnet\units\Driver::TYPE_IGNORE)
        ) {
            $this->data["dataType"] = $value;
        }
    }

}
?>
