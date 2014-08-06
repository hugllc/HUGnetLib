<?php
/**
 * Sensor driver for light sensors.
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsDatabase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
namespace HUGnet\db\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is for the base class */
require_once dirname(__FILE__)."/../../interfaces/DBDriver.php";
/**
 * This class implements photo sensors.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsDatabase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Mysql extends \HUGnet\db\Driver implements \HUGnet\interfaces\DBDriver
{
    /** @var bool Does this driver support auto_increment? */
    protected $AutoIncrement = "AUTO_INCREMENT";

    /**
    * Gets columns from a mysql server
    *
    * @return null
    */
    protected function driverColumns()
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
        $this->system->out("Checking table ".$this->table(), 1);
        $ret = $this->query("CHECK TABLE ".$this->table());
        if (($ret[count($ret)-1]["Msg_text"] != "OK") || $force) {
            $this->system->out("Repairing table ".$this->table(), 1);
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
        $this->system->out("Optimizing table ".$this->table(), 1);
        $ret = $this->query("OPTIMIZE TABLE ".$this->table());
        $this->unlock();
        // This must be done after the unlock
        if (!empty($error)) {
            // @codeCoverageIgnoreStart
            // It is impossible to make this run, since it only runs when the
            // table is corrupt and not fixable
            $this->system->out($error, 1);
            $this->logError(-99, $error, ErrorTable::SEVERITY_CRITICAL, "check");
        }
        // @codeCoverageIgnoreEnd
        $this->system->out("Done", 1);
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
    /**
    * Times out long running select queriess
    * 
    * @param int $timeout The timeout period to use
    *
    * @return int Count of the number of processes killed
    */
    public function selectTimeout($timeout = 120)
    {
        $ret = $this->query("SHOW FULL PROCESSLIST");
        $count = 0;
        foreach ($ret as $row => $field) {
            // Kill select queries only
            if (strtolower(substr(trim($field['Info']), 6)) != 'select') {
                continue;
            }
            // Kill anything running longer than our timeout
            if ($field['Time'] > $timeout) {
                $this->query("KILL ".$field['Id']);
                $this->logError(
                    -10, 
                    "Killed mysql process ".$field['Id']
                    ." after ".$field['Time']." s", 
                    ErrorTable::SEVERITY_WARNING, 
                    "timeout"
                );
                $count++;
            } 
        }
        return $count;
    }
    /**
    * Gets columns from a mysql server
    *
    * @return null
    */
    public function indexes()
    {
        $indexes = $this->query("SHOW INDEXES FROM ".$this->table());
        $inds    = array();
        if (is_array($indexes)) {
            foreach ($indexes as $ind) {
                // @codeCoverageIgnoreStart
                // This is impossible to test without a mysql server
                $name = $ind["Key_name"];
                if ($name !== "PRIMARY") {
                    $seq = $ind["Seq_in_index"] - 1;
                    if (!is_array($inds[$name])) {
                        $inds[$name] = array(
                            "Name" => $name,
                            "Unique" => !(bool)$ind["Non_unique"],
                            "Columns" => array($seq => $ind["Column_name"]),
                        );
                    } else {
                        $inds[$name]["Columns"][$seq] = $ind["Column_name"];
                    }
                }
                // @codeCoverageIgnoreEnd
            }
        }
        return (array)$inds;

    }

}

?>
