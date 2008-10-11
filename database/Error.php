<?php
/**
 * Class for saving analysis data
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Analysis
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: Analysis.php 1557 2008-09-15 14:50:12Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** The base for all database classes */
require_once HUGNET_INCLUDE_PATH."/base/HUGnetDB.php";

define("HUGNET_ERROR_OLD_SENSOR_READ", 1);
define("HUGNET_ERROR_OLD_POLL", 2);
define("HUGNET_ERROR_OLD_CONFIG", 3);


/**
 * A class for controlling processes
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Error extends HUGnetDB
{
    /** The database table to use */
    var $table = "error";            
    /** This is the Field name for the key of the record */
    var $id = "id";     
    /** The number of columns */
    private $_columns = 9;
    /**
     * Creates the SQLite DB table
     *
     * @param string $table Table to use if not the default
     * 
     * @return null
     */
    public function createTable($table=null) 
    {
        if (is_string($table) && !empty($table)) $this->table = $table;
        
        $query = "CREATE TABLE IF NOT EXISTS `error` (
            `id` varchar(16) NOT NULL,
            `err` int(11) NOT NULL,
            `msg` text NOT NULL,
            `errorLastSeen` datetime default '0000-00-00 00:00:00',
            `errorDate` datetime NOT NULL,
            `program` varchar(64) NOT NULL,
            `type` varchar(16) NOT NULL,
            `status` varchar(8) default 'NEW',
            `errorCount` int(8) default 0         
        );";
        $query = $this->cleanSql($query);
        $ret = $this->query($query);
        $ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `errorKey` ON `'.$this->table.'` (`err`,`errorDate`,`id`)');
        $this->getColumns();
    }
    
    /**
    * Adds an row to the database
    *
    * @param array $info    The row in array form
    * @param bool  $replace If true it replaces the "INSERT"
    *                       keyword with "REPLACE".  Not all
    *                       databases support "REPLACE".
    *
    * @return bool
    */
    public function add($info, $replace = false)
    {
        if (isset($info["err"])) $info["err"] = (int) $info["err"];      
        $ret = $this->getWhere("id = ? AND err = ? AND errorLastSeen > ?", array($info["id"], $info["err"], date("Y-m-d H:i:s", time()-86400)));
        if (count($ret) == 0) {
            $info["errorLastSeen"] = $info["errorDate"];
            $info["errorCount"] = 1;         
            return parent::add($info);
        } else {
            $where = "id = ? AND err = ? AND errorDate = ?";
            $data = array($info["id"], $info["err"], $ret[0]["errorDate"]);
            $info["errorLastSeen"] = $info["errorDate"];
            unset($info["errorDate"]);
            $info["errorCount"] = $ret[0]["errorCount"] + 1;
            $info["status"] = 'NEW';         
            return parent::updateWhere($info, $where, $data);
        }                  
    }
       
    
}

?>
