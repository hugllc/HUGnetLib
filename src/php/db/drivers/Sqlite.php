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
class Sqlite extends \HUGnet\db\Driver implements \HUGnet\interfaces\DBDriver
{

    /**
    * Gets columns from a SQLite server
    *
    * @return null
    */
    protected function driverColumns()
    {
        $columns = $this->query("PRAGMA table_info(".$this->table().")");
        $cols = array();
        if (is_array($columns)) {
            foreach ($columns as $col) {
                $cols[$col["name"]] = array(
                    "Name" => $col["name"],
                    "Type" => $col["type"],
                    "Default" => $col["dflt_value"],
                    "Null" => !(bool)$col["notnull"],
                );
            }
        }
        return (array)$cols;
    }
    /**
    * Checks the database table, repairs and optimizes it
    *
    * @param bool $force Force the repair
    *
    * @return mixed
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function check($force = false)
    {
        return true;
    }
    /**
    * Locks the table
    *
    * @return mixed
    */
    public function lock()
    {
        return true;
    }
    /**
    * Unlocks the table
    *
    * @return mixed
    */
    public function unlock()
    {
        return true;
    }
    /**
    *  This database doesn't support this
    *
    * @param array $column @See columnDef for format
    *
    * @return null
    */
    public function modifyColumn($column)
    {
    }
    /**
    *  This database doesn't support this
    *
    * @param string $column The column to drop.
    *
    * @return null
    */
    public function removeColumn($column)
    {
    }
    /**
    *  This database doesn't support this
    *
    * @param string $name The name of the index to remove
    *
    * @return null
    */
    public function removeIndex($name)
    {
    }
   /**
    * Get the names of all the tables in the current database
    *
    * @return array of table names
    */
    public function tables()
    {
        $ret = $this->query("SELECT * FROM SQLITE_MASTER");
        $return = array();
        foreach ((array)$ret as $t) {
            if (strtolower(substr($t["name"], 0, 7)) === "sqlite_") {
                continue;
            }
            $return[$t["name"]] = $t["name"];
        }
        return $return;
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
        return 0;
    }
    /**
    * Gets indexes from a SQLite server
    *
    * @return null
    */
    public function indexes()
    {
        $indexes = $this->query("PRAGMA index_list(".$this->table().")");
        $inds = array();
        if (is_array($indexes)) {
            foreach ($indexes as $key) {
                $name = $key["name"];
                if (substr($name, 0, 16) !== "sqlite_autoindex") {
                    // Get info on this index
                    $info = $this->query("PRAGMA index_info(".$name.")");
                    foreach ($info as $ind) {
                        $seq = $ind["seqno"];
                        if (!is_array($inds[$name])) {
                            $inds[$name] = array(
                                "Name" => $name,
                                "Unique" => (bool)$key["unique"],
                                "Columns" => array($seq => $ind["name"]),
                            );
                        } else {
                            $inds[$name]["Columns"][$seq] = $ind["name"];
                        }
                    }
                }
            }
        }
        return (array)$inds;
    }

}

?>
