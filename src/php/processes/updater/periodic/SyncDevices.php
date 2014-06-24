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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class SyncDevices extends \HUGnet\processes\updater\Periodic
{
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
        if ($this->ready() && $this->hasPartner()) {
            $now = $this->system()->now();
            $ids = $this->_device->ids();
            foreach (array_keys($ids) as $key) {
                $this->system()->out("Working on ".sprintf("%06X", $key), 2);
                $this->system()->main();
                if (!$this->ui()->loop()) {
                    break;
                }
                $this->_device->load($key);
                if ($this->_checkDevice($this->_device, $now)) {
                    $this->_pushDevice($this->_device, $now);
                }
            }
            $this->last = $now;
        }
    }
    /**
     * This checks to see if a device should be pushed...
     *
     * @param int &$dev The device to use
     * @param int $now  The time to use
     *
     * @return true if it should be pushed, false otherwise
     */
    private function _checkDevice(&$dev, $now)
    {
        /* Let's just push the regular devices */
        if ($dev->id() >= 0xFD0000) {
            $this->system()->out("DeviceID > FD0000", 2);
            return false;
        }
        $modified = $dev->getParam("Modified");
        $push     = $dev->getLocalParam("LastSync");
        /* Only push it if we have changed it since the last push */
        if (($modified < $push)) {
            $this->system()->out("Device not updated", 2);
            return false;
        }
        return true;
    }
    
    /**
     * This pushes out all of the sensors for a device
     *
     * @param int &$dev The device to use
     * @param int $now  The time to use
     *
     * @return none
     */
    private function _pushDevice(&$dev, $now)
    {
        $this->system()->out(
            "Pushing ".sprintf("%06X", $dev->id())." to partner (sync)..."
        );
        $dev->setLocalParam("LastSync", $now);
        $ret = $dev->action()->sync();
        $devid = hexdec($ret);
        if ($devid == $dev->id()) {
            $this->system()->out(
                "Successfully sync'd ".sprintf("%06X", $dev->id())."."
            );
            $dev->load($dev->id());
            $dev->setLocalParam("LastSync", $now);
            $dev->store();
        } else {
            $this->system()->out("Failure.");
            /* Don't store it if we fail */
        }
    }

}


?>
