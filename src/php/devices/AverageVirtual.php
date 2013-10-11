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
require_once dirname(__FILE__)."/Average.php";
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
    /** @var This is where we store which inputs are clones */
    private $_clones = array();
    /** @var This is where we store which inputs are clones */
    private $_channels = array();
    /** @var This is where we store which inputs are clones */
    private $_inputs = array();
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$system The network application object
    * @param object &$device The device device object
    *
    * @return null
    */
    protected function __construct(&$system, &$device)
    {
        parent::__construct($system, $device);
    }
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
        if (empty($date)) {
            $this->done = true;
            $this->_clearHistCache();
            $this->hist = null;
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
            $input = $this->_input($i);
            if (isset($this->_clones[$i])) {
                $table = $this->_getPoint($i);
                $val = $input->channels();
                if (is_object($table) && ($table->get("Date") == $date)) {
                    $point = $this->_getPointChan($i);
                    foreach ($val as $key => $dp) {
                        $field = "Data".(int)$point;
                        $val[$key]["value"] = $table->get($field);
                        $point++;
                    }
                }
            } else {
                $A = null;
                $val = $input->decodeData($A, 900, $prev, $rec);
            }
            if (is_array($val) && is_array($val[0])) {
                $rec = array_merge($rec, $val);
                //$rec[$i] = $val[0];
                if (!is_null($val[0]["value"])) {
                    $notEmpty = true;
                }
            }
        }
        $this->_next($date);
        if ($notEmpty) {
            $this->device->setParam("LastHistory", $rec["Date"]);
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
        foreach ($this->_clones() as $i) {
            $table = $this->_getPoint($i);
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
        $time = microtime(true);
        $date = null;
        foreach ($this->_clones() as $i) {
            $table = $this->_getPoint($i);
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
    * Gets an array composed of the inputs that are clones
    *
    * @return array Returns an array of the input numbers for clone virtuals
    */
    private function _clones()
    {
        if (empty($this->_clones) || !is_array($this->_clones)) {
            $tables = $this->device->get("InputTables");
            for ($i = 0; $i < $tables; $i++) {
                if ($this->_input($i)->get("driver") === "CloneVirtual") {
                    $this->_clones[$i] = $i;
                }
            }
        }
        return (array)$this->_clones;
    }
    /**
    * Polls the device and saves the poll
    *
    * @param int $key The input to get
    *
    * @return false on failure, the history object on success
    */
    private function &_input($key)
    {
        if (!is_object($this->_input[$key])) {
            $this->_input[$key] = $this->device->input($key);
        }
        return $this->_input[$key];
    }
    /**
    * Polls the device and saves the poll
    *
    * @param int $key The input key to use
    *
    * @return false on failure, the history object on success
    */
    private function _getPoint($key)
    {
        
        $input = $this->_input($key);
        if ($input->get("driver") !== "CloneVirtual") {
             // Only get clone virtual points.
             return null;
        }
        $extra = $input->get("extra");
        $dev = hexdec($extra[0]);
        if (empty($dev)) {
            return null;
        }
        if (!is_object($this->_histCache[$dev])) {
            $start = (int)$this->device->getLocalParam("LastAverage".$this->avgType);
            $device = $this->system->device($dev);
            $this->_histCache[$dev] = $device->historyFactory(array(), false);
            $this->_histCache[$dev]->sqlOrderBy = "Date ASC";
            $this->_histCache[$dev]->sqlLimit = $this->sqlLimit;
            $query = array(
                "id" => $dev,
                "Type" => $this->avgType,
                "Date" =>array('$gt' => (int)$start)
            );
            $lastAve = $device->getLocalParam("LastAverage".$this->avgType);
            if (!empty($lastAve)) {
                $query["Date"]['$lte'] = (int)$lastAve;
            }
            //var_dump($device->toArray(false));
            //var_dump($query);
            $this->_histCache[$dev]->selectInto($query);
            for ($i = 0; $i < $device->get("InputTables"); $i++) {
                $this->_channels[$extra[0]][$i] = $device->input($i)->channelStart();
            }
        }
        return $this->_histCache[$dev];
    }
    /**
    * Polls the device and saves the poll
    *
    * @param int $key The input key to use
    *
    * @return Returns the datachannel that corresponds to this point
    */
    private function _getPointChan($key)
    {
        $extra = $this->_input($key)->get("extra");
        return (int)$this->_channels[$extra[0]][$extra[1]];
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
