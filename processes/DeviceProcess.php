<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Processes
 * @package    HUGnetLib
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
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
 * @category   Processes
 * @package    HUGnetLib
 * @subpackage Processes
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
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
        $this->myPlugins = new PluginsContainer(array(
            "dir" => $this->PluginDir,
            "extension" => $this->PluginExtension,
        ));
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
        // Get the devices
        $where = "id <> ?";
        $data = array($this->myDevice->id);
        
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
            $this->device->getRow($key);
            $this->_check($this->device, $fct);
            $this->device->updateRow();
        }

    }
    /**
    * This function should be used to wait between config attempts
    *
    * @param DeviceContainer &$dev The device to check
    * @param string          $fct  The function to call
    *
    * @return int The number of packets routed
    */
    private function _check(DeviceContainer &$dev, $fct = "main")
    {
        foreach ($this->priority as $k => $p) {
            foreach ($p as $n) {
                if ($this->active[$n]->ready($dev)) {
                    if ($this->active[$n]->$fct($dev) === false) {
                        return;
                    }
                }
            }
        }
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
}
?>
