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
/** The data channels driver is necessary for a couple of constants */
require_once dirname(__FILE__)."/../TableParams.php";
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
class DevicePowers extends \HUGnet\db\TableParams
    implements \HUGnet\interfaces\DBTable
{
    /** @var string This is the table we should use */
    public $sqlTable = "devicePowers";
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
        "dev" => array(
            "Name" => "dev",
            "Type" => "int",
        ),
        "power" => array(
            "Name" => "power",
            "Type" => "int",
        ),
        "id" => array(
            "Name" => "id",
            "Type" => "int",
            "Default" => 0xFF,
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
        "driver" => array(
            "Name" => "driver",
            "Type" => "varchar(32)",
            "Default" => 'EmptyPower',
        ),
        "tableEntry" => array(
            "Name" => "tableEntry",
            "Type" => "text",
            "Default" => "",
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
        "DevPower" => array(
            "Name" => "DevPower",
            "Unique" => true,
            "Columns" => array(0 => "dev", 1 => "power"),
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
    );
    /** @var array These are reserved names that shouldn't be set */
    protected $setParams = array(
        "RawSetup", "extra", "lastTable",
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
    * Checks to see if our deviceID exists in the database
    *
    * @return bool True if it exists, false otherwise
    */
    public function exists()
    {

        $ret = (bool) $this->dbDriver()->countWhere(
            "dev = ? AND power = ?",
            array($this->get("dev"), $this->get("power")), "dev"
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
    * function to set dev
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setDev($value)
    {
        $this->data["dev"] = (int) $value;
    }
    /**
    * function to set power
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setPower($value)
    {
        $this->data["power"] = (int) $value;
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
        $this->data["id"] = (int) $value;
    }
    /**
    * function to set tableEntry
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setTableEntry($value)
    {
        if (is_array($value)) {
            $this->data["tableEntry"] = json_encode($value);
        } else if (is_string($value)) {
            $this->data["tableEntry"] = $value;
        }
    }
}
?>
