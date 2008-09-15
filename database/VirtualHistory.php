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
require_once HUGNET_INCLUDE_PATH."/database/Average.php";

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
class VirtualHistory extends Average
{
    /** History buffer */
    protected $hist = array();   
    /** History buffer */
    protected $histBuf = array();   
    /** The database table to use */
    protected $table = "average";
    /** This is the Field name for the key of the record */
    protected $id = "AverageKey";
    
    /** The number of data elements */
    private $_elements = 16;
    /** The number of columns */
    private $_columns = 6;
   
    /**
     * Gets history between two dates and returns it as an array
     *
     * @param array &$devInfo  The key for the device to get the history for
     * @param mixed $startDate The first date chronoligically.  Either a unix date or a string
     * @param mixed $endDate   The second date chronologically.  Either a unix date or a string
     * @param int   $maxRec    The max number of records to return
     *
     * @return array
     */
    public function getDates(&$devInfo, $startDate, $endDate = "NOW", $maxRec=0) 
    {
        $endpoint = HUGnetDriver::getInstance($this->config);
        $history = array();
        for ($i = 0; $i < $devInfo["ActiveSensors"]; $i++) {
            $devKey   = (int) $devInfo["params"]["device"][$i];
            $input = (int) $devInfo["params"]["input"][$i] - 1;

            if (empty($devKey)) continue;
            $dev = array("DeviceKey" => $devKey, "Driver" => $devInfo["params"]["Driver"][$i]);
            if (!is_object($this->hist[$devKey])) $this->hist[$devKey] = $endpoint->getHistoryInstance(array(), $dev);
            if (!is_array($this->histBuf[$devKey])) $this->histBuf[$devKey] = $this->hist[$devKey]->getDates($dev, $startDate, $endDate, $maxRec);
        }
        foreach ($this->histBuf as $devKey => $hist) {
            foreach ($hist as $row) {
                for ($i = 0; $i < $devInfo["ActiveSensors"]; $i++) {
                    if ($devKey != $devInfo["params"]["device"][$i]) continue;
                    $input =  (int) $devInfo["params"]["input"][$i] - 1;
                    $history[$row["Date"]]["Data".$i] = $row["Data".$input];
                    $history[$row["Date"]]["data"][$i] = $row["Data".$input];
                }
            }
        }
        krsort($history);
        $ret = array();
        $index = 0;
        foreach ($history as $date => $hist) {
            $ret[$index] = $hist;
            $ret[$index]["DeviceKey"] = $devInfo["DeviceKey"];
            $ret[$index]["Date"] = $date;
            $index++;
        }
        return $ret;
    }


}

?>
