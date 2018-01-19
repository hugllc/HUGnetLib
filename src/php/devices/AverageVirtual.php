<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class AverageVirtual extends Average
{
    /** @var This is where we store which inputs are clones */
    private $_histCache = array();
    /** @var This is where we store which inputs are clones */
    private $_clones = array();
    /** @var This is where we store which inputs are clones */
    private $_channels = array();
    /** @var This is where we store which inputs are clones */
    private $_inputs = array();
    /** @var This is where we store which inputs are clones */
    private $_dates = null;
    /** @var This is where we store which inputs are clones */
    private $_end = null;
    /** @var This is where we store which inputs are clones */
    private $_done = array();
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
                $val = $input->channels();
                $extra = $input->get("extra");
                $dev = hexdec($extra[0]);
                $point = $this->_getPointChan($i);
                foreach ($val as $key => $dp) {
                    $field = "Data".(int)$point;
                    $val[$key]["value"] = $this->_histCache[$date][$dev][$field];
                    $point++;
                }
            } else {
                $A = null;
                $val = $input->decodeData($A, 900, $prev, $rec);
            }
            if (is_array($val) && is_array($val[0])) {
                $rec = array_merge($rec, $val);
                if (!is_null($val[0]["value"])) {
                    $notEmpty = true;
                }
            }
        }
        if ($notEmpty) {
            $this->device->setParam("LastHistory", $rec["Date"]);
        }
        return $notEmpty;
    }
    /**
    * This returns the first average from this device
    *
    * @return null
    */
    protected function getNextAverageDate()
    {
        $this->_clones();
        if (is_null($this->_dates)) {
            $this->_dates = array_keys((array)$this->_histCache);
            sort($this->_dates);
        }
        if (empty($this->_dates)) {
            return null;
        }
        $date = array_shift($this->_dates);
        if ($this->_end < $date) {
            return null;
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
                    $this->_setupPoint($i);
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
    private function _setupPoint($key)
    {
        $input = $this->_input($key);
        if ($input->get("driver") !== "CloneVirtual") {
             // Only get clone virtual points.
             return null;
        }
        $extra = $input->get("extra");
        $dev = $extra[0];
        $dev = hexdec($extra[0]);
        if (empty($dev)) {
            return null;
        }
        if (!isset($this->_done[$dev])) {
            $start = (int)$this->device->getLocalParam("LastAverage".$this->avgType);
            $device = $this->system->device($dev);
            $hist = $device->historyFactory(array(), false);
            $hist->sqlOrderBy = "Date ASC";
            $hist->sqlLimit = $this->sqlLimit;
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
            $ret = $hist->selectInto($query);
            $cnt = 0;
            $end = $hist->get("Date");
            while ($ret) {
                $cnt++;
                $date = $hist->get("Date");
                if (!is_array($this->_histCache[$date])) {
                    $this->_histCache[$date] = array();
                }
                $this->_histCache[$date][$dev] = $hist->toArray(false);
                if ($date > $end) {
                    $end = $date;
                }
                $ret = $hist->nextInto();
            }
            if (($end < $this->_end) || is_null($this->_end)) {
                $this->_end = $end;
            }
            $this->_done[$dev] = $dev;
            for ($i = 0; $i < $device->get("InputTables"); $i++) {
                $this->_channels[$extra[0]][$i] = $device->input($i)->channelStart();
            }
        }
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
        foreach (array_keys((array)$this->_inputs) as $key) {
            unset($this->_inputs[$key]);
        }
        $this->_histCache = array();
        $this->_clones    = array();
        $this->_channels  = array();
        $this->_inputs    = array();
        $this->_dates     = null;
        $this->_end       = null;
        $this->_done      = array();
    }

    
}


?>
