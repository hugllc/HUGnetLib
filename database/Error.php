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
            `id` int(11) NOT NULL,
            `num` int(11) NOT NULL,
            `msg` varchar(255) NOT NULL,
            `errorDate` datetime NOT NULL,
            `program` varchar(64) NOT NULL,
            `severity` varchar(16) NOT NULL
        );";
        $query = $this->cleanSql($query);
        $ret = $this->query($query);
        $ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `id` ON `'.$this->table.'` (`id`)');
        $ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `errorKey` ON `'.$this->table.'` (`num`,`errorDate`,`program`)');
        $this->getColumns();
    }    
}

?>
