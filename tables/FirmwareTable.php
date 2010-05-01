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
require_once dirname(__FILE__)."/../containers/DeviceParamsContainer.php";
require_once dirname(__FILE__)."/../containers/DeviceSensorsContainer.php";

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
class FirmwareTable extends HUGnetDBTable
{
    /** @var string This is the table we should use */
    public $sqlTable = "firmware";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = "FirmwareKey";
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
        "FirmwareKey" => array(
            "Name" => "FirmwareKey",
            "Type" => "INTEGER",
            "AutoIncrement" => true,
            "Primary" => true,
        ),
        "FirmwareVersion" => array(
            "Name" => "FirmwareVersion",
            "Type" => "varchar(8)",
            "Default" => '',
        ),
        "FirmwareCode" => array(
            "Name" => "FirmwareCode",
            "Type" => "longtext",
            "Default" => '',
        ),
        "FirmwareData" => array(
            "Name" => "FirmwareData",
            "Type" => "longtext",
            "Default" => '',
        ),
        "FWPartNum" => array(
            "Name" => "FWPartNum",
            "Type" => "varchar(12)",
            "Default" => '',
        ),
        "HWPartNum" => array(
            "Name" => "HWPartNum",
            "Type" => "varchar(12)",
            "Default" => '',
        ),
        "Date" => array(
            "Name" => "Date",
            "Type" => "datetime",
            "Default" => '1970-01-01 00:00:00',
        ),
        "FirmwareFileType" => array(
            "Name" => "FirmwareFileType",
            "Type" => "varchar(4)",
            "Default" => 'SREC',
        ),
        "FirmwareStatus" => array(
            "Name" => "FirmwareStatus",
            "Type" => "varchar(8)",
            "Default" => 'DEV',
        ),
        "FirmwareTag" => array(
            "Name" => "FirmwareTag",
            "Type" => "varchar(64)",
            "Default" => '',
        ),
        "Target" => array(
            "Name" => "Target",
            "Type" => "varchar(16)",
            "Default" => '',
        ),
        "FirmwareActive" => array(
            "Name" => "FirmwareActive",
            "Type" => "tinyint(4)",
            "Default" => 1,
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
    );

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
    * function to set LastHistory
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setDate($value)
    {
        $this->data["Date"] = $this->sqlDate($value);
    }

}
?>
