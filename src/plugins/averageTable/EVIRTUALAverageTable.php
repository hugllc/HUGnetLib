<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsAverageTable
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../tables/AverageTableBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsAverageTable
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class EVIRTUALAverageTable extends AverageTableBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "EVIRTUALAverageTable",
        "Type" => "averageTable",
        "Class" => "EVIRTUALAverageTable",
        "Flags" => array("eVIRTUAL"),
    );
    /** @var string This is the table we should use */
    public $sqlTable = "eVIRTUAL_average";
    /** @var This is the dataset */
    public $datacols = 20;
    /** @var This is the dataset */
    public $done = false;

    /** @var This is how many averages we have done */
    public $averages = array();

    /**
    * This calculates the averages
    *
    * It will return once for each average that it calculates.  The average will be
    * stored in the instance this is called from.  If this is fed history table
    * then it will calculate 15 minute averages.
    *
    * @param HistoryTableBase &$data This is the data to use to calculate the average
    *                                This is not used here, but it is required to
    *                                match the main implementation.
    *
    * @return bool True on success, false on failure
    */
    protected function calc15MinAverage(HistoryTableBase &$data)
    {
        if ($this->done) {
            return false;
        }
        $this->sqlLimit = $data->sqlLimit;
        $this->_setAverageTables();
        $this->device->params->DriverInfo["LastPoll"] = time();
        do {
            $ret = $this->_get15MinAverage($rec);
        } while (($ret === false) && !$this->done);
        if ($ret) {
            $this->fromDataArray($rec);
            $this->device->params->DriverInfo["LastHistory"] = $this->Date;
            return true;
        }
        return false;
    }
    /**
    * This returns the first average from this device
    *
    * @return null
    */
    private function _setAverageTables()
    {
        $start = (int)$this->device->params->DriverInfo["LastAverage15MIN"];
        $aSen = &$this->averages["sensors"];
        $aDev = &$this->averages["DeviceID"];
        for ($i = 0; $i < $this->device->sensors->Sensors; $i++) {
            $sensor = &$this->device->sensor($i);
            if (method_exists($sensor, "getAverageTable")) {
                if (!is_a($aSen[$i], "AverageTableBase")) {
                    $devId = $sensor->getDeviceID();
                    if (!is_object($aDev[$devId])) {
                        $aDev[$devId] = &$sensor->getAverageTable();
                        $aDev[$devId]->sqlLimit = $this->sqlLimit;
                        $aDev[$devId]->sqlOrderBy = "Date ASC";
                        $query = "`id` = ? AND `Type`=? AND `Date` > ?";
                        $data = array(
                            $devId,
                            AverageTableBase::AVERAGE_15MIN,
                            (int)$start
                        );
                        $lastAve = $aDev[$devId]->device
                            ->params->DriverInfo["LastAverage15MIN"];
                        if (!empty($lastAve)) {
                            $query .= " AND `Date` <= ?";
                            $data[] = $lastAve;
                        }
                        $aDev[$devId]->selectInto($query, $data);
                    }
                    $aSen[$i] = &$aDev[$devId];
                }
            }
        }
        // Nothing to do.  Exit
        if (empty($aDev)) {
            $this->done = true;
            return;
        }
    }
    /**
    * This returns the first average from this device
    *
    * @param array &$rec The record to modify
    *
    * @return null
    */
    private function _get15MinAverage(&$rec)
    {
        $date = $this->getNextAverageDate();
        if (empty($date)) {
            $this->done = true;
            return false;
        }
        $rec = array("id" => $this->device->id, "Date" => $date);
        $this->Type = self::AVERAGE_15MIN;
        $notEmpty = false;
        for ($i = 0; $i < $this->device->sensors->Sensors; $i++) {
            $sensor = &$this->device->sensor($i);
            if (is_a($this->averages["sensors"][$i], "AverageTableBase")) {
                if ($this->averages["sensors"][$i]->Date == $date) {
                    $data = $this->averages["sensors"][$i]->toArray();
                } else {
                    $data = array();
                }
                $rec[$i] = $sensor->getUnits($A, $deltaT, $prev, $data);
            } else {
                $rec[$i] = $sensor->getUnits($A, $deltaT, $prev, $rec);
            }
            if (!is_null($rec[$i]["value"])) {
                $notEmpty = true;
            }
        }
        $this->_next($date);
        return $notEmpty;
    }

    /**
    * This returns the first average from this device
    *
    * @param int $date The date to check for
    *
    * @return null
    */
    private function _next($date)
    {
        $averages = &$this->averages["DeviceID"];
        foreach (array_keys((array)$averages) as $key) {
            while ($averages[$key]->Date <= $date) {
                $ret = $averages[$key]->nextInto();
                if ($ret === false) {
                    $this->done = true;
                    return;
                }
            }
        }
    }
    /**
    * This returns the first average from this device
    *
    * @return null
    */
    protected function getNextAverageDate()
    {
        $date = null;
        $averages = &$this->averages["DeviceID"];
        foreach (array_keys((array)$averages) as $key) {
            if (is_null($date) || ($averages[$key]->Date < $date)) {
                $date = $averages[$key]->Date;
            }
        }
        return (int)$date;
    }

    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>
