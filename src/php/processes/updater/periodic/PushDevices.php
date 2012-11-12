<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\processes\updater\periodic;
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class PushDevices extends \HUGnet\processes\updater\Periodic
{
    /** This is the maximum number of history records to get */
    const MAX_HISTORY = 100;
    /** This is the period */
    protected $period = 30;
    /** This is the object we use */
    private $_device;
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
        if ($this->ready() && $this->hasMaster()) {
            $now = time();
            $ids = $this->_device->ids();
            foreach ($ids as $key => $devID) {
                $this->system()->main();
                if (!$this->ui()->loop()) {
                    break;
                }

                $this->_device->load($key);
                $this->_pushDevice($this->_device);
                $this->_pushHistory($this->_device);
            }
            $this->last = $now;
        }
    }
    /**
     * This pushes out all of the sensors for a device
     *
     * @param int &$dev The device to use
     *
     * @return none
     */
    private function _pushDevice(&$dev)
    {
        /* Let's just push the regular devices */
        if ($dev->id() >= 0xFE0000) {
            return;
        }
        $lastContact = $dev->getParam("LastContact");
        /* Only push it if we have changed it since the last push */
        if ($lastContact < $dev->getParam("LastMasterPush")) {
            return;
        }
        $this->system()->out(
            "Pushing ".sprintf("%06X", $dev->id())." to master server..."
        );
        $dev->setParam("LastMasterPush", $now);
        $ret = $dev->action()->post($url);
        if (is_array($ret) && ($ret["id"] == $dev->id())) {
            $this->system()->out(
                "Successfully pushed ".sprintf("%06X", $dev->id())."."
            );
            $dev->load($dev->id());
            $dev->setParam("LastMasterPush", $now);
            $dev->store();
            $this->_pushSensors($dev);
        } else {
            $this->system()->out("Failure.");
            /* Don't store it if we fail */
        }
    }
    /**
     * This pushes out all of the sensors for a device
     *
     * @param int &$dev The device to use
     *
     * @return none
     */
    private function _pushSensors(&$dev)
    {
        $sens = $dev->get("totalSensors");
        $good = 0;
        $bad  = 0;
        for ($i = 0; $i < $sens; $i++) {
            $this->system()->main();
            if (!$this->ui()->loop()) {
                break;
            }
            $ret = $dev->sensor($i)->action()->post($url);
            if (is_array($ret)
                && ($ret["dev"] == $dev->id())
                && ($ret["sensor"] == $i)
            ) {
                $good++;
            } else {
                $bad++;
            }
        }
        if ($good > 0) {
            $this->system()->out("Successfully pushed ".$good." sensors");
        }
        if ($bad > 0) {
            $this->system()->out("Failure to push out ".$bad." sensors!");
        }
    }
    /**
     * This pushes out all of the sensors for a device
     *
     * @param int &$dev The device to use
     *
     * @return none
     */
    private function _pushHistory(&$dev)
    {
        $hist = $dev->historyFactory(array(), true);
        $hist->sqlOrderBy = "Date asc";
        $last = (int)$dev->getParam("LastMasterHistoryPush");
        $hist->sqlLimit = self::MAX_HISTORY;
        $first = time();
        $ret = $hist->getPeriod($last + 1, time(), $dev->id());
        if ($ret) {
            $records = array();
            while ($ret) {
                $this->system()->main();
                if (!$this->ui()->loop()) {
                    break;
                }
                $records[] = $hist->toArray(false);
                $ret = $hist->nextInto();
            }
            $ret = $this->_postHistory(null, $dev->id(), $records);
            $good = 0;
            $bad = count($records);
            if (is_array($ret)) {
                for ($i = 0; $i < count($records); $i++) {
                    if ($ret[$i] == 1) {
                        if ($last < $records[$i]["Date"]) {
                            $last = $records[$i]["Date"];
                        }
                        if ($first > $records[$i]["Date"]) {
                            $first = $records[$i]["Date"];
                        }
                        $good++;
                        $bad--;
                    }
                }
            }
            if ($good > 0) {
                $this->system()->out(
                    sprintf("%06X ", $dev->id())
                    ."Successfully pushed ".$good." history from "
                    .date("Y-m-d H:i:s", $first)." to "
                    .date("Y-m-d H:i:s", $last)
                );
            }
            if ($bad > 0) {
                $this->system()->out(
                    "Failure to push out ".$bad." history records!"
                );
            }
            $dev->load($dev->id());
            $dev->setParam("LastMasterHistoryPush", $last);
            $dev->store();
        }
    }
    /**
    * Gets the config and saves it
    *
    * @param string $url     The url to post to
    * @param string $did     The device id to use
    * @param array  $records The records to send
    *
    * @return string The left over string
    */
    private function _postHistory($url, $did, $records)
    {
        if (!is_string($url) || (strlen($url) == 0)) {
            $master = $this->system()->get("master");
            $url = $master["url"];
        }

        return \HUGnet\Util::postData(
            $url,
            array(
                "uuid"   => urlencode($this->system()->get("uuid")),
                "action" => "put",
                "task"   => "history",
                "id"     => sprintf("%06X", $did),
                "data"   => $records,
            ),
            120
        );
    }

}


?>
