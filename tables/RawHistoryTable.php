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
require_once dirname(__FILE__)."/../containers/DeviceContainer.php";
require_once dirname(__FILE__)."/../containers/PacketContainer.php";
require_once dirname(__FILE__)."/DevicesHistoryTable.php";

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
class RawHistoryTable extends HUGnetDBTable
{
    /** @var notice level severity */
    const SEVERITY_NOTICE = 1;
    /** @var warning level severity */
    const SEVERITY_WARNING = 2;
    /** @var error level severity */
    const SEVERITY_ERROR = 4;
    /** @var critical level severity */
    const SEVERITY_CRITICAL = 8;
    /** @var string This is the table we should use */
    public $sqlTable = "rawHistory";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = null;
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
            "Type" => "int",
        ),
        "Date" => array(
            "Name" => "Date",
            "Type" => "bigint",
            "Default" => '0',
        ),
        "packet" => array(
            "Name" => "packet",
            "Type" => "longblob",
            "Default" => "",
        ),
        "deviceHistoryID" => array(
            "Name" => "deviceHistoryID",
            "Type" => "int",
            "Default" => "",
        ),
        "command" => array(
            "Name" => "command",
            "Type" => "char(2)",
            "Default" => "",
        ),
        "dataIndex" => array(
            "Name" => "dataIndex",
            "Type" => "tinyint",
            "Default" => 0,
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
        "DateIDIndex" => array(
            "Name" => "DateIDIndex",
            "Unique" => true,
            "Columns" => array("Date", "id", "dataIndex", "command"),
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
    );
    /** @var This is the packet */
    public $packet = null;
    /** @var This is the device history container*/
    public $devHist = null;

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
    * Inserts a record into the database if it isn't there already
    *
    * @param mixed $data The string or data to use to insert this row
    *
    * @return null
    */
    static public function insertRecord($data)
    {
        $hist = new RawHistoryTable($data);
        return $hist->insertRow();
    }
    /**
    * Returns an array with only the values the database cares about
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toDB($default = true)
    {
        foreach ((array)$this->sqlColumns as $col) {
            if (is_object($this->data[$col["Name"]])) {
                $array[$col["Name"]] = $this->data[$col["Name"]]->toZip();
            } else {
                $array[$col["Name"]] = $this->data[$col["Name"]];
            }
        }
        return (array)$array;
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
        if (empty($this->deviceHistoryID) && isset($array["device"])) {
            if (is_object($array["device"])) {
                $dev = &$array["device"];
            } else {
                $dev = new DeviceContainer($array["device"]);
            }
            $this->devHist = new DevicesHistoryTable($dev);
            $this->devHist->insertRow();
            $this->deviceHistoryID = $this->devHist->id;
        }
        $this->_setupClasses();
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @return null
    */
    private function _setupClasses()
    {
        if (!is_object($this->data["packet"])) {
            // Do the sensors
            $this->data["packet"] = new PacketContainer($this->packet);
            $this->packet = &$this->data["packet"];
        }
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
        $this->data["Date"] = $this->unixDate($value);
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
