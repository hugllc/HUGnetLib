<?php
/**
 * Class for saving analysis data
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
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Analysis
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** The base for all database classes */
require_once HUGNET_INCLUDE_PATH."/base/HUGnetDB.php";
require_once HUGNET_INCLUDE_PATH."/database/Device.php";

/**
 * A class for controlling processes
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Analysis extends HUGnetDB
{
    /** The database table to use */
    var $table = "analysis";
    /** This is the Field name for the key of the record */
    var $id = "AnalysisKey";
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
        if (is_string($table) && !empty($table)) {
            $this->table = $table;
        }
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

    /**
     * Queries health information from the database.
     *
     * @param string $where Extra where clause for the SQL
     * @param array  $data  The data to use for the where clause
     * @param int    $days  The number of days back to go
     * @param mixed  $start The start date of the health report
     *
     * @return array The array of health information
     *
     * @todo SQLite does not support the STD function.  This should be fixed.
     */
    function health($where, $data = array(), $days = 7, $start=null)
    {
        if (!is_array($data)) {
            $data = array();
        }
        if ($start === null) {
            $start = time();
        } else if (is_string($start)) {
            $start = strtotime($start);
        }
        $end = $start - (86400 * $days);
        $cquery = "SELECT COUNT(DeviceKey) as count FROM `devices` AS d ";
        $cquery .= " WHERE PollInterval > 0 ";
        if (!empty($where)) {
            $cquery .= " AND ".$where;
        }
        $res   = $this->query($cquery, $data);
        $count = (int) $res[0]['count'];

        if (empty($count)) {
            $count = 1;
        }
        $query = " SELECT "
            ."  ROUND(AVG(AverageReplyTime), 2) as ReplyTime "
            .", ROUND(STD(AverageReplyTime), 2) as ReplyTimeSTD "
            .", ROUND(MIN(AverageReplyTime), 2) as ReplyTimeMIN "
            .", ROUND(MAX(AverageReplyTime), 2) as ReplyTimeMAX "
            .", ROUND(AVG(AveragePollTime), 2) as PollInterval "
            .", ROUND(STD(AveragePollTime), 2) as PollIntervalSTD "
            .", ROUND(MIN(AveragePollTime), 2) as PollIntervalMIN "
            .", ROUND(MAX(AveragePollTime), 2) as PollIntervalMAX "
            .", ROUND(AVG(PollInterval), 2) as PollIntervalSET "
            .", ROUND(AVG(PollInterval/AveragePollTime), 2) as PollDensity "
            .", ROUND(STD(PollInterval/AveragePollTime), 2) as PollDensitySTD "
            .", ROUND(MIN(PollInterval/AveragePollTime), 2) as PollDensityMIN "
            .", ROUND(MAX(PollInterval/AveragePollTime), 2) as PollDensityMAX "
            .", '1.0' as PollDensitySET "
            .", SUM(Powerups) as Powerups "
            .", SUM(Reconfigs) as Reconfigs "
            .", ROUND(SUM(Polls) / ".$days.") as DailyPolls "
            .", ROUND((1440 / AVG(PollInterval)) * ".$count.") as DailyPollsSET "
            ." ";

        $query .= " FROM " . $this->table." AS a ";

        $query .= " LEFT JOIN `devices` AS d ON a.DeviceKey=d.DeviceKey ";

        $query .= " WHERE ";
        if (!empty($where)) {
            $query .= $where." AND ";
        }
        $query .= "a.Date <= ? AND a.Date >= ? ";
        $data[] = date("Y-m-d H:i:s", $start);
        $data[] = date("Y-m-d H:i:s", $end);
        $res = $this->query($query, $data);

        // @codeCoverageIgnoreStart
        if (isset($res[0])) {
            $res = $res[0];
        }
         // @codeCoverageIgnoreEnd
       return $res;
    }


}

?>
