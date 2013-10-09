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
require_once dirname(__FILE__)."/Action.php";
/** This is the average classes */
require_once dirname(__FILE__)."/../db/FastAverage.php";
/** This is the average classes */
require_once dirname(__FILE__)."/../db/Average.php";
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
 * @since      0.9.8
 */
class ActionVirtual extends Action
{
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
        $object = new ActionVirtual($network, $device, $driver);
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
        $this->device->load($this->device->id());
        $this->device->setParam("LastContact", time());
        $this->device->setParam("ContactFail", 0);
        return true;
    }
    /**
    * Gets the config and saves it
    *
    * @return string The left over string
    */
    public function config()
    {
        $this->checkRecord();
        $this->device->load($this->device->id());
        $this->device->set(
            "FWVersion",
            $this->system->get("version")
        );
        $this->device->set(
            "RawSetup",
            $this->device->encode()
        );
        $this->device->set(
            "GatewayKey", (int)$this->system->get("GatewayKey")
        );
        $this->device->setParam("LastContact", time());
        $this->device->setParam("LastConfig", time());
        $this->device->setParam("ConfigFail", 0);
        $this->device->setParam("ContactFail", 0);
        $this->device->store();
        return true;
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
        // No polling a virtual device.
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
    /**
    * This calculates the averages
    *
    * It will return once for each average that it calculates.  The average will be
    * stored in the instance this is called from.  If this is fed history table
    * then it will calculate 15 minute averages.
    *
    * @param object &$data   This is the data to use to calculate the average
    *                        This is not used here, but it is required to
    *                        match the main implementation.
    * @param string $avgType The type of average to do
    *
    * @return null
    */
    public function &calcAverage(&$data, $avgType)
    {
        if (!is_object($this->average)) {
            include_once "AverageVirtual.php";
            $this->average = AverageVirtual::factory($this->system, $this->device);
        }
        // Make sure we try to do the averages
        $this->device->setLocalParam("LastHistory", $this->system->now());
        return parent::calcAverage($data, $avgType);
    }
}


?>
