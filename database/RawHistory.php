<?php
/**
 * Class for saving analysis data
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2009 Hunt Utilities Group, LLC
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
 * @category   Database
 * @package    HUGnetLib
 * @subpackage History
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** The base for all database classes */
require_once HUGNET_INCLUDE_PATH."/base/HUGnetDB.php";
/** The base for all database classes */
require_once HUGNET_INCLUDE_PATH."/database/History.php";

/**
 * A class for controlling processes
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage History
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class RawHistory extends History
{
    /** The database table to use */
    var $table = "history_raw";
    /** This is the Field name for the key of the record */
    var $id = "RawHistoryKey";    
    /** The number of columns */
    private $_columns = 11;
   
    /**
     * Creates the database table
     *
     * @param string $table The table to use
     *
     * @return null
     */   
    public function createTable($table=null)
    {
        if (is_string($table) && !empty($table)) $this->table = $table;
        $query = "CREATE TABLE IF NOT EXISTS `".$this->table."` (
                  `HistoryRawKey` int(11) NOT NULL,
                  `DeviceKey` int(11) NOT NULL default '0',
                  `Date` datetime NOT NULL default '0000-00-00 00:00:00',
                  `RawData` varchar(255) NOT NULL default '',
                  `ActiveSensors` tinyint(4) NOT NULL default '0',
                  `Driver` varchar(32) NOT NULL default 'eDEFAULT',
                  `RawSetup` varchar(128) NOT NULL,
                  `RawCalibration` varchar(255) NOT NULL,
                  `Status` varchar(16) NOT NULL default 'GOOD',
                  `ReplyTime` float NOT NULL default '0',
                  `sendCommand` char(2) NOT NULL default '',
                  PRIMARY KEY  (`HistoryRawKey`)
                 );";
        $ret   = $this->query($query, false);
        $ret   = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `DeviceKey` ON `'
                              .$this->table.'` (`DeviceKey`,`Date`,`sendCommand`)', false);
        $this->getColumns();
        return $ret;
    }

}

?>
