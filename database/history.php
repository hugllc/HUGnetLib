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
 * @version    SVN: $Id: Analysis.php 574 2007-12-18 18:27:46Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** The base for all database classes */
require_once HUGNET_INCLUDE_PATH."/base/DbBase.php";

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
class History extends DbBase
{
    /** The database table to use */
    var $table = "history";
    /** This is the Field name for the key of the record */
    var $id = "HistoryKey";    
   
   /**
    * Gets history between two dates and returns it as an array
    *
    * @param mixed $startDate The first date chronoligically.  Either a unix date or a string
    * @param mixed $endDate   The second date chronologically.  Either a unix date or a string
    * @param int   $maxRec    The max number of records to return
    *
    * @return array
    */
   public function getDates($startDate, $endDate = "NOW", $max=0) 
   {
   
   }
   
   
    /**
     * Creates the database table
     *
     * @param string $table    The table to use
     * @param mixed  $elements The number of data fields
     *
     * @return none
     */   
    public function createTable($table=null, $elements=16)
    {
        if (is_string($table) && !empty($table)) $this->table = $table;
        $query = "CREATE TABLE IF NOT EXISTS `".$this->table."` (
                  `DeviceKey` int(11) NOT NULL default '0',
                  `Date` datetime NOT NULL default '0000-00-00 00:00:00',
                  `deltaT` int(11) NOT NULL,
                 ";
        for ($i = 0; $i < $elements; $i++) {
            $query .= "`Data".$i."` float default NULL,\n";
        }
//        $query .= "UNIQUE KEY `DeviceKey` (`DeviceKey`,`Date`)
        $query .= "PRIMARY KEY  (`DeviceKey`)\n);";
        $ret = $this->query($query, false);        
        $ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `DeviceKey` ON `'.$this->table.'` (`DeviceKey`,`Date`)', false);
        $this->getColumns();
        return $ret;
    }

    /**
     * Creates the database table
     *
     * @param string $table    The table to use
     * @param mixed  $elements The number of data fields
     *
     * @return none
     */   
    public function createTableRaw($table=null, $elements=16)
    {
        if (is_string($table) && !empty($table)) $this->table = $table;
        $query = "CREATE TABLE IF NOT EXISTS `history_raw` (
                  `HistoryRawKey` int(11) NOT NULL auto_increment,
                  `DeviceKey` int(11) NOT NULL default '0',
                  `Date` datetime NOT NULL default '0000-00-00 00:00:00',
                  `RawData` varchar(255) NOT NULL default '',
                  `ActiveSensors` tinyint(4) NOT NULL default '0',
                  `Driver` varchar(32) NOT NULL default 'eDEFAULT',
                  `RawSetup` varchar(128) NOT NULL,
                  `RawCalibration` varchar(255) NOT NULL,
                  `Status` enum('GOOD','BAD','UNRELIABLE','DUPLICATE') NOT NULL default 'GOOD',
                  `ReplyTime` float NOT NULL default '0',
                  `sendCommand` char(2) NOT NULL default '',
                  PRIMARY KEY  (`HistoryRawKey`)                );";
        $ret = $this->query($query, false);
        $ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `DeviceKey` ON `'.$this->table.'` (`DeviceKey`,`Date`,`sendCommand`)', false);
        $this->getColumns();
        return $ret;
    }

}

?>
