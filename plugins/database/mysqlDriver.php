<?php
/**
 * Sensor driver for light sensors.
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** Get the required base class */
require_once dirname(__FILE__)."/../../base/HUGnetDBDriver.php";
/**
* This class implements photo sensors.
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Units
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2010 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class mysqlDriver extends HUGnetDBDriver
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name"  => "mysql",
        "Type"  => "database",
        "Class" => "mysqlDriver",
    );
    /**
    * Gets the instance of the class and
    *
    * @param object $table The table to attach myself to
    * @param object $pdo   The database object
    *
    * @return null
    */
    static public function &singleton(&$table, PDO &$pdo)
    {
        static $instance;
        if (empty($instance)) {
            $class = __CLASS__;
            $instance = new $class();
        }
        $instance->myTable = &$table;
        $instance->pdo = &$pdo;
        return $instance;
    }

    /**
    * Gets columns from a mysql server
    *
    * @return null
    */
    protected function columns()
    {
        $this->query = "SHOW COLUMNS FROM ".$this->table();
        $columns = $this->query();
        foreach ((array)$columns as $col) {
            // @codeCoverageIgnoreStart
            // This is impossible to test without a mysql server
            if (stripos($col["Extra"], "auto_increment") !== false) {
                $AutoIncrement = true;
            } else {
                $AutoIncrement = false;
            }
            if ($col["Key"] == "PRI") {
                $Key = "PRIMARY";
            } else if ($col["Key"] == "UNI") {
                $Key = "UNIQUE";
            } else {
                unset($Key);
            }

            $this->fields[$col['Field']] = array(
                "Type" => $col['Type'],
                "Null" => ($col["Null"] == "NO") ? false : true,
                "Default" => ($col["Default"] == "NULL") ? null : $col["Default"],
                "Key" => $Key,
                "AutoIncrement" => "AutoIncrement"
            );
            // @codeCoverageIgnoreEnd
        }

    }
    /**
    *  Adds a field to the devices table for cache information
    *
    *  The column parameter is defined as follows:
    *  $column["Name"] => string The name of the column
    *  $column["Type"] => string The type of the column
    *  $column["Default"] => mixed The default value for the column
    *  $column["Null"] => bool true if null is allowed, false otherwise
    *  $column["AutoIncrement"] => bool true if the column is auto_increment
    *  $column["CharSet"] => string the character set if the column is text or char
    *  $column["Collate"] => string colation if the table is text or char
    *  $column["Unsigned"] => bool For int and float types.
    *  $column["Key"] => string If defined it is the key type: UNIQUE or PRIMARY
    *
    * @param string $name    The name of the field
    * @param string $type    The type of field to add
    * @param mixed  $default The default value for the field
    * @param bool   $null    Whether null is a valid value for the field
    *
    * @return null
    */
    protected function columnDef($column)
    {
        $this->query .= "`".$column["Name"]."` ".$column["Type"]." ";
        if ((stripos($column["Type"], "TEXT") !== false)
            || (stripos($column["Type"], "CHAR") !== false)
            || (stripos($column["Type"], "ENUM") !== false)
            || (stripos($column["Type"], "SET") !== false)
        ) {
            if (!empty($column["CharSet"])) {
                $this->query .= " CHARACTER SET ".$column["CharSet"]." ";
            }
            if (!empty($column["Collate"])) {
                $this->query .= " COLLATE ".$column["Collate"]." ";
            }
            if (is_string($column["Key"])) {
                $this->query .= " ".strtoupper($column["Key"])." KEY ";
            }
        } else if ((stripos($column["Type"], "INT") !== false)
            || (stripos($column["Type"], "REAL") !== false)
            || (stripos($column["Type"], "DOUBLE") !== false)
            || (stripos($column["Type"], "FLOAT") !== false)
            || (stripos($column["Type"], "DECIMAL") !== false)
            || (stripos($column["Type"], "NUMERIC") !== false)
        ) {
            if ($column["Unsigned"] === true) {
                $this->query .= " UNSIGNED ";
            }
            if (($column["AutoIncrement"] === true) && $this->autoIncrement) {
                $this->query .= " AUTO_INCREMENT PRIMARY KEY";
            } else if (is_string($column["Key"])) {
                $this->query .= " ".strtoupper($column["Key"])." KEY ";
            }
        }
        if ($column["Null"] == true) {
            $this->query .= " NULL ";
        } else {
            $this->query .= " NOT NULL ";
        }
        if (!is_null($column["Default"])) {
            $this->query .= " DEFAULT '".$column["Default"]."'";
        }
    }


}

?>
