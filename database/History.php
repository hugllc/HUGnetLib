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
require_once HUGNET_INCLUDE_PATH."/base/HUGnetDB.php";

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
class History extends HUGnetDB
{
    /** The database table to use */
    protected $table = "history";
    /** This is the Field name for the key of the record */
    protected $id = "HistoryKey";
    /** The type of data */
    protected $dataType = "float";

    /** The number of data elements */
    private $_elements = 16;
    /** The number of columns */
    private $_columns = 3;
   
    /**
     * Gets history between two dates and returns it as an array
     *
     * @param int   $DeviceKey The key for the device to get the history for
     * @param mixed $startDate The first date chronoligically.  Either a unix date or a string
     * @param mixed $endDate   The second date chronologically.  Either a unix date or a string
     * @param int   $maxRec    The max number of records to return
     *
     * @return array
     */
    public function getDates($DeviceKey, $startDate, $endDate = "NOW", $maxRec=0) 
    {
        $startDate = $this->sqlDate($startDate);
        $endDate   = $this->sqlDate($endDate);
        $data      = array($startDate, $endDate, $DeviceKey);
        $query     = "Date >= ? AND Date <= ? AND DeviceKey = ? ";
        $orderby   = " ORDER BY `Date` DESC";
        return $this->getWhere($query, $data, $maxRec, 0, $orderby);
    }
   
    /**
     * Gets all rows from the database
     *
     * @param string $where   Where clause
     * @param array  $data    Data for query
     * @param int    $limit   The maximum number of rows to return (0 to return all)
     * @param int    $start   The row offset to start returning records at
     * @param string $orderby How to order the string.  Must include "ORDER BY"
     *
     * @return array
     */
    public function getWhere($where, $data = array(), $limit = 0, $start = 0, $orderby="") 
    {
        $ret = parent::getWhere($where, $data, $limit, $start, $orderby);
        foreach ($ret as $key => $val) {
            $ret[$key]["DeviceKey"] = (int) $val["DeviceKey"];
            $ret[$key]["deltaT"]    = (int) $val["deltaT"];
            for ($i = 0; $i < $this->_elements; $i++) {
                $ret[$key]["data"][$i] = self::fixType($val["Data".$i], $this->fields["Data".$i]);
                $ret[$key]["Data".$i]  = self::fixType($val["Data".$i], $this->fields["Data".$i]);
            }
        }
        return $ret;
    }

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
                  `deltaT` int(11) NOT NULL,
                 ";
        for ($i = 0; $i < $this->_elements; $i++) {
            $query .= "`Data".$i."` float default NULL,\n";
        }
        $query .= "PRIMARY KEY  (`DeviceKey`, `Date`)\n);";
        $ret    = $this->query($query, false);        
        //$ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `DeviceKey` ON `'.$this->table.'` (`DeviceKey`,`Date`)', false);
        $this->getColumns();
        return $ret;
    }
    
    
    /**
     * Creates a function to crunch numbers
     *
     * @param int    $DeviceKey The deviceKey to use
     * @param int    $input     The input to use
     * @param string $math      The math to use
     *
     * @return bool|string The name of the function created.
     */
    public function createFunction($DeviceKey, $input, $math)
    {
        $mathCode = $math;
        for ($i = 1; $i < 20; $i++) {
            $index = $i - 1;
            $mathCode = str_replace('{'.$i.'}', '$row["Data'.$index.'"]', $mathCode);
        }
        $code = 'return ('.$mathCode.');';
        return create_function('$row', $code);   
    }


    /**
     * This function crunches the numbers for the virtual sensors.
     *
     * @params array &$history The history to crunch
     * @params array &$devInfo The device array to use.
     * 
     * @return null
     */
    public function virtualSensorHistory(&$history, &$devInfo)
    {
        if ($devInfo["params"]["VSensors"] <= 0) return;
        foreach ($history as $key => $hist) {
            for ($i = $devInfo["NumSensors"]; $i < $devInfo["TotalSensors"]; $i++) {
                $history[$key]["Data".$i] = $this->virtualSensorValue($i, $history[$key], $devInfo);
                $history[$key]["data"][$i] = $history[$key]["Data".$i];
            }
        }
    }

    /**
     * This function crunches the numbers for the virtual sensors.
     *
     * @params string $sensor   The sensor to use
     * @params array  &$history The history row to use
     * @params array  &$devInfo The device array to use.
     * 
     * @return null
     */
    public function virtualSensorValue($sensor, &$history, &$devInfo)
    {
        $function =& $this->_functions[$devInfo["DeviceKey"]][$sensor];
        if (!function_exists($function)) $function = $this->createFunction($devInfo["DeviceKey"], $sensor, $devInfo["params"]["Math"][$sensor]);
        if (!function_exists($function)) return null;
        $d = $function($history);
        if (is_numeric($devInfo["params"]["sensorMax"][$sensor]) && ($d > $devInfo["params"]["sensorMax"][$sensor])) return $devInfo["params"]["sensorMax"][$sensor];
        if (is_numeric($devInfo["params"]["sensorMin"][$sensor]) && ($d < $devInfo["params"]["sensorMin"][$sensor])) return $devInfo["params"]["sensorMin"][$sensor];
        return $d;
    }

}

?>
