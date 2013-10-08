<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once "Average.php";
/**
 * Networking for devices.
 *
 * This class will do all of the networking for devices.  It will poll, get configs,
 * update software, and anything else related to talking to devices.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class AverageVirtual extends Average
{
    /**
    * This function creates the system.
    *
    * @param mixed  &$network (object)The system object to use
    * @param string &$device  (object)The device to use
    *
    * @return null
    */
    public static function &factory(&$network, &$device)
    {
        $object = new AverageVirtual($network, $device);
        return $object;
    }

    /**
    * This returns the first average from this device
    *
    * @param array &$rec  The record to modify
    * @param array $param The parameters to use
    *
    * @return null
    */
    public function get30SECAverage(&$rec, $param)
    {
        return $this->get15MINAverage($rec, $param);
    }
    /**
    * This returns the first average from this device
    *
    * @param array &$rec  The record to modify
    * @param array $param The parameters to use
    *
    * @return null
    */
    public function get15MINAverage(&$rec, $param)
    {
        $return = false;
        $now = $this->system->now();
        $this->device->setParam("LastPoll", $now);
        $this->device->setLocalParam("LastPoll", $now);
        $date = $this->getNextAverageDate();
        if (empty($date) || ($date > $this->end)) {
            $this->done = true;
            $this->_clearHistCache();
            $this->device->store();
            return false;
        }
        $rec = array(
            "id" => $this->device->get("id"), 
            "Date" => $date,
            "Type" => $this->avgType
        );
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
            if (is_array($val) && is_array($val[0])) {
                $rec[$i] = $val[0];
                if (!is_null($val[0]["value"])) {
                    $notEmpty = true;
                }
            }
        }
        $this->_next($date);
        if ($notEmpty) {
            $this->device->setParam("LastHistory", $rec["Date"]);
            $this->device->setLocalParam("LastHistory", $rec["Date"]);
        }
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
        $tables = $this->device->get("InputTables");
        for ($i = 0; ($i < $tables) && !$this->done; $i++) {
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
                $newdate = $table->get("Date");
                if (is_null($date) || ($newdate < $date)) {
                    $date = $newdate;
                }
            }
        }
        return (int)$date;
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
            $start = (int)$this->device->getParam("LastAverage".$this->avgType);
            $device = $this->system->device($dev);
            $this->_histCache[$dev] = $device->historyFactory(array(), false);
            $this->_histCache[$dev]->sqlOrderBy = "Date ASC";
            $this->_histCache[$dev]->sqlLimit = $this->sqlLimit;
            $query = array(
                "id" => $dev,
                "Type" => $this->avgType,
                "Date" =>array('$gt' => (int)$start)
            );
            $lastAve = $device->getParam("LastAverage".$this->avgType);
            if (!empty($lastAve)) {
                $query["Date"]['$lte'] = (int)$lastAve;
            }
            //var_dump($device->toArray(false));
            //var_dump($query);
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

    
}


?>
