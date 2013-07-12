<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
namespace HUGnet\db\tables;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our system interface */
require_once dirname(__FILE__)."/../../../interfaces/DBTable.php";
/** This is our system interface */
require_once dirname(__FILE__)."/../../../interfaces/DBTableAverage.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsAverageTable
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class EVIRTUALAverage extends \HUGnet\db\Average
    implements \HUGnet\interfaces\DBTable, \HUGnet\interfaces\DBTableAverage
{
    /** @var string This is the table we should use */
    public $sqlTable = "eVIRTUAL_average";
    /** @var This is the dataset */
    public $datacols = 20;
    /** @var This is the dataset */
    public $done = false;
    /** @var This is how many averages we have done */
    public $averages = array();
    /** @var This is our cache of averageTable objects */
    private $_histCache = array();

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
    protected function calc15MinAverage(\HUGnet\db\History &$data)
    {
        if ($this->done) {
            return false;
        }
        $this->_clearHistCache();
        $this->sqlLimit = $data->sqlLimit;
        $now = $this->system()->now();
        $this->device->setParam("LastPoll", $now);
        $this->device->setLocalParam("LastPoll", $now);
        do {
            $ret = $this->_get15MinAverage($rec);
        } while (($ret === false) && !$this->done);
        if ($ret) {
            $this->fromDataArray($rec);
            $this->device->setParam("LastHistory", $this->get("Date"));
            $this->device->setLocalParam("LastHistory", $this->get("Date"));
            return true;
        }
        return false;
    }
    /**
    * Polls the device and saves the poll
    *
    * @param object &$sensor The sensor to use
    *
    * @return false on failure, the history object on success
    */
    private function _getPoint(&$sensor)
    {
        if ($sensor->get("driver") !== "CloneVirtual") {
             // Only get clone virtual points.
             return null;
        }
        $extra = $sensor->get("extra");
        $dev = hexdec($extra[0]);
        if (empty($dev)) {
            return null;
        }
        if (!is_object($this->_histCache[$dev])) {
            $start = (int)$this->device->getParam("LastAverage15MIN");
            $device = $this->system()->device($dev);
            $this->_histCache[$dev] = $device->historyFactory(array(), false);
            $this->_histCache[$dev]->sqlOrderBy = "Date ASC";
            $query = array(
                "id" => $dev,
                "Type" => \HUGnet\db\Average::AVERAGE_15MIN,
                "Date" =>array('$gt' => (int)$start)
            );
            $lastAve = $device->getParam("LastAverage15MIN");
            if (!empty($lastAve)) {
                $query["Date"]['$lte'] = (int)$lastAve;
            }
            $this->_histCache[$dev]->selectInto($query);
        }
        return $this->_histCache[$dev];
    }
    /**
    * Polls the device and saves the poll
    *
    * @return false on failure, the history object on success
    */
    private function _clearHistCache()
    {
        foreach (array_keys((array)$this->_histCache) as $key) {
            unset($this->_histCache[$key]);
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
        $rec = array("id" => $this->device->get("id"), "Date" => $date);
        $this->Type = self::AVERAGE_15MIN;
        $notEmpty = false;
        for ($i = 0; $i < $this->device->get("InputTables"); $i++) {
            $input = $this->device->input($i);
            $table = $this->_getPoint($input);
            if (is_object($table)) {
                $val = $input->channels();
                if ($table->get("Date") == $date) {
                    $extra = $input->get("extra");
                    $field = "Data".(int)$extra[1];
                    $val[0]["value"] = $table->get($field);
                }
            } else {
                $A = null;
                $val = $input->decodeData($A, 900, $prev, $rec);
            }
            $rec[$i] = $val[0];
            if (!is_null($val[0]["value"])) {
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
        for ($i = 0; $i < $this->device->get("InputTables"); $i++) {
            $input = $this->device->input($i);
            $table = $this->_getPoint($input);
            while (is_object($table) && $table->get("Date") <= $date) {
                $ret = $table->nextInto();
                if ($ret === false) {
                    $this->done = true;
                    break;
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
        for ($i = 0; $i < $this->device->get("InputTables"); $i++) {
            $input = $this->device->input($i);
            $table = $this->_getPoint($input);
            if (is_object($table)) {
                $date = $table->get("Date");
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
