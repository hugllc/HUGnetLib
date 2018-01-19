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
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Fixtures extends \HUGnet\db\Table
    implements \HUGnet\interfaces\DBTable
{
    /** This is the maximum our SN can be */
    const MAX_GROUP_SN = 0xFFFFFF;
    /** This is the minimum our SN can be */
    const MIN_GROUP_SN = 0xFF0000;
    /** This is the maximum our SN can be */
    const MAX_SCRIPT_SN = 0xFEFFFF;
    /** This is the minimum our SN can be */
    const MIN_SCRIPT_SN = 0xFE0000;
    /** This is the maximum our SN can be */
    const MAX_TEMP_SN = 0xFDFFFF;
    /** This is the minimum our SN can be */
    const MIN_TEMP_SN = 0xFD0000;
    /** This is the minimum our SN can be */
    const MAX_VIRTUAL_SN = 0xFCFFFF;
    /** This is the minimum our SN can be */
    const MIN_VIRTUAL_SN = 0xFC0000;

    /** @var string This is the table we should use */
    public $sqlTable = "fixtures";
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
            "AutoIncrement" => true,
            "Primary" => true,
        ),
        "dev" => array(
            "Name" => "dev",
            "Type" => "int",
            "Null" => false,
        ),
        "fixture" => array(
            "Name" => "fixture",
            "Type" => "longtext",
            "Default" => '',
        ),
        "created" => array(
            "Name" => "created",
            "Type" => "bigint",
            "Null" => false,
        ),
        "modified" => array(
            "Name" => "modified",
            "Type" => "bigint",
            "Null" => false,
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
    * @param object &$system  The system object to use
    * @param mixed  $data     This is an array or string to create the object from
    * @param object &$connect The connection manager
    */
    protected function __construct(&$system, $data="", &$connect = null)
    {
        parent::__construct($system, $data, $connect);
        $this->create();
    }

    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/
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
    * function to set the fixture
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setFixture($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        if (is_string($value)) {
            $this->data["fixture"] = $value;
        }
    }
}
?>
