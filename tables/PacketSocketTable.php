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
class PacketSocketTable extends HUGnetDBTable
{
    /** @var string This is the table we should use */
    public $sqlTable = "PacketSocket";
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
        ),
        "Date" => array(
            "Name" => "Date",
            "Type" => "datetime",
            "Default" => "0000-00-00 00:00:00",
        ),
        "Command" => array(
            "Name" => "Command",
            "Type" => "varchar(2)",
        ),
        "PacketFrom" => array(
            "Name" => "PacketFrom",
            "Type" => "varchar(6)",
            "Default" => "000000",
        ),
        "PacketTo" => array(
            "Name" => "PacketTo",
            "Type" => "varchar(6)",
        ),
        "RawData" => array(
            "Name" => "RawData",
            "Type" => "text",
            "Default" => "",
        ),
        "Type" => array(
            "Name" => "Type",
            "Type" => "varchar(32)",
            "Default" => "UNSOLICITED",
        ),
        "ReplyTime" => array(
            "Name" => "ReplyTime",
            "Type" => "float",
            "Default" => 0,
        ),
        "Checked" => array(
            "Name" => "Checked",
            "Type" => "int(11)",
            "Default" => 0,
        ),
        "Timeout" => array(
            "Name" => "Timeout",
            "Type" => "int(11)",
            "Default" => 0,
        ),
        "PacketTime" => array(
            "Name" => "PacketTime",
            "Type" => "float",
            "Default" => 0.0,
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
        "PRIMARY" => array(
            "Name" => "PRIMARY",
            "Unique" => true,
            "Columns" => array(
                "PacketFrom", "Date", "Command", "PacketTo", "id", "PacketTime"
            ),
        ),
    );

    /** @var object This is where we store our sqlDriver */
    protected $myDriver = null;
    /** @var object This is where we store our configuration object */
    protected $myConfig = null;
    /** @var array This is the default values for the data */
    protected $default = array(
        "TimeoutPeriod" => 5,    // The timeout period of this packet
        "group" => "default",    // Server group to use
    );
    /** @var array This is where the data is stored */
    protected $data = array();
    /** @var array This is our standard order by clause */
    public $sqlOrderBy = "PacketTime ASC";
    /** @var array This is our standard order by clause */
    public $senderID = 0;


    /**
    * Creates the object from a string
    *
    * @param string &$pkt This is the raw string for the device
    *
    * @return null
    */
    public function fromPacket(PacketContainer &$pkt)
    {
        if ($pkt->isEmpty()) {
            return;
        }
        $this->Date = $pkt->Date;
        $this->Command = $pkt->Command;
        $this->PacketFrom = $pkt->From;
        $this->PacketTo = $pkt->To;
        $this->RawData = $pkt->Data;
        $this->Type = $pkt->Type;
        $this->ReplyTime = $pkt->replyTime();
        $this->TimeoutPeriod = $pkt->Timeout;
        $this->id = null;
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
        if (is_object($data) && (get_class($data) == "PacketContainer")) {
            $this->fromPacket($data);
        } else {
            parent::fromAny($data);
        }
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
        // exit if we are empty or timed out
        if ($this->isEmpty()) {
            return false;
        }
        // Set the timeout
        $this->Timeout = time() + $this->TimeoutPeriod;
        // Set the packettime
        $this->_packetTime();
        // Set our ID here
        $this->id = $this->senderID;
        // Insert the row and return
        return parent::insertRow($replace);
    }
    /**
    * This function updates the record currently in this table
    *
    * @return bool True on success, False on failure
    */
    public function deleteOld()
    {
        return $this->myDriver->deleteWhere("`Timeout` < ?", time());
    }
    /**
    * Gets the current time
    *
    * @return float The current time in seconds
    */
    public function &getNextPacket()
    {
        static $lastRead;
        if (empty($lastRead)) {
            $lastRead = (float)time();
        }
        $this->clearData();
        $this->selectInto(
            "`PacketTime` > ? AND `id` <> ?",
            array($lastRead, $this->senderID)
        );
        if ($this->isEmpty()) {
            return false;
        } else {
            // Set the last read
            $lastRead = $this->PacketTime;
        }
        return $this;
    }
    /**
    * Gets the current time
    *
    * @return float The current time in seconds
    */
    private function _packetTime()
    {
        list($usec, $sec) = explode(" ", microtime());
        $this->PacketTime = ((float)$usec + (float)$sec);
    }
}
?>
