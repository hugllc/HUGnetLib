<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
namespace HUGnet\db\tables;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our system interface */
require_once dirname(__FILE__)."/../../interfaces/DBTable.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DataCollectors extends \HUGnet\db\Table
    implements \HUGnet\interfaces\DBTable
{
    /** @var string This is the table we should use */
    public $sqlTable = "datacollectors";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = "uuid";
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
        "uuid" => array(
            "Name" => "uuid",
            "Type" => "varchar(36)",
            "Null" => false,
        ),
        "GatewayKey" => array(
            "Name" => "GatewayKey",
            "Type" => "int",
            "Null" => false,
        ),
        "name" => array(
            "Name" => "name",
            "Type" => "varchar(128)",
            "Default" => 'Unknown',
        ),
        "ip" => array(
            "Name" => "ip",
            "Type" => "varchar(128)",
            "Default" => '',
        ),
        "LastContact" => array(
            "Name" => "LastContact",
            "Type" => "bigint",
            "Default" => "0",
        ),
        "SetupString" => array(
            "Name" => "SetupString",
            "Type" => "varchar(255)",
            "Default" => "",
        ),
        "Config" => array(
            "Name" => "Config",
            "Type" => "longtext",
            "Default" => "",
        ),
        "Runtime" => array(
            "Name" => "Runtime",
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
        "ipGatewayKey" => array(
            "Name" => "ipGatewayKey",
            "Unique" => true,
            "Columns" => array(0 => "GatewayKey", 1 => "ip"),
        ),
        "uuid" => array(
            "Name" => "uuid",
            "Unique" => true,
            "Columns" => array("uuid"),
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
    );
    /**
    * This is the constructor
    *
    * @param object &$system  The system object to use
    * @param mixed  $data     This is an array or string to create the object from
    * @param object &$connect The connection manager
    */
    protected function __construct(&$system, $data="", &$connect = null)
    {
        parent::__construct($system, $data, $connect);
        $this->create();
    }
    /**
    * returns an object with the controller of this device in it
    *
    * @param int $GatewayKey The gateway ID to find
    *
    * @return bool
    */
    public function &onGateway($GatewayKey)
    {
        return $this->selectInto(
            "`GatewayKey` = ?",
            array($GatewayKey)
        );
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param DeviceContainer &$dev The device container to use
    *
    * @return null
    */
    public function fromDevice(&$dev)
    {
        $this->set("uuid", $dev->get("DeviceName"));
        $this->set("GatewayKey", $dev->get("GatewayKey"));
        $this->set("ip", $dev->get("DeviceLocation"));
        $this->set("SetupString", $dev->encode());
        $this->set("Config", json_encode($this->system()->config()));
    }
    /**
    * Creates the object from a string or array
    *
    * @param mixed $data This is whatever you want to give the class
    *
    * @return null
    */
    public function fromAny($data)
    {
        if (is_object($data) && is_a($data, "HUGnet\Device")) {
            $this->fromDevice($data);
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
    * function to set LastHistory
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setLastContact($value)
    {
        $this->data["LastContact"] = self::unixDate($value);
    }
}
?>
