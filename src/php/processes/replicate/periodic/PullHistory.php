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
namespace HUGnet\processes\replicate\periodic;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

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
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class PullHistory extends \HUGnet\processes\replicate\Periodic
{
    /** This is the maximum number of history records to get */
    const MAX_HISTORY = 1000;
    /** This is the period */
    protected $period = 60;
    /** This is the object we use */
    private $_device;
    /** This is the url to get stuff from */
    private $_url;
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$gui The user interface to use
    *
    * @return null
    */
    protected function __construct(&$gui)
    {
        parent::__construct($gui);
        $this->_device = $this->system()->device();
    }
    /**
    * This function creates the system.
    *
    * @param object &$gui the user interface object
    *
    * @return null
    */
    public static function &factory(&$gui)
    {
        return parent::intFactory($gui);
    }
    /**
    * This function creates the system.
    *
    * @return null
    */
    public function &execute()
    {
        $this->_url = $this->ui()->get("url");
        if ($this->ready() && !empty($this->_url)) {
            $now = $this->system()->now();
            $ids = $this->_device->ids();
            foreach (array_keys($ids) as $key) {
                $this->system()->main();
                if (!$this->ui()->loop()) {
                    break;
                }
                $this->_device->load($key);
                if ($this->_checkDevice($this->_device, $now)) {
                    $this->_pullHistory($this->_device);
                }
            }
            $this->last = $now;
        }
    }
    /**
     * This checks to see if a device should be pulled...
     *
     * @param int &$dev The device to use
     * @param int $now  The time to use
     *
     * @return true if it should be pulled, false otherwise
     */
    private function _checkDevice(&$dev, $now)
    {
        /* Let's just pull the regular devices */
        if ($dev->id() >= 0xFD0000) {
            $this->system()->out("DeviceID > FD0000", 2);
            return false;
        }
        return true;
    }
    
    /**
     * This pulles out all of the sensors for a device
     *
     * @param int &$dev The device to use
     *
     * @return none
     */
    private function _pullHistory(&$dev)
    {
        $pull = $dev->getParam("PushHistory");
        if (is_null($pull) || ($pull != 0)) {
            $this->system()->out("Pulling History for ".sprintf("%06X", $dev->id()));
            $hist = $dev->historyFactory(array(), true);
            $cnt = 0;
            do {
                $ret = $this->_pullHist($dev, $hist, "LastMasterHistoryPull", "");
                $cnt++;
                if (!$this->ui()->loop()) {
                    break;
                }
            } while (($ret == self::MAX_HISTORY) && ($cnt < 10));
            if ($ret === false) {
                $this->system()->out("Failed to contact server");
            } else if (($ret > 0) || ($cnt > 1)) {
                $this->system()->out("Done");
            } else {
                $this->system()->out("No new records");
            }
        }
    }
    /**
     * This pulles out all of the sensors for a device
     *
     * @param object &$dev  The device to use
     * @param object &$hist The history to use
     * @param string $param The params to set to see when we last did that
     * @param string $name  The name to print out
     *
     * @return none
     */
    private function _pullHist(&$dev, &$hist, $param, $name)
    {
        $last = (int)$dev->getLocalParam($param);
        if ($last == 0) {
            // If we don't have a last history date, use the created date.
            $last = (int)$dev->getParam("Created");
        }
        $first = $this->system()->now();
        $ret = $this->_getHistory($dev->id(), $last + 1, $first);
        if ($ret) {
            $good = 0;
            $bad = 0;
            $badfirst = $this->system()->now();
            $badlast = 0;
            if (is_array($ret)) {
                foreach ($ret as $record) {
                    $hist->clearData();
                    $hist->fromAny($record);
                    if ($hist->insertRow(true)) {
                        if ($last < $record["Date"]) {
                            $last = $record["Date"];
                        }
                        if ($first > $record["Date"]) {
                            $first = $record["Date"];
                        }
                        $good++;
                    } else {
                        if ($badlast < $record["Date"]) {
                            $badlast = $record["Date"];
                        }
                        if ($badfirst > $record["Date"]) {
                            $badfirst = $record["Date"];
                        }
                        $bad++;
                    }
                }
                $count = count($ret);
            } else {
                $this->system()->out(
                    sprintf("%06X ", $dev->id())
                    ." No reply to $name history pull"
                );
            }
            if ($good > 0) {
                $this->system()->out(
                    sprintf("%06X ", $dev->id())
                    ."Successfully pulled $good $name history from "
                    .date("Y-m-d H:i:s", $first)." to "
                    .date("Y-m-d H:i:s", $last)
                );
            }
            if ($bad > 0) {
                $this->system()->out(
                    sprintf("%06X ", $dev->id())
                    ."Failed to pull $bad $name history from "
                    .date("Y-m-d H:i:s", $badfirst)." to "
                    .date("Y-m-d H:i:s", $badlast)
                );
            }
            $dev->load($dev->id());
            $dev->setLocalParam($param, $last);
            // This sets the last history date.
            $dev->setLocalParam("Last".$name."History", $last);
            $dev->store();
        } else {
            return false;
        }
        return $count;
    }
    /**
    * Gets the config and saves it
    *
    * @param string $did   The device id to use
    * @param int    $start The start date (early date)
    * @param int    $end   The end date (later date)
    *
    * @return string The left over string
    */
    private function _getHistory($did, $start, $end)
    {
        return \HUGnet\Util::postData(
            $this->_url,
            array(
                "uuid"   => urlencode($this->system()->get("uuid")),
                "action" => "get",
                "task"   => "history",
                "id"     => sprintf("%06X", $did),
                "data"   => array(
                    "since"   => $start,
                    "until"   => $end,
                    "limit"   => self::MAX_HISTORY,
                    "order"   => "asc",
                    "type"    => "history",
                    "convert" => 0,  // No unit conversions in this record.
                ),
            ),
            120
        );
    }

}


?>
