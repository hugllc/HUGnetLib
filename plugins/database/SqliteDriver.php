<?php
/**
 * Sensor driver for light sensors.
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsDatabase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** Get the required base class */
require_once dirname(__FILE__)."/../../base/HUGnetDBDriver.php";
/**
* This class implements photo sensors.
*
* @category   Libraries
* @package    HUGnetLib
* @subpackage PluginsDatabase
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2011 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class SqliteDriver extends HUGnetDBDriver
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name"  => "sqlite",
        "Type"  => "database",
        "Class" => "sqliteDriver",
        "Flags" => array("sqlite", "DEFAULT"),
     );

    /**
    * Gets columns from a SQLite server
    *
    * @return null
    */
    public function columns()
    {
        $columns = $this->query("PRAGMA table_info(".$this->table().")");
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

}

?>
