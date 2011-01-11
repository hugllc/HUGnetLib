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
class ErrorTable extends HUGnetDBTable
{
    /** @var notice level severity */
    const SEVERITY_NOTICE = 1;
    /** @var warning level severity */
    const SEVERITY_WARNING = 2;
    /** @var error level severity */
    const SEVERITY_ERROR = 4;
    /** @var critical level severity */
    const SEVERITY_CRITICAL = 8;
    /** @var array The names of the error severities */
    static $severityNames = array(
        self::SEVERITY_NOTICE   => "Notice",
        self::SEVERITY_WARNING  => "Warning",
        self::SEVERITY_ERROR    => "Error",
        self::SEVERITY_CRITICAL => "Critical",
    );
    /** @var string This is the table we should use */
    public $sqlTable = "errors";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = "id";
    /** @var bool We can't log errors, because it is circular if it fails */
    protected $logErrors = false;
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
        "class" => array(
            "Name" => "class",
            "Type" => "varchar(128)",
            "Default" => 'Unknown',
        ),
        "method" => array(
            "Name" => "method",
            "Type" => "varchar(128)",
            "Default" => '',
        ),
        "errno" => array(
            "Name" => "errno",
            "Type" => "varchar(32)",
            "Default" => "0",
        ),
        "error" => array(
            "Name" => "error",
            "Type" => "text",
            "Default" => '',
        ),
        "Date" => array(
            "Name" => "Date",
            "Type" => "bigint",
            "Default" => '0',
        ),
        "Severity" => array(
            "Name" => "Severity",
            "Type" => "int",
            "Default" => self::SEVERITY_NOTICE,
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
        "ClassDate" => array(
            "Name" => "ClassDate",
            "Unique" => true,
            "Columns" => array("Date", "class"),
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
    * This function updates the record currently in this table
    *
    * @param bool $replace Replace any records found that collide with this one.
    *
    * @return bool True on success, False on failure
    */
    public function insertRow($replace = false)
    {
        // Can't save errors from me, as that would create an infinite loop
        if ($this->class == get_class($this)) {
            return false;
        }
        return parent::insertRow($replace);
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
        $this->data["Date"] = self::unixDate($value);
    }

}
?>
