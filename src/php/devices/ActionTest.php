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
require_once dirname(__FILE__)."/ActionVirtual.php";
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
 * @version    Release: 0.13.0
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.13.0
 */
class ActionTest extends ActionVirtual
{
    /** This is a cache of history records */
    private $_histCache = array();
    /** This is a cache of history records */
    private $_pingCache = array();
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        foreach (array_keys((array)$this->_histCache) as $key) {
            unset($this->_histCache[$key]);
        }
        unset($this->_pingCache);
        parent::__destruct();
    }
    /**
    * This function creates the system.
    *
    * @param mixed  &$network (object)The system object to use
    * @param string &$device  (object)The device to use
    * @param object &$driver  The device driver object
    *
    * @return null
    */
    public static function &factory(&$network, &$device, &$driver)
    {
        $object = new ActionTest($network, $device, $driver);
        return $object;
    }
    /**
    * Pings the device and sets the LastContact if it is successful
    *
    * @param bool $find Whether or not to use a find ping
    *
    * @return string The left over string
    */
    public function ping($find = false)
    {
        $time = $this->system->now();
        $sensors = $this->device->get("totalSensors");
        $ret = false;
        for ($i = 0; $i < $sensors; $i++) {
            $sen = $this->device->input($i);
            $ret = $this->_getPing($sen, $time);
            if ($ret) {
                $this->device->load($this->device->id());
                $this->device->setParam("LastContact", time());
                $this->device->setParam("ContactFail", 0);
                break;
            }
        }
        if (!$ret) {
            $fail = $this->device->getParam("ContactFail");
            $this->device->setParam("ContactFail", $fail+1);
            $ret = false;
        }
        $this->device->store();
        return $ret;
    }
    /**
    * Polls the device and saves the poll
    *
    * @param object &$sensor The sensor to use
    * @param int    $time    The time to use
    *
    * @return false on failure, the history object on success
    */
    private function _getPing(&$sensor, $time)
    {
        if ($sensor->get("driver") !== "CloneVirtual") {
             // Only get clone virtual points.
             return false;
        }
        $extra = $sensor->get("extra");
        $dev = hexdec($extra[0]);
        if (empty($dev)) {
            return false;
        }
        if ($this->_pingCache[$dev] != $time) {
            $device = $this->system->device($dev);
            $ret = $device->action()->ping();
            if (!$ret) {
                $this->_pingCache[$dev] = $time;
            } else {
                return true;
            }
        }
        return false;
    }
    /**
    * Polls the device and saves the poll
    *
    * @param object &$sensor The sensor to use
    * @param int    $time    The time to use
    *
    * @return false on failure, the history object on success
    */
    private function _getPoint(&$sensor, $time)
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
        if (!is_object($this->_histCache[$dev])
            && ($this->_histCache[$dev] != $time)
        ) {
            $device = $this->system->device($dev);
            $this->_histCache[$dev] = $device->action()->poll(
                $this->device->get('id'), $time
            );
            $device->store();
            if (!is_object($this->_histCache[$dev])) {
                $this->_histCache[$dev] = $time;
            }
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
        $this->_histCache = array();
    }
    /**
    * Polls the device and saves the poll
    *
    * @param int $TestID The test ID of this poll
    * @param int $time   The time to use for the poll
    *
    * @return false on failure, the history object on success
    */
    public function poll($TestID = null, $time = null)
    {
        $this->_clearHistCache();
        if (is_null($time)) {
            $time = time();
        }
        $did = $this->device->get("id");
        $prev = (array)$this->device->getParam("LastPollData");
        $hist = array(
            "id" => $did,
            "Date" => $time,
            "TestID" => $TestID,
            "deltaT" => $time - $prev["Date"],
        );
        $found = false;
        $sensors = $this->device->get("totalSensors");
        for ($i = 0; $i < $sensors; $i++) {
            $sen = $this->device->input($i);
            $point = $this->_getPoint($sen, $time);
            $found |= is_object($point);
            $hist = array_merge(
                $hist,
                $sen->decodeData(
                    $point,
                    $hist["deltaT"],
                    $prev[$i],
                    $hist
                )
            );
        }
        if ($found) {
            $this->device->load($this->device->id());
            $this->device->setParam("LastPollData", $hist);
            $this->device->setParam("LastPoll", $time);
            $this->device->setParam("LastContact", $time);
            $this->device->setParam("PollFail", 0);
            $this->device->setParam("ContactFail", 0);
            $history = $this->device->historyFactory($hist);
            if ($history->insertRow()) {
                $this->device->setParam("LastHistory", $time);
                $this->device->setLocalParam("LastHistory", $time);
            }
            $this->device->store();
            $this->_writeFile($history);
            return $history;
        }
        $this->device->load($this->device->id());
        $fail = $this->device->getParam("PollFail");
        $this->device->setParam("PollFail", $fail+1);
        $this->device->store();
        return false;
    }
    /**
    * Polls the device and saves the poll
    *
    * @param object $hist The history object to use
    *
    * @return false on failure, the history object on success
    */
    private function _writeFile($hist)
    {
        if (is_object($hist)) {
            $setupDir = (string)$this->system->get("dataDir");
            if ((strlen($setupDir) > 0) && file_exists($setupDir)) {
                $prefix = $setupDir;
            } else if (file_exists("/home/tmp")) {
                $prefix = "/home/tmp";
            } else if (file_exists("/var/tmp")) {
                $prefix = "/var/tmp";
            } else {
                $prefix = "/tmp";
            }
            $prefix .= "/LeNR.";
            $filename = $prefix.$this->device->get("DeviceID").".".date("Ymd");
            $new = !file_exists($filename);
            $fdr = fopen($filename, "a");
            $channels = $this->device->dataChannels();
            $chan = $channels->toArray();
            if ($new) {
                $sep = ",";
                fwrite($fdr, "Date");
                for ($i = 0; $i < count($chan); $i++) {
                    if ($chan[$i]["dataType"] !== 'ignore') {
                        fwrite($fdr, $sep.$chan[$i]['label']);
                        $sep = ",";
                    }
                }
                fwrite($fdr, "\r\n");
            }
            $sep = ",";
            fwrite($fdr, date("Y-m-d H:i:s", $hist->get("Date")));
            for ($i = 0; $i < count($chan); $i++) {
                if ($chan[$i]["dataType"] !== 'ignore') {
                    $data = $hist->get("Data".$i);
                    fwrite($fdr, $sep.$data);
                    $sep = ",";
                }
            }
            fwrite($fdr, "\r\n");
            fclose($fdr);
            chmod($filename, 0666);
        }
    }
}


?>
