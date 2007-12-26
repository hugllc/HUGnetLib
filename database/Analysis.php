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
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** The base for all database classes */
require_once HUGNET_INCLUDE_PATH."/base/DbBase.php";

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
class Analysis extends DbBase
{
    /** The database table to use */
    var $table = "Analysis";            
    /** This is the Field name for the key of the record */
    var $id = "AnalysisKey";     
    /** The number of columns */
    private $_columns = 9;
    /**
     * Creates the SQLite DB table
     *
     * @param string $table Table to use if not the default
     * 
     * @return none
     */
    public function createTable($table=null) 
    {
        if (is_string($table) && !empty($table)) $this->table = $table;
        
        $query = "CREATE TABLE IF NOT EXISTS `analysis` (
                  `DeviceKey` int(11) NOT NULL default '0',
                  `Date` date NOT NULL default '0000-00-00',
                  `AveragePollTime` float NOT NULL default '0',
                  `Polls` int(11) NOT NULL default '0',
                  `AverageReplyTime` float NOT NULL default '0',
                  `Replies` int(11) NOT NULL default '0',
                  `Reconfigs` mediumint(9) NOT NULL default '0',
                  `Boredom` mediumint(9) NOT NULL default '0',
                  `Powerups` mediumint(9) NOT NULL default '0',
                  PRIMARY KEY  (`DeviceKey`,`Date`)
                );";
        $this->query($query);
        $this->getColumns();
    }
    
}

?>
