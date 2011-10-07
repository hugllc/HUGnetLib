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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class RawHistoryOldTable extends HUGnetDBTable
{
    /** @var string This is the table we should use */
    public $sqlTable = "history_raw";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = null;
    /** @var string This is the date field for the table.  Leave blank if none  */
    public $dateField = "Date";
    /** @var string The orderby clause for this table */
    public $sqlOrderBy = "Date asc";
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
            "Columns" => array("DeviceKey"),
        ),
    );


    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "old",    // Server group to use
    );
    /** @var This is the packet */
    protected $packet = null;
    /** @var This is the device container*/
    protected $device = null;

    /**
    * This is the constructor
    *
    * @param mixed $data This is an array or string to create the object from
    */
    function __construct($data="")
    {
        parent::__construct($data);
        $this->sqlColumns = $this->myDriver->columns();
        $this->setupColsDefault();
        $this->device = new DeviceContainer();
        $this->packet = new PacketContainer();
    }
    /**
    * Returns a new raw history record
    *
    * @param string $group The group to use for the new data
    *
    * @return null
    */
    public function &toRaw($group = "default")
    {
        $this->device->clearData();
        $this->device->fromSetupString($this->RawSetup);
        if ($this->device->isEmpty() || ($this->device->id > 0x500)) {
            return false;
        }
        $time = $this->unixDate($this->Date, "UTC");
        $this->packet->fromArray(
            array(
                "To" =>  $this->device->DeviceID,
                "Command" => $this->sendCommand,
                "Time" => $time - $this->ReplyTime,
                "Date" => $time - $this->ReplyTime,
                "Reply" => new PacketContainer(
                    array(
                        "From" => $this->device->DeviceID,
                        "Command" => PacketContainer::COMMAND_REPLY,
                        "Data" => $this->RawData,
                        "Length" => strlen($this->RawData)/2,
                        "Time" => $time,
                        "Date" => $time,
                    )
                ),
            )
        );
        $new = new RawHistoryTable(
            array(
                "group" => $group,
                "id" => $this->device->id,
                "Date" => $time,
                "packet" => $this->packet,
                "device" => $this->device,
                "command" => $this->sendCommand,
                "dataIndex" => $this->device->dataIndex($this->RawData),
            )
        );
        return $new;
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
        $this->data["Date"] = self::sqlDate($value, "UTC");
    }
    /**
    * function to set id
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setDeviceKey($value)
    {
        $this->data["DeviceKey"] = (int)$value;
    }

}
?>
