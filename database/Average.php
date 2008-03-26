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
 * @subpackage History
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** The base for all database classes */
require_once HUGNET_INCLUDE_PATH."/database/History.php";

/**
 * A class for controlling processes
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage History
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Average extends History
{
    /** The database table to use */
    protected $table = "average";
    /** This is the Field name for the key of the record */
    protected $id = "AverageKey";
   

    /**
     * Creates the database table
     *
     * @param string $table    The table to use
     * @param mixed  $elements The number of data fields
     *
     * @return null
     */   
    public function createTable($table=null, $elements=null)
    {
        $elements = (int) $elements;
        if (!empty($elements)) $this->_elements = $elements;
        $this->_columns = 3 + $this->_elements;
        
        if (is_string($table) && !empty($table)) $this->table = $table;
        $query = "CREATE TABLE IF NOT EXISTS `".$this->table."` (
                  `DeviceKey` int(11) NOT NULL default '0',
                  `Date` datetime NOT NULL default '0000-00-00 00:00:00',
                  `Type` varchar(16) NOT NULL default '15MIN',
                 ";
        for ($i = 0; $i < $this->_elements; $i++) {
            $query .= "`Data".$i."` float default NULL,\n";
        }
//                  `Type` enum('15MIN', 'HOURLY', 'DAILY', 'WEEKLY', 'MONTHLY', 'YEARLY', '15MINTOTAL', 'HOURLYTOTAL', 'DAILYTOTAL', 'WEEKLYTOTAL', 'MONTHLYTOTAL', 'YEARLYTOTAL') NOT NULL default '15MIN',
        $query .= "PRIMARY KEY  (`DeviceKey`, `Date`)\n);";
        $ret    = $this->query($query, false);        
        //$ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `DeviceKey` ON `'.$this->table.'` (`DeviceKey`,`Date`)', false);
        $this->getColumns();
        return $ret;
    }
}

?>
