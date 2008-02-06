<?php
/**
 * Unix process information and manipulation
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
 * @category   UnixProcess
 * @package    HUGnetLib
 * @subpackage UnixProcess
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** The base for all database classes */
require_once HUGNET_INCLUDE_PATH."/base/DbBase.php";
/** We need a couple of functions out of this. */
require_once HUGNET_INCLUDE_PATH."/database/process.php";

/**
 * Saving statistics.  This class is written specifically for scripts that want
 * to save statistics about what they are doing.
 *
 * @category   UnixProcess
 * @package    HUGnetLib
 * @subpackage UnixProcess
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class ProcStats extends DbBase
{
    /** Stats table to use */
    protected $table = 'procStats';
    /** The table id to use */
    protected $id = 'PID';
    /** The number of columns */
    private $_columns = 6;
    /** Info about me.  This is set in the constructor*/
    protected $me = array();

    /** Stats period date formats */
    var $statPeriodic = array(
            'Daily' => 'Y-m-d',
            'Monthly' => 'Y-m',
            'Yearly' => 'Y',
        );

    /**
     * constructor
     *
     * @param string $file    The name of the file to use.  /tmp/HUGnetLocal will be used as the default.
     * @param string $table   The database table to use
     * @param string $id      The 'id' column to use
     * @param bool   $verbose Whether to be verbose or not
     */
    function __construct($file = null, $table = false, $id = false, $verbose = false) 
    {
        if (!is_string($file)) $file = null;
        parent::__construct($file, $table, $id, $verbose);
        $this->createTable();
        $this->me = process::getMyInfo();
    }
    
    /**
     * Creates the SQLite DB table
     * 
     * @return null
     */
    function createTable() 
    {
        $query = "CREATE TABLE IF NOT EXISTS `".$this->table."` (
                      `PID` int(11) NOT null,
                      `Program` varchar(32) NOT null,
                      `stype` varchar(32) NOT null,
                      `sdate` varchar(32) NOT null,
                      `sname` varchar(128) NOT null,
                      `svalue` text NOT null,
                      PRIMARY KEY  (`PID`,`Program`,`stype`,`sdate`,`sname`)
                     );
                    ";
        $this->query($query);        
        $this->getColumns();
    }

    /**
     * Increments stats in the database
     *
     * @param string $stat The stat to use
     *
     * @return null
     */
    function incStat($stat) 
    {
        $d = time();
        if (is_int($this->forceDate)) $d = $this->forceDate;
        $this->incField('totals', $stat);
        foreach ($this->statPeriodic as $type => $format) {
            $this->incField($type, $stat, date($format, $d));        
        }
    }


    /**
     * Increments fields in the database
     *
     * @param string $type The type of stat
     * @param string $name The name of the stat
     * @param string $date The date
     *
     * @return null
     */
    function incField($type, $name, $date="now") 
    {
        $value = $this->getMyStat($name, $date, $type);
        $value++;
        $this->setStat($name, $value, $date, $type);
    }

    /**
     * Retrieves a statistic
     *
     * @param string $name The name of the statistic to get
     * @param string $date The date of the statistic to get
     * @param string $type The type of statistic to get
     *
     * @return mixed The statistic in question
     */
    function getMyStat($name, $date="now", $type="stat") 
    {
        return $this->getStat($name, $this->me['Program'], $date, $type, true);
    }
    
    /**
     * Retrieves a statistic
     *
     * @param string $name    The name of the statistic to get
     * @param string $Program The name of the program
     * @param string $date    The date of the statistic to get
     * @param string $type    The type of statistic to get
     * @param int    $PID     The process id
     *
     * @return mixed The statistic in question
     */
    function getStat($name, $Program, $date="now", $type="stat", $PID=false) 
    {
        $data  = array($Program, $type, $date, $name);
        $query = " Program= ? "
                ." AND stype= ? "
                ." AND sdate= ? "
                ." AND sname= ? ";
        if ($PID) {
            $query .= " AND PID= ? ";
            $data[] = $this->me['PID'];
        }
        $ret = $this->getWhere($query, $data);
        if (isset($ret[0]['svalue'])) return $ret[0]['svalue'];    
        return 0;
    }

    /**
     * Saves a statistic
     *
     * @param string $name  The name of the statistic to get
     * @param mixed  $value The name of the statistic to get
     * @param string $date  The date of the statistic to get
     * @param string $type  The type of statistic to get
     *
     * @return mixed The statistic in question
     */
    function setStat($name, $value, $date="now", $type="stat") 
    {
        $d = time();
        if (is_int($this->forceDate)) $d = $this->forceDate;
        $this->_setStat($name, $value, $date, $type);
        return $this->_setStat('StatDate', date("Y-m-d H:i:s", $d));
    }

    /**
     * Saves a statistic
     *
     * @param string $name  The name of the statistic to get
     * @param mixed  $value The name of the statistic to get
     * @param string $date  The date of the statistic to get
     * @param string $type  The type of statistic to get
     *
     * @return mixed The statistic in question
     */
    private function _setStat($name, $value, $date="now", $type="stat") 
    {
        $data = array(
            "PID" => $this->me["PID"],
            "Program" => $this->me["Program"],
            "stype" => $type,
            "sdate" => $date,
            "sname" => $name,
            "svalue" => $value,
        );
        return $this->replace($data);
    }

    /**
     * Clears all statistics
     *
     * @return null
     */
    function clearStats() 
    {
        $data  = array($this->me['Program']);
        $query = "DELETE FROM '".$this->table."' "
                ." WHERE Program= ? ";
        $this->query($query, $data);
    }

    /**
     * gets all statistics for a program
     *
     * @param string $Program The name of the program to get stats for.
     *
     * @return array An array of statistics.
     */
    function getPeriodicStats($Program) 
    {
        $data  = array($Program);
        $query = " Program= ? "
                ." AND (";
        $sep   = "";
        foreach ($this->statPeriodic as $key => $value) {
            $data[] = $key;
            $query .= $sep."stype= ? ";
            $sep    = " OR ";
        }
        $query .= ") ORDER BY sdate desc";            
        $rows   = $this->getWhere($query, $data);
        $ret    = array();
        foreach ($rows as $row) {
            $ret[$row['stype']][$row['sdate']][$row['sname']] = $row['svalue'];
        }
        return $ret;

    }

    /**
     * gets all statistics for a program
     *
     * @param string $Program The name of the program to get stats for.
     *
     * @return array An array of statistics.
     */
    function getTotalStats($Program) 
    {
        $data  = array($Program);
        $query = " Program = ? "
                 ." AND "
                 ." stype='totals' ";
        $rows  = $this->getWhere($query, $data);
        $ret   = array();
        foreach ($rows as $row) {
            $ret[$row['sname']] = $row['svalue'];
        }
        return $ret;
    }

}

?>
