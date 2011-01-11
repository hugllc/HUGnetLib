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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PacketLogTable extends HUGnetDBTable
{
    /** @var string This is the table we should use */
    public $sqlTable = "PacketLog";
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
        "DeviceKey" => array(
            "Name" => "DeviceKey",
            "Type" => "int(11)",
            "Default" => 0,
        ),
        "GatewayKey" => array(
            "Name" => "GatewayKey",
            "Type" => "int(11)",
            "Default" => 0,
        ),
        "Date" => array(
            "Name" => "Date",
            "Type" => "bigint",
            "Default" => 0,
        ),
        "Command" => array(
            "Name" => "Command",
            "Type" => "varchar(2)",
        ),
        "sentCommand" => array(
            "Name" => "sentCommand",
            "Type" => "varchar(2)",
        ),
        "PacketFrom" => array(
            "Name" => "PacketFrom",
            "Type" => "varchar(6)",
        ),
        "RawData" => array(
            "Name" => "RawData",
            "Type" => "text",
            "Default" => "",
        ),
        "sentRawData" => array(
            "Name" => "sentRawData",
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
                "PacketFrom", "GatewayKey", "Date", "Command", "sentCommand"
            ),
        ),
        "DeviceKey" => array(
            "Name" => "DeviceKey",
            "Unique" => false,
            "Columns" => array("DeviceKey", "Date"),
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


    /**
    * Creates the object from a string
    *
    * @param string &$pkt This is the raw string for the device
    *
    * @return null
    */
    public function fromReply(&$pkt)
    {
        if (!is_object($pkt->Reply)) {
            $this->fromPacket($pkt);
            return;
        }
        $this->Date = $pkt->Reply->Date;
        $this->Command = $pkt->Reply->Command;
        $this->sentCommand = $pkt->Command;
        $this->PacketFrom = $pkt->Reply->From;
        $this->RawData = $pkt->Reply->Data;
        $this->sentRawData = $pkt->Data;
        $this->Type = $pkt->Type;
        $this->ReplyTime = $pkt->replyTime();
    }
    /**
    * Creates the object from a string
    *
    * @param string &$pkt This is the raw string for the device
    *
    * @return null
    * @See PacketLog::default for documentaion on the packet structure
    */
    public function fromPacket(&$pkt)
    {
        $this->Date = $pkt->Date;
        $this->Command = $pkt->Command;
        $this->PacketFrom = $pkt->From;
        $this->RawData = $pkt->Data;
        $this->Type = $pkt->Type;
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
            $this->fromReply($data);
        } else {
            parent::fromAny($data);
        }
    }

}
?>
