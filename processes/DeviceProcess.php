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
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/ProcessBase.php";
require_once dirname(__FILE__)."/../interfaces/PacketConsumerInterface.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceProcess extends ProcessBase implements PacketConsumerInterface
{
    /** @var array This is the default values for the data */
    protected $default = array(
        "group"      => "default",          // The groups to route between
        "GatewayKey" => 0,                  // The gateway key we are using
        "PluginDir"       => "./plugins",  // This is the plugin path
        "PluginExtension" => "php",
        "PluginType"      => "deviceProcess",
    );
    /** @var array Array of objects that are our plugins */
    protected $active = array();
    /** @var array Array of objects that are our plugins */
    protected $priority = array();
    /** @var object This is where our plugin object resides */
    protected $myPlugins = array();
    /** @var object This is where our plugin object resides */
    protected $myLocks = array();
    /**
    * Builds the class
    *
    * @param array $data   The data to build the class with
    * @param array $device This is the setup for my device class
    *
    * @return null
    */
    public function __construct($data, $device)
    {
        parent::__construct($data, $device);
        // Purge the locks
        $lock = new LockTable();
        $lock->purgeAll();
        // Set stuff up
        $this->registerHooks();
        $this->requireGateway();
        $this->registerPlugins();
        $this->main("pre");
    }
    /**
    * This function gets setup information from all of the devices
    *
    * This function should be called periodically as often as possible.  It will
    * check all plugins before returning
    *
    * @return null
    */
    protected function registerPlugins()
    {
        $this->active = array();
        $this->myPlugins = new PluginsContainer(
            array(
                "dir" => $this->PluginDir,
                "extension" => $this->PluginExtension,
            )
        );
        $classes = $this->myPlugins->getPlugin($this->PluginType);
        $data = array(
            "verbose" => $this->verbose,
        );
        foreach ((array)$classes as $class) {
            $c = $class["Class"];
            $n = $class["Name"];
            $p = (is_null($class["Priority"])) ? 50 : (int)$class["Priority"];
            if (is_subclass_of($c, "DeviceProcessPluginInterface")) {
                $this->active[$n] = new $c($data, $this);
                $this->priority[$p][$n] = $n;
            }
        }
        ksort($this->priority);
    }
    /**
    * This process runs analysis plugins on the data
    *
    * This function should be called periodically as often as possible.  It will
    * go through the whole list of devices before returning.
    *
    * @param string $fct The function to call
    *
    * @return null
    */
    public function main($fct = "main")
    {
        $this->myLocks = array();
        // Get the devices that are not part of this data collector
        $where = "id <> ? AND DeviceLocation <> ?";
        $data = array($this->myDevice->id, $this->myDevice->DeviceLocation);

        if ($this->GatewayKey != "all") {
            $where .= " AND GatewayKey = ?";
            $data[] = $this->GatewayKey;
        }
        $devs = $this->device->selectIDs(
            $where,
            $data
        );
        shuffle($devs);
        // Go through the devices
        foreach ($devs as $key) {
            if (!$this->loop()) {
                return;
            }
            $this->checkDev($key, $fct);
        }
        foreach ($this->myLocks as $name => $locks) {
            sort($locks);
            self::vprint(
                "$name locks:  ".implode($locks, ","),
                1
            );
        }


    }
    /**
    * This function should be used to wait between config attempts
    *
    * @param int    $devId The id of the device to work with
    * @param string $fct   The function to call
    *
    * @return int The number of packets routed
    */
    protected function checkDev($devId, $fct)
    {
        $this->device->getRow($devId);
        $this->checkPlugins($this->device, $fct);
        $this->updateDev($fct);
    }
    /**
    * This function should be used to wait between config attempts
    *
    * @param DeviceContainer &$dev The device to check
    * @param string          $fct  The function to call
    *
    * @return int The number of packets routed
    */
    protected function checkPlugins(DeviceContainer &$dev, $fct = "main")
    {
        $lock = $this->checkLock($dev);
        foreach ($this->priority as $p) {
            foreach ($p as $n) {
                if ($this->active[$n]->ready($dev)) {
                    $ret = $this->runPlugin($this->active[$n], $dev, $fct, $lock);
                    if (false === $ret) {
                        return;
                    }
                }
            }
        }
    }
    /**
    * This function should be used to wait between config attempts
    *
    * @param object          &$plugin A reference to the plugin to run
    * @param DeviceContainer &$dev    The device to check
    * @param string          $fct     The function to call
    * @param bool            $lock    Whether this device is locked
    *
    * @return int The number of packets routed
    */
    protected function runPlugin(
        &$plugin, DeviceContainer &$dev, $fct = "main", $lock = false
    ) {
        if (!$plugin->requireLock() || $lock) {
            $ret = $plugin->$fct($dev);
        }
        return $ret;
    }
    /**
    * This function should be used to wait between config attempts
    *
    * @param string $fct The function to call
    *
    * @return int The number of packets routed
    */
    protected function updateDev($fct = "main")
    {
        $this->preUpdate($fct);
        $this->device->updateRow();
    }
    /**
    * This is called just before the device update to set anything that needs to
    * be before the device is updated
    *
    * @param string $fct The function to call
    *
    * @return string
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function preUpdate($fct = "main")
    {
    }
    /**
    * This deals with Unsolicited Packets
    *
    * @param PacketContainer &$pkt The packet that is to us
    *
    * @return string
    */
    public function packetConsumer(PacketContainer &$pkt)
    {
        foreach ($this->priority as $p) {
            foreach ($p as $n) {
                if ($this->active[$n] instanceof PacketConsumerInterface) {
                    $this->active[$n]->packetConsumer($pkt);
                }
            }
        }
    }
    /**
    * This function should be used to wait between config attempts
    *
    * @param DeviceContainer &$dev The device to check
    *
    * @return int The number of packets routed
    */
    protected function checkLock(DeviceContainer &$dev)
    {
        $local = $this->myDevice->checkLocalDevLock($dev->DeviceID);
        // Renew the license if the expiration is looming
        $time = 60 + mt_rand(0, 120);
        if ($local->isEmpty() || (($local->expiration - $this->now()) < $time)) {
            $locks = $this->myDevice->getDevLock($dev);
            $setLock = true;
            foreach (array_keys($locks) as $key) {
                if (!$this->myDevice->myLock($locks[$key])
                    && !$locks[$key]->isEmpty()
                ) {
                    $setLock = false;
                    break;
                }
            }
            if ($setLock) {
                $this->myDevice->setDevLock($dev, null, true);
            }
            $local = $this->myDevice->checkLocalDevLock($dev->DeviceID);
        }
        return $this->_checkLockSet($dev, $local);
    }
    /**
    * This function sets a lock
    *
    * @param DeviceContainer &$dev   The device to check
    * @param LockTable       &$local The local lock if there is one
    *
    * @return int The number of packets routed
    */
    private function _checkLockSet(DeviceContainer &$dev, LockTable &$local)
    {
        $ret = $this->myDevice->myLock($local);
        if (!$dev->gateway()) {
            if ($ret) {
                $this->myLocks["My"][$dev->DeviceID] = $dev->DeviceID;
            } else if (!$local->isEmpty()) {
                $devId = self::stringSize(dechex($local->id), 6);
                $this->myLocks[$devId][$dev->DeviceID] = $dev->DeviceID;
            }
        }
        return $ret;
    }


}
?>
