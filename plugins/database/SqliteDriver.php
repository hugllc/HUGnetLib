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
class SqliteDriver extends HUGnetDBDriver
{
    /** @var bool Does this driver support auto_increment? */
    protected $autoIncrement = false;
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name"  => "sqlite",
        "Type"  => "database",
        "Class" => "sqliteDriver",
    );

    /**
    * Gets the instance of the class and
    *
    * @param object &$table The table to attach myself to
    * @param PDO    &$pdo   The database object
    *
    * @return null
    */
    static public function &singleton(&$table, PDO &$pdo)
    {
        static $instance;
        if (empty($instance)) {
            $class = __CLASS__;
            $instance = new $class($table, $pdo);
        }
        return $instance;
    }
    /**
    * Gets columns from a SQLite server
    *
    * @return null
    */
    public function columns()
    {
        $columns = $this->query("PRAGMA table_info(".$this->table().")");
        foreach ((array)$columns as $col) {
            $cols[$col["name"]] = array(
                "Name" => $col["name"],
                "Type" => $col["type"],
                "Default" => $col["dflt_value"],
                "Null" => !(bool)$col["notnull"],
            );
        }
        return $cols;
    }

}

?>
