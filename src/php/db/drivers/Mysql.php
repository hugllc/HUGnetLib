<?php
/**
 * Sensor driver for light sensors.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsDatabase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
namespace HUGnet\db\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/**
 * This class implements photo sensors.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsDatabase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Mysql extends \HUGnet\db\Driver
{
    /** @var bool Does this driver support auto_increment? */
    protected $AutoIncrement = "AUTO_INCREMENT";

    /**
    * Gets columns from a mysql server
    *
    * @return null
    */
    public function columns()
    {
        $columns = $this->query("SHOW COLUMNS FROM ".$this->table());
        $cols = array();
        if (is_array($columns)) {
            foreach ($columns as $col) {
                // @codeCoverageIgnoreStart
                // This is impossible to test without a mysql server
                $cols[$col['Field']] = array(
                    "Name" => $col['Field'],
                    "Type" => $col['Type'],
                    "Default" => ($col["Default"] == "NULL") ? null:$col["Default"],
                    "Null" => ($col["Null"] == "NO") ? false : true,
                    "Primary" => ($col["Key"] == "PRI"),
                    "Unique" => ($col["Key"] == "UNI"),
                    "AutoIncrement" => !is_bool(
                        stripos($col["Extra"], "auto_increment")
                    ),
                );
                // @codeCoverageIgnoreEnd
            }
        }
        return (array)$cols;

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
    *  $column["Primary"] => bool If we are a primary Key.
    *  $column["Unique"] => boll If we are a unique column.
    *
    * @param string $column array documented above
    *
    * @return null
    */
    protected function columnDef($column)
    {
        $this->query .= "`".$column["Name"]."` ".$column["Type"];
        if (!empty($column["CharSet"])) {
            $this->query .= " CHARACTER SET ".$column["CharSet"];
        }
        if (!empty($column["Collate"])) {
            $this->query .= " COLLATE ".$column["Collate"];
        }
        if ($column["Unsigned"] === true) {
            $this->query .= " UNSIGNED";
        }
        if ($column["AutoIncrement"] === true) {
            $this->query .= " AUTO_INCREMENT PRIMARY KEY";
        } else if ($column["Primary"]) {
            $this->query .= " PRIMARY KEY";
        } else if ($column["Unique"]) {
            $this->query .= " UNIQUE";
        }
        if ($column["Null"] == true) {
            $this->query .= " NULL";
        } else {
            $this->query .= " NOT NULL";
        }
        if (!is_null($column["Default"])) {
            $this->query .= " DEFAULT ".$this->pdo()->quote($column["Default"]);
        }
    }
    /**
    * Checks the database table, repairs and optimizes it
    *
    * @param bool $force Force the repair
    *
    * @return mixed
    */
    public function check($force = false)
    {
        $return = true;
        $error = "";
        $this->lock();
        $ret = $this->query("FLUSH TABLES ".$this->table());
        $ret = $this->query("CHECK TABLE ".$this->table());
        if (($ret[count($ret)-1]["Msg_text"] != "OK") || $force) {
            $ret = $this->query("REPAIR TABLE ".$this->table());
            $errText = $ret[count($ret)-1]["Msg_text"];
            if (($errText != "OK")
                && (strpos(strtolower($errText), "doesn't support repair") === false)
            ) {
                // @codeCoverageIgnoreStart
                // It is impossible to make this run, since it only runs when the
                // table is corrupt and not fixable
                $error = "Table ".$this->table()." is BROKEN (";
                $error .= $ret[count($ret)-1]["Msg_text"].")";
                $return = false;
            }
            // @codeCoverageIgnoreEnd
        }
        $ret = $this->query("OPTIMIZE TABLE ".$this->table());
        $this->unlock();
        // This must be done after the unlock
        if (!empty($error)) {
            // @codeCoverageIgnoreStart
            // It is impossible to make this run, since it only runs when the
            // table is corrupt and not fixable
            $this->logError(-99, $error, ErrorTable::SEVERITY_CRITICAL, "check");
        }
        // @codeCoverageIgnoreEnd
        return $return;
    }
    /**
    * Locks the table
    *
    * @return boolean True on success, false on failure
    */
    public function lock()
    {
        $this->query("LOCK TABLES ".$this->table()." WRITE");
        return true;
    }
    /**
    * Unlocks the table
    *
    * @return mixed
    */
    public function unlock()
    {
        $this->query("UNLOCK TABLES");
        return true;
    }
    /**
    * Get the names of all the tables in the current database
    *
    * @return array of table names
    *
    * @SuppressWarnings(PHPMD.UnusedLocalVariable)
    */
    public function tables()
    {
        $ret = $this->query("SHOW TABLES");
        $return = array();
        foreach ($ret as $t) {
            // @codeCoverageIgnoreStart
            // This is impossible to test without a mysql server
            list($key, $value) = each($t);
            $return[$value] = $value;
            // @codeCoverageIgnoreEnd
        }
        return $return;
    }
    /**
    * This function deals with errors
    *
    * @param array  $errorInfo The output of any of the pdo errorInfo() functions
    * @param string $method    The function or method the error was in
    * @param string $severity  The severity of the error.  This should be fed with
    *                          ErrorTable::SEVERITY_WARNING, et al.
    *
    * @return mixed
    */
    protected function errorHandler($errorInfo, $method, $severity)
    {
        parent::errorHandler($errorInfo, $method, $severity);
        if ($errorInfo[1] == 2006) {
            // The database has gone away, so disconnect.  Reconnection should be
            // automatically handled.
            $this->disconnect();
            $this->connect();
        }
    }

}

?>
